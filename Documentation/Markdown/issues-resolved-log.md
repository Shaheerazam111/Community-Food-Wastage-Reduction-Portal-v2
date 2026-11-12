# Issues resolved log

Track of checklist items closed in the **Phase 2 / production** pass. Update when fixing gaps from [improvements-needed-checklist.md](improvements-needed-checklist.md).

| Date | ID | Issue | Resolution | Verified by |
| ---- | -- | ----- | ---------- | ----------- |
| 2026-11-03 | C-01 | No automated PHPUnit for claim/search | Added `FoodSearchTest`, `FoodClaimTest`; SQLite in `phpunit.xml` | Subagent + `php artisan test` |
| 2026-11-03 | C-02 | Legacy `food` table unused | Migration `2026_11_03_000001_drop_legacy_food_table` | migrate |
| 2026-11-03 | B-01 | No dashboard notifications | `notifications` table, `NotificationService`, `/notifications`, alerts on claim/post/deliver | Manual demo |
| 2026-11-03 | B-02 | No feedback after pickup | `listing_feedback`, POST `/food/{id}/feedback` on My claims | Manual demo |
| 2026-11-03 | B-03 | No city search | GET `/food/search?city=` filters address/city | Manual demo |
| 2026-11-03 | B-04 | No listing photos | Optional `photo` upload → `storage/app/public/food-photos` | Manual + `storage:link` |
| 2026-11-03 | B-05 | No volunteer role | `volunteer` role, `/volunteer/tasks`, complete → DELIVERED | `VolunteerUserSeeder` |
| 2026-11-03 | B-06 | No admin user block | `users.is_active`, `/admin/users`, login block | Manual demo |
| 2026-11-03 | B-07 | No meals saved metric | Donor dashboard `meals_saved` via `MealsEstimate` | UI |
| 2026-11-03 | B-08 | Volunteer in brief unchecked | Volunteer tasks for CLAIMED listings | See B-05 |

| 2026-11-03 | B-09 | NGO document upload + admin review | Register upload, `/admin/verification`, private storage download | Manual demo |
| 2026-11-03 | B-10 | Listing moderation before publish | `moderation_status` PENDING → admin `/admin/listings` → APPROVED in search | Manual demo |

## Still open (not in this pass)

- Email SMTP notifications (dashboard only today)  
- Google Maps API (OSM/Leaflet kept)  
- PHPUnit beyond search/claim  
- Screen recording for submission (student artifact)  
- Staging sign-off with `APP_DEBUG=false` (manual)  
- MySQL backup cron (server ops)  

See checklist section **B** and **C** for full list.
