# Software Requirements Specification (SRS) — Version 2

## Community Food Wastage Reduction Portal

**Document version:** 2.0  
**Aligned with:** Laravel app at repo root, MySQL tables `users` and `food_listings`  
**Supervisor:** Hasnain Ahmed (hasnain@vu.edu.pk)

Diagrams for this SRS are defined in [`diagrams/`](diagrams/) and export to [`Documentation/images/v2/`](../../images/v2/).

---

## 1. Introduction

### 1.1 Purpose

This SRS describes what the **v2 portal** must do: connect **donors** who have surplus food with **recipients (NGOs)** who can pick it up before it expires, plus **admin reporting** on portal activity. It replaces the older SRS where the database had separate donations, claims, and volunteer tables — those remain **future work** unless the schema is migrated.

### 1.2 Problem statement

Urban donors often discard edible surplus food while nearby NGOs lack supply. Without a shared system, coordination is slow and food spoils. The portal lists available food near a location, lets one NGO claim each listing safely, and tracks status until the donor confirms delivery.

### 1.3 Scope

**In scope (implemented or specified for v2):**

- Registration and session login for **donor**, **recipient**, and **admin**
- Donors post **food listings** (title, category, quantity, expiry); pickup location from donor profile
- Recipients **search by GPS + radius** (Haversine) and **claim** one listing at a time (atomic update)
- Status workflow: **AVAILABLE → CLAIMED → DELIVERED**; **EXPIRED** for past best-before
- Blade UI for core flows; same URLs usable as JSON API (Postman / `/test-apis`)
- Admin **dashboard and reports** (date range, trends, CSV export)

**Planned / out of scope for v2 code (may appear in later releases):**

- Volunteer pickup tasks, disputes, NGO document verification, in-app notifications, feedback ratings, multi-item donation posts, listing edit/cancel UI, Firebase photo storage

See [implementation-status.md](../implementation-status.md) for a live checklist.

### 1.4 Definitions

| Term | Meaning |
| ---- | ------- |
| Listing | One row in `food_listings` — one claimable food offer |
| Donor | User with `role = donor` |
| Recipient / NGO | User with `role = recipient` |
| Admin | User with `role = admin` (not self-register; seeded or created by DBA) |
| Claim | Recipient reserves a listing; status becomes CLAIMED |
| Deliver | Donor confirms pickup; status becomes DELIVERED |

### 1.5 References

- Project brief: [project-requirements.md](../project-requirements.md)
- Assignment v2: [Community_Food_Wastage_Reduction_Portal_Assignment_v2.md](../../Community_Food_Wastage_Reduction_Portal_Assignment_v2.md)
- API behaviour: [core-workflows-api-and-data.md](../core-workflows-api-and-data.md)

---

## 2. Overall description

### 2.1 Product perspective

Web application: **Laravel 9+**, **Blade**, **Bootstrap 5**, **plain CSS/JS**, **MySQL**. Browser talks to `routes/web.php`; authenticated actions use Laravel session cookies. Optional JSON clients send `Accept: application/json`.

### 2.2 User classes

| Role | Description | Primary interfaces |
| ---- | ----------- | ------------------ |
| Donor | Restaurant, hotel, caterer, household | Donor dashboard, post food, my listings, mark delivered |
| Recipient | NGO, shelter, community kitchen | Search nearby, claim, my claims |
| Admin | Portal operator | Dashboard, reports, CSV export |
| System | Scheduler / cron | Hourly `food:expire` command |

### 2.3 Operating environment

- PHP 8.0+, Composer, MySQL 8.x
- Development: `php artisan serve`
- Production: Apache/Nginx + PHP-FPM (typical Laravel deployment)

### 2.4 Design constraints

- No SPA framework unless explicitly requested later
- Location search uses **stored lat/lng** and SQL distance (no mandatory Google Maps API in v2)
- CSRF exceptions on `/auth/*` and `/food*` for JSON testing; HTML forms still use `@csrf` where applicable

---

## 3. Functional requirements

Status: **Done** = in repo today; **Planned** = in SRS but not built; **N/A** = dropped for v2.

### 3.1 Authentication and users

| ID | Requirement | Status |
| -- | ----------- | ------ |
| FR-A01 | System shall allow donors and recipients to register with name, email, password, role, and optional profile fields (business/NGO name, phone, address, latitude, longitude). | Done |
| FR-A02 | System shall not allow public self-registration as admin. | Done |
| FR-A03 | System shall authenticate users via email/password and maintain a server session. | Done |
| FR-A04 | System shall allow logout and invalidate the session. | Done |
| FR-A05 | After login, system shall redirect users to a role home (donor dashboard, recipient search, or admin dashboard). | Done |
| FR-A06 | Admin shall be created via seeder or manual DB entry. | Done |

### 3.2 Food listings (donor)

