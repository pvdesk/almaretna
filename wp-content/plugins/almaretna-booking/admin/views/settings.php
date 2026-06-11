<?php
/**
 * Admin view — Impostazioni plugin
 *
 * @package AlmaretnaBooking
 */
defined('ABSPATH') || exit;

$s = is_array($settings) ? $settings : [];

// Helper mascheramento chiavi sensibili
$mask = static function (string $key): string {
    if ($key === '') return '—';
    return substr($key, 0, 8) . str_repeat('•', max(0, strlen($key) - 12)) . substr($key, -4);
};
$mask4 = static function (string $key): string {
    if ($key === '') return '—';
    return substr($key, 0, 4) . str_repeat('•', max(0, strlen($key) - 8)) . substr($key, -4);
};

// Stato servizi
$stripe_configured = ALM_Stripe::is_configured();
$stripe_from_cfg   = ALM_Stripe::keys_source() === 'config';
$stripe_db_keys    = get_option('alm_stripe_keys', []);
$b24_configured    = ALM_Beds24::is_configured();
$b24_from_cfg      = ALM_Beds24::keys_source() === 'config';
$b24_db_keys       = get_option('alm_beds24_keys', []);
$ga4_id            = get_option('alm_ga4_measurement_id', '');
$ga4_locked        = (bool) get_option('alm_ga4_locked', false);
$gsc_code          = get_option('alm_gsc_verification', '');
$gsc_locked        = (bool) get_option('alm_gsc_locked', false);
$gapi_configured   = class_exists('ALM_Google_API') && ALM_Google_API::is_configured();
$gapi_prop         = get_option('alm_ga4_property_id', '');
$gapi_site         = get_option('alm_gsc_site_url', home_url('/'));

// Tab attivo
$valid_tabs   = ['struttura', 'pagamenti', 'canali', 'analytics', 'email', 'sistema'];
$active_tab   = in_array($_GET['alm_tab'] ?? '', $valid_tabs, true) ? $_GET['alm_tab'] : 'struttura';
if (!empty($_GET['updated'])) $active_tab = 'struttura';

