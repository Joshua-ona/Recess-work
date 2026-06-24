<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class AdminUserController extends Controller
{
    /**
     * Approve a pending registration (req. 5 — rules acceptance gate).
     */
    public function approve(User $user)
    {
        $user->update(['status' => 'active']);

        return back()->with('status', "{$user->name} approved.");
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
     * See also: Console\Commands\CheckMemberInactivity for the scheduled
     * version of this same logic.
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

        return back()->with('status', "Warning issued to {$user->name}.");
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

        return back()->with('status', "{$user->name} blacklisted.");
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

        return back()->with('status', "{$user->name} reinstated.");
    }
}
