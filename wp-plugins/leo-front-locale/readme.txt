=== Leo Front Locale ===
Contributors: leoproject
Tags: locale, language, i18n, seo
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later

Forces the front-end locale to en_US while keeping the WordPress admin in its current language (zh_CN).

== Description ==

This plugin fixes the mismatch between the Chinese site language metadata and the English content actually served by eviehometech.com.

What it does:
* Forces `get_locale()` to return `en_US` on every front-end request
* Rewrites `<html lang="...">` to `en-US`
* Rewrites `<meta property="og:locale" content="...">` to `en_US`
* Keeps the WordPress admin in its current language (Chinese)
* Keeps REST, AJAX and WP-CLI contexts untouched

Result: Google classifies the site as English, which allows it to rank on EN B2B queries.

== Installation ==

1. Upload via Plugins > Add New > Upload Plugin
2. Activate
3. Reload the front page and verify the html tag now says `lang="en-US"`

== Changelog ==

= 1.0.0 =
* Initial release.