// Stato SMTP
$smtp_configured = class_exists('ALM_SMTP') && ALM_SMTP::is_configured();
$smtp_from_cfg   = class_exists('ALM_SMTP') && ALM_SMTP::config_source() === 'config';
$smtp_cfg        = class_exists('ALM_SMTP') ? ALM_SMTP::get_config() : [];
?>
<div class="wrap alm-admin-wrap">

    <!-- Intestazione -->
    <div class="alm-page-header">
        <h1><span class="dashicons dashicons-admin-settings"></span> Impostazioni</h1>
        <div class="alm-page-header__actions">
            <?php
            $done = (int)$stripe_configured + (int)$b24_configured + (int)$ga4_locked + (int)$gsc_locked + (int)$smtp_configured;
            $all  = 5;
            $clr  = $done === $all ? '#2e7d32' : ($done >= 2 ? '#e65100' : '#c62828');
            ?>
            <span style="font-size:12px;color:<?php echo $clr; ?>;font-weight:600;">
                <?php echo $done; ?>/<?php echo $all; ?> servizi attivi
            </span>
            <span class="alm-version-chip">v<?php echo esc_html(ALM_BOOKING_VERSION); ?></span>
            <button type="button" class="button button-small" id="alm-opcache-btn"
                    onclick="almResetOpcache('<?php echo esc_js(wp_create_nonce('alm_reset_opcache')); ?>')">
                ↺ Cache PHP
            </button>
            <span id="alm-opcache-msg"></span>
        </div>
    </div>

    <?php if (!empty($_GET['updated'])) : ?>
    <div class="notice notice-success is-dismissible"><p>✓ Impostazioni salvate correttamente.</p></div>
    <?php endif; ?>

    <!-- Navigazione tab -->
    <nav class="alm-tab-nav" role="tablist">
        <?php
        $tabs = [
            'struttura' => ['dashicons-building',   'Struttura', false],
            'pagamenti' => ['dashicons-money-alt',   'Pagamenti', !$stripe_configured],
            'canali'    => ['dashicons-admin-links',  'Canali',    !$b24_configured],
            'analytics' => ['dashicons-chart-bar',    'Analytics', !$ga4_locked || !$gsc_locked],
            'email'     => ['dashicons-email-alt',    'Email',     !$smtp_configured],
            'sistema'   => ['dashicons-admin-tools',  'Sistema',   false],
        ];
        foreach ($tabs as $slug => [$icon, $label, $has_alert]) :
        ?>
        <button type="button" class="alm-tab-btn <?php echo $active_tab === $slug ? 'is-active' : ''; ?>"
                data-tab="<?php echo esc_attr($slug); ?>" role="tab">
            <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            <?php echo esc_html($label); ?>
            <?php if ($has_alert) : ?><span class="alm-tab-dot"></span><?php endif; ?>
        </button>
        <?php endforeach; ?>
    </nav>

    <!-- ══════════════════════════════════════════════════
         TAB: STRUTTURA
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-struttura" class="alm-tab-pane <?php echo $active_tab === 'struttura' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Informazioni struttura</h2>
                        <p class="alm-card-desc">Dati usati nelle email agli ospiti e nel sito.</p>
                    </div>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-field-grid">
                        <div class="alm-field alm-field--full">
                            <label for="host_name">Nome struttura</label>
                            <input type="text" id="host_name"
                                   value="<?php echo esc_attr($s['host_name'] ?? 'Almaretna'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="host_email">Email struttura <span class="alm-hint">mittente e destinatario</span></label>
                            <input type="email" id="host_email"
                                   value="<?php echo esc_attr($s['host_email'] ?? get_option('admin_email')); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="host_phone">Telefono</label>
                            <input type="tel" id="host_phone"
                                   value="<?php echo esc_attr($s['host_phone'] ?? ''); ?>"
                                   placeholder="+39 095 000 0000" />
                        </div>
                        <div class="alm-field alm-field--full">
                            <label for="whatsapp_webhook_url">WhatsApp Webhook URL <span class="alm-hint">Make.com / Zapier / N8N — facoltativo</span></label>
                            <input type="url" id="whatsapp_webhook_url"
                                   value="<?php echo esc_attr(get_option('alm_whatsapp_webhook_url', '')); ?>"
                                   placeholder="https://hook.make.com/..." />
                        </div>
                        <div class="alm-field">
                            <label for="checkin_time">Check-in (orario)</label>
                            <input type="time" id="checkin_time"
                                   value="<?php echo esc_attr($s['checkin_time'] ?? '15:00'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="checkout_time">Check-out (orario)</label>
                            <input type="time" id="checkout_time"
                                   value="<?php echo esc_attr($s['checkout_time'] ?? '11:00'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="min_stay_global">Soggiorno minimo <span class="alm-hint">notti (override per camera)</span></label>
                            <input type="number" id="min_stay_global" min="1"
                                   value="<?php echo esc_attr((string) ($s['min_stay_global'] ?? 1)); ?>" />
                        </div>
                    </div>

                    <div class="alm-section-divider"></div>

                    <label class="alm-toggle-label">
                        <input type="checkbox" id="beds24_enabled" value="1"
                               <?php checked(!empty($s['beds24_enabled'])); ?> />
                        <span class="alm-toggle-text">
                            <strong>Sincronizzazione Beds24 attiva</strong>
                            <span class="alm-hint">Aggiorna disponibilità e prezzi da Beds24 automaticamente (2× al giorno)</span>
                        </span>
                    </label>
                </div>
            </div>

            <div id="alm-struttura-msg" class="alm-msg"></div>
            <div class="alm-form-actions">
                <button type="button" class="button button-primary"
                        onclick="almSaveStruttura('<?php echo esc_js(wp_create_nonce('alm_save_struttura')); ?>')">
                    Salva impostazioni
                </button>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: PAGAMENTI
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-pagamenti" class="alm-tab-pane <?php echo $active_tab === 'pagamenti' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

        <?php if (!$stripe_configured) : ?>
        <div class="alm-banner alm-banner--info">
            <span class="dashicons dashicons-info-outline"></span>
            <div>
                <strong>Come attivare i pagamenti online</strong>
                <p>Stripe è il sistema usato per accettare pagamenti con carta di credito. È gratuito da usare — paghi solo quando ricevi un pagamento.</p>
                <a href="https://dashboard.stripe.com/register" target="_blank" rel="noopener" class="button button-small" style="margin-top:6px;">Crea account Stripe gratuito →</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card Stripe chiavi -->
        <div class="alm-settings-card" id="alm-stripe-card">
            <div class="alm-settings-card__head alm-settings-card__head--toggle" onclick="almToggle('stripe')">
                <div>
                    <h2>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Stripe — Chiavi API
                    </h2>
                    <p class="alm-card-desc">Collegano il sito al tuo account Stripe per gestire i pagamenti.</p>
                </div>
                <div class="alm-settings-card__badges">
                    <?php if ($stripe_configured) : ?>
                        <span class="alm-badge alm-badge--<?php echo ALM_Stripe::is_test_mode() ? 'warn' : 'ok'; ?>">
                            <?php echo ALM_Stripe::is_test_mode() ? '⚠ TEST MODE' : '✓ LIVE'; ?>
                        </span>
                    <?php else : ?>
                        <span class="alm-badge alm-badge--miss">Non configurato</span>
                    <?php endif; ?>
                    <?php if ($stripe_from_cfg) : ?>
                        <span class="alm-badge alm-badge--blue">wp-config.php</span>
                    <?php endif; ?>
                    <span class="alm-chevron" id="alm-stripe-chev">▾</span>
                </div>
            </div>

            <div id="alm-stripe-body" class="alm-settings-card__body" style="display:none;">
                <?php if ($stripe_from_cfg) : ?>
                <div class="alm-infobox alm-infobox--blue">
                    <span class="dashicons dashicons-lock"></span>
                    <div>Chiavi definite in <strong>wp-config.php</strong> — per modificarle usa FTP/SSH e aggiorna le righe <code>ALM_STRIPE_*</code>.</div>
                </div>
                <div class="alm-key-grid">
                    <div class="alm-key-row"><span class="alm-key-label">Publishable Key</span><span class="alm-key-val"><?php echo esc_html($mask(ALM_Stripe::get_publishable_key())); ?></span></div>
                    <div class="alm-key-row"><span class="alm-key-label">Secret Key</span><span class="alm-key-val"><?php echo esc_html($mask(defined('ALM_STRIPE_SECRET_KEY') ? ALM_STRIPE_SECRET_KEY : '')); ?></span></div>
                    <div class="alm-key-row"><span class="alm-key-label">Webhook Secret</span><span class="alm-key-val"><?php echo esc_html($mask(defined('ALM_STRIPE_WEBHOOK_SECRET') ? ALM_STRIPE_WEBHOOK_SECRET : '')); ?></span></div>
                </div>
                <?php else : ?>
                <p class="alm-card-intro">
                    Inserisci le chiavi del tuo account Stripe.
                    <a href="https://dashboard.stripe.com/apikeys" target="_blank" rel="noopener">Trovale in Dashboard Stripe → Sviluppatori → Chiavi API</a>.
                </p>
                <div class="alm-field-list">
                    <div class="alm-field">
                        <label for="s-pk">Publishable Key <span class="alm-hint">inizia con pk_live_ o pk_test_</span></label>
                        <input type="text" id="s-pk" class="regular-text"
                               value="<?php echo esc_attr($stripe_db_keys['publishable_key'] ?? ''); ?>"
                               placeholder="pk_live_..." autocomplete="off" />
                    </div>
                    <div class="alm-field">
                        <label for="s-sk">Secret Key <span class="alm-hint">inizia con sk_live_ — non condividere mai</span></label>
                        <div class="alm-input-row">
                            <input type="password" id="s-sk" class="regular-text" value=""
                                   placeholder="<?php echo !empty($stripe_db_keys['secret_key']) ? '••••••••' . substr($stripe_db_keys['secret_key'], -4) : 'sk_live_...'; ?>"
                                   autocomplete="new-password" />
                            <button type="button" class="button button-small alm-reveal" data-for="s-sk">Mostra</button>
                        </div>
                        <span class="alm-hint" style="margin-top:3px;">Lascia vuoto per mantenere la chiave già salvata</span>
                    </div>
                    <div class="alm-field">
                        <label for="s-wh">Webhook Secret <span class="alm-hint">inizia con whsec_</span></label>
                        <div class="alm-input-row">
                            <input type="password" id="s-wh" class="regular-text" value=""
                                   placeholder="<?php echo !empty($stripe_db_keys['webhook_secret']) ? '••••••••' . substr($stripe_db_keys['webhook_secret'], -4) : 'whsec_...'; ?>"
                                   autocomplete="new-password" />
                            <button type="button" class="button button-small alm-reveal" data-for="s-wh">Mostra</button>
                        </div>
                        <span class="alm-hint" style="margin-top:3px;">Dashboard Stripe → Sviluppatori → Webhook → Signing secret</span>
                    </div>
                </div>
                <div id="alm-stripe-db-msg" class="alm-msg"></div>
                <div class="alm-form-actions">
                    <button type="button" class="button button-primary"
                            onclick="almSaveStripeDb('<?php echo esc_js(wp_create_nonce('alm_save_stripe_db')); ?>')">
                        Salva chiavi Stripe
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card URL webhook -->
        <div class="alm-settings-card">
            <div class="alm-settings-card__head">
                <div>
                    <h2>URL Webhook Stripe</h2>
                    <p class="alm-card-desc">Incolla questo URL in Dashboard Stripe per ricevere le notifiche di pagamento.</p>
                </div>
            </div>
            <div class="alm-settings-card__body">
                <div class="alm-copybox">
                    <code id="alm-stripe-wh-url"><?php echo esc_html(get_rest_url(null, 'alm/v1/stripe-webhook')); ?></code>
                    <button type="button" class="button button-small" onclick="almCopy('alm-stripe-wh-url',this)">Copia</button>
                </div>
                <p class="alm-hint" style="margin-top:8px;">
                    Stripe Dashboard → Sviluppatori → Webhook → Aggiungi endpoint<br>
                    Seleziona eventi: <code>payment_intent.succeeded</code>, <code>payment_intent.payment_failed</code>, <code>charge.refunded</code>
                </p>
            </div>
        </div>

        </div><!-- /.alm-tab-body -->
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: CANALI
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-canali" class="alm-tab-pane <?php echo $active_tab === 'canali' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

        <?php if (!$b24_configured) : ?>
        <div class="alm-banner alm-banner--info">
            <span class="dashicons dashicons-admin-links"></span>
            <div>
                <strong>Collega Beds24 per sincronizzare i calendari</strong>
                <p>Beds24 mantiene allineata la disponibilità su tutti i portali (Airbnb, Booking.com, ecc.). Se non lo usi, puoi ignorare questa sezione.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="alm-settings-card" id="alm-b24-card">
            <div class="alm-settings-card__head alm-settings-card__head--toggle" onclick="almToggle('b24')">
                <div>
                    <h2>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                        Beds24
                    </h2>
                    <p class="alm-card-desc">Sincronizzazione automatica disponibilità e tariffe.</p>
                </div>
                <div class="alm-settings-card__badges">
                    <?php if ($b24_configured) : ?>
                        <span class="alm-badge alm-badge--ok">✓ Configurato</span>
                    <?php else : ?>
                        <span class="alm-badge alm-badge--miss">Non configurato</span>
                    <?php endif; ?>
                    <?php if ($b24_from_cfg) : ?><span class="alm-badge alm-badge--blue">wp-config.php</span><?php endif; ?>
                    <span class="alm-chevron" id="alm-b24-chev">▾</span>
                </div>
            </div>

            <div id="alm-b24-body" class="alm-settings-card__body" style="display:none;">
                <?php if ($b24_from_cfg) : ?>
                <div class="alm-infobox alm-infobox--blue">
                    <span class="dashicons dashicons-lock"></span>
                    <div>Chiavi definite in <strong>wp-config.php</strong> — non modificabili da qui.</div>
                </div>
                <div class="alm-key-grid" style="margin-top:12px;">
                    <div class="alm-key-row"><span class="alm-key-label">API Token</span><span class="alm-key-val"><?php echo esc_html($mask4(defined('ALM_BEDS24_API_TOKEN') ? ALM_BEDS24_API_TOKEN : '')); ?></span></div>
                    <div class="alm-key-row"><span class="alm-key-label">Prop Key</span><span class="alm-key-val"><?php echo esc_html($mask4(defined('ALM_BEDS24_PROP_KEY') ? ALM_BEDS24_PROP_KEY : '')); ?></span></div>
                    <div class="alm-key-row"><span class="alm-key-label">Webhook Token</span><span class="alm-key-val"><?php echo esc_html($mask4(defined('ALM_BEDS24_WEBHOOK_TOKEN') ? ALM_BEDS24_WEBHOOK_TOKEN : '')); ?></span></div>
                </div>
                <?php else : ?>
                <p class="alm-card-intro">
                    <a href="https://beds24.com/control2.php?pagetype=apiv2tokens" target="_blank" rel="noopener">Beds24 → Account → API v2 Tokens</a> per trovare i valori.
                </p>
                <div class="alm-field-list">
                    <div class="alm-field">
                        <label for="b24-token">API Token</label>
                        <div class="alm-input-row">
                            <input type="password" id="b24-token" class="regular-text" value=""
                                   placeholder="<?php echo !empty($b24_db_keys['api_token']) ? '••••' . substr($b24_db_keys['api_token'], -4) : 'Incolla il token Beds24'; ?>"
                                   autocomplete="new-password" />
                            <button type="button" class="button button-small alm-reveal" data-for="b24-token">Mostra</button>
                        </div>
                    </div>
                    <div class="alm-field">
                        <label for="b24-propkey">Prop Key</label>
                        <div class="alm-input-row">
                            <input type="password" id="b24-propkey" class="regular-text" value=""
                                   placeholder="<?php echo !empty($b24_db_keys['prop_key']) ? '••••' . substr($b24_db_keys['prop_key'], -4) : 'Prop Key della proprietà'; ?>"
                                   autocomplete="new-password" />
                            <button type="button" class="button button-small alm-reveal" data-for="b24-propkey">Mostra</button>
                        </div>
                    </div>
                    <div class="alm-field">
                        <label for="b24-wh">Webhook Token</label>
                        <div class="alm-input-row">
                            <input type="password" id="b24-wh" class="regular-text" value=""
                                   placeholder="<?php echo !empty($b24_db_keys['webhook_token']) ? '••••' . substr($b24_db_keys['webhook_token'], -4) : 'Token segreto webhook'; ?>"
                                   autocomplete="new-password" />
                            <button type="button" class="button button-small alm-reveal" data-for="b24-wh">Mostra</button>
                        </div>
                    </div>
                </div>
                <div id="alm-b24-msg" class="alm-msg"></div>
                <div class="alm-form-actions">
                    <button type="button" class="button button-primary"
                            onclick="almSaveBeds24('<?php echo esc_js(wp_create_nonce('alm_save_beds24')); ?>')">
                        Salva chiavi Beds24
                    </button>
                    <?php if ($b24_configured) : ?>
                    <button type="button" class="button"
                            onclick="almTestBeds24('<?php echo esc_js(wp_create_nonce('alm_test_beds24')); ?>')">
                        Testa connessione
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="alm-section-divider"></div>
                <p style="font-size:12px;font-weight:700;letter-spacing:.4px;text-transform:uppercase;color:#6b7280;margin:0 0 8px;">URL Webhook Beds24</p>
                <div class="alm-copybox">
                    <code id="alm-b24-wh-url"><?php echo esc_html(get_rest_url(null, 'alm/v1/beds24/webhook')); ?></code>
                    <button type="button" class="button button-small" onclick="almCopy('alm-b24-wh-url',this)">Copia</button>
                </div>
            </div>
        </div>

        </div><!-- /.alm-tab-body -->
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: ANALYTICS
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-analytics" class="alm-tab-pane <?php echo $active_tab === 'analytics' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

            <!-- GA4 -->
            <div class="alm-settings-card" id="alm-ga4-card">
                <div class="alm-settings-card__head alm-settings-card__head--toggle" onclick="almToggle('ga4')">
                    <div>
                        <h2>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                            Google Analytics 4
                        </h2>
                        <p class="alm-card-desc">Traccia visite e conversioni sul sito.</p>
                    </div>
                    <div class="alm-settings-card__badges">
                        <?php if ($ga4_locked) : ?><span class="alm-badge alm-badge--ok">✓ Attivo</span>
                        <?php elseif ($ga4_id) : ?><span class="alm-badge alm-badge--warn">ID inserito</span>
                        <?php else : ?><span class="alm-badge alm-badge--miss">Non configurato</span><?php endif; ?>
                        <span class="alm-chevron" id="alm-ga4-chev">▾</span>
                    </div>
                </div>
                <div id="alm-ga4-body" class="alm-settings-card__body" style="display:none;">
                    <?php if ($ga4_locked) : ?>
                    <div class="alm-infobox alm-infobox--green">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <div>GA4 attivo — Measurement ID: <strong><?php echo esc_html($ga4_id); ?></strong></div>
                    </div>
                    <button type="button" class="button" style="margin-top:12px;"
                            onclick="almUnlock('ga4','<?php echo esc_js(wp_create_nonce('alm_unlock_ga4')); ?>')">
                        🔓 Modifica
                    </button>
                    <?php else : ?>
                    <p class="alm-card-intro">Trovi il Measurement ID in <strong>GA4 → Amministrazione → Flussi di dati → seleziona il flusso → ID misurazione</strong>.</p>
                    <div class="alm-field" style="max-width:320px;">
                        <label for="alm-ga4-id">Measurement ID</label>
                        <input type="text" id="alm-ga4-id"
                               value="<?php echo esc_attr($ga4_id); ?>"
                               placeholder="G-XXXXXXXXXX" style="font-family:monospace;" />
                    </div>
                    <div id="alm-ga4-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button button-primary"
                                onclick="almSaveAnalytics('ga4','<?php echo esc_js(wp_create_nonce('alm_save_ga4')); ?>')">
                            Salva e attiva
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- GSC -->
            <div class="alm-settings-card" id="alm-gsc-card">
                <div class="alm-settings-card__head alm-settings-card__head--toggle" onclick="almToggle('gsc')">
                    <div>
                        <h2>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Google Search Console
                        </h2>
                        <p class="alm-card-desc">Verifica il sito su Google e monitora le ricerche organiche.</p>
                    </div>
                    <div class="alm-settings-card__badges">
                        <?php if ($gsc_locked) : ?><span class="alm-badge alm-badge--ok">✓ Verificato</span>
                        <?php elseif ($gsc_code) : ?><span class="alm-badge alm-badge--warn">Codice inserito</span>
                        <?php else : ?><span class="alm-badge alm-badge--miss">Non configurato</span><?php endif; ?>
                        <span class="alm-chevron" id="alm-gsc-chev">▾</span>
                    </div>
                </div>
                <div id="alm-gsc-body" class="alm-settings-card__body" style="display:none;">
                    <?php if ($gsc_locked) : ?>
                    <div class="alm-infobox alm-infobox--green">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <div>Search Console verificata.</div>
                    </div>
                    <button type="button" class="button" style="margin-top:12px;"
                            onclick="almUnlock('gsc','<?php echo esc_js(wp_create_nonce('alm_unlock_gsc')); ?>')">
                        🔓 Modifica
                    </button>
                    <?php else : ?>
                    <p class="alm-card-intro">Vai in <strong>Google Search Console → Impostazioni → Verifica proprietà → Tag HTML</strong> e copia solo il contenuto dell'attributo <code>content</code>.</p>
                    <div class="alm-field">
                        <label for="alm-gsc-code">Codice di verifica</label>
                        <input type="text" id="alm-gsc-code"
                               value="<?php echo esc_attr($gsc_code); ?>" placeholder="AbCdEf1234…" />
                    </div>
                    <div id="alm-gsc-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button button-primary"
                                onclick="almSaveAnalytics('gsc','<?php echo esc_js(wp_create_nonce('alm_save_gsc')); ?>')">
                            Salva e verifica
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Google API / Service Account -->
            <div class="alm-settings-card" id="alm-gapi-card">
                <div class="alm-settings-card__head alm-settings-card__head--toggle" onclick="almToggle('gapi')">
                    <div>
                        <h2>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                            Dashboard Analytics (Google API)
                        </h2>
                        <p class="alm-card-desc">Mostra statistiche GA4 e Search Console direttamente nell'admin WordPress.</p>
                    </div>
                    <div class="alm-settings-card__badges">
                        <?php if ($gapi_configured && $gapi_prop) : ?><span class="alm-badge alm-badge--ok">✓ Configurato</span>
                        <?php elseif ($gapi_configured) : ?><span class="alm-badge alm-badge--warn">Manca Property ID</span>
                        <?php else : ?><span class="alm-badge alm-badge--miss">Non configurato</span><?php endif; ?>
                        <span class="alm-chevron" id="alm-gapi-chev">▾</span>
                    </div>
                </div>
                <div id="alm-gapi-body" class="alm-settings-card__body" style="display:none;">
                    <div class="alm-infobox alm-infobox--blue" style="margin-bottom:16px;">
                        <span class="dashicons dashicons-info"></span>
                        <div style="font-size:12px;line-height:1.7;">
                            <strong>Come configurare (una volta sola):</strong><br>
                            1. <a href="https://console.cloud.google.com/iam-admin/serviceaccounts" target="_blank" rel="noopener">Google Cloud Console</a> → IAM → Service Account → Crea → scarica il file JSON<br>
                            2. In <strong>GA4</strong>: Amministrazione → Accessi proprietà → Aggiungi l'email del Service Account (ruolo: Spettatore)<br>
                            3. In <strong>Search Console</strong>: Impostazioni → Utenti → Aggiungi l'email del Service Account<br>
                            4. Incolla il JSON qui sotto e salva
                        </div>
                    </div>
                    <div class="alm-field-list">
                        <div class="alm-field">
                            <label for="alm-gapi-sa">Service Account JSON</label>
                            <?php if ($gapi_configured) : ?>
                            <div class="alm-infobox alm-infobox--green" style="margin-bottom:8px;">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <code><?php echo esc_html(json_decode(get_option('alm_google_sa_json','{}'), true)['client_email'] ?? ''); ?></code>
                            </div>
                            <?php endif; ?>
                            <textarea id="alm-gapi-sa" rows="4" class="large-text code"
                                      style="font-size:11px;font-family:monospace;"
                                      placeholder='{"type":"service_account","project_id":"...","client_email":"...@....iam.gserviceaccount.com",...}'></textarea>
                        </div>
                        <div class="alm-field">
                            <label for="alm-gapi-prop">GA4 Property ID <span class="alm-hint">es. properties/123456789</span></label>
                            <input type="text" id="alm-gapi-prop"
                                   value="<?php echo esc_attr($gapi_prop); ?>" placeholder="properties/XXXXXXXXX" />
                        </div>
                        <div class="alm-field">
                            <label for="alm-gapi-site">GSC Site URL</label>
                            <input type="url" id="alm-gapi-site"
                                   value="<?php echo esc_attr($gapi_site); ?>" placeholder="https://www.almaretna.it/" />
                        </div>
                    </div>
                    <div id="alm-gapi-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button button-primary"
                                onclick="almSaveGapi('<?php echo esc_js(wp_create_nonce('alm_save_google_api')); ?>')">
                            Salva configurazione
                        </button>
                        <?php if ($gapi_configured) : ?>
                        <button type="button" class="button"
                                onclick="almTestApi('ga4','<?php echo esc_js(wp_create_nonce('alm_test_ga4')); ?>')">
                            Testa GA4
                        </button>
                        <button type="button" class="button"
                                onclick="almTestApi('gsc','<?php echo esc_js(wp_create_nonce('alm_test_gsc')); ?>')">
                            Testa Search Console
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: EMAIL / SMTP
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-email" class="alm-tab-pane <?php echo $active_tab === 'email' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

            <?php if ($smtp_from_cfg) : ?>
            <div class="notice notice-info inline" style="margin-bottom:16px;">
                <p>Le credenziali SMTP sono definite in <code>wp-config.php</code> — i campi sono in sola lettura.</p>
            </div>
            <?php endif; ?>

            <!-- Credenziali SMTP -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Configurazione SMTP</h2>
                        <p class="alm-card-desc">Usa il tuo server email per inviare prenotazioni, ricevute e notifiche.</p>
                    </div>
                    <span class="alm-badge <?php echo $smtp_configured ? 'alm-badge--ok' : 'alm-badge--warn'; ?>">
                        <?php echo $smtp_configured ? 'Configurato' : 'Non configurato'; ?>
                    </span>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-field-list">

                        <div class="alm-field">
                            <label for="smtp-host">Server SMTP</label>
                            <input type="text" id="smtp-host"
                                   value="<?php echo esc_attr($smtp_cfg['host'] ?? ''); ?>"
                                   placeholder="smtp.gmail.com"
                                   <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                            <span class="alm-hint">Es. <code>smtp.gmail.com</code> · <code>smtps.aruba.it</code> · <code>mail.siteground.com</code></span>
                        </div>

                        <div class="alm-field-grid">
                            <div class="alm-field">
                                <label for="smtp-port">Porta</label>
                                <input type="number" id="smtp-port"
                                       value="<?php echo esc_attr((string)($smtp_cfg['port'] ?? 587)); ?>"
                                       min="1" max="65535"
                                       <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                            </div>
                            <div class="alm-field">
                                <label for="smtp-enc">Cifratura</label>
                                <select id="smtp-enc" <?php echo $smtp_from_cfg ? 'disabled' : ''; ?>>
                                    <option value="tls" <?php selected($smtp_cfg['encryption'] ?? 'tls', 'tls'); ?>>TLS (porta 587)</option>
                                    <option value="ssl" <?php selected($smtp_cfg['encryption'] ?? '', 'ssl'); ?>>SSL (porta 465)</option>
                                    <option value=""    <?php selected($smtp_cfg['encryption'] ?? '', ''); ?>>Nessuna (porta 25)</option>
                                </select>
                            </div>
                        </div>

                        <hr class="alm-section-divider">

                        <div class="alm-field">
                            <label for="smtp-user">Username SMTP</label>
                            <input type="email" id="smtp-user"
                                   value="<?php echo esc_attr($smtp_cfg['username'] ?? ''); ?>"
                                   placeholder="noreply@almaretna.it"
                                   <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                            <span class="alm-hint">Di solito coincide con l'indirizzo email del mittente.</span>
                        </div>

                        <div class="alm-field">
                            <label for="smtp-pass">Password SMTP</label>
                            <div class="alm-input-row">
                                <input type="password" id="smtp-pass"
                                       value="<?php echo ($smtp_cfg['password'] ?? '') !== '' ? '••••••••' : ''; ?>"
                                       placeholder="<?php echo esc_attr($smtp_from_cfg ? 'Definita in wp-config.php' : 'Lascia vuoto per non modificare'); ?>"
                                       autocomplete="new-password"
                                       <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                                <?php if (!$smtp_from_cfg) : ?>
                                <button type="button" class="button button-small alm-reveal" data-for="smtp-pass">Mostra</button>
                                <?php endif; ?>
                            </div>
                            <?php if (!$smtp_from_cfg) : ?>
                            <span class="alm-hint">Per Gmail usa un'<strong>App Password</strong>, non la password dell'account.</span>
                            <?php endif; ?>
                        </div>

                        <hr class="alm-section-divider">

                        <div class="alm-field-grid">
                            <div class="alm-field">
                                <label for="smtp-from-email">Email mittente</label>
                                <input type="email" id="smtp-from-email"
                                       value="<?php echo esc_attr($smtp_cfg['from_email'] ?? ''); ?>"
                                       placeholder="noreply@almaretna.it"
                                       <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                            </div>
                            <div class="alm-field">
                                <label for="smtp-from-name">Nome mittente</label>
                                <input type="text" id="smtp-from-name"
                                       value="<?php echo esc_attr($smtp_cfg['from_name'] ?? ''); ?>"
                                       placeholder="Almaretna"
                                       <?php echo $smtp_from_cfg ? 'readonly' : ''; ?> />
                            </div>
                        </div>

                    </div><!-- /.alm-field-list -->

                    <?php if (!$smtp_from_cfg) : ?>
                    <div id="alm-smtp-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button button-primary"
                                onclick="almSaveSmtp('<?php echo esc_js(wp_create_nonce('alm_save_smtp')); ?>')">
                            Salva configurazione
                        </button>
                    </div>
                    <?php else : ?>
                    <p class="alm-hint" style="margin-top:16px;">Per modificare, aggiorna le costanti in <code>wp-config.php</code>.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Test email -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Test invio email</h2>
                        <p class="alm-card-desc">Verifica che la configurazione SMTP funzioni prima di andare live.</p>
                    </div>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-field">
                        <label for="smtp-test-to">Destinatario test</label>
                        <input type="email" id="smtp-test-to"
                               value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>"
                               placeholder="tua@email.it"
                               <?php echo !$smtp_configured ? 'disabled' : ''; ?> />
                    </div>
                    <div id="alm-smtp-test-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button" id="alm-smtp-test-btn"
                                <?php echo !$smtp_configured ? 'disabled' : ''; ?>
                                onclick="almTestSmtp('<?php echo esc_js(wp_create_nonce('alm_test_smtp')); ?>')">
                            ✉ Invia email di test
                        </button>
                        <?php if (!$smtp_configured) : ?>
                        <span class="alm-hint">Salva prima la configurazione SMTP.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Guida provider -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div><h2>Impostazioni per provider comuni</h2></div>
                </div>
                <div class="alm-settings-card__body" style="padding:0;">
                    <table class="widefat striped" style="border:none;box-shadow:none;">
                        <thead>
                            <tr>
                                <th>Provider</th>
                                <th>Server</th>
                                <th style="text-align:center;">Porta</th>
                                <th>Cifratura</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td><strong>Gmail</strong></td><td><code>smtp.gmail.com</code></td><td style="text-align:center;">587</td><td>TLS</td><td>App Password + 2FA</td></tr>
                            <tr><td><strong>Aruba</strong></td><td><code>smtps.aruba.it</code></td><td style="text-align:center;">465</td><td>SSL</td><td>Username = email completa</td></tr>
                            <tr><td><strong>SiteGround</strong></td><td><code>mail.siteground.com</code></td><td style="text-align:center;">587</td><td>TLS</td><td>O server del dominio</td></tr>
                            <tr><td><strong>Outlook / 365</strong></td><td><code>smtp.office365.com</code></td><td style="text-align:center;">587</td><td>TLS</td><td>Username = indirizzo M365</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: SISTEMA
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-sistema" class="alm-tab-pane <?php echo $active_tab === 'sistema' ? 'is-active' : ''; ?>">
        <div class="alm-tab-body">

            <!-- Checklist -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Checklist pre-lancio</h2>
                        <p class="alm-card-desc">Tutto verde = sito pronto per andare online.</p>
                    </div>
                </div>
                <ul class="alm-checklist">
                    <?php foreach (alm_run_launch_checklist() as $chk) : ?>
                    <li class="alm-checklist__item <?php echo $chk['ok'] ? 'is-ok' : 'is-warn'; ?>">
                        <span class="dashicons <?php echo $chk['ok'] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                        <span><?php echo esc_html($chk['label']); ?></span>
                        <?php if (!empty($chk['action'])) : ?>
                        <a href="<?php echo esc_url($chk['action']); ?>" class="button button-small" style="margin-left:auto;">Vai →</a>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Pending timeout -->
            <?php
            $pending_timeout = (int) get_option('alm_pending_timeout_minutes', 30);
            $pending_count   = (int) $GLOBALS['wpdb']->get_var(
                "SELECT COUNT(*) FROM {$GLOBALS['wpdb']->prefix}alm_bookings
                 WHERE status = 'pending' AND payment_status = 'unpaid'"
            );
            $next_expire     = wp_next_scheduled('alm_expire_pending_bookings');
            ?>
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>⏱ Prenotazioni non pagate — timeout</h2>
                        <p class="alm-card-desc">Le prenotazioni abbandonate (pending + non pagate) vengono annullate automaticamente dopo questo tempo, liberando la disponibilità.</p>
                    </div>
                    <span class="alm-badge <?php echo $pending_count > 0 ? 'alm-badge--warn' : 'alm-badge--ok'; ?>">
                        <?php echo $pending_count; ?> in attesa
                    </span>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-field-grid">
                        <div class="alm-field">
                            <label for="alm-pending-timeout">Timeout (minuti)</label>
                            <input type="number" id="alm-pending-timeout" min="5" max="1440"
                                   value="<?php echo esc_attr((string) $pending_timeout); ?>"
                                   style="width:120px;" />
                            <span class="alm-hint">Consigliato: 30 min. Minimo 5, massimo 1440 (24h).</span>
                        </div>
                        <div class="alm-field">
                            <label>Prossima esecuzione automatica</label>
                            <p style="margin:6px 0 0;font-size:13px;color:#374151;">
                                <?php echo $next_expire ? esc_html(date_i18n('d/m/Y H:i', $next_expire)) : '<span style="color:#dc2626">Non schedulata</span>'; ?>
                            </p>
                        </div>
                    </div>
                    <div id="alm-pending-msg" class="alm-msg"></div>
                    <div class="alm-form-actions">
                        <button type="button" class="button button-primary"
                                onclick="almSavePendingTimeout('<?php echo esc_js(wp_create_nonce('alm_save_pending_timeout')); ?>')">
                            Salva timeout
                        </button>
                        <button type="button" class="button" style="margin-left:8px;"
                                onclick="almExpirePendingNow('<?php echo esc_js(wp_create_nonce('alm_expire_pending_now')); ?>')">
                            🗑 Pulisci ora (<?php echo $pending_count; ?> in attesa)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Diagnostica -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Diagnostica</h2>
                        <p class="alm-card-desc">Strumenti di manutenzione per sviluppatori.</p>
                    </div>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-form-actions" style="justify-content:space-between;align-items:flex-start;">
                        <div>
                            <strong style="font-size:13px;color:#111827;">Cache PHP (OPcache)</strong>
                            <p class="alm-hint" style="margin:3px 0 0;">Svuota se le modifiche al plugin non sembrano applicate.</p>
                        </div>
                        <button type="button" class="button"
                                onclick="almResetOpcache('<?php echo esc_js(wp_create_nonce('alm_reset_opcache')); ?>')">
                            ↺ Svuota cache
                        </button>
                    </div>
                    <div class="alm-section-divider"></div>
                    <div class="alm-debug-info">
                        <span>Plugin <strong><?php echo esc_html(ALM_BOOKING_VERSION); ?></strong></span>
                        <span>PHP <strong><?php echo esc_html(PHP_VERSION); ?></strong></span>
                        <span>WP <strong><?php echo esc_html(get_bloginfo('version')); ?></strong></span>
                        <span>settings.php <strong><?php echo esc_html(date('d/m/Y H:i', filemtime(__FILE__))); ?></strong></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div><!-- /.alm-admin-wrap -->

