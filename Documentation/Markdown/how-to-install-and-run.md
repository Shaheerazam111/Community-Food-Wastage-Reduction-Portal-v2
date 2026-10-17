# How to Install and Run

Simple steps to get the **Community Food Wastage Reduction Portal** running on your machine.

Right now the screens use **dummy data** (`DemoData`), so you can open every page without a working MySQL database. MySQL is still listed below for when we wire up real tables later.

---

## What you need

| Tool | Version (approx.) | Why |
| ---- | ----------------- | --- |
| PHP | 8.0+ | Laravel app |
| Composer | 2.x | PHP packages |
| MySQL | 8.x (optional for UI demo) | Database later |
| Git | any recent | Clone the repo |
| Browser | Chrome / Edge / Firefox | View the site |

**Optional:** XAMPP / Laragon / WAMP already give you PHP + MySQL on Windows.

Check versions:

```bash
php -v
composer -V
```

---

## 1. Get the project

```bash
git clone <your-repo-url> ashir
cd ashir
```

If you already have the folder, just open a terminal in `ashir`.

---

## 2. Install PHP packages

The Laravel app lives in `portal/`.

```bash
cd portal
composer install
```

If `vendor/` is already there and you did not change `composer.json`, you can skip this.

---

## 3. Environment file

```bash
# From portal/
cp .env.example .env
```

On Windows (Git Bash / PowerShell):

```bash
cp .env.example .env
```

Generate the app key (needed once):

```bash
php artisan key:generate
```

### Database settings (optional for UI demo)

Open `portal/.env` and set MySQL if you use it:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=food_portal
DB_USERNAME=root
DB_PASSWORD=
```

Create the empty database in MySQL (phpMyAdmin or CLI):

```sql
CREATE DATABASE food_portal;
```

Then run migrations (only when you start using real tables):

```bash
php artisan migrate
```

> **UI demo note:** Browse / claim / admin screens read from `app/Support/DemoData.php`, not from MySQL. You can run the site even if DB is not set up yet.

---

## 4. Start the server

From `portal/`:

```bash
php artisan serve
```

Open in the browser:

**http://127.0.0.1:8000**

Stop the server with `Ctrl + C` in that terminal.

---

## 5. Try the demo UI

1. Go to **http://127.0.0.1:8000**
2. Click **Login**
3. Use a **demo button** (no real password):
   - Continue as Donor
   - Continue as NGO
   - Continue as Volunteer
   - Continue as Admin
   - Pending NGO (shows “account not active yet”)

Useful paths after login:

| Role | Start here |
| ---- | ---------- |
| Donor | `/donor/dashboard` — post donation, map pin, my donations |
| NGO | `/receiver/dashboard` — browse food (list / map), claims |
| Volunteer | `/volunteer/tasks` — task detail + route maps |
| Admin | `/admin/dashboard` — users, moderate, disputes, reports |

More screen → file mapping: [ui-file-index.md](ui-file-index.md)

---

## Common problems

### “No application encryption key”

```bash
cd portal
php artisan key:generate
```

### Port 8000 already in use

```bash
php artisan serve --port=8080
```

Then open **http://127.0.0.1:8080**

### `composer install` fails

- Make sure PHP 8.0+ is installed and on your PATH.
- On Windows, restart the terminal after installing PHP / Composer.

### Blank page or CSS missing

- Confirm you started the server from `portal/` (not the repo root).
- Hard refresh the browser (`Ctrl + F5`).

### Map does not load

- Post / Edit donation needs internet for OpenStreetMap tiles (Leaflet CDN).
- Check your network / firewall if the map area stays grey.

---

## Project layout (short)

```
ashir/
├── docs/                  # Design docs, diagrams, this guide
│   └── Markdown/
└── portal/                # Laravel + Blade UI
    ├── app/
    ├── public/            # css, js
    ├── resources/views/   # Blade screens
    ├── routes/web.php
    └── .env
```

Stack: **Laravel + Blade + Bootstrap 5 + plain CSS/JS + MySQL** (MySQL when backend is connected).

---

## Commands cheat sheet

```bash
cd portal
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

That is enough to run the current UI demo.
