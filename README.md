# Community Food Wastage Reduction Portal — v2

Laravel web app that connects **food donors** with **NGO recipients**: register, post surplus food, search nearby, claim safely, track delivery, and view **admin reports**. Same routes work as **JSON API** (Postman / `/test-apis`) or **HTML forms** (Blade UI).

## Features

- Session auth for donor, recipient, and admin  
- Food listing lifecycle: `AVAILABLE` → `CLAIMED` → `DELIVERED` (+ `EXPIRED`)  
- Nearby search (Haversine + radius)  
- Atomic claim (no double booking)  
- Hourly expiry job (`food:expire`)  
- Admin dashboard, analytics, CSV export  

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
# Set DB_DATABASE=cfwrp_v2 in .env, create DB in MySQL
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan serve
```

Open http://127.0.0.1:8000 — see [Documentation/Markdown/how-to-install-and-run.md](Documentation/Markdown/how-to-install-and-run.md) and [demo-and-viva-script.md](Documentation/Markdown/demo-and-viva-script.md).

## API endpoints

| Method | URL | Auth | Purpose |
|--------|-----|------|---------|
| POST | `/auth/register` | - | Create donor or recipient |
| POST | `/auth/login` | - | Start session |
| POST | `/auth/logout` | yes | End session |
| POST | `/food` | yes (donor) | Create listing |
| GET | `/food/mine` | yes (donor) | My listings |
| GET | `/food/search?latitude=&longitude=&radius_km=` | - | Nearby available food |
| POST | `/food/{id}/claim` | yes (recipient) | Claim listing |
| POST | `/food/{id}/deliver` | yes (donor) | Mark delivered |
| POST | `/food/{id}/cancel` | yes (donor) | Withdraw AVAILABLE listing |
| GET | `/food/claims` | yes (recipient) | My claims |

Admin UI: `/admin/dashboard`, `/admin/reports`

## Assignment challenges

1. **Distance** — Haversine in `FoodListing::searchAvailableNearby`  
2. **Race-safe claim** — conditional `UPDATE` in `ClaimController`  
3. **Expiry** — filter in search/claim + `php artisan food:expire` (scheduled hourly)

## Documentation

| Doc | Purpose |
|-----|---------|
| [SRS-v2](Documentation/Markdown/SRS-v2/SRS-v2-main.md) | Requirements aligned with code |
| [improvements-needed-checklist.md](Documentation/Markdown/improvements-needed-checklist.md) | Assignment vs full brief + production checklist |
| [production-deploy.md](Documentation/Markdown/production-deploy.md) | Server deployment |
| [Documentation/postman_collection.json](Documentation/postman_collection.json) | API tests |

CSRF is skipped for `/auth/*` and `/food*` for JSON clients (`VerifyCsrfToken`); browser forms still send `@csrf` where required.

## Production

Set `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, and cron for `schedule:run`. Details: [production-deploy.md](Documentation/Markdown/production-deploy.md).
