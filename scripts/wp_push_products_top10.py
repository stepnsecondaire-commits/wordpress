#!/usr/bin/env python3
"""
wp_push_products_top10.py - Replaces the generic template enrichment on
the top 10 flagship products with unique per-product B2B content.

Each product has a hand-written content file in content/products/NNNN-slug.html
with 3 sections delimited by HTML comments:

  <!-- META-TITLE: ... -->
  <!-- META-DESC: ... -->
  <!-- INTRO -->
  (intro HTML, goes BEFORE the existing Product specifications block)
  <!-- AFTER-SPECS -->
  (post-specs HTML, goes AFTER the existing Product specifications block)

The script:
  1. Fetches each product's current raw content from the WP REST API
  2. Extracts the Product specifications block (the factory-supplied specs + video)
  3. Replaces the surrounding generic sections with the unique intro + after-specs
  4. Pushes the new content
  5. Pushes SureRank meta title and description
  6. Purges the LiteSpeed cache
"""

import re
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from wp_push_pages import api, get_rest_nonce, purge_cache  # noqa: E402

ROOT = Path(__file__).resolve().parent.parent
CONTENT_DIR = ROOT / "content" / "products"

TARGETS = [
    (688, "688-camera-litter-box.html"),
    (799, "799-self-cleaning-cat-litter-box.html"),
    (866, "866-odor-free-litter-box.html"),
    (962, "962-smart-cat-feeder.html"),
    (943, "943-dog-feeder-3-bowls.html"),
    (1080, "1080-quiet-cat-water-fountain.html"),
    (919, "919-automatic-cat-water-fountain.html"),
    (1035, "1035-ai-pet-monitoring-robot-feeder.html"),
    (1062, "1062-dog-gps-tracker.html"),
    (1071, "1071-bird-feeder-with-camera.html"),
]


def parse_content_file(path):
    """Parse a content file and return (meta_title, meta_desc, intro, after_specs)."""
    raw = path.read_text(encoding="utf-8")

    m = re.search(r"<!--\s*META-TITLE:\s*(.*?)\s*-->", raw)
    meta_title = m.group(1) if m else ""

    m = re.search(r"<!--\s*META-DESC:\s*(.*?)\s*-->", raw)
    meta_desc = m.group(1) if m else ""

    m = re.search(r"<!--\s*INTRO\s*-->(.*?)<!--\s*AFTER-SPECS\s*-->", raw, re.DOTALL)
    intro = m.group(1).strip() if m else ""

    m = re.search(r"<!--\s*AFTER-SPECS\s*-->(.*)$", raw, re.DOTALL)
    after_specs = m.group(1).strip() if m else ""

    return meta_title, meta_desc, intro, after_specs


def extract_specs_block(raw_content):
    """Extract the 'Product specifications' block from the existing raw content.

    The block starts at '<h2>Product specifications</h2>' and ends at the
    next '<h2>' heading (which is the OEM section in the generic template).
    Returns the content including the <h2>Product specifications</h2> header.
    """
    m = re.search(
        r"(<h2>Product specifications</h2>.*?)(?=\n<h2>)",
        raw_content,
        re.DOTALL,
    )
    if m:
        return m.group(1).strip()
    # Fallback: if no closing h2 found, take from specs to end
    m = re.search(r"(<h2>Product specifications</h2>.*)", raw_content, re.DOTALL)
    if m:
        return m.group(1).strip()
    return ""


def push_product(pid, content_filename, nonce, dry_run=False):
    path = CONTENT_DIR / content_filename
    if not path.exists():
        print(f"  [ERROR] content file missing: {path}")
        return "error"

    meta_title, meta_desc, intro, after_specs = parse_content_file(path)

    # Fetch current product
    status, data = api(
        "GET",
        f"/wp/v2/products/{pid}",
        nonce=nonce,
        params={"context": "edit", "_fields": "id,title,content"},
    )
    if not isinstance(data, dict):
        print(f"  [ERROR] fetch {pid}: {status} {str(data)[:200]}")
        return "error"

    title = data["title"]["rendered"]
    raw = data["content"]["raw"]
    specs_block = extract_specs_block(raw)
    if not specs_block:
        print(f"  [ERROR] could not extract specs block for {pid}")
        return "error"

    # Assemble new content
    new_content = f"{intro}\n\n{specs_block}\n\n{after_specs}\n"

    print(f"  [{pid}] {title}")
    print(f"    meta_title: {meta_title} ({len(meta_title)} chars)")
    print(f"    meta_desc:  {meta_desc[:100]}... ({len(meta_desc)} chars)")
    print(f"    content:    {len(new_content)} chars")

    if dry_run:
        return "dry"

    # Push content
    status, data = api(
        "POST",
        f"/wp/v2/products/{pid}",
        data={"content": new_content},
        nonce=nonce,
    )
    if status not in (200, 201):
        print(f"    [ERROR] content update: {status} {str(data)[:200]}")
        return "error"
    print("    content: OK")

    # Push SureRank meta
    status, data = api(
        "POST",
        "/surerank/v1/post/settings",
        data={
            "post_id": pid,
            "metaData": {
                "page_title": meta_title,
                "page_description": meta_desc,
            },
        },
        nonce=nonce,
    )
    if status not in (200, 201):
        print(f"    [WARN] seo update: {status} {str(data)[:200]}")
    else:
        print("    seo: OK")

    return "ok"


def main():
    dry_run = "--dry-run" in sys.argv
    nonce = get_rest_nonce()
    if not nonce:
        print("ERROR: nonce fetch failed")
        return 1

    print(f"nonce: {nonce[:10]}... | dry-run: {dry_run}\n")

    counters = {"ok": 0, "dry": 0, "error": 0}
    for pid, fname in TARGETS:
        result = push_product(pid, fname, nonce, dry_run=dry_run)
        counters[result] = counters.get(result, 0) + 1
        if not dry_run:
            time.sleep(0.3)
        print()

    if not dry_run and counters["ok"] > 0:
        print("Purging cache...")
        purge_cache()

    print("\n=== Summary ===")
    for k, v in counters.items():
        print(f"  {k}: {v}")
    return 0 if counters["error"] == 0 else 1


if __name__ == "__main__":
    sys.exit(main())
