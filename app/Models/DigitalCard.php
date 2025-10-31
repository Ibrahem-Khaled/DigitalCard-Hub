<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DigitalCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'card_code',
        'card_pin',
        'card_number',
        'serial_number',
        'value',
        'currency',
        'expiry_date',
        'is_used',
        'used_at',
        'used_by',
        'order_item_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expiry_date' => 'date',
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the digital card.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who used the card.
     */
    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * Get the order item that contains this card.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the order through order item.
     */
    public function order()
    {
        return $this->hasOneThrough(Order::class, OrderItem::class, 'id', 'id', 'order_item_id', 'order_id');
    }

    /**
     * Mark the card as used.
     */
    public function markAsUsed(int $userId, int $orderItemId): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
            'used_by' => $userId,
            'order_item_id' => $orderItemId,
            'status' => 'used',
        ]);
    }

    /**
     * Check if card is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if card is available for use.
     */
    public function isAvailable(): bool
    {
        return !$this->is_used && !$this->isExpired() && $this->status === 'active';
    }

    /**
     * Scope to get only available cards.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_used', false)
                    ->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', now());
                    });
    }

    /**
     * Scope to get only used cards.
     */
    public function scopeUsed($query)
    {
        return $query->where('is_used', true);
    }

    /**
     * Scope to get only expired cards.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }
}

