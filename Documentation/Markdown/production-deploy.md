# Production deployment

Short guide to run the **Community Food Wastage Reduction Portal v2** on a real server (not `php artisan serve`).

---

## 1. Server requirements

- PHP 8.0+ with extensions: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer 2.x
- MySQL 8.x
- Nginx or Apache + PHP-FPM

---

## 2. First-time setup

```bash
cd /var/www/cfwrp   # your deploy path
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

Edit `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_DATABASE=cfwrp_v2
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

Run migrations and optional admin seed:

```bash
php artisan migrate --force
SEED_ADMIN=true php artisan db:seed --class=AdminUserSeeder
```

Change the default admin password after first login (or edit the seeder before deploy).

---

## 3. Web root

Point the vhost **document root** to `public/`, not the repo root.

Nginx should pass PHP to FPM; Laravel’s `public/index.php` is the front controller.

---

## 4. Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

After `.env` changes, run `php artisan config:clear` (or re-cache).

---

## 5. Scheduler (expired food)

The app marks stale AVAILABLE listings as EXPIRED every hour.

Add one cron entry for the **deploy user**:

```cron
* * * * * cd /var/www/cfwrp && php artisan schedule:run >> /dev/null 2>&1
```

Test manually:

```bash
php artisan food:expire
```

---

## 6. Permissions

Ensure `storage/` and `bootstrap/cache/` are writable by the web server user.

---

## 7. Security checklist

- `APP_DEBUG=false` in production  
- HTTPS certificate installed; `APP_URL` uses `https://`  
- Strong DB password; admin not using default `admin123`  
- Firewall: only 80/443 public  
- Do not commit `.env`  

---

## 8. Rollback

Keep database backups before migrations. To roll back one migration batch:

```bash
php artisan migrate:rollback --force
```

---

See also: [how-to-install-and-run.md](how-to-install-and-run.md) (local dev), [improvements-needed-checklist.md](improvements-needed-checklist.md).
