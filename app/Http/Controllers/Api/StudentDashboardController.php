<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Discussion;
use App\Models\Group;
use App\Models\QuizSubmission;
use App\Models\UserScore;

class StudentDashboardController extends Controller
{
    public function __construct(private GroupService $groupService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Same source as the web dashboard's "My Groups" panel
        $myGroups = $this->groupService->getMyGroups($user);
        $myGroupIds = $myGroups->pluck('id')->toArray();

        // Same source as the web dashboard's "Browse All Groups" panel,
        // pre-filtered here (desktop just wants plain names, no client-side filtering)
        $browseGroups = Group::where('status', 'approved')
            ->whereNotIn('id', $myGroupIds)
            ->orderBy('name')
            ->get();

        // Same source as the web dashboard's Participation Score card
        $userScore = UserScore::where('user_id', $user->id)->first();
        $score = $userScore ? round($userScore->score) : 0;

        // Same Python ML call as the web dashboard's "Groups You Might Like"
        $recommendedGroups = [];
        try {
            $response = Http::timeout(5)
                ->get('http://127.0.0.1:5001/recommend-groups/' . $user->id);

            if ($response->successful()) {
                $recommendedGroups = $response->json()['recommendations'] ?? [];
            }
        } catch (\Exception $e) {
            // ML service unreachable — desktop just shows "No recommendations yet."
            $recommendedGroups = [];
        }

        return response()->json([

            'posts' => Discussion::where('user_id', $user->id)->count(),

            'quizzes' => QuizSubmission::where('user_id', $user->id)->count(),

            'groups' => $myGroups->count(),

            // Real 0-100 participation score, matching the web dashboard
            'participation' => $score,

            // Desktop's ListView<String> just wants names
            'myGroups' => $myGroups->pluck('name')->values(),

            'browseGroups' => $browseGroups->pluck('name')->values(),

            // Desktop's RecommendedGroup expects: id, name, description, score, reason
            'recommendedGroups' => collect($recommendedGroups)->map(function ($group) {
                return [
                    'id' => $group['id'] ?? null,
                    'name' => $group['name'] ?? '',
                    'description' => $group['description'] ?? '',
                    'score' => $group['score'] ?? 0,
                    'reason' => $group['reason'] ?? 'Recommended for you',
                ];
            })->values(),

        ]);
    }
}