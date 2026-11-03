# Figure 4.6 — Sequence diagram: donor posts food listing

**Export target:** `Documentation/images/v2/figure-06-sequence-post-listing.png`  
**SRS section:** 3.2 — FR-D01–D03  
**Status:** Rendered → [`figure-06-sequence-post-listing.png`](../../../images/v2/figure-06-sequence-post-listing.png)

![Figure 4.6 — Post listing sequence](../../../images/v2/figure-06-sequence-post-listing.png)

---

## Purpose

Donor creates a row in `food_listings` with status **AVAILABLE**. Pickup location is taken from the logged-in donor profile, not from separate map fields on the listing form.

---

## Preconditions

- User authenticated as **donor**
- Donor has `latitude` and `longitude` set (usually at registration)

---

## Postconditions

- New listing with `status = AVAILABLE`
- `pickup_address`, lat/lng copied from donor

---

## Text for report

The donor submits title, category, quantity, and expiry. FoodListingController checks the donor role and profile coordinates, then inserts the listing. The donor is redirected to my listings or receives JSON with the new record.

---

## Mermaid source

```mermaid
sequenceDiagram
    autonumber
    actor Donor
    participant Browser
    participant Food as FoodListingController
    participant User as Auth user profile
    participant DB as MySQL food_listings

    Donor->>Browser: Fill post food form
    Browser->>Food: POST /food (session cookie)
    Food->>Food: role === donor?
    alt not donor
        Food-->>Browser: 403 / error flash
    else donor ok
        Food->>Food: Validate title, category, quantity, expiry_at
        Food->>User: Read address, lat, lng
        alt missing coordinates
            Food-->>Browser: 422 profile needs location
        else ok
            Food->>DB: INSERT listing AVAILABLE
            Food-->>Browser: Redirect /food/mine or JSON 201
        end
    end
```
