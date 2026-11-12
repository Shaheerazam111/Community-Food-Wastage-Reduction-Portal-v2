"""
Build VU-style final report (DOCX) from SRS-v2 and diagram PNGs.
Run: python Deliverables/Demo/build_final_report.py
"""
from __future__ import annotations

import os
from pathlib import Path

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.shared import Inches, Pt

REPO = Path(__file__).resolve().parents[2]
IMAGES = REPO / "Documentation" / "images" / "v2"
OUT = REPO / "Deliverables" / "Community-Food-Wastage-Reduction-Portal-Final-Report.docx"

PROJECT = "Community Food Wastage Reduction Portal"
SUPERVISOR = "Hasnain Ahmed"


def set_normal_style(doc: Document) -> None:
    style = doc.styles["Normal"]
    style.font.name = "Times New Roman"
    style.font.size = Pt(12)


def add_centered(doc: Document, text: str, bold: bool = False, size: int = 12) -> None:
    p = doc.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run(text)
    run.bold = bold
    run.font.size = Pt(size)
    run.font.name = "Times New Roman"


def heading(doc: Document, text: str, level: int = 1) -> None:
    h = doc.add_heading(text, level=level)
    for run in h.runs:
        run.font.name = "Times New Roman"


def para(doc: Document, text: str) -> None:
    p = doc.add_paragraph(text)
    for run in p.runs:
        run.font.name = "Times New Roman"
        run.font.size = Pt(12)


def figure(doc: Document, filename: str, caption: str, width: float = 6.0) -> None:
    path = IMAGES / filename
    if not path.is_file():
        para(doc, f"[Missing image: {filename}]")
        return
    doc.add_picture(str(path), width=Inches(width))
    cap = doc.add_paragraph(caption)
    cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
    for run in cap.runs:
        run.font.name = "Times New Roman"
        run.font.size = Pt(10)
        run.italic = True
    doc.add_paragraph()


def page_break(doc: Document) -> None:
    doc.add_page_break()


