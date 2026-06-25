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

        User::create(
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@forum.com',
                'password' => Hash::make('admin1234'),
                'role' => 'system_admin',
                'is_enabled' => true,
            ]);
    }
}
