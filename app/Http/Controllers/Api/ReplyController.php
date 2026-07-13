<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{

    // Get all replies for a discussion
    public function index($discussionId)
    {
        $replies = Reply::with('user')
            ->where('discussion_id', $discussionId)
            ->latest()
            ->get();


        return response()->json([
            'replies' => $replies
        ]);
    }


    // Create a reply
    public function store(Request $request, $discussionId)
    {
        $data = $request->validate([
            'body' => 'required|string'
        ]);


        $reply = Reply::create([
            'discussion_id' => $discussionId,
            'user_id' => Auth::id(),
            'body' => $data['body']
        ]);


        return response()->json([
            'message' => 'Reply created successfully',
            'reply' => $reply
        ], 201);
    }


    // Delete a reply
    public function destroy($id)
    {
        $reply = Reply::findOrFail($id);


        if ($reply->user_id != Auth::id()) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 403);

        }


        $reply->delete();


        return response()->json([
            'message' => 'Reply deleted successfully'
        ]);
    }
}