def front_matter(doc: Document) -> None:
    add_centered(doc, "Final Project Report", bold=True, size=16)
    doc.add_paragraph()
    add_centered(doc, PROJECT, bold=True, size=14)
    for _ in range(6):
        doc.add_paragraph()
    add_centered(doc, "Project Supervisor")
    add_centered(doc, SUPERVISOR)
    doc.add_paragraph()
    add_centered(doc, "Submitted By")
    doc.add_paragraph()
    add_centered(doc, "<<Project Group ID>>")
    doc.add_paragraph()
    add_centered(doc, "<<Group Member Name>>\t\t<<VU ID>>")
    add_centered(doc, "<<Group Member Name>>\t\t<<VU ID>>")
    for _ in range(4):
        doc.add_paragraph()
    add_centered(
        doc,
        "Software Projects & Research Section,\n"
        "Department of Computer Sciences,\n"
        "Virtual University of Pakistan",
    )
    page_break(doc)

    heading(doc, "CERTIFICATE", 1)
    para(
        doc,
        f"This is to certify that <<First Member Name>> (<<VU ID>>), <<Second Member Name>> "
        f"(<<VU ID>>) have worked on and completed their Software Project at Software & Research "
        "Projects Section, Department of Computer Sciences, Virtual University of Pakistan in partial "
        "fulfillment of the requirement for the degree of BS in Computer Sciences under my guidance "
        "and supervision.\n\n"
        "In our opinion, it is satisfactory and up to the mark and therefore fulfills the requirements "
        "of BS in Computer Sciences.",
    )
    doc.add_paragraph()
    para(doc, f"Supervisor / Internal Examiner\n\n{SUPERVISOR}\nSupervisor,\nSoftware Projects & Research Section")
    para(doc, "___________________\n(Signature)")
    para(doc, "External Examiner/Subject Specialist\n<<External Supervisor Name>>\n___________________\n(Signature)")
    para(doc, "Accepted By:\n_____________\n(For office use)")
    page_break(doc)

    heading(doc, "EXORDIUM", 1)
    for line in [
        "In the name of Allah, the Compassionate, the Merciful.",
        "Praise be to Allah, Lord of Creation,",
        "The Compassionate, the Merciful,",
        "King of Judgment-day!",
        "You alone we worship, and to You alone we pray for help,",
        "Guide us to the straight path",
        "The path of those who You have favored,",
        "Not of those who have incurred Your wrath,",
        "Nor of those who have gone astray.",
    ]:
        para(doc, line)
    page_break(doc)

    heading(doc, "DEDICATION", 1)
    para(doc, "<<Write your dedication here.>>")
    page_break(doc)

    heading(doc, "ACKNOWLEDGEMENT", 1)
    para(
        doc,
        "We thank our supervisor Hasnain Ahmed for guidance during this project. "
        "We also thank the Virtual University faculty and our families for support.",
    )
    page_break(doc)

    heading(doc, "PREFACE", 1)
    para(
        doc,
        f"This report describes the design and implementation of the {PROJECT}. "
        "Chapter 1 covers requirements and use cases. Chapter 2 covers design diagrams. "
        "Chapter 3 explains how the Laravel portal was built and tested.",
    )
    page_break(doc)

    heading(doc, "TABLE OF CONTENTS", 1)
    para(
        doc,
        "Open this document in Microsoft Word, click inside the Table of Contents below, "
        "then References → Table of Contents → Update Table to refresh page numbers after any edits.",
    )
    toc_lines = [
        "CHAPTER 1 — Gathering & Analyzing Info",
        "1.1 Introduction",
        "1.2 Purpose",
        "1.3 Scope",
        "1.4 Definitions, acronyms and abbreviations",
        "1.5 Project requirements (Functional & Non-Functional)",
        "1.6 Use cases and usage scenarios",
        "1.7 Development methodology & work plan",
        "CHAPTER 2 — Designing the Project",
        "2.1–2.4 Introduction, purpose, scope, definitions",
        "2.5 Architectural representation",
        "2.6 Dynamic model: sequence diagrams",
        "2.7 Object model: classes",
        "2.8 Database model",
        "2.9 Graphical user interfaces",
        "CHAPTER 3 — Development",
        "3.1 Development plan & implementation",
        "REFERENCES",
        "APPENDIX",
    ]
    for line in toc_lines:
        para(doc, line)
    page_break(doc)


