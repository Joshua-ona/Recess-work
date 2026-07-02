<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;

class StudentDashboardController extends Controller
{
    public function index()
    {
        return view('student.dashboard', [
            'notifCount' => auth()->user()->warnings()->whereNull('read_at')->count(),
        ]);
    }

    public function saved()
    {
        return view('student.saved');
    }
    public function quizzes()
{
    $quizzes = Quiz::all();
    return view('student.quizzes', compact('quizzes'));
}

public function messages()
{
    return view('student.messages');
}
public function categories()
{
    return view('student.categories');
}
public function reports()
{
    return view('student.reports');
}

public function settings()
{
    return view('student.settings');
}

    public function profile()
    {
        return view('student.profile');
    }

    public function notifications()
    {
        $warnings = auth()->user()->warnings()->with('issuer')->get();

        // Viewing this page counts as reading them — clears the sidebar badge.
        auth()->user()->warnings()->whereNull('read_at')->update(['read_at' => now()]);

        return view('student.notifications', [
            'warnings' => $warnings,
        ]);
    }

    public function browseCourses()
    {
        return view('student.courses.browse');
    }

    public function course($course)
    {
        return "Course ID: ".$course;
    }
}