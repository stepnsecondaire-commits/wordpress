---
subject: [Leo Project] UI redesign Phase 0-1-2 complete + Phase 3 path forward
to: stepnsecondaire@gmail.com, eyvenbest@163.com
---

# UI Redesign progress on eviehometech.com

Hi Leo,

The first part of the UI/UX redesign is now live on the site. Here is what was done, what was not, and what we need to discuss before going further.

## Phase 0 - Backup (done)

Two git branches created:

- `backup/pre-ui-redesign` - the full state of the project just before the redesign work started. If anything goes wrong we can rollback in one command.
- `feature/ui-redesign` - the active working branch where every Phase 1 and Phase 2 commit lives. The main branch is untouched until you validate the redesign work.

We also took a complete JSON snapshot of every WordPress page, product, post, menu and Breakdance template (header, footer, page archives, etc.) into `audit/backups/pre-ui-redesign/` in the repo. This includes the full Breakdance tree data for the home page (209 KB), about us, contact us, faqs, reviews, header (43) and footer (41). Before any Breakdance edit, the original tree is saved on disk so we can restore it byte-for-byte if a write produces a regression.

That backup discipline saved us once already this afternoon: the first attempt at writing back into Breakdance postmeta corrupted the home page render because of a WordPress slash-handling subtlety in the REST endpoint we wrote. We restored from backup, fixed the slash handling, and re-applied the fixes cleanly.

## Phase 1 - Critical fixes (5/5 done)

All 5 critical fixes from your prompt are live and verified.

### C1: contact form copy

The Contact Us page used to say "If you are interested in OEM batteries or wholesale batteries, please leave a message here and we will send you a quote as soon as possible." (placeholder copy from another business). It now says "Interested in OEM/ODM smart pet products or wholesale orders? Leave us a message and we will send you a detailed quote within 24 hours." Edited directly in the Breakdance tree of post id 27.

### C2: footer lorem ipsum

The newsletter section of the footer used to display "Sagittis scelerisque nulla cursus in enim consectetur quam. Dictum urna sed consectetur neque tristique pellentesque." (raw lorem ipsum left in the original template). It now says "Stay updated with our latest smart pet products, trade show appearances, and industry insights. Join 2000+ pet industry professionals." Edited directly in the Breakdance tree of the global footer template (post id 41).

### C3: Request a Quote button in the header

The "Request a Quote" button in the global header used to have `href="#"` so clicking it did nothing. It now points to `/contact-us/`. Edited directly in the Breakdance tree of the global header template (post id 43), in the `EssentialElements\Button` element id 114.

### C4: empty homepage counters

The homepage statistics counters were misconfigured: some had `start = end` (so the count-up was a no-op), one had `start > end` which broke the animation entirely, and one had a non-formatted "$50000000" value. They now show:

- 8+ Years of Experience
- 30+ Export Countries
- 500+ Serving Customers
- 200+ Products
- $50M+ Annual Sales Revenue
- 100+ Technical Personnel

The animation runs cleanly on scroll.

### C5: typo "Bark Collar& GPS Track"

The product category was named "Bark Collar& GPS Track" (missing space, not the proper "&" rendering). We fixed it in three places:

- The taxonomy term itself, via the WP REST API: now "Bark Collar & GPS Tracker"
- The `<h3>` heading in the homepage category section (Breakdance tree edit on post id 31)
- The product menu link in the global footer (Breakdance tree edit on post id 41)

## Phase 2 - Design system (done)

We injected a global design system through the `leo-front-locale` plugin. It loads on every front-end page after the Breakdance CSS so it can override style by specificity without touching the Breakdance settings UI. Specifically:

### CSS variables (in `:root`)

A coherent palette with primary `#0f172a`, accent `#16a34a`, CTA `#f97316`, neutral grays and a small set of semantic colors. Available everywhere as `--leo-primary`, `--leo-accent`, `--leo-cta`, etc. Border radius and shadow scale are also defined as variables.

### Inter and DM Sans typography

Loaded via Google Fonts with `preconnect` for performance. Applied to body, headings and Breakdance heading classes.

### Button styling

CTA buttons (the orange Request a Quote primary action) are now bold orange with a soft shadow, consistent rounding and a hover state that lifts the button.

### Card hover lift

Product cards and section cards now lift up on hover with a soft shadow and a 4px translateY. Subtle but visible.

### Sticky header on scroll

When the user scrolls past 40 pixels, the header gets `body.leo-scrolled` and we apply a backdrop blur and a soft shadow. The visual effect mirrors what every modern B2B site does today.

### Floating WhatsApp widget

Bottom-right corner of every page, 56x56 px green circle with the official WhatsApp icon. Hover reveals a "Chat with us" tooltip. Click opens `wa.me/8619956530913` with a pre-filled message. Mobile-optimized.

### Mobile responsive scaling

H1 and H2 sizes are reduced on mobile (under 768 px). Section padding scales down to 48 px on mobile to keep the page tight on small screens.

