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

        //$activeToday = User::where('last_active_at', '>=', Carbon::today())->count();


        //testing the admin dashboard since table are non existant
        $activeToday = 0;

        // $pendingApprovals = User::where('status', 'pending')
        //     ->orderByDesc('created_at')
        //     ->get();

        $pendingApprovals = User::where('is_enabled', 'false')->orderByDesc('created_at')->get();



        // $blacklistedCount = User::where('status', 'blacklisted')->count();

         $blacklistedCount = User::whereNotNull('blacklisted_until')
         ->where('blacklisted_until', '>=', now())
         ->count();
                        

        $warnedMembers = User::where('warning_count', '>', 0)
            ->whereNull('blacklisted_until')
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
