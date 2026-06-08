<?php
/**
 * Almaretna Child — footer.php
 *
 * @package AlmaretnaChild
 */
?>
</div><!-- #content -->

<footer class="site-footer">

    <div class="site-footer__top">
        <div class="site-footer__container">

            <!-- Brand col -->
            <div class="site-footer__col site-footer__col--brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer__logo" style="display:block;margin-bottom:var(--space-md);">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/logo-white-transparent.png'); ?>" alt="Almaretna Villa Vacanze" class="site-logo-footer" style="max-height: 91px; width: auto; height: auto; display: block;" />
                </a>
                <p class="site-footer__tagline">
                    <?php echo esc_html(alm_t('foot.tagline')); ?>
                </p>
                <div class="site-footer__social">
                    <?php $fb = get_option('alm_schema_facebook_url', ''); ?>
                    <?php $ig = get_option('alm_schema_instagram_url', ''); ?>
                    <?php if ($fb) : ?>
                        <a href="<?php echo esc_url($fb); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                    <?php endif; ?>
                    <?php if ($ig) : ?>
                        <a href="<?php echo esc_url($ig); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Nav col -->
            <div class="site-footer__col">
                <h4 class="site-footer__heading"><?php echo esc_html(alm_t('foot.explore')); ?></h4>
                <?php
                $camere_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-rooms.php', 'number' => 1]);
                $camere_url  = !empty($camere_page) ? get_permalink($camere_page[0]->ID) : home_url('/camere/');

                $prenota_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
                $prenota_url  = !empty($prenota_page) ? get_permalink($prenota_page[0]->ID) : home_url('/prenota/');

                wp_nav_menu([
                    'theme_location' => 'footer',
                    'menu_class'     => 'site-footer__menu',
                    'container'      => false,
                    'depth'          => 1,
                    'fallback_cb'    => function () use ($camere_url, $prenota_url): void {
                        echo '<ul class="site-footer__menu">';
                        echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
                        echo '<li><a href="' . esc_url($camere_url) . '">' . esc_html(alm_t('foot.our_rooms')) . '</a></li>';
                        echo '<li><a href="' . esc_url($prenota_url) . '">' . esc_html(alm_t('foot.book')) . '</a></li>';
                        echo '</ul>';
                    },
                ]); ?>
            </div>

            <!-- Contact col -->
            <div class="site-footer__col">
                <h4 class="site-footer__heading"><?php echo esc_html(alm_t('foot.contacts')); ?></h4>
                <address class="site-footer__address">
                    <p>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Via Scorciavacca Montarsi, 48<br>
                        Nunziata di Mascali (CT)
                    </p>
                    <?php
                    $settings  = get_option('alm_booking_settings', []);
                    $host_email = sanitize_email($settings['host_email'] ?? get_option('admin_email', ''));
                    $host_phone = sanitize_text_field($settings['host_phone'] ?? '');
                    ?>
                    <?php if ($host_email) : ?>
                    <p>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:<?php echo esc_attr($host_email); ?>"><?php echo esc_html($host_email); ?></a>
                    </p>
                    <?php endif; ?>
                    <?php if ($host_phone) : ?>
                    <p>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 0h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92z"/></svg>
                        <a href="tel:<?php echo esc_attr($host_phone); ?>"><?php echo esc_html($host_phone); ?></a>
                    </p>
                    <?php endif; ?>
                </address>
            </div>

            <!-- Check-in col -->
            <div class="site-footer__col">
                <h4 class="site-footer__heading"><?php echo esc_html(alm_t('foot.info')); ?></h4>
                <ul class="site-footer__info">
                    <?php
                    $cin_time  = sanitize_text_field($settings['checkin_time']  ?? '15:00');
                    $cout_time = sanitize_text_field($settings['checkout_time'] ?? '11:00');
                    ?>
                    <li><?php echo esc_html(alm_t('foot.cin')); ?> <?php echo esc_html(alm_t('foot.cin_from') . $cin_time); ?></li>
                    <li><?php echo esc_html(alm_t('foot.cout')); ?> <?php echo esc_html(alm_t('foot.cout_by') . $cout_time); ?></li>
                    <li><?php echo esc_html(alm_t('foot.pool')); ?></li>
                    <li><?php echo esc_html(alm_t('foot.bar')); ?></li>
                    <li><?php echo esc_html(alm_t('foot.wifi')); ?></li>
                    <li><?php echo esc_html(alm_t('foot.parking')); ?></li>
                </ul>
            </div>

        </div>
    </div><!-- /.site-footer__top -->

    <div class="site-footer__bottom">
        <div class="site-footer__container">
            <p>&copy; <?php echo esc_html(date('Y')); ?> Almaretna. <?php echo esc_html(alm_t('foot.rights')); ?></p>
            <p style="display:inline-flex;align-items:center;gap:.5rem;">
                <span><?php echo esc_html(alm_t('foot.payments')); ?></span>
                <svg width="44" height="20" viewBox="0 0 44 20" fill="none" style="flex-shrink:0;opacity:.85;display:block;" xmlns="http://www.w3.org/2000/svg">
                    <rect width="44" height="20" rx="3" fill="#635BFF"/>
                    <text x="6" y="14" font-size="8.5" fill="white" font-family="Arial,sans-serif" font-weight="600">Stripe</text>
                </svg>
            </p>
        </div>
    </div>

</footer>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
