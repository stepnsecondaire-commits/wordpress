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
 * Custom title + description for CPT archives (products) that SureRank
 * does not cover through its per-post UI. The /products/ URL resolves
 * to the "products" post_type_archive, not the page with slug "products".
 */
add_filter( 'pre_get_document_title', 'leo_cpt_archive_title', 9999 );
function leo_cpt_archive_title( $title ) {
    if ( is_post_type_archive( 'products' ) ) {
        return 'Smart Pet Products Catalog | OEM & Wholesale China | Eviehome';
    }
    return $title;
}

add_filter( 'surerank_set_meta', 'leo_cpt_archive_meta', 9999 );
function leo_cpt_archive_meta( $meta ) {
    if ( is_post_type_archive( 'products' ) ) {
        $meta['page_title']       = 'Smart Pet Products Catalog | OEM & Wholesale China | Eviehome';
        $meta['page_description'] = 'Full catalog of smart pet products: automatic litter boxes, feeders, fountains, air purifiers, bird feeders. OEM/ODM available. MOQ 500 units, CE/FCC certified.';
    }
    return $meta;
}

/**
 * Ensure the meta description is printed on the products archive even if
 * SureRank skips it. Hooks into wp_head at priority 1.
 */
add_action( 'wp_head', 'leo_cpt_archive_meta_description', 1 );
function leo_cpt_archive_meta_description() {
    if ( is_post_type_archive( 'products' ) ) {
        echo "\n<meta name=\"description\" content=\"Full catalog of smart pet products: automatic litter boxes, feeders, fountains, air purifiers, bird feeders. OEM/ODM available. MOQ 500 units, CE/FCC certified.\" />\n";
    }
}

/**
 * Phase 2 design system: inject global CSS variables, typography overrides,
 * button styles, card styles, sticky header behavior and the floating
 * WhatsApp widget. Hooks into wp_head with high priority so the styles win
 * over the default Breakdance ones via CSS specificity.
 */
add_action( 'wp_head', 'leo_design_system_inject', 100 );
function leo_design_system_inject() {
    if ( is_admin() ) {
        return;
    }
    ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@500;700;800&display=swap">
<style id="leo-design-system">
:root{
  --leo-primary:#0f172a;
  --leo-primary-light:#1e293b;
  --leo-accent:#16a34a;
  --leo-accent-hover:#15803d;
  --leo-cta:#f97316;
  --leo-cta-hover:#ea580c;
  --leo-bg:#ffffff;
  --leo-bg-alt:#f8fafc;
  --leo-text:#1e293b;
  --leo-text-light:#64748b;
  --leo-border:#e2e8f0;
  --leo-success:#22c55e;
  --leo-info:#3b82f6;
  --leo-radius:8px;
  --leo-radius-lg:12px;
  --leo-shadow-sm:0 1px 2px rgba(15,23,42,.06);
  --leo-shadow-md:0 4px 12px rgba(15,23,42,.08);
  --leo-shadow-lg:0 8px 24px rgba(15,23,42,.12);
  --leo-transition:all .2s ease;
}
html,body{font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;}
h1,h2,h3,h4,h5,h6,.bde-heading{font-family:'Inter','DM Sans',-apple-system,sans-serif;letter-spacing:-.01em;}
h1,.bde-heading.bde-h1{font-weight:800;}
h2,.bde-heading.bde-h2{font-weight:700;}
h3,.bde-heading.bde-h3{font-weight:600;}
.bde-button .breakdance-button-link,
a.breakdance-button-link{
  border-radius:var(--leo-radius)!important;
  font-weight:600!important;
  font-size:16px!important;
  padding:14px 28px!important;
  transition:var(--leo-transition)!important;
  letter-spacing:0!important;
}
/* Primary CTA orange */
.leo-cta-primary,
.bde-button-31-114 .breakdance-button-link,
[class*="bde-button"][data-leo-style="cta"] .breakdance-button-link{
  background:var(--leo-cta)!important;
  color:#fff!important;
  box-shadow:0 2px 8px rgba(249,115,22,.3);
}
.leo-cta-primary:hover,
.bde-button-31-114 .breakdance-button-link:hover{
  background:var(--leo-cta-hover)!important;
  transform:translateY(-1px);
  box-shadow:0 4px 12px rgba(249,115,22,.4)!important;
}
/* Cards lift on hover */
.bde-div[class*="card"],
.product-card,
.bde-div.bde-product-card{
  transition:var(--leo-transition);
  border-radius:var(--leo-radius-lg)!important;
}
.bde-div[class*="card"]:hover,
.product-card:hover{
  box-shadow:var(--leo-shadow-lg);
  transform:translateY(-4px);
}
/* Sticky header on scroll */
.bde-header-builder--sticky-scroll-slide,
.bde-header-builder{
  transition:all .3s ease;
}
body.leo-scrolled .bde-header-builder{
  box-shadow:var(--leo-shadow-md);
  backdrop-filter:saturate(180%) blur(10px);
  background:rgba(255,255,255,.95)!important;
}
/* Float WhatsApp widget */
#leo-whatsapp{
  position:fixed;
  bottom:24px;
  right:24px;
  width:56px;
  height:56px;
  border-radius:50%;
  background:#25D366;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 4px 16px rgba(37,211,102,.4);
  z-index:9999;
  cursor:pointer;
  transition:all .25s ease;
  text-decoration:none;
}
#leo-whatsapp:hover{
  transform:scale(1.08);
  box-shadow:0 8px 24px rgba(37,211,102,.5);
}
#leo-whatsapp svg{width:32px;height:32px;fill:#fff;}
#leo-whatsapp .leo-wa-tooltip{
  position:absolute;
  right:72px;
  white-space:nowrap;
  background:#0f172a;
  color:#fff;
  padding:8px 14px;
  border-radius:8px;
  font-size:14px;
  font-weight:500;
  opacity:0;
  pointer-events:none;
  transform:translateX(8px);
  transition:all .25s ease;
}
#leo-whatsapp:hover .leo-wa-tooltip{
  opacity:1;
  transform:translateX(0);
}
/* Mobile responsive headings only - do NOT touch existing Breakdance section padding */
@media (max-width:768px){
  h1,.bde-heading.bde-h1{font-size:32px!important;}
  h2,.bde-heading.bde-h2{font-size:24px!important;}
}
</style>
    <?php
}

/**
 * Output the floating WhatsApp widget and the sticky header script in the footer.
 */
add_action( 'wp_footer', 'leo_design_system_widgets', 100 );
function leo_design_system_widgets() {
    if ( is_admin() ) {
        return;
    }
    ?>
<a id="leo-whatsapp" href="https://wa.me/8619956530913?text=Hi,%20I%20am%20interested%20in%20your%20smart%20pet%20products" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zM6.597 20.13c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
  <span class="leo-wa-tooltip">Chat with us</span>
</a>
<script>
(function(){
  var t=null;
  function onScroll(){
    if(window.scrollY>40){document.body.classList.add('leo-scrolled');}
    else{document.body.classList.remove('leo-scrolled');}
  }
  window.addEventListener('scroll',function(){if(t)return;t=setTimeout(function(){onScroll();t=null;},80);},{passive:true});
  onScroll();
})();
</script>
    <?php
}

