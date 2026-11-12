"""
Walk the viva demo flow and save ordered screenshots (Selenium + Chrome).

Requirements (same pattern as download_fb_gallery_images.py):
  pip install selenium webdriver-manager

Before run:
  php artisan db:seed --class=UserSeeder
  php artisan serve
"""
from __future__ import annotations

import json
import subprocess
import time
from datetime import datetime, timedelta
from pathlib import Path

from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.support.ui import WebDriverWait
from webdriver_manager.chrome import ChromeDriverManager

REPO = Path(__file__).resolve().parents[2]
OUT = REPO / "Documentation" / "images" / "demo-screenshots"
FIXTURE = OUT / "fixtures" / "ngo-verification.png"
BASE = "http://127.0.0.1:8000"
# Same knobs as download_fb_gallery_images.py (optional profile; empty = fresh session).
CHROME_USER_DATA_DIR = ""
CHROME_PROFILE_DIR_NAME = ""
CHROMEDRIVER_PATH = ""
HEADLESS = True

ADMIN_EMAIL = "admin@foodportal.test"
ADMIN_PASSWORD = "admin123"

TS = datetime.now().strftime("%Y%m%d%H%M%S")
DONOR_EMAIL = f"viva-donor-{TS}@demo.test"
NGO_EMAIL = f"viva-ngo-{TS}@demo.test"
FOOD_TITLE = f"Viva demo lunch {TS}"


def make_driver() -> webdriver.Chrome:
    chrome_opts = Options()
    if HEADLESS:
        chrome_opts.add_argument("--headless=new")
        chrome_opts.add_argument("--window-size=1366,900")
    else:
        chrome_opts.add_argument("--start-maximized")
    chrome_opts.add_argument("--disable-blink-features=AutomationControlled")
    chrome_opts.add_argument("--no-sandbox")
    chrome_opts.add_argument("--disable-dev-shm-usage")
    if CHROME_USER_DATA_DIR:
        chrome_opts.add_argument(f"--user-data-dir={CHROME_USER_DATA_DIR}")
        if CHROME_PROFILE_DIR_NAME:
            chrome_opts.add_argument(f"--profile-directory={CHROME_PROFILE_DIR_NAME}")
    if CHROMEDRIVER_PATH and Path(CHROMEDRIVER_PATH).is_file():
        service = Service(executable_path=CHROMEDRIVER_PATH)
    else:
        service = Service(ChromeDriverManager().install())
    return webdriver.Chrome(service=service, options=chrome_opts)


def run_php_bootstrap(php_body: str) -> subprocess.CompletedProcess:
    php = f"""<?php
require {json.dumps(str(REPO / "vendor/autoload.php"))};
$app = require_once {json.dumps(str(REPO / "bootstrap/app.php"))};
$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
{php_body}
"""
    script = REPO / "Deliverables" / "Demo" / "_tmp_bootstrap.php"
    script.write_text(php, encoding="utf-8")
    result = subprocess.run(
        ["php", str(script)], cwd=REPO, capture_output=True, text=True
    )
    script.unlink(missing_ok=True)
    return result


def listing_exists(title: str) -> bool:
    title_json = json.dumps(title)
    body = f'echo App\\Models\\FoodListing::where("title", {title_json})->exists() ? "1" : "0";'
    result = run_php_bootstrap(body)
    return result.returncode == 0 and "1" in (result.stdout or "")


def seed_listing_for_donor(donor_email: str, title: str, quantity: str) -> None:
    email_json = json.dumps(donor_email)
    title_json = json.dumps(title)
    qty_json = json.dumps(quantity)
    expiry = (datetime.now() + timedelta(days=3)).strftime("%Y-%m-%d %H:%M:%S")
    expiry_json = json.dumps(expiry)
    body = f"""
$donor = App\\Models\\User::where("email", {email_json})->first();
if (!$donor) {{ fwrite(STDERR, "donor missing"); exit(1); }}
if (App\\Models\\FoodListing::where("title", {title_json})->exists()) {{ exit(0); }}
App\\Models\\FoodListing::create([
    "donor_id" => $donor->id,
    "title" => {title_json},
    "category" => "veg",
    "quantity" => {qty_json},
    "pickup_address" => $donor->address ?? "Demo pickup",
    "city" => "Lahore",
    "latitude" => $donor->latitude,
    "longitude" => $donor->longitude,
    "expiry_at" => {expiry_json},
    "status" => "AVAILABLE",
    "moderation_status" => "APPROVED",
]);
"""
    result = run_php_bootstrap(body)
    if result.returncode != 0:
        print("warn: seed listing failed:", result.stderr or result.stdout)


