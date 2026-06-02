<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — Schema Markup (JSON-LD)
 *
 * Aggiunge structured data:
 * - LodgingBusiness sulla home e pagine generali
 * - HotelRoom sulle single camera (già nel template, questo aggiunte globali)
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

/**
 * Output del JSON-LD nel <head> tramite wp_head.
 */
function alm_output_schema_markup(): void {
    if (is_singular('almaretna_room')) {
        alm_schema_hotel_room();
    } elseif (is_front_page() || is_home()) {
        alm_schema_lodging_business();
        alm_schema_website_search_action();
    } elseif (is_page('camere') || is_post_type_archive('almaretna_room')) {
        alm_schema_lodging_business();
    }
}
add_action('wp_head', 'alm_output_schema_markup');

// ── LodgingBusiness ──────────────────────────────────────────────────────────

function alm_schema_lodging_business(): void {
    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'LodgingBusiness',
        '@id'         => home_url('/#lodging'),
        'name'        => 'Almaretna',
        'description' => 'Villa con piscina a Nunziata di Mascali, alle pendici dell\'Etna. Camere eleganti, piscina panoramica e colazioni artigianali.',
        'url'         => home_url('/'),
        'telephone'   => get_option('alm_booking_settings')['host_phone'] ?? '',
        'email'       => get_option('alm_booking_settings')['host_email'] ?? get_option('admin_email', ''),
        'image'       => alm_schema_get_logo_url(),
        'address'     => [
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
        'hasMap'     => 'https://maps.google.com/?q=Via+Scorciavacca+Montarsi+48+Nunziata+di+Mascali',
        'priceRange' => '€€',
        'checkinTime'  => 'T15:00:00',
        'checkoutTime' => 'T11:00:00',
        'amenityFeature' => [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Piscina', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Wi-Fi gratuito', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Aria condizionata', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Parcheggio gratuito', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Colazione inclusa', 'value' => true],
        ],
        'sameAs' => array_filter([
            get_option('alm_schema_facebook_url',  ''),
            get_option('alm_schema_instagram_url', ''),
        ]),
    ];

    alm_print_json_ld($schema);
}

// ── HotelRoom (single camera) ─────────────────────────────────────────────────

function alm_schema_hotel_room(): void {
    global $post;
    if (!$post) return;

    $meta = alm_get_room_meta($post->ID);
    if (empty($meta)) return;

    $base_price   = (float) ($meta['base_price'] ?? 0);
    $thumb_url    = alm_get_room_thumbnail_url($post->ID, 'large');
    $amenity_terms= alm_get_room_amenities($post->ID);

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
        'url'         => get_permalink($post),
        'image'       => $thumb_url ?: alm_schema_get_logo_url(),
        'containedInPlace' => [
            '@type' => 'LodgingBusiness',
            '@id'   => home_url('/#lodging'),
            'name'  => 'Almaretna',
        ],
        'occupancy' => [
            '@type'     => 'QuantitativeValue',
            'minValue'  => 1,
            'maxValue'  => (int) ($meta['capacity_adults'] ?? 2) + (int) ($meta['capacity_children'] ?? 0),
        ],
        'amenityFeature'   => $amenity_features,
        'petsAllowed'      => false,
        'smokingAllowed'   => false,
        'offers' => [
            '@type'         => 'Offer',
            'price'         => $base_price,
            'priceCurrency' => 'EUR',
            'priceSpecification' => [
                '@type'     => 'UnitPriceSpecification',
                'price'     => $base_price,
                'priceCurrency' => 'EUR',
                'unitCode'  => 'DAY',
            ],
            'availability'  => 'https://schema.org/InStock',
            'validFrom'     => gmdate('Y-m-d'),
            'url'           => get_permalink(get_page_by_path('prenota')) ?: home_url('/prenota/'),
        ],
    ];

    alm_print_json_ld($schema);
}

// ── WebSite / SearchAction ────────────────────────────────────────────────────

function alm_schema_website_search_action(): void {
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => 'WebSite',
        'name'            => 'Almaretna',
        'url'             => home_url('/'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url('/camere/?search={search_term_string}'),
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];

    alm_print_json_ld($schema);
}

// ── Utility ──────────────────────────────────────────────────────────────────

function alm_print_json_ld(array $schema): void {
    echo '<script type="application/ld+json">' . "\n";
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n" . '</script>' . "\n";
}

function alm_schema_get_logo_url(): string {
    $logo_id = get_theme_mod('custom_logo');
    return $logo_id ? wp_get_attachment_url($logo_id) : '';
}
