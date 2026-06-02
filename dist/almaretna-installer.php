<?php
/**
 * Almaretna — One-Click Installer
 *
 * Carica questo file TEMPORANEAMENTE in wp-content/almaretna-installer.php
 * e aprilo da browser: https://tuosito.it/wp-content/almaretna-installer.php
 *
 * ⚠️  ELIMINA IL FILE DOPO L'INSTALLAZIONE per sicurezza.
 *
 * Cosa fa:
 *   1. Scarica (o legge da disco) almaretna-child.zip e almaretna-booking.zip
 *   2. Installa il tema figlio Astra (se non già presente)
 *   3. Installa + attiva il plugin almaretna-booking
 *   4. Attiva il tema child
 *   5. Crea le pagine base: Home, Camere, Prenota, Chi siamo, Contatti
 *   6. Configura le impostazioni WordPress di base
 *   7. Mostra una checklist finale con le istruzioni post-installazione
 */

declare(strict_types=1);

// ─── Sicurezza: token di accesso ──────────────────────────────────────────────
// Imposta una password nella URL per evitare esecuzioni non autorizzate:
//   https://tuosito.it/wp-content/almaretna-installer.php?token=TUO_TOKEN_SEGRETO

define('INSTALLER_TOKEN', 'almaretna-setup-2024'); // ← CAMBIA QUESTO TOKEN

$token_in = $_GET['token'] ?? '';
if (!hash_equals(INSTALLER_TOKEN, $token_in)) {
    http_response_code(403);
    echo '<h1>403 Forbidden</h1><p>Token mancante o errato. Aggiungi <code>?token=almaretna-setup-2024</code></p>';
    exit;
}

// ─── Bootstrap WordPress ─────────────────────────────────────────────────────

define('ABSPATH_OVERRIDE', dirname(__DIR__) . '/');
if (!defined('ABSPATH')) {
    require_once ABSPATH_OVERRIDE . 'wp-load.php';
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/theme.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';

if (!current_user_can('manage_options') && !defined('WP_CLI')) {
    wp_die('Devi essere loggato come amministratore.');
}

$log   = [];
$steps = [];

// ─── Helper log ──────────────────────────────────────────────────────────────

function alm_log(string $msg, bool $ok = true): void {
    global $log;
    $log[] = ['msg' => $msg, 'ok' => $ok];
    echo ($ok ? '✓ ' : '✗ ') . $msg . "\n";
    flush();
}

// ─── Step 1: verifica Astra ──────────────────────────────────────────────────

$astra_installed = file_exists(get_theme_root() . '/astra/style.css');
if (!$astra_installed) {
    // Installa Astra dal repository WordPress
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    $skin     = new Automatic_Upgrader_Skin();
    $upgrader = new Theme_Upgrader($skin);
    $result   = $upgrader->install('https://downloads.wordpress.org/theme/astra.latest-stable.zip');
    alm_log('Tema Astra installato', !is_wp_error($result));
} else {
    alm_log('Tema Astra già presente');
}

// ─── Step 2: installa tema child ─────────────────────────────────────────────

$child_installed = file_exists(get_theme_root() . '/almaretna-child/style.css');
if (!$child_installed) {
    $child_zip = __DIR__ . '/almaretna-child.zip';
    if (file_exists($child_zip)) {
        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Theme_Upgrader($skin);
        $result   = $upgrader->install($child_zip);
        alm_log('Tema child installato da file locale', !is_wp_error($result));
    } else {
        alm_log('almaretna-child.zip non trovato — copia il file in wp-content/', false);
    }
} else {
    alm_log('Tema child già installato');
}

// ─── Step 3: attiva tema child ───────────────────────────────────────────────

$active_theme = get_stylesheet();
if ($active_theme !== 'almaretna-child') {
    switch_theme('almaretna-child');
    alm_log('Tema child attivato');
} else {
    alm_log('Tema child già attivo');
}

// ─── Step 4: installa + attiva plugin booking ─────────────────────────────────

$plugin_slug = 'almaretna-booking/almaretna-booking.php';
$plugin_dir  = WP_PLUGIN_DIR . '/almaretna-booking/almaretna-booking.php';

if (!file_exists($plugin_dir)) {
    $booking_zip = __DIR__ . '/almaretna-booking.zip';
    if (file_exists($booking_zip)) {
        $skin     = new Automatic_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $result   = $upgrader->install($booking_zip);
        alm_log('Plugin almaretna-booking installato', !is_wp_error($result));
    } else {
        alm_log('almaretna-booking.zip non trovato — copia il file in wp-content/', false);
    }
}

if (file_exists($plugin_dir) && !is_plugin_active($plugin_slug)) {
    $activated = activate_plugin($plugin_slug);
    alm_log('Plugin almaretna-booking attivato', !is_wp_error($activated));
} elseif (is_plugin_active($plugin_slug)) {
    alm_log('Plugin almaretna-booking già attivo');
}

// ─── Step 5: crea pagine base ─────────────────────────────────────────────────

$pages = [
    'home'       => ['title' => 'Home',         'template' => ''],
    'camere'     => ['title' => 'Le Nostre Camere', 'template' => 'templates/template-rooms.php'],
    'prenota'    => ['title' => 'Prenota',       'template' => 'templates/template-booking.php'],
    'chi-siamo'  => ['title' => 'Chi Siamo',     'template' => ''],
    'contatti'   => ['title' => 'Contatti',      'template' => ''],
    'privacy-policy' => ['title' => 'Privacy Policy', 'template' => ''],
];

foreach ($pages as $slug => $page) {
    $existing = get_page_by_path($slug);
    if (!$existing) {
        $page_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ]);
        if ($page_id && !empty($page['template'])) {
            update_post_meta($page_id, '_wp_page_template', $page['template']);
        }
        alm_log("Pagina '{$page['title']}' creata (ID: {$page_id})");
    } else {
        alm_log("Pagina '{$page['title']}' già esistente");
    }
}

