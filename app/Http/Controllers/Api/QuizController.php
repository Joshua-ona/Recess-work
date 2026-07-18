<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizProgress;
use App\Models\QuizSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizController extends Controller
{
    /**
     * The absolute moment a quiz's scheduled window closes for everyone,
     * regardless of when an individual student started it. Same rule as
     * the web app's StudentQuizController::quizWindowEnd().
     */
    private function quizWindowEnd(Quiz $quiz): Carbon
    {
        return Carbon::parse($quiz->start_time)->addMinutes($quiz->duration_mins ?? 90);
    }

    /**
     * Available quizzes for the logged-in student: published, not yet
     * attempted, and still within their scheduled window. Requirement #1
     * (disappear after attempt) and part of requirement #2 are both
     * enforced here so a stale/expired/attempted quiz never even reaches
     * the client.
     */
    public function index()
    {
        $userId = Auth::id();

        $attemptedQuizIds = QuizSubmission::where('user_id', $userId)->pluck('quiz_id');

        $quizzes = Quiz::where('is_published', true)
            ->whereNotIn('quiz_id', $attemptedQuizIds)
            ->orderBy('start_time')
            ->get()
            ->filter(fn ($quiz) => now()->lessThan($this->quizWindowEnd($quiz)))
            ->values();

        return response()->json(['quizzes' => $quizzes]);
    }

    public function show($id)
    {
        $quiz = Quiz::findOrFail($id);

        return response()->json(['quiz' => $quiz]);
    }

    public function questions($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);

        return response()->json(['questions' => $quiz->questions]);
    }

    /**
     * Requirement #3 support: is this student currently locked into a
     * quiz? The desktop client calls this on launch and before every
     * navigation attempt. A non-null result means "force them back to
     * the attempt screen and disable everything else."
     */
    public function active()
    {
        $userId = Auth::id();

        $progress = QuizProgress::where('user_id', $userId)->first();

        if (!$progress) {
            return response()->json(['active' => false]);
        }

        // The window closed since they last checked in (e.g. app was
        // closed past the deadline) — auto-submit right now instead of
        // reporting a lock that would just immediately expire client-side.
        if (now()->greaterThanOrEqualTo($progress->deadline)) {
            $this->finalizeSubmission($progress, true);
            return response()->json(['active' => false]);
        }

        return response()->json([
            'active' => true,
            'quiz_id' => $progress->quiz_id,
            'deadline' => $progress->deadline->toIso8601String(),
            'remaining_seconds' => max(0, now()->diffInSeconds($progress->deadline, false)),
        ]);
    }

    /**
     * Begin (or resume) an attempt. Requirement #2: blocks entry before
     * start_time and after the window closes. Creates the shared deadline
     * once — resuming never grants extra time.
     */
    public function start($id)
    {
        $userId = Auth::id();
        $quiz = Quiz::with('questions')->findOrFail($id);

        $alreadySubmitted = QuizSubmission::where('quiz_id', $quiz->quiz_id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadySubmitted) {
            return response()->json([
                'message' => 'You have already attempted "' . $quiz->title . '".',
            ], 409);
        }

        if (now()->lessThan($quiz->start_time)) {
            return response()->json([
                'message' => '"' . $quiz->title . '" opens at '
                    . Carbon::parse($quiz->start_time)->format('M j, Y g:i A') . '. You can\'t attempt it yet.',
            ], 403);
        }

        $windowEnd = $this->quizWindowEnd($quiz);

        if (now()->greaterThanOrEqualTo($windowEnd)) {
            return response()->json([
                'message' => 'The window for "' . $quiz->title . '" has closed.',
            ], 403);
        }

        // A student can only ever have one quiz in progress at a time.
        $existingLock = QuizProgress::where('user_id', $userId)
            ->where('quiz_id', '!=', $quiz->quiz_id)
            ->first();

        if ($existingLock) {
            return response()->json([
                'message' => 'You already have another quiz in progress.',
                'active_quiz_id' => $existingLock->quiz_id,
            ], 409);
        }

        $progress = QuizProgress::firstOrCreate(
            ['user_id' => $userId, 'quiz_id' => $quiz->quiz_id],
            ['deadline' => $windowEnd, 'answers' => []]
        );

        $remainingSeconds = max(0, now()->diffInSeconds($progress->deadline, false));

        if ($remainingSeconds <= 0) {
            $this->finalizeSubmission($progress, true);
            return response()->json(['message' => 'Time already ran out for this quiz.'], 410);
        }

        return response()->json([
            'quiz' => $quiz,
            'questions' => $quiz->questions,
            'deadline' => $progress->deadline->toIso8601String(),
            'remaining_seconds' => $remainingSeconds,
            'saved_answers' => $progress->answers ?: [],
        ]);
    }

    /**
     * Periodic autosave from the client — the server-side source of truth
     * for in-progress answers (equivalent of the web app's session merge
     * in StudentQuizController::answer()).
     */
    public function saveAnswers(Request $request, $id)
    {
        $userId = Auth::id();
        $data = $request->validate(['answers' => 'required|array']);

        $progress = QuizProgress::where('user_id', $userId)
            ->where('quiz_id', $id)
            ->first();

        if (!$progress) {
            return response()->json(['message' => 'No active attempt for this quiz.'], 404);
        }

        if (now()->greaterThanOrEqualTo($progress->deadline)) {
            $this->finalizeSubmission($progress, true);
            return response()->json(['message' => 'Time ran out.', 'auto_submitted' => true], 410);
        }

        $merged = $data['answers'] + ($progress->answers ?: []);
        $progress->update(['answers' => $merged]);

        return response()->json(['message' => 'Saved']);
    }

    /**
     * Final submit — either the student clicking "Submit" or the client's
     * timer hitting zero (auto_submitted=true). Requirement #4.
     */
    public function submit(Request $request, $id)
    {
        $userId = Auth::id();
        $data = $request->validate([
            'answers' => 'sometimes|array',
            'auto_submitted' => 'sometimes|boolean',
        ]);

        $progress = QuizProgress::where('user_id', $userId)
            ->where('quiz_id', $id)
            ->first();

        // The client's own copy is merged on top of (but doesn't replace)
        // whatever the server already has saved — same defense as the web
        // app fix: never let a missing/partial client payload zero out
        // answers the server already persisted.
        $serverAnswers = $progress?->answers ?: [];
        $answers = ($data['answers'] ?? []) + $serverAnswers;
        $autoSubmitted = (bool) ($data['auto_submitted'] ?? false);

        if ($progress) {
            return response()->json(
                $this->finalizeSubmission($progress, $autoSubmitted, $answers)
            );
        }

        // No progress row (e.g. client never called /start, or it already
        // expired) — still allow a one-shot submit using whatever the
        // client sent, same as the original controller's behavior.
        $quiz = Quiz::with('questions')->findOrFail($id);

        if (QuizSubmission::where('quiz_id', $id)->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Already submitted.'], 409);
        }

        $submission = $this->saveQuizSubmission($quiz, $answers, $autoSubmitted);

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'score' => $submission->score,
            'submission' => $submission,
        ], 201);
    }

    /**
     * Shared by manual submit, timer expiry, and the safety-net checks in
     * active()/saveAnswers() when a deadline has silently passed.
     */
    private function finalizeSubmission(QuizProgress $progress, bool $autoSubmitted, ?array $answersOverride = null): array
    {
        $quiz = Quiz::with('questions')->findOrFail($progress->quiz_id);
        $answers = $answersOverride ?? ($progress->answers ?: []);

        $submission = $this->saveQuizSubmission($quiz, $answers, $autoSubmitted, $progress->user_id);
        $progress->delete();

        return [
            'message' => 'Quiz submitted successfully',
            'score' => $submission->score,
            'submission' => $submission,
        ];
    }

    private function saveQuizSubmission(Quiz $quiz, array $answers, bool $autoSubmitted, ?int $userId = null): QuizSubmission
    {
        $userId = $userId ?? Auth::id();
        $score = 0;
        $reviewAnswers = [];

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->question_id] ?? null;
            $isCorrect = $selected !== null
                && strtolower($selected) === strtolower($question->correct_answer);

            if ($isCorrect) {
                $score++;
            }

            if ($selected !== null) {
                $reviewAnswers[$question->question_id] = [
                    'selected' => $selected,
                    'correct' => $question->correct_answer,
                    'is_correct' => $isCorrect,
                ];
            }
        }

        return QuizSubmission::updateOrCreate(
            ['quiz_id' => $quiz->quiz_id, 'user_id' => $userId],
            [
                'score' => $score,
                'review_answers' => $reviewAnswers,
                'auto_submitted' => $autoSubmitted,
                'submitted_at' => now(),
            ]
        );
    }

    public function results($id)
    {
        $userId = Auth::id();
        $quiz = Quiz::findOrFail($id);

        $submission = QuizSubmission::where('quiz_id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$submission) {
            return response()->json(['message' => 'No submission found for this quiz.'], 404);
        }

        return response()->json([
            'quiz' => $quiz,
            'score' => $submission->score,
            'total' => $quiz->questions()->count(),
            'auto_submitted' => $submission->auto_submitted,
            'submission' => $submission,
        ]);
    }

    // Current user submissions (unchanged from the groupmate's version)
    public function mySubmissions()
    {
        $submissions = QuizSubmission::where('user_id', Auth::id())
            ->with('quiz')
            ->latest()
            ->get();

        return response()->json(['submissions' => $submissions]);
    }
}