| ID | Requirement | Status |
| -- | ----------- | ------ |
| FR-D01 | Donor shall create a listing with title, category (veg/non_veg), quantity, and expiry datetime in the future. | Done |
| FR-D02 | System shall copy pickup address and coordinates from the donor profile at post time. | Done |
| FR-D03 | System shall reject listing creation if donor has no latitude/longitude. | Done |
| FR-D04 | Donor shall view their listings (`GET /food/mine`) with status and claimer info. | Done |
| FR-D05 | Donor shall mark a **CLAIMED** listing as **DELIVERED**. | Done |
| FR-D06 | Donor shall cancel an available listing from the UI (withdrawn → EXPIRED). | Done |
| FR-D07 | Donor shall edit an available listing from the UI. | Planned |

### 3.3 Search and claim (recipient)

| ID | Requirement | Status |
| -- | ----------- | ------ |
| FR-R01 | Recipient shall search available listings by latitude, longitude, and optional radius (km). | Done |
| FR-R02 | Results shall exclude non-AVAILABLE and expired listings, sorted closest first. | Done |
| FR-R03 | Recipient shall claim a listing; system shall prevent double claim under concurrent requests. | Done |
| FR-R04 | Recipient shall view claimed listings and donor pickup details (`GET /food/claims`). | Done |
| FR-R05 | Recipient shall search by city name without coordinates. | Planned |

### 3.4 Admin and reporting

| ID | Requirement | Status |
| -- | ----------- | ------ |
| FR-M01 | Admin shall access dashboard and reports only when logged in as admin. | Done |
| FR-M02 | Admin shall filter reports by date range (with maximum range cap). | Done |
| FR-M03 | Admin shall view KPIs: listings posted, delivered, claim rate, user counts, status/category breakdown, daily trend, top areas, top donors/NGOs. | Done |
| FR-M04 | Admin shall export report summary as CSV. | Done |
| FR-M05 | Admin shall approve/block users and moderate listings. | Planned |

### 3.5 System automation

| ID | Requirement | Status |
| -- | ----------- | ------ |
| FR-S01 | Search and claim queries shall treat listings with `expiry_at <= now()` as unavailable. | Done |
| FR-S02 | System shall run a scheduled job to set stale AVAILABLE listings to EXPIRED. | Done |
| FR-S03 | System shall notify nearby NGOs when new food is posted. | Planned |

---

## 4. Non-functional requirements

| ID | Category | Requirement |
| -- | -------- | ----------- |
| NFR-01 | Security | Passwords stored with bcrypt; role checks on protected routes. |
| NFR-02 | Integrity | Claim uses a single conditional SQL update so two recipients cannot claim the same listing. |
| NFR-03 | Performance | Nearby search uses indexed filters on status/expiry and Haversine in SQL; admin report range capped (366 days). |
| NFR-04 | Usability | Responsive Bootstrap layout; clear labels on donor/recipient/admin screens. |
| NFR-05 | Maintainability | Thin controllers; report logic in `AdminReportService`; short comments on functions/queries. |
| NFR-06 | Compatibility | Modern browsers; session-based auth for HTML and API tester. |
| NFR-07 | Testability | Postman collection and `/test-apis` for endpoint verification. |

---

## 5. External interface requirements

### 5.1 User interfaces (HTML)

| Screen | Route |
| ------ | ----- |
| Home | `/` |
| Login / Register | `/login`, `/register` |
| Donor dashboard | `/donor/dashboard` |
| Post food | `/donor/donations/create` → `POST /food` |
| My listings | `/food/mine` |
| Recipient search | `/recipient/food` → `GET /food/search` |
| My claims | `/food/claims` |
| Admin dashboard | `/admin/dashboard` |
| Admin reports | `/admin/reports`, export `/admin/reports/export` |
| API tester | `/test-apis` |

### 5.2 API summary (same routes as above)

| Method | Path | Auth | Role |
| ------ | ---- | ---- | ---- |
| POST | `/auth/register` | No | donor/recipient |
| POST | `/auth/login` | No | any |
| POST | `/auth/logout` | Yes | any |
| POST | `/food` | Yes | donor |
| GET | `/food/mine` | Yes | donor |
| GET | `/food/search` | No* | recipient for HTML |
| POST | `/food/{id}/claim` | Yes | recipient |
| GET | `/food/claims` | Yes | recipient |
| POST | `/food/{id}/deliver` | Yes | donor |
| POST | `/food/{id}/cancel` | Yes | donor (AVAILABLE only) |

\*JSON search does not require auth; HTML search redirects non-recipients to login.

---

## 6. Data requirements (logical model)

### 6.1 Entity: User (`users`)

| Attribute | Type | Notes |
| --------- | ---- | ----- |
| id | PK | |
| name, email, password | | email unique |
| role | enum | `donor`, `recipient`, `admin` |
| business_name, phone | | donor-oriented |
| ngo_name, contact_person | | recipient-oriented |
| address, latitude, longitude | | shared; required for donor posting |
| timestamps | | |

