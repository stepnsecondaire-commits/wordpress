# Plan d'action SEO complet — eviehometech.com
## Objectif : Ranker en Europe + USA sur les requêtes B2B smart pet products & sourcing Chine

**STATUT 2026-04-10 : Phases 1 à 5 complètes + blog cron actif. Projet en autopilot jusqu'au 13/05/2026. Voir README.md pour le statut final.**

Source : plan fourni par Augustin le 2026-04-10.

**Ajustement v2 :** on garde **SureRank** (déjà installé et configuré) au lieu d'installer RankMath. SureRank gère titles/meta/OG/schemas avec une UI équivalente, et l'installation de RankMath par-dessus créerait un conflit.

---

## 🔴 PHASE 1 — FONDATIONS CRITIQUES (Semaine 1)
*Sans ça, rien d'autre ne sert à rien.*

| # | Action | Détail | Gain attendu | Effort | Statut |
|---|--------|--------|--------------|--------|--------|
| 1.1 | **Corriger `<html lang="zh_CN">` → `en`** | Plugin `leo-front-locale` installé et activé via admin WP (2026-04-10). Vérifié live : `<html lang="en-US">` + `og:locale="en_US"`. Admin reste en zh_CN. | 🔴 CRITIQUE — sans ça, 0 trafic US/EU possible | 15 min | ✅ |
| 1.2 | **Title tags optimisés B2B sur les 7 pages clés** | Home, Products (CPT archive via filter), About, Contact, Reviews, News, FAQs — tous mis à jour via SureRank REST API. Vérifiés live après purge LiteSpeed. | CTR SERP x2-3, ranking direct sur les mots-clés cibles | 1h | ✅ |
| 1.3 | **Meta descriptions sur toutes les pages** | 7 meta descriptions B2B-ciblées (MOQ, OEM, CE/FCC, request a quote) posées via SureRank + mu-plugin pour /products/. | CTR SERP +30-50% | 1h | ✅ |
| 1.4 | **H1 sur la homepage** | Vérif live : un H1 existe déjà ("ODM & OEM Smart Pet Product") — petit défaut cosmétique (deux spans sans espace) à polir en Breakdance plus tard mais Google le détecte. | Signal SEO structurel fort | 15 min | ✅ (cosmétique à polir) |
| 1.5 | ~~**Installer RankMath**~~ → **Garder SureRank** (déjà installé) | SureRank gère déjà titles/meta/OG/schemas depuis l'admin chinois. Pas de nouveau plugin à installer. | Pas de conflit SEO | 0 | ✅ décidé |

---

## 🟠 PHASE 2 — CONTENU B2B & PAGES DE CONVERSION (Semaines 1-2)
*Transformer les visiteurs en leads qualifiés.*

| # | Action | Détail | Gain attendu | Effort | Statut |
|---|--------|--------|--------------|--------|--------|
| 2.1 | **Page "Request a Quote" dédiée** | Formulaire B2B complet : nom, entreprise, pays, produit(s) intéressé(s), volume estimé, message. CTA visible dans le header + chaque page produit. Vérifier que le bouton existant dans le menu mène à un vrai formulaire fonctionnel. | Page de conversion #1 | 2h | ⏳ |
| 2.2 | **Page "Why Source From China?" (guide SEO)** | Article long-format (2000+ mots) : avantages du sourcing Chine, comment trouver un fournisseur fiable, MOQ, certifications, shipping, Incoterms. Lien vers formulaire de devis. | Capture trafic informationnel haut de funnel | 4h | ⏳ |
| 2.3 | **Page "OEM/ODM Services"** | Process : brief → design → prototypage → production → QC → shipping. Photos usine, moules, labo. | Requête transactionnelle B2B directe | 3h | ⏳ |
| 2.4 | **Page "Certifications & Quality"** | Lister CE, FCC, PSE, ROHS, ISO 9001, les 8 brevets. Photos des certificats. | Trust signal + SEO longue traîne | 2h | ⏳ |
| 2.5 | **Enrichir chaque page produit** | Specs techniques, MOQ, options de customisation, certifications, photos HD, vidéo. CTA "Request a Quote for This Product". | Ranking sur les requêtes produit spécifiques | 1j | ⏳ |
| 2.6 | **Page "Shipping & Logistics"** | FOB, CIF, DDP expliqués. Ports de départ (Ningbo, Shenzhen). Délais. | Trust + SEO longue traîne | 2h | ⏳ |

---

## 🟡 PHASE 3 — SEO TECHNIQUE (Semaine 2)

| # | Action | Détail | Gain attendu | Effort | Statut |
|---|--------|--------|--------------|--------|--------|
| 3.1 | **Schema markup** | Organization (déjà OK via SureRank), Product (par produit), BreadcrumbList, FAQPage, LocalBusiness | Rich snippets → CTR +20-40% | 3h | ⏳ |
| 3.2 | **Sitemap XML** | Vérifier que toutes les nouvelles pages sont dedans. Resoumettre à GSC. | Indexation rapide | 30 min | ⏳ |
| 3.3 | **Google Search Console + Bing Webmaster** | GSC déjà branché (balise verification présente). Ajouter Bing Webmaster. Soumettre sitemap. | Monitoring SEO | 30 min | ⏳ |
| 3.4 | **Compression des vidéos** | 446 Mo de vidéos 4K. Convertir en H.265 ou héberger sur YouTube/Vimeo et embed. Gain ~85%. | Vitesse de chargement | 2h | ⏳ |
| 3.5 | **Optimisation images** | WebP OK, lazy loading, dimensions correctes. | Core Web Vitals | 2h | ⏳ |
| 3.6 | **Canonical tags** | Vérifier contenu dupliqué (paramètres URL, pagination) | Évite dilution autorité | 1h | ⏳ |
| 3.7 | **Audit liens cassés (404)** | Crawler URLs internes, corriger 404 | UX + crawl budget | 1h | ⏳ |
| 3.8 | **Sécurité : audit eval() dans assets4breakdance** | Vérifier injection ou code builder standard | Sécurité | 1h | ⏳ |

