<?php
/**
 * Admin view — Dashboard
 *
 * Variabili disponibili: $total_bookings, $pending, $this_month_rev,
 * $checkins_today, $checkouts_today, $recent_bookings, $beds24_ok, $stripe_ok
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$rev_display = '€ ' . number_format($this_month_rev, 2, ',', '.');
?>

<div class="wrap alm-admin-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-calendar-alt"></span>
        <?php esc_html_e('Almaretna Booking — Dashboard', 'almaretna-booking'); ?>
    </h1>

    <?php if (!empty($_GET['synced'])) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Sincronizzazione Beds24 completata.', 'almaretna-booking'); ?></p></div>
    <?php endif; ?>

    <!-- KPI cards -->
    <div class="alm-kpi-grid">

        <div class="alm-kpi-card">
            <div class="alm-kpi-card__icon dashicons dashicons-calendar"></div>
            <div class="alm-kpi-card__body">
                <div class="alm-kpi-card__value"><?php echo esc_html((string) $total_bookings); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Prenotazioni totali', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card alm-kpi-card--warning">
            <div class="alm-kpi-card__icon dashicons dashicons-clock"></div>
            <div class="alm-kpi-card__body">
                <div class="alm-kpi-card__value"><?php echo esc_html((string) $pending); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('In attesa', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card alm-kpi-card--success">
            <div class="alm-kpi-card__icon dashicons dashicons-chart-line"></div>
            <div class="alm-kpi-card__body">
                <div class="alm-kpi-card__value"><?php echo esc_html($rev_display); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Fatturato mese corrente', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card">
            <div class="alm-kpi-card__icon dashicons dashicons-admin-home"></div>
            <div class="alm-kpi-card__body">
                <div class="alm-kpi-card__value"><?php echo esc_html((string) $checkins_today); ?> / <?php echo esc_html((string) $checkouts_today); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Check-in / Check-out oggi', 'almaretna-booking'); ?></div>
            </div>
        </div>

    </div><!-- /.alm-kpi-grid -->

    <div class="alm-admin-cols">

        <!-- Prenotazioni recenti -->
        <div class="alm-admin-col--main">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Prenotazioni recenti', 'almaretna-booking'); ?></h2>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings')); ?>" class="button button-small">
                        <?php esc_html_e('Vedi tutte', 'almaretna-booking'); ?>
                    </a>
                </div>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Rif.', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Ospite', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Camera', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Check-in', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Check-out', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Totale', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Stato', 'almaretna-booking'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($recent_bookings)) : ?>
                        <tr><td colspan="7" style="text-align:center;"><?php esc_html_e('Nessuna prenotazione.', 'almaretna-booking'); ?></td></tr>
                    <?php else : ?>
                        <?php foreach ($recent_bookings as $b) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings&booking_id=' . $b->id)); ?>">
                                    <?php echo esc_html($b->booking_ref); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html($b->guest_name); ?></td>
                            <td><?php
                                $room = $b->room_id ? ALM_Room::get($b->room_id) : null;
                                echo $room ? esc_html($room->title) : '—';
                            ?></td>
                            <td><?php echo esc_html((new DateTime($b->checkin_date))->format('d/m/Y')); ?></td>
                            <td><?php echo esc_html((new DateTime($b->checkout_date))->format('d/m/Y')); ?></td>
                            <td>€ <?php echo esc_html(number_format($b->total_price, 2, ',', '.')); ?></td>
                            <td><?php echo alm_booking_status_badge($b->status); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div><!-- /.alm-admin-col--main -->

        <!-- Stato integrazioni -->
        <div class="alm-admin-col--side">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Integrazioni', 'almaretna-booking'); ?></h2>
                </div>
                <ul class="alm-status-list">
                    <li class="alm-status-list__item <?php echo $stripe_ok ? 'is-ok' : 'is-error'; ?>">
                        <span class="dashicons <?php echo $stripe_ok ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                        Stripe
                        <?php if ($stripe_ok) : ?>
                            <span class="alm-badge alm-badge--<?php echo ALM_Stripe::is_test_mode() ? 'warning' : 'success'; ?>">
                                <?php echo ALM_Stripe::is_test_mode() ? 'TEST' : 'LIVE'; ?>
                            </span>
                        <?php else : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=alm-settings')); ?>"><?php esc_html_e('Configura', 'almaretna-booking'); ?></a>
                        <?php endif; ?>
                    </li>
                    <li class="alm-status-list__item <?php echo $beds24_ok ? 'is-ok' : 'is-error'; ?>">
                        <span class="dashicons <?php echo $beds24_ok ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
                        Beds24
                        <?php if (!$beds24_ok) : ?>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=alm-settings')); ?>"><?php esc_html_e('Configura', 'almaretna-booking'); ?></a>
                        <?php endif; ?>
                    </li>
                </ul>

                <?php if ($beds24_ok) : ?>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:16px;">
                    <?php wp_nonce_field('alm_sync_beds24'); ?>
                    <input type="hidden" name="action" value="alm_sync_beds24" />
                    <button type="submit" class="button button-secondary" onclick="return confirm('<?php esc_attr_e('Avviare la sincronizzazione con Beds24?', 'almaretna-booking'); ?>')">
                        <span class="dashicons dashicons-update" style="vertical-align:middle;"></span>
                        <?php esc_html_e('Sync Beds24 ora', 'almaretna-booking'); ?>
                    </button>
                </form>
                <?php endif; ?>
            </div>

            <div class="alm-admin-card" style="margin-top:16px;">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Accesso rapido', 'almaretna-booking'); ?></h2>
                </div>
                <ul style="margin:0;padding:0;list-style:none;">
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings')); ?>" class="alm-quick-link">
                        <span class="dashicons dashicons-list-view"></span> <?php esc_html_e('Lista prenotazioni', 'almaretna-booking'); ?>
                    </a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=alm-availability')); ?>" class="alm-quick-link">
                        <span class="dashicons dashicons-calendar-alt"></span> <?php esc_html_e('Gestisci disponibilità', 'almaretna-booking'); ?>
                    </a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=alm-rates')); ?>" class="alm-quick-link">
                        <span class="dashicons dashicons-tag"></span> <?php esc_html_e('Tariffe', 'almaretna-booking'); ?>
                    </a></li>
                    <li><a href="<?php echo esc_url(admin_url('admin.php?page=alm-reports')); ?>" class="alm-quick-link">
                        <span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e('Report', 'almaretna-booking'); ?>
                    </a></li>
                    <li><a href="<?php echo esc_url(get_permalink(get_page_by_path('prenota'))); ?>" target="_blank" class="alm-quick-link">
                        <span class="dashicons dashicons-external"></span> <?php esc_html_e('Vedi pagina prenotazione', 'almaretna-booking'); ?>
                    </a></li>
                </ul>
            </div>
        </div><!-- /.alm-admin-col--side -->

    </div><!-- /.alm-admin-cols -->
</div>

<?php
/**
 * Helper: restituisce il badge HTML per lo stato prenotazione.
 */
function alm_booking_status_badge(string $status): string {
    $map = [
        'confirmed' => ['label' => 'Confermata', 'class' => 'success'],
        'pending'   => ['label' => 'In attesa',  'class' => 'warning'],
        'cancelled' => ['label' => 'Annullata',  'class' => 'error'],
    ];
    $cfg = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'secondary'];
    return '<span class="alm-badge alm-badge--' . esc_attr($cfg['class']) . '">' . esc_html($cfg['label']) . '</span>';
}
