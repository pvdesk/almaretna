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
     * Determina la modalità di visualizzazione camere in base agli ospiti.
     *
     * nerina    → solo camera singola (1 adulto, 0 bambini)
     * standalone → matrimoniali + UGO singolo (2 adulti, 0 bambini)
     * carmina   → solo Suite CARMINA (2 adulti + 1-2 bambini, o 1 adulto + bambini)
     * multi     → matrimoniali + Suite CARMINA (gruppi numerosi o 2A+3B)
     *
     * @param int $adults
     * @param int $children
     * @return string
     */
    public static function get_display_mode(int $adults, int $children): string {
        if ($adults === 1 && $children === 0) {
            return 'nerina';
        }
        if ($adults === 2 && $children === 0) {
            return 'standalone';
        }
        if ($children >= 1 && $children <= 2) {
            return 'carmina';
        }
        return 'multi';
    }

    /**
     * Restituisce le camere disponibili applicando le regole di business
     * (quale tipologia mostrare in base al numero di ospiti).
     *
     * La camera doppia della suite (room_id che termina in -D) non viene
     * mai restituita individualmente: è sempre inclusa nella Suite CARMINA.
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
        $nights    = static::get_nights($checkin, $checkout);
        $mode      = static::get_display_mode($adults, $children);

        foreach ($all_rooms as $room) {
            $is_singola  = $room->capacity_adults === 1;
            $is_suite_secondary = $room->is_family_unit && str_ends_with($room->room_id, '-D');
            $is_suite_main      = $room->is_family_unit && !$is_suite_secondary;
            $is_matrimoniale    = !$is_singola && !$room->is_family_unit;

            // La camera secondaria della suite (doppia) non appare mai da sola
            if ($is_suite_secondary) {
                continue;
            }

            // Applica regole di visualizzazione per modalità
            switch ($mode) {
                case 'nerina':
                    // Solo camere singole
                    if (!$is_singola) continue 2;
                    break;

                case 'standalone':
                    // SARA, ANNAMARIA, UGO standalone — no singole
                    if ($is_singola) continue 2;
                    // Verifica capacità adulti per matrimoniali
                    if ($is_matrimoniale && $room->capacity_adults < $adults) continue 2;
                    break;

                case 'carmina':
                    // Solo Suite CARMINA (UGO come suite)
                    if (!$is_suite_main) continue 2;
                    break;

                case 'multi':
                default:
                    // SARA, ANNAMARIA, Suite CARMINA — no singole
                    if ($is_singola) continue 2;
                    // Matrimoniali: verifica capacità adulti
                    if ($is_matrimoniale && $room->capacity_adults < $adults) continue 2;
                    break;
            }

            // Verifica disponibilità date
            if (!static::check($room->id, $checkin, $checkout)) {
                continue;
            }

            // Verifica soggiorno minimo
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
