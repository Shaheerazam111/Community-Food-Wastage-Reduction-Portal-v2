# UI File Index

List of UI-related files for this project. Grouped by entity. Update this file whenever a Blade view, shared CSS/JS, UI route, or UI stub controller/helper is added, renamed, or removed.

Each row: **file path** | **what it does** | **related route**

---

## Shared

Layouts, CSS, JS, home, notifications, and shared partials (used across roles).

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/layouts/app.blade.php` | Logged-in layout: role nav, alerts bell + unread badge, flash alerts | (all role pages) |
| `resources/views/notifications/index.blade.php` | Dashboard notification list, mark one / mark all read | `/notifications`, POST read routes |
| `app/Support/PortalNotifier.php` | `notifyUser()` + city-based new listing alerts | (called from food/claim controllers) |
| `app/Models/PortalNotification.php` | `notifications` table model | — |
| `portal/resources/views/layouts/app.blade.php` | (legacy path in old docs) | — |
| `portal/resources/views/layouts/guest.blade.php` | Guest layout with Login / Register nav buttons | `/`, `/login`, `/register` |
| `portal/public/css/app.css` | Brand colors, nav buttons, home hero, status badges, map embeds, table min-widths | (all pages) |
| `portal/public/js/app.js` | Confirm dialogs, donation item rows, map pin picker | (all pages; map picker on post/edit) |
| `portal/resources/views/home.blade.php` | Landing hero, how-it-works steps, demo role links | `/` |
| `portal/resources/views/notifications/index.blade.php` | Dummy notification list with Mark as read | `/notifications`, POST `/notifications/{id}/read` |
| `portal/resources/views/partials/map_embed.blade.php` | Read-only OpenStreetMap iframe + Open in Maps link | (included by browse/detail/claim/task) |
| `portal/resources/views/partials/map_picker.blade.php` | Clickable Leaflet map to set pickup pin (hidden lat/lng) | (included by post/edit donation) |

---

## Auth

Login and register screens.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/auth/login.blade.php` | Login form; demo account hints | `/login`, POST `/auth/login` |
| `resources/views/auth/register.blade.php` | Register with city, password confirm, map pin, role-specific fields | `/register`, POST `/auth/register` |

---

## Donor

Donor dashboard, post/edit/cancel donation, my donations.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/donor/dashboard.blade.php` | Status counts + recent listings; unverified banner | `/donor/dashboard` (auth, donor) |
| `resources/views/donor/donations/create.blade.php` | Post food listing (full form) | `/donor/donations/create`, POST `/food` |
| `resources/views/donor/donations/edit.blade.php` | Edit or cancel AVAILABLE listing | `/donor/donations/{id}/edit`, POST `/food/{id}`, POST `/food/{id}/cancel` |
| `resources/views/donor/donations/_form.blade.php` | Shared listing fields + map pin | (included by create/edit) |
| `resources/views/partials/listing_photo.blade.php` | Food photo thumbnail | (listing tables) |
| `portal/resources/views/donor/donations/index.blade.php` | My donations table; Edit / Cancel for Available listings | `/donor/donations`, POST `/donor/donations/{id}/cancel` |
| `portal/resources/views/donor/donations/edit.blade.php` | Edit pickup + map pin + items; Cancel listing button | `/donor/donations/{id}/edit`, POST `/donor/donations/{id}`, POST `.../cancel` |
| `portal/resources/views/donor/donations/_item_row.blade.php` | Shared row for one food item (name, category, qty, unit, best before, photo) | (included by create/edit) |

---

## Recipient (NGO)

Recipient dashboard, find food, my claims.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/recipient/dashboard.blade.php` | City/org summary + recent claims | `/recipient/dashboard` (auth, recipient) |
| `resources/views/recipient/food/index.blade.php` | GPS / profile / city search + map pins + results table | `/recipient/food`, GET `/food/search` |
| `resources/views/recipient/food/show.blade.php` | Listing detail, map, claim | `/recipient/food/{id}` |
| `resources/views/partials/search_results_map.blade.php` | Leaflet pins for search results | (included on find food) |
| `resources/views/recipient/claims/index.blade.php` | Claim cards: pickup map, donor phone, mark delivered | `/food/claims`, POST `/food/{id}/deliver` |
| `resources/views/partials/recipient_nav.blade.php` | Recipient navbar links incl. My profile | (included on recipient pages) |
| `resources/views/profile/show.blade.php` | Update profile + verification document upload | `/profile`, POST `/profile`, POST `/profile/document` |

## Receiver (legacy stubs — not routed)

Old demo screens kept in repo; live app uses **Recipient** paths above.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/receiver/dashboard.blade.php` | (unused stub) | — |
| `portal/resources/views/receiver/donations/index.blade.php` | City search + List or Nearby/Map view with OSM pins | `/receiver/donations?view=list\|map` |
| `portal/resources/views/receiver/donations/show.blade.php` | Donation items, pickup map, claim whole donation | `/receiver/donations/{id}`, POST `.../claim` |
| `portal/resources/views/receiver/claims/index.blade.php` | Claim history table with Open link to detail | `/receiver/claims` |
| `portal/resources/views/receiver/claims/show.blade.php` | Claim detail: map, status update, raise dispute, feedback | `/receiver/claims/{id}`, POST `.../status`, `.../dispute`, `.../feedback` |

---

## Volunteer

Volunteer task list and task detail with route maps.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/resources/views/volunteer/tasks/index.blade.php` | Assigned pickup / delivery tasks table | `/volunteer/tasks` |
| `portal/resources/views/volunteer/tasks/show.blade.php` | Task detail, pickup/drop-off maps, update status | `/volunteer/tasks/{id}` (GET, POST) |

---

## Admin

Admin dashboard, users (view doc), moderate listings (reject reason), disputes, reports.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/admin/dashboard.blade.php` | User/listing counts; pending verification count | `/admin/dashboard` (auth, admin) |
| `resources/views/admin/users/index.blade.php` | Approve, reject, block donor/NGO users; view document | `/admin/users`, POST `/admin/users/{id}`, GET `.../document` |
| `portal/resources/views/admin/users/index.blade.php` | User list; View document modal stub; Approve / Block | `/admin/users`, POST `/admin/users/{id}` |
| `portal/resources/views/admin/donations/index.blade.php` | Moderate listings + reject reason; recent feedback list | `/admin/donations`, POST `/admin/donations/{id}` |
| `portal/resources/views/admin/disputes/index.blade.php` | Dispute cards: status select + admin note + Update | `/admin/disputes`, POST `/admin/disputes/{id}` |
| `portal/resources/views/admin/reports/index.blade.php` | Totals, by-status table, city filter + by-city table | `/admin/reports` |

---

## Backend for UI

Controllers, demo data, and routes that power the UI stubs.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/app/Support/DemoData.php` | Dummy users (doc path), donations (lat/lng, Expired), claims, tasks (route coords), disputes, feedback, notifications, report counts | (passed into views) |
| `portal/app/Http/Controllers/PageController.php` | Serves every UI screen + demo POST redirects with flash messages | (all UI routes) |
| `portal/routes/web.php` | GET/POST routes for home, auth, donor, receiver, volunteer, admin, notifications | (see Auth–Admin above) |
