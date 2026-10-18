# How to Install and Run

Steps to run the **Community Food Wastage Reduction Portal** on your machine. The app uses **MySQL** for users, listings, claims, notifications, and reports.

---

## What you need

| Tool | Version (approx.) | Why |
| ---- | ----------------- | --- |
| PHP | 8.0+ | Laravel |
| Composer | 2.x | PHP packages |
| MySQL | 8.x | Database |
| Git | any recent | Clone the repo |
| Browser | Chrome / Edge / Firefox | Use the portal |

**Optional:** XAMPP, Laragon, or WAMP on Windows (PHP + MySQL together).

Check versions:

```bash
php -v
composer -V
mysql --version
```

---

## 1. Get the project

```bash
git clone <your-repo-url> food-portal
cd food-portal
```

If you already have the folder, open a terminal in the project root (where `artisan` lives).

---

## 2. Install PHP packages

From the project root:

```bash
composer install
```

Skip this if `vendor/` is already present and you did not change `composer.json`.

---

## 3. Environment and database

Copy the example env file and generate an app key:

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=food_portal
DB_USERNAME=root
DB_PASSWORD=
```

Create the database (phpMyAdmin or CLI):

```sql
CREATE DATABASE food_portal;
```

Run migrations and seed demo users + sample listings:

```bash
php artisan migrate --seed
```

Or reset everything:

```bash
php artisan migrate:fresh --seed
```

Link public storage so listing photos and verification documents can be viewed:

```bash
php artisan storage:link
```

More sample data notes: [demo-test-data.md](demo-test-data.md).

---

## 4. Start the server

From the project root:

```bash
php artisan serve
```

Open **http://127.0.0.1:8000**

Stop the server with `Ctrl + C`.

**Expired listings:** the scheduler marks past-due `AVAILABLE` listings as `EXPIRED` hourly. For local dev you can run once manually:

```bash
php artisan food:expire
```

To run the scheduler in the background (optional):

```bash
php artisan schedule:work
```

---

## 5. Demo logins

Password for all seeded accounts: **`password`**

| Email | Role | Notes |
| ----- | ---- | ----- |
| `admin@food.test` | Admin | Verify users, categories, reports |
| `donor@food.test` | Donor (Lahore) | Post and manage food |
| `ngo@food.test` | Recipient (Lahore) | Search, claim, rate |
| `ngo.pending@food.test` | Recipient | Not verified — cannot post or claim |

After login you are sent to the dashboard for your role.

| Role | Start here |
| ---- | ---------- |
| Donor | `/donor/dashboard` — post food, my listings, profile |
| Recipient (NGO) | `/recipient/dashboard` — find food, my claims, profile |
| Admin | `/admin/dashboard` — users, categories, reports |

Screen → file mapping: [ui-file-index.md](ui-file-index.md).

---

## 6. Full flow to try in the browser

1. Register a new donor or NGO (or use demo accounts above).
2. Upload a verification document on **My profile**.
3. Log in as **admin** → **Users** → **Approve** the new account.
4. As donor → **Post food** (photo, quantity, map pin).
5. As NGO in the same city → check **Alerts** / **Find food** → **Claim**.
6. Either party → **Mark as delivered** → NGO leaves a **rating**.
7. Admin → **Reports** for city totals.

---

## Common problems

### “No application encryption key”

```bash
php artisan key:generate
```

### Port 8000 already in use

```bash
php artisan serve --port=8080
```

Then open **http://127.0.0.1:8080**

### Database connection error

- Confirm MySQL is running.
- Check `DB_*` values in `.env`.
- Create the database if it does not exist.

### Photos or documents 404

```bash
php artisan storage:link
```

### Map tiles stay grey

Find food and post/edit forms need internet for OpenStreetMap (Leaflet CDN).

### Running tests

Feature tests use `RefreshDatabase` and need a **separate** test database. Copy `.env.testing.example` to `.env.testing` and run:

```bash
php artisan test
```

Do not point tests at your dev database if you want to keep seeded demo data.

---

## Project layout (short)

```
food-portal/
├── app/                   # Controllers, models, middleware
├── public/                # css, js (web root for artisan serve)
├── resources/views/       # Blade screens
├── routes/web.php         # Portal routes
├── database/              # Migrations, seeders
├── Documentation/         # Requirements, this guide
└── .env
```

Stack: **Laravel + Blade + Bootstrap 5 + plain CSS/JS + MySQL + Leaflet (OpenStreetMap)**.

---

## Commands cheat sheet

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```
