# Coding rules and conventions (this project)

Rules come from **Cursor workspace rules**, **`Documentation/rules/*.mdc`**, and supervisor docs. Follow them when you edit code or UI copy.

---

## Stack (non‑negotiable unless user asks)

- **Laravel + Blade + Bootstrap 5 + plain CSS + plain JS + MySQL**
- No React, Vue, Tailwind, Livewire, Inertia, Alpine for portal screens
- Thin controllers; one method ≈ one user action
- Forms: Blade + `@csrf`; prefer named routes when they exist (`route('...')`), else `url('/auth/login')` as current views do

---

## Writing style (comments, UI text, docs)

Sound like a **competent uni student** — plain, direct, no buzzwords.

- UI buttons: “Post donation”, “Claim food”, “Mark as delivered”
- Errors: say what went wrong and what to do next
- **Every function and every query block:** one short comment above (about **3–8 words**), e.g. “List available food in city”

Do not add long paragraph comments or marketing language in the codebase.

---

## Keep it simple

- Smallest change that works; no extra layers “for later”
- No big service factories unless the project already uses that pattern
- Core flows first: register → post → search → claim → track status
- Prefer solutions you can explain in a viva

---

## PHP / Laravel patterns in **this** repo

- **Dual response:** controllers that serve Postman and HTML use `HandlesPortalResponses` (`expectsJson()` → JSON, else redirect + session flash).
- **Auth:** session cookies; CSRF disabled for `auth/*` and `food*` in `VerifyCsrfToken` so JSON clients work; browser forms on those paths still send `@csrf` where Laravel applies it on other routes.
- **Roles:** string column `users.role`: `donor` | `recipient` only in v2.
- **Pickup location:** listing copies donor’s `latitude` / `longitude` / address at create time (donor must register with coordinates).
- **Search/claim expiry:** always compare `expiry_at` to server `now()`; background job marks stale AVAILABLE rows as EXPIRED.

When splitting logic, use small private methods (validate / save / notify) — not one giant handler.

---

## Blade / UI

- Mobile-first Bootstrap grid (`col-12` then `col-md-*`)
- Shared layouts: `layouts/guest.blade.php`, `layouts/app.blade.php`
- Minimal custom CSS in `public/css/app.css`
- Small JS in `public/js/app.js` (confirm, map picker, etc.)

---

## Documentation maintenance

When you **add, rename, or remove**:

- Blade under `resources/views/`
- CSS/JS under `public/css/`, `public/js/`
- UI-related routes in `routes/web.php`
- UI-only controllers/helpers

→ Update **`Documentation/Markdown/ui-file-index.md`** in the same change. Use **repo-root paths** (e.g. `resources/views/...`), not `portal/...`.

When you add agent-facing facts (new major module), update **`implementation-status.md`** or **`core-workflows-api-and-data.md`** if behavior changes.

---

## Git / commits

Only commit when the user asks. Do not commit `.env` or secrets.

---

## Where rules are duplicated

| Location | Contents |
| --- | --- |
| User `.cursor/rules/` | uni-student-writing-style, keep-it-simple, functions-and-queries, laravel-stack, ui-file-index, blade-ui, laravel-php |
| `Documentation/rules/` | laravel-stack, laravel-php, uni-student-writing-style, ui-file-index |

Content should stay aligned; project-specific paths should match **v2 root layout**.
