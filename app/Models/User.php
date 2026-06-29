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
        'last_active_at',
        'warning_count',
        'status',
        'last_warning_at',
        'blacklisted_until',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


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

         /* Warning messages issued to this user, newest first.
     */
    public function warnings()
    {
        return $this->hasMany(UserWarning::class)->latest();
    }


    
}
