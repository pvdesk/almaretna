<?php
/**
 * Admin view — Impostazioni plugin
 *
 * Variabile: $settings (array)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$s = is_array($settings) ? $settings : [];
?>

<div class="wrap alm-admin-wrap">
    <h1>
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e('Impostazioni Almaretna Booking', 'almaretna-booking'); ?>
    </h1>

    <?php if (!empty($_GET['updated'])) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Impostazioni salvate.', 'almaretna-booking'); ?></p></div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('alm_save_settings'); ?>
        <input type="hidden" name="action" value="alm_save_settings" />

        <!-- Struttura -->
        <div class="alm-admin-card" style="max-width:720px;margin-bottom:24px;">
            <div class="alm-admin-card__header">
                <h2><?php esc_html_e('Struttura', 'almaretna-booking'); ?></h2>
            </div>

            <table class="form-table" role="presentation">
                <tr>
                    <th><label for="host_name"><?php esc_html_e('Nome struttura', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="text" id="host_name" name="host_name" class="regular-text"
                               value="<?php echo esc_attr($s['host_name'] ?? 'Almaretna'); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="host_email"><?php esc_html_e('Email struttura', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="email" id="host_email" name="host_email" class="regular-text"
                               value="<?php echo esc_attr($s['host_email'] ?? get_option('admin_email')); ?>" />
                        <p class="description"><?php esc_html_e('Mittente e destinatario delle email transazionali.', 'almaretna-booking'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="host_phone"><?php esc_html_e('Telefono struttura', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="tel" id="host_phone" name="host_phone" class="regular-text"
                               value="<?php echo esc_attr($s['host_phone'] ?? ''); ?>"
                               placeholder="+39 095 000 0000" />
                        <p class="description"><?php esc_html_e('Mostrato nel footer e nelle email agli ospiti.', 'almaretna-booking'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="checkin_time"><?php esc_html_e('Orario check-in', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="time" id="checkin_time" name="checkin_time"
                               value="<?php echo esc_attr($s['checkin_time'] ?? '15:00'); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="checkout_time"><?php esc_html_e('Orario check-out', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="time" id="checkout_time" name="checkout_time"
                               value="<?php echo esc_attr($s['checkout_time'] ?? '11:00'); ?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="min_stay_global"><?php esc_html_e('Soggiorno minimo globale (notti)', 'almaretna-booking'); ?></label></th>
                    <td>
                        <input type="number" id="min_stay_global" name="min_stay_global" class="small-text"
                               min="1" value="<?php echo esc_attr((string) ($s['min_stay_global'] ?? 1)); ?>" />
                        <p class="description"><?php esc_html_e('Può essere sovrascritto per singola camera o tariffa.', 'almaretna-booking'); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Stripe (card collassata) -->
        <?php
        $stripe_configured = ALM_Stripe::is_configured();
        $stripe_from_cfg   = ALM_Stripe::keys_source() === 'config';
        $stripe_db_keys    = get_option('alm_stripe_keys', []);

        // Helper: mostra solo i primi 8 + asterischi per le chiavi sensibili
        $mask = static function (string $key): string {
            if ($key === '') return '';
            return substr($key, 0, 8) . str_repeat('•', max(0, strlen($key) - 12)) . substr($key, -4);
        };
        ?>
        <div class="alm-admin-card alm-stripe-card" id="alm-stripe-card" style="max-width:720px;margin-bottom:24px;">

            <!-- Header cliccabile -->
            <div class="alm-admin-card__header alm-stripe-card__toggle"
                 style="cursor:pointer;user-select:none;"
                 onclick="almStripeToggle()">
                <h2 style="display:flex;align-items:center;gap:10px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;opacity:.7"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Stripe
                </h2>
                <div style="display:flex;align-items:center;gap:10px;">
                    <?php if ($stripe_configured) : ?>
                        <span class="alm-badge alm-badge--<?php echo ALM_Stripe::is_test_mode() ? 'warning' : 'success'; ?>">
                            <?php echo ALM_Stripe::is_test_mode() ? 'TEST MODE' : 'LIVE ✓'; ?>
                        </span>
                    <?php else : ?>
                        <span class="alm-badge alm-badge--error">Non configurato</span>
                    <?php endif; ?>
                    <?php if ($stripe_from_cfg) : ?>
                        <span class="alm-badge" style="background:#e8f4fd;color:#0077cc;border:1px solid #c2ddf5;">wp-config.php</span>
                    <?php endif; ?>
                    <span id="alm-stripe-chevron" style="font-size:18px;color:#888;transition:transform .2s;">▾</span>
                </div>
            </div>

            <!-- Body (nascosto di default) -->
            <div id="alm-stripe-body" style="display:none;border-top:1px solid #e5e5e5;padding:20px;">

                <?php if ($stripe_from_cfg) : ?>
                <!-- Chiavi provengono da wp-config.php — sola lettura -->
                <div style="background:#e8f4fd;border:1px solid #c2ddf5;border-radius:4px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#0077cc;">
                    <strong>Chiavi definite in wp-config.php</strong> — hanno priorità sui campi sottostanti e non possono essere modificate da qui.
                    Per usare l'editor, rimuovi le costanti <code>ALM_STRIPE_*</code> da wp-config.php.
                </div>
                <table class="form-table" role="presentation">
                    <tr>
                        <th>Publishable Key</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(ALM_Stripe::get_publishable_key())); ?></code></td>
                    </tr>
                    <tr>
                        <th>Secret Key</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(defined('ALM_STRIPE_SECRET_KEY') ? ALM_STRIPE_SECRET_KEY : '')); ?></code></td>
                    </tr>
                    <tr>
                        <th>Webhook Secret</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(defined('ALM_STRIPE_WEBHOOK_SECRET') ? ALM_STRIPE_WEBHOOK_SECRET : '')); ?></code></td>
                    </tr>
                </table>

                <?php else : ?>
                <!-- Chiavi da DB — modificabili -->
                <p style="font-size:13px;color:#666;margin:0 0 16px;">
                    Inserisci le chiavi del tuo account Stripe. Trovi i valori nella
                    <a href="https://dashboard.stripe.com/apikeys" target="_blank" rel="noopener">Dashboard Stripe → Sviluppatori → Chiavi API</a>.
                    Il campo Secret Key è mascherato: lascialo vuoto per non modificarlo.
                </p>
                <table class="form-table" role="presentation">
                    <tr>
                        <th><label for="stripe_publishable_key">Publishable Key</label></th>
                        <td>
                            <input type="text" id="stripe_publishable_key" name="stripe_publishable_key"
                                   class="regular-text"
                                   value="<?php echo esc_attr($stripe_db_keys['publishable_key'] ?? ''); ?>"
                                   placeholder="pk_live_... oppure pk_test_..."
                                   autocomplete="off" />
                            <p class="description">Inizia con <code>pk_live_</code> (produzione) o <code>pk_test_</code> (test).</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="stripe_secret_key">Secret Key</label></th>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="password" id="stripe_secret_key" name="stripe_secret_key"
                                       class="regular-text"
                                       value=""
                                       placeholder="<?php echo !empty($stripe_db_keys['secret_key']) ? '••••••••' . substr($stripe_db_keys['secret_key'], -4) : 'sk_live_...'; ?>"
                                       autocomplete="new-password" />
                                <button type="button" class="button button-small"
                                        onclick="var f=document.getElementById('stripe_secret_key');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Mostra':'Nascondi';">Mostra</button>
                            </div>
                            <p class="description">
                                Inizia con <code>sk_live_</code>. Lascia vuoto per mantenere il valore già salvato.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="stripe_webhook_secret">Webhook Secret</label></th>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="password" id="stripe_webhook_secret" name="stripe_webhook_secret"
                                       class="regular-text"
                                       value=""
                                       placeholder="<?php echo !empty($stripe_db_keys['webhook_secret']) ? '••••••••' . substr($stripe_db_keys['webhook_secret'], -4) : 'whsec_...'; ?>"
                                       autocomplete="new-password" />
                                <button type="button" class="button button-small"
                                        onclick="var f=document.getElementById('stripe_webhook_secret');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Mostra':'Nascondi';">Mostra</button>
                            </div>
                            <p class="description">
                                Inizia con <code>whsec_</code>. Ottenuto nella Dashboard Stripe → Sviluppatori → Webhook → Endpoint → Signing secret. Lascia vuoto per mantenere il valore salvato.
                            </p>
                        </td>
                    </tr>
                </table>

                <div style="background:#fffbeb;border:1px solid #f0d080;border-radius:4px;padding:12px 16px;margin-top:16px;font-size:12px;color:#7a6000;">
                    <strong>Alternativa sicura:</strong> definisci le costanti in <code>wp-config.php</code> — avranno priorità assoluta e non verranno mai scritte nel database.
                    <br><code>define('ALM_STRIPE_SECRET_KEY', 'sk_live_...');</code><br>
                    <code>define('ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...');</code><br>
                    <code>define('ALM_STRIPE_WEBHOOK_SECRET', 'whsec_...');</code>
                </div>
                <?php endif; ?>

            </div><!-- /body -->
        </div>

        <script>
        function almStripeToggle() {
            var body    = document.getElementById('alm-stripe-body');
            var chevron = document.getElementById('alm-stripe-chevron');
            if (body.style.display === 'none') {
                body.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                body.style.display = 'none';
                chevron.style.transform = '';
            }
        }
        // Apri automaticamente se non configurata
        <?php if (!$stripe_configured) : ?>
        almStripeToggle();
        <?php endif; ?>
        </script>

        <!-- Stripe → wp-config.php (scrittura diretta, sola lettura una volta compilata) -->
        <?php
        $wpcfg_path     = ABSPATH . 'wp-config.php';
        if (!file_exists($wpcfg_path)) $wpcfg_path = dirname(ABSPATH) . '/wp-config.php';
        $wpcfg_writable = file_exists($wpcfg_path) && is_writable($wpcfg_path);
        $wpcfg_locked   = $stripe_from_cfg;
        $wpcfg_nonce    = wp_create_nonce('alm_write_stripe_config');
        ?>
        <div class="alm-admin-card" id="alm-wpcfg-card" style="max-width:720px;margin-bottom:24px;">

            <div class="alm-admin-card__header" style="cursor:pointer;user-select:none;" onclick="almWpcfgToggle()">
                <h2 style="display:flex;align-items:center;gap:10px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;opacity:.7"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Stripe — scrivi in wp-config.php
                </h2>
                <div style="display:flex;align-items:center;gap:10px;">
                    <?php if ($wpcfg_locked) : ?>
                        <span class="alm-badge alm-badge--success">Attivo ✓</span>
                    <?php elseif (!$wpcfg_writable) : ?>
                        <span class="alm-badge alm-badge--warning">Non scrivibile</span>
                    <?php else : ?>
                        <span class="alm-badge alm-badge--error">Non configurato</span>
                    <?php endif; ?>
                    <span id="alm-wpcfg-chevron" style="font-size:18px;color:#888;transition:transform .2s;">▾</span>
                </div>
            </div>

            <div id="alm-wpcfg-body" style="display:none;border-top:1px solid #e5e5e5;padding:20px;">

                <?php if ($wpcfg_locked) : ?>
                <!-- ── Sola lettura: costanti già presenti ── -->
                <div style="background:#f0f9f0;border:1px solid #a8d5a2;border-radius:4px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#2e7d32;">
                    <strong>Le chiavi sono scritte in wp-config.php — sola lettura.</strong><br>
                    Per modificarle, rimuovi le righe <code>define('ALM_STRIPE_*', …)</code> dal file e aggiorna la pagina.
                </div>
                <table class="form-table" role="presentation">
                    <tr>
                        <th style="width:160px;">Publishable Key</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(ALM_Stripe::get_publishable_key())); ?></code></td>
                    </tr>
                    <tr>
                        <th>Secret Key</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(defined('ALM_STRIPE_SECRET_KEY') ? ALM_STRIPE_SECRET_KEY : '')); ?></code></td>
                    </tr>
                    <tr>
                        <th>Webhook Secret</th>
                        <td><code style="color:#555;"><?php echo esc_html($mask(defined('ALM_STRIPE_WEBHOOK_SECRET') ? ALM_STRIPE_WEBHOOK_SECRET : '')); ?></code></td>
                    </tr>
                </table>

                <?php elseif (!$wpcfg_writable) : ?>
                <!-- ── File non scrivibile: mostra blocco da copiare ── -->
                <div style="background:#fffbeb;border:1px solid #f0d080;border-radius:4px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#7a6000;">
                    <strong>wp-config.php non è scrivibile</strong> dal web server (permessi). Aggiungi manualmente le righe seguenti prima di <code>/* That's all, stop editing! */</code>:
                </div>
                <pre style="background:#f6f6f6;border:1px solid #ddd;border-radius:4px;padding:14px 16px;font-size:12px;overflow-x:auto;line-height:1.6;">// Stripe — Almaretna Booking
