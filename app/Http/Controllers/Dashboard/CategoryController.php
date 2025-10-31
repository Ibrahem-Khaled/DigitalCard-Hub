<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::with(['parent', 'children']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فلترة حسب النوع (فئة رئيسية أو فرعية)
        if ($request->filled('type')) {
            if ($request->type === 'parent') {
                $query->whereNull('parent_id');
            } elseif ($request->type === 'child') {
                $query->whereNotNull('parent_id');
            }
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['sort_order', 'name', 'created_at', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'sort_order';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $categories = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'parent_categories' => Category::whereNull('parent_id')->count(),
            'child_categories' => Category::whereNotNull('parent_id')->count(),
        ];

        return view('dashboard.categories.index', compact('categories', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->where('is_active', true)->orderBy('name')->get();
        return view('dashboard.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $categoryData = $request->except(['image']);

        // إنشاء slug تلقائياً
        $categoryData['slug'] = Str::slug($request->name);

        // التأكد من أن slug فريد
        $originalSlug = $categoryData['slug'];
        $counter = 1;
        while (Category::where('slug', $categoryData['slug'])->exists()) {
            $categoryData['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // رفع الصورة
        if ($request->hasFile('image')) {
            $categoryData['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($categoryData);

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'تم إنشاء الفئة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);

        return view('dashboard.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('dashboard.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $categoryData = $request->except(['image']);

        // تحديث slug إذا تغير الاسم
        if ($request->name !== $category->name) {
            $categoryData['slug'] = Str::slug($request->name);

            // التأكد من أن slug فريد
            $originalSlug = $categoryData['slug'];
            $counter = 1;
            while (Category::where('slug', $categoryData['slug'])->where('id', '!=', $category->id)->exists()) {
                $categoryData['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // رفع الصورة الجديدة
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $categoryData['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($categoryData);

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'تم تحديث الفئة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // حذف الصورة
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'تم حذف الفئة بنجاح');
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} الفئة بنجاح");
    }

    /**
     * Update sort order for categories.
     */
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->categories as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Export categories to CSV.
     */
    public function export()
    {
        $categories = Category::with(['parent'])->get();

        $filename = 'categories_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'الاسم',
                'الوصف',
                'الفئة الرئيسية',
                'الحالة',
                'ترتيب العرض',
                'تاريخ الإنشاء'
            ]);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->name,
                    $category->description,
                    $category->parent ? $category->parent->name : 'فئة رئيسية',
                    $category->is_active ? 'نشط' : 'معطل',
                    $category->sort_order,
                    $category->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