/**
 * Performance: force preload="none" on every <video> tag in the rendered HTML.
 * The product pages embed 4K MP4 files that currently auto-preload on page load,
 * which destroys the Core Web Vitals scores on mobile (LCP > 10s on low-end
 * connections). Adding preload="none" defers the video download until the user
 * clicks play. Poster images will be added later once we have per-product thumbnails.
 */
add_filter( 'leo_ob_html', 'leo_add_video_preload_none', 10 );
function leo_add_video_preload_none( $html ) {
    // Inject preload="none" on every <video tag that does not already have it
    $html = preg_replace_callback(
        '/<video(\s[^>]*?)?>/i',
        function ( $m ) {
            $attrs = isset( $m[1] ) ? $m[1] : '';
            if ( stripos( $attrs, 'preload=' ) !== false ) {
                return $m[0];
            }
            return '<video preload="none"' . $attrs . '>';
        },
        $html
    );
    return $html;
}

/**
 * REMOVED 2026-04-10 after production incident: this kill switch triggered
 * the "For security reasons..." message on live pages including
 * /oem-odm-services/. Further investigation needed before re-enabling.
 * See audit notes for the path forward on the eval() hardening.
 */
// add_filter( 'breakdance_php_code_block_is_inside_shortcode', '__return_true', 9999 );

/**
 * Serve the IndexNow key file at the domain root so Bing and Yandex can
 * validate the key we use to ping them when a new blog post is published.
 * The key file lives at https://eviehometech.com/{key}.txt and must return
 * plain text containing only the key value.
 */
/**
 * Admin-only REST endpoints to read and write private postmeta.
 * Needed because Breakdance stores its tree data in postmeta keys that are
 * not exposed by the default WP REST API. Gated behind manage_options.
 *
 * GET  /wp-json/leo/v1/postmeta?post_id=31
 *   Returns every postmeta key for the post. Admin only.
 * POST /wp-json/leo/v1/postmeta
 *   Body: { "post_id": 31, "key": "_breakdance_data", "value": "..." }
 *   Updates a single postmeta value. Admin only.
 */
add_action( 'rest_api_init', 'leo_register_postmeta_endpoints' );
function leo_register_postmeta_endpoints() {
    register_rest_route( 'leo/v1', '/postmeta', [
        [
            'methods'             => 'GET',
            'callback'            => 'leo_rest_get_postmeta',
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
            'args' => [
                'post_id' => [ 'required' => true, 'type' => 'integer' ],
            ],
        ],
        [
            'methods'             => 'POST',
            'callback'            => 'leo_rest_update_postmeta',
            'permission_callback' => function () {
                return current_user_can( 'manage_options' );
            },
        ],
    ] );
}
function leo_rest_get_postmeta( $request ) {
    $post_id = (int) $request->get_param( 'post_id' );
    if ( ! get_post( $post_id ) ) {
        return new WP_Error( 'no_post', 'Post not found', [ 'status' => 404 ] );
    }
    $meta = get_post_meta( $post_id );
    return [ 'post_id' => $post_id, 'meta' => $meta ];
}
function leo_rest_update_postmeta( $request ) {
    $body = $request->get_json_params();
    if ( empty( $body['post_id'] ) || empty( $body['key'] ) || ! array_key_exists( 'value', $body ) ) {
        return new WP_Error( 'bad_request', 'Missing post_id, key or value', [ 'status' => 400 ] );
    }
    $post_id = (int) $body['post_id'];
    if ( ! get_post( $post_id ) ) {
        return new WP_Error( 'no_post', 'Post not found', [ 'status' => 404 ] );
    }
    // Try update first, fall back to add if no existing meta
    $result = update_post_meta( $post_id, $body['key'], wp_slash( $body['value'] ) );
    if ( $result === false ) {
        // update_post_meta returns false if the value is the same OR on real failure.
        // Try delete + add to force a write.
        delete_post_meta( $post_id, $body['key'] );
        $result = add_post_meta( $post_id, $body['key'], wp_slash( $body['value'] ), true );
    }
    // Bust any object cache for this post's meta
    wp_cache_delete( $post_id, 'post_meta' );
    if ( function_exists( 'clean_post_cache' ) ) {
        clean_post_cache( $post_id );
    }
    // Read back to verify
    $verify = get_post_meta( $post_id, $body['key'], true );
    $persisted = ( $verify === $body['value'] );
    return [
        'success'    => $result !== false,
        'post_id'    => $post_id,
        'key'        => $body['key'],
        'result'     => is_bool( $result ) ? ( $result ? 'true' : 'false' ) : (int) $result,
        'persisted'  => $persisted,
        'read_len'   => is_string( $verify ) ? strlen( $verify ) : null,
        'sent_len'   => strlen( $body['value'] ),
    ];
}

/**
 * Admin-only REST endpoint to list posts of any post_type, including
 * private CPTs like breakdance_header, breakdance_footer, breakdance_template.
 */
add_action( 'rest_api_init', 'leo_register_posts_list_endpoint' );
function leo_register_posts_list_endpoint() {
    register_rest_route( 'leo/v1', '/posts', [
        'methods'             => 'GET',
        'callback'            => 'leo_rest_list_posts',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
        'args' => [
            'post_type' => [ 'required' => true, 'type' => 'string' ],
        ],
    ] );
}
/**
 * Admin-only REST endpoint to create a post of any post_type, used to create
 * Popup Maker popups (which are not exposed via /wp/v2/popup).
 *
 * POST /wp-json/leo/v1/create_post
 * Body: { "post_type": "popup", "post_title": "...", "post_content": "...", "post_status": "publish", "meta": {...} }
 */
add_action( 'rest_api_init', function () {
    register_rest_route( 'leo/v1', '/create_post', [
        'methods'             => 'POST',
        'callback'            => 'leo_rest_create_post',
        'permission_callback' => function () { return current_user_can( 'manage_options' ); },
    ] );
} );
function leo_rest_create_post( $request ) {
    $body = $request->get_json_params();
    if ( empty( $body['post_type'] ) ) {
        return new WP_Error( 'bad_request', 'post_type required', [ 'status' => 400 ] );
    }
    $post_id = wp_insert_post( [
        'post_type'    => $body['post_type'],
        'post_title'   => isset( $body['post_title'] ) ? $body['post_title'] : '',
        'post_content' => isset( $body['post_content'] ) ? $body['post_content'] : '',
        'post_status'  => isset( $body['post_status'] ) ? $body['post_status'] : 'publish',
        'post_name'    => isset( $body['post_name'] ) ? $body['post_name'] : '',
    ], true );
    if ( is_wp_error( $post_id ) ) {
        return new WP_Error( 'insert_failed', $post_id->get_error_message(), [ 'status' => 500 ] );
    }
    if ( ! empty( $body['meta'] ) && is_array( $body['meta'] ) ) {
        foreach ( $body['meta'] as $key => $value ) {
            update_post_meta( $post_id, $key, wp_slash( is_array( $value ) || is_object( $value ) ? $value : (string) $value ) );
        }
    }
    return [ 'success' => true, 'post_id' => $post_id, 'post_type' => $body['post_type'] ];
}

