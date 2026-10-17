<?php

namespace Tests\Feature;

use App\Models\FoodCategory;
use App\Models\FoodListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin-cat@test.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_verified' => true,
            'is_active' => true,
        ]);
    }

    private function donor(): User
    {
        return User::create([
            'name' => 'Donor',
            'email' => 'donor-cat@test.test',
            'password' => Hash::make('password'),
            'role' => 'donor',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'is_verified' => true,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_add_category(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/categories', ['name' => 'Packaged'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('food_categories', [
            'name' => 'Packaged',
            'is_active' => true,
        ]);
    }

    public function test_new_category_appears_on_donor_post_form(): void
    {
        FoodCategory::create(['name' => 'Packaged', 'is_active' => true]);

        $this->actingAs($this->donor())
            ->get('/donor/donations/create')
            ->assertOk()
            ->assertSee('Packaged');
    }

    public function test_deactivated_category_hidden_from_donor_form(): void
    {
        $cat = FoodCategory::create(['name' => 'Old type', 'is_active' => false]);

        $this->actingAs($this->donor())
            ->get('/donor/donations/create')
            ->assertOk()
            ->assertDontSee('Old type');
    }

    public function test_admin_can_deactivate_category_with_listings(): void
    {
        $donor = $this->donor();
        $cat = FoodCategory::create(['name' => 'Used category', 'is_active' => true]);

        FoodListing::create([
            'donor_id' => $donor->id,
            'category_id' => $cat->id,
            'title' => 'Rice',
            'pickup_address' => 'Gulberg',
            'city' => 'Lahore',
            'latitude' => 31.51,
            'longitude' => 74.34,
            'quantity_value' => 5,
            'quantity_unit' => 'kg',
            'expiry_at' => now()->addDay(),
            'status' => 'AVAILABLE',
        ]);

        $this->actingAs($this->admin())
            ->post('/admin/categories/'.$cat->id, ['action' => 'deactivate'])
            ->assertSessionHas('success');

        $this->assertFalse($cat->fresh()->is_active);
        $this->assertDatabaseHas('food_categories', ['id' => $cat->id, 'name' => 'Used category']);
    }

    public function test_non_admin_cannot_manage_categories(): void
    {
        $this->actingAs($this->donor())
            ->get('/admin/categories')
            ->assertRedirect('/');
    }
}
