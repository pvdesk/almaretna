<?php
declare(strict_types=1);

/**
 * Almaretna Booking — Uninstall
 *
 * Rimuove tutte le tabelle custom e le opzioni del plugin.
 * NON rimuove i CPT almaretna_room (gestiti dal child theme)
 * né i loro dati (preziosi per lo storico).
 *
 * @package AlmaretnaBooking
 */

// Sicurezza: eseguito solo da WordPress durante la disinstallazione
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// ─── Rimuovi tabelle custom ───────────────────────────────────────────────────

$tables = [
    $wpdb->prefix . 'alm_booking_extras',
    $wpdb->prefix . 'alm_sync_log',
    $wpdb->prefix . 'alm_extras',
    $wpdb->prefix . 'alm_blocks',
    $wpdb->prefix . 'alm_rates',
    $wpdb->prefix . 'alm_bookings',
];

foreach ($tables as $table) {
    // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query("DROP TABLE IF EXISTS `{$table}`");
}

// ─── Rimuovi opzioni WordPress ────────────────────────────────────────────────

$options = [
    'alm_booking_db_version',
    'alm_booking_settings',
    'alm_stripe_settings',
    'alm_beds24_settings',
    'alm_notification_settings',
];

foreach ($options as $option) {
    delete_option($option);
}

// ─── Rimuovi cron jobs schedulati ─────────────────────────────────────────────

$cron_hooks = [
    'alm_send_checkin_reminder',
    'alm_sync_beds24',
];

foreach ($cron_hooks as $hook) {
    $timestamp = wp_next_scheduled($hook);
    if ($timestamp) {
        wp_unschedule_event($timestamp, $hook);
    }
    wp_clear_scheduled_hook($hook);
}

// ─── Rimuovi transients ───────────────────────────────────────────────────────

$wpdb->query(
    "DELETE FROM `{$wpdb->options}` WHERE `option_name` LIKE '_transient_alm_%' OR `option_name` LIKE '_transient_timeout_alm_%'"
);
