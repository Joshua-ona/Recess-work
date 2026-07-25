<?php

namespace App\Services;

use App\Models\Discussion;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\QuizSubmission;
use App\Models\Reply;
use App\Models\User;
use App\Models\UserScore;
use App\Models\UserWarning;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Percentage change helper. Returns null when there's nothing to
     * compare against (avoids a division-by-zero "Infinity%").
     */
    public static function pctChange(float $current, float $previous): ?float
    {
        if ($previous == 0.0) {
            return $current == 0.0 ? 0.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Bucket a collection of dated items into a fixed window of days,
     * returning ['labels' => [...], 'counts' => [...]] with zero-filled gaps.
     */
    public static function dailyBuckets(Collection $items, int $days, string $dateField = 'created_at'): array
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays($days - 1);

        $grouped = $items->groupBy(fn ($i) => Carbon::parse($i->{$dateField})->format('Y-m-d'));

        $labels = [];
        $counts = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $labels[] = $d->format('M j');
            $counts[] = $grouped->get($key, collect())->count();
        }

        return ['labels' => $labels, 'counts' => $counts];
    }

    /**
     * Full analytics snapshot for a single student - reused by the
     * student's own "My analytics" page, and by the admin/lecturer
     * per-student detail page.
     */
    public static function studentSummary(User $student, int $trendDays = 28): array
    {
        $submissions = QuizSubmission::with(['quiz' => fn ($q) => $q->withCount('questions')])
            ->where('user_id', $student->id)
            ->orderBy('submitted_at')
            ->get()
            ->filter(fn ($s) => $s->quiz !== null)
            ->values();

        $pct = fn ($s) => $s->quiz->questions_count > 0
            ? round(($s->score / $s->quiz->questions_count) * 100, 1)
            : 0.0;

        $quizLabels = $submissions->map(fn ($s) => \Illuminate\Support\Str::limit($s->quiz->title, 18))->values();
        $myScores = $submissions->map($pct)->values();

        // Class average per attempted quiz, for a side-by-side comparison.
        $classAverages = $submissions->map(function ($s) use ($pct) {
            $all = QuizSubmission::where('quiz_id', $s->quiz_id)->pluck('score');
            $count = $s->quiz->questions_count ?: 1;
            return $all->isEmpty() ? 0 : round($all->avg() / $count * 100, 1);
        })->values();

        $overallAvg = $myScores->isEmpty() ? 0 : round($myScores->avg(), 1);

        $half = intdiv($submissions->count(), 2);
        $recentAvg = $half > 0 ? round($myScores->slice(-$half)->avg(), 1) : $overallAvg;
        $earlierAvg = $half > 0 ? round($myScores->slice(0, $submissions->count() - $half)->avg(), 1) : $overallAvg;

        // Discussion + reply activity, bucketed daily for the trend chart.
        $discussions = Discussion::where('user_id', $student->id)->get(['id', 'created_at', 'group_id']);
        $replies = Reply::where('user_id', $student->id)->get(['id', 'created_at', 'discussion_id']);

        $discussionTrend = self::dailyBuckets($discussions, $trendDays);
        $replyTrend = self::dailyBuckets($replies, $trendDays);

        // Participation rate: of all discussions in groups this student belongs
        // to, what share has the student engaged with (started or replied)?
        $groupIds = $student->groups()->pluck('groups.id');
        $discussionsInMyGroups = Discussion::whereIn('group_id', $groupIds)->pluck('id');
        $engagedDiscussionIds = $discussions->pluck('id')
            ->merge(Reply::where('user_id', $student->id)->pluck('discussion_id'))
            ->unique();

        $participationRate = $discussionsInMyGroups->count() > 0
            ? round($engagedDiscussionIds->intersect($discussionsInMyGroups)->count() / $discussionsInMyGroups->count() * 100, 1)
            : null;

        $userScore = UserScore::where('user_id', $student->id)->first();

        return [
            'student' => $student,
            'overall_score' => $userScore ? round($userScore->score) : 0,
            'quiz_labels' => $quizLabels,
            'my_scores' => $myScores,
            'class_averages' => $classAverages,
            'overall_avg_pct' => $overallAvg,
            'trend_delta' => self::pctChange($recentAvg, $earlierAvg),
            'quizzes_taken' => $submissions->count(),
            'best_quiz' => $submissions->isNotEmpty() ? $submissions->get($myScores->search($myScores->max()))->quiz->title : null,
            'best_quiz_pct' => $myScores->isNotEmpty() ? $myScores->max() : null,
            'weakest_quiz' => $submissions->isNotEmpty() ? $submissions->get($myScores->search($myScores->min()))->quiz->title : null,
            'weakest_quiz_pct' => $myScores->isNotEmpty() ? $myScores->min() : null,
            'discussions_started' => $discussions->count(),
            'replies_posted' => $replies->count(),
            'discussion_trend' => $discussionTrend,
            'reply_trend' => $replyTrend,
            'participation_rate' => $participationRate,
            'groups_joined' => $groupIds->count(),
        ];
    }

    /**
     * System-wide usage analytics for the admin "Analytics" page:
     * how the platform is being used day to day, and moderation signals.
     */
    public static function systemUsage(int $days = 14): array
    {
        $start = Carbon::today()->subDays($days - 1)->startOfDay();

        $activeUsers = User::whereNotNull('last_active_at')
            ->where('last_active_at', '>=', $start)
            ->get(['id', 'last_active_at']);
        $activeTrend = self::dailyBuckets($activeUsers, $days, 'last_active_at');

        $discussions = Discussion::where('created_at', '>=', $start)->get(['id', 'created_at']);
        $replies = Reply::where('created_at', '>=', $start)->get(['id', 'created_at']);
        $submissions = QuizSubmission::where('created_at', '>=', $start)->get(['submission_id', 'created_at']);
        $messages = \App\Models\GroupMessage::where('created_at', '>=', $start)->get(['id', 'created_at']);

        $contentDaily = self::dailyBuckets(
            $discussions->concat($replies)->concat($submissions)->concat($messages),
            $days
        );

        $signups = User::where('created_at', '>=', $start)->get(['id', 'created_at']);
        $signupTrend = self::dailyBuckets($signups, $days);

        $warnings = UserWarning::where('created_at', '>=', $start)->get(['id', 'created_at']);
        $warningTrend = self::dailyBuckets($warnings, $days);

        $todayActive = end($activeTrend['counts']) ?: 0;
        $yestActive = $activeTrend['counts'][count($activeTrend['counts']) - 2] ?? 0;

        $todayContent = end($contentDaily['counts']) ?: 0;
        $yestContent = $contentDaily['counts'][count($contentDaily['counts']) - 2] ?? 0;

        return [
            'days' => $days,
            'active_trend' => $activeTrend,
            'content_trend' => $contentDaily,
            'signup_trend' => $signupTrend,
            'warning_trend' => $warningTrend,
            'active_today' => $todayActive,
            'active_change_pct' => self::pctChange($todayActive, $yestActive),
            'content_today' => $todayContent,
            'content_change_pct' => self::pctChange($todayContent, $yestContent),
            'role_distribution' => [
                'labels' => ['Students', 'Lecturers', 'Admins'],
                'counts' => [
                    User::where('role', 'student')->count(),
                    User::where('role', 'lecturer')->count(),
                    User::where('role', 'system_admin')->count(),
                ],
            ],
            'status_distribution' => [
                'labels' => ['Active (clean)', 'Warned', 'Blacklisted', 'Pending'],
                'counts' => [
                    User::where('status', 'active')->where('warning_count', 0)->count(),
                    User::where('warning_count', '>', 0)->where('status', '!=', 'blacklisted')->count(),
                    User::where('status', 'blacklisted')->count(),
                    User::where('status', 'pending')->count(),
                ],
            ],
            'total_warnings' => UserWarning::count(),
            'total_blacklisted' => User::where('status', 'blacklisted')->count(),
            'total_warned' => User::where('warning_count', '>', 0)->where('status', '!=', 'blacklisted')->count(),
            'top_flagged' => User::where('warning_count', '>', 0)
                ->orderByDesc('warning_count')
                ->limit(8)
                ->get(['id', 'first_name', 'last_name', 'warning_count', 'status']),
        ];
    }

    /**
     * Everything a lecturer needs to see about the students taking their
     * quizzes / participating in their discussion topics.
     */
    public static function lecturerOverview(User $lecturer, int $trendDays = 14): array
    {
        $quizzes = Quiz::where('created_by', $lecturer->id)->withCount('questions')->orderBy('start_time')->get();
        $quizIds = $quizzes->pluck('quiz_id');

        $submissions = QuizSubmission::whereIn('quiz_id', $quizIds)->get();

        $quizStats = $quizzes->map(function ($quiz) use ($submissions) {
            $subs = $submissions->where('quiz_id', $quiz->quiz_id);
            $count = $subs->count();
            $avgPct = $count > 0 && $quiz->questions_count > 0
                ? round($subs->avg('score') / $quiz->questions_count * 100, 1)
                : null;

            return [
                'title' => $quiz->title,
                'attempts' => $count,
                'avg_pct' => $avgPct,
            ];
        })->values();

        $scored = $quizStats->pluck('avg_pct')->filter(fn ($v) => $v !== null)->values();
        $trendDelta = null;
        if ($scored->count() >= 2) {
            $trendDelta = self::pctChange($scored->last(), $scored->slice(0, -1)->avg());
        }

        // Score distribution buckets across every submission for this lecturer.
        $buckets = ['0-40%' => 0, '40-60%' => 0, '60-80%' => 0, '80-100%' => 0];
        foreach ($submissions as $s) {
            $quiz = $quizzes->firstWhere('quiz_id', $s->quiz_id);
            if (! $quiz || ! $quiz->questions_count) continue;
            $p = $s->score / $quiz->questions_count * 100;
            if ($p < 40) $buckets['0-40%']++;
            elseif ($p < 60) $buckets['40-60%']++;
            elseif ($p < 80) $buckets['60-80%']++;
            else $buckets['80-100%']++;
        }

        // My students = anyone who has taken one of my quizzes, is in a group
        // I administer, or has replied to one of my discussion topics.
        $myGroupIds = Group::where('admin_id', $lecturer->id)->pluck('id');
        $myDiscussionIds = Discussion::where('user_id', $lecturer->id)->pluck('id');

        $studentIds = $submissions->pluck('user_id')
            ->merge(\App\Models\GroupMember::whereIn('group_id', $myGroupIds)->where('user_id', '!=', $lecturer->id)->pluck('user_id'))
            ->merge(Reply::whereIn('discussion_id', $myDiscussionIds)->pluck('user_id'))
            ->unique()
            ->values();

        $students = User::whereIn('id', $studentIds)->get();

        $studentRows = $students->map(function ($student) use ($submissions, $quizzes) {
            $mySubs = $submissions->where('user_id', $student->id);
            $pcts = $mySubs->map(function ($s) use ($quizzes) {
                $quiz = $quizzes->firstWhere('quiz_id', $s->quiz_id);
                return ($quiz && $quiz->questions_count) ? $s->score / $quiz->questions_count * 100 : null;
            })->filter(fn ($v) => $v !== null);

            $replyCount = Reply::where('user_id', $student->id)->count();

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'quizzes_attempted' => $mySubs->count(),
                'avg_pct' => $pcts->isNotEmpty() ? round($pcts->avg(), 1) : null,
                'replies' => $replyCount,
                'last_active' => $student->last_active_at,
            ];
        })->sortByDesc('avg_pct')->values();

        // Discussion topics I've posted and how my students engaged with them.
        $myDiscussions = Discussion::where('user_id', $lecturer->id)
            ->withCount('replies')
            ->latest()
            ->limit(8)
            ->get(['id', 'title', 'created_at']);

        $totalMyDiscussions = Discussion::where('user_id', $lecturer->id)->count();
        $totalRepliesOnMine = Reply::whereIn('discussion_id', $myDiscussionIds)->count();
        $avgRepliesPerTopic = $totalMyDiscussions > 0 ? round($totalRepliesOnMine / $totalMyDiscussions, 1) : 0;

        // Engagement/participation trend: how many of "my students" were
        // active each day over the trend window, vs the prior window.
        $start = Carbon::today()->subDays($trendDays - 1)->startOfDay();
        $prevStart = $start->copy()->subDays($trendDays);

        $activityInWindow = collect()
            ->concat(Reply::whereIn('user_id', $studentIds)->where('created_at', '>=', $prevStart)->get(['user_id', 'created_at']))
            ->concat(QuizSubmission::whereIn('user_id', $studentIds)->where('created_at', '>=', $prevStart)->get(['user_id', 'created_at']))
            ->concat(Discussion::whereIn('user_id', $studentIds)->where('created_at', '>=', $prevStart)->get(['user_id', 'created_at']));

        $currentWindow = $activityInWindow->filter(fn ($i) => Carbon::parse($i->created_at)->gte($start));
        $priorWindow = $activityInWindow->filter(fn ($i) => Carbon::parse($i->created_at)->lt($start));

        $engagementTrend = self::dailyBuckets($currentWindow, $trendDays);
        // de-duplicate counts per day to distinct students engaged that day
        $groupedByDay = $currentWindow->groupBy(fn ($i) => Carbon::parse($i->created_at)->format('Y-m-d'));
        $engagementTrend['counts'] = collect($engagementTrend['labels'])->map(function ($label, $idx) use ($start, $groupedByDay) {
            $day = $start->copy()->addDays($idx)->format('Y-m-d');
            return $groupedByDay->get($day, collect())->pluck('user_id')->unique()->count();
        })->values()->all();

        $currentDistinct = $currentWindow->pluck('user_id')->unique()->count();
        $priorDistinct = $priorWindow->pluck('user_id')->unique()->count();
        $studentCount = max($studentIds->count(), 1);

        return [
            'quizzes' => $quizStats,
            'quiz_trend_delta' => $trendDelta,
            'score_distribution' => [
                'labels' => array_keys($buckets),
                'counts' => array_values($buckets),
            ],
            'students' => $studentRows,
            'student_count' => $studentIds->count(),
            'my_discussions' => $myDiscussions,
            'avg_replies_per_topic' => $avgRepliesPerTopic,
            'engagement_trend' => $engagementTrend,
            'engagement_rate_pct' => round($currentDistinct / $studentCount * 100, 1),
            'engagement_rate_change_pct' => self::pctChange(
                round($currentDistinct / $studentCount * 100, 1),
                round($priorDistinct / $studentCount * 100, 1)
            ),
        ];
    }

    /**
     * Admin's aggregate view across every student on the platform.
     */
    public static function studentsAdminOverview(): array
    {
        $students = User::where('role', 'student')->get();
        $submissions = QuizSubmission::with(['quiz' => fn ($q) => $q->withCount('questions')])
            ->get()
            ->filter(fn ($s) => $s->quiz);

        $avgByStudent = $students->map(function ($student) use ($submissions) {
            $mine = $submissions->where('user_id', $student->id);
            $pcts = $mine->map(fn ($s) => $s->quiz->questions_count > 0
                ? $s->score / $s->quiz->questions_count * 100
                : 0);

            $score = UserScore::where('user_id', $student->id)->value('score');

            return [
                'id' => $student->id,
                'name' => $student->full_name,
                'avg_pct' => $pcts->isNotEmpty() ? round($pcts->avg(), 1) : null,
                'quizzes_taken' => $mine->count(),
                'discussions' => Discussion::where('user_id', $student->id)->count(),
                'replies' => Reply::where('user_id', $student->id)->count(),
                'score' => $score ? round($score) : 0,
            ];
        });

        $withScores = $avgByStudent->filter(fn ($r) => $r['avg_pct'] !== null);

        $submissionTrend = self::dailyBuckets($submissions, 14, 'created_at');

        return [
            'total_students' => $students->count(),
            'system_avg_pct' => $withScores->isNotEmpty() ? round($withScores->avg('avg_pct'), 1) : 0,
            'leaderboard' => $avgByStudent->sortByDesc('score')->take(10)->values(),
            'top_performers' => $withScores->sortByDesc('avg_pct')->take(5)->values(),
            'needs_attention' => $withScores->sortBy('avg_pct')->take(5)->values(),
            'submission_trend' => $submissionTrend,
            'most_active' => $avgByStudent->sortByDesc(fn ($r) => $r['discussions'] + $r['replies'])->take(5)->values(),
        ];
    }
}
