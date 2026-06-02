<?php
/**
 * Almaretna — Package Builder
 *
 * Genera i file ZIP pronti all'installazione:
 *   dist/almaretna-child.zip          → tema figlio (richiede Astra attivo)
 *   dist/almaretna-booking.zip        → plugin prenotazione
 *   dist/almaretna-installer.php      → installer one-click da caricare in wp-content/
 *
 * Uso: php build-packages.php
 *      (da eseguire nella root di questo progetto WP o ovunque si abbia PHP CLI)
 *
 * @version 1.0.0
 */

declare(strict_types=1);

define('BUILD_DIR', __DIR__ . '/dist');
define('THEME_SRC',  __DIR__ . '/wp-content/themes/almaretna-child');
define('BOOKING_SRC',__DIR__ . '/wp-content/plugins/almaretna-booking');

// ─── Crea cartella dist ──────────────────────────────────────────────────────

if (!is_dir(BUILD_DIR)) {
    mkdir(BUILD_DIR, 0755, true);
    echo "✓ Cartella dist/ creata\n";
}

// ─── Helper: crea ZIP di una directory ──────────────────────────────────────

function zip_directory(string $src, string $zip_file, string $base_name): bool
{
    if (!class_exists('ZipArchive')) {
        die("ERRORE: l'estensione ZipArchive non è disponibile. Installa php-zip.\n");
    }

    if (file_exists($zip_file)) {
        unlink($zip_file);
    }

    $zip = new ZipArchive();
    if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo "ERRORE: impossibile creare $zip_file\n";
        return false;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $skip_patterns = [
        '/\.git/',
        '/\.DS_Store/',
        '/node_modules/',
        '/\.gitignore/',
        '/Thumbs\.db/',
        '/desktop\.ini/',
    ];

    foreach ($iterator as $file) {
        $real_path = $file->getRealPath();
        $relative  = $base_name . DIRECTORY_SEPARATOR . substr($real_path, strlen($src) + 1);
        $relative  = str_replace('\\', '/', $relative);

        // Salta file/cartelle da escludere
        foreach ($skip_patterns as $pattern) {
            if (preg_match($pattern, $relative)) {
                continue 2;
            }
        }

        if ($file->isDir()) {
            $zip->addEmptyDir($relative);
        } else {
            $zip->addFile($real_path, $relative);
        }
    }

    $zip->close();
    return true;
}

// ─── 1. ZIP Tema Child ───────────────────────────────────────────────────────

echo "\n📦 Packaging tema child...\n";

$theme_zip = BUILD_DIR . '/almaretna-child.zip';
if (zip_directory(THEME_SRC, $theme_zip, 'almaretna-child')) {
    $size = round(filesize($theme_zip) / 1024, 1);
    echo "   ✓ almaretna-child.zip ({$size} KB)\n";
} else {
    echo "   ✗ Errore tema child\n";
}

// ─── 2. ZIP Plugin Booking ───────────────────────────────────────────────────

echo "📦 Packaging plugin almaretna-booking...\n";

$booking_zip = BUILD_DIR . '/almaretna-booking.zip';
if (zip_directory(BOOKING_SRC, $booking_zip, 'almaretna-booking')) {
    $size = round(filesize($booking_zip) / 1024, 1);
    echo "   ✓ almaretna-booking.zip ({$size} KB)\n";
} else {
    echo "   ✗ Errore plugin booking\n";
}

// ─── 3. Genera almaretna-installer.php ──────────────────────────────────────

echo "🛠  Generazione installer...\n";

$installer_content = generate_installer();
file_put_contents(BUILD_DIR . '/almaretna-installer.php', $installer_content);
echo "   ✓ almaretna-installer.php\n";

// ─── 4. Genera README installazione ──────────────────────────────────────────

$readme = generate_readme();
file_put_contents(BUILD_DIR . '/INSTALLAZIONE.md', $readme);
echo "   ✓ INSTALLAZIONE.md\n";

echo "\n✅ Build completata in dist/\n";
echo "─────────────────────────────────────────────\n";
echo " dist/almaretna-child.zip\n";
echo " dist/almaretna-booking.zip\n";
echo " dist/almaretna-installer.php\n";
echo " dist/INSTALLAZIONE.md\n";
echo "─────────────────────────────────────────────\n\n";

