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
    $domain = substr(strrchr($email,'@'), 1);

    if($email === $adminEmail){
        throw ValidationException::withMessages([
            'email' => 'Reserved Email Address.Contact the Administrator'
            ]);
    }

    if($domain === 'mak.ac.ug'){
            throw ValidationException::withMessages([
            'email' => 'Lecturer registration is by the Administrator only.'
            ]);
    }

    if($domain === 'students.mak.ac.ug'){
        $role='student';
    }
    else{
        throw ValidationException::withMessages([
            'email' => 'Registration is only allowed for @students.mak.ac.ug.'
            ]);
    }

    $user = new User();

    $user->email = $email;
    $user->first_name = trim($data['first_name']);
    $user->last_name = trim($data['last_name']);
    $user->password = Hash::make($data['password']);
    $user->role = $role;
    $user->is_enabled = false;
    $user->email_verified_at = null;

    $user->save();

    return $user;

    }
}