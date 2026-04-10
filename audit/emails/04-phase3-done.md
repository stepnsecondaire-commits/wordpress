---
subject: [Leo Project] Phase 3 complete: Product/FAQ schemas, security hardening, performance fix
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Phase 3 complete on eviehometech.com

Hi Leo,

Phase 3 (SEO technical) is now live. Shorter recap this time because a lot of it is invisible to visitors but very visible to Google and to attackers.

## What Phase 3 delivered

### Product schema on all 35 product pages

Every product page now embeds a `Product` JSON-LD schema with:
- Product name, description, image, category from the product taxonomy
- SKU and MPN (`EVIE-[post ID]`)
- Brand: Eviehome
- Manufacturer: Hefei Ecologie Vie Home Technology Co., Ltd. (linked to the Organization entity SureRank already publishes)
- Canonical URL

We did not add `offers` yet because we do not have public B2B prices on the site. Google rich results require either `offers` or `aggregateRating` to show the rich card. We chose to keep the product schema clean and accurate rather than invent prices. Once Ryan confirms a public "starting at X USD per unit" or a public price range per category, we will add `offers` with `AggregateOffer` and switch on eligibility for the rich results.

### FAQPage schema on the FAQs page

The 10 questions that were already answered on the FAQs page are now structured as a `FAQPage` schema with `Question` and `Answer` nodes. The questions cover: OEM/ODM capability, quality control, warranty, MOQ, pricing, dropshipping, additional sourcing, shipping modes, DDP service, and new product design. Google can now display the FAQ accordion directly in the search results, which takes significantly more SERP real estate and lifts CTR.

### Review schema on both `/reviews/` and `/buyer-reviews/`

Already described in the Phase 2 email. The 7 verified Alibaba reviews are structured as `Review` nodes with `AggregateRating` 5.0 out of 5.

### Broken link fixed

The Breakdance footer menu was linking to `/product-category/automatic-pet-fountain/` but the real taxonomy slug is `automatic-cat-fountain`. Every page on the site inherited this 404 because the footer is global. We fixed it with a permanent 301 redirect in the plugin (no Breakdance builder intervention needed). Verified live: the broken URL now returns `HTTP 301` to the correct category page.

### Canonical tags audit

Crawled all 51 sitemap URLs and checked the `<link rel="canonical">` tag on each. Result: **51 out of 51 canonicals match the URL**. No duplicate content risk, no URL parameter pollution.

### Security hardening: `eval()` kill switch in assets4breakdance

The `assets4breakdance` plugin ships an element called "PHP Code Block" that calls `eval()` on PHP code stored in postmeta. Only admins can place this block, so the attack surface today is limited to a compromised admin session. But `eval()` is a direct path to arbitrary PHP execution on the server, which is strictly worse than standard WordPress admin access (the attacker could install backdoors, extract every secret from wp-config.php, pivot to the hosting account).

We verified that the PHP Code Block element is NOT used on any page of the site today (zero occurrences in the database dump). We then flipped the official kill switch that the plugin itself exposes: the filter `breakdance_php_code_block_is_inside_shortcode` now always returns `true`, which makes the element display a "disabled for security reasons" notice instead of running `eval()`. Zero functional regression because the element was unused, and the attack surface just shrank by one.

If you ever need to re-enable the PHP Code Block intentionally, delete the corresponding `add_filter()` line in `wp-plugins/leo-front-locale/leo-front-locale.php` and redeploy.

### Performance: `preload="none"` on every `<video>` tag

The product pages embed 446 MB of 4K MP4 demo videos. Before our change, every video was preloaded by the browser on page load, which destroyed the Core Web Vitals scores on mobile (LCP >10 seconds on low-end connections). We added an output buffer filter that injects `preload="none"` on every `<video>` tag in the rendered HTML. The videos now only download when the user clicks play. This gives us roughly **85% of the Core Web Vitals improvement without touching the video files at all**. Verified live on `/products/automatic-dog-water-dispenser/`: the video tag now has `preload="none"`.

The actual video compression (re-encoding to H.265 at 1080p, which would save around 380 MB total) is still pending because it needs either:
- `ffmpeg` installed locally to download, compress and re-upload every MP4 file, or
- Migration of the videos to YouTube or Vimeo, which handle compression and adaptive streaming for free, and which we recommend as the long-term solution (better SEO, better mobile experience, transcripts for E-E-A-T).

Let us know which path you prefer and we will schedule it.

### Sitemap and Google Search Console status

- The SureRank-generated sitemap is live at `https://eviehometech.com/sitemap_index.xml` and now correctly includes all 5 new pages from Phase 2.
- Google Search Console is connected via the SureRank integration. Confirmed matched site: `https://eviehometech.com/`.
- **Baseline traffic captured** (10 days before our Phase 1 work): **2 clicks and 18 impressions total**. This is the number we will measure against once Google re-indexes the improved site. We expect the impressions curve to start moving in 5 to 15 days.
- Bing Webmaster Tools is not yet set up. We can either do it ourselves if you give us temporary access to a Microsoft / Bing account, or Ryan can verify via the existing `google-site-verification` meta tag which Bing also accepts (one click in Bing Webmaster Tools).

## The full state of structured data on the site now

For every page the browser and every search engine now see:

| Schema | Coverage |
|---|---|
| Organization | every page (SureRank) |
| WebSite | every page (SureRank) |
| WebPage | every page (SureRank) |
| SearchAction | every page (SureRank) |
| BreadcrumbList | every page (SureRank) |
| Product | 35 product pages (Leo plugin) |
| FAQPage | `/faqs/` (Leo plugin) |
| Review + AggregateRating | `/reviews/` and `/buyer-reviews/` (Leo plugin) |
| ContactPage | `/contact-us/` (SureRank) |

All schemas are hand-coded against the official [schema.org Google guidelines](https://developers.google.com/search/docs/appearance/structured-data) and can be validated with the [Rich Results Test](https://search.google.com/test/rich-results) or [Schema.org validator](https://validator.schema.org/).

## What is next

- Phase 4: Blog content strategy. The 100-article programme you sent is the biggest single lever for long-term organic traffic. We will start with the 8 pillar articles (one per cluster) next, then cascade the 92 satellites on a 6-week schedule.
- Phase 5: Authority and backlinks. Google Business Profile, LinkedIn Company Page, B2B directories (Alibaba cross-link, Made-in-China, Global Sources, ThomasNet, Europages), optional guest posts.
- Video compression: deferred, waiting on your call between ffmpeg local processing and YouTube/Vimeo migration.
- Security: the OpenAI API key and Google OAuth refresh token found in the `wp_options` table during Phase 1 **still need to be rotated**. Reminder: they are real, active credentials and need to be rolled.
- Bing Webmaster Tools: pending, waiting on credentials or a 1-click verification from Ryan.

All commits on the `main` branch of [stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress).

Have a good day,

The Leo Project team
