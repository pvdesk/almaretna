<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Deactivator
 *
 * Eseguito alla disattivazione del plugin.
 * NON rimuove dati o tabelle (quello fa uninstall.php).
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Gestisce la disattivazione del plugin.
 */
class ALM_Deactivator {

    /**
     * Disattiva il plugin: pulisce cron e rewrite rules.
     */
    public static function deactivate(): void {
        self::clear_scheduled_events();
        flush_rewrite_rules();
    }

    /**
     * Rimuove tutti gli eventi cron schedulati dal plugin.
     */
    private static function clear_scheduled_events(): void {
        $hooks = [
            'alm_send_checkin_reminder',
            'alm_sync_beds24',
        ];

        foreach ($hooks as $hook) {
            wp_clear_scheduled_hook($hook);
        }
    }
}
