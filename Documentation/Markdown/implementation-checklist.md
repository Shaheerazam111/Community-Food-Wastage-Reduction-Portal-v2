# Implementation Checklist

**Project:** Community Food Wastage Reduction Portal  
**Source of truth:** `Documentation/Markdown/project-requirements.md`  
**Work order:** finish one numbered item fully (DB + controller + view + test) before starting the next.

This is the list we will follow to turn the current prototype into a complete, usable web app. It is **not** a new design document. It is a build list.

---

## 0. Rules to follow (read this before every item)

These come from `Documentation/rules/` plus the project brief. Do not skip them to “save time”.

### Keep it simple

- This is a uni web project. Prefer the shortest working solution.
- One file, one job. No extra folders, factories, or service layers unless we already have them.
- Do not add React, Vue, Tailwind, Livewire, Inertia, Alpine, Firebase, or Google Maps.
- Stack stays **Laravel + Blade + Bootstrap 5 + plain CSS + plain JS + MySQL**.
- Maps stay **OpenStreetMap + Leaflet** (already in the project, no API key).
- Photos and NGO documents go to **local storage** (`storage/app/public`), not Firebase.
- Build what the official requirements ask for. Skip nice-to-haves until the core flow works.
- If two approaches work, pick the one with less code and fewer moving parts.

### Laravel PHP

- One method = one job. Split validate / save / notify into small private methods.
- Every function and every query gets a **3–8 word** comment above it. One line only.
- Short controller methods that call a model or a small helper.

### Blade UI

- One shared layout: `layouts/app.blade.php` (navbar, flash alerts, footer).
- Mobile first: `col-12` then `col-md-*`.
- Forms: only the fields we need. Button labels stay plain (“Post donation”, “Claim food”, “Mark as delivered”).
- Tiny plain JS only (confirm dialogs, map pin, role field toggle). No JS frameworks.
- After adding, renaming, or removing a Blade/CSS/JS/UI route, update `Documentation/Markdown/ui-file-index.md` in the same change.

### Writing / comments / UI copy

- Sound like a uni student wrote it: plain, short, honest.
- No buzzwords. No long comments.
- Error messages tell the user what went wrong and what to do next.

### Real-world, but do not overdo

- Add only things that are **required** by the project doc, or that the portal cannot work without (login by role, seed users, file upload, verification so random people cannot claim food).
- Do **not** add a separate volunteer module, disputes, chat, payments, multi-item donations, listing-approval queue, or extra analytics libraries.
- Recipients in the official doc already include NGOs, shelters, **and volunteers**. They use the same recipient account. No extra role.

---

## 1. Where we are now

**Completed checklist steps:** Steps 1–9. **Next:** Step 10 (polish).

### Already working (do not rebuild)


| Area           | What exists                                                                                                                                                                   |
| -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Auth & roles   | Register/login/logout. Roles: `donor`, `recipient`, `admin`. City, map pin, password confirm. Demo seed (`admin@food.test`, `donor@food.test`, `ngo@food.test` / `password`). |
| Route security | `auth`, `active`, `role`, `verified.account` middleware on portal routes.                                                                                                     |
| Verification   | `verification_documents`, My profile upload, admin Users approve/reject/block, unverified banner. Unverified users cannot post or claim.                                      |
| Donor listing  | Full post: categories, description, kg/servings, photo, per-listing pickup map, edit/cancel (`CANCELLED`). Verified donors only.                                              |
| Search & claim | Haversine nearby search; atomic claim; status lifecycle; expiry filter + hourly `food:expire`.                                                                                |
| UI             | Donor/recipient/admin dashboards, find food, my claims, Bootstrap 5 + Leaflet.                                                                                                |


### Still to build (Steps 3–10)


| Requirement                            | Step         |
| -------------------------------------- | ------------ |
| Polish, install doc, home copy         | **10** (next) |


### Docs / cleanup (later)

- `how-to-install-and-run.md` still mentions old `portal/` + dummy data — update in Step 10.
- Home page still mentions assignment API tasks — rewrite in Step 10.

---

## 2. What we will not build

