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
                'role' => 'admin',
                'is_enabled' => true,
                'email_verified_at' => now(),
            ]);

        User::updateOrcreate(
            [
                'email' => 'mark@mak.ac.ug', 
            ],
            [
                'first_name' => 'mark',
                'last_name' => 'lectures',
                 
                'password' => Hash::make('mark2004'),
                'role' => 'lecturer',
                'is_enabled' => true,
                'email_verified_at' => now(),
            ]);
    }
}