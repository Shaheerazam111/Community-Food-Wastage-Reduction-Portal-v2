<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use Illuminate\Database\Seeder;

class FoodCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Vegetarian', 'Non-vegetarian'] as $name) {
            FoodCategory::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
