<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LecturerDashboardApiController extends Controller
{
    public function index()
    {
        return response()->json([

            'studentCount' => 0,
            'threadsThisWeek' => 0,
            'unansweredCount' => 0,
            'avgSatisfaction' => 0.0,

            'unansweredDiscussions' => [
                [
                    'id' => 1,
                    'title' => 'No unanswered questions yet.'
                ]
            ],

            'engagement' => [
                [
                    'courseCode' => 'CSC1100',
                    'courseName' => 'Programming Fundamentals',
                    'percentage' => 0
                ]
            ],

            'threads' => [
                [
                    'id' => 1,
                    'title' => 'Welcome to EduDiscuss',
                    'course' => 'General',
                    'replies' => 0,
                    'status' => 'Open'
                ]
            ]
        ]);
    }
}