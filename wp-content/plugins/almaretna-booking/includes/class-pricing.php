<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Pricing
 *
 * Gestisce il calcolo prezzi con tariffe stagionali, canali e extras.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Motore di pricing per prenotazioni e tariffe stagionali.
 */
class ALM_Pricing {

    /**
     * Calcola il prezzo completo per un soggiorno.
     *
     * Logica di applicazione tariffe (in ordine di priorità):
     *  1. Tariffa stagionale per canale specifico nel range di date
     *  2. Tariffa stagionale per canale 'all' nel range di date
     *  3. Prezzo base della camera
     *
     * @param int    $room_post_id
     * @param string $checkin   Y-m-d
     * @param string $checkout  Y-m-d
     * @param string $channel   direct | booking | airbnb | all
     * @return array<string, mixed>|WP_Error
     */
    public static function get_price_for_dates(
        int    $room_post_id,
        string $checkin,
        string $checkout,
        string $channel = 'direct'
    ): array|WP_Error {
        // Carica camera
        try {
            $room = new ALM_Room($room_post_id);
        } catch (InvalidArgumentException) {
            return new WP_Error('room_not_found', __('Camera non trovata.', 'almaretna-booking'));
        }

        $nights = ALM_Availability::get_nights($checkin, $checkout);

        if ($nights <= 0) {
            return new WP_Error('invalid_dates', __('Date non valide: il checkout deve essere successivo al checkin.', 'almaretna-booking'));
        }

        // Verifica soggiorno minimo
        if ($nights < $room->min_stay) {
            return new WP_Error(
                'min_stay',
                sprintf(
                    __('Soggiorno minimo per questa camera: %d notti.', 'almaretna-booking'),
                    $room->min_stay
                )
            );
        }

        // Calcola prezzo notte per notte
        $breakdown          = [];
        $total_price        = 0.0;
        $applied_rate       = null;
        $current_date       = new DateTime($checkin);
        $checkout_date_obj  = new DateTime($checkout);

        // Recupera tutte le tariffe attive per questa camera nel periodo
        $rates = self::get_rates_for_room($room_post_id, $checkin, $checkout, $channel);

        while ($current_date < $checkout_date_obj) {
            $date_str    = $current_date->format('Y-m-d');
            $night_price = $room->base_price;
            $rate_used   = null;

            // Cerca tariffa applicabile per questa notte
            foreach ($rates as $rate) {
                if ($rate['date_from'] <= $date_str && $rate['date_to'] > $date_str) {
                    $night_price = (float) $rate['price_per_night'];
                    $rate_used   = $rate;
                    $applied_rate = $rate; // Tiene l'ultima applicata
                    break;
                }
            }

            $breakdown[] = [
                'date'        => $date_str,
                'price'       => $night_price,
                'rate_name'   => $rate_used ? ($rate_used['rate_name'] ?? 'Tariffa stagionale') : 'Prezzo base',
            ];

            $total_price += $night_price;
            $current_date->modify('+1 day');
        }

        $price_per_night = $nights > 0 ? round($total_price / $nights, 2) : $room->base_price;

        return [
            'nights'          => $nights,
            'base_price'      => $room->base_price,
            'applied_rate'    => $applied_rate,
            'price_per_night' => $price_per_night,
            'subtotal'        => round($total_price, 2),
            'extras'          => 0.0,
            'total'           => round($total_price, 2),
            'min_stay'        => $room->min_stay,
            'breakdown'       => $breakdown,
        ];
    }

    /**
     * Restituisce le tariffe stagionali attive per una camera in un periodo.
     *
     * Priorità: tariffa per canale specifico > tariffa per canale 'all'.
     *
     * @param int    $room_post_id
     * @param string $date_from  Y-m-d (vuoto = nessun filtro)
     * @param string $date_to    Y-m-d (vuoto = nessun filtro)
     * @param string $channel    'all' = tutte le tariffe
     * @return array<int, array<string, mixed>>
     */
    public static function get_rates_for_room(
        int    $room_post_id,
        string $date_from = '',
        string $date_to   = '',
        string $channel   = 'all'
    ): array {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $where  = ['room_id = %d'];
        $params = [$room_post_id];

        if ($date_from && $date_to) {
            // Tariffe che si sovrappongono al periodo richiesto
            $where[]  = 'date_from < %s';
            $where[]  = 'date_to > %s';
            $params[] = $date_to;
            $params[] = $date_from;
        }

        // Filtra per canale: include tariffe per canale specifico e per 'all'
        if ($channel !== 'all') {
            $where[]  = "(channel = %s OR channel = 'all')";
            $params[] = $channel;
        }

        $sql = "SELECT * FROM {$prefix}alm_rates WHERE " .
               implode(' AND ', $where) .
               " ORDER BY FIELD(channel, %s, 'all') ASC, date_from ASC";
        $params[] = $channel;

        $rates = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

        // Priorità: tariffa canale specifico batte tariffa 'all' per lo stesso periodo
        return self::resolve_rate_priority($rates ?: [], $channel);
    }