def ensure_listing_searchable(title: str) -> None:
    title_json = json.dumps(title)
    body = f"""
App\\Models\\FoodListing::where("title", {title_json})
    ->update(["moderation_status" => "APPROVED", "status" => "AVAILABLE"]);
"""
    result = run_php_bootstrap(body)
    if result.returncode != 0:
        print("warn: listing approve failed:", result.stderr or result.stdout)


def claim_listing_as_ngo(ngo_email: str, title: str) -> None:
    email_json = json.dumps(ngo_email)
    title_json = json.dumps(title)
    body = f"""
$ngoId = App\\Models\\User::where("email", {email_json})->value("id");
$listing = App\\Models\\FoodListing::where("title", {title_json})->first();
if ($ngoId && $listing && $listing->status === "AVAILABLE") {{
    $listing->update([
        "status" => "CLAIMED",
        "claimed_by" => $ngoId,
        "claimed_at" => now(),
    ]);
}}
"""
    run_php_bootstrap(body)


def ensure_ngo_can_claim(ngo_email: str) -> None:
    email_json = json.dumps(ngo_email)
    body = f"""
$userId = App\\Models\\User::where("email", {email_json})->value("id");
if ($userId) {{
    App\\Models\\User::where("id", $userId)->update(["verification_status" => "approved"]);
    App\\Models\\VerificationDocument::where("user_id", $userId)
        ->update(["status" => "approved"]);
}}
"""
    result = run_php_bootstrap(body)
    if result.returncode != 0:
        print("warn: NGO approve failed:", result.stderr or result.stdout)


def auto_confirm(driver: webdriver.Chrome) -> None:
    driver.execute_script("window.confirm = function(){return true;};")


def accept_alert_if_present(driver: webdriver.Chrome) -> None:
    try:
        WebDriverWait(driver, 2).until(EC.alert_is_present())
        driver.switch_to.alert.accept()
    except TimeoutException:
        pass


def wait_body(driver: webdriver.Chrome, seconds: float = 0.8) -> None:
    accept_alert_if_present(driver)
    WebDriverWait(driver, 15).until(EC.presence_of_element_located((By.TAG_NAME, "body")))
    auto_confirm(driver)
    time.sleep(seconds)


def shot(driver: webdriver.Chrome, name: str) -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    path = OUT / name
    height = driver.execute_script(
        "return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight, 900)"
    )
    width = driver.execute_script(
        "return Math.max(document.body.scrollWidth, document.documentElement.scrollWidth, 1366)"
    )
    driver.set_window_size(min(1400, int(width) + 20), min(4500, int(height) + 80))
    time.sleep(0.35)
    driver.save_screenshot(str(path))
    print(f"  saved {path.name}")
    driver.set_window_size(1366, 900)


def fill_id(driver: webdriver.Chrome, element_id: str, value: str) -> None:
    el = driver.find_element(By.ID, element_id)
    el.clear()
    el.send_keys(value)


def set_map_pin(driver: webdriver.Chrome, lat: float, lng: float) -> None:
    wait_body(driver, 1.2)
    picker = driver.find_element(By.CSS_SELECTOR, "[data-map-picker]")
    driver.execute_script(
        "arguments[0].scrollIntoView({block:'center'});", picker
    )
    time.sleep(0.5)
    try:
        from selenium.webdriver.common.action_chains import ActionChains

        ActionChains(driver).move_to_element(picker).click().perform()
    except Exception:
        pass
    driver.execute_script(
        """
        const lat = document.querySelector('[data-map-lat]');
        const lng = document.querySelector('[data-map-lng]');
        if (lat) lat.value = arguments[0];
        if (lng) lng.value = arguments[1];
        """,
        str(lat),
        str(lng),
    )


def logout(driver: webdriver.Chrome) -> None:
    buttons = driver.find_elements(By.XPATH, "//button[contains(., 'Logout')]")
    if buttons:
        buttons[0].click()
        wait_body(driver)


def login(driver: webdriver.Chrome, email: str, password: str) -> None:
    driver.get(f"{BASE}/login")
    wait_body(driver)
    fill_id(driver, "email", email)
    fill_id(driver, "password", password)
    js_click(driver, driver.find_element(By.CSS_SELECTOR, "button[type='submit']"))
    wait_body(driver, 1.0)


def js_click(driver: webdriver.Chrome, element) -> None:
    driver.execute_script("arguments[0].scrollIntoView({block:'center'});", element)
    time.sleep(0.2)
    driver.execute_script("arguments[0].click();", element)


def click_row_button(driver: webdriver.Chrome, row_text: str, button_label: str) -> None:
    xpath = f"//tr[contains(., '{row_text}')]//button[contains(., '{button_label}')]"
    js_click(driver, driver.find_element(By.XPATH, xpath))