These appeared in older SRS/design notes. They are **out** unless the supervisor asks later.

- Separate **Volunteer** role, task list, or route maps
- **Disputes** module
- Multiple food items on one donation (`Donation_Item`)
- Admin must approve every listing before it goes live (that delays food; we verify **users**, not each listing)
- Firebase, Google Maps API keys, React/Vue
- Chat, payments, AI routing, mobile app
- Email alerts (requirements say email **or** dashboard; we use dashboard notifications)
- Fancy chart libraries (simple numbers + a small HTML/CSS bar or table is enough)

---

## 3. Simple data we will add (keep it small)

Keep the current `users` + `food_listings` tables. Add only what the requirements need:


| Table                    | Why                                                                                                                              |
| ------------------------ | -------------------------------------------------------------------------------------------------------------------------------- |
| `users` (extend)         | Add `admin` to role. Add `city`, `is_verified`, `is_active`.                                                                     |
| `food_listings` (extend) | Add `city`, `description`, `photo_path`, `quantity_value`, `quantity_unit`, `category_id`. Pickup address/lat/lng already exist. |
| `food_categories`        | Admin can manage categories. Seed Veg and Non-Veg.                                                                               |
| `verification_documents` | NGO (or donor) uploads one proof file. Admin approves/rejects.                                                                   |
| `notifications`          | Dashboard alerts: new nearby food, claim, delivered.                                                                             |
| `feedback`               | One rating + comment after a listing is `DELIVERED`.                                                                             |


One listing = one food post. One claim = one recipient (`claimed_by` already on the listing). Do not split into extra claim/task tables.

---

## 4. Build list (do these 1 by 1)

Mark `[ ]` → `[x]` when that item is working in the browser, not only “code written”.

---

### Step 1 — Foundation (roles, city, seed, cleanup)

Needed before any admin or city report can exist.

- [x] **1.1** Extend `users.role` to `donor`, `recipient`, `admin`.
- [x] **1.2** Add `city` (required), `is_verified` (default false for new donor/recipient, true for admin), `is_active` (default true) on `users`.
- [x] **1.3** Add `city` on `food_listings` (copied from donor when posting, can be edited on the form).
- [x] **1.4** Add a small `role` middleware (`donor` / `recipient` / `admin`) and protect portal routes with `auth` + role.
- [x] **1.5** Register: city field; password confirm; require map pin + address + city; require business name for donor / NGO name for recipient.
- [x] **1.6** After login, send donor → donor dashboard, recipient → recipient dashboard, admin → admin dashboard.
- [x] **1.7** Seeder with three demo users (password the same, easy to demo):
  - `admin@food.test` / Admin
  - `donor@food.test` / Donor (verified)
  - `ngo@food.test` / Recipient (verified)
- [x] **1.8** Drop unused `food` table (new migration `drop_food_table`). Do not rewrite old migrations.
- [x] **1.9** Hide `/test-apis` from the public home page (keep the route for us if needed).

**Done when:** you can register, log in as each role, and seed recreates the three accounts.

---

### Step 2 — User management + verification (Admin + Recipient input)

Official: *secure registration/login for Donors, Recipients, Admin. Admin verifies new donors/NGOs. Recipients upload verification documents.*

- [x] **2.1** Table `verification_documents` (`user_id`, `file_path`, `status` pending/approved/rejected, `admin_note`, timestamps).
- [x] **2.2** Recipient (and donor) can upload one document (PDF/JPG/PNG, size limit ~2MB) from a simple **My profile** page. Donor/NGO can **update profile** (name, phone, org, address, city, map pin) on the same page.
- [x] **2.3** New donor/recipient starts **unverified**. They can log in and look around, but **cannot post or claim** until admin approves.
- [x] **2.4** Admin **Users** page: list users, role, city, verified/active, view document, Approve / Reject / Block.
- [x] **2.5** Blocked (`is_active = false`) users cannot log in. Clear message: “This account is blocked.”
- [x] **2.6** Unverified user sees a simple banner: “Waiting for admin to verify your account.”

**Done when:** a new NGO uploads a file, admin approves, then that NGO can claim food.

---

