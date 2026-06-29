<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'is_enabled',
        'warning_count',
        'last_warning_at',
        'blacklisted_until',
    ];

    protected $hidden = [
        'password','remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_warning_at' => 'datetime',
        'blacklisted_until' => 'datetime',
        'password' => 'hashed',
        'is_enabled'=>'boolean',
    ];

    public function isStudent(): bool {return $this->role == 'student';}
    public function isLecturer(): bool {return $this->role == 'lecturer';}
    public function isGroupAdmin(): bool {return $this->role == 'group_admin';}
    public function isSystemAdmin(): bool {return $this->role == 'system_admin';}



    
}
