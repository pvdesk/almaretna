<?php
/**
 * Template Name: Pagina Prenotazione
 * Template Post Type: page
 *
 * Template per la pagina /prenota/ — wizard prenotazione a 4 step con layout Bootstrap.
 *
 * @package AlmaretnaChild
 */

declare(strict_types=1);

get_header();
?>

<main id="main-content" class="page-booking">

    <!-- Hero prenotazione -->
    <section style="
        background: var(--color-accent);
        border-bottom: 1px solid var(--color-border);
        padding: calc(var(--header-h) + 2.5rem) 2rem 3rem;
        text-align: center;
    ">
        <div style="max-width:680px;margin:0 auto;">
            <div class="page-hero__breadcrumb" style="display:flex;align-items:center;justify-content:center;gap:.75rem;text-transform:uppercase;font-weight:700;letter-spacing:.22em;font-size:.7rem;">
                <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
                <span style="color:var(--color-border);">/</span>
                <span style="color:var(--color-secondary);"><?php the_title(); ?></span>
            </div>
            <h1 style="font-family:var(--font-heading);font-size:clamp(1.75rem,4vw,2.75rem);font-weight:700;color:var(--color-text);letter-spacing:-.02em;margin-bottom:.875rem;">
                <?php the_title(); ?>
            </h1>
            <p style="font-size:.95rem;color:var(--color-text-light);line-height:1.7;">
                <?php esc_html_e('Check-in dalle 15:00 &nbsp;·&nbsp; Check-out entro le 11:00', 'almaretna-child'); ?>
            </p>
        </div>
    </section>

    <!-- Wizard prenotazione -->
    <section class="py-5" style="background-color: var(--color-bg);">
        <div class="container">

            <!-- Indicatore step -->
            <nav class="booking-steps d-flex align-items-center justify-content-between mx-auto mb-5 text-center flex-wrap gap-2" 
                 style="max-width: 680px;" aria-label="<?php esc_attr_e('Fasi prenotazione', 'almaretna-child'); ?>">
                
                <div class="booking-step booking-steps__item is-active" data-step="1">
                    <div class="booking-step__number">1</div>
                    <div class="booking-step__label"><?php esc_html_e('Date', 'almaretna-child'); ?></div>
                </div>
                <div class="booking-step-connector" data-connector="1"></div>
                
                <div class="booking-step booking-steps__item" data-step="2">
                    <div class="booking-step__number">2</div>
                    <div class="booking-step__label"><?php esc_html_e('Camera', 'almaretna-child'); ?></div>
                </div>
                <div class="booking-step-connector" data-connector="2"></div>
                
                <div class="booking-step booking-steps__item" data-step="3">
                    <div class="booking-step__number">3</div>
                    <div class="booking-step__label"><?php esc_html_e('Dati', 'almaretna-child'); ?></div>
                </div>
                <div class="booking-step-connector" data-connector="3"></div>
                
                <div class="booking-step booking-steps__item" data-step="4">
                    <div class="booking-step__number">4</div>
                    <div class="booking-step__label"><?php esc_html_e('Pagamento', 'almaretna-child'); ?></div>
                </div>
            </nav>

            <!-- Form prenotazione (dal plugin) -->
            <div class="booking-form-wrap mx-auto p-4 p-md-5 rounded-4 bg-white shadow-sm border border-light" style="max-width: 900px;">
                <?php
                // Priorità: shortcode del plugin
                echo do_shortcode('[alm_booking_form]');
                ?>
            </div>

        </div>
    </section>

    <!-- Rassicurazioni in Colonne Bootstrap -->
    <section class="py-5 bg-white border-top border-light">
        <div class="container py-3">
            <div class="row g-4 text-center">
                <div class="col-md-4">
                    <div class="px-3">
                        <span class="dashicons dashicons-lock d-inline-block mb-3" style="font-size: 32px; width: 32px; height: 32px; color: var(--color-secondary);"></span>
                        <h4 class="font-heading fw-bold mb-2 text-primary">
                            <?php esc_html_e('Pagamento sicuro', 'almaretna-child'); ?>
                        </h4>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            <?php esc_html_e('Dati crittografati SSL. Pagamento gestito e protetto da Stripe.', 'almaretna-child'); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 border-start border-light border-0-mobile">
                    <div class="px-3">
                        <span class="dashicons dashicons-calendar-alt d-inline-block mb-3" style="font-size: 32px; width: 32px; height: 32px; color: var(--color-secondary);"></span>
                        <h4 class="font-heading fw-bold mb-2 text-primary">
                            <?php esc_html_e('Prenotazione diretta', 'almaretna-child'); ?>
                        </h4>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            <?php esc_html_e('Senza intermediari. Miglior prezzo online garantito.', 'almaretna-child'); ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 border-start border-light border-0-mobile">
                    <div class="px-3">
                        <span class="dashicons dashicons-email-alt d-inline-block mb-3" style="font-size: 32px; width: 32px; height: 32px; color: var(--color-secondary);"></span>
                        <h4 class="font-heading fw-bold mb-2 text-primary">
                            <?php esc_html_e('Conferma immediata', 'almaretna-child'); ?>
                        </h4>
                        <p class="text-muted small mb-0" style="line-height: 1.6;">
                            <?php esc_html_e('Ricevi la conferma via email all’istante, non appena completato il pagamento.', 'almaretna-child'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<style>
@media (max-width: 767.98px) {
    .border-0-mobile {
        border-left: 0 !important;
    }
}
</style>

<?php get_footer(); ?>
