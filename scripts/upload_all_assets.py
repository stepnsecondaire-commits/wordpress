#!/usr/bin/env python3
"""
upload_all_assets.py - mass upload of customer photos, exhibition photos,
review screenshots, certificate PDFs and videos to the WordPress media
library on eviehometech.com. Sets a descriptive English filename, alt text,
caption and title on every uploaded asset for SEO. Saves the resulting
media IDs to audit/uploaded-assets.json so the site rebuild scripts can
reference them.
"""

import http.client

http.client._MAXHEADERS = 1000

import json
import mimetypes
import sys
import time
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from wp_push_pages import api, get_rest_nonce, BASE, COOKIE_HEADER  # noqa: E402

import urllib.error
import urllib.request

DESKTOP = Path("/Users/lestoilettesdeminette/Desktop/doc site leo")
OUT_FILE = Path(__file__).resolve().parent.parent / "audit" / "uploaded-assets.json"

# (source_path, target_filename, alt_text, caption, title)
ASSETS = [
    # Customer / partnership photos
    (
        "photo avec les clients/customers (1).jpg",
        "eviehome-factory-customer-visit-1.jpg",
        "International B2B buyers visiting Eviehome smart pet products factory in Hefei China to inspect cat litter box production line",
        "Eviehome welcomes international B2B buyers to inspect production at our Hefei factory",
        "Customer visit at Eviehome factory Hefei",
    ),
    (
        "photo avec les clients/customers (2).jpg",
        "eviehome-factory-customer-visit-2.jpg",
        "Foreign B2B importer reviewing Eviehome smart cat litter box product samples in person at Hefei factory China",
        "International importer reviewing smart pet product samples at Eviehome factory",
        "B2B customer inspecting Eviehome cat litter box samples",
    ),
    (
        "photo avec les clients/exhibitions (1).jpg",
        "eviehome-trade-show-booth-cat-litter-1.jpg",
        "Eviehome exhibition booth at China pet products trade show 2025 displaying smart self-cleaning cat litter boxes and certifications",
        "Eviehome booth at the 2025 China pet products trade show with full product line",
        "Eviehome exhibition booth pet products trade show",
    ),
    (
        "photo avec les clients/exhibitions (2).jpg",
        "eviehome-trade-show-cat-litter-display-2.jpg",
        "Eviehome smart automatic cat litter boxes on display at Zhejiang Zhaohong booth China pet exhibition",
        "Eviehome smart cat litter box range on display at the China pet expo",
        "Smart cat litter boxes display Eviehome trade show",
    ),
    (
        "photo avec les clients/exhibitions (3).jpg",
        "eviehome-export-manager-trade-show-3.jpg",
        "Eviehome export manager presenting OEM smart pet products at China international pet trade show",
        "Eviehome export team presenting OEM smart pet products at international trade show",
        "Eviehome export manager OEM smart pet products",
    ),
    (
        "photo avec les clients/exhibitions (4).jpg",
        "eviehome-cat-litter-box-lineup-trade-show-4.jpg",
        "Three Eviehome smart automatic cat litter box models displayed at China international pet supplies exhibition",
        "Three Eviehome cat litter box models on display at the China pet supplies expo",
        "Eviehome cat litter box product lineup trade show",
    ),
    # New review screenshots
    (
        "review7/review1.png",
        "eviehome-alibaba-review-saqib-farooq-pakistan-vladimir-fliser-slovenia.png",
        "Verified Alibaba reviews of Eviehome smart pet water dispenser by Saqib Farooq Pakistan and ultra-safe cat litter box by Vladimir Fliser Slovenia, both 5 stars",
        "Verified Alibaba Trade Assurance reviews from Pakistan and Slovenia",
        "Eviehome 5-star reviews Pakistan Slovenia",
    ),
    (
        "review7/review2.png",
        "eviehome-alibaba-review-shahid-mahmood-uae-m1-cat-litter.png",
        "Verified Alibaba review of Eviehome M1 large 100L automatic smart cat litter box by Shahid Mahmood from United Arab Emirates, 5 stars",
        "Verified Alibaba review from UAE on the M1 large cat litter box",
        "Eviehome 5-star review UAE M1 cat litter",
    ),
    (
        "review7/review3.png",
        "eviehome-alibaba-review-jasmin-movahedian-italy-cat-litter.png",
        "Verified Alibaba review of Eviehome OEM electric smart self cleaning cat litter box by Jasmin Movahedian from Italy, 5 stars",
        "Verified Alibaba review from Italy on the OEM smart cat litter box",
        "Eviehome 5-star review Italy cat litter",
    ),
    (
        "review7/review4.png",
        "eviehome-alibaba-review-user-singapore-multiple-orders.png",
        "Verified Alibaba reviews from Singapore on Eviehome smart cat litter box and factory wholesale cat feeder, 5 stars on multiple orders",
        "Verified repeat-buyer Alibaba reviews from Singapore on cat litter box and feeder",
        "Eviehome 5-star repeat buyer Singapore",
    ),
    (
        "review7/review5.png",
        "eviehome-alibaba-review-arno-gregary-korea-justin-davidson-australia.png",
        "Verified Alibaba reviews of Eviehome OEM cat litter box by Arno Gregary South Korea and pet water dispenser by Justin Davidson Australia, 5 stars",
        "Verified Alibaba reviews from South Korea and Australia",
        "Eviehome 5-star reviews Korea Australia",
    ),
    (
        "review7/review6.png",
        "eviehome-alibaba-review-mohammad-mohsin-india-cat-litter.png",
        "Verified Alibaba review of Eviehome OEM smart cat litter box by Mohammad Mohsin from India, 5 stars",
        "Verified Alibaba review from India on the OEM smart cat litter box",
        "Eviehome 5-star review India cat litter",
    ),
    (
        "review7/review7.png",
        "eviehome-alibaba-review-singapore-recordable-buzzers.png",
        "Verified Alibaba review of Eviehome recordable answer buzzers for dog talk buttons from Singapore buyer, 5 stars",
        "Verified Alibaba review from Singapore on recordable dog buttons",
        "Eviehome 5-star review Singapore dog buttons",
    ),
]

