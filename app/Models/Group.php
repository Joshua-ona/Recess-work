<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];
    public function discussions()
{
    return $this->hasMany(Discussion::class);
}
public function members()
{
    return $this->belongsToMany(User::class, 'group_user')->withPivot('agreed_terms')->withTimestamps();
}
}
