<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'total_price',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Get the cart that owns the cart item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product that owns the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the total price for this item.
     */
    public function calculateTotal(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Update the total price.
     */
    public function updateTotal(): void
    {
        $this->update(['total_price' => $this->calculateTotal()]);
    }

    /**
     * Increase quantity by given amount.
     */
    public function increaseQuantity(int $amount = 1): void
    {
        $this->update([
            'quantity' => $this->quantity + $amount,
        ]);
        $this->updateTotal();
    }

    /**
     * Decrease quantity by given amount.
     */
    public function decreaseQuantity(int $amount = 1): void
    {
        $newQuantity = max(0, $this->quantity - $amount);
        
        if ($newQuantity === 0) {
            $this->delete();
        } else {
            $this->update([
                'quantity' => $newQuantity,
            ]);
            $this->updateTotal();
        }
    }

    /**
     * Set quantity to specific amount.
     */
    public function setQuantity(int $quantity): void
    {
        if ($quantity <= 0) {
            $this->delete();
            return;
        }

        // تحميل المنتج مع التحقق من المخزون
        $this->load('product');
        $product = $this->product;

        if (!$product) {
            throw new \Exception('المنتج غير موجود');
        }

        // التحقق من المخزون للمنتجات الرقمية
        if ($product->is_digital) {
            $product->loadCount(['digitalCards as available_cards_count' => function($query) {
                $query->where('is_used', false)
                      ->where('status', 'active')
                      ->where(function ($q) {
                          $q->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>', now());
                      });
            }]);
            
            $availableStock = $product->available_cards_count ?? $product->stock_quantity;
            
            // حساب الكمية المطلوبة (الكمية الجديدة - الكمية الحالية في السلة)
            // نحتاج للتحقق من الكمية الجديدة فقط لأن الكمية الحالية موجودة بالفعل
            // لكن يجب التحقق من أن الكمية الجديدة لا تتجاوز المخزون المتاح
            if ($availableStock < $quantity) {
                throw new \Exception("الكمية المطلوبة غير متوفرة. المتوفر في المخزون: {$availableStock}");
            }
        } else {
            // للمنتجات غير الرقمية، التحقق من توفر المنتج
            if (!$product->is_in_stock) {
                throw new \Exception('المنتج غير متوفر حالياً');
            }
        }

        $this->update([
            'quantity' => $quantity,
        ]);
        $this->updateTotal();
    }
}

