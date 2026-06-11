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
        <h2 class="form-section-title"><?php echo esc_html(alm_t('wiz.when_stay')); ?></h2>

        <div class="date-fields">
            <div class="form-group">
                <label for="alm-checkin">
                    <?php echo esc_html(alm_t('wiz.checkin')); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="alm-checkin"
                    class="form-control"
                    placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
                    readonly
                    aria-required="true"
                />
                <span class="form-error" id="alm-checkin-error" role="alert"></span>
            </div>
            <div class="form-group">
                <label for="alm-checkout">
                    <?php echo esc_html(alm_t('wiz.checkout')); ?>
                    <span class="required" aria-hidden="true">*</span>
                </label>
                <input
                    type="text"
                    id="alm-checkout"
                    class="form-control"
                    placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
                    readonly
                    aria-required="true"
                />
                <span class="form-error" id="alm-checkout-error" role="alert"></span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="alm-adults"><?php echo esc_html(alm_t('wiz.adults')); ?> <span class="required">*</span></label>
                <select id="alm-adults" class="form-control" aria-required="true">
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <option value="<?php echo esc_attr((string) $i); ?>"><?php echo esc_html((string) $i); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="alm-children"><?php echo esc_html(alm_t('wiz.children')); ?></label>
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
                <?php echo esc_html(alm_t('wiz.search')); ?>
            </button>
        </div>
    </div>

    <!-- ── STEP 2: Scelta camera ─────────────────────────────────── -->
    <div class="booking-step-content" data-step="2" id="alm-step-2">
        <h2 class="form-section-title"><?php echo esc_html(alm_t('wiz.choose_room')); ?></h2>

        <div id="alm-rooms-loading" class="room-results__loading" style="display:none;">
            <span class="spinner"></span>
            <?php echo esc_html(alm_t('wiz.searching')); ?>
        </div>

        <div id="alm-rooms-list" class="room-results" role="list" aria-live="polite"></div>

        <div id="alm-step2-error" class="alert alert-error" style="display:none;" role="alert"></div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="1">
                &larr; <?php echo esc_html(alm_t('wiz.back_dates')); ?>
            </button>
        </div>
    </div>

    <!-- ── STEP 3: Dati ospite ───────────────────────────────────── -->
    <div class="booking-step-content" data-step="3" id="alm-step-3">
        <div class="booking-form-grid">

            <!-- Colonna principale: form -->
            <div>
                <h2 class="form-section-title"><?php echo esc_html(alm_t('wiz.your_data')); ?></h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="alm-first-name">
                            <?php echo esc_html(alm_t('wiz.first_name')); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="alm-first-name" class="form-control" autocomplete="given-name" aria-required="true" />
                        <span class="form-error" id="alm-first-name-error" role="alert"></span>
                    </div>
                    <div class="form-group">
                        <label for="alm-last-name">
                            <?php echo esc_html(alm_t('wiz.last_name')); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="text" id="alm-last-name" class="form-control" autocomplete="family-name" aria-required="true" />
                        <span class="form-error" id="alm-last-name-error" role="alert"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="alm-email">
                            <?php echo esc_html(alm_t('wiz.email')); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="email" id="alm-email" class="form-control" autocomplete="email" aria-required="true" />
                        <span class="form-error" id="alm-email-error" role="alert"></span>
                    </div>
                    <div class="form-group">
                        <label for="alm-phone">
                            <?php echo esc_html(alm_t('wiz.phone')); ?>
                            <span class="required">*</span>
                        </label>
                        <input type="tel" id="alm-phone" class="form-control" autocomplete="tel" aria-required="true" />
                        <span class="form-error" id="alm-phone-error" role="alert"></span>
                    </div>
                </div>

                <!-- Extras opzionali -->
                <div id="alm-extras-section" style="margin-bottom:1.5rem; display:none;">
                    <h3 class="form-section-title" style="font-size:var(--fs-lg);">
                        <?php echo esc_html(alm_t('wiz.extras')); ?>
                    </h3>
                    <div id="alm-extras-list" class="extras-list"></div>
                </div>

                <!-- Richieste speciali -->
                <div class="form-group">
                    <label for="alm-special-requests">
                        <?php echo esc_html(alm_t('wiz.special_req')); ?>
                        <span style="font-weight:400;color:var(--color-text-light);font-size:var(--fs-xs);">
                            (<?php echo esc_html(alm_t('wiz.optional')); ?>)
                        </span>
                    </label>
                    <textarea
                        id="alm-special-requests"
                        class="form-control"
                        rows="3"
                        placeholder="<?php echo esc_attr(alm_t('wiz.req_ph')); ?>"
                    ></textarea>
                </div>

                <!-- Privacy e T&C -->
                <div class="form-check">
                    <input type="checkbox" id="alm-privacy" value="1" />
                    <label for="alm-privacy">
                        <?php echo esc_html(alm_t('wiz.i_accept')); ?>
                        <a href="<?php echo esc_url($privacy_url); ?>" target="_blank" rel="noopener"><?php echo esc_html(alm_t('wiz.privacy_policy')); ?></a> *
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="alm-tc" value="1" />
                    <label for="alm-tc">
                        <?php echo esc_html(alm_t('wiz.i_accept')); ?>
                        <a href="<?php echo esc_url($cancellation_url); ?>" target="_blank" rel="noopener"><?php echo esc_html(alm_t('wiz.cancel_policy')); ?></a> *
                    </label>
                </div>

                <div id="alm-step3-error" class="alert alert-error" style="display:none;margin-top:1rem;" role="alert"></div>
            </div>

            <!-- Colonna destra: riepilogo -->
            <aside>
                <div class="booking-summary" id="alm-summary-step3">
                    <h3 class="booking-summary__title"><?php echo esc_html(alm_t('wiz.summary')); ?></h3>
                    <img src="" alt="" class="booking-summary__room-img" id="alm-summary-img" style="display:none;" />
                    <p class="booking-summary__room-name" id="alm-summary-room-name"></p>
                    <div class="booking-summary__dates">
                        <div>
                            <div class="booking-summary__date-label"><?php echo esc_html(alm_t('wiz.checkin')); ?></div>
                            <div class="booking-summary__date-value" id="alm-summary-checkin">—</div>
                        </div>
                        <div>
                            <div class="booking-summary__date-label"><?php echo esc_html(alm_t('wiz.checkout')); ?></div>
                            <div class="booking-summary__date-value" id="alm-summary-checkout">—</div>
                        </div>
                    </div>
                    <div class="booking-summary__row">
                        <span id="alm-summary-nights-label"><?php echo esc_html(alm_t('wiz.nights')); ?></span>
                        <span id="alm-summary-nights">—</span>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php echo esc_html(alm_t('wiz.guests')); ?></span>
                        <span id="alm-summary-guests">—</span>
                    </div>
                    <div class="booking-summary__row">
                        <span><?php echo esc_html(alm_t('wiz.room_price')); ?></span>
                        <span id="alm-summary-room-price">—</span>
                    </div>
                    <div class="booking-summary__row" id="alm-summary-extras-row" style="display:none;">
                        <span><?php echo esc_html(alm_t('wiz.extras_label')); ?></span>
                        <span id="alm-summary-extras">€ 0,00</span>
                    </div>
                    <div class="booking-summary__row booking-summary__row--total">
                        <span><?php echo esc_html(alm_t('wiz.total')); ?></span>
                        <span id="alm-summary-total">—</span>
                    </div>
                </div>
            </aside>

        </div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="2">
                &larr; <?php echo esc_html(alm_t('wiz.back_rooms')); ?>
            </button>
            <button type="button" class="btn btn-primary btn-lg" id="alm-to-payment">
                <?php echo esc_html(alm_t('wiz.proceed_pay')); ?>
                &rarr;
            </button>
        </div>
    </div>

    <!-- ── STEP 4: Pagamento ─────────────────────────────────────── -->
    <div class="booking-step-content" data-step="4" id="alm-step-4">

        <!-- Alert errore pagamento — sticky, visibile come in un e-commerce -->
        <div class="alm-pay-alert" id="payment-error" role="alert" aria-live="assertive" style="display:none;">
            <span class="alm-pay-alert__icon" aria-hidden="true">&#9888;</span>
            <span class="alm-pay-alert__msg"></span>
            <button type="button" class="alm-pay-alert__close" aria-label="Chiudi errore">&times;</button>
        </div>

        <div class="alm-checkout">

            <!-- Riepilogo prenotazione compatto -->
            <div class="alm-checkout__summary">
                <div class="alm-checkout__summary-room" id="alm-pay-summary-room">—</div>
                <div class="alm-checkout__summary-meta">
                    <span id="alm-pay-summary-checkin">—</span>
                    <span aria-hidden="true"> &rarr; </span>
                    <span id="alm-pay-summary-checkout">—</span>
                    <span class="alm-checkout__summary-sep" aria-hidden="true"> &middot; </span>
                    <span id="alm-pay-guests">—</span>
                </div>
                <div class="alm-checkout__summary-total">
                    <span><?php echo esc_html(alm_t('wiz.total_pay')); ?></span>
                    <strong id="alm-pay-summary-total">—</strong>
                </div>
            </div>

            <!-- Form pagamento -->
            <div class="alm-checkout__form">
                <h2 class="form-section-title"><?php echo esc_html(alm_t('wiz.payment')); ?></h2>

                <div class="stripe-payment-element" id="stripe-payment-element">
                    <div style="text-align:center;color:var(--color-text-light);padding:2rem;">
                        <span class="spinner"></span>
                        <p style="margin-top:1rem;"><?php echo esc_html(alm_t('wiz.pay_loading')); ?></p>
                    </div>
                </div>

                <button
                    type="button"
                    class="btn btn-primary alm-pay-btn"
                    id="alm-submit-payment"
                    disabled
                >
                    <span class="alm-pay-btn__label"><?php echo esc_html(alm_t('wiz.pay_now')); ?></span>
                    <span class="alm-pay-btn__spinner" aria-hidden="true"></span>
                </button>

                <div class="checkout-box__secure-note">
                    <span class="dashicons dashicons-lock"></span>
                    <?php echo esc_html(alm_t('wiz.pay_secure')); ?>
                </div>
            </div>

        </div>

        <div class="booking-form-nav">
            <button type="button" class="btn btn-outline" data-goto-step="3">
                &larr; <?php echo esc_html(alm_t('wiz.back_data')); ?>
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
                <?php echo esc_html(alm_t('wiz.confirmed')); ?>
            </h2>
            <div class="confirmation-box__ref" id="alm-confirm-ref">#ALM-XXXX</div>
            <div class="confirmation-box__details" id="alm-confirm-details"></div>
            <p style="color:var(--color-text-light);font-size:var(--fs-sm);margin-bottom:var(--space-xl);">
                <?php echo esc_html(alm_t('wiz.confirm_email')); ?>
            </p>
            <div style="background:var(--color-bg);border-radius:var(--radius-md);padding:var(--space-lg);font-size:var(--fs-sm);text-align:left;">
                <strong><?php echo esc_html(alm_t('wiz.checkin_inst')); ?></strong><br>
                <?php echo esc_html(alm_t('wiz.checkin_times')); ?><br>
                <br>
                <strong><?php echo esc_html(alm_t('wiz.address_label')); ?></strong><br>
                Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT)
            </div>
            <div style="margin-top:var(--space-xl);">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline">
                    <?php echo esc_html(alm_t('wiz.back_home')); ?>
                </a>
            </div>
        </div>
    </div>

</div><!-- /.alm-booking-wizard -->
