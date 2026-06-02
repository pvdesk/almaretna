<?php
/**
 * Almaretna Booking — Wizard prenotazione 4 step
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$privacy_url      = get_permalink(get_page_by_path('privacy-policy')) ?: home_url('/privacy-policy/');
$cancellation_url = get_permalink(get_page_by_path('cancellazione'))   ?: home_url('/cancellazione/');
?>

<div class="alm-booking-wizard" id="alm-booking-wizard" data-nonce="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>">

    <!-- ── STEP 1: Date e ospiti ─────────────────────────────────── -->
    <div class="booking-step-content is-active" data-step="1" id="alm-step-1">
        <h2 class="form-section-title"><?php esc_html_e('Quando vorresti soggiornare?', 'almaretna-booking'); ?></h2>

        <div class="date-fields">
            <div class="form-group">
                <label for="alm-checkin">
                    <?php esc_html_e('Check-in', 'almaretna-booking'); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="alm-checkin"
                    class="form-control"
                    placeholder="<?php esc_attr_e('gg/mm/aaaa', 'almaretna-booking'); ?>"
                    readonly
                    aria-required="true"
                />
                <span class="form-error" id="alm-checkin-error" role="alert"></span>
            </div>
            <div class="form-group">
                <label for="alm-checkout">
                    <?php esc_html_e('Check-out', 'almaretna-booking'); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="alm-checkout"
                    class="form-control"
                    placeholder="<?php esc_attr_e('gg/mm/aaaa', 'almaretna-booking'); ?>"
                    readonly
                    aria-required="true"
                />
                <span class="form-error" id="alm-checkout-error" role="alert"></span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="alm-adults"><?php esc_html_e('Adulti', 'almaretna-booking'); ?> <span class="required">*</span></label>
                <select id="alm-adults" class="form-control" aria-required="true">
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <option value="<?php echo esc_attr((string) $i); ?>"><?php echo esc_html((string) $i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="alm-children"><?php esc_html_e('Bambini (0-12 anni)', 'almaretna-booking'); ?></label>
                <select id="alm-children" class="form-control">
                    <?php for ($i = 0; $i <= 6; $i++) : ?>
                        <option value="<?php echo esc_attr((string) $i); ?>"><?php echo esc_html((string) $i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>

        <div id="alm-step1-error" class="alert alert-error" style="display:none;" role="alert"></div>

        <div class="booking-form-nav" style="justify-content:flex-end;">
            <button type="button" class="btn btn-primary btn-lg" id="alm-search-rooms">
                <?php esc_html_e('Cerca camere disponibili', 'almaretna-booking'); ?>
            </button>
        </div>
    </div>

    <!-- ── STEP 2: Scelta camera ─────────────────────────────────── -->
    <div class="booking-step-content" data-step="2" id="alm-step-2">
        <h2 class="form-section-title"><?php esc_html_e('Scegli la camera', 'almaretna-booking'); ?></h2>

        <div id="alm-rooms-loading" class="room-results__loading" style="display:none;">
            <span class="spinner"></span>
            <?php esc_html_e('Ricerca in corso...', 'almaretna-booking'); ?>
        </div>

        <div id="alm-rooms-list" class="room-results" role="list" aria-live="polite"></div>

        <div id="alm-step2-error" class="alert alert-error" style="display:none;" role="alert"></div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="1">
                &larr; <?php esc_html_e('Modifica date', 'almaretna-booking'); ?>
            </button>
        </div>
    </div>

    <!-- ── STEP 3: Dati ospite ───────────────────────────────────── -->
    <div class="booking-step-content" data-step="3" id="alm-step-3">
        <div class="booking-form-grid">

            <!-- Colonna principale: form -->
            <div>
                <h2 class="form-section-title"><?php esc_html_e('I tuoi dati', 'almaretna-booking'); ?></h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="alm-first-name">
                            <?php esc_html_e('Nome', 'almaretna-booking'); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="alm-first-name" class="form-control" autocomplete="given-name" aria-required="true" />
                        <span class="form-error" id="alm-first-name-error" role="alert"></span>
                    </div>
                    <div class="form-group">
                        <label for="alm-last-name">
                            <?php esc_html_e('Cognome', 'almaretna-booking'); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="alm-last-name" class="form-control" autocomplete="family-name" aria-required="true" />
                        <span class="form-error" id="alm-last-name-error" role="alert"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="alm-email">
                            <?php esc_html_e('Email', 'almaretna-booking'); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="email" id="alm-email" class="form-control" autocomplete="email" aria-required="true" />
                        <span class="form-error" id="alm-email-error" role="alert"></span>
                    </div>
                    <div class="form-group">
                        <label for="alm-phone">
                            <?php esc_html_e('Telefono', 'almaretna-booking'); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="tel" id="alm-phone" class="form-control" autocomplete="tel" aria-required="true" />
                        <span class="form-error" id="alm-phone-error" role="alert"></span>
                    </div>
                </div>

                <!-- Extras opzionali -->
                <div id="alm-extras-section" style="margin-bottom:1.5rem; display:none;">
                    <h3 class="form-section-title" style="font-size:var(--fs-lg);">
                        <?php esc_html_e('Servizi opzionali', 'almaretna-booking'); ?>
                    </h3>
                    <div id="alm-extras-list" class="extras-list"></div>
                </div>

                <!-- Richieste speciali -->
                <div class="form-group">
                    <label for="alm-special-requests">
                        <?php esc_html_e('Richieste speciali', 'almaretna-booking'); ?>
                        <span style="font-weight:400;color:var(--color-text-light);font-size:var(--fs-xs);">
                            (<?php esc_html_e('opzionale', 'almaretna-booking'); ?>)
                        </span>
                    </label>
                    <textarea
                        id="alm-special-requests"
                        class="form-control"
                        rows="3"
                        placeholder="<?php esc_attr_e('Allergie, orario di arrivo previsto, esigenze particolari...', 'almaretna-booking'); ?>"
                    ></textarea>
                </div>

                <!-- Privacy e T&C -->
                <div class="form-check">
                    <input type="checkbox" id="alm-privacy" value="1" />
                    <label for="alm-privacy">
                        <?php
                        printf(
                            wp_kses(
                                __('Ho letto e accetto la <a href="%s" target="_blank" rel="noopener">Privacy Policy</a> *', 'almaretna-booking'),
                                ['a' => ['href' => [], 'target' => [], 'rel' => []]]
                            ),
                            esc_url($privacy_url)
                        );
                        ?>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="alm-tc" value="1" />
                    <label for="alm-tc">
                        <?php
                        printf(
                            wp_kses(
                                __('Ho letto e accetto la <a href="%s" target="_blank" rel="noopener">Politica di cancellazione</a> *', 'almaretna-booking'),
                                ['a' => ['href' => [], 'target' => [], 'rel' => []]]
                            ),
                            esc_url($cancellation_url)
                        );
                        ?>
                    </label>
                </div>

                <div id="alm-step3-error" class="alert alert-error" style="display:none;margin-top:1rem;" role="alert"></div>
            </div>

            <!-- Colonna destra: riepilogo -->
            <aside>
                <div class="booking-summary" id="alm-summary-step3">
                    <h3 class="booking-summary__title"><?php esc_html_e('Riepilogo', 'almaretna-booking'); ?></h3>
                    <img src="" alt="" class="booking-summary__room-img" id="alm-summary-img" style="display:none;" />
                    <p class="booking-summary__room-name" id="alm-summary-room-name"></p>
                    <div class="booking-summary__dates">
                        <div>
                            <div class="booking-summary__date-label"><?php esc_html_e('Check-in', 'almaretna-booking'); ?></div>
                            <div class="booking-summary__date-value" id="alm-summary-checkin">—</div>
                        </div>
                        <div>
                            <div class="booking-summary__date-label"><?php esc_html_e('Check-out', 'almaretna-booking'); ?></div>
                            <div class="booking-summary__date-value" id="alm-summary-checkout">—</div>
                        </div>
                    </div>
                    <div class="booking-summary__row">
                        <span id="alm-summary-nights-label"><?php esc_html_e('Notti', 'almaretna-booking'); ?></span>
                        <span id="alm-summary-nights">—</span>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php esc_html_e('Ospiti', 'almaretna-booking'); ?></span>
                        <span id="alm-summary-guests">—</span>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php esc_html_e('Prezzo camera', 'almaretna-booking'); ?></span>
                        <span id="alm-summary-room-price">—</span>
                    </div>
                    <div class="booking-summary__row" id="alm-summary-extras-row" style="display:none;">
                        <span><?php esc_html_e('Extras', 'almaretna-booking'); ?></span>
                        <span id="alm-summary-extras">€ 0,00</span>
                    </div>
                    <div class="booking-summary__row booking-summary__row--total">
                        <span><?php esc_html_e('Totale', 'almaretna-booking'); ?></span>
                        <span id="alm-summary-total">—</span>
                    </div>
                </div>
            </aside>

        </div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="2">
                &larr; <?php esc_html_e('Torna alla scelta camera', 'almaretna-booking'); ?>
            </button>
            <button type="button" class="btn btn-primary btn-lg" id="alm-to-payment">
                <?php esc_html_e('Procedi al pagamento', 'almaretna-booking'); ?>
                &rarr;
            </button>
        </div>
    </div>

    <!-- ── STEP 4: Pagamento ─────────────────────────────────────── -->
    <div class="booking-step-content" data-step="4" id="alm-step-4">
        <div class="booking-form-grid">

            <!-- Pagamento -->
            <div>
                <h2 class="form-section-title"><?php esc_html_e('Pagamento', 'almaretna-booking'); ?></h2>

                <!-- Riepilogo readonly -->
                <div style="background:var(--color-bg);border-radius:var(--radius-md);padding:var(--space-lg);margin-bottom:var(--space-xl);border:1px solid var(--color-border);">
                    <div class="booking-summary__row">
                        <span><?php esc_html_e('Camera', 'almaretna-booking'); ?></span>
                        <strong id="alm-pay-room-name">—</strong>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php esc_html_e('Date', 'almaretna-booking'); ?></span>
                        <span id="alm-pay-dates">—</span>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php esc_html_e('Ospiti', 'almaretna-booking'); ?></span>
                        <span id="alm-pay-guests">—</span>
                    </div>
                    <div class="booking-summary__row booking-summary__row--total">
                        <span><?php esc_html_e('Totale da pagare', 'almaretna-booking'); ?></span>
                        <span id="alm-pay-total">—</span>
                    </div>
                </div>

                <!-- Stripe Payment Element -->
                <div class="stripe-payment-element" id="stripe-payment-element">
                    <div style="text-align:center;color:var(--color-text-light);padding:2rem;">
                        <span class="spinner"></span>
                        <p style="margin-top:1rem;"><?php esc_html_e('Caricamento modulo pagamento...', 'almaretna-booking'); ?></p>
                    </div>
                </div>

                <div id="payment-error" role="alert" aria-live="assertive"></div>

                <div class="checkout-box__secure-note">
                    <span class="dashicons dashicons-lock"></span>
                    <?php esc_html_e('Pagamento sicuro gestito da Stripe. I tuoi dati sono crittografati.', 'almaretna-booking'); ?>
                </div>
            </div>

            <!-- Riepilogo sidebar step 4 -->
            <aside>
                <div class="booking-summary">
                    <h3 class="booking-summary__title"><?php esc_html_e('La tua prenotazione', 'almaretna-booking'); ?></h3>
                    <p class="booking-summary__room-name" id="alm-pay-summary-room">—</p>
                    <div class="booking-summary__dates">
                        <div>
                            <div class="booking-summary__date-label">Check-in</div>
                            <div class="booking-summary__date-value" id="alm-pay-summary-checkin">—</div>
                        </div>
                        <div>
                            <div class="booking-summary__date-label">Check-out</div>
                            <div class="booking-summary__date-value" id="alm-pay-summary-checkout">—</div>
                        </div>
                    </div>
                    <div class="booking-summary__row booking-summary__row--total">
                        <span><?php esc_html_e('Totale', 'almaretna-booking'); ?></span>
                        <span id="alm-pay-summary-total">—</span>
                    </div>
                    <button
                        type="button"
                        class="btn btn-primary btn-lg"
                        id="alm-submit-payment"
                        style="width:100%;justify-content:center;margin-top:1.5rem;"
                        disabled
                    >
                        <?php esc_html_e('Paga ora', 'almaretna-booking'); ?>
                    </button>
                </div>
            </aside>

        </div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="3">
                &larr; <?php esc_html_e('Modifica dati', 'almaretna-booking'); ?>
            </button>
        </div>
    </div>

    <!-- ── CONFERMA (step nascosto, mostrato dopo redirect Stripe) ── -->
    <div class="booking-step-content" data-step="5" id="alm-step-confirm" style="display:none;">
        <div class="confirmation-box">
            <div class="confirmation-box__icon">
                <span class="dashicons dashicons-yes" aria-hidden="true"></span>
            </div>
            <h2 class="confirmation-box__title">
                <?php esc_html_e('Prenotazione confermata!', 'almaretna-booking'); ?>
            </h2>
            <div class="confirmation-box__ref" id="alm-confirm-ref">#ALM-XXXX</div>
            <div class="confirmation-box__details" id="alm-confirm-details"></div>
            <p style="color:var(--color-text-light);font-size:var(--fs-sm);margin-bottom:var(--space-xl);">
                <?php esc_html_e('Riceverai una email di conferma a breve all\'indirizzo indicato.', 'almaretna-booking'); ?>
            </p>
            <div style="background:var(--color-bg);border-radius:var(--radius-md);padding:var(--space-lg);font-size:var(--fs-sm);text-align:left;">
                <strong><?php esc_html_e('Indicazioni check-in:', 'almaretna-booking'); ?></strong><br>
                <?php esc_html_e('Check-in dalle ore 15:00 — Check-out entro le ore 11:00', 'almaretna-booking'); ?><br>
                <br>
                <strong><?php esc_html_e('Indirizzo:', 'almaretna-booking'); ?></strong><br>
                Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT)
            </div>
            <div style="margin-top:var(--space-xl);">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline">
                    <?php esc_html_e('Torna alla home', 'almaretna-booking'); ?>
                </a>
            </div>
        </div>
    </div>

</div><!-- /.alm-booking-wizard -->
