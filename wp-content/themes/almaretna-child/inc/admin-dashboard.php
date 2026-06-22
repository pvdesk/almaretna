<?php
/**
 * Almaretna — Admin Dashboard
 * 4 widget separati: Prenotazioni | Servizi Camere | Foto Sito | Foto Camere
 *
 * @package AlmaretnaChild
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/* ═══════════════════════════════════════════════════════════════
   COSTANTI
   ═══════════════════════════════════════════════════════════════ */

define('ALM_DASH_NONCE', 'alm_dashboard_v1');

// Tutti i servizi disponibili
function alm_all_amenities(): array {
    return [
        // Vista / posizione
        'vista-mare'        => ['🌊', 'Vista mare'],
        'vista-etna'        => ['🌋', 'Vista Etna'],
        'terrazza'          => ['🏡', 'Terrazza/Balcone'],
        // Comfort
        'aria-condizionata' => ['❄️',  'Aria condizionata'],
        'riscaldamento'     => ['🔥', 'Riscaldamento'],
        'wifi'              => ['📶', 'Wi-Fi'],
        // Bagno
        'bagno-privato'     => ['🚿', 'Bagno privato'],
        'bagno-condiviso'   => ['🚿', 'Bagno condiviso'],
        'doccia'            => ['🚿', 'Doccia'],
        'vasca'             => ['🛁', 'Vasca da bagno'],
        'phon'              => ['💨', 'Phon'],
        'asciugamani'       => ['🏖️', 'Asciugamani'],
        // In camera
        'biancheria'        => ['🛏️', 'Biancheria inclusa'],
        'frigorifero'       => ['🧊', 'Frigorifero'],
        'cassaforte'        => ['🔒', 'Cassaforte'],
        // Extra
        'pulizia'           => ['🧹', 'Pulizia inclusa'],
        'parcheggio'        => ['🅿️',  'Parcheggio'],
    ];
}

/* ═══════════════════════════════════════════════════════════════
   CSS — admin_head
   ═══════════════════════════════════════════════════════════════ */

add_action('admin_head', function (): void { ?>
<style id="alm-dash-css">
:root {
    --ad-sand:   #C5B49C;
    --ad-dark:   #1a1e22;
    --ad-warm:   #F9F7F3;
    --ad-muted:  #8C8070;
    --ad-border: #EFEBE4;
    --ad-green:  #28a745;
}

/* Tutte le inside dei widget Almaretna */
#alm-w-bookings .inside,
#alm-w-services .inside,
#alm-w-media .inside,
#alm-w-room-photos .inside,
#alm-w-prices .inside { padding: 0 !important; overflow: hidden; }

.ad { font-family: -apple-system, BlinkMacSystemFont,"Segoe UI",sans-serif; font-size: .85rem; }

/* ── Header colorato sotto il titolo widget ── */
.ad-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 14px;
    border-bottom: 2px solid var(--ad-sand);
    background: var(--ad-warm);
}
.ad-head__label {
    font-size: .62rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--ad-muted);
}
.ad-head__link { font-size: .75rem; color: var(--ad-sand) !important; text-decoration: none; }
.ad-head__link:hover { text-decoration: underline; }

/* ── Stats prenotazioni ── */
.ad-stats {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    border-bottom: 1px solid var(--ad-border);
}
.ad-stat {
    padding: 13px 8px;
    text-align: center;
    border-right: 1px solid var(--ad-border);
    text-decoration: none !important;
    display: block;
    transition: background .15s;
}
.ad-stat:last-child { border-right: none; }
.ad-stat:hover { background: var(--ad-warm); }
.ad-stat__n {
    display: block;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--ad-dark);
    line-height: 1;
    margin-bottom: 3px;
}
.ad-stat__n--hi { color: var(--ad-sand); }
.ad-stat__l {
    font-size: .6rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ad-muted);
}

/* ── Sezione interna ── */
.ad-section {
    padding: 10px 14px;
    border-bottom: 1px solid var(--ad-border);
}
.ad-section:last-child { border-bottom: none; }
.ad-section__title {
    font-size: .6rem;
    font-weight: 700;
    letter-spacing: .13em;
    text-transform: uppercase;
    color: var(--ad-muted);
    margin: 0 0 8px;
}

