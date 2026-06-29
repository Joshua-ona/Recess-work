<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Show the registration page.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with this email.',
            ])->onlyInput('email');
        }

        // Attempt login
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Prevent blacklisted users from logging in
        if ($user->status === 'blacklisted') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account has been blacklisted.',
            ])->onlyInput('email');
        }

        return match ($user->role) {
            'system_admin' => redirect()->route('admin.dashboard'),
            'lecturer'     => redirect()->route('lecturer.dashboard'),
            default        => redirect()->route('student.dashboard'),
        };
    }

    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'role'       => ['required'],
            'password'   => ['required', 'confirmed', 'min:8']
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'password'   => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return match ($user->role) {
            'system_admin' => redirect()->route('admin.dashboard'),
            'lecturer'     => redirect()->route('lecturer.dashboard'),
            default        => redirect()->route('student.dashboard'),
        };
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

