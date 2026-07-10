<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrivateComm;
use App\Models\Reply;
use App\Models\UserWarning;
use App\Models\Quiz;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // New private messages
        $messages = PrivateComm::where('receiver_id', $userId)
            ->latest()
            ->get()
            ->map(function ($message) {

                return [
                    'type' => 'message',
                    'id' => $message->id,
                    'title' => 'New message',
                    'body' => $message->content,
                    'created_at' => $message->created_at,
                    'read' => false,
                ];

            });


        // Replies to user's discussions
        $replies = Reply::whereHas('discussion', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->latest()
            ->get()
            ->map(function ($reply) {

                return [
                    'type' => 'reply',
                    'id' => $reply->id,
                    'title' => 'New reply',
                    'body' => $reply->body,
                    'created_at' => $reply->created_at,
                    'read' => false,
                ];

            });

            // New quizzes
$quizzes = Quiz::where('is_published', true)
    ->latest()
    ->get()
    ->map(function ($quiz) {

        return [
            'type' => 'quiz',
            'id' => $quiz->quiz_id,
            'title' => 'New quiz available',
            'body' => $quiz->title,
            'created_at' => $quiz->created_at,
            'read' => false,
        ];

    });

        // Warnings
        $warnings = UserWarning::where('user_id', $userId)
            ->latest()
            ->get()
            ->map(function ($warning) {

                return [
                    'type' => 'warning',
                    'id' => $warning->id,
                    'title' => 'Account warning',
                    'body' => $warning->message,
                    'created_at' => $warning->created_at,
                    'read' => $warning->read_at !== null,
                ];

            });


        return response()->json([
            'notifications' => collect()
                ->merge($messages)
                ->merge($replies)
                ->merge($warnings)
                ->merge($quizzes)
                ->sortByDesc('created_at')
                ->values()
        ]);
    }
    public function count()
{
    $userId = auth()->id();

    $messages = PrivateComm::where('receiver_id',$userId)
        ->count();

    $warnings = UserWarning::where('user_id',$userId)
        ->whereNull('read_at')
        ->count();

    $count = $messages + $warnings;

    return response()->json([
        'count'=>$count
    ]);
}
}