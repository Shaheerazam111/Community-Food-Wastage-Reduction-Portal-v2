# Project Samajhne Ka Guide (Roman Urdu)

Yeh file batati hai ke **kaun si file kya kaam karti hai** aur **data ka flow kya hai** — simple lafzon mein, jaise kisi dost ko samjhaya jaye.

---

## Pehle Poora Flow Samjho (Big Picture)

```
User browser kholo
    ↓
/test-apis page (UI)
    ↓
Form se API call (JavaScript)
    ↓
Laravel route → Controller → Database
    ↓
JSON response wapas UI pe
    ↓
Tables, map, login widget update
```

**Simple:** Browser se button dabao → Laravel backend chale → database se data aaye → screen pe dikhe.

---

## Assignment ke 4 Tasks — Kahan Hain?

| Task | Matlab | Main File |
|------|--------|-----------|
| Task 1 | Register + Login | `AuthController.php` |
| Task 2 | Donor food post kare | `FoodListingController.php` |
| Task 3 | Nearby search | `FoodListingController.php` → `search()` |
| Task 4 | Recipient claim kare | `ClaimController.php` |

**3 Challenges:**
- Challenge 1 (distance) → `FoodListingController::search()` mein Haversine SQL
- Challenge 2 (double claim) → `ClaimController::claim()` mein atomic UPDATE
- Challenge 3 (expired food) → `search()` filter + `ExpireFoodListings` command

---

## Routes — Darwaze (Konsa URL Kahan Jata Hai)

File: `routes/web.php`

| URL | Kaam |
|-----|------|
| `/` | Seedha `/test-apis` pe redirect |
| `/test-apis` | Testing UI page |
| `/test-apis/snapshot` | Tables ke liye live data (users + listings) |
| `/test-apis/reverse-geocode` | Map click se area ka naam (English) |
| `POST /auth/register` | Naya user |
| `POST /auth/login` | Login |
| `POST /auth/logout` | Logout |
| `GET /food/search` | Nearby food dhundo (login zaroori nahi) |
| `POST /food` | Naya listing (sirf donor) |
| `GET /food/mine` | Meri listings (donor) |
| `POST /food/{id}/claim` | Claim karo (sirf recipient) |
| `POST /food/{id}/deliver` | Deliver mark karo (donor) |

---

## Backend PHP Files

### 1. `app/Http/Controllers/AuthController.php`
**Yeh file:** User banane aur login/logout ka kaam karti hai.

| Function | Kya Karti Hai |
|----------|-------------|
| `register()` | Naya donor ya recipient database mein save. Password hash hota hai. Auto login bhi ho jata hai. |
| `login()` | Email + password check. Sahi ho to session start. |
| `logout()` | Session khatam. |
| `unauthenticated()` | Agar bina login protected API call karo to 401 JSON. |

---

### 2. `app/Http/Controllers/FoodListingController.php`
**Yeh file:** Donor ki taraf se food listing + public search.

| Function | Kya Karti Hai |
|----------|-------------|
| `store()` | Donor naya food item post karta hai. **Location donor profile se auto** (address, lat, lng). Sirf title, category, quantity, expiry form mein. |
| `myListings()` | Logged-in donor ki apni listings dikhao. |
| `search()` | **Task 3 + Challenge 1 & 3.** Lat/lng + radius se paas ki AVAILABLE listings. Distance SQL mein calculate. Expired hide. |
| `markDelivered()` | Status `CLAIMED` se `DELIVERED`. Pehle claim hona zaroori. |

---

### 3. `app/Http/Controllers/ClaimController.php`
**Yeh file:** Recipient food claim karta hai.

| Function | Kya Karti Hai |
|----------|-------------|
| `claim()` | **Task 4 + Challenge 2.** Ek hi UPDATE query — agar status ab bhi `AVAILABLE` hai tabhi claim hoga. Do log ek saath dabayen to sirf ek ko milega, doosra ko 409 error. |

---

### 4. `app/Http/Controllers/ApiTestController.php`
**Yeh file:** Sirf testing UI ke liye. Asli assignment APIs alag hain upar wali.

| Function | Kya Karti Hai |
|----------|-------------|
| `index()` | `/test-apis` page dikhata hai. |
| `snapshot()` | UI tables ke liye users + listings JSON bhejta hai. |
| `reverseGeocode()` | Map pe click ki hui jagah ka English naam laata hai. |

---

