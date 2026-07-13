<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    protected $primaryKey = 'submission_id';
    
    // Keep ONLY ONE $fillable declaration
    protected $fillable = [
        'quiz_id',
        'user_id',
        'score',
        'submitted_at',
        'review_answers',
        'auto_submitted'
    ];

    protected $casts = [
        'review_answers' => 'array',
        'auto_submitted' => 'boolean',
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