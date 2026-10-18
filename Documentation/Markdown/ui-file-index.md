# UI File Index

UI-related files for the Community Food Wastage Reduction Portal. Update this file when a Blade view, shared CSS/JS, or UI route is added, renamed, or removed.

Each row: **file path** | **what it does** | **related route**

Legacy demo stubs under `resources/views/receiver/` and `resources/views/volunteer/` are **not routed**; the live app uses **donor** and **recipient** paths below.

---

## Shared

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/layouts/app.blade.php` | Logged-in layout: role nav slot, alerts bell, flash messages | (all authenticated pages) |
| `resources/views/layouts/guest.blade.php` | Guest layout: Login / Register nav | `/`, `/login`, `/register` |
| `resources/views/home.blade.php` | Landing hero and how-it-works steps | `/` |
| `public/css/app.css` | Brand colors, cards, badges, maps, empty states | (all pages) |
| `public/js/app.js` | Confirm dialogs, register role toggle, map pin picker | (global; map on register/profile/post) |
| `resources/views/partials/unverified_banner.blade.php` | Banner when account is not verified | (included in app layout) |
| `resources/views/partials/map_embed.blade.php` | Read-only OSM iframe + external maps link | (claim detail, listing detail) |
| `resources/views/partials/map_picker.blade.php` | Leaflet click map for lat/lng | (register, profile, post/edit food) |
| `resources/views/partials/listing_photo.blade.php` | Food photo thumbnail | (tables and cards) |
| `resources/views/partials/listings_table.blade.php` | Donor listing table with optional actions | (donor dashboard, my listings) |
| `resources/views/partials/search_results_map.blade.php` | Leaflet pins for search results | (find food) |
| `resources/views/partials/donor_nav.blade.php` | Donor navbar links | (donor pages) |
| `resources/views/partials/recipient_nav.blade.php` | Recipient navbar links | (recipient pages) |
| `resources/views/partials/admin_nav.blade.php` | Admin navbar links | (admin pages) |
| `resources/views/notifications/index.blade.php` | Notification list; mark one / all read | `/notifications`, POST read routes |

---

## Auth

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/auth/login.blade.php` | Login form; demo account hints | `/login`, POST `/auth/login` |
| `resources/views/auth/register.blade.php` | Register: city, password confirm, map pin, role fields | `/register`, POST `/auth/register` |

---

## Donor

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/donor/dashboard.blade.php` | Impact stats, average rating, recent listings | `/donor/dashboard` |
| `resources/views/donor/donations/create.blade.php` | Post food listing | `/donor/donations/create`, POST `/food` |
| `resources/views/donor/donations/edit.blade.php` | Edit or cancel AVAILABLE listing | `/donor/donations/{id}/edit`, POST `/food/{id}`, POST `/food/{id}/cancel` |
| `resources/views/donor/donations/index.blade.php` | My listings (empty state or table) | `/food/mine` |
| `resources/views/donor/donations/_form.blade.php` | Shared listing fields + map pin | (create/edit) |

---

## Recipient (NGO / shelter / volunteer account)

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/recipient/dashboard.blade.php` | Nearby food, recent claims, notification note | `/recipient/dashboard` |
| `resources/views/recipient/food/index.blade.php` | GPS / profile / city search + map + results | `/recipient/food`, GET `/food/search` |
| `resources/views/recipient/food/show.blade.php` | Listing detail, map, claim button | `/recipient/food/{id}` |
| `resources/views/recipient/claims/index.blade.php` | Claim cards: pickup, donor phone, deliver, rate | `/food/claims`, POST `/food/{id}/deliver`, POST `/food/{id}/feedback` |
| `resources/views/partials/listing_feedback_form.blade.php` | 1–5 rating + comment after DELIVERED | (my claims) |
| `resources/views/profile/show.blade.php` | Update profile + verification document | `/profile`, POST `/profile`, POST `/profile/document` |

---

## Admin

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/admin/dashboard.blade.php` | User/listing counts, pending verification, recent feedback | `/admin/dashboard` |
| `resources/views/admin/reports/index.blade.php` | Portal totals + city impact table | `/admin/reports` |
| `resources/views/admin/listings/index.blade.php` | Read-only listings; filter by status/city | `/admin/listings` |
| `resources/views/admin/categories/index.blade.php` | List/add/activate food categories | `/admin/categories`, POST routes |
| `resources/views/admin/users/index.blade.php` | Approve, reject, block; view document | `/admin/users`, POST `/admin/users/{id}`, GET `.../document` |

---

## Dev-only (not linked from home)

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/test-apis.blade.php` | API tester UI for Postman-style checks | `/test-apis` |

---

## Backend for UI (main)

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `routes/web.php` | All portal GET/POST routes | (see sections above) |
| `app/Http/Controllers/AuthController.php` | Register, login, logout | `/auth/*` |
| `app/Http/Controllers/PortalController.php` | Dashboards, login/register views | role dashboards |
| `app/Http/Controllers/FoodListingController.php` | Post, search, claim list, deliver | `/food/*`, `/recipient/food/*` |
| `app/Http/Controllers/ClaimController.php` | Atomic claim | POST `/food/{id}/claim` |
| `app/Http/Controllers/FeedbackController.php` | One feedback per listing | POST `/food/{id}/feedback` |
| `app/Http/Controllers/ProfileController.php` | Profile + verification upload | `/profile` |
| `app/Http/Controllers/NotificationController.php` | Dashboard notifications | `/notifications` |
| `app/Http/Controllers/AdminUserController.php` | User verification | `/admin/users` |
| `app/Http/Controllers/AdminFoodCategoryController.php` | Categories | `/admin/categories` |
| `app/Http/Controllers/AdminReportController.php` | Reports + admin listing browser | `/admin/reports`, `/admin/listings` |
| `app/Http/Controllers/HomeController.php` | Public home | `/` |
| `app/Support/PortalNotifier.php` | `notifyUser()` + city alerts on new listings | (called from controllers) |
| `app/Support/ListingImpact.php` | Meals/kg saved for dashboards & reports | — |
| `app/Support/RoleDashboard.php` | Role → dashboard path (login redirect, guest middleware, navbar) | `/donor/dashboard`, `/recipient/dashboard`, `/admin/dashboard` |
| `app/Console/Commands/ExpireFoodListings.php` | Hourly `food:expire` command | `php artisan food:expire` |
