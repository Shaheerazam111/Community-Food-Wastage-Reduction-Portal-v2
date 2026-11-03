# SRS v2 — documentation index

Updated **Software Requirements Specification** for the **Community Food Wastage Reduction Portal (v2 codebase)**. This version matches the **MySQL schema and Laravel app in the repo**, not the older multi-table design doc.

---

## Main document

| File | Purpose |
| ---- | ------- |
| [SRS-v2-main.md](SRS-v2-main.md) | Full SRS: scope, FR/NFR, interfaces, **§10 production checklist** |
| [../improvements-needed-checklist.md](../improvements-needed-checklist.md) | Master checklist (assignment + brief + production) |
| [../production-deploy.md](../production-deploy.md) | Production server steps |
| [../demo-and-viva-script.md](../demo-and-viva-script.md) | Teacher demo script |

---

## Diagram specs (one file per figure)

Each file holds the report text, element list, and **Mermaid source**. PNG exports are in [`Documentation/images/v2/`](../../images/v2/) (generated via `@mermaid-js/mermaid-cli`).

| # | Spec file | Figure title |
| - | --------- | ------------ |
| 1 | [diagrams/01-use-case-diagram.md](diagrams/01-use-case-diagram.md) | Use case diagram |
| 2 | [diagrams/02-erd-database.md](diagrams/02-erd-database.md) | Entity relationship (implemented DB) |
| 3 | [diagrams/03-system-architecture.md](diagrams/03-system-architecture.md) | System architecture (3-tier) |
| 4 | [diagrams/04-listing-state-diagram.md](diagrams/04-listing-state-diagram.md) | Food listing status states |
| 5 | [diagrams/05-sequence-auth.md](diagrams/05-sequence-auth.md) | Sequence — register, login, logout |
| 6 | [diagrams/06-sequence-post-listing.md](diagrams/06-sequence-post-listing.md) | Sequence — donor posts food |
| 7 | [diagrams/07-sequence-search-claim.md](diagrams/07-sequence-search-claim.md) | Sequence — search and atomic claim |
| 8 | [diagrams/08-sequence-deliver-expire.md](diagrams/08-sequence-deliver-expire.md) | Sequence — deliver + expiry job |
| 9 | [diagrams/09-sequence-admin-reports.md](diagrams/09-sequence-admin-reports.md) | Sequence — admin reports |
| 10 | [diagrams/10-end-to-end-activity-flow.md](diagrams/10-end-to-end-activity-flow.md) | Activity — end-to-end happy path |

---

## Related docs (older or broader)

| Doc | Note |
| --- | ---- |
| [../SRS-sections.md](../SRS-sections.md) | Original full-portal SRS (volunteer, disputes, many tables) |
| [../project-requirements.md](../project-requirements.md) | Supervisor project brief |
| [../implementation-status.md](../implementation-status.md) | Code vs docs checklist |
| [../../images/](../../images/) | Legacy diagram PNGs from initial design |

---

## Suggested order for Mermaid MCP

1. ERD and architecture (structure first)  
2. State diagram (status rules)  
3. Sequences 5–9 (behaviour)  
4. Use case and activity flow (overview for report chapter)
