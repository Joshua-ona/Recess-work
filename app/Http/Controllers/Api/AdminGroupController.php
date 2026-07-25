<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupService;

class AdminGroupController extends Controller
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    public function index()
    {
        $groups = Group::where('status', 'pending')
            ->with('admin')
            ->latest()
            ->get();

        return response()->json($groups);
    }

    public function approve(Group $group)
    {
        $this->groupService->approve($group);

        return response()->json([
            'message' => 'Group approved'
        ]);
    }

    public function reject(Group $group)
    {
        $this->groupService->reject($group);

        return response()->json([
            'message' => 'Group rejected'
        ]);
    }
}