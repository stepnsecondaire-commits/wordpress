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
    // REST API JSON body is NOT magic-quoted, so we MUST NOT call wp_unslash here.
    // However update_post_meta() pipes its value through wp_unslash internally,
    // which would strip backslashes. We pre-slash to compensate, so the round trip
    // is a no-op for binary-safe JSON content.
    update_post_meta( $post_id, $body['key'], wp_slash( $body['value'] ) );
    return [ 'success' => true, 'post_id' => $post_id, 'key' => $body['key'] ];
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
        [
            'author'      => 'Jasmin Movahedian',
            'country'     => 'Italy',
            'rating'      => 5,
            'product'     => 'OEM Electric Smart Quick Self Cleaning Deodorant Cat Litter Box',
            'body'        => "Everything is perfect with this automatic litter. My two cats love it and I need to take out the trash only once a week. I really recommend it, especially because this one has the hidden drawer which is more elegant than other litters.",
        ],
        [
            'author'      => 'Arno Gregary',
            'country'     => 'South Korea',
            'rating'      => 5,
            'product'     => 'OEM Cat Litter Box Self Cleaning Smart Cat Litter Box',
            'body'        => "Since getting this automatic litter box, my three cats litter area has become so much cleaner and tidier. What I like most is its large capacity and automatic cleaning function, eliminating the need for manual scooping. The app allows me to view usage data and my cats littering habits.",
        ],
        [
            'author'      => 'Justin Davidson',
            'country'     => 'Australia',
            'rating'      => 5,
            'product'     => 'Smart Pet Water Dispenser 304 Stainless Steel Cat and Dog Fountain',
            'body'        => "It is better than the description. The dogs do not splash in the water. Definitely recommend it.",
        ],
        [
            'author'      => 'Mohammad Mohsin',
            'country'     => 'India',
            'rating'      => 5,
            'product'     => 'OEM Cat Litter Box Self Cleaning Smart Cat Litter Box',
            'body'        => "The product is very good value for money, quiet operation, no smell in the room.",
        ],
        [
            'author'      => 'user XVII',
            'country'     => 'Singapore',
            'rating'      => 5,
            'product'     => 'Automatic Cat Litter Box with Smart WiFi for Multi-Cat Households',
            'body'        => "Nice job.",
        ],
        [
            'author'      => 'user XVII',
            'country'     => 'Singapore',
            'rating'      => 5,
            'product'     => '304 Stainless Steel Motion Sensor Pet Fountain',
            'body'        => "Perfect.",
        ],
        [
            'author'      => 'user XVII',
            'country'     => 'Singapore',
            'rating'      => 5,
            'product'     => 'Automatic Dog Feeder Camera 1080P Live Streaming',
            'body'        => "Perfect.",
        ],
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
    // Apply other HTML transformations through the leo_ob_html filter chain
    $html = apply_filters( 'leo_ob_html', $html );
    return $html;
}
