<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LecturerActivationController extends Controller
{
    /**
     * Show the "set your password" form, or an error if the link is
     * invalid/expired.
     */
    public function show(string $token)
    {
        $user = $this->findByToken($token);

        if (! $user) {
            return view('auth.activate-lecturer', [
                'invalid' => true,
                'token' => $token,
            ]);
        }

        return view('auth.activate-lecturer', [
            'invalid' => false,
            'token' => $token,
        ]);
    }

    /**
     * Set the password, activate the account, and consume the token so it
     * can't be reused.
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
            'password' => $request->input('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'invite_token' => null,
            'invite_token_expires_at' => null,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Your account is active — you can now sign in.');
    }

    /**
     * Look up a pending lecturer account by the raw token from the URL,
     * hashing it the same way it was hashed at creation time. Returns null
     * if there's no match or the link has expired.
     */
    private function findByToken(string $token): ?User
    {
        return User::where('invite_token', hash('sha256', $token))
            ->where('invite_token_expires_at', '>', now())
            ->first();
    }
}
