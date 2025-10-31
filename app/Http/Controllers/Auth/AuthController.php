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

        if ($this->authService->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            $user = Auth::user();
            $redirectPath = $this->authService->getDashboardRedirectPath($user);

            return redirect()->intended($redirectPath)
                ->with('success', 'مرحباً بك، ' . $user->full_name);
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
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
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
            $user = $this->authService->register($request->all());

            // Auto login after registration
            Auth::login($user);

            return redirect()->route('dashboard.index')
                ->with('success', 'تم إنشاء حسابك بنجاح! مرحباً بك، ' . $user->full_name);

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

            // Send reset email (you can implement this later)
            // Mail::to($user->email)->send(new PasswordResetMail($token));

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
}
