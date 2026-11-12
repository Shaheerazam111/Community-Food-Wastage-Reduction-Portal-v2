<?php

namespace Tests\Feature;

use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FoodClaimTest extends TestCase
{
    use RefreshDatabase;

    // Only one recipient can claim a listing
    public function test_second_claim_on_same_listing_returns_conflict(): void
    {
        $donor = User::create([
            'name' => 'Donor One',
            'email' => 'donor-claim@test.com',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'business_name' => 'City Cafe',
            'phone' => '03001111111',
            'address' => 'Karachi',
            'latitude' => 24.8607,
            'longitude' => 67.0011,
        ]);

        $listing = FoodListing::create([
            'donor_id' => $donor->id,
            'title' => 'Cooked rice',
            'category' => 'veg',
            'quantity' => '5 kg',
            'pickup_address' => $donor->address,
            'latitude' => $donor->latitude,
            'longitude' => $donor->longitude,
            'expiry_at' => now()->addDays(2),
            'status' => 'AVAILABLE',
            'moderation_status' => 'APPROVED',
        ]);

        $firstRecipient = User::create([
            'name' => 'NGO Alpha',
            'email' => 'ngo-alpha@test.com',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'ngo_name' => 'Alpha Trust',
            'contact_person' => 'Ali Khan',
            'address' => 'Karachi',
            'latitude' => 24.8610,
            'longitude' => 67.0015,
            'verification_status' => 'approved',
        ]);

        $secondRecipient = User::create([
            'name' => 'NGO Beta',
            'email' => 'ngo-beta@test.com',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'ngo_name' => 'Beta Foundation',
            'contact_person' => 'Sara Ahmed',
            'address' => 'Karachi',
            'latitude' => 24.8612,
            'longitude' => 67.0020,
            'verification_status' => 'approved',
        ]);

        $firstResponse = $this->actingAs($firstRecipient)->postJson('/food/'.$listing->id.'/claim');

        $firstResponse->assertOk();
        $firstResponse->assertJson(['message' => 'Claim successful']);

        $secondResponse = $this->actingAs($secondRecipient)->postJson('/food/'.$listing->id.'/claim');

        $secondResponse->assertStatus(409);
        $secondResponse->assertJson(['message' => 'This item was already claimed or has expired']);
    }
}
