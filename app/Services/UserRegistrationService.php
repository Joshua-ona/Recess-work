<?php

namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRegistrationService
{

public function execute(array $data):User{

    $email = trim($data['email']);
    $adminEmail = trim(config('university.admin_email'));
    $domain = substr(strrchr($email,'@'), 1);

    // allowed domains
    $allowedDomains = ['mak.ac.ug','students.mak.ac.ug'];

    if(!in_array($domain, $allowedDomains)){
        throw new \Exception("Registration Not Allowed for this email domain");
    }
    
    if($email === $adminEmail){
        $role = 'system_admin';
    }

    else if($domain === 'mak.ac.ug'){
        $role='lecturer';
    }

    else if($domain === 'students.mak.ac.ug'){
        $role='student';
    }
    else{
        throw new \Exception('Registration Not Allowed for this Email');
    }

    $user = new User();

    $user->email = $email;
    $user->first_name = $data['first_name'];
    $user->last_name = $data['last_name'];
    $user->password = Hash::make($data['password']);
    $user->role = $role;
    $user->is_enabled = true;

    $user->save();

    return $user;

    }
}