def chapter1(doc: Document) -> None:
    add_centered(doc, "CHAPTER 1", bold=True)
    add_centered(doc, "Gathering & Analyzing Info", bold=True)
    page_break(doc)

    heading(doc, "1.1 Introduction", 2)
    para(
        doc,
        "Cities produce a large amount of surplus food from restaurants, hotels, events, and households. "
        "At the same time, NGOs and community kitchens often lack enough food for people in need. "
        "Donors and receivers rarely find each other in time, so edible food expires and is thrown away. "
        f"The {PROJECT} is a web portal that connects donors who have extra food with NGO recipients "
        "who can collect it before the best-before time. The system also supports admin reporting, "
        "user verification, listing moderation, dashboard notifications, volunteer pickup tasks, "
        "and feedback after delivery.",
    )

    heading(doc, "1.2 Purpose", 2)
    para(
        doc,
        "This chapter records how requirements were gathered and analysed for the portal. "
        "It states what the system must do for donors, recipients, volunteers, and admins, "
        "and how development was planned using the Virtual University process model.",
    )

    heading(doc, "1.3 Scope", 2)
    para(
        doc,
        "In scope: registration and login for donor, recipient, volunteer, and admin; "
        "food listings with category, quantity, expiry, and optional photo; GPS and city search; "
        "atomic claim so only one NGO gets each listing; status flow AVAILABLE → CLAIMED → DELIVERED "
        "(and EXPIRED when time runs out); notifications; NGO document upload and admin approval; "
        "admin user block/unblock and listing moderation; reports and CSV export; PHPUnit tests for "
        "search and concurrent claim.\n\n"
        "Out of scope for this version: paid delivery integration, native mobile apps, "
        "multi-language UI, and email/SMS alerts (dashboard alerts are implemented instead).",
    )

    heading(doc, "1.4 Definitions, acronyms and abbreviations", 2)
    items = [
        ("Listing", "One food offer stored in the food_listings table."),
        ("Donor", "User with role donor who posts surplus food."),
        ("Recipient / NGO", "User with role recipient who searches and claims food."),
        ("Volunteer", "User who sees pickup tasks and can mark delivery complete."),
        ("Admin", "Portal operator with reports and moderation tools."),
        ("Claim", "Recipient reserves a listing; status becomes CLAIMED."),
        ("Haversine formula", "SQL distance calculation using latitude and longitude."),
        ("SRS", "Software Requirements Specification."),
        ("ERD", "Entity–relationship diagram."),
        ("CSV", "Comma-separated values file for report export."),
    ]
    for term, meaning in items:
        para(doc, f"{term} — {meaning}")

    heading(doc, "1.5 Project requirements", 2)
    heading(doc, "1.5.1 Functional requirements", 3)
    para(doc, "Main functional requirements implemented in the Laravel application:")
    fr_rows = [
        "FR-A01–A05: Register donor/recipient/volunteer, session login/logout, role-based home pages; admin is seeded, not self-registered.",
        "FR-D01–D06: Donor creates listings (title, veg/non_veg, quantity, expiry); pickup location copied from profile; view mine; mark DELIVERED; cancel AVAILABLE listing.",
        "FR-R01–R04: Recipient searches by latitude, longitude, radius, or city; results exclude claimed/expired items; atomic claim; view my claims.",
        "FR-M01–M04: Admin dashboard, filtered reports, KPIs, CSV export.",
        "FR-M05 (extended): Admin block/unblock users, approve NGO documents, moderate listings before publish.",
        "FR-V01–V02: Volunteer task list and mark pickup complete.",
        "FR-N01: Dashboard notifications when food is posted, claimed, or delivered.",
        "FR-F01: Recipient may submit rating/feedback after delivery.",
        "FR-S01–S02: Expired listings hidden in search; hourly food:expire artisan command.",
    ]
    for row in fr_rows:
        para(doc, f"• {row}")

    heading(doc, "1.5.2 Non-functional requirements", 3)
    nfr = [
        "NFR-01 Security: Bcrypt passwords; middleware checks role and active user.",
        "NFR-02 Integrity: Single conditional UPDATE on claim prevents double booking.",
        "NFR-03 Performance: Indexed filters on status/expiry; admin report range capped at 366 days.",
        "NFR-04 Usability: Bootstrap layout; clear forms for each role.",
        "NFR-05 Maintainability: Thin controllers; AdminReportService for analytics.",
        "NFR-06 Compatibility: Modern browsers; same routes serve HTML and JSON.",
        "NFR-07 Testability: Postman collection, /test-apis page, PHPUnit feature tests.",
    ]
    for row in nfr:
        para(doc, f"• {row}")

    heading(doc, "1.6 Use cases and usage scenarios", 2)
    heading(doc, "1.6.1 Use case diagram", 3)
    figure(
        doc,
        "figure-01-use-case.png",
        "Figure 1.1 — Use case diagram (Community Food Wastage Reduction Portal v2)",
    )
    para(
        doc,
        "Actors are Donor, Recipient (NGO), Admin, Volunteer, and System. "
        "Donors post and manage listings; recipients search and claim; admins verify users and view reports; "
        "volunteers complete pickup tasks; the system expires stale listings on a schedule.",
    )

    heading(doc, "1.6.2 Usage scenarios", 3)
    scenarios = [
        (
            "UC-Post-Listing",
            "A logged-in donor opens Post donation, enters food details and best-before time, "
            "and submits. The listing is stored with pickup coordinates from the donor profile. "
            "If moderation is on, admin approves it before it appears in search. Nearby NGOs get a dashboard notification.",
        ),
        (
            "UC-Search-Claim",
            "A recipient opens Search food, enters map location or city, and runs search. "
            "The system returns available listings sorted by distance. The recipient claims one listing; "
            "a conditional database update ensures only one claim succeeds if two users try at once.",
        ),
        (
            "UC-Deliver",
            "After pickup, the donor or volunteer marks the listing DELIVERED. "
            "The recipient can leave feedback. Meals-saved estimate updates on the donor dashboard.",
        ),
        (
            "UC-Admin-Report",
            "Admin opens Reports, picks a date range, views trends and leader tables, "
            "and exports a CSV file for submission or analysis.",
        ),
    ]
    for title, text in scenarios:
        para(doc, f"{title}: {text}")

    figure(
        doc,
        "figure-10-end-to-end-flow.png",
        "Figure 1.2 — End-to-end activity flow",
        width=6.2,
    )

    heading(doc, "1.7 Development methodology", 2)
    heading(doc, "1.7.1 Chosen methodology", 3)
    para(
        doc,
        "The Virtual University Process Model was used. It combines structured documentation (like Waterfall) "
        "with review steps (like Spiral) so each phase produces a clear deliverable before coding continues.",
    )
    heading(doc, "1.7.2 Reasons for chosen methodology", 3)
    para(
        doc,
        "Agile and RAD focus less on formal SRS and design documents required for IT601P. "
        "Pure Waterfall is hard to adjust after supervisor feedback. "
        "The VU model fits a small student team: SRS first, then design diagrams, then incremental development and testing.",
    )
    heading(doc, "1.7.3 Work plan (Gantt chart)", 3)
    para(
        doc,
        "Work was split into: (1) SRS and environment setup, (2) design diagrams and database, "
        "(3) auth module, (4) donor listings, (5) search and claim, (6) admin reports, "
        "(7) portal extensions (notifications, verification, volunteer, moderation), "
        "(8) testing and final report. "
        "A detailed WBS with durations is in Documentation/Markdown/SRS-sections.md section 7; "
        "paste your MS Project Gantt screenshot here before submission.",
    )
    heading(doc, "1.7.4 Project schedule (submission calendar)", 3)
    para(
        doc,
        "Align task dates with your VULMS calendar: SRS and design early in the semester, "
        "working prototype before mid-term review, full portal and tests before final zip upload "
        "(application + report + presentation in one folder).",
    )


