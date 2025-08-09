<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin Lapangan
        User::firstOrCreate(
            ['email' => 'admin@lapangan.com'],
            [
                'name' => 'Admin Lapangan',
                'email' => 'admin@lapangan.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Staff Lapangan
        User::firstOrCreate(
            ['email' => 'staff@lapangan.com'],
            [
                'name' => 'Staff Lapangan',
                'email' => 'staff@lapangan.com',
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "✅ Admin users created for Lapangan Service\n";
    }
}
