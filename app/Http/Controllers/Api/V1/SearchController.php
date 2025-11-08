<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\ProductResource;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Product;
use App\Models\Category;
use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends BaseController
{
    /**
     * Advanced search
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all'); // all, products, categories
        $category = $request->input('category');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $brand = $request->input('brand');
        $cardProvider = $request->input('card_provider');
        $sort = $request->input('sort', 'relevance'); // relevance, price_asc, price_desc, latest, rating
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);

        if (empty($query) && $type === 'all') {
            return $this->errorResponse('يرجى إدخال كلمة البحث', 400);
        }

        $results = [
            'query' => $query,
            'total_results' => 0,
            'products' => [],
            'categories' => [],
            'suggestions' => [],
        ];

        // Search Products
        if ($type === 'all' || $type === 'products') {
            $productsQuery = Product::active()->with('category');

            // Text search
            if (!empty($query)) {
                $productsQuery->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('short_description', 'like', "%{$query}%")
                      ->orWhere('brand', 'like', "%{$query}%")
                      ->orWhere('card_provider', 'like', "%{$query}%")
                      ->orWhere('card_type', 'like', "%{$query}%")
                      ->orWhereJsonContains('tags', $query);
                });
            }

            // Category filter
            if ($category) {
                $productsQuery->whereHas('category', function($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            // Price range
            if ($minPrice) {
                $productsQuery->where('current_price', '>=', $minPrice);
            }
            if ($maxPrice) {
                $productsQuery->where('current_price', '<=', $maxPrice);
            }

            // Brand filter
            if ($brand) {
                $productsQuery->where('brand', 'like', "%{$brand}%");
            }

            // Card provider filter
            if ($cardProvider) {
                $productsQuery->where('card_provider', 'like', "%{$cardProvider}%");
            }

            // Sort
            switch ($sort) {
                case 'price_asc':
                    $productsQuery->orderBy('current_price', 'asc');
                    break;
                case 'price_desc':
                    $productsQuery->orderBy('current_price', 'desc');
                    break;
                case 'latest':
                    $productsQuery->latest();
                    break;
                case 'rating':
                    $productsQuery->orderBy('rating', 'desc');
                    break;
                case 'relevance':
                default:
                    // Relevance: exact match first, then partial
                    if (!empty($query)) {
                        $productsQuery->orderByRaw("
                            CASE 
                                WHEN name LIKE ? THEN 1
                                WHEN name LIKE ? THEN 2
                                WHEN description LIKE ? THEN 3
                                ELSE 4
                            END
                        ", ["{$query}", "%{$query}%", "%{$query}%"]);
                    } else {
                        $productsQuery->latest();
                    }
                    break;
            }

            $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);
            $results['products'] = ProductResource::collection($products->items());
            $results['total_results'] += $products->total();
        }

        // Search Categories
        if ($type === 'all' || $type === 'categories') {
            $categoriesQuery = Category::active();

            if (!empty($query)) {
                $categoriesQuery->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            }

            $categories = $categoriesQuery->limit(10)->get();
            $results['categories'] = CategoryResource::collection($categories);
            $results['total_results'] += $categories->count();
        }

        // Get suggestions
        if (!empty($query)) {
            $results['suggestions'] = $this->getSuggestions($query);
        }

        // Get filters
        $results['filters'] = $this->getAvailableFilters($query);

        // Save search history if user is authenticated
        if ($request->user() && !empty($query)) {
            $this->saveSearchHistory($request->user()->id, $query, $results['total_results'], $request);
        }

        return $this->successResponse($results, 'تم البحث بنجاح');
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('q', '');

        if (empty($query) || strlen($query) < 2) {
            return $this->successResponse(['suggestions' => []]);
        }

        $suggestions = $this->getSuggestions($query);

        return $this->successResponse(['suggestions' => $suggestions]);
    }

    /**
     * Get popular searches
     */
    public function popular(Request $request)
    {
        $limit = $request->input('limit', 10);

        $popularSearches = SearchHistory::select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($item) {
                return [
                    'query' => $item->query,
                    'count' => $item->count,
                ];
            });

        return $this->successResponse(['popular_searches' => $popularSearches]);
    }

    /**
     * Get search history for authenticated user
     */
    public function history(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول لعرض سجل البحث');
        }

        $history = SearchHistory::where('user_id', $request->user()->id)
            ->latest()
            ->limit(20)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'query' => $item->query,
                    'results_count' => $item->results_count,
                    'created_at' => $item->created_at->toIso8601String(),
                ];
            });

        return $this->successResponse(['history' => $history]);
    }

    /**
     * Clear search history
     */
    public function clearHistory(Request $request)
    {
        if (!$request->user()) {
            return $this->unauthorizedResponse('يجب تسجيل الدخول');
        }

        SearchHistory::where('user_id', $request->user()->id)->delete();

        return $this->successResponse(null, 'تم حذف سجل البحث بنجاح');
    }

    /**
     * Get suggestions based on query
     */
    private function getSuggestions(string $query): array
    {
        $suggestions = [];

        // Product name suggestions
        $productNames = Product::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(5)
            ->pluck('name')
            ->toArray();

        $suggestions = array_merge($suggestions, $productNames);

        // Brand suggestions
        $brands = Product::active()
            ->where('brand', 'like', "%{$query}%")
            ->distinct()
            ->limit(3)
            ->pluck('brand')
            ->toArray();

        $suggestions = array_merge($suggestions, $brands);

        // Category suggestions
        $categories = Category::active()
            ->where('name', 'like', "%{$query}%")
            ->limit(3)
            ->pluck('name')
            ->toArray();

        $suggestions = array_merge($suggestions, $categories);

        return array_unique(array_slice($suggestions, 0, 10));
    }

    /**
     * Get available filters
     */
    private function getAvailableFilters(?string $query = null): array
    {
        $filters = [];

        // Brands
        $brandsQuery = Product::active();
        if ($query) {
            $brandsQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }
        $filters['brands'] = $brandsQuery->distinct()->pluck('brand')->filter()->values()->toArray();

        // Card Providers
        $providersQuery = Product::active();
        if ($query) {
            $providersQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }
        $filters['card_providers'] = $providersQuery->distinct()->pluck('card_provider')->filter()->values()->toArray();

        // Price range
        $priceQuery = Product::active();
        if ($query) {
            $priceQuery->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            });
        }
        $filters['price_range'] = [
            'min' => (float) $priceQuery->min('current_price'),
            'max' => (float) $priceQuery->max('current_price'),
        ];

        // Categories
        $filters['categories'] = Category::active()
            ->select('id', 'name', 'slug')
            ->get()
            ->map(function($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'slug' => $cat->slug,
                ];
            })
            ->toArray();

        return $filters;
    }

    /**
     * Save search history
     */
    private function saveSearchHistory(int $userId, string $query, int $resultsCount, Request $request): void
    {
        // Check if same search exists in last hour
        $recentSearch = SearchHistory::where('user_id', $userId)
            ->where('query', $query)
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if (!$recentSearch) {
            SearchHistory::create([
                'user_id' => $userId,
                'query' => $query,
                'results_count' => $resultsCount,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } else {
            // Update results count
            $recentSearch->update(['results_count' => $resultsCount]);
        }
    }
}

