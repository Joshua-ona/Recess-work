<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return response()->json([

            'total_members' =>
                User::count(),

            'active_today' =>
                0,

            'blacklisted_count' =>
                User::whereNotNull('blacklisted_until')
                    ->where(
                        'blacklisted_until',
                        '>=',
                        now()
                    )
                    ->count(),

            'pending_approvals' =>
                User::where(
                    'is_enabled',
                    false
                )->count(),

            'warned_members' =>
                User::where(
                    'warning_count',
                    '>',
                    0
                )
                ->whereNull(
                    'blacklisted_until'
                )
                ->orderByDesc(
                    'warning_count'
                )
                ->take(10)
                ->get(),

            'pending_groups' =>
                Group::where(
                    'status',
                    'pending'
                )
                ->with('admin')
                ->latest()
                ->take(5)
                ->get()

        ]);
    }
}