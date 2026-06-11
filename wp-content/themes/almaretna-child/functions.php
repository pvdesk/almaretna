<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — functions.php
 *
 * @package AlmaretnaChild
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

// ─── Enqueue styles & scripts ───────────────────────────────────────────────

add_action('wp_enqueue_scripts', function (): void {
    $theme_version = wp_get_theme()->get('Version');
    $theme_uri     = get_stylesheet_directory_uri();

    // Parent theme (Astra)
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        [],
        defined('ASTRA_THEME_VERSION') ? ASTRA_THEME_VERSION : '1.0'
    );

    // Google Fonts — preconnect per performance
    wp_enqueue_style(
        'almaretna-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap',
        [],
        null
    );

    // Bootstrap 5.3 CSS
    wp_enqueue_style(
        'bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        [],
        '5.3.3'
    );

    // Bootstrap 5.3 JS Bundle
    wp_enqueue_script(
        'bootstrap-bundle',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        [],
        '5.3.3',
        true
    );

    // Child theme base (CSS variables + reset Astra nel style.css)
    wp_enqueue_style(
        'almaretna-child-style',
        get_stylesheet_uri(),
        ['astra-parent-style', 'almaretna-google-fonts', 'bootstrap'],
        $theme_version
    );

    // CSS assets — ordine: variabili → layout → rooms → booking → responsive
    wp_enqueue_style(
        'almaretna-variables',
        $theme_uri . '/assets/css/variables.css',
        ['almaretna-child-style'],
        $theme_version
    );

    wp_enqueue_style(
        'almaretna-layout',
        $theme_uri . '/assets/css/layout.css',
        ['almaretna-variables'],
        $theme_version
    );

    wp_enqueue_style(
        'almaretna-rooms',
        $theme_uri . '/assets/css/rooms.css',
        ['almaretna-layout'],
        $theme_version
    );

    wp_enqueue_style(
        'almaretna-booking',
        $theme_uri . '/assets/css/booking.css',
        ['almaretna-layout'],
        $theme_version
    );

    wp_enqueue_style(
        'almaretna-responsive',
        $theme_uri . '/assets/css/responsive.css',
        ['almaretna-layout', 'almaretna-rooms', 'almaretna-booking'],
        $theme_version
    );

    // Premium design CSS (animazioni, sezioni premium)
    wp_enqueue_style(
        'almaretna-premium',
        $theme_uri . '/assets/css/premium.css',
        ['almaretna-layout'],
        $theme_version
    );

    // JS assets
    wp_enqueue_script(
        'almaretna-nav',
        $theme_uri . '/assets/js/navigation.js',
        [],
        $theme_version,
        true
    );

    wp_enqueue_script(
        'almaretna-calendar',
        $theme_uri . '/assets/js/calendar.js',
        [],
        $theme_version,
        true
    );

    wp_enqueue_script(
        'almaretna-booking-form',
        $theme_uri . '/assets/js/booking-form.js',
        ['almaretna-calendar'],
        $theme_version,
        true
    );

    wp_localize_script('almaretna-booking-form', 'alm_theme_vars', [
        'ajax_url'               => admin_url('admin-ajax.php'),
        'nonce'                  => wp_create_nonce('alm_theme_nonce'),
        'stripe_publishable_key' => class_exists('ALM_Stripe') ? ALM_Stripe::get_publishable_key() : (defined('ALM_STRIPE_PUBLISHABLE_KEY') ? ALM_STRIPE_PUBLISHABLE_KEY : ''),
        'siteurl'                => get_site_url(),
        'rest_url'               => get_rest_url(null, 'scv/v1/'),
        'confirmation_url'       => (function (): string {
            $page = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'templates/template-booking.php', 'number' => 1]);
            return !empty($page) ? (string) get_permalink($page[0]->ID) : home_url('/prenota/');
        })(),
        'i18n'                   => [
            'calculating'  => alm_t('js.calculating'),
            'dates_unavail'=> alm_t('js.dates_unavail'),
            'night_1'      => alm_t('js.night_1'),
            'night_n'      => alm_t('js.night_n'),
            'selected'     => alm_t('js.selected'),
            'conn_error'   => alm_t('js.conn_error'),
        ],
    ]);
});

// ─── Theme support ───────────────────────────────────────────────────────────

add_action('after_setup_theme', function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');

    register_nav_menus([
        'primary' => __('Menu Principale', 'almaretna-child'),
        'footer'  => __('Menu Footer', 'almaretna-child'),
    ]);
});

// ─── AJAX: render shortcode per filtri camere ────────────────────────────────

add_action('wp_ajax_alm_render_shortcode', 'alm_ajax_render_shortcode');
add_action('wp_ajax_nopriv_alm_render_shortcode', 'alm_ajax_render_shortcode');

