<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivateComm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateCommController extends Controller
{
    // Users I have chatted with
    public function users()
    {
        $myId = Auth::id();

        $users = User::whereIn(
            'id',
            function($q) use ($myId) {
                $q->select('sender_id')
                  ->from('private_comms')
                  ->where('receiver_id',$myId)

                  ->union(

                      PrivateComm::select('receiver_id')
                          ->where('sender_id',$myId)

                  );
            }
        )->get();

        return response()->json([
            'users'=>$users
        ]);
    }


    // Conversation with one user
    public function conversation($userId)
    {
        $myId = Auth::id();

        $messages = PrivateComm::with([
            'sender:id,first_name,last_name,email'
        ])
        ->where(function($q)
            use ($myId,$userId){

            $q->where('sender_id',$myId)
              ->where('receiver_id',$userId);

        })
        ->orWhere(function($q)
            use ($myId,$userId){

            $q->where('sender_id',$userId)
              ->where('receiver_id',$myId);

        })
        ->oldest()
        ->get();

        return response()->json([
            'messages'=>$messages
        ]);
    }


    // Send message
    public function send(
        Request $request,
        $userId
    )
    {
        $data = $request->validate([
            'content'=>'required|string'
        ]);

        $message = PrivateComm::create([
            'sender_id'=>Auth::id(),
            'receiver_id'=>$userId,
            'content'=>$data['content']
        ]);

        return response()->json([
            'message'=>'Message sent',
            'private_message'=>$message
        ],201);
    }
}