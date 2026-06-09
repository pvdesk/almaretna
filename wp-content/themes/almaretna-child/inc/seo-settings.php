<?php
declare(strict_types=1);

/**
 * Almaretna — SEO Settings Page
 *
 * Aggiunge una voce di menu "Almaretna SEO" nelle Impostazioni di WP.
 * Permette di configurare:
 *   - Google Analytics 4 (Measurement ID: G-XXXXXXXXXX)
 *   - Google Search Console (codice verifica meta tag)
 *
 * Il tracking GA4 viene iniettato nel <head> del frontend (non-admin).
 * Il meta tag GSC viene iniettato nel <head> solo quando impostato.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ── Menu nell'area Impostazioni ───────────────────────────────────────────────

add_action('admin_menu', function (): void {
    add_options_page(
        'Almaretna — SEO & Analytics',              // <title> pagina
        'Almaretna SEO',                            // label nel menu
        'manage_options',
        'alm-seo-settings',
        'alm_seo_settings_render_page'
    );
});

// ── Registra opzioni (Settings API) ──────────────────────────────────────────

add_action('admin_init', function (): void {
    register_setting('alm_seo_options', 'alm_ga4_id', [
        'type'              => 'string',
        'sanitize_callback' => 'alm_sanitize_ga4_id',
        'default'           => '',
    ]);
    register_setting('alm_seo_options', 'alm_gsc_verification', [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ]);
    register_setting('alm_seo_options', 'alm_schema_facebook_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ]);
    register_setting('alm_seo_options', 'alm_schema_instagram_url', [
        'type'              => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'default'           => '',
    ]);
});

// ── Sanitize GA4 ID ───────────────────────────────────────────────────────────

function alm_sanitize_ga4_id(string $value): string {
    $value = sanitize_text_field($value);
    // Accetta: G-XXXXXXXXXX oppure vuoto
    if ($value !== '' && !preg_match('/^G-[A-Z0-9]{1,20}$/i', $value)) {
        add_settings_error('alm_ga4_id', 'invalid', 'Formato GA4 non valido. Deve essere tipo: G-XXXXXXXXXX');
        return '';
    }
    return strtoupper($value);
}

// ── CSS della settings page ───────────────────────────────────────────────────

add_action('admin_head', function (): void {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'settings_page_alm-seo-settings') return;
    ?>
    <style id="alm-settings-css">
    .alm-settings-wrap            { max-width: 820px; }
    .alm-settings-section         { background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 24px 28px; margin-bottom: 28px; }
    .alm-settings-section h2      { margin: 0 0 6px; font-size: 15px; color: #1d2327; border-bottom: 1px solid #f0f0f0; padding-bottom: 12px; margin-bottom: 18px; }
    .alm-settings-section .desc   { font-size: 13px; color: #646970; margin: 0 0 18px; }
    .alm-field-row                { display: flex; gap: 16px; align-items: flex-start; margin-bottom: 16px; }
    .alm-field-row:last-child     { margin-bottom: 0; }
    .alm-field-row label          { width: 220px; flex-shrink: 0; font-weight: 600; font-size: 13px; color: #1d2327; padding-top: 6px; }
    .alm-field-row .alm-input-col { flex: 1; }
    .alm-field-row input[type="text"] { width: 100%; max-width: 360px; box-sizing: border-box; }
    .alm-field-row .alm-desc      { font-size: 12px; color: #646970; margin: 5px 0 0; }
    .alm-field-row code           { background: #f6f7f7; border: 1px solid #ddd; border-radius: 3px; padding: 1px 5px; font-size: 12px; }
    .alm-status-chip              { display: inline-flex; align-items: center; gap: 5px; font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; margin-left: 10px; vertical-align: middle; }
    .alm-status-chip.active       { background: #d1fae5; color: #065f46; }
    .alm-status-chip.inactive     { background: #f3f4f6; color: #6b7280; }
    </style>
    <?php
});

// ── Render della pagina ───────────────────────────────────────────────────────

function alm_seo_settings_render_page(): void {
    if (!current_user_can('manage_options')) return;

    $ga4_id   = (string) get_option('alm_ga4_id', '');
    $gsc_code = (string) get_option('alm_gsc_verification', '');
    $fb_url   = (string) get_option('alm_schema_facebook_url', '');
    $ig_url   = (string) get_option('alm_schema_instagram_url', '');
    ?>
    <div class="wrap alm-settings-wrap">
        <h1>Almaretna — SEO &amp; Analytics</h1>

        <?php settings_errors(); ?>

        <form method="post" action="options.php">
            <?php settings_fields('alm_seo_options'); ?>

            <!-- ── Google Analytics 4 ─────────────────────────────────────── -->
            <div class="alm-settings-section">
                <h2>
                    Google Analytics 4
                    <?php if ($ga4_id): ?>
                        <span class="alm-status-chip active">● Attivo</span>
                    <?php else: ?>
                        <span class="alm-status-chip inactive">○ Non configurato</span>
                    <?php endif; ?>
                </h2>
                <p class="desc">
                    Il codice di tracciamento <strong>gtag.js</strong> viene iniettato automaticamente
                    nell'<code>&lt;head&gt;</code> di ogni pagina pubblica.<br>
                    Gli amministratori loggati <strong>non vengono tracciati</strong>.
                    La privacy degli IP è anonimizzata (GA4 Consent Mode ready).
                </p>

                <div class="alm-field-row">
                    <label for="alm_ga4_id">Measurement ID</label>
                    <div class="alm-input-col">
                        <input type="text"
                               id="alm_ga4_id"
                               name="alm_ga4_id"
                               value="<?php echo esc_attr($ga4_id); ?>"
                               placeholder="G-XXXXXXXXXX"
                               class="regular-text">
                        <p class="alm-desc">
                            Trova il codice in <strong>Analytics → Amministrazione → Flussi di dati</strong>.<br>
                            Formato atteso: <code>G-XXXXXXXXXX</code> &nbsp;|&nbsp;
                            <?php if ($ga4_id): ?>
                                Attuale: <code><?php echo esc_html($ga4_id); ?></code>
                            <?php else: ?>
                                Lascia vuoto per disabilitare il tracciamento.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── Google Search Console ──────────────────────────────────── -->
            <div class="alm-settings-section">
                <h2>
                    Google Search Console
                    <?php if ($gsc_code): ?>
                        <span class="alm-status-chip active">● Verificato</span>
                    <?php else: ?>
                        <span class="alm-status-chip inactive">○ Non verificato</span>
                    <?php endif; ?>
                </h2>
                <p class="desc">
                    Inserisci <strong>solo il valore</strong> del meta tag di verifica, non il tag HTML completo.<br>
                    Il meta verrà aggiunto automaticamente nell'<code>&lt;head&gt;</code> del sito.
                </p>

                <div class="alm-field-row">
                    <label for="alm_gsc_verification">Codice di verifica</label>
                    <div class="alm-input-col">
                        <input type="text"
                               id="alm_gsc_verification"
                               name="alm_gsc_verification"
                               value="<?php echo esc_attr($gsc_code); ?>"
                               placeholder="google1234567890abcdef"
                               class="regular-text">
                        <p class="alm-desc">
                            Da Search Console: <strong>Impostazioni → Verifica proprietà → Tag HTML</strong>.<br>
                            Copia solo il contenuto dell'attributo <code>content="…"</code>,
                            non tutto il tag.<br>
                            Esempio: <code>google1a2b3c4d5e6f7890</code>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ── Social Media (per Schema.org) ──────────────────────────── -->
            <div class="alm-settings-section">
                <h2>Social Media <small style="font-weight:400;color:#646970;">(Schema.org sameAs)</small></h2>
                <p class="desc">
                    URL dei profili social: vengono usati nel markup strutturato <code>sameAs</code>
                    del LodgingBusiness (migliora la Knowledge Graph).
                </p>

                <div class="alm-field-row">
                    <label for="alm_schema_facebook_url">Facebook</label>
                    <div class="alm-input-col">
                        <input type="text"
                               id="alm_schema_facebook_url"
                               name="alm_schema_facebook_url"
                               value="<?php echo esc_attr($fb_url); ?>"
                               placeholder="https://www.facebook.com/almaretna"
                               class="regular-text">
                    </div>
                </div>

                <div class="alm-field-row">
                    <label for="alm_schema_instagram_url">Instagram</label>
                    <div class="alm-input-col">
                        <input type="text"
                               id="alm_schema_instagram_url"
                               name="alm_schema_instagram_url"
                               value="<?php echo esc_attr($ig_url); ?>"
                               placeholder="https://www.instagram.com/almaretna"
                               class="regular-text">
                    </div>
                </div>
            </div>

            <?php submit_button('Salva impostazioni'); ?>
        </form>

        <?php if ($ga4_id || $gsc_code): ?>
        <div class="alm-settings-section" style="background:#f8f9fa;">
            <h2 style="color:#646970;">Codice generato nel &lt;head&gt;</h2>
            <pre style="background:#1d2327;color:#a8b4c0;padding:14px 18px;border-radius:4px;overflow-x:auto;font-size:12px;line-height:1.7;white-space:pre-wrap;"><?php
                if ($gsc_code) {
                    echo esc_html('<meta name="google-site-verification" content="' . $gsc_code . '" />');
                    echo "\n";
                }
                if ($ga4_id) {
                    echo esc_html('<!-- Google Analytics 4 -->' . "\n");
                    echo esc_html('<script async src="https://www.googletagmanager.com/gtag/js?id=' . $ga4_id . '"></script>' . "\n");
                    echo esc_html('<script>' . "\n");
                    echo esc_html('  window.dataLayer = window.dataLayer || [];' . "\n");
                    echo esc_html('  function gtag(){dataLayer.push(arguments);}' . "\n");
                    echo esc_html('  gtag(\'js\', new Date());' . "\n");
                    echo esc_html('  gtag(\'config\', \'' . $ga4_id . '\');' . "\n");
                    echo esc_html('</script>');
                }
            ?></pre>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// ── Iniezione GA4 nel <head> ──────────────────────────────────────────────────

add_action('wp_head', function (): void {
    $ga4_id = (string) get_option('alm_ga4_id', '');
    if (!$ga4_id) return;

    // Non tracciare gli amministratori loggati
    if (is_user_logged_in() && current_user_can('manage_options')) return;

    ?>
<!-- Google Analytics 4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($ga4_id); ?>');
</script>
    <?php
}, 10);

// ── Iniezione meta tag Google Search Console ──────────────────────────────────

add_action('wp_head', function (): void {
    $code = (string) get_option('alm_gsc_verification', '');
    if (!$code) return;
    printf('<meta name="google-site-verification" content="%s" />' . "\n", esc_attr($code));
}, 2);