def chapter2(doc: Document) -> None:
    page_break(doc)
    add_centered(doc, "CHAPTER 2", bold=True)
    add_centered(doc, "Designing the Project", bold=True)
    page_break(doc)

    heading(doc, "2.1 Introduction", 2)
    para(
        doc,
        "This chapter explains how the portal was designed: three-tier architecture, "
        "database schema, listing state machine, interaction sequences, and main UI screens.",
    )
    heading(doc, "2.2 Purpose", 2)
    para(doc, "To show how user requirements map to architecture, data model, and screens before implementation.")
    heading(doc, "2.3 Scope", 2)
    para(doc, "Design covers the Laravel + Blade + MySQL system as built in this repository, not a separate mobile app.")
    heading(doc, "2.4 Definitions", 2)
    para(doc, "Same terms as Chapter 1. MVC means Model–View–Controller pattern used by Laravel.")

    heading(doc, "2.5 Architectural representation", 2)
    figure(
        doc,
        "figure-03-system-architecture.png",
        "Figure 2.1 — Three-tier system architecture",
    )
    para(
        doc,
        "Users access Blade views in the browser. Laravel routes requests through middleware "
        "(auth, admin, active user) to controllers. Eloquent models read and write MySQL tables. "
        "The scheduler runs food:expire hourly. The same web.php routes can return HTML or JSON.",
    )

    heading(doc, "2.6 Dynamic model: sequence diagrams", 2)
    sequences = [
        ("figure-05-sequence-auth.png", "Figure 2.2 — Register and login sequence"),
        ("figure-06-sequence-post-listing.png", "Figure 2.3 — Post food listing"),
        ("figure-07-sequence-search-claim.png", "Figure 2.4 — Search and claim (race-safe update)"),
        ("figure-08-sequence-deliver-expire.png", "Figure 2.5 — Mark delivered and expire job"),
        ("figure-09-sequence-admin-reports.png", "Figure 2.6 — Admin reports and CSV export"),
    ]
    for fname, cap in sequences:
        figure(doc, fname, cap, width=5.8)

    figure(
        doc,
        "figure-04-listing-state.png",
        "Figure 2.7 — Listing status state diagram",
        width=5.5,
    )

    heading(doc, "2.7 Object model / logical model (classes)", 2)
    figure(
        doc,
        "figure-11-class-diagram.png",
        "Figure 2.9 — Simplified class diagram (Laravel models)",
        width=5.5,
    )
    para(
        doc,
        "The implementation uses Laravel models and thin controllers (simplified class view):"
    )
    classes = [
        "User — authentication, role, profile fields, location; relations to listings and notifications.",
        "FoodListing — donor_id, food details, pickup snapshot, status, claimed_by, timestamps.",
        "PortalNotification — user_id, type, message, read flag.",
        "VerificationDocument — recipient upload metadata for admin review.",
        "ListingFeedback — rating and comment after delivery.",
        "AuthController, FoodListingController, ClaimController — core donation workflow.",
        "Admin* controllers — dashboard, reports, users, verification, listing moderation.",
        "NotificationService — create alerts when listings change.",
    ]
    for c in classes:
        para(doc, f"• {c}")
    para(
        doc,
        "A full multi-table design (separate donations, claims, items) is documented in "
        "Documentation/Markdown/Design-doc-sections.md for future migration; v2 keeps one claim per listing row.",
    )

    heading(doc, "2.8 Database model", 2)
    figure(doc, "figure-02-erd.png", "Figure 2.8 — Entity–relationship diagram (v2 core)")
    para(
        doc,
        "Core tables are users and food_listings. Phase-2 migrations add portal_notifications, "
        "verification_documents, listing_feedback, and moderation flags on listings. "
        "Foreign keys link listings to donor and recipient users.",
    )

    heading(doc, "2.9 Graphical user interfaces", 2)
    para(doc, "Main screens (Blade templates under resources/views/):")
    screens = [
        "Home, login, register",
        "Donor dashboard, post donation, my donations",
        "Recipient search food, my claims, verification upload",
        "Volunteer tasks",
        "Notifications inbox",
        "Profile edit",
        "Admin dashboard, reports, users, verification queue, listing moderation",
    ]
    for s in screens:
        para(doc, f"• {s}")
    para(
        doc,
        "Add screenshots of these pages in the Appendix before VULMS upload "
        "(see Documentation/Markdown/demo-and-viva-script.md for a demo path).",
    )


