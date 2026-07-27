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
            ->route('admin.users.index')
            ->with('status', "Invitation sent to {$user->email}. Activation link: {$activationUrl}");
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

        return back()->with('status', "New invitation sent to {$user->email}. Activation link: {$activationUrl}");
    }

public function activate(string $token)
{
    $user = User::where('invite_token', $token)->first();

    if (!$user) {
        return view('auth.activate-lecturer', ['state' => 'invalid']);
    }

    if ($user->invite_token_expires_at === null || now()->greaterThan($user->invite_token_expires_at)) {
        return view('auth.activate-lecturer', [
            'state' => 'expired',
            'userEmail' => $user->email,
        ]);
    }

    return view('auth.activate-lecturer', [
        'state' => 'valid',
        'user' => $user,
        'token' => $token,
    ]);
}

public function completeActivation(Request $request, string $token)
{
    $user = User::where('invite_token', $token)->first();

    if (!$user) {
        return view('auth.activate-lecturer', ['state' => 'invalid']);
    }

    if ($user->invite_token_expires_at === null || now()->greaterThan($user->invite_token_expires_at)) {
        return view('auth.activate-lecturer', [
            'state' => 'expired',
            'userEmail' => $user->email,
        ]);
    }

    $data = $request->validate([
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user->update([
        'password'                => bcrypt($data['password']),
        'status'                  => 'active',
        'invite_token'            => null,
        'invite_token_expires_at' => null,
        'email_verified_at'       => now(),
    ]);

    return redirect()->route('login')->with('status', 'Your account is now active. Please log in.');
}

public function resendSelf(Request $request)
{
    $data = $request->validate([
        'email' => ['required', 'email'],
    ]);

    $user = User::where('email', $data['email'])
        ->where('role', 'lecturer')
        ->where('status', 'pending')
        ->first();

    // Always show the same success message, whether or not the email
    // matched a real pending account — avoids leaking which emails exist.
    if ($user) {
        $token = Str::random(32);

        $user->update([
            'invite_token'            => $token,
            'invite_token_expires_at' => now()->addDays(3),
        ]);

        $activationUrl = url('/lecturer/activate/' . $token);

        Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));
    }

    return back()->with('status', 'If that email matches a pending invitation, a new activation link has been sent.');
}
}