define( 'ALM_STRIPE_PUBLISHABLE_KEY', 'pk_live_...' );
define( 'ALM_STRIPE_SECRET_KEY',      'sk_live_...' );
define( 'ALM_STRIPE_WEBHOOK_SECRET',  'whsec_...' );</pre>

                <?php else : ?>
                <!-- ── Form: scrivi le chiavi in wp-config.php ── -->
                <p style="font-size:13px;color:#666;margin:0 0 16px;">
                    Inserisci le chiavi Stripe: verranno scritte direttamente in <code>wp-config.php</code>.<br>
                    Una volta salvate, questa card diventa <strong>sola lettura</strong> e le chiavi non saranno mai nel database.
                </p>
                <table class="form-table" role="presentation">
                    <tr>
                        <th style="width:160px;"><label for="wpcfg_pk">Publishable Key</label></th>
                        <td>
                            <input type="text" id="wpcfg_pk" class="regular-text"
                                   placeholder="pk_live_… oppure pk_test_…" autocomplete="off" />
                            <p class="description">Inizia con <code>pk_live_</code> (prod) o <code>pk_test_</code> (test).</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wpcfg_sk">Secret Key</label></th>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="password" id="wpcfg_sk" class="regular-text"
                                       placeholder="sk_live_…" autocomplete="new-password" />
                                <button type="button" class="button button-small"
                                        onclick="var f=document.getElementById('wpcfg_sk');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Mostra':'Nascondi';">Mostra</button>
                            </div>
                            <p class="description">Inizia con <code>sk_live_</code> o <code>sk_test_</code>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="wpcfg_whsec">Webhook Secret</label></th>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input type="password" id="wpcfg_whsec" class="regular-text"
                                       placeholder="whsec_…" autocomplete="new-password" />
                                <button type="button" class="button button-small"
                                        onclick="var f=document.getElementById('wpcfg_whsec');f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'Mostra':'Nascondi';">Mostra</button>
                            </div>
                            <p class="description">Ottenuto in Dashboard Stripe → Sviluppatori → Webhook → Signing secret.</p>
                        </td>
                    </tr>
                </table>

                <div id="alm-wpcfg-msg" style="display:none;margin-top:12px;padding:10px 14px;border-radius:4px;font-size:13px;"></div>

                <div style="margin-top:20px;display:flex;align-items:center;gap:16px;">
                    <button type="button" id="alm-wpcfg-save" class="button button-primary"
                            onclick="almWpcfgSave('<?php echo esc_js($wpcfg_nonce); ?>')">
                        Salva in wp-config.php
                    </button>
                    <span style="font-size:12px;color:#999;">Questa operazione modifica il file wp-config.php del sito.</span>
                </div>
                <?php endif; ?>

            </div><!-- /body -->
        </div><!-- /card -->

        <script>
        function almWpcfgToggle() {
            var body    = document.getElementById('alm-wpcfg-body');
            var chevron = document.getElementById('alm-wpcfg-chevron');
            if (body.style.display === 'none') {
                body.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
            } else {
                body.style.display = 'none';
                chevron.style.transform = '';
            }
        }
        function almWpcfgSave(nonce) {
            var pk    = (document.getElementById('wpcfg_pk')    || {}).value || '';
            var sk    = (document.getElementById('wpcfg_sk')    || {}).value || '';
            var whsec = (document.getElementById('wpcfg_whsec') || {}).value || '';
            var msgEl = document.getElementById('alm-wpcfg-msg');
            var btn   = document.getElementById('alm-wpcfg-save');

            function showMsg(text, ok) {
                msgEl.style.display = 'block';
                msgEl.style.background = ok ? '#f0f9f0' : '#fdf0f0';
                msgEl.style.border     = '1px solid ' + (ok ? '#a8d5a2' : '#f5a5a5');
                msgEl.style.color      = ok ? '#2e7d32' : '#c62828';
                msgEl.textContent      = text;
            }

            pk = pk.trim(); sk = sk.trim(); whsec = whsec.trim();
            if (!pk || !sk || !whsec) { showMsg('Compila tutti e tre i campi prima di salvare.', false); return; }

            btn.disabled    = true;
            btn.textContent = 'Salvataggio…';

            var fd = new FormData();
            fd.append('action', 'alm_write_stripe_config');
            fd.append('nonce',  nonce);
            fd.append('pk',    pk);
            fd.append('sk',    sk);
            fd.append('whsec', whsec);

            fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.success) {
                        showMsg('✓ ' + (data.data.message || 'Chiavi salvate!'), true);
                        setTimeout(function () { location.reload(); }, 1500);
                    } else {
                        showMsg((data.data || 'Si è verificato un errore.'), false);
                        btn.disabled    = false;
                        btn.textContent = 'Salva in wp-config.php';
                    }
                })
                .catch(function () {
                    showMsg('Errore di rete. Riprova.', false);
                    btn.disabled    = false;
                    btn.textContent = 'Salva in wp-config.php';
                });
        }
        <?php if (!$wpcfg_locked) : ?>
        almWpcfgToggle(); // aperta di default se non ancora configurata
        <?php endif; ?>
        </script>

        <!-- Beds24 -->
        <div class="alm-admin-card" style="max-width:720px;margin-bottom:24px;">
            <div class="alm-admin-card__header">
                <h2>Beds24</h2>
                <?php if (ALM_Beds24::is_configured()) : ?>
                    <span class="alm-badge alm-badge--success"><?php esc_html_e('Configurato', 'almaretna-booking'); ?></span>
                <?php else : ?>
                    <span class="alm-badge alm-badge--error"><?php esc_html_e('Non configurato', 'almaretna-booking'); ?></span>
                <?php endif; ?>
            </div>
            <p class="description" style="margin:0 0 16px;">
                <?php esc_html_e('Configura le credenziali Beds24 in wp-config.php:', 'almaretna-booking'); ?>
            </p>
            <pre style="background:#f0f0f0;padding:12px;border-radius:4px;font-size:12px;white-space:pre-wrap;">define('ALM_BEDS24_API_TOKEN',     'token-beds24');
