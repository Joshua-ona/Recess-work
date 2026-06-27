<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $primaryKey = 'quiz_id';

    protected $fillable = [
        'created_by',
        'group_id',
        'title',
        'start_time',
        'duration_mins',
        'target_category',
        'is_published',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id', 'quiz_id');
    }
}