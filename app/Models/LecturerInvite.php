<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A pending "add a lecturer" invitation.
 *
 * Deliberately NOT a users-table row: nobody should show up in the member
 * count, the Manage Users list, or the pending-approvals widget just
 * because an admin typed their name in. A real User is only created once
 * the invited lecturer follows the emailed link and sets a password
 * (see AdminLecturerController::completeActivation).
 */
class LecturerInvite extends Model
{
    protected $table = 'lecturer_invitations';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'token',
        'invited_by',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->expires_at === null || now()->greaterThan($this->expires_at);
    }
}