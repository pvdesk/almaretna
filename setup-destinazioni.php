<?php
/**
 * Setup script — crea le pagine destinazione in WordPress.
 *
 * USAGE: visitare http://tuo-sito.it/setup-destinazioni.php?key=almaretna_setup_2024
 * DOPO L'ESECUZIONE: eliminare questo file.
 */

$secret = 'almaretna_setup_2024';
if (($_GET['key'] ?? '') !== $secret) {
    http_response_code(403);
    die('Accesso negato. Aggiungere ?key= corretto.');
}

define('ABSPATH', __DIR__ . '/');
require_once __DIR__ . '/wp-load.php';

if (!current_user_can('manage_options') && !defined('WP_CLI')) {
    die('Devi essere loggato come admin.');
}

$pages = [
    [
        'post_title'   => 'Scopri la Sicilia',
        'post_name'    => 'scopri-la-sicilia',
        'post_content' => '',
        'template'     => 'page-scopri-la-sicilia.php',
    ],
    [
        'post_title'   => 'Taormina',
        'post_name'    => 'taormina',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
    [
        'post_title'   => 'Etna',
        'post_name'    => 'etna',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
    [
        'post_title'   => 'Catania',
        'post_name'    => 'catania',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
    [
        'post_title'   => 'Siracusa',
        'post_name'    => 'siracusa',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
    [
        'post_title'   => 'Messina',
        'post_name'    => 'messina',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
    [
        'post_title'   => 'Il mare di Sicilia',
        'post_name'    => 'mare-di-sicilia',
        'post_content' => '',
        'template'     => 'page-destinazione.php',
    ],
];

$results = [];

foreach ($pages as $p) {
    $existing = get_page_by_path($p['post_name']);
    if ($existing) {
        $results[] = "⚠️ Già esiste: <strong>{$p['post_title']}</strong> (ID {$existing->ID}) — saltato.";
        continue;
    }

    $id = wp_insert_post([
        'post_title'   => $p['post_title'],
        'post_name'    => $p['post_name'],
        'post_content' => $p['post_content'],
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_author'  => 1,
    ], true);

    if (is_wp_error($id)) {
        $results[] = "❌ Errore creando <strong>{$p['post_title']}</strong>: " . esc_html($id->get_error_message());
        continue;
    }

    update_post_meta($id, '_wp_page_template', $p['template']);
    $url = get_permalink($id);
    $results[] = "✅ Creata: <strong>{$p['post_title']}</strong> (ID {$id}) — <a href=\"{$url}\" target=\"_blank\">{$url}</a>";
}

?><!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8">
<title>Setup destinazioni — Almaretna</title>
<style>
body{font-family:system-ui,sans-serif;max-width:700px;margin:48px auto;padding:0 24px;color:#333}
h1{font-size:1.5rem;margin-bottom:8px}
.note{background:#fff3cd;border:1px solid #ffc107;padding:12px 16px;border-radius:8px;margin-bottom:24px;font-size:.9rem}
ul{list-style:none;padding:0;display:flex;flex-direction:column;gap:10px}
li{padding:12px 16px;border-radius:8px;border:1px solid #e5e5e5;font-size:.95rem}
</style>
</head>
<body>
<h1>Setup pagine destinazione — Almaretna</h1>
<div class="note">⚠️ <strong>Elimina questo file dopo l'esecuzione!</strong><br>
Percorso: <code><?php echo esc_html(__FILE__); ?></code></div>
<ul>
<?php foreach ($results as $r) : ?>
    <li><?php echo $r; ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
