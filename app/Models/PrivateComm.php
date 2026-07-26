<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateComm extends Model
{

    protected $table = 'private_comms';



    protected $fillable = [

        'sender_id',

        'receiver_id',

        'content',

        'is_read',

        'deleted',

        'created_at',

        'updated_at',

    ];



    protected $casts = [

        'is_read' => 'boolean',

        'deleted' => 'boolean',

        'created_at' => 'datetime',

        'updated_at' => 'datetime',

    ];





    public function sender()
    {

        return $this->belongsTo(
            User::class,
            'sender_id'
        );

    }





    public function receiver()
    {

        return $this->belongsTo(
            User::class,
            'receiver_id'
        );

    }

}