<?php
/**
 * Admin view — Dettaglio prenotazione
 *
 * Variabili: $booking (ALM_Booking), $room (ALM_Room|null)
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$status_labels = [
    'confirmed' => 'Confermata',
    'pending'   => 'In attesa di pagamento',
    'cancelled' => 'Annullata',
];
$status_label = $status_labels[$booking->status] ?? ucfirst($booking->status ?? '');
$ci = new DateTime($booking->checkin_date);
$co = new DateTime($booking->checkout_date);
?>

<div class="wrap alm-admin-wrap">
    <h1>
        <a href="<?php echo esc_url(admin_url('admin.php?page=alm-bookings')); ?>" class="page-title-action">
            &larr; <?php esc_html_e('Tutte le prenotazioni', 'almaretna-booking'); ?>
        </a>
        <?php echo esc_html($booking->booking_ref); ?>
    </h1>

    <div class="alm-admin-cols">

        <!-- Info principale -->
        <div class="alm-admin-col--main">

            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Dettagli soggiorno', 'almaretna-booking'); ?></h2>
                    <span><?php
                        $badge_class = match ($booking->status) {
                            'confirmed' => 'success',
                            'cancelled' => 'error',
                            default     => 'warning',
                        };
                        printf('<span class="alm-badge alm-badge--%s">%s</span>', esc_attr($badge_class), esc_html($status_label));
                    ?></span>
                </div>
                <table class="alm-detail-table">
                    <tr><th><?php esc_html_e('Camera', 'almaretna-booking'); ?></th>
                        <td><?php echo $room ? esc_html($room->title) : '—'; ?>
                            <?php if ($room) : ?>
                                <a href="<?php echo esc_url(get_permalink($room->id)); ?>" target="_blank" style="margin-left:8px;">
                                    <span class="dashicons dashicons-external" style="font-size:14px;"></span>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr><th><?php esc_html_e('Check-in', 'almaretna-booking'); ?></th>
                        <td><?php echo esc_html($ci->format('d/m/Y')); ?> — dalle ore 15:00</td>
                    </tr>
                    <tr><th><?php esc_html_e('Check-out', 'almaretna-booking'); ?></th>
                        <td><?php echo esc_html($co->format('d/m/Y')); ?> — entro le 11:00</td>
                    </tr>
                    <tr><th><?php esc_html_e('Durata', 'almaretna-booking'); ?></th>
                        <td><?php echo esc_html((string) $booking->nights); ?> notti</td>
                    </tr>
                    <tr><th><?php esc_html_e('Ospiti', 'almaretna-booking'); ?></th>
                        <td>
                            <?php echo esc_html((string) $booking->guest_adults); ?> adulti
                            <?php if ($booking->guest_children > 0) echo ' + ' . esc_html((string) $booking->guest_children) . ' bambini'; ?>
                        </td>
                    </tr>
                    <tr><th><?php esc_html_e('Canale', 'almaretna-booking'); ?></th>
                        <td><?php echo esc_html(ucfirst($booking->channel ?? 'diretto')); ?></td>
                    </tr>
                    <?php if (!empty($booking->external_id)) : ?>
                    <tr><th>Beds24 ID</th>
                        <td><?php echo esc_html((string) $booking->external_id); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($booking->payment_ref)) : ?>
                    <tr><th>Stripe ID</th>
                        <td><code><?php echo esc_html($booking->payment_ref); ?></code></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="alm-admin-card" style="margin-top:16px;">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Dati ospite', 'almaretna-booking'); ?></h2>
                </div>
                <table class="alm-detail-table">
                    <tr><th><?php esc_html_e('Nome', 'almaretna-booking'); ?></th>
                        <td><?php echo esc_html($booking->guest_name); ?></td>
                    </tr>
                    <tr><th><?php esc_html_e('Email', 'almaretna-booking'); ?></th>
                        <td><a href="mailto:<?php echo esc_attr($booking->guest_email); ?>"><?php echo esc_html($booking->guest_email); ?></a></td>
                    </tr>
                    <tr><th><?php esc_html_e('Telefono', 'almaretna-booking'); ?></th>
                        <td><a href="tel:<?php echo esc_attr($booking->guest_phone ?? ''); ?>"><?php echo esc_html($booking->guest_phone ?? '—'); ?></a></td>
                    </tr>
                    <?php if (!empty($booking->special_requests)) : ?>
                    <tr><th><?php esc_html_e('Richieste speciali', 'almaretna-booking'); ?></th>
                        <td><?php echo nl2br(esc_html($booking->special_requests)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($booking->notes)) : ?>
                    <tr><th><?php esc_html_e('Note interne', 'almaretna-booking'); ?></th>
                        <td><?php echo nl2br(esc_html($booking->notes)); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

        </div><!-- /.alm-admin-col--main -->

        <!-- Sidebar azioni -->
        <div class="alm-admin-col--side">

            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Importo', 'almaretna-booking'); ?></h2>
                </div>
                <div style="font-size:28px;font-weight:bold;color:#1A4E6B;text-align:center;padding:16px 0;">
                    € <?php echo esc_html(number_format($booking->total_price, 2, ',', '.')); ?>
                </div>
                <p style="text-align:center;color:#666;font-size:13px;margin:0;">
                    <?php echo esc_html($booking->nights . ' notti × camera'); ?>
                </p>
            </div>

            <?php if ($booking->status !== 'cancelled') : ?>
            <div class="alm-admin-card" style="margin-top:16px;">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Azioni', 'almaretna-booking'); ?></h2>
                </div>

                <!-- Cancella prenotazione -->
                <details style="margin-bottom:12px;">
                    <summary style="cursor:pointer;color:#c62828;font-weight:500;">
                        <span class="dashicons dashicons-no" style="vertical-align:middle;"></span>
                        <?php esc_html_e('Annulla prenotazione', 'almaretna-booking'); ?>
                    </summary>
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="margin-top:12px;">
                        <?php wp_nonce_field('alm_cancel_booking'); ?>
                        <input type="hidden" name="action"     value="alm_cancel_booking" />
                        <input type="hidden" name="booking_id" value="<?php echo esc_attr((string) $booking->id); ?>" />
                        <div class="form-field">
                            <label for="cancel-reason"><?php esc_html_e('Motivo (opzionale):', 'almaretna-booking'); ?></label>
                            <textarea id="cancel-reason" name="reason" rows="2" class="widefat"></textarea>
                        </div>
                        <button type="submit" class="button button-link-delete" style="margin-top:8px;"
                                onclick="return confirm('<?php esc_attr_e('Confermi la cancellazione? Verrà inviata email al cliente.', 'almaretna-booking'); ?>')">
                            <?php esc_html_e('Conferma annullamento', 'almaretna-booking'); ?>
                        </button>
                    </form>
                </details>

                <!-- Note interne -->
                <details>
                    <summary style="cursor:pointer;font-weight:500;">
                        <span class="dashicons dashicons-edit" style="vertical-align:middle;"></span>
                        <?php esc_html_e('Modifica note interne', 'almaretna-booking'); ?>
                    </summary>
                    <form method="post" style="margin-top:12px;">
                        <p>
                            <label><?php esc_html_e('Note (visibili solo admin):', 'almaretna-booking'); ?></label>
                            <textarea name="notes" rows="3" class="widefat"><?php echo esc_textarea($booking->notes ?? ''); ?></textarea>
                        </p>
                        <button type="submit" class="button"><?php esc_html_e('Salva note', 'almaretna-booking'); ?></button>
                    </form>
                </details>
            </div>
            <?php endif; ?>

            <div class="alm-admin-card" style="margin-top:16px;">
                <p style="margin:0;font-size:12px;color:#666;">
                    <?php esc_html_e('Creata il:', 'almaretna-booking'); ?>
                    <?php echo esc_html(!empty($booking->created_at)
                        ? (new DateTime($booking->created_at))->format('d/m/Y H:i')
                        : '—'
                    ); ?>
                </p>
                <?php if (!empty($booking->updated_at)) : ?>
                <p style="margin:4px 0 0;font-size:12px;color:#666;">
                    <?php esc_html_e('Aggiornata il:', 'almaretna-booking'); ?>
                    <?php echo esc_html((new DateTime($booking->updated_at))->format('d/m/Y H:i')); ?>
                </p>
                <?php endif; ?>
            </div>

        </div><!-- /.alm-admin-col--side -->

    </div>
</div>
