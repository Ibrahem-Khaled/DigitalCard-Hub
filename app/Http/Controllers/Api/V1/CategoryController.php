<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'all'); // all, parent, child
        
        $query = Category::active()->orderBy('sort_order');
        
        if ($type === 'parent') {
            $query->whereNull('parent_id');
        } elseif ($type === 'child') {
            $query->whereNotNull('parent_id');
        }
        
        $categories = $query
            ->withCount('products')
            ->with('parent')
            ->withCount('children')
            ->get();

        return $this->successResponse(CategoryResource::collection($categories));
    }

    /**
     * Get single category
     */
    public function show($id)
    {
        $category = Category::active()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->withCount('products')
            ->with(['parent', 'children'])
            ->withCount('children')
            ->first();

        if (!$category) {
            return $this->notFoundResponse('الفئة غير موجودة');
        }

        return $this->successResponse(new CategoryResource($category));
    }

    /**
     * Get category products
     */
    public function products(Request $request, $id)
    {
        $category = Category::active()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->first();

        if (!$category) {
            return $this->notFoundResponse('الفئة غير موجودة');
        }

        $query = $category->products()->active()
            ->with('category')
            ->withCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }]);

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
            default:
                $query->latest();
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return $this->paginatedResponse($products, ProductResource::class);
    }
}


