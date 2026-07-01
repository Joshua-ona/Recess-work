<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;

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
    return view('lecturer.performance');
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