// ════════════════════════════════════════════════════════════════════════════
// Generator functions
// ════════════════════════════════════════════════════════════════════════════

function generate_installer(): string
{
    return <<<'PHP'
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
PHP;
}

function generate_readme(): string
{
    return <<<'MD'
# Almaretna — Installazione su WordPress Vergine

## Requisiti
- WordPress 6.0+
- PHP 8.1+
- MySQL/MariaDB 5.7+
- Estensione PHP: `zip`, `curl`, `json`

---

## Metodo 1 — Installer automatico (consigliato)

1. Installa WordPress normalmente
2. Attiva il plugin **Astra** dal repository WP (o installalo manualmente)
3. Copia nella cartella `wp-content/` questi file:
   - `almaretna-child.zip`
   - `almaretna-booking.zip`
   - `almaretna-installer.php`
4. Apri nel browser:
   ```
   https://tuosito.it/wp-content/almaretna-installer.php?token=almaretna-setup-2024
   ```
5. L'installer creerà tutto automaticamente
6. **Elimina** `almaretna-installer.php` dopo l'installazione

---

## Metodo 2 — Installazione manuale

### A. Installa il tema

1. Installa e attiva il tema genitore **Astra** da WP → Aspetto → Temi
2. Carica `almaretna-child.zip` in WP → Aspetto → Temi → Aggiungi → Carica
3. Attiva il tema **Almaretna Child**

### B. Installa il plugin

1. Carica `almaretna-booking.zip` in WP → Plugin → Aggiungi → Carica plugin
2. Attiva il plugin **Almaretna Booking**

### C. Crea le pagine

Crea queste pagine in WP → Pagine → Aggiungi:

| Titolo          | Slug       | Template                          |
|-----------------|------------|-----------------------------------|
| Home            | home       | (default)                         |
| Le Nostre Camere | camere    | Template Rooms                    |
| Prenota         | prenota    | Template Booking                  |
| Chi Siamo       | chi-siamo  | (default)                         |
| Contatti        | contatti   | (default)                         |

Imposta "Home" come pagina statica: WP → Impostazioni → Lettura

### D. Configura le chiavi API

In `wp-config.php` aggiungi:

```php
// Stripe
define('ALM_STRIPE_SECRET_KEY',      'sk_live_...');
define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...');
define('ALM_STRIPE_WEBHOOK_SECRET',  'whsec_...');

// Beds24 (channel manager)
define('ALM_BEDS24_API_TOKEN',     'il_tuo_token');
define('ALM_BEDS24_PROP_KEY',      'il_tuo_prop_key');
define('ALM_BEDS24_WEBHOOK_TOKEN', 'segreto_webhook');
```

### E. Dove trovare le chiavi

**Stripe:**
- Vai su [dashboard.stripe.com](https://dashboard.stripe.com)
- Developers → API Keys → copia `Secret key` e `Publishable key`
- Developers → Webhooks → Add endpoint → inserisci: `https://tuosito.it/wp-json/scv/v1/stripe/webhook`
- Seleziona eventi: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`

**Beds24:**
- Vai su [beds24.com](https://beds24.com) → Settings → Account → API
- Crea un token API (token personale)
- Prop Key: Settings → Property → Property Key
- Webhook URL da inserire in Beds24: `https://tuosito.it/wp-json/scv/v1/beds24/webhook`

---

## Struttura file generati

```
dist/
├── almaretna-child.zip       # Tema figlio Astra
├── almaretna-booking.zip     # Plugin prenotazione + Beds24 + Stripe
├── almaretna-installer.php   # Installer one-click (eliminare dopo uso)
└── INSTALLAZIONE.md          # Questo file
```

---

## Dopo l'installazione — Checklist

- [ ] Chiavi Stripe configurate in wp-config.php
- [ ] Chiavi Beds24 configurate in wp-config.php
- [ ] Email struttura impostata in Prenotazioni → Impostazioni
- [ ] Telefono struttura impostato
- [ ] Almeno una camera inserita (CPT → Le Mie Camere)
- [ ] Logo caricato in Aspetto → Personalizza → Logo
- [ ] Foto hero impostata
- [ ] Menu di navigazione assegnato
- [ ] Permalink impostato su /%postname%/
- [ ] SSL/HTTPS attivo
- [ ] almaretna-installer.php eliminato
MD;
}
