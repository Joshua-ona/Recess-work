<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LecturerInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminLecturerController extends Controller
{
    public function create()
    {
        return view('admin.lecturers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
        ]);

        $rawToken = Str::random(60);

        $user = User::create([
            'first_name'               => $data['first_name'],
            'last_name'                => $data['last_name'],
            'email'                    => $data['email'],
            'role'                     => 'lecturer',
            'status'                   => 'pending',
            'password'                 => Str::random(32),
            'invite_token'             => hash('sha256', $rawToken),
            'invite_token_expires_at'  => now()->addDays(3),
        ]);

        $activationUrl = route('lecturer.activate.show', ['token' => $rawToken]);
        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));

        return redirect()
            ->route('admin.Users.index')
            ->with('status', "Invitation sent to {$user->email}.");
    }

    /**
     * Regenerate a fresh token and resend the activation email.
     * Works for both expired links (admin-triggered) and
     * lecturer self-service requests (via the expired-link page).
     */
    public function resend(User $user)
    {
        // Only makes sense for a lecturer who hasn't activated yet.
        if ($user->role !== 'lecturer' || $user->status !== 'pending') {
            return back()->with('status', 'This account does not need a new invite.');
        }

        $rawToken = Str::random(60);

        $user->update([
            'invite_token'            => hash('sha256', $rawToken),
            'invite_token_expires_at' => now()->addDays(3),
        ]);

        $activationUrl = route('lecturer.activate.show', ['token' => $rawToken]);
        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));

        return back()->with('status', "New invitation sent to {$user->email}.");
    }
}
