<?php
declare(strict_types=1);

/**
 * Almaretna Child — Helper functions globali
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

/**
 * Restituisce tutti i meta della camera come array associativo.
 *
 * @param int $post_id ID del post almaretna_room.
 * @return array<string, mixed>
 */
function alm_get_room_meta(int $post_id): array {
    return [
        'room_id'            => get_post_meta($post_id, '_room_id', true),
        'capacity_adults'    => (int) get_post_meta($post_id, '_room_capacity_adults', true),
        'capacity_children'  => (int) get_post_meta($post_id, '_room_capacity_children', true),
        'base_price'         => (float) get_post_meta($post_id, '_room_base_price', true),
        'floor'              => get_post_meta($post_id, '_room_floor', true),
        'bathroom_type'      => get_post_meta($post_id, '_room_bathroom_type', true) ?: 'private',
        'shared_with'        => get_post_meta($post_id, '_room_shared_with', true),
        'beds24_id'          => get_post_meta($post_id, '_room_beds24_id', true),
        'is_family_unit'     => (bool) get_post_meta($post_id, '_room_is_family_unit', true),
        'family_unit_rooms'  => alm_decode_family_unit_rooms($post_id),
        'min_stay'           => (int) (get_post_meta($post_id, '_room_min_stay', true) ?: 1),
        'active'             => (bool) get_post_meta($post_id, '_room_active', true),
    ];
}

/**
 * Decodifica il meta _room_family_unit_rooms (JSON string) in array.
 *
 * @param int $post_id
 * @return string[]
 */
