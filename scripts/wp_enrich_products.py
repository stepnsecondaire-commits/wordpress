#!/usr/bin/env python3
"""
wp_enrich_products.py - Enriches the 35 existing product pages on
eviehometech.com with B2B-focused content: SEO-optimized title, meta
description, rich introduction, OEM/ODM and shipping cross-links,
and a clear Request a Quote CTA. The existing product specs and
videos are preserved.

We rewrite only the content wrapper, not the actual specs, and we push
the result via the WordPress REST API. SEO meta goes through SureRank.
"""

import json
import re
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from wp_push_pages import api, get_rest_nonce, purge_cache, request, BASE  # noqa: E402


CATEGORY_MAP = {
    # Real term IDs from /wp-json/wp/v2/product-category
    4: ("Automatic Cat Litter Box", "wholesale self-cleaning cat litter boxes, OEM smart litter boxes, private-label cat litter robots"),
    6: ("Automatic Dog Feeder", "wholesale automatic dog feeders, OEM smart pet feeders, private-label programmable dog feeders"),
    7: ("Vacuum Cleaner", "wholesale pet vacuum cleaners, OEM robot vacuums for pet hair, pet hair vacuum manufacturer"),
    8: ("Pet Air Purifier", "wholesale pet air purifiers, OEM HEPA pet purifiers, odor control air purifier manufacturer"),
    9: ("Pet Smart Toys", "wholesale smart pet toys, OEM interactive pet toys, smart laser and motion toys for pets"),
    10: ("Bark Collar & GPS Tracker", "wholesale bark collars and GPS trackers, OEM dog training collars, pet GPS tracker manufacturer"),
    15: ("Bird Feeder", "wholesale smart bird feeders with camera, OEM bird feeder manufacturer, app-connected bird feeders"),
    16: ("Automatic Cat Fountain", "wholesale stainless steel cat fountains, OEM pet water dispensers, silent pump pet fountain manufacturer"),
}

DEFAULT_CATEGORY = ("Smart Pet Product", "wholesale smart pet products, OEM smart pet product manufacturer, private-label pet tech")


def build_meta_title(product_name, category):
    base = f"{product_name} | Wholesale & OEM | {category} Manufacturer China"
    # Trim to <= 65 chars if too long
    if len(base) > 65:
        base = f"{product_name} | Wholesale OEM China"
    return base


def build_meta_description(product_name, category, keywords):
    desc = (
        f"{product_name} for wholesale and private label buyers. "
        f"OEM and ODM available, MOQ from 500 units, CE, FCC and ROHS compliant. "
        f"Factory-direct manufacturer in Hefei, China. Request a quote in 24 hours."
    )
    return desc[:158]


def build_intro(product_name, category, keywords):
    return (
        f"<h2>Wholesale {product_name} manufacturer in China</h2>\n"
        f"<p>The <strong>{product_name}</strong> is part of our {category} product line, manufactured in our "
        f"factory in Hefei, Anhui Province, China. We supply this product on a wholesale basis to importers, distributors, "
        f"private-label brands and e-commerce operators worldwide, with OEM and ODM customization available on orders "
        f"starting at 500 units per SKU. Lead time from order to shipped container is 45 to 60 days, depending on "
        f"customization level and order volume.</p>\n"
        f"<p>If you are sourcing {keywords.split(',')[0]} for your brand or retail network, our foreign trade team can "
        f"provide a full quote with landed cost, FOB Ningbo pricing or DDP to your warehouse, sample availability, "
        f"and documentation for customs clearance in the United States, European Union, United Kingdom, Australia "
        f"and other markets.</p>\n"
    )


def build_b2b_section(product_name, category):
    return (
        f"\n<h2>OEM and ODM options for this {category.lower()}</h2>\n"
        f"<p>All our {category.lower()} models are available for customization. Common options include custom logo "
        f"(pad print, laser engraving or silk screen), custom colors with Pantone matching, custom packaging with your "
        f"artwork and barcodes, custom manual languages (English, French, German, Spanish, Italian, Portuguese, Polish, "
        f"Dutch), firmware localization, WiFi and Bluetooth pairing with your branded mobile app, and regional power plug "
        f"adapters (UK, EU, US, AU).</p>\n"
        f"<p>See our full <a href=\"/oem-odm-services/\">OEM and ODM services page</a> for process details, MOQs, "
        f"lead times, and customization scope.</p>\n"
        f"\n<h2>Certifications and quality</h2>\n"
        f"<p>This product is compliant with CE (European Union), UKCA (United Kingdom), FCC (United States), PSE (Japan), "
        f"RCM (Australia), ROHS and REACH. Test reports from accredited labs and Declarations of Conformity are available "
        f"on request. Third-party inspections (SGS, Bureau Veritas, TUV, QIMA) are welcome at the buyer's cost. See the "
        f"full list on our <a href=\"/certifications-quality/\">certifications and quality page</a>.</p>\n"
        f"\n<h2>Shipping and logistics</h2>\n"
        f"<p>We ship this {category.lower()} worldwide under Incoterms FOB, CIF or DDP. Typical ocean freight transit times: "
        f"18 to 25 days to the US West Coast, 30 to 40 days to the US East Coast, 32 to 40 days to Northern Europe, "
        f"22 to 30 days to Australia. Air freight is available for urgent reorders at higher cost. See our "
        f"<a href=\"/shipping-logistics/\">shipping and logistics page</a> for details on HS codes, container loadability, "
        f"and export documentation.</p>\n"
    )