// ─── Step 6: imposta homepage statica ─────────────────────────────────────────

$home_page = get_page_by_path('home');
if ($home_page) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_page->ID);
    alm_log('Homepage impostata su pagina "Home"');
}

// ─── Step 7: impostazioni WordPress base ─────────────────────────────────────

update_option('blogname',        'Almaretna');
update_option('blogdescription', 'Villa Vacanze con piscina — Nunziata di Mascali, Etna');
update_option('permalink_structure', '/%postname%/');
update_option('timezone_string', 'Europe/Rome');
update_option('date_format',     'd/m/Y');
update_option('time_format',     'H:i');
update_option('start_of_week',   '1');
alm_log('Impostazioni base WordPress configurate');

// ─── Step 8: crea menu di navigazione ────────────────────────────────────────

$menu_name    = 'Menu Principale';
$menu_exists  = wp_get_nav_menu_object($menu_name);
if (!$menu_exists) {
    $menu_id = wp_create_nav_menu($menu_name);
    $pages_order = ['home', 'camere', 'prenota', 'chi-siamo', 'contatti'];
    foreach ($pages_order as $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            wp_update_nav_menu_item($menu_id, 0, [
                'menu-item-title'     => $page->post_title,
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page->ID,
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
            ]);
        }
    }
    $locations = get_theme_mod('nav_menu_locations', []);
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
    alm_log('Menu di navigazione creato e assegnato');
} else {
    alm_log('Menu principale già esistente');
}

// ─── Riepilogo finale HTML ────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Almaretna Installer</title>
<style>
    body { font-family: system-ui, sans-serif; max-width: 720px; margin: 40px auto; padding: 0 20px; background: #FCFAF7; }
    h1 { color: #A6957E; }
    .log { background: #fff; border: 1px solid #EFEBE4; border-radius: 8px; padding: 20px; margin: 20px 0; }
    .log li { padding: 6px 0; border-bottom: 1px solid #f5f0ea; display: flex; align-items: center; gap: 8px; }
    .ok  { color: #2e7d32; font-weight: 700; }
    .err { color: #c62828; font-weight: 700; }
    .checklist { background: #e8f5e9; border-left: 4px solid #2e7d32; padding: 16px 20px; border-radius: 4px; }
    .checklist li { padding: 4px 0; }
    code { background: #f0ede8; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
    .warning { background: #fff8e1; border-left: 4px solid #ff8f00; padding: 16px 20px; border-radius: 4px; margin-top: 20px; }
</style>
</head>
<body>
<h1>🏡 Almaretna Installer</h1>
<h2>Log installazione</h2>
<div class="log">
<ul>
<?php foreach ($log as $entry): ?>
    <li>
        <span class="<?= $entry['ok'] ? 'ok' : 'err' ?>"><?= $entry['ok'] ? '✓' : '✗' ?></span>
        <?= esc_html($entry['msg']) ?>
    </li>
<?php endforeach; ?>
</ul>
</div>

<div class="checklist">
<h3>✅ Prossimi passi obbligatori</h3>
<ol>
    <li>Aggiungi in <code>wp-config.php</code> le chiavi Stripe e Beds24:<br>
        <code>define('ALM_STRIPE_SECRET_KEY', 'sk_live_...');</code><br>
        <code>define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...');</code><br>
        <code>define('ALM_STRIPE_WEBHOOK_SECRET', 'whsec_...');</code><br>
        <code>define('ALM_BEDS24_API_TOKEN', '...');</code><br>
        <code>define('ALM_BEDS24_PROP_KEY', '...');</code>
    </li>
    <li>Vai in <strong>Prenotazioni → Impostazioni</strong> e configura: email struttura, telefono, orari check-in/out</li>
    <li>Vai in <strong>Prenotazioni → Dashboard</strong> e aggiungi le camere (CPT almaretna_room)</li>
    <li>Vai in <strong>Aspetto → Personalizza</strong> per impostare logo, colori e foto hero</li>
    <li>Configura i permalink: <strong>Impostazioni → Permalink</strong> → Struttura personalizzata: <code>/%postname%/</code></li>
    <li>Configura lo stripe webhook su <a href="https://dashboard.stripe.com" target="_blank">Stripe Dashboard</a></li>
    <li><strong>⚠️ ELIMINA</strong> questo file (<code>wp-content/almaretna-installer.php</code>) dopo aver verificato tutto</li>
</ol>
</div>

<div class="warning">
    <strong>⚠️ Sicurezza:</strong> Elimina immediatamente questo file dopo l'installazione.<br>
    <code>rm wp-content/almaretna-installer.php</code>
</div>
</body>
</html>
<?php