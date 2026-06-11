<?php
/**
 * Almaretna Child — header.php
 *
 * @package AlmaretnaChild
 */

$camere_page  = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-rooms.php', 'number' => 1]);
$camere_url   = alm_url_with_lang(!empty($camere_page) ? get_permalink($camere_page[0]->ID) : home_url('/camere/'));
$prenota_page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
$prenota_url  = alm_url_with_lang(!empty($prenota_page) ? get_permalink($prenota_page[0]->ID) : home_url('/prenota/'));
$scopri_page  = get_page_by_path('scopri-la-sicilia');
$scopri_url   = alm_url_with_lang($scopri_page ? (string) get_permalink($scopri_page->ID) : home_url('/scopri-la-sicilia/'));

$dest_items = [
    ['taormina',         alm_t('nav.home') === 'Home' ? 'Taormina'          : 'Taormina',         'La perla dello Ionio'],
    ['etna',             'Etna',                                                                    "Il vulcano d'Europa"],
    ['catania',          'Catania',                                                                 'Barocco e vivacità'],
    ['siracusa',         'Siracusa',                                                                'Patrimonio UNESCO'],
    ['messina',          'Messina',                                                                 'Porta della Sicilia'],
    ['mare-di-sicilia',  'Il mare di Sicilia',                                                      'Acque cristalline'],
];
// Traduzione tagline non necessaria per ora — tutte le lingue usano nomi propri
$dest_items = [
    ['taormina',        'Taormina',          'La perla dello Ionio'],
    ['etna',            'Etna',              "Il vulcano d'Europa"],
    ['catania',         'Catania',           'Barocco e vivacità'],
    ['siracusa',        'Siracusa',          'Patrimonio UNESCO'],
    ['messina',         'Messina',           'Porta della Sicilia'],
    ['mare-di-sicilia', 'Il mare di Sicilia','Acque cristalline'],
];
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

<!-- ════ HEADER ════════════════════════════════════════════════════════════ -->
<header class="site-header<?php echo is_front_page() ? ' site-header--home' : ''; ?>" id="site-header">
    <div class="site-header__inner">

        <!-- Hamburger (mobile) -->
        <button class="hamburger" id="nav-toggle"
                aria-expanded="false"
                aria-controls="mobile-nav"
                aria-label="<?php echo esc_attr(alm_t('nav.open_menu')); ?>">
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
            <span class="hamburger__line"></span>
        </button>

        <!-- Logo -->
        <a href="<?php echo esc_url(alm_url_with_lang(home_url('/'))); ?>" class="site-header__logo" rel="home">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/logo-transparent.png'); ?>"
                 alt="Almaretna Villa Vacanze" class="site-logo" />
        </a>

        <!-- Desktop nav -->
        <nav class="site-nav" id="site-nav"
             aria-label="<?php echo esc_attr(alm_t('nav.main_menu')); ?>">
            <ul class="site-nav__list" role="list">

                <li>
                    <a href="<?php echo esc_url(alm_url_with_lang(home_url('/'))); ?>"
                       class="site-nav__list__link<?php echo is_front_page() ? ' current' : ''; ?>">
                        <?php echo esc_html(alm_t('nav.home')); ?>
                    </a>
                </li>

                <li>
                    <a href="<?php echo esc_url($camere_url); ?>"
                       class="site-nav__list__link<?php echo is_page_template('templates/template-rooms.php') ? ' current' : ''; ?>">
                        <?php echo esc_html(alm_t('nav.rooms')); ?>
                    </a>
                </li>

                <!-- Mega trigger -->
                <li class="site-nav__item--has-mega" id="nav-scopri-li">
                    <button class="site-nav__mega-trigger" id="nav-scopri-btn"
                            aria-expanded="false"
                            aria-controls="nav-mega-scopri"
                            aria-haspopup="true">
                        <?php echo esc_html(alm_t('nav.discover')); ?>
                        <svg class="nav-chevron" width="13" height="13" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                </li>

            </ul>
        </nav>

        <!-- Right end: lang switcher + prenota CTA -->
        <div class="site-header__end">
            <?php echo alm_lang_switcher_html(); ?>
            <div class="alm-lang-backdrop"
                 onclick="document.getElementById('almLang').classList.remove('open')"></div>
            <a href="<?php echo esc_url($prenota_url); ?>" class="site-header__book-btn">
                <?php echo esc_html(alm_t('nav.book_now')); ?>
            </a>
        </div>

    </div>
