# leo — eviehometech.com SEO

Projet d'optimisation SEO pour **Hefei Ecologie Vie Home Technology Co., Ltd.** (fabricant chinois de smart pet products, eviehometech.com).

**Cible business :** acheteurs B2B récurrents EU/US (importateurs, distributeurs, marques, e-commerçants) cherchant un partenaire wholesale/OEM en Chine.

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