<script>
/* ─── Tab navigation ─── */
function almSwitchTab(tab) {
    document.querySelectorAll('.alm-tab-pane').forEach(function(p){ p.classList.remove('is-active'); });
    document.querySelectorAll('.alm-tab-btn').forEach(function(b){ b.classList.remove('is-active'); });
    var pane = document.getElementById('alm-tab-' + tab);
    if (pane) pane.classList.add('is-active');
    document.querySelectorAll('[data-tab="' + tab + '"]').forEach(function(b){ b.classList.add('is-active'); });
    try { localStorage.setItem('alm_stab', tab); } catch(e) {}
}
document.querySelectorAll('.alm-tab-btn').forEach(function(b){
    b.addEventListener('click', function(){ almSwitchTab(this.dataset.tab); });
});
(function(){
    try {
        var s = localStorage.getItem('alm_stab');
        if (s && !<?php echo !empty($_GET['updated']) ? 'true' : 'false'; ?>) almSwitchTab(s);
    } catch(e) {}
})();

/* ─── Accordion ─── */
function almToggle(id) {
    var body = document.getElementById('alm-' + id + '-body');
    var chev = document.getElementById('alm-' + id + '-chev');
    if (!body) return;
    var open = body.style.display === 'block';
    body.style.display = open ? 'none' : 'block';
    if (chev) chev.classList.toggle('is-open', !open);
}
/* Auto-apri card non configurate quando il tab diventa visibile */
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!$stripe_configured) : ?>almToggle('stripe');<?php endif; ?>
    <?php if (!$b24_configured)    : ?>almToggle('b24');<?php endif; ?>
    <?php if (!$ga4_locked)        : ?>almToggle('ga4');<?php endif; ?>
    <?php if (!$gsc_locked)        : ?>almToggle('gsc');<?php endif; ?>
    <?php if (!$gapi_configured)   : ?>almToggle('gapi');<?php endif; ?>
});

