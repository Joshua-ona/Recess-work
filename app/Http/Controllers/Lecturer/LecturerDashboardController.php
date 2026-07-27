<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Discussion;
use App\Models\User;

class LecturerDashboardController extends Controller
{
    public function index()
    {
        return view('lecturer.dashboard');
    }

    public function categories()
    {
        return view('lecturer.categories');
    }

    public function quizzes()
    {
        return view('lecturer.quizzes');
    }

    public function performance()
    {
        $lecturer = auth()->user();

        // Groups this lecturer administers
        
        // Students who attempted any quiz this lecturer created
        $myQuizIds = Quiz::where('created_by', $lecturer->id)->pluck('quiz_id');

        $studentIdsFromQuizzes = QuizSubmission::whereIn('quiz_id', $myQuizIds)->pluck('user_id');

        $students = User::where('role', 'student')->whereIn('id', $studentIdsFromQuizzes)->get();

        $studentIds = $students->pluck('id');
        $student_count = $students->count();

        // Quizzes created by this lecturer, with per-quiz average score %
        $quizzes = Quiz::where('created_by', $lecturer->id)
            ->withCount('questions')
            ->with(['submissions' => function ($q) {
                $q->select('quiz_id', 'user_id', 'score', 'submitted_at');
            }])
            ->get()
            ->map(function ($quiz) {
                $totalQuestions = $quiz->questions_count ?: 1;
                $avgScore = $quiz->submissions->avg('score');
                $quiz->avg_pct = $quiz->submissions->isEmpty()
                    ? null
                    : round(($avgScore / $totalQuestions) * 100, 1);
                return $quiz;
            });

        // Trend: latest quiz avg vs average of prior quizzes
        $quiz_trend_delta = null;
        $scoredQuizzes = $quizzes->filter(fn($q) => $q->avg_pct !== null)->values();
        if ($scoredQuizzes->count() >= 2) {
            $latest = $scoredQuizzes->last();
            $priorAvg = $scoredQuizzes->slice(0, -1)->avg('avg_pct');
            $quiz_trend_delta = round($latest->avg_pct - $priorAvg, 1);
        }

        // All submissions across this lecturer's quizzes
        $quizIds = $quizzes->pluck('quiz_id');
        $allSubmissions = QuizSubmission::whereIn('quiz_id', $quizIds)->get();

        // Score distribution buckets (0-25, 26-50, 51-75, 76-100)
        $questionCounts = $quizzes->pluck('questions_count', 'quiz_id');
        $pctScores = $allSubmissions->map(function ($sub) use ($questionCounts) {
            $total = $questionCounts[$sub->quiz_id] ?? 1;
            return $total > 0 ? ($sub->score / $total) * 100 : 0;
        });

        $score_distribution = [
            'labels' => ['0-25%', '26-50%', '51-75%', '76-100%'],
            'counts' => [
                $pctScores->filter(fn($p) => $p <= 25)->count(),
                $pctScores->filter(fn($p) => $p > 25 && $p <= 50)->count(),
                $pctScores->filter(fn($p) => $p > 50 && $p <= 75)->count(),
                $pctScores->filter(fn($p) => $p > 75)->count(),
            ],
        ];

        // Discussions authored by this lecturer
        $my_discussions = Discussion::where('user_id', $lecturer->id)
            ->withCount('replies')
            ->latest()
            ->get();

        $avg_replies_per_topic = $my_discussions->isEmpty()
            ? 0
            : round($my_discussions->avg('replies_count'), 1);

        // Engagement: % of my students active (quiz submission or reply) in last 30 days vs prior 30 days
        $now = now();
        $period1Start = $now->copy()->subDays(30);
        $period2Start = $now->copy()->subDays(60);

        $activeInPeriod = function ($start, $end) use ($studentIds, $quizIds) {
            $activeQuizUserIds = QuizSubmission::whereIn('quiz_id', $quizIds)
                ->whereIn('user_id', $studentIds)
                ->whereBetween('submitted_at', [$start, $end])
                ->pluck('user_id');

            $activeReplyUserIds = \App\Models\Reply::whereIn('user_id', $studentIds)
                ->whereBetween('created_at', [$start, $end])
                ->pluck('user_id');

            return $activeQuizUserIds->merge($activeReplyUserIds)->unique()->count();
        };

        $activeNow = $student_count > 0 ? $activeInPeriod($period1Start, $now) : 0;
        $activePrev = $student_count > 0 ? $activeInPeriod($period2Start, $period1Start) : 0;

        $engagement_rate_pct = $student_count > 0
            ? round(($activeNow / $student_count) * 100, 1)
            : 0;

        $engagement_rate_change_pct = $student_count > 0
            ? round((($activeNow - $activePrev) / max($student_count, 1)) * 100, 1)
            : null;

        // Engagement trend: distinct active students per day, last 14 days
        $days = 14;
        $labels = [];
        $counts = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();

            $quizUsers = QuizSubmission::whereIn('quiz_id', $quizIds)
                ->whereIn('user_id', $studentIds)
                ->whereBetween('submitted_at', [$dayStart, $dayEnd])
                ->pluck('user_id');

            $replyUsers = \App\Models\Reply::whereIn('user_id', $studentIds)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->pluck('user_id');

            $labels[] = $day->format('M j');
            $counts[] = $quizUsers->merge($replyUsers)->unique()->count();
        }
        $engagement_trend = ['labels' => $labels, 'counts' => $counts];

