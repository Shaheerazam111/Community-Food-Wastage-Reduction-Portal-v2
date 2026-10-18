<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_donor_hitting_home_goes_to_donor_dashboard(): void
    {
        $donor = User::factory()->create(['role' => 'donor', 'is_verified' => true]);

        $response = $this->actingAs($donor)->get('/');

        $response->assertRedirect('/donor/dashboard');
    }

    public function test_logged_in_recipient_hitting_login_goes_to_recipient_dashboard(): void
    {
        $recipient = User::factory()->create(['role' => 'recipient', 'is_verified' => true]);

        $response = $this->actingAs($recipient)->get('/login');

        $response->assertRedirect('/recipient/dashboard');
    }

    public function test_logged_in_admin_hitting_register_goes_to_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_verified' => true]);

        $response = $this->actingAs($admin)->get('/register');

        $response->assertRedirect('/admin/dashboard');
    }
}
