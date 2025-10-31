<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_message_id',
        'knowledge_base_id',
        'prompt',
        'response',
        'model',
        'tokens_used',
        'processing_time',
        'confidence_score',
        'sources',
        'metadata',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
            'processing_time' => 'decimal:3',
            'confidence_score' => 'decimal:2',
            'sources' => 'array',
            'metadata' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    /**
     * Get the chat message that owns the AI response.
     */
    public function chatMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class);
    }

    /**
     * Get the knowledge base item that was used.
     */
    public function knowledgeBase(): BelongsTo
    {
        return $this->belongsTo(AIKnowledgeBase::class);
    }

    /**
     * Check if response has high confidence.
     */
    public function hasHighConfidence(): bool
    {
        return $this->confidence_score >= 0.8;
    }

    /**
     * Check if response has medium confidence.
     */
    public function hasMediumConfidence(): bool
    {
        return $this->confidence_score >= 0.5 && $this->confidence_score < 0.8;
    }

    /**
     * Check if response has low confidence.
     */
    public function hasLowConfidence(): bool
    {
        return $this->confidence_score < 0.5;
    }

    /**
     * Get response quality based on confidence score.
     */
    public function getQualityAttribute(): string
    {
        if ($this->hasHighConfidence()) {
            return 'high';
        } elseif ($this->hasMediumConfidence()) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Scope to get responses by model.
     */
    public function scopeByModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope to get high confidence responses.
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 0.8);
    }

    /**
     * Scope to get responses by knowledge base.
     */
    public function scopeByKnowledgeBase($query, int $knowledgeBaseId)
    {
        return $query->where('knowledge_base_id', $knowledgeBaseId);
    }

    /**
     * Scope to get recent responses.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('generated_at', '>=', now()->subHours($hours));
    }
}

