<?php
declare(strict_types=1);

/**
 * Almaretna — Custom XML Sitemap
 *
 * Disponibile su: /sitemap.xml
 * - Pagine pubbliche (escluse quelle noindex)
 * - Camere (CPT almaretna_room)
 * - hreflang IT / EN / DE / FR / ES + x-default per ogni URL
 *
 * Disabilita la sitemap nativa di WordPress (/wp-sitemap.xml).
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ── Disabilita sitemap nativa WP (usiamo la nostra) ───────────────────────────

add_filter('wp_sitemaps_enabled', '__return_false');

// ── Intercetta GET /sitemap.xml ───────────────────────────────────────────────

add_action('template_redirect', 'alm_serve_sitemap', 1);

function alm_serve_sitemap(): void {
    if (!isset($_SERVER['REQUEST_URI'])) return;

    $path = trim((string) parse_url(
        (string) $_SERVER['REQUEST_URI'],  // phpcs:ignore
        PHP_URL_PATH
    ), '/');

    if ($path !== 'sitemap.xml') return;

    status_header(200);
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=7200');   // 2 ore
    header('X-Robots-Tag: noindex, follow');

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo alm_build_sitemap_xml();
    exit;
}

// ── Generatore XML ────────────────────────────────────────────────────────────

function alm_build_sitemap_xml(): string {

    // Pagine da escludere (noindex / non utili per Google)
    $exclude = [
        'prenota',
        'privacy-policy',
        'cookie-policy',
        'termini-condizioni',
        'diritto-di-recesso',
        'cancellazione',
        'le-mie-prenotazioni',
    ];

    // Priorità esplicite per pagine chiave
    $priorities = [
        'camere'           => '0.9',
        'struttura'        => '0.7',
        'scopri-la-sicilia'=> '0.8',
        'taormina'         => '0.7',
        'etna'             => '0.7',
        'catania'          => '0.7',
        'siracusa'         => '0.7',
        'messina'          => '0.7',
        'mare-di-sicilia'  => '0.7',
        'dove-siamo'       => '0.6',
        'contatti'         => '0.6',
    ];

    // Mappa lingua → hreflang
    $langs = [
        'it' => 'it-IT',
        'en' => 'en-GB',
        'de' => 'de-DE',
        'fr' => 'fr-FR',
        'es' => 'es-ES',
    ];

    $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

    // Helper: aggiunge una <url> con hreflang
    $add = function (
        string $url,
        string $lastmod,
        string $changefreq,
        string $priority
    ) use (&$xml, $langs): void {

        // URL canonico (senza ?lang, con trailing slash)
        $base = trailingslashit(remove_query_arg('lang', $url));

        $xml .= "\t<url>\n";
        $xml .= "\t\t<loc>"          . esc_xml($base)       . "</loc>\n";
        $xml .= "\t\t<lastmod>"      . esc_html($lastmod)   . "</lastmod>\n";
        $xml .= "\t\t<changefreq>"   . esc_html($changefreq). "</changefreq>\n";
        $xml .= "\t\t<priority>"     . esc_html($priority)  . "</priority>\n";

        // x-default = versione italiana (URL pulito)
        $xml .= "\t\t<xhtml:link rel=\"alternate\" hreflang=\"x-default\""
              . " href=\"" . esc_xml($base) . "\" />\n";

        foreach ($langs as $code => $hreflang) {
            $href = ($code === 'it') ? $base : add_query_arg('lang', $code, $base);
            $xml .= "\t\t<xhtml:link rel=\"alternate\""
                  . " hreflang=\"" . esc_attr($hreflang) . "\""
                  . " href=\"" . esc_xml($href) . "\" />\n";
        }

        $xml .= "\t</url>\n";
    };

    // ── Home ──────────────────────────────────────────────────────────────────
    $front_id  = (int) get_option('page_on_front');
    $front_mod = $front_id
        ? gmdate('Y-m-d', strtotime((get_post($front_id))->post_modified_gmt ?: 'now'))
        : gmdate('Y-m-d');
    $add(home_url('/'), $front_mod, 'weekly', '1.0');

    // ── Pagine ────────────────────────────────────────────────────────────────
    $pages = get_pages([
        'post_status'  => 'publish',
        'sort_column'  => 'menu_order',
        'hierarchical' => false,
    ]);

    foreach ($pages as $page) {
        if (in_array($page->post_name, $exclude, true)) continue;
        if ($page->ID === $front_id) continue;           // home già aggiunta

        $add(
            (string) get_permalink($page),
            gmdate('Y-m-d', strtotime($page->post_modified_gmt ?: 'now')),
            'monthly',
            $priorities[$page->post_name] ?? '0.5'
        );
    }

    // ── Camere ────────────────────────────────────────────────────────────────
    $rooms = get_posts([
        'post_type'      => 'almaretna_room',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ]);

    foreach ($rooms as $room) {
        $add(
            (string) get_permalink($room),
            gmdate('Y-m-d', strtotime($room->post_modified_gmt ?: 'now')),
            'weekly',
            '0.8'
        );
    }

    $xml .= '</urlset>';
    return $xml;
}
