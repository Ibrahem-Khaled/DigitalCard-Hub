<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price', 'total_price', 'digital_cards', 'status', 'delivered_at'
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'digital_cards' => 'array',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع الطلب
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع المنتج
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * العلاقة مع البطاقات الرقمية
     */
    public function digitalCards(): HasMany
    {
        return $this->hasMany(DigitalCard::class);
    }

    /**
     * التحقق من حالة العنصر
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * الحصول على حالة العنصر بالعربية
     */
    public function getStatusInArabic(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            default => 'غير محدد'
        };
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * تحديث حالة العنصر
     */
    public function updateStatus(string $status): bool
    {
        $this->status = $status;

        if ($status === 'delivered') {
            $this->delivered_at = now();
        }

        return $this->save();
    }

    /**
     * التحقق من وجود بطاقات رقمية
     */
    public function hasDigitalCards(): bool
    {
        return !empty($this->digital_cards) && is_array($this->digital_cards) && count($this->digital_cards) > 0;
    }

    /**
     * الحصول على عدد البطاقات الرقمية
     */
    public function getDigitalCardsCount(): int
    {
        return $this->hasDigitalCards() ? count($this->digital_cards) : 0;
    }

    /**
     * إضافة بطاقة رقمية
     */
    public function addDigitalCard(array $cardData): bool
    {
        $cards = $this->digital_cards ?? [];
        $cards[] = $cardData;
        $this->digital_cards = $cards;
        return $this->save();
    }

    /**
     * حذف بطاقة رقمية
     */
    public function removeDigitalCard(int $index): bool
    {
        if (!$this->hasDigitalCards() || !isset($this->digital_cards[$index])) {
            return false;
        }

        $cards = $this->digital_cards;
        unset($cards[$index]);
        $this->digital_cards = array_values($cards); // إعادة ترقيم المصفوفة
        return $this->save();
    }

    /**
     * الحصول على إجمالي السعر المحسوب
     */
    public function getCalculatedTotal(): float
    {
        return $this->price * $this->quantity;
    }

    /**
     * التحقق من تطابق السعر
     */
    public function isPriceCorrect(): bool
    {
        return abs($this->total_price - $this->getCalculatedTotal()) < 0.01;
    }

    /**
     * Scopes للاستعلامات
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOrder($query, int $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    public function scopeByProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWithDigitalCards($query)
    {
        return $query->whereNotNull('digital_cards');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
