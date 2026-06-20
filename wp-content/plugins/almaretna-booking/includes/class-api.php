<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_API
 *
 * Registra tutti gli endpoint REST nel namespace scv/v1.
 *
 * Endpoint pubblici (con rate limiting):
 *   GET  /availability
 *   GET  /rooms
 *   GET  /price
 *   POST /booking/create
 *   POST /booking/confirm
 *   GET  /booking/status
 *   POST /stripe/webhook
 *   POST /beds24/webhook
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Controller REST API.
 */
class ALM_API {

    /** @var string Namespace REST. */
    private const NAMESPACE = 'scv/v1';

    /** @var int Max richieste GET per minuto per IP. */
    private const RATE_LIMIT_GET  = 20;

    /** @var int Max richieste POST /booking/create per minuto per IP. */
    private const RATE_LIMIT_POST = 5;

    /**
     * Registra tutte le route REST.
     */
    public function register_routes(): void {
        $ns = self::NAMESPACE;

        // GET /availability
        register_rest_route($ns, '/availability', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_availability'],
            'permission_callback' => [$this, 'check_rate_limit_get'],
            'args'                => [
                'room_id'    => ['required' => true,  'sanitize_callback' => 'absint'],
                'month_from' => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'month_to'   => ['required' => false, 'sanitize_callback' => 'sanitize_text_field'],
            ],
        ]);

