<?php
/**
 * Admin view — Tariffe
 *
 * Variabili: $rooms, $room_id, $rates
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

// Recupera messaggi di errore o successo
$error_msg = isset($_GET['error_msg']) ? sanitize_text_field(urldecode($_GET['error_msg'])) : '';
$room = $room_id ? ALM_Room::get($room_id) : null;

// Rileva in anticipo le sovrapposizioni di date nel database per questa camera
$overlapping_ids = [];
if ($room_id && !empty($rates)) {
    $rates_count = count($rates);
    for ($i = 0; $i < $rates_count; $i++) {
        for ($j = $i + 1; $j < $rates_count; $j++) {
            $r1 = $rates[$i];
            $r2 = $rates[$j];
            
            $channel_match = ($r1['channel'] === $r2['channel'] || $r1['channel'] === 'all' || $r2['channel'] === 'all');
            if ($channel_match) {
                // Sovrapposizione se: start1 < end2 AND end1 > start2
                $overlap = ($r1['date_from'] < $r2['date_to'] && $r1['date_to'] > $r2['date_from']);
                if ($overlap) {
                    $overlapping_ids[$r1['id']] = true;
                    $overlapping_ids[$r2['id']] = true;
                }
            }
        }
    }
}
?>

<style>
    .alm-room-select-card:hover {
        border-color: #708a72 !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08) !important;
        transform: translateY(-2px);
    }
    .alm-room-select-card {
        cursor: pointer;
        user-select: none;
    }
</style>

<div class="wrap alm-admin-wrap">
    <h1>
        <span class="dashicons dashicons-tag"></span>
        <?php esc_html_e('Gestione tariffe e listino prezzi', 'almaretna-booking'); ?>
    </h1>

    <?php if (!empty($error_msg)) : ?>
        <div class="notice notice-error is-dismissible">
            <p><strong><?php esc_html_e('Errore:', 'almaretna-booking'); ?></strong> <?php echo esc_html($error_msg); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($_GET['updated'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Modifica salvata con successo.', 'almaretna-booking'); ?></p></div><?php endif; ?>
    <?php if (!empty($_GET['deleted'])) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e('Tariffa speciale eliminata.', 'almaretna-booking'); ?></p></div><?php endif; ?>

    <!-- Selezione visuale camere (Griglia Card) -->
    <div class="alm-admin-card" style="margin-bottom: 24px; padding: 20px;">
        <h2 style="font-size: 15px; font-weight:600; margin-top: 0; margin-bottom: 16px; color: #1d2327;">
            <?php esc_html_e('Seleziona una camera per configurare i prezzi:', 'almaretna-booking'); ?>
        </h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
            <?php foreach ($rooms as $r) :
                $is_selected = ($room_id === $r->id);
                // Recupera tariffe per calcolare il conteggio
                $r_rates = ALM_Pricing::get_all_rates_for_room($r->id);
                $rates_count = count($r_rates);
                
                $card_style = $is_selected 
                    ? 'border: 2px solid #708a72; background: #fbfcfb; box-shadow: 0 4px 8px rgba(112, 138, 114, 0.12);' 
                    : 'border: 1px solid #ccd0d4; background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);';
            ?>
                <div class="alm-room-select-card" 
                     style="padding: 16px; border-radius: 10px; <?php echo esc_attr($card_style); ?> transition: all 0.2s ease-in-out; position:relative;"
                     onclick="window.location.href='<?php echo esc_url(admin_url('admin.php?page=alm-rates&room_id=' . $r->id)); ?>'">
                    
                    <?php if ($is_selected) : ?>
                        <span style="position:absolute; top:-10px; right:10px; background:#708a72; color:#fff; font-size:9px; padding: 2px 8px; border-radius: 10px; font-weight:700; text-transform:uppercase;">
                            <?php esc_html_e('Selezionata', 'almaretna-booking'); ?>
                        </span>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px;">
                        <h3 style="margin: 0; font-size: 14px; font-weight: 700; color: #1d2327;">
                            <?php echo esc_html($r->title); ?>
                        </h3>
                        <span style="font-size: 10px; background: #f0f0f1; padding: 2px 6px; border-radius: 4px; color: #50575e; font-weight: 600;">
                            <?php echo esc_html($r->room_id); ?>
                        </span>
                    </div>
                    
                    <div style="font-size: 12px; color: #646970; margin-bottom: 12px;">
                        Piano: <?php echo esc_html(ucfirst(str_replace('-', ' ', $r->floor))); ?> · 
                        <?php echo esc_html($r->bathroom_type === 'private' ? 'Bagno privato' : 'Bagno condiviso'); ?>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f1; padding-top: 10px; margin-top: 4px;">
                        <div>
                            <span style="font-size: 9px; color: #8c8f94; text-transform: uppercase; display:block; letter-spacing:0.02em;">Prezzo standard</span>
                            <strong style="font-size: 14px; color: #1d2327;">€ <?php echo number_format($r->base_price, 2, ',', '.'); ?></strong>
                        </div>
                        
                        <span style="font-size: 10px; font-weight:700; padding: 3px 10px; border-radius: 12px; <?php echo $rates_count > 0 ? 'background: #eef2ef; color: #708a72;' : 'background: #f0f0f1; color: #646970;'; ?>">
                            <?php printf(_n('%d speciale', '%d speciali', $rates_count, 'almaretna-booking'), $rates_count); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($room) : ?>

    <!-- Gestione Prezzo Standard per la Camera Selezionata -->
    <div class="alm-admin-card" style="margin-bottom: 20px; padding: 20px;">
        <div class="alm-admin-card__header" style="border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 14px;">
            <h2 style="margin:0; font-size:15px; font-weight:600;"><?php printf(esc_html__('Prezzo Standard — %s', 'almaretna-booking'), esc_html($room->title)); ?></h2>
        </div>
        <p style="color:#666; font-size:13px; margin-bottom:16px; margin-top:0;">
            <?php esc_html_e('Il prezzo standard (prezzo base) viene applicato per tutte le date dell\'anno in cui non è definita una tariffa speciale/stagionale qui sotto.', 'almaretna-booking'); ?>
        </p>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin:0;">
            <?php wp_nonce_field('alm_save_base_price'); ?>
            <input type="hidden" name="action" value="alm_save_base_price" />
            <input type="hidden" name="room_id" value="<?php echo esc_attr((string) $room_id); ?>" />
            
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-weight:600; font-size:13px; color:#50575e;"><?php esc_html_e('Prezzo standard a notte:', 'almaretna-booking'); ?></label>
                <div style="position:relative; display:inline-block;">
                    <span style="position:absolute; left:10px; top:50%; transform:translateY(-50%); font-weight:600; color:#555;">€</span>
                    <input type="number" name="base_price" value="<?php echo esc_attr((string) $room->base_price); ?>" min="0" step="0.01" style="width:110px; font-size:14px; font-weight:600; padding: 6px 6px 6px 22px; border-radius: 4px; border: 1px solid #8c8f94;" required />
                </div>
            </div>
            
            <button type="submit" class="button button-secondary" style="border-color:#708a72; color:#708a72; padding: 2px 14px; font-weight:600; height:32px;">
                <?php esc_html_e('Aggiorna Prezzo Standard', 'almaretna-booking'); ?>
            </button>
        </form>
    </div>

    <!-- Sezione Dettagli Tariffe Speciali -->
    <div class="alm-admin-cols">

        <!-- Lista tariffe -->
        <div class="alm-admin-col--main">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Tariffe Speciali e Stagionali Configurate', 'almaretna-booking'); ?></h2>
                </div>

                <!-- Notifica in caso di sovrapposizione rilevata -->
                <?php if (!empty($overlapping_ids)) : ?>
                    <div style="margin-bottom:16px; border-left: 4px solid #d94f4f; padding: 12px; background: #fdf2f2; border-radius: 4px; font-size: 13px;">
                        <p style="margin:0; font-weight:600; color:#b71c1c;">
                            <span class="dashicons dashicons-warning" style="vertical-align:middle; margin-right:6px; color:#d94f4f;"></span>
                            <?php esc_html_e('Attenzione: sono state rilevate tariffe con date sovrapposte per lo stesso canale di vendita. Questo causerà conflitti nel calcolo dei prezzi.', 'almaretna-booking'); ?>
                        </p>
                    </div>
                <?php endif; ?>

                <?php if (empty($rates)) : ?>
                    <p style="color:#666;"><?php esc_html_e('Nessuna tariffa speciale configurata. Per questo alloggio viene applicato unicamente il prezzo standard.', 'almaretna-booking'); ?></p>
                <?php else : ?>
                <table class="widefat striped alm-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Nome', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Prezzo / notte', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Dal', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Al', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Canale', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Min. notti', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Stato', 'almaretna-booking'); ?></th>
                            <th><?php esc_html_e('Azioni', 'almaretna-booking'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rates as $rate) :
                        $rid         = (int) $rate['id'];
                        $has_overlap = isset($overlapping_ids[$rid]);
                        $row_style   = $has_overlap ? 'background-color: #fff5f5 !important;' : '';
                        $df          = !empty($rate['date_from']) ? (new DateTime($rate['date_from']))->format('Y-m-d') : '';
                        $dt          = !empty($rate['date_to'])   ? (new DateTime($rate['date_to']))->format('Y-m-d')   : '';
                    ?>
                        <tr id="alm-rate-row-<?php echo $rid; ?>" style="<?php echo esc_attr($row_style); ?>">
                            <td><strong><?php echo esc_html($rate['rate_name'] ?? ''); ?></strong>
                                <?php if ($has_overlap) : ?>
                                    <span style="color:#d94f4f;font-size:10px;font-weight:700;display:block;margin-top:2px;">[<?php esc_html_e('CONFLITTO DATE', 'almaretna-booking'); ?>]</span>
                                <?php endif; ?>
                            </td>
                            <td>€ <?php echo esc_html(number_format((float)($rate['price_per_night'] ?? 0), 2, ',', '.')); ?></td>
                            <td><?php echo esc_html(!empty($rate['date_from']) ? (new DateTime($rate['date_from']))->format('d/m/Y') : '—'); ?></td>
                            <td><?php echo esc_html(!empty($rate['date_to'])   ? (new DateTime($rate['date_to']))->format('d/m/Y')   : '—'); ?></td>
                            <td><span class="alm-badge alm-badge--secondary" style="font-size:11px;"><?php echo esc_html(ucfirst($rate['channel'] ?? 'all')); ?></span></td>
                            <td><?php echo esc_html((string)($rate['min_stay'] ?? 1)); ?> gg</td>
                            <td>
                                <?php if ($has_overlap) : ?>
                                    <span style="color:#d94f4f;font-weight:600;font-size:11px;"><span class="dashicons dashicons-warning" style="font-size:15px;width:15px;height:15px;vertical-align:middle;margin-right:2px;"></span><?php esc_html_e('Sovrapposizione', 'almaretna-booking'); ?></span>
                                <?php else : ?>
                                    <span style="color:#708a72;font-weight:600;font-size:11px;"><span class="dashicons dashicons-yes-alt" style="font-size:15px;width:15px;height:15px;vertical-align:middle;margin-right:2px;"></span><?php esc_html_e('Valida', 'almaretna-booking'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space:nowrap;">
                                <button type="button" class="button button-small"
                                        onclick="almToggleEditRate(<?php echo $rid; ?>)"
                                        style="margin-right:4px;">
                                    ✏ <?php esc_html_e('Modifica', 'almaretna-booking'); ?>
                                </button>
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline;">
                                    <?php wp_nonce_field('alm_delete_rate'); ?>
                                    <input type="hidden" name="action"  value="alm_delete_rate" />
                                    <input type="hidden" name="rate_id" value="<?php echo esc_attr((string)$rid); ?>" />
                                    <input type="hidden" name="room_id" value="<?php echo esc_attr((string)$room_id); ?>" />
                                    <button type="submit" class="button button-small button-link-delete"
                                            onclick="return confirm('<?php esc_attr_e('Eliminare questa tariffa speciale?', 'almaretna-booking'); ?>')">
                                        <?php esc_html_e('Elimina', 'almaretna-booking'); ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <!-- Riga inline di modifica -->
                        <tr id="alm-rate-edit-<?php echo $rid; ?>" style="display:none; background:#f0f4f0;">
                            <td colspan="8" style="padding:16px 20px;">
                                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                                    <?php wp_nonce_field('alm_save_rates'); ?>
                                    <input type="hidden" name="action"  value="alm_save_rates" />
                                    <input type="hidden" name="rate_id" value="<?php echo esc_attr((string)$rid); ?>" />
                                    <input type="hidden" name="room_id" value="<?php echo esc_attr((string)$room_id); ?>" />

                                    <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Nome tariffa', 'almaretna-booking'); ?></label>
                                            <input type="text" name="name" required style="width:180px;"
                                                   value="<?php echo esc_attr($rate['rate_name'] ?? ''); ?>" />
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Prezzo/notte (€)', 'almaretna-booking'); ?></label>
                                            <input type="number" name="price" min="0" step="0.01" required style="width:100px;"
                                                   value="<?php echo esc_attr((string)($rate['price_per_night'] ?? 0)); ?>" />
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Data inizio', 'almaretna-booking'); ?></label>
                                            <input type="date" name="start_date" required style="width:140px;"
                                                   value="<?php echo esc_attr($df); ?>" />
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Data fine', 'almaretna-booking'); ?></label>
                                            <input type="date" name="end_date" required style="width:140px;"
                                                   value="<?php echo esc_attr($dt); ?>" />
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Canale', 'almaretna-booking'); ?></label>
                                            <select name="channel" style="width:130px;">
                                                <option value="all"     <?php selected($rate['channel'] ?? 'all', 'all'); ?>><?php esc_html_e('Tutti (Globale)', 'almaretna-booking'); ?></option>
                                                <option value="direct"  <?php selected($rate['channel'] ?? '', 'direct'); ?>><?php esc_html_e('Diretto', 'almaretna-booking'); ?></option>
                                                <option value="booking" <?php selected($rate['channel'] ?? '', 'booking'); ?>>Booking.com</option>
                                                <option value="airbnb"  <?php selected($rate['channel'] ?? '', 'airbnb'); ?>>Airbnb</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:11px;font-weight:700;color:#50575e;margin-bottom:4px;"><?php esc_html_e('Min. notti', 'almaretna-booking'); ?></label>
                                            <input type="number" name="min_stay" min="1" style="width:70px;"
                                                   value="<?php echo esc_attr((string)($rate['min_stay'] ?? 1)); ?>" />
                                        </div>
                                        <div style="display:flex;gap:8px;padding-top:2px;">
                                            <button type="submit" class="button button-primary" style="background:#708a72;border-color:#708a72;">
                                                <?php esc_html_e('Salva modifiche', 'almaretna-booking'); ?>
                                            </button>
                                            <button type="button" class="button" onclick="almToggleEditRate(<?php echo $rid; ?>)">
                                                <?php esc_html_e('Annulla', 'almaretna-booking'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Form nuova tariffa -->
        <div class="alm-admin-col--side">
            <div class="alm-admin-card">
                <div class="alm-admin-card__header">
                    <h2><?php esc_html_e('Nuova Tariffa Speciale', 'almaretna-booking'); ?></h2>
                </div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('alm_save_rates'); ?>
                    <input type="hidden" name="action"  value="alm_save_rates" />
                    <input type="hidden" name="room_id" value="<?php echo esc_attr((string) $room_id); ?>" />

                    <div class="form-field">
                        <label for="rate-name"><?php esc_html_e('Nome tariffa (es. Stagione Alta) *', 'almaretna-booking'); ?></label>
                        <input type="text" id="rate-name" name="name" class="widefat" required
                               placeholder="<?php esc_attr_e('es. Alta stagione estate', 'almaretna-booking'); ?>" />
                    </div>
                    <div class="form-field">
                        <label for="rate-price"><?php esc_html_e('Prezzo a notte (€) *', 'almaretna-booking'); ?></label>
                        <input type="number" id="rate-price" name="price" class="widefat" min="0" step="0.01" required />
                    </div>
                    <div class="form-field">
                        <label for="rate-start"><?php esc_html_e('Data di inizio (inclusa) *', 'almaretna-booking'); ?></label>
                        <input type="date" id="rate-start" name="start_date" class="widefat" required />
                    </div>
                    <div class="form-field">
                        <label for="rate-end"><?php esc_html_e('Data di fine (esclusa) *', 'almaretna-booking'); ?></label>
                        <input type="date" id="rate-end" name="end_date" class="widefat" required />
                    </div>
                    <div class="form-field">
                        <label for="rate-channel"><?php esc_html_e('Canale di vendita:', 'almaretna-booking'); ?></label>
                        <select id="rate-channel" name="channel" class="widefat">
                            <option value="all">Tutti (Globale)</option>
                            <option value="direct">Diretto (Sito Web)</option>
                            <option value="booking">Booking.com</option>
                            <option value="airbnb">Airbnb</option>
                        </select>
                    </div>
                    <div class="form-field">
                        <label for="rate-min-stay"><?php esc_html_e('Soggiorno minimo (notti):', 'almaretna-booking'); ?></label>
                        <input type="number" id="rate-min-stay" name="min_stay" class="widefat" min="1" value="1" />
                    </div>

                    <p class="submit" style="margin-bottom:0; padding-top: 10px;">
                        <button type="submit" class="button button-primary" style="background:#708a72; border-color:#708a72; font-weight:600; width:100%;">
                            <?php esc_html_e('Crea Tariffa Speciale', 'almaretna-booking'); ?>
                        </button>
                    </p>
                </form>
            </div>
        </div>

    </div>

    <?php else : ?>
        <div style="background:#fff; border:1px solid #ccd0d4; padding:30px; text-align:center; border-radius:12px; margin-top:20px; color:#646970;">
            <span class="dashicons dashicons-admin-home" style="font-size:48px; width:48px; height:48px; color:#b4b9be; margin-bottom:12px;"></span>
            <p style="font-size:14px; font-weight:600; margin:0;">
                <?php esc_html_e('Seleziona una delle camere qui sopra per visualizzarne i dettagli, impostare il prezzo standard o gestire le tariffe stagionali.', 'almaretna-booking'); ?>
            </p>
        </div>
    <?php endif; // $room ?>
</div>

<script>
function almToggleEditRate(id) {
    var editRow = document.getElementById('alm-rate-edit-' + id);
    var dataRow = document.getElementById('alm-rate-row-' + id);
    if (!editRow) return;
    var opening = editRow.style.display === 'none' || editRow.style.display === '';
    // Chiudi tutte le righe di modifica aperte
    document.querySelectorAll('[id^="alm-rate-edit-"]').forEach(function(r){ r.style.display = 'none'; });
    if (opening) {
        editRow.style.display = 'table-row';
        // Scroll morbido alla riga
        if (dataRow) dataRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>