    /**
     * Risolve la priorità tra tariffe: canale specifico batte 'all'.
     *
     * @param array<int, array<string, mixed>> $rates
     * @param string $channel
     * @return array<int, array<string, mixed>>
     */
    private static function resolve_rate_priority(array $rates, string $channel): array {
        if ($channel === 'all') {
            return $rates;
        }

        $specific = array_filter($rates, fn(array $r): bool => $r['channel'] === $channel);
        $fallback = array_filter($rates, fn(array $r): bool => $r['channel'] === 'all');

        // Per ogni range, se esiste tariffa specifica usa quella, altrimenti 'all'
        $resolved = array_values($specific);

        foreach ($fallback as $fb_rate) {
            $covered = false;
            foreach ($specific as $sp_rate) {
                if ($sp_rate['date_from'] <= $fb_rate['date_from'] &&
                    $sp_rate['date_to']   >= $fb_rate['date_to']) {
                    $covered = true;
                    break;
                }
            }
            if (!$covered) {
                $resolved[] = $fb_rate;
            }
        }

        usort($resolved, fn(array $a, array $b): int => strcmp($a['date_from'], $b['date_from']));
        return $resolved;
    }

    /**
     * Crea una nuova tariffa stagionale.
     *
     * @param array<string, mixed> $data
     * @return int|WP_Error  ID tariffa inserita o WP_Error.
     */
    public static function create_rate(array $data): int|WP_Error {
        global $wpdb;

        // Validazione
        $required = ['room_id', 'date_from', 'date_to', 'price_per_night'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return new WP_Error(
                    'missing_field',
                    sprintf(__('Campo obbligatorio: %s', 'almaretna-booking'), $field)
                );
            }
        }

        $room_id        = absint($data['room_id']);
        $date_from      = sanitize_text_field($data['date_from']);
        $date_to        = sanitize_text_field($data['date_to']);
        $price          = (float) $data['price_per_night'];
        $min_stay       = isset($data['min_stay']) ? max(1, (int) $data['min_stay']) : 1;
        $channel        = isset($data['channel']) ? sanitize_text_field($data['channel']) : 'all';
        $rate_name      = isset($data['rate_name']) ? sanitize_text_field($data['rate_name']) : '';

        if ($date_from >= $date_to) {
            return new WP_Error('invalid_dates', __('La data di inizio deve essere precedente alla data di fine.', 'almaretna-booking'));
        }

        if ($price <= 0) {
            return new WP_Error('invalid_price', __('Il prezzo deve essere maggiore di zero.', 'almaretna-booking'));
        }

        if ($min_stay < 1) {
            return new WP_Error('invalid_min_stay', __('Il soggiorno minimo deve essere almeno 1 notte.', 'almaretna-booking'));
        }

        // Controlla sovrapposizioni per stessa camera e canale
        $overlap = self::check_rate_overlap($room_id, $date_from, $date_to, $channel);
        if ($overlap) {
            return new WP_Error(
                'rate_overlap',
                __('Esiste già una tariffa per questo periodo, camera e canale.', 'almaretna-booking')
            );
        }

        $result = $wpdb->insert(
            $wpdb->prefix . 'alm_rates',
            [
                'room_id'         => $room_id,
                'rate_name'       => $rate_name,
                'date_from'       => $date_from,
                'date_to'         => $date_to,
                'price_per_night' => $price,
                'min_stay'        => $min_stay,
                'channel'         => $channel,
            ],
            ['%d', '%s', '%s', '%s', '%f', '%d', '%s']
        );

        if ($result === false) {
            return new WP_Error('db_error', __('Errore nel salvataggio della tariffa.', 'almaretna-booking'));
        }

