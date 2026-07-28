<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Question;

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

    protected $casts = [
        'start_time' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id', 'quiz_id');
    }
    public function group()
{
    return $this->belongsTo(Group::class, 'group_id');
}

public function submissions()
{
    return $this->hasMany(QuizSubmission::class, 'quiz_id', 'quiz_id');
}
}