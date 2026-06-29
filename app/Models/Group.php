<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
