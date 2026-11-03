<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (filter_var(env('SEED_ADMIN', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->call(AdminUserSeeder::class);
        }
    }
}
