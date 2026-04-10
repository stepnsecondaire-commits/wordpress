<?php
/**
 * Plugin Name: Leo Front Locale
 * Plugin URI:  https://github.com/stepnsecondaire-commits/wordpress
 * Description: Forces the front-end locale to en_US while keeping the WordPress admin in its current language (zh_CN). Fixes the mismatch between Chinese site language metadata and the English content actually served, so Google classifies the site as English and ranks it on EN queries. Also fixes html lang + og:locale on all front pages.
 * Version:     1.0.0
 * Author:      Leo Project (Augustin)
 * License:     GPL-2.0-or-later
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Force the front-end locale to en_US.
 * The WordPress admin keeps its current locale (zh_CN).
 * This filter runs on every locale request and discriminates by context.
 */
add_filter( 'locale', 'leo_force_frontend_en_us', 9999 );
function leo_force_frontend_en_us( $locale ) {
    if ( is_admin() ) {
        return $locale;
    }
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return $locale;
    }
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return $locale;
    }
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
        return $locale;
    }
    return 'en_US';
}

/**
 * Override the html lang attribute produced by language_attributes().
 * Ensures <html lang="en-US"> even if another filter tried to set it to zh-Hans.
 */
add_filter( 'language_attributes', 'leo_force_html_lang_en', 9999 );
function leo_force_html_lang_en( $output ) {
    if ( is_admin() ) {
        return $output;
    }
    $output = preg_replace( '/lang="[^"]*"/', 'lang="en-US"', $output );
    return $output;
}

/**
 * Override Open Graph locale for SureRank and any other SEO plugin.
 */
add_filter( 'surerank_og_locale', function () { return 'en_US'; }, 9999 );

/**
 * Final safety net: rewrite og:locale meta tag in the rendered HTML output
 * for the very front-end, in case a plugin writes it outside of filters.
 */
add_action( 'template_redirect', 'leo_start_ob_for_og_locale' );
function leo_start_ob_for_og_locale() {
    if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
        return;
    }
    ob_start( 'leo_rewrite_og_locale' );
}
function leo_rewrite_og_locale( $html ) {
    if ( empty( $html ) ) {
        return $html;
    }
    $html = preg_replace(
        '/<meta\s+property="og:locale"\s+content="[^"]*"\s*\/?>/i',
        '<meta property="og:locale" content="en_US" />',
        $html
    );
    return $html;
}
