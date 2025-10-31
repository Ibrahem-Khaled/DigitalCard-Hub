<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'cost_price',
        'category_id',
        'brand',
        'image',
        'gallery',
        'is_digital',
        'is_active',
        'is_featured',
        'loyalty_points_earn',
        'loyalty_points_cost',
        'card_type',
        'card_provider',
        'card_region',
        'card_denominations',
        'is_instant_delivery',
        'delivery_instructions',
        'meta_title',
        'meta_description',
        'tags',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'gallery' => 'array',
            'is_digital' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'loyalty_points_earn' => 'integer',
            'loyalty_points_cost' => 'integer',
            'card_denominations' => 'array',
            'is_instant_delivery' => 'boolean',
            'tags' => 'array',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the digital cards for the product.
     */
    public function digitalCards(): HasMany
    {
        return $this->hasMany(DigitalCard::class);
    }

    /**
     * Get the product reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the current price (sale price if available, otherwise regular price).
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if product is on sale.
     */
    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Check if product can be purchased with loyalty points.
     */
    public function canPurchaseWithPoints(): bool
    {
        return $this->loyalty_points_cost > 0;
    }

    /**
     * Get loyalty points earned for this product.
     */
    public function getLoyaltyPointsEarned(): int
    {
        return $this->loyalty_points_earn;
    }

    /**
     * Get loyalty points required to purchase this product.
     */
    public function getLoyaltyPointsRequired(): int
    {
        return $this->loyalty_points_cost;
    }

    /**
     * Get available denominations for this card.
     */
    public function getAvailableDenominations(): array
    {
        return $this->card_denominations ?? [];
    }

    /**
     * Check if product has instant delivery.
     */
    public function hasInstantDelivery(): bool
    {
        return $this->is_instant_delivery;
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get only digital products.
     */
    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    /**
     * Scope to get products on sale.
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
                    ->where('sale_price', '<', \DB::raw('price'));
    }

    /**
     * Scope to get products by card type.
     */
    public function scopeByCardType($query, $cardType)
    {
        return $query->where('card_type', $cardType);
    }

    /**
     * Scope to get products by card provider.
     */
    public function scopeByCardProvider($query, $cardProvider)
    {
        return $query->where('card_provider', $cardProvider);
    }

    /**
     * Scope to get products by card region.
     */
    public function scopeByCardRegion($query, $cardRegion)
    {
        return $query->where('card_region', $cardRegion);
    }

    /**
     * Scope to get products that can be purchased with loyalty points.
     */
    public function scopePurchasableWithPoints($query)
    {
        return $query->where('loyalty_points_cost', '>', 0);
    }

    /**
     * Scope to get products with instant delivery.
     */
    public function scopeInstantDelivery($query)
    {
        return $query->where('is_instant_delivery', true);
    }

    /**
     * Scope to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}

