<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function show()
    {
        if (!session('temp_user_id')) {
            return redirect()
                ->route('register')
                ->with('error', 'Please register first.');
        }

        return view('auth.verify');
    }

    public function sendOtp()
    {
        $userId = session('temp_user_id');

        if (!$userId) {
            return redirect()
                ->route('register')
                ->with('error', 'Session expired. Please register again.');
        }

        $user = User::find($userId);

        if (!$user) {
            session()->forget('temp_user_id');

            return redirect()
                ->route('register')
                ->with('error', 'User not found.');
        }

        return $this->sendOtpForUser($user);
    }

    public function sendOtpForUser(User $user)
    {
        $otp = (string) random_int(100000, 999999);

        Verification::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        Mail::raw(
            "Your Makerere verification code is: {$otp}",
            function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Email Verification');
            }
        );

        return back()->with('success', 'Verification code sent successfully.');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $userId = session('temp_user_id');

        if (!$userId) {
            return redirect()
                ->route('register')
                ->withErrors([
                    'otp' => 'Session expired. Please register again.'
                ]);
        }

        $verification = Verification::where('user_id', $userId)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$verification) {
            return back()->withErrors([
                'otp' => 'Invalid or expired verification code.'
            ]);
        }

        $user = User::findOrFail($userId);

        $user->update([
            'email_verified_at' => now(),
            'is_enabled' => true,
        ]);

        $verification->delete();

        session()->forget('temp_user_id');

        Auth::login($user);

        $request->session()->regenerate();

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'lecturer' => redirect()->route('lecturer.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default => redirect('/'),
        };
    }
}