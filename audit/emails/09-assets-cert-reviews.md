---
subject: [Leo Project] 41 client assets uploaded, modern certifications page, 12 reviews live, plugin inventory done
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# Major progress on eviehometech.com

Hi Leo,

A lot happened on the site over the last hour. This email recaps everything that is now live, fixes a UI bug you reported, and gives you our recommendation on the new conversion plugin stack you sent in the last prompt.

## 1. Hotfix: green announcement bar height (fixed)

You reported the green top bar at the top of the homepage was around 10 cm tall instead of being a thin announcement strip. The cause was a global CSS rule we shipped in Phase 2 (`bde-section { padding: 80px }`) that hit every Breakdance section, including the announcement bar. We removed that rule. The bar is back to its original Breakdance size. Hard refresh your browser if you still see the cached version (Cmd+Shift+R on Mac).

## 2. Featured images: 4 missing pillar article images filled in

The 7 pillar blog articles we published this morning had a problem: Unsplash rate-limited us after the first 3 images, so 4 articles were live without a featured image. We fetched fresh Unsplash images for each, uploaded them to the WordPress media library, and attached them as `featured_media` on each post. **All 9 blog posts now have a featured image** (visible in /news/ archive and in the homepage blog preview section).

## 3. The 41 assets you sent: all uploaded with SEO-optimized metadata

The folder `/Users/lestoilettesdeminette/Desktop/doc site leo/` was processed in full:

### 13 images uploaded
- 2 customer visit photos (real B2B buyers at the Hefei factory)
- 4 trade show booth photos (Eviehome at Chinese pet trade shows)
- 7 verified Alibaba review screenshots

Every image got an English filename, an English alt text targeting B2B keywords, and an English caption + title. The old Chinese filenames (which Google could not parse) are gone.

### 24 certification PDFs uploaded
- 17 PDFs for the smart pet water fountain product line (CE, FCC, ROHS, REACH Annex 17 phthalates, REACH Annex 17 lead and cadmium, EN IEC 60335 safety, BS EN UKCA, IP68 dust and water resistance)
- 7 PDFs for the LP-018 cordless dust mite vacuum (CE EMC, FCC SDOC, IEC temperature rise, ROHS)

Every PDF got an English filename like `eviehome-pet-fountain-ce-emc-certificate.pdf` and a descriptive English alt text + title. They are all directly viewable and downloadable in the browser without any login or form.

### 4 factory videos uploaded
- 1 product demo (55 MB)
- 3 factory tour videos (160 MB, 168 MB, 214 MB)

We were not sure whether Hostinger would accept 200 MB+ video uploads via the WordPress REST API, but it did. All 4 videos are now in the media library with English filenames like `eviehome-factory-tour-production-line-1.mp4`. Our Phase 3 plugin filter automatically applies `preload="none"` to every `<video>` tag, so even though these are big files they do not download until the user clicks play.

A manifest of all 41 uploaded assets with their WordPress media IDs and URLs is committed to the repo at `audit/uploaded-assets.json`.

## 4. New verified reviews: 7 to 12 reviews, 5 to 8 countries

You sent 7 new Alibaba review screenshots. We extracted 5 net new reviews from them (the other 2 were duplicates of reviews we already had). The total is now **12 verified Alibaba reviews from 8 countries**: Italy, South Korea, Pakistan, Slovenia, United Arab Emirates, Australia, India, and Singapore (with 5 reviews from one repeat buyer in Singapore who has placed multiple orders).

The new reviewers are:
- Saqib Farooq, Pakistan, smart pet water dispenser
- Vladimir Fliser, Slovenia, ultra-safe automatic cat litter box
- Shahid Mahmood, United Arab Emirates, M1 large 100L cat litter box
- Singapore repeat buyer, 2 additional product orders

The Review schema in the `leo-front-locale` plugin was updated to publish all 12 reviews as JSON-LD on /reviews/ and /buyer-reviews/. AggregateRating now reads `5.0 / 5 with reviewCount: 12`. We verified the schema validates with the Google Rich Results test format.

## 5. /buyer-reviews/ page rebuilt with the modern design

The page <https://eviehometech.com/buyer-reviews/> is now a full B2B trust page:

- Hero block with a dark gradient, your 5.0/5 rating, the count of 12 verified reviews, and the 2 real customer visit photos side by side
- 12 review cards in a grid, each with reviewer initials, country, product name, the verbatim quote, and a "Verified Alibaba purchase" badge
- Each review card uses inline schema.org Review microdata so Google can read each individual review
- A green CTA bar between the reviews and the trade show photos linking to /contact-us/
- A trade show photo grid (4 photos) with descriptive captions
- Full SEO content explaining why Alibaba Trade Assurance reviews are trustworthy for B2B buyers
- A reference call CTA at the bottom for buyers who want to speak directly with a current Eviehome distributor before placing their first order

