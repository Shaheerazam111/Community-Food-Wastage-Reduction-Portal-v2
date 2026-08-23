<?php

namespace App\Support;

class ApiTestCatalog
{
    public static function groups(): array
    {
        return [
            self::authGroup(),
            self::foodGroup(),
            self::searchGroup(),
            self::claimGroup(),
        ];
    }

    private static function authGroup(): array
    {
        return [
            'id' => 'auth',
            'title' => '1. Auth',
            'task' => 'Task 1 — User Signup & Authentication',
            'description' => 'Register donors and recipients, log in with sessions, and switch personas before donor/recipient actions.',
            'color' => '#2563eb',
            'apis' => [
                [
                    'id' => 'register-donor',
                    'name' => 'Register Donor',
                    'method' => 'POST',
                    'path' => '/auth/register',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '201 — user object with role donor',
                    'description' => 'Creates a business donor profile (business name, phone, address, coordinates). Auto-logs in.',
                    'mapPicker' => true,
                    'fields' => [
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'default' => 'Pizza Palace', 'required' => true],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'donor1@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                        ['name' => 'role', 'label' => 'Role', 'type' => 'hidden', 'default' => 'donor'],
                        ['name' => 'business_name', 'label' => 'Business Name', 'type' => 'text', 'default' => 'Pizza Palace'],
                        ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'default' => '03001234567'],
                        ['name' => 'address', 'label' => 'Address', 'type' => 'text', 'default' => 'Main St, Karachi'],
                        ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'default' => '24.8607', 'step' => 'any'],
                        ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'default' => '67.0011', 'step' => 'any'],
                    ],
                ],
                [
                    'id' => 'register-recipient-a',
                    'name' => 'Register Recipient A',
                    'method' => 'POST',
                    'path' => '/auth/register',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '201 — user object with role recipient',
                    'description' => 'Creates NGO recipient profile near the donor (used for successful claim test).',
                    'mapPicker' => true,
                    'fields' => [
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'default' => 'Hope NGO', 'required' => true],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'ngoA@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                        ['name' => 'role', 'label' => 'Role', 'type' => 'hidden', 'default' => 'recipient'],
                        ['name' => 'ngo_name', 'label' => 'NGO Name', 'type' => 'text', 'default' => 'Hope NGO'],
                        ['name' => 'contact_person', 'label' => 'Contact Person', 'type' => 'text', 'default' => 'Ali Khan'],
                        ['name' => 'address', 'label' => 'Address', 'type' => 'text', 'default' => 'Near St, Karachi'],
                        ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'default' => '24.8615', 'step' => 'any'],
                        ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'default' => '67.0020', 'step' => 'any'],
                    ],
                ],
                [
                    'id' => 'register-recipient-b',
                    'name' => 'Register Recipient B',
                    'method' => 'POST',
                    'path' => '/auth/register',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '201 — second recipient for double-claim test',
                    'description' => 'Second NGO used to prove claim conflict (409) after Recipient A claims.',
                    'mapPicker' => true,
                    'fields' => [
                        ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'default' => 'Care NGO', 'required' => true],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'ngoB@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                        ['name' => 'role', 'label' => 'Role', 'type' => 'hidden', 'default' => 'recipient'],
                        ['name' => 'ngo_name', 'label' => 'NGO Name', 'type' => 'text', 'default' => 'Care NGO'],
                        ['name' => 'contact_person', 'label' => 'Contact Person', 'type' => 'text', 'default' => 'Sara Ahmed'],
                        ['name' => 'address', 'label' => 'Address', 'type' => 'text', 'default' => 'Far St, Karachi'],
                        ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'default' => '24.9000', 'step' => 'any'],
                        ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'default' => '67.0500', 'step' => 'any'],
                    ],
                ],
                [
                    'id' => 'login-donor',
                    'name' => 'Login as Donor',
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '200 — session cookie set for donor',
                    'description' => 'Run before any donor food listing request.',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'donor1@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                    ],
                ],
                [
                    'id' => 'login-recipient-a',
                    'name' => 'Login as Recipient A',
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '200 — session for first claim',
                    'description' => 'Run before Claim Listing (should succeed).',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'ngoA@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                    ],
                ],
                [
                    'id' => 'login-recipient-b',
                    'name' => 'Login as Recipient B',
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '200 — session for failed second claim',
                    'description' => 'Run before Claim Again (should 409).',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'ngoB@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'secret123', 'required' => true],
                    ],
                ],
                [
                    'id' => 'login-wrong',
                    'name' => 'Login Wrong Password',
                    'method' => 'POST',
                    'path' => '/auth/login',
                    'auth' => false,
                    'task' => 'Task 1',
                    'expects' => '401 — Invalid credentials',
                    'description' => 'Proves bad passwords are rejected.',
                    'fields' => [
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'default' => 'donor1@test.com', 'required' => true],
                        ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'default' => 'totally-wrong-password', 'required' => true],
                    ],
                ],
                [
                    'id' => 'logout',
                    'name' => 'Logout',
                    'method' => 'POST',
                    'path' => '/auth/logout',
                    'auth' => true,
                    'task' => 'Task 1',
                    'expects' => '200 — Logged out',
                    'description' => 'Ends the current session.',
                    'fields' => [],
                ],
            ],
        ];
    }

    private static function foodGroup(): array
    {
        return [
            'id' => 'food',
            'title' => '2. Food Listings (Donor)',
            'task' => 'Task 2 — Food Listing Lifecycle',
            'description' => 'Donors post surplus food. Status must move AVAILABLE → CLAIMED → DELIVERED in order.',
            'color' => '#059669',
            'apis' => [
                [
                    'id' => 'create-listing',
                    'name' => 'Create Listing',
                    'method' => 'POST',
                    'path' => '/food',
                    'auth' => true,
                    'role' => 'donor',
                    'task' => 'Task 2',
                    'expects' => '201 — listing with status AVAILABLE; saves listing_id',
                    'description' => 'Login as donor first. Pickup location is taken automatically from the donor profile (address + lat/lng from registration).',
                    'usesDonorLocation' => true,
                    'capture' => 'listing_id',
                    'fields' => [
                        ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Leftover Pizzas', 'required' => true],
                        ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => ['veg' => 'Veg', 'non_veg' => 'Non-Veg'], 'default' => 'veg', 'required' => true],
                        ['name' => 'quantity', 'label' => 'Quantity', 'type' => 'text', 'default' => '10 servings', 'required' => true],
                        ['name' => 'expiry_at', 'label' => 'Expiry At', 'type' => 'datetime-local', 'default' => '+1hour', 'required' => true],
                    ],
                ],
                [
                    'id' => 'create-listing-invalid',
                    'name' => 'Create Listing — Missing Fields',
                    'method' => 'POST',
                    'path' => '/food',
                    'auth' => true,
                    'role' => 'donor',
                    'task' => 'Task 2',
                    'expects' => '422 — validation errors',
                    'description' => 'Proves required fields are enforced.',
                    'fields' => [
                        ['name' => 'title', 'label' => 'Title only', 'type' => 'text', 'default' => 'Missing everything else', 'required' => true],
                    ],
                ],
                [
                    'id' => 'my-listings',
                    'name' => 'My Listings',
                    'method' => 'GET',
                    'path' => '/food/mine',
                    'auth' => true,
                    'role' => 'donor',
                    'task' => 'Task 2',
                    'expects' => '200 — listings array for logged-in donor',
                    'description' => 'Shows all listings created by the current donor.',
                    'fields' => [],
                ],
                [
                    'id' => 'deliver-early',
                    'name' => 'Deliver (before claim — should fail)',
                    'method' => 'POST',
                    'path' => '/food/{listing_id}/deliver',
                    'auth' => true,
                    'role' => 'donor',
                    'task' => 'Task 2',
                    'expects' => '422 — must be CLAIMED first',
                    'description' => 'Proves AVAILABLE → DELIVERED shortcut is blocked.',
                    'usesListingId' => true,
                    'fields' => [],
                ],
                [
                    'id' => 'deliver-after-claim',
                    'name' => 'Deliver (after claim — should succeed)',
                    'method' => 'POST',
                    'path' => '/food/{listing_id}/deliver',
                    'auth' => true,
                    'role' => 'donor',
                    'task' => 'Task 2',
                    'expects' => '200 — status DELIVERED',
                    'description' => 'Login as donor after Recipient A claimed the listing.',
                    'usesListingId' => true,
                    'fields' => [],
                ],
            ],
        ];
    }

    private static function searchGroup(): array
    {
        return [
            'id' => 'search',
            'title' => '3. Nearby Search (Public)',
            'task' => 'Task 3 — Nearby Food Search',
            'challenge' => 'Challenge 1 (Haversine distance) + Challenge 3 (expiry filter)',
            'description' => 'Public search by coordinates and radius. Returns only AVAILABLE, non-expired listings sorted closest first.',
            'color' => '#d97706',
            'apis' => [
                [
                    'id' => 'search-nearby',
                    'name' => 'Search Nearby — in range',
                    'method' => 'GET',
                    'path' => '/food/search',
                    'auth' => false,
                    'task' => 'Task 3',
                    'challenge' => 'Challenge 1 & 3',
                    'expects' => '200 — listings with distance_km, sorted nearest first',
                    'description' => 'No login. Point ~0.1km from listing should find it within 5km radius.',
                    'showMap' => true,
                    'fields' => [
                        ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'default' => '24.8615', 'step' => 'any', 'required' => true],
                        ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'default' => '67.0020', 'step' => 'any', 'required' => true],
                        ['name' => 'radius_km', 'label' => 'Radius (km)', 'type' => 'number', 'default' => '5', 'step' => 'any', 'required' => true],
                    ],
                ],
                [
                    'id' => 'search-far',
                    'name' => 'Search Nearby — out of range',
                    'method' => 'GET',
                    'path' => '/food/search',
                    'auth' => false,
                    'task' => 'Task 3',
                    'challenge' => 'Challenge 1',
                    'expects' => '200 — empty listings array',
                    'description' => 'Point ~100km away with 1km radius proves radius filter works.',
                    'showMap' => true,
                    'fields' => [
                        ['name' => 'latitude', 'label' => 'Latitude', 'type' => 'number', 'default' => '25.5000', 'step' => 'any', 'required' => true],
                        ['name' => 'longitude', 'label' => 'Longitude', 'type' => 'number', 'default' => '68.5000', 'step' => 'any', 'required' => true],
                        ['name' => 'radius_km', 'label' => 'Radius (km)', 'type' => 'number', 'default' => '1', 'step' => 'any', 'required' => true],
                    ],
                ],
            ],
        ];
    }

    private static function claimGroup(): array
    {
        return [
            'id' => 'claim',
            'title' => '4. Claiming (Recipient)',
            'task' => 'Task 4 — Safe Food Claiming',
            'challenge' => 'Challenge 2 — atomic claim prevents double-booking',
            'description' => 'Recipients claim available food. Second simultaneous claim gets 409 Conflict.',
            'color' => '#dc2626',
            'apis' => [
                [
                    'id' => 'claim-success',
                    'name' => 'Claim Listing (Recipient A — succeed)',
                    'method' => 'POST',
                    'path' => '/food/{listing_id}/claim',
                    'auth' => true,
                    'role' => 'recipient',
                    'task' => 'Task 4',
                    'challenge' => 'Challenge 2',
                    'expects' => '200 — Claim successful',
                    'description' => 'Login as Recipient A first. Listing becomes CLAIMED.',
                    'usesListingId' => true,
                    'fields' => [],
                ],
                [
                    'id' => 'claim-fail',
                    'name' => 'Claim Again (Recipient B — 409)',
                    'method' => 'POST',
                    'path' => '/food/{listing_id}/claim',
                    'auth' => true,
                    'role' => 'recipient',
                    'task' => 'Task 4',
                    'challenge' => 'Challenge 2',
                    'expects' => '409 — already claimed or expired',
                    'description' => 'Login as Recipient B after A claimed. Proves double-claim is blocked.',
                    'usesListingId' => true,
                    'fields' => [],
                ],
            ],
        ];
    }
}
