<?php
/**
 * Almaretna Child — front-page.php
 * Design premium al livello delle migliori case vacanze d'Italia.
 *
 * @package AlmaretnaChild
 */
declare(strict_types=1);

get_header();

// ─── Dati ────────────────────────────────────────────────────────────────────

$rooms = get_posts([
    'post_type'      => 'almaretna_room',
    'posts_per_page' => -1,   // tutte le camere per il carosello
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
]);

$prenota_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
$prenota_url   = !empty($prenota_pages) ? get_permalink($prenota_pages[0]->ID) : home_url('/prenota/');

$camere_pages  = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-rooms.php', 'number' => 1]);
$camere_url    = !empty($camere_pages) ? get_permalink($camere_pages[0]->ID) : home_url('/camere/');

$settings   = get_option('alm_booking_settings', []);
$host_phone = sanitize_text_field($settings['host_phone'] ?? '');

// ─── Percorso base foto Facebook ─────────────────────────────
$_fb = content_url('uploads/2026/06/');

// Hero background — tinta unita premium (no immagine)
$hero_bg = '';
// Story — immagine principale
$villa_img = get_theme_mod('almaretna_villa_img', '');
if (!$villa_img) {
    $villa_img = $_fb . 'salottino-comune.webp';
}
// Story — immagine accent
$story_accent_img = $_fb . 'camera-matrimoniale.webp';

// Lingua corrente
$lang = alm_get_lang();

// Customizer text (IT usa Customizer; altre lingue usano i18n)
$hero_title    = ($lang === 'it')
    ? get_theme_mod('almaretna_hero_title',    alm_t('hero.title'))
    : alm_t('hero.title');
$hero_subtitle = ($lang === 'it')
    ? get_theme_mod('almaretna_hero_subtitle', alm_t('hero.subtitle'))
    : alm_t('hero.subtitle');
?>

<main id="main" class="home-page">

<?php /* ═══════════════════════════════════════════════════════
       HERO — Cinematografico, full-screen
       ════════════════════════════════════════════════════════ */ ?>

<section class="hero-premium" aria-label="Hero Almaretna">
    <div class="hero-premium__bg" id="heroBg"
         <?php if ($hero_bg) : ?>
         style="background-image:url('<?php echo esc_url($hero_bg); ?>')"
         <?php else : ?>
         style="background:linear-gradient(135deg,#2a1f14 0%,#1a130c 100%)"
         <?php endif; ?>></div>
    <div class="hero-premium__overlay"></div>

    <div class="hero-premium__content">
        <p class="hero-premium__eyebrow" data-reveal="fade">
            <?php echo alm_t('hero.eyebrow'); ?>
        </p>
        <h1 class="hero-premium__title" data-reveal="up" data-delay="100">
            <?php echo nl2br(esc_html($hero_title)); ?>
        </h1>
        <p class="hero-premium__subtitle" data-reveal="up" data-delay="200">
            <?php echo esc_html($hero_subtitle); ?>
        </p>
        <div class="hero-premium__actions" data-reveal="up" data-delay="300">
            <a href="<?php echo esc_url($prenota_url); ?>" class="hero-premium__cta-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html(alm_t('cta.book')); ?>
            </a>
            <a href="<?php echo esc_url($camere_url); ?>" class="hero-premium__cta-ghost">
                <?php echo esc_html(alm_t('cta.rooms')); ?>
            </a>
        </div>
    </div>

</section>

<?php /* ═══════════════════════════════════════════════════════
       BOOKING STRIP
       ════════════════════════════════════════════════════════ */ ?>

<div class="booking-strip" id="booking-strip">
    <form class="booking-strip__inner" method="get" action="<?php echo esc_url($prenota_url); ?>" id="hb-form">

        <div class="booking-strip__field">
            <label class="booking-strip__label" for="hb-checkin">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html(alm_t('strip.checkin')); ?>
            </label>
            <input type="text" class="booking-strip__input" id="hb-checkin"
                   placeholder="<?php echo esc_attr(alm_t('strip.date_ph')); ?>" readonly autocomplete="off" />
            <input type="hidden" name="checkin" id="hb-checkin-val"
                   value="<?php echo esc_attr($_GET['checkin'] ?? ''); ?>" />
        </div>

        <div class="booking-strip__field">
            <label class="booking-strip__label" for="hb-checkout">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html(alm_t('strip.checkout')); ?>
            </label>
            <input type="text" class="booking-strip__input" id="hb-checkout"
                   placeholder="<?php echo esc_attr(alm_t('strip.date_ph')); ?>" readonly autocomplete="off" />
            <input type="hidden" name="checkout" id="hb-checkout-val"
                   value="<?php echo esc_attr($_GET['checkout'] ?? ''); ?>" />
        </div>

        <div class="booking-strip__field">
            <label class="booking-strip__label" for="hb-adults">
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                <?php echo esc_html(alm_t('strip.guests')); ?>
            </label>
            <select class="booking-strip__input" name="adults" id="hb-adults" style="-webkit-appearance:none;appearance:none">
                <option value="1"><?php echo esc_html(alm_t('strip.a1')); ?></option>
                <option value="2" selected><?php echo esc_html(alm_t('strip.a2')); ?></option>
                <option value="3"><?php echo esc_html(alm_t('strip.a3')); ?></option>
                <option value="4"><?php echo esc_html(alm_t('strip.a4')); ?></option>
                <option value="5"><?php echo esc_html(alm_t('strip.a5')); ?></option>
            </select>
        </div>

        <button type="submit" class="booking-strip__btn" id="hb-submit">
            <?php echo esc_html(alm_t('strip.verify')); ?>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>

    </form>
