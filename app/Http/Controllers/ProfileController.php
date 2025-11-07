<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * عرض الملف الشخصي
     */
    public function index()
    {
        $user = Auth::user();
        $user->load([
            'orders' => function($query) {
                $query->latest()->take(5);
            },
            'loyaltyPoints',
            'referrals'
        ]);

        // حساب نقاط الولاء
        $totalPoints = $user->loyaltyPoints()
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');

        // إحصائيات الطلبات
        $ordersStats = [
            'total' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'processing' => $user->orders()->where('status', 'processing')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
        ];

        return view('profile.index', compact('user', 'totalPoints', 'ordersStats'));
    }

    /**
     * عرض صفحة تعديل الملف الشخصي
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * تحديث معلومات الملف الشخصي
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Prepare phone number with country code
        $phone = $request->input('phone');
        $countryCode = $request->input('country_code');
        $phoneNumber = $request->input('phone_number');
        
        // If phone is not directly provided, combine country code and phone number
        if (empty($phone) && !empty($phoneNumber)) {
            $phone = $countryCode . preg_replace('/^0+/', '', $phoneNumber);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        // Update phone in validated data
        $validated['phone'] = $phone;
        
        $user->update($validated);

        return redirect()->route('profile.index')
            ->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * عرض صفحة تغيير كلمة المرور
     */
    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        // التحقق من كلمة المرور الحالية
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * عرض الطلبات
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with(['orderItems.product']);

        // فلترة حسب حالة الطلب
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلترة حسب التاريخ (من)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // فلترة حسب التاريخ (إلى)
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortBy, ['created_at', 'total_amount', 'order_number'], true)) {
            $sortBy = 'created_at';
        }
        if (!in_array($sortOrder, ['asc', 'desc'], true)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(10)->withQueryString();

        return view('profile.orders', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب معين
     */
    public function orderDetails($id)
    {
        $user = Auth::user();
        $order = $user->orders()
            ->with(['orderItems.product.category', 'payments'])
            ->findOrFail($id);

        return view('profile.order-details', compact('order'));
    }

    /**
     * عرض نقاط الولاء
     */
    public function loyaltyPoints()
    {
        $user = Auth::user();
        $loyaltyPoints = $user->loyaltyPoints()
            ->latest()
            ->paginate(10);

        $totalPoints = $user->loyaltyPoints()
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->sum('points');

        return view('profile.loyalty-points', compact('loyaltyPoints', 'totalPoints'));
    }

    /**
     * عرض الإحالات
     */
    public function referrals()
    {
        $user = Auth::user();
        $referrals = $user->referrals()
            ->with(['referred', 'referralReward'])
            ->latest()
            ->paginate(10);

        $referralCode = $user->getReferralCodeAttribute();

        return view('profile.referrals', compact('referrals', 'referralCode'));
    }
}

