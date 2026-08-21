# Community Food Wastage Reduction Portal — v2 (Core Engine)

A separate, minimal Laravel app that implements only the **functional engine** from the
prototype assignment: signup, posting food, nearby search, and safe claiming.
No admin screens, no reports, no UI — this is an API tested with Postman/curl.
The original UI project (`Community-Food-Wastage-Reduction-Portal/`) is untouched.

## Setup

1. Create a MySQL database named `cfwrp_v2` (or edit `DB_DATABASE` in `.env`).
2. Run migrations:
   ```
   php artisan migrate
   ```
3. Start the server:
   ```
   php artisan serve
   ```
4. (Optional) Run the hourly expiry cleanup once manually:
   ```
   php artisan food:expire
   ```
   In real deployment, `php artisan schedule:work` (or a system cron calling
   `schedule:run` every minute) keeps this running hourly — already registered
   in `app/Console/Kernel.php`.

Auth is session/cookie based (Laravel's default), so requests must reuse the
same cookie jar across calls (Postman does this automatically per collection).
CSRF checks are skipped for `/auth/*` and `/food*` since this is a JSON API,
not a browser form (see `app/Http/Middleware/VerifyCsrfToken.php`).

## Endpoints

| Method | URL | Auth | Purpose |
|---|---|---|---|
| POST | `/auth/register` | - | Create donor or recipient account (`role` field) |
| POST | `/auth/login` | - | Log in, starts session |
| POST | `/auth/logout` | yes | End session |
| POST | `/food` | yes (donor) | Create a food listing |
| GET | `/food/mine` | yes (donor) | List my own listings |
| GET | `/food/search?latitude=&longitude=&radius_km=` | - | Nearby AVAILABLE, non-expired listings, closest first |
| POST | `/food/{id}/claim` | yes (recipient) | Atomically claim a listing |
| POST | `/food/{id}/deliver` | yes (donor) | Mark a CLAIMED listing DELIVERED |

## How the 3 challenges were solved

- **Distance search** (`FoodListingController::search`): raw SQL Haversine formula
  in a `selectRaw`, filtered with `havingRaw('distance_km <= ?')`, ordered by
  `distance_km`. See [`app/Http/Controllers/FoodListingController.php`](app/Http/Controllers/FoodListingController.php).
- **Double-claim race condition** (`ClaimController::claim`): a single atomic
  `UPDATE ... WHERE status = 'AVAILABLE'`. If 0 rows are affected, someone else
  already claimed it. See [`app/Http/Controllers/ClaimController.php`](app/Http/Controllers/ClaimController.php).
- **Expired food**: `search()` always filters `expiry_at > now()` (lazy filtering),
  and `php artisan food:expire` (scheduled hourly) sweeps stale AVAILABLE rows to
  EXPIRED. See [`app/Console/Commands/ExpireFoodListings.php`](app/Console/Commands/ExpireFoodListings.php).

Both were manually verified: two concurrent `claim` requests on the same listing
result in exactly one `"Claim successful"` and one `409 already claimed` — never both.
