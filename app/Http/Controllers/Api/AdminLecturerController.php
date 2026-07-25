<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\LecturerInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminLecturerController extends Controller
{
    public function store(Request $request)
    {
        if (
            $request->user()->role
            !== 'system_admin'
        ) {
            abort(403);
        }

        $data = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' =>
                'required|email|unique:users'
        ]);

        $token = Str::random(32);

        $user = User::create([

            'first_name' =>
                $data['first_name'],

            'last_name' =>
                $data['last_name'],

            'email' =>
                $data['email'],

            'role' =>
                'lecturer',

            'status' =>
                'pending',

            'is_enabled' =>
                true,

            'password' =>
                Str::random(32),

            'invite_token' =>
                $token,

            'invite_token_expires_at' =>
                now()->addDays(3)
        ]);

        $url =
            url(
                '/lecturer/activate/'
                .$token
            );

        Mail::to(
            $user->email
        )->send(
            new LecturerInvitation(
                $user,
                $url
            )
        );

        return response()->json([
            'message' =>
                'Invitation sent'
        ]);
    }
}