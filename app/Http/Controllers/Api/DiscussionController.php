<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscussionController extends Controller
{

    // Get all discussions
    public function index()
    {
        $discussions = Discussion::with('user')
            ->latest()
            ->get();

        return response()->json([
            'discussions'=>$discussions
        ]);
    }


    // Create discussion
    public function store(Request $request)
    {
        $data = $request->validate([
            'group_id'=>'required|exists:groups,id',
            'title'=>'required|string|max:255',
            'body'=>'required|string'
        ]);


        $discussion = Discussion::create([
            'group_id'=>$data['group_id'],
            'user_id'=>Auth::id(),
            'title'=>$data['title'],
            'body'=>$data['body']
        ]);


        return response()->json([
            'message'=>'Discussion created successfully',
            'discussion'=>$discussion
        ],201);
    }



    // View single discussion
    public function show($id)
    {
        $discussion = Discussion::with('user')
            ->findOrFail($id);


        return response()->json([
            'discussion'=>$discussion
        ]);
    }



    // Update discussion
    public function update(Request $request,$id)
    {
        $discussion = Discussion::findOrFail($id);


        if($discussion->user_id != Auth::id()){
            return response()->json([
                'message'=>'Unauthorized'
            ],403);
        }


        $discussion->update($request->only([
            'title',
            'body'
        ]));


        return response()->json([
            'message'=>'Discussion updated',
            'discussion'=>$discussion
        ]);
    }



    // Delete discussion
    public function destroy($id)
    {
        $discussion = Discussion::findOrFail($id);


        if($discussion->user_id != Auth::id()){
            return response()->json([
                'message'=>'Unauthorized'
            ],403);
        }


        $discussion->delete();


        return response()->json([
            'message'=>'Discussion deleted'
        ]);
    }

}