### 5. `app/Support/ApiTestCatalog.php`
**Yeh file:** Postman jaisi saari APIs ki list — forms mein kya fields hain, kaun si Task hai, yeh sab yahan defined hai.

| Function | Kya Karti Hai |
|----------|-------------|
| `groups()` | 4 groups return: Auth, Food, Search, Claim |
| `authGroup()` | Register, login, logout APIs ki details |
| `foodGroup()` | Create listing, my listings, deliver APIs |
| `searchGroup()` | Nearby search in/out range APIs |
| `claimGroup()` | Claim success + claim fail (409) APIs |

---

### 6. `app/Services/ApiTestSnapshotService.php`
**Yeh file:** Database se users aur listings nikal ke UI ko deti hai.

| Function | Kya Karti Hai |
|----------|-------------|
| `build()` | Poora snapshot banata hai: session user, users list, listings list |
| `users()` | Sirf users table se safe fields |
| `listings()` | Listings + donor/claimer naam ke sath |

---

### 7. `app/Services/GeocodingService.php`
**Yeh file:** Map pe click ki location ka area name dhundti hai (OpenStreetMap se).

| Function | Kya Karti Hai |
|----------|-------------|
| `reverse()` | Lat/lng se area ka naam English mein |
| `areaName()` | Raw geo data se readable naam banata hai |

---

### 8. `app/Models/User.php` aur `FoodListing.php`
**Yeh files:** Database tables ka Laravel representation.

- `User` → donors aur recipients
- `FoodListing` → food items, status, expiry, location

---

### 9. `app/Console/Commands/ExpireFoodListings.php`
**Yeh file:** Purani food ko `EXPIRED` mark karti hai (cron/scheduler se hourly).

---

## Frontend — Blade (HTML Pages)

### `resources/views/test-apis.blade.php`
**Yeh file:** Main testing page ka layout — header, tables, map, API sections.

### Partials (Choti HTML Pieces)

| File | Kaam |
|------|------|
| `partials/session-widget.blade.php` | Upar floating box — kaun login hai |
| `partials/api-panel.blade.php` | Ek API ka dropdown section (form + response box) |
| `partials/listing-picker.blade.php` | Claim/deliver ke liye listing ID choose karo |
| `partials/map-blocks.blade.php` | Register map + search preview map |

### `public/css/api-tester.css`
**Yeh file:** Saari styling — colors, buttons, tables, widget.

---

## Frontend — JavaScript Modules

Sab files: `public/js/api-tester/`

Padhne ka order (graduate ke liye best):

```
app.js  →  events.js  →  api-client.js  →  snapshot.js
```

---

### `app.js` — Shuru Yahan Se
**Yeh file:** Page load hone pe sab start karti hai.

| Function | Kaam |
|----------|------|
| `boot()` | Maps init, events bind, pehli data load |

---

### `state.js` — Shared Memory
**Yeh file:** Poore app ki memory — sab modules yahan se data lete hain.

| Function | Kaam |
|----------|------|
| `getSnapshot()` | Abhi database ka cached data lo |
| `setSnapshot()` | Naya data save karo refresh ke baad |

---

### `utils.js` — Chhoti Madadgar Cheezen
| Function | Kaam |
|----------|------|
| `escapeHtml()` | XSS se bachne ke liye text safe karo |
| `roundCoord()` | Lat/lng 6 decimal tak |
| `haversineKm()` | Do points ke beech distance km mein |
| `buildApiPath()` | URL mein `{listing_id}` replace |
| `availableListings()` | Sirf AVAILABLE + non-expired listings |
| `defaultExpiryInputs()` | Create listing form mein expiry 1 ghante baad set |
| `createTileLayer()` | Har map pe OpenStreetMap tiles |

---

### `session.js` — Login Widget
| Function | Kaam |
|----------|------|
| `updateSessionUI()` | Login/logout ke baad widget update |
| `showLoggedInUser()` | Naam, email, role dikhao |
| `showLoggedOutUser()` | "Not logged in" dikhao |

---

### `listings.js` — Listing ID Manage
| Function | Kaam |
|----------|------|
| `setListingId()` | Har jagah same ID set (dropdown, input, claim buttons) |
| `getListingId()` | Claim/deliver API ke liye konsi ID use hogi |
| `refreshListingPickers()` | Dropdowns aur claim buttons update |

---

### `geocode.js`
| Function | Kaam |
|----------|------|
| `fetchAreaName()` | Backend se area ka English naam mangwao |

---

### `api-client.js` — API Call Bhejna
**Yeh file:** Form/button se Laravel API hit karti hai.