/* ── Pulsanti ── */
.ad-actions { display: flex; gap: 6px; flex-wrap: wrap; }
.ad-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 5px 11px;
    font-size: .78rem;
    font-weight: 600;
    border-radius: 4px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
    white-space: nowrap;
    line-height: 1.4;
}
.ad-btn:hover { opacity: .82; transform: translateY(-1px); }
.ad-btn--sand   { background: var(--ad-sand); color: #fff !important; }
.ad-btn--dark   { background: var(--ad-dark); color: #fff !important; }
.ad-btn--ghost  { background: transparent; color: var(--ad-dark) !important; border: 1px solid var(--ad-border); }
.ad-btn--ghost:hover { background: var(--ad-warm); }
.ad-btn--sm { padding: 3px 8px; font-size: .72rem; }

/* ── Lista prenotazioni ── */
.ad-list { margin: 0; padding: 0; list-style: none; }
.ad-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 14px;
    border-bottom: 1px solid var(--ad-border);
    transition: background .15s;
}
.ad-row:last-child { border-bottom: none; }
.ad-row:hover { background: var(--ad-warm); }
.ad-row__name { font-weight: 600; color: var(--ad-dark) !important; text-decoration: none; }
.ad-row__name:hover { text-decoration: underline; }
.ad-row__dates { color: var(--ad-muted); font-size: .72rem; }
.ad-badge {
    font-size: .6rem; font-weight: 700; letter-spacing: .05em;
    text-transform: uppercase; padding: 2px 7px; border-radius: 20px; flex-shrink: 0;
}
.ad-b--confirmed { background:#d4edda; color:#155724; }
.ad-b--pending   { background:#fff3cd; color:#856404; }
.ad-b--paid      { background:#cce5ff; color:#004085; }
.ad-b--cancelled { background:#f8d7da; color:#721c24; }

/* ── Vuoto ── */
.ad-empty { padding: 12px 14px; color: var(--ad-muted); font-size: .82rem; margin: 0; }

/* ── Media grid ── */
.ad-media-grid {
    display: grid;
    grid-template-columns: repeat(6,1fr);
    gap: 3px;
    padding: 10px 14px;
}
.ad-thumb {
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 3px;
    display: block;
    position: relative;
    cursor: pointer;
}
.ad-thumb img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .2s;
    display: block;
}
.ad-thumb:hover img { transform: scale(1.08); }
.ad-thumb__over {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.42);
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; opacity: 0; transition: opacity .2s;
}
.ad-thumb:hover .ad-thumb__over { opacity: 1; }

/* ── Shortcode ── */
.ad-sc-list { display:flex; flex-direction:column; gap:4px; padding:0 14px 12px; }
.ad-sc-row {
    display: flex;
    align-items: center;
    gap: 7px;
    background: var(--ad-warm);
    border: 1px solid var(--ad-border);
    border-radius: 4px;
    padding: 6px 9px;
}
.ad-sc-row code { flex:1; font-size:.73rem; color:#c7254e; background:none; padding:0; word-break:break-all; }
.ad-sc-row__desc { font-size:.66rem; color:var(--ad-muted); margin-top:1px; }
.ad-copy-btn {
    flex-shrink: 0;
    padding: 2px 8px;
    font-size: .68rem; font-weight: 600;
    background: var(--ad-dark); color: #fff;
    border: none; border-radius: 3px; cursor: pointer;
    transition: background .15s;
}
.ad-copy-btn:hover  { background: var(--ad-sand); }
.ad-copy-btn.copied { background: var(--ad-green); }

/* ── Foto camere ── */
.ad-room-grid {
    display: flex;
    flex-direction: column;
    gap: 0;
}
.ad-room-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 14px;
    border-bottom: 1px solid var(--ad-border);
    transition: background .15s;
}
.ad-room-row:last-child { border-bottom: none; }
.ad-room-row:hover { background: var(--ad-warm); }
.ad-room-row__thumb {
    width: 52px; height: 40px;
    border-radius: 3px;
    overflow: hidden;
    background: var(--ad-border);
    flex-shrink: 0;
    position: relative;
}
.ad-room-row__thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.ad-room-row__thumb--empty {
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: var(--ad-muted);
}
.ad-room-row__name { flex: 1; font-weight: 600; font-size: .82rem; color: var(--ad-dark); }
.ad-room-row__actions { display:flex; gap:5px; flex-shrink:0; }

/* Feedback caricamento foto */
.ad-room-row__thumb.is-loading::after {
    content: '⟳';
    position: absolute; inset: 0;
    background: rgba(255,255,255,.8);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    animation: ad-spin .6s linear infinite;
}
@keyframes ad-spin { to { transform: rotate(360deg); } }

/* ── Servizi camere — card style (riusa .ad-room-row) ── */
.ad-room-row--svc { align-items: flex-start; padding: 10px 14px; }
.ad-room-row__body { flex: 1; min-width: 0; }
.ad-room-row__top  { display: flex; align-items: center; justify-content: space-between; margin-bottom: 7px; }
.ad-room-row__name-link {
    font-weight: 600; font-size: .83rem;
    color: var(--ad-dark) !important; text-decoration: none;
}
.ad-room-row__name-link:hover { text-decoration: underline; }
.ad-svc-chips { display: flex; flex-wrap: wrap; gap: 4px; }
.ad-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 3px 8px;
    font-size: .69rem;
    font-weight: 600;
    border-radius: 20px;
    border: 1.5px solid var(--ad-border);
    background: #fff;
    color: var(--ad-muted);
    cursor: pointer;
    user-select: none;
    transition: all .15s;
    white-space: nowrap;
}
.ad-chip:hover { border-color: var(--ad-sand); color: var(--ad-dark); }
.ad-chip.on {
    background: var(--ad-sand);
    border-color: var(--ad-sand);
    color: #fff;
}
.ad-chip.saving { opacity: .5; pointer-events: none; }
.ad-chip.saved-ok { background: var(--ad-green); border-color: var(--ad-green); }

/* ── Prezzi camere ── */
.ad-price-room {
    padding: 11px 14px;
    border-bottom: 1px solid var(--ad-border);
}
.ad-price-room:last-child { border-bottom: none; }

.ad-price-room__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}
.ad-price-room__name {
    flex: 1;
    font-weight: 700;
    font-size: .83rem;
    color: var(--ad-dark);
}

/* Riga prezzo base */
.ad-price-base {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 9px;
    flex-wrap: wrap;
}
.ad-price-base__label {
    font-size: .7rem;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--ad-muted);
    white-space: nowrap;
}
.ad-price-input {
    width: 90px;
    padding: 5px 8px;
    border: 1.5px solid var(--ad-border);
    border-radius: 4px;
    font-size: .85rem;
    font-weight: 600;
    color: var(--ad-dark);
    background: #fff;
    text-align: right;
    transition: border-color .15s;
}
.ad-price-input:focus { border-color: var(--ad-sand); outline: none; }
.ad-price-input--sm { width: 75px; }
.ad-price-suffix {
    font-size: .72rem;
    color: var(--ad-muted);
    white-space: nowrap;
}
.ad-save-btn {
    padding: 5px 11px;
    font-size: .72rem;
    font-weight: 600;
    background: var(--ad-sand);
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background .15s, transform .1s;
    white-space: nowrap;
}
.ad-save-btn:hover   { background: #b8a48c; transform: translateY(-1px); }
.ad-save-btn.saving  { opacity: .55; pointer-events: none; }
.ad-save-btn.saved   { background: var(--ad-green); }

/* Lista prezzi speciali */
.ad-specials { margin: 0 0 8px; padding: 0; list-style: none; }
.ad-special-item {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 5px 8px;
    background: var(--ad-warm);
    border: 1px solid var(--ad-border);
    border-radius: 4px;
    font-size: .78rem;
    margin-bottom: 4px;
}
.ad-special-item__note {
    font-weight: 700;
    color: var(--ad-dark);
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 90px;
}
.ad-special-item__dates { color: var(--ad-muted); font-size: .71rem; flex: 1; }
.ad-special-item__price {
    font-weight: 700;
    color: var(--ad-dark);
    white-space: nowrap;
}
.ad-del-btn {
    flex-shrink: 0;
    width: 22px; height: 22px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: var(--ad-muted);
    cursor: pointer;
    font-size: .85rem;
    line-height: 1;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s, color .15s;
}
.ad-del-btn:hover { background: #f8d7da; color: #721c24; }
.ad-del-btn.deleting { opacity: .4; pointer-events: none; }

/* Toggle mostra/nascondi periodi */
.ad-specials-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 4px 0 5px;
    background: none;
    border: none;
    border-bottom: 1px dashed var(--ad-border);
    cursor: pointer;
    font-size: .7rem;
    font-weight: 600;
    color: var(--ad-muted);
    margin-bottom: 6px;
    text-align: left;
    transition: color .15s;
}
.ad-specials-toggle:hover { color: var(--ad-dark); }
.ad-specials-toggle__icon { font-size: .6rem; flex-shrink: 0; }

/* Form aggiungi prezzo speciale */
.ad-add-toggle {
    font-size: .75rem;
    font-weight: 600;
    color: var(--ad-sand);
    background: none;
    border: 1.5px dashed var(--ad-border);
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    width: 100%;
    text-align: center;
    transition: border-color .15s, color .15s;
}
.ad-add-toggle:hover { border-color: var(--ad-sand); }

.ad-add-form {
    display: none;
    flex-direction: column;
    gap: 6px;
    margin-top: 7px;
    padding: 10px;
    background: var(--ad-warm);
    border: 1px solid var(--ad-border);
    border-radius: 4px;
}
.ad-add-form.open { display: flex; }
.ad-add-form__row { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.ad-add-form__label {
    font-size: .65rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ad-muted);
    white-space: nowrap;
    min-width: 30px;
}
.ad-date-input {
    flex: 1;
    min-width: 120px;
    padding: 5px 7px;
    border: 1.5px solid var(--ad-border);
    border-radius: 4px;
    font-size: .8rem;
    color: var(--ad-dark);
    background: #fff;
    transition: border-color .15s;
}
.ad-date-input:focus { border-color: var(--ad-sand); outline: none; }
.ad-note-input {
    flex: 1;
    min-width: 100px;
    padding: 5px 7px;
    border: 1.5px solid var(--ad-border);
    border-radius: 4px;
    font-size: .8rem;
    color: var(--ad-dark);
    background: #fff;
    transition: border-color .15s;
}
.ad-note-input:focus { border-color: var(--ad-sand); outline: none; }
.ad-note-input::placeholder { color: #bbb; }
.ad-add-form__actions { display: flex; gap: 5px; justify-content: flex-end; }

/* ── Menu laterale ── */
#adminmenu #toplevel_page_alm-booking > a {
    background: rgba(197,180,156,.09) !important;
    border-left: 3px solid #C5B49C !important;
}

/* ── Foto Sito — slot a posizione ── */
.ad-slot-section { padding: 8px 14px 4px; }
.ad-slot-section__title {
    font-size: .58rem; font-weight: 700; letter-spacing: .15em;
    text-transform: uppercase; color: var(--ad-muted);
    margin: 0 0 8px; padding-bottom: 5px;
    border-bottom: 1px solid var(--ad-border);
}
.ad-slot-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
    gap: 7px;
    margin-bottom: 10px;
}
.ad-slot {
    display: flex; flex-direction: column;
    border: 1.5px dashed var(--ad-border);
    border-radius: 5px; overflow: hidden;
    transition: border-color .15s;
}
.ad-slot.has-photo { border-style: solid; border-color: var(--ad-sand); }
.ad-slot:hover { border-color: var(--ad-sand); }
.ad-slot__preview {
    aspect-ratio: 1; background: var(--ad-warm);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
}
.ad-slot__preview img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .2s;
}
.ad-slot:hover .ad-slot__preview img { transform: scale(1.05); }
.ad-slot__preview--empty { color: #ccc; font-size: 1.4rem; }
.ad-slot__overlay {
    position: absolute; inset: 0; background: rgba(0,0,0,.52);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 4px; opacity: 0; transition: opacity .15s;
}
.ad-slot:hover .ad-slot__overlay { opacity: 1; }
.ad-slot__overlay-btn {
    padding: 3px 8px; font-size: .63rem; font-weight: 700;
    border: none; border-radius: 3px; cursor: pointer;
    background: rgba(255,255,255,.9); color: var(--ad-dark);
    transition: background .1s; white-space: nowrap;
}
.ad-slot__overlay-btn:hover { background: #fff; }
.ad-slot__overlay-btn--del { background: rgba(180,40,40,.85); color: #fff; }
.ad-slot__overlay-btn--del:hover { background: rgba(160,10,10,.9); }
.ad-slot__label {
    padding: 4px 5px 1px;
    font-size: .61rem; font-weight: 700; color: var(--ad-dark);
    text-align: center; line-height: 1.2;
}
.ad-slot__desc {
    padding: 0 4px 5px;
    font-size: .56rem; color: var(--ad-muted);
    text-align: center; line-height: 1.2;
}

/* ── Foto Camere — galleria multi-foto ── */
.ad-room-photos-row {
    padding: 10px 14px;
    border-bottom: 1px solid var(--ad-border);
}
.ad-room-photos-row:last-child { border-bottom: none; }
.ad-room-photos-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 8px;
}
.ad-room-photos-header__name {
    font-weight: 600; font-size: .83rem; color: var(--ad-dark);
    text-decoration: none;
}
.ad-room-photos-header__name:hover { text-decoration: underline; }
.ad-room-gallery { display: flex; gap: 6px; flex-wrap: wrap; }
.ad-room-gallery__item {
    position: relative; width: 62px; height: 62px;
    border-radius: 4px; overflow: hidden; flex-shrink: 0;
    border: 2px solid transparent; transition: border-color .15s;
}
.ad-room-gallery__item.is-main { border-color: var(--ad-sand); }
.ad-room-gallery__item img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.ad-room-gallery__actions {
    position: absolute; inset: 0; background: rgba(0,0,0,.52);
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 4px; opacity: 0; transition: opacity .15s;
}
.ad-room-gallery__item:hover .ad-room-gallery__actions { opacity: 1; }
.ad-room-gallery__star {
    background: rgba(255,255,255,.88); border: none; border-radius: 50%;
    width: 24px; height: 24px; cursor: pointer; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    transition: background .1s; line-height: 1;
}
.ad-room-gallery__star:hover { background: #fff; }
.ad-room-gallery__item.is-main .ad-room-gallery__star { background: var(--ad-sand); color: #fff; }
.ad-room-gallery__del {
    background: rgba(190,40,40,.85); border: none; border-radius: 50%;
    width: 24px; height: 24px; cursor: pointer; font-size: .78rem; color: #fff;
    display: flex; align-items: center; justify-content: center;
    transition: background .1s;
}
.ad-room-gallery__del:hover { background: rgba(160,10,10,.9); }
.ad-room-gallery__add {
    width: 62px; height: 62px; border-radius: 4px;
    border: 1.5px dashed var(--ad-border); background: var(--ad-warm);
    cursor: pointer; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 1px; color: var(--ad-muted); font-size: .6rem; font-weight: 700;
    transition: border-color .15s, color .15s;
}
.ad-room-gallery__add:hover { border-color: var(--ad-sand); color: var(--ad-sand); }
.ad-room-gallery__add-icon { font-size: 1.1rem; line-height: 1; }
.ad-room-count-warn { font-size: .62rem; color: #d35400; font-weight: 700; }
.ad-room-count-ok   { font-size: .62rem; color: var(--ad-green); font-weight: 700; }
</style>
<?php });

/* ═══════════════════════════════════════════════════════════════
   MEDIA UPLOADER (solo dashboard)
   ═══════════════════════════════════════════════════════════════ */

add_action('admin_enqueue_scripts', function (string $hook): void {
    if ($hook !== 'index.php') return;
    wp_enqueue_media();
    wp_enqueue_script(
        'alm-dashboard-js',
        get_stylesheet_directory_uri() . '/assets/js/admin-dashboard.js',
        ['jquery'],
        '1.0',
        true
    );
    wp_localize_script('alm-dashboard-js', 'almDash', [
        'ajax'    => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce(ALM_DASH_NONCE),
        'chooseTxt' => __('Scegli immagine', 'almaretna-child'),
        'useTxt'    => __('Usa questa immagine', 'almaretna-child'),
    ]);
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Imposta immagine camera
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_set_room_thumb', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id  = (int) ($_POST['room_id']  ?? 0);
    $media_id = (int) ($_POST['media_id'] ?? 0);

    if (!$room_id || !$media_id) wp_send_json_error('Dati mancanti');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    set_post_thumbnail($room_id, $media_id);
    $src = wp_get_attachment_image_src($media_id, 'thumbnail');

    wp_send_json_success(['thumb' => $src ? esc_url($src[0]) : '']);
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Toggle servizio camera
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_toggle_amenity', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id = (int) ($_POST['room_id'] ?? 0);
    $slug    = sanitize_key($_POST['amenity'] ?? '');
    $enable  = filter_var($_POST['enable'] ?? false, FILTER_VALIDATE_BOOLEAN);

    if (!$room_id || !$slug) wp_send_json_error('Dati mancanti');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    // Assicura che il termine esista nella tassonomia
    if (!term_exists($slug, 'amenity')) {
        $all = alm_all_amenities();
        if (isset($all[$slug])) {
            wp_insert_term($all[$slug][1], 'amenity', ['slug' => $slug]);
        }
    }

    $current = wp_get_object_terms($room_id, 'amenity', ['fields' => 'slugs']);
    if (!is_array($current)) $current = [];

    if ($enable) {
        $current[] = $slug;
    } else {
        $current = array_values(array_filter($current, fn($s) => $s !== $slug));
    }

    wp_set_object_terms($room_id, array_unique($current), 'amenity');
    wp_send_json_success();
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Salva prezzo base camera
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_save_base_price', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id = (int) ($_POST['room_id'] ?? 0);
    $price   = (float) ($_POST['price'] ?? -1);

    if (!$room_id || $price < 0) wp_send_json_error('Dati non validi');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    update_post_meta($room_id, '_room_base_price', round($price, 2));
    wp_send_json_success(['price' => round($price, 2)]);
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Aggiungi prezzo speciale
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_add_special_price', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id = (int) ($_POST['room_id'] ?? 0);
    $from    = sanitize_text_field($_POST['from']  ?? '');
    $to      = sanitize_text_field($_POST['to']    ?? '');
    $price   = (float) ($_POST['price'] ?? -1);
    $note    = sanitize_text_field($_POST['note']  ?? '');

    if (!$room_id || !$from || !$to || $price < 0) wp_send_json_error('Dati non validi');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
        wp_send_json_error('Formato data non valido');
    }
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    // Il form mostra date inclusive; alm_rates usa date_to esclusiva (giorno dopo la fine)
    $to_exclusive = date('Y-m-d', strtotime($to . ' +1 day'));

    $rate_id = ALM_Pricing::add_rate($room_id, [
        'date_from'       => $from,
        'date_to'         => $to_exclusive,
        'price_per_night' => $price,
        'rate_name'       => $note,
        'min_stay'        => 1,
        'channel'         => 'all',
    ]);

    if (is_wp_error($rate_id)) {
        wp_send_json_error($rate_id->get_error_message());
    }

    wp_send_json_success([
        'id'              => $rate_id,
        'date_from'       => $from,
        'date_to'         => $to_exclusive,
        'price_per_night' => round($price, 2),
        'rate_name'       => $note,
    ]);
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Elimina prezzo speciale
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_delete_special_price', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id  = (int) ($_POST['room_id']  ?? 0);
    $entry_id = (int) ($_POST['entry_id'] ?? 0);

    if (!$room_id || !$entry_id) wp_send_json_error('Dati mancanti');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    $ok = ALM_Pricing::delete_rate($entry_id);
    $ok ? wp_send_json_success() : wp_send_json_error('Errore eliminazione');
});

/* ═══════════════════════════════════════════════════════════════
   HELPER — slot foto sito
   ═══════════════════════════════════════════════════════════════ */

function alm_site_photo_slots(): array {
    return [
        'homepage' => [
            'label' => 'Homepage',
            'slots' => [
                'hero_bg'      => ['label' => 'Hero — Sfondo',     'desc' => 'Foto fullscreen hero'],
                'storia_foto'  => ['label' => 'Storia — Foto',     'desc' => 'Sezione storie'],
                'piscina_foto' => ['label' => 'Piscina — Foto',    'desc' => 'Bar bordo piscina'],
                'gallery_1'    => ['label' => 'Galleria 1',        'desc' => 'Strip foto 1'],
                'gallery_2'    => ['label' => 'Galleria 2',        'desc' => 'Strip foto 2'],
                'gallery_3'    => ['label' => 'Galleria 3',        'desc' => 'Strip foto 3'],
                'gallery_4'    => ['label' => 'Galleria 4',        'desc' => 'Strip foto 4'],
                'gallery_5'    => ['label' => 'Galleria 5',        'desc' => 'Strip foto 5'],
                'gallery_6'    => ['label' => 'Galleria 6',        'desc' => 'Strip foto 6'],
            ],
        ],
        'camere' => [
            'label' => 'Pagina Camere',
            'slots' => [
                'camere_hero' => ['label' => 'Hero', 'desc' => 'Foto fullscreen hero camere'],
            ],
        ],
        'struttura' => [
            'label' => 'La Villa e i Servizi',
            'slots' => [
                'struttura_hero'   => ['label' => 'Hero',   'desc' => 'Foto hero della pagina'],
                'struttura_foto_1' => ['label' => 'Foto 1', 'desc' => 'Prima foto di contenuto'],
                'struttura_foto_2' => ['label' => 'Foto 2', 'desc' => 'Seconda foto di contenuto'],
            ],
        ],
        'dove-siamo' => [
            'label' => 'Come raggiungerci',
            'slots' => [
                'dove_hero' => ['label' => 'Hero', 'desc' => 'Foto hero della pagina'],
            ],
        ],
        'contatti' => [
            'label' => 'Contattaci',
            'slots' => [
                'contatti_hero' => ['label' => 'Hero', 'desc' => 'Foto hero della pagina'],
            ],
        ],
        'scopri-sicilia' => [
            'label' => 'Scopri la Sicilia',
            'slots' => [
                'sicilia_hero' => ['label' => 'Hero', 'desc' => 'Foto hero della pagina'],
            ],
        ],
        'generale' => [
            'label' => 'Sito Generale',
            'slots' => [
                'og_image' => ['label' => 'OG Image (social)', 'desc' => '1200×630 Facebook/WhatsApp'],
            ],
        ],
    ];
}

function alm_flat_site_photo_slots(): array {
    $flat = [];
    foreach (alm_site_photo_slots() as $section) {
        foreach ($section['slots'] as $key => $slot) {
            $flat[$key] = $slot;
        }
    }
    return $flat;
}

/**
 * Restituisce l'URL dell'immagine per uno slot sito (o '' se non impostata).
 */
function alm_get_site_photo(string $key, string $size = 'full'): string {
    $id = (int) get_option('alm_site_photo_' . $key, 0);
    if (!$id) return '';
    return (string) wp_get_attachment_url($id);
}

/* ═══════════════════════════════════════════════════════════════
   AJAX — Salva foto sito (slot nominato)
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_save_site_photo', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('upload_files')) wp_send_json_error('Permesso negato');

    $key      = sanitize_key($_POST['key']      ?? '');
    $media_id = (int)          ($_POST['media_id'] ?? 0);

    if (!$key || !$media_id) wp_send_json_error('Dati mancanti');
    if (!array_key_exists($key, alm_flat_site_photo_slots())) wp_send_json_error('Slot non valido');

    update_option('alm_site_photo_' . $key, $media_id, false);
    $thumb = wp_get_attachment_image_src($media_id, 'thumbnail');

    wp_send_json_success(['thumb' => $thumb ? esc_url($thumb[0]) : '']);
});

add_action('wp_ajax_alm_remove_site_photo', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('upload_files')) wp_send_json_error('Permesso negato');

    $key = sanitize_key($_POST['key'] ?? '');
    if (!$key) wp_send_json_error('Dati mancanti');
    if (!array_key_exists($key, alm_flat_site_photo_slots())) wp_send_json_error('Slot non valido');

    delete_option('alm_site_photo_' . $key);
    wp_send_json_success();
});

/* ═══════════════════════════════════════════════════════════════
   AJAX — Galleria foto camera (multi-foto)
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_ajax_alm_add_room_gallery_photo', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id  = (int) ($_POST['room_id']  ?? 0);
    $media_id = (int) ($_POST['media_id'] ?? 0);

    if (!$room_id || !$media_id) wp_send_json_error('Dati mancanti');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    $raw     = get_post_meta($room_id, '_room_gallery', true);
    $gallery = $raw ? json_decode($raw, true) : [];
    if (!is_array($gallery)) $gallery = [];
    $gallery = array_values(array_map('intval', $gallery));

    if (!in_array($media_id, $gallery, true)) {
        $gallery[] = $media_id;
        update_post_meta($room_id, '_room_gallery', wp_json_encode($gallery));
    }

    // Prima foto = imposta anche come thumbnail principale
    if (!has_post_thumbnail($room_id)) {
        set_post_thumbnail($room_id, $media_id);
    }

    $thumb   = wp_get_attachment_image_src($media_id, 'thumbnail');
    $is_main = ((int) get_post_thumbnail_id($room_id) === $media_id);

    wp_send_json_success([
        'media_id' => $media_id,
        'thumb'    => $thumb ? esc_url($thumb[0]) : '',
        'is_main'  => $is_main,
    ]);
});

add_action('wp_ajax_alm_remove_room_gallery_photo', function (): void {
    check_ajax_referer(ALM_DASH_NONCE, 'nonce');
    if (!current_user_can('edit_posts')) wp_send_json_error('Permesso negato');

    $room_id  = (int) ($_POST['room_id']  ?? 0);
    $media_id = (int) ($_POST['media_id'] ?? 0);

    if (!$room_id || !$media_id) wp_send_json_error('Dati mancanti');
    if (get_post_type($room_id) !== 'almaretna_room') wp_send_json_error('Post non valido');

    $raw     = get_post_meta($room_id, '_room_gallery', true);
    $gallery = $raw ? json_decode($raw, true) : [];
    if (!is_array($gallery)) $gallery = [];
    $gallery = array_values(array_filter(array_map('intval', $gallery), fn($id) => $id !== $media_id));
    update_post_meta($room_id, '_room_gallery', wp_json_encode($gallery));

    // Se era la thumbnail principale, imposta la prima rimasta (o rimuovi)
    if ((int) get_post_thumbnail_id($room_id) === $media_id) {
        if (!empty($gallery)) {
            set_post_thumbnail($room_id, $gallery[0]);
        } else {
            delete_post_thumbnail($room_id);
        }
    }

    wp_send_json_success(['new_main' => (int) get_post_thumbnail_id($room_id)]);
});

/* ═══════════════════════════════════════════════════════════════
   REGISTRAZIONE WIDGET
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_dashboard_setup', function (): void {

    // 1 — Prenotazioni (colonna sx, prima)
    wp_add_dashboard_widget('alm-w-bookings',    '📅 Prenotazioni',    'alm_w_bookings',    null, null, 'normal', 'high');
    // 2 — Servizi camere (colonna sx, seconda)
    wp_add_dashboard_widget('alm-w-services',    '🛎️ Servizi Camere',   'alm_w_services',    null, null, 'normal', 'default');
    // 3 — Foto sito (colonna dx, prima)
    wp_add_dashboard_widget('alm-w-media',       '🖼️ Foto Sito',        'alm_w_media',       null, null, 'side',   'high');
    // 4 — Foto camere (colonna dx, seconda)
    wp_add_dashboard_widget('alm-w-room-photos', '📷 Foto Camere',      'alm_w_room_photos', null, null, 'side',   'default');
    // 5 — Prezzi camere (colonna sx, terza)
    wp_add_dashboard_widget('alm-w-prices',      '🏷️ Prezzi Camere',    'alm_w_prices',      null, null, 'normal', 'default');

    // Rimuovi widget predefiniti ingombranti
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_primary',     'dashboard', 'side');
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
    remove_meta_box('e-dashboard-overview',  'dashboard', 'normal');
});

/* ═══════════════════════════════════════════════════════════════
   WIDGET 1 — PRENOTAZIONI
   ═══════════════════════════════════════════════════════════════ */

function alm_w_bookings(): void {
    if (!current_user_can('edit_posts') && !current_user_can('manage_options')) {
        echo '<p class="ad-empty">Permessi insufficienti.</p>'; return;
    }

    global $wpdb;
    $table   = $wpdb->prefix . 'alm_bookings';
    $has_tbl = ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table);

    $stats = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'paid' => 0];
    $rows  = [];

    if ($has_tbl) {
        $stats['total']     = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");
        $stats['pending']   = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table}` WHERE status='pending'");
        $stats['confirmed'] = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table}` WHERE status='confirmed'");
        $stats['paid']      = (int)$wpdb->get_var("SELECT COUNT(*) FROM `{$table}` WHERE status='paid'");
        $rows = (array)$wpdb->get_results(
            "SELECT id, guest_name, checkin_date AS checkin, checkout_date AS checkout, status, total_price FROM `{$table}` ORDER BY created_at DESC LIMIT 8"
        );
    }

    $bu  = admin_url('admin.php?page=alm-bookings');
    $su  = admin_url('admin.php?page=alm-settings');
    $ru  = admin_url('edit.php?post_type=almaretna_room');
    $rnu = admin_url('post-new.php?post_type=almaretna_room');

    $smap = [
        'pending'   => ['In attesa',  'ad-b--pending'],
        'confirmed' => ['Confermata', 'ad-b--confirmed'],
        'paid'      => ['Pagata',     'ad-b--paid'],
        'cancelled' => ['Annullata',  'ad-b--cancelled'],
    ];
    ?>
    <div class="ad">
        <div class="ad-head">
            <span class="ad-head__label">Riepilogo prenotazioni</span>
            <a href="<?php echo esc_url($bu); ?>" class="ad-head__link">Vedi tutte →</a>
        </div>

        <div class="ad-stats">
            <a href="<?php echo esc_url($bu); ?>" class="ad-stat">
                <span class="ad-stat__n"><?php echo $stats['total']; ?></span>
                <span class="ad-stat__l">Totale</span>
            </a>
            <a href="<?php echo esc_url($bu . '&status=pending'); ?>" class="ad-stat">
                <span class="ad-stat__n ad-stat__n--hi"><?php echo $stats['pending']; ?></span>
                <span class="ad-stat__l">In attesa</span>
            </a>
            <a href="<?php echo esc_url($bu . '&status=confirmed'); ?>" class="ad-stat">
                <span class="ad-stat__n"><?php echo $stats['confirmed']; ?></span>
                <span class="ad-stat__l">Confermate</span>
            </a>
            <a href="<?php echo esc_url($bu . '&status=paid'); ?>" class="ad-stat">
                <span class="ad-stat__n"><?php echo $stats['paid']; ?></span>
                <span class="ad-stat__l">Pagate</span>
            </a>
        </div>

        <div class="ad-section">
            <div class="ad-actions">
                <a href="<?php echo esc_url($bu); ?>"  class="ad-btn ad-btn--sand">Tutte le prenotazioni</a>
                <a href="<?php echo esc_url($ru); ?>"  class="ad-btn ad-btn--ghost">🛏️ Camere</a>
                <a href="<?php echo esc_url($rnu); ?>" class="ad-btn ad-btn--ghost">+ Nuova camera</a>
                <a href="<?php echo esc_url($su); ?>"  class="ad-btn ad-btn--ghost">⚙️ Impostazioni</a>
            </div>
        </div>

        <?php if (!empty($rows)) : ?>
        <ul class="ad-list">
            <?php foreach ($rows as $b) :
                [$lbl, $cls] = $smap[$b->status] ?? [ucfirst((string)$b->status), ''];
                $link = admin_url('admin.php?page=alm-bookings&action=view&id=' . (int)$b->id);
            ?>
            <li class="ad-row">
                <span>
                    <a href="<?php echo esc_url($link); ?>" class="ad-row__name">
                        <?php echo esc_html($b->guest_name ?: '—'); ?>
                    </a><br>
                    <span class="ad-row__dates">
                        <?php
                        echo esc_html(
                            ($b->checkin  ? date_i18n('d M', strtotime((string)$b->checkin))      : '?') .
                            ' → ' .
                            ($b->checkout ? date_i18n('d M Y', strtotime((string)$b->checkout)) : '?')
                        );
                        if (!empty($b->total_price)) echo ' · €' . esc_html(number_format((float)$b->total_price, 0, ',', '.'));
                        ?>
                    </span>
                </span>
                <span class="ad-badge <?php echo esc_attr($cls); ?>"><?php echo esc_html($lbl); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
            <p class="ad-empty">Nessuna prenotazione. <a href="<?php echo esc_url($bu); ?>">Vai alle prenotazioni →</a></p>
        <?php endif; ?>
    </div>
    <?php
    // JS solo una volta
    alm_dash_inline_js();
}