/* ─── Reveal password ─── */
document.querySelectorAll('.alm-reveal').forEach(function(btn){
    btn.addEventListener('click', function(){
        var inp = document.getElementById(this.dataset.for);
        if (!inp) return;
        inp.type = inp.type === 'password' ? 'text' : 'password';
        this.textContent = inp.type === 'password' ? 'Mostra' : 'Nascondi';
    });
});

/* ─── Copy to clipboard ─── */
function almCopy(id, btn) {
    var el = document.getElementById(id);
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim()).then(function(){
        var orig = btn.textContent;
        btn.textContent = '✓ Copiato!';
        setTimeout(function(){ btn.textContent = orig; }, 2000);
    }).catch(function(){
        var r = document.createRange(); r.selectNode(el);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(r);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        var orig2 = btn.textContent;
        btn.textContent = '✓ Copiato!';
        setTimeout(function(){ btn.textContent = orig2; }, 2000);
    });
}

/* ─── Show message ─── */
function almMsg(id, text, ok) {
    var el = document.getElementById(id);
    if (!el) return;
    if (!text) { el.style.display = 'none'; return; }
    el.style.display = 'block';
    el.className = 'alm-msg ' + (ok ? 'alm-msg--ok' : 'alm-msg--err');
    el.textContent = text;
}