## 6. /certifications-quality/ page rebuilt with all PDFs accessible

The page <https://eviehometech.com/certifications-quality/> is now a full compliance reference page:

- Dark gradient hero block with the headline "Compliance-First Smart Pet Products Manufacturer" and 4 stat counters (24+ certificates, 8 markets, 8 patents, 2014 founding year)
- 17 certificate cards for the pet water fountain line, organized in a 3-column responsive grid
- 7 certificate cards for the LP-018 cordless dust mite vacuum
- Every card has a colored region badge (EU green, US blue, UK purple, Global orange), the certificate name, the issuing test report ID and date, a description of what the certificate proves, and **two buttons: "View" (opens the PDF in a new tab) and "Download" (saves the PDF)**
- A summary section listing the certifications across our other product categories
- A 3-step quality control process explanation (IQC, IPQC, OQC)
- A green CTA banner at the bottom linking to /contact-us/ for buyers who need certificates on a specific model not listed
- Modern card hover effects, gradient headers, mobile responsive

You said "PDFs must be accessible". Every PDF is now linked from a public page, has an English filename, has alt text and title, and can be downloaded in one click without any form or login. **24 PDFs are now indexable by Google**, which is a huge SEO signal because PDFs with technical content rank well on long-tail B2B queries.

## 7. Plugin inventory done — recommended next steps on the new conversion stack

You sent a separate prompt asking us to install a stack of 10 conversion plugins. Following your "audit before installing" rule, we listed every plugin currently installed via the WordPress REST API and matched it against the prompt. The full inventory is committed at `PLUGINS-INVENTORY.md` in the repo. Summary:

### Already done
- **SEO plugin**: SureRank + SureRank Business already installed and configured (we deliberately decided not to install RankMath at the start of the project to avoid plugin conflicts). All your title tag examples are already live on the 7 key pages.
- **Contact form**: native Breakdance form already in place on /contact-us/ with Name, Email, WhatsApp, Country, Company, Message. The "OEM batteries" copy was already replaced by the correct B2B copy (Phase 1 fix C1).
- **WhatsApp button**: floating green WhatsApp widget at the bottom-right of every page, already shipped in our `leo-front-locale` plugin (Phase 2). Hover tooltip "Chat with us", opens wa.me/8619956530913 with a pre-filled message.
- **LiteSpeed Cache**: already installed and tuned.
- **Many features that the prompt would install via separate plugins** are already implemented in our custom `leo-front-locale` plugin: locale forcing, Product/FAQ/Review schemas, design system, sticky header, video preload=none, IndexNow ping, 301 redirects, postmeta REST endpoints.

### Recommendation on the 7 remaining plugins from the prompt

| Plugin | Action | Reason |
|---|---|---|
| **Complianz GDPR** | **MUST install** | Legally required for any EU visitor under EU GDPR. Without it, eviehometech.com is exposed to up to 4% global revenue fines |
| **Redirection** | **Should install** | Lets Leo manage 301 redirects from the admin and logs 404s for SEO debugging. Lightweight, no CWV impact |
| **Google Site Kit** | **Should install** | Adds Analytics 4 + Search Console reporting inside the WP admin. Useful for Leo to see his own metrics. Heavier JS but acceptable |
| **Popup Maker** | **Optional** | Only if we want a single well-targeted popup (exit intent → catalog download lead magnet). Avoid the 3-popup spam approach. We can implement just the exit-intent one |
| **WPForms Lite** | **Skip** | The Breakdance form is already on /contact-us/ and works. Adding WPForms creates two systems for the same job |
| **Tidio Live Chat** | **Skip** | Adds 200+ KB of JS and requires real-time staffing. WhatsApp widget already serves the same purpose at zero cost and zero load impact |
| **HubSpot CRM** | **Skip for now** | Overkill for the current lead volume. Email forwarding to ryanlau@eviehometech.com from the Breakdance form is enough until 50+ inbound inquiries per month justify a real CRM |

We are about to install the "MUST" and "Should" items (Complianz, Redirection, Site Kit). Each one will be a separate commit so we can rollback in isolation if anything breaks the site.

## What is next

1. Install the 3 recommended plugins (Complianz, Redirection, Site Kit) in the next 30 minutes
2. Configure each one (consent banner, 404 logging, GA4 + Search Console linking)
3. Embed the 4 factory videos on /about-us/, /oem-odm-services/ and the homepage Phase 3 sections
4. Send a final recap email when the conversion stack is in place

The current state of the project is committed on the `feature/ui-redesign` branch in [stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress). Latest commit: `00b3c8b`. The `main` branch is still untouched until you validate the redesign.

Have a good day,

The Leo Project team
