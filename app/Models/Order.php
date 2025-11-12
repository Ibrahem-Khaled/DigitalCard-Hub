<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'payment_status', 'payment_method', 'payment_reference',
        'subtotal', 'tax_amount', 'shipping_amount', 'discount_amount', 'total_amount', 'currency',
        'coupon_code', 'shipping_address', 'billing_address', 'notes',
        'zoho_invoice_id', 'zoho_invoice_number',
        'processed_at', 'shipped_at', 'delivered_at', 'cancelled_at', 'refunded_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'processed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع عناصر الطلب
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * العلاقة مع المدفوعات
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * العلاقة مع الكوبون المستخدم
     */
    public function couponUsage(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * إنشاء رقم طلب فريد
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Y') . '-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * التحقق من حالة الطلب
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * التحقق من حالة الدفع
     */
    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPaymentPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isPaymentRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    /**
     * الحصول على حالة الطلب بالعربية
     */
    public function getStatusInArabic(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            default => 'غير محدد'
        };
    }

    /**
     * الحصول على حالة الدفع بالعربية
     */
    public function getPaymentStatusInArabic(): string
    {
        return match($this->payment_status) {
            'pending' => 'في الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل',
            'refunded' => 'مسترد',
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
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * الحصول على لون حالة الدفع
     */
    public function getPaymentStatusColor(): string
    {
        return match($this->payment_status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(string $status): bool
    {
        $this->status = $status;

        // تحديث التواريخ المناسبة
        switch ($status) {
            case 'processing':
                $this->processed_at = now();
                break;
            case 'shipped':
                $this->shipped_at = now();
                break;
            case 'delivered':
                $this->delivered_at = now();
                break;
            case 'cancelled':
                $this->cancelled_at = now();
                break;
            case 'refunded':
                $this->refunded_at = now();
                break;
        }

        return $this->save();
    }

    /**
     * تحديث حالة الدفع
     */
    public function updatePaymentStatus(string $status): bool
    {
        $this->payment_status = $status;
        return $this->save();
    }

    /**
     * الحصول على إجمالي العناصر
     */
    public function getTotalItems(): int
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * التحقق من وجود كوبون
     */
    public function hasCoupon(): bool
    {
        return !empty($this->coupon_code);
    }

    /**
     * الحصول على طريقة الدفع بالعربية
     */
    public function getPaymentMethodInArabic(): string
    {
        return match($this->payment_method) {
            'credit_card' => 'بطاقة ائتمان',
            'debit_card' => 'بطاقة خصم',
            'bank_transfer' => 'تحويل بنكي',
            'paypal' => 'باي بال',
            'stripe' => 'سترايب',
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'wallet' => 'محفظة رقمية',
            'loyalty_points' => 'نقاط الولاء',
            default => $this->payment_method
        };
    }

    /**
     * Scopes للاستعلامات
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentStatus($query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeWithCoupon($query)
    {
        return $query->whereNotNull('coupon_code');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
}
