<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'discount_amount',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'currency',
        'is_abandoned',
        'abandoned_at',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'is_abandoned' => 'boolean',
            'abandoned_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the cart.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cart items for the cart.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the coupon applied to the cart.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    /**
     * Calculate the total amount of the cart.
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $total = $subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;

        return max(0, $total);
    }

    /**
     * Get the subtotal of the cart.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get the total items count in the cart.
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if cart is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Mark cart as abandoned.
     */
    public function markAsAbandoned(): void
    {
        $this->update([
            'is_abandoned' => true,
            'abandoned_at' => now(),
        ]);

        // إرسال إيميل تلقائي إذا كان للمستخدم إيميل
        if ($this->user_id && $this->user && $this->user->email) {
            try {
                $this->sendAbandonedCartEmail();
            } catch (\Exception $e) {
                \Log::error("Failed to send abandoned cart email automatically", [
                    'cart_id' => $this->id,
                    'user_id' => $this->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send abandoned cart email automatically.
     */
    private function sendAbandonedCartEmail(): void
    {
        // تحميل العلاقات المطلوبة
        if (!$this->relationLoaded('user')) {
            $this->load('user');
        }
        
        if (!$this->relationLoaded('items')) {
            $this->load('items.product');
        }

        if (!$this->user || !$this->user->email) {
            return;
        }

        $subject = 'تذكير: لديك منتجات في سلة التسوق';
        $message = 'لاحظنا أنك تركت بعض المنتجات في سلة التسوق الخاصة بك. نحن هنا لمساعدتك في إكمال طلبك!';

        try {
            \Illuminate\Support\Facades\Mail::to($this->user->email)
                ->send(new \App\Mail\AbandonedCartMail($this->user, $this, $subject, $message));
        } catch (\Exception $e) {
            \Log::error("Failed to send abandoned cart email in model", [
                'cart_id' => $this->id,
                'user_id' => $this->user_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Scope to get only abandoned carts.
     */
    public function scopeAbandoned($query)
    {
        return $query->where('is_abandoned', true);
    }

    /**
     * Scope to get carts by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get carts by session.
     */
    public function scopeForSession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}

