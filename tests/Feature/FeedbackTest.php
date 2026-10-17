<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    private function categoryId(): int
    {
        return FoodCategory::where('name', 'Vegetarian')->value('id')
            ?? FoodCategory::create(['name' => 'Vegetarian', 'is_active' => true])->id;
    }

    private function deliveredClaim(): array
    {
        $categoryId = $this->categoryId();

        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-fb@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $recipient = User::create([
            'name' => 'NGO',
            'email' => 'ngo-fb@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'ngo_name' => 'Relief Org',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $listing = FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Soup pots',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 10,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'DELIVERED',
            'claimed_by' => $recipient->id,
            'claimed_at' => now()->subHour(),
            'delivered_at' => now(),
        ]);

        return compact('donor', 'recipient', 'listing');
    }

    public function test_recipient_can_submit_feedback_after_delivered(): void
    {
        $data = $this->deliveredClaim();

        $this->actingAs($data['recipient'])
            ->post('/food/'.$data['listing']->id.'/feedback', [
                'rating' => 4,
                'comment' => 'Clean and fresh.',
            ])
            ->assertRedirect('/food/claims');

        $this->assertDatabaseHas('feedback', [
            'food_listing_id' => $data['listing']->id,
            'recipient_id' => $data['recipient']->id,
            'rating' => 4,
            'comment' => 'Clean and fresh.',
        ]);
    }

    public function test_cannot_submit_feedback_twice(): void
    {
        $data = $this->deliveredClaim();

        Feedback::create([
            'food_listing_id' => $data['listing']->id,
            'recipient_id' => $data['recipient']->id,
            'rating' => 5,
        ]);

        $this->actingAs($data['recipient'])
            ->post('/food/'.$data['listing']->id.'/feedback', ['rating' => 3])
            ->assertSessionHas('error');

        $this->assertEquals(1, Feedback::where('food_listing_id', $data['listing']->id)->count());
    }

    public function test_cannot_rate_before_delivered(): void
    {
        $data = $this->deliveredClaim();
        $data['listing']->update(['status' => 'CLAIMED', 'delivered_at' => null]);

        $this->actingAs($data['recipient'])
            ->post('/food/'.$data['listing']->id.'/feedback', ['rating' => 5])
            ->assertNotFound();
    }

    public function test_donor_dashboard_shows_average_rating(): void
    {
        $data = $this->deliveredClaim();

        Feedback::create([
            'food_listing_id' => $data['listing']->id,
            'recipient_id' => $data['recipient']->id,
            'rating' => 4,
        ]);

        $this->actingAs($data['donor'])
            ->get('/donor/dashboard')
            ->assertOk()
            ->assertSee('4/5')
            ->assertSee('Average rating');
    }

    public function test_admin_dashboard_lists_recent_feedback(): void
    {
        $data = $this->deliveredClaim();

        Feedback::create([
            'food_listing_id' => $data['listing']->id,
            'recipient_id' => $data['recipient']->id,
            'rating' => 5,
            'comment' => 'Great hygiene',
        ]);

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin-fb@test.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Recent feedback')
            ->assertSee('Soup pots')
            ->assertSee('Great hygiene');
    }
}
