# Audit SEO — eviehometech.com

**Date :** 2026-04-10
**Cible :** Hefei Ecologie Vie Home Technology Co., Ltd. (fabricant chinois de produits smart pet)
**Objectif business :** maximiser le référencement organique Google EU/US pour capter des **acheteurs B2B récurrents** (importateurs, distributeurs, marques, e-commerçants)
**Admin WordPress :** reste en chinois (non négociable)
**Front :** doit devenir multilingue EN en priorité (+ FR/DE/ES en phase 2)

---

## 1. Résumé exécutif

Le site est **techniquement bien outillé** (stack moderne : WordPress 6.9.4 + Breakdance + ACF Pro + SureRank SEO + LiteSpeed Cache) mais **totalement invisible pour Google** à cause d'un blocage robots.txt, et **structurellement inadapté** à une stratégie B2B EU/US parce que tout est en chinois sans plugin multilingue.

**Score SEO actuel estimé : 15/100.** Le potentiel est fort (bon foncier, bons outils) mais il faut débloquer 3 verrous critiques avant toute stratégie de contenu.

### Top 5 des problèmes critiques

1. 🔴 **robots.txt bloque toute indexation** (`Disallow: /`) → site invisible de Google/Bing
2. 🔴 **Site 100% en chinois, zéro plugin multilingue** → aucun marché EU/US adressable
3. 🔴 **~78% des pages sans meta description** → CTR SERP en berne
4. 🟠 **446 Mo de vidéos 4K non compressées** → Core Web Vitals catastrophiques
5. 🟠 **Aucun formulaire de contact / devis** détecté → friction conversion B2B

### Potentiel estimé

Avec les corrections critiques + une stratégie de contenu B2B (landing pages par catégorie × marché, blog sourcing/OEM), le site peut réalistiquement viser :
- **Mois 1-3 :** passer de 0 à ~100 sessions organiques/mois (déblocage indexation + meta)
- **Mois 3-6 :** 500-1 500 sessions/mois (version EN + 15-20 articles de fond)
- **Mois 6-12 :** 3 000-8 000 sessions/mois (autorité thématique + hreflang EU/US)
- **Conversion B2B cible :** 0,5-1,5% → 15-120 leads qualifiés/mois à 12 mois

---

## 2. Contexte business identifié

### Identité
- **Nom légal :** Hefei Ecologie Vie Home Technology Co., Ltd.
- **Baseline :** "A globally leading wholesale and OEM manufacturer of smart pet products."
- **Admin unique :** `eyvenbest163-com` (eyvenbest@163.com), rôle Administrator
- **Téléphone :** +86 17333173263
- **Réseaux :** Instagram, Facebook, WhatsApp (dans schema Organization SureRank)
- **Hébergement :** Hostinger (mu-plugins `hostinger-auto-updates` + `hostinger-preview-domain`)

### Produits et services — 37 produits répartis en 8 catégories
| Catégorie | Nb produits |
|---|---|
| Automatic Cat Litter Box | 14 |
| Automatic Cat Fountain | 7 |
| Bird Feeder | 6 |
| Vacuum Cleaner | 4 |
| Pet Air Purifier | 4 |
| Automatic Dog Feeder | 3 |
| Pet Smart Toys | 2 |
| Bark Collar & GPS Track | 1 |

**Positionnement :** wholesale + OEM/ODM pour importateurs B2B. Mention de certifications visible dans les noms de fichiers uploadés (LCSE, DSS, LVD, CE) — atout à mettre en avant.

### Pages publiées (8)
Home · Products · About Us · Contact Us · Reviews · News · Privacy Policy · Terms and Conditions · FAQs

### Marchés cibles actuels vs visés
- **Actuel :** front entièrement en chinois, aucun ciblage géo, aucun hreflang
- **Visé :** acheteurs B2B anglophones (priorité EN-US + EN-GB), puis DE, FR, ES

---

## 3. Audit technique détaillé

