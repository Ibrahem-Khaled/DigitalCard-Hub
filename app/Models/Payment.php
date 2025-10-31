<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'user_id', 'payment_method', 'payment_gateway', 'gateway_transaction_id',
        'amount', 'currency', 'status', 'gateway_response', 'processed_at', 'failed_at',
        'refunded_at', 'refund_amount', 'refund_reason', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'refund_amount' => 'decimal:2',
            'gateway_response' => 'array',
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
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
     * العلاقة مع المستخدم
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * التحقق من حالة الدفع
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isPartiallyRefunded(): bool
    {
        return $this->status === 'partially_refunded';
    }

    /**
     * الحصول على حالة الدفع بالعربية
     */
    public function getStatusInArabic(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'successful' => 'نجح',
            'failed' => 'فشل',
            'refunded' => 'مسترد بالكامل',
            'partially_refunded' => 'مسترد جزئياً',
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
            'successful' => 'success',
            'failed' => 'danger',
            'refunded' => 'secondary',
            'partially_refunded' => 'info',
            default => 'secondary'
        };
    }

    /**
     * تحديث حالة الدفع
     */
    public function updateStatus(string $status): bool
    {
        $this->status = $status;

        // تحديث التواريخ المناسبة
        switch ($status) {
            case 'successful':
                $this->processed_at = now();
                break;
            case 'failed':
                $this->failed_at = now();
                break;
            case 'refunded':
            case 'partially_refunded':
                $this->refunded_at = now();
                break;
        }

        return $this->save();
    }

    /**
     * إضافة استرداد
     */
    public function addRefund(float $amount, string $reason = null): bool
    {
        $this->refund_amount += $amount;

        if ($this->refund_amount >= $this->amount) {
            $this->status = 'refunded';
        } else {
            $this->status = 'partially_refunded';
        }

        $this->refund_reason = $reason;
        $this->refunded_at = now();

        return $this->save();
    }

    /**
     * الحصول على المبلغ المتبقي للاسترداد
     */
    public function getRemainingRefundAmount(): float
    {
        return max(0, $this->amount - $this->refund_amount);
    }

    /**
     * التحقق من إمكانية الاسترداد
     */
    public function canRefund(): bool
    {
        return $this->isSuccessful() && $this->getRemainingRefundAmount() > 0;
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
     * الحصول على بوابة الدفع بالعربية
     */
    public function getPaymentGatewayInArabic(): string
    {
        return match($this->payment_gateway) {
            'stripe' => 'سترايب',
            'paypal' => 'باي بال',
            'square' => 'سكوير',
            'razorpay' => 'رازور باي',
            'moyasar' => 'مويصر',
            'tap' => 'تاب',
            'fawry' => 'فوري',
            'valu' => 'فاليو',
            default => $this->payment_gateway
        };
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

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByPaymentGateway($query, string $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->whereIn('status', ['refunded', 'partially_refunded']);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeByAmountRange($query, $minAmount, $maxAmount)
    {
        return $query->whereBetween('amount', [$minAmount, $maxAmount]);
    }
}
