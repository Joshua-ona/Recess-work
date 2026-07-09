<?php

namespace App\Http\Controllers;

use App\Mail\LecturerInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LecturerActivationController extends Controller
{
    public function show(string $token)
    {
        $user = User::where('invite_token', $token)->first();

        // Token not found at all
        if (! $user) {
            return view('auth.activate-lecturer', [
                'state' => 'invalid',
                'token' => $token,
            ]);
        }

        // Token found but expired — show resend form pre-filled with their email
        if ($user->invite_token_expires_at < now()) {
            return view('auth.activate-lecturer', [
                'state'     => 'expired',
                'token'     => $token,
                'userEmail' => $user->email,
            ]);
        }

        // Valid — show the set-password form
        return view('auth.activate-lecturer', [
            'state' => 'valid',
            'token' => $token,
        ]);
    }

    public function activate(Request $request, string $token)
    {
        $user = User::where('invite_token', $token)->first();

        if (! $user || $user->invite_token_expires_at < now()) {
            return redirect('/lecturer/activate/' . $token);
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password'                => $request->input('password'),
            'status'                  => 'active',
            'is_enabled'              => true,
            'email_verified_at'       => now(),
            'invite_token'            => null,
            'invite_token_expires_at' => null,
        ]);

        return redirect()->route('login')
            ->with('status', 'Your account is active — you can now sign in.');
    }

    public function resendForm()
    {
        return view('auth.resend-invite');
    }

    public function resendSelf(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->input('email'))
            ->where('role', 'lecturer')
            ->where('status', 'pending')
            ->first();

        if ($user) {
            $token = Str::random(32);

            $user->update([
                'invite_token'            => $token,
                'invite_token_expires_at' => now()->addDays(3),
            ]);

            $activationUrl = url('/lecturer/activate/' . $token);
            Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));
        }

        return back()->with('status',
            'If that email matches a pending lecturer account, a new activation link has been sent.'
        );
    }
}
