<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Public
 *
 * Gestisce enqueue di script e stili nel frontend.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Controller frontend pubblico.
 */
class ALM_Public {

    /** @var string Versione plugin. */
    private string $version;

    /**
     * @param string $version
     */
    public function __construct(string $version) {
        $this->version = $version;
    }

    /**
     * Enqueue script e stili solo sulle pagine che ne hanno bisogno.
     */
    public function enqueue_assets(): void {
        if (!$this->needs_booking_assets()) {
            return;
        }

        $plugin_url = ALM_BOOKING_URL;

        // ── Stili ────────────────────────────────────────────────────────────

        // Flatpickr datepicker
        wp_enqueue_style(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css',
            [],
            null
        );

        // CSS plugin
        wp_enqueue_style(
            'alm-booking-public',
            $plugin_url . 'public/assets/booking.css',
            ['flatpickr'],
            $this->version
        );

        // ── Script ───────────────────────────────────────────────────────────

        // Flatpickr core
        wp_enqueue_script(
            'flatpickr',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js',
            [],
            null,
            true
        );

        // Flatpickr locale italiano
        wp_enqueue_script(
            'flatpickr-it',
            'https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/it.js',
            ['flatpickr'],
            null,
            true
        );

        // Stripe.js — caricato da js.stripe.com per compliance PCI
        wp_enqueue_script(
            'stripe-js',
            'https://js.stripe.com/v3/',
            [],
            null,
            true
        );

        // JS principale del plugin
        wp_enqueue_script(
            'alm-booking-js',
            $plugin_url . 'public/assets/booking.js',
            ['flatpickr', 'flatpickr-it', 'stripe-js'],
            $this->version,
            true
        );

        // Variabili JS
        wp_localize_script('alm-booking-js', 'alm_vars', [
            'rest_url'             => get_rest_url(null, 'scv/v1/'),
            'ajax_url'             => admin_url('admin-ajax.php'),
            'nonce'                => wp_create_nonce('wp_rest'),
            'stripe_publishable_key' => defined('ALM_STRIPE_PUBLISHABLE_KEY') ? ALM_STRIPE_PUBLISHABLE_KEY : '',
            'checkin_time'         => '15:00',
            'checkout_time'        => '11:00',
            'min_stay_default'     => 1,
            'confirmation_url'     => get_permalink(get_page_by_path('prenota')),
            'i18n' => [
                'loading'            => __('Caricamento...', 'almaretna-booking'),
                'no_rooms'           => __('Nessuna camera disponibile per le date selezionate.', 'almaretna-booking'),
                'select_dates'       => __('Seleziona le date per vedere le camere disponibili.', 'almaretna-booking'),
                'error_generic'      => __('Si è verificato un errore. Riprova.', 'almaretna-booking'),
                'error_payment'      => __('Errore nel pagamento. Verifica i dati della carta.', 'almaretna-booking'),
                'pay_now'            => __('Paga ora', 'almaretna-booking'),
                'processing'         => __('Elaborazione in corso...', 'almaretna-booking'),
                'select_room'        => __('Seleziona una camera per continuare.', 'almaretna-booking'),
                'fill_required'      => __('Compila tutti i campi obbligatori.', 'almaretna-booking'),
                'accept_privacy'     => __('Accetta la privacy policy per continuare.', 'almaretna-booking'),
                'accept_tc'          => __('Accetta i termini e condizioni per continuare.', 'almaretna-booking'),
                'nights'             => __('notti', 'almaretna-booking'),
                'night'              => __('notte', 'almaretna-booking'),
                'adults'             => __('adulti', 'almaretna-booking'),
                'children'           => __('bambini', 'almaretna-booking'),
                'per_night'          => __('/ notte', 'almaretna-booking'),
                'total'              => __('Totale', 'almaretna-booking'),
                'sharing_note_label' => __('Nota:', 'almaretna-booking'),
            ],
        ]);
    }

    /**
     * Verifica se la pagina corrente necessita degli asset di prenotazione.
     *
     * @return bool
     */
    private function needs_booking_assets(): bool {
        // Template pagina prenotazione
        if (is_page_template('templates/template-booking.php')) {
            return true;
        }

        // Singola camera
        if (is_singular('almaretna_room')) {
            return true;
        }

        // Pagina con slug "prenota"
        if (is_page('prenota')) {
            return true;
        }

        // Pagina con shortcode booking form nel contenuto
        global $post;
        if ($post instanceof WP_Post &&
            (has_shortcode($post->post_content, 'alm_booking_form') ||
             has_shortcode($post->post_content, 'alm_availability_calendar') ||
             has_shortcode($post->post_content, 'alm_room_price'))) {
            return true;
        }

        return false;
    }
}
