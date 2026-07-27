<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * List every user, with a search box and an "online now" indicator
     * (based on whether they have a row in the sessions table), so the
     * admin can blacklist or log out anyone — not just members who already
     * have a warning.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        $users = User::query()
            ->with(['warnings.issuer'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        // A user "is online" here means they currently have a session row —
        // i.e. they haven't logged out (or been force-logged-out) since
        // last signing in. Only meaningful on the 'database' session driver.
        $onlineIds = [];
        if (config('session.driver') === 'database') {
            $onlineIds = DB::table(config('session.table', 'sessions'))
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->unique()
                ->all();
        }

        return view('admin.users.index', [
            'users' => $users,
            'onlineIds' => $onlineIds,
            'search' => $search ?? '',
        ]);
    }

    /**
     * Approve a pending registration (req. 5 — rules acceptance gate).
     */
    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('status', "{$user->full_name} approved.");
    }

    /**
     * Decline a pending registration. Deletes the record outright since a
     * declined applicant never had platform access to begin with.
     */
    public function decline(User $user)
    {
        $user->delete();

        return back()->with('status', 'Registration declined.');
    }

    /**
     * Send a warning message for violating platform rules (req. 4). After
     * 2 warnings the member is auto-blacklisted for a configurable number
     * of days.
     */
    public function warn(Request $request, User $user)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        if ($user->id === $request->user()->id) {
            return back()->with('status', "You can't warn your own account.");
        }

        $user->warnings()->create([
            'issued_by' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        $user->warning_count += 1;
        $user->last_warning_at = now();

        if ($user->warning_count >= 2) {
            $user->status = 'blacklisted';
            $user->blacklisted_until = now()->addDays(config('forum.blacklist_days', 7));
        }

        $user->save();

        if ($user->status === 'blacklisted') {
            $this->forceLogout($user);
        }

        return back()->with('status', "Warning sent to {$user->full_name}.");
    }

    /**
     * Manual override to blacklist a member outright — from anywhere in
     * the admin dashboard, regardless of whether they've been warned.
     */
    public function blacklist(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('status', "You can't blacklist your own account.");
        }

        $user->update([
            'status' => 'blacklisted',
            'blacklisted_until' => now()->addDays(config('forum.blacklist_days', 7)),
        ]);

        $this->forceLogout($user);

        return back()->with('status', "{$user->full_name} blacklisted and logged out.");
    }

    /**
     * Shared helper: clear a user's active session rows so they're kicked
     * out on their very next request (used by both the manual "Log out"
     * action and whenever someone gets blacklisted).
     */
    private function forceLogout(User $user): void
    {
        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }
    }

    /**
     * Force-log-out a member from every device right now — e.g. to act on a
     * security concern, or as a softer step before/instead of a blacklist.
     *
     * This project doesn't have Sanctum installed (no API tokens to revoke),
     * so "logout" here means clearing that member's web session rows. It
     * only has teeth on the 'database' session driver (which this project
     * uses by default) — on file/cookie/redis drivers there's no reliable
     * server-side way to invalidate a session by user id, so this is a
     * harmless no-op there.
     */
    public function logout(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('status', "Use the regular Log out button for your own account.");
        }

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }

        return back()->with('status', "{$user->full_name} has been logged out.");
    }

    /**
     * Reinstate a blacklisted member and reset their warning count.
     */
    public function unblacklist(User $user)
    {
        $user->update([
            'status' => 'active',
            'warning_count' => 0,
            'blacklisted_until' => null,
        ]);

        return back()->with('status', "{$user->full_name} reinstated.");
    }
}