function leo_rest_list_posts( $request ) {
    $post_type = $request->get_param( 'post_type' );
    $posts = get_posts( [
        'post_type'      => $post_type,
        'post_status'    => [ 'publish', 'draft', 'private' ],
        'posts_per_page' => 100,
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ] );
    $out = [];
    foreach ( $posts as $p ) {
        $out[] = [
            'id'        => $p->ID,
            'slug'      => $p->post_name,
            'title'     => $p->post_title,
            'status'    => $p->post_status,
            'post_type' => $p->post_type,
        ];
    }
    return $out;
}

add_action( 'parse_request', 'leo_serve_indexnow_key', 0 );
function leo_serve_indexnow_key( $wp ) {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    $key         = 'eviehome-indexnow-7f3a2b8c9d1e4f5a';
    if ( strpos( $request_uri, '/' . $key . '.txt' ) === 0 ) {
        status_header( 200 );
        nocache_headers();
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo $key;
        exit;
    }
}

/**
 * Fix the broken category link in the Breakdance footer menu.
 * The footer references /product-category/automatic-pet-fountain/ but the
 * real taxonomy slug is automatic-cat-fountain. Editing the footer would
 * require going into the Breakdance builder, so we handle it here with a
 * permanent 301 redirect instead.
 */
add_action( 'template_redirect', 'leo_fix_broken_category_link', 1 );
function leo_fix_broken_category_link() {
    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
    if ( strpos( $request_uri, '/product-category/automatic-pet-fountain' ) === 0 ) {
        wp_redirect( home_url( '/product-category/automatic-cat-fountain/' ), 301 );
        exit;
    }
}

/**
 * Inject Product schema on every single product page and FAQPage schema
 * on the /faqs/ page. SureRank already emits Organization, WebSite and
 * BreadcrumbList, so these are the two missing pieces for Google rich results.
 */
add_action( 'wp_head', 'leo_inject_product_schema', 5 );
function leo_inject_product_schema() {
    if ( ! is_singular( 'products' ) ) {
        return;
    }
    global $post;
    if ( ! $post ) {
        return;
    }

    $title       = get_the_title( $post );
    $description = get_post_meta( $post->ID, 'surerank_settings_page_description', true );
    if ( empty( $description ) ) {
        $description = wp_strip_all_tags( get_the_excerpt( $post ) );
        if ( empty( $description ) ) {
            $description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '...' );
        }
    }

    $image_url = '';
    if ( has_post_thumbnail( $post ) ) {
        $image_url = get_the_post_thumbnail_url( $post, 'full' );
    } else {
        // Try to grab the first image from the content
        if ( preg_match( '/<img[^>]+src="([^"]+)"/', $post->post_content, $m ) ) {
            $image_url = $m[1];
        }
    }

    $categories = wp_get_post_terms( $post->ID, 'product-category', [ 'fields' => 'names' ] );
    $category_name = ! is_wp_error( $categories ) && ! empty( $categories ) ? $categories[0] : 'Smart Pet Product';

    $product_schema = [
        '@context'     => 'https://schema.org',
        '@type'        => 'Product',
        '@id'          => get_permalink( $post ) . '#product',
        'name'         => $title,
        'description'  => $description,
        'sku'          => 'EVIE-' . $post->ID,
        'mpn'          => 'EVIE-' . $post->ID,
        'category'     => $category_name,
        'brand'        => [
            '@type' => 'Brand',
            'name'  => 'Eviehome',
        ],
        'manufacturer' => [
            '@type' => 'Organization',
            '@id'   => 'https://eviehometech.com/#organization',
            'name'  => 'Hefei Ecologie Vie Home Technology Co., Ltd.',
            'url'   => 'https://eviehometech.com/',
        ],
        'url'          => get_permalink( $post ),
    ];
    if ( $image_url ) {
        $product_schema['image'] = $image_url;
    }

    echo "\n<script type=\"application/ld+json\" id=\"leo-product-schema\">\n";
    echo wp_json_encode( $product_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}

/**
 * Inject FAQPage schema on the /faqs/ page, based on the actual Q and A
 * currently published on that page (verified via live scrape on 2026-04-10).
 */
add_action( 'wp_head', 'leo_inject_faq_schema', 5 );
function leo_inject_faq_schema() {
    if ( ! is_page( 'faqs' ) ) {
        return;
    }

    $qas = [
        [
            'q' => 'Could you do OEM and ODM orders?',
            'a' => 'Yes, we can produce the products according to your customized requirements. OEM and ODM are the core of our business: logo, colors, packaging, manuals, firmware and regional power plugs can all be customized.',
        ],
        [
            'q' => 'How do you guarantee the quality of your smart pet products?',
            'a' => 'Every order goes through a pre-production sample that you approve before mass production starts, and a final inspection on 100% of the finished units before shipment. Third-party inspections by SGS, Bureau Veritas, TUV or QIMA are welcome at the buyer cost.',
        ],
        [
            'q' => 'What is the warranty time for your pet products?',
            'a' => 'The standard warranty is one year. If a quality problem occurs within the warranty period, we offer free replacement accessories or a full replacement machine depending on the defect.',
        ],
        [
            'q' => 'What is the MOQ and how is the price calculated?',
            'a' => 'The standard MOQ is 500 units per SKU for ODM orders and 1 000 to 3 000 units for OEM orders requiring new tooling. Lower trial orders can be discussed for first-time customers. Unit price depends on the quantity: the higher the order, the lower the unit cost.',
        ],
        [
            'q' => 'What is the best price you can offer?',
            'a' => 'As a factory-direct manufacturer we offer the best combination of price and quality on the market. The exact price depends on the product, quantity, customization and destination. Contact us with your specifications and target volume for a precise quote.',
        ],
        [
            'q' => 'Do you accept dropshipping?',
            'a' => 'Yes, dropshipping is a significant part of our business. We can handle direct shipments to your end customers with your branding on the packaging and invoice.',
        ],
        [
            'q' => 'Can you help us source other pet products not in your catalog?',
            'a' => 'Yes, thanks to our strong supply chain in the Chinese pet products industry, we can source other items on request for our existing customers.',
        ],
        [
            'q' => 'How do you ship the goods?',
            'a' => 'We ship by sea, by air or by rail depending on your urgency and budget. Sea freight is the standard for full-container orders, air freight for urgent reorders, and rail (China to Europe via the New Silk Road) as an intermediate option.',
        ],
        [
            'q' => 'Do you support DDP (Delivered Duty Paid) services?',
            'a' => 'Yes. Send us your destination address and we can quote a DDP price that includes freight, customs clearance, import duties and final delivery to your warehouse.',
        ],
        [
            'q' => 'If we want to design a new smart pet product, can you help?',
            'a' => 'Yes. We can set up a WeChat or WhatsApp group between you and our engineering team so you can discuss your idea directly. From brief to prototype typically takes 2 to 4 weeks depending on complexity.',
        ],
    ];

    $main_entity = [];
    foreach ( $qas as $qa ) {
        $main_entity[] = [
            '@type'          => 'Question',
            'name'           => $qa['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $qa['a'],
            ],
        ];
    }

    $faq_schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        '@id'        => home_url( '/faqs/' ) . '#faqpage',
        'mainEntity' => $main_entity,
    ];

    echo "\n<script type=\"application/ld+json\" id=\"leo-faq-schema\">\n";
    echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}

/**
 * Inject Review + AggregateRating JSON-LD schema on the reviews pages.
 * Data is extracted verbatim from verified Alibaba Trade Assurance reviews.
 * No fake reviews. Updating this list requires real new reviews.
 */
