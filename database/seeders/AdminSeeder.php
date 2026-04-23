<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed an admin account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@khidma.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'Khidma',
                'username' => 'admin',
                'phone' => '0600000000',
                'role' => 'admin',
                'is_active' => true,
                'password' => Hash::make('admin123456'),
                'photo' => null,
            ]
        );
    }
}
