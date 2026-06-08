<?php
/**
 * Template singola camera (CPT: almaretna_room)
 * Restructured with Bootstrap 5.3 for a professional, lightweight layout.
 *
 * @package AlmaretnaChild
 */

declare(strict_types=1);

get_header();

while (have_posts()) :
    the_post();

    $post_id      = get_the_ID();
    $meta         = alm_get_room_meta($post_id);
    $amenities    = alm_get_room_amenities($post_id);
    $room_type    = alm_get_room_type($post_id);
    $room_floor   = alm_get_room_floor($post_id);
    $sharing_note = alm_get_bathroom_sharing_note($post_id);
    $thumb_url    = alm_get_room_thumbnail_url($post_id, 'full');

    // Gallery: immagine in evidenza + allegati
    $gallery_ids  = get_post_meta($post_id, '_room_gallery', true);
    $gallery_ids  = !empty($gallery_ids) ? array_filter(explode(',', $gallery_ids)) : [];
    $thumb_id     = get_post_thumbnail_id($post_id);
    if ($thumb_id) {
        array_unshift($gallery_ids, (string) $thumb_id);
    }
    $gallery_ids = array_unique($gallery_ids);
?>

<!-- Hero camera — design premium -->
<?php
$camere_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-rooms.php', 'number' => 1]);
$camere_url  = !empty($camere_page) ? get_permalink($camere_page[0]->ID) : home_url('/camere/');
?>
<section style="
    position: relative;
    min-height: 60vh;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background: #1a1e22;
    <?php if ($thumb_url) echo 'background-image:url(' . esc_url($thumb_url) . '); background-size:cover; background-position:center;'; ?>
">
    <div style="position:absolute;inset:0;background:linear-gradient(0deg,rgba(15,18,22,.90) 0%,rgba(15,18,22,.35) 60%,rgba(15,18,22,.20) 100%);z-index:1;"></div>

    <div style="position:relative;z-index:2;width:100%;padding:calc(var(--header-h) + 2rem) 2rem 3.5rem;max-width:1200px;margin:0 auto;">

        <!-- Breadcrumb -->
        <nav style="margin-bottom:1.5rem;">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="font-size:.72rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.5);text-decoration:none;">Home</a>
            <span style="color:rgba(255,255,255,.3);margin:0 .625rem;">/</span>
            <a href="<?php echo esc_url($camere_url); ?>" style="font-size:.72rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.5);text-decoration:none;"><?php echo esc_html(alm_t('room.breadcrumb')); ?></a>
            <span style="color:rgba(255,255,255,.3);margin:0 .625rem;">/</span>
            <span style="font-size:.72rem;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.75);"><?php echo esc_html(alm_get_room_translated($post_id, 'title')); ?></span>
        </nav>

        <!-- Badge piano/tipo -->
        <div style="display:flex;flex-wrap:wrap;gap:.625rem;margin-bottom:1.25rem;">
            <?php if ($room_floor) : ?>
            <span style="display:inline-block;padding:.35rem 1rem;background:rgba(197,180,156,.18);border:1px solid rgba(197,180,156,.35);color:#C5B49C;font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">
                <?php echo esc_html(alm_get_floor_label($room_floor->slug)); ?>
            </span>
            <?php endif; ?>
            <?php if ($room_type) : ?>
            <span style="display:inline-block;padding:.35rem 1rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:rgba(255,255,255,.75);font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">
                <?php echo esc_html($room_type->name); ?>
            </span>
            <?php endif; ?>
        </div>

        <h1 style="font-family:var(--font-heading);font-size:clamp(2rem,6vw,3.5rem);font-weight:700;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:1rem;">
            <?php echo esc_html(alm_get_room_translated($post_id, 'title')); ?>
        </h1>

        <?php $room_excerpt_t = alm_get_room_translated($post_id, 'excerpt'); if ($room_excerpt_t) : ?>
        <p style="font-size:1.05rem;color:rgba(255,255,255,.70);line-height:1.75;max-width:640px;margin:0;">
            <?php echo esc_html($room_excerpt_t); ?>
        </p>
        <?php endif; ?>

    </div>
</section>

