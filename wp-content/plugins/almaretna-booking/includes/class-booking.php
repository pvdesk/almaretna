<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Booking
 *
 * Rappresenta una prenotazione con tutta la logica di creazione,
 * conferma, cancellazione e calcolo totale.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Modello prenotazione.
 */
class ALM_Booking {

    /** @var int|null ID record nel DB. */
    public ?int $id = null;

    /** @var string Riferimento prenotazione (es. ALM-2024-0001). */
    public string $booking_ref = '';

    /** @var int ID post della camera. */
    public int $room_id;

    /** @var string Canale: direct | booking | airbnb | manual. */
    public string $channel = 'direct';

    /** @var string|null ID esterno (Beds24, Booking.com, ecc.). */
    public ?string $external_id = null;

    /** @var string Nome completo ospite. */
    public string $guest_name;

    /** @var string Email ospite. */
    public string $guest_email;

    /** @var string|null Telefono ospite. */
    public ?string $guest_phone = null;

    /** @var int Numero adulti. */
    public int $guest_adults = 1;

    /** @var int Numero bambini. */
    public int $guest_children = 0;

    /** @var string Data check-in (Y-m-d). */
    public string $checkin_date;

    /** @var string Data check-out (Y-m-d). */
    public string $checkout_date;

    /** @var int Numero notti. */
    public int $nights;

    /** @var float Prezzo per notte applicato. */
    public float $price_per_night;

    /** @var float Totale soggiorno (senza extras). */
    public float $total_price;

    /** @var float Totale extras. */
    public float $extras_total = 0.00;

    /** @var string Status: pending | confirmed | cancelled | completed | no_show. */
    public string $status = 'pending';

    /** @var string Status pagamento: unpaid | partial | paid | refunded. */
    public string $payment_status = 'unpaid';

    /** @var string|null Stripe PaymentIntent ID. */
    public ?string $payment_ref = null;

    /** @var string|null Richieste speciali. */
    public ?string $special_requests = null;

    /** @var string|null Note interne. */
    public ?string $notes = null;

    /** @var string|null Data creazione. */
    public ?string $created_at = null;

    /** @var string|null Data aggiornamento. */
    public ?string $updated_at = null;

    /**
     * Costruttore: accetta array di dati o booking ID.
     *
     * @param array<string, mixed>|int $data
     */
    public function __construct(array|int $data) {
        if (is_int($data)) {
            $data = self::fetch_by_id($data) ?? [];
        }
        $this->hydrate($data);
    }

    /**
     * Popola le proprietà da un array.
     *
     * @param array<string, mixed> $data
     */
    private function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    // ─── Factory methods ─────────────────────────────────────────────────────

    /**
     * Crea una nuova prenotazione.
     *
     * @param array<string, mixed> $data
     * @return static|WP_Error
     */
    public static function create(array $data): static|WP_Error {
        // Valida campi obbligatori
        $required = ['room_id', 'guest_name', 'guest_email', 'checkin_date', 'checkout_date', 'guest_adults'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return new WP_Error(
                    'missing_field',
                    sprintf(__('Campo obbligatorio mancante: %s', 'almaretna-booking'), $field)
                );
            }
        }

        // Sanitizza
        $room_id    = absint($data['room_id']);
        $checkin    = sanitize_text_field($data['checkin_date']);
        $checkout   = sanitize_text_field($data['checkout_date']);
        $adults     = absint($data['guest_adults']);
        $children   = isset($data['guest_children']) ? absint($data['guest_children']) : 0;

        // Valida date
        if (!self::is_valid_date($checkin) || !self::is_valid_date($checkout)) {
            return new WP_Error('invalid_dates', __('Date non valide.', 'almaretna-booking'));
        }

        if ($checkin >= $checkout) {
            return new WP_Error('invalid_dates', __('La data di check-out deve essere successiva al check-in.', 'almaretna-booking'));
        }

        // Carica camera
        try {
            $room = new ALM_Room($room_id);
        } catch (InvalidArgumentException) {
            return new WP_Error('room_not_found', __('Camera non trovata.', 'almaretna-booking'));
        }

        if (!$room->active) {
            return new WP_Error('room_inactive', __('La camera non è disponibile.', 'almaretna-booking'));
        }

        // Calcola notti e verifica min_stay
        $nights = ALM_Availability::get_nights($checkin, $checkout);
        if ($nights < $room->min_stay) {
            return new WP_Error(
                'min_stay',
                sprintf(
                    __('Soggiorno minimo per questa camera: %d notti.', 'almaretna-booking'),
                    $room->min_stay
                )
            );
        }

