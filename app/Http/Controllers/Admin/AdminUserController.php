<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
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
     * Manually issue an inactivity warning (req. 4). After 2 warnings the
     * member is auto-blacklisted for a configurable number of days.
     */
    public function warn(User $user)
    {
        $user->warning_count += 1;
        $user->last_warning_at = now();

        if ($user->warning_count >= 2) {
            $user->status = 'blacklisted';
            $user->blacklisted_until = now()->addDays(config('forum.blacklist_days', 7));
        }

        $user->save();

        return back()->with('status', "Warning issued to {$user->full_name}.");
    }

    /**
     * Manual override to blacklist a member outright.
     */
    public function blacklist(User $user)
    {
        $user->update([
            'status' => 'blacklisted',
            'blacklisted_until' => now()->addDays(config('forum.blacklist_days', 7)),
        ]);

        return back()->with('status', "{$user->full_name} blacklisted.");
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
    public function logout(User $user)
    {
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
