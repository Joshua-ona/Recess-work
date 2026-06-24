<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class StudentDashboardController extends Controller
{
    public function index()
    {
        return view('student.dashboard');
    }

    public function saved()
    {
        return view('student.saved');
    }
    public function quizzes()
{
    return view('student.quizzes');
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
        return view('student.notifications');
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