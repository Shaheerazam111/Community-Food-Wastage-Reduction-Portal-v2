# Demo test data

Use this after migrations to test the portal in the browser (search, claims, **notifications**, etc.).

**Important:** Migrations only create **tables**. They do **not** insert demo users. Use **seeders** (below).

**Do not** run `php artisan test` against your **dev** MySQL database. Feature tests use `RefreshDatabase`, which **drops all tables** and migrates again — that removes demo users and listings. Use a separate DB via `.env.testing` (see `.env.testing.example` in the project root).

## Load data

```bash
php artisan db:seed
```

Or refresh everything (drops all tables):

```bash
php artisan migrate:fresh --seed
```

## Accounts (password for all: `password`)

| Email | Role | Notes |
| ----- | ---- | ----- |
| `admin@food.test` | Admin | Users / verification |
| `donor@food.test` | Donor (Lahore) | Post & edit food |
| `donor.khi@food.test` | Donor (Karachi) | City filter test |
| `ngo@food.test` | Recipient (Lahore) | Search, dashboard, claim |
| `ngo.pending@food.test` | Recipient | **Not verified** — banner, cannot claim |

## Sample food listings (titles start with `[Demo]`)

**Lahore — should appear for `ngo@food.test`**

- Nearby dashboard (10 km) and GPS search: 3 **AVAILABLE** items near Gulberg / Model Town.
- **Search by city → Lahore**: same available Lahore items (sorted by distance if profile pin is set).
- Hidden from search: **Cancelled**, **Already claimed**, **Delivered**.

**Karachi — city filter only**

- 2 **AVAILABLE** items — pick **Karachi** on Find food; they should **not** show on Lahore NGO nearby search.

## Notifications flow (Step 6)

1. Ensure `php artisan migrate` has run (includes `notifications` table).
2. `php artisan db:seed` if accounts are missing.
3. Login **donor@food.test** → post new food in **Lahore**.
4. Login **ngo@food.test** → **Alerts** in navbar → “New food in Lahore: …”.
5. Claim a listing → donor gets “City Food Relief claimed …”.
6. **Mark as delivered** → the other account gets a pickup confirmation alert.

`ngo.pending@food.test` (same city, not verified) does **not** get new-listing alerts.

## Quick test flow (Step 4)

1. Login **ngo@food.test** → dashboard shows nearby `[Demo]` listings → **View** one.
2. **Find food** → **Use GPS** or **profile pin** → map pins + table.
3. **Search by city** → **Lahore** vs **Karachi**.
4. Open listing → **Claim this food** (use a listing still **AVAILABLE**).
5. Login **ngo.pending@food.test** → claim button disabled / blocked.

## Donor side

Login **donor@food.test** → **My listings** → edit/cancel an **AVAILABLE** `[Demo]` item (not the claimed one).
