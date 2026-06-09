<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Notifications
 *
 * Invia le email transazionali via wp_mail().
 * I template HTML si trovano in includes/views/emails/.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

class ALM_Notifications {

    private const VIEWS_DIR = ALM_BOOKING_PATH . 'includes/views/emails/';

    private static function host_email(): string {
        $opt = get_option('alm_booking_settings');
        $email = is_array($opt) && !empty($opt['host_email'])
            ? $opt['host_email']
            : get_option('admin_email', '');
        return sanitize_email($email);
    }

    private static function host_name(): string {
        $opt = get_option('alm_booking_settings');
        if (is_array($opt) && !empty($opt['host_name'])) {
            return sanitize_text_field($opt['host_name']);
        }
        return 'Almaretna';
    }

    // ── Email principali ────────────────────────────────────────────────────────

    public static function send_confirmation(int $booking_id): bool {
        $data = self::prepare_data($booking_id);
        if (!$data) return false;

        $ok_guest = self::send_to_guest($data, 'booking-confirmation-guest.php',
            sprintf(__('Prenotazione confermata — %s', 'almaretna-booking'), $data['ref'])
        );
        $ok_host  = self::send_to_host($data, 'booking-confirmation-host.php',
            sprintf(__('[Almaretna] Nuova prenotazione — %s', 'almaretna-booking'), $data['ref'])
        );

        return $ok_guest && $ok_host;
    }

    public static function send_reminder(int $booking_id): bool {
        $data = self::prepare_data($booking_id);
        if (!$data || $data['status'] !== 'confirmed') return false;

        return self::send_to_guest($data, 'booking-reminder.php',
            sprintf(__('Ci vediamo presto, %s!', 'almaretna-booking'), $data['first_name'])
        );
    }

    public static function send_cancellation(int $booking_id): bool {
        $data = self::prepare_data($booking_id);
        if (!$data) return false;

        $ok_guest = self::send_to_guest($data, 'booking-cancelled.php',
            sprintf(__('Prenotazione annullata — %s', 'almaretna-booking'), $data['ref'])
        );
        $ok_host  = self::send_to_host($data, 'booking-cancelled.php',
            sprintf(__('[Almaretna] Prenotazione annullata — %s', 'almaretna-booking'), $data['ref'])
        );

        return $ok_guest && $ok_host;
    }

    // ── Metodi statici richiesti da class-api.php ────────────────────────────────

    /**
     * Invia email di conferma all'ospite.
     *
     * @param ALM_Booking $booking
     * @return bool
     */
    public static function send_confirmation_guest(ALM_Booking $booking): bool {
        $data = self::prepare_data($booking->id);
        if (!$data) return false;

        return self::send_to_guest($data, 'booking-confirmation-guest.php',
            sprintf(__('Prenotazione confermata — %s', 'almaretna-booking'), $data['ref'])
        );
    }

    /**
     * Invia notifica di nuova prenotazione all'host.
     *
     * @param ALM_Booking $booking
     * @return bool
     */
    public static function send_confirmation_host(ALM_Booking $booking): bool {
        $data = self::prepare_data($booking->id);
        if (!$data) return false;

        return self::send_to_host($data, 'booking-confirmation-host.php',
            sprintf(__('[Almaretna] Nuova prenotazione — %s', 'almaretna-booking'), $data['ref'])
        );
    }

    /**
     * Schedula il reminder 48h prima del check-in via wp_schedule_single_event().
     *
     * @param ALM_Booking $booking
     * @return void
     */
    public static function schedule_reminder(ALM_Booking $booking): void {
        $ci = DateTime::createFromFormat('Y-m-d', $booking->checkin_date, new DateTimeZone('Europe/Rome'));
        if (!$ci) return;

        // Mezzanotte del giorno di check-in − 48h = mezzanotte 2 giorni prima
        $ci->setTime(9, 0, 0);
        $send_at = (clone $ci)->modify('-2 days');

        // Non schedulare se la data di invio è già passata
        if ($send_at->getTimestamp() <= time()) return;

        wp_schedule_single_event(
            $send_at->getTimestamp(),
            'alm_send_checkin_reminder',
            [$booking->id]
        );
    }

    /**
     * Invia notifica di cancellazione all'host.
     *
     * @param ALM_Booking $booking
     * @return bool
     */
    public static function send_cancellation_host(ALM_Booking $booking): bool {
        $data = self::prepare_data($booking->id);
        if (!$data) return false;

        return self::send_to_host($data, 'booking-cancelled.php',
            sprintf(__('[Almaretna] Prenotazione annullata — %s', 'almaretna-booking'), $data['ref'])
        );
    }

