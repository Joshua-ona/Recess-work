<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Group;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_enabled',
        'warning_count',
        'email_verified_at',
        'last_warning_at',
        'blacklisted_until',
        'status', // Added status field
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'timestamp',
        'last_warning_at' => 'timestamp',
        'blacklisted_until' => 'timestamp',
        'password' => 'hashed',
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Role check methods
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    public function isGroupAdmin(): bool
    {
        return $this->role === 'group_admin';
    }

    public function isSystemAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a super admin
     * Super admin bypasses email verification
     */
    public function isSuperAdmin(): bool
    {
        return $this->isSystemAdmin() && 
               $this->email === 'admin@mak.ac.ug' && 
               $this->is_enabled === true &&
               !is_null($this->email_verified_at);
    }

    /**
     * Check if the user needs email verification
     * Super admins don't need verification
     */
    public function needsVerification(): bool
    {
        // Super admin doesn't need verification
        if ($this->isSuperAdmin()) {
            return false;
        }

        // Check if user is enabled and email is verified
        return !$this->is_enabled || is_null($this->email_verified_at);
    }

    /**
     * Check if the user is blacklisted
     */
    public function isBlacklisted(): bool
    {
        return $this->status === 'blacklisted' || 
               (!is_null($this->blacklisted_until) && $this->blacklisted_until >= now());
    }

    /**
     * Check if the user is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->is_enabled && 
               !is_null($this->email_verified_at) &&
               !$this->isBlacklisted();
    }

    /**
     * Relationships
     */
    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class);
    }

    /**
     * Warning messages issued to this user, newest first.
     */
    public function warnings()
    {
        return $this->hasMany(UserWarning::class)->latest();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('is_enabled', true)
                     ->whereNotNull('email_verified_at');
    }

    /**
     * Scope for pending users
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->orWhere('is_enabled', false)
                     ->orWhereNull('email_verified_at');
    }

    /**
     * Scope for blacklisted users
     */
    public function scopeBlacklisted($query)
    {
        return $query->where('status', 'blacklisted')
                     ->orWhere(function($q) {
                         $q->whereNotNull('blacklisted_until')
                           ->where('blacklisted_until', '>=', now());
                     });
    }

    /**
     * Check if user can perform admin actions
     */
    public function canAdminister(): bool
    {
        return $this->isSystemAdmin() || $this->isSuperAdmin();
    }

    /**
     * Get the user's role label
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'System Administrator',
            'lecturer' => 'Lecturer',
            'student' => 'Student',
            'group_admin' => 'Group Administrator',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Determine if the user has been warned
     */
    public function hasWarnings(): bool
    {
        return $this->warning_count > 0;
    }

    /**
     * Get the number of active warnings
     */
    public function getActiveWarningCount(): int
    {
        // If blacklisted, return warning count, but could add logic for warning expiry
        return $this->warning_count ?? 0;
    }
}