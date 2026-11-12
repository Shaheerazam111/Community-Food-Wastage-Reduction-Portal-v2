"""
Chen notation ERD — layout matched to classic hand-drawn style (sparse attributes).
Output: Documentation/images/v2/figure-02-erd-chen.png
"""
from __future__ import annotations

from pathlib import Path

import matplotlib.pyplot as plt
import numpy as np
from matplotlib.patches import Ellipse, FancyBboxPatch, Polygon

V2 = Path(__file__).resolve().parents[1]
OUT = V2 / "figure-02-erd-chen.png"

ENTITY_FILL = "#F5B7B1"
ATTR_FILL = "#A8E6A1"
REL_FILL = "#81D4FA"
EDGE = "#2C2C2C"


def entity_box(ax, cx: float, cy: float, w: float, h: float, name: str) -> tuple[float, float, float, float]:
    patch = FancyBboxPatch(
        (cx - w / 2, cy - h / 2),
        w,
        h,
        boxstyle="round,pad=0.015,rounding_size=0.6",
        linewidth=1.4,
        edgecolor=EDGE,
        facecolor=ENTITY_FILL,
        zorder=5,
    )
    ax.add_patch(patch)
    ax.text(cx, cy, name, ha="center", va="center", fontsize=13, fontweight="bold", zorder=6)
    return cx - w / 2, cy - h / 2, cx + w / 2, cy + h / 2


def attribute_oval(ax, x: float, y: float, text: str, pk: bool = False) -> None:
    w = max(7.5, len(text) * 0.72)
    h = 3.2
    ell = Ellipse((x, y), w, h, facecolor=ATTR_FILL, edgecolor=EDGE, linewidth=1.0, zorder=4)
    ax.add_patch(ell)
    if pk:
        ax.text(x, y, text, ha="center", va="center", fontsize=9, fontweight="bold", zorder=5)
        ax.plot([x - len(text) * 0.17, x + len(text) * 0.17], [y - 1.05, y - 1.05], color=EDGE, lw=0.8, zorder=5)
    else:
        ax.text(x, y, text, ha="center", va="center", fontsize=9, zorder=5)


def relate_diamond(ax, x: float, y: float, label: str, scale: float = 1.0) -> None:
    s = 3.2 * scale
    pts = [(x, y + s), (x + s * 1.15, y), (x, y - s), (x - s * 1.15, y)]
    poly = Polygon(pts, closed=True, facecolor=REL_FILL, edgecolor=EDGE, linewidth=1.2, zorder=5)
    ax.add_patch(poly)
    ax.text(x, y, label, ha="center", va="center", fontsize=9, zorder=6)


def line(ax, x1: float, y1: float, x2: float, y2: float) -> None:
    ax.plot([x1, x2], [y1, y2], color=EDGE, linewidth=1.1, zorder=2, solid_capstyle="round")


def attrs_on_row(
    ax, ecx: float, ecy: float, eh: float, attrs: list[tuple[str, bool]], y: float, x_lo: float, x_hi: float
) -> None:
    n = len(attrs)
    xs = np.linspace(x_lo, x_hi, n)
    anchor_y = ecy + eh / 2
    for (text, pk), x in zip(attrs, xs):
        line(ax, x, y - 1.6, x, y)
        line(ax, x, y, ecx, anchor_y)
        attribute_oval(ax, x, y, text, pk=pk)


def attrs_on_column(
    ax, ecx: float, ecw: float, ecy: float, attrs: list[tuple[str, bool]], x: float, y_lo: float, y_hi: float, side: str
) -> None:
    n = len(attrs)
    ys = np.linspace(y_hi, y_lo, n)
    if side == "left":
        anchor_x = ecx - ecw / 2
    else:
        anchor_x = ecx + ecw / 2
    for (text, pk), y in zip(attrs, ys):
        line(ax, x, y, anchor_x, y)
        line(ax, anchor_x, y, anchor_x, ecy)
        attribute_oval(ax, x, y, text, pk=pk)


