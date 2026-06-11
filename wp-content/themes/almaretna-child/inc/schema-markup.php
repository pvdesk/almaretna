<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — Schema Markup (JSON-LD)
 *
 * Structured data:
 * - LodgingBusiness      → home, camere, archivio
 * - HotelRoom            → single camera
 * - BreadcrumbList       → tutte le pagine interne
 * - WebSite + SearchAction → home
 * - WebPage              → pagine legali
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'alm_output_schema_markup');

function alm_output_schema_markup(): void {
    if (is_singular('almaretna_room')) {
        alm_schema_hotel_room();
        alm_schema_breadcrumb();

    } elseif (is_front_page() || is_home()) {
        alm_schema_lodging_business();
        alm_schema_website_search_action();

    } elseif (is_page('camere') || is_post_type_archive('almaretna_room')) {
        alm_schema_lodging_business();
        alm_schema_breadcrumb();

    } elseif (is_page()) {
        alm_schema_breadcrumb();
        if (alm_is_legal_page()) {
            alm_schema_webpage();
        }
    }
}

// ── LodgingBusiness ───────────────────────────────────────────────────────────

function alm_schema_lodging_business(): void {
    $settings   = (array) get_option('alm_booking_settings', []);
    $num_rooms  = (int) wp_count_posts('almaretna_room')->publish;

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'LodgingBusiness',
        '@id'         => home_url('/#lodging'),
        'name'        => 'Almaretna',
        'alternateName' => 'Almaretna Villa Vacanze',
        'description' => 'Villa con piscina a Nunziata di Mascali, alle pendici dell\'Etna. Sette camere eleganti, piscina panoramica 12×5 m, parcheggio privato e vista sulla costa jonica.',
        'url'         => home_url('/'),
        'telephone'   => $settings['host_phone'] ?? '+393332621974',
        'email'       => $settings['host_email'] ?? 'info@almaretna.it',
        'image'       => alm_schema_get_logo_url(),
        'logo'        => [
            '@type' => 'ImageObject',
            'url'   => alm_schema_get_logo_url(),
        ],
        'address' => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Via Scorciavacca Montarsi, 48',
            'addressLocality' => 'Nunziata di Mascali',
            'addressRegion'   => 'CT',
            'postalCode'      => '95016',
            'addressCountry'  => 'IT',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => '37.7638',
            'longitude' => '15.2167',
        ],
        'hasMap'             => 'https://maps.google.com/?q=Via+Scorciavacca+Montarsi+48+Nunziata+di+Mascali',
        'numberOfRooms'      => $num_rooms ?: 7,
        'priceRange'         => '€€',
        'currenciesAccepted' => 'EUR',
        'paymentAccepted'    => 'Credit Card, Debit Card',
        'checkinTime'        => 'T15:00:00',
        'checkoutTime'       => 'T11:00:00',
        'amenityFeature' => [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Piscina', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Wi-Fi gratuito', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Aria condizionata', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Parcheggio gratuito', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Bar bordo piscina', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Piscina esterna', 'value' => true],
        ],
        'aggregateRating' => [
            '@type'       => 'AggregateRating',
            'ratingValue' => '4.9',
            'bestRating'  => '5',
            'worstRating' => '1',
            'ratingCount' => '47',
        ],
        'sameAs' => array_values(array_filter([
            get_option('alm_schema_facebook_url',  ''),
            get_option('alm_schema_instagram_url', ''),
        ])),
    ];

    alm_print_json_ld($schema);
}

// ── HotelRoom (single camera) ─────────────────────────────────────────────────

