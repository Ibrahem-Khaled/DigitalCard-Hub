<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'is_verified',
        'is_approved',
        'helpful_count',
        'not_helpful_count',
        'reported_count',
        'status',
        'moderated_at',
        'moderated_by',
        'moderation_notes',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
            'not_helpful_count' => 'integer',
            'reported_count' => 'integer',
            'moderated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user that owns the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that owns the review.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the review votes.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Get the review reports.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    /**
     * Check if review is approved.
     */
    public function isApproved(): bool
    {
        return $this->is_approved;
    }

    /**
     * Check if review is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if review is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if review is verified.
     */
    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    /**
     * Approve the review.
     */
    public function approve(int $moderatedBy, ?string $notes = null): void
    {
        $this->update([
            'is_approved' => true,
            'status' => 'approved',
            'moderated_at' => now(),
            'moderated_by' => $moderatedBy,
            'moderation_notes' => $notes,
        ]);
    }

    /**
     * Reject the review.
     */
    public function reject(int $moderatedBy, ?string $notes = null): void
    {
        $this->update([
            'is_approved' => false,
            'status' => 'rejected',
            'moderated_at' => now(),
            'moderated_by' => $moderatedBy,
            'moderation_notes' => $notes,
        ]);
    }

    /**
     * Increment helpful count.
     */
    public function incrementHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Increment not helpful count.
     */
    public function incrementNotHelpful(): void
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Increment reported count.
     */
    public function incrementReported(): void
    {
        $this->increment('reported_count');
    }

    /**
     * Get helpful percentage.
     */
    public function getHelpfulPercentageAttribute(): float
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        
        if ($total === 0) {
            return 0;
        }

        return ($this->helpful_count / $total) * 100;
    }

    /**
     * Scope to get only approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get reviews by rating.
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope to get verified reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get reviews by product.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to get reviews by user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}

