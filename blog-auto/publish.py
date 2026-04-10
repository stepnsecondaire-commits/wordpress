#!/usr/bin/env python3
"""
publish.py - Blog auto-publisher for eviehometech.com.

Reads articles.json, picks the next unpublished article whose scheduled_datetime
is in the past, generates the full article body via Claude Sonnet 4 (with strong
B2B SEO and GEO constraints), uploads a relevant Unsplash image, publishes to
WordPress as a regular post, sets SureRank SEO meta, pings IndexNow, and marks
the article as published in articles.json.

Runs in idempotent mode: safe to execute repeatedly (GitHub Actions cron).
Only publishes ONE article per run to stay under rate limits and keep the
publish cadence natural.

Environment variables (GitHub Actions secrets):
    ANTHROPIC_API_KEY        Claude API key
    WP_BASIC_AUTH            "user:app_password" base64 for WP REST Basic Auth
                              OR WP_COOKIE for cookie session string
    UNSPLASH_ACCESS_KEY      Unsplash API access key
    INDEXNOW_KEY             IndexNow key for Bing/Yandex notification

Local usage:
    python3 blog-auto/publish.py            # publish next due article
    python3 blog-auto/publish.py --force N  # force publish article index N
    python3 blog-auto/publish.py --dry-run  # simulate without publishing
"""

import base64
import json
import os
import re
import sys
import time
import urllib.error
import urllib.parse
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

HERE = Path(__file__).resolve().parent
ARTICLES_FILE = HERE / "articles.json"
LOG_FILE = HERE / "logs.txt"

BASE = "https://eviehometech.com"
INDEXNOW_HOST = "eviehometech.com"

ANTHROPIC_KEY = os.environ.get("ANTHROPIC_API_KEY", "")
WP_BASIC_AUTH = os.environ.get("WP_BASIC_AUTH", "")
WP_COOKIE = os.environ.get("WP_COOKIE", "")
UNSPLASH_KEY = os.environ.get("UNSPLASH_ACCESS_KEY", "JbbllKzCE3UxK6EMCrtxYHIn1EYQ06WEbBtQpc_3QKk")
INDEXNOW_KEY = os.environ.get("INDEXNOW_KEY", "eviehome-indexnow-7f3a2b8c9d1e4f5a")

CLUSTER_META = {
    1: {"name": "Sourcing & Import", "pillar_slug": "complete-guide-sourcing-smart-pet-products-china"},
    2: {"name": "Automatic Cat Litter Boxes", "pillar_slug": "automatic-cat-litter-box-b2b-buyers-guide"},
    3: {"name": "Smart Pet Feeders & Fountains", "pillar_slug": "smart-pet-feeders-wholesale-buyers-guide"},
    4: {"name": "Pet Tech & Innovation", "pillar_slug": "rise-smart-pet-products-market-2026"},
    5: {"name": "OEM/ODM & Business", "pillar_slug": "oem-vs-odm-pet-products-complete-guide"},
    6: {"name": "Certifications & Compliance", "pillar_slug": "pet-product-certifications-ce-fcc-pse-rohs"},
    7: {"name": "Market & Trends", "pillar_slug": "global-smart-pet-products-market-report-2026"},
    8: {"name": "Practical Guides", "pillar_slug": None},
}


def log(msg):
    ts = datetime.now(timezone.utc).isoformat()
    line = f"[{ts}] {msg}"
    print(line, flush=True)
    try:
        with LOG_FILE.open("a") as f:
            f.write(line + "\n")
    except Exception:
        pass


def read_articles():
    return json.loads(ARTICLES_FILE.read_text())


def write_articles(articles):
    ARTICLES_FILE.write_text(json.dumps(articles, indent=2))


def pick_next_due(articles, now):
    """Return the first unpublished article whose scheduled_datetime is in the past."""
    candidates = []
    for a in articles:
        if a.get("published"):
            continue
        try:
            sched = datetime.fromisoformat(a["scheduled_datetime"]).replace(tzinfo=timezone.utc)
        except Exception:
            continue
        if sched <= now:
            candidates.append((sched, a))
    if not candidates:
        return None
    candidates.sort(key=lambda x: x[0])
    return candidates[0][1]


_cached_nonce = None


