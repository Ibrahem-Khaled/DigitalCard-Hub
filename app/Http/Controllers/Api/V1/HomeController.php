<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\SliderResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseController
{
    /**
     * Get homepage data
     */
    public function index(Request $request)
    {
        // Get sliders for homepage
        $sliders = Slider::getHomepageSliders();

        // Get main categories (parent categories)
        $categories = Category::active()
            ->whereNull('parent_id')
            ->withCount('products')
            ->with('children')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        // Get featured products
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->take(8)
            ->get();

        // Get best selling products (based on order items count)
        $bestSellingProducts = Product::active()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(8)
            ->get();

        // Get new products (latest)
        $newProducts = Product::active()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->latest()
            ->take(8)
            ->get();

        // Get sale products (products with sale_price)
        $saleProducts = Product::active()
            ->onSale()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->orderByRaw('((price - COALESCE(sale_price, price)) / price * 100) DESC')
            ->take(8)
            ->get();

        // Get top rated products (based on reviews)
        $topRatedProducts = Product::active()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->having('reviews_avg_rating', '>=', 4)
            ->orderBy('reviews_avg_rating', 'desc')
            ->orderBy('reviews_count', 'desc')
            ->take(8)
            ->get();

        // Get statistics
        $stats = [
            'total_products' => Product::active()->count(),
            'total_categories' => Category::active()->count(),
            'total_orders' => DB::table('orders')->where('payment_status', 'completed')->count(),
            'total_customers' => DB::table('users')->where('is_active', true)->count(),
        ];

        return $this->successResponse([
            'sliders' => SliderResource::collection($sliders),
            'categories' => CategoryResource::collection($categories),
            'featured_products' => ProductResource::collection($featuredProducts),
            'best_selling_products' => ProductResource::collection($bestSellingProducts),
            'new_products' => ProductResource::collection($newProducts),
            'sale_products' => ProductResource::collection($saleProducts),
            'top_rated_products' => ProductResource::collection($topRatedProducts),
            'statistics' => $stats,
        ]);
    }
}