### Step 3 — Donation listing (Donor side, complete)

Official: *item names, category Veg/Non-Veg, quantity kg or servings, Best Before, photos, GPS pickup address.*

- [x] **3.1** Table `food_categories` (`name`, `is_active`). Seed **Vegetarian** and **Non-vegetarian**.
- [x] **3.2** Listing form fields: title, description (short), category dropdown, quantity number, unit (`kg` or `servings`), best-before, pickup address, city, map pin, optional photo.
- [x] **3.3** Default pickup pin/address/city from the donor profile, but donor **can change it** for this listing.
- [x] **3.4** Save photo on disk; show a small thumbnail on listing cards/tables.
- [x] **3.5** Donor can **edit** or **cancel** a listing only while status is `AVAILABLE`.
- [x] **3.6** Cancelled listings leave search (use status `EXPIRED` or a simple `CANCELLED` — prefer adding `CANCELLED` to the status enum so reports stay honest).
- [x] **3.7** Only **verified + active** donors can post.

**Done when:** a donor posts food with a photo, kg or servings, a map pin, then edits and cancels an unused listing.

---

### Step 4 — Search and map (Recipient side)

Official: *search by current geographic location **or** city. Interactive map.*

- [x] **4.1** Recipient **dashboard** is the home after login: nearby available food (from profile pin, default 10 km), then a short list of recent claims.
- [x] **4.2** Find food page: (a) use current GPS + radius, (b) use profile pin, (c) **filter by city** (text select from cities that have listings).
- [x] **4.3** Still hide claimed, cancelled, and expired food. Sort nearby results closest-first.
- [x] **4.4** Listing **detail** page: photo, description, qty/unit, expiry, donor name/phone, pickup address, map embed, Claim button.
- [x] **4.5** Optional small map on the search results page (pins for visible listings). List view stays the main view so it works on a phone.

**Done when:** an NGO can find food by GPS **and** by city, open a detail page with a map, and claim from there.

---

### Step 5 — Claiming and status (already started — finish it)

Official: *claim reserves the item. Status Available → Claimed → Delivered. Recipient confirms delivered.*

Keep the existing atomic claim. Do not rewrite it.

- [x] **5.1** Only verified + active recipients can claim.
- [x] **5.2** Claim button on detail page + confirm dialog (JS already supports `data-confirm`).
- [x] **5.3** After claim, recipient sees pickup address, map, and donor phone on **My claims**.
- [x] **5.4** Recipient can click **Mark as delivered** on a claimed listing (same status change the donor has). Either side may confirm pickup.
- [x] **5.5** Donor still sees who claimed it and can also mark delivered.
- [x] **5.6** Claimed item never appears in search. Second claim still fails.

**Done when:** two browsers cannot claim the same listing, and the NGO can mark it delivered after pickup.

---

### Step 6 — Notification engine (dashboard)

Official: *alerts to nearby NGOs when a new donation is posted. Instant dashboard notifications for claims/deliveries.*

Keep this small: a `notifications` table + a bell on the navbar. No queues, no email.

- [x] **6.1** Table `notifications` (`user_id`, `food_listing_id` nullable, `message`, `is_read`, timestamps).
- [x] **6.2** When a donor posts food, create one notification for each **verified recipient in the same city** (simple and fast). If no city match, skip rather than scanning the whole country.
- [x] **6.3** When someone claims food, notify the donor: “{NGO} claimed {title}.”
- [x] **6.4** When status becomes delivered, notify the other party.
- [x] **6.5** Navbar: unread count. `/notifications` list. Mark as read (one or all).
- [x] **6.6** Write a small helper, e.g. `notifyUser($userId, $message, $listingId)`, and call it from the existing controllers. Do not build an event/listener stack.

**Done when:** posting food in Lahore shows a dashboard alert for the Lahore NGO; claiming and delivering also create alerts.

---

### Step 7 — Feedback and rating

Official: *recipients rate quality and hygiene after receiving food.*

