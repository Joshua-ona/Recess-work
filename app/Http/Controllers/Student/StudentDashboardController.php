<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Models\PrivateComm;
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
    $notifications = collect();

    // --------------------
    // Warnings
    // --------------------
    $warnings = auth()->user()->warnings()->with('issuer')->get();

    foreach ($warnings as $warning) {
        $notifications->push([
            'type' => 'warning',
            'message' => $warning->message,
            'sender' => $warning->issuer?->full_name ?? 'Admin',
            'created_at' => $warning->created_at,
        ]);
    }

    // --------------------
    // Messages
    // --------------------
    $messages = PrivateComm::with('sender')
        ->where('receiver_id', auth()->id())
        ->latest()
        ->get();

    foreach ($messages as $message) {
        $notifications->push([
            'type' => 'message',
            'message' => $message->content,
           'sender' => $message->sender
    ? $message->sender->first_name . ' ' . $message->sender->last_name
    : 'Unknown',
            'created_at' => $message->created_at,
        ]);
    }

    // --------------------
    // Quizzes
    // --------------------
$groupIds = auth()->user()
    ->groups()
    ->pluck('groups.id');

$quizzes = Quiz::whereIn('group_id', $groupIds)
    ->where('is_published', true)
    ->latest()
    ->get();

foreach ($quizzes as $quiz) {
    $notifications->push([
        'type' => 'quiz',
        'message' => "New quiz: {$quiz->title}",
        'sender' => 'Course Quiz',
        'created_at' => $quiz->created_at,]);
}
    $notifications = $notifications->sortByDesc('created_at');

    auth()->user()->warnings()
        ->whereNull('read_at')
        ->update([
            'read_at' => now(),
        ]);

    return view('student.notifications', [
        'notifications' => $notifications,
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