        return $wpdb->insert_id;
    }

    /**
     * Aggiorna una tariffa esistente.
     *
     * @param int                  $rate_id
     * @param array<string, mixed> $data
     * @return bool
     */
    public static function update_rate(int $rate_id, array $data): bool|WP_Error {
        global $wpdb;

        $update = [];
        $format = [];

        if (isset($data['rate_name'])) {
            $update['rate_name'] = sanitize_text_field($data['rate_name']);
            $format[] = '%s';
        }
        if (isset($data['date_from'])) {
            $update['date_from'] = sanitize_text_field($data['date_from']);
            $format[] = '%s';
        }
        if (isset($data['date_to'])) {
            $update['date_to'] = sanitize_text_field($data['date_to']);
            $format[] = '%s';
        }
        if (isset($data['price_per_night'])) {
            $update['price_per_night'] = (float) $data['price_per_night'];
            $format[] = '%f';
        }
        if (isset($data['min_stay'])) {
            $update['min_stay'] = max(1, (int) $data['min_stay']);
            $format[] = '%d';
        }
        if (isset($data['channel'])) {
            $update['channel'] = sanitize_text_field($data['channel']);
            $format[] = '%s';
        }

        if (empty($update)) {
            return false;
        }

        // Verifica sovrapposizione in caso di variazione date/canale
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}alm_rates WHERE id = %d", $rate_id
        ), ARRAY_A);
        if ($existing) {
            $room_id   = (int) $existing['room_id'];
            $date_from = $data['date_from'] ?? $existing['date_from'];
            $date_to   = $data['date_to']   ?? $existing['date_to'];
            $channel   = $data['channel']   ?? $existing['channel'];

            if (self::check_rate_overlap($room_id, $date_from, $date_to, $channel, $rate_id)) {
                return new WP_Error(
                    'rate_overlap',
                    __('Esiste già una tariffa per questo periodo, camera e canale.', 'almaretna-booking')
                );
            }
        }

        $result = $wpdb->update(
            $wpdb->prefix . 'alm_rates',
            $update,
            ['id' => $rate_id],
            $format,
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Elimina una tariffa.
     *
     * @param int $rate_id
     * @return bool
     */
    public static function delete_rate(int $rate_id): bool {
        global $wpdb;

        $result = $wpdb->delete(
            $wpdb->prefix . 'alm_rates',
            ['id' => $rate_id],
            ['%d']
        );

        return $result !== false;
    }

    /**
     * Calcola il totale degli extras selezionati.
     *
     * @param int[]  $extra_ids   IDs degli extras da alm_extras.
     * @param int    $nights      Numero notti (per extras per_night).
     * @param int    $guests      Numero ospiti totali (per extras per_person).
     * @return array{items: array<int, array<string, mixed>>, total: float}
     */
    public static function calculate_extras(
        array $extra_ids,
        int   $nights = 1,
        int   $guests = 1
    ): array {
        if (empty($extra_ids)) {
            return ['items' => [], 'total' => 0.0];
        }

        global $wpdb;
        $prefix = $wpdb->prefix;

        $placeholders = implode(',', array_fill(0, count($extra_ids), '%d'));
        $extras = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$prefix}alm_extras WHERE id IN ({$placeholders}) AND active = 1",
                ...$extra_ids
            ),
            ARRAY_A
        );

        $items = [];
        $total = 0.0;

        foreach ($extras ?: [] as $extra) {
            $unit_price = (float) $extra['price'];
            $quantity   = 1;

            $line_total = match ($extra['price_type']) {
                'per_night'  => $unit_price * $nights,
                'per_person' => $unit_price * $guests,
                default      => $unit_price, // per_stay
            };

            $items[] = [
                'id'          => (int) $extra['id'],
                'name'        => $extra['name'],
                'price'       => $unit_price,
                'price_type'  => $extra['price_type'],
                'quantity'    => $quantity,
                'line_total'  => round($line_total, 2),
            ];

            $total += $line_total;
        }

        return [
            'items' => $items,
            'total' => round($total, 2),
        ];
    }

    /**
     * Verifica se esistono tariffe che si sovrappongono a quelle da inserire.
     *
     * @param int    $room_id
     * @param string $date_from
     * @param string $date_to
     * @param string $channel
     * @param int    $exclude_id  ID da escludere (per update).
     * @return bool  True se c'è sovrapposizione.
     */
    private static function check_rate_overlap(
        int    $room_id,
        string $date_from,
        string $date_to,
        string $channel,
        int    $exclude_id = 0
    ): bool {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}alm_rates
             WHERE room_id  = %d
               AND channel  = %s
               AND date_from < %s
               AND date_to   > %s
               AND id != %d",
            $room_id,
            $channel,
            $date_to,
            $date_from,
            $exclude_id
        );

        return (int) $wpdb->get_var($sql) > 0;
    }

    /**
     * Restituisce tutte le tariffe di una camera formattate per la vista admin.
     *
     * @param int $room_post_id
     * @return array<int, array<string, mixed>>
     */
    public static function get_all_rates_for_room(int $room_post_id): array {
        global $wpdb;

        $rates = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}alm_rates
             WHERE room_id = %d
             ORDER BY date_from ASC",
            $room_post_id
        ), ARRAY_A);

        return $rates ?: [];
    }
}