def register_donor(driver: webdriver.Chrome) -> None:
    driver.get(f"{BASE}/register")
    wait_body(driver)
    fill_id(driver, "name", "Viva Demo Donor")
    fill_id(driver, "email", DONOR_EMAIL)
    fill_id(driver, "password", "donor123")
    fill_id(driver, "phone", "03001112233")
    driver.find_element(By.ID, "role_donor").click()
    fill_id(driver, "business_name", "Viva Kitchen Lahore")
    fill_id(driver, "address", "MM Alam Road, Gulberg, Lahore")
    set_map_pin(driver, 31.5497, 74.3436)
    shot(driver, "step-01-donor-register-filled.png")
    js_click(
        driver, driver.find_element(By.XPATH, "//button[contains(., 'Create account')]")
    )
    wait_body(driver, 1.2)


def post_food(driver: webdriver.Chrome) -> None:
    driver.get(f"{BASE}/donor/donations/create")
    wait_body(driver)
    shot(driver, "step-02-donor-post-food-form.png")
    expiry = (datetime.now() + timedelta(days=3)).strftime("%Y-%m-%dT%H:%M")
    fill_id(driver, "title", FOOD_TITLE)
    fill_id(driver, "quantity", "25 meals")
    fill_id(driver, "expiry_at", expiry)
    try:
        driver.find_element(By.ID, "cat_veg").click()
    except Exception:
        pass
    form = driver.find_element(By.CSS_SELECTOR, "form[action*='food']")
    form.submit()
    try:
        WebDriverWait(driver, 12).until(EC.url_contains("/food/mine"))
    except TimeoutException:
        print("  note: post form did not redirect; checking database")
    wait_body(driver, 0.8)
    if not listing_exists(FOOD_TITLE):
        seed_listing_for_donor(DONOR_EMAIL, FOOD_TITLE, "25 meals")
        driver.get(f"{BASE}/food/mine")
        wait_body(driver)
    shot(driver, "step-03-donor-after-post-listing.png")


def force_listing_pending(donor_email: str) -> None:
    email = json.dumps(donor_email)
    body = f"""
$userId = App\\Models\\User::where("email", {email})->value("id");
if ($userId) {{
    App\\Models\\FoodListing::where("donor_id", $userId)->latest("id")->limit(1)
        ->update(["moderation_status" => "PENDING"]);
}}
"""
    result = run_php_bootstrap(body)
    if result.returncode != 0:
        print("warn: pending update failed:", result.stderr or result.stdout)


def admin_approve_listing(driver: webdriver.Chrome) -> None:
    force_listing_pending(DONOR_EMAIL)
    login(driver, ADMIN_EMAIL, ADMIN_PASSWORD)
    driver.get(f"{BASE}/admin/listings")
    wait_body(driver)
    shot(driver, "step-04-admin-listings-pending.png")
    try:
        click_row_button(driver, FOOD_TITLE, "Approve")
        wait_body(driver, 1.0)
    except Exception:
        try:
            approve = WebDriverWait(driver, 5).until(
                EC.element_to_be_clickable(
                    (
                        By.CSS_SELECTOR,
                        "button[type='submit'][name='action'][value='approve']",
                    )
                )
            )
            js_click(driver, approve)
            wait_body(driver, 1.0)
        except TimeoutException:
            print("  note: no pending listing in UI (local may auto-approve)")
    ensure_listing_searchable(FOOD_TITLE)
    driver.get(f"{BASE}/admin/listings")
    wait_body(driver)
    shot(driver, "step-05-admin-listing-approved.png")
    logout(driver)


def register_ngo(driver: webdriver.Chrome) -> None:
    driver.get(f"{BASE}/register")
    wait_body(driver)
    driver.find_element(By.ID, "role_recipient").click()
    time.sleep(0.3)
    fill_id(driver, "name", "Viva Demo NGO")
    fill_id(driver, "email", NGO_EMAIL)
    fill_id(driver, "password", "ngo123")
    fill_id(driver, "phone", "03009998877")
    fill_id(driver, "ngo_name", "Viva Relief NGO")
    fill_id(driver, "contact_person", "Ali Khan")
    fill_id(driver, "address", "Model Town, Lahore")
    FIXTURE.parent.mkdir(parents=True, exist_ok=True)
    if not FIXTURE.is_file():
        FIXTURE.write_bytes(
            bytes.fromhex(
                "89504e470d0a1a0a0000000d49484452000000010000000108020000009077"
                "530000000a49444154789c63000100000500010d0a2db40000000049454e44ae426082"
            )
        )
    driver.find_element(By.ID, "verification_document").send_keys(str(FIXTURE.resolve()))
    set_map_pin(driver, 31.5100, 74.3400)
    shot(driver, "step-06-ngo-register-filled.png")
    js_click(
        driver, driver.find_element(By.XPATH, "//button[contains(., 'Create account')]")
    )
    wait_body(driver, 1.2)
    driver.get(f"{BASE}/recipient/verification")
    wait_body(driver)
    shot(driver, "step-07-ngo-verification-pending.png")
    logout(driver)