</div>

<script>
/* Booking strip homepage — Flatpickr init */
(function () {
    'use strict';

    function pad(n) { return String(n).padStart(2, '0'); }
    function toYMD(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    /* Retry fino a 6s: SiteGround può caricare flatpickr async */
    var _fpAttempts = 0;
    function initBookingStrip() {
        if (typeof flatpickr === 'undefined') {
            if (_fpAttempts++ < 60) setTimeout(initBookingStrip, 100);
            return;
        }
        _runBookingStrip();
    }
    initBookingStrip();

    function _runBookingStrip() {
        if (typeof flatpickr === 'undefined') return;

        var locale = (flatpickr.l10ns && flatpickr.l10ns.it) ? flatpickr.l10ns.it : {};
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);

        var checkoutFp; // dichiarata prima per il riferimento incrociato

        var checkinFp = flatpickr('#hb-checkin', {
            locale:        locale,
            dateFormat:    'j F Y',
            minDate:       'today',
            disableMobile: true,
            onReady: function (_, __, fp) {
                fp.calendarContainer.classList.add('booking-cal');
            },
            onChange: function (selectedDates) {
                if (!selectedDates.length) return;
                var d = selectedDates[0];
                document.getElementById('hb-checkin-val').value = toYMD(d);
                // aggiorna min checkout al giorno dopo
                var nextDay = new Date(d);
                nextDay.setDate(nextDay.getDate() + 1);
                if (checkoutFp) {
                    checkoutFp.set('minDate', nextDay);
                    // se checkout è già impostato prima del checkin, resettalo
                    if (checkoutFp.selectedDates.length &&
                        checkoutFp.selectedDates[0] <= d) {
                        checkoutFp.clear();
                        document.getElementById('hb-checkout-val').value = '';
                    }
                    // apri checkout se non è ancora stato selezionato
                    if (!checkoutFp.selectedDates.length) {
                        setTimeout(function () { checkoutFp.open(); }, 120);
                    }
                }
            }
        });

        checkoutFp = flatpickr('#hb-checkout', {
            locale:        locale,
            dateFormat:    'j F Y',
            minDate:       tomorrow,
            disableMobile: true,
            onReady: function (_, __, fp) {
                fp.calendarContainer.classList.add('booking-cal');
            },
            onChange: function (selectedDates) {
                if (!selectedDates.length) return;
                document.getElementById('hb-checkout-val').value = toYMD(selectedDates[0]);
            }
        });

        // Pre-compila i display se arrivano da URL (es. redirect dal wizard)
        var preCheckin  = document.getElementById('hb-checkin-val').value;
        var preCheckout = document.getElementById('hb-checkout-val').value;
        if (preCheckin)  checkinFp.setDate(preCheckin,  true);
        if (preCheckout) checkoutFp.setDate(preCheckout, true);

        // Validazione prima del submit
        document.getElementById('hb-form').addEventListener('submit', function (e) {
            var ci = document.getElementById('hb-checkin-val').value;
            var co = document.getElementById('hb-checkout-val').value;
            if (!ci) {
                e.preventDefault();
                checkinFp.open();
                return;
            }
            if (!co) {
                e.preventDefault();
                checkoutFp.open();
            }
        });
    } // fine _runBookingStrip
})();
</script>

<?php /* ═══════════════════════════════════════════════════════
       AMENITIES BAR
       ════════════════════════════════════════════════════════ */ ?>

