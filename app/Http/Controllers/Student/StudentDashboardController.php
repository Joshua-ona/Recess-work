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