## Phase 5 - /catalog/ lead magnet page (done)

A new page is live at <https://eviehometech.com/catalog/> with a full description of the catalog content (37 models across 8 categories, certifications, OEM options, customization, lead times) and a clear instruction to email Ryan Lau or use the contact form to request the PDF. SEO title and meta description set via SureRank. This is a lead capture page: every catalog request becomes a qualified B2B inquiry.

You will need to actually generate the PDF (1 to 4 pages per product, 40-80 pages total) and store it somewhere accessible (Dropbox, Google Drive, or directly on Hostinger). Once the PDF exists, we can upgrade the catalog page from "email us to receive it" to "fill the form, get the link automatically", which is the typical lead magnet pattern.

## Phase 3 - Homepage structural redesign (decision required)

This is where we need to talk before pushing further. Your prompt asks for a complete restructure of the homepage with:

- A new hero section, full-screen, with a product photo and overlay
- A trust badges row
- A 4-column category grid with hover effects
- A 4-column "Why Choose Us" with line icons
- A redesigned counters section with a colored background
- A 6-step OEM process timeline
- A featured product section with gallery
- A testimonials carousel
- A blog preview
- A final CTA strip

Each of these is a major architectural change to the existing Breakdance tree. There are three realistic ways to ship them:

### Option A: Edit the Breakdance tree directly via the JSON API we built

Doable but high-risk. Each of those sections is a deep nested structure in Breakdance with section, columns, divs, headings, buttons, image objects, breakpoint settings for desktop / tablet / mobile and design tokens. Building one of these sections from scratch in JSON is roughly 2 000 to 5 000 lines of properties to set, and a single typo breaks the render (we already saw this happen today). Doable for someone with deep Breakdance internals knowledge plus access to a Breakdance test instance to compare against. Not the right tool for this stage of the project.

### Option B: Build the new sections as raw HTML / CSS in our `leo-front-locale` plugin and inject them via a `the_content` filter on the homepage

We control everything from PHP, no Breakdance internals to wrestle with, and the result renders inside the Breakdance Zero theme like our 5 new Phase 2 pages already do. Limitation: Ryan cannot edit the new sections from the WordPress admin Breakdance UI - they live in the plugin code. He would need to ask us to update them.

### Option C: Open Breakdance in a real browser and rebuild the homepage visually

Best result for a redesign of this scope, because Breakdance is a visual builder and this is the workflow it was designed for. The downside is that automating the visual builder from a script is essentially impossible (it is a complex single-page React app inside the WordPress admin). Either you do it yourself in front of the screen, or you give us VNC / screen-share access for an hour and we walk through it together.

### Our recommendation

Phase 3 is best done as a hybrid:

1. **Today, via Option B**: we ship the trust badges row, the floating WhatsApp widget (already live), the sticky header (already live), the OEM process timeline as a styled section, the testimonials carousel, the blog preview, and the final CTA strip. Each of these is a self-contained block that we inject below the existing Breakdance content via a `the_content` filter on the homepage. They live in the plugin, are versioned in git, and they immediately improve the homepage without touching the existing Breakdance tree.

2. **Later, via Option C**: when you have an hour for a screen-share session, we walk through Breakdance together to restructure the existing hero, the category grid and the "Why Choose Us" section. These are the sections that benefit most from a real visual builder workflow because they involve image cropping, hover states, custom layouts and pixel-perfect positioning.

3. **Or fully via Option B if you prefer**: we ship every Phase 3 item in our plugin without touching the Breakdance tree. The result is visually consistent but the homepage becomes a hybrid of Breakdance content and plugin-injected content, and Ryan loses some of the in-admin editability you initially required.

## What we need from you to start Phase 3

1. Confirmation on the option (A, B, C, or hybrid).
2. If Option C: a 60-minute window to do a screen-share or to give us temporary remote desktop access to your Mac with the Breakdance editor open.
3. If Option B: validation of the section list we will inject (trust badges, OEM timeline, testimonials carousel, blog preview, CTA strip). We can ship them in a couple of hours.

## What is still pending across all phases

- Video compression: still waiting on your call between local re-encoding and YouTube/Vimeo migration.
- Bing Webmaster Tools: you asked us to skip.
- OpenAI key and Google OAuth refresh token rotation: rotation instructions sent in the Phase 5 assets email.
- GitHub Actions workflow file for the blog auto-publication cron: needs to be uploaded manually because the current personal access token does not have the `workflow` scope.
- Real customer testimonials text: we have 5 verified Alibaba reviews used in the Review schema, but Ryan can probably export more from his Alibaba supplier profile to grow the social proof on /buyer-reviews/.

All Phase 0-1-2 work is committed on the `feature/ui-redesign` branch of the GitHub repo, separately from the `main` branch which still holds the pre-redesign state. When you validate the redesign, we merge `feature/ui-redesign` into `main` in one PR.

Have a good day,

The Leo Project team