<!-- Layout 2 colonne: gallery + dettagli -->
<main id="main-content" style="background-color: var(--color-bg); padding: 4rem 0 6rem;">
    <div class="container" style="max-width:1200px;padding-inline:2rem;">
        <div class="row g-5">

            <!-- Colonna sinistra: gallery + contenuto -->
            <div class="col-lg-8">
                
                <!-- Gallery -->
                <?php if (!empty($gallery_ids)) : ?>
                <div style="margin-bottom:3rem;">
                    <?php $first_img = wp_get_attachment_image_src($gallery_ids[0], 'large'); ?>

                    <!-- Immagine principale -->
                    <div style="overflow:hidden;margin-bottom:6px;aspect-ratio:16/9;">
                        <?php if ($first_img) : ?>
                        <img id="room-gallery-main-img"
                             src="<?php echo esc_url($first_img[0]); ?>"
                             alt="<?php echo esc_attr(get_the_title()); ?>"
                             style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;"
                             loading="eager" />
                        <?php endif; ?>
                    </div>

                    <!-- Thumbnails strip -->
                    <?php if (count($gallery_ids) > 1) : ?>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px;">
                        <?php foreach ($gallery_ids as $idx => $img_id) :
                            $thumb = wp_get_attachment_image_src($img_id, 'thumbnail');
                            $full  = wp_get_attachment_image_src($img_id, 'large');
                            if (!$thumb || !$full) continue;
                        ?>
                        <div class="room-gallery__thumb"
                             role="button" tabindex="0"
                             data-full="<?php echo esc_url($full[0]); ?>"
                             style="aspect-ratio:1;overflow:hidden;cursor:pointer;opacity:<?php echo $idx === 0 ? '1' : '.65'; ?>;transition:opacity .2s;<?php echo $idx === 0 ? 'outline:2px solid #C5B49C;outline-offset:-2px;' : ''; ?>"
                             aria-label="<?php echo esc_attr(alm_t('room.img_view')); ?>">
                            <img src="<?php echo esc_url($thumb[0]); ?>" alt="" loading="lazy"
                                 style="width:100%;height:100%;object-fit:cover;display:block;" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                    <script>
                    (function () {
                        var mainImg = document.getElementById('room-gallery-main-img');
                        var thumbs  = document.querySelectorAll('.room-gallery__thumb');
                        thumbs.forEach(function (thumb) {
                            thumb.addEventListener('click', function () {
                                thumbs.forEach(function (t) {
                                    t.style.opacity = '.65';
                                    t.style.outline = 'none';
                                });
                                thumb.style.opacity = '1';
                                thumb.style.outline = '2px solid #C5B49C';
                                thumb.style.outlineOffset = '-2px';
                                if (mainImg) mainImg.src = thumb.dataset.full;
                            });
                            thumb.addEventListener('keydown', function (e) {
                                if (e.key === 'Enter' || e.key === ' ') { thumb.click(); }
                            });
                        });
                    })();
                    </script>
                <?php endif; ?>

                <!-- Descrizione completa -->
                <?php
                $room_content_t = alm_get_room_translated($post_id, 'content');
                if ($room_content_t) :
                ?>
                    <div class="room-details__content bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-4" style="line-height: 1.8;">
                        <h2 class="font-heading fw-bold text-primary mb-4 pb-2 border-bottom border-light">
                            <?php echo esc_html(alm_t('room.the_room')); ?>
                        </h2>
                        <?php echo wp_kses_post(wpautop($room_content_t)); ?>
                    </div>
                <?php endif; ?>

                <!-- Dotazioni -->
                <?php if (!empty($amenities)) : ?>
                    <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-4">
                        <h2 class="font-heading fw-bold text-primary mb-4 pb-2 border-bottom border-light">
                            <?php echo esc_html(alm_t('room.amenities')); ?>
                        </h2>
                        <div class="row row-cols-2 row-cols-md-3 g-3">
                            <?php foreach ($amenities as $amenity) : ?>
                                <div class="col d-flex align-items-center gap-2 py-2">
                                    <span class="dashicons <?php echo esc_attr(alm_get_amenity_icon($amenity->slug)); ?>" style="color: var(--color-secondary); font-size: 20px; width: 20px; height: 20px;"></span>
                                    <span class="fw-medium text-muted small">
                                        <?php echo esc_html($amenity->name); ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Nota bagno condiviso -->
                <?php if ($sharing_note) : ?>
                    <div class="alert alert-warning p-4 rounded-4 shadow-sm border-0 mb-4 d-flex align-items-center gap-3" style="background-color: #FAF3E0; color: #8A6D3B;">
                        <span class="dashicons dashicons-info" style="font-size: 24px; width: 24px; height: 24px; color: #8A6D3B;"></span>
                        <span class="fw-medium small"><?php echo esc_html($sharing_note); ?></span>
                    </div>
                <?php endif; ?>

                <!-- Dettagli soggiorno -->
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-4">
                    <h2 class="font-heading fw-bold text-primary mb-4 pb-2 border-bottom border-light">
                        <?php echo esc_html(alm_t('room.stay_details')); ?>
                    </h2>
                    <div class="row row-cols-1 row-cols-sm-2 g-3">
                        <div class="col d-flex align-items-center gap-2 py-1">
                            <span class="dashicons dashicons-admin-users text-secondary"></span>
                            <span class="text-muted small">
                                <?php
                                printf(
                                    esc_html($meta['capacity_adults'] === 1 ? alm_t('room.adults_1') : alm_t('room.adults_n')),
                                    $meta['capacity_adults']
                                );
                                if ($meta['capacity_children'] > 0) {
                                    echo ' + ';
                                    printf(
                                        esc_html($meta['capacity_children'] === 1 ? alm_t('room.children_1') : alm_t('room.children_n')),
                                        $meta['capacity_children']
                                    );
                                }
                                ?>
                            </span>
                        </div>
                        <div class="col d-flex align-items-center gap-2 py-1">
                            <span class="dashicons dashicons-calendar-alt text-secondary"></span>
                            <span class="text-muted small">
                                <?php
                                if ($meta['min_stay'] > 1) {
                                    printf(esc_html(alm_t('room.min_stay_n')), $meta['min_stay']);
                                } else {
                                    echo esc_html(alm_t('room.min_stay_1'));
                                }
                                ?>
                            </span>
                        </div>
                        <div class="col d-flex align-items-center gap-2 py-1">
                            <span class="dashicons dashicons-clock text-secondary"></span>
                            <span class="text-muted small">
                                <?php echo esc_html(alm_t('room.checkin_time')); ?>
                            </span>
                        </div>
                        <div class="col d-flex align-items-center gap-2 py-1">
                            <span class="dashicons dashicons-clock text-secondary"></span>
                            <span class="text-muted small">
                                <?php echo esc_html(alm_t('room.checkout_time')); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Calendario disponibilità -->
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border border-light mb-4">
                    <h2 class="font-heading fw-bold text-primary mb-4 pb-2 border-bottom border-light">
                        <?php echo esc_html(alm_t('room.availability')); ?>
                    </h2>
                    <?php echo do_shortcode('[alm_availability_calendar room_id="' . esc_attr((string) $post_id) . '"]'); ?>
                </div>

            </div>

            <!-- Colonna destra: box prezzo sticky -->
            <div class="col-lg-4">
                <aside style="position:sticky;top:calc(var(--header-h) + 1.5rem);">
                    <?php echo do_shortcode('[alm_room_price room_id="' . esc_attr((string) $post_id) . '"]'); ?>
                </aside>
            </div>

        </div>
    </div>
