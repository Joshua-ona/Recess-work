<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Verification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    public function show(){

    // if(!session('temp_user_id')){
    //     return redirect()->route('register')->withErrors(['otp' => 'Please register first.']);
    // }
        return views('auth.verify');
    }

       public function sendOtp(){
        $userId = session('temp_user_id');

        if(!$userId){
            return redirect()->route('register')->with('error', 'Session expired please register again');
        }

        $user = User::find($userId);

        if(!$user){
            session()->forget('temp_user_id');
            return redirect()->route('register'->with('error', 'User not found'));
        }

        return $this->sendOtpForUser($user);
    }

    public function sendOtpForUser($user)
    {
        $otp = random_int(100000,999999);

        \Log::info('2. OTP GENERATED', ['otp' => $otp]);
         Verification::updateOrCreate(
            ['user_id' => $user->id],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(10)],
            
         );

        Mail::raw("Your verification code is: $otp", function($message) use($user) {
            $message->to($user->email)->subject('Account Verification Code');
        });

        return back()->with('success','A new code has been sent to your email');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        $userId = Auth::id() ?? session('temp_user_id');

        if(!$userId){
            return redirect()->route('register')->withErrors(['otp' => 'Session expired. Register Again']);
        }

        $record = Verification::where('user_id', $userId)
                                ->where('otp', $request->otp)
                                ->where('expires_at', '>', now())
                                ->first();

        if($record){
                $user = User::find($userId);

                $user->update([
                    'email_verified_at' => now(),
                    'is_enabled' => true

                ]);

                Auth::login($user);
                $request->session()->regenerate();
                session()->forget('temp_user_id');
                $record->delete();

                return match($user->role){
                    'admin' => redirect()->route('admin.dashboard'),
                    'lecturer' => redirect()->route('lecturer.dashboard'),
                    default => redirect()->route('student.dashboard'),
                };
        }

        return back()->withErrors([
            'otp' => 'The code is invalid or has expired.'
        ]);
    }

}