<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'module',
        'action',
        'is_active',
        'is_system',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the roles for the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include system permissions.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include non-system permissions.
     */
    public function scopeNonSystem($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope a query to filter by module.
     */
    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('module')->orderBy('action');
    }

    /**
     * Get permissions grouped by module.
     */
    public static function getGroupedByModule(): array
    {
        return static::active()
                    ->select('module', 'slug', 'display_name', 'action', 'description')
                    ->get()
                    ->groupBy('module')
                    ->toArray();
    }

    /**
     * Get permissions grouped by action.
     */
    public static function getGroupedByAction(): array
    {
        return static::active()
                    ->select('module', 'slug', 'display_name', 'action', 'description')
                    ->get()
                    ->groupBy('action')
                    ->toArray();
    }

    /**
     * Get all available modules.
     */
    public static function getModules(): array
    {
        return static::active()
                    ->distinct()
                    ->pluck('module')
                    ->filter()
                    ->sort()
                    ->values()
                    ->toArray();
    }

    /**
     * Get all available actions.
     */
    public static function getActions(): array
    {
        return static::active()
                    ->distinct()
                    ->pluck('action')
                    ->filter()
                    ->sort()
                    ->values()
                    ->toArray();
    }

    /**
     * Check if permission can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system && $this->roles()->count() === 0;
    }

    /**
     * Get permission's display name with fallback.
     */
    public function getDisplayNameAttribute($value): string
    {
        return $value ?: $this->name;
    }

    /**
     * Get permission's full name (module + action).
     */
    public function getFullNameAttribute(): string
    {
        if ($this->module && $this->action) {
            return ucfirst($this->module) . ' - ' . ucfirst($this->action);
        }

        return $this->display_name;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = Str::slug($permission->name);
            }
        });

        static::updating(function ($permission) {
            if ($permission->isDirty('name') && empty($permission->slug)) {
                $permission->slug = Str::slug($permission->name);
            }
        });
    }
}
