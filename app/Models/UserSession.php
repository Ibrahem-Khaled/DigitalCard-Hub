<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'os',
        'country',
        'city',
        'login_at',
        'logout_at',
        'last_activity_at',
        'is_active',
        'login_method',
        'referrer',
    ];

    protected function casts(): array
    {
        return [
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && $this->logout_at === null;
    }

    /**
     * Get session duration.
     */
    public function getDurationAttribute(): ?int
    {
        if ($this->logout_at) {
            return $this->login_at->diffInMinutes($this->logout_at);
        }

        if ($this->last_activity_at) {
            return $this->login_at->diffInMinutes($this->last_activity_at);
        }

        return null;
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        $duration = $this->duration;

        if (!$duration) {
            return 'غير محدد';
        }

        if ($duration < 60) {
            return $duration . ' دقيقة';
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours < 24) {
            return $hours . ' ساعة و ' . $minutes . ' دقيقة';
        }

        $days = floor($hours / 24);
        $remainingHours = $hours % 24;

        return $days . ' يوم و ' . $remainingHours . ' ساعة';
    }

    /**
     * Get device icon.
     */
    public function getDeviceIconAttribute(): string
    {
        return match (strtolower($this->device_type)) {
            'mobile' => 'bi-phone',
            'tablet' => 'bi-tablet',
            'desktop' => 'bi-laptop',
            default => 'bi-device-hdd'
        };
    }

    /**
     * Get browser icon.
     */
    public function getBrowserIconAttribute(): string
    {
        return match (strtolower($this->browser)) {
            'chrome' => 'bi-browser-chrome',
            'firefox' => 'bi-browser-firefox',
            'safari' => 'bi-browser-safari',
            'edge' => 'bi-browser-edge',
            'opera' => 'bi-browser-opera',
            default => 'bi-browser'
        };
    }

    /**
     * Scope to get only active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNull('logout_at');
    }

    /**
     * Scope to get only inactive sessions.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false)
                    ->orWhereNotNull('logout_at');
    }

    /**
     * Scope to get sessions by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get recent sessions.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }
}
