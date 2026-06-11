<?php
declare(strict_types=1);
/**
 * Almaretna — AJAX: immagini hero destinazioni per il mega-menu
 */
defined('ABSPATH') || exit;

add_action('wp_ajax_alm_nav_destinations',        'alm_ajax_nav_destinations');
add_action('wp_ajax_nopriv_alm_nav_destinations', 'alm_ajax_nav_destinations');

function alm_ajax_nav_destinations(): void {
    $slugs  = ['taormina', 'etna', 'catania', 'siracusa', 'messina', 'mare-di-sicilia'];
    $images = [];

    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if (!$page) continue;
        $hero_id = (int) get_post_meta($page->ID, '_alm_dest_hero', true);
        if ($hero_id) {
            $url = wp_get_attachment_image_url($hero_id, 'medium_large');
            if ($url) $images[$slug] = $url;
        }
    }

    wp_send_json_success($images);
}
