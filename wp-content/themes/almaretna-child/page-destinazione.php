<?php
/**
 * Template Name: Destinazione Sicilia
 *
 * Template per le pagine destinazione: Taormina, Etna, Catania, Siracusa, Messina, Mare di Sicilia.
 * Assegnare questo template alla pagina corrispondente dall'editor WP.
 */
defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/i18n.php';
require_once get_stylesheet_directory() . '/inc/destinazioni.php';

$slug   = get_post_field('post_name', get_the_ID());
$d      = alm_destinazione_data($slug);
$lang   = alm_get_lang();
$list   = alm_destinazioni_list();
$site   = home_url('/');
$label_back = [
    'it' => '← Scopri la Sicilia',
    'en' => '← Discover Sicily',
    'de' => '← Sizilien entdecken',
    'fr' => '← Découvrir la Sicile',
    'es' => '← Descubrir Sicilia',
][$lang] ?? '← Scopri la Sicilia';
$label_summer = [
    'it' => 'Estate',  'en' => 'Summer',  'de' => 'Sommer', 'fr' => 'Été', 'es' => 'Verano',
][$lang] ?? 'Estate';
$label_winter = [
    'it' => 'Inverno', 'en' => 'Winter',  'de' => 'Winter', 'fr' => 'Hiver', 'es' => 'Invierno',
][$lang] ?? 'Inverno';
$label_dist = [
    'it' => 'Distanza da Almaretna', 'en' => 'Distance from Almaretna',
    'de' => 'Entfernung von Almaretna', 'fr' => 'Distance d\'Almaretna', 'es' => 'Distancia desde Almaretna',
][$lang] ?? 'Distanza da Almaretna';
$label_book = [
    'it' => 'Prenota il tuo soggiorno', 'en' => 'Book your stay',
    'de' => 'Ihren Aufenthalt buchen', 'fr' => 'Réserver votre séjour', 'es' => 'Reserva tu estancia',
][$lang] ?? 'Prenota il tuo soggiorno';
$label_attractions = [
    'it' => 'Cosa vedere e fare', 'en' => 'What to see and do',
    'de' => 'Was zu sehen und zu tun', 'fr' => 'Que voir et faire', 'es' => 'Qué ver y hacer',
][$lang] ?? 'Cosa vedere e fare';
$label_year_round = [
    'it' => 'Tutto l\'anno', 'en' => 'Year-round',
    'de' => 'Das ganze Jahr', 'fr' => 'Toute l\'année', 'es' => 'Todo el año',
][$lang] ?? 'Tutto l\'anno';

/* ── Foto (post meta) ─────────────────────────────────────────────────── */
$post_id  = get_the_ID();
$hero_id  = (int) get_post_meta($post_id, '_alm_dest_hero', true);
$hero_url = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : '';

$gall_raw  = (string) get_post_meta($post_id, '_alm_dest_gallery', true);
$gall_ids  = array_filter(array_map('intval', explode(',', $gall_raw)));
$gall_imgs = [];
foreach ($gall_ids as $gid) {
    $url = wp_get_attachment_image_url($gid, 'large');
    if ($url) $gall_imgs[] = [
        'url'  => $url,
        'full' => wp_get_attachment_image_url($gid, 'full'),
        'alt'  => (string) get_post_meta($gid, '_wp_attachment_image_alt', true),
    ];
}

/* ── Sezioni contenuto ───────────────────────────────────────────────── */
$sec_ids = [
    (int) get_post_meta($post_id, '_alm_dest_sec1', true),
    (int) get_post_meta($post_id, '_alm_dest_sec2', true),
    (int) get_post_meta($post_id, '_alm_dest_sec3', true),
];
$sections = $d['sections'] ?? [];

/* ── SEO ─────────────────────────────────────────────────────────────── */
$seo = alm_destinazione_seo($slug);
if (!empty($seo)) {
    add_filter('pre_get_document_title', function() use ($seo) {
        return $seo['title'] ?? '';
    }, 99);
    add_action('wp_head', function() use ($seo, $hero_url, $post_id) {
        $desc   = esc_attr($seo['desc'] ?? '');
        $t      = esc_attr($seo['title'] ?? '');
        $og_img = $hero_url;
        echo '<meta name="description" content="'.$desc.'" />'."\n";
        echo '<meta property="og:title" content="'.$t.'" />'."\n";
        echo '<meta property="og:description" content="'.$desc.'" />'."\n";
        echo '<meta property="og:type" content="website" />'."\n";
        echo '<meta property="og:url" content="'.esc_url(get_permalink($post_id)).'" />'."\n";
        if ($og_img) echo '<meta property="og:image" content="'.esc_url($og_img).'" />'."\n";
        echo '<meta name="twitter:card" content="'.($og_img ? 'summary_large_image' : 'summary').'" />'."\n";
        echo '<meta name="twitter:title" content="'.$t.'" />'."\n";
        echo '<meta name="twitter:description" content="'.$desc.'" />'."\n";
        if ($og_img) echo '<meta name="twitter:image" content="'.esc_url($og_img).'" />'."\n";
        if (!empty($seo['geo'])) {
            $jsonld = [
                '@context'         => 'https://schema.org',
                '@type'            => $seo['geo']['type'] ?? 'TouristAttraction',
                'name'             => $seo['title'] ?? '',
                'description'      => $seo['desc'] ?? '',
                'url'              => get_permalink($post_id),
                'geo'              => ['@type' => 'GeoCoordinates', 'latitude' => $seo['geo']['lat'], 'longitude' => $seo['geo']['lon']],
                'containedInPlace' => ['@type' => 'Country', 'name' => 'Italy'],
            ];
            if ($og_img) $jsonld['image'] = $og_img;
            echo '<script type="application/ld+json">'.json_encode($jsonld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES).'</script>'."\n";
        }
    }, 2);
}

