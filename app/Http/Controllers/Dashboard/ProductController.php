<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الفئة
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            if ($request->type === 'digital') {
                $query->where('is_digital', true);
            } elseif ($request->type === 'physical') {
                $query->where('is_digital', false);
            }
        }

        // فلترة حسب المميز
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->featured === 'yes');
        }

        // فلترة حسب المخزون (إزالة - غير مناسب للبطاقات الرقمية)
        // if ($request->filled('stock')) {
        //     if ($request->stock === 'low') {
        //         $query->whereRaw('stock_quantity <= low_stock_threshold');
        //     } elseif ($request->stock === 'out') {
        //         $query->where('stock_quantity', '<=', 0);
        //     }
        // }

        // فلترة حسب نوع البطاقة
        if ($request->filled('card_type')) {
            $query->where('card_type', $request->card_type);
        }

        // فلترة حسب مزود البطاقة
        if ($request->filled('card_provider')) {
            $query->where('card_provider', $request->card_provider);
        }

        // فلترة حسب المنطقة
        if ($request->filled('card_region')) {
            $query->where('card_region', $request->card_region);
        }

        // فلترة حسب نقاط الولاء
        if ($request->filled('loyalty_points')) {
            if ($request->loyalty_points === 'earn') {
                $query->where('loyalty_points_earn', '>', 0);
            } elseif ($request->loyalty_points === 'cost') {
                $query->where('loyalty_points_cost', '>', 0);
            }
        }

        // فلترة حسب التسليم الفوري
        if ($request->filled('instant_delivery')) {
            $query->where('is_instant_delivery', $request->instant_delivery === 'yes');
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'name', 'price', 'stock_quantity', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'digital_products' => Product::where('is_digital', true)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'instant_delivery_products' => Product::where('is_instant_delivery', true)->count(),
            'products_with_loyalty_points' => Product::where('loyalty_points_earn', '>', 0)->count(),
            'purchasable_with_points' => Product::where('loyalty_points_cost', '>', 0)->count(),
            'card_types_count' => Product::distinct('card_type')->count('card_type'),
            'card_providers_count' => Product::distinct('card_provider')->count('card_provider'),
        ];

        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.products.index', compact('products', 'stats', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('dashboard.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_digital' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $productData = $request->except(['image', 'gallery']);

        // إنشاء slug تلقائياً
        $productData['slug'] = Str::slug($request->name);

        // التأكد من أن slug فريد
        $originalSlug = $productData['slug'];
        $counter = 1;
        while (Product::where('slug', $productData['slug'])->exists()) {
            $productData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // رفع الصورة الرئيسية
        if ($request->hasFile('image')) {
            $productData['image'] = $request->file('image')->store('products', 'public');
        }

        // رفع معرض الصور
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $productData['gallery'] = $galleryPaths;
        }

        Product::create($productData);

        return redirect()->route('dashboard.products.index')
            ->with('success', 'تم إنشاء المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'digitalCards', 'reviews']);

        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_digital' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|array',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'tags' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $productData = $request->except(['image', 'gallery']);

        // تحديث slug إذا تغير الاسم
        if ($request->name !== $product->name) {
            $productData['slug'] = Str::slug($request->name);

            // التأكد من أن slug فريد
            $originalSlug = $productData['slug'];
            $counter = 1;
            while (Product::where('slug', $productData['slug'])->where('id', '!=', $product->id)->exists()) {
                $productData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // رفع الصورة الجديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $productData['image'] = $request->file('image')->store('products', 'public');
        }

        // رفع معرض الصور الجديد
        if ($request->hasFile('gallery')) {
            // حذف الصور القديمة
            if ($product->gallery) {
                foreach ($product->gallery as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $productData['gallery'] = $galleryPaths;
        }

        $product->update($productData);

        return redirect()->route('dashboard.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // حذف الصورة الرئيسية
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // حذف معرض الصور
        if ($product->gallery) {
            foreach ($product->gallery as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $product->delete();

        return redirect()->route('dashboard.products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * Toggle product active status.
     */
    public function toggleStatus(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} المنتج بنجاح");
    }

    /**
     * Toggle product featured status.
     */
    public function toggleFeatured(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);

        $status = $product->is_featured ? 'تمييز' : 'إلغاء تمييز';

        return redirect()->back()
            ->with('success', "تم {$status} المنتج بنجاح");
    }

    /**
     * Update stock quantity.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $product->update(['stock_quantity' => $request->stock_quantity]);

        return redirect()->back()
            ->with('success', 'تم تحديث كمية المخزون بنجاح');
    }

    /**
     * Export products to CSV.
     */
    public function export()
    {
        $products = Product::with(['category'])->get();

        $filename = 'products_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'الاسم',
                'SKU',
                'الفئة',
                'السعر',
                'سعر البيع',
                'العلامة التجارية',
                'النوع',
                'المخزون',
                'الحالة',
                'مميز',
                'تاريخ الإنشاء'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->sku,
                    $product->category->name,
                    $product->price,
                    $product->sale_price,
                    $product->brand,
                    $product->is_digital ? 'رقمي' : 'مادي',
                    $product->stock_quantity,
                    $product->is_active ? 'نشط' : 'معطل',
                    $product->is_featured ? 'نعم' : 'لا',
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
