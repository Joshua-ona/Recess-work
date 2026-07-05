<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Check if super admin already exists
        $admin = User::where('email', 'admin@mak.ac.ug')->first();

        if (!$admin) {
            User::create([
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@mak.ac.ug',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'is_enabled' => true,
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
        }
    }
}