<div class="amenities-bar" data-reveal="up">
    <div class="amenities-bar__grid">

        <?php
        $icons = [
            '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M2 12h20M2 17c1.5 0 3-1 3-2s1.5-2 3-2 3 1 3 2 1.5 2 3 2 3-1 3-2"/><circle cx="7" cy="7" r="2"/><path d="M7 9v3"/></svg>',
            '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><polygon points="3 20 12 4 21 20"/><polyline points="3 20 8 13 12 16 16 11 21 20"/></svg>',
            '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M17 8h1a4 4 0 010 8h-1"/><path d="M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
            '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0114.08 0"/><path d="M1.42 9a16 16 0 0121.16 0"/><path d="M8.53 16.11a6 6 0 016.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
            '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>',
        ];
        $amenity_texts = alm_i18n_amenities_bar();
        $amenities = array_map(fn($idx, $t) => ['icon' => $icons[$idx], 'name' => $t[0], 'sub' => $t[1]], array_keys($amenity_texts), $amenity_texts);
        foreach ($amenities as $a) : ?>
        <div class="amenity-item">
            <div class="amenity-item__icon"><?php echo $a['icon']; ?></div>
            <div>
                <div class="amenity-item__name"><?php echo esc_html($a['name']); ?></div>
                <div class="amenity-item__sub"><?php echo esc_html($a['sub']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>

<?php /* ═══════════════════════════════════════════════════════
       EXPERIENCE — Sezione oscura atmosferica
       ════════════════════════════════════════════════════════ */ ?>

<section class="experience-section">
    <?php if ($hero_bg) : ?>
    <div class="experience-section__bg" style="background-image:url('<?php echo esc_url($hero_bg); ?>')"></div>
    <?php endif; ?>

    <div class="experience-section__inner">
        <p class="hero-premium__eyebrow" style="color:#C5B49C;justify-content:center;" data-reveal="fade">
            <?php echo esc_html(alm_t('exp.eyebrow')); ?>
        </p>
        <blockquote class="experience-section__quote" data-reveal="up" data-delay="100">
            <?php echo alm_t('exp.quote'); ?>
        </blockquote>
        <p class="experience-section__author" data-reveal="up" data-delay="200">
            <?php echo esc_html(alm_t('exp.author')); ?>
        </p>

        <div class="experience-section__pills" data-reveal="up" data-delay="300">
            <?php $pills = alm_i18n_pills();
            foreach ($pills as $p) : ?>
            <span class="experience-pill"><?php echo $p[0]; ?> <?php echo esc_html($p[1]); ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       CAMERE — Cards premium
       ════════════════════════════════════════════════════════ */ ?>

<?php if (!empty($rooms)) : ?>
<section class="rooms-section" id="section-camere">
    <div class="rooms-section-inner" style="max-width:1260px;margin:0 auto;padding-inline:2rem;">

        <?php /* Header sezione */ ?>
        <div class="rooms-section-header" style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:1rem;margin-bottom:2.5rem;">
            <div data-reveal="up">
                <span class="section-eyebrow"><?php echo esc_html(alm_t('rooms.eyebrow')); ?></span>
                <h2 class="section-headline"><?php echo alm_t('rooms.headline'); ?></h2>
            </div>
            <a href="<?php echo esc_url($camere_url); ?>" class="rooms-carousel__all" data-reveal="up" data-delay="100">
                <?php echo esc_html(alm_t('cta.all_rooms')); ?>
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <?php /* Carosello */ ?>
        <div class="rooms-carousel-outer" data-reveal="up" data-delay="150">
            <button class="rooms-carousel__btn rooms-carousel__btn--prev" id="roomsPrev" aria-label="Camera precedente">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            </button>

            <div class="rooms-carousel__viewport" id="roomsViewport">
                <div class="rooms-carousel__track" id="roomsTrack">
                    <?php foreach ($rooms as $room) :
                        $meta         = function_exists('alm_get_room_meta') ? alm_get_room_meta($room->ID) : [];
                        $thumb        = get_the_post_thumbnail_url($room->ID, 'large');
                        $price        = (float) ($meta['base_price'] ?? 0);
                        $adults       = (int) ($meta['capacity_adults'] ?? 2);
                        $size_m2      = (int) ($meta['size_m2'] ?? 0);
                        $room_url     = get_permalink($room->ID);
                        $room_prenota = add_query_arg(['room' => $room->ID], $prenota_url);
                    ?>
                    <article class="rooms-carousel__slide room-card-premium">

                        <div class="room-card-premium__image-wrap">
                            <?php if ($thumb) : ?>
                                <a href="<?php echo esc_url($room_url); ?>">
                                    <img class="room-card-premium__image"
                                         src="<?php echo esc_url($thumb); ?>"
                                         alt="<?php echo esc_attr(alm_get_room_translated($room->ID, 'title')); ?>"
                                         loading="lazy" />
                                </a>
                            <?php else : ?>
                                <div class="room-card-premium__image"
                                     style="background:linear-gradient(135deg,#C5B49C,#A6957E);display:flex;align-items:center;justify-content:center;">
                                    <svg width="48" height="48" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            <?php endif; ?>
                            <span class="room-card-premium__badge"><?php echo esc_html(alm_get_room_translated($room->ID, 'title')); ?></span>
                            <?php if ($price > 0) : ?>
                            <span class="room-card-premium__price-tag">
                                <?php echo esc_html(alm_t('rooms.from')); ?> €<?php echo esc_html(number_format($price, 0, ',', '.')); ?><?php echo esc_html(alm_t('rooms.night')); ?>
                            </span>
                            <?php endif; ?>
                        </div>

                        <div class="room-card-premium__body">
                            <div class="room-card-premium__meta">
                                <span>
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    <?php echo esc_html($adults . alm_t('rooms.guests_sfx')); ?>
                                </span>
                                <?php if ($size_m2 > 0) : ?>
                                <span><?php echo esc_html($size_m2 . ' m²'); ?></span>
                                <?php endif; ?>
                            </div>

                            <h3 class="room-card-premium__title">
                                <a href="<?php echo esc_url($room_url); ?>"><?php echo esc_html(alm_get_room_translated($room->ID, 'title')); ?></a>
                            </h3>

                            <p class="room-card-premium__excerpt">
                                <?php echo esc_html(wp_trim_words(alm_get_room_translated($room->ID, 'excerpt'), 16, '…')); ?>
                            </p>

                            <div class="room-card-premium__footer">
                                <?php if ($price > 0) : ?>
                                <div>
                                    <span class="room-card-premium__price-from"><?php echo esc_html(alm_t('rooms.from')); ?></span>
                                    <span class="room-card-premium__price-amount">€<?php echo esc_html(number_format($price, 0, ',', '.')); ?></span>
                                </div>
                                <?php else : ?><div></div><?php endif; ?>
                                <a href="<?php echo esc_url($room_prenota); ?>" class="room-card-premium__cta">
                                    <?php echo esc_html(alm_t('rooms.book')); ?>
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                        </div>

                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="rooms-carousel__btn rooms-carousel__btn--next" id="roomsNext" aria-label="Camera successiva">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

    </div><!-- /rooms-section-inner -->

    <?php /* Dots */ ?>
    <div class="rooms-carousel__dots" id="roomsDots" aria-label="Navigazione camere"></div>

</section>

<style>
/* ── Carosello camere ──────────────────────────────────────── */
.rooms-carousel__all {
    display: inline-flex; align-items: center; gap: .5rem;
    font-size: .875rem; font-weight: 600; color: #A6957E;
    text-decoration: none; transition: gap .2s;
}
.rooms-carousel__all:hover { gap: .875rem; color: #A6957E; }

.rooms-carousel-outer {
    position: relative;
    overflow: hidden; /* clip del track più largo del viewport a tutte le dimensioni */
}

.rooms-carousel__viewport {
    overflow: hidden;
    border-radius: 0;
}

.rooms-carousel__track {
    display: flex;
    gap: 1.75rem;
    transition: transform .55s cubic-bezier(.4,0,.2,1);
    will-change: transform;
}

/* Slide: larghezza calcolata per mostrare 3 card su desktop */
.rooms-carousel__slide {
    flex: 0 0 calc((100% - 3.5rem) / 3);
    min-width: 0;
}

/* Frecce */
.rooms-carousel__btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    width: 48px; height: 48px;
    padding: 0; min-width: 0;
    border-radius: 50%;
    border: 2px solid #EFEBE4;
    background: #fff;
    color: #5C5246;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    transition: background .2s, border-color .2s, color .2s, transform .2s;
}
.rooms-carousel__btn:hover {
    background: #C5B49C;
    border-color: #C5B49C;
    color: #fff;
    transform: translateY(-50%) scale(1.08);
}
.rooms-carousel__btn:disabled {
    opacity: .35;
    cursor: not-allowed;
    pointer-events: none;
}
.rooms-carousel__btn--prev { left: -28px; }
.rooms-carousel__btn--next { right: -28px; }

/* Dots */
.rooms-carousel__dots {
    display: flex; justify-content: center; gap: .5rem;
    margin-top: 2rem; padding-bottom: .5rem;
}
.rooms-carousel__dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    border: none; padding: 0; min-width: 0;
    background: #EFEBE4;
    cursor: pointer;
    transition: background .25s, transform .25s;
}
.rooms-carousel__dot.is-active {
    background: #C5B49C;
    transform: scale(1.35);
}