/* ─── Struttura (AJAX save) ─── */
function almSaveStruttura(nonce) {
    var fd = new FormData();
    fd.append('action','alm_save_struttura'); fd.append('nonce',nonce);
    fd.append('host_name',        (document.getElementById('host_name')            ||{value:''}).value.trim());
    fd.append('host_email',       (document.getElementById('host_email')           ||{value:''}).value.trim());
    fd.append('host_phone',       (document.getElementById('host_phone')           ||{value:''}).value.trim());
    fd.append('whatsapp_webhook_url',(document.getElementById('whatsapp_webhook_url')||{value:''}).value.trim());
    fd.append('checkin_time',     (document.getElementById('checkin_time')         ||{value:''}).value);
    fd.append('checkout_time',    (document.getElementById('checkout_time')        ||{value:''}).value);
    fd.append('min_stay_global',  (document.getElementById('min_stay_global')      ||{value:'1'}).value);
    fd.append('beds24_enabled',   (document.getElementById('beds24_enabled')       ||{checked:false}).checked ? 1 : 0);
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-struttura-msg', d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            btn.disabled=false; btn.textContent=orig;
        }).catch(function(){
            almMsg('alm-struttura-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}

/* ─── OPcache ─── */
function almResetOpcache(nonce) {
    var btn   = event.currentTarget;
    var msgEl = document.getElementById('alm-opcache-msg');
    var orig  = btn.textContent;
    btn.disabled = true; btn.textContent = '…';
    var fd = new FormData();
    fd.append('action','alm_reset_opcache'); fd.append('nonce',nonce);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (msgEl){ msgEl.style.color = d.success ? '#059669' : '#dc2626'; msgEl.textContent = d.data||(d.success?'✓ Fatto':'✗ Errore'); }
            btn.disabled = false; btn.textContent = orig;
            if (d.success) setTimeout(function(){ location.reload(); },1500);
        }).catch(function(){
            if (msgEl){ msgEl.style.color='#dc2626'; msgEl.textContent='Errore di rete'; }
            btn.disabled = false; btn.textContent = orig;
        });
}

/* ─── Stripe DB save ─── */
function almSaveStripeDb(nonce) {
    var pk = (document.getElementById('s-pk')||{value:''}).value.trim();
    var sk = (document.getElementById('s-sk')||{value:''}).value.trim();
    var wh = (document.getElementById('s-wh')||{value:''}).value.trim();
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action','alm_save_stripe_db'); fd.append('nonce',nonce);
    fd.append('pk',pk); fd.append('sk',sk); fd.append('whsec',wh);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-stripe-db-msg', d.data?.message||d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            if (d.success) setTimeout(function(){ location.reload(); },1400);
            else { btn.disabled=false; btn.textContent=orig; }
        }).catch(function(){
            almMsg('alm-stripe-db-msg','Errore di rete. Riprova.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}

/* ─── Beds24 ─── */
function almSaveBeds24(nonce) {
    var token   = (document.getElementById('b24-token')  ||{value:''}).value.trim();
    var propkey = (document.getElementById('b24-propkey')||{value:''}).value.trim();
    var wh      = (document.getElementById('b24-wh')     ||{value:''}).value.trim();
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action','alm_save_beds24'); fd.append('nonce',nonce);
    fd.append('api_token',token); fd.append('prop_key',propkey); fd.append('webhook_token',wh);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-b24-msg', d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            if (d.success) setTimeout(function(){ location.reload(); },1400);
            else { btn.disabled=false; btn.textContent=orig; }
        }).catch(function(){
            almMsg('alm-b24-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}
function almTestBeds24(nonce) {
    fetch(ajaxurl+'?action=alm_test_beds24&nonce='+nonce)
        .then(function(r){ return r.json(); })
        .then(function(d){ almMsg('alm-b24-msg', d.data||(d.success?'✓ OK':'✗ Errore'), d.success); });
}

/* ─── Analytics (GA4 + GSC) ─── */
function almSaveAnalytics(service, nonce) {
    var val, action;
    if (service === 'ga4') { val = (document.getElementById('alm-ga4-id')||{value:''}).value.trim(); action = 'alm_save_ga4'; }
    else                   { val = (document.getElementById('alm-gsc-code')||{value:''}).value.trim(); action = 'alm_save_gsc'; }
    if (!val) return;
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action',action); fd.append('nonce',nonce);
    fd.append(service==='ga4'?'id':'code', val);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-'+service+'-msg', d.data?.message||d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            if (d.success) setTimeout(function(){ location.reload(); },1400);
            else { btn.disabled=false; btn.textContent=orig; }
        }).catch(function(){
            almMsg('alm-'+service+'-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}
function almUnlock(service, nonce) {
    if (!confirm('Sbloccare la card ' + service.toUpperCase() + ' per modificarla?')) return;
    var fd = new FormData();
    fd.append('action','alm_unlock_'+service); fd.append('nonce',nonce);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(){ location.reload(); });
}

/* ─── SMTP ─── */
function almSaveSmtp(nonce) {
    var host  = (document.getElementById('smtp-host')      ||{value:''}).value.trim();
    var port  = (document.getElementById('smtp-port')      ||{value:'587'}).value.trim();
    var enc   = (document.getElementById('smtp-enc')       ||{value:'tls'}).value;
    var user  = (document.getElementById('smtp-user')      ||{value:''}).value.trim();
    var pass  = (document.getElementById('smtp-pass')      ||{value:''}).value;
    var fe    = (document.getElementById('smtp-from-email')||{value:''}).value.trim();
    var fn    = (document.getElementById('smtp-from-name') ||{value:''}).value.trim();
    if (!host || !user) { almMsg('alm-smtp-msg','Server e username sono obbligatori.',false); return; }
    // non inviare il placeholder "••••••••" come password
    if (pass === '••••••••') pass = '';
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action','alm_save_smtp'); fd.append('nonce',nonce);
    fd.append('host',host); fd.append('port',port); fd.append('encryption',enc);
    fd.append('username',user); fd.append('password',pass);
    fd.append('from_email',fe); fd.append('from_name',fn);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-smtp-msg', d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            if (d.success) setTimeout(function(){ location.reload(); },1400);
            else { btn.disabled=false; btn.textContent=orig; }
        }).catch(function(){
            almMsg('alm-smtp-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}
function almTestSmtp(nonce) {
    var to  = (document.getElementById('smtp-test-to')||{value:''}).value.trim();
    if (!to) { almMsg('alm-smtp-test-msg','Inserisci un indirizzo email destinatario.',false); return; }
    var btn = document.getElementById('alm-smtp-test-btn');
    if (btn) { btn.disabled=true; btn.textContent='Invio in corso…'; }
    almMsg('alm-smtp-test-msg','','');  // nasconde il messaggio precedente
    var fd = new FormData();
    fd.append('action','alm_test_smtp'); fd.append('nonce',nonce); fd.append('to',to);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-smtp-test-msg', d.data||(d.success?'✓ Email inviata':'✗ Errore'), d.success);
            if (btn) { btn.disabled=false; btn.textContent='✉ Invia email di test'; }
        }).catch(function(){
            almMsg('alm-smtp-test-msg','Errore di rete — controlla la connessione.',false);
            if (btn) { btn.disabled=false; btn.textContent='✉ Invia email di test'; }
        });
}

/* ─── Pending timeout ─── */
function almSavePendingTimeout(nonce) {
    var val = parseInt((document.getElementById('alm-pending-timeout')||{value:'30'}).value, 10);
    if (isNaN(val) || val < 5) { almMsg('alm-pending-msg','Valore minimo: 5 minuti.',false); return; }
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action','alm_save_pending_timeout'); fd.append('nonce',nonce); fd.append('minutes',val);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-pending-msg', d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            btn.disabled=false; btn.textContent=orig;
        }).catch(function(){
            almMsg('alm-pending-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}
function almExpirePendingNow(nonce) {
    if (!confirm('Cancellare subito tutte le prenotazioni pending non pagate?')) return;
    var btn = event.currentTarget; btn.disabled=true; var orig=btn.textContent; btn.textContent='…';
    var fd = new FormData();
    fd.append('action','alm_expire_pending_now'); fd.append('nonce',nonce);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-pending-msg', d.data||(d.success?'✓ Fatto':'✗ Errore'), d.success);
            btn.disabled=false; btn.textContent=orig;
            if (d.success) setTimeout(function(){ location.reload(); },1500);
        }).catch(function(){
            almMsg('alm-pending-msg','Errore di rete.',false);
            btn.disabled=false; btn.textContent=orig;
        });
}

/* ─── Google API / Service Account ─── */
function almSaveGapi(nonce) {
    var sa   = (document.getElementById('alm-gapi-sa')  ||{value:''}).value.trim();
    var prop = (document.getElementById('alm-gapi-prop')||{value:''}).value.trim();
    var site = (document.getElementById('alm-gapi-site')||{value:''}).value.trim();
    if (sa) { try { JSON.parse(sa); } catch(e){ almMsg('alm-gapi-msg','JSON non valido: '+e.message,false); return; } }
    var fd = new FormData();
    fd.append('action','alm_save_google_api'); fd.append('nonce',nonce);
    fd.append('sa_json',sa); fd.append('property_id',prop); fd.append('site_url',site);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            almMsg('alm-gapi-msg', d.data||(d.success?'✓ Salvato':'✗ Errore'), d.success);
            if (d.success) setTimeout(function(){ location.reload(); },1400);
        }).catch(function(){ almMsg('alm-gapi-msg','Errore di rete.',false); });
}
function almTestApi(service, nonce) {
    almMsg('alm-gapi-msg','Test '+service.toUpperCase()+' in corso…',true);
    var fd = new FormData();
    fd.append('action','alm_test_'+service); fd.append('nonce',nonce);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){ almMsg('alm-gapi-msg', d.data||(d.success?'✓ OK':'✗ Errore'), d.success); });
}
</script>

<?php
function alm_run_launch_checklist(): array {
    $checks = [];

    $checks[] = ['label' => 'Stripe: chiavi API configurate',                   'ok' => ALM_Stripe::is_configured(),    'action' => ''];
    if (ALM_Stripe::is_configured()) {
        $checks[] = ['label' => 'Stripe: modalità ' . (ALM_Stripe::is_test_mode() ? 'TEST ⚠ (cambia in LIVE prima del lancio)' : 'LIVE ✓'), 'ok' => !ALM_Stripe::is_test_mode(), 'action' => ''];
    }
    $checks[] = ['label' => 'Beds24: API token configurato',                     'ok' => ALM_Beds24::is_configured(),    'action' => ''];

    $host_email = get_option('alm_booking_settings')['host_email'] ?? '';
    $checks[] = ['label' => 'Email struttura configurata',                       'ok' => !empty($host_email),            'action' => admin_url('admin.php?page=alm-settings')];

    $rooms_count = wp_count_posts('almaretna_room')->publish ?? 0;
    $checks[] = ['label' => 'Camere inserite (' . $rooms_count . ')',            'ok' => $rooms_count > 0,               'action' => admin_url('post-new.php?post_type=almaretna_room')];

    $prenota_page = get_page_by_path('prenota');
    $checks[] = ['label' => 'Pagina "Prenota" creata',                           'ok' => $prenota_page !== null,         'action' => admin_url('post-new.php?post_type=page')];

    $checks[] = ['label' => 'Permalink non-plain (consigliato: /%postname%/)',   'ok' => get_option('permalink_structure') !== '', 'action' => admin_url('options-permalink.php')];
    $checks[] = ['label' => 'Sito su HTTPS',                                     'ok' => is_ssl() || str_starts_with(home_url(), 'https://'), 'action' => ''];
    $checks[] = ['label' => 'WP-Cron attivo (necessario per email reminder)',    'ok' => !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON, 'action' => ''];

    return $checks;
}
