<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
   public function index()
{
    $userId = auth()->id();
    $myGroups = Group::whereHas('users', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })->get();

    $availableGroups = Group::whereDoesntHave('users', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })->get();

    return view('groups.index', compact('myGroups', 'availableGroups'));
}

    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);
        

        Group::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect('/groups')->with('success', 'Group created!');
    }

    public function stats(Group $group)
{
    $stats = [
        'discussions_count' => $group->discussions()->count(),
        'replies_count' => \App\Models\Reply::whereIn('discussion_id', $group->discussions()->pluck('id'))->count(),
        'latest_discussion' => $group->discussions()->latest()->first(),
    ];

    return view('groups.stats', compact('group', 'stats'));
}

    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
    }

    public function edit(Group $group)
    {
        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable'
        ]);

        $group->update($request->all());
        return redirect('/groups')->with('success', 'Group updated!');
    }

    public function destroy(Group $group)
    {
        $group->delete();
        return redirect('/groups')->with('success', 'Group deleted!');
    }

    public function discussions(Group $group)
    {
        return view('groups.discussions', compact('group'));
    }

    public function join(Group $group)
{
    $user = auth()->user();

    if ($group->users()->where('user_id', $user->id)->exists()) {
        return redirect("/groups/{$group->id}")->with('success', 'You are already a member!');
    }

    $group->users()->attach($user->id, ['agreed_terms' => true]);

    return redirect("/groups/{$group->id}")->with('success', 'You have joined the group!');
}
}
