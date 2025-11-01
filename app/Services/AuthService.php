<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Attempt to authenticate a user with email/phone and password.
     */
    public function attempt(array $credentials, bool $remember = false): bool
    {
        $user = $this->findUserByCredentials($credentials);

        if (!$user || !$this->validateCredentials($user, $credentials)) {
            return false;
        }

        if (!$user->isActive()) {
            return false;
        }

        // Update last login information
        $this->updateLastLogin($user);

        // Login the user
        Auth::login($user, $remember);

        return true;
    }

    /**
     * Register a new user.
     */
    public function register(array $data): User
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'birth_date' => $data['birth_date'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'country' => $data['country'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'is_active' => true,
        ]);

        // Assign default customer role
        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id, [
                'assigned_at' => now(),
                'assigned_by' => null, // Self-registration
            ]);
        }

        return $user;
    }

    /**
     * Logout the current user.
     */
    public function logout(): void
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
    }

    /**
     * Find user by email or phone.
     */
    private function findUserByCredentials(array $credentials): ?User
    {
        $loginField = $credentials['login'] ?? null;

        if (!$loginField) {
            return null;
        }

        // Check if it's an email or phone
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $loginField)->first();
        }

        // Check if it's a phone number
        if (preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $loginField)) {
            return User::where('phone', $loginField)->first();
        }

        return null;
    }

    /**
     * Validate user credentials.
     */
    private function validateCredentials(User $user, array $credentials): bool
    {
        return Hash::check($credentials['password'], $user->password);
    }

    /**
     * Update user's last login information.
     */
    public function updateLastLogin(User $user): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * Generate a secure password reset token.
     */
    public function generatePasswordResetToken(User $user): string
    {
        $token = Str::random(64);

        // Store token in cache with expiration (1 hour)
        cache()->put("password_reset_{$token}", $user->id, 3600);

        return $token;
    }

    /**
     * Verify password reset token.
     */
    public function verifyPasswordResetToken(string $token): ?User
    {
        $userId = cache()->get("password_reset_{$token}");

        if (!$userId) {
            return null;
        }

        return User::find($userId);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->verifyPasswordResetToken($token);

        if (!$user) {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Remove the token from cache
        cache()->forget("password_reset_{$token}");

        return true;
    }

    /**
     * Change user password.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }

    /**
     * Check if user can access dashboard.
     */
    public function canAccessDashboard(User $user): bool
    {
        return $user->hasPermission('dashboard.view');
    }

    /**
     * Get user's dashboard redirect path based on role.
     */
    public function getDashboardRedirectPath(User $user): string
    {
        if ($user->isAdmin()) {
            return '/dashboard';
        }

        if ($user->hasRole('manager')) {
            return '/dashboard';
        }

        if ($user->hasRole('employee')) {
            return '/dashboard';
        }

        if ($user->hasRole('customer')) {
            return '/dashboard';
        }

        return '/';
    }

    /**
     * Generate and send verification code.
     */
    public function generateAndSendVerificationCode(User $user, string $type = 'login'): VerificationCode
    {
        // Deactivate all previous unverified codes for this user and type
        VerificationCode::where('user_id', $user->id)
            ->where('type', $type)
            ->where('verified', false)
            ->update(['verified' => true]);

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create verification code record
        $verificationCode = VerificationCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $type,
            'email' => $user->email,
            'phone' => $user->phone,
            'verified' => false,
            'expires_at' => now()->addMinutes(10), // Code expires in 10 minutes
            'ip_address' => request()->ip(),
        ]);

        // Send verification code via email
        Mail::to($user->email)->send(
            new \App\Mail\VerificationCodeMail($user, $code, $type)
        );

        return $verificationCode;
    }

    /**
     * Verify verification code.
     */
    public function verifyCode(User $user, string $code, string $type = 'login'): bool
    {
        $verificationCode = VerificationCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->where('verified', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verificationCode) {
            return false;
        }

        // Mark code as verified
        $verificationCode->markAsVerified();

        return true;
    }

    /**
     * Resend verification code.
     */
    public function resendVerificationCode(User $user, string $type = 'login'): VerificationCode
    {
        return $this->generateAndSendVerificationCode($user, $type);
    }
}