</main>

<!-- Sezione "Potrebbe interessarti" -->
<?php
$related_args = [
    'post_type'      => 'almaretna_room',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'post__not_in'   => [$post_id],
    'orderby'        => 'rand',
];

// Prova prima stessa tipologia
if ($room_type) {
    $related_args['tax_query'] = [[
        'taxonomy' => 'room_type',
        'field'    => 'slug',
        'terms'    => $room_type->slug,
    ]];
}

$related_query = new WP_Query($related_args);

// Fallback: se non ci sono abbastanza camere, rimuovi filtro tipologia
if (!$related_query->have_posts() || $related_query->post_count < 1) {
    unset($related_args['tax_query']);
    $related_query = new WP_Query($related_args);
}

if ($related_query->have_posts()) : ?>
    <section class="related-rooms py-5 bg-white border-top border-light">
        <div class="container py-4">
            <h2 class="display-5 fw-bold font-heading text-primary text-center mb-5">
                <?php echo esc_html(alm_t('room.related')); ?>
            </h2>
            <div class="row g-4">
                <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                    <?php
                    $rel_id       = get_the_ID();
                    $rel_meta     = alm_get_room_meta($rel_id);
                    $rel_thumb    = alm_get_room_thumbnail_url($rel_id, 'large');
                    $rel_floor    = alm_get_room_floor($rel_id);
                    $rel_type     = alm_get_room_type($rel_id);
                    ?>
                    <div class="col-md-4">
                        <article class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden" style="background-color: var(--color-bg);">
                            <div class="position-relative overflow-hidden" style="height: 200px;">
                                <?php if ($rel_thumb) : ?>
                                    <img
                                        src="<?php echo esc_url($rel_thumb); ?>"
                                        alt="<?php echo esc_attr(get_the_title()); ?>"
                                        loading="lazy"
                                        class="w-100 h-100 object-fit-cover transition-all hover-scale"
                                    />
                                <?php endif; ?>
                                <div class="position-absolute top-0 start-0 m-3">
                                    <?php if ($rel_floor) : ?>
                                        <span class="badge px-3 py-2 fw-semibold" style="background-color: var(--color-secondary); color: var(--color-white);">
                                            <?php echo esc_html(alm_get_floor_label($rel_floor->slug)); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <h3 class="fs-5 fw-bold font-heading mb-3 text-primary">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-primary hover-text-secondary"><?php echo esc_html(alm_get_room_translated($rel_id, 'title')); ?></a>
                                </h3>
                                <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;"><?php echo esc_html(wp_trim_words(alm_get_room_translated($rel_id, 'excerpt'), 18, '…')); ?></p>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-light">
                                    <div class="room-card__price">
                                        <span class="fs-5 fw-bold text-primary">
                                            <?php echo wp_kses_post(alm_format_price($rel_meta['base_price'])); ?>
                                        </span>
                                        <span class="text-muted small"><?php echo esc_html(alm_t('room.per_night')); ?></span>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary px-3 py-2 rounded-3 fw-semibold border-1" style="color: var(--color-primary); border-color: var(--color-primary); font-size: 0.85rem;">
                                        <?php echo esc_html(alm_t('room.discover')); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Schema markup JSON-LD HotelRoom -->