CERT_FOUNTAIN = [
    ("certificates for fountain/PRMS2506107-01 CE-EMC Certificate of Compliance_已签章.pdf",  "eviehome-pet-fountain-ce-emc-certificate.pdf",   "CE EMC Certificate of Compliance for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-01 CE-EMC report_已签章.pdf",                     "eviehome-pet-fountain-ce-emc-test-report.pdf",   "CE EMC test report for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-01SC EN IEC 60335_certificate_已签章.pdf",        "eviehome-pet-fountain-en-iec-60335-safety-certificate.pdf", "EN IEC 60335 electrical safety certificate for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-01SR EN IEC 60335_report_已签章.pdf",             "eviehome-pet-fountain-en-iec-60335-safety-report.pdf",      "EN IEC 60335 electrical safety test report for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-02 BS EN-EMC Certificate of Compliance_已签章.pdf","eviehome-pet-fountain-uk-bs-en-emc-certificate.pdf",        "UK BS EN EMC certificate of compliance for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-02 BS EN-EMC report_已签章.pdf",                  "eviehome-pet-fountain-uk-bs-en-emc-report.pdf",             "UK BS EN EMC test report for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-03SC_ROHS_certificate_已签章.pdf",                "eviehome-pet-fountain-rohs-certificate.pdf",                "ROHS certificate for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506107-03SR_ROHS_Report_已签章.pdf",                     "eviehome-pet-fountain-rohs-test-report.pdf",                "ROHS test report for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506155-01SC BS EN IEC 60335_certificate_已签章.pdf",     "eviehome-pet-fountain-uk-bs-en-iec-60335-certificate.pdf",  "UK BS EN IEC 60335 safety certificate for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506155-01SR BS EN IEC 60335_report(2)_已签章.pdf",       "eviehome-pet-fountain-uk-bs-en-iec-60335-report.pdf",       "UK BS EN IEC 60335 safety test report for Eviehome pet water fountain"),
    ("certificates for fountain/PRMS2506224-01 FCC Part 15 Subpart B_已签章.pdf",             "eviehome-pet-fountain-fcc-part-15-subpart-b-report.pdf",    "FCC Part 15 Subpart B test report for Eviehome pet water fountain (USA)"),
    ("certificates for fountain/PRMS2506224-01 FCC SDOC-Certificate_已签章.pdf",              "eviehome-pet-fountain-fcc-sdoc-certificate.pdf",            "FCC SDOC certificate for Eviehome pet water fountain (USA)"),
    ("certificates for fountain/AZT24070550C-E1-宠物饮水机-REACH 附件 17 邻苯 英_已签章(1).pdf","eviehome-pet-fountain-reach-annex-17-phthalates-report.pdf","REACH Annex 17 phthalates compliance report for Eviehome pet water fountain"),
    ("certificates for fountain/AZT24070551C-E1-宠物饮水机-REACH 附件 17 铅 镉 英_已签章(1).pdf","eviehome-pet-fountain-reach-annex-17-lead-cadmium-report.pdf","REACH Annex 17 lead and cadmium compliance report for Eviehome pet water fountain"),
    ("certificates for fountain/宠物饮水机防尘防水IP68报告.pdf",                             "eviehome-pet-fountain-ip68-dust-water-resistance-report.pdf","IP68 dust and water resistance test report for Eviehome pet water fountain"),
    ("certificates for fountain/宠物饮水机防尘防水IP68证书.pdf",                             "eviehome-pet-fountain-ip68-dust-water-resistance-certificate.pdf","IP68 dust and water resistance certificate for Eviehome pet water fountain"),
    ("certificates for fountain/CE doc.jpg",                                                  "eviehome-pet-fountain-ce-mark-document.jpg",                "CE mark document image for Eviehome pet water fountain"),
]

