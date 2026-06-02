<?php
/**
 * Admin view — Lista prenotazioni
 *
 * Variabili: $bookings (array di ALM_Booking), $filters (array)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

if (!function_exists('alm_booking_status_badge')) {
    function alm_booking_status_badge(string $status): string {
        $map = [
            'confirmed' => ['label' => 'Confermata', 'class' => 'success'],
            'pending'   => ['label' => 'In attesa',  'class' => 'warning'],
            'cancelled' => ['label' => 'Annullata',  'class' => 'error'],
        ];
        $cfg = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'secondary'];
        return '<span class="alm-badge alm-badge--' . esc_attr($cfg['class']) . '">' . esc_html($cfg['label']) . '</span>';
    }
}

$channel_labels = ['direct' => 'Diretto', 'booking' => 'Booking.com', 'airbnb' => 'Airbnb'];
?>

<div class="wrap alm-admin-wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-list-view"></span>
        <?php esc_html_e('Prenotazioni', 'almaretna-booking'); ?>
    </h1>

    <?php if (!empty($_GET['cancelled'])) : ?>
        <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Prenotazione annullata.', 'almaretna-booking'); ?></p></div>
    <?php endif; ?>

    <!-- Filtri -->
    <form method="get" class="alm-filter-bar">
        <input type="hidden" name="page" value="alm-bookings" />

        <select name="status" class="alm-filter-select">
            <option value=""><?php esc_html_e('Tutti gli stati', 'almaretna-booking'); ?></option>
            <option value="confirmed" <?php selected($filters['status'], 'confirmed'); ?>><?php esc_html_e('Confermate', 'almaretna-booking'); ?></option>
            <option value="pending"   <?php selected($filters['status'], 'pending');   ?>><?php esc_html_e('In attesa', 'almaretna-booking'); ?></option>
            <option value="cancelled" <?php selected($filters['status'], 'cancelled'); ?>><?php esc_html_e('Annullate', 'almaretna-booking'); ?></option>
        </select>

        <select name="channel" class="alm-filter-select">
            <option value=""><?php esc_html_e('Tutti i canali', 'almaretna-booking'); ?></option>
            <?php foreach ($channel_labels as $val => $label) : ?>
                <option value="<?php echo esc_attr($val); ?>" <?php selected($filters['channel'], $val); ?>><?php echo esc_html($label); ?></option>
            <?php endforeach; ?>
        </select>

        <input type="date" name="from" value="<?php echo esc_attr($filters['from']); ?>" class="alm-filter-input" placeholder="Dal" />
        <input type="date" name="to"   value="<?php echo esc_attr($filters['to']);   ?>" class="alm-filter-input" placeholder="Al" />

        <input type="text" name="search" value="<?php echo esc_attr($filters['search']); ?>" class="alm-filter-input" placeholder="<?php esc_attr_e('Nome, email, rif…', 'almaretna-booking'); ?>" />

        <button type="submit" class="button"><?php esc_html_e('Filtra', 'almaretna-booking'); ?></button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings')); ?>" class="button button-secondary"><?php esc_html_e('Azzera', 'almaretna-booking'); ?></a>
    </form>

    <table class="widefat striped alm-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Rif.', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Ospite', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Camera', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Check-in', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Check-out', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Notti', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Ospiti', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Totale', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Canale', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Stato', 'almaretna-booking'); ?></th>
                <th><?php esc_html_e('Azioni', 'almaretna-booking'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($bookings)) : ?>
            <tr><td colspan="11" style="text-align:center;padding:32px;">
                <?php esc_html_e('Nessuna prenotazione trovata.', 'almaretna-booking'); ?>
            </td></tr>
        <?php else : ?>
            <?php foreach ($bookings as $b) : ?>
                <?php
                $room = $b->room_id ? ALM_Room::get($b->room_id) : null;
                $channel_label = $channel_labels[$b->channel] ?? ucfirst($b->channel ?? 'diretto');
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings&booking_id=' . $b->id)); ?>">
                            <strong><?php echo esc_html($b->booking_ref); ?></strong>
                        </a>
                    </td>
                    <td>
                        <?php echo esc_html($b->guest_name); ?><br>
                        <small style="color:#666;"><?php echo esc_html($b->guest_email); ?></small>
                    </td>
                    <td><?php echo $room ? esc_html($room->title) : '<em>—</em>'; ?></td>
                    <td><?php echo esc_html((new DateTime($b->checkin_date))->format('d/m/Y')); ?></td>
                    <td><?php echo esc_html((new DateTime($b->checkout_date))->format('d/m/Y')); ?></td>
                    <td><?php echo esc_html((string) $b->nights); ?></td>
                    <td>
                        <?php echo esc_html((string) $b->guest_adults); ?>A
                        <?php if ($b->guest_children > 0) echo ' + ' . esc_html((string) $b->guest_children) . 'B'; ?>
                    </td>
                    <td><strong>€ <?php echo esc_html(number_format($b->total_price, 2, ',', '.')); ?></strong></td>
                    <td><span class="alm-badge alm-badge--secondary"><?php echo esc_html($channel_label); ?></span></td>
                    <td><?php echo alm_booking_status_badge($b->status); ?></td>
                    <td>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings&booking_id=' . $b->id)); ?>" class="button button-small">
                            <?php esc_html_e('Dettaglio', 'almaretna-booking'); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <p style="color:#666;font-size:13px;margin-top:8px;">
        <?php printf(
            esc_html__('%d prenotazioni trovate.', 'almaretna-booking'),
            count($bookings)
        ); ?>
    </p>
</div>
