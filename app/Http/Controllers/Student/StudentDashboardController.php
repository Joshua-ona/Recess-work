<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Services\{GroupService,GroupChatService};
use Iluminate\Http\Request;
use App\Models\Group;
use App\Models\PrivateComm;
use App\Models\Quiz;

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
        //$this->authorize('view',$group);

        $admin = $group->admin;
        $members = $group->users()->where('user_id', '!=', $admin->id)->get();
        $messages = $group->messages()->with('user')->latest()->get();

        $browseGroups = Group::where('status','approved')
                            ->whereNotIn('id',$user->groups()->pluck('groups.id'))
                            ->latest('id')
                            ->get();

        return view('student.dashboard', [
            'activeGroup' => $group,
            'admin' => $admin,
            'members' => $members,
            'messages' => $messages,
            'role' => 'student',
            'browseGroups' => $browseGroups,
            'user' => $user,
            'discover' => $this->groupService->getDiscoverableGroupsFor($user),
            'myGroups' => $this->groupService->getMyGroups($user),
            'enrolledCourses' => collect(),
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
    $quizzes = Quiz::all();
    return view('student.quizzes', compact('quizzes'));
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
    $notifications = collect();

    // --------------------
    // Warnings
    // --------------------
    $warnings = auth()->user()->warnings()->with('issuer')->get();

    foreach ($warnings as $warning) {
        $notifications->push([
            'type' => 'warning',
            'message' => $warning->message,
            'sender' => $warning->issuer?->full_name ?? 'Admin',
            'created_at' => $warning->created_at,
        ]);
    }

    // --------------------
    // Messages
    // --------------------
    $messages = PrivateComm::with('sender')
        ->where('receiver_id', auth()->id())
        ->latest()
        ->get();

    foreach ($messages as $message) {
        $notifications->push([
            'type' => 'message',
            'message' => $message->content,
           'sender' => $message->sender
    ? $message->sender->first_name . ' ' . $message->sender->last_name
    : 'Unknown',
            'created_at' => $message->created_at,
        ]);
    }

    // --------------------
    // Quizzes
    // --------------------
$groupIds = auth()->user()
    ->groups()
    ->pluck('groups.id');

$quizzes = Quiz::whereIn('group_id', $groupIds)
    ->where('is_published', true)
    ->latest()
    ->get();

foreach ($quizzes as $quiz) {
    $notifications->push([
        'type' => 'quiz',
        'message' => "New quiz: {$quiz->title}",
        'sender' => 'Course Quiz',
        'created_at' => $quiz->created_at,]);
}
    $notifications = $notifications->sortByDesc('created_at');

    auth()->user()->warnings()
        ->whereNull('read_at')
        ->update([
            'read_at' => now(),
        ]);

    return view('student.notifications', [
        'notifications' => $notifications,
    ]);
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