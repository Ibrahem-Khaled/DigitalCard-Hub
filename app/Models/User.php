<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'avatar',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        'postal_code',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user's display name (first letter of first and last name).
     */
    public function getDisplayNameAttribute(): string
    {
        $firstInitial = mb_substr($this->first_name, 0, 1);
        $lastInitial = mb_substr($this->last_name, 0, 1);
        return $firstInitial . $lastInitial;
    }

    /**
     * Get the user's roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
                    ->withPivot(['assigned_at', 'assigned_by'])
                    ->withTimestamps();
    }

    /**
     * Get the user's permissions through roles.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_roles', 'user_id', 'permission_id')
                    ->join('roles', 'user_roles.role_id', '=', 'roles.id')
                    ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
                    ->where('role_permissions.permission_id', '=', 'permissions.id')
                    ->distinct();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string|array $roleSlug): bool
    {
        if (is_array($roleSlug)) {
            return $this->roles()->whereIn('slug', $roleSlug)->exists();
        }
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    /**
     * Check if user has any of the given roles.
     */
    public function hasAnyRole(array $roleSlugs): bool
    {
        return $this->roles()->whereIn('slug', $roleSlugs)->exists();
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs): bool
    {
        return $this->permissions()->whereIn('slug', $permissionSlugs)->exists();
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get users assigned by this user.
     */
    public function assignedUsers(): HasMany
    {
        return $this->hasMany(UserRole::class, 'assigned_by');
    }

    /**
     * Get the user sessions.
     */
    public function userSessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }

    /**
     * Get the user's carts.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the user's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the user's payments.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user's product reviews.
     */
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the user's review votes.
     */
    public function reviewVotes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Get the user's review reports.
     */
    public function reviewReports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's chat sessions.
     */
    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    /**
     * Get the user's chat messages.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the user's loyalty points.
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Get the user's loyalty point transactions.
     */
    public function loyaltyPointTransactions(): HasMany
    {
        return $this->hasMany(LoyaltyPointTransaction::class);
    }

    /**
     * Get referrals made by this user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    /**
     * Get referrals received by this user.
     */
    public function referredBy(): HasMany
    {
        return $this->hasMany(Referral::class, 'referred_id');
    }

    /**
     * Get the user's referral rewards.
     */
    public function referralRewards(): HasMany
    {
        return $this->hasMany(ReferralReward::class);
    }

    /**
     * Get the user's coupon usages.
     */
    public function couponUsages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Get digital cards used by this user.
     */
    public function usedDigitalCards(): HasMany
    {
        return $this->hasMany(DigitalCard::class, 'used_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function assignedContacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'assigned_to');
    }

    /**
     * Get the user's verification codes.
     */
    public function verificationCodes(): HasMany
    {
        return $this->hasMany(VerificationCode::class);
    }

    /**
     * Get total loyalty points for user.
     */
    public function getTotalLoyaltyPointsAttribute(): int
    {
        // يمكن إضافة منطق حساب النقاط هنا لاحقاً
        return 0;
    }

    /**
     * Get user's referral code.
     */
    public function getReferralCodeAttribute(): string
    {
        return 'REF-' . strtoupper(substr(md5($this->id . $this->email), 0, 8));
    }

    /**
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get users by role.
     */
    public function scopeWithRole($query, string $roleSlug)
    {
        return $query->whereHas('roles', function ($q) use ($roleSlug) {
            $q->where('slug', $roleSlug);
        });
    }
}
