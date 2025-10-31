<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'type',
        'priority',
        'status',
        'assigned_to',
        'admin_response',
        'responded_at',
        'ip_address',
        'user_agent',
        'attachments',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'attachments' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTypeTextAttribute(): string
    {
        return match($this->type) {
            'general' => 'عام',
            'support' => 'دعم فني',
            'complaint' => 'شكوى',
            'suggestion' => 'اقتراح',
            'business' => 'أعمال',
            'technical' => 'تقني',
            default => 'غير محدد'
        };
    }

    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
            default => 'غير محدد'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'new' => 'جديد',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
            'spam' => 'مزعج',
            default => 'غير محدد'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'success',
            'medium' => 'info',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'primary',
            'in_progress' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            'spam' => 'danger',
            default => 'secondary'
        };
    }

    public function getIsRespondedAttribute(): bool
    {
        return !is_null($this->responded_at);
    }

    public function getIsAssignedAttribute(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function getIsRegisteredUserAttribute(): bool
    {
        return !is_null($this->user_id);
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('Y-m-d H:i');
    }

    public function getFormattedRespondedAtAttribute(): string
    {
        return $this->responded_at ? $this->responded_at->format('Y-m-d H:i') : 'لم يتم الرد';
    }

    // Methods
    public function markAsInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markAsResolved(): void
    {
        $this->update(['status' => 'resolved']);
    }

    public function markAsClosed(): void
    {
        $this->update(['status' => 'closed']);
    }

    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }

    public function assignTo(User $user): void
    {
        $this->update(['assigned_to' => $user->id]);
    }

    public function respond(string $response): void
    {
        $this->update([
            'admin_response' => $response,
            'responded_at' => now(),
        ]);
    }

    public function updatePriority(string $priority): void
    {
        $this->update(['priority' => $priority]);
    }

    public function updateType(string $type): void
    {
        $this->update(['type' => $type]);
    }
}