/**
 * Inject an enhanced Organization schema on every page. Extends the default
 * SureRank Organization entity with the data we collected from the live site
 * footer (Ryan Lau, phone numbers, WhatsApp, factory address, founding year,
 * patents, certifications). Large GEO signal for LLM citation and Google
 * knowledge graph entry.
 */
add_action( 'wp_head', 'leo_inject_enhanced_org_schema', 3 );
function leo_inject_enhanced_org_schema() {
    if ( is_admin() ) {
        return;
    }
    $schema = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Organization',
        '@id'           => 'https://eviehometech.com/#organization-enhanced',
        'name'          => 'Hefei Ecologie Vie Home Technology Co., Ltd.',
        'alternateName' => [ 'Eviehome', 'Ecologie Vie Home Technology' ],
        'url'           => 'https://eviehometech.com/',
        'logo'          => 'https://eviehometech.com/wp-content/uploads/2026/03/eviehometech-logo-1.webp',
        'slogan'        => 'Factory-direct smart pet products manufacturer since 2014',
        'description'   => 'Hefei Ecologie Vie Home Technology Co., Ltd. (Eviehome) is a factory-direct smart pet products manufacturer based in Hefei, Anhui Province, China. Since 2014 we have supplied wholesale, OEM and ODM smart pet products (automatic cat litter boxes, pet feeders, pet water fountains, pet air purifiers, bird feeders, GPS trackers, bark collars, robot vacuums) to importers, distributors and private-label brands in more than 30 countries.',
        'foundingDate'  => '2014',
        'numberOfEmployees' => [
            '@type'     => 'QuantitativeValue',
            'minValue'  => 51,
            'maxValue'  => 200,
        ],
        'address'       => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Mingmen Industrial Park, No. 7235 Ziyun Road, Economic and Technological Development Zone',
            'addressLocality' => 'Hefei',
            'addressRegion'   => 'Anhui',
            'postalCode'      => '230601',
            'addressCountry'  => 'CN',
        ],
        'contactPoint'  => [
            [
                '@type'             => 'ContactPoint',
                'name'              => 'Ryan Lau',
                'contactType'       => 'sales',
                'email'             => 'ryanlau@eviehometech.com',
                'telephone'         => '+86 17333173263',
                'availableLanguage' => [ 'English', 'Chinese' ],
                'areaServed'        => [ 'US', 'CA', 'GB', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'PL', 'PT', 'AU', 'NZ', 'JP', 'KR', 'SG', 'IN', 'AE', 'BR' ],
            ],
            [
                '@type'             => 'ContactPoint',
                'contactType'       => 'customer support',
                'telephone'         => '+86 19956530913',
                'contactOption'     => 'TollFree',
                'availableLanguage' => [ 'English', 'Chinese' ],
            ],
        ],
        'knowsAbout'    => [
            'OEM smart pet products manufacturing',
            'ODM pet products development',
            'Automatic cat litter box manufacturing',
            'Smart pet feeders',
            'Pet water fountains',
            'Pet air purifiers',
            'Smart bird feeders',
            'Pet GPS trackers',
            'Bark collars',
            'Pet vacuum cleaners',
            'Private label pet products',
            'CE certification',
            'FCC certification',
            'ROHS compliance',
            'REACH compliance',
        ],
        'hasCredential' => [
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'CE Marking', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'FCC Part 15', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'ROHS', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'REACH', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'ISO 9001', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'PSE Japan', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'IP68', 'credentialCategory' => 'certification' ],
            [ '@type' => 'EducationalOccupationalCredential', 'name' => 'UKCA', 'credentialCategory' => 'certification' ],
        ],
        'sameAs'        => [
            'https://www.instagram.com/eviehometech/',
            'https://www.facebook.com/profile.php?id=61585057945796',
            'https://wa.me/8617333173263',
        ],
        'award'         => '8 international design patents for smart pet products',
        'makesOffer'    => [
            '@type' => 'Offer',
            'description' => 'OEM and ODM smart pet products manufacturing with MOQ from 500 units',
            'priceSpecification' => [
                '@type' => 'PriceSpecification',
                'priceCurrency' => 'USD',
                'description' => 'Factory-direct wholesale pricing, custom quote within 24 hours',
            ],
        ],
    ];
    echo "\n<script type=\"application/ld+json\" id=\"leo-enhanced-org-schema\">\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}

/**
 * Inject a visible breadcrumb trail at the top of every non-home page.
 * The BreadcrumbList schema is already emitted by SureRank, but the visible
 * rendering is missing. We add a simple breadcrumb bar via output buffer
 * injection right after the <body class="..."> opening tag.
 */
add_filter( 'leo_ob_html', 'leo_inject_visible_breadcrumbs', 20 );
function leo_inject_visible_breadcrumbs( $html ) {
    if ( is_front_page() || is_home() || is_admin() ) {
        return $html;
    }
    // Build the breadcrumb trail based on the current WP context
    $trail = [ [ 'label' => 'Home', 'url' => home_url( '/' ) ] ];
    if ( is_singular( 'products' ) ) {
        $trail[] = [ 'label' => 'Products', 'url' => home_url( '/products/' ) ];
        $trail[] = [ 'label' => get_the_title(), 'url' => '' ];
    } elseif ( is_singular( 'post' ) ) {
        $trail[] = [ 'label' => 'Blog', 'url' => home_url( '/news/' ) ];
        $trail[] = [ 'label' => get_the_title(), 'url' => '' ];
    } elseif ( is_post_type_archive( 'products' ) ) {
        $trail[] = [ 'label' => 'Products', 'url' => '' ];
    } elseif ( is_page() ) {
        $trail[] = [ 'label' => get_the_title(), 'url' => '' ];
    } else {
        return $html;
    }

    $crumbs_html = '<nav class="leo-breadcrumbs" aria-label="Breadcrumb"><div class="leo-breadcrumbs-inner">';
    foreach ( $trail as $i => $crumb ) {
        if ( $i > 0 ) {
            $crumbs_html .= '<span class="leo-breadcrumbs-sep">&rsaquo;</span>';
        }
        if ( ! empty( $crumb['url'] ) ) {
            $crumbs_html .= '<a href="' . esc_url( $crumb['url'] ) . '">' . esc_html( $crumb['label'] ) . '</a>';
        } else {
            $crumbs_html .= '<span aria-current="page">' . esc_html( $crumb['label'] ) . '</span>';
        }
    }
    $crumbs_html .= '</div></nav>';

    $style = '<style id="leo-breadcrumbs-style">.leo-breadcrumbs{background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:12px 0;font-family:Inter,sans-serif;font-size:13px;color:#64748b;}.leo-breadcrumbs-inner{max-width:1200px;margin:0 auto;padding:0 24px;}.leo-breadcrumbs a{color:#16a34a;text-decoration:none;font-weight:500;}.leo-breadcrumbs a:hover{text-decoration:underline;}.leo-breadcrumbs-sep{margin:0 8px;color:#cbd5e1;}.leo-breadcrumbs [aria-current]{color:#0f172a;font-weight:600;}</style>';

    // Insert right after the Breakdance header container
    $marker = '<main class="bde-';
    $pos = strpos( $html, $marker );
    if ( $pos !== false ) {
        $html = substr( $html, 0, $pos ) . $style . $crumbs_html . substr( $html, $pos );
    }
    return $html;
}

