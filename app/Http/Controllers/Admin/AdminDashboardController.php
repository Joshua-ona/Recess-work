<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Quiz;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMembers = User::count();

        // Active today — users who have a live session row
        $activeToday = 0;
        if (config('session.driver') === 'database') {
            $activeToday = DB::table('sessions')
                ->whereNotNull('user_id')
                ->distinct('user_id')
                ->count('user_id');
        }

        // Lecturers who were invited but haven't yet activated their account
        $pendingApprovals = User::where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        // Blacklisted count
        $blacklistedCount = User::where('status', 'blacklisted')->count();

        // Warned members (not yet blacklisted)
        $warnedMembers = User::where('warning_count', '>', 0)
            ->where('status', '!=', 'blacklisted')
            ->orderByDesc('warning_count')
            ->limit(10)
            ->get();

        // Groups pending admin approval
        $groups = Group::where('status', 'pending')
            ->with('admin')
            ->latest()
            ->take(5)
            ->get();

        // Top contributors from real discussions + replies tables
        $topContributors = DB::table('users')
            ->leftJoin('discussions', 'users.id', '=', 'discussions.user_id')
            ->leftJoin('replies', 'users.id', '=', 'replies.user_id')
            ->selectRaw("users.id, TRIM(users.first_name || ' ' || users.last_name) as name,
                (COUNT(DISTINCT discussions.id) + COUNT(DISTINCT replies.id)) as posts")
            ->groupBy('users.id', 'users.first_name', 'users.last_name')
            ->orderByDesc('posts')
            ->limit(5)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'posts' => $r->posts]);

        // Upcoming quizzes
        $upcomingQuizzes = Quiz::where('is_published', false)
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(fn($q) => [
                'name'     => $q->title,
                'category' => $q->target_category ?? 'All',
                'opens'    => $q->start_time
                    ? \Carbon\Carbon::parse($q->start_time)->format('D H:i')
                    : 'TBC',
            ]);

        $trendingTopics = DB::table('topics')
            ->where('status', 'active')
            ->limit(5)
            ->pluck('name')
            ->toArray();

        if (empty($trendingTopics)) {
            $trendingTopics = ['Normalization', 'Indexing', 'Group project tools'];
        }

        return view('admin.dashboard', compact(
            'totalMembers',
            'activeToday',
            'pendingApprovals',
            'blacklistedCount',
            'warnedMembers',
            'topContributors',
            'upcomingQuizzes',
            'trendingTopics',
            'groups',
        ) + AnalyticsService::systemUsage(7));
    }

    public function analytics()
    {
        return view('admin.analytics', AnalyticsService::systemUsage());
    }
    public function discussions() { return view('admin.discussions'); }
    public function courses()     { return view('admin.courses'); }
    public function reports()     { return view('admin.reports'); }
    public function settings()    { return view('admin.settings'); }
}