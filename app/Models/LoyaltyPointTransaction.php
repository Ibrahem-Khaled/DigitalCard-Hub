<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_point_id',
        'user_id',
        'points',
        'type',
        'source',
        'source_id',
        'description',
        'balance_before',
        'balance_after',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_before' => 'integer',
            'balance_after' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the loyalty point that owns the transaction.
     */
    public function loyaltyPoint(): BelongsTo
    {
        return $this->belongsTo(LoyaltyPoint::class);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get transactions by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get transactions by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get transactions by source.
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope to get recent transactions.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('processed_at', '>=', now()->subDays($days));
    }
}

