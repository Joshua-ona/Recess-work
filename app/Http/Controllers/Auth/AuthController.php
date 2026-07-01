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

    $user = Auth::user();

    // Blacklisting stops a login outright.
    if ($user->status === 'blacklisted') {
        Auth::logout();
        $request->session()->invalidate();

        return back()->withErrors([
            'email' => 'This account has been blacklisted.',
        ])->onlyInput('email');
    }

    // Lecturers who self-registered (picked "Lecturer" at sign-up, rather
    // than being invited by an admin) need explicit admin approval before
    // they can sign in.
    if ($user->status === 'pending') {
        Auth::logout();
        $request->session()->invalidate();

        return back()->withErrors([
            'email' => 'Your lecturer account is awaiting admin approval.',
        ])->onlyInput('email');
    }

    return match ($user->role) {
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
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'role' => $data['role'],
        'password' => $data['password'],
        // Self-registered lecturers need an admin's go-ahead before they
        // can use the account; everyone else is active immediately.
        'status' => $data['role'] === 'lecturer' ? 'pending' : 'active',
    ]);

    if ($user->status === 'pending') {
        return redirect()
            ->route('login')
            ->with('status', 'Account created. An admin needs to approve your lecturer account before you can sign in.');
    }

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