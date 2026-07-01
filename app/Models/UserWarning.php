<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWarning extends Model
{
    protected $fillable = [
        'user_id',
        'issued_by',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * The member who received the warning.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin who issued it. Nullable in case that admin account is
     * later deleted — the warning itself should still stand.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
