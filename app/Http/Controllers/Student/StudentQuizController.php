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
        $userId = auth()->id();

        $attemptedQuizIds = QuizSubmission::where('user_id', $userId)->pluck('quiz_id');

        $quizzes = Quiz::where('is_published', 1)
                        ->whereNotIn('quiz_id', $attemptedQuizIds)
                        ->orderBy('start_time')
                        ->get()
                        // A quiz disappears entirely once its scheduled window has closed,
                        // whether or not the student ever attempted it.
                        ->filter(function ($quiz) {
                            return now()->lessThan($this->quizWindowEnd($quiz));
                        })
                        ->values();

        return view('student.quizzes.index', compact('quizzes'));
    }

    private function quizWindowEnd(Quiz $quiz)
    {
        return \Carbon\Carbon::parse($quiz->start_time)->addMinutes($quiz->duration_mins ?? 90);
    }

    public function attempt(Quiz $quiz, $number = 1)
    {
        $userId = auth()->id();

        // Block re-entry once the student already has a submission for this quiz.
        $alreadySubmitted = QuizSubmission::where('quiz_id', $quiz->quiz_id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('student.quizzes')
                ->with('error', 'You have already attempted "' . $quiz->title . '".');
        }

        // Block access before the scheduled start time.
        if (now()->lessThan($quiz->start_time)) {
            return redirect()->route('student.quizzes')
                ->with('error', '"' . $quiz->title . '" opens at ' . \Carbon\Carbon::parse($quiz->start_time)->format('M j, Y g:i A') . '. You can\'t attempt it yet.');
        }

        $quizWindowEnd = $this->quizWindowEnd($quiz);

        // Block access once the scheduled window has fully closed.
        if (now()->greaterThanOrEqualTo($quizWindowEnd)) {
            return redirect()->route('student.quizzes')
                ->with('error', 'The window for "' . $quiz->title . '" has closed.');
        }

        $perPage = 5;
        $questions = $quiz->questions->values();
        $pageQuestions = $questions->slice(($number - 1) * $perPage, $perPage);

        $deadlineKey = 'quiz_deadline_' . $quiz->quiz_id;
        $answersKey = 'quiz_answers_' . $quiz->quiz_id;

        if (!session()->has($deadlineKey)) {
           
            session([$deadlineKey => $quizWindowEnd]);
        }

        // Lock the student into this quiz: any other dashboard route will
        // bounce them back here until they finish or the window closes.
        session(['active_quiz_id' => $quiz->quiz_id]);

        $remainingSeconds = max(0, session($deadlineKey)->getTimestamp() - now()->getTimestamp());

        // Time already ran out
        if ($remainingSeconds <= 0) {
            session()->forget($deadlineKey);
            session()->forget('active_quiz_id');
            $savedAnswers = session()->pull($answersKey, []);
            $score = $this->scoreAnswers($quiz, $savedAnswers);

            $this->saveQuizSubmission($quiz, $savedAnswers, $score, true);

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
        session()->forget('active_quiz_id');

        // Save to database using QuizSubmission
        $this->saveQuizSubmission($quiz, $savedAnswers, $score, $timedOut);

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
            $answers = json_decode($request->all_answers, true) ?: [];
            $autoSubmitted = $request->auto_submitted;
            $userId = auth()->id();

            // Get the quiz
            $quiz = Quiz::findOrFail($quizId);

            
            $sessionAnswers = session('quiz_answers_' . $quizId, []);
            $answers = $answers + $sessionAnswers;

            // Calculate score
            $score = $this->scoreAnswers($quiz, $answers);

            // Save using QuizSubmission
            $this->saveQuizSubmission($quiz, $answers, $score, $autoSubmitted);

            // Clear session data
            session()->forget('quiz_answers_' . $quizId);
            session()->forget('quiz_deadline_' . $quizId);
            session()->forget('active_quiz_id');

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