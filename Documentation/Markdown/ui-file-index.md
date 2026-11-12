# UI File Index

List of UI-related files for this project. Grouped by entity. Update this file whenever a Blade view, shared CSS/JS, UI route, or UI stub controller/helper is added, renamed, or removed.

Each row: **file path** | **what it does** | **related route**

---

## Shared

Layouts, CSS, JS, home, notifications, and shared partials (used across roles).

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/resources/views/layouts/app.blade.php` | Logged-in layout: role nav yield, flash alerts, sticky footer | (all role pages) |
| `portal/resources/views/layouts/guest.blade.php` | Guest layout with Login / Register nav buttons | `/`, `/login`, `/register` |
| `portal/public/css/app.css` | Brand colors, nav buttons, home hero, status badges, map embeds, table min-widths | (all pages) |
| `portal/public/js/app.js` | Confirm dialogs, donation item rows, map pin picker | (all pages; map picker on post/edit) |
| `portal/resources/views/home.blade.php` | Landing hero, how-it-works steps, demo role links | `/` |
| `portal/resources/views/notifications/index.blade.php` | Dummy notification list with Mark as read | `/notifications`, POST `/notifications/{id}/read` |
| `portal/resources/views/partials/map_embed.blade.php` | Read-only OpenStreetMap iframe + Open in Maps link | (included by browse/detail/claim/task) |
| `portal/resources/views/partials/map_picker.blade.php` | Clickable Leaflet map to set pickup pin (hidden lat/lng) | (included by post/edit donation) |
| `resources/views/partials/role_nav.blade.php` | Picks admin/donor/recipient/volunteer nav by logged-in role | (included by notifications and similar) |
| `resources/views/partials/donor_nav.blade.php` | Donor navbar links + notifications | (included by donor pages) |
| `resources/views/profile/edit.blade.php` | Update name, email, role fields, map pin, password | `/profile`, POST `/profile` |
| `app/Http/Controllers/ProfileController.php` | Profile form + save | `/profile` |

---

## Auth

Login and register screens.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/resources/views/auth/login.blade.php` | Login form + demo role buttons including Pending NGO flash | `/login` (GET, POST), `/login/demo/{role}` |
| `portal/resources/views/auth/register.blade.php` | Register form with * required markers; NGO fields required when Receiver | `/register` (GET, POST) |

---

## Donor

Donor dashboard, post/edit/cancel donation, my donations.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/resources/views/donor/dashboard.blade.php` | Summary cards (incl. Meals saved) + recent donations with status badges | `/donor/dashboard` |
| `portal/resources/views/donor/donations/create.blade.php` | Post donation with map pin picker + multiple food item rows | `/donor/donations/create`, POST `/donor/donations` |
| `portal/resources/views/donor/donations/index.blade.php` | My donations table; Edit / Cancel for Available listings | `/donor/donations`, POST `/donor/donations/{id}/cancel` |
| `portal/resources/views/donor/donations/edit.blade.php` | Edit pickup + map pin + items; Cancel listing button | `/donor/donations/{id}/edit`, POST `/donor/donations/{id}`, POST `.../cancel` |
| `portal/resources/views/donor/donations/_item_row.blade.php` | Shared row for one food item (name, category, qty, unit, best before, photo) | (included by create/edit) |

---

## Receiver

NGO/receiver dashboard, browse food (list/map), donation detail + claim, claim history + claim detail.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/resources/views/receiver/dashboard.blade.php` | Nearby food count, available list, recent claims | `/receiver/dashboard` |
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

## Notifications

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/notifications/index.blade.php` | Dashboard alerts list + mark read | `/notifications`, POST `/notifications/{id}/read` |

---

## Volunteer

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/volunteer/tasks/index.blade.php` | CLAIMED pickup tasks + complete | `/volunteer/tasks`, POST `/volunteer/tasks/{id}/complete` |
| `resources/views/partials/volunteer_nav.blade.php` | Volunteer nav | (included) |

---

## Admin

Admin dashboard, reports, and user block/unblock.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `resources/views/admin/dashboard.blade.php` | Last 30 days KPIs + status bars | `/admin/dashboard` |
| `resources/views/admin/reports/index.blade.php` | Date filter, trends, top donors/NGOs, areas, CSV export | `/admin/reports`, `/admin/reports/export` |
| `resources/views/partials/admin_nav.blade.php` | Admin navbar links | (included by admin pages) |
| `app/Http/Controllers/AdminDashboardController.php` | Dashboard data | `/admin/dashboard` |
| `app/Http/Controllers/AdminReportController.php` | Reports + CSV | `/admin/reports`, `/admin/reports/export` |
| `app/Services/AdminReportService.php` | Report SQL aggregations | (used by admin controllers) |
| `app/Http/Middleware/EnsureUserIsAdmin.php` | Blocks non-admin users | `admin` middleware |
| `resources/views/admin/users/index.blade.php` | Block/unblock users | `/admin/users`, POST `/admin/users/{id}` |
| `app/Http/Controllers/AdminUserController.php` | User list + is_active toggle | `/admin/users` |
| `app/Http/Controllers/NotificationController.php` | Alerts list | `/notifications` |
| `app/Http/Controllers/FeedbackController.php` | Post-delivery rating | POST `/food/{listing}/feedback` |
| `app/Http/Controllers/VolunteerController.php` | Pickup tasks | `/volunteer/tasks` |
| `resources/views/admin/verification/index.blade.php` | Review NGO documents | `/admin/verification` |
| `resources/views/admin/listings/index.blade.php` | Approve/reject food listings | `/admin/listings` |
| `resources/views/recipient/verification/show.blade.php` | NGO upload + status | `/recipient/verification` |
| `app/Http/Controllers/AdminVerificationController.php` | Doc review + download | `/admin/verification` |
| `app/Http/Controllers/AdminListingModerationController.php` | Listing moderation | `/admin/listings` |
| `app/Http/Controllers/RecipientVerificationController.php` | NGO re-upload | `/recipient/verification` |

---

## Backend for UI

Controllers, demo data, and routes that power the UI stubs.

| File | What it does | Related route |
| ---- | ------------ | ------------- |
| `portal/app/Support/DemoData.php` | Dummy users (doc path), donations (lat/lng, Expired), claims, tasks (route coords), disputes, feedback, notifications, report counts | (passed into views) |
| `portal/app/Http/Controllers/PageController.php` | Serves every UI screen + demo POST redirects with flash messages | (all UI routes) |
| `routes/web.php` | Routes for home, auth, food API, admin, test-apis | (see repo README) |
| `Documentation/Markdown/improvements-needed-checklist.md` | Assignment vs brief vs production checklist | (docs) |
| `Documentation/Markdown/production-deploy.md` | Production deployment | (docs) |
| `Documentation/Markdown/demo-and-viva-script.md` | 5-minute demo for teacher | (docs) |
