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
        } else {
            $this->update([
                'quantity' => $quantity,
            ]);
            $this->updateTotal();
        }
    }
}

