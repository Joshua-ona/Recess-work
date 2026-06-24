<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalMembers = User::count();

        $activeToday = User::where('last_active_at', '>=', Carbon::today())->count();

        $pendingApprovals = User::where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        $blacklistedCount = User::where('status', 'blacklisted')->count();

        $warnedMembers = User::where('warning_count', '>', 0)
            ->orWhere('status', 'blacklisted')
            ->orderByDesc('warning_count')
            ->limit(10)
            ->get();

        // --- Placeholder data below ---
        // These sections depend on the forum/quiz/recommendation modules,
        // which per the SDD are scoped for later sprints. Swap these arrays
        // for real Eloquent queries once those tables exist.

        $topContributors = [
            ['name' => 'Nina K.', 'posts' => 38],
            ['name' => 'Brian O.', 'posts' => 31],
            ['name' => 'Faith A.', 'posts' => 22],
            ['name' => 'Tom M.', 'posts' => 9],
        ];

        $flaggedContent = [
            ['title' => '"Check out my side hustle!!" in #databases', 'meta' => 'Flagged as off-topic · 3 reports'],
            ['title' => 'Duplicate ad post in #general', 'meta' => 'Flagged as off-topic · 1 report'],
        ];

        $upcomingQuizzes = [
            ['name' => 'SQL joins quiz', 'category' => 'Year 2 students', 'opens' => 'Mon 09:00'],
            ['name' => 'Normalization quiz', 'category' => 'Year 2 students', 'opens' => 'Wed 14:00'],
        ];

        $trendingTopics = ['Normalization', 'Indexing', 'Group project tools'];

        return view('admin.dashboard', compact(
            'totalMembers',
            'activeToday',
            'pendingApprovals',
            'blacklistedCount',
            'warnedMembers',
            'topContributors',
            'flaggedContent',
            'upcomingQuizzes',
            'trendingTopics'
        ));
    }
}
