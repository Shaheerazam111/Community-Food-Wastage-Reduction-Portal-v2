# Demo & viva script (≈5 minutes)

Use this when showing the project to your teacher. Assumes a **fresh MySQL database**.

---

## What to say about scope (30 seconds)

> This submission implements **assignment v2**: auth, post food, nearby search, safe claim, expiry handling, plus a Blade UI and admin reports. The full supervisor brief also mentions volunteers, notifications, and feedback — those are **Phase 2** and documented in our improvements checklist.

Point to: [improvements-needed-checklist.md](improvements-needed-checklist.md) and [SRS-v2/SRS-v2-main.md](SRS-v2/SRS-v2-main.md).

---

## Setup before class (you do this at home)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Create DB cfwrp_v2 in MySQL, set .env
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan serve
```

Open `http://127.0.0.1:8000`

---

## Live demo steps

| Step | Who | Action | What teacher should see |
| ---- | --- | ------ | ------------------------ |
| 1 | Donor | Register as **Donor** — fill business name, phone, address, **click map pin** | Account created → donor dashboard |
| 2 | Donor | **Post food** — title, veg/non-veg, quantity, expiry in future | Pending admin moderation |
| 2b | Admin | **/admin/listings** → Approve | Listing visible in search |
| 3 | NGO | Logout → Register as **Recipient** — NGO name, contact, **verification PDF/image**, map pin | Pending verification message |
| 3b | Admin | Approve NGO at **/admin/verification** | Recipient can claim after approve |
| 4 | NGO | **Search** (default coords from profile) | Listing appears, distance shown |
| 5 | NGO | **Claim** | Success → My claims; status CLAIMED |
| 6 | Donor | Login as donor → **My listings** → **Mark delivered** | Status DELIVERED |
| 7 | Optional | Open second browser/incognito as another NGO → claim same item | **409** / error (Challenge 2) — use Postman or `/test-apis` if time is short |
| 8 | Admin | Login `admin@foodportal.test` / `admin123` (change in production) | `/admin/reports` shows counts and trend |

---

## Three challenges (one sentence each)

1. **Distance** — Haversine in SQL, sorted by km.  
2. **Double claim** — one `UPDATE … WHERE status = AVAILABLE`.  
3. **Expiry** — filter in search/claim + hourly `food:expire`.

---

## If something breaks

| Problem | Fix |
| ------- | --- |
| Donor cannot post food | Register again with **map pin** set (lat/lng required) |
| Search empty | Donor and NGO pins must be within radius; listing not expired |
| Demo login fails | Run `php artisan db:seed --class=UserSeeder` |

---

## Artifacts to submit

- Source code  
- [SRS-v2](SRS-v2/SRS-v2-main.md) + `Documentation/images/v2/*.png`  
- Postman: `Documentation/postman_collection.json`  
- UI screenshots: [`Documentation/images/demo-screenshots/`](../images/demo-screenshots/) (see `README.md` there for file names vs demo steps)

## Screenshot folder (report / PPT)

Pre-captured PNGs live in **`Documentation/images/demo-screenshots/`** — home, login, register, donor, NGO search/claims, admin reports/moderation, and `/test-apis`. Use them in the Word **Appendix** and the presentation **User Interfaces** slide. Re-capture after big UI changes while following the table in `demo-screenshots/README.md`.
