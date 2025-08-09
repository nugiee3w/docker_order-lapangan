<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@booking.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
            'email_verified_at' => now(),
        ]);

        // Staff user
        User::create([
            'name' => 'Staff Lapangan',
            'email' => 'staff@booking.com',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'phone' => '081234567891',
            'address' => 'Jl. Staff No. 2, Jakarta',
            'email_verified_at' => now(),
        ]);

        // Customer users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'phone' => '081234567892',
            'address' => 'Jl. Customer No. 3, Jakarta',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
            'phone' => '081234567893',
            'address' => 'Jl. Customer No. 4, Jakarta',
            'email_verified_at' => now(),
        ]);
    }
}
