<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Activator
 *
 * Eseguito all'attivazione del plugin via register_activation_hook.
 * Crea le tabelle DB e inserisce i dati predefiniti.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Gestisce l'attivazione del plugin.
 */
class ALM_Activator {

    /** @var string Versione corrente dello schema DB. */
    private const DB_VERSION = '1.0.0';

    /**
     * Attiva il plugin: crea tabelle, inserisce dati di default.
     */
    public static function activate(): void {
        self::create_tables();
        self::insert_default_extras();
        update_option('alm_booking_db_version', self::DB_VERSION);
        flush_rewrite_rules();
    }

    /**
     * Crea tutte le tabelle custom con dbDelta().
     */
    private static function create_tables(): void {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();
        $prefix  = $wpdb->prefix;

        // dbDelta richiede un formato preciso: 2 spazi prima delle KEY, no trailing comma
        $sql = [];

        // ── alm_bookings ────────────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_bookings (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_ref     VARCHAR(20) NOT NULL,
  room_id         BIGINT UNSIGNED NOT NULL,
  channel         ENUM('direct','booking','airbnb','manual') NOT NULL DEFAULT 'direct',
  external_id     VARCHAR(100) DEFAULT NULL,
  guest_name      VARCHAR(100) NOT NULL,
  guest_email     VARCHAR(100) NOT NULL,
  guest_phone     VARCHAR(30) DEFAULT NULL,
  guest_adults    TINYINT UNSIGNED NOT NULL DEFAULT 1,
  guest_children  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  checkin_date    DATE NOT NULL,
  checkout_date   DATE NOT NULL,
  nights          TINYINT UNSIGNED NOT NULL,
  price_per_night DECIMAL(8,2) NOT NULL,
  total_price     DECIMAL(10,2) NOT NULL,
  extras_total    DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  status          ENUM('pending','confirmed','cancelled','completed','no_show') NOT NULL DEFAULT 'pending',
  payment_status  ENUM('unpaid','partial','paid','refunded') NOT NULL DEFAULT 'unpaid',
  payment_ref     VARCHAR(100) DEFAULT NULL,
  special_requests TEXT DEFAULT NULL,
  notes           TEXT DEFAULT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY booking_ref (booking_ref),
  KEY idx_checkin (checkin_date),
  KEY idx_room_dates (room_id, checkin_date, checkout_date),
  KEY idx_status (status)
) $charset;";

        // ── alm_rates ───────────────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_rates (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  room_id         BIGINT UNSIGNED NOT NULL,
  rate_name       VARCHAR(100) DEFAULT NULL,
  date_from       DATE NOT NULL,
  date_to         DATE NOT NULL,
  price_per_night DECIMAL(8,2) NOT NULL,
  min_stay        TINYINT UNSIGNED NOT NULL DEFAULT 1,
  channel         VARCHAR(50) NOT NULL DEFAULT 'all',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_room_dates (room_id, date_from, date_to)
) $charset;";

        // ── alm_blocks ──────────────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_blocks (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  room_id     BIGINT UNSIGNED NOT NULL,
  date_from   DATE NOT NULL,
  date_to     DATE NOT NULL,
  reason      VARCHAR(200) DEFAULT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_room_dates (room_id, date_from, date_to)
) $charset;";

        // ── alm_extras ──────────────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_extras (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100) NOT NULL,
  description TEXT DEFAULT NULL,
  price       DECIMAL(8,2) NOT NULL,
  price_type  ENUM('per_stay','per_night','per_person') NOT NULL DEFAULT 'per_stay',
  active      TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) $charset;";

        // ── alm_booking_extras ──────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_booking_extras (
  booking_id  BIGINT UNSIGNED NOT NULL,
  extra_id    BIGINT UNSIGNED NOT NULL,
  quantity    TINYINT UNSIGNED NOT NULL DEFAULT 1,
  price       DECIMAL(8,2) NOT NULL,
  PRIMARY KEY (booking_id, extra_id)
) $charset;";

        // ── alm_sync_log ────────────────────────────────────────────────────
        $sql[] = "CREATE TABLE {$prefix}alm_sync_log (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  channel     VARCHAR(50) DEFAULT NULL,
  action      VARCHAR(50) DEFAULT NULL,
  status      ENUM('success','error') NOT NULL,
  message     TEXT DEFAULT NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_created (created_at)
) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        foreach ($sql as $query) {
            dbDelta($query);
        }
    }

    /**
     * Inserisce i servizi extra predefiniti.
     */
    private static function insert_default_extras(): void {
        global $wpdb;
        $table = $wpdb->prefix . 'alm_extras';

        // Controlla se esistono già
        $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");
        if ($count > 0) {
            return;
        }

        $extras = [
            [
                'name'        => 'Parcheggio aggiuntivo',
                'description' => 'Posto auto aggiuntivo nel parcheggio privato (se disponibile)',
                'price'       => 10.00,
                'price_type'  => 'per_night',
                'active'      => 1,
            ],
        ];

        foreach ($extras as $extra) {
            $wpdb->insert($table, $extra, ['%s', '%s', '%f', '%s', '%d']);
        }
    }
}