/* Responsive */
@media (max-width: 900px) {
    .rooms-carousel__slide { flex: 0 0 calc((100% - 1.75rem) / 2); }
    .rooms-carousel__btn--prev { left: -18px; }
    .rooms-carousel__btn--next { right: -18px; }
}
@media (max-width: 640px) {
    /* Carosello full-bleed su mobile */
    .rooms-section-inner    { padding-inline: 0 !important; }
    .rooms-section-header   { padding-inline: 1.25rem; }
    .rooms-carousel__dots   { padding-inline: 1.25rem; }
    .rooms-carousel__slide  { flex: 0 0 calc(100vw - 2.5rem); max-width: 420px; }
    .rooms-carousel__btn    { display: none; }
    .rooms-carousel__track  { gap: 1rem; padding-inline: 1.25rem; }
    .rooms-carousel__viewport { overflow-x: auto; scroll-snap-type: x mandatory;
                                -webkit-overflow-scrolling: touch; scrollbar-width: none;
                                scroll-padding-inline-start: 1.25rem; }
    .rooms-carousel__viewport::-webkit-scrollbar { display: none; }
    .rooms-carousel__slide  { scroll-snap-align: start; }
    /* Card più alta su mobile */
    .rooms-carousel__slide .room-card-premium__image-wrap { height: 220px; }
}
</style>