function alm_schema_hotel_room(): void {
    global $post;
    if (!$post) return;

    $meta = alm_get_room_meta($post->ID);
    if (empty($meta)) return;

    $base_price    = (float) ($meta['base_price'] ?? 0);
    $thumb_url     = alm_get_room_thumbnail_url($post->ID, 'large');
    $amenity_terms = alm_get_room_amenities($post->ID);

    $amenity_features = array_map(function (WP_Term $term): array {
        return [
            '@type' => 'LocationFeatureSpecification',
            'name'  => $term->name,
            'value' => true,
        ];
    }, $amenity_terms);

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'HotelRoom',
        'name'        => get_the_title($post),
        'description' => get_the_excerpt($post),
        'url'         => (string) get_permalink($post),
        'image'       => $thumb_url ?: alm_schema_get_logo_url(),
        'containedInPlace' => [
            '@type' => 'LodgingBusiness',
            '@id'   => home_url('/#lodging'),
            'name'  => 'Almaretna',
        ],
        'occupancy' => [
            '@type'    => 'QuantitativeValue',
            'minValue' => 1,
            'maxValue' => (int) ($meta['capacity_adults'] ?? 2) + (int) ($meta['capacity_children'] ?? 0),
        ],
        'bed'            => $meta['bed_type'] ?? null,
        'amenityFeature' => $amenity_features,
        'petsAllowed'    => false,
        'smokingAllowed' => false,
        'offers' => [
            '@type'         => 'Offer',
            'price'         => $base_price,
            'priceCurrency' => 'EUR',
            'priceSpecification' => [
                '@type'         => 'UnitPriceSpecification',
                'price'         => $base_price,
                'priceCurrency' => 'EUR',
                'unitCode'      => 'DAY',
                'unitText'      => 'notte',
            ],
            'availability' => 'https://schema.org/InStock',
            'validFrom'    => gmdate('Y-m-d'),
            'url'          => (string) (get_permalink(get_page_by_path('prenota')) ?: home_url('/prenota/')),
            'seller'       => [
                '@type' => 'LodgingBusiness',
                '@id'   => home_url('/#lodging'),
                'name'  => 'Almaretna',
            ],
        ],
    ];

    // Rimuove chiavi null
    $schema = array_filter($schema, fn($v) => !is_null($v));

    alm_print_json_ld($schema);
}

// ── BreadcrumbList ────────────────────────────────────────────────────────────

/**
 * Struttura breadcrumb per rich snippet nelle SERP.
 * Home > ... > Pagina corrente
 */
function alm_schema_breadcrumb(): void {
    if (is_front_page()) return;

    $items = [[
        '@type'    => 'ListItem',
        'position' => 1,
        'name'     => 'Home',
        'item'     => home_url('/'),
    ]];

    if (is_singular('almaretna_room')) {
        $camere_url = (string) (get_post_type_archive_link('almaretna_room') ?: home_url('/camere/'));
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => 'Camere',
            'item'     => $camere_url,
        ];
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 3,
            'name'     => get_the_title(),
            'item'     => (string) get_permalink(),
        ];

    } elseif (is_post_type_archive('almaretna_room')) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => 'Camere',
            'item'     => (string) get_post_type_archive_link('almaretna_room'),
        ];

    } elseif (is_page()) {
        global $post;
        if (!$post instanceof WP_Post) return;

        // Pagina figlia: Home > Genitore > Pagina
        if ($post->post_parent) {
            $parent = get_post($post->post_parent);
            if ($parent instanceof WP_Post) {
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => get_the_title($parent),
                    'item'     => (string) get_permalink($parent),
                ];
                $items[] = [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => get_the_title($post),
                    'item'     => (string) get_permalink($post),
                ];
            }
        } else {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title($post),
                'item'     => (string) get_permalink($post),
            ];
        }
    }

    if (count($items) < 2) return;

    alm_print_json_ld([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ]);
}

// ── WebPage (pagine legali) ───────────────────────────────────────────────────

function alm_schema_webpage(): void {
    global $post;
    if (!$post instanceof WP_Post) return;

    alm_print_json_ld([
        '@context'      => 'https://schema.org',
        '@type'         => 'WebPage',
        'name'          => get_the_title($post) . ' — Almaretna',
        'url'           => (string) get_permalink($post),
        'description'   => strip_tags(has_excerpt($post) ? get_the_excerpt($post) : ''),
        'datePublished' => get_the_date('c', $post),
        'dateModified'  => get_the_modified_date('c', $post),
        'inLanguage'    => 'it-IT',
        'isPartOf'      => [
            '@type' => 'WebSite',
            'url'   => home_url('/'),
            'name'  => 'Almaretna',
        ],
        'publisher' => [
            '@type' => 'LodgingBusiness',
            '@id'   => home_url('/#lodging'),
            'name'  => 'Almaretna',
        ],
    ]);
}

/**
 * Controlla se la pagina corrente è una pagina legale.
 */
function alm_is_legal_page(): bool {
    global $post;
    if (!$post instanceof WP_Post) return false;
    return in_array($post->post_name, [
        'privacy-policy', 'cookie-policy', 'termini-condizioni', 'diritto-di-recesso',
    ], true);
}

// ── WebSite / SearchAction ────────────────────────────────────────────────────

function alm_schema_website_search_action(): void {
    alm_print_json_ld([
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'Almaretna',
        'url'      => home_url('/'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url('/camere/?search={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ]);
}

// ── Utility ───────────────────────────────────────────────────────────────────

function alm_print_json_ld(array $schema): void {
    echo '<script type="application/ld+json">' . "\n";
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}

function alm_schema_get_logo_url(): string {
    $logo_id = get_theme_mod('custom_logo');
    return $logo_id ? (string) wp_get_attachment_url($logo_id) : '';
}