### 3.1 Stack
| Composant | Version | État |
|---|---|---|
| WordPress core | 6.9.4 | ✅ à jour |
| Thème actif | `breakdance-zero-theme-master` v1.0.0 | ✅ minimaliste sain |
| Page builder | Breakdance v2.6.0 | ✅ à jour |
| Custom fields | ACF Pro v6.8.0.1 | ✅ licence active |
| Plugin SEO | SureRank + SureRank Business v1.6.6 / v1.5.0 | ⚠️ sous-configuré |
| Cache | LiteSpeed Cache v7.8.1 | ✅ bien configuré |
| Image optimizer | Cimo Image Optimizer | ✅ actif (WebP ok) |
| Menu manager | IKS Menu Pro v1.12.7 | ✅ |
| Gestionnaire médias | CatFolders Pro v2.5.4 | ✅ |
| **Multilingue** | **AUCUN** | 🔴 **bloquant** |
| **WooCommerce** | **AUCUN** | ℹ️ CPT custom à la place |

### 3.2 Thème — excellent foncier
- `themes/breakdance-zero-theme-master/` = 4 fichiers PHP, ~40 Ko
- **Zéro texte hardcodé** dans le PHP → **tout le contenu front est éditable depuis l'admin** ✅
- Tout le rendu passe par Breakdance (builder visuel)
- Pas de thème enfant — à créer si on customise

### 3.3 SEO on-page
- Meta titles : ✅ présents (SureRank les gère)
- Meta descriptions : 🔴 manquantes sur ~78% des pages
- Schemas JSON-LD : ✅ WebSite, Organization, SearchAction, WebPage/AboutPage configurés
- Open Graph : ✅ présents
- Canonical : ✅ présents
- Hreflang : 🔴 absent (pas de multilingue)
- Breadcrumbs : ⚠️ schéma configuré mais non rendu visuellement
- H1/H2 : ⚠️ structure hiérarchique faible (SureRank signale des pages sans H2)
- robots.txt : 🔴 **`Disallow: /` — blocage total** (`litespeed/robots.txt`)
- Sitemap : ⚠️ à vérifier une fois robots.txt corrigé

### 3.4 Performance & médias
- **Total uploads :** 508 Mo pour 1 640 fichiers
- **Images :** 98,5% en WebP, 41 AVIF, moyenne 31 Ko — ✅ excellent
- **Vidéos :** 42 fichiers, 446,8 Mo en 4K (H.264), tailles 8-29 Mo — 🔴 catastrophique pour CWV
- **LiteSpeed Cache** actif avec UCSS, CCSS, LQIP, image optimization

