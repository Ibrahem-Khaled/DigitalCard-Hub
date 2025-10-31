<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id',
        'referred_id',
        'referral_code',
        'status',
        'commission_amount',
        'commission_percentage',
        'reward_amount',
        'reward_percentage',
        'completed_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'commission_amount' => 'decimal:2',
            'commission_percentage' => 'decimal:2',
            'reward_amount' => 'decimal:2',
            'reward_percentage' => 'decimal:2',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get the user who made the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Get the user who was referred.
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Get the referral rewards.
     */
    public function rewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    /**
     * Check if referral is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if referral is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark referral as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Generate unique referral code.
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Scope to get only completed referrals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get referrals by referrer.
     */
    public function scopeByReferrer($query, int $referrerId)
    {
        return $query->where('referrer_id', $referrerId);
    }

    /**
     * Scope to get referrals by referred user.
     */
    public function scopeByReferred($query, int $referredId)
    {
        return $query->where('referred_id', $referredId);
    }

    /**
     * Scope to get active referrals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

