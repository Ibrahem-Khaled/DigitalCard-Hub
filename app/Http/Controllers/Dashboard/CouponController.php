<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            } elseif ($request->status === 'valid') {
                $query->valid();
            }
        }

        // فلترة حسب الفترة
        if ($request->filled('period')) {
            $days = match ($request->period) {
                'week' => 7,
                'month' => 30,
                'quarter' => 90,
                'year' => 365,
                default => 30
            };
            $query->where('created_at', '>=', now()->subDays($days));
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'code', 'used_count', 'expires_at', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $coupons = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::active()->count(),
            'expired_coupons' => Coupon::expired()->count(),
            'valid_coupons' => Coupon::valid()->count(),
            'total_usage' => CouponUsage::count(),
            'total_discount_given' => CouponUsage::sum('discount_amount'),
            'recent_coupons' => Coupon::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('dashboard.coupons.index', compact('coupons', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.coupons.create', compact('products', 'categories', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'user_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'applicable_users' => 'nullable|array',
            'applicable_users.*' => 'exists:users,id',
            'first_time_only' => 'boolean',
            'stackable' => 'boolean',
        ]);

        $couponData = $request->except(['applicable_products', 'applicable_categories', 'applicable_users']);

        // تحويل التواريخ
        if ($request->starts_at) {
            $couponData['starts_at'] = $request->starts_at;
        }
        if ($request->expires_at) {
            $couponData['expires_at'] = $request->expires_at;
        }

        // تحويل المصفوفات إلى JSON
        $couponData['applicable_products'] = $request->applicable_products ?: null;
        $couponData['applicable_categories'] = $request->applicable_categories ?: null;
        $couponData['applicable_users'] = $request->applicable_users ?: null;

        Coupon::create($couponData);

        return redirect()->route('dashboard.coupons.index')
            ->with('success', 'تم إنشاء الكوبون بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['usages.user', 'carts.user', 'orders.user']);

        // إحصائيات الكوبون
        $couponStats = [
            'total_usage' => $coupon->usages->count(),
            'total_discount_given' => $coupon->usages->sum('discount_amount'),
            'unique_users' => $coupon->usages->groupBy('user_id')->count(),
            'usage_rate' => $coupon->usage_limit ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0,
            'avg_discount_per_use' => $coupon->usages->avg('discount_amount'),
            'recent_usage' => $coupon->usages()->where('used_at', '>=', now()->subDays(30))->count(),
        ];

        // الاستخدامات الحديثة
        $recentUsages = $coupon->usages()
            ->with('user')
            ->orderBy('used_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.coupons.show', compact('coupon', 'couponStats', 'recentUsages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'name')->get();
        $categories = Category::select('id', 'name')->get();
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.coupons.edit', compact('coupon', 'products', 'categories', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')->ignore($coupon->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'user_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'applicable_products' => 'nullable|array',
            'applicable_products.*' => 'exists:products,id',
            'applicable_categories' => 'nullable|array',
            'applicable_categories.*' => 'exists:categories,id',
            'applicable_users' => 'nullable|array',
            'applicable_users.*' => 'exists:users,id',
            'first_time_only' => 'boolean',
            'stackable' => 'boolean',
        ]);

        $couponData = $request->except(['applicable_products', 'applicable_categories', 'applicable_users']);

        // تحويل التواريخ
        if ($request->starts_at) {
            $couponData['starts_at'] = $request->starts_at;
        } else {
            $couponData['starts_at'] = null;
        }

        if ($request->expires_at) {
            $couponData['expires_at'] = $request->expires_at;
        } else {
            $couponData['expires_at'] = null;
        }

        // تحويل المصفوفات إلى JSON
        $couponData['applicable_products'] = $request->applicable_products ?: null;
        $couponData['applicable_categories'] = $request->applicable_categories ?: null;
        $couponData['applicable_users'] = $request->applicable_users ?: null;

        $coupon->update($couponData);

        return redirect()->route('dashboard.coupons.index')
            ->with('success', 'تم تحديث الكوبون بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        // منع حذف الكوبونات المستخدمة
        if ($coupon->usages()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الكوبون المستخدم');
        }

        $coupon->delete();

        return redirect()->route('dashboard.coupons.index')
            ->with('success', 'تم حذف الكوبون بنجاح');
    }

    /**
     * Toggle coupon active status.
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} الكوبون بنجاح");
    }

    /**
     * Duplicate coupon.
     */
    public function duplicate(Coupon $coupon)
    {
        $newCoupon = $coupon->replicate();
        $newCoupon->code = $coupon->code . '_COPY_' . time();
        $newCoupon->name = $coupon->name . ' (نسخة)';
        $newCoupon->used_count = 0;
        $newCoupon->is_active = false;
        $newCoupon->save();

        return redirect()->route('dashboard.coupons.edit', $newCoupon)
            ->with('success', 'تم إنشاء نسخة من الكوبون بنجاح');
    }

    /**
     * Export coupons to CSV.
     */
    public function export(Request $request)
    {
        $query = Coupon::withCount('usages');

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'expired') {
                $query->expired();
            }
        }

        $coupons = $query->get();

        $filename = 'coupons_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($coupons) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'الكود',
                'الاسم',
                'النوع',
                'القيمة',
                'الحد الأدنى',
                'الحد الأقصى للخصم',
                'حد الاستخدام',
                'عدد الاستخدامات',
                'حد المستخدم',
                'الحالة',
                'تاريخ البداية',
                'تاريخ الانتهاء',
                'للمرة الأولى فقط',
                'قابل للتجميع',
                'تاريخ الإنشاء'
            ]);

            foreach ($coupons as $coupon) {
                fputcsv($file, [
                    $coupon->code,
                    $coupon->name,
                    $coupon->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت',
                    $coupon->value,
                    $coupon->minimum_amount,
                    $coupon->maximum_discount,
                    $coupon->usage_limit,
                    $coupon->used_count,
                    $coupon->user_limit,
                    $coupon->is_active ? 'نشط' : 'معطل',
                    $coupon->starts_at?->format('Y-m-d H:i:s'),
                    $coupon->expires_at?->format('Y-m-d H:i:s'),
                    $coupon->first_time_only ? 'نعم' : 'لا',
                    $coupon->stackable ? 'نعم' : 'لا',
                    $coupon->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get coupon usage statistics.
     */
    public function usageStats()
    {
        $stats = [
            'total_usage' => CouponUsage::count(),
            'total_discount_given' => CouponUsage::sum('discount_amount'),
            'avg_discount_per_use' => CouponUsage::avg('discount_amount'),
            'usage_last_24h' => CouponUsage::where('used_at', '>=', now()->subDay())->count(),
            'usage_last_week' => CouponUsage::where('used_at', '>=', now()->subWeek())->count(),
            'usage_last_month' => CouponUsage::where('used_at', '>=', now()->subMonth())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Validate coupon code.
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'الكوبون غير موجود'
            ]);
        }

        $isValid = $coupon->isValid();
        $canBeUsed = $request->user_id ? $coupon->canBeUsedBy($request->user_id) : true;
        $discountAmount = $request->cart_total ? $coupon->calculateDiscount($request->cart_total) : 0;

        return response()->json([
            'valid' => $isValid && $canBeUsed,
            'coupon' => $coupon,
            'discount_amount' => $discountAmount,
            'message' => $isValid && $canBeUsed ? 'الكوبون صالح' : 'الكوبون غير صالح'
        ]);
    }
}
