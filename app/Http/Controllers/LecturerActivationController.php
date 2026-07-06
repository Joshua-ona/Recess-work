<?php

namespace App\Http\Controllers;

use App\Mail\LecturerInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LecturerActivationController extends Controller
{
    /**
     * Show the "set your password" form, or an expired/invalid message.
     */
    public function show(string $token)
    {
        $user = $this->findByToken($token);

        return view('auth.activate-lecturer', [
            'invalid' => ! $user,
            'token'   => $token,
        ]);
    }

    /**
     * Set the password, activate the account, and burn the token.
     */
    public function activate(Request $request, string $token)
    {
        $user = $this->findByToken($token);

        if (! $user) {
            return redirect()
                ->route('lecturer.activate.show', ['token' => $token])
                ->withErrors(['password' => 'This activation link is invalid or has expired.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password'                => $request->input('password'),
            'status'                  => 'active',
            'email_verified_at'       => now(),
            'invite_token'            => null,
            'invite_token_expires_at' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Your account is active — you can now sign in.');
    }

    /**
     * Show the self-service resend form (lecturer enters their email).
     */
    public function resendForm()
    {
        return view('auth.resend-invite');
    }

    /**
     * Lecturer submits their email → regenerate token and resend the invite.
     * Fails silently if the email doesn't match a pending lecturer account,
     * to avoid leaking which emails are registered.
     */
    public function resendSelf(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->input('email'))
            ->where('role', 'lecturer')
            ->where('status', 'pending')
            ->first();

        if ($user) {
            $rawToken = Str::random(60);

            $user->update([
                'invite_token'            => hash('sha256', $rawToken),
                'invite_token_expires_at' => now()->addDays(3),
            ]);

            $activationUrl = route('lecturer.activate.show', ['token' => $rawToken]);
            Mail::to($user->email)->send(new LecturerInvitation($user, $activationUrl));
        }

        // Always show the same message whether the email matched or not.
        return back()->with('status',
            'If that email matches a pending lecturer account, a new activation link has been sent.'
        );
    }

    private function findByToken(string $token): ?User
    {
        return User::where('invite_token', hash('sha256', $token))
            ->where('invite_token_expires_at', '>', now())
            ->first();
    }
}
