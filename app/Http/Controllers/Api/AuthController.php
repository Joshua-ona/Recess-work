<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if(!Auth::attempt($credentials)){
            return response()->json([
                'message'=>'Invalid email or password'
            ],401);
        }

        $user = Auth::user();

<<<<<<< HEAD
        $token = $user->createToken('desktop-app')->plainTextToken;
=======

        $token = $user->createToken('javafx-desktop')->plainTextToken;
>>>>>>> 3930dd6f892868253f8de36e2aaa384a143c5beb

        return response()->json([
            'message'=>'Login successful',
            'token'=>$token,
            'user'=>[
    'id'=>$user->id,
    'first_name'=>$user->first_name,
    'last_name'=>$user->last_name,
    'email'=>$user->email,
    'role'=>$user->role
]
        ]);
    }
}