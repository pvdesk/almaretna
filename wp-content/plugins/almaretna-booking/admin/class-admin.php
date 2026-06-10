<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Admin
 *
 * Controller del pannello amministrativo WordPress.
 * Registra menu, gestisce form action e AJAX.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_Admin {

    private string $version;
    private string $views_dir;

    public function __construct(string $version) {
        $this->version   = $version;
        $this->views_dir = ALM_BOOKING_PATH . 'admin/views/';
    }

    // ── Menu ───────────────────────────────────────────────────────────────

    public function add_admin_menus(): void {
        add_menu_page(
            __('Almaretna Booking', 'almaretna-booking'),
            __('Prenotazioni', 'almaretna-booking'),
            'manage_options',
            'alm-booking',
            [$this, 'page_dashboard'],
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'alm-booking',
            __('Dashboard', 'almaretna-booking'),
            __('Dashboard', 'almaretna-booking'),
            'manage_options',
            'alm-booking',
            [$this, 'page_dashboard']
        );

        add_submenu_page(
            'alm-booking',
            __('Prenotazioni', 'almaretna-booking'),
            __('Prenotazioni', 'almaretna-booking'),
            'manage_options',
            'alm-bookings',
            [$this, 'page_bookings']
        );

        add_submenu_page(
            'alm-booking',
            __('Disponibilità', 'almaretna-booking'),
            __('Disponibilità', 'almaretna-booking'),
            'manage_options',
            'alm-availability',
            [$this, 'page_availability']
        );

        add_submenu_page(
            'alm-booking',
            __('Tariffe', 'almaretna-booking'),
            __('Tariffe', 'almaretna-booking'),
            'manage_options',
            'alm-rates',
            [$this, 'page_rates']
        );

        add_submenu_page(
            'alm-booking',
            __('Report', 'almaretna-booking'),
            __('Report', 'almaretna-booking'),
            'manage_options',
            'alm-reports',
            [$this, 'page_reports']
        );

        add_submenu_page(
            'alm-booking',
            __('Impostazioni', 'almaretna-booking'),
            __('Impostazioni', 'almaretna-booking'),
            'manage_options',
            'alm-settings',
            [$this, 'page_settings']
        );
    }

    // ── Assets ─────────────────────────────────────────────────────────────

    public function enqueue_assets(string $hook): void {
        if (!str_contains($hook, 'alm-')) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            'alm-admin',
            ALM_BOOKING_URL . 'admin/assets/admin.css',
            [],
            $this->version
        );

        wp_enqueue_script(
            'alm-admin-js',
            ALM_BOOKING_URL . 'admin/assets/admin.js',
            ['jquery', 'wp-color-picker'],
            $this->version,
            true
        );

        wp_localize_script('alm-admin-js', 'alm_admin', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('alm_admin_nonce'),
            'i18n'     => [
                'confirm_cancel' => __('Sei sicuro di voler cancellare questa prenotazione?', 'almaretna-booking'),
                'confirm_sync'   => __('Avviare la sincronizzazione con Beds24?', 'almaretna-booking'),
                'loading'        => __('Caricamento…', 'almaretna-booking'),
            ],
        ]);
    }

    // ── Pagine ─────────────────────────────────────────────────────────────

    public function page_dashboard(): void {
        $this->render_view('dashboard.php', $this->get_dashboard_data());
    }

    public function page_bookings(): void {
        // Dettaglio singola prenotazione
        $booking_id = isset($_GET['booking_id']) ? absint($_GET['booking_id']) : 0;
        if ($booking_id) {
            $booking = ALM_Booking::get($booking_id);
            if (!$booking) {
                wp_die(esc_html__('Prenotazione non trovata.', 'almaretna-booking'));
            }
            $room = $booking->room_id ? ALM_Room::get($booking->room_id) : null;
            $this->render_view('booking-detail.php', ['booking' => $booking, 'room' => $room]);
            return;
        }

        // Lista prenotazioni con filtri
        $filters = [
            'status'    => sanitize_text_field($_GET['status']  ?? ''),
            'channel'   => sanitize_text_field($_GET['channel'] ?? ''),
            'search'    => sanitize_text_field($_GET['search']  ?? ''),
            'from'      => sanitize_text_field($_GET['from']    ?? ''),
            'to'        => sanitize_text_field($_GET['to']      ?? ''),
            'date_from' => sanitize_text_field($_GET['from']    ?? ''),
            'date_to'   => sanitize_text_field($_GET['to']      ?? ''),
        ];
        $bookings = ALM_Booking::get_all($filters);
        $this->render_view('bookings-list.php', ['bookings' => $bookings, 'filters' => $filters]);
    }

    public function page_availability(): void {
        $rooms = ALM_Room::get_all(['posts_per_page' => -1]);
        $this->render_view('availability.php', ['rooms' => $rooms]);
    }

    public function page_rates(): void {
        $rooms = ALM_Room::get_all(['posts_per_page' => -1]);
        $room_id = isset($_GET['room_id']) ? absint($_GET['room_id']) : 0;
        $rates   = $room_id ? ALM_Pricing::get_all_rates_for_room($room_id) : [];
        $this->render_view('rates.php', [
            'rooms'   => $rooms,
            'room_id' => $room_id,
            'rates'   => $rates,
        ]);
    }

    public function page_reports(): void {
        $year  = absint($_GET['year']  ?? gmdate('Y'));
        $month = absint($_GET['month'] ?? 0);
        $this->render_view('reports.php', $this->get_report_data($year, $month));
    }

    public function page_settings(): void {
        $settings = get_option('alm_booking_settings', []);
        $this->render_view('settings.php', ['settings' => $settings]);
    }

    // ── Azioni form (admin_post_*) ──────────────────────────────────────────

    public function save_settings(): void {
        check_admin_referer('alm_save_settings');

        $settings = [
            'host_name'         => sanitize_text_field($_POST['host_name']         ?? ''),
            'host_email'        => sanitize_email($_POST['host_email']             ?? ''),
            'host_phone'        => sanitize_text_field($_POST['host_phone']        ?? ''),
            'checkin_time'      => sanitize_text_field($_POST['checkin_time']      ?? '15:00'),
            'checkout_time'     => sanitize_text_field($_POST['checkout_time']     ?? '11:00'),
            'currency'          => sanitize_text_field($_POST['currency']          ?? 'EUR'),
            'min_stay_global'   => absint($_POST['min_stay_global']                ?? 1),
            'beds24_enabled'    => !empty($_POST['beds24_enabled']) ? 1 : 0,
        ];

        update_option('alm_booking_settings', $settings);

        // Chiavi Stripe — salva solo se non provengono da costanti wp-config.php
        if (ALM_Stripe::keys_source() === 'db') {
            $existing = get_option('alm_stripe_keys', []);
            $stripe   = [];

            // Preserva il valore esistente se il campo arriva vuoto (campo mascherato non modificato)
            $pk = sanitize_text_field($_POST['stripe_publishable_key'] ?? '');
            $sk = sanitize_text_field($_POST['stripe_secret_key']      ?? '');
            $wh = sanitize_text_field($_POST['stripe_webhook_secret']  ?? '');

            $stripe['publishable_key'] = $pk !== '' ? $pk : ($existing['publishable_key'] ?? '');
            $stripe['secret_key']      = $sk !== '' ? $sk : ($existing['secret_key']      ?? '');
            $stripe['webhook_secret']  = $wh !== '' ? $wh : ($existing['webhook_secret']  ?? '');

            update_option('alm_stripe_keys', $stripe);
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-settings', 'updated' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function save_rate(): void {
        check_admin_referer('alm_save_rates');

        $data = [
            'room_id'        => absint($_POST['room_id']    ?? 0),
            'rate_name'      => sanitize_text_field($_POST['name']       ?? ''),
            'price_per_night'=> (float) ($_POST['price']                 ?? 0),
            'date_from'      => sanitize_text_field($_POST['start_date'] ?? ''),
            'date_to'        => sanitize_text_field($_POST['end_date']   ?? ''),
            'channel'        => sanitize_text_field($_POST['channel']    ?? 'all'),
            'min_stay'       => absint($_POST['min_stay']                ?? 1),
        ];

        $rate_id = absint($_POST['rate_id'] ?? 0);

        if ($rate_id) {
            $res = ALM_Pricing::update_rate($rate_id, $data);
        } else {
            $res = ALM_Pricing::create_rate($data);
        }

        if (is_wp_error($res)) {
            wp_redirect(add_query_arg(
                ['page' => 'alm-rates', 'room_id' => $data['room_id'], 'error_msg' => urlencode($res->get_error_message())],
                admin_url('admin.php')
            ));
            exit;
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-rates', 'room_id' => $data['room_id'], 'updated' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function save_base_price(): void {
        check_admin_referer('alm_save_base_price');

        $room_id    = absint($_POST['room_id'] ?? 0);
        $base_price = (float) ($_POST['base_price'] ?? 0.0);

        if ($room_id && $base_price >= 0) {
            update_post_meta($room_id, '_room_base_price', $base_price);
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-rates', 'room_id' => $room_id, 'updated' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function delete_rate(): void {
        check_admin_referer('alm_delete_rate');

        $rate_id = absint($_POST['rate_id'] ?? 0);
        $room_id = absint($_POST['room_id'] ?? 0);

        if ($rate_id) {
            ALM_Pricing::delete_rate($rate_id);
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-rates', 'room_id' => $room_id, 'deleted' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function add_block(): void {
        check_admin_referer('alm_add_block');

        $room_id = absint($_POST['room_id']    ?? 0);
        $start   = sanitize_text_field($_POST['start_date'] ?? '');
        $end     = sanitize_text_field($_POST['end_date']   ?? '');
        $reason  = sanitize_text_field($_POST['reason']     ?? 'Blocco manuale');

        if ($room_id && $start && $end) {
            $avail = new ALM_Availability();
            $avail->block($room_id, $start, $end, $reason);
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-availability', 'room_id' => $room_id, 'blocked' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function remove_block(): void {
        check_admin_referer('alm_remove_block');

        $block_id = absint($_POST['block_id'] ?? 0);
        $room_id  = absint($_POST['room_id']  ?? 0);

        if ($block_id) {
            $avail = new ALM_Availability();
            $avail->unblock($block_id);
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-availability', 'room_id' => $room_id, 'unblocked' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function cancel_booking(): void {
        check_admin_referer('alm_cancel_booking');

        $booking_id = absint($_POST['booking_id'] ?? 0);
        $reason     = sanitize_textarea_field($_POST['reason'] ?? '');

        if ($booking_id) {
            $booking = ALM_Booking::get($booking_id);
            if ($booking) {
                $booking->cancel($reason);
                ALM_Notifications::send_cancellation($booking_id);
            }
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-bookings', 'cancelled' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    public function sync_beds24(): void {
        check_admin_referer('alm_sync_beds24');

        if (ALM_Beds24::is_configured()) {
            ALM_Beds24::sync_all();
        }

        wp_redirect(add_query_arg(
            ['page' => 'alm-booking', 'synced' => '1'],
            admin_url('admin.php')
        ));
        exit;
    }

    // ── AJAX ───────────────────────────────────────────────────────────────

    public function ajax_get_bookings(): void {
        check_ajax_referer('alm_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $filters  = [
            'status'    => sanitize_text_field($_POST['status']  ?? ''),
            'room_id'   => absint($_POST['room_id']              ?? 0),
            'date_from' => sanitize_text_field($_POST['from']    ?? ''),
            'date_to'   => sanitize_text_field($_POST['to']      ?? ''),
        ];
        $bookings = ALM_Booking::get_all($filters);

        wp_send_json_success(array_map(
            fn(ALM_Booking $b) => $b->to_array(),
            $bookings
        ));
    }

    public function ajax_get_calendar(): void {
        check_ajax_referer('alm_admin_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $room_id = absint($_POST['room_id'] ?? 0);
        $year    = absint($_POST['year']    ?? gmdate('Y'));
        $month   = absint($_POST['month']   ?? gmdate('n'));

        if (!$room_id) {
            wp_send_json_error('room_id required');
        }

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = gmdate('Y-m-t', mktime(0, 0, 0, $month, 1, $year));

        $avail  = new ALM_Availability();
        $dates  = $avail->get_blocked_dates($room_id, $start, $end);

        wp_send_json_success(['blocked' => $dates, 'start' => $start, 'end' => $end]);
    }

    // ── Dati pagine ─────────────────────────────────────────────────────────

    private function get_dashboard_data(): array {
        global $wpdb;
        $table = $wpdb->prefix . 'alm_bookings';

        $today       = gmdate('Y-m-d');
        $month_start = gmdate('Y-m-01');
        $month_end   = gmdate('Y-m-t');

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $total_bookings   = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status != 'cancelled'");
        $pending          = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE status = 'pending'");
        $this_month_rev   = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(total_price),0) FROM {$table} WHERE status = 'confirmed' AND checkin_date BETWEEN %s AND %s",
            $month_start, $month_end
        ));
        $checkins_today   = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE checkin_date = %s AND status = 'confirmed'",
            $today
        ));
        $checkouts_today  = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE checkout_date = %s AND status = 'confirmed'",
            $today
        ));
        $recent_bookings  = ALM_Booking::get_all(['limit' => 8]);
        // phpcs:enable

        return [
            'total_bookings'  => $total_bookings,
            'pending'         => $pending,
            'this_month_rev'  => $this_month_rev,
            'checkins_today'  => $checkins_today,
            'checkouts_today' => $checkouts_today,
            'recent_bookings' => $recent_bookings,
            'beds24_ok'       => ALM_Beds24::is_configured(),
            'stripe_ok'       => ALM_Stripe::is_configured(),
        ];
    }

    private function get_report_data(int $year, int $month): array {
        global $wpdb;
        $table = $wpdb->prefix . 'alm_bookings';

        if ($month > 0) {
            $start = sprintf('%04d-%02d-01', $year, $month);
            $end   = gmdate('Y-m-t', mktime(0, 0, 0, $month, 1, $year));
        } else {
            $start = $year . '-01-01';
            $end   = $year . '-12-31';
        }

        // phpcs:disable WordPress.DB.DirectDatabaseQuery
        $revenue = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(total_price),0) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $confirmed = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $cancelled = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE status='cancelled' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $by_channel = $wpdb->get_results($wpdb->prepare(
            "SELECT channel, COUNT(*) as cnt, SUM(total_price) as rev FROM {$table}
             WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s GROUP BY channel",
             $start, $end
        ), ARRAY_A);
        $avg_nights = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(nights) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $total_nights = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(nights), 0) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $total_adults = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(guest_adults), 0) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $total_children = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(guest_children), 0) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        $avg_lead_time = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(AVG(DATEDIFF(checkin_date, created_at)), 0) FROM {$table} WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s",
            $start, $end
        ));
        // phpcs:enable

        // Calcolo Tasso di Occupazione
        $rooms_count = count(ALM_Room::get_all());
        if ($rooms_count <= 0) {
            $rooms_count = 7;
        }
        $num_days = (strtotime($end) - strtotime($start)) / 86400 + 1;
        $total_capacity = $rooms_count * $num_days;
        $occupancy_pct = $total_capacity > 0 ? round(($total_nights / $total_capacity) * 100, 1) : 0.0;
        $occupancy_pct = min(100.0, $occupancy_pct);

        // Calcolo Trend per Grafico Periodo
        $trend_data = [];
        if ($month == 0) {
            $monthly_data = $wpdb->get_results($wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                "SELECT MONTH(checkin_date) as m, COALESCE(SUM(total_price), 0) as rev, COUNT(*) as cnt
                 FROM {$table}
                 WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s
                 GROUP BY MONTH(checkin_date)
                 ORDER BY m",
                $start, $end
            ), ARRAY_A) ?: [];

            for ($i = 1; $i <= 12; $i++) {
                $trend_data[$i] = ['rev' => 0.0, 'cnt' => 0];
            }
            foreach ($monthly_data as $row) {
                $m_num = (int) $row['m'];
                $trend_data[$m_num] = [
                    'rev' => (float) $row['rev'],
                    'cnt' => (int) $row['cnt']
                ];
            }
        } else {
            $daily_data = $wpdb->get_results($wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                "SELECT DAY(checkin_date) as d, COALESCE(SUM(total_price), 0) as rev, COUNT(*) as cnt
                 FROM {$table}
                 WHERE status='confirmed' AND checkin_date BETWEEN %s AND %s
                 GROUP BY DAY(checkin_date)
                 ORDER BY d",
                $start, $end
            ), ARRAY_A) ?: [];

            $num_days_month = (int) gmdate('t', mktime(0, 0, 0, $month, 1, $year));
            for ($i = 1; $i <= $num_days_month; $i++) {
                $trend_data[$i] = ['rev' => 0.0, 'cnt' => 0];
            }
            foreach ($daily_data as $row) {
                $d_num = (int) $row['d'];
                $trend_data[$d_num] = [
                    'rev' => (float) $row['rev'],
                    'cnt' => (int) $row['cnt']
                ];
            }
        }

        return [
            'year'           => $year,
            'month'          => $month,
            'start'          => $start,
            'end'            => $end,
            'revenue'        => $revenue,
            'confirmed'      => $confirmed,
            'cancelled'      => $cancelled,
            'by_channel'     => $by_channel ?: [],
            'avg_nights'     => round($avg_nights, 1),
            'total_nights'   => $total_nights,
            'total_guests'   => $total_adults + $total_children,
            'total_adults'   => $total_adults,
            'total_children' => $total_children,
            'avg_lead_time'  => round($avg_lead_time, 1),
            'occupancy_pct'  => $occupancy_pct,
            'trend_data'     => $trend_data,
        ];
    }

    // ── Utility ─────────────────────────────────────────────────────────────

    private function render_view(string $template, array $data = []): void {
        $file = $this->views_dir . $template;
        if (!file_exists($file)) {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                esc_html(sprintf('Vista non trovata: %s', $template))
            );
            return;
        }
        extract($data, EXTR_SKIP);
        include $file;
    }

    // ── AJAX: scrivi chiavi Stripe in wp-config.php ───────────────────────────

    public function ajax_write_stripe_config(): void {
        check_ajax_referer('alm_write_stripe_config', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permesso negato.', 'almaretna-booking'), 403);
        }

        if (ALM_Stripe::keys_source() === 'config') {
            wp_send_json_error(__('Le chiavi sono già definite in wp-config.php.', 'almaretna-booking'));
        }

        $pk    = sanitize_text_field(wp_unslash($_POST['pk']    ?? ''));
        $sk    = sanitize_text_field(wp_unslash($_POST['sk']    ?? ''));
        $whsec = sanitize_text_field(wp_unslash($_POST['whsec'] ?? ''));

        if (!preg_match('/^pk_(live|test)_/', $pk)) {
            wp_send_json_error(__('Publishable Key non valida (deve iniziare con pk_live_ o pk_test_).', 'almaretna-booking'));
        }
        if (!preg_match('/^sk_(live|test)_/', $sk)) {
            wp_send_json_error(__('Secret Key non valida (deve iniziare con sk_live_ o sk_test_).', 'almaretna-booking'));
        }
        if (!str_starts_with($whsec, 'whsec_')) {
            wp_send_json_error(__('Webhook Secret non valido (deve iniziare con whsec_).', 'almaretna-booking'));
        }

        // Individua wp-config.php (root o directory superiore)
        $config_path = ABSPATH . 'wp-config.php';
        if (!file_exists($config_path)) {
            $config_path = dirname(ABSPATH) . '/wp-config.php';
        }

        if (!file_exists($config_path) || !is_writable($config_path)) {
            wp_send_json_error(__('wp-config.php non trovato o non scrivibile. Inserisci le costanti manualmente.', 'almaretna-booking'));
        }

        $content = file_get_contents($config_path);
        if ($content === false) {
            wp_send_json_error(__('Impossibile leggere wp-config.php.', 'almaretna-booking'));
        }

        // Evita duplicati se il blocco è già presente ma non rilevato come costante
        if (str_contains($content, 'ALM_STRIPE_SECRET_KEY')) {
            wp_send_json_error(__('Le costanti ALM_STRIPE_* esistono già nel file. Rimuovile e riprova.', 'almaretna-booking'));
        }

        $block = "// Stripe — Almaretna Booking\n" .
                 "define( 'ALM_STRIPE_PUBLISHABLE_KEY', '" . addslashes($pk)    . "' );\n" .
                 "define( 'ALM_STRIPE_SECRET_KEY',      '" . addslashes($sk)    . "' );\n" .
                 "define( 'ALM_STRIPE_WEBHOOK_SECRET',  '" . addslashes($whsec) . "' );\n";

        // Inserisci prima del marcatore "stop editing" (vari formati)
        $inserted = false;
        foreach (["/* That's all, stop editing!", "/* That's all", 'require_once ABSPATH', 'require_once(ABSPATH'] as $marker) {
            $pos = strpos($content, $marker);
            if ($pos !== false) {
                $content  = substr($content, 0, $pos) . $block . "\n" . substr($content, $pos);
                $inserted = true;
                break;
            }
        }

        if (!$inserted) {
            $content .= "\n" . $block;
        }

        // Scrittura atomica tramite file temporaneo
        $tmp = $config_path . '.alm-tmp';
        if (file_put_contents($tmp, $content, LOCK_EX) === false) {
            wp_send_json_error(__('Impossibile scrivere il file temporaneo.', 'almaretna-booking'));
        }

        if (!rename($tmp, $config_path)) {
            @unlink($tmp);
            wp_send_json_error(__('Impossibile aggiornare wp-config.php.', 'almaretna-booking'));
        }

        wp_send_json_success([
            'message' => __('Chiavi Stripe salvate in wp-config.php.', 'almaretna-booking'),
            'reload'  => true,
        ]);
    }

    // ── AJAX: Google Search Console ───────────────────────────────────────────

    public function ajax_save_gsc(): void {
        check_ajax_referer('alm_save_gsc', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permesso negato.', 403);
        }
        $code = sanitize_text_field(wp_unslash($_POST['code'] ?? ''));
        if ($code === '') {
            wp_send_json_error('Inserisci il codice di verifica.');
        }
        update_option('alm_gsc_verification', $code);
        update_option('alm_gsc_locked', true);
        wp_send_json_success(['message' => 'Codice salvato. Card congelata.']);
    }

    public function ajax_unlock_gsc(): void {
        check_ajax_referer('alm_unlock_gsc', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permesso negato.', 403);
        }
        update_option('alm_gsc_locked', false);
        wp_send_json_success(['message' => 'Card sbloccata.']);
    }

    // ── AJAX: Google Analytics 4 ───────────────────────────────────────────────

    public function ajax_save_ga4(): void {
        check_ajax_referer('alm_save_ga4', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permesso negato.', 403);
        }
        $id = strtoupper(sanitize_text_field(wp_unslash($_POST['id'] ?? '')));
        if (!preg_match('/^G-[A-Z0-9]+$/', $id)) {
            wp_send_json_error('Measurement ID non valido (formato atteso: G-XXXXXXXXXX).');
        }
        update_option('alm_ga4_measurement_id', $id);
        update_option('alm_ga4_locked', true);
        wp_send_json_success(['message' => 'ID salvato. Card congelata.']);
    }

    public function ajax_unlock_ga4(): void {
        check_ajax_referer('alm_unlock_ga4', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permesso negato.', 403);
        }
        update_option('alm_ga4_locked', false);
        wp_send_json_success(['message' => 'Card sbloccata.']);
    }

    // ── AJAX: testa la connessione Beds24 ─────────────────────────────────────

    public function ajax_test_beds24(): void {
        check_ajax_referer('alm_test_beds24', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permesso negato.', 'almaretna-booking'), 403);
        }

        if (!ALM_Beds24::is_configured()) {
            wp_send_json_error(__('Beds24 non configurato. Aggiungi le costanti in wp-config.php.', 'almaretna-booking'));
        }

        // Chiama l'API Beds24 — endpoint info per verificare autenticazione
        $result = ALM_Beds24::get_bookings([
            'arrivalFrom' => gmdate('Y-m-d'),
            'arrivalTo'   => gmdate('Y-m-d', strtotime('+1 day')),
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error(
                sprintf(__('Errore Beds24: %s', 'almaretna-booking'), $result->get_error_message())
            );
        }

        wp_send_json_success(
            __('✓ Connessione Beds24 OK. Token valido.', 'almaretna-booking')
        );
    }

    // ── Dashboard widgets: GA4 + GSC ─────────────────────────────────────────

    public function register_dashboard_widgets(): void {
        wp_add_dashboard_widget(
            'alm_ga4_widget',
            '&#128202; Analytics 4 — Ultimi 28 giorni',
            [$this, 'render_ga4_widget']
        );
        wp_add_dashboard_widget(
            'alm_gsc_widget',
            '&#128269; Search Console — Ultimi 28 giorni',
            [$this, 'render_gsc_widget']
        );
    }

    public function render_ga4_widget(): void {
        if (!class_exists('ALM_Google_API') || !ALM_Google_API::is_configured() || ALM_Google_API::ga4_property_id() === '') {
            echo '<p style="color:#888;font-size:13px;margin:0;">Configura il <a href="'
                . esc_url(admin_url('admin.php?page=alm-settings')) . '#alm-google-card">Service Account e il GA4 Property ID</a>.</p>';
            return;
        }
        $data = ALM_Google_API::fetch_ga4_data();
        if (is_wp_error($data)) {
            echo '<p style="color:#c62828;font-size:13px;margin:0;">&#10007; ' . esc_html($data->get_error_message()) . '</p>';
            return;
        }
        $this->print_ga4_widget($data);
    }

    public function render_gsc_widget(): void {
        if (!class_exists('ALM_Google_API') || !ALM_Google_API::is_configured()) {
            echo '<p style="color:#888;font-size:13px;margin:0;">Configura il <a href="'
                . esc_url(admin_url('admin.php?page=alm-settings')) . '#alm-google-card">Service Account Google</a>.</p>';
            return;
        }
        $data = ALM_Google_API::fetch_gsc_data();
        if (is_wp_error($data)) {
            echo '<p style="color:#c62828;font-size:13px;margin:0;">&#10007; ' . esc_html($data->get_error_message()) . '</p>';
            return;
        }
        $this->print_gsc_widget($data);
    }

    private function print_ga4_widget(array $data): void {
        $pages_report = $data['pages'] ?? [];
        $totals_row   = $pages_report['totals'][0]['metricValues'] ?? [];
        $rows         = $pages_report['rows'] ?? [];
        $country_rows = ($data['countries']['rows']) ?? [];

        $sessions   = number_format((float) ($totals_row[0]['value'] ?? 0));
        $users      = number_format((float) ($totals_row[1]['value'] ?? 0));
        $pageviews  = number_format((float) ($totals_row[2]['value'] ?? 0));
        $bounce_raw = (float) ($totals_row[3]['value'] ?? 0);
        $bounce     = number_format($bounce_raw * 100, 1) . '%';
        $dur_raw    = (int) ($totals_row[4]['value'] ?? 0);
        $dur        = ($dur_raw >= 60 ? floor($dur_raw / 60) . 'm ' : '') . ($dur_raw % 60) . 's';

        $max_pv = !empty($rows) ? max(1, (int) ($rows[0]['metricValues'][2]['value'] ?? 1)) : 1;
        $max_s  = !empty($country_rows) ? max(1, (int) ($country_rows[0]['metricValues'][0]['value'] ?? 1)) : 1;
        ?>
        <style>
        .alm-wdg{font-size:13px;color:#1d2327;}.alm-wdg-kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:4px;margin:0 0 10px;}.alm-wdg-kpi{background:#f6f7f7;border-radius:4px;padding:8px 6px;text-align:center;}.alm-wdg-kpi b{display:block;font-size:17px;color:#1d2327;font-weight:700;line-height:1.2;}.alm-wdg-kpi span{font-size:10px;color:#646970;text-transform:uppercase;letter-spacing:.04em;}.alm-wdg-bar-wrap{display:flex;align-items:center;gap:6px;margin:3px 0;font-size:12px;}.alm-wdg-bar-label{flex:0 0 130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#444;}.alm-wdg-bar-track{flex:1;background:#e5e7eb;border-radius:2px;height:6px;}.alm-wdg-bar-fill{height:6px;border-radius:2px;background:#2271b1;}.alm-wdg-bar-val{flex:0 0 40px;text-align:right;color:#646970;font-size:11px;}.alm-wdg-sep{border:none;border-top:1px solid #f0f0f0;margin:8px 0;}.alm-wdg-foot{display:flex;align-items:center;justify-content:space-between;margin-top:8px;font-size:11px;color:#999;}
        </style>
        <div class="alm-wdg">
            <div class="alm-wdg-kpis">
                <div class="alm-wdg-kpi"><b><?php echo esc_html($sessions); ?></b><span>Sessioni</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($users); ?></b><span>Utenti</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($pageviews); ?></b><span>Pag.viste</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($bounce); ?></b><span>Rimbalzo</span></div>
            </div>
            <p style="font-size:11px;color:#646970;margin:0 0 8px;">Durata media sessione: <strong><?php echo esc_html($dur); ?></strong></p>

            <?php if (!empty($rows)) : ?>
            <hr class="alm-wdg-sep">
            <p style="font-size:11px;font-weight:600;color:#444;margin:0 0 4px;">Pagine più visitate</p>
            <?php foreach (array_slice($rows, 0, 6) as $row) :
                $path = $row['dimensionValues'][0]['value'] ?? '—';
                $pv   = (int) ($row['metricValues'][2]['value'] ?? 0);
                $pct  = $max_pv > 0 ? round($pv / $max_pv * 100) : 0;
            ?>
            <div class="alm-wdg-bar-wrap">
                <span class="alm-wdg-bar-label" title="<?php echo esc_attr($path); ?>"><?php echo esc_html($path); ?></span>
                <div class="alm-wdg-bar-track"><div class="alm-wdg-bar-fill" style="width:<?php echo (int) $pct; ?>%;"></div></div>
                <span class="alm-wdg-bar-val"><?php echo number_format($pv); ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($country_rows)) : ?>
            <hr class="alm-wdg-sep">
            <p style="font-size:11px;font-weight:600;color:#444;margin:0 0 4px;">Paesi</p>
            <?php foreach (array_slice($country_rows, 0, 5) as $row) :
                $country = $row['dimensionValues'][0]['value'] ?? '—';
                $s       = (int) ($row['metricValues'][0]['value'] ?? 0);
                $pct     = $max_s > 0 ? round($s / $max_s * 100) : 0;
            ?>
            <div class="alm-wdg-bar-wrap">
                <span class="alm-wdg-bar-label"><?php echo esc_html($country); ?></span>
                <div class="alm-wdg-bar-track"><div class="alm-wdg-bar-fill" style="width:<?php echo (int) $pct; ?>;background:#0ea5e9;"></div></div>
                <span class="alm-wdg-bar-val"><?php echo number_format($s); ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <div class="alm-wdg-foot">
                <span><?php echo esc_html($data['fetched'] ?? ''); ?> &nbsp;·&nbsp; <?php echo esc_html($data['range'] ?? ''); ?></span>
                <button type="button" class="button button-small"
                        onclick="almRefreshWidget('ga4','<?php echo esc_js(wp_create_nonce('alm_refresh_ga4')); ?>')">
                    &#8635; Aggiorna
                </button>
            </div>
        </div>
        <?php
        $this->print_widget_refresh_script();
    }

    private function print_gsc_widget(array $data): void {
        $totals  = $data['totals']  ?? [];
        $queries = ($data['queries']['rows']) ?? [];
        $pages   = ($data['pages']['rows'])   ?? [];

        $impressions = number_format((float) ($totals['impressions'] ?? 0));
        $clicks      = number_format((float) ($totals['clicks']      ?? 0));
        $ctr_raw     = (float) ($totals['ctr']      ?? 0);
        $pos_raw     = (float) ($totals['position'] ?? 0);
        $ctr         = number_format($ctr_raw * 100, 1) . '%';
        $pos         = number_format($pos_raw, 1);

        $max_q = !empty($queries) ? max(1, (int) ($queries[0]['clicks'] ?? 1)) : 1;
        $max_p = !empty($pages)   ? max(1, (int) ($pages[0]['clicks']   ?? 1)) : 1;
        ?>
        <div class="alm-wdg">
            <div class="alm-wdg-kpis">
                <div class="alm-wdg-kpi"><b><?php echo esc_html($impressions); ?></b><span>Impres.</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($clicks); ?></b><span>Click</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($ctr); ?></b><span>CTR</span></div>
                <div class="alm-wdg-kpi"><b><?php echo esc_html($pos); ?></b><span>Posiz.</span></div>
            </div>

            <?php if (!empty($queries)) : ?>
            <hr class="alm-wdg-sep">
            <p style="font-size:11px;font-weight:600;color:#444;margin:0 0 4px;">Query più cercate</p>
            <?php foreach (array_slice($queries, 0, 6) as $row) :
                $q   = $row['keys'][0] ?? '—';
                $c   = (int) ($row['clicks'] ?? 0);
                $imp = (int) ($row['impressions'] ?? 0);
                $pct = $max_q > 0 ? round($c / $max_q * 100) : 0;
            ?>
            <div class="alm-wdg-bar-wrap">
                <span class="alm-wdg-bar-label" title="<?php echo esc_attr($q); ?>"><?php echo esc_html($q); ?></span>
                <div class="alm-wdg-bar-track"><div class="alm-wdg-bar-fill" style="width:<?php echo (int) $pct; ?>%;background:#059669;"></div></div>
                <span class="alm-wdg-bar-val"><?php echo $c; ?> / <?php echo $imp; ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($pages)) : ?>
            <hr class="alm-wdg-sep">
            <p style="font-size:11px;font-weight:600;color:#444;margin:0 0 4px;">Pagine più cliccate</p>
            <?php foreach (array_slice($pages, 0, 5) as $row) :
                $pg  = parse_url($row['keys'][0] ?? '', PHP_URL_PATH) ?: ($row['keys'][0] ?? '—');
                $c   = (int) ($row['clicks'] ?? 0);
                $pct = $max_p > 0 ? round($c / $max_p * 100) : 0;
            ?>
            <div class="alm-wdg-bar-wrap">
                <span class="alm-wdg-bar-label" title="<?php echo esc_attr($row['keys'][0] ?? ''); ?>"><?php echo esc_html($pg); ?></span>
                <div class="alm-wdg-bar-track"><div class="alm-wdg-bar-fill" style="width:<?php echo (int) $pct; ?>%;background:#059669;"></div></div>
                <span class="alm-wdg-bar-val"><?php echo $c; ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>

            <div class="alm-wdg-foot">
                <span><?php echo esc_html($data['fetched'] ?? ''); ?> &nbsp;·&nbsp; <?php echo esc_html($data['range'] ?? ''); ?></span>
                <button type="button" class="button button-small"
                        onclick="almRefreshWidget('gsc','<?php echo esc_js(wp_create_nonce('alm_refresh_gsc')); ?>')">
                    &#8635; Aggiorna
                </button>
            </div>
        </div>
        <?php
    }

    private static bool $widget_script_printed = false;

    private function print_widget_refresh_script(): void {
        if (self::$widget_script_printed) return;
        self::$widget_script_printed = true;
        ?>
        <script>
        function almRefreshWidget(type, nonce) {
            var fd = new FormData();
            fd.append('action', 'alm_refresh_' + type);
            fd.append('nonce', nonce);
            fetch(ajaxurl, { method: 'POST', credentials: 'same-origin', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(d){ if (d.success) location.reload(); });
        }
        </script>
        <?php
    }

    // ── AJAX: refresh widget data ─────────────────────────────────────────────

    public function ajax_refresh_ga4(): void {
        check_ajax_referer('alm_refresh_ga4', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permesso negato.', 403);
        delete_transient('alm_ga4_widget_v1');
        ALM_Google_API::fetch_ga4_data(true);
        wp_send_json_success('ok');
    }

    public function ajax_refresh_gsc(): void {
        check_ajax_referer('alm_refresh_gsc', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permesso negato.', 403);
        delete_transient('alm_gsc_widget_v1');
        ALM_Google_API::fetch_gsc_data(true);
        wp_send_json_success('ok');
    }

    // ── AJAX: salva configurazione Google API ─────────────────────────────────

    public function ajax_save_google_api(): void {
        check_ajax_referer('alm_save_google_api', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permesso negato.', 403);

        $json_raw    = wp_unslash($_POST['sa_json']      ?? '');
        $property_id = sanitize_text_field(wp_unslash($_POST['property_id'] ?? ''));
        $site_url    = esc_url_raw(wp_unslash($_POST['site_url'] ?? ''));

        if ($json_raw !== '') {
            $sa = json_decode($json_raw, true);
            if (!is_array($sa) || ($sa['type'] ?? '') !== 'service_account' || empty($sa['private_key'])) {
                wp_send_json_error('JSON non valido: deve essere un Service Account Google scaricato da Cloud Console.');
            }
            update_option('alm_google_sa_json', $json_raw);
        }

        if ($property_id !== '') {
            if (!preg_match('/^properties\/\d+$/', $property_id)) {
                wp_send_json_error('Property ID non valido — formato atteso: properties/123456789');
            }
            update_option('alm_ga4_property_id', $property_id);
        }

        if ($site_url !== '') {
            update_option('alm_gsc_site_url', $site_url);
        }

        delete_transient('alm_ga4_widget_v1');
        delete_transient('alm_gsc_widget_v1');

        wp_send_json_success('✓ Configurazione salvata.');
    }

    // ── AJAX: test connessione GA4 / GSC ─────────────────────────────────────

    public function ajax_test_ga4(): void {
        check_ajax_referer('alm_test_ga4', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permesso negato.', 403);

        if (!class_exists('ALM_Google_API') || !ALM_Google_API::is_configured()) {
            wp_send_json_error('Service Account non configurato.');
        }
        if (ALM_Google_API::ga4_property_id() === '') {
            wp_send_json_error('Property ID GA4 non configurato.');
        }

        $result = ALM_Google_API::ga4_run_report([
            'dateRanges' => [['startDate' => '7daysAgo', 'endDate' => 'yesterday']],
            'metrics'    => [['name' => 'sessions']],
            'limit'      => 1,
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error('&#10007; ' . $result->get_error_message());
        }

        $sessions = $result['rows'][0]['metricValues'][0]['value'] ?? '0';
        wp_send_json_success('&#10003; Connessione OK — ' . number_format((float) $sessions) . ' sessioni negli ultimi 7 giorni.');
    }

    public function ajax_test_gsc(): void {
        check_ajax_referer('alm_test_gsc', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Permesso negato.', 403);

        if (!class_exists('ALM_Google_API') || !ALM_Google_API::is_configured()) {
            wp_send_json_error('Service Account non configurato.');
        }

        $result = ALM_Google_API::gsc_query([
            'startDate' => gmdate('Y-m-d', strtotime('-10 days')),
            'endDate'   => gmdate('Y-m-d', strtotime('-3 days')),
        ]);

        if (is_wp_error($result)) {
            wp_send_json_error('&#10007; ' . $result->get_error_message());
        }

        $clicks = number_format((float) ($result['clicks'] ?? 0));
        wp_send_json_success('&#10003; Connessione OK — ' . $clicks . ' click negli ultimi 7 giorni.');
    }

    // ── AJAX: reset OPcache PHP ───────────────────────────────────────────────

    public function ajax_reset_opcache(): void {
        check_ajax_referer('alm_reset_opcache', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permesso negato.', 403);
        }

        if (!function_exists('opcache_reset')) {
            wp_send_json_success('OPcache non attiva su questo server — nessuna azione necessaria.');
        }

        opcache_reset();
        wp_send_json_success('✓ OPcache svuotata. Ricarica la pagina.');
    }
}
