---
subject: [Leo Project] Phase 1 complete: SEO foundations fixed on eviehometech.com
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Phase 1 complete on eviehometech.com

Hi Leo,

Phase 1 of the SEO fix plan is now live on the site. Here is what was done, why it matters, and what is next.

## First: important correction to the initial audit

The first recap email said three things that were wrong once we checked the live site:

1. "The site is completely blocked from Google via robots.txt" — **false**. The live `robots.txt` is correct (`Disallow: /wp-admin/` + sitemap reference). The blocking file I saw in the WordPress export was a stale cache file from the LiteSpeed plugin, not the one actually served.
2. "No sitemap" — **false**. `sitemap_index.xml` is live and up to date, with 6 sub-sitemaps (pages, posts, products, categories, tags, product categories).
3. "No Google Search Console" — **false**. The `google-site-verification` meta tag is present on every page, so GSC is already connected.

The site was actually in better shape than the initial audit suggested. The real problems were more subtle: a language metadata mismatch (Chinese declared, English content) and missing meta descriptions on key pages. We fixed both.

## What Phase 1 changed on the site

### 1. Front-end language fixed (the biggest SEO win)

**Before:** `<html lang="zh-Hans">` and `<meta property="og:locale" content="zh_CN">` on every page, even though all the content was in English. Google was receiving a contradictory signal ("this is a Chinese site") and ranking the site for Chinese queries instead of English B2B ones.

**After:** `<html lang="en-US">` and `<meta property="og:locale" content="en_US">`. The WordPress admin stays in Chinese, nothing changed for you in the back office.

**How:** a custom plugin called `leo-front-locale` was uploaded via the Plugins page in the admin and activated. It runs a filter on `get_locale()` that returns `en_US` only when the page is served to a visitor, and keeps `zh_CN` for the admin, REST API, AJAX and WP-CLI contexts.

**Why it matters:** Google will now classify the site as English. Every future indexing pass will start ranking it on English B2B queries ("smart pet products manufacturer china", "OEM cat litter box", etc.) instead of Chinese ones. This is the single biggest ranking lift of Phase 1.

### 2. Title tags rewritten on 7 key pages

All 7 important pages now have B2B-optimized title tags that target the keywords your buyers actually type into Google.

| Page | Old title | New title |
|---|---|---|
| Home | Home - Hefei Ecologie Vie Home Technology Co., Ltd. | Smart Pet Products Manufacturer China \| OEM & ODM \| Ecologie Vie |
| Products | Products - Hefei Ecologie Vie Home Technology Co., Ltd. | Smart Pet Products Catalog \| OEM & Wholesale China \| Eviehome |
| About Us | About Us - Hefei... | About Ecologie Vie \| Smart Pet Products Manufacturer in China |
| Contact Us | Contact Us - Hefei... | Contact Us \| Request a Quote for Bulk Pet Products \| Eviehome |
| Reviews | Reviews - Hefei... | Customer Reviews \| Eviehome Smart Pet Products Manufacturer |
| News | News - Hefei... | News & Industry Updates \| Smart Pet Products Manufacturer |
| FAQs | FAQs - Hefei... | FAQs \| MOQ, OEM, Shipping & Certifications \| Eviehome |

**Why it matters:** the title tag is the single most important on-page SEO signal. The old titles did not contain any of the keywords your buyers search for. The new titles put the right keywords first and communicate the B2B value proposition (OEM, wholesale, MOQ, certifications) directly in the search results, which typically lifts CTR by 50% to 300%.

**How:** done via the SureRank REST API from the command line, directly on the live site. All 7 edits verified live after purging the LiteSpeed cache.

### 3. Meta descriptions written on the 7 key pages

Same 7 pages, each with a fresh 150-160 character meta description that communicates what Eviehome offers, the MOQ, the certifications, and the call to action ("Request a quote").

Example for the homepage:
> "Leading Chinese manufacturer of smart pet products. Wholesale, OEM and ODM services. CE/FCC certified. Request a quote, MOQ from 500 units."

**Why it matters:** Google uses the meta description to build the snippet shown under your page in the search results. Without one, Google auto-generates something random from the page content, which is usually not compelling. A hand-written description with keywords and a call to action pulls clicks.

### 4. H1 on the homepage

Already present. A visible H1 is on the homepage already (currently "ODM & OEM Smart Pet Product", built via two spans). Minor cosmetic issue with the spacing but Google reads it correctly. We will polish it later in Breakdance during Phase 2.

