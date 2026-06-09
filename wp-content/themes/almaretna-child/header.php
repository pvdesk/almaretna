<?php
/**
 * Almaretna Child — header.php
 *
 * @package AlmaretnaChild
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <!-- PWA -->
    <meta name="theme-color" content="#C5B49C">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Almaretna">
    <link rel="manifest" href="<?php echo esc_url(home_url('/manifest.json')); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/img/icon-192.png">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

<header class="site-header<?php echo is_front_page() ? ' site-header--home' : ''; ?>" id="site-header">
    <div class="site-header__inner">

        <!-- Hamburger: primo figlio → appare a sinistra su mobile, display:none su desktop -->
        <button class="hamburger" id="nav-toggle"
                aria-expanded="false"
                aria-controls="mobile-nav"
                aria-label="<?php echo esc_attr(alm_t('nav.open_menu')); ?>">
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
        </button>

        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-header__logo" rel="home">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/logo-transparent.png'); ?>" alt="Almaretna Villa Vacanze" class="site-logo" />
        </a>

        <nav class="site-nav" id="site-nav" aria-label="<?php esc_attr_e('Menu principale', 'almaretna-child'); ?>">
            <?php
            $camere_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-rooms.php', 'number' => 1]);
            $camere_url  = !empty($camere_page) ? get_permalink($camere_page[0]->ID) : home_url('/camere/');

            $prenota_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
            $prenota_url  = !empty($prenota_page) ? get_permalink($prenota_page[0]->ID) : home_url('/prenota/');

            wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'site-nav__list',
                'container'      => false,
                'fallback_cb'    => function () use ($camere_url): void {
                    echo '<ul class="site-nav__list">';
                    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
                    echo '<li><a href="' . esc_url($camere_url) . '">Camere</a></li>';
                    echo '</ul>';
                },
            ]); ?>
        </nav>

        <div class="site-header__end">
            <?php echo alm_lang_switcher_html(); ?>
            <div class="alm-lang-backdrop" onclick="document.getElementById('almLang').classList.remove('open')"></div>
            <a href="<?php echo esc_url($prenota_url); ?>" class="site-header__book-btn">
                <?php echo esc_html(alm_t('nav.book_now')); ?>
            </a>
        </div>

    </div>
</header>

<!-- Mobile navigation drawer -->
<div class="mobile-nav" id="mobile-nav" aria-hidden="true" role="dialog" aria-label="<?php esc_attr_e('Menu mobile', 'almaretna-child'); ?>">
    <div class="mobile-nav__header">
        <span class="mobile-nav__brand">Almaretna</span>
        <button class="mobile-nav__close" id="nav-close" aria-label="<?php echo esc_attr(alm_t('nav.close_menu')); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <?php wp_nav_menu([
        'theme_location' => 'primary',
        'menu_class'     => 'mobile-nav__list',
        'container'      => false,
        'fallback_cb'    => function () use ($camere_url, $prenota_url): void {
            echo '<ul class="mobile-nav__list">';
            echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
            echo '<li><a href="' . esc_url($camere_url) . '">Camere</a></li>';
            echo '<li><a href="' . esc_url($prenota_url) . '">Prenota</a></li>';
            echo '</ul>';
        },
    ]); ?>
    <a href="<?php echo esc_url($prenota_url); ?>" class="mobile-nav__cta">
        <?php echo esc_html(alm_t('nav.book_now')); ?>
    </a>
</div>
<div class="nav-backdrop" id="nav-backdrop"></div>

<div id="content" class="site-content">
