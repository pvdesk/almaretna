<?php
/**
 * Template Name: Pagina Camere
 * Template Post Type: page
 *
 * Template per la pagina /camere/ — archivio camere con filtri e layout Bootstrap.
 *
 * @package AlmaretnaChild
 */

declare(strict_types=1);

get_header();

// Hero: admin dashboard → customizer → fallback
$hero_photo = alm_get_site_photo('camere_hero')
    ?: get_theme_mod('almaretna_rooms_hero_bg')
    ?: content_url('uploads/2026/06/camera-matrimoniale.webp');
?>

<main id="main-content" class="page-rooms">

    <!-- Hero premium pagina camere -->
    <section class="page-hero-premium" style="
        position: relative;
        min-height: 52vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        overflow: hidden;
        background: #1a1e22;
        <?php if ($hero_photo) : ?>background-image: url('<?php echo esc_url($hero_photo); ?>'); background-size: cover; background-position: center;<?php endif; ?>
    ">
        <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,24,28,.55) 0%,rgba(20,24,28,.72) 100%);z-index:1;"></div>
        <div style="position:relative;z-index:2;padding:calc(var(--header-h) + 3rem) 2rem 4rem;max-width:760px;width:100%;">
            <p style="font-size:.7rem;font-weight:700;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.65);margin-bottom:1.25rem;display:flex;align-items:center;justify-content:center;gap:.875rem;">
                <span style="display:block;width:28px;height:1px;background:rgba(255,255,255,.35);"></span>
                Nunziata di Mascali &nbsp;·&nbsp; Sicilia
                <span style="display:block;width:28px;height:1px;background:rgba(255,255,255,.35);"></span>
            </p>
            <h1 style="font-family:var(--font-heading);font-size:clamp(2.25rem,6vw,3.75rem);font-weight:700;color:#fff;line-height:1.1;letter-spacing:-.02em;margin-bottom:1.25rem;">
                <?php echo esc_html(alm_t('rooms.page_h1')); ?>
            </h1>
            <p style="font-size:1.05rem;color:rgba(255,255,255,.75);line-height:1.75;max-width:560px;margin:0 auto;">
                <?php
                $subtitle = get_post_meta(get_the_ID(), '_page_subtitle', true);
                echo esc_html($subtitle ?: alm_t('rooms.page_subtitle'));
                ?>
            </p>
        </div>
    </section>

    <!-- Contenuto pagina (se impostato) -->
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php
        $content = get_the_content();
        if (trim($content)) :
            $content_filtered = preg_replace('/\[alm_rooms_grid[^\]]*\]/i', '', $content);
            if (trim($content_filtered)) :
        ?>
            <section class="py-5 bg-white">
                <div class="container">
                    <div class="entry-content text-center mx-auto" style="max-width: 800px; line-height: 1.7;">
                        <?php echo apply_filters('the_content', $content_filtered); ?>
                    </div>
                </div>
            </section>
            <?php endif; ?>
        <?php endif; ?>
    <?php endwhile; endif; wp_reset_postdata(); ?>

    <!-- Filtri + Griglia -->
    <section class="py-5" style="background-color: var(--color-bg);">
        <div class="container">

            <!-- Filtri (Bootstrap Row + Form Controls) -->
            <div class="rooms-filters row g-3 align-items-end mb-5 p-4 rounded-4 bg-white shadow-sm border border-light" id="rooms-filters">
                <div class="col-12 col-md-auto mb-2 mb-md-0 d-flex align-items-center gap-2 fw-semibold text-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span class="text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.1em;"><?php echo esc_html(alm_t('rooms.filter')); ?></span>
                </div>

                <div class="col-sm-6 col-md-3">
                    <label for="filter-type" class="form-label text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;"><?php echo esc_html(alm_t('rooms.type_label')); ?></label>
                    <select id="filter-type" class="form-select border-light p-2 rounded-3 shadow-none bg-light" data-filter="type" style="font-size: 0.9rem; cursor: pointer;">
                        <option value=""><?php echo esc_html(alm_t('rooms.all_types')); ?></option>
                        <?php
                        $types = get_terms(['taxonomy' => 'room_type', 'hide_empty' => true]);
                        if (is_array($types)) {
                            foreach ($types as $term) {
                                printf(
                                    '<option value="%s">%s</option>',
                                    esc_attr($term->slug),
                                    esc_html($term->name)
                                );
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-6 col-md-3">
                    <label for="filter-floor" class="form-label text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;"><?php echo esc_html(alm_t('rooms.floor_label')); ?></label>
                    <select id="filter-floor" class="form-select border-light p-2 rounded-3 shadow-none bg-light" data-filter="floor" style="font-size: 0.9rem; cursor: pointer;">
                        <option value=""><?php echo esc_html(alm_t('rooms.all_floors')); ?></option>
                        <?php
                        $floors = get_terms(['taxonomy' => 'room_floor', 'hide_empty' => true]);
                        if (is_array($floors)) {
                            foreach ($floors as $term) {
                                printf(
                                    '<option value="%s">%s</option>',
                                    esc_attr($term->slug),
                                    esc_html($term->name)
                                );
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-sm-6 col-md-3">
                    <label for="filter-adults" class="form-label text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;"><?php echo esc_html(alm_t('rooms.guests_label')); ?></label>
                    <select id="filter-adults" class="form-select border-light p-2 rounded-3 shadow-none bg-light" data-filter="adults" style="font-size: 0.9rem; cursor: pointer;">
                        <option value=""><?php echo esc_html(alm_t('rooms.any')); ?></option>
                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                            <option value="<?php echo esc_attr((string) $i); ?>">
                                <?php echo esc_html(sprintf($i === 1 ? alm_t('rooms.guest_1') : alm_t('rooms.guest_n'), $i)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="col-sm-6 col-md text-sm-end">
                    <button type="button" class="btn btn-outline-secondary w-100 py-2 rounded-3" id="reset-filters" style="display:none; font-size: 0.9rem; border-color: var(--color-border); color: var(--color-text-light);">
                        <?php echo esc_html(alm_t('rooms.reset')); ?>
                    </button>
                </div>
            </div>

            <!-- Contatore risultati -->
            <div class="rooms-results-header mb-4 text-muted small fw-medium" id="rooms-results-header">
                <?php
                $total_rooms = alm_rooms_query()->found_posts;
                printf(
                    '<span class="rooms-results-count bg-white px-3 py-2 rounded-pill border border-light shadow-sm">%s</span>',
                    esc_html(sprintf($total_rooms === 1 ? alm_t('rooms.n_avail_1') : alm_t('rooms.n_avail_n'), $total_rooms))
                );
                ?>
            </div>

            <!-- Grid camere -->
            <div id="rooms-grid-container" class="rooms-grid-wrapper">
                <?php echo do_shortcode('[alm_rooms_grid columns="3"]'); ?>
            </div>

        </div>
    </section>

    <!-- CTA finale -->
    <?php
    $prenota_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
    $prenota_url  = alm_url_with_lang(!empty($prenota_page) ? get_permalink($prenota_page[0]->ID) : home_url('/prenota/'));
    ?>
    <section class="py-5 bg-white border-top border-light">
        <div class="container py-4">
            <div class="rooms-cta-inner p-5 rounded-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-4" style="background-color: var(--color-bg);">
                <div class="rooms-cta-text text-center text-md-start">
                    <h2 class="fw-bold font-heading text-primary mb-2"><?php echo esc_html(alm_t('rooms.cta_title')); ?></h2>
                    <p class="text-muted mb-0"><?php echo esc_html(alm_t('rooms.cta_desc')); ?></p>
                </div>
                <a href="<?php echo esc_url($prenota_url); ?>" class="btn btn-primary px-4 py-3 rounded-3 fw-semibold border-0" style="background-color: var(--color-primary); color: var(--color-white);">
                    <?php echo esc_html(alm_t('rooms.verify_avail')); ?>
                </a>
            </div>
        </div>
    </section>

</main>

<script>
(function () {
    'use strict';
    var selects     = document.querySelectorAll('[data-filter]');
    var resetBtn    = document.getElementById('reset-filters');
    var resultsHdr  = document.getElementById('rooms-results-header');
    var container   = document.getElementById('rooms-grid-container');

    function getFilters() {
        return {
            type:   document.querySelector('[data-filter="type"]').value,
            floor:  document.querySelector('[data-filter="floor"]').value,
            adults: document.querySelector('[data-filter="adults"]').value,
        };
    }

    function hasActiveFilters(f) {
        return f.type || f.floor || f.adults;
    }

    function buildShortcode(f) {
        return '[alm_rooms_grid columns="3"' +
            (f.type   ? ' type="'   + f.type   + '"' : '') +
            (f.floor  ? ' floor="'  + f.floor  + '"' : '') +
            (f.adults ? ' adults="' + f.adults + '"' : '') +
            ']';
    }

    var originalContent = '';
    function setLoading(on) {
        if (on) {
            originalContent = container.innerHTML;
            container.innerHTML = '<div class="row g-4">' +
                '<div class="col-12 col-md-4"><div class="skeleton-card"><div class="skeleton-image"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--title"><div class="shimmer"></div></div><div class="skeleton-text"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--short"><div class="shimmer"></div></div></div></div>' +
                '<div class="col-12 col-md-4"><div class="skeleton-card"><div class="skeleton-image"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--title"><div class="shimmer"></div></div><div class="skeleton-text"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--short"><div class="shimmer"></div></div></div></div>' +
                '<div class="col-12 col-md-4"><div class="skeleton-card"><div class="skeleton-image"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--title"><div class="shimmer"></div></div><div class="skeleton-text"><div class="shimmer"></div></div><div class="skeleton-text skeleton-text--short"><div class="shimmer"></div></div></div></div>' +
                '</div>';
            container.style.opacity = '0.9';
            container.style.pointerEvents = 'none';
        } else {
            container.style.opacity   = '1';
            container.style.pointerEvents = '';
        }
    }

    var almAvail1 = '<?php echo esc_js(alm_t('rooms.n_avail_1')); ?>';
    var almAvailN = '<?php echo esc_js(alm_t('rooms.n_avail_n')); ?>';
    function updateResultsCount(html) {
        var tmp = document.createElement('div');
        tmp.innerHTML = html;
        var cards = tmp.querySelectorAll('.room-card');
        var n = cards.length;
        if (resultsHdr) {
            resultsHdr.querySelector('.rooms-results-count').textContent =
                (n === 1 ? almAvail1 : almAvailN).replace('%d', n);
        }
    }

    function reload() {
        var f  = getFilters();
        var sc = buildShortcode(f);

        if (resetBtn) resetBtn.style.display = hasActiveFilters(f) ? '' : 'none';

        var data = new FormData();
        data.append('action',    'alm_render_shortcode');
        data.append('shortcode', sc);
        data.append('nonce',     (typeof alm_theme_vars !== 'undefined' ? alm_theme_vars.nonce : ''));

        setLoading(true);
        fetch(
            (typeof alm_theme_vars !== 'undefined' ? alm_theme_vars.ajax_url : '/wp-admin/admin-ajax.php'),
            { method: 'POST', body: data }
        )
        .then(function (r) { return r.json(); })
        .then(function (resp) {
            if (resp.success) {
                container.innerHTML = resp.data;
                updateResultsCount(resp.data);
            }
        })
        .catch(function () {
            if (originalContent) {
                container.innerHTML = originalContent;
            }
        })
        .finally(function () { setLoading(false); });
    }

    selects.forEach(function (sel) {
        sel.addEventListener('change', reload);
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            selects.forEach(function (sel) { sel.value = ''; });
            reload();
        });
    }
})();
</script>

<?php get_footer(); ?>
