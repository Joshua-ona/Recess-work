<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->onlyInput('email');
    }

    $request->session()->regenerate();

    return match (Auth::user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
}

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
{
    $data = $request->validate([
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'confirmed', 'min:8'],
        'role' => ['required', 'in:student,lecturer,admin'],
        'student_id' => ['required', 'string', 'max:255'],
    ]);

    $user = User::create([
        'name' => $data['first_name'].' '.$data['last_name'],
        'email' => $data['email'],
        'role' => $data['role'],
        'password' => $data['password'],
    ]);

    Auth::login($user);

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
}
  public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
}