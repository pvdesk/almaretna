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
$valid_tabs = ['struttura', 'pagamenti', 'canali', 'analytics', 'sistema'];
$active_tab = in_array($_GET['alm_tab'] ?? '', $valid_tabs, true) ? $_GET['alm_tab'] : 'struttura';
if (!empty($_GET['updated'])) $active_tab = 'struttura';
?>
<div class="wrap alm-admin-wrap">

    <!-- Intestazione -->
    <div class="alm-page-header">
        <h1><span class="dashicons dashicons-admin-settings"></span> Impostazioni</h1>
        <div class="alm-page-header__actions">
            <span class="alm-version-chip">v<?php echo esc_html(ALM_BOOKING_VERSION); ?></span>
            <button type="button" class="button button-small" id="alm-opcache-btn"
                    onclick="almResetOpcache('<?php echo esc_js(wp_create_nonce('alm_reset_opcache')); ?>')">
                ↺ Svuota cache PHP
            </button>
            <span id="alm-opcache-msg"></span>
        </div>
    </div>

    <?php if (!empty($_GET['updated'])) : ?>
    <div class="notice notice-success is-dismissible"><p>✓ Impostazioni salvate correttamente.</p></div>
    <?php endif; ?>

    <!-- Barra di stato rapida -->
    <div class="alm-status-bar">
        <button type="button" class="alm-status-chip <?php echo $stripe_configured ? ($stripe_from_cfg || !empty($stripe_db_keys['secret_key']) ? 'is-ok' : 'is-warn') : 'is-miss'; ?>"
                onclick="almSwitchTab('pagamenti')">
            <span class="dashicons <?php echo $stripe_configured ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            Stripe<?php if ($stripe_configured): ?> <small><?php echo ALM_Stripe::is_test_mode() ? 'TEST' : 'LIVE'; ?></small><?php endif; ?>
        </button>
        <button type="button" class="alm-status-chip <?php echo $b24_configured ? 'is-ok' : 'is-miss'; ?>"
                onclick="almSwitchTab('canali')">
            <span class="dashicons <?php echo $b24_configured ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            Beds24
        </button>
        <button type="button" class="alm-status-chip <?php echo $ga4_locked ? 'is-ok' : ($ga4_id ? 'is-warn' : 'is-miss'); ?>"
                onclick="almSwitchTab('analytics')">
            <span class="dashicons <?php echo $ga4_locked ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            Analytics
        </button>
        <button type="button" class="alm-status-chip <?php echo $gsc_locked ? 'is-ok' : ($gsc_code ? 'is-warn' : 'is-miss'); ?>"
                onclick="almSwitchTab('analytics')">
            <span class="dashicons <?php echo $gsc_locked ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            Search Console
        </button>
        <?php
        $done = (int)$stripe_configured + (int)$b24_configured + (int)$ga4_locked + (int)$gsc_locked;
        ?>
        <span class="alm-status-bar__summary"><?php echo esc_html($done); ?>/4 servizi configurati</span>
    </div>

    <!-- Navigazione tab -->
    <nav class="alm-tab-nav" role="tablist">
        <?php
        $tabs = [
            'struttura' => ['dashicons-building',   'Struttura', false],
            'pagamenti' => ['dashicons-money-alt',   'Pagamenti', !$stripe_configured],
            'canali'    => ['dashicons-admin-links',  'Canali',    !$b24_configured],
            'analytics' => ['dashicons-chart-bar',    'Analytics', !$ga4_locked || !$gsc_locked],
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

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('alm_save_settings'); ?>
            <input type="hidden" name="action" value="alm_save_settings" />

            <div class="alm-settings-card" style="max-width:700px;">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Informazioni struttura</h2>
                        <p class="alm-card-desc">Questi dati compaiono nelle email agli ospiti e nel sito.</p>
                    </div>
                </div>
                <div class="alm-settings-card__body">
                    <div class="alm-field-grid">
                        <div class="alm-field alm-field--full">
                            <label for="host_name">Nome struttura</label>
                            <input type="text" id="host_name" name="host_name" class="regular-text"
                                   value="<?php echo esc_attr($s['host_name'] ?? 'Almaretna'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="host_email">Email <span class="alm-hint">mittente e destinatario email prenotazioni</span></label>
                            <input type="email" id="host_email" name="host_email" class="regular-text"
                                   value="<?php echo esc_attr($s['host_email'] ?? get_option('admin_email')); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="host_phone">Telefono</label>
                            <input type="tel" id="host_phone" name="host_phone" class="regular-text"
                                   value="<?php echo esc_attr($s['host_phone'] ?? ''); ?>"
                                   placeholder="+39 095 000 0000" />
                        </div>
                        <div class="alm-field">
                            <label for="checkin_time">Check-in</label>
                            <input type="time" id="checkin_time" name="checkin_time"
                                   value="<?php echo esc_attr($s['checkin_time'] ?? '15:00'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="checkout_time">Check-out</label>
                            <input type="time" id="checkout_time" name="checkout_time"
                                   value="<?php echo esc_attr($s['checkout_time'] ?? '11:00'); ?>" />
                        </div>
                        <div class="alm-field">
                            <label for="min_stay_global">Soggiorno minimo <span class="alm-hint">notti · override per camera</span></label>
                            <input type="number" id="min_stay_global" name="min_stay_global"
                                   class="small-text" min="1"
                                   value="<?php echo esc_attr((string) ($s['min_stay_global'] ?? 1)); ?>" />
                        </div>
                    </div>

                    <div class="alm-section-divider"></div>

                    <label class="alm-toggle-label">
                        <input type="checkbox" name="beds24_enabled" value="1"
                               <?php checked(!empty($s['beds24_enabled'])); ?> />
                        <span class="alm-toggle-text">
                            Sincronizzazione Beds24 attiva
                            <span class="alm-hint" style="display:block;margin-top:2px;">Aggiorna disponibilità e prezzi da Beds24 automaticamente (2× al giorno)</span>
                        </span>
                    </label>
                </div>
            </div>

            <div style="margin-top:16px;">
                <button type="submit" class="button button-primary button-large">Salva impostazioni</button>
            </div>
        </form>
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: PAGAMENTI
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-pagamenti" class="alm-tab-pane <?php echo $active_tab === 'pagamenti' ? 'is-active' : ''; ?>">

        <?php if (!$stripe_configured) : ?>
        <div class="alm-banner alm-banner--info" style="max-width:700px;margin-bottom:20px;">
            <span class="dashicons dashicons-info-outline"></span>
            <div>
                <strong>Come attivare i pagamenti online</strong>
                <p>Stripe è il sistema usato per accettare pagamenti con carta di credito. È gratuito da usare — paghi solo quando ricevi un pagamento.</p>
                <a href="https://dashboard.stripe.com/register" target="_blank" rel="noopener" class="button button-small" style="margin-top:6px;">Crea account Stripe gratuito →</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card Stripe chiavi -->
        <div class="alm-settings-card" id="alm-stripe-card" style="max-width:700px;margin-bottom:20px;">
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
                <div id="alm-stripe-db-msg" style="display:none;margin-top:12px;" class="alm-msg"></div>
                <div style="margin-top:16px;display:flex;gap:10px;align-items:center;">
                    <button type="button" class="button button-primary"
                            onclick="almSaveStripeDb('<?php echo esc_js(wp_create_nonce('alm_save_stripe_db')); ?>')">
                        Salva chiavi Stripe
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card URL webhook -->
        <div class="alm-settings-card" style="max-width:700px;">
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
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: CANALI
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-canali" class="alm-tab-pane <?php echo $active_tab === 'canali' ? 'is-active' : ''; ?>">

        <?php if (!$b24_configured) : ?>
        <div class="alm-banner alm-banner--info" style="max-width:700px;margin-bottom:20px;">
            <span class="dashicons dashicons-admin-links"></span>
            <div>
                <strong>Collega Beds24 per sincronizzare i calendari</strong>
                <p>Beds24 mantiene allineata la disponibilità su tutti i portali (Airbnb, Booking.com, ecc.). Se non lo usi, puoi ignorare questa sezione.</p>
            </div>
        </div>
        <?php endif; ?>

        <div class="alm-settings-card" id="alm-b24-card" style="max-width:700px;">
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
                <div id="alm-b24-msg" style="display:none;margin-top:12px;" class="alm-msg"></div>
                <div style="display:flex;gap:10px;margin-top:16px;flex-wrap:wrap;">
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
                <p style="font-size:13px;font-weight:600;margin:0 0 8px;">URL Webhook Beds24</p>
                <div class="alm-copybox">
                    <code id="alm-b24-wh-url"><?php echo esc_html(get_rest_url(null, 'alm/v1/beds24/webhook')); ?></code>
                    <button type="button" class="button button-small" onclick="almCopy('alm-b24-wh-url',this)">Copia</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════
         TAB: ANALYTICS
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-analytics" class="alm-tab-pane <?php echo $active_tab === 'analytics' ? 'is-active' : ''; ?>">
        <div style="max-width:700px;">

            <!-- GA4 -->
            <div class="alm-settings-card" id="alm-ga4-card" style="margin-bottom:16px;">
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
                        <input type="text" id="alm-ga4-id" class="regular-text"
                               value="<?php echo esc_attr($ga4_id); ?>"
                               placeholder="G-XXXXXXXXXX" style="font-family:monospace;" />
                    </div>
                    <div id="alm-ga4-msg" style="display:none;margin-top:10px;" class="alm-msg"></div>
                    <button type="button" class="button button-primary" style="margin-top:14px;"
                            onclick="almSaveAnalytics('ga4','<?php echo esc_js(wp_create_nonce('alm_save_ga4')); ?>')">
                        Salva e attiva
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- GSC -->
            <div class="alm-settings-card" id="alm-gsc-card" style="margin-bottom:16px;">
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
                    <div class="alm-field" style="max-width:480px;">
                        <label for="alm-gsc-code">Codice di verifica</label>
                        <input type="text" id="alm-gsc-code" class="large-text"
                               value="<?php echo esc_attr($gsc_code); ?>" placeholder="AbCdEf1234…" />
                    </div>
                    <div id="alm-gsc-msg" style="display:none;margin-top:10px;" class="alm-msg"></div>
                    <button type="button" class="button button-primary" style="margin-top:14px;"
                            onclick="almSaveAnalytics('gsc','<?php echo esc_js(wp_create_nonce('alm_save_gsc')); ?>')">
                        Salva e verifica
                    </button>
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
                        <div class="alm-field" style="max-width:400px;">
                            <label for="alm-gapi-prop">GA4 Property ID <span class="alm-hint">es. properties/123456789</span></label>
                            <input type="text" id="alm-gapi-prop" class="regular-text"
                                   value="<?php echo esc_attr($gapi_prop); ?>" placeholder="properties/XXXXXXXXX" />
                        </div>
                        <div class="alm-field" style="max-width:400px;">
                            <label for="alm-gapi-site">GSC Site URL</label>
                            <input type="url" id="alm-gapi-site" class="regular-text"
                                   value="<?php echo esc_attr($gapi_site); ?>" placeholder="https://www.almaretna.it/" />
                        </div>
                    </div>
                    <div id="alm-gapi-msg" style="display:none;margin-top:12px;" class="alm-msg"></div>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:16px;">
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
         TAB: SISTEMA
    ══════════════════════════════════════════════════ -->
    <div id="alm-tab-sistema" class="alm-tab-pane <?php echo $active_tab === 'sistema' ? 'is-active' : ''; ?>">
        <div style="max-width:700px;">

            <!-- Checklist -->
            <div class="alm-settings-card" style="margin-bottom:16px;">
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

            <!-- Diagnostica -->
            <div class="alm-settings-card">
                <div class="alm-settings-card__head">
                    <div>
                        <h2>Diagnostica</h2>
                        <p class="alm-card-desc">Strumenti di manutenzione per sviluppatori.</p>
                    </div>
                </div>
                <div class="alm-settings-card__body">
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                        <div>
                            <strong style="font-size:13px;">Cache PHP (OPcache)</strong>
                            <p class="alm-hint" style="margin:2px 0 0;">Svuota se le modifiche al plugin non sembrano applicate.</p>
                        </div>
                        <button type="button" class="button" style="margin-left:auto;white-space:nowrap;"
                                onclick="almResetOpcache('<?php echo esc_js(wp_create_nonce('alm_reset_opcache')); ?>')">
                            ↺ Svuota cache PHP
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
    el.style.display = 'block';
    el.className = 'alm-msg ' + (ok ? 'alm-msg--ok' : 'alm-msg--err');
    el.textContent = text;
}

/* ─── OPcache ─── */
function almResetOpcache(nonce) {
    var btn  = event.currentTarget;
    var msgEl = document.getElementById('alm-opcache-msg');
    var orig = btn.textContent;
    btn.disabled = true; btn.textContent = '…';
    var fd = new FormData();
    fd.append('action','alm_reset_opcache'); fd.append('nonce',nonce);
    fetch(ajaxurl,{method:'POST',credentials:'same-origin',body:fd})
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (msgEl){ msgEl.style.color = d.success ? '#2e7d32' : '#c62828'; msgEl.textContent = d.data||(d.success?'✓ Fatto':'✗ Errore'); }
            btn.disabled = false; btn.textContent = orig;
            if (d.success) setTimeout(function(){ location.reload(); },1500);
        }).catch(function(){
            if (msgEl){ msgEl.style.color='#c62828'; msgEl.textContent='Errore di rete'; }
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
