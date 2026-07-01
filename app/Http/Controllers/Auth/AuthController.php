<?php


namespace App\Http\Controllers\Auth;


<<<<<<< HEAD
    use App\Http\Controllers\Controller;
    use App\Models\User;
    use App\Services\UserRegistrationService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
     use Illuminate\Auth\Events\Registered;
=======
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

<<<<<<< HEAD

public function login(Request $request)
=======
    public function login(Request $request)
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f
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

    if(!$user->is_enabled){
        Auth::logout();
        $request->session()->invalidate();
        
        return back()->withErrors([
            'email' => 'Account is disabled.Contact Admin.',
        ])->onlyInput('email');
    }

     if(is_null($user->email_verified_at)){
        Auth::logout();
        $request->session()->invalidate();
        
        return back()->withErrors([
            'email' => 'Email not verified.Check your email for OTP.',
        ])->onlyInput('email');
    }

    return match ($user->role) {
        'system_admin' => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        default => redirect()->route('home'),
    };
}

<<<<<<< HEAD
    public function showRegister(){
            return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users',
            'regex:/^[a-zA-Z0-9._%+-]+@(students\.)?mak\.ac\.ug$'
        ],
        'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $registrationService->execute($data);

        event(new Registered($user));

        //Auth::login($user);

        return redirect()->route('verification.notice')->with('success','You have successfully registed.Please check your email to verify your account');

    }
    public function logout(Request $request)
    {
        Auth::logout();
=======
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
    ]);

    Auth::login($user);
>>>>>>> 44d2470e921153fee253e0c93f4c5d1009eeb50f

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