<?php
/**
 * Admin view — Gestione disponibilità e blocchi
 *
 * Variabili: $rooms (array di ALM_Room)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$selected_room_id = isset($_GET['room_id']) ? absint($_GET['room_id']) : 0;
$selected_room    = $selected_room_id
    ? array_filter($rooms, fn($r) => $r->id === $selected_room_id)
    : [];
$selected_room    = !empty($selected_room) ? array_values($selected_room)[0] : null;

// Blocchi attivi per la camera selezionata
$blocks = [];
if ($selected_room_id) {
    global $wpdb;
    $blocks = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}alm_blocks WHERE room_id = %d AND date_to >= %s ORDER BY date_from",
            $selected_room_id, gmdate('Y-m-d')
        ),
        ARRAY_A
    ) ?: [];
}
?>

<div class="wrap alm-admin-wrap">
    <h1>
        <span class="dashicons dashicons-calendar-alt"></span>
        <?php esc_html_e('Gestione disponibilità', 'almaretna-booking'); ?>
    </h1>

    <?php if (!empty($_GET['blocked']))   : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Date bloccate.', 'almaretna-booking'); ?></p></div><?php endif; ?>
    <?php if (!empty($_GET['unblocked'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Blocco rimosso.', 'almaretna-booking'); ?></p></div><?php endif; ?>

    <!-- Seleziona camera -->
    <div class="alm-admin-card" style="margin-bottom:16px;">
        <form method="get" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
            <input type="hidden" name="page" value="alm-availability" />
            <label style="font-weight:600;"><?php esc_html_e('Camera:', 'almaretna-booking'); ?></label>
            <select name="room_id" class="alm-filter-select" onchange="this.form.submit()">
                <option value=""><?php esc_html_e('— Seleziona camera —', 'almaretna-booking'); ?></option>
                <?php foreach ($rooms as $room) : ?>
                    <option value="<?php echo esc_attr((string) $room->id); ?>" <?php selected($selected_room_id, $room->id); ?>>
                        <?php echo esc_html($room->title . ' (' . $room->room_id . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($selected_room) : ?>

    <div class="alm-admin-cols">

        <!-- Blocchi attivi -->
        <div class="alm-admin-col--main">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php printf(esc_html__('Blocchi manuali — %s', 'almaretna-booking'), esc_html($selected_room->title)); ?></h2>
                </div>

                <?php if (empty($blocks)) : ?>
                    <p style="color:#666;padding:8px 0;"><?php esc_html_e('Nessun blocco attivo.', 'almaretna-booking'); ?></p>
                <?php else : ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Dal', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Al', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Motivo', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Azioni', 'almaretna-booking'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($blocks as $block) : ?>
                        <tr>
                            <td><?php echo esc_html((new DateTime($block['date_from']))->format('d/m/Y')); ?></td>
                            <td><?php echo esc_html((new DateTime($block['date_to']))->format('d/m/Y')); ?></td>
                            <td><?php echo esc_html($block['reason'] ?? '—'); ?></td>
                            <td>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                                    <?php wp_nonce_field('alm_remove_block'); ?>
                                    <input type="hidden" name="action"   value="alm_remove_block" />
                                    <input type="hidden" name="block_id" value="<?php echo esc_attr((string) $block['id']); ?>" />
                                    <input type="hidden" name="room_id"  value="<?php echo esc_attr((string) $selected_room_id); ?>" />
                                    <button type="submit" class="button button-small button-link-delete"
                                            onclick="return confirm('<?php esc_attr_e('Rimuovere il blocco?', 'almaretna-booking'); ?>')">
                                        <?php esc_html_e('Rimuovi', 'almaretna-booking'); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form nuovo blocco -->
        <div class="alm-admin-col--side">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Aggiungi blocco', 'almaretna-booking'); ?></h2>
                </div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('alm_add_block'); ?>
                    <input type="hidden" name="action"  value="alm_add_block" />
                    <input type="hidden" name="room_id" value="<?php echo esc_attr((string) $selected_room_id); ?>" />

                    <div class="form-field">
                        <label for="block-start"><?php esc_html_e('Dal (incluso):', 'almaretna-booking'); ?></label>
                        <input type="date" id="block-start" name="start_date" class="widefat"
                               min="<?php echo esc_attr(gmdate('Y-m-d')); ?>" required />
                    </div>
                    <div class="form-field">
                        <label for="block-end"><?php esc_html_e('Al (escluso check-out):', 'almaretna-booking'); ?></label>
                        <input type="date" id="block-end" name="end_date" class="widefat"
                               min="<?php echo esc_attr(gmdate('Y-m-d', strtotime('+1 day'))); ?>" required />
                    </div>
                    <div class="form-field">
                        <label for="block-reason"><?php esc_html_e('Motivo:', 'almaretna-booking'); ?></label>
                        <input type="text" id="block-reason" name="reason" class="widefat"
                               value="<?php esc_attr_e('Blocco manuale', 'almaretna-booking'); ?>" maxlength="100" />
                    </div>

                    <p class="submit" style="margin-bottom:0;">
                        <button type="submit" class="button button-primary">
                            <span class="dashicons dashicons-lock" style="vertical-align:middle;"></span>
                            <?php esc_html_e('Blocca date', 'almaretna-booking'); ?>
                        </button>
                    </p>
                </form>
            </div>

            <?php if (ALM_Beds24::is_configured()) : ?>
            <div class="alm-admin-card" style="margin-top:16px;">
                <div class="alm-admin-card__header">
                    <h2>Beds24</h2>
                </div>
                <p style="font-size:13px;color:#666;">
                    <?php esc_html_e('I blocchi vengono sincronizzati automaticamente con Beds24 due volte al giorno.', 'almaretna-booking'); ?>
                </p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <?php endif; // $selected_room ?>

</div>
