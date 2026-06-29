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



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
