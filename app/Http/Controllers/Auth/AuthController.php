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

<<<<<<< HEAD
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

    // Blacklisting still actually stops someone from logging back in;
    // there's just no pending-approval gate before that point anymore.
    if ($user->status === 'blacklisted') {
        Auth::logout();
        $request->session()->invalidate();

        return back()->withErrors([
            'email' => 'This account has been blacklisted.',
        ])->onlyInput('email');
    }

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
}

    public function showRegister()
=======
        public function login(Request $request)
>>>>>>> 6db54cd608af2260cbdbd38d1d960cd85f2c3889
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        //check first for user existence
        $user = User::where('email',$request->email)->first();

        if(!$user){
            return back()->withErrors([
                'email' => 'No account found with this email',
            ])->onlyInput('email');
        }

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return match (Auth::user()->role) {
            'system_admin' => redirect()->route('admin.dashboard'),
            'lecturer' => redirect()->route('lecturer.dashboard'),
            default => redirect()->route('student.dashboard'),
        };
    }


<<<<<<< HEAD
    $user = User::create([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'email' => $data['email'],
        'role' => $data['role'],
        'password' => $data['password'],
    ]);
=======
>>>>>>> 6db54cd608af2260cbdbd38d1d960cd85f2c3889

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
