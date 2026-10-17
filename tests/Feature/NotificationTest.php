<?php

namespace Tests\Feature;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function categoryId(): int
    {
        return FoodCategory::where('name', 'Vegetarian')->value('id')
            ?? FoodCategory::create(['name' => 'Vegetarian', 'is_active' => true])->id;
    }

    private function listingPayload(int $categoryId, string $city = 'Lahore'): array
    {
        return [
            'title' => 'Fresh veg trays',
            'description' => 'Test listing',
            'category_id' => $categoryId,
            'quantity_value' => 10,
            'quantity_unit' => 'servings',
            'pickup_address' => 'Gulberg, '.$city,
            'city' => $city,
            'latitude' => 31.512,
            'longitude' => 74.345,
            'expiry_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ];
    }

    public function test_new_listing_notifies_verified_recipients_in_same_city_only(): void
    {
        $categoryId = $this->categoryId();

        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-notify@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $lahoreNgo = User::create([
            'name' => 'Lahore NGO',
            'email' => 'ngo-lhr@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'ngo_name' => 'Lahore Relief',
            'city' => 'Lahore',
            'latitude' => 31.48,
            'longitude' => 74.32,
            'is_verified' => true,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Pending Lahore',
            'email' => 'ngo-pending@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'city' => 'Lahore',
            'is_verified' => false,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Karachi NGO',
            'email' => 'ngo-khi@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'city' => 'Karachi',
            'latitude' => 24.81,
            'longitude' => 67.03,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $this->actingAs($donor)
            ->post('/food', $this->listingPayload($categoryId, 'Lahore'))
            ->assertRedirect('/food/mine');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $lahoreNgo->id,
            'is_read' => false,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => User::where('email', 'ngo-khi@test.test')->value('id'),
        ]);

        $this->assertDatabaseMissing('notifications', [
            'user_id' => User::where('email', 'ngo-pending@test.test')->value('id'),
        ]);
    }

    public function test_claim_notifies_donor(): void
    {
        $categoryId = $this->categoryId();

        $donor = User::create([
            'name' => 'Donor',
            'email' => 'donor-claim@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'city' => 'Lahore',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $recipient = User::create([
            'name' => 'Sara',
            'email' => 'ngo-claim@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'ngo_name' => 'City Food Relief',
            'city' => 'Lahore',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $listing = FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => 'Rice pots',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.512,
            'longitude' => 74.345,
            'quantity_value' => 5,
            'quantity_unit' => 'kg',
            'expiry_at' => now()->addDay(),
            'status' => 'AVAILABLE',
        ]);

        $this->actingAs($recipient)
            ->post('/food/'.$listing->id.'/claim')
            ->assertRedirect('/food/claims');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $donor->id,
            'food_listing_id' => $listing->id,
            'message' => 'City Food Relief claimed Rice pots.',
            'is_read' => false,
        ]);
    }

    public function test_recipient_deliver_notifies_donor(): void
    {
        $categoryId = $this->categoryId();
        $users = $this->claimedListingFixtures($categoryId, 'Bread loaves');
        $listing = $users['listing'];

        $this->actingAs($users['recipient'])
            ->post('/food/'.$listing->id.'/deliver')
            ->assertRedirect('/food/claims');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $users['donor']->id,
            'food_listing_id' => $listing->id,
            'message' => 'Pickup confirmed: Bread loaves was marked delivered by the recipient.',
        ]);
    }

    public function test_donor_deliver_notifies_recipient(): void
    {
        $categoryId = $this->categoryId();
        $users = $this->claimedListingFixtures($categoryId, 'Fruit boxes');
        $listing = $users['listing'];

        $this->actingAs($users['donor'])
            ->from('/food/mine')
            ->post('/food/'.$listing->id.'/deliver')
            ->assertRedirect('/food/mine');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $users['recipient']->id,
            'food_listing_id' => $listing->id,
            'message' => 'Pickup confirmed: Fruit boxes was marked delivered.',
        ]);
    }

    /** Donor + recipient + one CLAIMED listing. */
    private function claimedListingFixtures(int $categoryId, string $title): array
    {
        $donor = User::create([
            'name' => 'Donor',
            'email' => strtolower(str_replace(' ', '-', $title)).'-donor@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $recipient = User::create([
            'name' => 'NGO',
            'email' => strtolower(str_replace(' ', '-', $title)).'-ngo@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'city' => 'Lahore',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $listing = FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $categoryId,
            'title' => $title,
            'pickup_address' => 'Model Town',
            'city' => 'Lahore',
            'latitude' => 31.48,
            'longitude' => 74.32,
            'quantity_value' => 20,
            'quantity_unit' => 'servings',
            'expiry_at' => now()->addDay(),
            'status' => 'CLAIMED',
            'claimed_by' => $recipient->id,
            'claimed_at' => now(),
        ]);

        return compact('donor', 'recipient', 'listing');
    }

    public function test_mark_read_and_mark_all_read(): void
    {
        $user = User::create([
            'name' => 'NGO',
            'email' => 'ngo-read@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $n1 = PortalNotification::create([
            'user_id' => $user->id,
            'message' => 'Alert one',
            'is_read' => false,
        ]);

        PortalNotification::create([
            'user_id' => $user->id,
            'message' => 'Alert two',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->post('/notifications/'.$n1->id.'/read')
            ->assertSessionHas('success');

        $this->assertTrue($n1->fresh()->is_read);

        $this->actingAs($user)
            ->post('/notifications/read-all')
            ->assertSessionHas('success');

        $this->assertEquals(0, PortalNotification::where('user_id', $user->id)->where('is_read', false)->count());
    }

    public function test_notifications_page_shows_unread_badge_in_nav(): void
    {
        $user = User::create([
            'name' => 'Donor',
            'email' => 'donor-nav@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'is_verified' => true,
            'is_active' => true,
        ]);

        PortalNotification::create([
            'user_id' => $user->id,
            'message' => 'Someone claimed your food.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->get('/notifications')
            ->assertOk()
            ->assertSee('Someone claimed your food.')
            ->assertSee('nav-notifications-badge', false);
    }

    public function test_user_cannot_mark_another_users_notification(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $other = User::create([
            'name' => 'Other',
            'email' => 'other@test.test',
            'password' => Hash::make('password'),
            'role' => 'recipient',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $note = PortalNotification::create([
            'user_id' => $owner->id,
            'message' => 'Private alert',
            'is_read' => false,
        ]);

        $this->actingAs($other)
            ->post('/notifications/'.$note->id.'/read')
            ->assertNotFound();
    }
}
