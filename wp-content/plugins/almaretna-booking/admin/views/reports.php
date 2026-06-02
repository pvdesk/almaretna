<?php
/**
 * Admin view — Report
 *
 * Variabili: $year, $month, $start, $end,
 *            $revenue, $confirmed, $cancelled, $by_channel, $avg_nights,
 *            $total_nights, $total_guests, $total_adults, $total_children,
 *            $avg_lead_time, $occupancy_pct, $trend_data
 *
 * @package AlmaretnaBooking
 */

defined('ABSPATH') || exit;

$year_now  = (int) gmdate('Y');
$month_now = (int) gmdate('n');

$channel_labels = ['direct' => 'Diretto', 'booking' => 'Booking.com', 'airbnb' => 'Airbnb'];
$months_it = ['', 'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
               'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];

$revenue_display   = '€ ' . number_format($revenue / 100, 2, ',', '.');

// Calcolo metriche utili per case vacanze
$adr = $total_nights > 0 ? ($revenue / 100) / $total_nights : 0.0;
$rev_par = ($confirmed > 0) ? ($revenue / 100) / (count(ALM_Room::get_all()) ?: 7) : 0.0; // RevPAR approssimativo
$avg_booking_val = $confirmed > 0 ? ($revenue / 100) / $confirmed : 0.0;
$cancellation_rate = ($confirmed + $cancelled) > 0 ? round(($cancelled / ($confirmed + $cancelled)) * 100, 1) : 0.0;

// Scala del grafico
$max_val = 0.0;
foreach ($trend_data as $pt) {
    if ($pt['rev'] > $max_val) {
        $max_val = $pt['rev'];
    }
}
$max_val_eur = $max_val / 100;
// Calcolo di un tetto di scala proporzionale
if ($max_val_eur <= 0) {
    $ceil_val = 500;
} else {
    $ceil_val = ceil($max_val_eur / 100) * 100;
}

$bar_count = count($trend_data);
$bar_width_style = $bar_count > 12 ? 'width: 10px;' : 'width: 28px;';
$bar_gap_style = $bar_count > 12 ? 'gap: 2px;' : 'gap: 8px;';
?>

<style>
    /* Stili premium isolati per la sezione Reports */
    .alm-reports-container {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        color: #3c434a;
        margin-top: 20px;
    }
    .alm-reports-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }
    .alm-reports-title .dashicons {
        font-size: 28px;
        width: 28px;
        height: 28px;
        color: #708a72;
    }
    /* Grid KPI estesa */
    .alm-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .alm-kpi-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .alm-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    .alm-kpi-card__icon {
        font-size: 32px;
        width: 32px;
        height: 32px;
        color: #708a72;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f4f6f4;
        padding: 8px;
        border-radius: 10px;
    }
    .alm-kpi-card--success .alm-kpi-card__icon {
        color: #708a72;
        background: #eef2ef;
    }
    .alm-kpi-card--warning .alm-kpi-card__icon {
        color: #d94f4f;
        background: #fdf2f2;
    }
    .alm-kpi-card__value {
        font-size: 22px;
        font-weight: 700;
        color: #1d2327;
        line-height: 1.2;
    }
    .alm-kpi-card__label {
        font-size: 12px;
        color: #646970;
        margin-top: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    /* Layout sezioni */
    .alm-reports-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }
    @media (max-width: 991px) {
        .alm-reports-grid {
            grid-template-columns: 1fr;
        }
    }
    .alm-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .alm-card__title {
        font-size: 16px;
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 20px;
        color: #1d2327;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 12px;
    }
    /* Grafico CSS Premium */
    .alm-chart-box {
        position: relative;
    }
    .alm-chart-container {
        display: flex;
        height: 250px;
        margin-top: 10px;
    }
    .alm-chart-y-axis {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding-right: 12px;
        color: #8c8f94;
        font-size: 10px;
        border-right: 1px solid #e0e0e0;
        text-align: right;
        width: 65px;
        padding-bottom: 20px;
    }
    .alm-chart-plot {
        display: flex;
        flex: 1;
        justify-content: space-around;
        align-items: flex-end;
        padding-left: 12px;
        padding-bottom: 20px;
        position: relative;
    }
    .alm-chart-gridlines {
        position: absolute;
        top: 0;
        left: 77px;
        right: 24px;
        height: 230px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        pointer-events: none;
        z-index: 1;
    }
    .alm-chart-gridline {
        width: 100%;
        border-top: 1px dashed #f0f0f0;
    }
    .alm-chart-bar-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        height: 100%;
        position: relative;
        cursor: pointer;
        z-index: 2;
    }
    .alm-chart-bar-track {
        flex: 1;
        background: #f8fafc;
        border-radius: 4px 4px 0 0;
        position: relative;
        display: flex;
        align-items: flex-end;
        margin-bottom: 6px;
        width: 100%;
        max-width: 32px;
        justify-content: center;
    }
    .alm-chart-bar-fill {
        width: 100%;
        background: linear-gradient(180deg, #9bb09c 0%, #708a72 100%);
        border-radius: 4px 4px 0 0;
        transition: height 0.4s cubic-bezier(0.16, 1, 0.3, 1), filter 0.2s ease;
    }
    .alm-chart-bar-col:hover .alm-chart-bar-fill {
        filter: brightness(0.9);
    }
    .alm-chart-bar-label {
        font-size: 10px;
        color: #8c8f94;
        text-transform: capitalize;
        white-space: nowrap;
    }
    .alm-chart-bar-tooltip {
        visibility: hidden;
        opacity: 0;
        position: absolute;
        bottom: 105%;
        background: #1d2327;
        color: #fff;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 11px;
        white-space: nowrap;
        z-index: 99;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        line-height: 1.4;
        text-align: left;
        transition: opacity 0.15s ease, visibility 0.15s;
    }
    .alm-chart-bar-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: #1d2327 transparent transparent transparent;
    }
    .alm-chart-bar-col:hover .alm-chart-bar-tooltip {
        visibility: visible;
        opacity: 1;
    }
    /* Metriche aggiuntive */
    .alm-metric-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .alm-metric-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .alm-metric-label {
        font-weight: 500;
        color: #646970;
        font-size: 13px;
    }
    .alm-metric-value {
        font-weight: 600;
        color: #1d2327;
        font-size: 14px;
    }
    .alm-metric-hint {
        font-size: 11px;
        color: #8c8f94;
        display: block;
        margin-top: 2px;
    }
