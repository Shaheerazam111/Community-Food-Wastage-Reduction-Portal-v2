# How to Install and Run

Steps for the **Community Food Wastage Reduction Portal v2** (Laravel app at the **repo root**).

---

## What you need

| Tool | Version (approx.) |
| ---- | ------------------- |
| PHP | 8.0+ |
| Composer | 2.x |
| MySQL | 8.x |
| Browser | Chrome / Edge / Firefox |

```bash
php -v
composer -V
```

---

## 1. Clone and install

```bash
git clone <your-repo-url> Community-Food-Wastage-Reduction-Portal-v2
cd Community-Food-Wastage-Reduction-Portal-v2
composer install
```

---

## 2. Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
DB_DATABASE=cfwrp_v2
DB_USERNAME=root
DB_PASSWORD=
```

Create the database:

```sql
CREATE DATABASE cfwrp_v2;
```

---

## 3. Database

```bash
php artisan migrate
php artisan db:seed --class=AdminUserSeeder
```

Default admin (change in production): `admin@foodportal.test` / `admin123`

Optional: set `SEED_ADMIN=true` before `php artisan db:seed` if you extend `DatabaseSeeder`.

---

## 4. Run locally

```bash
php artisan serve
```

Open **http://127.0.0.1:8000**

Optional expiry job (manual):

```bash
php artisan food:expire
```

For scheduled expiry in dev: `php artisan schedule:work`

---

## 5. Try the app

1. **Register** a donor — business name, phone, address, **map pin** (required).  
2. **Post food** from donor dashboard.  
3. **Register** an NGO (new browser or logout) with map pin.  
4. **Search** and **claim** food.  
5. Donor **Mark delivered** on my listings.  
6. Admin → `/admin/dashboard` (after seeder).

Full demo: [demo-and-viva-script.md](demo-and-viva-script.md)

API playground: **http://127.0.0.1:8000/test-apis**  
Postman: `Documentation/postman_collection.json`

---

## Production

See [production-deploy.md](production-deploy.md).

---

## Common problems

| Issue | Fix |
| ----- | --- |
| No application key | `php artisan key:generate` |
| Donor cannot post | Register with map pin (lat/lng required) |
| Admin login fails | `php artisan db:seed --class=AdminUserSeeder` |
| Port 8000 in use | `php artisan serve --port=8080` |

---

## Project layout

```
Community-Food-Wastage-Reduction-Portal-v2/
├── app/                 # Controllers, models, services
├── public/              # CSS, JS, index.php
├── resources/views/     # Blade UI
├── routes/web.php       # Routes + API
├── Documentation/       # SRS, diagrams, checklists
└── .env
```

Stack: **Laravel + Blade + Bootstrap 5 + MySQL**.