add_action( 'wp_head', 'leo_inject_reviews_schema', 5 );
function leo_inject_reviews_schema() {
    if ( ! ( is_page( 'buyer-reviews' ) || is_page( 'reviews' ) ) ) {
        return;
    }

    $item_reviewed = [
        '@type' => 'Organization',
        '@id'   => 'https://eviehometech.com/#organization',
        'name'  => 'Hefei Ecologie Vie Home Technology Co., Ltd.',
        'url'   => 'https://eviehometech.com/',
    ];

    $reviews = [
        [ 'author' => 'Jasmin Movahedian',  'country' => 'Italy',                'rating' => 5, 'product' => 'OEM Electric Smart Quick Self Cleaning Cat Litter Box', 'body' => "Everything is perfect with this automatic litter. My two cats love it and I need to take out the trash only once a week. I really recommend it, especially because this one has the hidden drawer which is more elegant than other litters." ],
        [ 'author' => 'Arno Gregary',       'country' => 'South Korea',          'rating' => 5, 'product' => 'OEM Cat Litter Box Self Cleaning Smart Cat Litter Box', 'body' => "Since getting this automatic litter box, my three cats litter area has become so much cleaner and tidier. What I like most is its large capacity and automatic cleaning function, eliminating the need for manual scooping." ],
        [ 'author' => 'Saqib Farooq',       'country' => 'Pakistan',             'rating' => 5, 'product' => 'Smart Pet Water Dispenser 304SS Material', 'body' => "Material: Perfect. Application: Perfect. Feature: Perfect." ],
        [ 'author' => 'Vladimir Fliser',    'country' => 'Slovenia',             'rating' => 5, 'product' => 'Ultra-Safe Automatic Self-Cleaning Cat Litter Box', 'body' => "Super." ],
        [ 'author' => 'Shahid Mahmood',     'country' => 'United Arab Emirates', 'rating' => 5, 'product' => 'M1 Large 100L Automatic Smart Cat Litter Box', 'body' => "Delivered on time with full vessel tracking." ],
        [ 'author' => 'Justin Davidson',    'country' => 'Australia',            'rating' => 5, 'product' => 'Smart Pet Water Dispenser 304 Stainless Steel Cat and Dog Fountain', 'body' => "It is better than the description. The dogs do not splash in the water. Definitely recommend it." ],
        [ 'author' => 'Mohammad Mohsin',    'country' => 'India',                'rating' => 5, 'product' => 'OEM Cat Litter Box Self Cleaning Smart Cat Litter Box', 'body' => "The product is very good value for money, quiet operation, no smell in the room." ],
        [ 'author' => 'Singapore buyer',    'country' => 'Singapore',            'rating' => 5, 'product' => 'Automatic Cat Litter Box with Smart WiFi for Multi-Cat Households', 'body' => "Nice job." ],
        [ 'author' => 'Singapore buyer',    'country' => 'Singapore',            'rating' => 5, 'product' => '304 Stainless Steel Motion Sensor Pet Fountain', 'body' => "Perfect." ],
        [ 'author' => 'Singapore buyer',    'country' => 'Singapore',            'rating' => 5, 'product' => 'Automatic Dog Feeder Camera 1080P Live Streaming', 'body' => "Perfect." ],
        [ 'author' => 'Singapore buyer',    'country' => 'Singapore',            'rating' => 5, 'product' => 'Factory Wholesale Pet Products Supplier Cat Dog Feeder', 'body' => "Nice work." ],
        [ 'author' => 'Singapore buyer',    'country' => 'Singapore',            'rating' => 5, 'product' => 'Recordable Answer Buzzers for Dog Talk Buttons', 'body' => "Super qualite tout est bien." ],
    ];

    $review_nodes = [];
    foreach ( $reviews as $r ) {
        $review_nodes[] = [
            '@type'         => 'Review',
            'itemReviewed'  => $item_reviewed,
            'author'        => [
                '@type'  => 'Person',
                'name'   => $r['author'],
                'address' => [
                    '@type'        => 'PostalAddress',
                    'addressCountry' => $r['country'],
                ],
            ],
            'reviewBody'    => $r['body'],
            'name'          => 'Verified Alibaba Trade Assurance review for ' . $r['product'],
            'reviewRating'  => [
                '@type'       => 'Rating',
                'ratingValue' => $r['rating'],
                'bestRating'  => 5,
                'worstRating' => 1,
            ],
            'publisher'     => [
                '@type' => 'Organization',
                'name'  => 'Alibaba Trade Assurance',
            ],
        ];
    }

    $aggregate = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Organization',
        '@id'           => 'https://eviehometech.com/#organization',
        'name'          => 'Hefei Ecologie Vie Home Technology Co., Ltd.',
        'url'           => 'https://eviehometech.com/',
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => 5.0,
            'bestRating'  => 5,
            'worstRating' => 1,
            'ratingCount' => count( $reviews ),
            'reviewCount' => count( $reviews ),
        ],
        'review' => $review_nodes,
    ];

    echo "\n<script type=\"application/ld+json\" id=\"leo-reviews-schema\">\n";
    echo wp_json_encode( $aggregate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    echo "\n</script>\n";
}

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

    // Footer lorem ipsum hotfix - Breakdance overwrites our postmeta updates
    // on this template, so we patch the rendered HTML at the output buffer level.
    $html = str_replace(
        'Sagittis scelerisque nulla cursus in enim consectetur quam. Dictum urna sed consectetur neque tristique pellentesque.',
        'Stay updated with our latest smart pet products, trade show appearances, and industry insights. Join 2000+ pet industry professionals.',
        $html
    );
    // Belt and braces if the two sentences are split:
    $html = str_replace(
        'Sagittis scelerisque nulla cursus in enim consectetur quam.',
        'Stay updated with our latest smart pet products, trade show appearances, and industry insights. Join 2000+ pet industry professionals.',
        $html
    );
    $html = str_replace(
        ' Dictum urna sed consectetur neque tristique pellentesque.',
        '',
        $html
    );

    // Footer broken /faq/ link hotfix - same reason as above
    $html = preg_replace( '#href="/faq/"#', 'href="/faqs/"', $html );
    $html = preg_replace( '#href="/faq"#', 'href="/faqs/"', $html );

    // Apply other HTML transformations through the leo_ob_html filter chain
    $html = apply_filters( 'leo_ob_html', $html );
    // Inject homepage Phase 3 sections just before the Breakdance footer
    if ( ( is_front_page() || is_home() ) && function_exists( 'leo_homepage_extra_sections' ) ) {
        $marker  = "<div class='breakdance'><footer";
        $extras  = leo_homepage_extra_sections();
        $html    = str_replace( $marker, $extras . $marker, $html );
    }
    // Inject About Us factory tour video section just before the Breakdance footer
    if ( is_page( 'about-us' ) && function_exists( 'leo_about_extra_sections' ) ) {
        $marker  = "<div class='breakdance'><footer";
        $extras  = leo_about_extra_sections();
        $html    = str_replace( $marker, $extras . $marker, $html );
    }
    return $html;
}

/**
 * Factory tour video section for /about-us/. Three Hefei factory tour videos
 * with VideoObject schema. Injected via output buffer to avoid touching the
 * Breakdance tree of the about page.
 */
