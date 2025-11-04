<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\Payment;
use App\Models\LoyaltyPoint;
use App\Mail\DigitalCardsEmail;
use Exception;

class AmwalPayController extends Controller
{
    protected $environment;
    protected $merchant_id;
    protected $terminal_id;
    protected $secret_key;
    protected $callback_url;

    /**
     * Initialize AmwalPay Configuration from config file.
     */
    public function __construct()
    {
        $this->environment = config('amwalpay.environment');
        $this->merchant_id = config('amwalpay.merchant_id');
        $this->terminal_id = config('amwalpay.terminal_id');
        $this->secret_key = config('amwalpay.secret_key');
        $this->callback_url = config('amwalpay.callback_url');
    }

    /**
     * Handle the payment process view rendering.
     * Prepares the data and secure hash to send to the Amwal SmartBox.
     */
    public function process(Request $request)
    {
        try {
            // Get order data from session
            $orderId = session('order_id', '1');
            $amount = session('amount', '1');
            $order_number = session('order_number');

            // If no order data in session, try to get from request
            if ($request->has('order_id')) {
                $order = Order::find($request->order_id);
                if ($order) {
                    $orderId = $order->id;
                    $amount = (string) $order->total_amount;
                    $order_number = $order->order_number;

                    // Store in session for callback
                    session([
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'amount' => $order->total_amount,
                        'customer_email' => $request->customer_email ?? $order->billing_address['email'] ?? '',
                        'customer_name' => $request->customer_name ?? '',
                    ]);
                }
            }

            // Language code handling
            $locale = app()->getLocale();
            $locale = (strpos($locale, 'en') !== false) ? 'en' : 'ar';

            $datetime = date('YmdHis');

            // Generate secure hash for request integrity
            $secureHash = self::generateString(
                $amount,
                512,
                $this->merchant_id,
                $orderId,
                $this->terminal_id,
                $this->secret_key,
                $datetime
            );

            // Construct the payload for SmartBox
            $data = (object) [
                'AmountTrxn' => $amount,
                'MerchantReference' => $orderId,
                'MID' => $this->merchant_id,
                'TID' => $this->terminal_id,
                'CurrencyId' => 512,
                'LanguageId' => $locale,
                'SecureHash' => $secureHash,
                'TrxDateTime' => $datetime,
                'PaymentViewType' => 1,
                'RequestSource' => 'Checkout_Direct_Integration',
                'SessionToken' => '',
            ];
            $jsonData = json_encode($data);
            $url = $this->getSmartBoxUrl($this->environment);
            $callback = $this->callback_url;
            $cancel_url= ''; // Adjust the cancel url according your needs

            Log::info('Initiating AmwalPay payment process', [
                'data' => $data,
                'url' => $url,
                'callback' => $callback,
                'cancel_url' => $cancel_url,
            ]);
            // Return the SmartBox payment view
            return view('amwalpay::smartbox', compact('jsonData', 'url', 'callback','cancel_url'));

        } catch (Exception $e) {
            // Log or handle the exception
            Log::error('AmwalPay process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $response = ['IsSuccess' => 'false', 'Message' => $e->getMessage()];
            return response()->json($response);
        }
    }

    /**
     * Handle the callback from Amwal after payment attempt.
     * Verifies the transaction integrity and returns success or failure.
     */
    public function callBack(Request $request)
    {
        try {
            $responseCode = self::sanitizeVar('responseCode', 'GET');
            $secureHashValue = self::sanitizeVar('secureHashValue', 'GET');
            $merchantReference = self::sanitizeVar('merchantReference', 'GET');

            Log::info('AmwalPay callback received', [
                'response_code' => $responseCode,
                'merchant_reference' => $merchantReference,
                'request_data' => $request->all()
            ]);

            // Prepare data for integrity hash check
            $integrityParameters = [
                "amount" => self::sanitizeVar('amount', 'GET'),
                "currencyId" => self::sanitizeVar('currencyId', 'GET'),
                "customerId" => self::sanitizeVar('customerId', 'GET'),
                "customerTokenId" => self::sanitizeVar('customerTokenId', 'GET'),
                "merchantId" => $this->merchant_id,
                "merchantReference" => self::sanitizeVar('merchantReference', 'GET'),
                "responseCode" => self::sanitizeVar('responseCode', 'GET'),
                "terminalId" => $this->terminal_id,
                "transactionId" => self::sanitizeVar('transactionId', 'GET'),
                "transactionTime" => self::sanitizeVar('transactionTime', 'GET')
            ];

            $calculatedHash = self::generateStringForFilter($integrityParameters, $this->secret_key);
            $receivedHash = $secureHashValue;

            Log::info('Hash verification', [
                'calculated' => $calculatedHash,
                'received' => $receivedHash,
                'match' => $calculatedHash === $receivedHash
            ]);

            // Check payment status
            $isPaymentApproved = ($responseCode === '00' && $calculatedHash === $receivedHash);

            if ($isPaymentApproved) {
                // Get order from merchant reference (order ID)
                $order = Order::find($merchantReference);

                if ($order) {
                    // Update order status
                    $order->update([
                        'status' => 'processing',
                        'payment_status' => 'paid',
                        'payment_reference' => $integrityParameters['transactionId'],
                        'processed_at' => now()
                    ]);

                    // Update payment record
                    Payment::where('order_id', $order->id)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'successful',
                            'payment_gateway' => 'amwalpay',
                            'gateway_transaction_id' => $integrityParameters['transactionId'],
                            'gateway_response' => $integrityParameters,
                            'processed_at' => now()
                        ]);

                    // Send digital cards via email
                    $orderCards = Cache::get("order_cards_{$order->id}");
                    if ($orderCards) {
                        try {
                            $customerEmail = session('customer_email', $order->billing_address['email'] ?? '');
                            $customerName = session('customer_name', $order->billing_address['first_name'] . ' ' . $order->billing_address['last_name']);

                            Mail::to($customerEmail)->send(
                                new DigitalCardsEmail($order, $orderCards, $customerName)
                            );

                            Log::info("Digital cards email sent for order {$order->order_number}");
                        } catch (\Exception $e) {
                            Log::error('Error sending digital cards email: ' . $e->getMessage());
                        }

                        // Clear cache
                        Cache::forget("order_cards_{$order->id}");
                    }

                    // إضافة نقاط الولاء للمستخدم (إذا كان مسجل دخول)
                    if ($order->user_id) {
                        try {
                            // التحقق من عدم إضافة نقاط مسبقاً لهذا الطلب
                            $existingPoints = LoyaltyPoint::where('user_id', $order->user_id)
                                ->where('source', 'purchase')
                                ->where('source_id', $order->id)
                                ->first();

                            if (!$existingPoints) {
                                LoyaltyPoint::addPointsForPurchase($order->user_id, $order->total_amount, $order->id);
                                Log::info("Loyalty points added for order {$order->order_number}");
                            }
                        } catch (\Exception $e) {
                            Log::error('Error adding loyalty points: ' . $e->getMessage());
                        }
                    }

                    return redirect()->route('checkout.success', $order->id)
                        ->with('success', 'تم الدفع بنجاح! تم إرسال البطاقات الرقمية إلى بريدك الإلكتروني.');
                }

                return redirect()->route('checkout.index')
                    ->with('error', 'تم الدفع بنجاح لكن لم يتم العثور على الطلب.');
            } else {
                Log::warning('Payment failed', [
                    'response_code' => $responseCode,
                    'merchant_reference' => $merchantReference
                ]);

                return redirect()->route('checkout.index')
                    ->with('error', 'فشلت عملية الدفع. يرجى المحاولة مرة أخرى.');
            }

        } catch (Exception $e) {
            Log::error('AmwalPay callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'حدث خطأ أثناء معالجة الدفع.');
        }
    }

