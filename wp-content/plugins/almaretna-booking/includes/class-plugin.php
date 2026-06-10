<?php
declare(strict_types=1);

/**
 * Almaretna Booking — Classe core ALM_Plugin
 *
 * Inizializza e registra tutti gli hook del plugin.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Classe principale del plugin. Orchestra admin, public e API.
 */
class ALM_Plugin {

    /** @var string Versione corrente del plugin. */
    private string $version;

    /**
     * Costruttore: imposta versione e carica dipendenze.
     */
    public function __construct() {
        $this->version = ALM_BOOKING_VERSION;
        $this->load_dependencies();
    }

    /**
     * Require_once di tutti i file di classe non gestiti dall'autoloader.
     */
    private function load_dependencies(): void {
        // Tutte le classi sono gestite dall'autoloader nel main file.
        // Qui carichiamo eventuali helper o file non-classe.
    }

    /**
     * Avvia il plugin: registra tutti gli hook.
     */
    public function run(): void {
        // WP 6.7+: load_plugin_textdomain deve essere sull'hook init o successivo
        add_action('init', [$this, 'load_textdomain']);
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
        $this->define_cron_hooks();
    }

    /**
     * Carica il text domain per le traduzioni.
     * Registrato su 'init' per rispettare WP 6.7.0+.
     */
    public function load_textdomain(): void {
        load_plugin_textdomain(
            'almaretna-booking',
            false,
            dirname(plugin_basename(ALM_BOOKING_FILE)) . '/languages/'
        );
    }

    /**
     * Registra gli hook dell'area amministrativa.
     */
    private function define_admin_hooks(): void {
        if (!is_admin()) {
            return;
        }

        $admin = new ALM_Admin($this->version);

        add_action('admin_menu',            [$admin, 'add_admin_menus']);
        add_action('admin_enqueue_scripts', [$admin, 'enqueue_assets']);
        add_action('admin_post_alm_save_settings', [$admin, 'save_settings']);
        add_action('admin_post_alm_save_rates',    [$admin, 'save_rate']);
        add_action('admin_post_alm_save_base_price', [$admin, 'save_base_price']);
        add_action('admin_post_alm_delete_rate',   [$admin, 'delete_rate']);
        add_action('admin_post_alm_add_block',     [$admin, 'add_block']);
        add_action('admin_post_alm_remove_block',  [$admin, 'remove_block']);
        add_action('admin_post_alm_cancel_booking',[$admin, 'cancel_booking']);
        add_action('admin_post_alm_sync_beds24',   [$admin, 'sync_beds24']);
        add_action('wp_ajax_alm_admin_get_bookings',    [$admin, 'ajax_get_bookings']);
        add_action('wp_ajax_alm_admin_get_calendar',  [$admin, 'ajax_get_calendar']);
        add_action('wp_ajax_alm_test_beds24',         [$admin, 'ajax_test_beds24']);
        add_action('wp_ajax_alm_write_stripe_config', [$admin, 'ajax_write_stripe_config']);
    }

    /**
     * Registra gli hook del frontend pubblico.
     */
    private function define_public_hooks(): void {
        $public = new ALM_Public($this->version);

        add_action('wp_enqueue_scripts', [$public, 'enqueue_assets']);

        // Shortcodes
        $shortcodes = new ALM_Shortcodes();
        $shortcodes->register();
    }

    /**
     * Registra gli endpoint REST API.
     */
    private function define_api_hooks(): void {
        $api = new ALM_API();
        add_action('rest_api_init', [$api, 'register_routes']);
    }

    /**
     * Registra gli hook per wp-cron.
     */
    private function define_cron_hooks(): void {
        // Reminder pre-arrivo
        add_action('alm_send_checkin_reminder', [ALM_Notifications::class, 'process_reminder_queue']);

        // Frequenza custom — display senza __() perché cron_schedules può sparare
        // prima di init e causerebbe il notice "textdomain triggered too early"
        add_filter('cron_schedules', function (array $schedules): array {
            $schedules['five_times_daily'] = [
                'interval' => 17280,
                'display'  => 'Cinque volte al giorno',
            ];
            return $schedules;
        });

        // Sync con Beds24 (cinque volte al giorno)
        add_action('alm_sync_beds24', function (): void {
            if (ALM_Beds24::is_configured()) {
                ALM_Beds24::sync_all();
            }
        });

        // Scheduling su init: wp_schedule_event chiama wp_get_schedules() che spara
        // cron_schedules → se fosse qui (plugins_loaded) triggererebbe __() troppo presto
        add_action('init', function (): void {
            $current_schedule = wp_get_schedule('alm_sync_beds24');
            if ($current_schedule && $current_schedule !== 'five_times_daily') {
                wp_clear_scheduled_hook('alm_sync_beds24');
            }
            if (!wp_next_scheduled('alm_sync_beds24')) {
                wp_schedule_event(time(), 'five_times_daily', 'alm_sync_beds24');
            }
        }, 1);
    }

    /**
     * Restituisce la versione del plugin.
     */
    public function get_version(): string {
        return $this->version;
    }
}
