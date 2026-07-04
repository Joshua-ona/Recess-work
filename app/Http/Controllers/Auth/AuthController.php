<?php


namespace App\Http\Controllers\Auth;


    use App\Http\Controllers\Controller;
    use App\Models\User;
    use App\Services\UserRegistrationService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Auth\Events\Registered;
     


class AuthController extends Controller
{

     public function __construct(UserRegistrationService $registrationService)
     {
        $this->registrationService = $registrationService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

   public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    //$request->session()->regenerateToken();
    return redirect()->route('login');
}

    public function showRegister(){
            return view('auth.register');
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

    // Blacklisting still actually stops someone from logging back in;
    // there's just no pending-approval gate before that point anymore.
    if ($user->status === 'blacklisted') {
        Auth::logout();
        $request->session()->invalidate();

        return back()->withErrors([
            'email' => 'This account has been blacklisted.',
        ])->onlyInput('email');
    }


    return match ($user->role){
        'system_admin' => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        //default => redirect()->route('home'),

    };
    
    
        
}


    public function register(Request $request)
    {
        \Log::info('Register parts');
        $data = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => [
            'required',
            'string',
            'email',
            'max:255',
            'unique:users',
        ],
        'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $this->registrationService->execute($data);

        return redirect()->route('student.dashboard');

    }
  
}

    
 