<?php
declare(strict_types=1);

/**
 * Plugin Name:       Almaretna Booking
 * Plugin URI:        https://almaretna.it
 * Description:       Sistema di prenotazione diretta per Almaretna. Integra Stripe Payments e Beds24 channel manager.
 * Version:           1.0.0
 * Author:            Sviluppatore
 * Author URI:        https://almaretna.it
 * Text Domain:       almaretna-booking
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * License:           GPL-2.0-or-later
 */

defined('ABSPATH') || exit;

// ─── Costanti ─────────────────────────────────────────────────────────────────

define('ALM_BOOKING_VERSION', '1.0.0');
define('ALM_BOOKING_PATH',    plugin_dir_path(__FILE__));
define('ALM_BOOKING_URL',     plugin_dir_url(__FILE__));
define('ALM_BOOKING_FILE',    __FILE__);

// ─── Autoload classi ─────────────────────────────────────────────────────────

spl_autoload_register(function (string $class_name): void {
    $map = [
        'ALM_Plugin'       => ALM_BOOKING_PATH . 'includes/class-plugin.php',
        'ALM_Activator'    => ALM_BOOKING_PATH . 'includes/class-activator.php',
        'ALM_Deactivator'  => ALM_BOOKING_PATH . 'includes/class-deactivator.php',
        'ALM_Room'         => ALM_BOOKING_PATH . 'includes/class-room.php',
        'ALM_Booking'      => ALM_BOOKING_PATH . 'includes/class-booking.php',
        'ALM_Availability' => ALM_BOOKING_PATH . 'includes/class-availability.php',
        'ALM_Pricing'      => ALM_BOOKING_PATH . 'includes/class-pricing.php',
        'ALM_Stripe'       => ALM_BOOKING_PATH . 'includes/class-stripe.php',
        'ALM_Beds24'       => ALM_BOOKING_PATH . 'includes/class-beds24.php',
        'ALM_Google_API'   => ALM_BOOKING_PATH . 'includes/class-google-api.php',
        'ALM_Notifications'=> ALM_BOOKING_PATH . 'includes/class-notifications.php',
        'ALM_API'          => ALM_BOOKING_PATH . 'includes/class-api.php',
        'ALM_Admin'        => ALM_BOOKING_PATH . 'admin/class-admin.php',
        'ALM_Public'       => ALM_BOOKING_PATH . 'public/class-public.php',
        'ALM_Shortcodes'   => ALM_BOOKING_PATH . 'public/class-shortcodes.php',
    ];

    if (isset($map[$class_name]) && file_exists($map[$class_name])) {
        require_once $map[$class_name];
    }
});

// ─── Hook attivazione / disattivazione / disinstallazione ────────────────────

register_activation_hook(__FILE__, function (): void {
    ALM_Activator::activate();
});

register_deactivation_hook(__FILE__, function (): void {
    ALM_Deactivator::deactivate();
});

register_uninstall_hook(__FILE__, 'alm_booking_uninstall');

function alm_booking_uninstall(): void {
    $uninstall = ALM_BOOKING_PATH . 'uninstall.php';
    if (file_exists($uninstall)) {
        require_once $uninstall;
    }
}

// ─── Bootstrap ───────────────────────────────────────────────────────────────

add_action('plugins_loaded', function (): void {
    $plugin = new ALM_Plugin();
    $plugin->run();
});