<script type="application/ld+json">
<?php
$amenity_features = [];
foreach ($amenities as $am) {
    $amenity_features[] = [
        '@type' => 'LocationFeatureSpecification',
        'name'  => $am->name,
        'value' => true,
    ];
}

$schema = [
    '@context'       => 'https://schema.org',
    '@type'          => 'HotelRoom',
    'name'           => get_the_title(),
    'description'    => get_the_excerpt(),
    'occupancy'      => [
        '@type'    => 'QuantitativeValue',
        'maxValue' => $meta['capacity_adults'] + $meta['capacity_children'],
    ],
    'amenityFeature' => $amenity_features,
    'containedInPlace' => [
        '@type'   => 'LodgingBusiness',
        'name'    => 'Almaretna',
        'address' => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Via Scorciavacca Montarsi, 48',
            'addressLocality' => 'Nunziata di Mascali',
            'addressRegion'   => 'CT',
            'postalCode'      => '95016',
            'addressCountry'  => 'IT',
        ],
    ],
];

if ($thumb_url) {
    $schema['image'] = $thumb_url;
}
if ($meta['base_price'] > 0) {
    $schema['offers'] = [
        '@type'         => 'Offer',
        'price'         => $meta['base_price'],
        'priceCurrency' => 'EUR',
        'url'           => get_permalink(),
    ];
}

echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
</script>

<?php
// Recupera le 4 foto pre-footer
$footer_images = [];
for ($i = 1; $i <= 4; $i++) {
    $img_id = (int) get_post_meta($post_id, "_room_footer_img_{$i}", true);
    if ($img_id > 0) {
        $img_url = wp_get_attachment_image_url($img_id, 'large');
        if ($img_url) {
            $footer_images[] = $img_url;
        }
    }
}

if (!empty($footer_images)) :
?>
<!-- Galleria Dettaglio Pre-Footer -->
<section class="room-details__pre-footer py-5" style="background-color: var(--color-accent); border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <?php foreach ($footer_images as $img_url) : ?>
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card border-0 rounded-4 overflow-hidden shadow-sm h-100 position-relative" style="aspect-ratio: 4/3;">
                        <img src="<?php echo esc_url($img_url); ?>" alt="" class="w-100 h-100 object-fit-cover transition-all hover-scale" />
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
endwhile;
get_footer();
?>
