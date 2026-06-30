<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create the system administrator

        User::updateOrcreate(
            [
                'email' => config('university.admin_email'), 
            ],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                 
                'password' => Hash::make(config('university.admin_password')),
                'role' => 'system_admin',
                'is_enabled' => true,
                'email_verified_at' => now(),
            ]);
    }
}
