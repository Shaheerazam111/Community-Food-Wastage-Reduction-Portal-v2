# Flow Test Guide — v2 Core Engine

How to test every requirement from the assignment using the Postman collection
(`postman_collection.json`) in this folder. Import it into Postman, then run
requests **in the order listed** — later steps depend on earlier ones (e.g. you
must create a listing before you can claim it).

Before you start:
1. `php artisan serve` is running.
2. `{{base_url}}` in the collection matches it (default `http://127.0.0.1:8000`).
3. The database is empty or fresh (`php artisan migrate:fresh`) so IDs/emails don't clash.

Each step below names the exact Postman request to run and what "pass" looks like.
Every request already has a **Tests** tab assertion in Postman (green checkmark = pass).

---

## Task 1 — User Signup & Authentication

| Step | Request | Expected result |
|---|---|---|
| 1.1 | `1. Auth > Register Donor` | `201 Created`, response has a `user` object with `role: "donor"` |
| 1.2 | `1. Auth > Register Recipient A` | `201 Created`, `role: "recipient"` |
| 1.3 | `1. Auth > Register Recipient B` | `201 Created`, `role: "recipient"` |
| 1.4 | `1. Auth > Login with Wrong Password (should fail)` | `401 Unauthorized` — proves bad credentials are rejected |
| 1.5 | `1. Auth > Login as Donor` | `200 OK`, session cookie set (Postman stores it automatically) |

**What this proves:** two distinct profile types exist (donor fields vs. recipient
fields), passwords are checked, and login starts a session.

---

## Task 2 — Food Listing Lifecycle (Donor Side)

Run `1. Auth > Login as Donor` first so the session belongs to the donor.

| Step | Request | Expected result |
|---|---|---|
| 2.1 | `2. Food Listings (Donor) > Create Listing - Missing Fields (should fail)` | `422 Unprocessable` — required fields are enforced |
| 2.2 | `2. Food Listings (Donor) > Create Listing` | `201 Created`, status is `AVAILABLE`. Postman auto-saves the new id into `{{listing_id}}` |
| 2.3 | `2. Food Listings (Donor) > My Listings` | `200 OK`, your new listing appears |
| 2.4 | `2. Food Listings (Donor) > Deliver Listing (before it is claimed - should fail)` | `422 Unprocessable` — you cannot skip straight from `AVAILABLE` to `DELIVERED` |

**What this proves:** the status can only move forward in order
(`AVAILABLE → CLAIMED → DELIVERED`), never skipped.

Step 2.5 (marking it actually `DELIVERED`) happens later, in Task 4, after
someone has claimed it — see step 4.3 below.

---

## Task 3 — Nearby Food Search (Recipient Side) / Challenge 1 (Distance)

No login required — search is public.

| Step | Request | Expected result |
|---|---|---|
| 3.1 | `3. Nearby Search (Public) > Search Nearby - in range (should list the item)` | `200 OK`, listing appears with a `distance_km` field (a small number, e.g. `0.12`) |
| 3.2 | `3. Nearby Search (Public) > Search Nearby - out of range (should be empty)` | `200 OK`, `listings: []` — a point ~100km away with a 1km radius finds nothing |

**What this proves:** Challenge 1 (Haversine distance calculation in SQL,
radius filtering, closest-first sorting) works. If you create a second listing
farther away and re-run 3.1 with a wider radius, you'll see both listings and
the nearer one listed first.

---

## Task 4 — Safe Food Claiming Workflow / Challenge 2 (Race Condition)

| Step | Request | Expected result |
|---|---|---|
| 4.1 | `1. Auth > Login as Recipient A` | `200 OK` |
| 4.2 | `4. Claiming (Recipient) > Claim Listing (Recipient A - should succeed)` | `200 OK`, `"message": "Claim successful"`. Listing status is now `CLAIMED` |
| 4.3 | `1. Auth > Login as Recipient B`, then `4. Claiming (Recipient) > Claim Listing Again (Recipient B - should FAIL with 409)` | `409 Conflict`, `"message": "This item was already claimed or has expired"` |
| 4.4 | Re-run `3. Nearby Search (Public) > Search Nearby - in range` | The claimed listing no longer appears (search only shows `AVAILABLE` items) |
| 4.5 | `1. Auth > Login as Donor`, then `2. Food Listings (Donor) > Deliver Listing (after claim - should succeed)` | `200 OK`, status is now `DELIVERED` |

**What this proves:** only one recipient can ever claim a given listing, and the
lifecycle finishes at `DELIVERED`.

### About true "same-millisecond" concurrency

Steps 4.2 and 4.3 above are run one after another, which proves the *rule*
works (second claim on an already-claimed item is always rejected). Postman
alone can't fire two requests at the exact same instant to prove the *race
condition* itself is closed, not just the end state.

That was verified separately with two real simultaneous HTTP requests fired at
once from the command line:

```bash
curl -s -b recipientA.cookies -X POST http://127.0.0.1:8000/food/1/claim &
curl -s -b recipientB.cookies -X POST http://127.0.0.1:8000/food/1/claim &
wait
```

Result: exactly one request returned `"Claim successful"` and the other
returned `409`, and the database showed only one `claimed_by` value — never
both. This is because the claim is a single atomic SQL `UPDATE ... WHERE
status = 'AVAILABLE'` (see `ClaimController::claim` in the non-developer guide),
so the database itself — not the PHP code — is what prevents the double-claim,
which is what makes it safe even under real simultaneous traffic.

---

## Challenge 3 — Handling Expired Food Cleanly

This isn't in the Postman collection because it depends on time passing, but
you can prove both halves of it manually:

**3a. Lazy filtering (always live, no waiting required)**

1. Create a listing with `expiry_at` a few minutes in the *past* (edit the
   `Create Listing` request body just for this test, e.g.
   `"expiry_at": "2020-01-01 00:00:00"` — validation normally requires a future
   date, so you'll need to temporarily create it directly via
   `php artisan tinker`, matching how it was verified during development:
   ```
   php artisan tinker --execute="App\Models\FoodListing::create(['donor_id'=>1,'title'=>'Stale Rice','category'=>'veg','quantity'=>'1 kg','pickup_address'=>'Main St','latitude'=>24.8607,'longitude'=>67.0011,'expiry_at'=>now()->subMinutes(5),'status'=>'AVAILABLE']);"
   ```
2. Run `3. Nearby Search (Public) > Search Nearby - in range`.
3. **Expected:** "Stale Rice" does NOT appear, even though its status in the
   database is still `AVAILABLE` — the search query itself always excludes
   anything past its expiry time.

**3b. Scheduled cleanup**

1. Run `php artisan food:expire`.
2. Expected console output: `Marked 1 listing(s) as EXPIRED.`
3. Check the database — "Stale Rice"'s status is now `EXPIRED` instead of `AVAILABLE`.

In real deployment this command runs automatically once an hour
(`app/Console/Kernel.php` schedules it) so listings don't accumulate as stale
`AVAILABLE` rows forever, even though the search filter alone already keeps
them hidden from recipients.

---

## Summary checklist

- [ ] Task 1: register donor, register recipient, wrong password rejected, login works
- [ ] Task 2: required fields enforced, listing created as `AVAILABLE`, can't skip to `DELIVERED` early
- [ ] Task 3 / Challenge 1: nearby search finds in-range items with distance, excludes out-of-range
- [ ] Task 4 / Challenge 2: first claim succeeds, second claim on same item blocked with `409`, verified concurrently via curl
- [ ] Challenge 3: expired-but-`AVAILABLE` item hidden from search immediately; `food:expire` sweeps it to `EXPIRED`
