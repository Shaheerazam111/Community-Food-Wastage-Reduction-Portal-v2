# Local Setup (Linux / macOS)

Laravel 9 API — PHP, Composer, MySQL. Test with curl or Postman.

**Windows?** See [setup-windows.md](setup-windows.md).

## Prerequisites

- PHP 8.0.2+
- Composer 2.x
- MySQL or MariaDB
- Extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`

```bash
php --version && composer --version && mysql --version
```

## Install tools

**Composer:**

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**PHP (Ubuntu/Debian):**

```bash
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip
```

**MariaDB:**

```bash
sudo apt install mariadb-server
sudo systemctl enable --now mariadb
```

## Database

**Docker (if local MySQL password is unknown):**

```bash
docker run -d --name cfwrp-v2-mysql \
  -e MYSQL_ROOT_PASSWORD=secret \
  -e MYSQL_DATABASE=cfwrp_v2 \
  -p 3307:3306 mysql:8.0
```

`.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=cfwrp_v2
DB_USERNAME=root
DB_PASSWORD=secret
```

**phpMyAdmin (XAMPP / LAMP / local MySQL):**

1. In phpMyAdmin, create database **`cfwrp_v2`** (collation `utf8mb4_unicode_ci`).
2. In `.env` set:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cfwrp_v2
DB_USERNAME=root
DB_PASSWORD=your_mysql_root_password
```

Use the same **username/password** you use to log into phpMyAdmin. If root has no password, leave `DB_PASSWORD=` empty.

3. Clear config cache and migrate:

```bash
php artisan config:clear
php artisan migrate
```

**Local MySQL (CLI):**

```bash
mysql -u root -p -e "CREATE DATABASE cfwrp_v2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

`.env`: port `3306`, your password.

## Project setup

```bash
cd Community-Food-Wastage-Reduction-Portal-v2
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
```

Demo users and sample listings for testing search/claim: [Documentation/Markdown/demo-test-data.md](Documentation/Markdown/demo-test-data.md).

## Run

```bash
php artisan serve
```

→ http://127.0.0.1:8000

Optional scheduler: `php artisan schedule:work` or `php artisan food:expire`

## Verify

```bash
curl http://127.0.0.1:8000/
curl -X POST http://127.0.0.1:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123","role":"donor","latitude":24.86,"longitude":67.00}'
```

## API endpoints

| Method | URL | Auth |
|---|---|---|
| POST | `/auth/register` | - |
| POST | `/auth/login` | - |
| POST | `/auth/logout` | yes |
| POST | `/food` | donor |
| GET | `/food/mine` | donor |
| GET | `/food/search?latitude=&longitude=&radius_km=` | - |
| POST | `/food/{id}/claim` | recipient |
| POST | `/food/{id}/deliver` | donor |

Postman: `Documentation/postman_collection.json` — see `Documentation/flow-test.md`.

## Troubleshooting

- **1045 Access denied** — fix `DB_PASSWORD` in `.env`
- **2002 Connection refused** — start MySQL or Docker container
- **No encryption key** — `php artisan key:generate`
- **Port 8000 in use** — `php artisan serve --port=8001`
- **storage permissions** — `chmod -R ug+rwx storage bootstrap/cache`

## Daily workflow

```bash
docker start cfwrp-v2-mysql   # if using Docker
cd Community-Food-Wastage-Reduction-Portal-v2
php artisan serve
```
