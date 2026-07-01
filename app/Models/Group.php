<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\{BelongsTo,HasMany};
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
=======
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
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f
