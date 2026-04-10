#!/usr/bin/env python3
"""
wp_media_unsplash.py - Fetches images from Unsplash by query, uploads them to the
WordPress media library on eviehometech.com, and returns the WP media IDs and URLs.

Why: we need relevant imagery on the new B2B pages (factories, production lines,
logistics, quality control, sourcing) without creating fake pictures. Unsplash gives
us free-to-use, professionally-shot photos.

Usage:
    python3 scripts/wp_media_unsplash.py

Output: JSON file at audit/unsplash-uploaded.json with mapping of key -> {wp_id, url, alt}
"""

import json
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from wp_push_pages import get_rest_nonce, COOKIE_HEADER, BASE, request, api  # noqa: E402

UNSPLASH_ACCESS_KEY = "JbbllKzCE3UxK6EMCrtxYHIn1EYQ06WEbBtQpc_3QKk"

# key -> query, alt_text, description
IMAGE_QUERIES = {
    "factory-production": {
        "query": "electronics manufacturing factory production line",
        "alt": "Smart pet products manufacturing line in a Chinese electronics factory",
        "filename": "factory-production-line.jpg",
    },
    "oem-assembly": {
        "query": "assembly line worker precision electronics",
        "alt": "Assembly line worker building smart pet products in our Hefei factory",
        "filename": "oem-assembly-worker.jpg",
    },
    "quality-control": {
        "query": "quality control inspection electronics factory",
        "alt": "Quality control inspector testing smart pet product on the production line",
        "filename": "quality-control-inspection.jpg",
    },
    "certifications": {
        "query": "laboratory testing quality certification",
        "alt": "Third-party laboratory testing pet products for CE, FCC and ROHS compliance",
        "filename": "certifications-testing-lab.jpg",
    },
    "shipping-port": {
        "query": "container ship cargo port logistics",
        "alt": "Container vessel loading shipments of smart pet products at Ningbo port",
        "filename": "shipping-container-port.jpg",
    },
    "cargo-containers": {
        "query": "shipping containers stacked port crane",
        "alt": "Stacked export containers ready to ship smart pet products from China",
        "filename": "cargo-containers-stacked.jpg",
    },
    "factory-warehouse": {
        "query": "modern warehouse factory logistics",
        "alt": "Warehouse of smart pet products ready for international shipment",
        "filename": "factory-warehouse.jpg",
    },
    "global-trade": {
        "query": "global trade world map business",
        "alt": "Eviehome exports smart pet products to more than 30 countries worldwide",
        "filename": "global-trade-map.jpg",
    },
    "team-meeting": {
        "query": "business team meeting discussion factory",
        "alt": "Eviehome foreign trade team in a project briefing with a B2B customer",
        "filename": "team-business-meeting.jpg",
    },
    "chinese-factory-exterior": {
        "query": "industrial factory building china exterior",
        "alt": "Eviehome industrial factory building in Hefei, Anhui Province, China",
        "filename": "chinese-factory-exterior.jpg",
    },
}


def unsplash_search(query, per_page=3):
    url = f"https://api.unsplash.com/search/photos?query={urllib.parse.quote(query)}&per_page={per_page}&orientation=landscape"
    req = urllib.request.Request(url, headers={"Authorization": f"Client-ID {UNSPLASH_ACCESS_KEY}"})
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read())


def download_image(download_url):
    req = urllib.request.Request(download_url, headers={"User-Agent": "Mozilla/5.0 leo-project"})
    with urllib.request.urlopen(req, timeout=60) as resp:
        return resp.read()


def upload_to_wp(image_bytes, filename, alt, nonce):
    boundary = "----LeoFormBoundary" + str(int(time.time()))
    body_parts = []
    body_parts.append(f"--{boundary}\r\n".encode())
    body_parts.append(f'Content-Disposition: form-data; name="file"; filename="{filename}"\r\n'.encode())
    body_parts.append(b"Content-Type: image/jpeg\r\n\r\n")
    body_parts.append(image_bytes)
    body_parts.append(f"\r\n--{boundary}--\r\n".encode())
    body = b"".join(body_parts)

    headers = {
        "Cookie": COOKIE_HEADER,
        "Content-Type": f"multipart/form-data; boundary={boundary}",
        "X-WP-Nonce": nonce,
        "User-Agent": "leo-wp-cli/1.0",
    }
    req = urllib.request.Request(f"{BASE}/wp-json/wp/v2/media", data=body, headers=headers, method="POST")
    try:
        with urllib.request.urlopen(req, timeout=120) as resp:
            data = json.loads(resp.read())
    except urllib.error.HTTPError as e:
        return None, e.read().decode("utf-8", errors="replace")

    media_id = data.get("id")
    # Set alt text
    alt_req = urllib.request.Request(
        f"{BASE}/wp-json/wp/v2/media/{media_id}",
        data=json.dumps({"alt_text": alt}).encode(),
        headers={**headers, "Content-Type": "application/json"},
        method="POST",
    )
    with urllib.request.urlopen(alt_req, timeout=30) as r:
        r.read()

    return data, None


def main():
    nonce = get_rest_nonce()
    results = {}

    for key, spec in IMAGE_QUERIES.items():
        print(f"\n[{key}] query={spec['query']!r}")
        try:
            search = unsplash_search(spec["query"])
        except Exception as e:
            print(f"  unsplash error: {e}")
            continue
        if not search.get("results"):
            print("  no results")
            continue
        photo = search["results"][0]
        download_url = photo["urls"]["regular"]
        photo_id = photo["id"]
        author = photo["user"]["name"]
        unsplash_url = photo["links"]["html"]
        print(f"  chose photo {photo_id} by {author}")

        try:
            img_bytes = download_image(download_url)
        except Exception as e:
            print(f"  download error: {e}")
            continue

        upload_data, err = upload_to_wp(img_bytes, spec["filename"], spec["alt"], nonce)
        if err:
            print(f"  upload error: {err[:200]}")
            continue

        results[key] = {
            "wp_id": upload_data.get("id"),
            "url": upload_data.get("source_url"),
            "alt": spec["alt"],
            "credit_author": author,
            "credit_url": unsplash_url,
        }
        print(f"  uploaded: id={results[key]['wp_id']} url={results[key]['url']}")

        time.sleep(0.5)

    out_path = Path(__file__).resolve().parent.parent / "audit" / "unsplash-uploaded.json"
    out_path.write_text(json.dumps(results, indent=2))
    print(f"\nSaved mapping to {out_path}")


if __name__ == "__main__":
    main()
