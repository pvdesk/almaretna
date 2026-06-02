<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — SEO meta tags
 *
 * Aggiunge Open Graph, Twitter Card e meta description
 * per le pagine principali e le singole camere.
 * Non si sovrappone ad Yoast/RankMath se presenti.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

/**
 * Inietta i meta tag SEO nel <head> se non c'è un plugin SEO attivo.
 */
function alm_output_seo_meta(): void {
    // Non sovrascrivere se Yoast o RankMath è attivo
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return;
    }

    $meta = alm_build_seo_meta();
    if (empty($meta)) return;

    // Meta description
    if (!empty($meta['description'])) {
        printf(
            '<meta name="description" content="%s" />' . "\n",
            esc_attr($meta['description'])
        );
    }

    // Canonical
    if (!empty($meta['canonical'])) {
        printf('<link rel="canonical" href="%s" />' . "\n", esc_url($meta['canonical']));
    }

    // Open Graph
    alm_meta_og('og:type',        $meta['og_type']    ?? 'website');
    alm_meta_og('og:site_name',   'Almaretna');
    alm_meta_og('og:title',       $meta['title']       ?? '');
    alm_meta_og('og:description', $meta['description'] ?? '');
    alm_meta_og('og:url',         $meta['canonical']   ?? '');
    alm_meta_og('og:image',       $meta['image']       ?? '');
    alm_meta_og('og:locale',      'it_IT');

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    alm_meta_name('twitter:title',       $meta['title']       ?? '');
    alm_meta_name('twitter:description', $meta['description'] ?? '');
    alm_meta_name('twitter:image',       $meta['image']       ?? '');
}
add_action('wp_head', 'alm_output_seo_meta', 5);

// ── Builder meta per pagina ──────────────────────────────────────────────────

function alm_build_seo_meta(): array {
    // Singola camera
    if (is_singular('almaretna_room')) {
        return alm_room_seo_meta();
    }

    // Home
    if (is_front_page()) {
        return [
            'title'       => 'Almaretna — Villa con piscina sull\'Etna | Nunziata di Mascali',
            'description' => 'Prenota il tuo soggiorno ad Almaretna: villa con piscina alle pendici dell\'Etna, camere eleganti, colazioni artigianali e vista sul vulcano.',
            'canonical'   => home_url('/'),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
        ];
    }

    // Pagina Camere
    if (is_page('camere') || is_post_type_archive('almaretna_room')) {
        return [
            'title'       => 'Le nostre camere — Almaretna | Villa sull\'Etna',
            'description' => 'Scopri le camere di Almaretna: suite con vista Etna, mansarda romantica e suite famiglia con piscina privata. Prenota direttamente.',
            'canonical'   => get_post_type_archive_link('almaretna_room') ?: get_permalink(get_page_by_path('camere')),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
        ];
    }

    // Pagina Prenota
    if (is_page('prenota')) {
        return [
            'title'       => 'Prenota — Almaretna | Miglior prezzo garantito',
            'description' => 'Prenota direttamente su Almaretna: miglior prezzo garantito, pagamento sicuro con Stripe, cancellazione flessibile.',
            'canonical'   => get_permalink(get_page_by_path('prenota')) ?: home_url('/prenota/'),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
            'robots'      => 'noindex',  // La pagina checkout non serve nell'indice
        ];
    }

    // Pagina generica con title/description
    global $post;
    if ($post instanceof WP_Post) {
        $title = get_the_title($post) . ' — Almaretna';
        $desc  = has_excerpt($post)
            ? strip_tags(get_the_excerpt($post))
            : wp_trim_words(strip_tags($post->post_content), 30, '…');
        return [
            'title'       => $title,
            'description' => $desc,
            'canonical'   => get_permalink($post),
            'og_type'     => 'article',
            'image'       => get_the_post_thumbnail_url($post, 'large') ?: alm_seo_default_image(),
        ];
    }

    return [];
}

function alm_room_seo_meta(): array {
    global $post;
    if (!$post) return [];

    $meta       = alm_get_room_meta($post->ID);
    $room_type  = alm_get_room_type($post->ID);
    $floor      = alm_get_floor_label(get_post_meta($post->ID, '_room_floor', true) ?? '');
    $adults     = (int) ($meta['capacity_adults']   ?? 2);
    $base_price = number_format((float) ($meta['base_price'] ?? 0), 0, ',', '.');

    $desc = sprintf(
        '%s — %s. Fino a %d adulti, da €%s/notte. Prenota direttamente su Almaretna per il miglior prezzo.',
        get_the_title($post),
        $room_type ? $room_type->name : 'Camera elegante',
        $adults,
        $base_price
    );

    return [
        'title'     => get_the_title($post) . ' — Almaretna | Villa sull\'Etna',
        'description'=> $desc,
        'canonical'  => get_permalink($post),
        'og_type'    => 'product',
        'image'      => alm_get_room_thumbnail_url($post->ID, 'large') ?: alm_seo_default_image(),
    ];
}

// ── Robots meta per pagine noindex ───────────────────────────────────────────

function alm_output_robots_meta(): void {
    $meta = alm_build_seo_meta();
    if (!empty($meta['robots'])) {
        printf('<meta name="robots" content="%s" />' . "\n", esc_attr($meta['robots']));
    }
}
add_action('wp_head', 'alm_output_robots_meta', 4);

// ── Title tag ────────────────────────────────────────────────────────────────

/**
 * Filtra il title tag di WordPress per le pagine chiave.
 */
function alm_filter_document_title(array $title): array {
    // Solo se non c'è plugin SEO
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return $title;
    }

    if (is_singular('almaretna_room')) {
        $title['title'] = get_the_title() . ' — Almaretna';
        $title['site']  = '';
    }

    return $title;
}
add_filter('document_title_parts', 'alm_filter_document_title');

// ── Sitemap: aggiungi camere ─────────────────────────────────────────────────

/**
 * Assicura che le camere siano incluse nella sitemap nativa di WordPress (WP 5.5+).
 */
function alm_sitemap_add_rooms(array $args, string $post_type): array {
    if ($post_type === 'almaretna_room') {
        $args['public'] = true;
    }
    return $args;
}
add_filter('wp_sitemaps_posts_query_args', 'alm_sitemap_add_rooms', 10, 2);

// ── Utility ──────────────────────────────────────────────────────────────────

function alm_meta_og(string $property, string $content): void {
    if (!$content) return;
    printf('<meta property="%s" content="%s" />' . "\n", esc_attr($property), esc_attr($content));
}

function alm_meta_name(string $name, string $content): void {
    if (!$content) return;
    printf('<meta name="%s" content="%s" />' . "\n", esc_attr($name), esc_attr($content));
}

function alm_seo_default_image(): string {
    // Restituisce l'immagine di default del sito (custom logo o prima immagine disponibile)
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) return (string) wp_get_attachment_url($logo_id);

    // Fallback: prima immagine trovata nelle camere
    $rooms = get_posts(['post_type' => 'almaretna_room', 'posts_per_page' => 1, 'fields' => 'ids']);
    if (!empty($rooms) && has_post_thumbnail($rooms[0])) {
        return (string) get_the_post_thumbnail_url($rooms[0], 'large');
    }

    return '';
}
