<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Availability
 *
 * Gestisce la disponibilità delle camere: controllo prenotazioni,
 * blocchi manuali, logica suite familiare e mansarda.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Gestione disponibilità camere.
 */
class ALM_Availability {

    /**
     * Verifica se una camera è disponibile per il periodo richiesto.
     *
     * Controlla:
     * - Prenotazioni esistenti (status != cancelled)
     * - Blocchi manuali
     * - Camera partner (bagno condiviso per suite familiare)
     *
     * @param int    $room_post_id  ID post WordPress della camera.
     * @param string $checkin       Data check-in Y-m-d.
     * @param string $checkout      Data check-out Y-m-d.
     * @return bool
     */
    public static function check(int $room_post_id, string $checkin, string $checkout): bool {
        global $wpdb;

        $prefix = $wpdb->prefix;

        // ── Controllo prenotazioni esistenti ─────────────────────────────────
        $booked = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$prefix}alm_bookings
             WHERE room_id = %d
               AND status NOT IN ('cancelled')
               AND checkin_date  < %s
               AND checkout_date > %s",
            $room_post_id,
            $checkout,
            $checkin
        ));

        if ($booked > 0) {
            return false;
        }

        // ── Controllo blocchi manuali ─────────────────────────────────────────
        $blocked = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$prefix}alm_blocks
             WHERE room_id = %d
               AND date_from < %s
               AND date_to   > %s",
            $room_post_id,
            $checkout,
            $checkin
        ));

        if ($blocked > 0) {
            return false;
        }

        // ── Logica suite familiare (P2-FAM-M / P2-FAM-D) ─────────────────────
        try {
            $room = new ALM_Room($room_post_id);

            if ($room->is_family_unit && !empty($room->family_unit_rooms)) {
                $partner = $room->get_family_partner();
                if ($partner) {
                    // Se la camera partner è occupata, la suite non è disponibile
                    // come unità separata (bagno condiviso bloccato)
                    $partner_booked = (int) $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM {$prefix}alm_bookings
                         WHERE room_id = %d
                           AND status NOT IN ('cancelled')
                           AND checkin_date  < %s
                           AND checkout_date > %s",
                        $partner->id,
                        $checkout,
                        $checkin
                    ));

                    // Le camere della suite possono essere prenotate separatamente
                    // (logica spec: solo avviso, non blocco)
                    // Il blocco incrociato si applica solo se è la stessa prenotazione
                    // Per prenotazioni separate: permettiamo ma aggiungiamo sharing_warning
                }
            }
        } catch (InvalidArgumentException) {
            // Camera non trovata — non bloccare
        }

        return true;
    }

    /**
     * Restituisce le camere disponibili per le date e la capacità richiesta.
     *
     * @param string $checkin
     * @param string $checkout
     * @param int    $adults
     * @param int    $children
     * @return ALM_Room[]
     */
    public static function get_available_rooms(
        string $checkin,
        string $checkout,
        int $adults,
        int $children = 0
    ): array {
        $all_rooms = ALM_Room::get_all();
        $available = [];

        foreach ($all_rooms as $room) {
            // Verifica capacità adulti
            if ($room->capacity_adults < $adults) {
                continue;
            }

            // Verifica disponibilità
            if (!static::check($room->id, $checkin, $checkout)) {
                continue;
            }

            // Verifica soggiorno minimo
            $nights = static::get_nights($checkin, $checkout);
            if ($nights < $room->min_stay) {
                continue;
            }

            $available[] = $room;
        }

        return $available;
    }

    /**
     * Restituisce le date bloccate per una camera in un intervallo di mesi.
     *
     * @param int    $room_post_id
     * @param string $month_from  Y-m
     * @param string $month_to    Y-m
     * @return string[]  Array di date Y-m-d
     */
    public static function get_blocked_dates(
        int $room_post_id,
        string $month_from,
        string $month_to
    ): array {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $date_from = $month_from . '-01';
        $date_to   = date('Y-m-t', strtotime($month_to . '-01'));

        // Date da prenotazioni attive
        $bookings = $wpdb->get_results($wpdb->prepare(
            "SELECT checkin_date, checkout_date FROM {$prefix}alm_bookings
             WHERE room_id = %d
               AND status NOT IN ('cancelled')
               AND checkin_date  <= %s
               AND checkout_date >= %s",
            $room_post_id,
            $date_to,
            $date_from
        ));

        // Date da blocchi manuali
        $blocks = $wpdb->get_results($wpdb->prepare(
            "SELECT date_from AS checkin_date, date_to AS checkout_date FROM {$prefix}alm_blocks
             WHERE room_id = %d
               AND date_from  <= %s
               AND date_to    >= %s",
            $room_post_id,
            $date_to,
            $date_from
        ));

        $ranges  = array_merge($bookings ?: [], $blocks ?: []);
        $blocked = [];

        foreach ($ranges as $range) {
            $current = new DateTime($range->checkin_date);
            $end     = new DateTime($range->checkout_date);

            // checkout_date = giorno di partenza (non bloccato)
            while ($current < $end) {
                $blocked[] = $current->format('Y-m-d');
                $current->modify('+1 day');
            }
        }

        return array_values(array_unique($blocked));
    }

    /**
     * Aggiunge un blocco manuale di disponibilità.
     *
     * @param int    $room_post_id
     * @param string $date_from  Y-m-d
     * @param string $date_to    Y-m-d
     * @param string $reason
     * @return bool
     */
    public static function block(
        int $room_post_id,
        string $date_from,
        string $date_to,
        string $reason = ''
    ): bool {
        global $wpdb;

        $result = $wpdb->insert(
            $wpdb->prefix . 'alm_blocks',
            [
                'room_id'   => $room_post_id,
                'date_from' => $date_from,
                'date_to'   => $date_to,
                'reason'    => $reason,
            ],
            ['%d', '%s', '%s', '%s']
        );

        return $result !== false;
    }

    /**
     * Rimuove un blocco manuale per ID.
     *
     * @param int $block_id
     * @return bool
     */
    public static function unblock(int $block_id): bool {
        global $wpdb;

        $result = $wpdb->delete(
            $wpdb->prefix . 'alm_blocks',
            ['id' => $block_id],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Restituisce avvisi per camere con bagno condiviso entrambe occupate.
     *
     * @param string $checkin
     * @param string $checkout
     * @return array<int, array{room_a: string, room_b: string, message: string}>
     */
    public static function get_sharing_warnings(string $checkin, string $checkout): array {
        global $wpdb;
        $prefix   = $wpdb->prefix;
        $warnings = [];
        $checked  = [];

        $shared_rooms = get_posts([
            'post_type'      => 'almaretna_room',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => [[
                'key'     => '_room_bathroom_type',
                'value'   => 'shared',
                'compare' => '=',
            ]],
        ]);

        foreach ($shared_rooms as $post) {
            try {
                $room = new ALM_Room($post);
            } catch (InvalidArgumentException) {
                continue;
            }

            $pair_key = implode('|', array_unique([$room->room_id, $room->shared_with]));
            if (in_array($pair_key, $checked, true)) {
                continue;
            }
            $checked[] = $pair_key;

            $partner = $room->get_bathroom_partner();
            if (!$partner) {
                continue;
            }

            $room_booked = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$prefix}alm_bookings
                 WHERE room_id = %d AND status NOT IN ('cancelled')
                   AND checkin_date < %s AND checkout_date > %s",
                $room->id, $checkout, $checkin
            ));

            $partner_booked = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$prefix}alm_bookings
                 WHERE room_id = %d AND status NOT IN ('cancelled')
                   AND checkin_date < %s AND checkout_date > %s",
                $partner->id, $checkout, $checkin
            ));

            if ($room_booked > 0 && $partner_booked > 0) {
                $warnings[] = [
                    'room_a'  => $room->title,
                    'room_b'  => $partner->title,
                    'message' => sprintf(
                        'Bagno condiviso tra "%s" e "%s" — entrambe occupate nel periodo %s / %s.',
                        $room->title,
                        $partner->title,
                        $checkin,
                        $checkout
                    ),
                ];
            }
        }

        return $warnings;
    }

    /**
     * Calcola il numero di notti tra due date.
     *
     * @param string $checkin   Y-m-d
     * @param string $checkout  Y-m-d
     * @return int
     */
    public static function get_nights(string $checkin, string $checkout): int {
        $d1 = new DateTime($checkin);
        $d2 = new DateTime($checkout);
        return max(0, (int) $d1->diff($d2)->days);
    }
}
