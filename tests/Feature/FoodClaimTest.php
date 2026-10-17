<?php

namespace Tests\Feature;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FoodClaimTest extends TestCase
{
    use RefreshDatabase;

    /** Build donor, recipient, and one available listing. */
    private function seedClaimFixtures(): array
    {
        $categoryId = FoodCategory::where('name', 'Vegetarian')->value('id')
            ?? FoodCategory::create(['name' => 'Vegetarian', 'is_active' => true])->id;

        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $recipientA = User::create([
            'name' => 'NGO A',
            'email' => 'ngo-a@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'city' => 'Lahore',
            'latitude' => 31.48,
            'longitude' => 74.32,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $recipientB = User::create([
            'name' => 'NGO B',
            'email' => 'ngo-b@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'city' => 'Lahore',
            'latitude' => 31.48,
            'longitude' => 74.32,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $listing = FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Test rice',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.512,
            'longitude' => 74.345,
            'quantity_value' => 10,
            'quantity_unit' => 'kg',
            'expiry_at' => now()->addDay(),
            'status' => 'AVAILABLE',
        ]);

        return compact('donor', 'recipientA', 'recipientB', 'listing');
    }

    public function test_second_claim_returns_conflict(): void
    {
        $fixtures = $this->seedClaimFixtures();

        $this->actingAs($fixtures['recipientA'])
            ->post('/food/'.$fixtures['listing']->id.'/claim')
            ->assertRedirect('/food/claims');

        $this->actingAs($fixtures['recipientB'])
            ->post('/food/'.$fixtures['listing']->id.'/claim')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('food_listings', [
            'id' => $fixtures['listing']->id,
            'status' => 'CLAIMED',
            'claimed_by' => $fixtures['recipientA']->id,
        ]);
    }

    public function test_recipient_who_claimed_can_mark_delivered(): void
    {
        $fixtures = $this->seedClaimFixtures();

        $this->actingAs($fixtures['recipientA'])
            ->post('/food/'.$fixtures['listing']->id.'/claim');

        $this->actingAs($fixtures['recipientA'])
            ->post('/food/'.$fixtures['listing']->id.'/deliver')
            ->assertRedirect('/food/claims');

        $this->assertDatabaseHas('food_listings', [
            'id' => $fixtures['listing']->id,
            'status' => 'DELIVERED',
            'claimed_by' => $fixtures['recipientA']->id,
        ]);
    }

    public function test_claimed_listing_not_in_nearby_search(): void
    {
        $fixtures = $this->seedClaimFixtures();

        $this->actingAs($fixtures['recipientA'])
            ->post('/food/'.$fixtures['listing']->id.'/claim');

        $results = FoodListing::searchAvailableNearby(31.48, 74.32, 10);

        $this->assertTrue($results->where('id', $fixtures['listing']->id)->isEmpty());
    }
}
