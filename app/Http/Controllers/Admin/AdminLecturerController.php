<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LecturerInvitation;
use App\Models\LecturerInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminLecturerController extends Controller
{
    public function create()
    {
        return view('admin.lecturers.create');
    }

    /**
     * Create a pending invitation and email it. Deliberately does NOT create
     * a row in the users table — nobody should count as a "user" (or show
     * up in Manage Users) until they've actually activated the account by
     * setting a password.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        // Clear out any invite for this email that's already expired, so a
        // fresh one can be created without tripping the unique constraint.
        LecturerInvite::where('email', $data['email'])
            ->where('expires_at', '<', now())
            ->delete();

        if (LecturerInvite::where('email', $data['email'])->exists()) {
            return back()
                ->withErrors(['email' => 'An invitation is already pending for this email. Use "Resend" from the pending invitations list instead.'])
                ->withInput();
        }

        $token = Str::random(32); // short, URL-safe, no special chars

        $invite = LecturerInvite::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'token'      => $token,
            'invited_by' => $request->user()->id,
            'expires_at' => now()->addDays(3),
        ]);

        $activationUrl = url('/lecturer/activate/' . $token); // url() not route() — avoids APP_URL caching issues

        Mail::to($invite->email)->send(new LecturerInvitation($invite, $activationUrl));

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Invitation sent to {$invite->email}. Activation link: {$activationUrl}");
    }

    public function resend(LecturerInvite $invite)
    {
        $token = Str::random(32);

        $invite->update([
            'token'      => $token,
            'expires_at' => now()->addDays(3),
        ]);

        $activationUrl = url('/lecturer/activate/' . $token);

        Mail::to($invite->email)->send(new LecturerInvitation($invite, $activationUrl));

        return back()->with('status', "New invitation sent to {$invite->email}. Activation link: {$activationUrl}");
    }

    public function cancel(LecturerInvite $invite)
    {
        $invite->delete();

        return back()->with('status', 'Invitation cancelled.');
    }

    public function activate(string $token)
    {
        $invite = LecturerInvite::where('token', $token)->first();

        if (!$invite) {
            return view('auth.activate-lecturer', ['state' => 'invalid']);
        }

        if ($invite->isExpired()) {
            return view('auth.activate-lecturer', [
                'state' => 'expired',
                'userEmail' => $invite->email,
            ]);
        }

        return view('auth.activate-lecturer', [
            'state' => 'valid',
            'invite' => $invite,
            'token' => $token,
        ]);
    }

    /**
     * This is the moment the real User account is actually created — not
     * when the admin sent the invite.
     */
    public function completeActivation(Request $request, string $token)
    {
        $invite = LecturerInvite::where('token', $token)->first();

        if (!$invite) {
            return view('auth.activate-lecturer', ['state' => 'invalid']);
        }

        if ($invite->isExpired()) {
            return view('auth.activate-lecturer', [
                'state' => 'expired',
                'userEmail' => $invite->email,
            ]);
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Someone could theoretically have registered this email through
        // another path while the invite was outstanding — guard against a
        // duplicate-email crash rather than a confusing 500.
        if (User::where('email', $invite->email)->exists()) {
            $invite->delete();

            return view('auth.activate-lecturer', ['state' => 'invalid']);
        }

        $user = User::create([
            'first_name'        => $invite->first_name,
            'last_name'         => $invite->last_name,
            'email'             => $invite->email,
            'role'              => 'lecturer',
            'status'            => 'active',
            'is_enabled'        => true,
            'password'          => bcrypt($data['password']),
            'email_verified_at' => now(),
        ]);

        $invite->delete();

        // Log the lecturer in directly instead of sending them to /login.
        // (Sending them to /login was unreliable: if this activation link was opened
        // in a browser/tab that already had an authenticated session — e.g. the admin
        // who just created the invitation — the "guest" middleware on /login would
        // bounce them straight back to that existing session's dashboard instead of
        // letting the new lecturer sign in.)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('lecturer.dashboard')->with('status', 'Your account is now active. Welcome!');
    }

    public function resendSelf(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $invite = LecturerInvite::where('email', $data['email'])->first();

        // Always show the same success message, whether or not the email
        // matched a real pending invitation — avoids leaking which emails exist.
        if ($invite) {
            $token = Str::random(32);

            $invite->update([
                'token'      => $token,
                'expires_at' => now()->addDays(3),
            ]);

            $activationUrl = url('/lecturer/activate/' . $token);

            Mail::to($invite->email)->send(new LecturerInvitation($invite, $activationUrl));
        }

        return back()->with('status', 'If that email matches a pending invitation, a new activation link has been sent.');
    }
}