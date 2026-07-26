<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivateComm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PrivateCommController extends Controller
{


    /*
     * Users available for messaging
     */
    public function users()
    {

        $myId = Auth::id();


        $users = User::where(
                'id',
                '!=',
                $myId
            )
            ->select(
                'id',
                'first_name',
                'last_name',
                'email'
            )
            ->get();



        foreach($users as $user){


            $user->unread_count =
                PrivateComm::where(
                    'sender_id',
                    $user->id
                )
                ->where(
                    'receiver_id',
                    $myId
                )
                ->where(
                    'is_read',
                    false
                )
                ->where(
                    'deleted',
                    false
                )
                ->count();


        }



        return response()->json($users);

    }







    /*
     * Get conversation
     */
    public function conversation($userId)
    {


        $myId = Auth::id();



        $messages = PrivateComm::where(

            function($query)
            use(
                $myId,
                $userId
            ){

                $query
                ->where(
                    'sender_id',
                    $myId
                )
                ->where(
                    'receiver_id',
                    $userId
                );


            }

        )
        ->orWhere(

            function($query)
            use(
                $myId,
                $userId
            ){

                $query
                ->where(
                    'sender_id',
                    $userId
                )
                ->where(
                    'receiver_id',
                    $myId
                );

            }

        )
        ->where(
            'deleted',
            false
        )
        ->orderBy(
            'created_at',
            'asc'
        )
        ->get();



        return response()->json([
            "messages"=>$messages
        ]);

    }








    /*
     * Send message
     */
    public function send(
        Request $request,
        $userId
    )
    {


        $data =
        $request->validate([

            'content'=>'required|string',

            'created_at'=>'nullable|string'

        ]);



        $message = PrivateComm::create([

            'sender_id'=>Auth::id(),

            'receiver_id'=>$userId,

            'content'=>$data['content'],

            'created_at'=>
                $data['created_at']
                ??
                now(),

            'updated_at'=>now(),

            'is_read'=>false,

            'deleted'=>false

        ]);





        return response()->json([

            'message'=>'Message sent',

            'private_message'=>[

                'id'=>$message->id,

                'sender_id'=>$message->sender_id,

                'receiver_id'=>$message->receiver_id,

                'content'=>$message->content,

                'created_at'=>$message->created_at,

                'updated_at'=>$message->updated_at

            ]

        ],201);

    }








    /*
     * Sync messages from server
     */
    public function sync()
    {


        $userId = Auth::id();



        $messages = PrivateComm::where(

            function($query)
            use($userId){

                $query->where(
                    'receiver_id',
                    $userId
                )
                ->orWhere(
                    'sender_id',
                    $userId
                );

            }

        )
        ->where(
            'deleted',
            false
        )
        ->orderBy(
            'created_at'
        )
        ->get();




        return response()->json([

            "messages"=>$messages

        ]);

    }

}