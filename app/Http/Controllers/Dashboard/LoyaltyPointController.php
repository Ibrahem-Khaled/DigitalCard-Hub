<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyPointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoyaltyPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LoyaltyPoint::with(['user', 'transactions']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('source', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // فلترة حسب المصدر
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // فلترة حسب المستخدم
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where(function ($q) {
                          $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                      });
            } elseif ($request->status === 'expired') {
                $query->where(function ($q) {
                    $q->where('is_active', false)
                      ->orWhere('expires_at', '<=', now());
                });
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
        $allowedSortFields = ['created_at', 'points', 'type', 'source', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $loyaltyPoints = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_points' => LoyaltyPoint::sum('points'),
            'total_value_usd' => LoyaltyPoint::sum('total_value_usd'),
            'active_points' => LoyaltyPoint::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->sum('points'),
            'active_value_usd' => LoyaltyPoint::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->sum('total_value_usd'),
            'earned_points' => LoyaltyPoint::where('type', 'earned')->sum('points'),
            'earned_value_usd' => LoyaltyPoint::where('type', 'earned')->sum('total_value_usd'),
            'redeemed_points' => LoyaltyPoint::where('type', 'redeemed')->sum('points'),
            'redeemed_value_usd' => LoyaltyPoint::where('type', 'redeemed')->sum('total_value_usd'),
            'expired_points' => LoyaltyPoint::where('type', 'expired')->sum('points'),
            'expired_value_usd' => LoyaltyPoint::where('type', 'expired')->sum('total_value_usd'),
            'bonus_points' => LoyaltyPoint::where('type', 'bonus')->sum('points'),
            'bonus_value_usd' => LoyaltyPoint::where('type', 'bonus')->sum('total_value_usd'),
            'recent_points' => LoyaltyPoint::where('created_at', '>=', now()->subDays(7))->sum('points'),
            'recent_value_usd' => LoyaltyPoint::where('created_at', '>=', now()->subDays(7))->sum('total_value_usd'),
            'top_users' => LoyaltyPoint::select('user_id', DB::raw('sum(points) as total_points'), DB::raw('sum(total_value_usd) as total_value_usd'))
                ->with('user')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->groupBy('user_id')
                ->orderBy('total_value_usd', 'desc')
                ->limit(5)
                ->get(),
        ];

        // المستخدمين للفلترة
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.loyalty-points.index', compact('loyaltyPoints', 'stats', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.loyalty-points.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'point_value_usd' => 'required|numeric|min:0.001|max:100',
            'type' => 'required|in:earned,redeemed,expired,bonus',
            'source' => 'required|string|max:255',
            'source_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
        ], [
            'user_id.required' => 'يجب اختيار المستخدم',
            'user_id.exists' => 'المستخدم غير موجود',
            'points.required' => 'يجب إدخال عدد النقاط',
            'points.min' => 'عدد النقاط يجب أن يكون أكبر من صفر',
            'point_value_usd.required' => 'يجب إدخال قيمة النقطة بالدولار',
            'point_value_usd.min' => 'قيمة النقطة يجب أن تكون أكبر من 0.001 دولار',
            'point_value_usd.max' => 'قيمة النقطة يجب أن تكون أقل من 100 دولار',
            'type.required' => 'يجب اختيار نوع النقاط',
            'type.in' => 'نوع النقاط غير صحيح',
            'source.required' => 'يجب إدخال مصدر النقاط',
        ]);

        $loyaltyPointData = $request->except(['expires_at']);

        // حساب القيمة الإجمالية
        $loyaltyPointData['total_value_usd'] = $request->points * $request->point_value_usd;

        // تحويل التاريخ
        if ($request->expires_at) {
            $loyaltyPointData['expires_at'] = $request->expires_at;
        }

        // تحديد النقاط حسب النوع
        if ($request->type === 'redeemed' || $request->type === 'expired') {
            $loyaltyPointData['points'] = -abs($request->points);
            $loyaltyPointData['total_value_usd'] = -abs($loyaltyPointData['total_value_usd']);
        }

        $loyaltyPoint = LoyaltyPoint::create($loyaltyPointData);

        // إنشاء معاملة
        $this->createTransaction($loyaltyPoint);

        return redirect()->route('dashboard.loyalty-points.index')
            ->with('success', 'تم إنشاء نقاط الولاء بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(LoyaltyPoint $loyaltyPoint)
    {
        $loyaltyPoint->load(['user', 'transactions']);

        // إحصائيات النقاط
        $pointStats = [
            'total_points' => $loyaltyPoint->points,
            'point_value_usd' => $loyaltyPoint->point_value_usd,
            'total_value_usd' => $loyaltyPoint->total_value_usd,
            'is_active' => $loyaltyPoint->isActive(),
            'is_expired' => $loyaltyPoint->isExpired(),
            'days_since_created' => $loyaltyPoint->created_at->diffInDays(now()),
            'user_total_points' => LoyaltyPoint::getTotalPointsForUser($loyaltyPoint->user_id),
            'user_total_value' => LoyaltyPoint::getTotalValueForUser($loyaltyPoint->user_id),
            'user_earned_points' => LoyaltyPoint::forUser($loyaltyPoint->user_id)->where('type', 'earned')->sum('points'),
            'user_redeemed_points' => LoyaltyPoint::forUser($loyaltyPoint->user_id)->where('type', 'redeemed')->sum('points'),
        ];

        // المعاملات المرتبطة
        $transactions = $loyaltyPoint->transactions()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.loyalty-points.show', compact('loyaltyPoint', 'pointStats', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoyaltyPoint $loyaltyPoint)
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.loyalty-points.edit', compact('loyaltyPoint', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LoyaltyPoint $loyaltyPoint)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'point_value_usd' => 'required|numeric|min:0.001|max:100',
            'type' => 'required|in:earned,redeemed,expired,bonus',
            'source' => 'required|string|max:255',
            'source_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ], [
            'user_id.required' => 'يجب اختيار المستخدم',
            'user_id.exists' => 'المستخدم غير موجود',
            'points.required' => 'يجب إدخال عدد النقاط',
            'points.min' => 'عدد النقاط يجب أن يكون أكبر من صفر',
            'point_value_usd.required' => 'يجب إدخال قيمة النقطة بالدولار',
            'point_value_usd.min' => 'قيمة النقطة يجب أن تكون أكبر من 0.001 دولار',
            'point_value_usd.max' => 'قيمة النقطة يجب أن تكون أقل من 100 دولار',
            'type.required' => 'يجب اختيار نوع النقاط',
            'type.in' => 'نوع النقاط غير صحيح',
            'source.required' => 'يجب إدخال مصدر النقاط',
        ]);

        $loyaltyPointData = $request->except(['expires_at']);

        // حساب القيمة الإجمالية
        $loyaltyPointData['total_value_usd'] = $request->points * $request->point_value_usd;

        // تحويل التاريخ
        if ($request->expires_at) {
            $loyaltyPointData['expires_at'] = $request->expires_at;
        } else {
            $loyaltyPointData['expires_at'] = null;
        }

        // تحديد النقاط حسب النوع
        if ($request->type === 'redeemed' || $request->type === 'expired') {
            $loyaltyPointData['points'] = -abs($request->points);
            $loyaltyPointData['total_value_usd'] = -abs($loyaltyPointData['total_value_usd']);
        }

        $loyaltyPoint->update($loyaltyPointData);

        return redirect()->route('dashboard.loyalty-points.index')
            ->with('success', 'تم تحديث نقاط الولاء بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoyaltyPoint $loyaltyPoint)
    {
        // منع حذف النقاط التي لها معاملات
        if ($loyaltyPoint->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف النقاط التي لها معاملات مرتبطة');
        }

        $loyaltyPoint->delete();

        return redirect()->route('dashboard.loyalty-points.index')
            ->with('success', 'تم حذف نقاط الولاء بنجاح');
    }

    /**
     * Toggle points status.
     */
    public function toggleStatus(LoyaltyPoint $loyaltyPoint)
    {
        $loyaltyPoint->update(['is_active' => !$loyaltyPoint->is_active]);

        $status = $loyaltyPoint->is_active ? 'تفعيل' : 'إلغاء تفعيل';

        return redirect()->back()
            ->with('success', "تم {$status} النقاط بنجاح");
    }

    /**
     * Mark points as expired.
     */
    public function markExpired(LoyaltyPoint $loyaltyPoint)
    {
        if ($loyaltyPoint->isExpired()) {
            return redirect()->back()
                ->with('error', 'النقاط منتهية الصلاحية بالفعل');
        }

        $loyaltyPoint->update([
            'is_active' => false,
            'type' => 'expired',
            'expires_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'تم وضع علامة على النقاط كمنتهية الصلاحية');
    }

    /**
     * Add points to user.
     */
    public function addPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'point_value_usd' => 'required|numeric|min:0.001|max:100',
            'type' => 'required|in:earned,bonus',
            'source' => 'required|string|max:255',
            'description' => 'nullable|string',
            'expires_at' => 'nullable|date|after:today',
        ]);

        $loyaltyPoint = LoyaltyPoint::addPoints(
            $request->user_id,
            $request->points,
            $request->type,
            $request->source,
            null,
            $request->description,
            $request->point_value_usd
        );

        if ($request->expires_at) {
            $loyaltyPoint->update(['expires_at' => $request->expires_at]);
        }

        // إنشاء معاملة
        $this->createTransaction($loyaltyPoint);

        return redirect()->back()
            ->with('success', 'تم إضافة النقاط بنجاح');
    }

    /**
     * Deduct points from user.
     */
    public function deductPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'point_value_usd' => 'required|numeric|min:0.001|max:100',
            'type' => 'required|in:redeemed,expired',
            'source' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // التحقق من وجود نقاط كافية
        $totalPoints = LoyaltyPoint::getTotalPointsForUser($request->user_id);
        if ($totalPoints < $request->points) {
            return redirect()->back()
                ->with('error', 'النقاط المتاحة غير كافية');
        }

        $loyaltyPoint = LoyaltyPoint::deductPoints(
            $request->user_id,
            $request->points,
            $request->type,
            $request->source,
            null,
            $request->description,
            $request->point_value_usd
        );

        // إنشاء معاملة
        $this->createTransaction($loyaltyPoint);

        return redirect()->back()
            ->with('success', 'تم خصم النقاط بنجاح');
    }

    /**
     * Export loyalty points to CSV.
     */
    public function export(Request $request)
    {
        $query = LoyaltyPoint::with(['user']);

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('source', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $loyaltyPoints = $query->get();

        $filename = 'loyalty_points_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($loyaltyPoints) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'المستخدم',
                'النقاط',
                'النوع',
                'المصدر',
                'الوصف',
                'تاريخ الانتهاء',
                'الحالة',
                'تاريخ الإنشاء'
            ]);

            foreach ($loyaltyPoints as $point) {
                fputcsv($file, [
                    $point->user->full_name ?? 'غير محدد',
                    $point->points,
                    $this->getTypeText($point->type),
                    $point->source,
                    $point->description ?? 'لا يوجد وصف',
                    $point->expires_at?->format('Y-m-d H:i:s'),
                    $point->is_active ? 'نشط' : 'غير نشط',
                    $point->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get loyalty points statistics.
     */
    public function stats()
    {
        $stats = [
            'total_points' => LoyaltyPoint::sum('points'),
            'active_points' => LoyaltyPoint::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->sum('points'),
            'earned_points' => LoyaltyPoint::where('type', 'earned')->sum('points'),
            'redeemed_points' => LoyaltyPoint::where('type', 'redeemed')->sum('points'),
            'expired_points' => LoyaltyPoint::where('type', 'expired')->sum('points'),
            'bonus_points' => LoyaltyPoint::where('type', 'bonus')->sum('points'),
            'points_last_24h' => LoyaltyPoint::where('created_at', '>=', now()->subDay())->sum('points'),
            'points_last_week' => LoyaltyPoint::where('created_at', '>=', now()->subWeek())->sum('points'),
            'points_last_month' => LoyaltyPoint::where('created_at', '>=', now()->subMonth())->sum('points'),
        ];

        return response()->json($stats);
    }

    /**
     * Create transaction for loyalty point.
     */
    private function createTransaction(LoyaltyPoint $loyaltyPoint)
    {
        $balanceBefore = LoyaltyPoint::getTotalPointsForUser($loyaltyPoint->user_id) - $loyaltyPoint->points;
        $balanceAfter = LoyaltyPoint::getTotalPointsForUser($loyaltyPoint->user_id);

        LoyaltyPointTransaction::create([
            'loyalty_point_id' => $loyaltyPoint->id,
            'user_id' => $loyaltyPoint->user_id,
            'points' => $loyaltyPoint->points,
            'type' => $loyaltyPoint->type,
            'source' => $loyaltyPoint->source,
            'source_id' => $loyaltyPoint->source_id,
            'description' => $loyaltyPoint->description,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'processed_at' => now(),
        ]);
    }

    /**
     * Get type text in Arabic.
     */
    private function getTypeText($type)
    {
        return match ($type) {
            'earned' => 'مكتسب',
            'redeemed' => 'مسترد',
            'expired' => 'منتهي',
            'bonus' => 'مكافأة',
            default => 'غير محدد',
        };
    }
}
