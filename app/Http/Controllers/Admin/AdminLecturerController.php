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
    /**
     * Show the "add a lecturer" form.
     */
    public function create()
    {
        return view('admin.lecturers.create');
    }

    /**
     * Create a lecturer account on the admin's behalf and email them an
     * activation link. The account has no usable password until they set
     * one through that link — the random one generated here is never
     * shared with anyone.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $rawToken = Str::random(60);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'role' => 'lecturer',
            'status' => 'pending',
            'password' => Str::random(32),
            'invite_token' => hash('sha256', $rawToken),
            'invite_token_expires_at' => now()->addDays(3),
        ]);

        $activationUrl = route('lecturer.activate.show', ['token' => $rawToken]);

        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));

        return redirect()
            ->route('admin.Users.index')
            ->with('status', "Invitation sent to {$user->email}.");
    }
}
