<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\ReviewResource;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    /**
     * Get all products
     */
    public function index(Request $request)
    {
        $query = Product::active()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }]);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by price range
        if ($request->has('min_price') && $request->min_price) {
            $query->whereRaw('COALESCE(sale_price, price) >= ?', [$request->min_price]);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->whereRaw('COALESCE(sale_price, price) <= ?', [$request->max_price]);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('card_provider', 'like', "%{$search}%")
                  ->orWhereJsonContains('tags', $search);
            });
        }

        // Filter by featured
        if ($request->has('featured') && $request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('orderItems')
                      ->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return $this->paginatedResponse($products);
    }

    /**
     * Get single product
     */
    public function show($id)
    {
        $product = Product::active()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->with(['category', 'reviews.user'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->first();

        if (!$product) {
            return $this->notFoundResponse('المنتج غير موجود');
        }

        return $this->successResponse(new ProductResource($product));
    }

    /**
     * Search products
     */
    public function search(Request $request, $query)
    {
        $products = Product::active()
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('brand', 'like', "%{$query}%")
                  ->orWhere('card_provider', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })
            ->with('category')
            ->limit(20)
            ->get();

        return $this->successResponse(ProductResource::collection($products));
    }

    /**
     * Get product reviews
     */
    public function reviews($id)
    {
        $product = Product::findOrFail($id);

        $reviews = $product->reviews()
            ->where('is_approved', true)
            ->with('user')
            ->latest()
            ->paginate(15);

        return $this->paginatedResponse($reviews);
    }

    /**
     * Store product review
     */
    public function storeReview(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        // Check if user already reviewed this product
        $existingReview = ProductReview::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return $this->errorResponse('لقد قمت بتقييم هذا المنتج من قبل', 400);
        }

        $review = ProductReview::create([
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
            'is_verified_purchase' => $product->orderItems()
                ->whereHas('order', function($q) use ($request) {
                    $q->where('user_id', $request->user()->id)
                      ->where('payment_status', 'completed');
                })
                ->exists(),
            'is_approved' => false, // Requires admin approval
        ]);

        return $this->successResponse(new ReviewResource($review->load('user')), 'تم إضافة التقييم بنجاح. سيتم مراجعته قبل النشر', 201);
    }

    /**
     * Update product review
     */
    public function updateReview(Request $request, $review)
    {
        $review = ProductReview::findOrFail($review);

        if ($review->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'comment' => 'sometimes|nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $review->update($request->only(['rating', 'comment']));
        $review->update(['is_approved' => false]); // Requires re-approval

        return $this->successResponse(new ReviewResource($review->load('user')), 'تم تحديث التقييم بنجاح');
    }

    /**
     * Delete product review
     */
    public function deleteReview(Request $request, $review)
    {
        $review = ProductReview::findOrFail($review);

        if ($review->user_id !== $request->user()->id) {
            return $this->forbiddenResponse();
        }

        $review->delete();

        return $this->successResponse(null, 'تم حذف التقييم بنجاح');
    }
}


