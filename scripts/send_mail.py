#!/usr/bin/env python3
"""
send_mail.py - Sends recap emails for the Leo project (eviehometech.com SEO).

Routing: uses the ScoreImmo `send-email` Supabase Edge Function with `type: "custom"`.
The edge function wraps Resend and sends from `ScoreImmo <noreply@score-immo.fr>`.
No userId is sent, which bypasses preferences, cooldown and dedup checks.

Usage:
    python3 scripts/send_mail.py audit/emails/01-audit-initial.md

Email file format (Markdown with simple YAML frontmatter):
    ---
    subject: Your subject here
    to: stepnsecondaire@gmail.com
    ---
    Body in Markdown...
"""

import json
import os
import re
import sys
import urllib.request
from pathlib import Path

SUPABASE_URL = "https://afvtxiklivnmakqixkml.supabase.co"
SUPABASE_ANON_KEY = (
    "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9."
    "eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImFmdnR4aWtsaXZubWFrcWl4a21sIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzExOTcyMTgsImV4cCI6MjA4Njc3MzIxOH0."
    "oJIKsKtcEujZ3jAq79IZJqn16KRUDI-Ihzdjc7fE-wM"
)
SEND_EMAIL_URL = f"{SUPABASE_URL}/functions/v1/send-email"


def parse_email_file(path: Path) -> tuple[dict, str]:
    content = path.read_text(encoding="utf-8")
    m = re.match(r"^---\n(.*?)\n---\n(.*)$", content, re.DOTALL)
    if not m:
        raise ValueError(f"{path} has no YAML frontmatter (--- ... ---)")
    frontmatter_raw, body = m.group(1), m.group(2).lstrip("\n")
    meta: dict = {}
    for line in frontmatter_raw.splitlines():
        if ":" in line:
            k, _, v = line.partition(":")
            meta[k.strip()] = v.strip()
    return meta, body


def apply_inline(text: str) -> str:
    text = re.sub(r"\[([^\]]+)\]\(([^)]+)\)", r'<a href="\2" style="color:#2563eb;text-decoration:none;">\1</a>', text)
    text = re.sub(r"\*\*([^*]+)\*\*", r"<strong>\1</strong>", text)
    text = re.sub(r"`([^`]+)`", r'<code style="background:#f4f4f4;padding:2px 5px;border-radius:3px;font-family:Menlo,monospace;font-size:13px;">\1</code>', text)
    return text


def markdown_to_html(md: str) -> str:
    html_lines: list[str] = []
    in_list = False
    in_code = False
    for raw_line in md.split("\n"):
        line = raw_line.rstrip()

        if line.startswith("```"):
            if in_code:
                html_lines.append("</pre>")
                in_code = False
            else:
                if in_list:
                    html_lines.append("</ul>")
                    in_list = False
                html_lines.append("<pre style='background:#f4f4f4;padding:12px;border-radius:6px;overflow-x:auto;font-family:Menlo,monospace;font-size:13px;line-height:1.4;'>")
                in_code = True
            continue
        if in_code:
            html_lines.append(line.replace("<", "&lt;").replace(">", "&gt;"))
            continue

        if not line:
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append("")
            continue

        if line.startswith("### "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h3 style='color:#111;margin-top:24px;font-size:17px;'>{apply_inline(line[4:])}</h3>")
            continue
        if line.startswith("## "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h2 style='color:#111;margin-top:32px;border-bottom:1px solid #eee;padding-bottom:8px;font-size:20px;'>{apply_inline(line[3:])}</h2>")
            continue
        if line.startswith("# "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h1 style='color:#111;font-size:26px;'>{apply_inline(line[2:])}</h1>")
            continue

        if line.startswith("- ") or line.startswith("* "):
            if not in_list:
                html_lines.append("<ul style='padding-left:20px;'>")
                in_list = True
            item = apply_inline(line[2:])
            html_lines.append(f"<li style='margin:6px 0;'>{item}</li>")
            continue

        if in_list:
            html_lines.append("</ul>")
            in_list = False

        html_lines.append(f"<p style='margin:12px 0;'>{apply_inline(line)}</p>")

    if in_list:
        html_lines.append("</ul>")
    if in_code:
        html_lines.append("</pre>")

    body_html = "\n".join(html_lines)
    return f"""<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,Segoe UI,Helvetica,Arial,sans-serif;max-width:680px;margin:0 auto;padding:24px;color:#333;line-height:1.6;background:#fff;">
{body_html}
<hr style='margin-top:40px;border:none;border-top:1px solid #eee;'>
<p style='color:#888;font-size:12px;margin-top:16px;'>Automated recap email from the Leo Project (eviehometech.com SEO).</p>
</body></html>"""


def send(subject: str, to_addr: str, body_md: str) -> dict:
    payload = {
        "type": "custom",
        "email": to_addr,
        "subject": subject,
        "html": markdown_to_html(body_md),
    }
    req = urllib.request.Request(
        SEND_EMAIL_URL,
        data=json.dumps(payload).encode("utf-8"),
        headers={
            "Content-Type": "application/json",
            "Authorization": f"Bearer {SUPABASE_ANON_KEY}",
            "apikey": SUPABASE_ANON_KEY,
        },
        method="POST",
    )
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            body = resp.read().decode("utf-8")
            return {"status": resp.status, "body": json.loads(body) if body else {}}
    except urllib.error.HTTPError as e:
        return {"status": e.code, "body": e.read().decode("utf-8")}


def main() -> int:
    if len(sys.argv) < 2:
        print("Usage: python3 scripts/send_mail.py <path-to-mail.md>", file=sys.stderr)
        return 1

    mail_path = Path(sys.argv[1]).resolve()
    if not mail_path.exists():
        print(f"File not found: {mail_path}", file=sys.stderr)
        return 1

    meta, body = parse_email_file(mail_path)
    subject = meta.get("subject", "(no subject)")
    to_addr = meta.get("to", "stepnsecondaire@gmail.com")

    print(f"Sending to {to_addr}")
    print(f"Subject:   {subject}")
    result = send(subject, to_addr, body)
    print(f"Status:    {result['status']}")
    print(f"Response:  {result['body']}")
    return 0 if result["status"] == 200 else 1


if __name__ == "__main__":
    sys.exit(main())
