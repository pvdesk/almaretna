<?php
/**
 * Setup menu primario — assegna/crea il menu alla location "primary".
 *
 * USAGE: http://tuo-sito.it/setup-menu.php?key=almaretna_setup_2024
 * DOPO L'ESECUZIONE: eliminare questo file.
 */

$secret = 'almaretna_setup_2024';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Accesso negato.');
}

define('ABSPATH', __DIR__ . '/');
require_once __DIR__ . '/wp-load.php';

$log = [];

/* ── 1. Trova o crea il menu "Menu Principale" ─────────────────────────────── */
$menu_name = 'Menu Principale';
$menu      = wp_get_nav_menu_object($menu_name);

if (!$menu) {
    $menu_id = wp_create_nav_menu($menu_name);
    if (is_wp_error($menu_id)) {
        die('Errore creazione menu: ' . $menu_id->get_error_message());
    }
    $log[] = "✅ Menu <strong>{$menu_name}</strong> creato (ID {$menu_id}).";
    $menu  = wp_get_nav_menu_object($menu_name);
} else {
    $menu_id = $menu->term_id;
    $log[]   = "⚠️ Menu <strong>{$menu_name}</strong> già esistente (ID {$menu_id}) — aggiorno le voci.";
}

/* ── 2. Definisci le voci del menu ──────────────────────────────────────────── */
// Recupera pagine per template o slug
$find_page = function (string $slug): ?WP_Post {
    return get_page_by_path($slug) ?: null;
};

$find_by_tpl = function (string $tpl): ?WP_Post {
    $pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => $tpl, 'number' => 1]);
    return !empty($pages) ? $pages[0] : null;
};

$items_def = [
    [
        'title'  => 'Home',
        'url'    => home_url('/'),
        'type'   => 'custom',
        'order'  => 1,
    ],
    [
        'page'   => $find_by_tpl('templates/template-rooms.php') ?? $find_page('camere'),
        'title'  => 'Camere',
        'order'  => 2,
    ],
    [
        'page'  => $find_page('scopri-la-sicilia'),
        'title' => 'Scopri la Sicilia',
        'order' => 3,
    ],
    [
        'page'  => $find_by_tpl('templates/template-booking.php') ?? $find_page('prenota'),
        'title' => 'Prenota',
        'order' => 4,
    ],
];

/* ── 3. Rimuovi le voci esistenti del menu per ripartire pulito ─────────────── */
$existing_items = wp_get_nav_menu_items($menu_id) ?: [];
foreach ($existing_items as $item) {
    wp_delete_post($item->ID, true);
}

/* ── 4. Aggiungi le voci ────────────────────────────────────────────────────── */
foreach ($items_def as $def) {
    if (isset($def['type']) && $def['type'] === 'custom') {
        $item_id = wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'    => $def['title'],
            'menu-item-url'      => $def['url'],
            'menu-item-type'     => 'custom',
            'menu-item-status'   => 'publish',
            'menu-item-position' => $def['order'],
        ]);
        $log[] = is_wp_error($item_id)
            ? "❌ Errore voce custom <strong>{$def['title']}</strong>: " . $item_id->get_error_message()
            : "✅ Aggiunta voce custom: <strong>{$def['title']}</strong>";
        continue;
    }

    if (empty($def['page'])) {
        $log[] = "⚠️ Pagina <strong>{$def['title']}</strong> non trovata — saltata.";
        continue;
    }

    $item_id = wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'     => $def['title'],
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $def['page']->ID,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
        'menu-item-position'  => $def['order'],
    ]);
    $log[] = is_wp_error($item_id)
        ? "❌ Errore voce <strong>{$def['title']}</strong>: " . $item_id->get_error_message()
        : "✅ Aggiunta voce: <strong>{$def['title']}</strong> (page ID {$def['page']->ID})";
}

/* ── 5. Assegna alla location "primary" ─────────────────────────────────────── */
$locations                = get_theme_mod('nav_menu_locations', []);
$locations['primary']     = $menu_id;
set_theme_mod('nav_menu_locations', $locations);
$log[] = "✅ Menu assegnato alla location <strong>primary</strong>.";

?><!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>Setup menu — Almaretna</title>
<style>
body{font-family:system-ui,sans-serif;max-width:700px;margin:48px auto;padding:0 24px;color:#333}
h1{font-size:1.5rem;margin-bottom:8px}
.note{background:#fff3cd;border:1px solid #ffc107;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:.9rem}
ul{list-style:none;padding:0;display:flex;flex-direction:column;gap:10px}
li{padding:12px 16px;border-radius:8px;border:1px solid #e5e5e5;font-size:.95rem}
</style>
</head>
<body>
<h1>Setup menu primario — Almaretna</h1>
<div class="note">⚠️ <strong>Elimina questo file dopo l'esecuzione!</strong><br>
Percorso: <code><?php echo esc_html(__FILE__); ?></code></div>
<ul>
<?php foreach ($log as $r) : ?>
    <li><?php echo $r; ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
