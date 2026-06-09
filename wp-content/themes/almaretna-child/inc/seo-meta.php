<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — SEO meta tags
 *
 * - Meta description, canonical, robots
 * - Open Graph (con locale dinamico per lingua corrente)
 * - Twitter Card
 * - Hreflang per IT/EN/DE/FR/ES
 * - Sitemap rooms
 *
 * Non si sovrappone a Yoast/RankMath se presenti.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ── Mappa locale per lingua ───────────────────────────────────────────────────

define('ALM_LANG_LOCALE', [
    'it' => 'it_IT',
    'en' => 'en_GB',
    'de' => 'de_DE',
    'fr' => 'fr_FR',
    'es' => 'es_ES',
]);

// ── Robots — output PRIMA di altri meta ──────────────────────────────────────

add_action('wp_head', function (): void {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

    // Indicizza le immagini a dimensione grande nelle SERP visive
    echo '<meta name="robots" content="max-image-preview:large" />' . "\n";

    $meta = alm_build_seo_meta();
    if (!empty($meta['robots'])) {
        printf('<meta name="robots" content="%s" />' . "\n", esc_attr($meta['robots']));
    }
}, 3);

// ── Hreflang ─────────────────────────────────────────────────────────────────

/**
 * Hreflang per IT/EN/DE/FR/ES.
 * IT e x-default puntano all'URL senza parametro lang.
 * EN/DE/FR/ES aggiungono ?lang=xx.
 */
