<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DiscussionController extends Controller
{
    // Get all discussions
public function index($group)
{
    $discussions = Discussion::with('user')
        ->where('group_id', $group)
        ->latest()
        ->get();
    return response()->json(['discussions'=>$discussions
    ]);
}

public function myDiscussions()
{
    $user = Auth::user();

    $startedDiscussions = Discussion::with(['user', 'group'])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

    $repliedDiscussionIds = \App\Models\Reply::where('user_id', $user->id)
        ->pluck('discussion_id')
        ->unique();

    $repliedDiscussions = Discussion::with(['user', 'group'])
        ->whereIn('id', $repliedDiscussionIds)
        ->where('user_id', '!=', $user->id)
        ->latest()
        ->get();

    return response()->json([
        'started'  => $startedDiscussions,
        'replied'  => $repliedDiscussions,
    ]);
}

public function addReply(Request $request, $discussionId)
{
    $request->validate([
        'content' => 'required|string'
    ]);
    $discussion = Discussion::findOrFail($discussionId);
    $reply = Reply::create([
        'discussion_id' => $discussionId,
        'user_id' => Auth::id(),
        'content' => $request->content
    ]);
    return response()->json([
        'message' => 'Reply added successfully',
        'reply' => $reply
    ], 201);
}
    // Create discussion
    public function store(Request $request, $group)
    {
        $data = $request->validate([
            'title'=>'required|string|max:255',
            'body'=>'required|string'
        ]);
        $discussion = Discussion::create([
            'group_id'=>$group,
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