### 6.2 Entity: Food listing (`food_listings`)

| Attribute | Type | Notes |
| --------- | ---- | ----- |
| id | PK | |
| donor_id | FK → users | |
| title, category, quantity | | category: veg, non_veg |
| pickup_address, latitude, longitude | | snapshot at create |
| expiry_at | datetime | best-before |
| status | enum | AVAILABLE, CLAIMED, DELIVERED, EXPIRED |
| claimed_by | FK → users, nullable | recipient |
| claimed_at, delivered_at | nullable | |
| timestamps | | |

**Relationships:** One donor has many listings. One listing has at most one claimer (recipient). See [diagrams/02-erd-database.md](diagrams/02-erd-database.md).

---

## 7. System models (diagrams)

| Figure | Topic | Spec file |
| ------ | ----- | --------- |
| 4.1 | Use cases | [01-use-case-diagram.md](diagrams/01-use-case-diagram.md) |
| 4.2 | ERD | [02-erd-database.md](diagrams/02-erd-database.md) |
| 4.3 | Architecture | [03-system-architecture.md](diagrams/03-system-architecture.md) |
| 4.4 | Listing states | [04-listing-state-diagram.md](diagrams/04-listing-state-diagram.md) |
| 4.5 | Auth sequence | [05-sequence-auth.md](diagrams/05-sequence-auth.md) |
| 4.6 | Post listing | [06-sequence-post-listing.md](diagrams/06-sequence-post-listing.md) |
| 4.7 | Search & claim | [07-sequence-search-claim.md](diagrams/07-sequence-search-claim.md) |
| 4.8 | Deliver & expire | [08-sequence-deliver-expire.md](diagrams/08-sequence-deliver-expire.md) |
| 4.9 | Admin reports | [09-sequence-admin-reports.md](diagrams/09-sequence-admin-reports.md) |
| 4.10 | End-to-end flow | [10-end-to-end-activity-flow.md](diagrams/10-end-to-end-activity-flow.md) |

Exported images (generated): [`Documentation/images/v2/`](../../images/v2/) — `figure-01-use-case.png` through `figure-10-end-to-end-flow.png`.

---

## 8. Requirement traceability (assignment v2)

| Assignment task | SRS IDs | Verification |
| ----------------- | ------- | -------------- |
| User signup & auth | FR-A01–A05 | Postman register/login |
| Food listing lifecycle | FR-D01–D05, FR-S01 | Post food, claim, deliver |
| Nearby search | FR-R01–R02 | GET `/food/search` with lat/lng/radius |
| Safe claiming | FR-R03, NFR-02 | Two parallel claim → one 409 |
| Expired food | FR-S01–S02 | Search after expiry; `food:expire` |

---

## 9. Appendix — technology stack (as built)

| Layer | Choice |
| ----- | ------ |
| Backend | PHP Laravel |
| UI | Blade + Bootstrap 5 + `public/css/app.css`, `public/js/app.js` |
| Database | MySQL |
| Auth | Session (Laravel default) |
| Maps (UI) | OpenStreetMap / Leaflet on forms where included; search uses coordinates |

---

## 10. Production & submission checklist

Master checklist (tick boxes maintained there): **[improvements-needed-checklist.md](../improvements-needed-checklist.md)**

### 10.1 Assignment v2 — ready to submit

| Status | Item |
| ------ | ---- |
| [x] | Auth + donor/recipient profiles with location |
| [x] | Post, search, claim, deliver lifecycle |
| [x] | Three technical challenges documented and testable |
| [x] | UI + Postman + `/test-apis` |
| [x] | SRS v2 + diagram PNGs in `Documentation/images/v2/` |

### 10.2 Production readiness (Phase 1 deploy)

| Status | Item |
| ------ | ---- |
| [x] | Install/deploy docs ([how-to-install-and-run.md](../how-to-install-and-run.md), [production-deploy.md](../production-deploy.md)) |
| [x] | Register validation: address + lat/lng + role profile fields |
| [x] | Auth POST rate limiting |
| [x] | HTTPS scheme when `APP_ENV=production` |
| [x] | Donor cancel AVAILABLE listing |
| [x] | Admin seeder + demo script |
| [ ] | PHPUnit feature tests for claim/search |
| [ ] | Staging run with `APP_DEBUG=false` |

### 10.3 Full supervisor brief — Phase 2

| Status | Item |
| ------ | ---- |
| [ ] | Notifications, feedback, volunteer role |
| [ ] | Photos, NGO verification, listing moderation |
| [ ] | City-based search without GPS |

See section **B** in [improvements-needed-checklist.md](../improvements-needed-checklist.md).

---

*End of SRS v2 main document.*
