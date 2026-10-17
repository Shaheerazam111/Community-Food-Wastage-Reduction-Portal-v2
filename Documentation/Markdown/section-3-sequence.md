## 3. Sequence Diagrams

### 3.1 What We Include

We use **3 sequence diagrams only**:

| # | Diagram | Covers |
|---|---------|--------|
| 1 | **Auth & User Flow** | Register, login, logout, admin verify NGO |
| 2 | **Donation Process** | Post → claim → volunteer pickup → delivered |
| 3 | **Dispute Flow** | Raise dispute → admin resolve → notify both |

**Tool:** [PlantUML Online](https://www.plantuml.com/plantuml) — open a `.puml` file below → paste → export PNG.

| File |
|------|
| `docs/diagrams/sequence-auth-flow.puml` |
| `docs/diagrams/sequence-donation-flow.puml` |
| `docs/diagrams/sequence-dispute-flow.puml` |

---

### 3.2 Diagram 1 — Auth & User Flow

**Figure 3.1** — Register (Donor/NGO/Volunteer), login with role dashboard, logout, admin verifies NGO document.

Copy from: `docs/diagrams/sequence-auth-flow.puml`

---

### 3.3 Diagram 2 — Full Donation Process

**Figure 3.2** — Donor posts donation + items, NGO claims, volunteer completes pickup, notifications sent.

Copy from: `docs/diagrams/sequence-donation-flow.puml`

---

### 3.4 Diagram 3 — Dispute Flow

**Figure 3.3** — Donor or NGO raises dispute, admin reviews and resolves, both parties notified.

Copy from: `docs/diagrams/sequence-dispute-flow.puml`

---

### 3.5 What to Write Under Each Diagram in Word

**Figure 3.1:** User registers and logs in through AuthController. NGOs wait for admin approval. Admin uses AdminController to verify documents.

**Figure 3.2:** Full donation path from DonationController (post) to ClaimController (claim) to VolunteerController (delivery).

**Figure 3.3:** ClaimController saves dispute; AdminController resolves it and NotificationController alerts donor and NGO.

---
