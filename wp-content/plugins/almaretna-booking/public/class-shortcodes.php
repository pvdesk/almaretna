<?php
declare(strict_types=1);

/**
 * Almaretna Booking — ALM_Shortcodes (plugin)
 *
 * Registra gli shortcode del plugin che sovrascrivono
 * i placeholder del child theme.
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

/**
 * Registra tutti gli shortcode del plugin.
 */
class ALM_Shortcodes {

    /**
     * Registra gli shortcode WordPress.
     */
    public function register(): void {
        add_shortcode('alm_booking_form',         [$this, 'booking_form']);
        add_shortcode('alm_availability_calendar', [$this, 'availability_calendar']);
        add_shortcode('alm_room_price',            [$this, 'room_price']);
        // alm_rooms_grid è gestito dal child theme
    }

    /**
     * [alm_booking_form]
     * Renderizza il wizard di prenotazione a 4 step.
     *
     * @param array<string, string>|string $atts
     * @return string
     */
    public function booking_form(array|string $atts = []): string {
        $view = ALM_BOOKING_PATH . 'public/views/booking-form.php';
        if (!file_exists($view)) {
            return '';
        }
        ob_start();
        include $view;
        return (string) ob_get_clean();
    }

    /**
     * [alm_availability_calendar room_id=""]
     * Calendario disponibilità per singola camera.
     *
     * @param array<string, string>|string $atts
     * @return string
     */
    public function availability_calendar(array|string $atts = []): string {
        $atts    = shortcode_atts(['room_id' => ''], is_array($atts) ? $atts : []);
        $room_id = absint($atts['room_id']);
        if (!$room_id) {
            return '';
        }

        ob_start();
        ?>
        <div
            class="availability-calendar alm-js-calendar"
            data-room-id="<?php echo esc_attr((string) $room_id); ?>"
            aria-label="<?php esc_attr_e('Calendario disponibilità', 'almaretna-booking'); ?>"
        >
            <div class="availability-calendar__header">
                <button
                    class="availability-calendar__nav"
                    data-dir="prev"
                    aria-label="<?php esc_attr_e('Mese precedente', 'almaretna-booking'); ?>"
                >&lsaquo;</button>
                <span class="availability-calendar__month-title" data-js="cal-title">
                    <?php echo esc_html(date_i18n('F Y')); ?>
                </span>
                <button
                    class="availability-calendar__nav"
                    data-dir="next"
                    aria-label="<?php esc_attr_e('Mese successivo', 'almaretna-booking'); ?>"
                >&rsaquo;</button>
            </div>
            <div class="availability-calendar__grid" data-js="cal-grid">
                <div class="availability-calendar__loading" style="grid-column:1/-1;text-align:center;padding:2rem;">
                    <span class="spinner"></span>
                </div>
            </div>
            <div style="padding:.75rem 1rem;font-size:.75rem;display:flex;gap:1rem;flex-wrap:wrap;">
                <span style="display:flex;align-items:center;gap:.4rem;">
                    <span style="width:12px;height:12px;border-radius:2px;background:#e8f5e9;display:inline-block;"></span>
                    <?php esc_html_e('Disponibile', 'almaretna-booking'); ?>
                </span>
                <span style="display:flex;align-items:center;gap:.4rem;">
                    <span style="width:12px;height:12px;border-radius:2px;background:#ffebee;display:inline-block;"></span>
                    <?php esc_html_e('Occupata', 'almaretna-booking'); ?>
                </span>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * [alm_room_price room_id=""]
     * Box prezzo con mini-form date per singola camera.
     *
     * @param array<string, string>|string $atts
     * @return string
     */
    public function room_price(array|string $atts = []): string {
        $atts    = shortcode_atts(['room_id' => ''], is_array($atts) ? $atts : []);
        $room_id = absint($atts['room_id']);

        if (!$room_id) {
            return '';
        }

        try {
            $room = new ALM_Room($room_id);
        } catch (InvalidArgumentException) {
            return '';
        }

        $book_url = get_permalink(get_page_by_path('prenota')) ?: home_url('/prenota/');

        ob_start();
        ?>
        <div class="room-price-box" data-room-id="<?php echo esc_attr((string) $room_id); ?>">

            <div class="room-price-box__capacita">
                <span class="dashicons dashicons-admin-users"></span>
                <?php
                printf(
                    esc_html(_n('Max %d adulto', 'Max %d adulti', $room->capacity_adults, 'almaretna-booking')),
                    $room->capacity_adults
                );
                if ($room->capacity_children > 0) {
                    echo ' + ' . esc_html((string) $room->capacity_children) . ' ' .
                         esc_html__('bambini', 'almaretna-booking');
                }
                ?>
            </div>

            <div class="room-price-box__price">
                <span class="room-price-box__amount" data-js="price-display">
                    &euro;&nbsp;<?php echo esc_html(number_format($room->base_price, 2, ',', '.')); ?>
                </span>
                <span class="room-price-box__per-night">
                    <?php esc_html_e('/ notte', 'almaretna-booking'); ?>
                </span>
            </div>

            <?php if ($room->min_stay > 1) : ?>
                <p style="font-size:var(--fs-xs);color:var(--color-text-light);margin-bottom:var(--space-md);">
                    <?php
                    printf(
                        esc_html__('Soggiorno minimo: %d notti', 'almaretna-booking'),
                        $room->min_stay
                    );
                    ?>
                </p>
            <?php endif; ?>

            <div class="room-price-box__form">
                <div class="form-group">
                    <label for="rpb-checkin-<?php echo esc_attr((string) $room_id); ?>">
                        <?php esc_html_e('Check-in', 'almaretna-booking'); ?>
                    </label>
                    <input
                        type="text"
                        id="rpb-checkin-<?php echo esc_attr((string) $room_id); ?>"
                        class="form-control alm-datepicker-checkin"
                        placeholder="<?php esc_attr_e('gg/mm/aaaa', 'almaretna-booking'); ?>"
                        readonly
                        data-room-id="<?php echo esc_attr((string) $room_id); ?>"
                    />
                </div>
                <div class="form-group">
                    <label for="rpb-checkout-<?php echo esc_attr((string) $room_id); ?>">
                        <?php esc_html_e('Check-out', 'almaretna-booking'); ?>
                    </label>
                    <input
                        type="text"
                        id="rpb-checkout-<?php echo esc_attr((string) $room_id); ?>"
                        class="form-control alm-datepicker-checkout"
                        placeholder="<?php esc_attr_e('gg/mm/aaaa', 'almaretna-booking'); ?>"
                        readonly
                        data-room-id="<?php echo esc_attr((string) $room_id); ?>"
                    />
                </div>
                <p id="rpb-price-preview-<?php echo esc_attr((string) $room_id); ?>"
                   class="room-price-box__info" style="display:none;">
                </p>
                <a
                    href="<?php echo esc_url($book_url . '?room=' . $room_id); ?>"
                    class="btn btn-primary btn-lg"
                    id="rpb-btn-<?php echo esc_attr((string) $room_id); ?>"
                >
                    <?php esc_html_e('Prenota ora', 'almaretna-booking'); ?>
                </a>
            </div>

            <p class="room-price-box__info">
                <span class="dashicons dashicons-lock" style="font-size:14px;vertical-align:middle;"></span>
                <?php esc_html_e('Pagamento sicuro. Nessun costo extra.', 'almaretna-booking'); ?>
            </p>

        </div>
        <?php
        return (string) ob_get_clean();
    }
}
