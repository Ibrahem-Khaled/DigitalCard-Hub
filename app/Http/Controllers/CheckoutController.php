<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\DigitalCard;
use App\Models\LoyaltyPoint;
use App\Mail\DigitalCardsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    /**
     * عرض صفحة الدفع
     */
    public function index()
    {
        $cart = $this->getCart();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'سلة التسوق فارغة');
        }

        $cart->load(['items.product.category']);

        // حساب الإجماليات
        $subtotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $tax = $subtotal * 0.14; // ضريبة 14%
        $shipping = 0; // شحن مجاني
        $discount = $cart->discount_amount ?? 0;
        $total = $subtotal + $tax + $shipping - $discount;

        return view('cart.checkout', compact('cart', 'subtotal', 'tax', 'shipping', 'discount', 'total'));
    }

    /**
     * معالجة الطلب
     */
    public function process(Request $request)
    {
        // Debug: Log the request
        Log::info('Checkout process started', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'payment_method' => 'required|in:amwalpay,credit_card,paypal,bank_transfer',
            'terms' => 'accepted',
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'phone.required' => 'رقم الهاتف مطلوب',
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
        ]);

        $cart = $this->getCart();

        if (!$cart || $cart->items->count() === 0) {
            Log::warning('Empty cart detected');
            return redirect()->route('cart.index')
                ->with('error', 'سلة التسوق فارغة');
        }

        Log::info('Cart found', [
            'cart_id' => $cart->id,
            'items_count' => $cart->items->count()
        ]);

        try {
            DB::beginTransaction();

            // حساب الإجماليات
            $subtotal = $cart->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            $tax = $subtotal * 0.14;
            $total = $subtotal + $tax - ($cart->discount_amount ?? 0);

            // إنشاء الطلب
            $billingAddress = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
            ];

            $order = Order::create([
                'user_id' => Auth::id() ?? null, // Support for guest checkout
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => 0,
                'discount_amount' => $cart->discount_amount ?? 0,
                'total_amount' => $total,
                'currency' => 'USD',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'billing_address' => $billingAddress,
                'shipping_address' => $billingAddress, // Same as billing for digital products
            ]);

            // إضافة عناصر الطلب وتخصيص البطاقات الرقمية
            $orderItemsWithCards = [];
            $hasInsufficientCards = false;

            foreach ($cart->items as $item) {
                // تحميل المنتج للتأكد من أنه منتج رقمي
                $item->load('product');
                
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total_price' => $item->total_price,
                    'status' => 'pending',
                ]);

                // تخصيص البطاقات الرقمية فقط للمنتجات الرقمية
                if ($item->product && $item->product->is_digital) {
                    try {
                        $assignedCards = $this->assignDigitalCards($orderItem, $item->quantity);

                        if (!empty($assignedCards)) {
                            $orderItemsWithCards[] = [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'cards' => $assignedCards
                            ];
                        } else {
                            // إذا لم تكن هناك بطاقات كافية، نضيف المنتج مع رسالة خطأ
                            $hasInsufficientCards = true;
                            Log::error("Insufficient digital cards for product {$item->product->name} (ID: {$item->product_id}). Requested: {$item->quantity}");
                        }
                    } catch (\Exception $e) {
                        // إذا حدث خطأ في تخصيص البطاقات، نضيف المنتج مع رسالة خطأ
                        $hasInsufficientCards = true;
                        Log::error("Error assigning digital cards for product {$item->product->name} (ID: {$item->product_id}): " . $e->getMessage());
                    }
                }
            }

            // إذا لم تكن هناك بطاقات كافية، نعيد الخطأ
            if ($hasInsufficientCards && empty($orderItemsWithCards)) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'عذراً، لا توجد بطاقات كافية في المخزون لإتمام طلبك. يرجى المحاولة مرة أخرى لاحقاً.')
                    ->withInput();
            }

            // إنشاء سجل الدفع
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'amount' => $total,
                'currency' => 'USD',
                'payment_method' => $request->payment_method,
                'payment_gateway' => null,
                'status' => 'pending',
            ]);

            // إذا كانت طريقة الدفع AmwalPay، ننتقل لمعالجتها
            if ($request->payment_method === 'amwalpay') {
                // حفظ بيانات البطاقات مؤقتاً في Cache
                Cache::put("order_cards_{$order->id}", $orderItemsWithCards, now()->addHours(1));

                DB::commit();

                // إرسال بيانات الطلب إلى AmwalPay
                return redirect()->route('amwal.process')
                    ->with([
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'amount' => $total,
                        'customer_name' => $request->first_name . ' ' . $request->last_name,
                        'customer_email' => $request->email,
                    ]);
            }

            // للطرق الأخرى (يدوية)، نرسل الأكواد مباشرة
            // إرسال البطاقات الرقمية عبر الإيميل
            if (!empty($orderItemsWithCards)) {
                try {
                    $customerName = $request->first_name . ' ' . $request->last_name;
                    $customerEmail = $request->email;

                    Mail::to($customerEmail)->send(
                        new DigitalCardsEmail($order, $orderItemsWithCards, $customerName)
                    );

                    // تحديث حالة الطلب إلى تمت المعالجة
                    $order->update([
                        'status' => 'processing',
                        'payment_status' => 'paid',
                        'processed_at' => now()
                    ]);

                    // تحديث حالة الدفع
                    $payment->update([
                        'status' => 'successful',
                        'processed_at' => now()
                    ]);

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

                    Log::info("Digital cards email sent successfully to {$customerEmail} for order {$order->order_number}");
                } catch (\Exception $e) {
                    Log::error('Error sending digital cards email: ' . $e->getMessage());
                }
            }

            // حذف السلة
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'cards_sent' => !empty($orderItemsWithCards)
            ]);

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'تم إنشاء طلبك بنجاح! تم إرسال البطاقات الرقمية إلى بريدك الإلكتروني.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout process failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء معالجة طلبك. حاول مرة أخرى.')
                ->withInput();
        }
    }

    /**
     * صفحة نجاح الطلب
     */
    public function success($orderId)
    {
        $order = Order::with(['orderItems.product', 'payments'])->findOrFail($orderId);

        // التحقق من أن الطلب يخص المستخدم الحالي (if logged in)
        if (Auth::check() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('cart.success', compact('order'));
    }

    /**
     * الحصول على السلة
     */
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        } else {
            return Cart::where('session_id', Session::getId())->first();
        }
    }

    /**
     * تخصيص البطاقات الرقمية للطلب
     */
    private function assignDigitalCards(OrderItem $orderItem, int $quantity)
    {
        // جلب البطاقات المتاحة للمنتج
        $availableCards = DigitalCard::where('product_id', $orderItem->product_id)
            ->available()
            ->limit($quantity)
            ->lockForUpdate() // قفل البطاقات لمنع التضارب
            ->get();

        if ($availableCards->count() < $quantity) {
            // إذا لم تكن هناك بطاقات كافية، يمكن إرسال إشعار للإدارة
            Log::error("Not enough digital cards for product {$orderItem->product_id}. Requested: {$quantity}, Available: {$availableCards->count()}");
            throw new \Exception("لا توجد بطاقات كافية في المخزون للمنتج. المطلوب: {$quantity}, المتوفر: {$availableCards->count()}");
        }

        $assignedCards = [];

        // تعيين البطاقات للمستخدم وربطها بالطلب
        foreach ($availableCards as $card) {
            // Get user_id from order if available
            $userId = $orderItem->order->user_id;

            $card->markAsUsed(
                $userId,
                $orderItem->id
            );

            // إعادة تحميل البطاقة للحصول على البيانات المحدثة
            $card->refresh();
            $assignedCards[] = $card;
        }

        // تحديث حالة OrderItem
        if (!empty($assignedCards)) {
            $orderItem->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);
        }

        return $assignedCards;
    }
}

