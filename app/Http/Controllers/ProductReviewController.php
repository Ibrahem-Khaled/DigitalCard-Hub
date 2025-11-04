<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductReviewController extends Controller
{
    /**
     * عرض نموذج إضافة تقييم
     */
    public function create(Product $product): View
    {
        $user = Auth::user();
        
        // التحقق من أن المستخدم قد اشترى المنتج
        $hasPurchased = $this->hasUserPurchasedProduct($user->id, $product->id);
        
        if (!$hasPurchased) {
            abort(403, 'يجب أن تكون قد اشتريت هذا المنتج لتتمكن من تقييمه');
        }

        // التحقق من وجود تقييم سابق
        $existingReview = ProductReview::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        return view('products.review-form', compact('product', 'existingReview'));
    }

    /**
     * حفظ التقييم
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        // التحقق من أن المستخدم قد اشترى المنتج
        $hasPurchased = $this->hasUserPurchasedProduct($user->id, $product->id);
        
        if (!$hasPurchased) {
            return redirect()->back()
                ->with('error', 'يجب أن تكون قد اشتريت هذا المنتج لتتمكن من تقييمه');
        }

        // التحقق من وجود تقييم سابق
        $existingReview = ProductReview::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'لقد قمت بتقييم هذا المنتج مسبقاً');
        }

        // الحصول على order_id من أول طلب يحتوي على هذا المنتج
        $orderItem = OrderItem::whereHas('order', function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->whereIn('status', ['delivered', 'shipped', 'processing']);
        })
        ->where('product_id', $product->id)
        ->first();

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'يجب اختيار تقييم',
            'rating.min' => 'التقييم يجب أن يكون على الأقل نجمة واحدة',
            'rating.max' => 'التقييم يجب أن يكون على الأكثر 5 نجوم',
        ]);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_id' => $orderItem ? $orderItem->order_id : null,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified' => $orderItem ? true : false,
            'is_approved' => true, // الموافقة تلقائياً للتقارير
            'status' => 'approved',
        ]);

        return redirect()->route('products.show', $product->slug)
            ->with('success', 'تم إضافة تقييمك بنجاح');
    }

    /**
     * تحديث التقييم
     */
    public function update(Request $request, ProductReview $review): RedirectResponse
    {
        $user = Auth::user();

        // التحقق من أن التقييم يخص المستخدم الحالي
        if ($review->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
        ], [
            'rating.required' => 'يجب اختيار تقييم',
            'rating.min' => 'التقييم يجب أن يكون على الأقل نجمة واحدة',
            'rating.max' => 'التقييم يجب أن يكون على الأكثر 5 نجوم',
        ]);

        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'status' => 'pending', // إعادة المراجعة بعد التعديل
            'is_approved' => false,
        ]);

        return redirect()->route('products.show', $review->product->slug)
            ->with('success', 'تم تحديث تقييمك بنجاح');
    }

    /**
     * حذف التقييم
     */
    public function destroy(ProductReview $review): RedirectResponse
    {
        $user = Auth::user();

        // التحقق من أن التقييم يخص المستخدم الحالي
        if ($review->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذا التقييم');
        }

        $productSlug = $review->product->slug;
        $review->delete();

        return redirect()->route('products.show', $productSlug)
            ->with('success', 'تم حذف تقييمك بنجاح');
    }

    /**
     * التحقق من أن المستخدم قد اشترى المنتج
     */
    private function hasUserPurchasedProduct(int $userId, int $productId): bool
    {
        return OrderItem::whereHas('order', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['delivered', 'shipped', 'processing', 'pending']);
        })
        ->where('product_id', $productId)
        ->exists();
    }

    /**
     * الحصول على أول طلب يحتوي على المنتج
     */
    public function getOrderForProduct(int $userId, int $productId): ?Order
    {
        $orderItem = OrderItem::whereHas('order', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->whereIn('status', ['delivered', 'shipped', 'processing']);
        })
        ->where('product_id', $productId)
        ->first();

        return $orderItem ? $orderItem->order : null;
    }
}

