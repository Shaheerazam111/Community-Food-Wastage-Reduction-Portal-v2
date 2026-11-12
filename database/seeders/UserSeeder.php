<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    // Demo logins for local / viva (not for production as-is)
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@foodportal.test'],
            [
                'name' => 'Portal Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'verification_status' => 'approved',
            ]
        );

        User::updateOrCreate(
            ['email' => 'donor@foodportal.test'],
            [
                'name' => 'Demo Donor',
                'password' => Hash::make('donor123'),
                'role' => 'donor',
                'is_active' => true,
                'verification_status' => 'approved',
                'business_name' => 'Green Kitchen Cafe',
                'phone' => '03001234567',
                'address' => 'MM Alam Road, Lahore',
                'latitude' => 31.5497,
                'longitude' => 74.3436,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ngo@foodportal.test'],
            [
                'name' => 'City Food Relief',
                'password' => Hash::make('ngo123'),
                'role' => 'recipient',
                'is_active' => true,
                'verification_status' => 'approved',
                'ngo_name' => 'City Food Relief NGO',
                'contact_person' => 'Ayesha Khan',
                'address' => 'Model Town, Lahore',
                'latitude' => 31.5100,
                'longitude' => 74.3400,
            ]
        );

        User::updateOrCreate(
            ['email' => 'volunteer@foodportal.test'],
            [
                'name' => 'Pickup Volunteer',
                'password' => Hash::make('volunteer123'),
                'role' => 'volunteer',
                'is_active' => true,
                'verification_status' => 'approved',
                'address' => 'City pickup hub',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
            ]
        );
    }
}