function leo_about_extra_sections() {
    $html = '';
    $html .= '<style id="leo-about-video-styles"> .leo-vid-section{padding:80px 24px;background:#f8fafc;font-family:Inter,sans-serif;color:#1e293b;} .leo-vid-container{max-width:1200px;margin:0 auto;} .leo-vid-eyebrow{display:block;text-align:center;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#16a34a;margin-bottom:16px;} .leo-vid-section h2{text-align:center;font-size:36px;font-weight:800;margin:0 0 16px;line-height:1.2;color:#0f172a;} .leo-vid-lead{text-align:center;font-size:18px;color:#64748b;max-width:720px;margin:0 auto 48px;line-height:1.6;} .leo-vid-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;} .leo-vid-grid figure{margin:0;background:#0f172a;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.15);transition:all .25s;} .leo-vid-grid figure:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(15,23,42,.2);} .leo-vid-grid video{width:100%;height:auto;display:block;background:#0f172a;} .leo-vid-grid figcaption{padding:14px 18px;background:#fff;font-size:13px;color:#475569;line-height:1.4;} @media(max-width:1024px){.leo-vid-grid{grid-template-columns:1fr 1fr;}} @media(max-width:640px){.leo-vid-grid{grid-template-columns:1fr;}.leo-vid-section h2{font-size:28px;}} </style>';
    $html .= '<section class="leo-vid-section" id="leo-about-factory-tour">';
    $html .= '<div class="leo-vid-container">';
    $html .= '<p class="leo-vid-eyebrow">Inside the factory</p>';
    $html .= '<h2>Real Footage from our Hefei Production Lines</h2>';
    $html .= '<p class="leo-vid-lead">No stock video, no marketing reel. Three short clips shot inside the Eviehome assembly line, quality control station and packing area in Hefei, China. This is exactly what your buyers see when they visit us in person.</p>';
    $html .= '<div class="leo-vid-grid">';
    $html .= '<figure><video controls preload="none" playsinline><source src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-1.mp4" type="video/mp4">Your browser does not support video playback.</video><figcaption>Eviehome smart pet products assembly line in operation</figcaption></figure>';
    $html .= '<figure><video controls preload="none" playsinline><source src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-2.mp4" type="video/mp4">Your browser does not support video playback.</video><figcaption>Quality control and finished goods inspection</figcaption></figure>';
    $html .= '<figure><video controls preload="none" playsinline><source src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-3.mp4" type="video/mp4">Your browser does not support video playback.</video><figcaption>Packing and palletizing for export shipments</figcaption></figure>';
    $html .= '</div>';
    $html .= '<p style="text-align:center;margin-top:40px;"><a href="/contact-us/" style="display:inline-block;padding:14px 32px;background:#f97316;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;box-shadow:0 4px 16px rgba(249,115,22,.3);">Schedule a factory visit or video call</a></p>';
    $html .= '</div></section>';
    $html .= '<script type="application/ld+json">{"@context":"https://schema.org","@graph":[';
    $html .= '{"@type":"VideoObject","name":"Eviehome smart pet products assembly line in operation","description":"Live footage from the Eviehome smart pet products assembly line in Hefei, China.","contentUrl":"https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-1.mp4","uploadDate":"2026-04-10","embedUrl":"https://eviehometech.com/about-us/","publisher":{"@type":"Organization","name":"Hefei Ecologie Vie Home Technology Co., Ltd."}},';
    $html .= '{"@type":"VideoObject","name":"Eviehome quality control and finished goods inspection","description":"Quality control inspection on Eviehome smart pet products before packing.","contentUrl":"https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-2.mp4","uploadDate":"2026-04-10","embedUrl":"https://eviehometech.com/about-us/","publisher":{"@type":"Organization","name":"Hefei Ecologie Vie Home Technology Co., Ltd."}},';
    $html .= '{"@type":"VideoObject","name":"Eviehome packing and palletizing for export shipments","description":"Packing and palletizing of Eviehome smart pet products for international export shipments.","contentUrl":"https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-tour-production-line-3.mp4","uploadDate":"2026-04-10","embedUrl":"https://eviehometech.com/about-us/","publisher":{"@type":"Organization","name":"Hefei Ecologie Vie Home Technology Co., Ltd."}}';
    $html .= ']}</script>';
    return $html;
}

/**
 * Phase 3 homepage extra sections, injected via output buffer just before
 * the Breakdance footer. Pure HTML + scoped CSS, no JavaScript dependency
 * other than a tiny IntersectionObserver counter for the OEM timeline.
 */
