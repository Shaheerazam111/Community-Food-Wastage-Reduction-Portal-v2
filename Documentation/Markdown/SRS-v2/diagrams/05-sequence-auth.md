# Figure 4.5 — Sequence diagram: authentication

**Export target:** `Documentation/images/v2/figure-05-sequence-auth.png`  
**SRS section:** 3.1 — FR-A01–A05  
**Status:** Rendered → [`figure-05-sequence-auth.png`](../../../images/v2/figure-05-sequence-auth.png)

![Figure 4.5 — Auth sequence](../../../images/v2/figure-05-sequence-auth.png)

---

## Purpose

Register, login, and logout using **session-based auth** (`AuthController`). Same endpoints serve HTML forms and JSON (Postman).

---

## Participants

- Donor / Recipient / Admin (actor)
- Browser
- AuthController
- User model / MySQL `users`
- Session store

---

## Main flows

1. **Register** — validate → hash password → insert user → `Auth::login` → redirect or JSON 201  
2. **Login** — validate credentials → regenerate session → redirect by role  
3. **Logout** — invalidate session → redirect home  

Admin is not accepted on `role` during public register (validation allows only donor/recipient).

---

## Text for report

The user submits registration or login through the browser. AuthController validates input, reads or writes the users table, and starts or ends a Laravel session. After login, donors, recipients, and admins are sent to their respective home pages.

---

## Mermaid source

```mermaid
sequenceDiagram
    autonumber
    actor User as User (Donor / NGO / Admin)
    participant Browser
    participant Auth as AuthController
    participant DB as MySQL users
    participant Session as Session store

    rect rgb(240, 248, 255)
        Note over User,Session: Register (donor or recipient only)
        User->>Browser: Submit register form
        Browser->>Auth: POST /auth/register
        Auth->>Auth: Validate fields + role
        Auth->>DB: INSERT user (password hashed)
        Auth->>Session: Auth login + session id
        Auth-->>Browser: Redirect home or JSON 201
    end

    rect rgb(255, 248, 240)
        Note over User,Session: Login
        User->>Browser: Submit login
        Browser->>Auth: POST /auth/login
        Auth->>DB: SELECT by email
        Auth->>Auth: Verify password
        Auth->>Session: Regenerate session
        Auth-->>Browser: Redirect by role or JSON 200
    end

    rect rgb(245, 245, 245)
        Note over User,Session: Logout
        User->>Browser: Logout
        Browser->>Auth: POST /auth/logout
        Auth->>Session: Invalidate session
        Auth-->>Browser: Redirect / or JSON
    end
```