<script>
(function(){
    var track    = document.getElementById('roomsTrack');
    var viewport = document.getElementById('roomsViewport');
    var dotsEl   = document.getElementById('roomsDots');
    var btnPrev  = document.getElementById('roomsPrev');
    var btnNext  = document.getElementById('roomsNext');
    if(!track || !viewport) return;

    var slides      = track.querySelectorAll('.rooms-carousel__slide');
    var total       = slides.length;
    var current     = 0;
    var autoTimer   = null;
    var AUTO_MS     = 4500;

    /* Mobile: usa scroll nativo CSS (≤640px corrisponde al breakpoint CSS) */
    function isMobileScroll(){
        return viewport.offsetWidth <= 640;
    }

    /* Calcola quante slide sono visibili in base alla larghezza */
    function visibleCount(){
        var vw = viewport.offsetWidth;
        if(vw <= 640) return 1;
        if(vw <= 900) return 2;
        return 3;
    }

    function maxIndex(){
        return Math.max(0, total - visibleCount());
    }

    /* Sposta il track */
    function goTo(idx){
        current = Math.max(0, Math.min(idx, maxIndex()));
        var slideW = slides[0].offsetWidth;
        var gap    = parseFloat(getComputedStyle(track).gap) || 28;
        if(isMobileScroll()){
            /* Mobile: usa scrollLeft — il CSS gestisce il layout con overflow-x:auto */
            viewport.scrollLeft = current * (slideW + gap);
        } else {
            track.style.transform = 'translateX(-' + (current * (slideW + gap)) + 'px)';
        }
        updateDots();
        updateArrows();
    }

    /* Crea i dots */
    function buildDots(){
        dotsEl.innerHTML = '';
        var pages = maxIndex() + 1;
        for(var i = 0; i < pages; i++){
            var d = document.createElement('button');
            d.className = 'rooms-carousel__dot' + (i === 0 ? ' is-active' : '');
            d.setAttribute('aria-label', 'Vai alla camera ' + (i+1));
            d.dataset.idx = i;
            d.addEventListener('click', function(){ goTo(+this.dataset.idx); resetAuto(); });
            dotsEl.appendChild(d);
        }
    }

    function updateDots(){
        dotsEl.querySelectorAll('.rooms-carousel__dot').forEach(function(d,i){
            d.classList.toggle('is-active', i === current);
        });
    }

    function updateArrows(){
        if(btnPrev) btnPrev.disabled = current === 0;
        if(btnNext) btnNext.disabled = current >= maxIndex();
    }

    /* Auto-scroll */
    function startAuto(){
        clearInterval(autoTimer);
        autoTimer = setInterval(function(){
            goTo(current >= maxIndex() ? 0 : current + 1);
        }, AUTO_MS);
    }
    function resetAuto(){ startAuto(); }

    /* Swipe touch — solo su desktop (mobile usa scroll nativo) */
    var touchStartX = 0;
    viewport.addEventListener('touchstart', function(e){ touchStartX = e.touches[0].clientX; }, {passive:true});
    viewport.addEventListener('touchend', function(e){
        if(isMobileScroll()) return; /* scroll CSS nativo gestisce lo swipe */
        var dx = e.changedTouches[0].clientX - touchStartX;
        if(Math.abs(dx) > 40){
            goTo(dx < 0 ? current + 1 : current - 1);
            resetAuto();
        }
    }, {passive:true});

    /* Aggiorna dots/frecce durante lo scroll nativo mobile */
    viewport.addEventListener('scroll', function(){
        if(!isMobileScroll()) return;
        var slideW = slides[0].offsetWidth;
        var gap    = parseFloat(getComputedStyle(track).gap) || 16;
        var newIdx = Math.round(viewport.scrollLeft / (slideW + gap));
        newIdx = Math.max(0, Math.min(newIdx, maxIndex()));
        if(newIdx !== current){ current = newIdx; updateDots(); updateArrows(); }
    }, {passive:true});

    /* Bottoni freccia */
    if(btnPrev) btnPrev.addEventListener('click', function(){ goTo(current - 1); resetAuto(); });
    if(btnNext) btnNext.addEventListener('click', function(){ goTo(current + 1); resetAuto(); });

    /* Pausa al hover */
    viewport.addEventListener('mouseenter', function(){ clearInterval(autoTimer); });
    viewport.addEventListener('mouseleave', startAuto);

    /* Init */
    buildDots();
    goTo(0);
    startAuto();

    /* Ricalcola al resize */
    window.addEventListener('resize', function(){
        /* Se si passa a mobile, azzera il transform desktop */
        if(isMobileScroll()) track.style.transform = '';
        buildDots();
        goTo(Math.min(current, maxIndex()));
    });
})();
</script>
<?php endif; ?>

<?php /* ═══════════════════════════════════════════════════════
       THE STORY — Split layout con immagine
       ════════════════════════════════════════════════════════ */ ?>