define('ALM_BEDS24_PROP_KEY',      'prop-key');
define('ALM_BEDS24_WEBHOOK_TOKEN', 'webhook-secret');</pre>

            <table class="form-table" role="presentation" style="margin-top:16px;">
                <tr>
                    <th>
                        <label for="beds24_enabled"><?php esc_html_e('Sincronizzazione attiva', 'almaretna-booking'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" id="beds24_enabled" name="beds24_enabled" value="1"
                               <?php checked(!empty($s['beds24_enabled'])); ?> />
                        <label for="beds24_enabled"><?php esc_html_e('Abilita sync automatico (twicedaily)', 'almaretna-booking'); ?></label>
                    </td>
                </tr>
                <?php if (ALM_Beds24::is_configured()) : ?>
                <tr>
                    <th><?php esc_html_e('Test connessione', 'almaretna-booking'); ?></th>
                    <td>
                        <button type="button" id="alm-test-beds24" class="button button-secondary">
                            <?php esc_html_e('Testa connessione Beds24', 'almaretna-booking'); ?>
                        </button>
                        <span id="alm-beds24-test-result" style="margin-left:12px;font-size:13px;"></span>
                        <script>
                        document.getElementById('alm-test-beds24').addEventListener('click', function() {
                            var btn = this;
                            var res = document.getElementById('alm-beds24-test-result');
                            btn.disabled = true;
                            res.textContent = '<?php echo esc_js(__('Test in corso…', 'almaretna-booking')); ?>';
                            fetch(ajaxurl + '?action=alm_test_beds24&nonce=<?php echo wp_create_nonce('alm_test_beds24'); ?>')
                                .then(function(r){ return r.json(); })
                                .then(function(data){
                                    btn.disabled = false;
                                    res.style.color = data.success ? '#2e7d32' : '#c62828';
                                    res.textContent = data.data || (data.success ? '✓ Connessione OK' : '✗ Errore');
                                })
                                .catch(function(){
                                    btn.disabled = false;
                                    res.style.color = '#c62828';
                                    res.textContent = '<?php echo esc_js(__('Errore di rete.', 'almaretna-booking')); ?>';
                                });
                        });
                        </script>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <?php submit_button(__('Salva impostazioni', 'almaretna-booking')); ?>

    </form>

    <!-- ── Checklist pre-lancio ────────────────────────────────────────── -->
    <div class="alm-admin-card" style="max-width:720px;margin-top:32px;margin-bottom:24px;">
        <div class="alm-admin-card__header">
            <h2><?php esc_html_e('Checklist pre-lancio', 'almaretna-booking'); ?></h2>
        </div>
        <ul class="alm-status-list" style="padding:12px 20px !important;">
            <?php
            $checks = alm_run_launch_checklist();
            foreach ($checks as $check) :
                $icon  = $check['ok'] ? 'dashicons-yes-alt' : 'dashicons-warning';
                $color = $check['ok'] ? '#2e7d32' : '#c62828';
            ?>
            <li style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f0f0f0;font-size:13px;">
                <span class="dashicons <?php echo esc_attr($icon); ?>" style="color:<?php echo esc_attr($color); ?>;flex-shrink:0;"></span>
                <span><?php echo esc_html($check['label']); ?></span>
                <?php if (!empty($check['action'])) : ?>
                    <a href="<?php echo esc_url($check['action']); ?>" style="margin-left:auto;font-size:12px;">
                        <?php esc_html_e('Configura', 'almaretna-booking'); ?>
                    </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- ── URL Webhook ─────────────────────────────────────────────────── -->
    <div class="alm-admin-card" style="max-width:720px;margin-bottom:24px;">
        <div class="alm-admin-card__header">
            <h2><?php esc_html_e('URL Webhook', 'almaretna-booking'); ?></h2>
        </div>
        <p style="padding:12px 20px 0;font-size:13px;color:#666;">
            <?php esc_html_e('Usa questi URL per configurare i webhook nei rispettivi pannelli:', 'almaretna-booking'); ?>
        </p>
        <table class="alm-detail-table" style="margin:0 20px 16px;width:calc(100% - 40px);">
            <tr>
                <th style="width:100px;">Stripe</th>
                <td>
                    <code style="font-size:12px;word-break:break-all;">
                        <?php echo esc_html(get_rest_url(null, 'scv/v1/stripe/webhook')); ?>
                    </code>
                </td>
            </tr>
            <tr>
                <th>Beds24</th>
                <td>
                    <code style="font-size:12px;word-break:break-all;">
                        <?php echo esc_html(get_rest_url(null, 'scv/v1/beds24/webhook')); ?>
                    </code>
                </td>
            </tr>
        </table>
        <p style="padding:0 20px 12px;font-size:12px;color:#888;">
            <?php esc_html_e('Stripe Dashboard: Developers → Webhooks → Add endpoint. Seleziona gli eventi: payment_intent.succeeded, payment_intent.payment_failed, charge.refunded.', 'almaretna-booking'); ?>
        </p>
    </div>

</div>
<?php

function alm_run_launch_checklist(): array {
    $checks = [];

    // Stripe
    $checks[] = [
        'label'  => 'Stripe: chiavi API configurate',
        'ok'     => ALM_Stripe::is_configured(),
        'action' => '',
    ];
    if (ALM_Stripe::is_configured()) {
        $checks[] = [
            'label' => 'Stripe: modalità ' . (ALM_Stripe::is_test_mode() ? 'TEST (cambia in LIVE prima del lancio)' : 'LIVE ✓'),
            'ok'    => !ALM_Stripe::is_test_mode(),
            'action'=> '',
        ];
    }

    // Beds24
    $checks[] = [
        'label'  => 'Beds24: API token configurato',
        'ok'     => ALM_Beds24::is_configured(),
        'action' => '',
    ];

    // Email host
    $host_email = get_option('alm_booking_settings')['host_email'] ?? '';
    $checks[] = [
        'label'  => 'Email struttura configurata',
        'ok'     => !empty($host_email),
        'action' => admin_url('admin.php?page=alm-settings'),
    ];

    // Camere
    $rooms_count = wp_count_posts('almaretna_room')->publish ?? 0;
    $checks[]    = [
        'label'  => 'Camere inserite (' . $rooms_count . ')',
        'ok'     => $rooms_count > 0,
        'action' => admin_url('post-new.php?post_type=almaretna_room'),
    ];

    // Pagina prenota
    $prenota_page = get_page_by_path('prenota');
    $checks[] = [
        'label'  => 'Pagina "Prenota" creata',
        'ok'     => $prenota_page !== null,
        'action' => admin_url('post-new.php?post_type=page'),
    ];

    // Permalink non plain
    $checks[] = [
        'label'  => 'Permalink non-plain (consigliato: /%postname%/)',
        'ok'     => get_option('permalink_structure') !== '',
        'action' => admin_url('options-permalink.php'),
    ];

    // HTTPS
    $checks[] = [
        'label' => 'Sito su HTTPS',
        'ok'    => is_ssl() || str_starts_with(home_url(), 'https://'),
        'action'=> '',
    ];

    // wp-cron attivo
    $checks[] = [
        'label' => 'WP-Cron attivo (necessario per reminder email)',
        'ok'    => !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON,
        'action'=> '',
    ];

    return $checks;
}
