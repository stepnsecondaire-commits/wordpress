#!/usr/bin/env python3
"""
ui_critical_fixes.py - applies the Phase 1 critical UI fixes on eviehometech.com
by parsing the Breakdance _breakdance_data JSON tree, walking the nodes,
mutating the right values, and posting the result back via the leo plugin endpoint.

Each fix is atomic, idempotent, and preceded by a backup of the current value.

C1 Contact Us  - replace OEM batteries placeholder copy.
C2 Footer      - replace lorem ipsum newsletter copy.
C3 Header      - replace href="#" on the Request a Quote button with /contact-us/.
C4 Homepage    - sane counter start/end/prefix/suffix values.
C5 Taxonomy    - rename "Bark Collar& GPS Track" to "Bark Collar & GPS Tracker".
"""

import http.client

http.client._MAXHEADERS = 1000

import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from wp_push_pages import api, get_rest_nonce, purge_cache  # noqa: E402


def get_bd_tree(post_id, nonce):
    status, data = api("GET", "/leo/v1/postmeta", nonce=nonce, params={"post_id": post_id})
    if not isinstance(data, dict) or "meta" not in data:
        raise RuntimeError(f"cannot read postmeta for {post_id}: {data}")
    bd_raw = data["meta"].get("_breakdance_data", [None])[0]
    if not bd_raw:
        raise RuntimeError(f"no _breakdance_data on post {post_id}")
    parsed = json.loads(bd_raw)
    tree = json.loads(parsed["tree_json_string"])
    return parsed, tree


def set_bd_tree(post_id, parsed, tree, nonce):
    parsed["tree_json_string"] = json.dumps(tree, ensure_ascii=False, separators=(",", ":"))
    new_value = json.dumps(parsed, ensure_ascii=False, separators=(",", ":"))
    status, data = api(
        "POST",
        "/leo/v1/postmeta",
        nonce=nonce,
        data={"post_id": post_id, "key": "_breakdance_data", "value": new_value},
    )
    if not isinstance(data, dict) or not data.get("success"):
        raise RuntimeError(f"update failed for {post_id}: {data}")


def walk(node, fn):
    """Walk the tree and call fn(node) on every dict node."""
    if isinstance(node, dict):
        fn(node)
        for v in node.values():
            walk(v, fn)
    elif isinstance(node, list):
        for v in node:
            walk(v, fn)


def find_text_node(node, text):
    """Return True if this node is a text-bearing element containing `text`."""
    if not isinstance(node, dict):
        return False
    data = node.get("data", {}) if isinstance(node.get("data"), dict) else {}
    props = data.get("properties", {})

    def search(o):
        if isinstance(o, str) and text in o:
            return True
        if isinstance(o, dict):
            return any(search(v) for v in o.values())
        if isinstance(o, list):
            return any(search(v) for v in o)
        return False

    return search(props)


def replace_in_strings(node, find_text, replace_text):
    """Recursively replace `find_text` with `replace_text` in every string field."""
    count = [0]

    def helper(o):
        if isinstance(o, dict):
            for k, v in list(o.items()):
                if isinstance(v, str) and find_text in v:
                    o[k] = v.replace(find_text, replace_text)
                    count[0] += 1
                else:
                    helper(v)
        elif isinstance(o, list):
            for v in o:
                helper(v)

    helper(node)
    return count[0]


def c1_contact_battery(nonce):
    print("C1: contact form battery text")
    parsed, tree = get_bd_tree(27, nonce)
    full_old = "If you are interested in OEM batteries or wholesale batteries, please leave a message here and we will send you a quote as soon as possible."
    full_new = "Interested in OEM/ODM smart pet products or wholesale orders? Leave us a message and we will send you a detailed quote within 24 hours."
    n = replace_in_strings(tree, full_old, full_new)
    if n == 0:
        # Fallback: partial replacement
        n += replace_in_strings(tree, "OEM batteries", "OEM smart pet products")
        n += replace_in_strings(tree, "wholesale batteries", "wholesale pet products")
    if n == 0:
        print("  no change")
        return False
    set_bd_tree(27, parsed, tree, nonce)
    print(f"  OK - {n} replacement(s)")
    return True


