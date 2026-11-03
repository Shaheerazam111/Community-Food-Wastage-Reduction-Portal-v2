# Improvements & production readiness checklist

Based on [project-requirements.md](project-requirements.md) (supervisor brief) and [Community_Food_Wastage_Reduction_Portal_Assignment_v2.md](../Community_Food_Wastage_Reduction_Portal_Assignment_v2.md) (prototype assignment).

**Legend:** `[x]` done in repo · `[ ]` not done (Phase 2 or optional)

Update this file when you ship a item. Mirror summary in [SRS-v2/SRS-v2-main.md](SRS-v2/SRS-v2-main.md#10-production--submission-checklist).

---

## A. Assignment v2 — core engine (submission baseline)

| Status | Item |
| ------ | ---- |
| [x] | **Task 1** — Register/login/logout with session auth |
| [x] | **Task 1** — Donor profile fields (business, phone, address, lat/lng) |
| [x] | **Task 1** — Recipient profile fields (NGO, contact, address, lat/lng) |
| [x] | **Task 2** — Post food listing (title, category, quantity, expiry) |
| [x] | **Task 2** — Status flow AVAILABLE → CLAIMED → DELIVERED |
| [x] | **Task 3** — Nearby search with radius, closest first |
| [x] | **Task 3** — Hide claimed and expired from search |
| [x] | **Task 4** — Atomic claim (no double booking) |
| [x] | **Challenge 1** — Haversine distance in SQL |
| [x] | **Challenge 2** — Conditional UPDATE on claim |
| [x] | **Challenge 3** — Lazy expiry filter + `food:expire` schedule |
| [x] | Postman collection + `/test-apis` for demo |
| [x] | Blade UI for donor / recipient core paths |
| [x] | Admin reports (beyond assignment minimum) |
| [x] | SRS v2 + Mermaid diagram PNGs |

---

## B. Supervisor brief — full portal (Phase 2)

From [project-requirements.md](project-requirements.md). Not required for assignment v2 pass; needed only if teacher expects the **full** brief.

| Status | Requirement |
| ------ | ----------- |
| [x] | Donors + recipients + admin roles (volunteer as separate login — see below) |
| [ ] | **Volunteer** role: pickup tasks and status updates |
| [x] | Food listing: name, category, quantity, best-before |
| [ ] | **Photos** on listings (Firebase or local storage) |
| [x] | Search by **geographic location** (GPS + radius) |
| [ ] | Search by **city name** without coordinates |
| [x] | Claim prevents multiple NGOs on same item |
| [x] | Status Available → Claimed → Delivered |
| [ ] | **Notification engine** (email or dashboard) for new nearby food |
| [ ] | **Feedback & rating** after pickup |
| [ ] | NGO **verification documents** + admin approve |
| [ ] | Admin **user verification** (block/approve NGOs) |
| [ ] | Admin **moderate listings** (approve/reject before live) |
| [x] | **Admin reports** (analytics; CSV export) |
| [ ] | Donor dashboard **“meals saved”** metric from quantity parsing |
| [ ] | **Real-time alerts** UI for donors/recipients |
| [ ] | Google Maps API (optional; OSM/Leaflet used today) |

---

## C. Production & operations readiness

| Status | Item | Notes |
| ------ | ---- | ----- |
| [x] | MySQL required; migrations documented | `cfwrp_v2` |
| [x] | `.env.example` with DB + production hints | |
| [x] | Install guide matches **repo root** (not `portal/`) | [how-to-install-and-run.md](how-to-install-and-run.md) |
| [x] | [production-deploy.md](production-deploy.md) — deploy, cron, cache | |
| [x] | Register **requires** address + map pin (lat/lng) | Server + client validation |
| [x] | Role-specific profile fields required on register | Matches assignment profiles |
| [x] | Rate limit on `/auth/*` POST | Brute-force mitigation |
| [x] | HTTPS URL forcing when `APP_ENV=production` | `AppServiceProvider` |
| [x] | Secure session cookie flag documented for HTTPS | `.env` comment |
| [x] | Donor can **withdraw** AVAILABLE listing | `POST /food/{id}/cancel` → EXPIRED |
| [x] | Admin user via `AdminUserSeeder` / `SEED_ADMIN` | Not in public register |
| [x] | Scheduler documented for `food:expire` | Cron every minute → `schedule:run` |
| [ ] | Automated tests (PHPUnit feature tests for claim/search) | Recommended |
| [ ] | `APP_DEBUG=false` verified on staging | Manual before go-live |
| [ ] | Remove unused `food` table migration | Cleanup |
| [ ] | Email (SMTP) for notifications | Phase 2 |
| [ ] | Backups (MySQL dump schedule) | Server ops |
| [ ] | Error monitoring (Sentry etc.) | Optional |

---

## D. Documentation & teacher demo

| Status | Item |
| ------ | ---- |
| [x] | README reflects UI + admin + endpoints |
| [x] | [demo-and-viva-script.md](demo-and-viva-script.md) — 5‑minute demo |
| [x] | [SRS-v2](SRS-v2/SRS-v2-main.md) aligned with code |
| [x] | [ui-file-index.md](ui-file-index.md) paths at repo root |
| [x] | Scope statement for viva (Phase 1 vs full brief) | In demo script |
| [ ] | Screen recording or screenshot appendix | Your submission |

---

## E. Suggested order before submission

1. Run through [demo-and-viva-script.md](demo-and-viva-script.md) on a clean DB.  
2. Tick section **A** — all should be `[x]`.  
3. For viva, explain section **B** as **Phase 2** unless teacher confirms full brief.  
4. Section **C** — complete all `[x]`; plan `[ ]` after submission if needed.

---

*Last aligned with codebase: production pass (register validation, deploy docs, cancel listing, throttling).*