<section class="story-section">
    <div class="story-section__grid">

        <div class="story-section__images" data-reveal="left">
            <?php if ($villa_img) : ?>
                <img class="story-section__img-main"
                     src="<?php echo esc_url($villa_img); ?>"
                     alt="Almaretna Villa" loading="lazy" />
            <?php else : ?>
                <div class="story-section__img-main"
                     style="background:linear-gradient(135deg,#C5B49C 0%,#8a7560 100%);display:flex;align-items:center;justify-content:center;">
                    <svg width="64" height="64" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                </div>
            <?php endif; ?>

            <div class="story-section__badge">
                <span class="story-section__badge-num">★ 5</span>
                <span class="story-section__badge-label"><?php echo esc_html(alm_t('story.badge')); ?></span>
            </div>

            <?php
            // Immagine accent (foto Facebook camera matrimoniale)
            $img2 = $story_accent_img;
            if (!$img2 && count($rooms) > 1 && has_post_thumbnail($rooms[1]->ID)) {
                $img2 = get_the_post_thumbnail_url($rooms[1]->ID, 'medium_large');
            }
            if ($img2) :
            ?>
            <img class="story-section__img-accent"
                 src="<?php echo esc_url($img2); ?>"
                 alt="Dettaglio Almaretna" loading="lazy" />
            <?php endif; ?>
        </div>

        <div data-reveal="right">
            <span class="section-eyebrow"><?php echo esc_html(alm_t('story.eyebrow')); ?></span>
            <h2 class="section-headline"><?php echo alm_t('story.headline'); ?></h2>
            <span class="divider-line"></span>

            <div class="story-section__text">
                <p><?php echo esc_html($lang === 'it' ? get_theme_mod('almaretna_villa_text_1', alm_t('story.text1')) : alm_t('story.text1')); ?></p>
                <p><?php echo esc_html($lang === 'it' ? get_theme_mod('almaretna_villa_text_2', alm_t('story.text2')) : alm_t('story.text2')); ?></p>
            </div>

            <ul class="story-section__checklist">
                <?php
                $checks = alm_i18n_story_checks();
                foreach ($checks as $c) : ?>
                <li>
                    <span class="story-check">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    <?php echo esc_html($c); ?>
                </li>
                <?php endforeach; ?>
            </ul>

            <a href="<?php echo esc_url($camere_url); ?>" class="hero-premium__cta-primary alm-section-cta"
               style="margin-top:.5rem;">
                <?php echo esc_html(alm_t('story.cta')); ?>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       PISCINA
       ════════════════════════════════════════════════════════ */ ?>

<?php
$pool_img  = get_theme_mod('almaretna_pool_img',  '') ?: $_fb . 'piscina-cover-principale.webp';
$pool_img2 = get_theme_mod('almaretna_pool_img2', '') ?: $_fb . 'piscina-panoramica.webp';
?>

<section class="pool-section" id="la-piscina">
    <div class="pool-section__grid">
<div class="pool-section__images" data-reveal="left">
<div class="pool-section__img-main">
                <?php if ($pool_img) : ?>
                    <img src="<?php echo esc_url($pool_img); ?>"
                         alt="Piscina Almaretna"
                         loading="lazy"
                         style="width:100%;height:100%;object-fit:cover;display:block;" />
                <?php else : ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#a8c5d8,#7da8c0);display:flex;align-items:center;justify-content:center;">
                        <svg width="64" height="64" fill="none" stroke="rgba(255,255,255,.6)" stroke-width="1.2" viewBox="0 0 24 24">
                            <path d="M2 12h20M2 17c1.5 0 3-1 3-2s1.5-2 3-2 3 1 3 2 1.5 2 3 2 3-1 3-2"/>
                            <circle cx="7" cy="7" r="2"/><path d="M7 9v3"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
<div class="pool-section__badge">
                <span class="pool-section__badge-num">12<small>m</small></span>
                <span class="pool-section__badge-label"><?php echo esc_html(alm_t('pool.badge')); ?></span>
            </div>
<?php if ($pool_img2) : ?>
            <div class="pool-section__img-accent">
                <img src="<?php echo esc_url($pool_img2); ?>"
                     alt="Bar piscina Almaretna"
                     loading="lazy"
                     style="width:100%;height:100%;object-fit:cover;display:block;" />
            </div>
            <?php endif; ?>
        </div>
<div data-reveal="right">
            <span class="section-eyebrow"><?php echo esc_html(alm_t('pool.eyebrow')); ?></span>
            <h2 class="section-headline"><?php echo alm_t('pool.headline'); ?></h2>
            <span class="divider-line"></span>

            <p style="font-size:1.05rem;color:#6B6058;line-height:1.9;margin-bottom:1.25rem;">
                <?php echo alm_t('pool.desc'); ?>
            </p>
