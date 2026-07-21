<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
   public function index()
{
    $userId = Auth::id();
    $groups = Group::latest()->get()->map(function($group) use ($userId) {
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();
        $group->is_member = $isMember;
        return $group;
    });

    return response()->json([
        'groups' => $groups
    ]);
}

    public function show($id)
    {
        return response()->json([
            'group' => Group::findOrFail($id)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'description'=>'nullable|string'
        ]);

        $group = Group::create([
            'name'=>$data['name'],
            'description'=>$data['description'] ?? null,
            'status'=>'active'
        ]);

        GroupMember::create([
            'group_id'=>$group->id,
            'user_id'=>Auth::id(),
            'role'=>'admin',
            'agreed_terms'=>true
        ]);

        return response()->json([
            'message'=>'Group created successfully',
            'group'=>$group
        ],201);
    }

    public function join($id)
    {
        $group = Group::findOrFail($id);

        $exists = GroupMember::where('group_id',$id)
            ->where('user_id',Auth::id())
            ->exists();

        if ($exists) {
            return response()->json([
                'message'=>'Already a member'
            ],409);
        }

        $member = GroupMember::create([
            'group_id'=>$group->id,
            'user_id'=>Auth::id(),
            'role'=>'member',
            'agreed_terms'=>true
        ]);

        return response()->json([
            'message'=>'Joined group successfully',
            'member'=>$member
        ]);
    }

    public function members($id)
    {
        $members = GroupMember::with([
            'user:id,first_name,last_name,email'
        ])
        ->where('group_id',$id)
        ->get();

        return response()->json([
            'members'=>$members
        ]);
    }
}