def chapter3(doc: Document) -> None:
    page_break(doc)
    add_centered(doc, "CHAPTER 3", bold=True)
    add_centered(doc, "Development", bold=True)
    page_break(doc)

    heading(doc, "3.1 Development plan and implementation", 2)
    para(
        doc,
        "Technology stack: PHP 8, Laravel 9+, Blade, Bootstrap 5, plain CSS/JS, MySQL 8. "
        "Development followed the architecture in Figure 2.1. Modules were built in order so each could be demoed separately.",
    )

    heading(doc, "3.1.1 Environment and database", 3)
    para(
        doc,
        "composer install, .env configuration, php artisan migrate, and UserSeeder create demo accounts. "
        "Migrations define users, food_listings, and phase-2 portal tables. "
        "Legacy food table was removed in a later migration.",
    )

    heading(doc, "3.1.2 Authentication module", 3)
    para(
        doc,
        "AuthController handles register, login, logout with validation and bcrypt hashing. "
        "RedirectIfAuthenticated sends each role to the correct dashboard. "
        "EnsureUserIsActive blocks disabled accounts.",
    )

    heading(doc, "3.1.3 Donor module", 3)
    para(
        doc,
        "FoodListingController::store validates expiry in the future and copies donor coordinates. "
        "Donors view /food/mine, mark deliver on claimed rows, and cancel available listings. "
        "Optional photo upload uses local storage (php artisan storage:link).",
    )

    heading(doc, "3.1.4 Recipient module", 3)
    para(
        doc,
        "FoodListingController::search applies Haversine distance, radius, or city filter. "
        "ClaimController runs one UPDATE … WHERE status = AVAILABLE to avoid double claims. "
        "RecipientVerificationController stores NGO documents for admin approval.",
    )

    heading(doc, "3.1.5 Admin and volunteer module", 3)
    para(
        doc,
        "AdminReportController aggregates KPIs and exports CSV. "
        "AdminUserController blocks users; AdminVerificationController approves documents; "
        "AdminListingModerationController approves or rejects new listings. "
        "VolunteerController lists CLAIMED tasks and can complete delivery.",
    )

    heading(doc, "3.1.6 Notifications and feedback", 3)
    para(
        doc,
        "NotificationService writes portal_notifications rows when listings are created, claimed, or delivered. "
        "FeedbackController saves listing_feedback after DELIVERED.",
    )

    heading(doc, "3.1.7 Testing", 3)
    para(
        doc,
        "PHPUnit feature tests: FoodSearchTest (search filters) and FoodClaimTest (concurrent claim). "
        "Manual tests use Documentation/postman_collection.json and the /test-apis browser page. "
        "Demo script: Documentation/Markdown/demo-and-viva-script.md.",
    )

    heading(doc, "3.1.8 Sample code (atomic claim)", 3)
    para(
        doc,
        "The claim action uses a conditional update so only one recipient wins under load "
        "(simplified excerpt from ClaimController):"
    )
    code = (
        "UPDATE food_listings SET status = 'CLAIMED', claimed_by = ?, claimed_at = NOW() "
        "WHERE id = ? AND status = 'AVAILABLE' AND expiry_at > NOW()"
    )
    p = doc.add_paragraph(code)
    p.style = "No Spacing"
    for run in p.runs:
        run.font.name = "Courier New"
        run.font.size = Pt(10)

    para(
        doc,
        "If zero rows are updated, the API returns an error so the second client knows the listing is gone.",
    )