        // Per-student breakdown
        $studentsTable = $students->map(function ($student) use ($quizIds) {
            $subs = QuizSubmission::where('user_id', $student->id)
                ->whereIn('quiz_id', $quizIds)
                ->get();

            $avgPct = null;
            if ($subs->isNotEmpty()) {
                $quizQuestionCounts = Quiz::whereIn('quiz_id', $subs->pluck('quiz_id'))
                    ->withCount('questions')
                    ->get()
                    ->pluck('questions_count', 'quiz_id');

                $pcts = $subs->map(function ($sub) use ($quizQuestionCounts) {
                    $total = $quizQuestionCounts[$sub->quiz_id] ?? 1;
                    return $total > 0 ? ($sub->score / $total) * 100 : 0;
                });
                $avgPct = round($pcts->avg(), 1);
            }

            $repliesCount = \App\Models\Reply::where('user_id', $student->id)->count();

            $lastQuizAt = $subs->max('submitted_at');
            $lastReplyAt = \App\Models\Reply::where('user_id', $student->id)->max('created_at');
            $lastActive = collect([$lastQuizAt, $lastReplyAt])->filter()->max();

            return [
                'id' => $student->id,
                'name' => trim($student->first_name . ' ' . $student->last_name),
                'avg_pct' => $avgPct,
                'quizzes_attempted' => $subs->count(),
                'replies' => $repliesCount,
                'last_active' => $lastActive,
            ];
        })->sortByDesc('last_active')->values();

        return view('lecturer.performance', [
            'student_count' => $student_count,
            'engagement_rate_pct' => $engagement_rate_pct,
            'engagement_rate_change_pct' => $engagement_rate_change_pct,
            'quizzes' => $quizzes,
            'quiz_trend_delta' => $quiz_trend_delta,
            'avg_replies_per_topic' => $avg_replies_per_topic,
            'score_distribution' => $score_distribution,
            'engagement_trend' => $engagement_trend,
            'students' => $studentsTable,
            'my_discussions' => $my_discussions,
        ]);
    }

    public function messages()
    {
        return view('lecturer.messages');
    }

    public function notifications()
    {
        $warnings = auth()->user()->warnings()->with('issuer')->get();

        auth()->user()->warnings()->whereNull('read_at')->update(['read_at' => now()]);

        return view('lecturer.notifications', [
            'warnings' => $warnings,
        ]);
    }

    public function settings()
    {
        return view('lecturer.settings');
    }
}