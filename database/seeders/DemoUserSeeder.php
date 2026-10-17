<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Demo logins (password for all: password).
     */
    public function run(): void
    {
        $password = Hash::make('password');

        User::updateOrCreate(
            ['email' => 'admin@food.test'],
            [
                'name' => 'Portal Admin',
                'password' => $password,
                'role' => 'admin',
                'phone' => '03000000001',
                'address' => 'Admin office, Mall Road',
                'city' => 'Lahore',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'donor@food.test'],
            [
                'name' => 'Demo Donor',
                'password' => $password,
                'role' => 'donor',
                'business_name' => 'Green Kitchen Restaurant',
                'phone' => '03000000002',
                'address' => '12 Food Street, Gulberg',
                'city' => 'Lahore',
                'latitude' => 31.5120,
                'longitude' => 74.3450,
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ngo@food.test'],
            [
                'name' => 'Demo NGO',
                'password' => $password,
                'role' => 'recipient',
                'ngo_name' => 'City Food Relief',
                'contact_person' => 'Sara Khan',
                'phone' => '03000000003',
                'address' => '45 Hope Avenue, Model Town',
                'city' => 'Lahore',
                'latitude' => 31.4850,
                'longitude' => 74.3200,
                'is_verified' => true,
                'is_active' => true,
            ]
        );
    }
}
