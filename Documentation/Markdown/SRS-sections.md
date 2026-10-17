# Software Requirements Specification (SRS)
## Community Food Wastage Reduction Portal

> Copy each section into your Word doc as needed. Written in a simple uni-student style — technical but not over-complicated.

---

## 1. Scope of Project

### 1.1 Project Name
Community Food Wastage Reduction Portal (also called Food Wastage Reduction Portal)

### 1.2 Problem Statement
In cities, a lot of extra food from restaurants, hotels, events, and homes gets thrown away. At the same time, NGOs and shelters often don't have enough food for people who need it. The main problem is that donors and receivers don't know about each other, so food goes bad before anyone can pick it up.

### 1.3 Proposed Solution
This project is a web-based portal that connects food donors with NGOs/receivers (and volunteers for pickup help). Donors can post surplus food with basic details and location. Receivers can search nearby listings and claim food before it expires. Admin checks listings and users to keep the system safe. The system also sends simple notifications when something important happens (new listing, claim update, etc.).

### 1.4 Project Objectives
- Reduce food waste by making surplus food easy to find and claim
- Give donors a simple way to list food and track pickup status
- Help NGOs find available food near their area quickly
- Let admin verify users and moderate listings
- Show basic reports (donations, claims, deliveries) for tracking impact

### 1.5 Target Users
| Role | Who they are | Main job on portal |
|------|----------------|-------------------|
| Donor | Restaurant, hotel, event planner, individual | Post and manage food listings |
| Receiver / NGO | NGO, shelter, community kitchen | Search, claim, and update pickup status |
| Volunteer | Person helping with pickup/delivery | View tasks and update delivery status |
| Admin | System manager | Manage users, moderate listings, view reports |
| System | Background process | Expire old listings, send notifications, generate reports |

### 1.6 In Scope
- User registration and login for Donor, Receiver/NGO, Volunteer, and Admin
- Create, edit, delete food listings (donor)
- Search and claim available food (receiver)
- Pickup/delivery status updates
- Admin moderation of listings and user management
- Basic notifications (dashboard and/or email)
- Simple reports and history views
- Map/location-based search (city or nearby area)

### 1.7 Out of Scope (for this version)
- Payment or billing features
- Full mobile app (only responsive web)
- Advanced AI routing or demand prediction
- Multi-language support
- Complex chat/messaging between all users
- Integration with third-party delivery companies

### 1.8 Tools & Technology (planned)
- **Frontend:** HTML5, CSS3 (Tailwind or Bootstrap), React.js or Vue.js
- **Backend:** Node.js (Express) or Python (Flask/Django)
- **Database:** MySQL, PostgreSQL, or MongoDB
- **Other:** Google Maps API (location), Firebase (optional for images/storage)

---

## 2. Functional Requirements

### 2.1 User Management
| ID | Requirement |
|----|-------------|
| FR-01 | System shall allow Donor, Receiver/NGO, and Volunteer to register with basic details (name, email, password, role). |
| FR-02 | System shall allow all users to login and logout securely. |
| FR-03 | System shall support role-based access (donor sees donor features, NGO sees claim features, etc.). |
| FR-04 | Admin shall manage user accounts (approve, block, or update NGO/donor verification status). |
| FR-05 | NGO users may upload verification documents during or after registration (admin reviews). |

### 2.2 Donation Listing (Donor)
| ID | Requirement |
|----|-------------|
| FR-06 | Donor shall post a food listing with item name, category (Veg/Non-Veg), quantity (kg or servings), best-before time, pickup address, and optional photo. |
| FR-07 | Donor shall edit or delete their own active listings. |
| FR-08 | Donor shall view all their past and current donations on a dashboard. |
| FR-09 | Donor shall see pickup status (Available, Claimed, Picked Up / Delivered). |
| FR-10 | New listings shall go to admin for moderation before or after going live (based on project setup). |

### 2.3 Search & Claim (Receiver / NGO)
| ID | Requirement |
|----|-------------|
| FR-11 | Receiver shall search available food by city, area, or nearby location. |
| FR-12 | Receiver shall view listing details (food type, quantity, expiry, donor location). |
| FR-13 | Receiver shall claim an available listing so no other NGO can claim the same one. |
| FR-14 | Receiver shall update pickup status after collection. |
| FR-15 | Receiver shall view claim history. |

### 2.4 Volunteer Module
| ID | Requirement |
|----|-------------|
| FR-16 | Volunteer shall view assigned pickup/delivery tasks. |
| FR-17 | Volunteer shall update delivery task status (e.g. in progress, completed). |
| FR-18 | Volunteer shall view basic route/location info for pickup (map or address list). |