### 3.5 Sécurité
- ⚠️ `plugins/assets4breakdance/elements/Supa_Code_Block/ssr.php` contient `eval()` — à auditer (restreindre qui peut publier des blocs de code)
- ✅ Pas de backdoor détecté par ailleurs
- ✅ Permissions fichiers normales
- ✅ Tous les plugins à jour
- ✅ Un seul admin (réduit la surface d'attaque)

### 3.6 Poids repo / backup
- **Total extraction :** 905 Mo
  - `uploads/` : 508 Mo → à exclure du git (mettre sur stockage externe)
  - `wpvividbackups/` : 243 Mo → à supprimer du repo (backups obsolètes)
  - `plugins/` : 137 Mo → question licence (Breakdance, ACF Pro, SureRank Pro achetés)
  - `litespeed/` : 5 Mo (cache)
  - `languages/` : 4,3 Mo (zh_CN uniquement)
  - `themes/` : 40 Ko
  - `mu-plugins/` : 20 Ko
  - `database.sql` : 7,2 Mo

---

## 4. Problèmes critiques (à corriger immédiatement)

- [ ] **C1.** Corriger `robots.txt` — remplacer `Disallow: /` par `Disallow: /wp-admin/\nAllow: /wp-admin/admin-ajax.php` + référencer le sitemap. **Impact : débloque l'indexation totale. Effort : 2 minutes.**
- [ ] **C2.** Installer un plugin multilingue (**Polylang Pro recommandé**, léger, SEO-friendly, compatible ACF/Breakdance) et créer la version **EN** complète du site. **Impact : ouvre les marchés EU/US. Effort : 3-5 jours (install + traduction des 8 pages + menus + produits).**
- [ ] **C3.** Remplir les **meta descriptions manquantes** pour toutes les pages et produits via SureRank (8 pages + 37 produits = 45 entrées). **Impact : +20-40% CTR SERP. Effort : 1 journée.**
- [ ] **C4.** Ajouter un **formulaire de devis/contact B2B** (WPForms Lite ou Fluent Forms, tous deux gratuits et légers) sur `/contact` avec champs qualifiants (entreprise, pays, volume annuel, produits d'intérêt). **Impact : capture des leads. Effort : 2h.**
- [ ] **C5.** Auditer l'usage du `eval()` dans `assets4breakdance/Supa_Code_Block` — vérifier qui peut publier ce bloc, ajouter des garde-fous capability check. **Impact : sécurité. Effort : 1h.**

## 5. Améliorations importantes (impact fort)

### SEO technique
- [ ] **I1.** Publier un **sitemap.xml** propre (SureRank le génère, vérifier qu'il est actif) et le soumettre à Google Search Console + Bing Webmaster Tools
- [ ] **I2.** Configurer **Google Search Console** et **Bing Webmaster Tools** (vérification DNS ou HTML meta)
- [ ] **I3.** Ajouter **Google Analytics 4** ou **Plausible** (plus RGPD-friendly) pour tracking
- [ ] **I4.** Configurer **hreflang** dès que le multilingue est en place (`en-US`, `en-GB`, `zh-CN`)
- [ ] **I5.** Implémenter l'affichage visuel des **breadcrumbs** (le schéma est déjà là, il manque le rendu HTML) via un bloc Breakdance sur le template produit
- [ ] **I6.** Ajouter le **schema `Product`** sur les fiches produits (nom, marque, description, image, catégorie, manufacturer) pour éligibilité rich results Google Shopping organic
- [ ] **I7.** Ajouter le **schema `FAQPage`** sur la page FAQs
- [ ] **I8.** Ajouter le **schema `BreadcrumbList`** sur les templates produits/catégories

### Performance / Core Web Vitals
- [ ] **P1.** Réencoder les **42 vidéos 4K en H.265 + downscale 1080p + CRF 28** → gain estimé **-380 Mo (-85%)**. Commande : `ffmpeg -i input.mp4 -c:v hevc -preset fast -crf 28 -vf scale=1920:-2 output.mp4`
- [ ] **P2.** Ajouter un **poster image WebP** pour chaque vidéo et activer `preload="none"`
- [ ] **P3.** Limiter les **tailles de thumbnails générées** par WordPress (actuellement 4+ par image) — configurer dans Media settings + plugin Regenerate Thumbnails
- [ ] **P4.** Activer **lazy-loading natif** (`loading="lazy"`) sur toutes les images sous la ligne de flottaison
- [ ] **P5.** Vérifier et **minifier le CSS Breakdance** via LiteSpeed UCSS (déjà activé, vérifier la config)
- [ ] **P6.** Activer la **compression Brotli** côté serveur (LiteSpeed la gère)
- [ ] **P7.** Passer sur un **CDN** (Cloudflare gratuit suffit) — critique pour livrer rapidement à des acheteurs US/EU

### Contenu existant
- [ ] **C6.** Réécrire les **titres produits** en anglais optimisé SEO ("Automatic Self-Cleaning Cat Litter Box for Multi-Cat Homes" > "Cat Litter Box 001")
- [ ] **C7.** Ajouter une **description longue** (400-800 mots) sur chaque fiche produit : specs, use cases B2B, MOQ, certifications, délais, compatibilité OEM/ODM
- [ ] **C8.** Ajouter des **balises alt** optimisées en anglais sur toutes les images produits
- [ ] **C9.** Structurer les pages avec **H1 unique + H2/H3 hiérarchisés**
- [ ] **C10.** Ajouter sur chaque fiche produit un **bloc de CTAs B2B** ("Request a quote", "Download datasheet", "Become a distributor")

### Conversion & trust B2B
- [ ] **T1.** Créer une page dédiée **"OEM/ODM Capabilities"** avec process, délais, MOQ, certifications, études de cas
- [ ] **T2.** Créer une page **"Quality & Certifications"** (CE, RoHS, FCC, ISO 9001 si applicable)
- [ ] **T3.** Créer une page **"Factory Tour"** avec photos/vidéos de la chaîne de production (fort signal trust pour importateurs)
- [ ] **T4.** Ajouter des **études de cas clients** (témoignages de distributeurs existants, volumes, marchés)
- [ ] **T5.** Ajouter un **chat WhatsApp Business** visible (le numéro est déjà dans le schema Organization)

---

## 6. Améliorations secondaires (nice to have)

- [ ] **S1.** Ajouter un thème enfant pour sécuriser les customisations futures
- [ ] **S2.** Supprimer les 243 Mo de backups WPvivid obsolètes du serveur (les externaliser sur S3/Backblaze)
- [ ] **S3.** Configurer un **backup automatique externe** (UpdraftPlus vers Google Drive ou S3)
- [ ] **S4.** Mettre en place **2FA** sur le compte admin
- [ ] **S5.** Renommer l'admin par défaut (user unique actuel)
- [ ] **S6.** Ajouter un **favicon** si absent
- [ ] **S7.** Configurer les **webhooks SureRank → Slack/email** pour alertes SEO
- [ ] **S8.** Ajouter un **fil d'actualités RSS** propre pour la page News

---

## 7. Stratégie de contenu recommandée

### 7.1 Architecture cible (après multilingue EN)

```
eviehometech.com/en/                              → home
├── /en/products/                                 → catalogue
│   ├── /en/products/automatic-cat-litter-box/   → cat litter boxes (cluster 14 produits)
│   ├── /en/products/automatic-pet-feeder/       → feeders (cluster 3 + catégorie parent)
│   ├── /en/products/pet-vacuum-cleaner/         → vacuums (4)
│   ├── /en/products/pet-air-purifier/           → air purifiers (4)
│   ├── /en/products/automatic-cat-fountain/     → fountains (7)
│   ├── /en/products/bird-feeder/                → bird feeders (6)
│   ├── /en/products/pet-smart-toys/             → smart toys (2)
│   └── /en/products/bark-collar-gps-tracker/    → collar & GPS (1)
├── /en/oem-odm/                                  → capabilities page (NOUVELLE)
├── /en/quality-certifications/                   → certs (NOUVELLE)
├── /en/factory/                                  → factory tour (NOUVELLE)
├── /en/case-studies/                             → cases distributeurs (NOUVELLE)
├── /en/about/                                    → about us
├── /en/contact/                                  → contact + formulaire B2B
├── /en/blog/                                     → blog SEO (NOUVEAU)
└── /en/faq/                                      → FAQs
```

### 7.2 Clusters de mots-clés cibles

#### Cluster "sourcing / manufacturer" (intent = recherche de fournisseur, **priorité 1**)
- `smart pet products manufacturer china` (KD~30, vol~200/mo)
- `china pet supplies manufacturer`
- `pet products oem manufacturer china`
- `wholesale smart pet products supplier`
- `automatic pet feeder manufacturer china`
- `cat litter box factory china`
- `china pet electronics manufacturer`
- → **Landing page :** `/en/oem-odm/` + articles blog ciblés

#### Cluster "catégorie produit + buyer intent B2B"
- `wholesale automatic cat litter box`
- `bulk pet feeder supplier`
- `private label pet air purifier`
- `oem bird feeder china`
- `smart pet vacuum wholesale`
- → **Landing pages :** une par catégorie produit

#### Cluster "import / logistics" (intent = acheteur qui veut importer)
- `how to import pet products from china`
- `china pet products trade terms`
- `pet supplies import regulations EU`
- `pet products HS code`
- `MOQ smart pet products china`
- → **Articles blog** (acquisition + autorité)

#### Cluster "comparatif / éducation"
- `best smart cat litter box 2026`
- `automatic vs manual pet feeder`
- `pet air purifier reviews`
- → **Articles blog** (trafic large top of funnel)

### 7.3 Plan éditorial blog (4 mois)

| Mois | Articles | Angle |
|---|---|---|
| **M1** | 4 articles fondamentaux | Guides import (HS codes, MOQ, incoterms, certifications EU/US) |
| **M2** | 4 articles catégories | "Ultimate guide to wholesale [category] from china" × 4 catégories top |
| **M3** | 4 articles produits phares | Études techniques sur les best-sellers + comparatifs |
| **M4** | 4 articles trust | Factory tour, case studies distributeurs, certifications, témoignages |

**Fréquence cible stable post-M4 :** 2-4 articles/mois via blog auto (même archi que les autres blogs du user : publish.py + GitHub Actions + Claude Sonnet 4 + Shopify/WP API).

### 7.4 Landing pages marché (phase 3)

Une fois le contenu EN stabilisé, créer des landings marché :
- `/en/markets/usa/` — focus incoterms US, FDA si applicable, exemples distributeurs US
- `/en/markets/europe/` — CE, RoHS, REACH, WEEE, VAT
- `/en/markets/uk/` — post-Brexit UKCA marking
- `/de/` — version allemande complète (second marché EU après UK)

---

## 8. Stratégie multilingue recommandée

**Plugin retenu : Polylang Pro** (vs WPML/Weglot/TranslatePress)
- ✅ léger, bon pour perf (vs WPML lourd)
- ✅ compatible Breakdance et ACF (confirmé doc Polylang)
- ✅ hreflang automatique
- ✅ URL propres `/en/` `/fr/` `/de/`
- ✅ pas de SaaS externe (vs Weglot, qui coûte + dépendance)
- ⚠️ il faut traduire soi-même (pas de traduction auto native, mais c'est mieux pour la qualité B2B)

**Alternative envisageable :** TranslatePress Pro si le client veut une UX de traduction WYSIWYG (plus friendly pour lui côté admin chinois).

**Phasage :**
1. Install Polylang + config langues (zh_CN par défaut, en_US secondaire)
2. Traduction manuelle des 8 pages + 37 produits (outil : DeepL Pro + relecture humaine B2B)
3. Traduction menus + widgets
4. Config hreflang + sitemap multilingue
5. Validation Google Search Console

---

## 9. Plan d'implémentation priorisé

### Phase 1 — Déblocage (semaine 1) — objectif : rendre le site indexable
1. ✅ Audit complet (fait)
2. **C1** robots.txt (2 min)
3. **C5** audit eval() assets4breakdance (1h)
4. **I1+I2** sitemap + Search Console (1h)
5. **C4** formulaire contact B2B (2h)
6. **C3** meta descriptions (1 journée)
7. Premier mail récap envoyé

### Phase 2 — Multilingue EN (semaines 2-3)
1. **C2** install Polylang Pro + config
2. Traduction des 8 pages en EN (outil DeepL + relecture)
3. Traduction des 37 produits en EN
4. Traduction menus + widgets
5. **I4** hreflang
6. Soumission sitemap multilingue Search Console

### Phase 3 — Performance (semaine 4)
1. **P1** réencodage des 42 vidéos en H.265 1080p
2. **P7** mise en place Cloudflare CDN
3. **P3/P4/P5** tuning thumbnails + lazy + UCSS
4. Vérification Core Web Vitals (PageSpeed Insights, CrUX)

### Phase 4 — Contenu B2B (semaines 5-8)
1. **T1** page OEM/ODM
2. **T2** page Quality & Certifications
3. **T3** page Factory Tour
4. **C6-C10** réécriture produits + descriptions longues
5. **I5-I8** schemas Product/FAQ/Breadcrumb
6. **T5** WhatsApp Business

### Phase 5 — Blog SEO (mois 3-6)
- Mise en place de l'architecture blog auto (même modèle que les autres projets user)
- Plan éditorial 16 articles M1-M4
- Publication continue post-M4

### Phase 6 — Expansion marchés (mois 6+)
- Landing pages marché US/EU/UK
- Traductions DE/FR/ES
- Outreach backlinks B2B (annuaires fournisseurs, Alibaba cross-linking)

---

## 10. KPIs de suivi

| KPI | Baseline | M3 | M6 | M12 |
|---|---|---|---|---|
| Sessions organiques / mois | 0 | 100-500 | 1 500 | 5 000-8 000 |
| Pages indexées Google | 0 | 50 | 120 | 250+ |
| Mots-clés top 10 | 0 | 5 | 30 | 150+ |
| Leads B2B (form contact) | 0 | 5 | 20 | 60-120 |
| Core Web Vitals (LCP) | ? | < 2,5s | < 2s | < 1,8s |
| Domain Authority | ? | 5 | 15 | 25 |

---

## Annexes

- `audit/raw-findings/` — sorties brutes des 4 audits parallèles
- `scripts/send_mail.py` — script d'envoi des emails récap à stepnsecondaire@gmail.com
- `audit/emails/` — brouillons des emails récap par étape
