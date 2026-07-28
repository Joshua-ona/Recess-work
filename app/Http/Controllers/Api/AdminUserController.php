<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    private function authorizeAdmin(Request $request)
    {
        if (!in_array($request->user()->role, ['admin', 'system_admin'], true)) {
            abort(403, 'Unauthorized');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        $search = $request->search;

        $users = User::with(['warnings.issuer'])
            ->when($search, function ($query, $search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'first_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'last_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'email',
                        'like',
                        "%{$search}%"
                    );

                });

            })
            ->orderBy('first_name')
            ->get();

        $onlineIds = [];

        if (config('session.driver') === 'database') {

            $onlineIds =
                DB::table(
                    config(
                        'session.table',
                        'sessions'
                    )
                )
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->values();

        }

        return response()->json([
            'users' => $users,
            'online_ids' => $onlineIds
        ]);
    }

    public function approve(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        $user->update([
            'status' => 'active'
        ]);

        return response()->json([
            'message' => 'User approved'
        ]);
    }

    public function decline(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        $user->delete();

        return response()->json([
            'message' => 'User declined'
        ]);
    }

    public function warn(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $user->warnings()->create([

            'issued_by' =>
                $request->user()->id,

            'message' =>
                $request->message
        ]);

        \App\Models\Notification::create([
            'user_id'      => $user->id,
            'type'         => 'warning',
            'reference_id' => null,
            'message'      => $request->message,
            'sender'       => $request->user()->first_name . ' ' . $request->user()->last_name,
        ]);

        $user->warning_count++;

        $user->last_warning_at = now();

        if ($user->warning_count >= 2) {

            $user->status = 'blacklisted';

            $user->blacklisted_until =
                now()->addDays(
                    config(
                        'forum.blacklist_days',
                        7
                    )
                );
        }

        $user->save();

        return response()->json([
            'message' =>
                'Warning sent successfully'
        ]);
    }

    public function blacklist(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        $user->update([

            'status' => 'blacklisted',

            'blacklisted_until' =>
                now()->addDays(
                    config(
                        'forum.blacklist_days',
                        7
                    )
                )
        ]);

        return response()->json([
            'message' =>
                'User blacklisted'
        ]);
    }

    public function unblacklist(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        $user->update([

            'status' => 'active',

            'warning_count' => 0,

            'blacklisted_until' => null
        ]);

        return response()->json([
            'message' =>
                'User reinstated'
        ]);
    }

    public function logout(
        Request $request,
        User $user
    )
    {
        $this->authorizeAdmin($request);

        if (
            config('session.driver')
            === 'database'
        ) {

            DB::table(
                config(
                    'session.table',
                    'sessions'
                )
            )
            ->where(
                'user_id',
                $user->id
            )
            ->delete();
        }

        return response()->json([
            'message' =>
                'User logged out'
        ]);
    }
}