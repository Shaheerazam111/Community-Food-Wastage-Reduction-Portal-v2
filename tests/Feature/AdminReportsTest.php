<?php

namespace Tests\Feature;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\User;
use App\Support\ListingImpact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminReportsTest extends TestCase
{
    use RefreshDatabase;

    private function categoryId(): int
    {
        return FoodCategory::where('name', 'Vegetarian')->value('id')
            ?? FoodCategory::create(['name' => 'Vegetarian', 'is_active' => true])->id;
    }

    private function adminUser(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-reports@test.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);
    }

    public function test_donor_dashboard_shows_meals_and_kg_saved(): void
    {
        $categoryId = $this->categoryId();
        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-impact@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Rice',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 25,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'DELIVERED',
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Daal',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 12.5,
            'quantity_unit' => 'kg',
            'expiry_at' => now()->addDay(),
            'status' => 'DELIVERED',
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Still open',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 100,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'AVAILABLE',
        ]);

        $impact = ListingImpact::totals($donor->id);
        $this->assertEquals(25, $impact['meals_saved']);
        $this->assertEquals(12.5, $impact['kg_saved']);

        $this->actingAs($donor)
            ->get('/donor/dashboard')
            ->assertOk()
            ->assertSee('Meals saved')
            ->assertSee('25')
            ->assertSee('12.5');
    }

    public function test_admin_reports_page_shows_totals_and_city_table(): void
    {
        $categoryId = $this->categoryId();
        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-city@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Karachi fish',
            'pickup_address' => 'Clifton',
            'city' => 'Karachi',
            'latitude' => 24.81,
            'longitude' => 67.03,
            'quantity_value' => 10,
            'quantity_unit' => 'kg',
            'expiry_at' => now()->addDay(),
            'status' => 'DELIVERED',
        ]);

        $this->actingAs($this->adminUser())
            ->get('/admin/reports')
            ->assertOk()
            ->assertSee('Meals saved')
            ->assertSee('Karachi')
            ->assertSee('10');
    }

    public function test_admin_listings_filter_by_status(): void
    {
        $categoryId = $this->categoryId();
        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-filter@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Open trays',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 5,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'AVAILABLE',
        ]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Done trays',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 5,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'DELIVERED',
        ]);

        $this->actingAs($this->adminUser())
            ->get('/admin/listings?status=AVAILABLE')
            ->assertOk()
            ->assertSee('Open trays')
            ->assertDontSee('Done trays');
    }

    public function test_admin_dashboard_shows_listing_status_counts(): void
    {
        $this->actingAs($this->adminUser())
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Available')
            ->assertSee('Claimed')
            ->assertSee('Delivered');
    }
}