        // GET /rooms
        register_rest_route($ns, '/rooms', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_rooms'],
            'permission_callback' => [$this, 'check_rate_limit_get'],
            'args'                => [
                'checkin'  => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'checkout' => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'adults'   => ['required' => true,  'sanitize_callback' => 'absint'],
                'children' => ['required' => false, 'sanitize_callback' => 'absint', 'default' => 0],
            ],
        ]);

        // GET /price
        register_rest_route($ns, '/price', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_price'],
            'permission_callback' => [$this, 'check_rate_limit_get'],
            'args'                => [
                'room_id'  => ['required' => true,  'sanitize_callback' => 'absint'],
                'checkin'  => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'checkout' => ['required' => true,  'sanitize_callback' => 'sanitize_text_field'],
                'channel'  => ['required' => false, 'sanitize_callback' => 'sanitize_text_field', 'default' => 'direct'],
            ],
        ]);

        // POST /booking/create
        register_rest_route($ns, '/booking/create', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'create_booking'],
            'permission_callback' => [$this, 'check_rate_limit_post'],
        ]);

        // POST /booking/confirm
        register_rest_route($ns, '/booking/confirm', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'confirm_booking'],
            'permission_callback' => '__return_true',
        ]);

        // GET /booking/status
        register_rest_route($ns, '/booking/status', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_booking_status'],
            'permission_callback' => '__return_true',
            'args'                => [
                'ref' => ['required' => true, 'sanitize_callback' => 'sanitize_text_field'],
            ],
        ]);

        // POST /stripe/webhook
        register_rest_route($ns, '/stripe/webhook', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'stripe_webhook'],
            'permission_callback' => '__return_true',
        ]);

        // POST /beds24/webhook
        register_rest_route($ns, '/beds24/webhook', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'beds24_webhook'],
            'permission_callback' => '__return_true',
        ]);

        // POST /guest-contact (utente loggato — area personale)
        register_rest_route($ns, '/guest-contact', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'guest_contact'],
            'permission_callback' => 'is_user_logged_in',
        ]);
    }

    // ─── Handlers ────────────────────────────────────────────────────────────

    /**
     * GET /availability
     * Restituisce le date bloccate per una camera in un range di mesi.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_availability(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $room_id    = (int) $request->get_param('room_id');
        $month_from = (string) $request->get_param('month_from');
        $month_to   = $request->get_param('month_to') ?: $month_from;

        if (!$room_id || !get_post($room_id)) {
            return new WP_Error('not_found', __('Camera non trovata.', 'almaretna-booking'), ['status' => 404]);
        }

        $blocked_dates = ALM_Availability::get_blocked_dates($room_id, $month_from, (string) $month_to);

        return rest_ensure_response([
            'room_id'       => $room_id,
            'month_from'    => $month_from,
            'month_to'      => $month_to,
            'blocked_dates' => $blocked_dates,
        ]);
    }

    /**
     * GET /rooms
     * Restituisce le camere disponibili con prezzi per le date richieste.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_rooms(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $checkin  = (string) $request->get_param('checkin');
        $checkout = (string) $request->get_param('checkout');
        $adults   = (int)   $request->get_param('adults');
        $children = (int)   $request->get_param('children');

        if (!$this->is_valid_date($checkin) || !$this->is_valid_date($checkout)) {
            return new WP_Error('invalid_dates', __('Date non valide.', 'almaretna-booking'), ['status' => 400]);
        }
        if ($checkin >= $checkout) {
            return new WP_Error('invalid_dates', __('Il checkout deve essere successivo al checkin.', 'almaretna-booking'), ['status' => 400]);
        }
        if ($adults < 1 || $adults > 12) {
            return new WP_Error('invalid_guests', __('Numero ospiti non valido.', 'almaretna-booking'), ['status' => 400]);
        }

        $available_rooms = ALM_Availability::get_available_rooms($checkin, $checkout, $adults, $children);
        $nights          = ALM_Availability::get_nights($checkin, $checkout);
        $mode            = ALM_Availability::get_display_mode($adults, $children);
        $result          = [];

        foreach ($available_rooms as $room) {
            $pricing = ALM_Pricing::get_price_for_dates($room->id, $checkin, $checkout);
            if (is_wp_error($pricing)) {
                continue;
            }

            $room_data = $room->to_array();

            // CARMINA (room_id termina in -D) = quadrupla con proprio prezzo
            $is_carmina = $room->is_family_unit && str_ends_with($room->room_id, '-D');

            $result[] = array_merge($room_data, [
                'is_suite'        => $is_carmina,
                'suite_note'      => $is_carmina ? 'Camera matrimoniale + camera con 2 letti singoli, bagno in comune' : '',
                'sharing_warning' => false,
                'sharing_note'    => '',
                'price_per_night' => $pricing['price_per_night'],
                'total_price'     => $pricing['subtotal'],
                'nights'          => $nights,
                'applied_rate'    => $pricing['applied_rate'] ? [
                    'name'  => $pricing['applied_rate']['rate_name'] ?? '',
                    'price' => $pricing['applied_rate']['price_per_night'],
                ] : null,
            ]);
        }

        return rest_ensure_response([
            'checkin'  => $checkin,
            'checkout' => $checkout,
            'nights'   => $nights,
            'adults'   => $adults,
            'children' => $children,
            'rooms'    => $result,
            'count'    => count($result),
        ]);
    }

    /**
     * GET /price
     * Restituisce il breakdown prezzi per una camera e un periodo.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_price(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $room_id  = (int)    $request->get_param('room_id');
        $checkin  = (string) $request->get_param('checkin');
        $checkout = (string) $request->get_param('checkout');
        $channel  = (string) $request->get_param('channel');

        $pricing = ALM_Pricing::get_price_for_dates($room_id, $checkin, $checkout, $channel);
        if (is_wp_error($pricing)) {
            return new WP_Error(
                $pricing->get_error_code(),
                $pricing->get_error_message(),
                ['status' => 400]
            );
        }

        return rest_ensure_response($pricing);
    }

    /**
     * POST /booking/create
     * Crea una prenotazione pending e restituisce il PaymentIntent Stripe.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function create_booking(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $body = $request->get_json_params() ?: $request->get_params();

        // Verifica nonce WordPress
        $nonce = $request->get_header('X-WP-Nonce') ?: ($body['nonce'] ?? '');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_Error('invalid_nonce', __('Richiesta non autorizzata.', 'almaretna-booking'), ['status' => 403]);
        }

        // Sanitizza input
        $first_name = sanitize_text_field($body['guest_first_name'] ?? $body['first_name'] ?? '');
        $last_name  = sanitize_text_field($body['guest_last_name']  ?? $body['last_name']  ?? '');
        $data = [
            'room_id'          => absint($body['room_id'] ?? 0),
            'checkin_date'     => sanitize_text_field($body['checkin'] ?? ''),
            'checkout_date'    => sanitize_text_field($body['checkout'] ?? ''),
            'guest_adults'     => absint($body['adults'] ?? 1),
            'guest_children'   => absint($body['children'] ?? 0),
            'guest_name'       => trim($first_name . ' ' . $last_name),
            'guest_email'      => sanitize_email($body['guest_email'] ?? $body['email'] ?? ''),
            'guest_phone'      => sanitize_text_field($body['guest_phone'] ?? $body['phone'] ?? ''),
            'special_requests' => sanitize_textarea_field($body['special_requests'] ?? ''),
            'channel'          => 'direct',
        ];

        // Crea prenotazione
        $booking = ALM_Booking::create($data);
        if (is_wp_error($booking)) {
            return new WP_Error(
                $booking->get_error_code(),
                $booking->get_error_message(),
                ['status' => 422]
            );
        }

        // Gestisci extras — accetta [{extra_id, qty}] oppure [id1, id2]
        $extras_raw = isset($body['extras']) && is_array($body['extras']) ? $body['extras'] : [];
        $extras_map = []; // extra_id => qty
        foreach ($extras_raw as $item) {
            if (is_array($item) && isset($item['extra_id'])) {
                $qty = max(0, absint($item['qty'] ?? $item['quantity'] ?? 1));
                if ($qty > 0) {
                    $extras_map[absint($item['extra_id'])] = $qty;
                }
            } elseif (is_numeric($item)) {
                $extras_map[absint($item)] = 1;
            }
        }
        // Legacy: extra_ids flat array
        if (empty($extras_map) && isset($body['extra_ids']) && is_array($body['extra_ids'])) {
            foreach ($body['extra_ids'] as $eid) {
                $extras_map[absint($eid)] = 1;
            }
        }

        $extras_total = 0.0;
        if (!empty($extras_map)) {
            $extra_ids    = array_keys($extras_map);
            $extras_data  = ALM_Pricing::calculate_extras($extra_ids, $booking->nights, $booking->guest_adults + $booking->guest_children);
            $extras_total = $extras_data['total'];
            $booking->extras_total = $extras_total;

            // Salva extras nel pivot
            global $wpdb;
            foreach ($extras_data['items'] as $item) {
                $qty = $extras_map[$item['id']] ?? $item['quantity'] ?? 1;
                $wpdb->insert(
                    $wpdb->prefix . 'alm_booking_extras',
                    [
                        'booking_id' => $booking->id,
                        'extra_id'   => $item['id'],
                        'quantity'   => $qty,
                        'price'      => $item['line_total'],
                    ],
                    ['%d', '%d', '%d', '%f']
                );
            }

            // Aggiorna extras_total nel DB
            $wpdb->update(
                $wpdb->prefix . 'alm_bookings',
                ['extras_total' => $extras_total],
                ['id' => $booking->id],
                ['%f'],
                ['%d']
            );
        }

        $total_cents = $booking->get_total_cents();

        // Crea PaymentIntent Stripe
        $client_secret = null;
        if ($total_cents > 0) {
            try {
                $intent = ALM_Stripe::create_payment_intent(
                    $total_cents,
                    'eur',
                    [
                        'booking_ref' => $booking->booking_ref,
                        'room_id'     => (string) $booking->room_id,
                        'guest_email' => $booking->guest_email,
                        'description' => sprintf(
                            'Almaretna — %s — %d notti (%s / %s)',
                            get_the_title($booking->room_id),
                            $booking->nights,
                            $booking->checkin_date,
                            $booking->checkout_date
                        ),
                    ]
                );

                if (!is_wp_error($intent) && !empty($intent['client_secret'])) {
                    $client_secret = $intent['client_secret'];
                    // Salva payment_intent_id nella prenotazione
                    global $wpdb;
                    $wpdb->update(
                        $wpdb->prefix . 'alm_bookings',
                        ['payment_ref' => $intent['id']],
                        ['id' => $booking->id],
                        ['%s'],
                        ['%d']
                    );
                }
            } catch (Exception $e) {
                // Non blocchiamo: la prenotazione esiste, Stripe può essere ritentato
                error_log('[Almaretna] Stripe PaymentIntent error: ' . $e->getMessage());
            }
        }

        return rest_ensure_response([
            'success'               => true,
            'booking_ref'           => $booking->booking_ref,
            'booking_id'            => $booking->id,
            'total'                 => $booking->calculate_total(),
            'total_formatted'       => '€ ' . number_format($booking->calculate_total(), 2, ',', '.'),
            'payment_intent_client_secret' => $client_secret,
        ]);
    }

    /**
     * POST /booking/confirm
     * Conferma manuale (alternativa a webhook, usata in test).
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function confirm_booking(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $body        = $request->get_json_params() ?: $request->get_params();
        $booking_ref = sanitize_text_field($body['booking_ref'] ?? $body['ref'] ?? '');
        $pi_id       = sanitize_text_field($body['payment_intent_id'] ?? '');

        if (!$booking_ref) {
            return new WP_Error('missing_ref', __('Riferimento prenotazione mancante.', 'almaretna-booking'), ['status' => 400]);
        }

        $booking = ALM_Booking::get_by_ref($booking_ref);
        if (!$booking) {
            return new WP_Error('not_found', __('Prenotazione non trovata.', 'almaretna-booking'), ['status' => 404]);
        }

        if ($booking->status === 'confirmed') {
            return rest_ensure_response([
                'success'      => true,
                'booking_ref'  => $booking->booking_ref,
                'status'       => $booking->status,
                'already_done' => true,
            ]);
        }

        // Verifica pagamento Stripe prima di confermare.
        // Usa il pi_id dalla richiesta oppure quello già salvato nella prenotazione.
        $pi_id_to_verify = $pi_id ?: ($booking->payment_ref ?: '');

        if (!$pi_id_to_verify) {
            return new WP_Error(
                'payment_required',
                __('ID pagamento richiesto per la conferma.', 'almaretna-booking'),
                ['status' => 400]
            );
        }

        if (!ALM_Stripe::is_configured()) {
            return new WP_Error(
                'payment_system_unavailable',
                __('Il sistema di pagamento non è configurato. Impossibile verificare il pagamento.', 'almaretna-booking'),
                ['status' => 503]
            );
        }

        $intent = ALM_Stripe::get_payment_intent($pi_id_to_verify);

        if (is_wp_error($intent)) {
            return new WP_Error(
                'payment_verify_failed',
                __('Impossibile verificare il pagamento con Stripe.', 'almaretna-booking'),
                ['status' => 422]
            );
        }

        if (($intent['status'] ?? '') !== 'succeeded') {
            return new WP_Error(
                'payment_not_succeeded',
                __('Il pagamento non risulta completato.', 'almaretna-booking'),
                ['status' => 422]
            );
        }

        // Verifica che il PaymentIntent appartenga a questa prenotazione.
        $ref_in_meta = $intent['metadata']['booking_ref'] ?? '';
        if ($ref_in_meta && $ref_in_meta !== $booking_ref) {
            return new WP_Error(
                'payment_mismatch',
                __('Il pagamento non corrisponde alla prenotazione indicata.', 'almaretna-booking'),
                ['status' => 403]
            );
        }

        $confirmed = $booking->confirm($pi_id_to_verify ?: null);
        if (!$confirmed) {
            return new WP_Error('confirm_failed', __('Impossibile confermare la prenotazione.', 'almaretna-booking'), ['status' => 500]);
        }

        // Crea o collega account WP guest
        self::link_or_create_guest_user($booking);

        // Notifiche
        ALM_Notifications::send_confirmation_guest($booking);
        ALM_Notifications::send_confirmation_host($booking);
        ALM_Notifications::send_payment_receipt($booking);
        ALM_Notifications::schedule_reminder($booking);
        ALM_Notifications::maybe_send_whatsapp_directions($booking);

        return rest_ensure_response([
            'success'      => true,
            'booking_ref'  => $booking->booking_ref,
            'checkin'      => $booking->checkin_date,
            'checkout'     => $booking->checkout_date,
            'status'       => 'confirmed',
        ]);
    }

    /**
     * GET /booking/status
     * Restituisce lo stato di una prenotazione.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function get_booking_status(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $ref     = sanitize_text_field((string) $request->get_param('ref'));
        $booking = ALM_Booking::get_by_ref($ref);

        if (!$booking) {
            return new WP_Error('not_found', __('Prenotazione non trovata.', 'almaretna-booking'), ['status' => 404]);
        }

        return rest_ensure_response([
            'booking_ref'    => $booking->booking_ref,
            'status'         => $booking->status,
            'payment_status' => $booking->payment_status,
            'checkin'        => $booking->checkin_date,
            'checkout'       => $booking->checkout_date,
            'nights'         => $booking->nights,
            'room_title'     => get_the_title($booking->room_id),
            'guest_name'     => $booking->guest_name,
            'total'          => $booking->calculate_total(),
        ]);
    }

    /**
     * POST /stripe/webhook
     * Gestisce gli eventi Stripe: payment_intent.succeeded e payment_intent.payment_failed.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function stripe_webhook(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $payload    = $request->get_body();
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        if (empty($payload) || empty($sig_header)) {
            return new WP_Error('invalid_payload', 'Missing payload or signature.', ['status' => 400]);
        }

        if (!ALM_Stripe::verify_webhook_signature($payload, $sig_header)) {
            return new WP_Error('invalid_signature', 'Firma webhook Stripe non valida.', ['status' => 400]);
        }

        $event = json_decode($payload);
        if (!$event) {
            return new WP_Error('invalid_payload', 'Payload JSON non valido.', ['status' => 400]);
        }

        $event_type = $event->type ?? '';
        $object     = $event->data->object ?? null;

        switch ($event_type) {

            case 'payment_intent.succeeded':
                if (!$object) break;

                $booking_ref = $object->metadata->booking_ref ?? '';
                if (!$booking_ref) break;

                $booking = ALM_Booking::get_by_ref($booking_ref);
                if (!$booking || $booking->status === 'confirmed') break;

                $booking->confirm($object->id);

                // Crea o collega account WP guest
                self::link_or_create_guest_user($booking);

                // Sincronizza con Beds24
                if (ALM_Beds24::is_configured()) {
                    try {
                        ALM_Beds24::push_booking((int) $booking->id);
                    } catch (Exception $e) {
                        error_log('[Almaretna] Beds24 push booking error: ' . $e->getMessage());
                    }
                }

                // Notifiche
                ALM_Notifications::send_confirmation_guest($booking);
                ALM_Notifications::send_confirmation_host($booking);
                ALM_Notifications::send_payment_receipt($booking);
                ALM_Notifications::schedule_reminder($booking);
                ALM_Notifications::maybe_send_whatsapp_directions($booking);
                break;

            case 'payment_intent.payment_failed':
                if (!$object) break;

                $booking_ref = $object->metadata->booking_ref ?? '';
                if (!$booking_ref) break;

                $booking = ALM_Booking::get_by_ref($booking_ref);
                if ($booking && $booking->status === 'pending') {
                    $booking->cancel('Pagamento Stripe fallito.');
                }
                break;

            case 'charge.refunded':
                if (!$object) break;
                // Aggiorna payment_status a refunded se presente booking
                $pi_id = $object->payment_intent ?? '';
                if ($pi_id) {
                    global $wpdb;
                    $wpdb->update(
                        $wpdb->prefix . 'alm_bookings',
                        ['payment_status' => 'refunded'],
                        ['payment_ref' => $pi_id],
                        ['%s'],
                        ['%s']
                    );
                }
                break;
        }

        return rest_ensure_response(['received' => true]);
    }

    /**
     * POST /beds24/webhook
     * Gestisce prenotazioni arrivate da Booking.com / Airbnb via Beds24.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function beds24_webhook(WP_REST_Request $request): WP_REST_Response|WP_Error {
        // Verifica token Beds24
        $token = $request->get_header('X-Beds24-Token')
               ?: sanitize_text_field($request->get_param('token') ?? '');

        $expected = defined('ALM_BEDS24_WEBHOOK_TOKEN') ? ALM_BEDS24_WEBHOOK_TOKEN : get_option('alm_beds24_webhook_token', '');
        if (!$expected || !hash_equals($expected, $token)) {
            return new WP_Error('unauthorized', 'Token non valido.', ['status' => 403]);
        }

        $body = $request->get_json_params() ?: [];
        if (empty($body)) {
            return rest_ensure_response(['received' => true]);
        }

        $channel_map = [
            1 => 'booking',
            2 => 'airbnb',
        ];

        $channel_id  = (int) ($body['channelId'] ?? 0);
        $channel     = $channel_map[$channel_id] ?? 'manual';
        $external_id = (string) ($body['bookingId'] ?? '');
        $beds24_room = (string) ($body['roomId']    ?? '');

        // Trova camera WordPress dal beds24_id
        $room_posts = get_posts([
            'post_type'      => 'almaretna_room',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => [[
                'key'     => '_room_beds24_id',
                'value'   => $beds24_room,
                'compare' => '=',
            ]],
        ]);

        if (empty($room_posts)) {
            // Log e rispondi OK per non bloccare Beds24
            error_log("[Almaretna] Beds24 webhook: camera non trovata per beds24_id={$beds24_room}");
            return rest_ensure_response(['received' => true, 'mapped' => false]);
        }

        $room_post_id = $room_posts[0]->ID;

        // Controlla se prenotazione esiste già per external_id
        global $wpdb;
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}alm_bookings WHERE external_id = %s LIMIT 1",
            $external_id
        ));

        $guest_name = trim(
            sanitize_text_field($body['guestFirstName'] ?? '') . ' ' .
            sanitize_text_field($body['guestLastName'] ?? '')
        );
        $checkin     = sanitize_text_field($body['firstNight'] ?? '');
        $checkout    = sanitize_text_field($body['lastNight']  ?? '');
        $beds24_status = (int) ($body['status'] ?? 1);

        if ($existing) {
            // Aggiorna status se cambiato
            $new_status = $beds24_status === 0 ? 'cancelled' : 'confirmed';
            $wpdb->update(
                $wpdb->prefix . 'alm_bookings',
                ['status' => $new_status],
                ['id' => (int) $existing],
                ['%s'],
                ['%d']
            );

            // Notifica cancellazione host
            if ($new_status === 'cancelled') {
                $booking = ALM_Booking::get((int) $existing);
                if ($booking) {
                    ALM_Notifications::send_cancellation_host($booking);
                }
            }

            return rest_ensure_response(['received' => true, 'action' => 'updated']);
        }

        // Crea nuova prenotazione
        $nights = ALM_Availability::get_nights($checkin, $checkout);
        $total  = (float) ($body['totalPrice'] ?? 0.0);

        $data = [
            'booking_ref'     => ALM_Booking::generate_ref_static(),
            'room_id'         => $room_post_id,
            'channel'         => $channel,
            'external_id'     => $external_id,
            'guest_name'      => $guest_name ?: __('Ospite', 'almaretna-booking'),
            'guest_email'     => sanitize_email($body['guestEmail'] ?? ''),
            'guest_phone'     => sanitize_text_field($body['guestPhone'] ?? ''),
            'guest_adults'    => absint($body['adults'] ?? 1),
            'guest_children'  => absint($body['children'] ?? 0),
            'checkin_date'    => $checkin,
            'checkout_date'   => $checkout,
            'nights'          => $nights,
            'price_per_night' => $nights > 0 ? round($total / $nights, 2) : $total,
            'total_price'     => $total,
            'extras_total'    => 0.00,
            'status'          => $beds24_status === 0 ? 'cancelled' : 'confirmed',
            'payment_status'  => $total > 0 ? 'paid' : 'unpaid',
        ];

        $wpdb->insert($wpdb->prefix . 'alm_bookings', $data,
            ['%s','%d','%s','%s','%s','%s','%s','%d','%d','%s','%s','%d','%f','%f','%f','%s','%s']
        );

        $new_id = $wpdb->insert_id;
        if ($new_id && $data['status'] === 'confirmed') {
            $booking = ALM_Booking::get($new_id);
            if ($booking) {
                ALM_Notifications::send_confirmation_host($booking);
            }
        }

        return rest_ensure_response(['received' => true, 'action' => 'created']);
    }

    // ─── Guest user management ───────────────────────────────────────────────

    /**
     * Crea o collega un utente WP all'ospite che ha prenotato.
     * Invia email di benvenuto solo se l'utente è appena stato creato.
     *
     * @param ALM_Booking $booking
     * @return int User ID (0 in caso di errore)
     */
    private static function link_or_create_guest_user(ALM_Booking $booking): int {
        global $wpdb;

        if (empty($booking->guest_email)) return 0;

        $is_new_user = false;
        $set_password_url = '';

        $user = get_user_by('email', $booking->guest_email);

        if (!$user) {
            // Genera username unico da nome
            $base_login = sanitize_user(
                strtolower(preg_replace('/\s+/', '.', trim($booking->guest_name))),
                true
            );
            $base_login = $base_login ?: 'ospite';
            $user_login = $base_login;
            $suffix     = 1;
            while (username_exists($user_login)) {
                $user_login = $base_login . $suffix;
                $suffix++;
            }

            // Separa nome e cognome
            $name_parts = explode(' ', trim($booking->guest_name), 2);
            $first_name = $name_parts[0] ?? '';
            $last_name  = $name_parts[1] ?? '';

            $user_id = wp_insert_user([
                'user_login'  => $user_login,
                'user_email'  => $booking->guest_email,
                'display_name'=> $booking->guest_name,
                'first_name'  => $first_name,
                'last_name'   => $last_name,
                'role'        => 'subscriber',
                'user_pass'   => wp_generate_password(16),
            ]);

            if (is_wp_error($user_id)) {
                error_log('[Almaretna] link_or_create_guest_user error: ' . $user_id->get_error_message());
                return 0;
            }

            // Impedisce la notifica email di default di WP
            update_user_meta($user_id, 'alm_suppress_wp_welcome', '1');

            $user       = get_userdata($user_id);
            $is_new_user = true;

        } else {
            $user_id    = $user->ID;
            $first_name = $user->first_name ?: (explode(' ', $user->display_name, 2)[0] ?? '');
        }

        // Aggiorna meta nome/cognome se mancanti
        $name_parts = explode(' ', trim($booking->guest_name), 2);
        if (empty(get_user_meta($user_id, 'first_name', true))) {
            update_user_meta($user_id, 'first_name', $name_parts[0] ?? '');
        }
        if (empty(get_user_meta($user_id, 'last_name', true))) {
            update_user_meta($user_id, 'last_name', $name_parts[1] ?? '');
        }

        // Associa user_id alla prenotazione
        $wpdb->update(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prefix . 'alm_bookings',
            ['user_id' => $user_id],
            ['id' => $booking->id],
            ['%d'],
            ['%d']
        );

        // Invia email di benvenuto solo ai nuovi utenti
        if ($is_new_user && $user) {
            $key = get_password_reset_key($user);
            if (!is_wp_error($key)) {
                $set_password_url = wp_nonce_url(
                    network_home_url(
                        "wp-login.php?action=rp&key={$key}&login=" . rawurlencode($user->user_login)
                    ),
                    'reset-password'
                );
            }

            $dashboard_page = get_page_by_path('le-mie-prenotazioni');
            $dashboard_url  = $dashboard_page
                ? (string) get_permalink($dashboard_page)
                : home_url('/le-mie-prenotazioni/');

            $fn = get_user_meta($user_id, 'first_name', true) ?: (explode(' ', $booking->guest_name, 2)[0] ?? $booking->guest_name);

            ALM_Notifications::send_account_welcome(
                $user_id,
                $booking->guest_email,
                $fn,
                $booking->booking_ref,
                $dashboard_url,
                $set_password_url
            );
        }

        return $user_id;
    }

    /**
     * POST /guest-contact
     * Invia un messaggio alla struttura dall'area personale ospite.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public function guest_contact(WP_REST_Request $request): WP_REST_Response|WP_Error {
        $subject = trim((string) $request->get_param('subject'));
        $message = trim((string) $request->get_param('message'));

        if ($subject === '' || $message === '') {
            return new WP_Error('missing_fields', 'Compila tutti i campi.', ['status' => 400]);
        }

        $user       = wp_get_current_user();
        $from_name  = $user->display_name ?: trim($user->first_name . ' ' . $user->last_name);
        $from_email = $user->user_email;
        $to         = (string) get_option('admin_email');

        $body = sprintf(
            "Messaggio dall'area personale\n\nDa: %s (%s)\n\nOggetto: %s\n\n%s",
            $from_name,
            $from_email,
            $subject,
            $message
        );

        $sent = wp_mail(
            $to,
            '[Almaretna] ' . $subject,
            $body,
            ['Reply-To: ' . $from_name . ' <' . $from_email . '>']
        );

        if (!$sent) {
            return new WP_Error('mail_failed', 'Errore invio. Riprova più tardi.', ['status' => 500]);
        }

        return rest_ensure_response(['success' => true]);
    }

    // ─── Rate limiting ────────────────────────────────────────────────────────

    /**
     * Rate limiting per richieste GET (20/min per IP).
     *
     * @return bool|WP_Error
     */
    public function check_rate_limit_get(): bool|WP_Error {
        return $this->check_rate_limit('get', self::RATE_LIMIT_GET);
    }

    /**
     * Rate limiting per POST /booking/create (5/min per IP).
     *
     * @return bool|WP_Error
     */
    public function check_rate_limit_post(): bool|WP_Error {
        return $this->check_rate_limit('post', self::RATE_LIMIT_POST);
    }

    /**
     * Implementa rate limiting via WordPress transients.
     *
     * @param string $type    Chiave tipo (get|post).
     * @param int    $limit   Max richieste al minuto.
     * @return bool|WP_Error
     */
    private function check_rate_limit(string $type, int $limit): bool|WP_Error {
        $ip  = $this->get_client_ip();
        $key = 'alm_rl_' . $type . '_' . md5($ip);

        $count = (int) get_transient($key);

        if ($count >= $limit) {
            return new WP_Error(
                'rate_limit_exceeded',
                __('Troppe richieste. Riprova tra un minuto.', 'almaretna-booking'),
                ['status' => 429]
            );
        }

        if ($count === 0) {
            set_transient($key, 1, 60);
        } else {
            set_transient($key, $count + 1, 60);
        }

        return true;
    }

    /**
     * Restituisce l'IP del client in modo sicuro.
     *
     * @return string
     */
    private function get_client_ip(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = explode(',', $_SERVER[$header])[0];
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    /**
     * Valida una stringa data Y-m-d.
     *
     * @param string $date
     * @return bool
     */
    private function is_valid_date(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
