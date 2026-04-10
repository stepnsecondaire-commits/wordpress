---
subject: [Leo Project] Enhanced Organization schema + breadcrumbs + home clients section + IndexNow batch
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Enhanced SEO signals live on eviehometech.com

Hi Leo,

Short but high-impact update. Five new SEO and UX enhancements are now live on the site, all focused on the "reference supplier" positioning you asked for.

## 1. Enhanced Organization schema (every page)

The `leo-front-locale` plugin now injects a much richer Organization schema on every page of the site. Before, the only Organization entity was the minimal one SureRank generates automatically. Now every page includes an additional JSON-LD block with:

- **Company name + 2 aliases** (Hefei Ecologie Vie Home Technology, Eviehome, Ecologie Vie Home Technology)
- **Full factory address**: Mingmen Industrial Park, No. 7235 Ziyun Road, Economic and Technological Development Zone, Hefei, Anhui, China 230601
- **Founding year**: 2014
- **Employees range**: 51 to 200
- **2 contact points**: Ryan Lau (sales, ryanlau@eviehometech.com, +86 17333173263, English + Chinese) and a secondary WhatsApp (+86 19956530913)
- **19 countries served**: US, CA, GB, DE, FR, IT, ES, NL, BE, PL, PT, AU, NZ, JP, KR, SG, IN, AE, BR
- **15 topical knowsAbout entries** covering every product category and every certification
- **8 credentials** (CE, FCC, ROHS, REACH, ISO 9001, PSE, IP68, UKCA) each tagged as `EducationalOccupationalCredential`
- **Award**: "8 international design patents for smart pet products"
- **sameAs**: Instagram, Facebook, WhatsApp
- **makesOffer**: OEM and ODM manufacturing with MOQ from 500 units

### Why this matters for SEO and GEO

This is the single richest Organization schema on the site and feeds two critical machines:

1. **Google Knowledge Graph**: the Hefei Ecologie Vie entity is now discoverable with its full profile. When someone searches "Eviehome" or "Hefei Ecologie Vie" on Google, the knowledge panel on the right side of the results has all the data it needs to show a full business card.

2. **LLM entity graphs**: when someone asks ChatGPT, Claude, Perplexity or Gemini "who makes smart pet products in China", the LLM parses pages for structured Organization data. The richer the schema, the more likely the LLM cites the business by name. This is the GEO (Generative Engine Optimization) lever you asked for.

## 2. Visible breadcrumbs on every non-home page

The `BreadcrumbList` schema was already emitted by SureRank, but there was no visible rendering on the page. Now the plugin injects a light grey breadcrumb bar right under the main content start on every page that is not the homepage. Example on `/products/`:

> Home › Products

Single product pages show `Home › Products › Product name`. Blog posts show `Home › Blog › Article title`. The breadcrumbs are styled in the same accent green as the rest of the site and match the BreadcrumbList schema exactly.

Benefit: better UX (visitors always know where they are in the navigation) + richer Google SERP snippets (breadcrumbs appear under the title in search results) + improved internal link graph.

## 3. "Our Buyers Come to Hefei" section on the homepage

A new section on the homepage, right after the "Live Demo" video section and before the "Trust Badges" row. It shows the 2 real customer visit photos you sent, with overlay captions:

- Photo 1: "B2B importers meeting our team — International buyers at the Eviehome showroom in Hefei"
- Photo 2: "Hands-on sample inspection — Customer reviewing our smart cat litter box range in person"

An orange "Schedule your factory visit" CTA button at the bottom links to `/contact-us/`.

### Why this matters

Customer visit photos are the strongest visual trust signal in B2B sourcing. Unlike stock images or rendered product shots, these photos cannot be faked cheaply and they trigger the correct mental model in the visitor: "other serious B2B buyers have come here, this is a real factory, I should come too". This is exactly the conversion mechanic that turns a visitor into a qualified lead.

## 4. IndexNow batch ping to Bing and Yandex

We pinged 16 URLs via IndexNow to tell Bing and Yandex to re-index the pages that were updated or created:

- 6 Phase 2 pages (/oem-odm-services/, /certifications-quality/, /shipping-logistics/, /why-source-from-china/, /buyer-reviews/, /catalog/)
- 7 pillar blog articles (complete-guide-sourcing..., automatic-cat-litter-box..., smart-pet-feeders..., rise-smart-pet-products..., oem-vs-odm..., pet-product-certifications..., global-smart-pet-products-market-report...)
- Homepage
- About Us
- Contact Us

Response: `HTTP 202 Accepted` from the IndexNow API. Bing and Yandex should re-crawl these URLs within 24 to 48 hours. Google does not support IndexNow yet but will re-crawl on its own schedule via the sitemap.

## 5. Contact page: Google Map already embedded (no change needed)

We checked the live contact page and confirmed that a Google Maps iframe for the Hefei factory address is already embedded via a Breakdance block. Nothing to do there.

## 6. Full audit document: LIVE-AUDIT-RESULTS.md

A comprehensive audit report covering all 20 pages, all 5 Phase 1 fixes, all SEO signals, all new content sections, all uploaded assets and the full plugin list is committed at `LIVE-AUDIT-RESULTS.md` on the `feature/ui-redesign` branch.

Every check was performed via `curl` against the production site (not the local repo). Every value quoted in the report is a live verification.

## What is still blocked on your side

The same three things:
1. **Complianz wizard** (2 min) at <https://eviehometech.com/wp-admin/admin.php?page=cmplz-wizard>
2. **Site Kit OAuth** (30 sec) at <https://eviehometech.com/wp-admin/admin.php?page=googlesitekit-splash>
3. **Catalog PDF** generation by Leo

Once those three are done, the site is in its fully optimized state and we can focus on:
- Publishing the 93 blog satellite articles (needs ANTHROPIC_API_KEY in GitHub Actions)
- Validating the redesign visually and merging `feature/ui-redesign` into `main`
- Posting the Phase 5 assets (LinkedIn, GBP, Alibaba) from the earlier email

Latest commit: will be pushed right after this email.

Have a good day,

The Leo Project team
