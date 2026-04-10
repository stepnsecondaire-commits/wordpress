---
subject: [leo] Audit initial eviehometech.com — 3 verrous critiques à débloquer
to: stepnsecondaire@gmail.com
---

# Audit initial — eviehometech.com

Salut,

Voici le recap de l'audit complet du site **eviehometech.com** (Hefei Ecologie Vie Home Technology Co., Ltd.) qu'on vient de faire. Objectif rappel : maximiser le référencement organique Google EU/US pour capter des acheteurs B2B récurrents (importateurs, distributeurs, marques qui sourcent/achètent en Chine).

## Ce qu'on a fait

- Extraction complète du fichier `.wpress` (905 Mo)
- Audit read-only de la structure technique (thèmes, plugins, mu-plugins, versions)
- Audit de la base de données MySQL (548 posts, 2329 postmeta, 131 termes)
- Audit SEO on-page (meta, schemas, H1/H2, hreflang, textes hardcodés)
- Audit performance (1 640 médias, détail vidéos 4K)
- Rédaction du document `AUDIT.md` avec plan d'action priorisé en checkboxes

Aucune modif faite sur le site — pur mode lecture.

## Ce que fait le fournisseur (Léo)

Fabricant chinois de **smart pet products** — vente wholesale + OEM/ODM. 37 produits publiés, 8 catégories :

- Automatic Cat Litter Box (14)
- Automatic Cat Fountain (7)
- Bird Feeder (6)
- Pet Air Purifier (4)
- Vacuum Cleaner (4)
- Automatic Dog Feeder (3)
- Pet Smart Toys (2)
- Bark Collar & GPS Tracker (1)

Il y a 8 pages publiées : Home, Products, About, Contact, Reviews, News, Privacy, Terms, FAQs.

## Ce qui va bien

- **Stack moderne et saine** : WordPress 6.9.4, Breakdance (page builder), ACF Pro, SureRank SEO, LiteSpeed Cache, Cimo Image Optimizer. Tout est à jour.
- **Thème custom minimaliste** : zéro texte hardcodé dans le PHP, tout le contenu est éditable depuis l'admin WordPress — c'est exactement ce qu'on voulait.
- **Images très bien optimisées** : 98,5% en WebP, moyenne 31 Ko/image.
- **Schemas JSON-LD déjà configurés** : Organization, WebSite, SearchAction, WebPage.
- **Hébergeur sérieux** : Hostinger (mu-plugins officiels propres).
- **Sécurité globalement saine** : un seul admin, pas de backdoor, plugins à jour.

## 🔴 Les 3 verrous critiques (à débloquer avant toute stratégie de contenu)

### 1. Le site est TOTALEMENT invisible pour Google

Le fichier `litespeed/robots.txt` contient littéralement :

```
User-agent: *
Disallow: /
```

→ Google et Bing ont l'interdiction formelle de crawler quoi que ce soit. Le site existe mais il n'est pas dans l'index. **Aucun SEO possible tant que ce n'est pas corrigé.**

**Fix : 2 minutes de travail.** On remplace par un robots.txt propre qui autorise le crawl et référence le sitemap.

### 2. Le site est 100% en chinois et n'a AUCUN plugin multilingue

`WPLANG = zh_CN`. Pas de WPML, pas de Polylang, pas de Weglot, rien. Tout le contenu (pages, produits, menus, ACF) est en chinois. Le nom du menu principal c'est "主菜单".

→ Aujourd'hui le site est **inadressable** pour des acheteurs B2B américains, anglais, allemands, français. Même si on débloque Google, ce qu'il va indexer c'est du chinois, ce qui ne matchera jamais les requêtes anglophones qu'on vise (`china sourcing`, `pet products manufacturer`, `OEM cat feeder`, etc.).

**Fix : 3-5 jours de travail.** Installer **Polylang Pro** (recommandé vs WPML, plus léger, compatible Breakdance + ACF, meilleur pour perf) puis créer la version EN complète des 8 pages + 37 produits.

### 3. 78% des pages n'ont pas de meta description