        // Verifica disponibilità
        if (!ALM_Availability::check($room_id, $checkin, $checkout)) {
            return new WP_Error('not_available', __('La camera non è disponibile per le date selezionate.', 'almaretna-booking'));
        }

        // Calcola prezzo
        $pricing = ALM_Pricing::get_price_for_dates($room_id, $checkin, $checkout);
        if (is_wp_error($pricing)) {
            return $pricing;
        }

        // Genera booking_ref univoco
        $booking_ref = self::generate_ref();

        // Prepara dati DB
        $insert_data = [
            'booking_ref'     => $booking_ref,
            'room_id'         => $room_id,
            'channel'         => isset($data['channel']) ? sanitize_text_field($data['channel']) : 'direct',
            'external_id'     => isset($data['external_id']) ? sanitize_text_field($data['external_id']) : null,
            'guest_name'      => sanitize_text_field($data['guest_name']),
            'guest_email'     => sanitize_email($data['guest_email']),
            'guest_phone'     => isset($data['guest_phone']) ? sanitize_text_field($data['guest_phone']) : null,
            'guest_adults'    => $adults,
            'guest_children'  => $children,
            'checkin_date'    => $checkin,
            'checkout_date'   => $checkout,
            'nights'          => $nights,
            'price_per_night' => $pricing['price_per_night'],
            'total_price'     => $pricing['subtotal'],
            'extras_total'    => 0.00,
            'status'          => 'pending',
            'payment_status'  => 'unpaid',
            'special_requests'=> isset($data['special_requests']) ? sanitize_textarea_field($data['special_requests']) : null,
        ];

        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'alm_bookings',
            $insert_data,
            ['%s','%d','%s','%s','%s','%s','%s','%d','%d','%s','%s','%d','%f','%f','%f','%s','%s','%s']
        );

        if ($result === false) {
            return new WP_Error('db_error', __('Errore nel salvataggio della prenotazione.', 'almaretna-booking'));
        }

        $insert_data['id'] = $wpdb->insert_id;
        return new static($insert_data);
    }

    /**
     * Recupera una prenotazione per ID.
     *
     * @param int $id
     * @return static|null
     */
    public static function get(int $id): ?static {
        $data = self::fetch_by_id($id);
        return $data ? new static($data) : null;
    }

    /**
     * Recupera una prenotazione per booking_ref.
     *
     * @param string $ref
     * @return static|null
     */
    public static function get_by_ref(string $ref): ?static {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}alm_bookings WHERE booking_ref = %s LIMIT 1",
            $ref
        ), ARRAY_A);

        return $row ? new static($row) : null;
    }

    /**
     * Restituisce tutte le prenotazioni con filtri opzionali.
     *
     * @param array<string, mixed> $filters
     * @return static[]
     */
    public static function get_all(array $filters = []): array {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]  = 'status = %s';
            $params[] = $filters['status'];
        }
        if (!empty($filters['room_id'])) {
            $where[]  = 'room_id = %d';
            $params[] = (int) $filters['room_id'];
        }
        if (!empty($filters['channel'])) {
            $where[]  = 'channel = %s';
            $params[] = $filters['channel'];
        }
        if (!empty($filters['date_from'])) {
            $where[]  = 'checkin_date >= %s';
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]  = 'checkout_date <= %s';
            $params[] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $where[]  = '(guest_name LIKE %s OR guest_email LIKE %s OR booking_ref LIKE %s)';
            $search   = '%' . $wpdb->esc_like($filters['search']) . '%';
            $params   = array_merge($params, [$search, $search, $search]);
        }

        $limit  = isset($filters['limit'])  ? absint($filters['limit'])  : 20;
        $offset = isset($filters['offset']) ? absint($filters['offset']) : 0;

        $sql = "SELECT * FROM {$prefix}alm_bookings WHERE " . implode(' AND ', $where) .
               " ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;

        $rows = $wpdb->get_results($wpdb->prepare($sql, ...$params), ARRAY_A);

        return array_map(fn(array $row): static => new static($row), $rows ?: []);
    }

    // ─── Azioni ──────────────────────────────────────────────────────────────

    /**
     * Conferma la prenotazione (aggiorna status a confirmed e payment_status a paid).
     *
     * @param string|null $payment_ref  Stripe PaymentIntent ID.
     * @return bool
     */
    public function confirm(?string $payment_ref = null): bool {
        global $wpdb;

        $update = [
            'status'         => 'confirmed',
            'payment_status' => 'paid',
        ];

        if ($payment_ref !== null) {
            $update['payment_ref'] = $payment_ref;
        }

        $result = $wpdb->update(
            $wpdb->prefix . 'alm_bookings',
            $update,
            ['id' => $this->id],
            ['%s', '%s', '%s'],
            ['%d']
        );

        if ($result !== false) {
            $this->status         = 'confirmed';
            $this->payment_status = 'paid';
            if ($payment_ref !== null) {
                $this->payment_ref = $payment_ref;
            }
        }

        return $result !== false;
    }

    /**
     * Cancella la prenotazione.
     *
     * @param string $reason  Motivo cancellazione (salvato in notes).
     * @return bool
     */
    public function cancel(string $reason = ''): bool {
        global $wpdb;

        $notes = $this->notes ? $this->notes . "\n" : '';
        $notes .= '[Cancellazione ' . current_time('Y-m-d H:i') . ']' . ($reason ? ': ' . $reason : '');

        $result = $wpdb->update(
            $wpdb->prefix . 'alm_bookings',
            ['status' => 'cancelled', 'notes' => $notes],
            ['id' => $this->id],
            ['%s', '%s'],
            ['%d']
        );

        if ($result !== false) {
            $this->status = 'cancelled';
            $this->notes  = $notes;
        }

        return $result !== false;
    }

    /**
     * Aggiorna le note interne.
     *
     * @param string $notes
     * @return bool
     */
    public function update_notes(string $notes): bool {
        global $wpdb;

        $result = $wpdb->update(
            $wpdb->prefix . 'alm_bookings',
            ['notes' => sanitize_textarea_field($notes)],
            ['id' => $this->id],
            ['%s'],
            ['%d']
        );

        if ($result !== false) {
            $this->notes = $notes;
        }

        return $result !== false;
    }

    // ─── Calcoli ─────────────────────────────────────────────────────────────

    /**
     * Calcola il totale della prenotazione (soggiorno + extras).
     *
     * @return float
     */
    public function calculate_total(): float {
        return round($this->total_price + $this->extras_total, 2);
    }

    /**
     * Restituisce il numero di notti.
     *
     * @return int
     */
    public function get_nights(): int {
        return ALM_Availability::get_nights($this->checkin_date, $this->checkout_date);
    }

    /**
     * Restituisce il totale in centesimi (per Stripe).
     *
     * @return int
     */
    public function get_total_cents(): int {
        return (int) round($this->calculate_total() * 100);
    }

    /**
     * Serializza la prenotazione in array.
     *
     * @return array<string, mixed>
     */
    public function to_array(): array {
        return [
            'id'               => $this->id,
            'booking_ref'      => $this->booking_ref,
            'room_id'          => $this->room_id,
            'channel'          => $this->channel,
            'external_id'      => $this->external_id,
            'guest_name'       => $this->guest_name,
            'guest_email'      => $this->guest_email,
            'guest_phone'      => $this->guest_phone,
            'guest_adults'     => $this->guest_adults,
            'guest_children'   => $this->guest_children,
            'checkin_date'     => $this->checkin_date,
            'checkout_date'    => $this->checkout_date,
            'nights'           => $this->nights,
            'price_per_night'  => $this->price_per_night,
            'total_price'      => $this->total_price,
            'extras_total'     => $this->extras_total,
            'total'            => $this->calculate_total(),
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'payment_ref'      => $this->payment_ref,
            'special_requests' => $this->special_requests,
            'created_at'       => $this->created_at,
        ];
    }

    // ─── Helpers privati ─────────────────────────────────────────────────────

    /**
     * Recupera una riga dal DB per ID.
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    private static function fetch_by_id(int $id): ?array {
        global $wpdb;

        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}alm_bookings WHERE id = %d LIMIT 1",
            $id
        ), ARRAY_A);

        return $row ?: null;
    }

    /**
     * Genera un booking_ref univoco — wrapper pubblico per usi esterni (es. webhook).
     *
     * @return string
     */
    public static function generate_ref_static(): string {
        return self::generate_ref();
    }

    /**
     * Genera un booking_ref univoco nel formato ALM-YYYY-XXXX.
     *
     * @return string
     */
    private static function generate_ref(): string {
        global $wpdb;
        $year = date('Y');

        $max = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(CAST(SUBSTRING_INDEX(booking_ref, '-', -1) AS UNSIGNED))
             FROM {$wpdb->prefix}alm_bookings
             WHERE booking_ref LIKE %s",
            'ALM-' . $year . '-%'
        ));

        $next = str_pad((string) ($max + 1), 4, '0', STR_PAD_LEFT);
        return "ALM-{$year}-{$next}";
    }

    /**
     * Valida una stringa data in formato Y-m-d.
     *
     * @param string $date
     * @return bool
     */
    private static function is_valid_date(string $date): bool {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