get_header();
?>
<style>
.dest-page{color:#3D3530;font-family:inherit}
.dest-hero{position:relative;background:linear-gradient(135deg,#4A3E36 0%,#3D3530 60%,#2A211D 100%);color:#fff;padding:96px 24px 72px;text-align:center;overflow:hidden}
.dest-hero::before{content:'';position:absolute;inset:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="60" fill="rgba(255,255,255,.04)"/><circle cx="80" cy="80" r="40" fill="rgba(255,255,255,.03)"/></svg>') center/cover;pointer-events:none}
.dest-hero__back{display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.75);font-size:.85rem;text-decoration:none;margin-bottom:32px;letter-spacing:.5px;transition:color .2s}
.dest-hero__back:hover{color:#fff}
.dest-hero__eyebrow{font-size:.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:16px}
.dest-hero__title{font-size:clamp(2.4rem,6vw,4rem);font-weight:300;margin:0 0 16px;line-height:1.1}
.dest-hero__tagline{font-size:1.2rem;color:rgba(255,255,255,.8);margin:0;font-style:italic}
.dest-body{max-width:820px;margin:0 auto;padding:64px 24px 80px}
.dest-intro{font-size:1.15rem;line-height:1.9;color:#5a4f49;margin-bottom:56px;padding-bottom:40px;border-bottom:1px solid #e8e2dc}
.dest-section-title{font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#C5B49C;margin-bottom:28px}
.dest-attractions{display:flex;flex-direction:column;gap:32px;margin-bottom:56px}
.dest-attraction{display:grid;grid-template-columns:3px 1fr;gap:0 20px;align-items:start}
.dest-attraction__bar{width:3px;background:linear-gradient(180deg,#C5B49C,#e8d9c8);border-radius:2px;height:100%}
.dest-attraction__content{}
.dest-attraction h3{font-size:1.05rem;font-weight:600;color:#3D3530;margin:0 0 8px}
.dest-attraction p{font-size:.97rem;line-height:1.8;color:#6B6058;margin:0}
.dest-seasons{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:56px}
@media(max-width:600px){.dest-seasons{grid-template-columns:1fr}}
.dest-season{background:#f8f5f2;border-radius:12px;padding:24px;border:1px solid #ede8e3}
.dest-season__label{font-size:.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#C5B49C;margin-bottom:10px}
.dest-season__icon{font-size:1.8rem;margin-bottom:8px;display:block}
.dest-season p{font-size:.93rem;line-height:1.75;color:#6B6058;margin:0}
.dest-distance{background:#3D3530;color:#fff;border-radius:12px;padding:24px 28px;margin-bottom:56px;display:flex;align-items:flex-start;gap:16px}
.dest-distance__icon{font-size:2rem;flex-shrink:0}
.dest-distance__label{font-size:.7rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:6px}
.dest-distance__val{font-size:1.6rem;font-weight:300;margin-bottom:4px}
.dest-distance__note{font-size:.85rem;color:rgba(255,255,255,.65)}
.dest-cta{text-align:center;padding:48px;background:linear-gradient(135deg,#fdf9f5,#f5ede3);border-radius:16px;border:1px solid #e8ddd3}
.dest-cta h2{font-size:1.5rem;font-weight:300;color:#3D3530;margin:0 0 8px}
.dest-cta p{color:#8a7a72;margin:0 0 28px;font-size:.95rem}
.dest-cta__btn{display:inline-block;background:#A6957E;color:#fff;padding:14px 32px;border-radius:50px;text-decoration:none;font-size:.95rem;font-weight:600;letter-spacing:.5px;transition:background .2s,transform .15s}
.dest-cta__btn:hover{background:#8C7D6C;transform:translateY(-1px)}
.dest-nav{margin-top:64px;padding-top:40px;border-top:1px solid #e8e2dc}
.dest-nav__title{font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#C5B49C;margin-bottom:20px;text-align:center}
.dest-nav__grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px}
.dest-nav__card{background:#f8f5f2;border:1px solid #ede8e3;border-radius:10px;padding:14px;text-align:center;text-decoration:none;color:#3D3530;transition:border-color .2s,transform .15s;display:flex;flex-direction:column;align-items:center;gap:6px}
.dest-nav__card:hover{border-color:#C5B49C;transform:translateY(-2px)}
.dest-nav__card--current{border-color:#C5B49C;background:#fdf9f5;pointer-events:none}
.dest-nav__icon{font-size:1.6rem}
.dest-nav__name{font-size:.85rem;font-weight:600}
.dest-nav__dist{font-size:.75rem;color:#a09090}
.dest-gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px;margin-bottom:56px}
.dest-gallery__link{display:block;aspect-ratio:4/3;overflow:hidden;border-radius:10px;border:1px solid #e8e2dc}
.dest-gallery__img{width:100%;height:100%;object-fit:cover;transition:transform .3s;display:block}
.dest-gallery__link:hover .dest-gallery__img{transform:scale(1.05)}
@media(max-width:600px){.dest-gallery{grid-template-columns:1fr 1fr}}
.dest-sections{display:flex;flex-direction:column;gap:64px;margin-bottom:64px;padding-top:8px}
.dest-sec{display:grid;grid-template-columns:1fr 400px;gap:48px;align-items:center}
.dest-sec--reverse{grid-template-columns:400px 1fr}
.dest-sec--reverse .dest-sec__text{order:2}
.dest-sec--reverse .dest-sec__img{order:1}
.dest-sec__title{font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:600;color:#3D3530;margin:0 0 16px;line-height:1.3}
.dest-sec__body{font-size:.97rem;line-height:1.9;color:#6B6058;margin:0}
.dest-sec__img{border-radius:16px;overflow:hidden;aspect-ratio:4/3;background:#f0ece6;border:1px solid #e8e2dc;flex-shrink:0}
.dest-sec__img img{width:100%;height:100%;object-fit:cover;display:block}
.dest-sec__img--empty{display:flex;align-items:center;justify-content:center}
.dest-sec__placeholder{font-size:3rem;opacity:.25}
@media(max-width:768px){
    .dest-sec,.dest-sec--reverse{grid-template-columns:1fr}
    .dest-sec--reverse .dest-sec__text,.dest-sec--reverse .dest-sec__img{order:unset}
    .dest-sec__img{max-width:100%}
}
</style>

<main class="dest-page">
    <?php
    $hero_style = '';
    if ($hero_url) {
        $hero_style = ' style="background-image:linear-gradient(rgba(42,33,29,.50),rgba(42,33,29,.65)),url('.esc_url($hero_url).');background-size:cover;background-position:center 35%;"';
    }
    ?>
    <div class="dest-hero"<?php echo $hero_style; ?>>
        <?php
        $scopri_page = get_page_by_path('scopri-la-sicilia');
        $scopri_url  = $scopri_page ? get_permalink($scopri_page) : home_url('/scopri-la-sicilia/');
        if ($lang !== 'it') $scopri_url = add_query_arg('lang', $lang, $scopri_url);
        ?>
        <a href="<?php echo esc_url($scopri_url); ?>" class="dest-hero__back"><?php echo esc_html($label_back); ?></a>
        <div class="dest-hero__eyebrow">Almaretna &nbsp;·&nbsp; Sicilia</div>
        <h1 class="dest-hero__title"><?php echo esc_html($d['title'] ?? get_the_title()); ?></h1>
        <?php if (!empty($d['tagline'])) : ?>
            <p class="dest-hero__tagline"><?php echo esc_html($d['tagline']); ?></p>
        <?php endif; ?>
    </div>

    <div class="dest-body">
        <?php if (!empty($d['intro'])) : ?>
            <p class="dest-intro"><?php echo esc_html($d['intro']); ?></p>
        <?php endif; ?>

        <?php if (!empty($d['attractions'])) : ?>
            <div class="dest-section-title"><?php echo esc_html($label_attractions); ?></div>
            <div class="dest-attractions">
                <?php foreach ($d['attractions'] as [$title, $text]) : ?>
                    <div class="dest-attraction">
                        <div class="dest-attraction__bar"></div>
                        <div class="dest-attraction__content">
                            <h3><?php echo esc_html($title); ?></h3>
                            <p><?php echo esc_html($text); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($gall_imgs)) : ?>
            <div class="dest-gallery">
                <?php foreach ($gall_imgs as $img) : ?>
                    <a href="<?php echo esc_url($img['full']); ?>" class="dest-gallery__link" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url($img['url']); ?>"
                             alt="<?php echo esc_attr($img['alt'] ?: ($d['title'] ?? '')); ?>"
                             class="dest-gallery__img" loading="lazy">
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($sections)) : ?>
            <div class="dest-sections">
                <?php foreach ($sections as $i => $sec) :
                    $img_id  = $sec_ids[$i] ?? 0;
                    $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'large') : '';
                    $reverse = ($i % 2 === 1);
                ?>
                    <div class="dest-sec<?php echo $reverse ? ' dest-sec--reverse' : ''; ?>">
                        <div class="dest-sec__text">
                            <h2 class="dest-sec__title"><?php echo esc_html($sec['title']); ?></h2>
                            <p class="dest-sec__body"><?php echo esc_html($sec['text']); ?></p>
                        </div>
                        <div class="dest-sec__img<?php echo !$img_url ? ' dest-sec__img--empty' : ''; ?>">
                            <?php if ($img_url) : ?>
                                <img src="<?php echo esc_url($img_url); ?>"
                                     alt="<?php echo esc_attr($sec['title']); ?>"
                                     loading="lazy">
                            <?php else : ?>
                                <span class="dest-sec__placeholder">📷</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($d['summer']) || !empty($d['winter'])) : ?>
            <div class="dest-section-title"><?php echo esc_html($label_year_round); ?></div>
            <div class="dest-seasons">
                <?php if (!empty($d['summer'])) : ?>
                    <div class="dest-season">
                        <span class="dest-season__icon">☀️</span>
                        <div class="dest-season__label"><?php echo esc_html($label_summer); ?></div>
                        <p><?php echo esc_html($d['summer']); ?></p>
                    </div>
                <?php endif; ?>
                <?php if (!empty($d['winter'])) : ?>
                    <div class="dest-season">
                        <span class="dest-season__icon">❄️</span>
                        <div class="dest-season__label"><?php echo esc_html($label_winter); ?></div>
                        <p><?php echo esc_html($d['winter']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($d['distance'])) : ?>
            <div class="dest-distance">
                <div class="dest-distance__icon">📍</div>
                <div>
                    <div class="dest-distance__label"><?php echo esc_html($label_dist); ?></div>
                    <div class="dest-distance__val"><?php echo esc_html($d['distance']); ?></div>
                    <?php if (!empty($d['dist_note'])) : ?>
                        <div class="dest-distance__note"><?php echo esc_html($d['dist_note']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="dest-cta">
            <h2><?php echo esc_html($label_book); ?></h2>
            <p><?php
            $cta_texts = [
                'it' => "Almaretna è la base perfetta per esplorare la Sicilia orientale. Prenota direttamente per il miglior prezzo.",
                'en' => "Almaretna is the perfect base for exploring eastern Sicily. Book directly for the best price.",
                'de' => "Almaretna ist die perfekte Basis zur Erkundung Ostsiziliens. Direkt buchen für den besten Preis.",
                'fr' => "Almaretna est la base parfaite pour explorer la Sicile orientale. Réservez en direct pour le meilleur prix.",
                'es' => "Almaretna es la base perfecta para explorar Sicilia oriental. Reserva directamente para el mejor precio.",
            ];
            echo esc_html($cta_texts[$lang] ?? $cta_texts['it']);
            ?></p>
            <a href="<?php echo esc_url(home_url('/prenota/')); ?>" class="dest-cta__btn"><?php echo esc_html($label_book); ?></a>
        </div>

        <?php if (!empty($list)) : ?>
            <nav class="dest-nav">
                <?php
                $other_dest = [
                    'it' => 'Esplora altre destinazioni',
                    'en' => 'Explore other destinations',
                    'de' => 'Andere Ziele erkunden',
                    'fr' => 'Explorer d\'autres destinations',
                    'es' => 'Explorar otros destinos',
                ];
                ?>
                <div class="dest-nav__title"><?php echo esc_html($other_dest[$lang] ?? $other_dest['it']); ?></div>
                <div class="dest-nav__grid">
                    <?php foreach ($list as $item) :
                        $page    = get_page_by_path($item['slug']);
                        $url     = $page ? get_permalink($page) : home_url('/' . $item['slug'] . '/');
                        if ($lang !== 'it') $url = add_query_arg('lang', $lang, $url);
                        $is_current = ($item['slug'] === $slug);
                    ?>
                        <a href="<?php echo esc_url($url); ?>"
                           class="dest-nav__card<?php echo $is_current ? ' dest-nav__card--current' : ''; ?>">
                            <span class="dest-nav__icon"><?php echo esc_html($item['icon']); ?></span>
                            <span class="dest-nav__name"><?php echo esc_html($item['title']); ?></span>
                            <span class="dest-nav__dist"><?php echo esc_html($item['distance']); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </nav>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