add_action('wp_head', function (): void {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

    $base = alm_get_canonical_base();

    $langs = [
        'it' => 'it-IT',
        'en' => 'en-GB',
        'de' => 'de-DE',
        'fr' => 'fr-FR',
        'es' => 'es-ES',
    ];

    // x-default = versione italiana (URL pulito)
    printf('<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url($base));

    foreach ($langs as $code => $hreflang) {
        $url = ($code === 'it') ? $base : add_query_arg('lang', $code, $base);
        printf(
            '<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
            esc_attr($hreflang),
            esc_url($url)
        );
    }
}, 4);

// ── Meta SEO principali ───────────────────────────────────────────────────────

add_action('wp_head', 'alm_output_seo_meta', 5);

function alm_output_seo_meta(): void {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) return;

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

    // Locale OG basato sulla lingua corrente
    $current_lang  = function_exists('alm_get_lang') ? alm_get_lang() : 'it';
    $og_locale     = ALM_LANG_LOCALE[$current_lang] ?? 'it_IT';

    // Open Graph
    alm_meta_og('og:type',        $meta['og_type']    ?? 'website');
    alm_meta_og('og:site_name',   'Almaretna');
    alm_meta_og('og:title',       $meta['title']       ?? '');
    alm_meta_og('og:description', $meta['description'] ?? '');
    alm_meta_og('og:url',         $meta['canonical']   ?? '');
    alm_meta_og('og:locale',      $og_locale);

    // og:image con dimensioni per preview social ottimale
    if (!empty($meta['image'])) {
        alm_meta_og('og:image',        $meta['image']);
        alm_meta_og('og:image:secure_url', $meta['image']);
        alm_meta_og('og:image:width',  '1200');
        alm_meta_og('og:image:height', '630');
        alm_meta_og('og:image:alt',    $meta['title'] ?? 'Almaretna');
    }

    // og:locale:alternate per le altre lingue
    foreach (ALM_LANG_LOCALE as $locale) {
        if ($locale !== $og_locale) {
            alm_meta_og('og:locale:alternate', $locale);
        }
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    alm_meta_name('twitter:title',       $meta['title']       ?? '');
    alm_meta_name('twitter:description', $meta['description'] ?? '');
    alm_meta_name('twitter:image',       $meta['image']       ?? '');
    alm_meta_name('twitter:site',        '@almaretna');
}

// ── Builder meta per pagina ───────────────────────────────────────────────────

function alm_build_seo_meta(): array {

    // Singola camera
    if (is_singular('almaretna_room')) {
        return alm_room_seo_meta();
    }

    // Home
    if (is_front_page()) {
        return [
            'title'       => 'Almaretna — Villa con piscina alle pendici dell\'Etna | Sicilia',
            'description' => 'Prenota il tuo soggiorno ad Almaretna: villa esclusiva con piscina a Nunziata di Mascali, tra Taormina e Catania. Vista sull\'Etna, camere eleganti, miglior prezzo garantito.',
            'canonical'   => home_url('/'),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
        ];
    }

    // Pagina Camere / archivio
    if (is_page('camere') || is_post_type_archive('almaretna_room')) {
        return [
            'title'       => 'Camere — Almaretna | Villa con piscina sull\'Etna',
            'description' => 'Sette camere eleganti ad Almaretna: suite con vista Etna, mansarda romantica, suite famiglia. Piscina esclusiva, parcheggio gratuito. Prenota direttamente al miglior prezzo.',
            'canonical'   => (string) (get_post_type_archive_link('almaretna_room') ?: get_permalink(get_page_by_path('camere'))),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
        ];
    }

    // Pagina Prenota (noindex: la checkout non deve essere indicizzata)
    if (is_page('prenota')) {
        return [
            'title'       => 'Prenota — Almaretna | Miglior prezzo garantito',
            'description' => 'Prenota direttamente ad Almaretna: verifica disponibilità, pagamento sicuro con Stripe, miglior prezzo garantito.',
            'canonical'   => (string) (get_permalink(get_page_by_path('prenota')) ?: home_url('/prenota/')),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
            'robots'      => 'noindex, follow',
        ];
    }

    // Pagine legali (noindex: boilerplate, non utile in SERP)
    global $post;
    $legal_slugs = ['privacy-policy', 'cookie-policy', 'termini-condizioni', 'diritto-di-recesso'];
    if ($post instanceof WP_Post && in_array($post->post_name, $legal_slugs, true)) {
        return [
            'title'       => get_the_title($post) . ' — Almaretna',
            'description' => strip_tags(has_excerpt($post) ? get_the_excerpt($post) : wp_trim_words($post->post_content, 25, '…')),
            'canonical'   => (string) get_permalink($post),
            'og_type'     => 'website',
            'image'       => alm_seo_default_image(),
            'robots'      => 'noindex, follow',
        ];
    }

    // Pagina generica
    if ($post instanceof WP_Post) {
        return [
            'title'       => get_the_title($post) . ' — Almaretna',
            'description' => has_excerpt($post)
                ? strip_tags(get_the_excerpt($post))
                : wp_trim_words(strip_tags($post->post_content), 30, '…'),
            'canonical'   => (string) get_permalink($post),
            'og_type'     => 'article',
            'image'       => get_the_post_thumbnail_url($post, 'large') ?: alm_seo_default_image(),
        ];
    }

    return [];
}

// ── SEO singola camera ────────────────────────────────────────────────────────

function alm_room_seo_meta(): array {
    global $post;
    if (!$post) return [];

    $meta       = alm_get_room_meta($post->ID);
    $room_type  = alm_get_room_type($post->ID);
    $adults     = (int) ($meta['capacity_adults'] ?? 2);
    $base_price = number_format((float) ($meta['base_price'] ?? 0), 0, ',', '.');

    $room_type_name = $room_type ? $room_type->name : 'Camera elegante';
    $desc = sprintf(
        '%s — %s ad Almaretna. Fino a %d ospiti, da €%s/notte. Piscina esclusiva, vista sull\'Etna. Prenota al miglior prezzo garantito.',
        get_the_title($post),
        $room_type_name,
        $adults,
        $base_price
    );

    return [
        'title'       => get_the_title($post) . ' — Almaretna | Villa sull\'Etna, Sicilia',
        'description' => $desc,
        'canonical'   => (string) get_permalink($post),
        'og_type'     => 'product',
        'image'       => alm_get_room_thumbnail_url($post->ID, 'large') ?: alm_seo_default_image(),
    ];
}

// ── Title tag ─────────────────────────────────────────────────────────────────

add_filter('document_title_parts', 'alm_filter_document_title');

function alm_filter_document_title(array $title): array {
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION')) {
        return $title;
    }

    if (is_front_page()) {
        $title['title']   = 'Almaretna — Villa con piscina alle pendici dell\'Etna | Sicilia';
        $title['site']    = '';
        $title['tagline'] = '';
        return $title;
    }

    if (is_singular('almaretna_room')) {
        $title['title'] = get_the_title() . ' — Almaretna | Villa sull\'Etna';
        $title['site']  = '';
        return $title;
    }

    if (is_page('camere') || is_post_type_archive('almaretna_room')) {
        $title['title'] = 'Camere — Almaretna | Villa con piscina sull\'Etna';
        $title['site']  = '';
        return $title;
    }

    if (is_page('prenota')) {
        $title['title'] = 'Prenota — Almaretna | Miglior prezzo garantito';
        $title['site']  = '';
        return $title;
    }

    // Tutte le altre pagine: "Titolo — Almaretna"
    if (is_page()) {
        global $post;
        if ($post instanceof WP_Post) {
            $title['title'] = get_the_title($post) . ' — Almaretna';
            $title['site']  = '';
        }
    }

    return $title;
}

