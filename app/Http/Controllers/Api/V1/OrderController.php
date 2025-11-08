<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\DigitalCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    /**
     * Get user orders
     */
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($orders);
    }

    /**
     * Get single order
     */
    public function show(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['items.product', 'items.digitalCard', 'payment'])
            ->first();

        if (!$order) {
            return $this->notFoundResponse('الطلب غير موجود');
        }

        return $this->successResponse(new OrderResource($order));
    }

    /**
     * Create order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_email' => 'required|email',
            'shipping_phone' => 'required|string',
            'shipping_address' => 'nullable|string|max:500',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:20',
            'payment_method' => 'required|in:amwalpay,credit_card,paypal,bank_transfer',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('is_abandoned', false)
            ->with('items.product')
            ->first();

        if (!$cart || $cart->items->count() === 0) {
            return $this->errorResponse('سلة التسوق فارغة', 400);
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $cart->items->sum(function($item) {
                return $item->quantity * $item->price;
            });
            $tax = $subtotal * 0.14; // 14% tax
            $shipping = 0; // Free shipping for digital products
            $discount = $cart->discount_amount ?? 0;
            $total = $subtotal + $tax + $shipping - $discount;

            // Create order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => $this->generateOrderNumber(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'currency' => 'OMR',
                'shipping_first_name' => $request->input('shipping_first_name'),
                'shipping_last_name' => $request->input('shipping_last_name'),
                'shipping_email' => $request->input('shipping_email'),
                'shipping_phone' => $request->input('shipping_phone'),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_city' => $request->input('shipping_city'),
                'shipping_country' => $request->input('shipping_country'),
                'shipping_postal_code' => $request->input('shipping_postal_code'),
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'subtotal' => $cartItem->subtotal,
                    'status' => 'pending',
                ]);

                // Assign digital card if available
                if ($cartItem->product->is_digital) {
                    $digitalCard = DigitalCard::where('product_id', $cartItem->product_id)
                        ->where('status', 'available')
                        ->first();

                    if ($digitalCard) {
                        $digitalCard->update([
                            'order_item_id' => $orderItem->id,
                            'status' => 'reserved',
                        ]);
                        $orderItem->digital_card_id = $digitalCard->id;
                        $orderItem->save();
                    }
                }
            }

            // Create payment
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $request->user()->id,
                'method' => $request->input('payment_method'),
                'amount' => $total,
                'status' => 'pending',
                'currency' => 'OMR',
            ]);

            // Mark cart as abandoned (completed)
            $cart->is_abandoned = true;
            $cart->abandoned_at = now();
            $cart->save();

            DB::commit();

            $order->load(['items.product', 'items.digitalCard', 'payment']);

            return $this->successResponse(new OrderResource($order), 'تم إنشاء الطلب بنجاح', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ أثناء إنشاء الطلب: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$order) {
            return $this->notFoundResponse('الطلب غير موجود');
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return $this->errorResponse('لا يمكن إلغاء هذا الطلب', 400);
        }

        try {
            DB::beginTransaction();

            $order->status = 'cancelled';
            $order->save();

            // Release digital cards
            foreach ($order->items as $item) {
                if ($item->digitalCard) {
                    $item->digitalCard->update([
                        'order_item_id' => null,
                        'status' => 'available',
                    ]);
                }
            }

            DB::commit();

            return $this->successResponse(new OrderResource($order->load(['items.product', 'payment'])), 'تم إلغاء الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('حدث خطأ أثناء إلغاء الطلب', 500);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
}


