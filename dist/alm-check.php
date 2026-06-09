<?php
/**
 * Almaretna — Diagnostica server
 * Carica in wp-content/ ed apri da browser. ELIMINA dopo l'uso.
 */
if (!isset($_GET['go'])) {
    echo '<form><input type="hidden" name="go" value="1"><button>Esegui diagnostica</button></form>';
    exit;
}

require_once dirname(__DIR__) . '/wp-load.php';
global $wpdb;

header('Content-Type: text/plain; charset=utf-8');

$p = $wpdb->prefix;
echo "=== ALMARETNA DIAGNOSTICA SERVER ===\n";
echo "Data: " . date('Y-m-d H:i:s') . "\n";
echo "Prefisso tabelle WP: {$p}\n";
echo "siteurl: " . get_option('siteurl') . "\n";
echo "home:    " . get_option('home') . "\n\n";

// 1. Tabelle plugin
echo "=== TABELLE PLUGIN ===\n";
foreach (['alm_bookings','alm_blocks','alm_rates','alm_extras','alm_sync_log','alm_booking_extras'] as $t) {
    $full  = $p . $t;
    $exist = $wpdb->get_var("SHOW TABLES LIKE '{$full}'");
    $count = $exist ? (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$full}`") : -1;
    echo ($exist ? "✓" : "✗") . " {$full}" . ($exist ? " ({$count} righe)" : " — MANCANTE") . "\n";
}

// 2. Camere
echo "\n=== CAMERE (CPT almaretna_room) ===\n";
$rooms = $wpdb->get_results(
    "SELECT ID, post_title, post_status FROM {$p}posts WHERE post_type='almaretna_room' ORDER BY ID"
);
if ($rooms) {
    foreach ($rooms as $r) echo "ID={$r->ID} [{$r->post_status}] {$r->post_title}\n";
} else {
    echo "✗ NESSUNA CAMERA TROVATA\n";
}

// 3. Pagine chiave
echo "\n=== PAGINE CHIAVE ===\n";
foreach (['camere','prenota','conferma-prenotazione'] as $slug) {
    $pg = $wpdb->get_row("SELECT ID, post_status FROM {$p}posts WHERE post_name='{$slug}' AND post_type='page' LIMIT 1");
    if ($pg) echo "✓ /{$slug}/ (ID={$pg->ID}, status={$pg->post_status})\n";
    else      echo "✗ /{$slug}/ — MANCANTE\n";
}

// 4. Plugin attivo
echo "\n=== PLUGIN ATTIVO ===\n";
$active = get_option('active_plugins', []);
$found  = in_array('almaretna-booking/almaretna-booking.php', $active);
echo ($found ? "✓" : "✗") . " almaretna-booking " . ($found ? "ATTIVO" : "NON ATTIVO") . "\n";

// 5. Tema attivo
echo "\n=== TEMA ===\n";
echo "Tema attivo: " . get_option('stylesheet') . "\n";
echo "Template:   " . get_option('template') . "\n";

// 6. Meta camere (se esistono)
if ($rooms) {
    echo "\n=== META CAMERE ===\n";
    foreach ($rooms as $r) {
        $active_meta = get_post_meta($r->ID, '_room_active', true);
        $price       = get_post_meta($r->ID, '_room_capacity_adults', true);
        $min_stay    = get_post_meta($r->ID, '_room_min_stay', true);
        echo "ID={$r->ID}: _room_active=" . var_export($active_meta, true)
           . " capacity_adults={$price} min_stay={$min_stay}\n";
    }
}

// 7. Tabelle WP standard
echo "\n=== TABELLE WP (sanity check) ===\n";
foreach (['posts','postmeta','options','terms','term_taxonomy'] as $t) {
    $full  = $p . $t;
    $exist = $wpdb->get_var("SHOW TABLES LIKE '{$full}'");
    $count = $exist ? (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$full}`") : -1;
    echo ($exist ? "✓" : "✗") . " {$full}" . ($exist ? " ({$count} righe)" : " — MANCANTE") . "\n";
}

echo "\n=== FINE DIAGNOSTICA ===\n";