def c2_footer_lorem(nonce):
    print("C2: footer lorem ipsum")
    parsed, tree = get_bd_tree(41, nonce)
    new_text = (
        "Stay updated with our latest smart pet products, trade show appearances, "
        "and industry insights. Join 2000+ pet industry professionals."
    )
    s1 = "Sagittis scelerisque nulla cursus in enim consectetur quam."
    s2 = "Dictum urna sed consectetur neque tristique pellentesque."
    n = replace_in_strings(tree, s1 + " " + s2, new_text)
    if n == 0:
        n += replace_in_strings(tree, s1, new_text)
        n += replace_in_strings(tree, s2, "")
    if n == 0:
        print("  no change")
        return False
    set_bd_tree(41, parsed, tree, nonce)
    print(f"  OK - {n} replacement(s)")
    return True


def c3_header_request_quote(nonce):
    print("C3: header Request a Quote href")
    parsed, tree = get_bd_tree(43, nonce)

    found_count = [0]
    fixed_count = [0]

    def fix_button(node):
        if not isinstance(node, dict):
            return
        data = node.get("data", {}) if isinstance(node.get("data"), dict) else {}
        if data.get("type") != "EssentialElements\\Button":
            return
        props = data.get("properties", {})
        content = props.get("content", {}).get("content", {})
        text = content.get("text", "")
        if text != "Request a Quote":
            return
        found_count[0] += 1
        link = content.get("link", {})
        old_url = link.get("url", "")
        if old_url in ("", "#", "/"):
            link["url"] = "/contact-us/"
            link["type"] = "url"
            fixed_count[0] += 1
            print(f"  found button id={node.get('id')}, old url={old_url!r}, set to /contact-us/")

    walk(tree, fix_button)
    print(f"  buttons matching 'Request a Quote': {found_count[0]}")
    if fixed_count[0] == 0:
        print("  nothing to fix")
        return False
    set_bd_tree(43, parsed, tree, nonce)
    print(f"  OK - {fixed_count[0]} button(s) fixed")
    return True


def c4_homepage_counters(nonce):
    print("C4: homepage counters")
    parsed, tree = get_bd_tree(31, nonce)

    target_values = {
        "Years of Experience":  {"start": 0, "end": 8,   "prefix": None, "suffix": "+"},
        "Export Countries":     {"start": 0, "end": 30,  "prefix": None, "suffix": "+"},
        "Serving Customers":    {"start": 0, "end": 500, "prefix": None, "suffix": "+"},
        "Products":             {"start": 0, "end": 200, "prefix": None, "suffix": "+"},
        "Annual Sales Revenue": {"start": 0, "end": 50,  "prefix": "$",  "suffix": "M+"},
        "Technical Personnel":  {"start": 0, "end": 100, "prefix": None, "suffix": "+"},
    }
    fixed = [0]

    def fix_counter(node):
        if not isinstance(node, dict):
            return
        data = node.get("data", {}) if isinstance(node.get("data"), dict) else {}
        if data.get("type") != "EssentialElements\\SimpleCounter":
            return
        counter = data.get("properties", {}).get("content", {}).get("counter", {})
        title = counter.get("title", "").strip()
        if title in target_values:
            new = target_values[title]
            counter["start"] = new["start"]
            counter["end"] = new["end"]
            counter["prefix"] = new["prefix"]
            counter["suffix"] = new["suffix"]
            fixed[0] += 1
            print(f"  set {title!r} -> {new}")

    walk(tree, fix_counter)
    if fixed[0] == 0:
        print("  no counters matched")
        return False
    set_bd_tree(31, parsed, tree, nonce)
    print(f"  OK - {fixed[0]} counter(s) updated")
    return True


def c5_taxonomy_rename(nonce):
    print("C5: rename taxonomy term Bark Collar& GPS Track")
    status, data = api(
        "POST",
        "/wp/v2/product-category/10",
        nonce=nonce,
        data={"name": "Bark Collar & GPS Tracker"},
    )
    if isinstance(data, dict) and data.get("id") == 10:
        print(f"  OK - renamed to {data['name']!r}")
        return True
    print(f"  failed: {data}")
    return False


def main():
    nonce = get_rest_nonce()
    print(f"REST nonce: {nonce}\n")

    c5_taxonomy_rename(nonce)
    print()
    c1_contact_battery(nonce)
    print()
    c2_footer_lorem(nonce)
    print()
    c3_header_request_quote(nonce)
    print()
    c4_homepage_counters(nonce)
    print()

    print("Purging cache...")
    purge_cache()
    print("Done.")


if __name__ == "__main__":
    main()