def get_nonce():
    """Fetch the REST nonce from wp-admin (only needed with cookie auth)."""
    global _cached_nonce
    if _cached_nonce:
        return _cached_nonce
    if not WP_COOKIE:
        return None
    req = urllib.request.Request(
        f"{BASE}/wp-admin/",
        headers={"Cookie": WP_COOKIE, "User-Agent": "eviehome-blog-auto/1.0"},
    )
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            html = r.read().decode()
        m = re.search(r'rest_nonce":"([a-f0-9]+)', html)
        if m:
            _cached_nonce = m.group(1)
            return _cached_nonce
    except Exception as e:
        log(f"nonce fetch error: {e}")
    return None


def wp_request(method, path, data=None, extra_headers=None):
    url = f"{BASE}/wp-json{path}"
    headers = {"User-Agent": "eviehome-blog-auto/1.0"}
    if WP_BASIC_AUTH:
        headers["Authorization"] = f"Basic {WP_BASIC_AUTH}"
    if WP_COOKIE:
        headers["Cookie"] = WP_COOKIE
        nonce = get_nonce()
        if nonce:
            headers["X-WP-Nonce"] = nonce
    if extra_headers:
        headers.update(extra_headers)
    body = None
    if data is not None:
        if isinstance(data, (dict, list)):
            body = json.dumps(data).encode()
            headers["Content-Type"] = "application/json"
        else:
            body = data
    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=120) as resp:
            return resp.status, json.loads(resp.read().decode())
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode()


def load_prewritten(article):
    """If a pre-written HTML file exists in content/, return a fake "generated" dict."""
    filename = f"{article['index']:03d}-{article['slug']}.html"
    path = HERE / "content" / filename
    if not path.exists():
        return None
    html = path.read_text()
    # Derive meta title and description from the article metadata
    title = article["title"]
    brand_suffix = " | Eviehome"
    max_title_len = 65
    if len(title) + len(brand_suffix) <= max_title_len:
        meta_title = title + brand_suffix
    else:
        meta_title = title[: max_title_len - 3] + "..."
    # Derive description from the first paragraph of the HTML
    first_p = re.search(r"<p>(.*?)</p>", html, re.DOTALL)
    raw = first_p.group(1) if first_p else ""
    raw = re.sub(r"<[^>]+>", "", raw).strip()
    meta_desc = raw[:157] + "..." if len(raw) > 160 else raw
    excerpt = raw[:200] + "..." if len(raw) > 200 else raw
    primary_kw = article["keywords"].split(",")[0].strip()
    # Build an Unsplash query from keywords
    unsplash_query = primary_kw
    return {
        "html": html,
        "meta_title": meta_title,
        "meta_description": meta_desc,
        "excerpt": excerpt,
        "unsplash_query": unsplash_query,
    }


