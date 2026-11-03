# Figure 4.3 — System architecture (three-tier)

**Export target:** `Documentation/images/v2/figure-03-system-architecture.png`  
**SRS section:** 2.1 — Product perspective  
**Status:** Rendered → [`figure-03-system-architecture.png`](../../../images/v2/figure-03-system-architecture.png)

![Figure 4.3 — System architecture](../../../images/v2/figure-03-system-architecture.png)

---

## Purpose

Shows how the browser, Laravel application, and MySQL fit together for v2, including the scheduler for listing expiry and the dual HTML/JSON response path.

---

## Tiers

| Tier | Components |
| ---- | ------------ |
| Presentation | Browser, Blade views, Bootstrap, `public/css` & `public/js`, optional `/test-apis` client |
| Application | `web.php` routes, middleware (`auth`, `admin`), controllers, `AdminReportService`, Eloquent/Query Builder |
| Data | MySQL (`users`, `food_listings`) |
| Background | Laravel scheduler → `php artisan food:expire` hourly |

---

## Main controllers (v2)

| Controller | Responsibility |
| ---------- | -------------- |
| AuthController | Register, login, logout |
| FoodListingController | Store, search, mine, claims, deliver |
| ClaimController | Atomic claim |
| PortalController | GET-only HTML shells |
| AdminDashboardController / AdminReportController | Admin UI |
| ApiTestController | API playground |

---

## Text for report

Users access the portal through a web browser. Laravel handles routing, authentication, and business rules. All persistent data lives in MySQL. A scheduled command updates expired listings so search results stay accurate.

---

## Mermaid source

```mermaid
flowchart TB
    subgraph Client["Presentation tier"]
        Browser["Web browser"]
        Blade["Blade templates"]
        Assets["CSS / JS / Bootstrap"]
        Tester["/test-apis client"]
    end

    subgraph App["Application tier — Laravel"]
        WebServer["PHP web server"]
        Routes["routes/web.php"]
        MW["Middleware\nauth, admin, CSRF"]
        Ctrl["Controllers"]
        AuthC["AuthController"]
        FoodC["FoodListingController"]
        ClaimC["ClaimController"]
        AdminC["AdminReportService\n+ Admin controllers"]
        PortalC["PortalController"]
        Dual["HandlesPortalResponses\nJSON or redirect"]
        Sched["Scheduler\nfood:expire hourly"]
    end

    subgraph Data["Data tier"]
        DB[("MySQL")]
        TUsers["users"]
        TList["food_listings"]
    end

    Browser --> Blade
    Browser --> Tester
    Blade --> Assets
    Browser -->|"HTTP"| WebServer
    WebServer --> Routes --> MW --> Ctrl
    Ctrl --> AuthC
    Ctrl --> FoodC
    Ctrl --> ClaimC
    Ctrl --> AdminC
    Ctrl --> PortalC
    AuthC --> Dual
    FoodC --> Dual
    ClaimC --> Dual
    AuthC --> DB
    FoodC --> DB
    ClaimC --> DB
    AdminC --> DB
    PortalC --> DB
    DB --- TUsers
    DB --- TList
    Sched -->|"UPDATE status EXPIRED"| DB
```