function alm_decode_family_unit_rooms(int $post_id): array {
    $raw = get_post_meta($post_id, '_room_family_unit_rooms', true);
    if (empty($raw)) {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Formatta un prezzo con simbolo euro.
 *
 * @param float $price
 * @return string  Es. "€ 90,00"
 */
function alm_format_price(float $price): string {
    return '&euro;&nbsp;' . number_format($price, 2, ',', '.');
}

/**
 * Restituisce l'URL dell'immagine in evidenza della camera.
 *
 * @param int    $post_id
 * @param string $size  Dimensione immagine WordPress.
 * @return string  URL immagine o stringa vuota se non impostata.
 */
function alm_get_room_thumbnail_url(int $post_id, string $size = 'large'): string {
    if (!has_post_thumbnail($post_id)) {
        return '';
    }
    $src = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
    return $src ? (string) $src[0] : '';
}

/**
 * Restituisce la classe dashicon corrispondente a una dotazione (amenity slug).
 *
 * @param string $amenity_slug
 * @return string  Classe dashicons (es. "dashicons-admin-home").
 */
function alm_get_amenity_icon(string $amenity_slug): string {
    $icons = [
        'bagno-privato'      => 'dashicons-admin-home',
        'bagno-condiviso'    => 'dashicons-groups',
        'vista-mare'         => 'dashicons-visibility',
        'vista-etna'         => 'dashicons-image-filter',
        'aria-condizionata'  => 'dashicons-cloud',
        'riscaldamento'      => 'dashicons-heating',
        'wifi'               => 'dashicons-wifi',
        'frigorifero'        => 'dashicons-store',
        'cassaforte'         => 'dashicons-lock',
        'parcheggio'         => 'dashicons-car',
        'piscina'            => 'dashicons-water',
    ];
    return $icons[$amenity_slug] ?? 'dashicons-yes-alt';
}

/**
 * Restituisce l'etichetta leggibile per uno slug di piano.
 *
 * @param string $floor_slug
 * @return string
 */
function alm_get_floor_label(string $floor_slug): string {
    $labels = [
        'piano-1'  => alm_t('h.floor_1'),
        'piano-2'  => alm_t('h.floor_2'),
        'mansarda' => alm_t('h.floor_m'),
    ];
    return $labels[$floor_slug] ?? ucfirst(str_replace('-', ' ', $floor_slug));
}

/**
 * Esegue una WP_Query sulle camere con filtri opzionali.
 *
 * @param array<string, mixed> $args  Argomenti aggiuntivi per WP_Query.
 * @return WP_Query
 */
function alm_rooms_query(array $args = []): WP_Query {
    $defaults = [
        'post_type'      => 'almaretna_room',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => '_room_active',
                'value'   => '1',
                'compare' => '=',
            ],
            [
                'key'     => '_room_active',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ];

    $query_args = wp_parse_args($args, $defaults);

    // Filtro per tipo camera
    if (!empty($args['room_type'])) {
        $query_args['tax_query'][] = [
            'taxonomy' => 'room_type',
            'field'    => 'slug',
            'terms'    => $args['room_type'],
        ];
        unset($query_args['room_type']);
    }

    // Filtro per piano
    if (!empty($args['room_floor'])) {
        $query_args['tax_query'][] = [
            'taxonomy' => 'room_floor',
            'field'    => 'slug',
            'terms'    => $args['room_floor'],
        ];
        unset($query_args['room_floor']);
    }

    // Filtro per capacità adulti
    if (!empty($args['adults'])) {
        $query_args['meta_query'][] = [
            'key'     => '_room_capacity_adults',
            'value'   => (int) $args['adults'],
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ];
        unset($query_args['adults']);
    }

    if (isset($query_args['tax_query']) && count($query_args['tax_query']) > 1) {
        $query_args['tax_query']['relation'] = 'AND';
    }

    return new WP_Query($query_args);
}

/**
 * Restituisce le dotazioni di una camera come array di term WP_Term.
 *
 * @param int $post_id
 * @return WP_Term[]
 */
function alm_get_room_amenities(int $post_id): array {
    $terms = get_the_terms($post_id, 'amenity');
    return (is_array($terms) && !is_wp_error($terms)) ? $terms : [];
}

/**
 * Restituisce il tipo di camera (room_type) come WP_Term o null.
 *
 * @param int $post_id
 * @return WP_Term|null
 */
function alm_get_room_type(int $post_id): ?WP_Term {
    $terms = get_the_terms($post_id, 'room_type');
    if (!is_array($terms) || is_wp_error($terms) || empty($terms)) {
        return null;
    }
    return $terms[0];
}

/**
 * Restituisce il piano della camera (room_floor) come WP_Term o null.
 *
 * @param int $post_id
 * @return WP_Term|null
 */
function alm_get_room_floor(int $post_id): ?WP_Term {
    $terms = get_the_terms($post_id, 'room_floor');
    if (!is_array($terms) || is_wp_error($terms) || empty($terms)) {
        return null;
    }
    return $terms[0];
}

/**
 * Verifica se una camera ha bagno condiviso.
 *
 * @param int $post_id
 * @return bool
 */
function alm_room_has_shared_bathroom(int $post_id): bool {
    return get_post_meta($post_id, '_room_bathroom_type', true) === 'shared';
}

/**
 * Restituisce la nota sul bagno condiviso da mostrare agli ospiti.
 *
 * @param int $post_id
 * @return string  Stringa vuota se bagno privato.
 */
function alm_get_bathroom_sharing_note(int $post_id): string {
    if (!alm_room_has_shared_bathroom($post_id)) {
        return '';
    }
    $shared_with_id = get_post_meta($post_id, '_room_shared_with', true);
    if (!empty($shared_with_id)) {
        $partner = alm_get_post_by_room_id($shared_with_id);
        if ($partner) {
            return sprintf(
                alm_t('h.shared_bath_with'),
                esc_html(get_the_title($partner->ID))
            );
        }
    }
    return alm_t('h.shared_bath');
}

/**
 * Restituisce il campo testuale della camera tradotto nella lingua corrente.
 *
 * Legge da meta `_room_{field}_{lang}` (es. `_room_excerpt_en`).
 * Se assente o vuoto, torna al campo WordPress nativo (IT).
 *
 * @param int    $post_id
 * @param string $field   'title' | 'excerpt' | 'content'
 * @param string $lang    Lingua esplicita; vuoto = lingua corrente.
 * @return string
 */
function alm_get_room_translated(int $post_id, string $field, string $lang = ''): string {
    $lang = $lang ?: alm_get_lang();

    if ($lang !== 'it') {
        $meta = get_post_meta($post_id, "_room_{$field}_{$lang}", true);
        if (!empty($meta)) {
            return (string) $meta;
        }
    }

    // Fallback: valore nativo WordPress (IT)
    $post = get_post($post_id);
    if (!$post) return '';

    switch ($field) {
        case 'title':
            return $post->post_title;
        case 'excerpt':
            if (!empty($post->post_excerpt)) {
                return $post->post_excerpt;
            }
            // Se excerpt vuoto, genera da content
            return wp_trim_words(wp_strip_all_tags($post->post_content), 25, '…');
        case 'content':
            return $post->post_content;
        default:
            return '';
    }
}

/**
 * Cerca un post almaretna_room per il meta _room_id.
 *
 * @param string $room_id  Es. "P2-M01"
 * @return WP_Post|null
 */
function alm_get_post_by_room_id(string $room_id): ?WP_Post {
    $posts = get_posts([
        'post_type'      => 'almaretna_room',
        'posts_per_page' => 1,
        'meta_query'     => [[
            'key'     => '_room_id',
            'value'   => $room_id,
            'compare' => '=',
        ]],
    ]);
    return !empty($posts) ? $posts[0] : null;
}
