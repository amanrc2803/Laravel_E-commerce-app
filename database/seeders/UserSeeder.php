<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   

    public function run()
    {
        // Create an admin user with role 1
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'), // Ensure password is hashed
            'role' => 1, // 1 for admin
        ]);

        // Create a regular user with role 2
        User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('user'), // Ensure password is hashed
            'role' => 2, // 2 for user
        ]);
    }
}
