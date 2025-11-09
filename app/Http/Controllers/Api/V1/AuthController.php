<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends BaseController
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        try {
            $user = $this->authService->register($request->all());

            // Generate verification code
            $this->authService->generateAndSendVerificationCode($user, 'registration');

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            return $this->successResponse([
                'user' => new UserResource($user),
                'token' => $token,
                'requires_verification' => true,
            ], 'تم التسجيل بنجاح. يرجى التحقق من بريدك الإلكتروني لإكمال التسجيل', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('حدث خطأ أثناء التسجيل: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        // Rate limiting
        $key = 'api_login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->errorResponse("تم تجاوز عدد المحاولات المسموح. حاول مرة أخرى خلال {$seconds} ثانية.", 429);
        }

        $loginField = $request->input('login');
        $user = null;

        // Find user by email or phone
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginField)->first();
        } elseif (preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $loginField)) {
            $user = User::where('phone', $loginField)->first();
        }

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($key, 300);
            return $this->errorResponse('بيانات الدخول غير صحيحة', 401);
        }

        if (!$user->isActive()) {
            RateLimiter::hit($key, 300);
            return $this->errorResponse('حسابك غير نشط. يرجى التواصل مع الإدارة', 403);
        }

        // Check if user is admin - admins can login without verification code
        if ($user->isAdmin()) {
            // Update last login
            $this->authService->updateLastLogin($user);

            // Create token
            $token = $user->createToken('api-token')->plainTextToken;

            RateLimiter::clear($key);

            return $this->successResponse([
                'user' => new \App\Http\Resources\Api\UserResource($user),
                'token' => $token,
                'requires_verification' => false,
            ], 'تم تسجيل الدخول بنجاح', 200);
        }

        // For non-admin users, generate verification code
        $this->authService->generateAndSendVerificationCode($user, 'login');

        RateLimiter::clear($key);

        return $this->successResponse([
            'user_id' => $user->id,
            'requires_verification' => true,
        ], 'تم إرسال كود التحقق إلى بريدك الإلكتروني', 200);
    }

    /**
     * Verify code and complete login
     */
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'code' => 'required|string|size:6',
            'type' => 'nullable|string|in:login,registration',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = User::find($request->input('user_id'));
        $code = $request->input('code');
        $type = $request->input('type', 'login');

        if (!$this->authService->verifyCode($user, $code, $type)) {
            return $this->errorResponse('كود التحقق غير صحيح أو منتهي الصلاحية', 400);
        }

        // Update last login
        $this->authService->updateLastLogin($user);

        // Create token
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'تم التحقق بنجاح');
    }

    /**
     * Resend verification code
     */
    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'type' => 'nullable|string|in:login,registration',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = User::find($request->input('user_id'));
        $type = $request->input('type', 'login');

        $this->authService->resendVerificationCode($user, $type);

        return $this->successResponse(null, 'تم إرسال كود التحقق مرة أخرى');
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        return $this->successResponse([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|unique:users,phone,' . $user->id,
            'birth_date' => 'sometimes|nullable|date|before:today',
            'gender' => 'sometimes|nullable|in:male,female',
            'address' => 'sometimes|nullable|string|max:500',
            'city' => 'sometimes|nullable|string|max:255',
            'country' => 'sometimes|nullable|string|max:255',
            'postal_code' => 'sometimes|nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone',
            'birth_date', 'gender', 'address', 'city', 'country', 'postal_code'
        ]));

        return $this->successResponse([
            'user' => new UserResource($user->fresh()),
        ], 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = $request->user();

        if (!$this->authService->changePassword($user, $request->input('current_password'), $request->input('password'))) {
            return $this->errorResponse('كلمة المرور الحالية غير صحيحة', 400);
        }

        return $this->successResponse(null, 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return $this->errorResponse('البريد الإلكتروني غير موجود', 404);
        }

        $token = $this->authService->generatePasswordResetToken($user);

        // In a real app, send email with reset link
        // For now, return token (in production, send via email)

        return $this->successResponse([
            'reset_token' => $token, // In production, don't return this
        ], 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors()->toArray());
        }

        $user = $this->authService->verifyPasswordResetToken($request->input('token'));

        if (!$user) {
            return $this->errorResponse('رمز إعادة التعيين غير صحيح أو منتهي الصلاحية', 400);
        }

        $this->authService->resetPassword($request->input('token'), $request->input('password'));

        return $this->successResponse(null, 'تم إعادة تعيين كلمة المرور بنجاح');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'تم تسجيل الخروج بنجاح');
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $user = $request->user();

        // Delete current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->successResponse([
            'token' => $token,
        ], 'تم تحديث الرمز المميز بنجاح');
    }
}