CERT_CLEANER = [
    ("cleaner certificates/无线除螨仪LP-018 CE-EMC证书正本.pdf",     "eviehome-cordless-dust-mite-vacuum-lp018-ce-emc-certificate.pdf", "CE EMC certificate for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 CE-EMC报告正本.pdf",     "eviehome-cordless-dust-mite-vacuum-lp018-ce-emc-report.pdf",      "CE EMC test report for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 FCCSDOC证书正本.pdf",    "eviehome-cordless-dust-mite-vacuum-lp018-fcc-sdoc-certificate.pdf","FCC SDOC certificate for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 FCCSDOC报告正本.pdf",    "eviehome-cordless-dust-mite-vacuum-lp018-fcc-sdoc-report.pdf",    "FCC SDOC test report for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 IEC温升报告正本.pdf",    "eviehome-cordless-dust-mite-vacuum-lp018-iec-temperature-rise-report.pdf","IEC temperature rise test report for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 ROHS证书正本.pdf",       "eviehome-cordless-dust-mite-vacuum-lp018-rohs-certificate.pdf",   "ROHS certificate for Eviehome cordless dust mite vacuum LP-018"),
    ("cleaner certificates/无线除螨仪LP-018 ROHS报告正本.pdf",       "eviehome-cordless-dust-mite-vacuum-lp018-rohs-report.pdf",        "ROHS test report for Eviehome cordless dust mite vacuum LP-018"),
]

VIDEOS = [
    ("1 (38).mp4",     "eviehome-smart-cat-litter-box-product-demo.mp4",  "Smart self-cleaning cat litter box product demo by Eviehome factory"),
    ("factory (1).mp4","eviehome-factory-tour-production-line-1.mp4",      "Eviehome smart pet products factory tour production line video 1"),
    ("factory (2).mp4","eviehome-factory-tour-production-line-2.mp4",      "Eviehome smart pet products factory tour production line video 2"),
    ("factory (3).mp4","eviehome-factory-tour-production-line-3.mp4",      "Eviehome smart pet products factory tour production line video 3"),
]


