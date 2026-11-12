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
        $seedDemo = filter_var(env('SEED_DEMO_USERS', env('SEED_ADMIN', false)), FILTER_VALIDATE_BOOLEAN);
        if ($seedDemo) {
            $this->call(UserSeeder::class);
        }
    }
}
