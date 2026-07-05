<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;

class StudentQuizController extends Controller
{
  public function index()
{
    $quizzes = Quiz::where('is_published', 1)
                    ->orderBy('start_time')
                    ->get();

    return view('student.quizzes.index', compact('quizzes'));
}  //
public function attempt(Quiz $quiz,$number=1)
{
  $questions = $quiz->questions;

    $question = $questions->values()->get($number - 1);

    return view('student.quizzes.attempt',[
        'quiz'=>$quiz,
        'question'=>$question,
        'currentQuestion'=>$number,
        'totalQuestions'=>$questions->count(),
        'previousQuestion'=>$number>1 ? $number-1 : null,
    ]);
}
public function answer(Request $request, Quiz $quiz, $question)
{
    $selected = $request->answer;

    $questions = $quiz->questions;
    $currentQuestion = $questions->firstWhere('question_id', $question);

    if (!$currentQuestion) {
        abort(404);
    }

    // check correctness
    $isCorrect = $selected === $currentQuestion->correct_answer;

    // TODO: save result (optional for now)

    // move to next question
    $nextNumber = $request->get('next', null);

    if ($nextNumber) {
        return redirect()->route('student.quizzes.attempt', [
            'quiz' => $quiz->quiz_id,
            'number' => $nextNumber
        ]);
    }

    return redirect()->route('student.quizzes.attempt', [
        'quiz' => $quiz->quiz_id,
        'number' => 1
    ]);
}
}
