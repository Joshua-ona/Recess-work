<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use App\Services\{GroupService,GroupChatService};
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\UserScore;
use App\Models\PrivateComm;
use App\Models\Quiz;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;

class StudentDashboardController extends Controller
{
    public function __construct(private GroupService $groupService,private GroupChatService $chat){
    
    }

public function index()
{
    $user = auth()->user();
    $myGroups = $this->groupService->getMyGroups($user);
    $myGroupIds = $user->groups()->pluck('groups.id')->toArray();

    // 1. BROWSE ALL: Show ALL approved groups. No filter
    $browseGroups = Group::where('status', 'approved')
    ->withCount('discussions')
    ->distinct()
    ->orderBy('name')
    ->get();

    $discoverGroups = $this->groupService->getDiscoverableGroupsFor($user);

    // 2. Recommended Discussions from Python
    $response = Http::get('http://127.0.0.1:5001/recommendations/' . $user->id);
    $recommendations = $response->successful() ? $response->json() : [];

    // 3. Recommended Groups from Python
    $responseGroups = Http::get('http://127.0.0.1:5001/recommend-groups/' . $user->id);
    $recommendedGroups = [];

    if ($responseGroups->successful()) {
        $data = $responseGroups->json();
        $recommendedGroups = $data['recommendations'] ?? [];

        foreach ($recommendedGroups as &$group) {
            $groupModel = Group::find($group['id']);
            $group['discussions_count'] = $groupModel ? $groupModel->discussions()->count() : 0;
        }
    }

    $userScore = UserScore::where('user_id', $user->id)->first();
    $score = $userScore ? round($userScore->score) : 0;

    $notifCount = Notification::where('user_id', $user->id)
    ->whereNull('read_at')
    ->count();

    // Stat counts
    $postCount = \App\Models\Discussion::where('user_id', $user->id)->count(); // Adjust model if your posts are named differently
    $quizCount = 0; // Update with your actual quiz count logic if available
    $groupCount = count($myGroupIds);

    return view('student.dashboard', [
        'myGroups' => $myGroups,
        'browseGroups' => $browseGroups,
        'joinedGroups' => $myGroupIds,
        'recommendations' => $recommendations,
        'recommendedGroups' => $recommendedGroups,
        'discoverGroups' => $discoverGroups,
        'score' => $score,
        'user' => $user,
        'postCount' => $postCount,
        'quizCount' => $quizCount,
        'groupCount' => $groupCount,
        'activeGroup' => null,
        'admin' => null,
    ]);
}



    public function show(Group $group)
{
    $user = auth()->user();
    $admin = $group->admin;
    $members = $group->users()->where('user_id', '!=', $admin->id)->get();
    $messages = $group->messages()->with('user')->latest()->get();

    $browseGroups = Group::where('status','approved')
                        ->whereNotIn('id',$user->groups()->pluck('groups.id'))
                        ->latest('id')
                        ->get();

    
    $userScore = UserScore::where('user_id', $user->id)->first();
    $score = $userScore ? round($userScore->score) : 0;
    
    $responseGroups = Http::get('http://127.0.0.1:5001/recommend-groups/' . $user->id);
    $recommendedGroups = [];


    if ($responseGroups->successful()) {
    $data = $responseGroups->json();
    $recommendedGroups = $data['recommendations'] ?? [];


    foreach ($recommendedGroups as &$group) {
        $groupModel = Group::find($group['id']);

        $group['discussions_count'] = $groupModel
            ? $groupModel->discussions()->count()
            : 0;
    }
}


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
        'score' => $score, // ADD
        'recommendedGroups' => $recommendedGroups, // ADD
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
    $notifications = Notification::where('user_id', auth()->id())
        ->latest()
        ->get();

    $notifCount = Notification::where('user_id', auth()->id())
        ->whereNull('read_at')
        ->count();

    Notification::where('user_id', auth()->id())
        ->whereNull('read_at')
        ->update([
            'read_at' => now(),
        ]);

    return view('student.notifications', [
        'notifications' => $notifications,
        'notifCount' => $notifCount,
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