def main() -> None:
    fig, ax = plt.subplots(figsize=(22, 15))
    ax.set_xlim(0, 100)
    ax.set_ylim(0, 100)
    ax.axis("off")
    ax.set_title(
        "Figure 4.2 — Entity Relationship Diagram (Chen notation)",
        fontsize=16,
        fontweight="bold",
        y=0.98,
    )

    # --- Entities (similar positions to sample: USER top, two middle, two bottom) ---
    _, _, ux1, uy1 = entity_box(ax, 50.0, 84.0, 14.0, 5.0, "USER")
    _, _, lx1, ly1 = entity_box(ax, 26.0, 54.0, 18.0, 5.0, "FOOD_LISTING")
    _, _, vx1, vy1 = entity_box(ax, 74.0, 54.0, 22.0, 5.0, "VERIFICATION\nDOCUMENT")
    _, _, nx1, ny1 = entity_box(ax, 26.0, 22.0, 17.0, 5.0, "NOTIFICATION")
    _, _, fx1, fy1 = entity_box(ax, 74.0, 22.0, 17.0, 5.0, "LISTING\nFEEDBACK")

    # --- Attributes: keep count low like sample (full columns in SRS doc) ---
    attrs_on_row(
        ax,
        50.0,
        84.0,
        5.0,
        [
            ("User ID", True),
            ("Name", False),
            ("Email", False),
            ("Phone", False),
            ("Role", False),
            ("Location", False),
            ("Verified status", False),
        ],
        y=93.5,
        x_lo=18,
        x_hi=82,
    )

    attrs_on_column(
        ax,
        26.0,
        18.0,
        54.0,
        [
            ("Listing ID", True),
            ("Donor ID", False),
            ("Food type", False),
            ("Quantity", False),
            ("Expiry time", False),
            ("Pickup location", False),
            ("Status", False),
            ("Claimed by", False),
        ],
        x=7.5,
        y_lo=42,
        y_hi=66,
        side="left",
    )

    attrs_on_column(
        ax,
        74.0,
        22.0,
        54.0,
        [
            ("Doc ID", True),
            ("User ID", False),
            ("File path", False),
            ("Status", False),
            ("Reviewed at", False),
        ],
        x=92.5,
        y_lo=46,
        y_hi=62,
        side="right",
    )

    attrs_on_row(
        ax,
        26.0,
        22.0,
        5.0,
        [
            ("Notification ID", True),
            ("User ID", False),
            ("Title", False),
            ("Body", False),
            ("Read at", False),
        ],
        y=11.5,
        x_lo=8,
        x_hi=44,
    )

    attrs_on_row(
        ax,
        74.0,
        22.0,
        5.0,
        [
            ("Feedback ID", True),
            ("Listing ID", False),
            ("Recipient ID", False),
            ("Rating", False),
            ("Comment", False),
        ],
        y=11.5,
        x_lo=56,
        x_hi=92,
    )

    # --- Relationships ---
    posts = (34.0, 70.0)
    claims = (38.0, 65.0)
    uploads = (66.0, 70.0)
    receives = (33.0, 38.0)
    for_listing = (48.0, 31.0)
    given_by = (58.0, 31.0)

    relate_diamond(ax, *posts, "Posts")
    relate_diamond(ax, *claims, "Claims")
    relate_diamond(ax, *uploads, "Uploads")
    relate_diamond(ax, *receives, "Receives")
    relate_diamond(ax, *for_listing, "For listing")
    relate_diamond(ax, *given_by, "Given by")

    user_bottom = 84.0 - 2.5
    user_left = 50.0 - 7.0
    user_right = 50.0 + 7.0

    line(ax, user_left, user_bottom, posts[0], posts[1] + 3.2)
    line(ax, posts[0] - 3.6, posts[1], lx1 + 1, ly1 + 2.5)
    line(ax, 50.0, user_bottom, claims[0], claims[1] + 3.2)
    line(ax, claims[0] - 3.6, claims[1], 26.0 + 6, ly1 + 2.5)

    line(ax, user_right, user_bottom, uploads[0], uploads[1] + 3.2)
    line(ax, uploads[0] + 3.6, uploads[1], vx1 - 1, vy1 + 2.5)

    line(ax, user_left, user_bottom, receives[0], receives[1] + 3.2)
    line(ax, receives[0], receives[1] - 3.2, 26.0, ny1 + 2.5)

    listing_bottom = 54.0 - 2.5
    line(ax, 26.0, listing_bottom, for_listing[0], for_listing[1] + 3.2)
    line(ax, for_listing[0], for_listing[1] - 3.2, 74.0 - 6, fy1 + 2.5)

    line(ax, user_right, user_bottom, given_by[0], given_by[1] + 3.2)
    line(ax, given_by[0] + 3.6, given_by[1], 74.0 - 1, fy1 + 2.5)

    ax.text(
        3.0,
        3.5,
        "Tables: users, food_listings, verification_documents, notifications, listing_feedback. "
        "Claims are stored on food_listings (no separate claims table). Legacy table `food` is unused.",
        fontsize=9,
        color="#444444",
        wrap=True,
    )

    fig.savefig(OUT, dpi=300, bbox_inches="tight", facecolor="white", pad_inches=0.35)
    print("Wrote", OUT)


if __name__ == "__main__":
    main()
