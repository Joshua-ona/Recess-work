<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Reply;
use App\Models\UserWarning;
use App\Models\Quiz;

class NotificationController extends Controller
{
   public function index(Request $request)
{
    $userId = auth()->id();

    $notifications = Notification::where('user_id', $userId)
        ->latest()
        ->get()
        ->map(function ($notification) {

            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->sender,
                'body' => $notification->message,
                'reference_id' => $notification->reference_id,
                'created_at' => $notification->created_at,
                'read' => $notification->read_at !== null,
            ];

        });


    return response()->json([
        'notifications' => $notifications
    ]);
}
  public function count()
{
    $userId = auth()->id();

    $count = Notification::where('user_id', $userId)
        ->whereNull('read_at')
        ->count();

    return response()->json([
        'count' => $count
    ]);
}
public function markRead(Request $request, $id)
{
    $notification = Notification::where('id', $id)
        ->where('user_id', auth()->id())
        ->first();

    if (!$notification) {
        return response()->json([
            'message' => 'Notification not found'
        ], 404);
    }


    $notification->update([
        'read_at' => now()
    ]);


    return response()->json([
        'message' => 'Notification marked as read'
    ]);
}
}