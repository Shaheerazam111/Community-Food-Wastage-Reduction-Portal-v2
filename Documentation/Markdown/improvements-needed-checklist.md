# Improvements & production readiness checklist

Based on [project-requirements.md](project-requirements.md) and [Community_Food_Wastage_Reduction_Portal_Assignment_v2.md](../Community_Food_Wastage_Reduction_Portal_Assignment_v2.md).

**Legend:** `[x]` done · `[ ]` not done  

**Resolution log:** [issues-resolved-log.md](issues-resolved-log.md)

Mirror: [SRS-v2/SRS-v2-main.md](SRS-v2/SRS-v2-main.md#10-production--submission-checklist)

---

## A. Assignment v2 — core engine

| Status | Item |
| ------ | ---- |
| [x] | Tasks 1–4 + three challenges + UI + admin reports + SRS v2 |

---

## B. Supervisor brief — full portal

| Status | Requirement |
| ------ | ----------- |
| [x] | Donors + recipients + admin roles |
| [x] | **Volunteer** role: pickup task list + mark complete → DELIVERED |
| [x] | Food listing fields + best-before |
| [x] | **Photos** on listings (local storage, optional upload) |
| [x] | Search by **GPS + radius** |
| [x] | Search by **city name** (`?city=`) |
| [x] | Safe claim + status workflow |
| [x] | **Dashboard notifications** (new food nearby, claimed, delivered) |
| [x] | **Feedback & rating** after delivery (My claims) |
| [x] | NGO **verification documents** + admin document review |
| [x] | Admin **block/unblock users** (`/admin/users`) |
| [x] | Admin **moderate listings** (approve/reject before live) |
| [x] | Admin reports + CSV |
| [x] | Donor **meals saved (est.)** on dashboard |
| [x] | **Alerts** nav + unread count |
| [ ] | Google Maps API (OSM/Leaflet used instead) |

---

## C. Production & operations

| Status | Item |
| ------ | ---- |
| [x] | Install / deploy docs |
| [x] | Register validation + rate limit + HTTPS in production |
| [x] | Cancel AVAILABLE listings |
| [x] | **PHPUnit** feature tests (search + claim race) |
| [x] | **Drop legacy `food` table** |
| [ ] | `APP_DEBUG=false` verified on staging (manual) |
| [ ] | Email SMTP for notifications |
| [ ] | DB backup schedule (server) |
| [ ] | Error monitoring (optional) |

---

## D. Documentation & demo

| Status | Item |
| ------ | ---- |
| [x] | README, demo script, SRS v2, ui-file-index |
| [ ] | Screen recording / screenshot appendix (your submission) |

---

## E. Before submission

1. `php artisan migrate`  
2. `php artisan db:seed --class=UserSeeder`  
3. `php artisan storage:link` (for food photos)  
4. Run [demo-and-viva-script.md](demo-and-viva-script.md)  
5. `php artisan test`
