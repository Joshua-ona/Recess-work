<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Services\GroupService;

class GroupController extends Controller 
{
    protected GroupService $groupService;

    public function __construct(GroupService $groupService){
        $this->groupService = $groupService;
    }

    public function index()
    {
        $groups = Group::where('status',pending)->with('creator')->latest()
                ->take(5)
                ->get();
        return view('admin.groups.index',compact('groups'));
    }

    public function approve(Group $group)
    {
        $this->groupService->approve($group);
        return back()->with('success', 'Group Approved');
    }

    public function reject(Group $group)
    {
        $this->groupService->reject($group);
        return back()->with('error', 'Group Rejected');
    }

}