<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserRegistrationService
{

public function execute(array $data):User{

    $email = trim($data['email']);
    $adminEmail = trim(config('university.admin_email'));

    if($email === $adminEmail){
        throw ValidationException::withMessages([
            'email' => 'Reserved Email Address.Contact the Administrator'
            ]);
    }


    $user = new User();

    $user->email = $email;
    $user->first_name = trim($data['first_name']);
    $user->last_name = trim($data['last_name']);
    $user->warning_count = 0;
    $user->password = Hash::make($data['password']);
    $user->role = 'student';

    $user->save();
    return $user;

    }
}