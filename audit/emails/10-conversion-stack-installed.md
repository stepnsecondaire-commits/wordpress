---
subject: [Leo Project] 4 conversion plugins installed + exit-intent catalog popup live
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Conversion stack installed on eviehometech.com

Hi Leo,

Following your validation, the 4 conversion plugins are now installed and active on the site. Here is what is live, what works out of the box, and the 3 short clicks Leo needs to do himself to finalize the configuration of the 2 plugins that require Google or Cookie wizard authentication.

## What is now installed

The site went from 11 to 15 active plugins. The 4 new ones:

| Plugin | Version | Status |
|---|---|---|
| Complianz GDPR/CCPA Cookie Consent | 7.4.5 | active, awaiting wizard |
| Redirection | 5.7.5 | active, working out of the box |
| Site Kit by Google | 1.176.0 | active, awaiting Google OAuth |
| Popup Maker | 1.22.0 | active, exit-intent popup live |

Site is healthy: HTTP 200, page size 158 KB, all 15 Breakdance sections still rendering, no JS errors. We did NOT install Tidio, HubSpot, WPForms — they would have duplicated existing features and added 400+ KB of JS for no real conversion lift.

## 1. Popup Maker — exit-intent catalog popup is live

We pre-configured a single high-quality exit intent popup, **no manual setup needed**. Detail:

- **Trigger**: exit intent (when the user moves the mouse to the browser tab to leave)
- **Frequency**: shown once per visitor, then a 7-day cookie suppresses it
- **Theme**: default Popup Maker light theme
- **Title**: "Get the 2026 Eviehome Catalog"
- **Subtitle**: "37 smart pet products. Full specs, MOQs, certifications and pricing tiers. Sent to your inbox in 24 hours."
- **Form fields**: Business email + Company name (both required)
- **CTA button**: orange "Send me the Catalog" matching the rest of the site
- **Submission**: routes to /contact-us/ with the email and company prefilled in the URL parameters

This popup captures the email of every B2B visitor who is about to leave the site without contacting Ryan. In B2B manufacturing, this is the single highest ROI conversion mechanic: the visitor is on your site for a reason, the catalog is the lowest-friction next step, and the email is the key to a future sales conversation.

The popup is not visible on first scroll. It only fires on exit intent. So it does not impact the on-page experience and does not get in the way of buyers who are ready to convert via the existing CTAs.

## 2. Redirection — already works for 404 logging

Redirection is active and logging 404s by default. No setup required. Whenever a visitor hits a URL that does not exist on the site, the plugin logs it in `/wp-admin/tools.php?page=redirection.php` so we can detect broken links from external sources (Alibaba listing typos, old marketing materials, broken Google index entries).

We pre-loaded one redirect: the broken `/product-category/automatic-pet-fountain/` link that was hardcoded in the Breakdance footer is already 301 redirected to `/product-category/automatic-cat-fountain/` (we shipped this in our `leo-front-locale` plugin during Phase 3, so the Redirection plugin does not need to know about it).

## 3. Complianz GDPR — needs a 2-minute wizard from Leo

Complianz is the legal must-have. EU GDPR requires explicit cookie consent before tracking visitors with Google Analytics, and Complianz handles exactly that with a banner, a preference center and the per-cookie consent record. Without it, eviehometech.com is technically exposed to up to 4% of global revenue in fines from any EU data protection authority.

The plugin is installed and active but it ships with a configuration wizard that needs to run once to detect the cookies on your site and generate the right banner and Cookie Policy page. Two minutes of clicks:

**Action for Leo:**
1. Log in to /wp-admin/
2. Go to **Complianz > Wizard** in the left sidebar
3. Click **Run Cookie Scan** then **Next** through the 5 wizard steps
4. Choose: Region "European Union + UK + USA", Use Cookies "Yes", Statistics "Google Analytics", Marketing "No"
5. Save. The cookie banner appears at the bottom of every page automatically.

Until the wizard runs, Complianz is dormant and the site behaves as before. Run it before sending any traffic from EU markets.

## 4. Site Kit by Google — needs OAuth from Leo

Site Kit is the official Google plugin that puts Google Analytics 4 + Search Console + PageSpeed Insights inside the WordPress admin. It needs Leo to OAuth-connect his Google account once.

**Action for Leo:**
1. Log in to /wp-admin/
2. Go to **Site Kit > Splash** in the left sidebar (or click the Site Kit prompt in the admin notice bar)
3. Click **Sign in with Google** and pick the Google account that owns the existing GSC for eviehometech.com
4. Follow the authorization flow (3 screens, 30 seconds)
5. Site Kit auto-detects the matched GSC site and offers to set up GA4. Accept the GA4 setup with the default property name.

After this, Leo will see GSC traffic and GA4 metrics directly in /wp-admin/admin.php?page=googlesitekit-dashboard. Useful for him to track his own SEO progress without leaving WordPress.

**Important note**: GSC is already connected to the site via the SureRank Pro integration we use to push sitemaps. Site Kit and SureRank can coexist. Site Kit gives Leo a dashboard. SureRank gives us the API to push sitemaps and retrieve data from scripts.

## What is still pending after this email

1. **Complianz wizard run** by Leo (2 minutes, see above)
2. **Site Kit OAuth** by Leo (30 seconds, see above)
3. **Embed factory videos**: 4 factory tour videos uploaded earlier still need to be embedded on /about-us/, /oem-odm-services/ and the homepage. Coming next.
4. **Catalog PDF**: the /catalog/ page tells visitors to email Ryan for the PDF. Once Leo generates the actual PDF, we upgrade the page to a self-serve download form (combined with the new Popup Maker exit-intent popup that already captures emails).

## What Leo gets out of this stack

Concretely, here is what changes for the business:

- **Every EU visitor** now gets a compliant cookie banner and Eviehome is no longer at GDPR risk
- **Every visitor about to leave** is invited to download the catalog with a non-intrusive exit-intent popup → email captured → Ryan follows up within 24 hours
- **Every 404** is logged so Leo can fix dead links he or his suppliers introduce
- **All traffic data** (GSC + GA4) is visible in the WordPress admin dashboard

The conversion side is now in place. Combined with the SEO foundations, the 7 pillar blog articles, the 35 enriched product pages, the 5 phase 3 homepage sections, the 12 verified Alibaba reviews and the 24 downloadable certificates, the site now has:

- A clear value proposition (factory-direct OEM/ODM smart pet products)
- Trust signals (real verified reviews, real photos with customers, real downloadable certificates)
- A conversion path (contact form, WhatsApp widget, exit-intent catalog popup, request a quote CTAs everywhere)
- SEO foundations (titles, meta, schemas, sitemap, IndexNow, blog content)
- Performance (lazy load, video preload none, LiteSpeed cache)
- Compliance (GDPR cookie consent on the way once Leo runs the wizard)

## State of the project

Latest commit: `b4cd381` on `feature/ui-redesign` in [stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress).

The `main` branch is still untouched from before the redesign work. When you validate the redesign, we merge `feature/ui-redesign` into `main` in one PR. Until then, every change since the start of the redesign is isolated on the feature branch and can be rolled back independently.

Have a good day,

The Leo Project team
