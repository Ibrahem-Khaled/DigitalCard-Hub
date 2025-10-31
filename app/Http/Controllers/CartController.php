<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * عرض السلة
     */
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cart->load(['items.product.category']);

        // حساب الإجماليات
        $subtotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $tax = $subtotal * 0.14; // ضريبة 14%
        $shipping = 0; // شحن مجاني للمنتجات الرقمية
        $discount = $cart->discount_amount ?? 0;
        $total = $subtotal + $tax + $shipping - $discount;

        return view('cart.index', compact('cart', 'subtotal', 'tax', 'shipping', 'discount', 'total'));
    }

    /**
     * إضافة منتج للسلة
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = $this->getOrCreateCart();

        // التحقق من وجود المنتج في السلة
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            // تحديث الكمية
            $cartItem->quantity += $request->quantity;
            $cartItem->total_price = $cartItem->quantity * $cartItem->price;
            $cartItem->save();
        } else {
            // إضافة منتج جديد
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->current_price,
                'total_price' => $request->quantity * $product->current_price,
            ]);
        }

        $cart->updateActivity();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المنتج للسلة بنجاح',
            'cart_count' => $cart->items->sum('quantity')
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem->setQuantity($request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكمية بنجاح'
        ]);
    }

    /**
     * حذف منتج من السلة
     */
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المنتج من السلة'
        ]);
    }

    /**
     * تطبيق كوبون خصم
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cart = $this->getOrCreateCart();

        // البحث عن الكوبون
        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم غير صحيح'
            ], 400);
        }

        // التحقق من صحة الكوبون
        if (!$coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'كود الخصم غير صالح أو منتهي الصلاحية'
            ], 400);
        }

        // التحقق من إمكانية استخدام الكوبون للمستخدم الحالي
        if (Auth::check() && !$coupon->canBeUsedBy(Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك استخدام هذا الكوبون'
            ], 400);
        }

        // حساب المجموع الفرعي للسلة
        $subtotal = $cart->items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        // التحقق من الحد الأدنى للطلب
        if ($subtotal < $coupon->minimum_amount) {
            return response()->json([
                'success' => false,
                'message' => 'الحد الأدنى للطلب لاستخدام هذا الكوبون هو ' . number_format($coupon->minimum_amount, 2) . ' $'
            ], 400);
        }

        // حساب مبلغ الخصم
        $discountAmount = $coupon->calculateDiscount($subtotal);

        if ($discountAmount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تطبيق هذا الكوبون على طلبك الحالي'
            ], 400);
        }

        // تحديث السلة بالكوبون والخصم
        $cart->update([
            'coupon_code' => $coupon->code,
            'discount_amount' => $discountAmount,
        ]);

        // تسجيل استخدام الكوبون
        if (Auth::check()) {
            CouponUsage::create([
                'coupon_id' => $coupon->id,
                'user_id' => Auth::id(),
                'cart_id' => $cart->id,
                'discount_amount' => $discountAmount,
                'used_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تطبيق كود الخصم بنجاح',
            'discount' => $discountAmount,
            'coupon_name' => $coupon->name,
            'coupon_type' => $coupon->type,
            'coupon_value' => $coupon->value,
        ]);
    }

    /**
     * إزالة كوبون الخصم
     */
    public function removeCoupon(Request $request)
    {
        $cart = $this->getOrCreateCart();

        $cart->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة كود الخصم بنجاح'
        ]);
    }

    /**
     * الحصول على أو إنشاء سلة للمستخدم الحالي
     */
    private function getOrCreateCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Session::getId()]
            );
        } else {
            $cart = Cart::firstOrCreate(
                ['session_id' => Session::getId()]
            );
        }

        return $cart;
    }
}

