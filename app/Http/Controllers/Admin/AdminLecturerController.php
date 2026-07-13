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

        $token = Str::random(32); // short, URL-safe, no special chars

        $user = User::create([
            'first_name'              => $data['first_name'],
            'last_name'               => $data['last_name'],
            'email'                   => $data['email'],
            'role'                    => 'lecturer',
            'status'                  => 'pending',
            'is_enabled'              => true,
            'password'                => Str::random(32),
            'invite_token'            => $token,       // raw, no hash
            'invite_token_expires_at' => now()->addDays(3),
        ]);

        $activationUrl = url('/lecturer/activate/' . $token); // url() not route() — avoids APP_URL caching issues

        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));

        return redirect()
            ->route('admin.Users.index')
            ->with('status', "Invitation sent to {$user->email}.");
    }

    public function resend(User $user)
    {
        if ($user->role !== 'lecturer' || $user->status !== 'pending') {
            return back()->with('status', 'This account does not need a new invite.');
        }

        $token = Str::random(32);

        $user->update([
            'invite_token'            => $token,
            'invite_token_expires_at' => now()->addDays(3),
        ]);

        $activationUrl = url('/lecturer/activate/' . $token);

        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));

        return back()->with('status', "New invitation sent to {$user->email}.");
    }
}