// ── Sitemap: includi camere ───────────────────────────────────────────────────

add_filter('wp_sitemaps_posts_query_args', function (array $args, string $post_type): array {
    if ($post_type === 'almaretna_room') {
        $args['public'] = true;
    }
    return $args;
}, 10, 2);

// Escludi dalla sitemap le pagine noindex
add_filter('wp_sitemaps_posts_entry', function (array $sitemap_entry, WP_Post $post): array {
    $exclude = ['privacy-policy', 'cookie-policy', 'termini-condizioni', 'diritto-di-recesso', 'prenota'];
    if (in_array($post->post_name, $exclude, true)) {
        // Ritorna array vuoto per saltare questa entry
        return [];
    }
    return $sitemap_entry;
}, 10, 2);

// ── Utility ───────────────────────────────────────────────────────────────────

function alm_meta_og(string $property, string $content): void {
    if (!$content) return;
    printf('<meta property="%s" content="%s" />' . "\n", esc_attr($property), esc_attr($content));
}

function alm_meta_name(string $name, string $content): void {
    if (!$content) return;
    printf('<meta name="%s" content="%s" />' . "\n", esc_attr($name), esc_attr($content));
}

/**
 * URL canonico base della pagina corrente (senza ?lang).
 */
function alm_get_canonical_base(): string {
    if (is_front_page()) return home_url('/');
    if (is_singular())   return (string) get_permalink();
    if (is_post_type_archive('almaretna_room')) {
        return (string) (get_post_type_archive_link('almaretna_room') ?: home_url('/camere/'));
    }
    global $post;
    return ($post instanceof WP_Post) ? (string) get_permalink($post) : home_url('/');
}

/**
 * Immagine OG di default: prima dalla libreria media (attachment con slug "og-default"),
 * poi logo del tema, poi prima thumbnail di una camera.
 */
function alm_seo_default_image(): string {
    // 1. Immagine dedicata con slug "og-default" nella libreria media
    $og = get_page_by_path('og-default', OBJECT, 'attachment');
    if ($og instanceof WP_Post) {
        return (string) wp_get_attachment_url($og->ID);
    }

    // 2. Logo del tema
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) return (string) wp_get_attachment_url($logo_id);

    // 3. Prima thumbnail trovata nelle camere
    $rooms = get_posts(['post_type' => 'almaretna_room', 'posts_per_page' => 1, 'fields' => 'ids']);
    if (!empty($rooms) && has_post_thumbnail($rooms[0])) {
        return (string) get_the_post_thumbnail_url($rooms[0], 'large');
    }

    return '';
}
