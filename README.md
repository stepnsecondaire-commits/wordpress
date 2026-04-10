# leo — eviehometech.com SEO

Projet d'optimisation SEO pour **Hefei Ecologie Vie Home Technology Co., Ltd.** (fabricant chinois de smart pet products, eviehometech.com).

**Cible business :** acheteurs B2B récurrents EU/US (importateurs, distributeurs, marques, e-commerçants) cherchant un partenaire wholesale/OEM en Chine.

## Statut — CLOSED 2026-04-10 (autopilot)

Projet fermé pour l'instant, tourne en autonomie. Voir [audit/emails/21-blog-cron-live-and-roadmap-unblocked.md](audit/emails/21-blog-cron-live-and-roadmap-unblocked.md) pour le récap final.

**Ce qui tourne tout seul jusqu'au 13 mai 2026 :**
- Blog cron GitHub Actions publie 80 articles pre-written (Mon-Sat, 3/jour à 06:00, 14:00, 18:00 UTC) du 13/04 au 13/05/2026
- Feature image Unsplash auto via cascade fallback (cluster-based + generic)
- SureRank SEO meta, IndexNow ping, bot commit back to main — tout câblé
- Monitoring : https://github.com/stepnsecondaire-commits/wordpress/actions

**Ce qui reste à toi (UI wp-admin, non bloquant) :**
- Complianz wizard (5 min) — consent banner GDPR
- Site Kit OAuth (10 min) — Google Analytics + Search Console
- Catalog PDF upload (optionnel) — page /catalog/ marche déjà comme lead gen

**Livrables finaux :**
- 15 pages live (home, products, about, contact, reviews, news, privacy, terms, faqs + 6 B2B : oem-odm-services, certifications-quality, shipping-logistics, why-source-from-china, buyer-reviews, catalog)
- 35 produits (top 10 avec contenu unique B2B, 25 avec template enrichi)
- 100 articles blog dans 8 clusters (19 déjà live + 1 smoke test + 80 programmés)
- 23 PDFs de certifications dans la media library
- 12 buyer reviews vérifiés sur /buyer-reviews/
- 21 recap emails (audit/emails/01 à 21) documentant tout l'historique

**Quand rouvrir le projet :** voir `.claude/memory/leo-project.md` section "When to reopen this project".

## Structure

```
leo/
├── AUDIT.md                  ← audit complet + plan d'action en checkboxes
├── site/                     ← export WordPress (.wpress extrait)
├── audit/
│   ├── raw-findings/         ← sorties brutes des audits parallèles
│   └── emails/               ← brouillons des emails récap
└── scripts/
    └── send_mail.py          ← envoi des récaps à stepnsecondaire@gmail.com
```

## Workflow

1. L'audit initial est dans `AUDIT.md`. Chaque item est une checkbox qu'on coche au fur et à mesure.
2. À chaque étape importante : commit git + email récap (voir `scripts/send_mail.py`).
3. Les modifications techniques se font côté code (repo), les modifications contenu se font dans l'admin WordPress (qui reste en chinois).

## Contraintes

- ❌ L'admin WP reste en chinois — on ne touche pas.
- ✅ Tout contenu front doit être éditable depuis l'admin WP (pas de texte hardcodé).
- ✅ Modifs techniques libres (code, config, structured data, sitemap, perf).

## Stack du site

- WordPress 6.9.4 sur Hostinger
- Thème `breakdance-zero-theme-master` v1.0.0 (délégation 100% à Breakdance)
- Page builder : Breakdance v2.6.0
- SEO : SureRank + SureRank Business
- Cache : LiteSpeed Cache v7.8.1
- Custom fields : ACF Pro v6.8.0.1
- Pas de plugin multilingue (🔴 à corriger)
- Pas de WooCommerce (CPT `products` custom via ACF)
