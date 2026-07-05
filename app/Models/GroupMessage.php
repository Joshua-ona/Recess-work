<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMessage extends Model
{
    protected $fillable = ['group_id','user_id','body'];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     public function group():BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}