def claude_generate(article):
    """Call Claude Sonnet 4 to generate a full article."""
    cluster = CLUSTER_META.get(article["cluster"], {})
    pillar_hint = ""
    if cluster.get("pillar_slug") and not article.get("pillar"):
        pillar_hint = f"\nInternal link to the cluster pillar article at /{cluster['pillar_slug']}/ under an anchor like the pillar's exact title."

    prompt = f"""You are writing a B2B SEO article for Hefei Ecologie Vie Home Technology Co., Ltd., a factory-direct smart pet products manufacturer in Hefei, China. The brand name is Eviehome. The website is https://eviehometech.com/. Contact is Ryan Lau, ryanlau@eviehometech.com, WhatsApp +86 199 5653 0913.

ARTICLE SPEC
Title: {article['title']}
Primary keyword: {article['keywords'].split(',')[0].strip()}
Secondary keywords: {', '.join(k.strip() for k in article['keywords'].split(',')[1:])}
Cluster: {cluster.get('name', 'General')}
Is pillar article: {article.get('pillar', False)}

AUDIENCE
B2B buyers: importers, distributors, private-label brand owners, e-commerce operators sourcing pet products from China for Europe, the United States, the United Kingdom, Australia.

WRITING CONSTRAINTS
- Language: English only.
- Length: {"2800 to 3500 words" if article.get('pillar') else "1500 to 2200 words"}.
- Professional B2B tone, direct, concrete, data-oriented, never fluffy or marketing-cliche.
- NEVER use em dashes (U+2014) or en dashes (U+2013). Use hyphen, colon or comma instead.
- NO AI-giveaway phrases: no "in today's fast-paced world", no "in the rapidly evolving landscape", no "navigate the complexities", no "unleash the power". Write like a real B2B expert who has been in this industry for 10 years.
- First sentence of each section must be a direct answer to an implicit question (helps GEO / LLM citations).
- Include concrete numbers, HS codes, ocean freight days, MOQs, lead times, certifications. Fabricate nothing: if you do not know an exact number, state a plausible range that aligns with the real B2B context.
- Mention "Eviehome" or "Ecologie Vie Home Technology" at least 3 times naturally in the body.
- Include the exact phrase "based in Hefei, China" at least once.

STRUCTURE (HTML output only, no markdown)
- Start with an <h1> containing the exact title.
- Introduction paragraph (150-250 words) with the primary keyword in the first sentence.
- 5 to 8 <h2> sections with meaningful subheadings. No filler.
- Use <h3> for sub-sections inside longer <h2> blocks.
- Include at least 1 table (<table>) with relevant data, or 2 detailed bullet lists.
- Include a "Frequently asked questions" <h2> at the end with 3 to 5 Q&A pairs ({'including FAQPage-worthy questions' if article.get('pillar') else 'covering the most likely buyer questions'}).
- Close with an "About Eviehome" section and a direct CTA linking to /contact-us/ (internal link to https://eviehometech.com/contact-us/).
- Add 3 to 5 internal links throughout the body to: /oem-odm-services/, /certifications-quality/, /shipping-logistics/, /buyer-reviews/, /products/, /why-source-from-china/. {pillar_hint}

OUTPUT FORMAT
Return a JSON object (only valid JSON, no markdown fence, no prose around it) with these keys:
- "html": the full article body as HTML (starting with <h1>, ending with the CTA paragraph)
- "meta_title": a Google-optimized SEO title, 55 to 65 characters, primary keyword first, brand last
- "meta_description": 150 to 160 characters, action-oriented, primary keyword in first half
- "unsplash_query": a short 2-5 word Unsplash search query to find a relevant feature image
- "excerpt": a 2-sentence excerpt for the post listing page

Output only the JSON object."""

    body = {
        "model": "claude-sonnet-4-5-20250929",
        "max_tokens": 16000,
        "messages": [{"role": "user", "content": prompt}],
    }
    req = urllib.request.Request(
        "https://api.anthropic.com/v1/messages",
        data=json.dumps(body).encode(),
        headers={
            "Content-Type": "application/json",
            "x-api-key": ANTHROPIC_KEY,
            "anthropic-version": "2023-06-01",
        },
        method="POST",
    )
    with urllib.request.urlopen(req, timeout=300) as resp:
        data = json.loads(resp.read())
    text = data["content"][0]["text"].strip()
    # Strip any markdown fence if Claude wraps it
    if text.startswith("```"):
        text = re.sub(r"^```[a-z]*\s*", "", text)
        text = re.sub(r"\s*```$", "", text)
    return json.loads(text)


def unsplash_upload(query, alt_text):
    """Search Unsplash, download the first result, upload to WP media library."""
    url = f"https://api.unsplash.com/search/photos?query={urllib.parse.quote(query)}&per_page=3&orientation=landscape"
    req = urllib.request.Request(url, headers={"Authorization": f"Client-ID {UNSPLASH_KEY}"})
    with urllib.request.urlopen(req, timeout=30) as r:
        search = json.loads(r.read())
    if not search.get("results"):
        return None, None
    photo = search["results"][0]
    download_url = photo["urls"]["regular"]
    req = urllib.request.Request(download_url, headers={"User-Agent": "eviehome-blog-auto/1.0"})
    with urllib.request.urlopen(req, timeout=60) as r:
        img_bytes = r.read()

    boundary = "----blogFormBoundary" + str(int(time.time()))
    filename = re.sub(r"[^a-z0-9]+", "-", query.lower()).strip("-") + ".jpg"
    body_parts = [
        f"--{boundary}\r\n".encode(),
        f'Content-Disposition: form-data; name="file"; filename="{filename}"\r\n'.encode(),
        b"Content-Type: image/jpeg\r\n\r\n",
        img_bytes,
        f"\r\n--{boundary}--\r\n".encode(),
    ]
    headers = {
        "Content-Type": f"multipart/form-data; boundary={boundary}",
        "User-Agent": "eviehome-blog-auto/1.0",
    }
    if WP_BASIC_AUTH:
        headers["Authorization"] = f"Basic {WP_BASIC_AUTH}"
    if WP_COOKIE:
        headers["Cookie"] = WP_COOKIE
        nonce = get_nonce()
        if nonce:
            headers["X-WP-Nonce"] = nonce
    req = urllib.request.Request(f"{BASE}/wp-json/wp/v2/media", data=b"".join(body_parts), headers=headers, method="POST")
    try:
        with urllib.request.urlopen(req, timeout=120) as resp:
            data = json.loads(resp.read())
    except urllib.error.HTTPError as e:
        log(f"unsplash upload error: {e.read().decode()[:200]}")
        return None, None
    media_id = data.get("id")
    media_url = data.get("source_url")
    if media_id and alt_text:
        try:
            req2 = urllib.request.Request(
                f"{BASE}/wp-json/wp/v2/media/{media_id}",
                data=json.dumps({"alt_text": alt_text}).encode(),
                headers={**headers, "Content-Type": "application/json"},
                method="POST",
            )
            with urllib.request.urlopen(req2, timeout=30) as r:
                r.read()
        except Exception:
            pass
    return media_id, media_url


