<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discussion;
use App\Models\GroupMember;
use App\Models\QuizSubmission;
use App\Models\Reply;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([

            // Discussions created by the student
            'posts' => Discussion::where('user_id', $user->id)->count(),

            // Quizzes attempted by the student
            'quizzes' => QuizSubmission::where('user_id', $user->id)->count(),

            // Groups the student belongs to
            'groups' => GroupMember::where('user_id', $user->id)->count(),

            // Number of replies made by the student
            'participation' => Reply::where('user_id', $user->id)->count()

        ]);
    }
}