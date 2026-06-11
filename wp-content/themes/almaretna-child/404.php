<?php
defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/i18n.php';

$lang = alm_get_lang();

get_header();
?>
<style>
.page-404 {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 6rem 1.5rem;
    background: var(--color-bg);
    text-align: center;
}
.page-404__inner {
    max-width: 600px;
}
.page-404__code {
    font-family: var(--font-heading);
    font-size: clamp(5rem, 20vw, 9rem);
    font-weight: 700;
    color: var(--color-primary);
    line-height: 1;
    margin: 0 0 .5rem;
}
.page-404__title {
    font-family: var(--font-heading);
    font-size: clamp(1.4rem, 4vw, 2rem);
    color: var(--color-text);
    margin: 0 0 1rem;
}
.page-404__text {
    color: var(--color-text-light);
    font-size: 1.05rem;
    margin: 0 0 2.5rem;
    line-height: 1.7;
}
.page-404__links {
    display: flex;
    flex-wrap: wrap;
    gap: .75rem;
    justify-content: center;
}
.page-404__links a {
    display: inline-block;
    padding: .7rem 1.6rem;
    border-radius: var(--radius-full);
    font-family: var(--font-body);
    font-size: .9rem;
    font-weight: 500;
    text-decoration: none;
    transition: background .2s, color .2s, border-color .2s;
}
.page-404__links a.btn-primary {
    background: var(--color-primary);
    color: #fff;
    border: 1px solid var(--color-primary);
}
.page-404__links a.btn-primary:hover {
    background: var(--color-secondary);
    border-color: var(--color-secondary);
}
.page-404__links a.btn-outline {
    background: transparent;
    color: var(--color-secondary);
    border: 1px solid var(--color-border);
}
.page-404__links a.btn-outline:hover {
    border-color: var(--color-primary);
    color: var(--color-primary);
}
</style>

<main class="page-404">
    <div class="page-404__inner">
        <p class="page-404__code">404</p>
        <h1 class="page-404__title"><?php echo esc_html(alm_t('404.title')); ?></h1>
        <p class="page-404__text"><?php echo esc_html(alm_t('404.text')); ?></p>
        <nav class="page-404__links" aria-label="<?php esc_attr_e('Navigazione rapida', 'almaretna-child'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary"><?php echo esc_html(alm_t('nav.home')); ?></a>
            <a href="<?php echo esc_url(home_url('/camere/')); ?>" class="btn-outline"><?php echo esc_html(alm_t('nav.rooms')); ?></a>
            <a href="<?php echo esc_url(home_url('/scopri-la-sicilia/')); ?>" class="btn-outline"><?php echo esc_html(alm_t('nav.discover')); ?></a>
            <a href="<?php echo esc_url(home_url('/contatti/')); ?>" class="btn-outline"><?php echo esc_html(alm_t('foot.contacts')); ?></a>
        </nav>
    </div>
</main>

<?php
// BreadcrumbList schema
$schema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => 'Home',
            'item'     => home_url('/'),
        ],
        [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => '404',
        ],
    ],
];
echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";

get_footer();
?>