### 2.5 Admin Module
| ID | Requirement |
|----|-------------|
| FR-19 | Admin shall login to admin dashboard. |
| FR-20 | Admin shall moderate food listings (approve, reject, or remove). |
| FR-21 | Admin shall manage users (view, verify, block). |
| FR-22 | Admin shall view reports (total donations, claims, deliveries, city-wise stats). |
| FR-23 | Admin shall handle basic disputes between donor and receiver (notes or status update). |

### 2.6 System / Automated Features
| ID | Requirement |
|----|-------------|
| FR-24 | System shall automatically mark expired listings as unavailable when best-before time passes. |
| FR-25 | System shall send notification when new food is posted near an NGO area. |
| FR-26 | System shall send notification when pickup status is updated. |
| FR-27 | System shall generate report data for admin (from stored history). |
| FR-28 | Receiver may submit simple feedback/rating after pickup (optional trust feature). |

---

## 3. Non-Functional Requirements

| ID | Category | Requirement |
|----|----------|-------------|
| NFR-01 | Performance | Main pages (login, search, dashboard) should load within 3–5 seconds on normal internet. |
| NFR-02 | Security | Passwords must be stored hashed; only logged-in users access their role features. |
| NFR-03 | Usability | UI should be simple and easy for non-technical users (clear buttons, readable forms). |
| NFR-04 | Reliability | System should keep donation status consistent (once claimed, others cannot claim same listing). |
| NFR-05 | Availability | Portal should be usable during normal hours; basic uptime target for demo/deployment phase. |
| NFR-06 | Scalability | Design should support more users/listings without major code rewrite (normal DB indexing). |
| NFR-07 | Compatibility | Web app should work on common browsers (Chrome, Edge, Firefox) and mobile screen sizes. |
| NFR-08 | Maintainability | Code should be modular (separate routes for donor, NGO, admin) with short comments on functions. |
| NFR-09 | Data Integrity | Donation records must not be lost after claim or delivery update; history kept for reports. |
| NFR-10 | Privacy | User documents (NGO verification) visible only to admin, not public. |

---

## 4. Use Case Diagram

### 4.1 Diagram Description (for your report)

The use case diagram shows five actors and one system boundary (**Food Wastage Reduction Portal**):

**Actors:**
1. **Donor** — posts and manages food listings
2. **Receiver / NGO** — searches and claims food
3. **Volunteer** — handles pickup/delivery tasks
4. **Admin** — manages users, moderates listings, views reports
5. **System** — automated tasks (expire listings, notifications, reports)

**Main use cases by actor:**

| Actor | Use Cases |
|-------|-----------|
| Donor | Register/Login, Post food listing, Edit/delete listing, View my donations, View pickup status |
| Receiver/NGO | Register/Login, Search available food, Claim food listing, Update pick status, View history |
| Volunteer | Register/Login, View pickup task, Update delivery task, View routes |
| Admin | Login, Manage users, Moderate listing, View reports, Handle disputes |
| System | Update expired listing, Send notification, Generate reports |

**Relationships shown in diagram:**
- Post food listing → linked to **Moderate listing** (admin approval flow)
- Update pick status → triggers **Send notification**
- View reports → uses data from **View history**
- Moderate listing → may lead to **Update expired listing** when listing is no longer valid

> Insert your improved diagram image in the Word doc under this section. Use the AI prompt in Section 4.2 below.

### 4.2 AI Tool + Prompt to Generate a Better Use Case Diagram

**Recommended tools (pick one):**

| Tool | Why use it | Link |
|------|------------|------|
| **Lucidchart** | Has AI diagram assist; good UML shapes | https://www.lucidchart.com |
| **Creately** | AI use case templates | https://creately.com |
| **Draw.io (diagrams.net)** | Free; paste Mermaid/PlantUML or build manually | https://app.diagrams.net |
| **ChatGPT / Claude** | Ask for PlantUML or Mermaid code, then render | — |

