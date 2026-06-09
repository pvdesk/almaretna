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
        add_shortcode('alm_my_bookings',           [$this, 'my_bookings']);
        add_shortcode('alm_login_form',            [$this, 'login_form']);
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
                    esc_html($room->capacity_adults === 1 ? alm_t('room.adults_1') : alm_t('room.adults_n')),
                    $room->capacity_adults
                );
                if ($room->capacity_children > 0) {
                    echo ' + ' . esc_html((string) $room->capacity_children) . ' ' .
                         esc_html(alm_t('sc.children'));
                }
                ?>
            </div>

            <div class="room-price-box__price">
                <span class="room-price-box__amount" data-js="price-display">
                    &euro;&nbsp;<?php echo esc_html(number_format($room->base_price, 2, ',', '.')); ?>
                </span>
                <span class="room-price-box__per-night">
                    <?php echo esc_html(alm_t('rooms.night')); ?>
                </span>
            </div>

            <?php if ($room->min_stay > 1) : ?>
                <p style="font-size:var(--fs-xs);color:var(--color-text-light);margin-bottom:var(--space-md);">
                    <?php
                    printf(
                        esc_html(alm_t('sc.min_stay')),
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
                        placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
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
                        placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
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
                    <?php echo esc_html(alm_t('nav.book_now')); ?>
                </a>
            </div>

            <p class="room-price-box__info">
                <span class="dashicons dashicons-lock" style="font-size:14px;vertical-align:middle;"></span>
                <?php echo esc_html(alm_t('sc.no_extra')); ?>
            </p>

        </div>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * [alm_my_bookings]
     * Area personale ospite: lista delle prenotazioni dell'utente loggato.
     *
     * @param array<string, string>|string $atts
     * @return string
     */
    public function my_bookings(array|string $atts = []): string {
        global $wpdb;

        ob_start();

        $css = '
<style>
.alm-my-bookings{font-family:Arial,Helvetica,sans-serif;color:#1A1714;max-width:900px;margin:0 auto;}
.alm-my-bookings__title{font-family:Georgia,"Times New Roman",serif;font-size:24px;color:#3B2F22;margin:0 0 24px;letter-spacing:1px;}
.alm-my-bookings__login-wrap{background:#FAF8F5;border:1px solid #EFEBE4;border-top:3px solid #D4A96A;border-radius:4px;padding:32px;max-width:480px;margin:0 auto;}
.alm-my-bookings__login-msg{font-size:15px;color:#3B2F22;margin:0 0 20px;line-height:1.6;}
.alm-my-bookings__empty{background:#FAF8F5;border:1px solid #EFEBE4;border-radius:4px;padding:40px;text-align:center;color:#8A7B68;font-size:15px;}
.alm-booking-card{background:#FAF8F5;border:1px solid #EFEBE4;border-radius:4px;margin:0 0 16px;overflow:hidden;box-shadow:0 1px 4px rgba(59,47,34,.06);}
.alm-booking-card__header{background:#3B2F22;padding:14px 24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;}
.alm-booking-card__ref{font-family:Georgia,"Times New Roman",serif;font-size:15px;color:#FAF8F5;letter-spacing:1px;}
.alm-booking-card__body{padding:20px 24px;}
.alm-booking-card__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;margin:0 0 12px;}
.alm-booking-card__item-label{font-size:11px;letter-spacing:1px;text-transform:uppercase;color:#8A7B68;margin:0 0 4px;}
.alm-booking-card__item-value{font-size:14px;color:#1A1714;font-weight:bold;margin:0;}
.alm-booking-card__item-value--total{font-family:Georgia,"Times New Roman",serif;font-size:18px;color:#D4A96A;}
.alm-status-badge{display:inline-block;font-size:11px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;padding:4px 12px;border-radius:2px;}
.alm-status-badge--confirmed{background:#D4A96A;color:#3B2F22;}
.alm-status-badge--pending{background:#C5B49C;color:#3B2F22;}
.alm-status-badge--cancelled{background:#8A7B68;color:#FAF8F5;}
.alm-status-badge--completed{background:#3B2F22;color:#D4A96A;}
.alm-status-badge--no_show{background:#1A1714;color:#C5B49C;}
</style>';

        if (!is_user_logged_in()) {
            $dashboard_page = get_page_by_path('le-mie-prenotazioni');
            $redirect       = $dashboard_page ? get_permalink($dashboard_page) : home_url('/le-mie-prenotazioni/');

            echo $css; // phpcs:ignore WordPress.Security.EscapeOutput
            ?>
            <div class="alm-my-bookings">
              <div class="alm-my-bookings__login-wrap">
                <p class="alm-my-bookings__login-msg">
                  Accedi per visualizzare le tue prenotazioni.
                </p>
                <?php
                wp_login_form([
                    'redirect'       => esc_url($redirect),
                    'form_id'        => 'alm-login-form',
                    'label_username' => __('Email o username', 'almaretna-booking'),
                    'label_password' => __('Password', 'almaretna-booking'),
                    'label_log_in'   => __('Accedi', 'almaretna-booking'),
                ]);
                ?>
              </div>
            </div>
            <?php
            return (string) ob_get_clean();
        }

        $current_user_id = (int) get_current_user_id();

        $bookings = $wpdb->get_results(  // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}alm_bookings
                 WHERE user_id = %d
                 ORDER BY checkin_date DESC",
                $current_user_id
            )
        );

        $status_labels = [
            'pending'   => 'In attesa',
            'confirmed' => 'Confermata',
            'cancelled' => 'Annullata',
            'completed' => 'Completata',
            'no_show'   => 'No Show',
        ];

        echo $css; // phpcs:ignore WordPress.Security.EscapeOutput
        ?>
        <div class="alm-my-bookings">
          <h2 class="alm-my-bookings__title">Le mie prenotazioni</h2>

          <?php if (empty($bookings)) : ?>
            <div class="alm-my-bookings__empty">
              Nessuna prenotazione trovata.
            </div>
          <?php else : ?>
            <?php foreach ($bookings as $row) :
                $ci = DateTime::createFromFormat('Y-m-d', $row->checkin_date);
                $co = DateTime::createFromFormat('Y-m-d', $row->checkout_date);
                $ci_display = $ci ? $ci->format('d/m/Y') : esc_html($row->checkin_date);
                $co_display = $co ? $co->format('d/m/Y') : esc_html($row->checkout_date);
                $room_title = get_the_title((int) $row->room_id) ?: '—';
                $status     = esc_attr($row->status);
                $status_label = $status_labels[$row->status] ?? ucfirst($row->status);
                $total_display = '&euro;&nbsp;' . number_format((float) $row->total_price + (float) $row->extras_total, 2, ',', '.');
            ?>
              <div class="alm-booking-card">
                <div class="alm-booking-card__header">
                  <span class="alm-booking-card__ref"><?php echo esc_html($row->booking_ref); ?></span>
                  <span class="alm-status-badge alm-status-badge--<?php echo esc_attr($status); ?>">
                    <?php echo esc_html($status_label); ?>
                  </span>
                </div>
                <div class="alm-booking-card__body">
                  <div class="alm-booking-card__grid">
                    <div>
                      <p class="alm-booking-card__item-label">Camera</p>
                      <p class="alm-booking-card__item-value"><?php echo esc_html($room_title); ?></p>
                    </div>
                    <div>
                      <p class="alm-booking-card__item-label">Check-in</p>
                      <p class="alm-booking-card__item-value"><?php echo esc_html($ci_display); ?></p>
                    </div>
                    <div>
                      <p class="alm-booking-card__item-label">Check-out</p>
                      <p class="alm-booking-card__item-value"><?php echo esc_html($co_display); ?></p>
                    </div>
                    <div>
                      <p class="alm-booking-card__item-label">Notti</p>
                      <p class="alm-booking-card__item-value"><?php echo esc_html((string) $row->nights); ?></p>
                    </div>
                    <div>
                      <p class="alm-booking-card__item-label">Totale</p>
                      <p class="alm-booking-card__item-value alm-booking-card__item-value--total"><?php echo $total_display; // phpcs:ignore WordPress.Security.EscapeOutput ?></p>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
        <?php
        return (string) ob_get_clean();
    }

    /**
     * [alm_login_form]
     * Form di login WordPress stilizzato per il tema Almaretna.
     *
     * @param array<string, string>|string $atts
     * @return string
     */
    public function login_form(array|string $atts = []): string {
        if (is_user_logged_in()) {
            return '';
        }

        $dashboard_page = get_page_by_path('le-mie-prenotazioni');
        $redirect       = $dashboard_page ? get_permalink($dashboard_page) : home_url('/le-mie-prenotazioni/');

        ob_start();
        ?>
        <div style="max-width:480px;margin:0 auto;background:#FAF8F5;border:1px solid #EFEBE4;border-top:3px solid #D4A96A;border-radius:4px;padding:32px;font-family:Arial,Helvetica,sans-serif;">
          <?php
          wp_login_form([
              'redirect'       => esc_url((string) $redirect),
              'form_id'        => 'alm-login-form-sc',
              'label_username' => __('Email o username', 'almaretna-booking'),
              'label_password' => __('Password', 'almaretna-booking'),
              'label_log_in'   => __('Accedi', 'almaretna-booking'),
          ]);
          ?>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}