function alm_ajax_render_shortcode(): void {
    check_ajax_referer('alm_theme_nonce', 'nonce');
    $shortcode = isset($_POST['shortcode']) ? wp_unslash((string) $_POST['shortcode']) : '';
    // Whitelist: solo shortcode alm_rooms_grid
    if (!preg_match('/^\[alm_rooms_grid\b[^\]]*\]$/', $shortcode)) {
        wp_send_json_error('Shortcode non consentito.');
    }
    wp_send_json_success(do_shortcode($shortcode));
}

// ─── Scroll-to-top personalizzato (sostituisce quello di Astra) ──────────────

add_action('wp_footer', function (): void {
    ?>
    <button id="alm-scroll-top"
            aria-label="<?php esc_attr_e('Torna su', 'almaretna-child'); ?>"
            onclick="window.scrollTo({top:0,behavior:'smooth'})"
            style="
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                z-index: 9999;
                width: 48px;
                height: 48px;
                min-width: 0;
                padding: 0;
                box-sizing: border-box;
                border-radius: 50%;
                background: #C5B49C;
                color: #fff;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                line-height: 1;
                box-shadow: 0 4px 16px rgba(0,0,0,.18);
                opacity: 0;
                transform: translateY(12px);
                transition: opacity .3s ease, transform .3s ease, background .2s ease;
                pointer-events: none;
            ">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round"
             style="display:block;margin:0 auto;">
            <polyline points="18 15 12 9 6 15"/>
        </svg>
    </button>
    <script>
    (function(){
        var btn = document.getElementById('alm-scroll-top');
        if(!btn) return;
        window.addEventListener('scroll', function(){
            var show = window.scrollY > 320;
            btn.style.opacity     = show ? '1'    : '0';
            btn.style.transform   = show ? 'translateY(0)' : 'translateY(12px)';
            btn.style.pointerEvents = show ? 'auto' : 'none';
        }, {passive: true});
        btn.addEventListener('mouseenter', function(){ btn.style.background = '#A6957E'; });
        btn.addEventListener('mouseleave', function(){ btn.style.background = '#C5B49C'; });
    })();
    </script>
    <?php
}, 20);

// ─── Scroll Reveal inline (mai in cache, sempre aggiornato) ─────────────────
// Rivela tutti i [data-reveal] subito e poi anima quelli sotto la fold.

add_action('wp_footer', function (): void {
    ?>
    <script id="alm-reveal">
    /* Rivela subito gli elementi [data-reveal] già nel viewport al caricamento.
       L'IntersectionObserver per il resto è gestito da navigation.js. */
    (function(){
        var winH = window.innerHeight;
        document.querySelectorAll('[data-reveal]').forEach(function(el){
            if(el.getBoundingClientRect().top < winH + 80){
                el.classList.add('is-visible');
            }
        });
    })();
    </script>
    <?php
}, 5);

// ─── PWA: Service Worker registration ───────────────────────────────────────

add_action('wp_footer', function (): void {
    ?>
    <script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js', { scope: '/' })
            .catch(function(err) { console.warn('SW registration failed:', err); });
    }
    </script>
    <?php
}, 99);

// ─── Preconnect / DNS-prefetch (Core Web Vitals) ────────────────────────────

