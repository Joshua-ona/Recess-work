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

$deadlineKey = 'quiz_deadline_' . $quiz->quiz_id;
$answersKey = 'quiz_answers_' . $quiz->quiz_id;

// Only set the deadline the first time the student opens this quiz.
// On every later page load we reuse the same deadline, so the timer
// keeps counting down instead of resetting.
if (!session()->has($deadlineKey)) {
    session([$deadlineKey => now()->addMinutes($quiz->duration_mins ?? 90)]);
}

$remainingSeconds = max(0, session($deadlineKey)->getTimestamp() - now()->getTimestamp());

// Time already ran out (e.g. student closed the tab and came back later).
if ($remainingSeconds <= 0) {
    session()->forget($deadlineKey);
    $savedAnswers = session()->pull($answersKey, []);
    $score = $this->scoreAnswers($quiz, $savedAnswers);

    return view('student.quizzes.result', [
        'quiz' => $quiz,
        'score' => $score,
        'total' => $quiz->questions()->count(),
        'timedOut' => true,
    ]);
}


$savedAnswers = session($answersKey, []);

   return view('student.quizzes.attempt', [
    'quiz' => $quiz,
    'questions' => $pageQuestions,
    'currentPage' => $number,
    'totalPages' => ceil($questions->count() / $perPage),
    'previousPage' => $number > 1 ? $number - 1 : null,
    'remainingSeconds' => $remainingSeconds,
    'savedAnswers' => $savedAnswers,
]);
}
public function answer(Request $request, Quiz $quiz)
{
    $answers = $request->input('answers', []);

    $deadlineKey = 'quiz_deadline_' . $quiz->quiz_id;
    $answersKey = 'quiz_answers_' . $quiz->quiz_id;

    
    $savedAnswers = $answers + session($answersKey, []);
    session([$answersKey => $savedAnswers]);

    $nextPage = $request->input('next');

    $perPage = 5;
    $totalPages = ceil($quiz->questions()->count() / $perPage);

    $timedOut = session()->has($deadlineKey)
        && now()->greaterThanOrEqualTo(session($deadlineKey));

    if ($nextPage && $nextPage <= $totalPages && !$timedOut) {
        return redirect()->route('student.quizzes.attempt', [
            'quiz' => $quiz->quiz_id,
            'number' => $nextPage,
        ]);
    }

   
    
    $score = $this->scoreAnswers($quiz, $savedAnswers);

    session()->forget($deadlineKey);
    session()->forget($answersKey);

    return view('student.quizzes.result', [
        'quiz' => $quiz,
        'score' => $score,
        'total' => $quiz->questions()->count(),
        'timedOut' => $timedOut,
    ]);
    
}


public function scoreAnswers(Quiz $quiz, array $answers): int
{
    $score = 0;

    foreach ($answers as $questionId => $selectedAnswer) {
        $question = $quiz->questions()
            ->where('question_id', $questionId)
            ->first();

        if (!$question) {
            continue;
        }

       if (strtolower($selectedAnswer) == strtolower($question->correct_answer)) {
    $score++;

        }
    }

    return $score;
}

}
