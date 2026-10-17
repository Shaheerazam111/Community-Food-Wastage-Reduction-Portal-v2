<?php

namespace Database\Seeders;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Sample listings for testing Steps 1–4 (search, city filter, dashboard, claim).
 * Safe to run again: matches on donor + title.
 */
class DemoFoodSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        $donorLhr = User::where('email', 'donor@food.test')->first();
        $ngo = User::where('email', 'ngo@food.test')->first();

        if (! $donorLhr || ! $ngo) {
            $this->command?->warn('Run DemoUserSeeder first (php artisan db:seed).');

            return;
        }

        $donorKhi = User::updateOrCreate(
            ['email' => 'donor.khi@food.test'],
            [
                'name' => 'Karachi Kitchen',
                'password' => $password,
                'role' => 'donor',
                'business_name' => 'Harbor Kitchen Karachi',
                'phone' => '03000000004',
                'address' => 'Clifton Block 5',
                'city' => 'Karachi',
                'latitude' => 24.8138,
                'longitude' => 67.0299,
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ngo.pending@food.test'],
            [
                'name' => 'Pending NGO',
                'password' => $password,
                'role' => 'recipient',
                'ngo_name' => 'Waiting Relief',
                'contact_person' => 'Ali Raza',
                'phone' => '03000000005',
                'address' => 'Johar Town',
                'city' => 'Lahore',
                'latitude' => 31.4697,
                'longitude' => 74.2728,
                'is_verified' => false,
                'is_active' => true,
            ]
        );

        $veg = FoodCategory::where('name', 'Vegetarian')->first();
        $nonVeg = FoodCategory::where('name', 'Non-vegetarian')->first();

        if (! $veg || ! $nonVeg) {
            $this->command?->warn('Run FoodCategorySeeder first.');

            return;
        }

        $this->seedListing($donorLhr, $veg->id, [
            'title' => '[Demo] 20 veg lunch boxes',
            'description' => 'Fresh vegetarian lunch packs from today service.',
            'quantity_value' => 20,
            'quantity_unit' => 'servings',
            'pickup_address' => '12 Food Street, Gulberg, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.5120,
            'longitude' => 74.3450,
            'expiry_at' => now()->addHours(10),
            'status' => 'AVAILABLE',
        ]);

        $this->seedListing($donorLhr, $veg->id, [
            'title' => '[Demo] 15 kg cooked rice',
            'description' => 'Plain rice, kept warm. Bring containers.',
            'quantity_value' => 15,
            'quantity_unit' => 'kg',
            'pickup_address' => '12 Food Street, Gulberg, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.5095,
            'longitude' => 74.3410,
            'expiry_at' => now()->addHours(6),
            'status' => 'AVAILABLE',
        ]);

        $this->seedListing($donorLhr, $nonVeg->id, [
            'title' => '[Demo] 40 chicken biryani servings',
            'description' => 'Non-veg biryani trays. Pickup before expiry.',
            'quantity_value' => 40,
            'quantity_unit' => 'servings',
            'pickup_address' => 'Gulberg III, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.5155,
            'longitude' => 74.3520,
            'expiry_at' => now()->addHours(8),
            'status' => 'AVAILABLE',
        ]);

        $this->seedListing($donorLhr, $veg->id, [
            'title' => '[Demo] Cancelled listing (hidden)',
            'description' => 'Should not appear in NGO search.',
            'quantity_value' => 5,
            'quantity_unit' => 'servings',
            'pickup_address' => 'Gulberg, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.5120,
            'longitude' => 74.3450,
            'expiry_at' => now()->addDay(),
            'status' => 'CANCELLED',
        ]);

        $this->seedListing($donorLhr, $veg->id, [
            'title' => '[Demo] Already claimed (hidden)',
            'description' => 'Claimed by Demo NGO — for claims history.',
            'quantity_value' => 25,
            'quantity_unit' => 'servings',
            'pickup_address' => 'Model Town, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.4810,
            'longitude' => 74.3250,
            'expiry_at' => now()->addHours(12),
            'status' => 'CLAIMED',
            'claimed_by' => $ngo->id,
            'claimed_at' => now()->subHours(2),
        ]);

        $this->seedListing($donorLhr, $veg->id, [
            'title' => '[Demo] Delivered last week',
            'description' => 'Finished pickup — donor stats only.',
            'quantity_value' => 10,
            'quantity_unit' => 'kg',
            'pickup_address' => 'Gulberg, Lahore',
            'city' => 'Lahore',
            'latitude' => 31.5120,
            'longitude' => 74.3450,
            'expiry_at' => now()->subDay(),
            'status' => 'DELIVERED',
            'claimed_by' => $ngo->id,
            'claimed_at' => now()->subDays(3),
            'delivered_at' => now()->subDays(3)->addHours(2),
        ]);

        $this->seedListing($donorKhi, $nonVeg->id, [
            'title' => '[Demo] Karachi fish curry 12 kg',
            'description' => 'Karachi-only listing for city filter test.',
            'quantity_value' => 12,
            'quantity_unit' => 'kg',
            'pickup_address' => 'Clifton, Karachi',
            'city' => 'Karachi',
            'latitude' => 24.8138,
            'longitude' => 67.0299,
            'expiry_at' => now()->addHours(9),
            'status' => 'AVAILABLE',
        ]);

        $this->seedListing($donorKhi, $veg->id, [
            'title' => '[Demo] Karachi veg daal 30 servings',
            'description' => 'Another Karachi listing.',
            'quantity_value' => 30,
            'quantity_unit' => 'servings',
            'pickup_address' => 'DHA Phase 6, Karachi',
            'city' => 'Karachi',
            'latitude' => 24.8050,
            'longitude' => 67.0750,
            'expiry_at' => now()->addHours(7),
            'status' => 'AVAILABLE',
        ]);
    }

    private function seedListing(User $donor, int $categoryId, array $data): void
    {
        FoodListing::updateOrCreate(
            [
                'donor_id' => $donor->id,
                'title' => $data['title'],
            ],
            array_merge($data, [
                'donor_id' => $donor->id,
                'category_id' => $categoryId,
            ])
        );
    }
}