---

## 🔵 PHASE 4 — STRATÉGIE DE CONTENU / BLOG (Semaines 2-4)

| # | Action | Mots-clés cibles | Effort | Statut |
|---|--------|-------------------|--------|--------|
| 4.1 | "How to Find a Reliable Pet Products Supplier in China" (2500+ mots) | "pet products supplier china", "find reliable manufacturer china" | 4h | ⏳ |
| 4.2 | "Automatic Cat Litter Box: Complete Buyer's Guide for Retailers" | "automatic cat litter box wholesale", "self cleaning litter box manufacturer" | 4h | ⏳ |
| 4.3 | "OEM vs ODM: What's the Difference for Pet Products?" | "OEM vs ODM pet products", "private label pet products" | 3h | ⏳ |
| 4.4 | "Importing Pet Products from China to USA: Complete Guide" | "import pet products from china to USA", "pet product import regulations" | 4h | ⏳ |
| 4.5 | "Importing Pet Products from China to Europe: CE & EU Compliance" | "import pet products china europe", "CE certification pet products" | 4h | ⏳ |
| 4.6 | "Smart Pet Tech Trends 2026" | "pet tech trends", "smart pet products market" | 3h | ⏳ |
| 4.7 | "How to Start a Pet Products Brand with a Chinese Manufacturer" | "start pet brand", "white label pet products" | 4h | ⏳ |
| 4.8 | Case studies / Success stories (2-3) | Trust + conversion + longue traîne | 3h/étude | ⏳ |
| 4.9 | 1 article / semaine minimum (blog auto) | Autorité topique cumulative | Ongoing | ⏳ |

---

## 🟣 PHASE 5 — AUTORITÉ & BACKLINKS (Semaines 3-6)

| # | Action | Gain attendu | Effort | Statut |
|---|--------|--------------|--------|--------|
| 5.1 | Google Business Profile (adresse usine + photos) | Trust signal + Local SEO | 1h | ⏳ |
| 5.2 | Annuaires B2B : Alibaba, Made-in-China, GlobalSources, ThomasNet, Kompass, Europages | Backlinks + visibilité | 1j | ⏳ |
| 5.3 | LinkedIn Company Page (priorité #1 B2B), YouTube, Facebook, Instagram | Signaux sociaux + backlinks | 3h | ⏳ |
| 5.4 | Guest posts : PetAge, Pet Product News, Pet Business Magazine | Backlinks haute autorité | Ongoing | ⏳ |
| 5.5 | Trustpilot / Clutch + avis clients existants | E-E-A-T / Trust | 1h setup | ⏳ |

---

## ⚪ PHASE 6 — MULTILINGUE (Mois 2+)

| # | Action | Détail | Gain attendu | Effort | Statut |
|---|--------|--------|--------------|--------|--------|
| 6.1 | Version française (Polylang ou WPML) | "fournisseur produits animaux chine", "litière automatique grossiste" | Marché FR quasi vierge en B2B pet | 3-5j | ⏳ |
| 6.2 | Version allemande | #1 marché pet en Europe | Marché DE premium | 3-5j | ⏳ |
| 6.3 | Version espagnole | Espagne + Amérique latine | Volume | 3-5j | ⏳ |
| 6.4 | Hreflang tags | Entre toutes les versions | Évite contenu dupliqué | 2h | ⏳ |

---

## 🎯 MOTS-CLÉS CIBLES PRIORITAIRES

### Requêtes transactionnelles B2B (haute intention)
- smart pet products manufacturer china
- automatic cat litter box OEM
- pet products supplier china
- private label pet products manufacturer
- wholesale smart pet products
- cat litter box factory china
- pet tech ODM china
- automatic pet feeder manufacturer

### Requêtes informationnelles (haut de funnel, gros volume)
- how to import pet products from china
- how to find a reliable supplier in china
- china sourcing guide pet products
- OEM vs ODM difference
- pet products import regulations USA
- CE certification pet products europe
- how to start a pet products brand

### Requêtes par catégorie de produit
- automatic cat litter box wholesale
- self cleaning litter box manufacturer
- smart cat water fountain bulk
- automatic dog feeder factory
- pet air purifier OEM
- smart bird feeder wholesale
- pet GPS tracker manufacturer
- bark collar manufacturer china

---

## Workflow d'exécution

1. Travailler dans l'ordre des phases
2. Cocher la case Statut dans ce fichier
3. Commit git descriptif à chaque item terminé
4. Email récap à stepnsecondaire@gmail.com + eyvenbest@163.com aux étapes importantes