**Best workflow:** Use **ChatGPT or Claude** to get **PlantUML** code → paste into **PlantUML Online** (https://www.plantuml.com/plantuml) or Draw.io → export PNG.

**Copy-paste prompt:**

```
Create a clean UML use case diagram for a "Food Wastage Reduction Portal" web application.

Actors (stick figures on left and right):
- Donor (left)
- Receiver/NGO (left)
- Admin (left)
- Volunteer (right)
- System (right, secondary actor for automated processes)

System boundary box title: Food Wastage Reduction Portal

Use cases inside the boundary:

Donor: Register/Login, Post food listing, Edit/delete listing, View my donations, View pickup status

Receiver/NGO: Register/Login, Search available food, Claim food listing, Update pick status, View history

Volunteer: Register/Login, View pickup task, Update delivery task, View routes

Admin: Login, Manage users, Moderate listing, View reports, Handle disputes

System: Update expired listing, Send notification, Generate reports

Include these relationships (dashed arrows <<include>> or simple arrows with labels):
- Post food listing connects to Moderate listing
- Update pick status connects to Send notification
- View reports connects to View history
- Moderate listing connects to Update expired listing

Style: Simple university project diagram. White background, black lines, readable labels, no fancy colors. Landscape layout.

Output: PlantUML code I can render to PNG. Also give a short figure caption for an SRS document.
```

**Optional Mermaid prompt** (if your tool supports Mermaid — note: Mermaid use case support is limited; PlantUML is better):

```
Generate PlantUML use case diagram syntax (not Mermaid) for Food Wastage Reduction Portal with actors Donor, Receiver/NGO, Volunteer, Admin, System and all use cases listed above. Keep it readable for a student SRS report.
```

---

## 5. Usage Scenarios

> Each scenario is in table format (same as Word SRS). Copy table into doc and adjust borders/fonts if needed.  
> Total: **28 use cases**

---

### 1. Donor Registration

| Field | Description |
|-------|-------------|
| Use Case Title | Donor Registration |
| Use Case ID | UC-01 |
| Actor | Donor |
| Description | A new donor signs up on the portal to post surplus food. |
| Actions | • Donor opens registration page<br>• Enters name, email, password, phone<br>• Selects role Donor and submits form<br>• System validates input and creates account |
| Alternative Paths | If email already exists, system shows error and asks donor to login instead. |
| Pre Conditions | Donor is not registered yet. |
| Post Conditions | Donor account is created and donor can login. |
| Author | System Developer |
| Exceptions | Server or database error — system shows "Registration failed, try again". |

---

### 2. Receiver/NGO Registration

| Field | Description |
|-------|-------------|
| Use Case Title | Receiver/NGO Registration |
| Use Case ID | UC-02 |
| Actor | Receiver/NGO |
| Description | An NGO registers on the portal to search and claim food donations. |
| Actions | • NGO opens registration page<br>• Enters org name, email, password, city<br>• Uploads verification document<br>• Submits form<br>• System saves account as pending verification |
| Alternative Paths | If document upload fails, system asks NGO to retry with a smaller file. |
| Pre Conditions | NGO is not registered yet. |
| Post Conditions | NGO account exists; admin verifies before full access. |
| Author | System Developer |
| Exceptions | Invalid file type uploaded — system rejects and shows allowed formats. |

---

### 3. Volunteer Registration

| Field | Description |
|-------|-------------|
| Use Case Title | Volunteer Registration |
| Use Case ID | UC-03 |
| Actor | Volunteer |
| Description | A volunteer creates an account to help with pickup and delivery tasks. |
| Actions | • Volunteer opens registration page<br>• Enters personal details and selects Volunteer role<br>• Submits form<br>• System creates volunteer account |
| Alternative Paths | If required field is empty, system highlights field and asks to fill it. |
| Pre Conditions | Volunteer is not registered yet. |
| Post Conditions | Volunteer can login and view assigned tasks. |
| Author | System Developer |
| Exceptions | Network timeout — form data lost; volunteer must re-enter details. |

---

### 4. User Login

| Field | Description |
|-------|-------------|
| Use Case Title | User Login |
| Use Case ID | UC-04 |
| Actor | Donor, Receiver/NGO, Volunteer, Admin |
| Description | A registered user logs in to access role-based features. |
| Actions | • User opens login page<br>• Enters email and password<br>• System checks credentials<br>• System opens dashboard based on user role |
| Alternative Paths | User clicks "Forgot password" to reset password via email link. |
| Pre Conditions | User already has a registered account. |
| Post Conditions | User is logged in and sees correct menu for their role. |
| Author | System Developer |
| Exceptions | Account blocked by admin — system shows "Account suspended, contact admin". |

---

### 5. Post Food Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Post Food Listing |
| Use Case ID | UC-05 |
| Actor | Donor |
| Description | Donor adds surplus food details to the system for NGOs to claim. |
| Actions | • Donor logs in<br>• Opens post listing form<br>• Fills food name, type, quantity, expiry, location, image<br>• Submits form<br>• System validates and saves data<br>• System sends listing for moderation and notifies nearby NGOs |
| Alternative Paths | If expiry date is invalid, system shows error and asks to re-enter. |
| Pre Conditions | Donor must be registered and logged in. |
| Post Conditions | Food listing is saved with status Available (or Pending) and visible to receivers after approval. |
| Author | System Developer |
| Exceptions | Image upload fails — listing saved without photo; donor can edit later. |

---

### 6. Admin Moderates Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Admin Moderates Listing |
| Use Case ID | UC-06 |
| Actor | Admin |
| Description | Admin reviews new food listings before they go live on the portal. |
| Actions | • Admin logs in<br>• Opens moderation queue<br>• Reviews listing details and photo<br>• Approves or rejects listing<br>• System updates listing status |
| Alternative Paths | Admin rejects listing and adds reason; donor gets notification to fix and repost. |
| Pre Conditions | Donor submitted a new listing; admin is logged in. |
| Post Conditions | Approved listings appear in search; rejected listings stay hidden. |
| Author | System Developer |
| Exceptions | Listing deleted by donor before review — system removes it from moderation queue. |

---

### 7. Edit Food Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Edit Food Listing |
| Use Case ID | UC-07 |
| Actor | Donor |
| Description | Donor updates details of an active food listing. |
| Actions | • Donor opens My Donations<br>• Selects listing and clicks Edit<br>• Updates quantity, expiry, or address<br>• Saves changes<br>• System updates database |
| Alternative Paths | If listing is already claimed, system only allows minor edits (e.g. pickup notes). |
| Pre Conditions | Donor owns the listing; listing is not yet picked up. |
| Post Conditions | Updated listing appears in search and donor dashboard. |
| Author | System Developer |
| Exceptions | Listing expired while editing — system blocks save and marks listing as Expired. |

---

### 8. Delete Food Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Delete Food Listing |
| Use Case ID | UC-08 |
| Actor | Donor |
| Description | Donor removes a listing that is no longer available for pickup. |
| Actions | • Donor opens My Donations<br>• Selects listing and clicks Delete<br>• Confirms deletion<br>• System marks listing as cancelled or removes it |
| Alternative Paths | Donor cancels delete action — listing stays unchanged. |
| Pre Conditions | Listing belongs to donor and is not picked up yet. |
| Post Conditions | Listing no longer appears in NGO search. |
| Author | System Developer |
| Exceptions | Listing already claimed — system warns donor; admin may need to cancel claim first. |

---

### 9. Search Available Food by City

| Field | Description |
|-------|-------------|
| Use Case Title | Search Available Food by City |
| Use Case ID | UC-09 |
| Actor | Receiver/NGO |
| Description | NGO searches for available food donations in a selected city or area. |
| Actions | • NGO logs in<br>• Opens search page<br>• Enters city or area name<br>• System shows available listings sorted by expiry time |
| Alternative Paths | No listings found — system shows "No food available in this area". |
| Pre Conditions | NGO is logged in and verified. |
| Post Conditions | NGO sees list of food available near selected area. |
| Author | System Developer |
| Exceptions | Database connection error — system shows error and asks to refresh page. |

---

### 10. Search Food on Map

| Field | Description |
|-------|-------------|
| Use Case Title | Search Food on Map |
| Use Case ID | UC-10 |
| Actor | Receiver/NGO |
| Description | NGO views available donations on a map to find nearest pickup points. |
| Actions | • NGO opens map view<br>• System loads pins for available listings<br>• NGO clicks a pin to view listing details |
| Alternative Paths | Map API unavailable — system falls back to list view only. |
| Pre Conditions | Map API is configured; listings have location data. |
| Post Conditions | NGO can see location before claiming food. |
| Author | System Developer |
| Exceptions | Invalid GPS data on listing — pin hidden; listing still visible in list search. |

---

### 11. Claim Food Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Claim Food Listing |
| Use Case ID | UC-11 |
| Actor | Receiver/NGO |
| Description | NGO reserves an available food listing for pickup. |
| Actions | • NGO opens listing details<br>• Clicks Claim food<br>• System checks listing is still Available<br>• System marks listing as Claimed and links it to NGO |
| Alternative Paths | If listing already claimed, system shows error and returns NGO to search page. |
| Pre Conditions | Listing status is Available; NGO is logged in. |
| Post Conditions | Listing is reserved; other NGOs cannot claim the same item. |
| Author | System Developer |
| Exceptions | Listing expires during claim — system rejects claim and marks listing Expired. |

---

### 12. Failed Claim (Already Claimed)

| Field | Description |
|-------|-------------|
| Use Case Title | Failed Claim (Already Claimed) |
| Use Case ID | UC-12 |
| Actor | Receiver/NGO |
| Description | NGO tries to claim food that another NGO already reserved. |
| Actions | • NGO opens listing and clicks Claim<br>• System checks status is already Claimed<br>• System shows error message<br>• NGO returns to search results |
| Alternative Paths | NGO refreshes search to find other available listings. |
| Pre Conditions | Another NGO claimed the listing first. |
| Post Conditions | No duplicate claim is created. |
| Author | System Developer |
| Exceptions | None — normal business rule when listing is taken. |

---

### 13. Update Pickup Status

| Field | Description |
|-------|-------------|
| Use Case Title | Update Pickup Status |
| Use Case ID | UC-13 |
| Actor | Receiver/NGO |
| Description | NGO updates status after collecting food from the donor. |
| Actions | • NGO opens active claim<br>• Changes status to Picked Up or Delivered<br>• Submits update<br>• System saves status and notifies donor |
| Alternative Paths | NGO marks as Cancelled if pickup failed — admin may review case. |
| Pre Conditions | NGO has claimed the listing and collected the food. |
| Post Conditions | Donor and admin see updated status; record saved in history. |
| Author | System Developer |
| Exceptions | Notification service down — status still saved; alert sent when service is back. |

---

### 14. View Pickup Status

| Field | Description |
|-------|-------------|
| Use Case Title | View Pickup Status |
| Use Case ID | UC-14 |
| Actor | Donor |
| Description | Donor checks whether posted food has been claimed or picked up. |
| Actions | • Donor logs in<br>• Opens View pickup status or dashboard<br>• System shows each listing with current status |
| Alternative Paths | Donor filters by status (Available, Claimed, Picked Up). |
| Pre Conditions | Donor has at least one listing. |
| Post Conditions | Donor knows if food was collected. |
| Author | System Developer |
| Exceptions | No listings found — system shows empty state message. |

---

### 15. Send Notification on New Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Send Notification on New Listing |
| Use Case ID | UC-15 |
| Actor | System |
| Description | System alerts nearby NGOs when new food is posted in their area. |
| Actions | • System detects new approved listing<br>• Finds NGOs in same city or area<br>• Sends dashboard notification and optional email |
| Alternative Paths | No NGOs in area — notification skipped; listing still visible in search. |
| Pre Conditions | Approved listing posted; NGOs registered in that area. |
| Post Conditions | NGOs are alerted about new food nearby. |
| Author | System Developer |
| Exceptions | Email server failure — dashboard notification still sent. |

---

### 16. Send Notification on Status Update

| Field | Description |
|-------|-------------|
| Use Case Title | Send Notification on Status Update |
| Use Case ID | UC-16 |
| Actor | System |
| Description | System notifies users when pickup or delivery status changes. |
| Actions | • System records status change<br>• Identifies affected users (donor, volunteer)<br>• Sends dashboard notification |
| Alternative Paths | Volunteer not assigned — only donor gets notification. |
| Pre Conditions | NGO or volunteer updated pickup/delivery status. |
| Post Conditions | Relevant users see update on their dashboard. |
| Author | System Developer |
| Exceptions | User offline — notification stored and shown on next login. |

---

### 17. Update Expired Listing

| Field | Description |
|-------|-------------|
| Use Case Title | Update Expired Listing |
| Use Case ID | UC-17 |
| Actor | System |
| Description | System automatically marks listings as expired when best-before time passes. |
| Actions | • Scheduled job runs at set interval<br>• Finds listings past best-before time<br>• Marks them as Expired or Unavailable<br>• Removes from active NGO search |
| Alternative Paths | Listing already Claimed but not picked up — admin notified to follow up. |
| Pre Conditions | Listing best-before time has passed. |
| Post Conditions | NGOs cannot claim expired food. |
| Author | System Developer |
| Exceptions | Job fails to run — next run catches expired listings; no data loss. |

---

### 18. View Pickup Task

| Field | Description |
|-------|-------------|
| Use Case Title | View Pickup Task |
| Use Case ID | UC-18 |
| Actor | Volunteer |
| Description | Volunteer sees assigned pickup tasks with address and food details. |
| Actions | • Volunteer logs in<br>• Opens Pickup tasks page<br>• System shows task list with address, food summary, contact info |
| Alternative Paths | No tasks assigned — system shows "No tasks yet". |
| Pre Conditions | Volunteer is logged in; task assigned by admin or system. |
| Post Conditions | Volunteer knows where to go for pickup. |
| Author | System Developer |
| Exceptions | Task cancelled by admin — removed from volunteer list with notice. |

---

### 19. Update Delivery Task

| Field | Description |
|-------|-------------|
| Use Case Title | Update Delivery Task |
| Use Case ID | UC-19 |
| Actor | Volunteer |
| Description | Volunteer updates progress and completion of a delivery task. |
| Actions | • Volunteer selects assigned task<br>• Marks status In Progress<br>• After delivery, marks Completed<br>• System updates donation workflow |
| Alternative Paths | Volunteer marks task as Failed — admin notified to reassign. |
| Pre Conditions | Volunteer has an assigned pickup or delivery task. |
| Post Conditions | Donor, NGO, and admin see updated delivery progress. |
| Author | System Developer |
| Exceptions | Status update conflict — system shows latest status and asks to refresh. |

---

### 20. View Routes

| Field | Description |
|-------|-------------|
| Use Case Title | View Routes |
| Use Case ID | UC-20 |
| Actor | Volunteer |
| Description | Volunteer views route or map directions for pickup and drop-off. |
| Actions | • Volunteer opens route view for a task<br>• System shows map or ordered address list<br>• Volunteer follows route to complete task |
| Alternative Paths | Map unavailable — system shows text addresses only. |
| Pre Conditions | Task has pickup and drop-off addresses. |
| Post Conditions | Volunteer can follow route for pickup/delivery. |
| Author | System Developer |
| Exceptions | Missing address on listing — volunteer contacts NGO via listed phone. |

---

### 21. Manage Users

| Field | Description |
|-------|-------------|
| Use Case Title | Manage Users |
| Use Case ID | UC-21 |
| Actor | Admin |
| Description | Admin approves, verifies, or blocks user accounts on the portal. |
| Actions | • Admin logs in<br>• Opens user management page<br>• Searches user by name or email<br>• Approves NGO, blocks fake account, or updates status |
| Alternative Paths | Admin resets user password on request after identity check. |
| Pre Conditions | Admin is logged in. |
| Post Conditions | User account status is updated in the system. |
| Author | System Developer |
| Exceptions | Attempt to block own admin account — system prevents action. |

---

### 22. View Reports

| Field | Description |
|-------|-------------|
| Use Case Title | View Reports |
| Use Case ID | UC-22 |
| Actor | Admin |
| Description | Admin views summary reports on donations, claims, and deliveries. |
| Actions | • Admin opens reports section<br>• Selects report type (city-wise, meals saved, etc.)<br>• System generates charts or tables from history data |
| Alternative Paths | Admin exports report to PDF or Excel for supervisor review. |
| Pre Conditions | System has donation and claim history data. |
| Post Conditions | Admin sees summary for monitoring and project demo. |
| Author | System Developer |
| Exceptions | No data for selected date range — system shows empty report message. |

---

### 23. Generate Reports

| Field | Description |
|-------|-------------|
| Use Case Title | Generate Reports |
| Use Case ID | UC-23 |
| Actor | System |
| Description | System builds report data from stored donation and claim history. |
| Actions | • Admin requests a report<br>• System queries history tables<br>• Aggregates counts and trends<br>• Returns result to admin dashboard |
| Alternative Paths | Large date range — system may take longer and show loading message. |
| Pre Conditions | Data exists in database. |
| Post Conditions | Report displayed on admin dashboard. |
| Author | System Developer |
| Exceptions | Query timeout — system asks admin to narrow date range. |

---

### 24. Handle Disputes

| Field | Description |
|-------|-------------|
| Use Case Title | Handle Disputes |
| Use Case ID | UC-24 |
| Actor | Admin |
| Description | Admin resolves issues between donor and NGO (wrong quantity, no pickup, etc.). |
| Actions | • Admin opens dispute ticket<br>• Reads details from donor and NGO<br>• Updates listing status or adds admin note<br>• Notifies both parties of decision |
| Alternative Paths | Admin closes dispute as invalid if not enough evidence. |
| Pre Conditions | Donor or NGO raised a dispute on a listing or claim. |
| Post Conditions | Dispute marked resolved; record kept in history. |
| Author | System Developer |
| Exceptions | One party unresponsive — admin decides based on available logs. |

---

### 25. View Claim History

| Field | Description |
|-------|-------------|
| Use Case Title | View Claim History |
| Use Case ID | UC-25 |
| Actor | Receiver/NGO |
| Description | NGO reviews past food claims and pickup records. |
| Actions | • NGO logs in<br>• Opens View history<br>• System lists past claims with date, donor, and status |
| Alternative Paths | NGO filters history by date or status. |
| Pre Conditions | NGO has claimed food at least once before. |
| Post Conditions | NGO can review previous pickups for their records. |
| Author | System Developer |
| Exceptions | History record missing — system shows partial list with note. |

---

### 26. View My Donations

| Field | Description |
|-------|-------------|
| Use Case Title | View My Donations |
| Use Case ID | UC-26 |
| Actor | Donor |
| Description | Donor views all active and completed food listings they posted. |
| Actions | • Donor logs in<br>• Opens View my donations<br>• System shows listings with status and totals |
| Alternative Paths | Donor sorts by date or filters by status. |
| Pre Conditions | Donor has posted at least one listing. |
| Post Conditions | Donor sees impact summary (e.g. total meals donated). |
| Author | System Developer |
| Exceptions | None — empty list shown if donor has no listings yet. |

---

### 27. Submit Feedback After Pickup

| Field | Description |
|-------|-------------|
| Use Case Title | Submit Feedback After Pickup |
| Use Case ID | UC-27 |
| Actor | Receiver/NGO |
| Description | NGO rates food quality and hygiene after a successful pickup. |
| Actions | • NGO opens completed claim<br>• Selects rating and adds short comment<br>• Submits feedback<br>• System saves feedback linked to listing |
| Alternative Paths | NGO skips feedback — claim still marked complete. |
| Pre Conditions | Pickup status is completed. |
| Post Conditions | Feedback stored for trust and future reference. |
| Author | System Developer |
| Exceptions | Feedback already submitted — system prevents duplicate entry. |

---

### 28. Invalid Login Attempt

| Field | Description |
|-------|-------------|
| Use Case Title | Invalid Login Attempt |
| Use Case ID | UC-28 |
| Actor | Donor, Receiver/NGO, Volunteer, Admin |
| Description | User enters wrong credentials and cannot access the system. |
| Actions | • User opens login page<br>• Enters wrong email or password<br>• System rejects login<br>• Shows error: Invalid email or password |
| Alternative Paths | After several failed tries, system may ask for captcha or temporary lockout. |
| Pre Conditions | User is on login page. |
| Post Conditions | User remains logged out and can retry. |
| Author | System Developer |
| Exceptions | Account locked after too many attempts — user must wait or contact admin. |

---

## 6. Adopted Methodology

After studying Waterfall, Spiral, Agile, and RAD models, I selected the **Virtual University Process Model** for the Community Food Wastage Reduction Portal. VU recommends this model for IT601P because it combines the structure of Waterfall with the flexibility of Spiral. This makes it suitable for academic projects that need clear deliverables and room to refine work after feedback.

The VU Process Model is a hybrid approach. It keeps proper documentation and also handles risk through review at each phase. It has **6 phases**:

---

### Phase 1: System Requirement Specification (SRS)

**Purpose:** Define what the system will do before building it.

**Activities for my project:**
- Conducted interviews with mock donors, NGOs, and volunteers to understand food donation problems.
- Documented functional requirements: user registration, food listing, claiming, notifications, delivery tracking, and admin reports.
- Documented non-functional requirements: usability, response time under 3 seconds, data security, and availability.
- Created use case diagram and detailed use case descriptions for all actors (Donor, Receiver/NGO, Volunteer, Admin, System).

**Deliverable:** SRS document.

This phase helps avoid scope creep later and acts as the base plan for the whole project.

---

### Phase 2: Design Phase

**Purpose:** Define how the system will work.

**Activities for my project:**
- **Database Design:** Created ER diagram and normalized tables for Users, Food Listings, Claims, and Notifications.
- **System Design:** Developed DFD Level 0 and Level 1, and sequence diagrams for "Post Food" and "Claim Food" flows.
- **UI/UX Design:** Designed wireframes for donor dashboard, NGO dashboard, volunteer panel, and admin panel in draw.io.
- **Architecture:** Chose 3-tier architecture — Presentation layer, Business Logic layer, and Database layer.

**Deliverable:** Design document with all diagrams.

This phase reduces coding errors because we plan the system properly before development starts.

---

### Phase 3: Development Phase

**Purpose:** Convert design into working code.

**Activities for my project:**
- Used PHP/Laravel for backend and HTML/CSS/JS for frontend.
- Developed modules one by one: first User Management, then Food Listing, then Claiming, then Reporting.
- Followed modular coding standards so code stays easy to maintain.
- Integrated MySQL database with backend using Laravel migrations and Eloquent models.

**Deliverable:** Working software prototype.

Each module was tested after development before moving to the next module.

---

### Phase 4: Test Phase

**Purpose:** Verify the system works as per SRS and is error-free.

**Activities for my project:**
- **Unit Testing:** Tested each function like login, post listing, and claim food individually.
- **Integration Testing:** Checked data flow between modules (e.g. donor posts food → NGO sees listing → claim updates status).
- **System Testing:** Tested full workflow from donor posting food to NGO pickup and delivery update.
- **User Acceptance Testing:** Got feedback from 3 mock users to check usability and fixed reported issues.

**Deliverable:** Test cases and test report.

Bugs found during testing were fixed before final submission.

---

### Phase 5: Final Report

**Purpose:** Document the entire project for evaluation and future maintenance.

**Activities for my project:**
- Compiled all chapters: Introduction, Literature Review, System Analysis & Design, Implementation, Testing, and Conclusion.
- Included diagrams, screenshots, source code snippets, and a simple user manual.
- Ensured formatting follows VU guidelines.

**Deliverable:** Complete project report in MS Word/PDF format.

---

### Phase 6: Presentation and Viva

**Purpose:** Demonstrate the system and defend the project logic.

**Activities for my project:**
- Prepared PowerPoint slides covering problem statement, solution, methodology, and live demo.
- Prepared for viva questions on database design, use cases, and why VU Process Model was chosen.
- Created a working demo video for submission backup.

**Deliverable:** Presentation slides and demo video.

---

### 6.1 Why VU Process Model (Summary)

| Compared Model | Limitation for this project | Why VU model fits better |
|----------------|----------------------------|---------------------------|
| Waterfall | Too rigid; hard to fix design issues late | VU adds review loops like Spiral |
| Spiral | Heavy on risk analysis for a small uni team | VU is simpler but still structured |
| Agile | Less emphasis on formal SRS/design docs | VU requires SRS and design — good for VU evaluation |
| RAD | Needs very experienced team and stable requirements | VU phases match IT601P deliverables step by step |

---

## 7. Work Plan (MS Project Schedule)

> Create this schedule in **Microsoft Project**. Below is the WBS you can type in or import. Durations assume a ~12–14 week semester project with a small team (2–3 students). Adjust dates to your actual start.

### 7.1 Project Summary
| Item | Detail |
|------|--------|
| Project name | Food Wastage Reduction Portal |
| Estimated duration | 12 weeks |
| Tool | Microsoft Project |

### 7.2 Work Breakdown Structure (WBS)

| WBS | Task Name | Duration | Predecessor | Assigned To |
|-----|-----------|----------|-------------|-------------|
| 1 | Project Initiation | 1 week | — | Team |
| 1.1 | Finalize requirements & SRS | 3 days | — | Analyst |
| 1.2 | Supervisor review of SRS | 2 days | 1.1 | Supervisor |
| 1.3 | Setup Git repo & dev environment | 2 days | 1.1 | Dev |
| 2 | System Design | 1.5 weeks | 1 | Team |
| 2.1 | Use case & activity diagrams | 2 days | 1.2 | Analyst |
| 2.2 | Database ER design | 2 days | 1.2 | Dev |
| 2.3 | UI wireframes (main pages) | 3 days | 1.2 | UI |
| 2.4 | Design review | 1 day | 2.1, 2.2, 2.3 | Supervisor |
| 3 | Increment 1 — Authentication | 1.5 weeks | 2 | Dev |
| 3.1 | User registration API + forms | 3 days | 2.4 | Backend |
| 3.2 | Login/logout + session/JWT | 2 days | 3.1 | Backend |
| 3.3 | Role-based dashboard layout | 2 days | 3.2 | Frontend |
| 3.4 | Test login module | 1 day | 3.3 | QA |
| 4 | Increment 2 — Donor Module | 2 weeks | 3 | Dev |
| 4.1 | Post food listing (CRUD) | 4 days | 3.4 | Full stack |
| 4.2 | Image upload (Firebase/local) | 2 days | 4.1 | Backend |
| 4.3 | Donor dashboard & history | 3 days | 4.1 | Frontend |
| 4.4 | Test donor flows | 1 day | 4.3 | QA |
| 5 | Increment 3 — NGO Module | 2 weeks | 4 | Dev |
| 5.1 | Search by city/location | 3 days | 4.4 | Full stack |
| 5.2 | Map integration (Google Maps) | 3 days | 5.1 | Frontend |
| 5.3 | Claim listing logic | 2 days | 5.1 | Backend |
| 5.4 | Update pickup status + history | 2 days | 5.3 | Full stack |
| 5.5 | Test NGO flows | 1 day | 5.4 | QA |
| 6 | Increment 4 — Admin & System | 1.5 weeks | 5 | Dev |
| 6.1 | Admin login & user management | 3 days | 5.5 | Full stack |
| 6.2 | Listing moderation | 2 days | 6.1 | Full stack |
| 6.3 | Auto-expire listings (cron/job) | 2 days | 6.2 | Backend |
| 6.4 | Notifications (dashboard/email) | 2 days | 6.2 | Backend |
| 7 | Increment 5 — Volunteer & Reports | 1 week | 6 | Dev |
| 7.1 | Volunteer task list & status | 3 days | 6.4 | Full stack |
| 7.2 | Basic admin reports | 2 days | 6.4 | Full stack |
| 8 | Testing & Final Delivery | 1.5 weeks | 7 | Team |
| 8.1 | Integration testing | 3 days | 7.2 | QA |
| 8.2 | Bug fixing | 3 days | 8.1 | Dev |
| 8.3 | Prepare final report & demo | 2 days | 8.2 | Team |
| 8.4 | Final presentation / viva prep | 1 day | 8.3 | Team |

### 7.3 Milestones
| Milestone | Target (example) |
|-----------|------------------|
| M1 — SRS approved | End of Week 1 |
| M2 — Design complete | End of Week 2 |
| M3 — Login demo ready | End of Week 4 |
| M4 — Donor + NGO demo ready | End of Week 8 |
| M5 — Full system demo | End of Week 11 |
| M6 — Final submission | End of Week 12 |

### 7.4 MS Project Tips (quick)
1. Open MS Project → New Blank Project.  
2. Set project start date in **Project → Project Information**.  
3. Enter tasks from table above in **Task Name** column.  
4. Add **Duration** (e.g. `3 days`, `1 wk`).  
5. Link tasks in **Predecessors** column (e.g. `5` for task 5, `5,6` for multiple).  
6. Switch to **Gantt Chart** view for supervisor submission.  
7. Export: **File → Export → Save as PDF** for attaching to SRS.

---

## Supervisor Details

| Field | Value |
|-------|--------|
| Name | Hasnain Ahmed |
| Email | hasnain@vu.edu.pk |
| Microsoft Teams | tohasnain.bukhari@outlook.com |