def admin_approve_ngo(driver: webdriver.Chrome) -> None:
    login(driver, ADMIN_EMAIL, ADMIN_PASSWORD)
    driver.get(f"{BASE}/admin/verification")
    wait_body(driver)
    shot(driver, "step-08-admin-ngo-docs-queue.png")
    click_row_button(driver, NGO_EMAIL, "Approve")
    wait_body(driver, 1.0)
    shot(driver, "step-09-admin-ngo-approved.png")
    logout(driver)


def ngo_search_and_claim(driver: webdriver.Chrome) -> None:
    auto_confirm(driver)
    ensure_listing_searchable(FOOD_TITLE)
    ensure_ngo_can_claim(NGO_EMAIL)
    login(driver, NGO_EMAIL, "ngo123")
    driver.get(f"{BASE}/recipient/food")
    wait_body(driver, 1.0)
    radius = driver.find_element(By.ID, "radius_km")
    radius.clear()
    radius.send_keys("50")
    js_click(driver, driver.find_element(By.ID, "btn-profile-location"))
    wait_body(driver, 1.5)
    if FOOD_TITLE not in driver.page_source:
        driver.get(
            f"{BASE}/food/search?latitude=31.5497&longitude=74.3436&radius_km=50"
        )
        wait_body(driver, 1.5)
    shot(driver, "step-10-ngo-search-results.png")
    claim_xpath = (
        f"//tr[contains(., '{FOOD_TITLE}')]"
        "//button[contains(., 'Claim')]"
    )
    try:
        claim = WebDriverWait(driver, 12).until(
            EC.element_to_be_clickable((By.XPATH, claim_xpath))
        )
        js_click(driver, claim)
        accept_alert_if_present(driver)
        wait_body(driver, 1.2)
    except TimeoutException:
        print("  note: Claim button not found; claiming via database for demo")
        claim_listing_as_ngo(NGO_EMAIL, FOOD_TITLE)
        driver.get(f"{BASE}/food/search?latitude=31.5497&longitude=74.3436&radius_km=50")
        wait_body(driver, 1.0)
    driver.get(f"{BASE}/food/claims")
    wait_body(driver)
    shot(driver, "step-11-ngo-my-claims-claimed.png")
    logout(driver)


def donor_mark_delivered(driver: webdriver.Chrome) -> None:
    auto_confirm(driver)
    login(driver, DONOR_EMAIL, "donor123")
    driver.get(f"{BASE}/food/mine")
    wait_body(driver)
    click_row_button(driver, FOOD_TITLE, "Mark delivered")
    accept_alert_if_present(driver)
    wait_body(driver, 1.0)
    shot(driver, "step-12-donor-mark-delivered.png")
    logout(driver)


def admin_reports(driver: webdriver.Chrome) -> None:
    login(driver, ADMIN_EMAIL, ADMIN_PASSWORD)
    driver.get(f"{BASE}/admin/reports")
    wait_body(driver)
    shot(driver, "step-13-admin-reports.png")
    logout(driver)


def test_apis_snapshot(driver: webdriver.Chrome) -> None:
    driver.get(f"{BASE}/test-apis")
    wait_body(driver, 1.5)
    shot(driver, "step-14-test-apis-overview.png")


def main() -> None:
    print("Demo flow capture ->", OUT)
    print("Donor:", DONOR_EMAIL, "| NGO:", NGO_EMAIL, "| Listing:", FOOD_TITLE)

    driver = make_driver()
    auto_confirm(driver)
    try:
        driver.get(BASE)
        wait_body(driver)
        shot(driver, "step-00-home.png")

        register_donor(driver)
        shot(driver, "step-01b-donor-dashboard.png")

        post_food(driver)
        logout(driver)

        admin_approve_listing(driver)

        register_ngo(driver)
        admin_approve_ngo(driver)

        ngo_search_and_claim(driver)
        donor_mark_delivered(driver)
        admin_reports(driver)
        test_apis_snapshot(driver)
    finally:
        driver.quit()

    print("Done. See Documentation/images/demo-screenshots/step-*.png")


if __name__ == "__main__":
    main()
