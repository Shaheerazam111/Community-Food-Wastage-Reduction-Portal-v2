<?php

namespace Tests\Feature;

use App\Models\FoodListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodSearchTest extends TestCase
{
    use RefreshDatabase;

    // Donor posts food; nearby search finds it
    public function test_search_nearby_returns_posted_listing(): void
    {
        $latitude = 24.8607;
        $longitude = 67.0011;

        $this->postJson('/auth/register', [
            'name' => 'Test Donor',
            'email' => 'donor-search@test.com',
            'password' => 'secret12',
            'role' => 'donor',
            'business_name' => 'Corner Bakery',
            'phone' => '03001234567',
            'address' => '123 Main St, Karachi',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ])->assertCreated();

        $this->postJson('/food', [
            'title' => 'Surplus bread',
            'category' => 'veg',
            'quantity' => '10 kg',
            'expiry_at' => now()->addDay()->toDateTimeString(),
        ])->assertCreated();

        FoodListing::query()->update(['moderation_status' => 'APPROVED']);

        $query = http_build_query([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius_km' => 5,
        ]);

        $response = $this->getJson('/food/search?'.$query);

        $response->assertOk();
        $response->assertJsonCount(1, 'listings');
        $response->assertJsonFragment(['title' => 'Surplus bread']);
    }
}
