# Implementation status — built vs planned

Use this so you do not assume features from the SRS or old UI docs are already in the repo.

---

## Assignment v2 (core engine) — **implemented**

| Requirement | Status | Where |
| --- | --- | --- |
| Register donor / recipient | Done | `AuthController::register`, `users` migration |
| Session login / logout | Done | `AuthController`, Laravel session |
| Donor creates listing | Done | `FoodListingController::store` |
| Status flow AVAILABLE → CLAIMED → DELIVERED | Done | Claim + deliver; enum also has EXPIRED |
| Nearby search + radius + sort by distance | Done | `FoodListing::searchAvailableNearby` (Haversine) |
| Hide claimed / expired from search | Done | `status = AVAILABLE`, `expiry_at > now()` |
| Atomic claim (race-safe) | Done | `ClaimController` single `UPDATE ... WHERE status = AVAILABLE` |
| Expired cleanup | Done | Lazy filter in search/claim + `food:expire` hourly |

---

## Thin Blade UI — **partial**

| Screen | Status | Notes |
| --- | --- | --- |
| Home | Done | `home.blade.php` |
| Login / Register | Done | Posts to same `/auth/*` as API |
| Donor dashboard | Done | Real DB counts + recent listings |
| Post donation | Done | Form → `POST /food` |
| My donations | Done | `GET /food/mine` |
| Recipient search | Done | Form → `GET /food/search` |
| My claims | Done | `GET /food/claims` |
| Mark delivered from UI | Depends on views | API exists: `POST /food/{id}/deliver` |
| Cancel listing (AVAILABLE) | Done | `POST /food/{id}/cancel` → EXPIRED |
| Edit listing | Not in routes | Planned |
| Map browse / city filter | Partial | Search uses lat/lng; full receiver map UI not ported |
| Demo login buttons (no password) | Not in current login blade | Old prototype had demo roles |

---

## Admin reporting — **implemented (v2 data)**

| Feature | Status |
| --- | --- |
| Admin role + middleware | Done — migration adds `admin` to `users.role`; seed with `AdminUserSeeder` |
| Admin dashboard | Done — `/admin/dashboard` |
| Reports (filters, trends, leaders, CSV) | Done — `/admin/reports` |
| User verify, moderate listings, disputes | Not built |

---

## Full portal (SRS + design doc) — **mostly not in this codebase**

These are documented for reports and future work, not running here:

- Admin user moderation, listing approval, disputes (beyond reporting)
- Volunteer tasks and route maps
- Notifications (email / dashboard)
- Feedback and ratings
- NGO document upload and approval workflow
- Donation with **multiple items** per pickup (design ERD: `donations` + `donation_items`)
- Separate `claims` table (v2 stores claim on `food_listings.claimed_by`)

When adding features, decide whether to **extend v2’s flat `food_listings` model** or **migrate toward the design ERD** — that is a product choice; design doc is in `Documentation/Markdown/Design-doc-sections.md`.

---

## Database notes

| Table | Purpose |
| --- | --- |
| `users` | Auth + role + donor/recipient profile + lat/lng |
| `food_listings` | One row = one listable food item at donor’s pickup location |

**Legacy / unused:** migration `create_food_table.php` creates a generic `food` table (name, price, description). It is **not** used by controllers. Safe to ignore or remove in a future cleanup migration.

---

## Docs that may mislead

| Doc | Issue |
| --- | --- |
| `how-to-install-and-run.md` | Says `cd portal`, `DemoData`, optional DB |
| `ui-file-index.md` | Paths prefixed with `portal/`; lists admin/receiver/volunteer views that are **not** in this repo |
| `README.md` | Says “no UI” — UI was added after; engine description still valid |

Prefer [agent-read-me-first.md](agent-read-me-first.md) and this file over those for “what exists today.”

---

## Extra tooling in repo (beyond assignment minimum)

- **`/test-apis`** — browser client to hit the same endpoints as Postman (`ApiTestController`, `public/js/api-tester/*`)
- **Geocoding** — `GeocodingService` for tester reverse geocode
- **Postman collection** — `Documentation/postman_collection.json`
