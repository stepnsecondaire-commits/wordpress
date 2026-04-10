#!/usr/bin/env python3
"""
send_mail.py — Envoi des emails récap du projet leo (eviehometech.com SEO).

Usage :
    python3 scripts/send_mail.py audit/emails/01-audit-initial.md

Format des fichiers email (Markdown avec frontmatter YAML simple) :
    ---
    subject: Ton sujet ici
    to: stepnsecondaire@gmail.com
    ---
    Corps du mail en markdown...

Configuration :
    GMAIL_USER          = adresse Gmail expéditrice (ex: augustin.foucheres@gmail.com)
    GMAIL_APP_PASSWORD  = mot de passe d'application Google (16 caractères sans espaces)
                          → https://myaccount.google.com/apppasswords

Stockage recommandé : mettre ces vars dans ~/.zshrc ou dans /Users/lestoilettesdeminette/leo/.env
(le .env est gitignored).
"""

import os
import re
import smtplib
import sys
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from pathlib import Path


def load_env_file(env_path: Path) -> None:
    """Charge un fichier .env basique (KEY=VALUE par ligne)."""
    if not env_path.exists():
        return
    for raw in env_path.read_text().splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, _, value = line.partition("=")
        os.environ.setdefault(key.strip(), value.strip().strip('"').strip("'"))


def parse_email_file(path: Path) -> tuple[dict, str]:
    """Parse un fichier markdown avec frontmatter YAML simple."""
    content = path.read_text(encoding="utf-8")
    m = re.match(r"^---\n(.*?)\n---\n(.*)$", content, re.DOTALL)
    if not m:
        raise ValueError(f"{path} n'a pas de frontmatter YAML valide (--- ... ---)")

    frontmatter_raw, body = m.group(1), m.group(2).lstrip("\n")
    meta = {}
    for line in frontmatter_raw.splitlines():
        if ":" in line:
            k, _, v = line.partition(":")
            meta[k.strip()] = v.strip()
    return meta, body


def markdown_to_html(md: str) -> str:
    """Conversion markdown → HTML minimale (titres, gras, code, listes, liens, paragraphes)."""
    html_lines = []
    in_list = False
    in_code = False
    for raw_line in md.split("\n"):
        line = raw_line.rstrip()

        if line.startswith("```"):
            if in_code:
                html_lines.append("</pre>")
                in_code = False
            else:
                html_lines.append("<pre style='background:#f4f4f4;padding:10px;border-radius:4px;overflow-x:auto;font-family:Menlo,monospace;font-size:13px;'>")
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

        # Headings
        if line.startswith("### "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h3 style='color:#222;margin-top:24px;'>{line[4:]}</h3>")
            continue
        if line.startswith("## "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h2 style='color:#222;margin-top:28px;border-bottom:1px solid #eee;padding-bottom:6px;'>{line[3:]}</h2>")
            continue
        if line.startswith("# "):
            if in_list:
                html_lines.append("</ul>")
                in_list = False
            html_lines.append(f"<h1 style='color:#111;'>{line[2:]}</h1>")
            continue

        # List items
        if line.startswith("- ") or line.startswith("* "):
            if not in_list:
                html_lines.append("<ul>")
                in_list = True
            item = line[2:]
            item = apply_inline(item)
            html_lines.append(f"<li>{item}</li>")
            continue

        if in_list:
            html_lines.append("</ul>")
            in_list = False

        html_lines.append(f"<p>{apply_inline(line)}</p>")

    if in_list:
        html_lines.append("</ul>")
    if in_code:
        html_lines.append("</pre>")

    body_html = "\n".join(html_lines)
    return f"""<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:-apple-system,Segoe UI,Helvetica,Arial,sans-serif;max-width:680px;margin:0 auto;padding:24px;color:#333;line-height:1.55;">
{body_html}
<hr style='margin-top:32px;border:none;border-top:1px solid #eee;'>
<p style='color:#888;font-size:12px;'>Email auto-genere par leo/scripts/send_mail.py — projet SEO eviehometech.com</p>
</body></html>"""


def apply_inline(text: str) -> str:
    text = re.sub(r"\[([^\]]+)\]\(([^)]+)\)", r'<a href="\2" style="color:#2563eb;">\1</a>', text)
    text = re.sub(r"\*\*([^*]+)\*\*", r"<strong>\1</strong>", text)
    text = re.sub(r"`([^`]+)`", r'<code style="background:#f4f4f4;padding:2px 5px;border-radius:3px;font-family:Menlo,monospace;font-size:13px;">\1</code>', text)
    return text


def send(subject: str, to_addr: str, body_md: str) -> None:
    user = os.environ.get("GMAIL_USER")
    password = os.environ.get("GMAIL_APP_PASSWORD")
    if not user or not password:
        raise RuntimeError(
            "Variables manquantes : GMAIL_USER et GMAIL_APP_PASSWORD.\n"
            "Cree /Users/lestoilettesdeminette/leo/.env avec :\n"
            "    GMAIL_USER=ton.email@gmail.com\n"
            "    GMAIL_APP_PASSWORD=xxxxxxxxxxxxxxxx\n"
            "(genere sur https://myaccount.google.com/apppasswords)"
        )

    msg = MIMEMultipart("alternative")
    msg["Subject"] = subject
    msg["From"] = f"leo (eviehometech SEO) <{user}>"
    msg["To"] = to_addr
    msg.attach(MIMEText(body_md, "plain", "utf-8"))
    msg.attach(MIMEText(markdown_to_html(body_md), "html", "utf-8"))

    with smtplib.SMTP_SSL("smtp.gmail.com", 465) as server:
        server.login(user, password)
        server.sendmail(user, [to_addr], msg.as_string())

    print(f"[OK] Envoye a {to_addr} : {subject}")


def main() -> int:
    if len(sys.argv) < 2:
        print("Usage : python3 scripts/send_mail.py <chemin-vers-mail.md>", file=sys.stderr)
        return 1

    project_root = Path(__file__).resolve().parent.parent
    load_env_file(project_root / ".env")

    mail_path = Path(sys.argv[1]).resolve()
    if not mail_path.exists():
        print(f"Fichier introuvable : {mail_path}", file=sys.stderr)
        return 1

    meta, body = parse_email_file(mail_path)
    subject = meta.get("subject", "(sans sujet)")
    to_addr = meta.get("to", "stepnsecondaire@gmail.com")

    send(subject, to_addr, body)
    return 0


if __name__ == "__main__":
    sys.exit(main())
