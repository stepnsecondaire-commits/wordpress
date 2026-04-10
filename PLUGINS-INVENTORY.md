# Plugins Inventory — eviehometech.com

Snapshot via WordPress REST API on 2026-04-10.

## Currently installed and active (11 plugins)

| Slug | Version | Category | Notes |
|---|---|---|---|
| `breakdance/plugin` | 2.6.0 | Page builder | Core builder, every page is built here |
| `assets4breakdance/plugin` | 1.5.3 | Breakdance addon | Contains the `eval()` PHP Code Block element (kill switch tested then rolled back) |
| `advanced-custom-fields-pro/acf` | 6.8.0.1 | Custom fields | Used by Breakdance for product CPT |
| `surerank/surerank` | 1.6.6 | **SEO** | Free version, generates titles/meta/sitemap/JSON-LD |
| `surerank-pro/surerank-pro` | 1.5.0 | **SEO Pro** | Adds Google Search Console integration, advanced schemas |
| `litespeed-cache/litespeed-cache` | 7.8.1 | Cache & perf | Page cache, UCSS, image WebP, IPSec, QUIC.cloud |
| `cimo-image-optimizer/cimo` | 1.3.1 | Image optimizer | WebP conversion (98.5% of media is already WebP) |
| `catfolders-pro/catfolders` | 2.5.4 | Media organizer | Folders in the media library |
| `iks-menu-pro/iks-menu` | 1.12.7 | Menu | Custom menu manager |
| `all-in-one-wp-migration/all-in-one-wp-migration` | 7.105 | Backup / migration | Used to export the original `.wpress` we extracted |
| `leo-front-locale/leo-front-locale` | 1.0.0 | **Custom (us)** | Forces front locale en_US, injects Product/FAQ/Review schemas, design system, WhatsApp widget, sticky header, output buffer Phase 3 sections, REST endpoints for postmeta and posts list, IndexNow key file, video preload=none filter, /product-category/automatic-pet-fountain/ 301 redirect |

No Yoast, no RankMath, no AIOSEO, no SEOPress. The active SEO stack is **SureRank + SureRank Business**, which we decided to keep at the start of the project (avoiding RankMath would have created plugin conflicts on the meta tags).

## What the new conversion stack prompt asks vs what we already have

| Conversion stack item | Already done? | How |
|---|---|---|
| SEO plugin | ✅ Done | SureRank + SureRank Pro, fully configured. 7 page titles + 7 meta desc rewritten, Product/FAQ/Review schemas live |
| Contact form | ✅ Done (native Breakdance) | Breakdance form on /contact-us/ with Name, Email, WhatsApp, Country, Company, Message |
| WhatsApp floating button | ✅ Done | `#leo-whatsapp` custom widget injected by `leo-front-locale` plugin in Phase 2. Bottom-right, hover tooltip, opens wa.me/8619956530913 |
| Live chat (Tidio) | ❌ Not installed | Adds 200+ KB of JS, may slow down CWV. Recommendation: skip unless Leo wants real human chat |
| HubSpot CRM | ❌ Not installed | Heavy JS, English-only admin UI. Recommendation: skip until lead volume justifies it. Use email forwarding to ryanlau@eviehometech.com instead |
| Popup Maker | ❌ Not installed | Conversion-killer if overdone. Recommendation: install only if we want exit-intent + catalog download popup (1 popup, not 3) |
| Site Kit Google | ❌ Not installed | GSC is already connected via SureRank Pro integration. Site Kit would duplicate that. **Worth installing** for in-WP analytics dashboard if Leo wants metrics in his admin |
| Complianz (GDPR) | ❌ Not installed | **Legally required** for EU traffic. **Must install.** |
| Redirection plugin | ❌ Not installed | We have one redirect hard-coded in `leo-front-locale`. A real redirection plugin lets Leo manage 301s from the admin and logs 404s. **Worth installing** |
| LiteSpeed Cache | ✅ Done | Already installed and tuned (we triggered 50+ purges this session) |
| WPForms | ⚠️ Maybe | The Breakdance form on /contact-us/ already does the job. Installing WPForms would add a second form solution and create UX inconsistency. **Skip unless** Leo wants advanced features (multi-step forms, conditional logic, payment integration) |

## Recommended installs (ranked)

### Must install (legal + tracking)

1. **Complianz GDPR** — legally required for EU visitors. Without it, eviehometech.com is exposed to up to 4% global revenue fines under EU GDPR enforcement.

### Should install (improves operational quality)

2. **Redirection** — manage 301s from the admin and log 404s for SEO debugging. Lightweight, no impact on CWV.
3. **Google Site Kit** — adds Analytics 4 + Search Console + PageSpeed dashboards inside the WP admin. Heavier on the JS side but useful for Leo to see his own metrics.

### Should consider (conversion-driven, optional)

4. **Popup Maker** — only if we want one well-targeted popup (exit intent → catalog download lead magnet). Avoid the 3-popup spam approach.

### Skip for now (too heavy or duplicates existing)

5. **WPForms Lite** — Breakdance form is already in place and working. Adding WPForms creates two systems for the same job.
6. **Tidio Live Chat** — adds 200+ KB of JS, requires staffing the live chat in real time. WhatsApp widget already serves the same purpose at zero cost.
7. **HubSpot CRM** — overkill for current lead volume. Once Leo has 50+ inbound inquiries per month, we revisit.

## Custom code already shipped in `leo-front-locale`

For the record, our custom plugin already implements many features that the new stack prompt would otherwise install separately:

- Locale forcing (`en_US` on front, `zh_CN` admin) — single largest SEO win
- 7 page titles + 7 meta descriptions via SureRank REST
- Product schema on 35 product pages
- FAQPage schema on /faqs/
- Review + AggregateRating schema (12 reviews) on /reviews/ and /buyer-reviews/
- 5 design tokens via CSS variables (Inter font, palette, radius, shadow)
- Floating WhatsApp widget
- Sticky header on scroll
- Card hover lift
- Output buffer injection of 5 Phase 3 sections on the homepage (trust badges, OEM timeline, testimonials, blog preview, CTA strip)
- 301 redirect for the broken footer category link
- IndexNow key file at `/eviehome-indexnow-7f3a2b8c9d1e4f5a.txt`
- Video `preload="none"` filter for Core Web Vitals
- REST endpoints `/leo/v1/postmeta` and `/leo/v1/posts` for Breakdance tree editing
- Custom CPT archive title and meta description (for `/products/`)

This means a lot of what the conversion stack prompt asks for is already done. The plan below adds only what brings new value.
