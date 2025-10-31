<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'user_limit',
        'is_active',
        'starts_at',
        'expires_at',
        'applicable_products',
        'applicable_categories',
        'applicable_users',
        'first_time_only',
        'stackable',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'minimum_amount' => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'user_limit' => 'integer',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'applicable_products' => 'array',
            'applicable_categories' => 'array',
            'applicable_users' => 'array',
            'first_time_only' => 'boolean',
            'stackable' => 'boolean',
        ];
    }

    /**
     * Get the coupon usages.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get the carts that use this coupon.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'coupon_code', 'code');
    }

    /**
     * Get the orders that use this coupon.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_code', 'code');
    }

    /**
     * Calculate discount amount for given cart total.
     */
    public function calculateDiscount(float $cartTotal): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($cartTotal < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($cartTotal * $this->value) / 100;
        } elseif ($this->type === 'fixed') {
            $discount = $this->value;
        }

        // Apply maximum discount limit
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return min($discount, $cartTotal);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon can be used by user.
     */
    public function canBeUsedBy(int $userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check if user has reached usage limit
        if ($this->user_limit) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->user_limit) {
                return false;
            }
        }

        // Check if first time only
        if ($this->first_time_only) {
            $hasUsedBefore = $this->usages()->where('user_id', $userId)->exists();
            if ($hasUsedBefore) {
                return false;
            }
        }

        // Check if user is in applicable users list
        if ($this->applicable_users && !in_array($userId, $this->applicable_users)) {
            return false;
        }

        return true;
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Scope to get only active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get valid coupons.
     */
    public function scopeValid($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    /**
     * Scope to get expired coupons.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}

