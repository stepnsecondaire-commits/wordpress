#!/usr/bin/env python3
"""
wp_push_pages.py - Creates or updates WordPress pages on eviehometech.com
via REST API, then pushes SureRank SEO meta (title + description), then
purges the LiteSpeed cache and verifies live.

Auth is done via the admin session cookies saved at /tmp/leo-cookiejar-path.
The REST nonce is read from wp_admin/index.php each run.

Usage:
    python3 scripts/wp_push_pages.py
"""

import json
import os
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from pathlib import Path

BASE = "https://eviehometech.com"
ROOT = Path(__file__).resolve().parent.parent
CONTENT_DIR = ROOT / "content" / "phase2"

COOKIE_FILE = Path("/tmp/leo-cookiejar-path").read_text().strip()


def load_cookies():
    jar = {}
    for raw in Path(COOKIE_FILE).read_text().splitlines():
        line = raw.strip()
        if not line:
            continue
        # Netscape format marks HttpOnly with a "#HttpOnly_" prefix that must be stripped
        if line.startswith("#HttpOnly_"):
            line = line[len("#HttpOnly_"):]
        elif line.startswith("#"):
            continue
        parts = line.split("\t")
        if len(parts) >= 7:
            jar[parts[5]] = parts[6]
    return "; ".join(f"{k}={v}" for k, v in jar.items())


COOKIE_HEADER = load_cookies()


def request(method, url, data=None, extra_headers=None):
    headers = {
        "Cookie": COOKIE_HEADER,
        "User-Agent": "leo-wp-cli/1.0",
    }
    if extra_headers:
        headers.update(extra_headers)
    body = None
    if data is not None:
        if isinstance(data, (dict, list)):
            body = json.dumps(data).encode("utf-8")
            headers["Content-Type"] = "application/json"
        elif isinstance(data, bytes):
            body = data
        else:
            body = data.encode("utf-8")
    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            raw = resp.read().decode("utf-8", errors="replace")
            try:
                return resp.status, json.loads(raw)
            except json.JSONDecodeError:
                return resp.status, raw
    except urllib.error.HTTPError as e:
        raw = e.read().decode("utf-8", errors="replace")
        try:
            return e.code, json.loads(raw)
        except json.JSONDecodeError:
            return e.code, raw


def get_rest_nonce():
    status, raw = request("GET", f"{BASE}/wp-admin/")
    if isinstance(raw, str):
        m = re.search(r'rest_nonce":"([a-f0-9]+)', raw)
        if m:
            return m.group(1)
    raise RuntimeError("Could not fetch REST nonce")


def api(method, path, data=None, nonce=None, params=None):
    url = f"{BASE}/wp-json{path}"
    if params:
        url += "?" + urllib.parse.urlencode(params)
    headers = {}
    if nonce:
        headers["X-WP-Nonce"] = nonce
    return request(method, url, data=data, extra_headers=headers)


def find_page_by_slug(slug, nonce):
    status, data = api("GET", "/wp/v2/pages", nonce=nonce, params={"slug": slug, "status": "publish,draft,private"})
    if status == 200 and isinstance(data, list) and data:
        return data[0]
    return None


def upsert_page(slug, title, content, nonce, parent=None, template=None):
    existing = find_page_by_slug(slug, nonce)
    payload = {
        "title": title,
        "slug": slug,
        "content": content,
        "status": "publish",
    }
    if parent:
        payload["parent"] = parent
    if template:
        payload["template"] = template
    if existing:
        status, data = api("POST", f"/wp/v2/pages/{existing['id']}", data=payload, nonce=nonce)
        action = "updated"
    else:
        status, data = api("POST", "/wp/v2/pages", data=payload, nonce=nonce)
        action = "created"
    if status in (200, 201):
        return action, data["id"], data["link"]
    return f"ERROR {status}", None, str(data)[:200]


def set_seo(post_id, title, description, nonce):
    payload = {
        "post_id": post_id,
        "metaData": {"page_title": title, "page_description": description},
    }
    status, data = api("POST", "/surerank/v1/post/settings", data=payload, nonce=nonce)
    return status, data


