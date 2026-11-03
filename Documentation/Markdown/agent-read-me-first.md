# Agent read-me-first (start of session)

Use this file at the beginning of a new chat so you know **which repo this is**, **what is actually running**, and **where the truth lives**.

---

## What this repository is

**Community Food Wastage Reduction Portal — v2** is a Laravel app at the **repo root** (not a `portal/` subfolder). It implements the **assignment v2 “core engine”**:

- Register / login (session auth)
- Donors post food listings
- Recipients search nearby (Haversine + radius)
- Safe atomic claim (no double-booking)
- Expiry handling (lazy filter + hourly `food:expire` command)

On top of that, there is a **thin Blade UI** for donor/recipient flows and a **browser API tester** at `/test-apis`. There is **no** admin, volunteer, notifications, disputes, or full SRS feature set in code yet.

**Full product vision** (donor + NGO + volunteer + admin, notifications, feedback, reports) lives in:

| Doc | Purpose |
| --- | --- |
| [project-requirements.md](project-requirements.md) | Supervisor brief / high-level FRs |
| [SRS-v2/00-index.md](SRS-v2/00-index.md) | **Current SRS** + per-diagram Mermaid specs (v2 DB) |
| [improvements-needed-checklist.md](improvements-needed-checklist.md) | Assignment vs brief vs **production checklist** |
| [SRS-sections.md](SRS-sections.md) | Original full-portal SRS (legacy design) |
| [Design-doc-sections.md](Design-doc-sections.md) | Planned ERD, controllers, screens |
| [Community_Food_Wastage_Reduction_Portal_Assignment_v2.md](../Community_Food_Wastage_Reduction_Portal_Assignment_v2.md) | What v2 code must deliver |

**README.md** at repo root describes the API; it is accurate for the engine. **how-to-install-and-run.md** and **ui-file-index.md** still mention `portal/` and `DemoData` — those are **out of date** for this v2 tree. Prefer the agent docs below and live code.

---

## Five docs for session context (read order)

1. **This file** — scope and pointers  
2. [repository-layout.md](repository-layout.md) — folders, controllers, views  
3. [implementation-status.md](implementation-status.md) — built vs planned  
4. [coding-rules-and-conventions.md](coding-rules-and-conventions.md) — how to write code here  
5. [core-workflows-api-and-data.md](core-workflows-api-and-data.md) — routes, DB, status flow, tricky bits  

Also keep [ui-file-index.md](ui-file-index.md) updated when you change Blade/CSS/JS/routes (paths should match repo root, not `portal/`).

---

## Run locally (quick)

```bash
cd D:/ashir/Community-Food-Wastage-Reduction-Portal-v2
composer install
cp .env.example .env
php artisan key:generate
# set DB_DATABASE=cfwrp_v2 (or similar) in .env
php artisan migrate
php artisan serve
```

- Site: `http://127.0.0.1:8000`  
- API tester: `http://127.0.0.1:8000/test-apis`  
- Postman: `Documentation/postman_collection.json`

---

## Roles in **running code** (not full SRS)

| Role in DB | `users.role` | Notes |
| --- | --- | --- |
| Donor | `donor` | Can `POST /food`, mark delivered |
| Recipient (NGO) | `recipient` | Can search, claim |
| Admin | `admin` | Reports at `/admin/dashboard`, `/admin/reports` (seed admin user separately) |
| Volunteer | — | Not implemented in v2 |

Naming: Blade folders use **`recipient`**; older design docs say **receiver** — same idea, different folder name.

---

## Architecture in one sentence

**GET** pages → `PortalController` / `HomeController` / some **GET** on `FoodListingController` for HTML lists. **POST** (and JSON **GET** search) → `AuthController`, `FoodListingController`, `ClaimController`, with **`HandlesPortalResponses`** so the same action returns JSON (Postman) or redirect + flash (forms).
