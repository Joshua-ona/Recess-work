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

// Whatever the student already answered (on this page or earlier ones)
// so we can pre-select those options when the page renders.
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

    // Merge this page's answers into whatever has already been answered
    // on previous pages, so nothing gets lost when navigating back and forth.
    // NOTE: array_merge() would renumber these since question_id keys are
    // numeric — the + operator preserves the actual question_id keys.
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

    // Quiz finished (or time ran out) — score once from the full set of
    // saved answers, then clear the session so the next attempt starts fresh.
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

/**
 * Score a full set of question_id => selected_option_id answers against
 * the quiz's correct answers. Kept as a single pass so re-visiting pages
 * or resubmitting never double-counts a question.
 */
private function scoreAnswers(Quiz $quiz, array $answers): int
{
    $score = 0;

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

    return $score;
}

}
