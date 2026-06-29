<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function showRegistrationForm()
    {
        return view('auth.register');
    }


    public function register(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required',
            'agreed_rules' => 'accepted',
        ]);


        $user = User::create([

            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_enabled' => false,

        ]);


        return response()->json([

            'message' => 'Registered successfully',
            'user' => $user

        ], 201);

    }

}