def upload_file(src_path, dest_filename, alt_text, caption, title, nonce, mime_type=None):
    """Upload a single file to WP media via REST. Returns (id, url) or (None, error)."""
    src = DESKTOP / src_path
    if not src.exists():
        return None, f"file not found: {src}"
    if mime_type is None:
        mime_type = mimetypes.guess_type(dest_filename)[0] or "application/octet-stream"

    with src.open("rb") as f:
        data = f.read()

    boundary = "----leoBoundary" + str(int(time.time() * 1000))
    body_parts = [
        f"--{boundary}\r\n".encode(),
        f'Content-Disposition: form-data; name="file"; filename="{dest_filename}"\r\n'.encode(),
        f"Content-Type: {mime_type}\r\n\r\n".encode(),
        data,
        f"\r\n--{boundary}--\r\n".encode(),
    ]
    body = b"".join(body_parts)
    headers = {
        "Cookie": COOKIE_HEADER,
        "Content-Type": f"multipart/form-data; boundary={boundary}",
        "X-WP-Nonce": nonce,
        "User-Agent": "leo-asset-uploader/1.0",
    }
    req = urllib.request.Request(f"{BASE}/wp-json/wp/v2/media", data=body, headers=headers, method="POST")
    try:
        with urllib.request.urlopen(req, timeout=600) as resp:
            result = json.loads(resp.read())
    except urllib.error.HTTPError as e:
        return None, f"HTTP {e.code}: {e.read().decode()[:200]}"
    except Exception as e:
        return None, f"{type(e).__name__}: {e}"

    media_id = result.get("id")
    if not media_id:
        return None, str(result)[:200]

    # Set alt text + caption + title
    update_payload = {
        "alt_text": alt_text,
        "caption": caption,
        "title": title,
    }
    api(
        "POST",
        f"/wp/v2/media/{media_id}",
        nonce=nonce,
        data=update_payload,
    )
    return media_id, result.get("source_url")


def main():
    nonce = get_rest_nonce()
    print(f"REST nonce: {nonce}\n")

    output = {"images": {}, "pdfs_fountain": {}, "pdfs_cleaner": {}, "videos": {}, "errors": []}

    print("=== IMAGES (12 customer + review) ===")
    for src, dest, alt, caption, title in ASSETS:
        media_id, url_or_err = upload_file(src, dest, alt, caption, title, nonce)
        if media_id:
            print(f"  [ok] {src[:50]:50} -> {dest[:50]} (id={media_id})")
            output["images"][src] = {"id": media_id, "url": url_or_err, "alt": alt, "filename": dest}
        else:
            print(f"  [ERR] {src}: {url_or_err}")
            output["errors"].append({"src": src, "err": url_or_err})

    print("\n=== PDFs fountain (17) ===")
    for src, dest, alt in CERT_FOUNTAIN:
        media_id, url_or_err = upload_file(src, dest, alt, alt, alt, nonce)
        if media_id:
            print(f"  [ok] {dest[:60]} (id={media_id})")
            output["pdfs_fountain"][src] = {"id": media_id, "url": url_or_err, "alt": alt, "filename": dest}
        else:
            print(f"  [ERR] {src}: {url_or_err}")
            output["errors"].append({"src": src, "err": url_or_err})

    print("\n=== PDFs cleaner (7) ===")
    for src, dest, alt in CERT_CLEANER:
        media_id, url_or_err = upload_file(src, dest, alt, alt, alt, nonce)
        if media_id:
            print(f"  [ok] {dest[:60]} (id={media_id})")
            output["pdfs_cleaner"][src] = {"id": media_id, "url": url_or_err, "alt": alt, "filename": dest}
        else:
            print(f"  [ERR] {src}: {url_or_err}")
            output["errors"].append({"src": src, "err": url_or_err})

    print("\n=== VIDEOS (4) ===")
    for src, dest, alt in VIDEOS:
        size_mb = (DESKTOP / src).stat().st_size / (1024 * 1024)
        print(f"  uploading {src} ({size_mb:.0f} MB) ...")
        media_id, url_or_err = upload_file(src, dest, alt, alt, alt, nonce)
        if media_id:
            print(f"  [ok] {dest} (id={media_id})")
            output["videos"][src] = {"id": media_id, "url": url_or_err, "alt": alt, "filename": dest}
        else:
            print(f"  [ERR] {src}: {url_or_err}")
            output["errors"].append({"src": src, "err": url_or_err})

    OUT_FILE.parent.mkdir(parents=True, exist_ok=True)
    OUT_FILE.write_text(json.dumps(output, indent=2))
    print(f"\nSaved manifest to {OUT_FILE}")
    print(f"\nSummary: {len(output['images'])} images, {len(output['pdfs_fountain'])} fountain PDFs, {len(output['pdfs_cleaner'])} cleaner PDFs, {len(output['videos'])} videos, {len(output['errors'])} errors")


if __name__ == "__main__":
    main()