SureRank a bien la capacité de gérer les meta descriptions, mais elles n'ont pas été remplies pour la majorité des pages et produits. Résultat : Google va fabriquer lui-même des snippets souvent mauvais, et le CTR sur les résultats de recherche sera faible même si on monte en position.

**Fix : 1 journée de travail.** Remplir les 45 meta descriptions à la main (8 pages + 37 produits), en anglais optimisé pour les mots-clés B2B.

## 🟠 Autres points importants (pas critiques mais gros impact)

- **446 Mo de vidéos 4K non compressées** (42 fichiers, 8 à 29 Mo chacun) — ça tue les Core Web Vitals. On peut gagner **~380 Mo (-85%)** en réencodant en H.265 1080p avec ffmpeg.
- **Aucun formulaire de contact/devis** détecté (pas de Contact Form 7, WPForms, Fluent Forms). Pour un site B2B dont l'objectif est de générer des leads, c'est un trou énorme.
- **Un `eval()` dans le plugin `assets4breakdance`** (Supa Code Block) → à auditer pour vérifier qui peut publier ce bloc (risque RCE si mauvaise ACL).
- **Pages sans structure H1/H2** propre signalées par SureRank.
- **Pas de WhatsApp Business chat** visible alors que le numéro est déjà dans le schema Organization.

## Pourquoi on branche GitHub

On met le projet sur GitHub ([stepnsecondaire-commits/wordpress](https://github.com/stepnsecondaire-commits/wordpress)) pour trois raisons :

1. **Versionning** : chaque modification est tracée (qui, quand, pourquoi). Si on casse quelque chose, on revient en arrière en une commande.
2. **Collaboration** : toi + moi on voit la même chose, on peut review les changements avant qu'ils aillent en prod.
3. **Sauvegarde** : le code du site est dupliqué hors Hostinger. Si le serveur meurt, on a tout.

On ne pousse PAS tout le contenu du `.wpress` :

- ❌ `uploads/` (508 Mo d'images et vidéos) — trop lourd, on garde en externe
- ❌ `wpvividbackups/` (243 Mo de backups obsolètes) — inutile
- ❌ Code des plugins payants (Breakdance, ACF Pro, SureRank Pro) — licences, pas à redistribuer
- ✅ On garde : `AUDIT.md`, le thème custom `breakdance-zero-theme-master`, le dump `database.sql`, les scripts d'automatisation, les emails récap

Repo final propre, ~10 Mo au lieu de 905 Mo.

## Plan des prochaines étapes

### Phase 1 — Déblocage (cette semaine)
1. Corriger `robots.txt`
2. Auditer l'`eval()` de assets4breakdance
3. Publier sitemap + configurer Google Search Console + Bing Webmaster
4. Installer un formulaire contact B2B
5. Remplir les 45 meta descriptions

### Phase 2 — Multilingue EN (semaines 2-3)
Polylang Pro + traduction intégrale en anglais.

### Phase 3 — Performance (semaine 4)
Réencodage vidéos H.265 + Cloudflare CDN.

### Phase 4 — Contenu B2B (semaines 5-8)
Pages OEM/ODM, Quality & Certifications, Factory Tour, Case Studies + réécriture produits + schemas Product/FAQ/Breadcrumb.

### Phase 5 — Blog SEO (mois 3-6)
Mise en place de l'architecture blog auto (même modèle que tes autres blogs : publish.py + GitHub Actions + Claude Sonnet 4).

## Ce qu'il me faut de toi pour démarrer la phase 1

1. **Validation du plan ci-dessus** (ou ajustements)
2. **Confirmation pour pousser le repo initial sur GitHub** (token d'accès si tu veux que je push directement)
3. **Accès admin WordPress** (URL + login) quand on passera aux actions en prod
4. **Credentials Hostinger** (ou accès SFTP/SSH) pour corriger le robots.txt
5. **Gmail App Password** pour que ce script d'envoi d'emails fonctionne (ce mail-ci est un brouillon, je ne peux pas encore l'envoyer)

Le document complet avec toutes les checkboxes est dans `AUDIT.md` à la racine du repo.

Bonne journée —
Claude (pour Augustin)