def publish_post(article, generated, media_id):
    payload = {
        "title": article["title"],
        "slug": article["slug"],
        "status": "publish",
        "content": generated["html"],
        "excerpt": generated.get("excerpt", ""),
    }
    if media_id:
        payload["featured_media"] = media_id
    status, data = wp_request("POST", "/wp/v2/posts", data=payload)
    if status not in (200, 201):
        log(f"publish error: {status} {str(data)[:400]}")
        return None
    post_id = data.get("id")
    # Set SureRank SEO meta
    seo_payload = {
        "post_id": post_id,
        "metaData": {
            "page_title": generated["meta_title"],
            "page_description": generated["meta_description"],
        },
    }
    wp_request("POST", "/surerank/v1/post/settings", data=seo_payload)
    return post_id, data.get("link")


def indexnow_ping(url):
    payload = json.dumps({
        "host": INDEXNOW_HOST,
        "key": INDEXNOW_KEY,
        "keyLocation": f"{BASE}/{INDEXNOW_KEY}.txt",
        "urlList": [url],
    }).encode()
    req = urllib.request.Request(
        "https://api.indexnow.org/indexnow",
        data=payload,
        headers={"Content-Type": "application/json"},
        method="POST",
    )
    try:
        with urllib.request.urlopen(req, timeout=20) as r:
            log(f"indexnow: {r.status}")
    except Exception as e:
        log(f"indexnow error: {e}")


def main():
    force_index = None
    dry_run = False
    for arg in sys.argv[1:]:
        if arg == "--dry-run":
            dry_run = True
        elif arg.startswith("--force"):
            if "=" in arg:
                force_index = int(arg.split("=", 1)[1])
            elif len(sys.argv) > sys.argv.index(arg) + 1:
                force_index = int(sys.argv[sys.argv.index(arg) + 1])

    articles = read_articles()
    now = datetime.now(timezone.utc)

    if force_index:
        article = next((a for a in articles if a["index"] == force_index), None)
        if not article:
            log(f"article index={force_index} not found")
            return 1
    else:
        article = pick_next_due(articles, now)
        if not article:
            log("no article due for publication")
            return 0

    log(f"picked article #{article['index']} [{article['title']}]")

    if dry_run:
        log("dry run, stopping before generation")
        return 0

    # Prefer pre-written content if a matching file exists in content/
    generated = load_prewritten(article)
    if generated:
        log(f"using pre-written content file for article #{article['index']}")
    else:
        if not ANTHROPIC_KEY:
            log("missing ANTHROPIC_API_KEY and no pre-written content file")
            return 1
        try:
            generated = claude_generate(article)
        except Exception as e:
            log(f"claude generation error: {e}")
            return 1

    log(f"generated {len(generated['html'])} chars, meta_title='{generated['meta_title'][:60]}'")

    # Fetch + upload feature image
    media_id = None
    media_url = None
    if generated.get("unsplash_query"):
        try:
            media_id, media_url = unsplash_upload(generated["unsplash_query"], article["title"])
            log(f"image uploaded id={media_id} url={media_url}")
        except Exception as e:
            log(f"image upload failed: {e}")

    result = publish_post(article, generated, media_id)
    if not result:
        return 1
    post_id, post_link = result
    log(f"published post_id={post_id} link={post_link}")

    indexnow_ping(post_link)

    article["published"] = True
    article["published_at"] = now.isoformat()
    article["wp_post_id"] = post_id
    article["wp_link"] = post_link
    write_articles(articles)
    log(f"marked article #{article['index']} as published")
    return 0


if __name__ == "__main__":
    sys.exit(main())
