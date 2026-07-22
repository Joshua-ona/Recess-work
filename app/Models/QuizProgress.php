<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizProgress extends Model
{
    protected $table = 'QuizProgress';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'deadline',
        'answers',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'answers' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
