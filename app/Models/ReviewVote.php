<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'user_id',
        'vote_type',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Get the review that owns the vote.
     */
    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class, 'review_id');
    }

    /**
     * Get the user that owns the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if vote is helpful.
     */
    public function isHelpful(): bool
    {
        return $this->vote_type === 'helpful';
    }

    /**
     * Check if vote is not helpful.
     */
    public function isNotHelpful(): bool
    {
        return $this->vote_type === 'not_helpful';
    }

    /**
     * Scope to get helpful votes.
     */
    public function scopeHelpful($query)
    {
        return $query->where('vote_type', 'helpful');
    }

    /**
     * Scope to get not helpful votes.
     */
    public function scopeNotHelpful($query)
    {
        return $query->where('vote_type', 'not_helpful');
    }

    /**
     * Scope to get votes by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get votes by review.
     */
    public function scopeByReview($query, int $reviewId)
    {
        return $query->where('review_id', $reviewId);
    }
}

