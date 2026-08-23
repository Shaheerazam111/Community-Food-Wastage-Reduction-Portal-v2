# Windows Setup (XAMPP)

Laravel 9 API — run locally with **XAMPP + Composer**. No Docker needed.

## 1. Install XAMPP

1. Download: https://www.apachefriends.org/ (PHP 8.1+ build)
2. Install to `C:\xampp`
3. Open **XAMPP Control Panel** → **Start** MySQL

Default login: `root` / no password.

## 2. Add PHP to PATH

1. Win + S → **Environment Variables**
2. User **Path** → **New** → `C:\xampp\php`
3. New terminal → `php --version`

## 3. Enable PHP extensions

Edit `C:\xampp\php\php.ini` — uncomment:

```ini
extension=bcmath
extension=curl
extension=fileinfo
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
```

Verify: `php -m`

## 4. Install Composer

1. Download **Composer-Setup.exe**: https://getcomposer.org/download/
2. Point it to `C:\xampp\php\php.exe`
3. Verify: `composer --version`

## 5. Create database

**phpMyAdmin:** Start Apache + MySQL → http://localhost/phpmyadmin → New → `cfwrp_v2`

**Or CMD:**

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE cfwrp_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 6. Project setup

```cmd
cd C:\path\to\Community-Food-Wastage-Reduction-Portal-v2
copy .env.example .env
```

Edit `.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cfwrp_v2
DB_USERNAME=root
DB_PASSWORD=
```

Then:

```cmd
composer install
php artisan key:generate
php artisan migrate
```

## 7. Run

MySQL running in XAMPP → then:

```cmd
php artisan serve
```

Open: **http://127.0.0.1:8000**

## 8. Test with Postman

1. Import `Documentation\postman_collection.json`
2. Set `base_url` = `http://127.0.0.1:8000`
3. Follow `Documentation\flow-test.md`

## Troubleshooting

| Problem | Fix |
|---|---|
| `php` not recognized | Add `C:\xampp\php` to PATH, new terminal |
| Access denied (1045) | `.env`: `root` + empty password (XAMPP default) |
| Connection refused | Start MySQL in XAMPP |
| could not find driver | Enable `pdo_mysql` in php.ini |
| Port 8000 in use | `php artisan serve --port=8001` |
| No encryption key | `php artisan key:generate` |

## Daily workflow

```cmd
:: XAMPP → Start MySQL
cd C:\path\to\Community-Food-Wastage-Reduction-Portal-v2
php artisan serve
```
