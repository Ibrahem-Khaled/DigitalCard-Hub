<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
            'remember' => 'boolean',
        ], [
            'login.required' => 'البريد الإلكتروني أو رقم الهاتف مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'login' => "تم تجاوز عدد المحاولات المسموح. حاول مرة أخرى خلال {$seconds} ثانية.",
            ]);
        }

        $credentials = $request->only(['login', 'password']);
        $remember = $request->boolean('remember');

        // Find user by credentials first (without logging in)
        $loginField = $credentials['login'] ?? null;
        $user = null;

        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $loginField)->first();
        } elseif (preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $loginField)) {
            $user = \App\Models\User::where('phone', $loginField)->first();
        }

        // Validate credentials
        if ($user && \Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password) && $user->isActive()) {
            // Don't login yet, send verification code instead
            $this->authService->generateAndSendVerificationCode($user, 'login');

            // Store user ID in session for verification
            $request->session()->put('verification_user_id', $user->id);
            $request->session()->put('verification_remember', $remember);
            $request->session()->put('verification_type', 'login');

            RateLimiter::clear($key);

            return redirect()->route('verification.show')
                ->with('success', 'تم إرسال كود التحقق إلى بريدك الإلكتروني');
        }

        RateLimiter::hit($key, 300); // 5 minutes

        throw ValidationException::withMessages([
            'login' => 'بيانات الدخول غير صحيحة أو الحساب غير نشط.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        // Prepare phone number with country code
        $phone = $request->input('phone');
        $countryCode = $request->input('country_code');
        $phoneNumber = $request->input('phone_number');
        
        // If phone is not directly provided, combine country code and phone number
        if (empty($phone) && !empty($phoneNumber)) {
            $phone = $countryCode . preg_replace('/^0+/', '', $phoneNumber);
        }

        $validator = Validator::make(array_merge($request->all(), ['phone' => $phone]), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ], [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
            'birth_date.date' => 'تاريخ الميلاد غير صحيح',
            'birth_date.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم',
            'gender.in' => 'الجنس يجب أن يكون ذكر أو أنثى',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Prepare registration data with phone
            $registrationData = $request->all();
            $registrationData['phone'] = $phone; // Use the combined phone number
            
            $user = $this->authService->register($registrationData);

            // Don't login yet, send verification code instead
            $this->authService->generateAndSendVerificationCode($user, 'registration');

            // Store user ID in session for verification
            $request->session()->put('verification_user_id', $user->id);
            $request->session()->put('verification_remember', false);
            $request->session()->put('verification_type', 'registration');

            return redirect()->route('verification.show')
                ->with('success', 'تم إنشاء حسابك بنجاح! تم إرسال كود التحقق إلى بريدك الإلكتروني');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء إنشاء الحساب. حاول مرة أخرى.'])
                ->withInput();
        }
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Show password reset request form.
     */
    public function showPasswordResetForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request.
     */
    public function sendPasswordResetLink(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.exists' => 'البريد الإلكتروني غير مسجل',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            $token = $this->authService->generatePasswordResetToken($user);

            // Send reset email
            Mail::to($user->email)->send(
                new \App\Mail\PasswordResetMail($user, $token)
            );

            return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
        }

        return back()->withErrors(['email' => 'البريد الإلكتروني غير مسجل']);
    }

    /**
     * Show password reset form.
     */
    public function showPasswordReset(Request $request, string $token)
    {
        $user = $this->authService->verifyPasswordResetToken($token);

        if (!$user) {
            return redirect()->route('login')
                ->withErrors(['error' => 'رابط إعادة تعيين كلمة المرور غير صحيح أو منتهي الصلاحية']);
        }

        return view('auth.reset-password', compact('token'));
    }

    /**
     * Handle password reset.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'token.required' => 'رمز إعادة التعيين مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($this->authService->resetPassword($request->token, $request->password)) {
            return redirect()->route('login')
                ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن');
        }

        return back()->withErrors(['error' => 'رابط إعادة تعيين كلمة المرور غير صحيح أو منتهي الصلاحية']);
    }

    /**
     * Show change password form.
     */
    public function showChangePasswordForm(): View
    {
        return view('auth.change-password');
    }

    /**
     * Handle change password request.
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة',
            'password.required' => 'كلمة المرور الجديدة مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        if ($this->authService->changePassword(
            Auth::user(),
            $request->current_password,
            $request->password
        )) {
            return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
        }

        return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
    }

    /**
     * Show verification code form.
     */
    public function showVerificationForm(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('verification_user_id');
        $type = $request->session()->get('verification_type');

        if (!$userId || !$type) {
            return redirect()->route('login')
                ->withErrors(['error' => 'يرجى تسجيل الدخول أو إنشاء حساب جديد']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            $request->session()->forget(['verification_user_id', 'verification_type', 'verification_remember']);
            return redirect()->route('login')
                ->withErrors(['error' => 'المستخدم غير موجود']);
        }

        // Get the latest unverified verification code for this user
        $verificationCode = \App\Models\VerificationCode::where('user_id', $user->id)
            ->where('type', $type)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        $expiresAt = $verificationCode ? $verificationCode->expires_at->timestamp : null;

        return view('auth.verify', compact('user', 'type', 'expiresAt'));
    }

    /**
     * Handle verification code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'كود التحقق مطلوب',
            'code.size' => 'كود التحقق يجب أن يكون 6 أرقام',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $userId = $request->session()->get('verification_user_id');
        $type = $request->session()->get('verification_type');
        $remember = $request->session()->get('verification_remember', false);

        if (!$userId || !$type) {
            return redirect()->route('login')
                ->withErrors(['error' => 'انتهت صلاحية الجلسة. يرجى المحاولة مرة أخرى']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            $request->session()->forget(['verification_user_id', 'verification_type', 'verification_remember']);
            return redirect()->route('login')
                ->withErrors(['error' => 'المستخدم غير موجود']);
        }

        // Verify code
        if (!$this->authService->verifyCode($user, $request->code, $type)) {
            return back()->withErrors(['code' => 'كود التحقق غير صحيح أو منتهي الصلاحية'])
                ->withInput();
        }

        // Clear verification session data
        $request->session()->forget(['verification_user_id', 'verification_type', 'verification_remember']);

        // Login the user
        Auth::login($user, $remember);
        $request->session()->regenerate();

        // Update last login information
        $this->authService->updateLastLogin($user);

        // Redirect based on user role
        $message = $type === 'registration' 
            ? 'تم إنشاء حسابك بنجاح! مرحباً بك، ' . $user->full_name
            : 'مرحباً بك، ' . $user->full_name;

        // Redirect admin, manager, employee to dashboard
        if ($user->isAdmin() || $user->hasAnyRole(['manager', 'employee'])) {
            return redirect()->route('dashboard.index')
                ->with('success', $message);
        }

        // Redirect customers to home page
        return redirect()->route('home')
            ->with('success', $message);
    }

    /**
     * Resend verification code.
     */
    public function resendVerificationCode(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('verification_user_id');
        $type = $request->session()->get('verification_type');

        if (!$userId || !$type) {
            return redirect()->route('login')
                ->withErrors(['error' => 'انتهت صلاحية الجلسة. يرجى المحاولة مرة أخرى']);
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            $request->session()->forget(['verification_user_id', 'verification_type', 'verification_remember']);
            return redirect()->route('login')
                ->withErrors(['error' => 'المستخدم غير موجود']);
        }

        // Resend verification code
        $this->authService->resendVerificationCode($user, $type);

        return back()->with('success', 'تم إعادة إرسال كود التحقق إلى بريدك الإلكتروني');
    }

    /**
     * Show verification codes for admin (development only).
     * Only accessible in local/development environment or by authenticated admin.
     */
    public function showAdminVerificationCodes(Request $request)
    {
        // Only allow in development/local environment or authenticated admin
        if (config('app.env') !== 'local' && config('app.env') !== 'development') {
            // Check if user is authenticated and is admin
            if (!Auth::check() || !Auth::user()->isAdmin()) {
                abort(403, 'غير مصرح به');
            }
        }

        // Get admin users
        $adminUsers = \App\Models\User::whereHas('roles', function($query) {
            $query->where('slug', 'admin');
        })->get();

        // Get latest verification codes for admin users
        $verificationCodes = \App\Models\VerificationCode::whereIn('user_id', $adminUsers->pluck('id'))
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // If accessed via web, return view, else return JSON for API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'codes' => $verificationCodes->map(function($code) {
                    return [
                        'user' => $code->user->email,
                        'code' => $code->code,
                        'type' => $code->type,
                        'created_at' => $code->created_at->format('Y-m-d H:i:s'),
                        'expires_at' => $code->expires_at->format('Y-m-d H:i:s'),
                        'time_left' => now()->diffInMinutes($code->expires_at) . ' دقائق',
                    ];
                })
            ]);
        }

        // Return simple HTML view for easy access
        $html = '<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكواد التحقق - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0F0F0F; color: #fff; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #a855f7; }
        .code-item { background: #1A1A1A; padding: 15px; margin: 10px 0; border-radius: 8px; border: 1px solid #a855f7; }
        .code { font-size: 24px; font-weight: bold; color: #f97316; margin: 10px 0; }
        .info { color: #9ca3af; font-size: 14px; }
        .refresh-btn { background: #a855f7; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 20px; }
        .refresh-btn:hover { background: #9333ea; }
        .no-codes { color: #9ca3af; text-align: center; padding: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 أكواد التحقق الخاصة بالادمن</h1>
        <button class="refresh-btn" onclick="location.reload()">🔄 تحديث</button>';
        
        if ($verificationCodes->isEmpty()) {
            $html .= '<div class="no-codes">لا توجد أكواد تحقق نشطة حالياً</div>';
        } else {
            foreach ($verificationCodes as $code) {
                $html .= '<div class="code-item">
                    <div class="code">' . $code->code . '</div>
                    <div class="info">
                        <strong>المستخدم:</strong> ' . $code->user->email . '<br>
                        <strong>النوع:</strong> ' . ($code->type === 'registration' ? 'تسجيل' : 'دخول') . '<br>
                        <strong>تاريخ الإنشاء:</strong> ' . $code->created_at->format('Y-m-d H:i:s') . '<br>
                        <strong>ينتهي في:</strong> ' . $code->expires_at->format('Y-m-d H:i:s') . ' (' . now()->diffInMinutes($code->expires_at) . ' دقيقة) 
                    </div>
                </div>';
            }
        }

        $html .= '</div>
    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() { location.reload(); }, 30000);
    </script>
</body>
</html>';

        return response($html);
    }
}
