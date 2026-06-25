<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\UserRegistrationService;
use Illuminate\Http\Request;
use Exception;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    // use RegistersUsers;
    protected $registrationService;


    public function __construct(UserRegistrationService $service)
    {
        $this->registrationService = $service;
    }
   

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */


    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'terms' => ['required','accepted'],
        ]);

        try{
            $user = $this->registrationService->execute($request->all());
            auth()->login($user);
            return redirect('/home');
        }catch(\Exception $e){
            return back()->withErrors([
                'email' =>
                        $e->getMessage()
            ]);
        }
    }


    protected function create(array $data)
    {
      return $this->registrationService->execute($data);
    }

    protected function redirectTo()
    {

    if(!auth() -> check()){
        return '/home';
    }
        $role = auth()->user()->role;

        return match($role){
            'system_admin' => route('admin.dashboard'),
            'lecturer' => route('lecturer.dashboard'),
            'student' => route('student.dashboard'),
            default => '/home',
        };
    }

    public function showRegister()
    {
         return view('auth.register');
    }

    public function register(Request $request){
        $this->validator($request->all())->validate();
        $user = $this->create($request->all());
        auth()->login($user);

        return redirect($this->redirectTo());
    }
}
