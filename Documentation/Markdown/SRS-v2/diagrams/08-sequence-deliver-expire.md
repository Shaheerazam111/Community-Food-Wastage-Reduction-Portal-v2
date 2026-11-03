# Figure 4.8 — Sequence diagram: mark delivered and expiry job

**Export target:** `Documentation/images/v2/figure-08-sequence-deliver-expire.png`  
**SRS section:** 3.2 / 3.5 — FR-D05, FR-S01–S02  
**Status:** Rendered → [`figure-08-sequence-deliver-expire.png`](../../../images/v2/figure-08-sequence-deliver-expire.png)

![Figure 4.8 — Deliver and expire sequence](../../../images/v2/figure-08-sequence-deliver-expire.png)

---

## Purpose

Two related flows:

1. **Donor** confirms pickup → **DELIVERED**  
2. **System** hourly job marks overdue **AVAILABLE** rows as **EXPIRED** (Challenge 3)

Lazy expiry: search/claim already filter `expiry_at > now()` even before the job runs.

---

## Deliver preconditions

- Authenticated donor owns the listing (`donor_id`)
- Current status is **CLAIMED**

---

## Text for report

After pickup, the donor marks the listing delivered. Separately, a scheduled command scans for available listings past their expiry time and sets them to EXPIRED so reports and data stay consistent.

---

## Mermaid source

```mermaid
sequenceDiagram
    autonumber
    actor Donor
    participant Browser
    participant Food as FoodListingController
    participant DB as MySQL food_listings
    participant Cron as Scheduler
    participant Cmd as food:expire command

    rect rgb(240, 248, 255)
        Note over Donor,DB: Mark delivered
        Donor->>Browser: Mark delivered
        Browser->>Food: POST /food/{id}/deliver
        Food->>DB: SELECT listing
        alt not owner or not CLAIMED
            Food-->>Browser: 403 or 422
        else ok
            Food->>DB: UPDATE status DELIVERED, delivered_at
            Food-->>Browser: Success redirect or JSON
        end
    end

    rect rgb(245, 245, 245)
        Note over Cron,DB: Hourly expiry (Challenge 3)
        Cron->>Cmd: Run food:expire
        Cmd->>DB: UPDATE WHERE status AVAILABLE AND expiry_at <= now()
        Cmd->>DB: SET status EXPIRED
        Cmd-->>Cron: Count updated
    end
```
