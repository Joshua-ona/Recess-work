<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\{GroupService,GroupChatService};
use Iluminate\Http\Request;
use App\Models\Group;

class StudentDashboardController extends Controller
{
    public function __construct(private GroupService $groupService,private GroupChatService $chat){
    
    }

    public function index()
    {
        $user = auth()->user();
        $myGroupIds = $user->groups()->pluck('groups.id');
        $joinedGroupIds = auth()->user()->groups()->pluck('groups.id')->toArray();

        $myGroups = $this->groupService->getMyGroups($user);
        $browseGroups = Group::where('status','approved')
                            ->whereNotIn('id',$myGroupIds)
                            ->latest('id')
                            ->get();

        $discoverGroups = $this->groupService->getDiscoverableGroupsFor($user);


        $postsMade = 0;
        $upvotesReceived = 0;
        $enrolledCourses = collect();
        $participationScore = 0;

        return view('student.dashboard',
        [
                    'myGroups' => $myGroups,
                    'browseGroups' => $browseGroups,
                    'joinedGroups' => $joinedGroupIds,
                    'discoverGroups' =>  $discoverGroups, 
                    'postsMade' =>  $postsMade,
                    'upvotesReceived' =>  $upvotesReceived,
                    'enrolledCourses' =>  $enrolledCourses,
                    'participationScore' => $participationScore,
                    'user' => $user,
                    'activeGroup' => null,
                    'admin' => null,
                    
        ]);
    }

     public function show(Group $group)
     {
        $user = auth()->user();
        $this->authorize('view',$group);

        $admin = $group->admin;
        $members = $group->members()->where('user_id', '!=', $admin->id)->get();
        $messages = $group->messages()->with('user')->latest()->get();

        return view('student.dashboard', [
            'activeGroup' => $group,
            'admin' => $admin,
            'members' => $members,
            'messages' => $messages,
            'role' => 'student',
            'user' => $user,
            'discover' => $this->groupService->getDiscoverableGroupsFor($user),
            'myGroups' => $this->groupService->getMyGroups($user),
            'enrolledCourses' => collects(),
            'unreadCount' => 0,
            'notifCount' => 0,

        ]);
    }

     public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:groups,name',
                'description' => 'nullable|string|max:1000',
            ]);

            $this->groupService->create($validated, auth()->user());

            return back()->with('success', 'Group requested.Waiting for admin approval.');
        
        }


    public function join(Group $group)
    {
        abort_unless($group->status === 'approved', 403, 'Group Not Approved');
        auth()->user()->groups()->syncWithoutDetaching([
            $group->id => ['role' => 'member']
        ]);

        return back()->with('success', 'Joined '.$group->name);

    }

     public function message(Request $request, Group $group)
    {
        $request->validate(['body'=>'required|string|max:1000']);
        $this->chat->postMessage($group, auth()->user(),$request->body);

        return back();
    }


    public function saved()
    {
        return view('student.saved');
    }
    public function quizzes()
{
    return view('student.quizzes');
}

public function messages()
{
    return view('student.messages');
}
public function categories()
{
    return view('student.categories');
}
public function reports()
{
    return view('student.reports');
}

public function settings()
{
    return view('student.settings');
}

    public function profile()
    {
        return view('student.profile');
    }

    public function notifications()
    {
        return view('student.notifications');
    }

    public function browseCourses()
    {
        return view('student.courses.browse');
    }

    public function course($course)
    {
        return "Course ID: ".$course;
    }
}