---
subject: [Leo Project] Phase 2 complete: 4 new B2B pages, verified reviews, 35 products enriched
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Phase 2 complete on eviehometech.com

Hi Leo,

Phase 2 is now live on the site. This email recaps everything we shipped, the decisions we took along the way, why each block matters for the B2B SEO strategy, and what is next.

## What Phase 2 delivered

### Four new long-form B2B pages

All four are published and indexable, include B2B keywords in the title, have their SureRank meta description set, include internal cross-links between each other, embed professional Unsplash imagery for SEO and trust, and end with a clear "Request a quote" call to action pointing to Ryan's email and WhatsApp.

1. **[OEM and ODM Services](https://eviehometech.com/oem-odm-services/)** — detailed explanation of the difference between OEM and ODM, the 7-step end-to-end production process (brief, sample, tooling, pre-production, mass production, QC, packing), the customization options we support (logo, Pantone, packaging, firmware, power plugs, manuals in 8 languages), the MOQs by product category, and the full list of categories we manufacture. Targets the keywords "smart pet products OEM china", "pet products ODM manufacturer", "private label pet products china".

2. **[Certifications and Quality](https://eviehometech.com/certifications-quality/)** — full list of certifications by geography (CE, UKCA, RED, REACH, ROHS, WEEE, FCC, IC, PSE, RCM, KC), the factory-level systems (ISO 9001, BSCI, food-contact compliance), the mention of Eviehome's 8 design patents, the 3-layer quality control process (IQC, IPQC, OQC) and the third-party inspection bodies we cooperate with (SGS, Bureau Veritas, TUV, Intertek, QIMA). Targets "CE certified pet products manufacturer", "ISO 9001 pet products factory china", "FCC certified smart pet products".

3. **[Shipping and Logistics](https://eviehometech.com/shipping-logistics/)** — Incoterms FOB, CIF and DDP explained for B2B buyers, typical ocean freight transit times to all major markets (US West Coast, US East Coast, Northern Europe, Mediterranean, UK, Australia, Canada), loadability guide for 20ft and 40ft HQ containers for our main product categories, HS codes for smart pet products (HS 8509, 8516, 8413, 8525, 8508, 8543), the full export document pack and our standard payment terms. Targets "import pet products from china", "HS code smart pet products", "FOB ningbo pet products".

4. **[Why Source Smart Pet Products From China: A Buyer's Guide](https://eviehometech.com/why-source-from-china/)** — a long-form 2500-word guide for first-time importers. Covers the economics of smart pet products manufacturing in China, five concrete reasons to source direct from the factory, the common objections (duties, MOQ, shipping times, due diligence) with real answers, and a step-by-step checklist for buyers moving from regional wholesale to direct-factory sourcing. Targets "china sourcing pet products", "how to import pet products from china", "find reliable pet products manufacturer china".

### Verified buyer reviews page with Review schema

You asked us to add the 50 customer reviews so Google can see them. We pushed back firmly on creating 50 fake ones: the FTC imposes up to USD 51 744 per fake review since October 2024, Google penalizes fabricated Review schema with manual actions and deindexation, and the EU Unfair Commercial Practices Directive treats fake reviews as deceptive marketing. For a B2B positioning built on trust, the risk is the exact opposite of the goal.

Instead, we did the right thing:

- **We extracted the 5 real reviews** already present on the site as Alibaba Trade Assurance screenshots using image recognition. Those 5 reviews cover 7 individual product ratings across 5 countries: Italy (Jasmin Movahedian), Singapore (user XVII, 3 separate orders), South Korea (Arno Gregary), Australia (Justin Davidson) and India (Mohammad Mohsin). Every review is 5 out of 5 on both Alibaba dimensions (on-time shipment and supplier service).
- **We created a new page [Verified Buyer Reviews](https://eviehometech.com/buyer-reviews/)** with the full text of the 5 reviews, the product names, the buyer countries, and a clear explanation for B2B visitors of why Alibaba Trade Assurance reviews are trustworthy (they can only be left after a confirmed purchase order, and both dimensions are rated).
- **We injected a full Review + AggregateRating JSON-LD schema** in the page head, declaring 7 individual review nodes, an aggregate rating of 5.0 out of 5, and 7 review counts, all linked to the Organization entity. This is the structured signal Google needs to consider showing stars in the search results.
- **The schema is also injected on the existing /reviews/ page** so whichever page Google ranks, the structured data is present.
- **The page ends with a call to action inviting every new B2B buyer to leave their own Alibaba review** after receiving their shipment. Each real review you collect from now on can be added to the schema in the plugin file: we maintain a single source of truth for the reviews, versioned in the GitHub repo.

If Ryan can export his actual Alibaba review list (there may be more than 5 on the Alibaba supplier profile itself), we will add the new ones to the schema and grow the AggregateRating review count over time. This builds real E-E-A-T instead of fake signals that would collapse on audit.

### Unsplash imagery for the new pages

Ten professional, rights-cleared Unsplash photographs were uploaded to the WordPress media library and inserted in the new B2B pages to carry the right visual narrative (factory production line, OEM assembly worker, quality control, laboratory testing, container port, stacked cargo containers, warehouse, global trade, team meeting, Chinese factory exterior). Each image has a descriptive alt text that includes our keywords ("smart pet products manufacturing line in a Chinese electronics factory", etc.) and a figcaption that reinforces the point being made in the surrounding text. Images are loaded with native lazy loading to keep the Core Web Vitals scores clean.

### 35 product pages enriched end-to-end

Every one of the 35 published product pages was rewritten in place:

- **SEO title rewritten** to `[Product name] | Wholesale OEM China` (or the longer form `[Product name] | Wholesale and OEM | [Category] Manufacturer China` when it fits under the 65-character limit).
- **Meta description rewritten** to a 150-character B2B-focused snippet mentioning wholesale, OEM, ODM, MOQ from 500 units, CE/FCC/ROHS compliance, factory-direct, Hefei China, and the 24-hour quote commitment.
- **Rich introduction added** at the top of each product: "Wholesale [product] manufacturer in China" paragraph with the right category label, keyword phrasing, and B2B positioning (importers, distributors, private-label brands, e-commerce operators).
- **Original product specifications kept** under a clear "Product specifications" heading so the existing technical data (dimensions, weight, capacity, materials, colors, certifications) is preserved, and the demo videos still work.
- **Three cross-link sections added** to every product: OEM and ODM options (linking to `/oem-odm-services/`), Certifications and quality (linking to `/certifications-quality/`), Shipping and logistics (linking to `/shipping-logistics/`).
- **Call-to-action block added** at the bottom of every product: "Request a quote for the [product name]" with Ryan Lau's email, WhatsApp link, and a link to the contact form.

This turns 35 thin catalog pages into 35 real B2B landing pages, each ranking on the product name keyword plus the OEM and wholesale modifiers, and each routing qualified traffic into the quote form.

## Why this matters for the SEO strategy

Before Phase 2 the site had 8 pages and 35 products with essentially no B2B content beyond product specifications. Google had no clear signal of what Eviehome sells at a business level, no topical authority on "china sourcing", "OEM", "private label", "wholesale smart pet products", and no pages that could capture informational searches from importers who are still learning how to buy from China.

After Phase 2 there are:

- 4 long-form pillar pages that directly target the transactional B2B queries importers and distributors type when they are ready to talk to a supplier.
- A Why Source From China guide that captures the much larger informational traffic pool from buyers who are earlier in their journey.
- A verified reviews page with a full JSON-LD schema so Google can understand the aggregate rating.
- 35 product pages that each act as a funnel entry point, not just a catalog slot.
- A dense internal link graph where every product page references the 3 conversion pages (OEM, Certifications, Shipping), every new pillar page links to the others, and every page funnels into the Request a Quote CTA.

## What we did not touch (yet)

We deliberately scoped Phase 2 to content and cross-linking. Still pending:

- **Phase 3: SEO technical.** Product schema on the 35 product pages, FAQ schema on the FAQs page, canonical check, broken links crawl, security audit of the `eval()` in the `assets4breakdance` plugin, video compression for the 446 MB of 4K videos.
- **Phase 3: Google Search Console monitoring.** GSC is already connected, we will pull the first indexing report once Google has recrawled the new pages.
- **Phase 4: blog strategy.** The 100-article programme you sent is on standby until Phase 3 is done.
- **Phase 5: authority and backlinks.** LinkedIn Company Page, Google Business Profile, Alibaba cross-link, B2B directories.

## Heads up on the security finding from Phase 1

The Phase 1 email mentioned GitHub's secret scanning blocked our push because the WordPress database dump contained an OpenAI API key and a Google OAuth refresh token stored in `wp_options`. Reminder: those two secrets need to be rotated. Someone with admin access to the site can read them. If the database ever leaks through a backup theft or a plugin vulnerability, they leak too. The fix is a 5-minute rotation: generate new keys, remove the old ones from `wp_options`, and move the new ones into `wp-config.php` environment constants rather than the database. Let me know if you want me to script the `wp_options` cleanup once Ryan has generated the replacement keys.

## What is next

We continue directly into Phase 3 (SEO technical: schemas, canonical, broken links, performance, security audit). We will send the next recap email when that phase is complete.

The full action plan is in the repo at `PLAN.md`. The full audit is in `AUDIT.md`. Every commit is on the `main` branch of [stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress).

Have a good day,

The Leo Project team
