<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'user_id',
        'message',
        'type',
        'sender',
        'metadata',
        'ai_response',
        'processing_time',
        'tokens_used',
        'sent_at',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'ai_response' => 'array',
            'processing_time' => 'decimal:3',
            'tokens_used' => 'integer',
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the chat session that owns the message.
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Get the user that owns the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message is from user.
     */
    public function isFromUser(): bool
    {
        return $this->sender === 'user';
    }

    /**
     * Check if message is from AI.
     */
    public function isFromAI(): bool
    {
        return $this->sender === 'ai';
    }

    /**
     * Check if message is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Get message content based on type.
     */
    public function getContentAttribute(): string
    {
        if ($this->type === 'text') {
            return $this->message;
        } elseif ($this->type === 'ai_response') {
            return $this->ai_response['content'] ?? $this->message;
        }

        return $this->message;
    }

    /**
     * Scope to get only user messages.
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender', 'user');
    }

    /**
     * Scope to get only AI messages.
     */
    public function scopeFromAI($query)
    {
        return $query->where('sender', 'ai');
    }

    /**
     * Scope to get messages by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to get recent messages.
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('sent_at', '>=', now()->subHours($hours));
    }
}