<div class="pool-specs">
                <div class="pool-spec">
                    <svg width="20" height="20" fill="none" stroke="#C5B49C" stroke-width="1.8" viewBox="0 0 24 24"><path d="M2 12h20M2 17c1.5 0 3-1 3-2s1.5-2 3-2 3 1 3 2 1.5 2 3 2 3-1 3-2"/><circle cx="7" cy="7" r="2"/><path d="M7 9v3"/></svg>
                    <div>
                        <span class="pool-spec__val">12 × 5 m</span>
                        <span class="pool-spec__label"><?php echo esc_html(alm_t('pool.spec1')); ?></span>
                    </div>
                </div>
                <div class="pool-spec">
                    <svg width="20" height="20" fill="none" stroke="#C5B49C" stroke-width="1.8" viewBox="0 0 24 24"><path d="M12 2v20M2 7h20M2 17h20"/></svg>
                    <div>
                        <span class="pool-spec__val"><?php echo esc_html(alm_t('pool.spec2_val')); ?></span>
                        <span class="pool-spec__label"><?php echo esc_html(alm_t('pool.spec2')); ?></span>
                    </div>
                </div>
            </div>
<div class="pool-bar">
                <div class="pool-bar__header">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 8h1a4 4 0 010 8h-1"/><path d="M3 8h14v9a4 4 0 01-4 4H7a4 4 0 01-4-4z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>
                    <h3 class="pool-bar__title"><?php echo esc_html(alm_t('pool.bar_title')); ?></h3>
                </div>
                <p class="pool-bar__desc"><?php echo esc_html(alm_t('pool.bar_desc')); ?></p>
                <ul class="pool-bar__list">
                    <?php $dotazioni = alm_i18n_pool_dotazioni();
                    foreach ($dotazioni as $d) :
                    ?>
                    <li>
                        <span class="pool-bar__check">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        </span>
                        <?php echo esc_html($d); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <a href="<?php echo esc_url($prenota_url); ?>" class="hero-premium__cta-primary alm-section-cta" style="margin-top:2rem;">
                <?php echo esc_html(alm_t('cta.book')); ?>
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       NUMERI — Stats in bella tipografia
       ════════════════════════════════════════════════════════ */ ?>

<section class="numbers-section">
    <div class="numbers-grid">
        <?php
        $numbers = alm_i18n_numbers();
        foreach ($numbers as $i => $n) : ?>
        <div class="number-item" data-reveal="up" data-delay="<?php echo $i * 100; ?>">
            <span class="number-item__num"><?php echo esc_html($n[0]); ?></span>
            <span class="number-item__label"><?php echo esc_html($n[1]); ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       GALLERY STRIP — Griglia fotografica full-width
       ════════════════════════════════════════════════════════ */ ?>

<?php if (count($rooms) >= 1) : ?>
<div class="gallery-strip">
    <?php
    // 1. Prima prova le foto caricate dal Customizer (Aspetto → Personalizza → Galleria Homepage)
    $gallery_imgs = [];
    for ($slot = 1; $slot <= 6; $slot++) {
        $url = get_theme_mod('almaretna_gallery_img_' . $slot, '');
        if ($url) $gallery_imgs[] = esc_url($url);
    }

    // 2. Fallback: foto Facebook (in ordine curato per la strip)
    if (empty($gallery_imgs)) {
        foreach ([
            'piscina-cover-principale.webp',
            'villa-esterna.webp',
            'camera-matrimoniale.webp',
            'camera-singola.webp',
            'salottino-comune.webp',
        ] as $fn) {
            $gallery_imgs[] = $_fb . $fn;
        }
    }

    // Mostra massimo 5 immagini, completa con placeholder se non bastano
    $gallery_imgs = array_slice($gallery_imgs, 0, 5);
    while (count($gallery_imgs) < 5) {
        $gallery_imgs[] = '';
    }

    $colors = ['#C5B49C','#A6957E','#8a7560','#b5a489','#EFEBE4'];
    for ($g = 0; $g < 5; $g++) :
        $img = $gallery_imgs[$g] ?? '';
    ?>
    <div class="gallery-strip__item">
        <?php if ($img) : ?>
            <img class="gallery-strip__img" src="<?php echo esc_url($img); ?>"
                 alt="Almaretna gallery" loading="lazy" />
        <?php else : ?>
            <div class="gallery-strip__img"
                 style="background:<?php echo esc_attr($colors[$g]); ?>;min-height:200px;"></div>
        <?php endif; ?>
        <div class="gallery-strip__overlay"></div>
        <?php if ($g === 4) : ?>
        <a href="<?php echo esc_url($camere_url); ?>" class="gallery-strip__view-all">
            <?php echo esc_html(alm_t('gallery.all')); ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php /* ═══════════════════════════════════════════════════════
       TESTIMONIALS
       ════════════════════════════════════════════════════════ */ ?>

