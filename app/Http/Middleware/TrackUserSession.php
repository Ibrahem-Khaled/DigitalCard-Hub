<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // تتبع الجلسة فقط للمستخدمين المسجلين
        if (Auth::check()) {
            $this->trackSession($request);
        }

        return $response;
    }

    /**
     * Track user session.
     */
    private function trackSession(Request $request): void
    {
        $user = Auth::user();
        $sessionId = $request->session()->getId();

        // البحث عن جلسة موجودة
        $existingSession = UserSession::where('session_id', $sessionId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingSession) {
            // تحديث آخر نشاط
            $existingSession->update([
                'last_activity_at' => now(),
            ]);
        } else {
            // إنشاء جلسة جديدة
            UserSession::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->getDeviceType($request->userAgent()),
                'browser' => $this->getBrowser($request->userAgent()),
                'os' => $this->getOperatingSystem($request->userAgent()),
                'country' => $this->getCountryFromIP($request->ip()),
                'city' => $this->getCityFromIP($request->ip()),
                'login_at' => now(),
                'last_activity_at' => now(),
                'is_active' => true,
                'login_method' => 'web',
                'referrer' => $request->header('referer'),
            ]);
        }
    }

    /**
     * Get device type from user agent.
     */
    private function getDeviceType(string $userAgent): string
    {
        if (preg_match('/Mobile|Android|iPhone|iPad/', $userAgent)) {
            if (preg_match('/iPad|Tablet/', $userAgent)) {
                return 'tablet';
            }
            return 'mobile';
        }
        return 'desktop';
    }

    /**
     * Get browser from user agent.
     */
    private function getBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome/', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Firefox/', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Safari/', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/Opera/', $userAgent)) {
            return 'Opera';
        }
        return 'Unknown';
    }

    /**
     * Get operating system from user agent.
     */
    private function getOperatingSystem(string $userAgent): string
    {
        if (preg_match('/Windows/', $userAgent)) {
            return 'Windows';
        } elseif (preg_match('/Mac/', $userAgent)) {
            return 'macOS';
        } elseif (preg_match('/Linux/', $userAgent)) {
            return 'Linux';
        } elseif (preg_match('/Android/', $userAgent)) {
            return 'Android';
        } elseif (preg_match('/iOS/', $userAgent)) {
            return 'iOS';
        }
        return 'Unknown';
    }

    /**
     * Get country from IP address (simplified version).
     */
    private function getCountryFromIP(string $ip): ?string
    {
        // في التطبيق الحقيقي، يمكن استخدام خدمة مثل ipapi أو MaxMind
        // هنا نستخدم قيم افتراضية للاختبار
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Saudi Arabia';
        }

        // يمكن إضافة منطق أكثر تعقيداً هنا
        return 'Unknown';
    }

    /**
     * Get city from IP address (simplified version).
     */
    private function getCityFromIP(string $ip): ?string
    {
        // في التطبيق الحقيقي، يمكن استخدام خدمة مثل ipapi أو MaxMind
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'Riyadh';
        }

        return null;
    }
}
