<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\ReferralReward;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Referral::with(['referrer', 'referred', 'rewards']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhereHas('referrer', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('referred', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب المستخدم المحيل
        if ($request->filled('referrer_id')) {
            $query->where('referrer_id', $request->referrer_id);
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
        $allowedSortFields = ['created_at', 'referral_code', 'commission_amount', 'status', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $referrals = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_referrals' => Referral::count(),
            'active_referrals' => Referral::active()->count(),
            'completed_referrals' => Referral::completed()->count(),
            'total_commission' => Referral::sum('commission_amount'),
            'total_rewards' => Referral::sum('reward_amount'),
            'recent_referrals' => Referral::where('created_at', '>=', now()->subDays(7))->count(),
            'top_referrers' => Referral::select('referrer_id', DB::raw('count(*) as count'))
                ->with('referrer')
                ->groupBy('referrer_id')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
        ];

        // المستخدمين للفلترة
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();

        return view('dashboard.referrals.index', compact('referrals', 'stats', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.referrals.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'referred_id' => 'required|exists:users,id|different:referrer_id',
            'referral_code' => 'nullable|string|unique:referrals,referral_code',
            'status' => 'required|in:active,completed,expired,cancelled',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_percentage' => 'nullable|numeric|min:0|max:100',
            'expires_at' => 'nullable|date|after:today',
            'notes' => 'nullable|string',
        ], [
            'referrer_id.required' => 'يجب اختيار المستخدم المحيل',
            'referrer_id.exists' => 'المستخدم المحيل غير موجود',
            'referred_id.required' => 'يجب اختيار المستخدم المحال إليه',
            'referred_id.exists' => 'المستخدم المحال إليه غير موجود',
            'referred_id.different' => 'لا يمكن للمستخدم أن يحيل نفسه',
            'referral_code.unique' => 'كود الإحالة موجود مسبقاً',
        ]);

        $referralData = $request->except(['referral_code']);

        // إنشاء كود إحالة فريد إذا لم يتم توفيره
        if (!$request->referral_code) {
            $referralData['referral_code'] = Referral::generateReferralCode();
        } else {
            $referralData['referral_code'] = strtoupper($request->referral_code);
        }

        // تحويل التاريخ
        if ($request->expires_at) {
            $referralData['expires_at'] = $request->expires_at;
        }

        // تحديد تاريخ الإكمال إذا كانت الحالة مكتملة
        if ($request->status === 'completed') {
            $referralData['completed_at'] = now();
        }

        $referral = Referral::create($referralData);

        return redirect()->route('dashboard.referrals.index')
            ->with('success', 'تم إنشاء الإحالة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Referral $referral)
    {
        $referral->load(['referrer', 'referred', 'rewards.user']);

        // إحصائيات الإحالة
        $referralStats = [
            'total_commission' => $referral->commission_amount,
            'total_rewards' => $referral->reward_amount,
            'rewards_count' => $referral->rewards->count(),
            'processed_rewards' => $referral->rewards()->processed()->count(),
            'pending_rewards' => $referral->rewards()->where('status', 'pending')->count(),
            'days_since_created' => $referral->created_at->diffInDays(now()),
            'is_expired' => $referral->isExpired(),
            'is_completed' => $referral->isCompleted(),
        ];

        // المكافآت المرتبطة
        $rewards = $referral->rewards()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.referrals.show', compact('referral', 'referralStats', 'rewards'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Referral $referral)
    {
        $users = User::select('id', 'first_name', 'last_name', 'email')->get();
        return view('dashboard.referrals.edit', compact('referral', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Referral $referral)
    {
        $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'referred_id' => 'required|exists:users,id|different:referrer_id',
            'referral_code' => ['required', 'string', Rule::unique('referrals', 'referral_code')->ignore($referral->id)],
            'status' => 'required|in:active,completed,expired,cancelled',
            'commission_amount' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'reward_amount' => 'nullable|numeric|min:0',
            'reward_percentage' => 'nullable|numeric|min:0|max:100',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ], [
            'referrer_id.required' => 'يجب اختيار المستخدم المحيل',
            'referrer_id.exists' => 'المستخدم المحيل غير موجود',
            'referred_id.required' => 'يجب اختيار المستخدم المحال إليه',
            'referred_id.exists' => 'المستخدم المحال إليه غير موجود',
            'referred_id.different' => 'لا يمكن للمستخدم أن يحيل نفسه',
            'referral_code.unique' => 'كود الإحالة موجود مسبقاً',
        ]);

        $referralData = $request->except(['referral_code']);
        $referralData['referral_code'] = strtoupper($request->referral_code);

        // تحويل التاريخ
        if ($request->expires_at) {
            $referralData['expires_at'] = $request->expires_at;
        } else {
            $referralData['expires_at'] = null;
        }

        // تحديد تاريخ الإكمال إذا كانت الحالة مكتملة ولم تكن مكتملة من قبل
        if ($request->status === 'completed' && !$referral->isCompleted()) {
            $referralData['completed_at'] = now();
        } elseif ($request->status !== 'completed') {
            $referralData['completed_at'] = null;
        }

        $referral->update($referralData);

        return redirect()->route('dashboard.referrals.index')
            ->with('success', 'تم تحديث الإحالة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Referral $referral)
    {
        // منع حذف الإحالات التي لها مكافآت معالجة
        if ($referral->rewards()->processed()->count() > 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف الإحالة التي لها مكافآت معالجة');
        }

        $referral->delete();

        return redirect()->route('dashboard.referrals.index')
            ->with('success', 'تم حذف الإحالة بنجاح');
    }

    /**
     * Mark referral as completed.
     */
    public function markCompleted(Referral $referral)
    {
        if ($referral->isCompleted()) {
            return redirect()->back()
                ->with('error', 'الإحالة مكتملة بالفعل');
        }

        $referral->markAsCompleted();

        return redirect()->back()
            ->with('success', 'تم وضع علامة على الإحالة كمكتملة');
    }

    /**
     * Cancel referral.
     */
    public function cancel(Referral $referral)
    {
        if ($referral->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'الإحالة ملغية بالفعل');
        }

        $referral->update(['status' => 'cancelled']);

        return redirect()->back()
            ->with('success', 'تم إلغاء الإحالة بنجاح');
    }

    /**
     * Generate referral code.
     */
    public function generateCode()
    {
        $code = Referral::generateReferralCode();

        return response()->json(['code' => $code]);
    }

    /**
     * Export referrals to CSV.
     */
    public function export(Request $request)
    {
        $query = Referral::with(['referrer', 'referred']);

        // تطبيق نفس الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                  ->orWhereHas('referrer', function ($subQ) use ($search) {
                      $subQ->where('first_name', 'like', "%{$search}%")
                           ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('referrer_id')) {
            $query->where('referrer_id', $request->referrer_id);
        }

        $referrals = $query->get();

        $filename = 'referrals_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($referrals) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'كود الإحالة',
                'المستخدم المحيل',
                'المستخدم المحال إليه',
                'الحالة',
                'مبلغ العمولة',
                'نسبة العمولة',
                'مبلغ المكافأة',
                'نسبة المكافأة',
                'تاريخ الإكمال',
                'تاريخ الانتهاء',
                'تاريخ الإنشاء'
            ]);

            foreach ($referrals as $referral) {
                fputcsv($file, [
                    $referral->referral_code,
                    $referral->referrer->full_name ?? 'غير محدد',
                    $referral->referred->full_name ?? 'غير محدد',
                    $this->getStatusText($referral->status),
                    $referral->commission_amount,
                    $referral->commission_percentage,
                    $referral->reward_amount,
                    $referral->reward_percentage,
                    $referral->completed_at?->format('Y-m-d H:i:s'),
                    $referral->expires_at?->format('Y-m-d H:i:s'),
                    $referral->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get referral statistics.
     */
    public function stats()
    {
        $stats = [
            'total_referrals' => Referral::count(),
            'active_referrals' => Referral::active()->count(),
            'completed_referrals' => Referral::completed()->count(),
            'total_commission' => Referral::sum('commission_amount'),
            'total_rewards' => Referral::sum('reward_amount'),
            'referrals_last_24h' => Referral::where('created_at', '>=', now()->subDay())->count(),
            'referrals_last_week' => Referral::where('created_at', '>=', now()->subWeek())->count(),
            'referrals_last_month' => Referral::where('created_at', '>=', now()->subMonth())->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get status text in Arabic.
     */
    private function getStatusText($status)
    {
        return match ($status) {
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'expired' => 'منتهي',
            'cancelled' => 'ملغي',
            default => 'غير محدد',
        };
    }
}