<section class="testimonials-section">
    <div style="max-width:1200px;margin:0 auto;">
        <div style="text-align:center;" data-reveal="up">
            <span class="section-eyebrow" style="justify-content:center;"><?php echo esc_html(alm_t('test.eyebrow')); ?></span>
            <h2 class="section-headline"><?php echo alm_t('test.headline'); ?></h2>
        </div>

        <div class="testimonials-grid">
            <?php
            $reviews = alm_i18n_reviews();
            foreach ($reviews as $i => $rev) : ?>
            <div class="testimonial-card" data-reveal="up" data-delay="<?php echo $i * 100; ?>">
                <span class="testimonial-card__quote">"</span>
                <div class="testimonial-card__stars">
                    <?php for ($s = 0; $s < $rev['stars']; $s++) echo '★'; ?>
                </div>
                <p class="testimonial-card__text">"<?php echo esc_html($rev['text']); ?>"</p>
                <div class="testimonial-card__author">
                    <div class="testimonial-card__avatar"><?php echo esc_html($rev['init']); ?></div>
                    <div>
                        <div class="testimonial-card__name"><?php echo esc_html($rev['name']); ?></div>
                        <div class="testimonial-card__origin"><?php echo esc_html($rev['origin']); ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonials-cta" data-reveal="up">
            <a href="<?php echo esc_url($prenota_url); ?>" class="hero-premium__cta-primary alm-section-cta">
                <?php echo esc_html(alm_t('cta.book_exp')); ?>
            </a>
            <div class="testimonials-rating">
                <span style="color:#D4A853;font-size:1rem;">★★★★★</span>
                <strong>4.9 / 5</strong>
                <span style="color:#8C8070;"><?php echo alm_t('test.rating'); ?></span>
            </div>
        </div>
    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       LOCATION
       ════════════════════════════════════════════════════════ */ ?>

<section class="location-premium">
    <div class="location-premium__grid">

        <div data-reveal="left">
            <span class="section-eyebrow"><?php echo esc_html(alm_t('loc.eyebrow')); ?></span>
            <h2 class="section-headline"><?php echo alm_t('loc.headline'); ?></h2>
            <span class="divider-line"></span>

            <address style="font-style:normal;font-size:.95rem;color:#5C5246;line-height:1.7;margin-bottom:2rem;">
                <strong>Almaretna Villa Vacanze</strong><br>
                Via Scorciavacca Montarsi, 48<br>
                Nunziata di Mascali (CT) · 95016
            </address>

            <ul class="distance-list">
                <?php
                $distances = alm_i18n_distances();
                foreach ($distances as $d) : ?>
                <li class="distance-item">
                    <span class="distance-item__place">
                        <span style="font-size:1rem;"><?php echo $d[0]; ?></span>
                        <?php echo esc_html($d[1]); ?>
                    </span>
                    <span class="distance-item__time"><?php echo esc_html($d[2]); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <a href="https://maps.google.com/?q=Via+Scorciavacca+Montarsi+48+Nunziata+di+Mascali"
               target="_blank" rel="noopener noreferrer"
               class="hero-premium__cta-primary alm-section-cta"
               style="margin-top:2rem;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?php echo esc_html(alm_t('cta.maps')); ?>
            </a>
        </div>

        <div data-reveal="right">
            <div class="location-premium__map">
                <iframe
                    title="<?php echo esc_attr(alm_t('loc.map_title')); ?>"
                    src="https://maps.google.com/maps?q=Via+Scorciavacca+Montarsi+48+Nunziata+di+Mascali&output=embed&z=14"
                    allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>

    </div>
</section>

<?php /* ═══════════════════════════════════════════════════════
       FINAL CTA — L'invito definitivo
       ════════════════════════════════════════════════════════ */ ?>

<section class="final-cta">
    <?php if ($hero_bg) : ?>
    <div class="final-cta__bg" style="background-image:url('<?php echo esc_url($hero_bg); ?>')"></div>
    <?php endif; ?>

    <div class="final-cta__inner">
        <span class="final-cta__eyebrow" data-reveal="fade"><?php echo esc_html(alm_t('fcta.eyebrow')); ?></span>
        <h2 class="final-cta__headline" data-reveal="up" data-delay="100">
            <?php echo alm_t('fcta.headline'); ?>
        </h2>
        <p class="final-cta__text" data-reveal="up" data-delay="200">
            <?php echo esc_html(alm_t('fcta.text')); ?>
        </p>

        <div class="final-cta__actions" data-reveal="up" data-delay="300">
            <a href="<?php echo esc_url($prenota_url); ?>" class="final-cta__btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?php echo esc_html(alm_t('fcta.btn')); ?>
            </a>

            <?php if ($host_phone) : ?>
            <a href="tel:<?php echo esc_attr($host_phone); ?>" class="final-cta__tel">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg>
                <?php echo esc_html(alm_t('fcta.tel')); ?> <?php echo esc_html($host_phone); ?>
            </a>
            <?php endif; ?>
        </div>

        <div class="final-cta__guarantee" data-reveal="up" data-delay="400">
            <span class="guarantee-item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <?php echo esc_html(alm_t('fcta.g1')); ?>
            </span>
            <span class="guarantee-item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                <?php echo esc_html(alm_t('fcta.g2')); ?>
            </span>
            <span class="guarantee-item">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <?php echo esc_html(alm_t('fcta.g3')); ?>
            </span>
        </div>
    </div>
</section>

</main>

<?php get_footer(); ?>
