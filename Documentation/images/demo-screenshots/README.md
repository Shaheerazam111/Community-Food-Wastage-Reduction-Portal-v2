# Demo flow screenshots

Ordered PNGs for the **live demo table** in [demo-and-viva-script.md](../../Markdown/demo-and-viva-script.md).

## How to generate (Selenium + Chrome — same as `download_fb_gallery_images.py`)

1. Start the app:
   ```bash
   php artisan db:seed --class=UserSeeder
   php artisan serve
   ```
2. Install Python deps once:
   ```bash
   pip install selenium webdriver-manager
   ```
   (Uses installed Google Chrome + `ChromeDriverManager` — no Playwright download.)
3. Run the capture script:
   ```bash
   python Deliverables/Demo/capture_demo_flow.py
   ```
   Set `HEADLESS = False` in the script if you want to watch the browser.

Output files are named **`step-00` … `step-14`** in this folder. Each step follows the viva script (register donor → post food → admin moderate → register NGO → admin verify → search → claim → deliver → reports).

Old loose `01-home.png` style files can be deleted after you have the `step-*` set.

## Step file map

| File | Demo script |
|------|-------------|
| `step-00-home.png` | Setup / intro |
| `step-01-donor-register-filled.png` | Step 1 — register donor + map pin |
| `step-01b-donor-dashboard.png` | Step 1 — after login |
| `step-02-donor-post-food-form.png` | Step 2 — post food |
| `step-03-donor-after-post-listing.png` | Step 2 — my listings |
| `step-04-admin-listings-pending.png` | Step 2b — moderation queue |
| `step-05-admin-listing-approved.png` | Step 2b — after approve |
| `step-06-ngo-register-filled.png` | Step 3 — NGO register + doc |
| `step-07-ngo-verification-pending.png` | Step 3 — pending message |
| `step-08-admin-ngo-docs-queue.png` | Step 3b — admin queue |
| `step-09-admin-ngo-approved.png` | Step 3b — approved |
| `step-10-ngo-search-results.png` | Step 4 — search + distance |
| `step-11-ngo-my-claims-claimed.png` | Step 5 — claim |
| `step-12-donor-mark-delivered.png` | Step 6 — DELIVERED |
| `step-13-admin-reports.png` | Step 8 — reports |
| `step-14-test-apis-overview.png` | Step 7 (optional) |

Admin user: `admin@foodportal.test` / `admin123` (from `UserSeeder`).