</style>

<div class="wrap alm-reports-container">
    <div class="alm-reports-title">
        <span class="dashicons dashicons-chart-bar"></span>
        <h1><?php esc_html_e('Report & Statistiche Struttura', 'almaretna-booking'); ?></h1>
    </div>

    <!-- Filtro periodo -->
    <div class="alm-admin-card" style="margin-bottom:20px; background:#fff; padding: 16px; border: 1px solid #e0e0e0; border-radius: 12px;">
        <form method="get" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap; margin:0;">
            <input type="hidden" name="page" value="alm-reports" />

            <div style="display:flex;align-items:center;gap:8px;">
                <label style="font-weight:600; font-size:13px; color:#555;"><?php esc_html_e('Anno:', 'almaretna-booking'); ?></label>
                <select name="year" class="alm-filter-select" style="min-width:100px;">
                    <?php for ($y = $year_now + 1; $y >= $year_now - 3; $y--) : ?>
                        <option value="<?php echo esc_attr((string) $y); ?>" <?php selected($year, $y); ?>><?php echo esc_html((string) $y); ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div style="display:flex;align-items:center;gap:8px;">
                <label style="font-weight:600; font-size:13px; color:#555;"><?php esc_html_e('Mese:', 'almaretna-booking'); ?></label>
                <select name="month" class="alm-filter-select" style="min-width:150px;">
                    <option value="0" <?php selected($month, 0); ?>><?php esc_html_e('Tutti i mesi (Consolidato)', 'almaretna-booking'); ?></option>
                    <?php
                    for ($m = 1; $m <= 12; $m++) : ?>
                        <option value="<?php echo esc_attr((string) $m); ?>" <?php selected($month, $m); ?>>
                            <?php echo esc_html($months_it[$m]); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="button button-primary" style="background:#708a72; border-color:#708a72;">
                <?php esc_html_e('Aggiorna Report', 'almaretna-booking'); ?>
            </button>
        </form>
    </div>

    <!-- KPI principalissimi -->
    <div class="alm-kpi-grid">

        <div class="alm-kpi-card alm-kpi-card--success">
            <div class="alm-kpi-card__icon dashicons dashicons-chart-line"></div>
            <div>
                <div class="alm-kpi-card__value"><?php echo esc_html($revenue_display); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Fatturato Lordo', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card">
            <div class="alm-kpi-card__icon dashicons dashicons-admin-home"></div>
            <div>
                <div class="alm-kpi-card__value"><?php echo esc_html($occupancy_pct . '%'); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Tasso Occupazione', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card">
            <div class="alm-kpi-card__icon dashicons dashicons-clock"></div>
            <div>
                <div class="alm-kpi-card__value"><?php echo esc_html(number_format($avg_nights, 1, ',', '')); ?> gg</div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Soggiorno Medio', 'almaretna-booking'); ?></div>
            </div>
        </div>

        <div class="alm-kpi-card">
            <div class="alm-kpi-card__icon dashicons dashicons-calendar-alt"></div>
            <div>
                <div class="alm-kpi-card__value"><?php echo esc_html((string) $confirmed); ?></div>
                <div class="alm-kpi-card__label"><?php esc_html_e('Prenotazioni Conf.', 'almaretna-booking'); ?></div>
            </div>
        </div>

    </div>

    <!-- Layout a due colonne per Grafico + Canali -->
    <div class="alm-reports-grid">

        <!-- Colonna Principale: Grafico Periodo -->
        <div class="alm-card alm-chart-box">
            <h2 class="alm-card__title">
                <?php
                if ($month == 0) {
                    printf(esc_html__('Andamento Fatturato Mensile — Anno %d', 'almaretna-booking'), $year);
                } else {
                    printf(esc_html__('Dettaglio Fatturato Giornaliero — %s %d', 'almaretna-booking'), $months_it[$month], $year);
                }
                ?>
            </h2>

            <!-- Linee di griglia di sfondo -->
            <div class="alm-chart-gridlines">
                <div class="alm-chart-gridline"></div>
                <div class="alm-chart-gridline"></div>
                <div class="alm-chart-gridline"></div>
            </div>

            <div class="alm-chart-container">
                <!-- Asse Y -->
                <div class="alm-chart-y-axis">
                    <span>€<?php echo number_format($ceil_val, 0, ',', '.'); ?></span>
                    <span>€<?php echo number_format($ceil_val / 2, 0, ',', '.'); ?></span>
                    <span>€0</span>
                </div>

                <!-- Barre -->
                <div class="alm-chart-plot">
                    <?php foreach ($trend_data as $key => $pt) :
                        $rev_eur = $pt['rev'] / 100;
                        $h_pct   = $ceil_val > 0 ? ($rev_eur / $ceil_val) * 100 : 0;
                        // Limitiamo graficamente ad un'altezza minima se c'è fatturato ma è minuscolo
                        if ($rev_eur > 0 && $h_pct < 3) {
                            $h_pct = 3;
                        }
                        $label_name = $month == 0 ? substr($months_it[$key], 0, 3) : $key;
                        $tooltip_title = $month == 0 ? $months_it[$key] : sprintf('%d %s', $key, $months_it[$month]);
                    ?>
                        <div class="alm-chart-bar-col">
                            <!-- Tooltip su Hover -->
                            <div class="alm-chart-bar-tooltip">
                                <strong><?php echo esc_html($tooltip_title); ?></strong><br>
                                Fatturato: €<?php echo number_format($rev_eur, 2, ',', '.'); ?><br>
                                Notti totali: <?php echo esc_html($pt['cnt'] * $avg_nights); ?><br>
                                Prenotazioni: <?php echo esc_html($pt['cnt']); ?>
                            </div>

                            <!-- Traccia e Barra -->
                            <div class="alm-chart-bar-track" style="<?php echo esc_attr($bar_width_style); ?>">
                                <div class="alm-chart-bar-fill" style="height: <?php echo esc_attr((string) $h_pct); ?>%;"></div>
                            </div>

                            <!-- Etichetta -->
                            <div class="alm-chart-bar-label"><?php echo esc_html((string)$label_name); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Colonna Laterale: Canali e Acquisizione -->
        <div class="alm-card">
            <h2 class="alm-card__title"><?php esc_html_e('Canali di Acquisizione', 'almaretna-booking'); ?></h2>

            <?php if (empty($by_channel)) : ?>
                <p style="color:#8c8f94; font-size:13px; text-align:center; padding: 20px 0;">
                    <?php esc_html_e('Nessun dato di canale nel periodo selezionato.', 'almaretna-booking'); ?>
                </p>
            <?php else : ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($by_channel as $row) :
                        $ch_label = $channel_labels[$row['channel']] ?? ucfirst($row['channel'] ?? '—');
                        $ch_rev   = (int) $row['rev'];
                        $pct      = $revenue > 0 ? round(($ch_rev / $revenue) * 100, 1) : 0;
                    ?>
                        <div>
                            <div style="display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:6px; color:#1d2327;">
                                <span><?php echo esc_html($ch_label); ?></span>
                                <span><?php echo esc_html($row['cnt']); ?> prenotazioni</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <div style="flex:1; height:8px; background:#f4f6f4; border-radius:4px; overflow:hidden; border: 1px solid #e2e8f0;">
                                    <div style="width:<?php echo esc_attr((string) $pct); ?>%; height:100%; background:linear-gradient(90deg, #9bb09c 0%, #708a72 100%); border-radius:4px;"></div>
                                </div>
                                <span style="font-size:12px; font-weight:700; color:#4a5568; min-width:35px; text-align:right;"><?php echo esc_html($pct . '%'); ?></span>
                            </div>
                            <div style="font-size:11px; color:#718096; margin-top:2px;">
                                Fatturato canale: <strong>€<?php echo number_format($ch_rev / 100, 2, ',', '.'); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- Grid Metriche Dettagliate utili per Gestore Casa Vacanze -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">

        <!-- Prestazioni Finanziarie -->
        <div class="alm-card">
            <h2 class="alm-card__title"><?php esc_html_e('Indicatori di Performance (KPI)', 'almaretna-booking'); ?></h2>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('ADR (Tariffa Media Giornaliera)', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php esc_html_e('Ricavo medio per singola notte venduta.', 'almaretna-booking'); ?></span>
                </span>
                <span class="alm-metric-value">€ <?php echo number_format($adr, 2, ',', '.'); ?></span>
            </div>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('RevPAR (Fatturato medio per camera)', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php esc_html_e('Fatturato generato diviso per le 7 camere totali.', 'almaretna-booking'); ?></span>
                </span>
                <span class="alm-metric-value">€ <?php echo number_format($rev_par, 2, ',', '.'); ?></span>
            </div>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('Valore Medio Prenotazione', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php esc_html_e('Incasso lordo generato mediamente da un soggiorno.', 'almaretna-booking'); ?></span>
                </span>
                <span class="alm-metric-value">€ <?php echo number_format($avg_booking_val, 2, ',', '.'); ?></span>
            </div>
        </div>

        <!-- Statistiche Prenotazioni e Ospiti -->
        <div class="alm-card">
            <h2 class="alm-card__title"><?php esc_html_e('Analisi del Comportamento Clienti', 'almaretna-booking'); ?></h2>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('Anticipo Prenotazione Medio', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php esc_html_e('Numero di giorni medi tra prenotazione e check-in.', 'almaretna-booking'); ?></span>
                </span>
                <span class="alm-metric-value"><?php echo esc_html(number_format($avg_lead_time, 1, ',', '')); ?> gg prima</span>
            </div>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('Totale Ospiti Accolti', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php printf(esc_html__('Di cui %d adulti e %d bambini.', 'almaretna-booking'), $total_adults, $total_children); ?></span>
                </span>
                <span class="alm-metric-value">
                    <span class="dashicons dashicons-groups" style="font-size:16px; vertical-align:middle; color:#708a72;"></span>
                    <?php echo esc_html((string)$total_guests); ?> persone
                </span>
            </div>

            <div class="alm-metric-row">
                <span class="alm-metric-label">
                    <?php esc_html_e('Tasso di Cancellazione', 'almaretna-booking'); ?>
                    <span class="alm-metric-hint"><?php printf(esc_html__('%d prenotazioni annullate sul totale.', 'almaretna-booking'), $cancelled); ?></span>
                </span>
                <span class="alm-metric-value" style="color: <?php echo $cancellation_rate > 15 ? '#c62828' : 'inherit'; ?>;">
                    <?php echo esc_html($cancellation_rate . '%'); ?>
                </span>
            </div>
        </div>

    </div>

    <!-- Informazioni Utili -->
    <div style="font-size:12px; color:#646970; border-top: 1px solid #dcdcde; padding-top: 12px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <span>
            <?php printf(
                esc_html__('Intervallo analizzato: %s — %s', 'almaretna-booking'),
                esc_html((new DateTime($start))->format('d/m/Y')),
                esc_html((new DateTime($end))->format('d/m/Y'))
            ); ?>
        </span>
        <span>
            <?php esc_html_e('* I dati del tasso di occupazione e performance escludono le prenotazioni annullate.', 'almaretna-booking'); ?>
        </span>
    </div>
</div>