add_filter('wp_resource_hints', function (array $hints, string $relation_type): array {
    if ($relation_type === 'preconnect') {
        $hints[] = ['href' => 'https://fonts.googleapis.com'];
        $hints[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous'];
        $hints[] = ['href' => 'https://cdn.jsdelivr.net'];
    }
    if ($relation_type === 'dns-prefetch') {
        $hints[] = ['href' => 'https://fonts.googleapis.com'];
        $hints[] = ['href' => 'https://fonts.gstatic.com'];
        $hints[] = ['href' => 'https://cdn.jsdelivr.net'];
        $hints[] = ['href' => 'https://js.stripe.com'];
    }
    return $hints;
}, 10, 2);

// ─── Include files ───────────────────────────────────────────────────────────

$alm_includes = [
    'inc/i18n.php',              // Sistema multilingua (deve essere il primo)
    'inc/destinazioni.php',      // Contenuti pagine destinazione Sicilia
    'inc/dest-admin.php',        // Metabox foto destinazione
    'inc/custom-post-types.php',
    'inc/helpers.php',
    'inc/shortcodes.php',
    'inc/schema-markup.php',
    'inc/seo-meta.php',
    'inc/seo-admin.php',
    'inc/seo-settings.php',     // Settings page GA4 + Search Console
    'inc/sitemap.php',          // Sitemap XML custom con hreflang
    'inc/sample-data.php',
    'inc/room-translations.php',
    'inc/setup-pages.php',
    'inc/customizer.php',
    'inc/admin-dashboard.php',  // Dashboard widget + menu rapido
];

foreach ($alm_includes as $file) {
    $path = get_stylesheet_directory() . '/' . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

// ─── Force Beige & White Palette Overrides ───────────────────────────────────

add_action('wp_head', function (): void {
    ?>
    <style id="almaretna-force-palette-overrides">
    :root {
        --color-primary: #C5B49C !important;
        --color-secondary: #A6957E !important;
        --color-accent: #F7F4ED !important;
        --color-bg: #FCFAF7 !important;
        --color-text: #5C5246 !important;
        --color-text-light: #8C8070 !important;
        --color-border: #EFEBE4 !important;
        --color-white: #FFFFFF !important;
    }

    /* Force text-primary override to avoid Bootstrap blue */
    .text-primary,
    a.text-primary,
    span.text-primary,
    div.text-primary,
    h1.text-primary,
    h2.text-primary,
    h3.text-primary,
    h4.text-primary {
        color: var(--color-primary) !important;
    }

    /* Primary and Accent Button Overrides */
    .btn-primary,
    .button,
    .btn[type="submit"],
    .booking-bar__submit,
    .room-card-home__btn,
    .villa-section__cta,
    .cta-banner__btn,
    .room-card__cta,
    .btn-outline-primary:hover,
    #alm-search-rooms,
    #alm-to-payment,
    #alm-submit-payment,
    .room-result-card__footer button,
    .room-result-card__footer .btn {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
        color: var(--color-white) !important;
        box-shadow: none !important;
    }

    .btn-primary:hover,
    .button:hover,
    .btn[type="submit"]:hover,
    .booking-bar__submit:hover,
    .room-card-home__btn:hover,
    .villa-section__cta:hover,
    .cta-banner__btn:hover,
    .room-card__cta:hover,
    #alm-search-rooms:hover,
    #alm-to-payment:hover,
    #alm-submit-payment:hover,
    .room-result-card__footer button:hover,
    .room-result-card__footer .btn:hover {
        background-color: var(--color-secondary) !important;
        border-color: var(--color-secondary) !important;
        color: var(--color-white) !important;
        box-shadow: none !important;
    }

    /* Header and Menu Buttons */
    .site-header__book-btn,
    .mobile-nav__cta {
        background-color: var(--color-primary) !important;
        color: var(--color-white) !important;
        border: none !important;
    }
    .site-header__book-btn:hover,
    .mobile-nav__cta:hover {
        background-color: var(--color-secondary) !important;
        color: var(--color-white) !important;
    }

    /* Active/Hover Menu item backgrounds and default colors */
    .site-nav__list li a,
    .site-header--home:not(.is-scrolled) .site-nav__list li a {
        color: var(--color-text) !important;
        background: transparent;
    }

    .site-nav__list li a:hover,
    .site-nav__list li.current-menu-item > a,
    .site-header--home:not(.is-scrolled) .site-nav__list li a:hover,
    .site-header--home:not(.is-scrolled) .site-nav__list li.current-menu-item > a,
    .mobile-nav__list li a:hover,
    .mobile-nav__list li.current-menu-item > a {
        color: var(--color-primary) !important;
        background: rgba(197, 180, 156, 0.12) !important;
    }

    /* CTA Menu button in header (Prenota) */
    .site-nav__list li a.nav-cta,
    .site-header--home:not(.is-scrolled) .site-nav__list li a.nav-cta {
        background-color: var(--color-primary) !important;
        color: var(--color-white) !important;
        font-weight: 600 !important;
        border-radius: var(--radius-md) !important;
        padding: 8px 16px !important;
        transition: all var(--transition-base) !important;
    }
    .site-nav__list li a.nav-cta:hover,
    .site-header--home:not(.is-scrolled) .site-nav__list li a.nav-cta:hover,
    .site-nav__list li.current-menu-item > a.nav-cta,
    .site-header--home:not(.is-scrolled) .site-nav__list li.current-menu-item > a.nav-cta {
        background-color: var(--color-secondary) !important;
        color: var(--color-white) !important;
    }

    /* Booking Steps Indicator Overrides */
    .booking-step.is-active .booking-step__number {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
        color: var(--color-white) !important;
        box-shadow: 0 0 0 4px rgba(197, 180, 156, 0.25) !important;
    }
    .booking-step.is-active .booking-step__label {
        color: var(--color-primary) !important;
        font-weight: 600 !important;
    }
    .booking-step.is-completed .booking-step__number {
        background-color: var(--color-success) !important;
        border-color: var(--color-success) !important;
        color: var(--color-white) !important;
    }
    .booking-step.is-completed .booking-step__label {
        color: var(--color-success) !important;
    }
    .booking-steps .booking-step-connector.is-completed {
        background-color: var(--color-success) !important;
    }

    /* Page Booking Hero section styling overrides */
    .page-booking .page-hero,
    .page-hero,
    .page-hero--rooms,
    .room-details__hero {
        background-color: var(--color-accent) !important;
        background-image: none !important;
        color: var(--color-text) !important;
        border-bottom: 1px solid var(--color-border) !important;
        padding-top: 120px !important;
    }

    .page-booking .page-hero__title,
    .page-hero__title,
    .room-details__hero-title {
        color: var(--color-text) !important;
    }

    .page-booking .page-hero__subtitle,
    .page-hero__subtitle,
    .room-details__hero-excerpt {
        color: var(--color-text-light) !important;
        opacity: 1 !important;
    }

    .page-booking .page-hero__breadcrumb,
    .page-booking .page-hero__breadcrumb a,
    .page-hero__breadcrumb,
    .page-hero__breadcrumb a {
        color: var(--color-text-light) !important;
    }

    .page-hero__overlay,
    .hero__overlay,
    .room-details__hero::before {
        background: linear-gradient(180deg, rgba(252, 250, 247, 0.45) 0%, rgba(252, 250, 247, 0.78) 100%) !important;
    }

    /* ── Nasconde la voce "Prenota" dal menu di navigazione ──────────
       Il bottone dedicato "Prenota ora" a destra è sufficiente.
       Rimuove ridondanza visiva nel nav principale e mobile.      ── */
    .site-nav__list li a.nav-cta,
    .site-nav__list li.menu-item-prenota > a,
    .site-nav__list li a[href*="prenota"]:not(.site-header__book-btn) {
        display: none !important;
    }
    .mobile-nav__list li a[href*="prenota"] {
        display: none !important;
    }

    /* ── Nascondi completamente il pulsante scroll-to-top di Astra ── */
    /* Usiamo il nostro personalizzato (iniettato via wp_footer)       */
    #ast-scroll-top,
    .ast-scroll-top-icon,
    .ast-scroll-to-top-right,
    [class*="ast-scroll-top"],
    [class*="ast-scroll-to-top"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /* Form control focus states */
    .form-control:focus,
    .booking-widget__field input:focus,
    .booking-widget__field select:focus {
        border-color: var(--color-primary) !important;
        box-shadow: 0 0 0 3px rgba(197, 180, 156, 0.2) !important;
    }

    /* Footer Styling Overrides */
    .site-footer {
        background: #1a1e22 !important;
        color: rgba(255, 255, 255, 0.8) !important;
        border-top: 1px solid var(--color-border) !important;
    }

    .site-footer__heading {
        color: #EFECE6 !important; /* Light cream for headings */
    }

    .site-footer__menu li a,
    .site-footer__address p,
    .site-footer__address a,
    .site-footer__info li {
        color: rgba(255, 255, 255, 0.75) !important;
    }

    .site-footer__menu li a:hover,
    .site-footer__address a:hover {
        color: var(--color-primary) !important;
    }

    .site-footer__bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .site-footer__bottom p {
        color: rgba(255, 255, 255, 0.55) !important;
    }
    </style>
    <?php
}, 999);

// ─── Contact info seeder (prima esecuzione — scrive in DB una volta) ────────

add_action('init', function (): void {
    if (!get_option('alm_schema_facebook_url')) {
        update_option('alm_schema_facebook_url', 'https://www.facebook.com/share/1H8YRGCYaC/');
    }
    if (!get_option('alm_schema_instagram_url')) {
        update_option('alm_schema_instagram_url', 'https://www.instagram.com/almaretna_sicily/');
    }
    $settings = (array) get_option('alm_booking_settings', []);
    $changed  = false;
    if (empty($settings['host_email'])) { $settings['host_email'] = 'info@almaretna.it'; $changed = true; }
    if (empty($settings['host_phone'])) { $settings['host_phone'] = '+393332621974'; $changed = true; }
    if ($changed) update_option('alm_booking_settings', $settings);
}, 1);

// ─── Favicon ────────────────────────────────────────────────────────────────
// Rimuove il placeholder WordPress e inserisce le icone generate dal logo
remove_action('wp_head', 'wp_site_icon', 99);

add_action('wp_head', function (): void {
    $root = trailingslashit(home_url());
    $img  = get_stylesheet_directory_uri() . '/assets/img/';
    echo "\n";
    echo '<link rel="icon" type="image/x-icon"   href="' . esc_url($root . 'favicon.ico') . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32"  href="' . esc_url($root . 'favicon-32x32.png') . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="16x16"  href="' . esc_url($root . 'favicon-16x16.png') . '">' . "\n";
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($root . 'apple-touch-icon.png') . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url($img . 'favicon-192.png') . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url($img . 'favicon-512.png') . '">' . "\n";
    echo '<meta name="theme-color" content="#5C5246">' . "\n";
}, 1);

