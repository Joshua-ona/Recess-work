<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('groups.index', compact('groups'));
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
}
