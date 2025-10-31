<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية
     */
    public function index()
    {
        // جلب السلايدرات للصفحة الرئيسية
        $sliders = Slider::getHomepageSliders();

        // جلب الفئات النشطة
        $categories = Category::active()
            ->orderBy('sort_order')
            ->with(['products' => function($query) {
                $query->active()->take(4);
            }])
            ->get();

        // جلب المنتجات المميزة
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->take(8)
            ->get();

        // جلب الأكثر مبيعاً (بناءً على عدد الطلبات)
        $bestSellers = Product::active()
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->with('category')
            ->take(8)
            ->get();

        // جلب أحدث المنتجات
        $newProducts = Product::active()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        // جلب المنتجات المخفضة
        $saleProducts = Product::active()
            ->onSale()
            ->with('category')
            ->take(8)
            ->get();

        // إحصائيات للعرض
        $stats = [
            'total_products' => Product::active()->count(),
            'total_categories' => Category::active()->count(),
            'total_sales' => DB::table('order_items')->count(),
        ];

        return view('home', compact(
            'sliders',
            'categories',
            'featuredProducts',
            'bestSellers',
            'newProducts',
            'saleProducts',
            'stats'
        ));
    }
}

