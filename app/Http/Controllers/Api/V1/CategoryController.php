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
        $categories = Category::active()
            ->orderBy('sort_order')
            ->withCount('products')
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

        $query = $category->products()->active()->with('category');

        // Sort
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('current_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('current_price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        return $this->paginatedResponse($products);
    }
}