function leo_homepage_extra_sections() {
    $html = '';
    $html .= '<style id="leo-home-video-styles"> .leo-home-vid{padding:80px 24px;background:#0f172a;color:#fff;font-family:Inter,sans-serif;} .leo-home-vid-container{max-width:1100px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:center;} .leo-home-vid-eyebrow{display:inline-block;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#16a34a;margin-bottom:16px;} .leo-home-vid h2{font-size:36px;font-weight:800;color:#fff;margin:0 0 16px;line-height:1.2;} .leo-home-vid p{font-size:17px;color:#cbd5e1;line-height:1.6;margin:0 0 24px;} .leo-home-vid ul{list-style:none;padding:0;margin:0 0 32px;} .leo-home-vid li{padding:8px 0 8px 28px;color:#cbd5e1;font-size:15px;position:relative;} .leo-home-vid li:before{content:"\2713";position:absolute;left:0;color:#16a34a;font-weight:800;font-size:16px;} .leo-home-vid-cta{display:inline-block;padding:14px 28px;background:#f97316;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;box-shadow:0 4px 16px rgba(249,115,22,.4);transition:all .2s;} .leo-home-vid-cta:hover{transform:translateY(-2px);background:#ea580c;} .leo-home-vid-player{background:#000;border-radius:12px;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.4);} .leo-home-vid-player video{width:100%;height:auto;display:block;} @media(max-width:1024px){.leo-home-vid-container{grid-template-columns:1fr;gap:32px;}.leo-home-vid h2{font-size:28px;}} </style>';
    $html .= '<section class="leo-home-vid" id="leo-product-demo-video"><div class="leo-home-vid-container"><div><span class="leo-home-vid-eyebrow">See it in action</span><h2>Smart Self-Cleaning Cat Litter Box: Live Demo</h2><p>This is the actual product, demoed inside our Hefei factory. No CGI, no marketing edit. Watch the rotating drum cycle, the sealed waste drawer, the silent motor and the WiFi app pairing.</p><ul><li>14 cat litter box models in production</li><li>OEM and ODM available from 500 units</li><li>CE, FCC, ROHS certified</li><li>45 to 60 days from order to shipped container</li></ul><a href="/contact-us/" class="leo-home-vid-cta">Request a Quote</a></div><div class="leo-home-vid-player"><video controls preload="none" playsinline><source src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-smart-cat-litter-box-product-demo.mp4" type="video/mp4">Your browser does not support video playback.</video></div></div></section>';
    $html .= '<script type="application/ld+json">{"@context":"https://schema.org","@type":"VideoObject","name":"Eviehome smart self-cleaning cat litter box live product demo","description":"Live demo of the Eviehome smart self-cleaning cat litter box including rotating drum cycle, sealed waste drawer, silent motor and WiFi app pairing. Filmed inside the Hefei factory in China.","contentUrl":"https://eviehometech.com/wp-content/uploads/2026/04/eviehome-smart-cat-litter-box-product-demo.mp4","uploadDate":"2026-04-10","embedUrl":"https://eviehometech.com/","publisher":{"@type":"Organization","name":"Hefei Ecologie Vie Home Technology Co., Ltd."}}</script>';
    $html .= '<style id="leo-clients-style"> .leo-clients{padding:80px 24px;background:#fff;font-family:Inter,sans-serif;} .leo-clients-container{max-width:1200px;margin:0 auto;} .leo-clients-eyebrow{display:block;text-align:center;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#16a34a;margin-bottom:16px;} .leo-clients h2{text-align:center;font-size:36px;font-weight:800;color:#0f172a;margin:0 0 16px;line-height:1.2;} .leo-clients p.lead{text-align:center;font-size:18px;color:#64748b;max-width:720px;margin:0 auto 48px;line-height:1.6;} .leo-clients-grid{display:grid;grid-template-columns:1fr 1fr;gap:32px;} .leo-clients-grid figure{margin:0;border-radius:12px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.1);transition:all .25s;position:relative;} .leo-clients-grid figure:hover{transform:translateY(-4px);box-shadow:0 16px 32px rgba(15,23,42,.15);} .leo-clients-grid img{width:100%;height:380px;object-fit:cover;display:block;} .leo-clients-grid figcaption{position:absolute;bottom:0;left:0;right:0;padding:20px 24px;background:linear-gradient(0deg,rgba(15,23,42,.85),transparent);color:#fff;font-size:14px;line-height:1.5;} .leo-clients-grid figcaption strong{display:block;font-size:15px;font-weight:700;margin-bottom:4px;} @media(max-width:768px){.leo-clients-grid{grid-template-columns:1fr;}.leo-clients h2{font-size:28px;}} </style>';
    $html .= '<section class="leo-clients" id="leo-clients-factory-visit"><div class="leo-clients-container"><span class="leo-clients-eyebrow">Real customers, real factory visits</span><h2>Our Buyers Come to Hefei</h2><p class="lead">International B2B buyers from more than 30 countries visit our factory to inspect our smart pet product lines before placing their first order. You are welcome to do the same.</p><div class="leo-clients-grid"><figure><img src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-customer-visit-1-scaled.jpg" alt="International B2B buyers visiting Eviehome smart pet products factory in Hefei China to inspect cat litter box production line" loading="lazy"><figcaption><strong>B2B importers meeting our team</strong>International buyers at the Eviehome showroom in Hefei</figcaption></figure><figure><img src="https://eviehometech.com/wp-content/uploads/2026/04/eviehome-factory-customer-visit-2-scaled.jpg" alt="Foreign B2B importer reviewing Eviehome smart cat litter box product samples in person at Hefei factory China" loading="lazy"><figcaption><strong>Hands-on sample inspection</strong>Customer reviewing our smart cat litter box range in person</figcaption></figure></div><p style="text-align:center;margin-top:40px;"><a href="/contact-us/" style="display:inline-block;padding:14px 28px;background:#f97316;color:#fff;border-radius:8px;font-weight:700;text-decoration:none;box-shadow:0 4px 16px rgba(249,115,22,.3);">Schedule your factory visit</a></p></div></section>';
    $html .= '<style id="leo-phase3-styles"> .leo-section{padding:80px 24px;font-family:Inter,sans-serif;color:#1e293b;} .leo-section.leo-bg-alt{background:#f8fafc;} .leo-container{max-width:1200px;margin:0 auto;} .leo-eyebrow{display:block;text-align:center;font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#16a34a;margin-bottom:16px;} .leo-section h2{text-align:center;font-size:36px;font-weight:800;margin:0 0 16px;line-height:1.2;color:#0f172a;} .leo-section .leo-section-lead{text-align:center;font-size:18px;color:#64748b;max-width:720px;margin:0 auto 48px;line-height:1.6;} .leo-trust{display:grid;grid-template-columns:repeat(6,1fr);gap:24px;align-items:center;text-align:center;} .leo-trust .leo-badge{padding:24px 12px;background:#fff;border:1px solid #e2e8f0;border-radius:12px;transition:all .2s;} .leo-trust .leo-badge:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(15,23,42,.12);border-color:#16a34a;} .leo-trust .leo-badge .leo-badge-name{display:block;font-size:18px;font-weight:800;color:#0f172a;} .leo-trust .leo-badge .leo-badge-label{display:block;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-top:6px;} .leo-timeline{display:grid;grid-template-columns:repeat(6,1fr);gap:24px;position:relative;} .leo-timeline:before{content:"";position:absolute;top:32px;left:8%;right:8%;height:2px;background:#e2e8f0;z-index:0;} .leo-step{position:relative;text-align:center;z-index:1;} .leo-step-num{display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:50%;background:#fff;color:#f97316;border:3px solid #f97316;font-size:22px;font-weight:800;margin-bottom:16px;transition:all .2s;} .leo-step:hover .leo-step-num{background:#f97316;color:#fff;transform:scale(1.05);} .leo-step h4{font-size:16px;font-weight:700;margin:0 0 8px;color:#0f172a;} .leo-step p{font-size:14px;color:#64748b;margin:0;line-height:1.5;} .leo-testimonials{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;} .leo-testimonial{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:32px;transition:all .2s;} .leo-testimonial:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(15,23,42,.12);border-color:#16a34a;} .leo-stars{color:#fbbf24;font-size:18px;letter-spacing:2px;margin-bottom:16px;} .leo-quote{font-size:15px;line-height:1.7;color:#1e293b;margin-bottom:24px;font-style:italic;} .leo-author{display:flex;align-items:center;gap:12px;} .leo-author-init{width:44px;height:44px;border-radius:50%;background:#16a34a;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;} .leo-author-name{font-weight:700;color:#0f172a;font-size:13px;} .leo-author-meta{color:#64748b;font-size:13px;} .leo-blog-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:32px;} .leo-blog-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;transition:all .2s;text-decoration:none;color:inherit;display:block;} .leo-blog-card:hover{transform:translateY(-4px);box-shadow:0 8px 24px rgba(15,23,42,.12);border-color:#16a34a;} .leo-blog-card .leo-blog-body{padding:24px;} .leo-blog-tag{display:inline-block;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#16a34a;margin-bottom:12px;} .leo-blog-card h3{font-size:18px;font-weight:700;margin:0 0 12px;color:#0f172a;line-height:1.4;} .leo-blog-card p{font-size:14px;color:#64748b;margin:0;line-height:1.5;} .leo-cta-strip{padding:96px 24px;background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;text-align:center;} .leo-cta-strip h2{font-size:42px;font-weight:800;margin:0 0 16px;color:#fff;} .leo-cta-strip p{font-size:18px;color:#cbd5e1;max-width:640px;margin:0 auto 40px;line-height:1.6;} .leo-cta-buttons{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;} .leo-btn{display:inline-flex;align-items:center;padding:16px 32px;border-radius:8px;font-weight:600;font-size:16px;text-decoration:none;transition:all .2s;} .leo-btn-cta{background:#f97316;color:#fff;box-shadow:0 4px 16px rgba(249,115,22,.4);} .leo-btn-cta:hover{background:#ea580c;transform:translateY(-2px);} .leo-btn-wa{background:#25D366;color:#fff;} .leo-btn-wa:hover{background:#1ea952;transform:translateY(-2px);} @media(max-width:768px){.leo-trust{grid-template-columns:repeat(3,1fr);}.leo-timeline{grid-template-columns:1fr;gap:32px;}.leo-timeline:before{display:none;}.leo-testimonials{grid-template-columns:1fr;}.leo-blog-grid{grid-template-columns:1fr;}.leo-cta-strip h2{font-size:30px;}} @media(max-width:1024px){.leo-testimonials{grid-template-columns:1fr 1fr;}.leo-blog-grid{grid-template-columns:1fr 1fr;}} </style>';
    $html .= '<section class="leo-section leo-bg-alt" id="leo-trust-badges"> <div class="leo-container"> <p class="leo-eyebrow">Certifications</p> <h2>Built to Pass Every Compliance Audit</h2> <p class="leo-section-lead">Every electronic smart pet product we ship holds the certifications required by your destination market. Test reports from accredited labs available on request.</p> <div class="leo-trust"> <div class="leo-badge"><span class="leo-badge-name">CE</span><span class="leo-badge-label">European Union</span></div> <div class="leo-badge"><span class="leo-badge-name">FCC</span><span class="leo-badge-label">United States</span></div> <div class="leo-badge"><span class="leo-badge-name">UKCA</span><span class="leo-badge-label">United Kingdom</span></div> <div class="leo-badge"><span class="leo-badge-name">PSE</span><span class="leo-badge-label">Japan</span></div> <div class="leo-badge"><span class="leo-badge-name">ROHS</span><span class="leo-badge-label">EU and global</span></div> <div class="leo-badge"><span class="leo-badge-name">ISO 9001</span><span class="leo-badge-label">Quality system</span></div> </div></div></section>';
    $html .= '<section class="leo-section" id="leo-oem-timeline"> <div class="leo-container"> <p class="leo-eyebrow">From Brief to Container</p> <h2>Our 6-Step OEM Process</h2> <p class="leo-section-lead">A predictable factory-direct workflow that takes your idea from a written brief to a shipped container in 60 to 90 days for OEM and 45 days for ODM.</p> <div class="leo-timeline"> <div class="leo-step"><div class="leo-step-num">1</div><h4>Inquiry</h4><p>Share your specs, target volume and destination market.</p></div> <div class="leo-step"><div class="leo-step-num">2</div><h4>Design</h4><p>Our R and D team turns your brief into CAD files and a feasibility quote.</p></div> <div class="leo-step"><div class="leo-step-num">3</div><h4>Sampling</h4><p>Pre-production sample shipped within 15 to 20 days for your approval.</p></div> <div class="leo-step"><div class="leo-step-num">4</div><h4>Production</h4><p>Mass production on a dedicated line with weekly progress reports.</p></div> <div class="leo-step"><div class="leo-step-num">5</div><h4>QC and Testing</h4><p>100% function test plus AQL 2.5 inspection before packing.</p></div> <div class="leo-step"><div class="leo-step-num">6</div><h4>Shipping</h4><p>FOB Ningbo, CIF or DDP to your warehouse worldwide.</p></div> </div> <p style="text-align:center;margin-top:48px;"><a class="leo-btn leo-btn-cta" href="/oem-odm-services/">See the full OEM/ODM services</a></p> </div></section>';
    $html .= '<section class="leo-section leo-bg-alt" id="leo-testimonials"> <div class="leo-container"> <p class="leo-eyebrow">Verified Reviews</p> <h2>5 Stars on Alibaba Trade Assurance</h2> <p class="leo-section-lead">Real B2B buyers from Italy, Singapore, South Korea, Australia and India. Every review left after a confirmed Alibaba purchase order. <a href="/buyer-reviews/" style="color:#16a34a;">Read all reviews</a>.</p> <div class="leo-testimonials"> <div class="leo-testimonial"><div class="leo-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p class="leo-quote">"Everything is perfect with this automatic litter. My two cats love it and I need to take out the trash only once a week. I really recommend it, especially because this one has the hidden drawer which is more elegant than other litters."</p><div class="leo-author"><div class="leo-author-init">JM</div><div><div class="leo-author-name">Jasmin Movahedian</div><div class="leo-author-meta">Italy</div></div></div></div> <div class="leo-testimonial"><div class="leo-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p class="leo-quote">"Since getting this automatic litter box, my three cats litter area has become so much cleaner and tidier. What I like most is its large capacity and automatic cleaning function, eliminating the need for manual scooping."</p><div class="leo-author"><div class="leo-author-init">AG</div><div><div class="leo-author-name">Arno Gregary</div><div class="leo-author-meta">South Korea</div></div></div></div> <div class="leo-testimonial"><div class="leo-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div><p class="leo-quote">"It is better than the description. The dogs do not splash in the water. Definitely recommend it."</p><div class="leo-author"><div class="leo-author-init">JD</div><div><div class="leo-author-name">Justin Davidson</div><div class="leo-author-meta">Australia</div></div></div></div> </div></div></section>';
    $html .= '<section class="leo-section" id="leo-blog-preview"> <div class="leo-container"> <p class="leo-eyebrow">Industry Insights</p> <h2>Latest from the Eviehome B2B Blog</h2> <p class="leo-section-lead">Sourcing guides, OEM playbooks and market reports for importers and private-label brands. Updated weekly.</p> <div class="leo-blog-grid"> <a class="leo-blog-card" href="/complete-guide-sourcing-smart-pet-products-china/"><div class="leo-blog-body"><span class="leo-blog-tag">Sourcing and Import</span><h3>The Complete Guide to Sourcing Smart Pet Products from China</h3><p>The full B2B framework for sourcing from Chinese factories: discovery, due diligence, MOQs, landed cost calculation and common mistakes.</p></div></a> <a class="leo-blog-card" href="/automatic-cat-litter-box-b2b-buyers-guide/"><div class="leo-blog-body"><span class="leo-blog-tag">Cat Litter Boxes</span><h3>Automatic Cat Litter Box: The Definitive B2B Buyers Guide</h3><p>The 3 families of automatic cat litter boxes, the 10 specs that matter, unit cost breakdown and private label options.</p></div></a> <a class="leo-blog-card" href="/oem-vs-odm-pet-products-complete-guide/"><div class="leo-blog-body"><span class="leo-blog-tag">OEM and ODM</span><h3>OEM vs ODM for Pet Products: Everything You Need to Know</h3><p>The difference in practical B2B terms, cost and timeline differences, when to choose which, hybrid approaches and IP protection.</p></div></a> </div></div></section>';
    $html .= '<section class="leo-cta-strip" id="leo-final-cta"> <h2>Ready to Build Your Smart Pet Product Line?</h2> <p>Get a custom quote within 24 hours. MOQ from 500 units. CE, FCC, ROHS certified. Factory-direct pricing in Hefei, China.</p> <div class="leo-cta-buttons"> <a class="leo-btn leo-btn-cta" href="/contact-us/">Get a Free Quote</a> <a class="leo-btn leo-btn-wa" href="https://wa.me/8619956530913?text=Hi,%20I%20am%20interested%20in%20your%20smart%20pet%20products" target="_blank" rel="noopener">WhatsApp Us</a> </div> </section>';
    return $html;
}