def references_and_appendix(doc: Document) -> None:
    page_break(doc)
    heading(doc, "REFERENCES", 1)
    refs = [
        "Virtual University of Pakistan, IT601P Software Project I course material.",
        "Laravel documentation — https://laravel.com/docs",
        "Bootstrap 5 documentation — https://getbootstrap.com/docs/5.3/",
        "Project SRS v2 — Documentation/Markdown/SRS-v2/SRS-v2-main.md",
        "MySQL 8 reference manual — https://dev.mysql.com/doc/",
    ]
    for i, r in enumerate(refs, 1):
        para(doc, f"[{i}] {r}")

    page_break(doc)
    heading(doc, "APPENDIX", 1)
    heading(doc, "A. Installation (short)", 2)
    steps = [
        "composer install && cp .env.example .env && php artisan key:generate",
        "Create MySQL database cfwrp_v2; set DB_* in .env",
        "php artisan migrate && php artisan db:seed --class=UserSeeder",
        "php artisan storage:link",
        "php artisan serve → http://127.0.0.1:8000",
    ]
    for s in steps:
        para(doc, f"• {s}")

    heading(doc, "B. Screenshot checklist", 2)
    para(
        doc,
        "Paste screenshots here: login, donor post, search results, claim success, admin report, "
        "notification bell, NGO verification, listing moderation.",
    )

    heading(doc, "C. Demo accounts", 2)
    para(
        doc,
        "Use accounts from UserSeeder (see README.md and demo-and-viva-script.md). "
        "Replace with your own VU group details on the title page before submission.",
    )


def main() -> None:
    if not IMAGES.is_dir():
        raise SystemExit(f"Images folder not found: {IMAGES}")
    doc = Document()
    set_normal_style(doc)
    front_matter(doc)
    chapter1(doc)
    chapter2(doc)
    chapter3(doc)
    references_and_appendix(doc)
    OUT.parent.mkdir(parents=True, exist_ok=True)
    doc.save(OUT)
    print(f"Wrote {OUT}")
    print(f"Size: {OUT.stat().st_size // 1024} KB")


if __name__ == "__main__":
    main()
