<?php
declare(strict_types=1);

/**
 * Almaretna Child — Shortcodes
 *
 * [alm_booking_form]
 * [alm_rooms_grid columns="3" floor="" type=""]
 * [alm_availability_calendar room_id=""]
 * [alm_room_price room_id=""]
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ─── [alm_booking_form] ───────────────────────────────────────────────────────

add_shortcode('alm_booking_form', function (array $atts = []): string {
    // Delegato al plugin almaretna-booking se attivo
    $plugin_view = WP_PLUGIN_DIR . '/almaretna-booking/public/views/booking-form.php';

    if (file_exists($plugin_view)) {
        ob_start();
        include $plugin_view;
        return (string) ob_get_clean();
    }

    // Fallback: il plugin non è ancora attivo
    return '<div class="alert alert-info">' .
        esc_html(alm_t('wiz.form_unavailable')) .
        '</div>';
});

// ─── [alm_rooms_grid] ────────────────────────────────────────────────────────

add_shortcode('alm_rooms_grid', function (array|string $atts = []): string {
    $atts = shortcode_atts([
        'columns' => '3',
        'floor'   => '',
        'type'    => '',
        'adults'  => '',
    ], is_array($atts) ? $atts : [], 'alm_rooms_grid');

    $columns = max(1, min(3, (int) $atts['columns']));

    $query_args = [];
    if (!empty($atts['floor'])) {
        $query_args['room_floor'] = sanitize_text_field($atts['floor']);
    }
    if (!empty($atts['type'])) {
        $query_args['room_type'] = sanitize_text_field($atts['type']);
    }
    if (!empty($atts['adults'])) {
        $query_args['adults'] = absint($atts['adults']);
    }

    $rooms_query = alm_rooms_query($query_args);

    if (!$rooms_query->have_posts()) {
        return '<p class="text-center text-light">' .
            esc_html(alm_t('sc.no_rooms')) .
            '</p>';
    }

    $grid_class = 'rooms-grid';
    if ($columns === 2) {
        $grid_class .= ' rooms-grid--2col';
    } elseif ($columns === 1) {
        $grid_class .= ' rooms-grid--1col';
    }

    ob_start();
    // URL prenotazione
    $prenota_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
    $prenota_base  = !empty($prenota_pages) ? get_permalink($prenota_pages[0]->ID) : home_url('/prenota/');
    ?>
    <div class="<?php echo esc_attr($grid_class); ?>">
        <?php while ($rooms_query->have_posts()) : $rooms_query->the_post(); ?>
            <?php
            $post_id      = get_the_ID();
            $meta         = alm_get_room_meta($post_id);
            $thumb_url    = alm_get_room_thumbnail_url($post_id, 'large');
            $amenities    = alm_get_room_amenities($post_id);
            $room_type    = alm_get_room_type($post_id);
            $room_floor   = alm_get_room_floor($post_id);
            $sharing_note = alm_get_bathroom_sharing_note($post_id);
            $price        = (float) ($meta['base_price'] ?? 0);
            $adults       = (int) ($meta['capacity_adults'] ?? 2);
            $room_prenota = add_query_arg(['room' => $post_id], $prenota_base);
            ?>
            <article class="room-card-premium">

                <div class="room-card-premium__image-wrap">
                    <?php if ($thumb_url) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <img class="room-card-premium__image"
                                 src="<?php echo esc_url($thumb_url); ?>"
                                 alt="<?php echo esc_attr(alm_get_room_translated($post_id, 'title')); ?>"
                                 loading="lazy" width="600" height="450" />
                        </a>
                    <?php else : ?>
                        <div class="room-card-premium__image"
                             style="background:linear-gradient(135deg,#C5B49C,#A6957E);display:flex;align-items:center;justify-content:center;">
                            <svg width="48" height="48" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    <?php endif; ?>

                    <span class="room-card-premium__badge"><?php echo esc_html(alm_get_room_translated($post_id, 'title')); ?></span>

                    <?php if ($price > 0) : ?>
                    <span class="room-card-premium__price-tag">
                        <?php echo esc_html(sprintf(alm_t('sc.price_tag'), number_format($price, 0, ',', '.'))); ?>
                    </span>
                    <?php endif; ?>
                </div>

                <div class="room-card-premium__body">
                    <div class="room-card-premium__meta">
                        <span style="display:flex;align-items:center;gap:.3rem;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <?php echo esc_html($adults . alm_t('rooms.guests_sfx')); ?>
                        </span>
                        <?php if ($room_floor) : ?>
                        <span style="display:flex;align-items:center;gap:.3rem;">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21V3h18v18H3z"/></svg>
                            <?php echo esc_html(alm_get_floor_label($room_floor->slug)); ?>
                        </span>
                        <?php endif; ?>
                        <?php if ($room_type) : ?>
                        <span><?php echo esc_html($room_type->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <h3 class="room-card-premium__title">
                        <a href="<?php the_permalink(); ?>"><?php echo esc_html(alm_get_room_translated($post_id, 'title')); ?></a>
                    </h3>

                    <p class="room-card-premium__excerpt">
                        <?php echo esc_html(wp_trim_words(alm_get_room_translated($post_id, 'excerpt'), 18, '…')); ?>
                    </p>

                    <?php if (!empty($amenities)) : ?>
                    <div style="display:flex;flex-wrap:wrap;gap:.375rem;margin-bottom:1rem;">
                        <?php foreach (array_slice($amenities, 0, 4) as $amenity) : ?>
                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .625rem;background:#F7F4ED;border:1px solid #EFEBE4;border-radius:4px;font-size:.72rem;font-weight:500;color:#5C5246;">
                            <span class="dashicons <?php echo esc_attr(alm_get_amenity_icon($amenity->slug)); ?>" style="font-size:12px;width:12px;height:12px;color:#C5B49C;"></span>
                            <?php echo esc_html($amenity->name); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="room-card-premium__footer">
                        <?php if ($price > 0) : ?>
                        <div>
                            <span class="room-card-premium__price-from"><?php echo esc_html(alm_t('rooms.from')); ?></span>
                            <span class="room-card-premium__price-amount">€<?php echo esc_html(number_format($price, 0, ',', '.')); ?></span>
                        </div>
                        <?php else : ?>
                        <div></div>
                        <?php endif; ?>
                        <a href="<?php echo esc_url($room_prenota); ?>" class="room-card-premium__cta">
                            <?php echo esc_html(alm_t('rooms.book')); ?>
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                </div>

            </article>
        <?php endwhile; ?>
    </div>
    <?php
    wp_reset_postdata();
    return (string) ob_get_clean();
});

// ─── [alm_availability_calendar] ─────────────────────────────────────────────

add_shortcode('alm_availability_calendar', function (array|string $atts = []): string {
    $atts = shortcode_atts([
        'room_id' => '',
    ], is_array($atts) ? $atts : [], 'alm_availability_calendar');

    $room_id = absint($atts['room_id']);
    if (!$room_id) {
        return '';
    }

    // Se il plugin è attivo, delega
    $plugin_view = WP_PLUGIN_DIR . '/almaretna-booking/public/views/calendar.php';
    if (file_exists($plugin_view)) {
        ob_start();
        include $plugin_view;
        return (string) ob_get_clean();
    }

    // Fallback: calendario statico placeholder
    $meta = alm_get_room_meta($room_id);
    ob_start();
    ?>
    <div
        class="availability-calendar"
        data-room-id="<?php echo esc_attr((string) $room_id); ?>"
        data-room-slug="<?php echo esc_attr($meta['room_id']); ?>"
    >
        <div class="availability-calendar__header">
            <button class="availability-calendar__nav" data-dir="prev" aria-label="<?php echo esc_attr(alm_t('sc.cal_prev')); ?>">&lsaquo;</button>
            <span class="availability-calendar__month-title"><?php echo esc_html(alm_t('sc.cal_title')); ?></span>
            <button class="availability-calendar__nav" data-dir="next" aria-label="<?php echo esc_attr(alm_t('sc.cal_next')); ?>">&rsaquo;</button>
        </div>
        <p style="padding:var(--space-lg);text-align:center;color:var(--color-text-light);">
            <?php echo esc_html(alm_t('sc.cal_loading')); ?>
        </p>
    </div>
    <?php
    return (string) ob_get_clean();
});

// ─── [alm_room_price] ────────────────────────────────────────────────────────

add_shortcode('alm_room_price', function (array|string $atts = []): string {
    $atts = shortcode_atts([
        'room_id' => '',
    ], is_array($atts) ? $atts : [], 'alm_room_price');

    $room_id = absint($atts['room_id']);
    if (!$room_id) {
        return '';
    }

    $meta     = alm_get_room_meta($room_id);
    $prenota_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
    $book_url  = !empty($prenota_page) ? get_permalink($prenota_page[0]->ID) : home_url('/prenota/');

    ob_start();
    ?>
    <div
        class="room-price-box"
        data-room-id="<?php echo esc_attr((string) $room_id); ?>"
    >
        <div class="room-price-box__capacita">
            <span class="dashicons dashicons-admin-users"></span>
            <?php
            echo esc_html(sprintf(alm_t('sc.up_to_adults'), $meta['capacity_adults']));
            if ($meta['capacity_children'] > 0) {
                echo ' + ' . esc_html((string) $meta['capacity_children']) . ' ' .
                    esc_html(alm_t('sc.children'));
            }
            ?>
        </div>

        <div class="room-price-box__price">
            <span class="room-price-box__amount" data-js="price-amount">
                <?php echo wp_kses_post(alm_format_price($meta['base_price'])); ?>
            </span>
            <span class="room-price-box__per-night">
                <?php echo esc_html(alm_t('rooms.night')); ?>
            </span>
        </div>

        <?php if ($meta['min_stay'] > 1) : ?>
            <p style="font-size:var(--fs-xs);color:var(--color-text-light);margin-bottom:var(--space-md);">
                <?php echo esc_html(sprintf(alm_t('sc.min_stay'), $meta['min_stay'])); ?>
            </p>
        <?php endif; ?>

        <div class="room-price-box__form">
            <div class="form-group">
                <label for="rpb-checkin-<?php echo esc_attr((string) $room_id); ?>">
                    <?php echo esc_html(alm_t('strip.checkin')); ?>
                </label>
                <input
                    type="text"
                    id="rpb-checkin-<?php echo esc_attr((string) $room_id); ?>"
                    class="form-control alm-datepicker-checkin"
                    data-room-id="<?php echo esc_attr((string) $room_id); ?>"
                    placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
                    readonly
                />
            </div>
            <div class="form-group">
                <label for="rpb-checkout-<?php echo esc_attr((string) $room_id); ?>">
                    <?php echo esc_html(alm_t('strip.checkout')); ?>
                </label>
                <input
                    type="text"
                    id="rpb-checkout-<?php echo esc_attr((string) $room_id); ?>"
                    class="form-control alm-datepicker-checkout"
                    data-room-id="<?php echo esc_attr((string) $room_id); ?>"
                    placeholder="<?php echo esc_attr(alm_t('sc.date_ph')); ?>"
                    readonly
                />
            </div>
            <a
                href="<?php echo esc_url($book_url ?: home_url('/prenota/')); ?>"
                class="btn btn-primary btn-lg"
            >
                <?php echo esc_html(alm_t('nav.book_now')); ?>
            </a>
        </div>

        <p class="room-price-box__info">
            <span class="dashicons dashicons-lock" style="font-size:14px;vertical-align:middle;"></span>
            <?php echo esc_html(alm_t('sc.secure_cancel')); ?>
        </p>
    </div>
    <?php
    return (string) ob_get_clean();
});


// ─── [alm_image] ─────────────────────────────────────────────────────────────
//
// Permette di inserire immagini nelle pagine con posizionamento preciso.
//
// Parametri:
//   src        URL immagine (obbligatorio) oppure id=<media_library_id>
//   id         ID media library WordPress (alternativo a src)
//   align      left | right | center | full (default: center)
//   width      es. "50%" oppure "400px" (default: auto)
//   caption    Testo didascalia (opzionale)
//   alt        Testo alternativo (default: caption o nome file)
//   link       URL a cui linkare l'immagine (opzionale)
//   radius     border-radius override es. "0" per disabilitare (default: tema)
//   shadow     yes | no (default: yes)
//   class      Classi CSS aggiuntive
//
// Esempi:
//   [alm_image id="42" align="right" width="40%" caption="Vista Etna"]
//   [alm_image src="https://..." align="left" width="300px"]
//   [alm_image id="55" align="full"]

add_shortcode('alm_image', function (array $atts = [], ?string $content = null): string {
    $atts = shortcode_atts([
        'src'     => '',
        'id'      => '',
        'align'   => 'center',
        'width'   => '',
        'caption' => '',
        'alt'     => '',
        'link'    => '',
        'radius'  => '',
        'shadow'  => 'yes',
        'class'   => '',
    ], $atts, 'alm_image');

    // Risolvi URL immagine
    $src = '';
    $alt = esc_attr($atts['alt']);

    if (!empty($atts['id'])) {
        $img_id = absint($atts['id']);
        $src    = wp_get_attachment_url($img_id);
        if (empty($alt)) {
            $alt = esc_attr(get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: get_the_title($img_id));
        }
        // Se caption non specificata, usa quella della media library
        if (empty($atts['caption'])) {
            $attachment = get_post($img_id);
            $atts['caption'] = $attachment ? wp_strip_all_tags($attachment->post_excerpt) : '';
        }
    } elseif (!empty($atts['src'])) {
        $src = esc_url($atts['src']);
        if (empty($alt)) {
            $alt = esc_attr(basename((string) $atts['src']));
        }
    }

    if (!$src) {
        return '<!-- [alm_image] errore: specificare src="" oppure id="" -->';
    }

    // Classi wrapper
    $align_class = 'alm-image-wrap--' . sanitize_html_class($atts['align']);
    $extra_class = sanitize_html_class((string) $atts['class']);
    $wrap_class  = implode(' ', array_filter(['alm-image-wrap', $align_class, $extra_class]));

    // Stili inline wrapper
    $wrap_style = '';
    if (!empty($atts['width'])) {
        $wrap_style .= 'max-width:' . esc_attr($atts['width']) . ';';
    }

    // Stili inline immagine
    $img_style = '';
    if ($atts['radius'] !== '') {
        $img_style .= 'border-radius:' . esc_attr($atts['radius']) . ';';
    }
    if ($atts['shadow'] === 'no') {
        $img_style .= 'box-shadow:none;';
    }

    // Immagine
    $img_tag = sprintf(
        '<img src="%s" alt="%s" loading="lazy"%s />',
        esc_url($src),
        $alt,
        $img_style ? ' style="' . $img_style . '"' : ''
    );

    // Eventuale link
    if (!empty($atts['link'])) {
        $img_tag = sprintf('<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url($atts['link']), $img_tag);
    }

    // Caption
    $caption_html = '';
    if (!empty($atts['caption'])) {
        $caption_html = '<figcaption class="alm-image-caption">' . esc_html($atts['caption']) . '</figcaption>';
    }

    return sprintf(
        '<figure class="%s"%s>%s%s</figure>',
        esc_attr($wrap_class),
        $wrap_style ? ' style="' . $wrap_style . '"' : '',
        $img_tag,
        $caption_html
    );
});

// ─── [alm_gallery] ───────────────────────────────────────────────────────────
//
// Galleria immagini grid dal media library.
//
// Parametri:
//   ids     ID immagini separati da virgola (obbligatorio)
//   cols    2 | 3 | 4 (default: 3)
//   link    yes | no — clicca apre immagine originale (default: no)
//
// Esempio:
//   [alm_gallery ids="10,11,12,13" cols="3"]

add_shortcode('alm_gallery', function (array $atts = []): string {
    $atts = shortcode_atts([
        'ids'  => '',
        'cols' => '3',
        'link' => 'no',
    ], $atts, 'alm_gallery');

    if (empty($atts['ids'])) {
        return '<!-- [alm_gallery] errore: specificare ids="" -->';
    }

    $ids  = array_filter(array_map('absint', explode(',', $atts['ids'])));
    $cols = in_array($atts['cols'], ['2','3','4'], true) ? $atts['cols'] : '3';

    if (empty($ids)) {
        return '';
    }

    $items = '';
    foreach ($ids as $img_id) {
        $src = wp_get_attachment_url($img_id);
        if (!$src) continue;
        $alt = esc_attr(get_post_meta($img_id, '_wp_attachment_image_alt', true) ?: '');
        $img = sprintf('<img src="%s" alt="%s" loading="lazy" />', esc_url($src), $alt);
        if ($atts['link'] === 'yes') {
            $img = sprintf('<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url($src), $img);
        }
        $items .= '<div class="alm-gallery__item">' . $img . '</div>';
    }

    return sprintf(
        '<div class="alm-gallery alm-gallery--cols-%s">%s</div>',
        esc_attr($cols),
        $items
    );
});
