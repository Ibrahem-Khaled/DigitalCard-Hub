<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'point_value_usd',
        'total_value_usd',
        'type',
        'source',
        'source_id',
        'description',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'point_value_usd' => 'decimal:4',
            'total_value_usd' => 'decimal:2',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the loyalty points.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loyalty point transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyPointTransaction::class);
    }

    /**
     * Check if points are expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if points are active.
     */
    public function isActive(): bool
    {
        return $this->is_active && !$this->isExpired();
    }

    /**
     * Get total points for user.
     */
    public static function getTotalPointsForUser(int $userId): int
    {
        return self::where('user_id', $userId)
                  ->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                  })
                  ->sum('points');
    }

    /**
     * Add points to user.
     */
    public static function addPoints(int $userId, int $points, string $type, string $source, ?int $sourceId = null, ?string $description = null, ?float $pointValueUsd = null): self
    {
        // استخدام الإعدادات العامة إذا لم يتم تحديد قيمة النقطة
        if ($pointValueUsd === null) {
            $pointValueUsd = LoyaltySetting::getValue('default_point_value_usd', 0.01);
        }

        $totalValueUsd = $points * $pointValueUsd;

        return self::create([
            'user_id' => $userId,
            'points' => $points,
            'point_value_usd' => $pointValueUsd,
            'total_value_usd' => $totalValueUsd,
            'type' => $type,
            'source' => $source,
            'source_id' => $sourceId,
            'description' => $description,
            'is_active' => true,
        ]);
    }

    /**
     * Deduct points from user.
     */
    public static function deductPoints(int $userId, int $points, string $type, string $source, ?int $sourceId = null, ?string $description = null, ?float $pointValueUsd = null): self
    {
        // استخدام الإعدادات العامة إذا لم يتم تحديد قيمة النقطة
        if ($pointValueUsd === null) {
            $pointValueUsd = LoyaltySetting::getValue('default_point_value_usd', 0.01);
        }

        $totalValueUsd = $points * $pointValueUsd;

        return self::create([
            'user_id' => $userId,
            'points' => -$points,
            'point_value_usd' => $pointValueUsd,
            'total_value_usd' => -$totalValueUsd,
            'type' => $type,
            'source' => $source,
            'source_id' => $sourceId,
            'description' => $description,
            'is_active' => true,
        ]);
    }

    /**
     * Get total USD value for user.
     */
    public static function getTotalValueForUser(int $userId): float
    {
        return self::where('user_id', $userId)
                  ->where('is_active', true)
                  ->where(function ($q) {
                      $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                  })
                  ->sum('total_value_usd');
    }

    /**
     * Calculate total value based on points and point value.
     */
    public function calculateTotalValue(): float
    {
        return $this->points * $this->point_value_usd;
    }

    /**
     * Update total value when point value changes.
     */
    public function updateTotalValue(): void
    {
        $this->total_value_usd = $this->calculateTotalValue();
        $this->save();
    }

    /**
     * Get effective point value for user (custom or default).
     */
    public static function getEffectivePointValueForUser(int $userId): float
    {
        $user = User::find($userId);

        if ($user && $user->use_custom_loyalty_settings && $user->custom_point_value_usd) {
            return $user->custom_point_value_usd;
        }

        return LoyaltySetting::getValue('default_point_value_usd', 0.01);
    }

    /**
     * Get effective points per dollar for user.
     */
    public static function getEffectivePointsPerDollarForUser(int $userId): int
    {
        $user = User::find($userId);

        if ($user && $user->use_custom_loyalty_settings && $user->custom_points_per_dollar) {
            return $user->custom_points_per_dollar;
        }

        return LoyaltySetting::getValue('points_per_dollar', 100);
    }

    /**
     * Calculate points earned for purchase amount.
     */
    public static function calculatePointsForPurchase(int $userId, float $purchaseAmount): int
    {
        $pointsPerDollar = self::getEffectivePointsPerDollarForUser($userId);
        return (int) ($purchaseAmount * $pointsPerDollar);
    }

    /**
     * Add points for purchase.
     */
    public static function addPointsForPurchase(int $userId, float $purchaseAmount, ?int $orderId = null): self
    {
        $points = self::calculatePointsForPurchase($userId, $purchaseAmount);
        $pointValue = self::getEffectivePointValueForUser($userId);

        return self::addPoints(
            $userId,
            $points,
            'earned',
            'purchase',
            $orderId,
            "نقاط مكتسبة من شراء بقيمة $" . number_format($purchaseAmount, 2),
            $pointValue
        );
    }

    /**
     * Scope to get only active points.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope to get points by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get points by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get points by source.
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }
}

