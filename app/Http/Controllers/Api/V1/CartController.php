<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    /**
     * Get user cart
     */
    public function index(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $product = Product::active()->findOrFail($request->product_id);

        if (!$product->is_in_stock) {
            return $this->errorResponse('المنتج غير متوفر حالياً', 400);
        }

        $cart = $this->getOrCreateCart($request->user());

        // Check if product already in cart
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->current_price,
                'subtotal' => $request->quantity * $product->current_price,
            ]);
        }

        $cart->updateActivity();
        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart), 'تم إضافة المنتج للسلة بنجاح');
    }

    /**
     * Update cart item
     */
    public function update(Request $request, $cartItem)
    {
        $cartItem = CartItem::findOrFail($cartItem);

        // Check if cart item belongs to user
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->subtotal = $cartItem->quantity * $cartItem->price;
        $cartItem->save();

        $cart = $cartItem->cart;
        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart), 'تم تحديث الكمية بنجاح');
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, $cartItem)
    {
        $cartItem = CartItem::findOrFail($cartItem);

        // Check if cart item belongs to user
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $cartItem->delete();

        $cart = $cartItem->cart;
        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart), 'تم حذف المنتج من السلة بنجاح');
    }

    /**
     * Clear cart
     */
    public function clear(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->items()->delete();
        $cart->coupon_id = null;
        $cart->discount_amount = 0;
        $cart->save();

        return $this->successResponse(null, 'تم تفريغ السلة بنجاح');
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $cart = $this->getOrCreateCart($request->user());
        $coupon = Coupon::where('code', $request->input('code'))->first();

        if (!$coupon) {
            return $this->errorResponse('كود الخصم غير صحيح', 400);
        }

        if (!$coupon->isValid()) {
            return $this->errorResponse('كود الخصم غير صالح أو منتهي الصلاحية', 400);
        }

        if (!$coupon->canBeUsedBy($request->user()->id)) {
            return $this->errorResponse('لا يمكنك استخدام هذا الكوبون', 400);
        }

        $subtotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        if ($subtotal < $coupon->minimum_amount) {
            return $this->errorResponse("الحد الأدنى للطلب هو {$coupon->minimum_amount}", 400);
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($subtotal);

        $cart->coupon_id = $coupon->id;
        $cart->discount_amount = $discount;
        $cart->save();

        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart), 'تم تطبيق الكوبون بنجاح');
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(Request $request)
    {
        $cart = $this->getOrCreateCart($request->user());
        $cart->coupon_id = null;
        $cart->discount_amount = 0;
        $cart->save();

        $cart->load(['items.product', 'coupon']);

        return $this->successResponse(new CartResource($cart), 'تم إزالة الكوبون بنجاح');
    }

    /**
     * Get or create cart for user
     */
    private function getOrCreateCart($user)
    {
        $cart = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
            ]);
        }

        return $cart;
    }
}

