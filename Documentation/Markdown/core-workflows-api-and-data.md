# Core workflows, API, and data model

This describes **what the running v2 app does**. For the full future portal, see SRS and design doc ERD (donations + items + separate claims, etc.).

---

## User registration and login

**Register — `POST /auth/register`**

Required: `name`, `email`, `password` (min 6), `role` (`donor` | `recipient`).

Optional profile fields:

- Donor: `business_name`, `phone`
- Recipient: `ngo_name`, `contact_person`
- Both: `address`, `latitude`, `longitude`

On success: user created, **session started**, redirect to role home (HTML) or JSON `201` with `user`.

**Login — `POST /auth/login`** — email + password; session regenerated.

**Logout — `POST /auth/logout`** — session invalidated.

HTML forms: `resources/views/auth/*.blade.php` → same URLs.

Role homes (HTML):

- Donor → `/donor/dashboard`
- Recipient → `/recipient/food`

---

## Food listing lifecycle

```
AVAILABLE ──claim──► CLAIMED ──deliver (donor)──► DELIVERED
     │
     └── expiry (time or food:expire job) ──► EXPIRED
```

| Status | Meaning |
| --- | --- |
| AVAILABLE | Shows in search; can be claimed |
| CLAIMED | `claimed_by`, `claimed_at` set |
| DELIVERED | Donor marked pickup complete; `delivered_at` set |
| EXPIRED | Past `expiry_at` while still AVAILABLE (job) or filtered out in search |

**Create — `POST /food`** (auth, donor only)

Body: `title`, `category` (`veg` | `non_veg`), `quantity`, `expiry_at` (must be after now).

Pickup coords come from **donor profile**, not the form. Missing lat/lng → 422.

**Search — `GET /food/search`**

Query: `latitude`, `longitude`, optional `radius_km` (default 5, max 100).

Returns AVAILABLE, non-expired listings within radius, **closest first** (`distance_km` on each row).

HTML: recipient must be logged in; JSON: no auth required (same as Postman testing).

**My listings — `GET /food/mine`** (auth, donor) — donor’s listings with claimer info.

**My claims — `GET /food/claims`** (auth, recipient) — listings where `claimed_by = me`.

**Claim — `POST /food/{id}/claim`** (auth, recipient)

Single SQL update:

- `WHERE id = ? AND status = 'AVAILABLE' AND expiry_at > now()`
- Sets `CLAIMED`, `claimed_by`, `claimed_at`

If 0 rows updated → **409** “already claimed or expired” (handles race).

**Deliver — `POST /food/{id}/deliver`** (auth, donor owner)

Only if status is CLAIMED → DELIVERED.

**Cancel — `POST /food/{id}/cancel`** (auth, donor owner)

Only if status is AVAILABLE → EXPIRED (withdrawn from search).

---

## Database schema (v2)

### `users`

Standard Laravel auth columns plus:

- `role`: enum `donor`, `recipient`
- Donor: `business_name`, `phone`
- Recipient: `ngo_name`, `contact_person`
- Shared: `address`, `latitude`, `longitude` (nullable decimals)

### `food_listings`

| Column | Notes |
| --- | --- |
| `donor_id` | FK users |
| `title`, `category`, `quantity` | Listing content |
| `pickup_address`, `latitude`, `longitude` | Copied from donor at post time |
| `expiry_at` | datetime |
| `status` | AVAILABLE, CLAIMED, DELIVERED, EXPIRED |
| `claimed_by`, `claimed_at`, `delivered_at` | Claim / delivery |

Index on `(status, expiry_at)` for expiry sweeps and filters.

---

## Three assignment challenges (how this repo solves them)

### 1. Nearby search performance

Haversine in SQL via `selectRaw` + `havingRaw('distance_km <= ?')` + `orderBy('distance_km')` in `FoodListing::searchAvailableNearby`.

### 2. Double claim

No read-then-write. One conditional `UPDATE` in `ClaimController::claim`.

### 3. Expired food

- **Lazy:** search and claim require `expiry_at > now()`
- **Eager (optional):** `php artisan food:expire` marks AVAILABLE past expiry as EXPIRED; scheduled hourly in `app/Console/Kernel.php`

---

## JSON vs HTML (same controller methods)

Trait: `app/Http/Controllers/Concerns/HandlesPortalResponses.php`

- Client sends `Accept: application/json` or expects JSON → JSON body + HTTP status
- Otherwise → `redirect()` with `success` / `error` session flash

`FoodListingController::myListings`, `search`, `myClaims` also branch: JSON early return, else view.

---

## Testing endpoints

| Tool | Location |
| --- | --- |
| Postman | `Documentation/postman_collection.json` |
| Browser | `/test-apis` + `ApiTestController` |
| curl | Reuse session cookie jar after login |

Default DB name in README: `cfwrp_v2` (set in `.env`).

---

## Sequence / architecture diagrams

Planned full-portal flows (admin, volunteer, disputes): `Documentation/Markdown/section-3-sequence.md` and `Documentation/diagrams/*.puml`.

Those diagrams **do not** all match v2 code; use them for documentation assignments, not as a map of current classes.