/* ═══════════════════════════════════════════════════════════════
   WIDGET 2 — SERVIZI CAMERE
   ═══════════════════════════════════════════════════════════════ */

function alm_w_services(): void {
    if (!current_user_can('edit_posts')) {
        echo '<p class="ad-empty">Permessi insufficienti.</p>'; return;
    }

    $rooms = get_posts([
        'post_type'      => 'almaretna_room',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    $all_amenities = alm_all_amenities();
    $nonce         = wp_create_nonce(ALM_DASH_NONCE);
    ?>
    <div class="ad">
        <div class="ad-head">
            <span class="ad-head__label">Clicca un servizio per attivarlo / disattivarlo</span>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=almaretna_room')); ?>" class="ad-head__link">Gestisci camere →</a>
        </div>

        <?php if (empty($rooms)) : ?>
            <p class="ad-empty">Nessuna camera trovata. <a href="<?php echo esc_url(admin_url('post-new.php?post_type=almaretna_room')); ?>">Crea la prima →</a></p>
        <?php else : ?>
        <div class="ad-room-grid">
            <?php foreach ($rooms as $room) :
                $active_slugs = wp_get_object_terms($room->ID, 'amenity', ['fields' => 'slugs']);
                if (!is_array($active_slugs)) $active_slugs = [];
                $has_thumb = has_post_thumbnail($room->ID);
                $thumb_url = $has_thumb ? get_the_post_thumbnail_url($room->ID, 'thumbnail') : '';
            ?>
            <div class="ad-room-row ad-room-row--svc">

                <?php /* Thumbnail — stessa card di Foto Camere */ ?>
                <div class="ad-room-row__thumb <?php echo $has_thumb ? '' : 'ad-room-row__thumb--empty'; ?>">
                    <?php if ($has_thumb) : ?>
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($room->post_title); ?>" />
                    <?php else : ?>
                        🖼️
                    <?php endif; ?>
                </div>

                <?php /* Body: nome + chips */ ?>
                <div class="ad-room-row__body">
                    <div class="ad-room-row__top">
                        <a href="<?php echo esc_url(get_edit_post_link($room->ID)); ?>"
                           class="ad-room-row__name-link">
                            <?php echo esc_html($room->post_title); ?>
                        </a>
                        <a href="<?php echo esc_url(get_edit_post_link($room->ID)); ?>"
                           class="ad-btn ad-btn--ghost ad-btn--sm" title="Modifica camera">✏️</a>
                    </div>

                    <div class="ad-svc-chips">
                        <?php foreach ($all_amenities as $slug => [$icon, $label]) :
                            $is_on = in_array($slug, $active_slugs, true);
                        ?>
                        <button class="ad-chip <?php echo $is_on ? 'on' : ''; ?>"
                                data-room="<?php echo (int)$room->ID; ?>"
                                data-amenity="<?php echo esc_attr($slug); ?>"
                                data-nonce="<?php echo esc_attr($nonce); ?>"
                                onclick="almToggleAmenity(this)"
                                title="<?php echo esc_attr($label); ?>">
                            <?php echo $icon; ?> <?php echo esc_html($label); ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
   WIDGET 3 — FOTO SITO
   ═══════════════════════════════════════════════════════════════ */

function alm_w_media(): void {
    if (!current_user_can('upload_files') && !current_user_can('edit_posts')) {
        echo '<p class="ad-empty">Permessi insufficienti.</p>'; return;
    }

    $sections = alm_site_photo_slots();
    $nonce    = wp_create_nonce(ALM_DASH_NONCE);
    ?>
    <div class="ad">
        <div class="ad-head">
            <span class="ad-head__label">Foto per posizione nel sito — clicca per caricare</span>
            <a href="<?php echo esc_url(admin_url('upload.php')); ?>" class="ad-head__link">Libreria →</a>
        </div>

        <?php foreach ($sections as $section_key => $section) : ?>
        <div class="ad-slot-section">
            <p class="ad-slot-section__title"><?php echo esc_html($section['label']); ?></p>
            <div class="ad-slot-grid">
                <?php foreach ($section['slots'] as $key => $slot) :
                    $saved_id  = (int) get_option('alm_site_photo_' . $key, 0);
                    $has_photo = $saved_id > 0;
                    $thumb_src = $has_photo ? wp_get_attachment_image_src($saved_id, 'thumbnail') : null;
                    $thumb_url = ($thumb_src && !empty($thumb_src[0])) ? $thumb_src[0] : '';
                ?>
                <div class="ad-slot <?php echo $has_photo ? 'has-photo' : ''; ?>"
                     id="alm-slot-<?php echo esc_attr($key); ?>">
                    <div class="ad-slot__preview <?php echo !$has_photo ? 'ad-slot__preview--empty' : ''; ?>">
                        <?php if ($has_photo && $thumb_url) : ?>
                            <img src="<?php echo esc_url($thumb_url); ?>"
                                 id="alm-slot-img-<?php echo esc_attr($key); ?>"
                                 alt="<?php echo esc_attr($slot['label']); ?>" />
                        <?php else : ?>
                            <span id="alm-slot-img-<?php echo esc_attr($key); ?>">📷</span>
                        <?php endif; ?>
                        <div class="ad-slot__overlay">
                            <button class="ad-slot__overlay-btn"
                                    onclick="almPickSitePhoto('<?php echo esc_js($key); ?>','<?php echo esc_js($nonce); ?>')">
                                <?php echo $has_photo ? 'Cambia' : 'Scegli'; ?>
                            </button>
                            <?php if ($has_photo) : ?>
                            <button class="ad-slot__overlay-btn ad-slot__overlay-btn--del"
                                    onclick="almRemoveSitePhoto('<?php echo esc_js($key); ?>','<?php echo esc_js($nonce); ?>')">
                                Rimuovi
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ad-slot__label"><?php echo esc_html($slot['label']); ?></div>
                    <div class="ad-slot__desc"><?php echo esc_html($slot['desc']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="ad-section">
            <div class="ad-actions">
                <a href="<?php echo esc_url(admin_url('media-new.php')); ?>" class="ad-btn ad-btn--sand">➕ Carica foto</a>
                <a href="<?php echo esc_url(admin_url('upload.php')); ?>" class="ad-btn ad-btn--ghost">📂 Libreria</a>
            </div>
        </div>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
   WIDGET 4 — FOTO CAMERE
   ═══════════════════════════════════════════════════════════════ */

function alm_w_room_photos(): void {
    if (!current_user_can('edit_posts')) {
        echo '<p class="ad-empty">Permessi insufficienti.</p>'; return;
    }

    $rooms = get_posts([
        'post_type'      => 'almaretna_room',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    $nonce = wp_create_nonce(ALM_DASH_NONCE);
    ?>
    <div class="ad">
        <div class="ad-head">
            <span class="ad-head__label">Galleria foto per camera — ★ = principale nella card</span>
            <a href="<?php echo esc_url(admin_url('upload.php')); ?>" class="ad-head__link">Libreria →</a>
        </div>

        <?php if (empty($rooms)) : ?>
            <p class="ad-empty">Nessuna camera trovata. <a href="<?php echo esc_url(admin_url('post-new.php?post_type=almaretna_room')); ?>">Crea la prima →</a></p>
        <?php else : ?>
        <div>
            <?php foreach ($rooms as $room) :
                $raw     = get_post_meta($room->ID, '_room_gallery', true);
                $gallery = $raw ? json_decode($raw, true) : [];
                if (!is_array($gallery)) $gallery = [];
                $gallery = array_values(array_unique(array_map('intval', $gallery)));

                $main_id = (int) get_post_thumbnail_id($room->ID);

                // Thumb principale non ancora in galleria → aggiungila in testa
                if ($main_id > 0 && !in_array($main_id, $gallery, true)) {
                    array_unshift($gallery, $main_id);
                    update_post_meta($room->ID, '_room_gallery', wp_json_encode($gallery));
                }

                $count = count($gallery);
                $missing = max(0, 4 - $count);
            ?>
            <div class="ad-room-photos-row" id="alm-rp-<?php echo (int)$room->ID; ?>">

                <div class="ad-room-photos-header">
                    <a href="<?php echo esc_url((string) get_edit_post_link($room->ID)); ?>"
                       class="ad-room-photos-header__name">
                        <?php echo esc_html($room->post_title); ?>
                    </a>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <?php if ($missing > 0) : ?>
                        <span class="ad-room-count-warn" id="alm-cnt-<?php echo (int)$room->ID; ?>">
                            ⚠ <?php echo $missing; ?> foto mancanti
                        </span>
                        <?php else : ?>
                        <span class="ad-room-count-ok" id="alm-cnt-<?php echo (int)$room->ID; ?>">
                            ✓ <?php echo $count; ?> foto
                        </span>
                        <?php endif; ?>
                        <a href="<?php echo esc_url((string) get_edit_post_link($room->ID)); ?>"
                           class="ad-btn ad-btn--ghost ad-btn--sm">✏️</a>
                    </div>
                </div>

                <div class="ad-room-gallery" id="alm-gallery-<?php echo (int)$room->ID; ?>">
                    <?php foreach ($gallery as $photo_id) :
                        $photo_id  = (int) $photo_id;
                        if (!$photo_id) continue;
                        $t_src     = wp_get_attachment_image_src($photo_id, 'thumbnail');
                        if (!$t_src) continue;
                        $is_main   = ($photo_id === $main_id);
                    ?>
                    <div class="ad-room-gallery__item <?php echo $is_main ? 'is-main' : ''; ?>"
                         id="alm-gp-<?php echo (int)$room->ID; ?>-<?php echo $photo_id; ?>">
                        <img src="<?php echo esc_url($t_src[0]); ?>"
                             alt="<?php echo esc_attr($room->post_title); ?>" />
                        <div class="ad-room-gallery__actions">
                            <button class="ad-room-gallery__star"
                                    title="<?php echo $is_main ? 'Foto principale' : 'Imposta come principale'; ?>"
                                    onclick="almSetMainRoomPhoto(<?php echo (int)$room->ID; ?>,<?php echo $photo_id; ?>,'<?php echo esc_js($nonce); ?>')">
                                <?php echo $is_main ? '★' : '☆'; ?>
                            </button>
                            <button class="ad-room-gallery__del"
                                    title="Rimuovi foto"
                                    onclick="almRemoveRoomPhoto(<?php echo (int)$room->ID; ?>,<?php echo $photo_id; ?>,'<?php echo esc_js($nonce); ?>')">
                                ✕
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <button class="ad-room-gallery__add"
                            onclick="almAddRoomPhoto(<?php echo (int)$room->ID; ?>,'<?php echo esc_js($nonce); ?>')">
                        <span class="ad-room-gallery__add-icon">＋</span>
                        Foto
                    </button>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="ad-section">
            <p class="ad-section__title">Aggiungi nuova camera</p>
            <div class="ad-actions">
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=almaretna_room')); ?>" class="ad-btn ad-btn--ghost">+ Nuova camera</a>
                <a href="<?php echo esc_url(admin_url('media-new.php')); ?>" class="ad-btn ad-btn--ghost">➕ Carica foto</a>
            </div>
        </div>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
   WIDGET 5 — PREZZI CAMERE
   ═══════════════════════════════════════════════════════════════ */

function alm_w_prices(): void {
    if (!current_user_can('edit_posts')) {
        echo '<p class="ad-empty">Permessi insufficienti.</p>'; return;
    }

    $rooms = get_posts([
        'post_type'      => 'almaretna_room',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    $nonce = wp_create_nonce(ALM_DASH_NONCE);
    ?>
    <div class="ad">
        <div class="ad-head">
            <span class="ad-head__label">Prezzo base + tariffe speciali per data</span>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=almaretna_room')); ?>" class="ad-head__link">Camere →</a>
        </div>

        <?php if (empty($rooms)) : ?>
            <p class="ad-empty">Nessuna camera trovata.</p>
        <?php else :
            foreach ($rooms as $room) :
                $base_price = (float) get_post_meta($room->ID, '_room_base_price', true);
                $specials   = ALM_Pricing::get_all_rates_for_room($room->ID);
                $has_thumb     = has_post_thumbnail($room->ID);
                $thumb_url     = $has_thumb ? get_the_post_thumbnail_url($room->ID, 'thumbnail') : '';
                $uid           = 'alm-pr-' . (int)$room->ID;
        ?>
        <div class="ad-price-room">

            <!-- Intestazione camera -->
            <div class="ad-price-room__head">
                <div class="ad-room-row__thumb <?php echo $has_thumb ? '' : 'ad-room-row__thumb--empty'; ?>">
                    <?php if ($has_thumb) : ?>
                        <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($room->post_title); ?>" />
                    <?php else : ?>
                        🛏️
                    <?php endif; ?>
                </div>
                <span class="ad-price-room__name"><?php echo esc_html($room->post_title); ?></span>
                <a href="<?php echo esc_url(get_edit_post_link($room->ID)); ?>"
                   class="ad-btn ad-btn--ghost ad-btn--sm" title="Modifica">✏️</a>
            </div>

            <!-- Prezzo base -->
            <div class="ad-price-base">
                <span class="ad-price-base__label">Base</span>
                <input type="number"
                       class="ad-price-input"
                       id="<?php echo esc_attr($uid); ?>-base"
                       value="<?php echo esc_attr($base_price > 0 ? $base_price : ''); ?>"
                       min="0" step="0.5" placeholder="0"
                       onkeydown="if(event.key==='Enter'){almSaveBasePrice(<?php echo (int)$room->ID; ?>,'<?php echo esc_js($nonce); ?>')}" />
                <span class="ad-price-suffix">€ / notte</span>
                <button class="ad-save-btn"
                        id="<?php echo esc_attr($uid); ?>-save-btn"
                        onclick="almSaveBasePrice(<?php echo (int)$room->ID; ?>,'<?php echo esc_js($nonce); ?>')">
                    Salva
                </button>
            </div>

            <!-- Lista prezzi speciali -->
            <?php $sp_count = count($specials); ?>
            <button class="ad-specials-toggle"
                    id="<?php echo esc_attr($uid); ?>-specials-toggle"
                    onclick="almToggleSpecials('<?php echo esc_js($uid); ?>')">
                <span id="<?php echo esc_attr($uid); ?>-specials-count">
                    <?php if ($sp_count > 0) : ?>
                        <?php echo $sp_count; ?> <?php echo $sp_count === 1 ? 'periodo' : 'periodi'; ?> definiti
                    <?php else : ?>
                        Nessun periodo speciale
                    <?php endif; ?>
                </span>
                <span class="ad-specials-toggle__icon" id="<?php echo esc_attr($uid); ?>-specials-icon">▼</span>
            </button>
            <ul class="ad-specials" id="<?php echo esc_attr($uid); ?>-specials"
                style="<?php echo $sp_count > 0 ? 'display:none' : ''; ?>">
                <?php foreach ($specials as $sp) :
                    if (empty($sp['id'])) continue;
                    $d_from       = date_i18n('d/m/Y', strtotime((string)($sp['date_from'] ?? '')));
                    // date_to è esclusiva: mostra il giorno precedente come fine periodo
                    $to_inclusive = date('Y-m-d', strtotime((string)($sp['date_to'] ?? '') . ' -1 day'));
                    $d_to         = ($to_inclusive !== ($sp['date_from'] ?? ''))
                        ? ' → ' . date_i18n('d/m/Y', strtotime($to_inclusive))
                        : '';
                ?>
                <li class="ad-special-item" id="alm-sp-<?php echo esc_attr($sp['id']); ?>">
                    <span class="ad-special-item__note"><?php echo esc_html($sp['rate_name'] ?: '—'); ?></span>
                    <span class="ad-special-item__dates"><?php echo esc_html($d_from . $d_to); ?></span>
                    <span class="ad-special-item__price">€<?php echo esc_html(number_format((float)($sp['price_per_night'] ?? 0), 0, ',', '.')); ?></span>
                    <button class="ad-del-btn"
                            title="Elimina"
                            onclick="almDeleteSpecialPrice(this,<?php echo (int)$room->ID; ?>,<?php echo (int)$sp['id']; ?>,'<?php echo esc_js($nonce); ?>')">✕</button>
                </li>
                <?php endforeach; ?>
            </ul>

            <!-- Toggle form aggiungi -->
            <button class="ad-add-toggle"
                    id="<?php echo esc_attr($uid); ?>-toggle"
                    onclick="almToggleAddForm('<?php echo esc_js($uid); ?>')">
                + Aggiungi tariffa speciale
            </button>

            <!-- Form aggiungi prezzo speciale -->
            <div class="ad-add-form" id="<?php echo esc_attr($uid); ?>-form">
                <div class="ad-add-form__row">
                    <span class="ad-add-form__label">Dal</span>
                    <input type="date" class="ad-date-input" id="<?php echo esc_attr($uid); ?>-from" />
                    <span class="ad-add-form__label">Al</span>
                    <input type="date" class="ad-date-input" id="<?php echo esc_attr($uid); ?>-to" />
                </div>
                <div class="ad-add-form__row">
                    <span class="ad-add-form__label">€</span>
                    <input type="number" class="ad-price-input ad-price-input--sm" id="<?php echo esc_attr($uid); ?>-sp-price"
                           min="0" step="0.5" placeholder="Prezzo" />
                    <span class="ad-price-suffix">/notte</span>
                    <input type="text" class="ad-note-input" id="<?php echo esc_attr($uid); ?>-sp-note"
                           placeholder="Etichetta (es. Estate, Natale…)" maxlength="40" />
                </div>
                <div class="ad-add-form__actions">
                    <button class="ad-btn ad-btn--ghost ad-btn--sm"
                            onclick="almToggleAddForm('<?php echo esc_js($uid); ?>')">Annulla</button>
                    <button class="ad-save-btn"
                            onclick="almAddSpecialPrice(<?php echo (int)$room->ID; ?>,'<?php echo esc_js($uid); ?>','<?php echo esc_js($nonce); ?>')">
                        Aggiungi
                    </button>
                </div>
            </div>

        </div>
        <?php endforeach; endif; ?>
    </div>
    <?php
}

/* ═══════════════════════════════════════════════════════════════
   JS INLINE — dichiarato una sola volta
   ═══════════════════════════════════════════════════════════════ */

function alm_dash_inline_js(): void {
    static $printed = false;
    if ($printed) return;
    $printed = true;
    ?>
    <script>
    /* ── Copia shortcode ── */
    function almCopy(btn, code) {
        function ok(){
            btn.textContent = '✓ Copiato';
            btn.classList.add('copied');
            setTimeout(function(){ btn.textContent = 'Copia'; btn.classList.remove('copied'); }, 2200);
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(code).then(ok);
        } else {
            var ta = document.createElement('textarea');
            ta.value = code; ta.style.cssText = 'position:fixed;top:-9999px;opacity:0';
            document.body.appendChild(ta); ta.focus(); ta.select();
            try { document.execCommand('copy'); ok(); } catch(e){}
            document.body.removeChild(ta);
        }
    }

    /* ── Toggle amenity via AJAX ── */
    function almToggleAmenity(btn) {
        var roomId  = btn.dataset.room;
        var amenity = btn.dataset.amenity;
        var nonce   = btn.dataset.nonce;
        var enable  = !btn.classList.contains('on'); // inverte lo stato

        btn.classList.add('saving');

        var fd = new FormData();
        fd.append('action',  'alm_toggle_amenity');
        fd.append('nonce',   nonce);
        fd.append('room_id', roomId);
        fd.append('amenity', amenity);
        fd.append('enable',  enable ? '1' : '0');

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                btn.classList.remove('saving');
                if (data.success) {
                    btn.classList.toggle('on', enable);
                    btn.classList.add('saved-ok');
                    setTimeout(function(){ btn.classList.remove('saved-ok'); }, 900);
                }
            })
            .catch(function(){ btn.classList.remove('saving'); });
    }

    /* ── Media uploader per foto camere ── */
    function almPickPhoto(roomId, nonce) {
        if (typeof wp === 'undefined' || !wp.media) {
            alert('Media uploader non disponibile. Aggiorna la pagina.');
            return;
        }
        var frame = wp.media({
            title:    'Scegli immagine per la camera',
            button:   { text: 'Usa questa immagine' },
            multiple: false,
            library:  { type: 'image' }
        });
        frame.on('select', function() {
            var att  = frame.state().get('selection').first().toJSON();
            var wrap = document.getElementById('alm-thumb-wrap-' + roomId);
            var img  = document.getElementById('alm-thumb-img-'  + roomId);
            if (wrap) wrap.classList.add('is-loading');

            var fd = new FormData();
            fd.append('action',   'alm_set_room_thumb');
            fd.append('nonce',    nonce);
            fd.append('room_id',  roomId);
            fd.append('media_id', att.id);

            fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    if (wrap) wrap.classList.remove('is-loading');
                    if (data.success && data.data.thumb) {
                        wrap.classList.remove('ad-room-row__thumb--empty');
                        // Sostituisce testo/icona con <img>
                        if (img && img.tagName !== 'IMG') {
                            var newImg = document.createElement('img');
                            newImg.id  = img.id;
                            newImg.alt = '';
                            img.parentNode.replaceChild(newImg, img);
                            img = newImg;
                        }
                        if (img) img.src = data.data.thumb + '?' + Date.now();
                        // Aggiorna testo pulsante
                        var row = document.getElementById('alm-rr-' + roomId);
                        if (row) {
                            var pickBtn = row.querySelector('.ad-btn--sand');
                            if (pickBtn) pickBtn.textContent = '📷 Cambia';
                        }
                    }
                })
                .catch(function(){ if (wrap) wrap.classList.remove('is-loading'); });
        });
        frame.open();
    }

    /* ── Prezzi camere ── */

    function almToggleSpecials(uid) {
        var list = document.getElementById(uid + '-specials');
        var icon = document.getElementById(uid + '-specials-icon');
        if (!list) return;
        var hidden = list.style.display === 'none';
        list.style.display = hidden ? '' : 'none';
        if (icon) icon.textContent = hidden ? '▲' : '▼';
    }

    function almUpdateSpecialsCount(uid) {
        var list  = document.getElementById(uid + '-specials');
        var badge = document.getElementById(uid + '-specials-count');
        if (!list || !badge) return;
        var n = list.querySelectorAll('.ad-special-item').length;
        badge.textContent = n > 0
            ? n + ' ' + (n === 1 ? 'periodo' : 'periodi') + ' definiti'
            : 'Nessun periodo speciale';
    }

    function almToggleAddForm(uid) {
        var form   = document.getElementById(uid + '-form');
        var toggle = document.getElementById(uid + '-toggle');
        if (!form) return;
        var opening = !form.classList.contains('open');
        form.classList.toggle('open', opening);
        if (toggle) toggle.textContent = opening ? '▲ Chiudi' : '+ Aggiungi tariffa speciale';
    }

    function almSaveBasePrice(roomId, nonce) {
        var uid = 'alm-pr-' + roomId;
        var inp = document.getElementById(uid + '-base');
        var btn = document.getElementById(uid + '-save-btn');
        if (!inp || !btn) return;
        var price = parseFloat(inp.value);
        if (isNaN(price) || price < 0) { inp.focus(); return; }

        btn.classList.add('saving');

        var fd = new FormData();
        fd.append('action',  'alm_save_base_price');
        fd.append('nonce',   nonce);
        fd.append('room_id', roomId);
        fd.append('price',   price);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                btn.classList.remove('saving');
                if (data.success) {
                    btn.classList.add('saved');
                    setTimeout(function(){ btn.classList.remove('saved'); }, 1500);
                }
            })
            .catch(function(){ btn.classList.remove('saving'); });
    }

    function almAddSpecialPrice(roomId, uid, nonce) {
        var fromEl  = document.getElementById(uid + '-from');
        var toEl    = document.getElementById(uid + '-to');
        var priceEl = document.getElementById(uid + '-sp-price');
        var noteEl  = document.getElementById(uid + '-sp-note');
        if (!fromEl || !toEl || !priceEl) return;

        var from  = fromEl.value;
        var to    = toEl.value   || from;
        var price = parseFloat(priceEl.value);
        var note  = noteEl ? noteEl.value : '';

        if (!from || isNaN(price) || price < 0) {
            if (!from)  fromEl.focus();
            else        priceEl.focus();
            return;
        }

        var fd = new FormData();
        fd.append('action',  'alm_add_special_price');
        fd.append('nonce',   nonce);
        fd.append('room_id', roomId);
        fd.append('from',    from);
        fd.append('to',      to);
        fd.append('price',   price);
        fd.append('note',    note);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (!data.success) { alert(data.data || 'Errore nel salvataggio'); return; }
                var sp   = data.data;
                var list = document.getElementById(uid + '-specials');
                if (list) {
                    // date_to è esclusiva, mostra il giorno precedente
                    var toInclusive = new Date(sp.date_to);
                    toInclusive.setDate(toInclusive.getDate() - 1);
                    var dFrom = new Date(sp.date_from).toLocaleDateString('it-IT');
                    var dTo   = (toInclusive.toISOString().slice(0,10) !== sp.date_from)
                        ? ' → ' + toInclusive.toLocaleDateString('it-IT') : '';
                    var li = document.createElement('li');
                    li.className = 'ad-special-item';
                    li.id = 'alm-sp-' + sp.id;
                    li.innerHTML =
                        '<span class="ad-special-item__note">' + (sp.rate_name || '—') + '</span>' +
                        '<span class="ad-special-item__dates">' + dFrom + dTo + '</span>' +
                        '<span class="ad-special-item__price">€' + Math.round(sp.price_per_night) + '</span>' +
                        '<button class="ad-del-btn" title="Elimina" onclick="almDeleteSpecialPrice(this,' + roomId + ',' + sp.id + ',\'' + nonce + '\')">✕</button>';
                    list.appendChild(li);
                }
                // Reset form
                fromEl.value = ''; toEl.value = ''; priceEl.value = '';
                if (noteEl) noteEl.value = '';
                almToggleAddForm(uid);
                almUpdateSpecialsCount(uid);
            });
    }

    function almDeleteSpecialPrice(btn, roomId, entryId, nonce) {
        if (!confirm('Eliminare questa tariffa?')) return;
        btn.classList.add('deleting');

        var fd = new FormData();
        fd.append('action',   'alm_delete_special_price');
        fd.append('nonce',    nonce);
        fd.append('room_id',  roomId);
        fd.append('entry_id', entryId);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var li = document.getElementById('alm-sp-' + entryId);
                    if (li) li.remove();
                    almUpdateSpecialsCount('alm-pr-' + roomId);
                } else {
                    btn.classList.remove('deleting');
                }
            })
            .catch(function(){ btn.classList.remove('deleting'); });
    }

    /* ── Foto Sito — slot nominati ── */

    function almPickSitePhoto(key, nonce) {
        if (typeof wp === 'undefined' || !wp.media) {
            alert('Media uploader non disponibile. Aggiorna la pagina.');
            return;
        }
        var frame = wp.media({
            title:    'Scegli foto per: ' + key,
            button:   { text: 'Usa questa foto' },
            multiple: false,
            library:  { type: 'image' }
        });
        frame.on('select', function () {
            var att   = frame.state().get('selection').first().toJSON();
            var slot  = document.getElementById('alm-slot-' + key);
            var imgEl = document.getElementById('alm-slot-img-' + key);
            if (!slot) return;

            var fd = new FormData();
            fd.append('action',   'alm_save_site_photo');
            fd.append('nonce',    nonce);
            fd.append('key',      key);
            fd.append('media_id', att.id);

            fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success) return;
                    slot.classList.add('has-photo');
                    var preview = slot.querySelector('.ad-slot__preview');
                    if (preview) preview.classList.remove('ad-slot__preview--empty');

                    if (imgEl && imgEl.tagName !== 'IMG') {
                        var img = document.createElement('img');
                        img.id  = imgEl.id; img.alt = '';
                        imgEl.parentNode.replaceChild(img, imgEl);
                        imgEl = img;
                    }
                    if (imgEl && data.data.thumb) {
                        imgEl.src = data.data.thumb + '?' + Date.now();
                    }
                    // Overlay: mostra Cambia + Rimuovi
                    var overlay = slot.querySelector('.ad-slot__overlay');
                    if (overlay) {
                        overlay.innerHTML =
                            '<button class="ad-slot__overlay-btn" onclick="almPickSitePhoto(\'' + key + '\',\'' + nonce + '\')">Cambia</button>' +
                            '<button class="ad-slot__overlay-btn ad-slot__overlay-btn--del" onclick="almRemoveSitePhoto(\'' + key + '\',\'' + nonce + '\')">Rimuovi</button>';
                    }
                });
        });
        frame.open();
    }

    function almRemoveSitePhoto(key, nonce) {
        var slot = document.getElementById('alm-slot-' + key);
        if (!slot) return;

        var fd = new FormData();
        fd.append('action', 'alm_remove_site_photo');
        fd.append('nonce',  nonce);
        fd.append('key',    key);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) return;
                slot.classList.remove('has-photo');
                var preview = slot.querySelector('.ad-slot__preview');
                if (preview) preview.classList.add('ad-slot__preview--empty');
                var imgEl = document.getElementById('alm-slot-img-' + key);
                if (imgEl && imgEl.tagName === 'IMG') {
                    var span = document.createElement('span');
                    span.id = imgEl.id; span.textContent = '📷';
                    imgEl.parentNode.replaceChild(span, imgEl);
                }
                var overlay = slot.querySelector('.ad-slot__overlay');
                if (overlay) {
                    overlay.innerHTML =
                        '<button class="ad-slot__overlay-btn" onclick="almPickSitePhoto(\'' + key + '\',\'' + nonce + '\')">Scegli</button>';
                }
            });
    }

    /* ── Foto Camere — galleria multi-foto ── */

    function almAddRoomPhoto(roomId, nonce) {
        if (typeof wp === 'undefined' || !wp.media) {
            alert('Media uploader non disponibile. Aggiorna la pagina.');
            return;
        }
        var frame = wp.media({
            title:    'Aggiungi foto alla camera',
            button:   { text: 'Aggiungi foto' },
            multiple: true,
            library:  { type: 'image' }
        });
        frame.on('select', function () {
            var selection = frame.state().get('selection');
            selection.each(function (att) {
                var attData = att.toJSON();
                var fd = new FormData();
                fd.append('action',   'alm_add_room_gallery_photo');
                fd.append('nonce',    nonce);
                fd.append('room_id',  roomId);
                fd.append('media_id', attData.id);

                fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) return;
                        // Evita duplicati nell'UI
                        if (document.getElementById('alm-gp-' + roomId + '-' + data.data.media_id)) return;
                        almAppendGalleryItem(roomId, data.data.media_id, data.data.thumb, data.data.is_main, nonce);
                        almUpdateRoomCount(roomId);
                    });
            });
        });
        frame.open();
    }

    function almAppendGalleryItem(roomId, mediaId, thumbUrl, isMain, nonce) {
        var gallery = document.getElementById('alm-gallery-' + roomId);
        if (!gallery) return;
        var addBtn = gallery.querySelector('.ad-room-gallery__add');

        var div = document.createElement('div');
        div.className = 'ad-room-gallery__item' + (isMain ? ' is-main' : '');
        div.id = 'alm-gp-' + roomId + '-' + mediaId;
        div.innerHTML =
            '<img src="' + thumbUrl + '" alt="" />' +
            '<div class="ad-room-gallery__actions">' +
                '<button class="ad-room-gallery__star"' +
                    ' title="' + (isMain ? 'Foto principale' : 'Imposta come principale') + '"' +
                    ' onclick="almSetMainRoomPhoto(' + roomId + ',' + mediaId + ',\'' + nonce + '\')">' +
                    (isMain ? '★' : '☆') +
                '</button>' +
                '<button class="ad-room-gallery__del" title="Rimuovi foto"' +
                    ' onclick="almRemoveRoomPhoto(' + roomId + ',' + mediaId + ',\'' + nonce + '\')">' +
                    '✕' +
                '</button>' +
            '</div>';

        if (addBtn) { gallery.insertBefore(div, addBtn); }
        else        { gallery.appendChild(div); }
    }

    function almRemoveRoomPhoto(roomId, mediaId, nonce) {
        var item = document.getElementById('alm-gp-' + roomId + '-' + mediaId);
        if (item) item.style.opacity = '.35';

        var fd = new FormData();
        fd.append('action',   'alm_remove_room_gallery_photo');
        fd.append('nonce',    nonce);
        fd.append('room_id',  roomId);
        fd.append('media_id', mediaId);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) { if (item) item.style.opacity = ''; return; }
                if (item) item.remove();
                almUpdateRoomCount(roomId);
                // Se era la principale, evidenzia la prima rimasta
                almRefreshMainStar(roomId, data.data.new_main);
            })
            .catch(function () { if (item) item.style.opacity = ''; });
    }

    function almSetMainRoomPhoto(roomId, mediaId, nonce) {
        var fd = new FormData();
        fd.append('action',   'alm_set_room_thumb');
        fd.append('nonce',    nonce);
        fd.append('room_id',  roomId);
        fd.append('media_id', mediaId);

        fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) return;
                var gallery = document.getElementById('alm-gallery-' + roomId);
                if (!gallery) return;
                gallery.querySelectorAll('.ad-room-gallery__item').forEach(function (it) {
                    var isThis = it.id === ('alm-gp-' + roomId + '-' + mediaId);
                    it.classList.toggle('is-main', isThis);
                    var star = it.querySelector('.ad-room-gallery__star');
                    if (star) {
                        star.textContent = isThis ? '★' : '☆';
                        star.title       = isThis ? 'Foto principale' : 'Imposta come principale';
                    }
                });
            });
    }

    function almUpdateRoomCount(roomId) {
        var gallery = document.getElementById('alm-gallery-' + roomId);
        var badge   = document.getElementById('alm-cnt-' + roomId);
        if (!gallery || !badge) return;
        var count   = gallery.querySelectorAll('.ad-room-gallery__item').length;
        var missing = Math.max(0, 4 - count);
        if (missing > 0) {
            badge.className     = 'ad-room-count-warn';
            badge.textContent   = '⚠ ' + missing + ' foto mancanti';
        } else {
            badge.className     = 'ad-room-count-ok';
            badge.textContent   = '✓ ' + count + ' foto';
        }
    }

    function almRefreshMainStar(roomId, newMainId) {
        var gallery = document.getElementById('alm-gallery-' + roomId);
        if (!gallery) return;
        gallery.querySelectorAll('.ad-room-gallery__item').forEach(function (it) {
            var isMain = newMainId && it.id === ('alm-gp-' + roomId + '-' + newMainId);
            it.classList.toggle('is-main', isMain);
            var star = it.querySelector('.ad-room-gallery__star');
            if (star) {
                star.textContent = isMain ? '★' : '☆';
                star.title       = isMain ? 'Foto principale' : 'Imposta come principale';
            }
        });
    }
    </script>
    <?php
}

