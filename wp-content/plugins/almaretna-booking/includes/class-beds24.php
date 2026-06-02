<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Beds24
 *
 * Integrazione con Beds24 API v2 (channel manager).
 * Gestisce sincronizzazione prenotazioni bidirezionale
 * e blocco disponibilità su Booking.com / Airbnb.
 *
 * Costanti richieste in wp-config.php:
 *   define('ALM_BEDS24_API_TOKEN',  'xxxxxxxx');  // token personale Beds24
 *   define('ALM_BEDS24_PROP_KEY',   'xxxxxxxx');  // prop-key della proprietà
 *   define('ALM_BEDS24_WEBHOOK_TOKEN', 'secret'); // token per autenticare webhook in ingresso
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_Beds24 {

    private const API_BASE = 'https://beds24.com/api/v2/';

    // ── Configurazione ──────────────────────────────────────────────────────

    public static function is_configured(): bool {
        return defined('ALM_BEDS24_API_TOKEN') && ALM_BEDS24_API_TOKEN !== '';
    }

    private static function api_token(): string {
        return defined('ALM_BEDS24_API_TOKEN') ? ALM_BEDS24_API_TOKEN : '';
    }

    private static function prop_key(): string {
        return defined('ALM_BEDS24_PROP_KEY') ? ALM_BEDS24_PROP_KEY : '';
    }

    // ── Sync completo (chiamato dal cron twicedaily) ─────────────────────────

    /**
     * Importa tutte le prenotazioni recenti da Beds24 e le salva nel DB locale.
     * Aggiorna le prenotazioni esistenti (identificate per beds24_booking_id).
     *
     * @return void
     */
    public static function sync_all(): void {
        if (!self::is_configured()) {
            return;
        }

        // Recupera prenotazioni degli ultimi 90 giorni + future
        $start = gmdate('Y-m-d', strtotime('-90 days'));
        $end   = gmdate('Y-m-d', strtotime('+365 days'));

        $result = self::get_bookings(['arrivalFrom' => $start, 'arrivalTo' => $end]);

        if (is_wp_error($result) || !isset($result['data'])) {
            self::log_sync('sync_all', 'error', is_wp_error($result) ? $result->get_error_message() : 'Risposta Beds24 non valida.');
            return;
        }

        $bookings = $result['data'];
        $synced   = 0;

        foreach ($bookings as $b24) {
            self::import_beds24_booking($b24);
            $synced++;
        }

        self::log_sync('sync_all', 'success', sprintf('Sincronizzate %d prenotazioni da Beds24.', $synced));
    }

    // ── Prenotazioni ────────────────────────────────────────────────────────

    /**
     * Recupera prenotazioni da Beds24.
     *
     * @param array $params Filtri opzionali: arrivalFrom, arrivalTo, departureFrom, departureTo, status…
     * @return array|WP_Error
     */
    public static function get_bookings(array $params = []): array|WP_Error {
        $query = array_merge([
            'includeInvoice' => 'false',
            'includeInfoItems'=> 'false',
        ], $params);

        return self::request('GET', 'bookings', [], $query);
    }

    /**
     * Crea una prenotazione su Beds24 partendo da una prenotazione locale.
     *
     * @param int $booking_id  ID del record locale in alm_bookings.
     * @return array|WP_Error
     */
    public static function push_booking(int $booking_id): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('beds24_not_configured', 'Beds24 non configurato.');
        }

        $booking = ALM_Booking::get($booking_id);
        if (!$booking) {
            return new WP_Error('booking_not_found', 'Prenotazione non trovata.');
        }

        $room = ALM_Room::get($booking->room_id);
        if (!$room || !$room->beds24_id) {
            return new WP_Error('beds24_room_not_mapped', 'Camera non mappata su Beds24.');
        }

        $name_parts = explode(' ', $booking->guest_name, 2);
        $first_name = $name_parts[0] ?? $booking->guest_name;
        $last_name  = $name_parts[1] ?? '';
        $propKey    = self::prop_key();

        $payload = [
            'roomId'       => (int) $room->beds24_id,
            'propKey'      => $propKey,
            'arrival'      => $booking->checkin_date,
            'departure'    => $booking->checkout_date,
            'firstName'    => $first_name,
            'lastName'     => $last_name,
            'email'        => $booking->guest_email,
            'phone'        => $booking->guest_phone ?? '',
            'numAdult'     => $booking->guest_adults,
            'numChild'     => $booking->guest_children,
            'status'       => self::map_status_to_beds24($booking->status),
            'price'        => (float) $booking->total_price,
            'currency'     => 'EUR',
            'notes'        => $booking->special_requests ?? '',
            'referer'      => 'AlmaretnaBooking',
            'externalRef'  => $booking->booking_ref,
        ];

        $result = self::request('POST', 'bookings', [$payload]);

        if (!is_wp_error($result) && !empty($result['data'][0]['id'])) {
            $beds24_id = (int) $result['data'][0]['id'];
            self::update_beds24_id($booking_id, $beds24_id);
            self::log_sync('push_booking', 'success', 'Prenotazione ' . $booking->booking_ref . ' inviata a Beds24 (ID: ' . $beds24_id . ').');
        }

        return $result;
    }

    /**
     * Aggiorna una prenotazione su Beds24.
     *
     * @param int    $beds24_booking_id  ID prenotazione su Beds24.
     * @param array  $updates            Campi da aggiornare.
     * @return array|WP_Error
     */
    public static function update_booking(int $beds24_booking_id, array $updates): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('beds24_not_configured', 'Beds24 non configurato.');
        }
        return self::request('PUT', 'bookings/' . $beds24_booking_id, $updates);
    }

    /**
     * Cancella una prenotazione su Beds24 impostando lo status su 'cancelled'.
     *
     * @param int $beds24_booking_id
     * @return array|WP_Error
     */
    public static function cancel_booking(int $beds24_booking_id): array|WP_Error {
        return self::update_booking($beds24_booking_id, ['status' => '4']); // 4 = cancelled
    }

    // ── Disponibilità / Blocco date ─────────────────────────────────────────

    /**
     * Recupera la disponibilità per una camera in un intervallo.
     *
     * @param string $beds24_room_id  ID camera su Beds24.
     * @param string $start           'YYYY-MM-DD'
     * @param string $end             'YYYY-MM-DD'
     * @return array|WP_Error  Array di date bloccate.
     */
    public static function get_availability(string $beds24_room_id, string $start, string $end): array|WP_Error {
        return self::request('GET', 'inventory/rooms', [], [
            'roomId'    => $beds24_room_id,
            'startDate' => $start,
            'endDate'   => $end,
        ]);
    }

    /**
     * Blocca date su Beds24 (es. manutenzione o blocco manuale).
     *
     * @param string $beds24_room_id
     * @param string $start
     * @param string $end
     * @param string $reason  Testo opzionale visualizzato in Beds24.
     * @return array|WP_Error
     */
    public static function block_dates(
        string $beds24_room_id,
        string $start,
        string $end,
        string $reason = 'Blocco manuale'
    ): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('beds24_not_configured', 'Beds24 non configurato.');
        }

        // Costruisce l'array di blocchi giorno per giorno
        $dates   = [];
        $current = new DateTime($start);
        $endDt   = new DateTime($end);

        while ($current < $endDt) {
            $dates[] = [
                'roomId'    => $beds24_room_id,
                'date'      => $current->format('Y-m-d'),
                'units'     => 0,       // 0 = camera non disponibile
                'minStay'   => 1,
                'closeToArrival'   => false,
                'closeToDeparture' => false,
                'note'      => substr($reason, 0, 100),
            ];
            $current->modify('+1 day');
        }

        if (empty($dates)) {
            return new WP_Error('beds24_invalid_range', 'Intervallo date non valido.');
        }

        return self::request('POST', 'inventory/rooms', $dates);
    }

    /**
     * Sblocca date su Beds24 (ripristina disponibilità a 1 unità).
     *
     * @param string $beds24_room_id
     * @param string $start
     * @param string $end
     * @return array|WP_Error
     */
    public static function unblock_dates(string $beds24_room_id, string $start, string $end): array|WP_Error {
        if (!self::is_configured()) {
            return new WP_Error('beds24_not_configured', 'Beds24 non configurato.');
        }

        $dates   = [];
        $current = new DateTime($start);
        $endDt   = new DateTime($end);

        while ($current < $endDt) {
            $dates[] = [
                'roomId'  => $beds24_room_id,
                'date'    => $current->format('Y-m-d'),
                'units'   => 1,
            ];
            $current->modify('+1 day');
        }

        if (empty($dates)) {
            return new WP_Error('beds24_invalid_range', 'Intervallo date non valido.');
        }

        return self::request('POST', 'inventory/rooms', $dates);
    }

    // ── Import webhook (chiamato da ALM_API::beds24_webhook) ────────────────

    /**
     * Importa / aggiorna una singola prenotazione Beds24 nel DB locale.
     * Chiamato sia dal sync_all() che dal webhook in ingresso.
     *
     * @param array $b24  Dati prenotazione Beds24 normalizzati.
     * @return int|WP_Error  ID booking locale, o errore.
     */
    public static function import_beds24_booking(array $b24): int|WP_Error {
        global $wpdb;

        $table      = $wpdb->prefix . 'alm_bookings';
        $beds24_id  = (int) ($b24['id'] ?? 0);
        $channel    = self::map_channel($b24['referer'] ?? '');
        $status_raw = (string) ($b24['status'] ?? '1');

        if (!$beds24_id) {
            return new WP_Error('beds24_invalid_data', 'ID prenotazione Beds24 mancante.');
        }

        // Trova la camera locale tramite beds24_id
        $room = ALM_Room::get_by_room_id((string) ($b24['roomId'] ?? ''));
        if (!$room) {
            // Prova a trovare tramite meta _room_beds24_id
            $room = self::find_room_by_beds24_id((int) ($b24['roomId'] ?? 0));
        }
        $room_post_id = $room ? $room->id : 0;

        // Cerca record esistente
        $existing_id = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE external_id = %s LIMIT 1",
                (string) $beds24_id
            )
        );

        $checkin_date  = sanitize_text_field($b24['arrival']   ?? '');
        $checkout_date = sanitize_text_field($b24['departure'] ?? '');
        $nights        = ALM_Availability::get_nights($checkin_date, $checkout_date);

        $first = sanitize_text_field($b24['firstName'] ?? '');
        $last  = sanitize_text_field($b24['lastName']  ?? '');
        $guest_name = trim($first . ' ' . $last) ?: 'Ospite';

        $data = [
            'room_id'          => $room_post_id,
            'external_id'      => (string) $beds24_id,
            'channel'          => $channel,
            'status'           => self::map_status_from_beds24($status_raw),
            'checkin_date'     => $checkin_date,
            'checkout_date'    => $checkout_date,
            'nights'           => max(1, $nights),
            'guest_adults'     => max(1, (int) ($b24['numAdult'] ?? 1)),
            'guest_children'   => max(0, (int) ($b24['numChild'] ?? 0)),
            'guest_name'       => $guest_name,
            'guest_email'      => sanitize_email($b24['email'] ?? ''),
            'guest_phone'      => sanitize_text_field($b24['phone'] ?? ''),
            'total_price'      => (float) ($b24['price'] ?? 0),
            'special_requests' => sanitize_textarea_field($b24['notes'] ?? ''),
            'updated_at'       => current_time('mysql', true),
        ];

        if ($existing_id) {
            $wpdb->update($table, $data, ['id' => $existing_id]); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            self::log_sync('import', 'success', 'Beds24 #' . $beds24_id . ' → aggiornata prenotazione locale #' . $existing_id);
            return $existing_id;
        }

        // Nuova prenotazione
        $data['booking_ref'] = ALM_Booking::generate_ref_static();
        $data['created_at']  = current_time('mysql', true);

        $wpdb->insert($table, $data); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $new_id = (int) $wpdb->insert_id;

        self::log_sync('import', 'success', 'Beds24 #' . $beds24_id . ' → nuova prenotazione locale #' . $new_id . ' (' . $data['booking_ref'] . ')');

        return $new_id ?: new WP_Error('beds24_import_failed', 'Errore nell\'inserimento della prenotazione.');
    }

    // ── Utility ─────────────────────────────────────────────────────────────

    /**
     * Mappa il referer Beds24 al channel locale.
     */
    private static function map_channel(string $referer): string {
        $referer_lower = strtolower($referer);
        if (str_contains($referer_lower, 'booking')) return 'booking';
        if (str_contains($referer_lower, 'airbnb'))  return 'airbnb';
        return 'direct';
    }

    /**
     * Mappa lo status Beds24 (numerico stringa) allo status locale.
     *
     * Beds24 status codes: 0=inquiry, 1=confirmed, 2=tentative, 3=no-show, 4=cancelled, 5=past
     */
    private static function map_status_from_beds24(string $status): string {
        return match ($status) {
            '1', '5'  => 'confirmed',
            '4'       => 'cancelled',
            default   => 'pending',
        };
    }

    /**
     * Mappa lo status locale al codice Beds24.
     */
    private static function map_status_to_beds24(string $status): string {
        return match ($status) {
            'confirmed' => '1',
            'cancelled' => '4',
            default     => '2',
        };
    }

    /**
     * Trova la camera WP tramite il meta _room_beds24_id.
     */
    private static function find_room_by_beds24_id(int $beds24_room_id): ?ALM_Room {
        if (!$beds24_room_id) return null;

        $posts = get_posts([
            'post_type'      => 'almaretna_room',
            'posts_per_page' => 1,
            'meta_key'       => '_room_beds24_id',
            'meta_value'     => (string) $beds24_room_id,
            'fields'         => 'ids',
        ]);

        if (empty($posts)) return null;

        try {
            return ALM_Room::get((int) $posts[0]);
        } catch (InvalidArgumentException) {
            return null;
        }
    }

    /**
     * Salva l'ID Beds24 sul record prenotazione locale.
     */
    private static function update_beds24_id(int $booking_id, int $beds24_id): void {
        global $wpdb;
        $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prefix . 'alm_bookings',
            ['external_id' => (string) $beds24_id, 'updated_at' => current_time('mysql', true)],
            ['id' => $booking_id],
            ['%s', '%s'],
            ['%d']
        );
    }

    /**
     * Scrive un record nella tabella alm_sync_log.
     */
    private static function log_sync(string $action, string $status, string $message): void {
        global $wpdb;
        $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prefix . 'alm_sync_log',
            [
                'action'     => substr($action,  0, 50),
                'status'     => substr($status,  0, 20),
                'message'    => substr($message, 0, 500),
                'created_at' => current_time('mysql', true),
            ],
            ['%s', '%s', '%s', '%s']
        );
    }

    // ── HTTP helper ─────────────────────────────────────────────────────────

    /**
     * Esegue una chiamata all'API Beds24 v2.
     *
     * @param string $method    'GET' | 'POST' | 'PUT' | 'DELETE'
     * @param string $endpoint  Percorso relativo (es. 'bookings').
     * @param array  $body      Payload JSON per POST/PUT.
     * @param array  $query     Parametri query-string per GET.
     * @return array|WP_Error
     */
    private static function request(
        string $method,
        string $endpoint,
        array  $body  = [],
        array  $query = []
    ): array|WP_Error {
        $token = self::api_token();
        if (!$token) {
            return new WP_Error('beds24_not_configured', 'Token API Beds24 mancante.');
        }

        $url = self::API_BASE . $endpoint;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $args = [
            'method'  => $method,
            'timeout' => 30,
            'headers' => [
                'token'        => $token,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ];

        if (!empty($body) && in_array($method, ['POST', 'PUT'], true)) {
            $args['body'] = wp_json_encode($body);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code($response);
        $raw  = wp_remote_retrieve_body($response);
        $data = json_decode($raw, true);

        if (!is_array($data)) {
            return new WP_Error(
                'beds24_invalid_response',
                __('Risposta Beds24 non valida.', 'almaretna-booking')
            );
        }

        if ($code >= 400) {
            $msg = $data['error'] ?? $data['message'] ?? __('Errore API Beds24.', 'almaretna-booking');
            return new WP_Error('beds24_api_error', is_string($msg) ? $msg : wp_json_encode($msg));
        }

        return $data;
    }
}
