<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الدور
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // ترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // التحقق من صحة معاملات الترتيب
        $allowedSortFields = ['created_at', 'first_name', 'last_name', 'email', 'updated_at'];
        $allowedSortOrders = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(15)->withQueryString();

        // إحصائيات
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admin_users' => User::whereHas('roles', function ($q) {
                $q->where('slug', 'admin');
            })->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];

        $roles = Role::all();

        return view('dashboard.users.index', compact('users', 'stats', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('dashboard.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'roles.required' => 'يجب اختيار دور واحد على الأقل للمستخدم',
            'roles.min' => 'يجب اختيار دور واحد على الأقل للمستخدم',
            'roles.*.exists' => 'الدور المحدد غير موجود',
        ]);

        $userData = $request->except(['password', 'password_confirmation', 'roles', 'avatar']);
        $userData['password'] = Hash::make($request->password);

        // رفع الصورة
        if ($request->hasFile('avatar')) {
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($userData);

        // إضافة الأدوار
        $user->roles()->attach($request->roles);

        return redirect()->route('dashboard.users.index')
            ->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'orders', 'payments']);

        return view('dashboard.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('dashboard.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'roles.required' => 'يجب اختيار دور واحد على الأقل للمستخدم',
            'roles.min' => 'يجب اختيار دور واحد على الأقل للمستخدم',
            'roles.*.exists' => 'الدور المحدد غير موجود',
        ]);

        $userData = $request->except(['password', 'password_confirmation', 'roles', 'avatar']);

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // رفع الصورة الجديدة
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($userData);

        // تحديث الأدوار
        $user->roles()->sync($request->roles);

        return redirect()->route('dashboard.users.index')
            ->with('success', 'تم تحديث المستخدم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // منع حذف المستخدم الحالي
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'لا يمكنك حذف حسابك الخاص');
        }

        // حذف الصورة
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('dashboard.users.index')
            ->with('success', 'تم حذف المستخدم بنجاح');
    }

    /**
     * Toggle user active status.
     */
    public function toggleStatus(User $user)
    {
        // منع تعطيل المستخدم الحالي
        if ($user->id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'لا يمكنك تعطيل حسابك الخاص');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'تفعيل' : 'تعطيل';

        return redirect()->back()
            ->with('success', "تم {$status} المستخدم بنجاح");
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
            ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
    }

    /**
     * Show user sessions.
     */
    public function sessions(User $user, Request $request)
    {
        $query = $user->userSessions()->orderBy('login_at', 'desc');

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
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
            $query->recent($days);
        }

        $sessions = $query->paginate(15)->withQueryString();

        // إحصائيات الجلسات
        $stats = [
            'total_sessions' => $user->userSessions()->count(),
            'active_sessions' => $user->userSessions()->active()->count(),
            'total_duration' => $user->userSessions()->whereNotNull('logout_at')->sum(DB::raw('TIMESTAMPDIFF(MINUTE, login_at, logout_at)')),
            'avg_session_duration' => $user->userSessions()->whereNotNull('logout_at')->avg(DB::raw('TIMESTAMPDIFF(MINUTE, login_at, logout_at)')),
            'unique_devices' => $user->userSessions()->distinct('device_type')->count('device_type'),
            'unique_locations' => $user->userSessions()->distinct('country')->count('country'),
        ];

        return view('dashboard.users.sessions', compact('user', 'sessions', 'stats'));
    }

    /**
     * Terminate user session.
     */
    public function terminateSession(UserSession $session)
    {
        $session->update([
            'logout_at' => now(),
            'is_active' => false,
        ]);

        return redirect()->back()
            ->with('success', 'تم إنهاء الجلسة بنجاح');
    }

    /**
     * Terminate all user sessions.
     */
    public function terminateAllSessions(User $user)
    {
        $user->userSessions()->active()->update([
            'logout_at' => now(),
            'is_active' => false,
        ]);

        return redirect()->back()
            ->with('success', 'تم إنهاء جميع جلسات المستخدم بنجاح');
    }

    /**
     * Export users to CSV.
     */
    public function export()
    {
        $users = User::with('roles')->get();

        $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');

            // إضافة BOM للدعم العربي
            fwrite($file, "\xEF\xBB\xBF");

            // رؤوس الأعمدة
            fputcsv($file, [
                'الاسم الأول',
                'الاسم الأخير',
                'البريد الإلكتروني',
                'رقم الهاتف',
                'تاريخ الميلاد',
                'الجنس',
                'المدينة',
                'الدولة',
                'الحالة',
                'الأدوار',
                'تاريخ الإنشاء'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->first_name,
                    $user->last_name,
                    $user->email,
                    $user->phone,
                    $user->birth_date?->format('Y-m-d'),
                    $user->gender === 'male' ? 'ذكر' : ($user->gender === 'female' ? 'أنثى' : ''),
                    $user->city,
                    $user->country,
                    $user->is_active ? 'نشط' : 'معطل',
                    $user->roles->pluck('name')->join(', '),
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