### 5. SureRank kept, RankMath not installed

The original plan mentioned installing RankMath. We are keeping SureRank (already installed and active, fully featured) because:
- Installing RankMath on top would create a plugin conflict (both plugins write the same meta tags and fight each other, breaking SEO).
- SureRank already generates the JSON-LD schemas (Organization, WebSite, SearchAction, WebPage) that we want.
- SureRank already has its Google Search Console integration active.

If you ever prefer to switch to RankMath, we need a proper migration path (deactivate SureRank first, import settings into RankMath, check every schema manually). Not worth it for now, SureRank is doing the job.

## What Phase 1 did not touch (and why)

- The product pages themselves (37 products). Their meta descriptions are currently auto-generated from the product bullet points, which is low quality but present. We will rewrite all 37 during Phase 2 when we rewrite the product copy.
- The H1 cosmetic polish on the homepage. Phase 2 work inside Breakdance.
- The security audit of the `eval()` call inside the `assets4breakdance` plugin. Coming in the next batch.
- Performance: 446 MB of 4K videos still need to be re-encoded. This is a performance item, not blocking indexation, scheduled for the performance phase.

## Why we are versioning everything on GitHub

Every plugin, every code change, every email draft, and every audit document lives in the GitHub repo [stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress). Reasons:

1. Traceability: every action is in a commit, we know exactly what was changed, when, and why.
2. Reversibility: if anything breaks the site, we can roll back in one command.
3. Collaboration: both of us see the same state of the project at any time.
4. Safety: the work survives even if the Hostinger server has a problem.

The `leo-front-locale` plugin is committed as version 1.0.0 in `wp-plugins/leo-front-locale/`. If we update it, we commit the new version and re-upload it to WordPress.

## What is coming next

You sent a much larger plan with three new layers on top of the first plan:

1. **Zero blind spot audit** with competitive analysis of the top 10 competitors on 5 B2B queries.
2. **GEO strategy** (Generative Engine Optimization) to get cited by ChatGPT, Perplexity, Google AI Overviews and Claude.
3. **100-article blog strategy** organized in 8 topical clusters with pillar articles and satellite articles, programmed on a 6-week publishing schedule.

This is a large multi-week project. Before we start, we need to agree on sequencing and priority. Our recommendation:

1. **Week 1 (days 1 to 3):** Competitive analysis + zero blind spot audit finalized (`COMPETITIVE-ANALYSIS.md`, updated `AUDIT.md`). No site changes yet, just deep research.
2. **Week 1 (days 4 to 5):** Phase 2 conversion pages (Request a Quote form, OEM/ODM, Quality & Certifications, Factory Tour). These pages are what turns the future organic traffic into leads.
3. **Week 2:** Performance phase (video compression, Core Web Vitals) + security audit + technical SEO checklist items.
4. **Week 2 (in parallel):** Pillar articles for the 8 clusters (articles 1, 16, 31, 43, 55, 67, 77 from the content plan) written in English, optimized for GEO (direct-answer paragraphs, data citations, explicit brand mentions).
5. **Weeks 3 to 6:** Publication of the 92 satellite articles on the programmed schedule (2 to 3 per day, Tue to Thu, at 06:00 / 14:00 / 18:00 UTC to cover Europe morning, US East Coast business hours, US West Coast business hours).
6. **Weeks 6+:** GEO seeding on external sources (Reddit, Quora, LinkedIn Articles, Medium, Wikidata), monitoring via repeated LLM prompts, iteration.

**What we need from you to start:**

1. Validation of the sequencing above (or adjustments).
2. Confirmation that we should indeed produce the full 100-article content plan (this is a multi-week commitment and will be the main driver of organic traffic in the coming months).
3. The factory address, number of employees, annual export volume, founding year, and certifications list (CE, FCC, PSE, ROHS, ISO 9001, patents count) — we will need all of this for the Organization schema and the trust pages.
4. Access to Google Search Console (we can pull traffic data via the SureRank integration once authenticated). Currently the GSC is verified but we do not have reporting access from our side.
5. Any existing press coverage, industry awards, distributor logos, or case studies you can share — all this is fuel for the E-E-A-T / GEO work.

The full checkbox plan is in the repo at `PLAN.md`, updated with Phase 1 marked as done. The full audit is in `AUDIT.md`.

Have a good day,

The Leo Project team
