<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizSubmission;

class StudentQuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::where('is_published', 1)
                        ->orderBy('start_time')
                        ->get();

        return view('student.quizzes.index', compact('quizzes'));
    }

    public function attempt(Quiz $quiz, $number = 1)
    {
        $perPage = 5;
        $questions = $quiz->questions->values();
        $pageQuestions = $questions->slice(($number - 1) * $perPage, $perPage);

        $deadlineKey = 'quiz_deadline_' . $quiz->quiz_id;
        $answersKey = 'quiz_answers_' . $quiz->quiz_id;

        if (!session()->has($deadlineKey)) {
            session([$deadlineKey => now()->addMinutes($quiz->duration_mins ?? 90)]);
        }

        $remainingSeconds = max(0, session($deadlineKey)->getTimestamp() - now()->getTimestamp());

        // Time already ran out
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

        // Save to database using QuizSubmission
        $this->saveQuizSubmission($quiz, $savedAnswers, $score, false);

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

    // NEW: Save quiz submission using your existing QuizSubmission model
    private function saveQuizSubmission($quiz, $answers, $score, $autoSubmitted = false)
    {
        $userId = auth()->id();

        // Check if submission exists
        $submission = QuizSubmission::where('quiz_id', $quiz->quiz_id)
            ->where('user_id', $userId)
            ->first();

        $reviewAnswers = [];
        foreach ($answers as $questionId => $selectedOption) {
            $question = $quiz->questions()->where('question_id', $questionId)->first();
            if ($question) {
                $reviewAnswers[$questionId] = [
                    'selected' => $selectedOption,
                    'correct' => $question->correct_answer,
                    'is_correct' => strtolower($selectedOption) == strtolower($question->correct_answer)
                ];
            }
        }

        if ($submission) {
            $submission->score = $score;
            $submission->review_answers = $reviewAnswers;
            $submission->auto_submitted = $autoSubmitted;
            $submission->submitted_at = now();
            $submission->save();
        } else {
            QuizSubmission::create([
                'quiz_id' => $quiz->quiz_id,
                'user_id' => $userId,
                'score' => $score,
                'review_answers' => $reviewAnswers,
                'auto_submitted' => $autoSubmitted,
                'submitted_at' => now(),
            ]);
        }
    }

    
    public function submitAll(Request $request, $quizId)
    {
        try {
            $answers = json_decode($request->all_answers, true);
            $autoSubmitted = $request->auto_submitted;
            $userId = auth()->id();

            // Get the quiz
            $quiz = Quiz::findOrFail($quizId);

            // Calculate score
            $score = $this->scoreAnswers($quiz, $answers);

            // Save using QuizSubmission
            $this->saveQuizSubmission($quiz, $answers, $score, $autoSubmitted);

            // Clear session data
            session()->forget('quiz_answers_' . $quizId);
            session()->forget('quiz_deadline_' . $quizId);

            return response()->json([
                'success' => true,
                'message' => 'Quiz submitted successfully',
                'redirect' => route('student.quizzes.results', $quizId)
            ]);

        } catch (\Exception $e) {
            \Log::error('Quiz submission error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error submitting quiz: ' . $e->getMessage()
            ], 500);
        }
    }

    public function results(Quiz $quiz)
    {
        $userId = auth()->id();

        // Get the submission
        $submission = QuizSubmission::where('quiz_id', $quiz->quiz_id)
            ->where('user_id', $userId)
            ->first();

        $score = $submission ? $submission->score : 0;
        $total = $quiz->questions()->count();

        return view('student.quizzes.result', [
            'quiz' => $quiz,
            'score' => $score,
            'total' => $total,
            'submission' => $submission,
            'timedOut' => false
        ]);
    }
}