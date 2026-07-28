<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Quiz;

class Question extends Model
{
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'quiz_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
    ];

   
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id', 'quiz_id');
    }
    public function getOptionsAttribute()
{
    return [
        (object)[
            'id' => 'a',
            'option_text' => $this->option_a,
        ],
        (object)[
            'id' => 'b',
            'option_text' => $this->option_b,
        ],
        (object)[
            'id' => 'c',
            'option_text' => $this->option_c,
        ],
        (object)[
            'id' => 'd',
            'option_text' => $this->option_d,
        ],
    ];
}
}