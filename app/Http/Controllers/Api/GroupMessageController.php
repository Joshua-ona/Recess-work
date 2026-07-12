<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupMessageController extends Controller
{
    public function index($groupId)
    {
        $messages = GroupMessage::with([
            'user:id,first_name,last_name,email'
        ])
        ->where('group_id', $groupId)
        ->oldest()
        ->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function store(Request $request, $groupId)
    {
        $data = $request->validate([
            'body' => 'required|string'
        ]);

        $message = GroupMessage::create([
            'group_id' => $groupId,
            'user_id' => Auth::id(),
            'body' => $data['body']
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'group_message' => $message
        ], 201);
    }
}