- [x] **7.1** Table `feedback` (`food_listing_id` unique, `recipient_id`, `rating` 1–5, `comment`, timestamps).
- [x] **7.2** After `DELIVERED`, the recipient sees a short form: star/number rating + optional comment.
- [x] **7.3** One feedback per listing. Cannot submit twice.
- [x] **7.4** Donor dashboard shows average rating (simple). Admin can see recent feedback on reports or users — a short list is enough.

**Done when:** NGO marks delivered, rates 1–5, donor sees that rating.

---

### Step 8 — Dashboards and admin reports (outputs)

Official outputs: donor impact, recipient nearby list, admin city-wise trends, waste reduced, people fed.

Keep numbers honest and simple.

- [x] **8.1** **Donor dashboard:** active listings, delivered count, **meals saved** = sum of `quantity_value` where unit is `servings` and status is `DELIVERED`. Also show **kg saved** for kg listings. Recent listings table stays.
- [x] **8.2** **Recipient dashboard:** nearby available food (prioritized by distance), claim history link, unread notification note.
- [x] **8.3** **Admin dashboard:** counts of users, available/claimed/delivered listings, unverified users waiting.
- [x] **8.4** **Admin reports** page:
  - totals: listings, delivered, kg saved, meals saved
  - table: city | listings | delivered | meals/kg
  - no Chart.js unless a plain Bootstrap table looks empty — prefer a table first
- [x] **8.5** Admin can open a read-only list of all donations (filter by status/city). No extra “moderate listing” workflow.

**Done when:** each role’s home page shows the numbers from the database, not dummy data.

---

### Step 9 — Admin food categories

Official: *admin manages food categories.*

- [x] **9.1** Admin **Categories** page: list, add name, activate/deactivate.
- [x] **9.2** Do not delete Veg / Non-veg if listings already use them; only deactivate.
- [x] **9.3** Post-food dropdown shows active categories only.

**Done when:** admin adds e.g. “Packaged”, and it appears on the donor form.

---

### Step 10 — Polish so it can be used for real

Not extra features. These stop the app from looking unfinished.

- [ ] **10.1** Validation errors show under fields (`@error`). Flash success/error already exists.
- [ ] **10.2** Empty states: “No food nearby yet”, “No listings posted”, “No claims yet”.
- [ ] **10.3** Expired food: keep lazy filter in search/claim; hourly `food:expire` stays.
- [ ] **10.4** Navbar per role only shows that role’s links + Notifications + Logout.
- [ ] **10.5** Rewrite home page copy for real users (how it works), not assignment task numbers.
- [ ] **10.6** Update `ui-file-index.md` to match real files/routes (no `portal/` prefix, no DemoData).
- [ ] **10.7** Update `how-to-install-and-run.md`: real MySQL, `php artisan migrate --seed`, demo logins, no dummy-data note.
- [ ] **10.8** Check main flows on desktop and a phone-width screen.
- [ ] **10.9** Confirm: register → verify → post → notify → search → claim → deliver → rate → admin report.

**Done when:** a new person can run the app from the install doc and complete a full donation without using Postman.

---

## 5. Suggested order in one line

`1 Foundation → 2 Verify users → 3 Complete listing → 4 Search/map → 5 Claim/deliver → 6 Notifications → 7 Feedback → 8 Dashboards/reports → 9 Categories → 10 Polish`

Do not start Step 6–9 until Steps 1–5 work end to end. Notifications and reports need real listings and real users.

---

## 6. Quick role map (final app)


| Role                                      | Can do                                                                                                    |
| ----------------------------------------- | --------------------------------------------------------------------------------------------------------- |
| **Donor**                                 | Register, upload proof, post/edit/cancel food, see claims, mark delivered, see meals/kg saved and ratings |
| **Recipient** (NGO / shelter / volunteer) | Register, upload proof, search by GPS or city, claim, mark delivered, rate food, read notifications       |
| **Admin**                                 | Login, verify/block users, view documents, manage categories, see reports                                 |


---

## 7. How to use this list in coding sessions

1. Pick the **next unchecked item** (smallest number).
2. Change only what that item needs.
3. Keep methods short; add the one-line comments.
4. Update `ui-file-index.md` if a screen changed.
5. Click through it in the browser before ticking `[x]`.
6. Then move to the next item.

