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
  $perPage = 5;

$questions = $quiz->questions->values();

$pageQuestions = $questions->slice(
    ($number - 1) * $perPage,
    $perPage
);;

   return view('student.quizzes.attempt', [
    'quiz' => $quiz,
    'questions' => $pageQuestions,
    'currentPage' => $number,
    'totalPages' => ceil($questions->count() / $perPage),
    'previousPage' => $number > 1 ? $number - 1 : null,
]);
}
public function answer(Request $request, Quiz $quiz)
{
    $answers = $request->input('answers', []);

    $score = session('score', 0);

    foreach ($answers as $questionId => $selectedAnswer) {

        $question = $quiz->questions()
            ->where('question_id', $questionId)
            ->first();

        if (!$question) {
            continue;
        }

        if ($selectedAnswer == $question->correct_answer) {
            $score++;
        }
    }

    session(['score' => $score]);

    $nextPage = $request->input('next');

    $perPage = 5;
    $totalPages = ceil($quiz->questions()->count() / $perPage);

    if ($nextPage <= $totalPages) {
        return redirect()->route('student.quizzes.attempt', [
            'quiz' => $quiz->quiz_id,
            'page' => $nextPage,
        ]);
    }

    return view('student.quizzes.result', [
        'quiz' => $quiz,
        'score' => $score,
        'total' => $quiz->questions()->count(),
    ]);
}
}