    /**
     * Invia email di benvenuto con link per impostare la password.
     *
     * @param int    $user_id
     * @param string $guest_email
     * @param string $first_name
     * @param string $booking_ref
     * @param string $booking_page_url
     * @param string $set_password_url
     * @return bool
     */
    public static function send_account_welcome(
        int $user_id,
        string $guest_email,
        string $first_name,
        string $booking_ref,
        string $booking_page_url,
        string $set_password_url
    ): bool {
        $file = self::VIEWS_DIR . 'account-welcome.php';
        if (!file_exists($file)) return false;

        ob_start();
        include $file;
        $html = (string) ob_get_clean();

        if (!$html || !$guest_email) return false;

        return self::send(
            $guest_email,
            __('Benvenuto su Almaretna — Il tuo account è pronto', 'almaretna-booking'),
            $html
        );
    }

    // ── Cron: invia reminder 48h prima del check-in ──────────────────────────────

    public static function process_reminder_queue(): void {
        global $wpdb;

        $target_date = gmdate('Y-m-d', strtotime('+2 days'));

        // Usa checkin_date che è il nome reale della colonna in alm_bookings
        $ids = $wpdb->get_col($wpdb->prepare(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            "SELECT id FROM {$wpdb->prefix}alm_bookings
             WHERE checkin_date = %s AND status = 'confirmed'",
            $target_date
        ));

        foreach ($ids as $id) {
            self::send_reminder((int) $id);
        }
    }

    // ── Preparazione dati ────────────────────────────────────────────────────────

    private static function prepare_data(int $booking_id): ?array {
        $booking = ALM_Booking::get($booking_id);
        if (!$booking) return null;

        $room = $booking->room_id ? ALM_Room::get($booking->room_id) : null;

        // guest_name è il nome completo; separiamo per i template
        $name_parts = explode(' ', $booking->guest_name, 2);
        $first_name = $name_parts[0] ?? $booking->guest_name;

        $ci = DateTime::createFromFormat('Y-m-d', $booking->checkin_date);
        $co = DateTime::createFromFormat('Y-m-d', $booking->checkout_date);

        return [
            'booking_id'       => $booking->id,
            'ref'              => $booking->booking_ref,
            'status'           => $booking->status,
            'first_name'       => $first_name,
            'guest_name'       => $booking->guest_name,
            'email'            => $booking->guest_email,
            'phone'            => $booking->guest_phone ?? '',
            'room_name'        => $room ? $room->title : '—',
            'checkin'          => $booking->checkin_date,
            'checkout'         => $booking->checkout_date,
            'checkin_display'  => $ci  ? $ci->format('d/m/Y')  : $booking->checkin_date,
            'checkout_display' => $co  ? $co->format('d/m/Y') : $booking->checkout_date,
            'nights'           => $booking->nights,
            'adults'           => $booking->guest_adults,
            'children'         => $booking->guest_children,
            'total_price'      => $booking->get_total_cents(),
            'total_display'    => self::format_price($booking->get_total_cents()),
            'special_requests' => $booking->special_requests ?? '',
            'payment_method'   => 'stripe',
            'channel'          => $booking->channel,
            'host_name'        => self::host_name(),
            'host_email'       => self::host_email(),
            'site_url'         => home_url('/'),
            'checkin_time'     => '15:00',
            'checkout_time'    => '11:00',
            'dashboard_url'    => self::get_dashboard_url(),
        ];
    }

    private static function get_dashboard_url(): string {
        $page = get_page_by_path('le-mie-prenotazioni');
        return $page ? get_permalink($page) : home_url('/le-mie-prenotazioni/');
    }

    private static function render(string $template, array $data): ?string {
        $file = self::VIEWS_DIR . $template;
        if (!file_exists($file)) return null;
        ob_start();
        include $file;
        return (string) ob_get_clean();
    }

    private static function send_to_guest(array $data, string $template, string $subject): bool {
        $html = self::render($template, $data);
        if ($html === null || !$data['email']) return false;
        return self::send($data['email'], $subject, $html);
    }

    private static function send_to_host(array $data, string $template, string $subject): bool {
        $html = self::render($template, $data);
        if ($html === null) return false;
        return self::send(self::host_email(), $subject, $html);
    }

    private static function send(string $to, string $subject, string $html): bool {
        if (!$to) return false;
        return wp_mail($to, $subject, $html, [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::host_name() . ' <' . self::host_email() . '>',
        ]);
    }

    private static function format_price(int $cents): string {
        return '€ ' . number_format($cents / 100, 2, ',', '.');
    }
}
