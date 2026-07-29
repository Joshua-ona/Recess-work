<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Group;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory,Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

  protected $fillable = [
    'first_name','last_name','email','password','role','status','is_enabled',
    'warning_count','email_verified_at','last_warning_at','blacklisted_until',
    'invite_token','invite_token_expires_at',
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
        'email_verified_at' => 'timestamp',
        'last_warning_at' => 'timestamp',
        'blacklisted_until' => 'timestamp',
        'password' => 'hashed',
        'is_enabled'=>'boolean',
    ];

    public function isStudent(): bool {return $this->role == 'student';}
    public function isLecturer(): bool {return $this->role == 'lecturer';}
    public function isGroupAdmin(): bool {return $this->role == 'group_admin';}
    public function isSystemAdmin(): bool {return $this->role == 'system_admin';}

    /**
     * "First Last" — used all over the views/controllers. There's no
     * full_name column; this derives it from first_name/last_name so
     * every existing $user->full_name reference works.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

 

    public function verification(): HasOne
    {
        return $this->hasOne(Verification::class);

    }

    public function discussions()
{
    return $this->hasMany(Discussion::class);
}

public function replies()
{
    return $this->hasMany(Reply::class);
}
    
         /* Warning messages issued to this user, newest first.
     */
    public function warnings()
    {
        return $this->hasMany(UserWarning::class)->latest();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_members')->withPivot('role')->withTimestamps();
    }
    public function notifications()
{
    return $this->hasMany(Notification::class);
}

    
}