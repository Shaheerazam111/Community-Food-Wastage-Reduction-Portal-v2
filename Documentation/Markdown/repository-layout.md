# Repository layout (where code lives)

Laravel application root = **repository root**. There is no separate `portal/` app in this v2 repo.

---

## Top-level map

```
Community-Food-Wastage-Reduction-Portal-v2/
├── app/                    # Application code
├── bootstrap/
├── config/
├── database/
│   ├── migrations/         # users, food_listings (+ legacy `food` table)
│   └── seeders/
├── public/                 # Web root: css/, js/, index.php
├── resources/
│   └── views/              # Blade UI
├── routes/
│   ├── web.php             # All main HTTP routes (API + HTML)
│   └── api.php             # Only Sanctum /user stub
├── Documentation/          # Specs, diagrams, rules, Markdown
├── README.md               # Core API description
└── .env.example
```

---

## `app/` — backend

| Path | Role |
| --- | --- |
| `Http/Controllers/AuthController.php` | Register, login, logout |
| `Http/Controllers/FoodListingController.php` | Create listing, search, my listings, my claims, mark delivered |
| `Http/Controllers/ClaimController.php` | Atomic claim |
| `Http/Controllers/PortalController.php` | GET-only HTML: login, register, donor dashboard, create form, recipient search form |
| `Http/Controllers/HomeController.php` | Landing `home` |
| `Http/Controllers/ApiTestController.php` | `/test-apis` page + snapshot + reverse geocode |
| `Http/Controllers/Concerns/HandlesPortalResponses.php` | JSON vs HTML redirect helper |
| `Models/User.php` | Auth user + profile fields + role |
| `Models/FoodListing.php` | Listing model + `searchAvailableNearby()` |
| `Services/GeocodingService.php` | Used by API tester geocode |
| `Services/ApiTestSnapshotService.php` | Data for test-apis UI |
| `Support/ApiTestCatalog.php` | Endpoint catalog for test-apis |
| `Console/Commands/ExpireFoodListings.php` | `php artisan food:expire` |
| `Console/Kernel.php` | Schedules `food:expire` hourly |
| `Http/Middleware/VerifyCsrfToken.php` | CSRF **except** `auth/*` and `food*` (JSON API + same URLs for forms) |

There is **no** `PageController`, **no** `DemoData.php` in this repo.

---

## `routes/web.php` — routing style

- Public HTML: `/`, `/login`, `/register`
- Portal GET: `/donor/dashboard`, `/donor/donations/create`, `/recipient/food`
- Redirects: `/donor/donations` → `/food/mine`, `/recipient/claims` → `/food/claims`
- Auth API: `POST /auth/register|login|logout`
- Food API: `GET /food/search`, `POST /food`, `GET /food/mine`, `GET /food/claims`, `POST /food/{id}/claim`, `POST /food/{id}/deliver` (claim/deliver/post behind `auth` middleware)
- Dev UI: `/test-apis`, snapshot, reverse-geocode

`routes/api.php` is minimal (Sanctum user). **Real endpoints are on `web.php`.**

---

## `resources/views/` — UI (current)

| Area | Views |
| --- | --- |
| Layouts | `layouts/app.blade.php`, `layouts/guest.blade.php` |
| Auth | `auth/login.blade.php`, `auth/register.blade.php` |
| Donor | `donor/dashboard.blade.php`, `donor/donations/create.blade.php`, `donor/donations/index.blade.php` |
| Recipient | `recipient/food/index.blade.php`, `recipient/claims/index.blade.php` |
| Shared | `home.blade.php`, `partials/*` (maps, tables, nav) |
| API tester | `test-apis.blade.php` + `test-apis/partials/*` |

**Not present** (planned in design docs / old ui-file-index): `receiver/*`, `volunteer/*`, `admin/*`, `notifications/*`, donation edit, multi-item rows partial.

---

## `public/` — front-end assets

| Path | Role |
| --- | --- |
| `public/css/app.css` | Global styles |
| `public/js/app.js` | Shared JS (e.g. map picker on forms) |
| `public/js/api-tester/*` | Client for `/test-apis` |
| `public/css/api-tester.css` | Tester styling |

Stack rule: **Bootstrap 5 + plain CSS + plain JS** — no React/Vue/Tailwind unless the user asks.

---

## `Documentation/`

| Folder / file | Use |
| --- | --- |
| `Markdown/` | Human + agent docs (requirements, install, agent session set) |
| `rules/*.mdc` | Copies of Cursor rules (stack, PHP, UI index) |
| `diagrams/` | PlantUML / Mermaid architecture and sequences |
| `postman_collection.json` | API tests |

Cursor rules also live under the user’s `.cursor/rules/` (writing style, keep-it-simple, functions-and-queries, etc.) — see [coding-rules-and-conventions.md](coding-rules-and-conventions.md).