def purge_cache():
    nonce_cache = "6540b972f7"  # Captured from LiteSpeed toolbox earlier
    url = f"{BASE}/wp-admin/admin.php?page=litespeed-toolbox&LSCWP_CTRL=PURGE_ALL&LSCWP_NONCE={nonce_cache}"
    request("GET", url)


def verify_page(url):
    status, html = request("GET", url + f"?t={int(time.time())}")
    if not isinstance(html, str):
        return {"error": "not_html"}
    title_m = re.search(r"<title>([^<]+)", html)
    desc_m = re.search(r'<meta name="description" content="([^"]*)"', html)
    h1_m = re.search(r"<h1[^>]*>(.*?)</h1>", html, re.DOTALL)
    return {
        "title": (title_m.group(1) if title_m else "MISSING")[:150],
        "desc": (desc_m.group(1) if desc_m else "MISSING")[:150],
        "h1": re.sub(r"<[^>]+>", "", h1_m.group(1))[:100] if h1_m else "MISSING",
    }


# --- Pages to push ---

PAGES = [
    {
        "slug": "oem-odm-services",
        "title": "OEM & ODM Smart Pet Products Manufacturing in China",
        "content_file": "oem-odm-services.html",
        "seo_title": "OEM & ODM Smart Pet Products Manufacturer China | Eviehome",
        "seo_desc": "Factory-direct OEM & ODM services for smart pet products in China. 8 patents, 2 production lines, 30+ countries served. MOQ 500 units. Request a quote.",
    },
    {
        "slug": "certifications-quality",
        "title": "Certifications & Quality for Smart Pet Products",
        "content_file": "certifications-quality.html",
        "seo_title": "CE, FCC, ROHS, ISO 9001 Certified Pet Products Manufacturer | Eviehome",
        "seo_desc": "All certifications held by our smart pet products factory: CE, UKCA, FCC, PSE, RCM, ROHS, REACH, ISO 9001. Full test reports and DoC available on request.",
    },
    {
        "slug": "shipping-logistics",
        "title": "Shipping & Logistics for Smart Pet Products from China",
        "content_file": "shipping-logistics.html",
        "seo_title": "Shipping Smart Pet Products from China: FOB, CIF, DDP | Eviehome",
        "seo_desc": "How we ship smart pet products from Hefei, China: Incoterms FOB / CIF / DDP, lead times to US and EU, HS codes, export docs, container loadability guide.",
    },
    {
        "slug": "why-source-from-china",
        "title": "Why Source Smart Pet Products From China: A Buyer's Guide",
        "content_file": "why-source-from-china.html",
        "seo_title": "Why Source Smart Pet Products From China: 2026 Buyer's Guide | Eviehome",
        "seo_desc": "Complete 2026 guide for importers and distributors sourcing smart pet products directly from Chinese manufacturers. Margins, MOQs, Incoterms, due diligence checklist.",
    },
    {
        "slug": "buyer-reviews",
        "title": "Verified Buyer Reviews of Eviehome Smart Pet Products",
        "content_file": "buyer-reviews.html",
        "seo_title": "Verified Alibaba Buyer Reviews | Eviehome Smart Pet Products",
        "seo_desc": "Verified 5-star reviews from Alibaba Trade Assurance buyers in Italy, Singapore, Korea, Australia and India. Real B2B customers of Eviehome smart pet products.",
    },
]


def main():
    nonce = get_rest_nonce()
    print(f"REST nonce: {nonce}\n")

    results = []
    for page in PAGES:
        content = (CONTENT_DIR / page["content_file"]).read_text(encoding="utf-8")
        action, pid, link = upsert_page(page["slug"], page["title"], content, nonce)
        print(f"[{action}] /{page['slug']}/ id={pid}")
        if pid:
            seo_status, seo_data = set_seo(pid, page["seo_title"], page["seo_desc"], nonce)
            print(f"   SEO: {seo_status} {seo_data}")
            results.append({"slug": page["slug"], "id": pid, "link": link, "action": action})
        else:
            print(f"   ERROR: {link}")

    print("\n=== Purging LiteSpeed cache ===")
    purge_cache()
    time.sleep(1)

    print("\n=== Verifying live ===")
    for r in results:
        v = verify_page(r["link"])
        print(f"\n{r['link']}")
        for k, val in v.items():
            print(f"  {k:6}: {val}")


if __name__ == "__main__":
    main()