// Aggancia JS anche al widget media (viene dopo bookings)
add_action('wp_dashboard_setup', function(): void {
    add_action('wp_footer', 'alm_dash_inline_js', 99);
    add_action('admin_footer', 'alm_dash_inline_js', 99);
}, 20);

/* ═══════════════════════════════════════════════════════════════
   RIORDINA MENU ADMIN
   ═══════════════════════════════════════════════════════════════ */

add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', function (array $menu_order): array {
    $priority = ['index.php', 'admin.php?page=alm-booking', 'upload.php', 'edit.php?post_type=almaretna_room'];
    $result   = array_values(array_filter($priority, fn($i) => in_array($i, $menu_order, true)));
    foreach ($menu_order as $item) {
        if (!in_array($item, $result, true)) $result[] = $item;
    }
    return $result;
});

/* ═══════════════════════════════════════════════════════════════
   ADMIN BAR
   ═══════════════════════════════════════════════════════════════ */

add_action('admin_bar_menu', function (WP_Admin_Bar $bar): void {
    if (!is_admin_bar_showing() || !current_user_can('edit_posts')) return;
    $bar->add_node(['id' => 'alm-quick', 'title' => '🏨 Almaretna', 'href' => admin_url()]);
    $bar->add_node(['id' => 'alm-q-book',   'parent' => 'alm-quick', 'title' => '📅 Prenotazioni', 'href' => admin_url('admin.php?page=alm-bookings')]); // alm-bookings = lista prenotazioni (submenu)
    $bar->add_node(['id' => 'alm-q-rooms',  'parent' => 'alm-quick', 'title' => '🛏️ Camere',        'href' => admin_url('edit.php?post_type=almaretna_room')]);
    if (current_user_can('upload_files'))
        $bar->add_node(['id' => 'alm-q-media', 'parent' => 'alm-quick', 'title' => '🖼️ Carica Foto', 'href' => admin_url('media-new.php')]);
    if (current_user_can('customize'))
        $bar->add_node(['id' => 'alm-q-cust', 'parent' => 'alm-quick', 'title' => '🎨 Foto sito',   'href' => admin_url('customize.php')]);
}, 999);