</header>

<!-- ════ MEGA DROPDOWN ════════════════════════════════════════════════════ -->
<div class="nav-mega" id="nav-mega-scopri"
     role="region"
     aria-label="<?php esc_attr_e('Destinazioni Sicilia', 'almaretna-child'); ?>"
     aria-hidden="true">
    <div class="nav-mega__inner">

        <div class="nav-mega__head">
            <div>
                <p class="nav-mega__eyebrow"><?php echo esc_html(alm_t('nav.discover_eyebrow')); ?></p>
                <h2 class="nav-mega__title"><?php echo esc_html(alm_t('nav.discover')); ?></h2>
            </div>
            <a href="<?php echo esc_url($scopri_url); ?>" class="nav-mega__all-link">
                <?php echo esc_html(alm_t('nav.all_destinations')); ?>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </a>
        </div>

        <div class="nav-mega__grid" id="nav-mega-grid">
            <?php foreach ($dest_items as $i => [$slug, $title, $tagline]) :
                $dp  = get_page_by_path($slug);
                $url = $dp ? alm_url_with_lang((string) get_permalink($dp->ID)) : home_url('/' . $slug . '/');
            ?>
            <a href="<?php echo esc_url($url); ?>"
               class="mega-card mega-card--skeleton"
               data-slug="<?php echo esc_attr($slug); ?>">
                <div class="mega-card__img"></div>
                <div class="mega-card__body">
                    <h3 class="mega-card__title"><?php echo esc_html($title); ?></h3>
                    <p class="mega-card__desc"><?php echo esc_html($tagline); ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
</div>
<div class="nav-mega-backdrop" id="nav-mega-backdrop"></div>

<!-- ════ MOBILE DRAWER ════════════════════════════════════════════════════ -->
<div class="mobile-nav" id="mobile-nav"
     aria-hidden="true"
     role="dialog"
     aria-label="<?php echo esc_attr(alm_t('nav.mobile_menu')); ?>">

    <div class="mobile-nav__header">
        <span class="mobile-nav__brand">Almaretna</span>
        <button class="mobile-nav__close" id="nav-close"
                aria-label="<?php echo esc_attr(alm_t('nav.close_menu')); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" aria-hidden="true">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <ul class="mobile-nav__list">

        <li>
            <a href="<?php echo esc_url(alm_url_with_lang(home_url('/'))); ?>">
                <?php echo esc_html(alm_t('nav.home')); ?>
            </a>
        </li>

        <li>
            <a href="<?php echo esc_url($camere_url); ?>">
                <?php echo esc_html(alm_t('nav.rooms')); ?>
            </a>
        </li>

        <!-- Accordion: Scopri la Sicilia -->
        <li class="mobile-nav__item--has-sub">
            <button class="mobile-nav__sub-toggle" aria-expanded="false">
                <?php echo esc_html(alm_t('nav.discover')); ?>
                <svg class="mobile-nav__sub-chevron" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
            <ul class="mobile-nav__sub">
                <?php foreach ($dest_items as [$slug, $title, $tagline]) :
                    $dp  = get_page_by_path($slug);
                    $url = $dp ? alm_url_with_lang((string) get_permalink($dp->ID)) : home_url('/' . $slug . '/');
                ?>
                <li>
                    <a href="<?php echo esc_url($url); ?>">
                        <span class="mobile-nav__sub-title"><?php echo esc_html($title); ?></span>
                        <span class="mobile-nav__sub-desc"><?php echo esc_html($tagline); ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
                <li>
                    <a href="<?php echo esc_url($scopri_url); ?>" class="mobile-nav__sub-all">
                        <?php echo esc_html(alm_t('nav.all_destinations')); ?> →
                    </a>
                </li>
            </ul>
        </li>

    </ul>

    <a href="<?php echo esc_url($prenota_url); ?>" class="mobile-nav__cta">
        <?php echo esc_html(alm_t('nav.book_now')); ?>
    </a>

</div>
<div class="nav-backdrop" id="nav-backdrop"></div>

<div id="content" class="site-content">
