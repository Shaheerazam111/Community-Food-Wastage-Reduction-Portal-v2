# SRS v2 diagram exports

PNG files generated from the Mermaid specs in [`Documentation/Markdown/SRS-v2/diagrams/`](../Markdown/SRS-v2/diagrams/). **All 10 figures are rendered** (see table below). Source `.mmd` files live in `source/` for re-export.

Regenerate (from repo root):

```bash
npx @mermaid-js/mermaid-cli -i Documentation/images/v2/source/01-use-case-diagram.mmd -o Documentation/images/v2/figure-01-use-case.png -b white -w 1200
```

### Higher quality exports

| Goal | What to do |
| ---- | ---------- |
| **Sharper PNG (print / Word)** | Raise **scale** and **width**. Scale doubles pixel density without changing layout size in many viewers. |
| **Best clarity (recommended for reports)** | Export **SVG** (vector — no blur when zoomed). Word 2016+ can insert SVG; or convert SVG → PDF. |
| **Large sequence diagrams** | Increase **width** and **height** so text is not cramped before scaling. |

**High-res PNG example** (about 2× pixels vs `-w 1200 -s 1`):

```bash
npx @mermaid-js/mermaid-cli \
  -i Documentation/images/v2/source/07-sequence-search-claim.mmd \
  -o Documentation/images/v2/figure-07-sequence-search-claim.png \
  -b white -w 1600 -H 1200 -s 2
```

**SVG example** (best for design doc / thesis):

```bash
npx @mermaid-js/mermaid-cli \
  -i Documentation/images/v2/source/02-erd-database.mmd \
  -o Documentation/images/v2/figure-02-erd.svg \
  -b white -w 1400
```

**Re-render all PNGs (batch, hi-res)** — from repo root in Git Bash:

```bash
SCALE=2 WIDTH=1600
for f in Documentation/images/v2/source/*.mmd; do
  base=$(basename "$f" .mmd)
  case "$base" in
    01-use-case-diagram) out=figure-01-use-case ;;
    02-erd-database) out=figure-02-erd ;;
    03-system-architecture) out=figure-03-system-architecture ;;
    04-listing-state-diagram) out=figure-04-listing-state ;;
    05-sequence-auth) out=figure-05-sequence-auth ;;
    06-sequence-post-listing) out=figure-06-sequence-post-listing ;;
    07-sequence-search-claim) out=figure-07-sequence-search-claim ;;
    08-sequence-deliver-expire) out=figure-08-sequence-deliver-expire ;;
    09-sequence-admin-reports) out=figure-09-sequence-admin-reports ;;
    10-end-to-end-activity-flow) out=figure-10-end-to-end-flow ;;
  esac
  npx @mermaid-js/mermaid-cli -i "$f" -o "Documentation/images/v2/${out}.png" -b white -w "$WIDTH" -s "$SCALE"
done
```

Flags (see `npx @mermaid-js/mermaid-cli --help`):

- **`-s` / `--scale`** — Puppeteer device scale (default `1`). Try `2` or `3` for sharper PNGs.
- **`-w` / `--width`** — Page width in px (default `800`). We used `1200`; use `1600–2400` for wide architecture/sequence charts.
- **`-H` / `--height`** — Page height if the chart is clipped at the bottom.
- **`-o …svg`** — Vector output; preferred when your report tool supports it.
- **`-o …pdf`** — PDF vector; use `-f` / `--pdfFit` if the page should fit the diagram.

If text still looks small, edit the `.mmd` file (shorter labels) or add a Mermaid **config file** (`-c config.json`) with e.g. `"themeVariables": { "fontSize": "18px" }`.

| Planned filename | Source spec |
| ---------------- | ----------- |
| `figure-01-use-case.png` | `01-use-case-diagram.md` |
| `figure-02-erd.png` | `02-erd-database.md` (Mermaid crow’s-foot) |
| `figure-02-erd-chen.png` | Chen notation — `python Documentation/images/v2/source/generate_chen_erd.py` |
| `figure-03-system-architecture.png` | `03-system-architecture.md` |
| `figure-04-listing-state.png` | `04-listing-state-diagram.md` |
| `figure-05-sequence-auth.png` | `05-sequence-auth.md` |
| `figure-06-sequence-post-listing.png` | `06-sequence-post-listing.md` |
| `figure-07-sequence-search-claim.png` | `07-sequence-search-claim.md` |
| `figure-08-sequence-deliver-expire.png` | `08-sequence-deliver-expire.md` |
| `figure-09-sequence-admin-reports.png` | `09-sequence-admin-reports.md` |
| `figure-10-end-to-end-flow.png` | `10-end-to-end-activity-flow.md` |

Older reference images (full-portal design) stay in [`Documentation/images/`](../) (parent folder).
