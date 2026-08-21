# What Was Built — Plain-English Guide

This explains every file created for the v2 backend and what each piece of code
inside it does — no programming background required. Useful for a project
report, a viva/defense, or just understanding your own project before
presenting it.

## The big picture, with an analogy

Think of the app like a small office with a filing cabinet:

- **Migrations** = the blueprint that builds the filing cabinet and labels its
  drawers (the database tables and their columns).
- **Models** = a labeled folder template for one drawer — "this is what a User
  record looks like, this is what a Food Listing record looks like."
- **Controllers** = the staff members. Each one has a specific job: one handles
  sign-ups and logins, one handles posting/searching food, one handles claiming.
- **Routes** = the reception desk's directory — "if a request comes in for
  `/auth/login`, send it to the login staff member."
- **Middleware** = the security guard at the door — checks "are you logged in?"
  before letting a request through to certain staff members.
- **Console Command** = a janitor who does one chore (sweeping out expired food
  listings) on a schedule, without anyone needing to ask.

---

## Database blueprints (migrations)

### `database/migrations/..._add_role_and_profile_fields_to_users_table.php`

Adds new columns to the existing "users" drawer so one signup form can serve
both kinds of users the assignment asks for:

- `role` — is this person a `donor` or a `recipient`?
- `business_name`, `phone` — filled in by donors (a restaurant, a shop, etc.)
- `ngo_name`, `contact_person` — filled in by recipients (an NGO/shelter)
- `address`, `latitude`, `longitude` — filled in by both, used later for distance search

All the role-specific fields are optional/blank for the *other* role — a
recipient never fills in `business_name`, for example.

### `database/migrations/..._create_food_listings_table.php`

Builds a brand-new drawer called "food_listings," one folder per donated food
item, with columns for:

- What it is: `title`, `category` (veg/non-veg), `quantity`
- Where to get it: `pickup_address`, `latitude`, `longitude`
- When it goes bad: `expiry_at`
- Its current stage: `status`, which can only be `AVAILABLE`, `CLAIMED`,
  `DELIVERED`, or `EXPIRED`
- Who's involved: `donor_id` (who posted it), `claimed_by` (who reserved it,
  blank until claimed), plus timestamps for when it was claimed/delivered

---

## Folder templates (models)

### `app/Models/User.php`

Describes one user record and which fields are allowed to be filled in when
creating one (`name`, `email`, `password`, `role`, and all the profile fields
above). Also defines a shortcut, `foodListings()`, meaning "if I have a User,
I can ask for every food listing that user (as a donor) has posted."

### `app/Models/FoodListing.php`

Describes one food listing record the same way, and defines two shortcuts:
`donor()` — "who posted this?" — and `claimedBy()` — "who reserved this
(if anyone)?"

---

## The staff members (controllers)

### `app/Http/Controllers/AuthController.php` — Task 1: Signup & Login

- **`register`** — Reads the signup form, checks everything required is filled
  in correctly (valid email, `role` is exactly `donor` or `recipient`, etc.),
  scrambles the password so it's never stored in plain text, saves the new
  user, and immediately logs them in.
- **`login`** — Checks the email/password against what's stored. If they
  match, starts a session (like a name badge that proves who you are for the
  rest of your visit). If not, replies "Invalid credentials."
- **`logout`** — Ends the session, i.e. removes the name badge.
- **`unauthenticated`** — A polite "you're not logged in" reply, used
  automatically whenever someone tries to reach a staff member that requires a
  badge without having one.

### `app/Http/Controllers/FoodListingController.php` — Task 2 & 3

- **`store`** — Task 2. Only lets donors through (checks `role`). Validates the
  food details (title, veg/non-veg, quantity, pickup address, coordinates, and
  that the expiry time is in the future), then creates the listing, which
  starts life as `AVAILABLE`.
- **`myListings`** — Returns every listing the currently logged-in donor has
  posted, most recent first.
- **`search`** — Task 3 and Challenge 1. Takes a recipient's current
  latitude/longitude and a search radius (defaults to 5km). Asks the database
  to calculate, for every listing, the straight-line distance from that point
  using the Haversine formula (the standard math for distance between two
  points on a sphere like the Earth). Only keeps listings that are still
  `AVAILABLE`, not yet past their expiry time, and within the radius — then
  sorts the results closest-first.
- **`markDelivered`** — The last step of Task 2's lifecycle. Only the donor who
  posted the item can call this, and only once it's `CLAIMED` — it refuses to
  jump straight from `AVAILABLE` to `DELIVERED`. Marks it `DELIVERED` and
  records the time.

### `app/Http/Controllers/ClaimController.php` — Task 4 & Challenge 2

- **`claim`** — The one function solving the trickiest requirement: two people
  clicking "Claim" on the same item at the same instant must never both
  succeed. Instead of first *checking* the status and then separately
  *updating* it (which leaves a tiny window where two requests could both pass
  the check), it does both in one single instruction to the database: *"update
  this row to CLAIMED, but only if it's still AVAILABLE."* The database
  guarantees only one such instruction can win, even if two arrive at the
  exact same moment. If a request loses that race, the database reports "0 rows
  changed," and the code replies `409 — This item was already claimed or has
  expired` instead of pretending it worked.

---

## The reception desk (routes)

### `routes/web.php`

The full directory of every web address ("route") the app understands and
which staff member (controller function) handles it — e.g. `POST /auth/login`
goes to `AuthController::login`, `POST /food/{id}/claim` goes to
`ClaimController::claim`. Routes under the `auth` group require a valid
session (you must be logged in); `food/search` and the auth routes themselves
are open to anyone.

---

## The security guard (middleware)

### `app/Http/Middleware/VerifyCsrfToken.php`

Laravel normally requires a special anti-forgery token on every form
submission, to stop a malicious webpage from secretly submitting forms on your
behalf while you're logged in elsewhere. That protection is designed for
webpages with forms — this project is a plain JSON API tested with Postman, so
that check is switched off (`except`) specifically for the `/auth/*` and
`/food*` addresses, while everything else in Laravel keeps its normal
protection.

---

## The scheduled janitor (console command)

### `app/Console/Commands/ExpireFoodListings.php` — Challenge 3

- **`handle`** — The one chore this janitor does: find every listing that's
  still marked `AVAILABLE` but whose expiry time has already passed, and
  relabel it `EXPIRED`. Prints how many it cleaned up.

### `app/Console/Kernel.php` (one line added)

Tells Laravel's built-in scheduler to run that chore automatically once every
hour, so listings don't sit around forever incorrectly marked `AVAILABLE`
after their expiry time — even though (separately) the search function
already hides expired items immediately regardless of whether this hourly
chore has run yet.

---

## Everything else

- **`.env` / `.env.example`** — The settings file: which database to connect
  to, the app's name, etc. Not something you read code in — just configuration.
- **`README.md`** — Setup instructions and an endpoint reference table for
  anyone opening the project fresh.
- **`Documentation/postman_collection.json`** — A ready-made set of test
  requests you can import into Postman.
- **`Documentation/flow-test.md`** — Step-by-step instructions for testing
  every requirement using that Postman collection.