| Function | Kaam |
|----------|------|
| `sendApi()` | Asli fetch request — sab APIs yahan se jati hain |
| `sendFormApi()` | Form submit handle |
| `sendButtonApi()` | Bina form wale buttons (claim, deliver) |
| `quickLogin()` | Widget se jaldi login |
| `logout()` | Session khatam |

---

### `snapshot.js` — Tables Refresh
| Function | Kaam |
|----------|------|
| `refreshSnapshot()` | Backend se data lo → users table, listings table, maps update |
| `renderUsersTable()` | Users table HTML banayo |
| `renderListingsTable()` | Listings table HTML banayo |

---

### `overview-map.js` — Upar Wala Bara Map
| Function | Kaam |
|----------|------|
| `initOverviewMap()` | Page ke top wala map start |
| `drawOverviewMap()` | Listings ke markers + search circle |
| `drawOverviewFromSnapshot()` | Snapshot se overview map update |

---

### `picker-map.js` — Register Map (Click se Location)
| Function | Kaam |
|----------|------|
| `initRegisterMapPicker()` | Register forms pe chota map |
| `applyPickedLocation()` | Click pe lat/lng + address fill |

---

### `search-map.js` — Search Preview Map
| Function | Kaam |
|----------|------|
| `initSearchPreviewMap()` | Search API ke neeche wala map |
| `redrawSearchPreview()` | Tumhari location (blue) + in-range (green) + out-range (gray) |
| `storeSearchResults()` | API response save taake map pe dikhe |

---

### `ui-toggle.js` — Dropdown Open/Close
| Function | Kaam |
|----------|------|
| `toggleGroup()` | Auth / Food / Search / Claim section kholo/bando |
| `toggleApi()` | Ek API panel kholo, map init bhi yahan |
| `initOpenPanels()` | Page load pe jo khule hon un ke maps ready |

---

### `events.js` — Sab Buttons Connect
| Function | Kaam |
|----------|------|
| `bindEvents()` | Forms, login buttons, listing click — sab listeners yahan lagte hain |

---

## Testing UI Flow — Step by Step

```
1. Register Donor     → AuthController::register()
2. Login Donor        → AuthController::login()
3. Create Listing     → FoodListingController::store()
   → listing_id auto save (JavaScript)
4. Search Nearby      → FoodListingController::search()
   → map pe green markers
5. Login Recipient A  → login
6. Claim Listing      → ClaimController::claim()
7. Login Recipient B  → claim dubara → 409 error (Challenge 2 proof)
8. Login Donor        → Deliver       → markDelivered()
```

---

## Setup Files (Project Chalana)

| File | Kaam |
|------|------|
| `setup.md` | Linux / Mac setup |
| `setup-windows.md` | Windows + XAMPP setup |
| `README.md` | Short project intro + API list |

---

## Database Migrations

| File | Kaam |
|------|------|
| `create_users_table` | Users table |
| `add_role_and_profile_fields` | Donor/recipient extra fields |
| `create_food_listings_table` | Food items table |

---

## Kaun Si File Padhu Pehle? (Graduate ke liye)

**Assignment samajhna ho:**
1. `Documentation/Community_Food_Wastage_Reduction_Portal_Assignment_v2.md`
2. `routes/web.php`
3. `AuthController.php` → `FoodListingController.php` → `ClaimController.php`

**Testing UI samajhna ho:**
1. `test-apis.blade.php`
2. `public/js/api-tester/app.js`
3. `api-client.js` → `snapshot.js`

**Poora project:**
1. README
2. Controllers (upar wale 3)
3. `ApiTestCatalog.php` (UI mein kya test ho sakta hai)
4. JavaScript modules ek ek kar ke

---

## Ek Line Mein Har Important File

| File | Ek Line |
|------|---------|
| `AuthController.php` | User banao, login karo |
| `FoodListingController.php` | Food post karo, dhundo, deliver karo |
| `ClaimController.php` | Safe claim — double claim nahi |
| `ApiTestController.php` | Testing page ka backend |
| `ApiTestCatalog.php` | UI forms ki poori list |
| `app.js` | Browser app start |
| `api-client.js` | API calls bhejo |
| `snapshot.js` | Tables refresh karo |
| `search-map.js` | Search map dikhao |
| `picker-map.js` | Register map pe click karo |
| `session.js` | Login widget update |
| `listings.js` | Claim ke liye ID choose |

---

*Last updated: project v2 with `/test-apis` UI*
