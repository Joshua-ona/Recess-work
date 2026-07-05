<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class Group extends Model
{
    protected $fillable = ['name','description','admin_id','status'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')->withPivot('role')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class)->oldest();
    }

     public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
}