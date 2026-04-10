---
subject: [Leo Project] Initial SEO audit of eviehometech.com: 3 critical blockers to unlock
to: stepnsecondaire@gmail.com
---

# Initial SEO audit: eviehometech.com

Hi Leo,

Here is the recap of the complete audit we just ran on **eviehometech.com** (Hefei Ecologie Vie Home Technology Co., Ltd.). The goal is clear: maximize organic Google traffic from Europe and the United States to capture recurring B2B buyers (importers, distributors, private label brands, e-commerce operators) who are looking for a reliable wholesale / OEM partner in China.

## What we did

- Extracted the full WordPress export (905 MB `.wpress` file)
- Read-only audit of the technical stack (themes, plugins, mu-plugins, versions)
- Analysis of the MySQL database (548 posts, 2329 postmeta, 131 terms)
- On-page SEO audit (meta tags, schemas, headings, hreflang, hardcoded strings)
- Performance audit (1 640 media files, focus on 4K videos)
- Drafted the full action plan in `AUDIT.md`

Nothing was modified on the live site. Pure read-only mode.

## Your business as we see it

Chinese manufacturer of **smart pet products**, wholesale + OEM/ODM. 37 products currently published across 8 categories:

- Automatic Cat Litter Box (14)
- Automatic Cat Fountain (7)
- Bird Feeder (6)
- Pet Air Purifier (4)
- Vacuum Cleaner (4)
- Automatic Dog Feeder (3)
- Pet Smart Toys (2)
- Bark Collar & GPS Tracker (1)

Eight live pages: Home, Products, About, Contact, Reviews, News, Privacy, Terms, FAQs.

## What is working well

- **Modern and clean stack**: WordPress 6.9.4, Breakdance (page builder), ACF Pro, SureRank SEO, LiteSpeed Cache, Cimo Image Optimizer. Everything is up to date.
- **Custom minimal theme**: zero hardcoded text in the PHP files, 100% of the front-end content is editable from the WordPress admin. This is exactly what we want.
- **Images are already very well optimized**: 98.5% WebP, average 31 KB per file.
- **JSON-LD schemas already configured**: Organization, WebSite, SearchAction, WebPage.
- **Serious hosting**: Hostinger (clean official mu-plugins).
- **Security is overall healthy**: one admin only, no backdoor detected, all plugins up to date.

## The 3 critical blockers (must be fixed before any content strategy)

### 1. The site is completely invisible to Google

The file `litespeed/robots.txt` literally contains:

```
User-agent: *
Disallow: /
```

Google and Bing are formally forbidden from crawling anything. The site exists but it is not in the index. **No SEO is possible until this is fixed.**

**Fix: 2 minutes of work.** Replace it with a clean robots.txt that allows crawling and references the sitemap.

### 2. The site is 100% in Chinese with zero multilingual plugin

`WPLANG = zh_CN`. No WPML, no Polylang, no Weglot, nothing. All content (pages, products, menus, ACF fields) is in Chinese. The main menu is named "主菜单".

Today the site is **unreachable** for American, British, German, French B2B buyers. Even if we unlock Google, what it would index is Chinese, which will never match the English queries we are targeting (`china sourcing`, `pet products manufacturer`, `OEM cat feeder`, and so on).

**Fix: 3 to 5 days of work.** Install **Polylang Pro** (recommended over WPML: lighter, fully compatible with Breakdance and ACF, better for performance) then build the complete English version of the 8 pages and 37 products.

### 3. 78% of pages have no meta description

SureRank can handle meta descriptions, but most pages and products have theirs empty. As a result, Google will fabricate its own SERP snippets, often poor ones, and the CTR on search results will be weak even if we rank well.

**Fix: 1 day of work.** Manually fill 45 meta descriptions (8 pages + 37 products) in SEO-optimized English targeting the B2B keywords.

## Other important findings (not critical but high impact)

- **446 MB of uncompressed 4K videos** (42 files, 8 to 29 MB each). This kills the Core Web Vitals scores. Re-encoding in H.265 1080p with ffmpeg would save around **-380 MB (-85%)**.
- **No contact or quote form** detected (no Contact Form 7, WPForms, Fluent Forms). For a B2B site whose goal is to generate leads, this is a huge gap.
- **An `eval()` call** in the `assets4breakdance` plugin (Supa Code Block) needs a quick security review: who can publish this block? (RCE risk if the capability check is too loose).
- **Pages with weak H1 / H2 structure** flagged by SureRank.
- **No WhatsApp Business chat** visible even though the number is already declared in the Organization schema.

## Why we are using GitHub

We put the project on GitHub ([stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress)) for three reasons:

1. **Versioning**: every modification is tracked (who, when, why). If we break something, we revert in one command.
2. **Collaboration**: both of us see the same thing and can review changes before they hit production.
3. **Backup**: the site code is duplicated outside Hostinger. If the server dies, we still have everything.

We are **not** pushing the entire `.wpress` content:

- The 508 MB `uploads/` folder stays out of git (images and videos, too heavy)
- The 243 MB `wpvividbackups/` stays out (obsolete backups)
- Paid plugins code (Breakdance, ACF Pro, SureRank Pro) stays out (license compliance)
- What is tracked: `AUDIT.md`, the custom theme `breakdance-zero-theme-master`, the `database.sql` dump, the automation scripts, and the recap emails

Final repo is clean, around 10 MB instead of 905 MB.

## Next steps

### Phase 1: unblocking (this week)
1. Fix `robots.txt`
2. Security review of the `eval()` in assets4breakdance
3. Publish sitemap + set up Google Search Console and Bing Webmaster Tools
4. Install a B2B contact form
5. Write the 45 missing meta descriptions

### Phase 2: English version (weeks 2 to 3)
Polylang Pro + full English translation.

### Phase 3: performance (week 4)
Re-encode videos in H.265 + Cloudflare CDN.

### Phase 4: B2B content (weeks 5 to 8)
OEM/ODM capabilities page, Quality & Certifications page, Factory Tour, Case Studies, product copy rewrite, Product/FAQ/Breadcrumb schemas.

### Phase 5: SEO blog (months 3 to 6)
Programmatic SEO blog pipeline to keep publishing targeted articles without manual effort.

## What we need from you to start Phase 1

1. **Validation of the plan above** (or adjustments you want)
2. **WordPress admin access** (URL + login) when we move to live actions
3. **Hostinger credentials** (or SFTP / SSH access) to fix robots.txt
4. **Confirmation you are OK with the repo setup** on GitHub

The full document with all the checkbox action items is in `AUDIT.md` at the root of the repo.

Have a good day,

The Leo Project team
