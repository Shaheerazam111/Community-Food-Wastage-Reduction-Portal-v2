<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    // Default admin login for reports (local demo only)
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@foodportal.test'],
            [
                'name' => 'Portal Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
    }
}
