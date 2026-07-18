<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\Reply;
use App\Models\Notification;

class DiscussionController extends Controller
{
    public function index(Group $group)
    {
        $discussions = $group->discussions()->latest()->get();
        return view('groups.discussions', compact('group', 'discussions'));
    }

    public function create(Group $group)
    {
        return view('groups.discussions-create', compact('group'));
    }

    public function store(Request $request, Group $group)
    {
        $request->validate([
            'title' => 'required',
            'body'  => 'required'
        ]);

       $discussion = $group->discussions()->create([
    'user_id' => auth()->id(),
    'title'   => $request->title,
    'body'    => $request->body
]);
foreach ($group->users as $student) {

    // Don't notify the person who created the discussion
    if ($student->id == auth()->id()) {
        continue;
    }

    Notification::create([
        'user_id'      => $student->id,
        'type'         => 'discussion',
        'reference_id' => $discussion->id,
        'message'      => 'New discussion: ' . $discussion->title,
        'sender'       => auth()->user()->first_name . ' ' . auth()->user()->last_name,
    ]);
}

        return redirect("/groups/{$group->id}/discussions")
            ->with('success', 'Discussion created!');
    }

    public function show(Group $group, Discussion $discussion)
    {
        $discussion->load('replies.user');
        return view('groups.discussions-show', compact('group', 'discussion'));
    }

    public function storeReply(Request $request, Group $group, Discussion $discussion)
    {
        $request->validate([
            'body' => 'required'
        ]);

        $discussion->replies()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body
        ]);

        return redirect("/groups/{$group->id}/discussions/{$discussion->id}")
            ->with('success', 'Reply posted!');
    }

    public function exportPdf(Group $group, Discussion $discussion)
    {
        $discussion->load('replies.user');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('groups.discussion-pdf', compact('group', 'discussion'));
        return $pdf->download('discussion-' . $discussion->id . '.pdf');
    }
}
