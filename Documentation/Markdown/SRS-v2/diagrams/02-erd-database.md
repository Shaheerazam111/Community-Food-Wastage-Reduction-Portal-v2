# Figure 4.2 — Entity relationship diagram (implemented database)

**Export target:** `Documentation/images/v2/figure-02-erd.png`  
**SRS section:** 6 — Data requirements  
**Status:** Rendered → [`figure-02-erd.png`](../../../images/v2/figure-02-erd.png)

![Figure 4.2 — ERD](../../../images/v2/figure-02-erd.png)

---

## Purpose

Documents the **actual MySQL schema** used by the Laravel v2 app: `users` and `food_listings`. Claim data is stored on the listing row (`claimed_by`, `claimed_at`), not a separate `claims` table.

---

## Entities

### users

| Column | Key | Notes |
| ------ | --- | ----- |
| id | PK | |
| name, email, password | | |
| role | | donor \| recipient \| admin |
| business_name, phone | | donor profile |
| ngo_name, contact_person | | recipient profile |
| address, latitude, longitude | | location |
| created_at, updated_at | | |

### food_listings

| Column | Key | Notes |
| ------ | --- | ----- |
| id | PK | |
| donor_id | FK → users.id | cascade delete |
| title, category, quantity | | |
| pickup_address, latitude, longitude | | copied from donor at post |
| expiry_at | | best-before |
| status | | AVAILABLE, CLAIMED, DELIVERED, EXPIRED |
| claimed_by | FK → users.id, nullable | recipient |
| claimed_at, delivered_at | nullable | |
| created_at, updated_at | | |

Index: `(status, expiry_at)` for search and expiry job.

---

## Relationships

- **User (donor)** 1 —— * **Food listing** via `donor_id`
- **User (recipient)** 0..1 —— * **Food listing** via `claimed_by` (only when claimed)

---

## Note on legacy table

Migration `food` (name, price, description) exists but is **unused** by application code — omit from ERD or mark deprecated.

---

## Text for report

The ERD shows two main entities. Donors create many food listings. Each listing can be claimed by at most one recipient; status and timestamps record the lifecycle for reporting.

---

## Mermaid source

```mermaid
erDiagram
    USERS ||--o{ FOOD_LISTINGS : "donor_id posts"
    USERS ||--o{ FOOD_LISTINGS : "claimed_by claims"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "donor|recipient|admin"
        string business_name
        string phone
        string ngo_name
        string contact_person
        string address
        decimal latitude
        decimal longitude
        datetime created_at
        datetime updated_at
    }

    FOOD_LISTINGS {
        bigint id PK
        bigint donor_id FK
        string title
        enum category "veg|non_veg"
        string quantity
        string pickup_address
        decimal latitude
        decimal longitude
        datetime expiry_at
        enum status "AVAILABLE|CLAIMED|DELIVERED|EXPIRED"
        bigint claimed_by FK
        datetime claimed_at
        datetime delivered_at
        datetime created_at
        datetime updated_at
    }
```