def build_cta(product_name):
    return (
        f"\n<h2>Request a quote for the {product_name}</h2>\n"
        f"<p>To get a full quote on the {product_name} for your market, with MOQ, unit price, lead time and shipping "
        f"cost to your destination, contact Ryan Lau, our Foreign Trade Manager, at "
        f"<a href=\"mailto:ryanlau@eviehometech.com\">ryanlau@eviehometech.com</a> or on WhatsApp at "
        f"<a href=\"https://wa.me/8619956530913\">+86 199 5653 0913</a>. You can also "
        f"<a href=\"/contact-us/\">use the contact form</a> to send your specification and target volume.</p>\n"
        f"<p>We respond to every serious B2B inquiry within 24 business hours.</p>\n"
    )


def get_category(categories):
    if not categories:
        return DEFAULT_CATEGORY
    for tid in categories:
        if tid in CATEGORY_MAP:
            return CATEGORY_MAP[tid]
    return DEFAULT_CATEGORY


def enrich_product(product, nonce, force=False):
    pid = product["id"]
    title = product["title"]["rendered"] if isinstance(product["title"], dict) else product["title"]
    raw_content = product["content"]["raw"] if "raw" in product["content"] else ""
    cats = product.get("product-category", [])
    category, keywords = get_category(cats)

    # Detect previous enrichment: strip it and keep only the Product specifications block
    already_enriched = "<h2>Wholesale" in raw_content
    if already_enriched:
        if not force:
            print(f"  [skip] {title} — already enriched")
            return "skipped"
        # Extract the original specs block (between <h2>Product specifications</h2> and the next <h2>)
        m = re.search(r"<h2>Product specifications</h2>\s*(.*?)(?=\n<h2>)", raw_content, re.DOTALL)
        if m:
            raw_content = m.group(1).strip()
        else:
            # Fallback: strip every h2 section we added
            raw_content = re.sub(r"<h2>(Wholesale|OEM|Certifications|Shipping|Request a quote|Product specifications)[^<]*</h2>.*?(?=\n<h2>|$)", "", raw_content, flags=re.DOTALL)

    intro = build_intro(title, category, keywords)
    b2b = build_b2b_section(title, category)
    cta = build_cta(title)

    # Keep the original content (specs + video) as the "Product specifications" block
    specs_section = (
        "\n<h2>Product specifications</h2>\n"
        + raw_content.strip()
        + "\n"
    )

    new_content = intro + specs_section + b2b + cta

    # Push content update
    status, data = api(
        "POST",
        f"/wp/v2/products/{pid}",
        data={"content": new_content},
        nonce=nonce,
    )
    if status not in (200, 201):
        print(f"  [ERROR] content update {pid}: {status} {str(data)[:200]}")
        return "error"

    # Push SEO meta
    meta_title = build_meta_title(title, category)
    meta_desc = build_meta_description(title, category, keywords)
    status, data = api(
        "POST",
        "/surerank/v1/post/settings",
        data={"post_id": pid, "metaData": {"page_title": meta_title, "page_description": meta_desc}},
        nonce=nonce,
    )
    if status not in (200, 201):
        print(f"  [WARN] seo update {pid}: {status} {str(data)[:200]}")

    print(f"  [ok] {title} (id={pid}, cat={category})")
    return "ok"


def main():
    nonce = get_rest_nonce()
    print(f"nonce: {nonce}")

    # Fetch all products in one page (35 items)
    status, data = api(
        "GET",
        "/wp/v2/products",
        nonce=nonce,
        params={"per_page": 100, "context": "edit", "status": "publish"},
    )
    if not isinstance(data, list):
        print(f"ERROR fetching products: {status} {str(data)[:200]}")
        return
    print(f"fetched {len(data)} products\n")

    force = "--force" in sys.argv
    counters = {"ok": 0, "skipped": 0, "error": 0}
    for product in data:
        result = enrich_product(product, nonce, force=force)
        counters[result] = counters.get(result, 0) + 1
        time.sleep(0.2)

    print("\n=== Summary ===")
    for k, v in counters.items():
        print(f"  {k}: {v}")

    print("\n=== Purging LiteSpeed cache ===")
    purge_cache()


if __name__ == "__main__":
    main()
