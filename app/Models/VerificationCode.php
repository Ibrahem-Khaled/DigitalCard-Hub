<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class VerificationCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'email',
        'phone',
        'verified',
        'expires_at',
        'verified_at',
        'ip_address',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the user that owns the verification code.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the code is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the code is valid (not expired and not verified).
     */
    public function isValid(): bool
    {
        return !$this->verified && !$this->isExpired();
    }

    /**
     * Mark the code as verified.
     */
    public function markAsVerified(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
        ]);
    }

    /**
     * Scope to get only unverified codes.
     */
    public function scopeUnverified($query)
    {
        return $query->where('verified', false);
    }

    /**
     * Scope to get only valid (not expired) codes.
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope to get codes by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get codes for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
