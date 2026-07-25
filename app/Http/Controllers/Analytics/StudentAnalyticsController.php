<?php

namespace App\Http\Controllers\Analytics;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AnalyticsService;

class StudentAnalyticsController extends Controller
{
    /**
     * The logged-in student's own analytics page.
     */
    public function myAnalytics()
    {
        $user = auth()->user();
        $data = AnalyticsService::studentSummary($user);

        return view('analytics.student', array_merge($data, [
            'backRoute' => null,
            'role' => $user->role,
        ]));
    }

    /**
     * Admin drill-down into one student's analytics.
     */
    public function studentDetail(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = AnalyticsService::studentSummary($student);

        return view('analytics.student', array_merge($data, [
            'backRoute' => route('admin.analytics.overview'),
            'backLabel' => 'Back to student analytics',
            'role' => 'system_admin',
        ]));
    }

    /**
     * Admin's aggregate view across every student.
     */
    public function adminOverview()
    {
        $data = AnalyticsService::studentsAdminOverview();

        return view('admin.analytics-students', $data);
    }
}