    /**
     * Get SmartBox JavaScript URL based on environment.
     */
    public function getSmartBoxUrl($env)
    {
        if ($env === "prod") {
            return "https://checkout.amwalpg.com/js/SmartBox.js?v=1.1";
        } elseif ($env === "uat") {
            return "https://test.amwalpg.com:7443/js/SmartBox.js?v=1.1";
        } elseif ($env === "sit") {
            return "https://test.amwalpg.com:19443/js/SmartBox.js?v=1.1";
        }

        return null;
    }
    /**
     * Generate secure hash string for SmartBox payment initiation.
     */
    public static function generateString(
        $amount,
        $currencyId,
        $merchantId,
        $merchantReference,
        $terminalId,
        $hmacKey,
        $trxDateTime
    ) {

        $string = "Amount={$amount}&CurrencyId={$currencyId}&MerchantId={$merchantId}&MerchantReference={$merchantReference}&RequestDateTime={$trxDateTime}&SessionToken=&TerminalId={$terminalId}";

        $sign = self::encryptWithSHA256($string, $hmacKey);
        return strtoupper($sign);
    }
    /**
     * Generate HMAC-SHA256 hash using hex-encoded key.
     */
    public static function encryptWithSHA256($input, $hexKey)
    {
        // Convert the hex key to binary
        $binaryKey = hex2bin($hexKey);
        // Calculate the SHA-256 hash using hash_hmac
        $hash = hash_hmac('sha256', $input, $binaryKey);
        return $hash;
    }
    /**
     * Generate secure hash from a key-value array of payment data.
     */
    public static function generateStringForFilter(
        $data,
        $hmacKey

    ) {
        // Convert data array to string key value with and sign
        $string = '';
        foreach ($data as $key => $value) {
            $string .= $key . '=' . ($value === "null" || $value === "undefined" ? '' : $value) . '&';
        }
        $string = rtrim($string, '&');
        // Generate SIGN
        $sign = self::encryptWithSHA256($string, $hmacKey);
        return strtoupper($sign);
    }
    /**
     * Safely retrieve a GET or POST variable, sanitized.
     */
    public static function sanitizeVar($name, $global = 'GET')
    {
        if (isset($GLOBALS['_' . $global][$name])) {
            if (is_array($GLOBALS['_' . $global][$name])) {
                return $GLOBALS['_' . $global][$name];
            }
            return htmlspecialchars($GLOBALS['_' . $global][$name], ENT_QUOTES);
        }
        return null;
    }
}
