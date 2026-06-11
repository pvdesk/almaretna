<?php
/**
 * Template Name: Scopri la Sicilia (Overview)
 *
 * Pagina riepilogativa delle destinazioni e escursioni raggiungibili da Almaretna.
 * Slug della pagina: scopri-la-sicilia
 */
defined('ABSPATH') || exit;

require_once get_stylesheet_directory() . '/inc/i18n.php';
require_once get_stylesheet_directory() . '/inc/destinazioni.php';

$lang = alm_get_lang();
$list = alm_destinazioni_list();

$content = [
    'it' => [
        'eyebrow'   => 'Partendo da Almaretna',
        'title'     => 'Scopri la Sicilia orientale',
        'intro'     => "Almaretna si trova in una posizione privilegiata al centro della costa est della Sicilia, a pochi minuti dalle spiagge di Fondachello e a 20 minuti dall'Etna. Da qui, ogni angolo dell'isola è raggiungibile con facilità. Su richiesta organizziamo escursioni personalizzate verso tutte le mete elencate.",
        'excursion_title' => 'Escursioni su misura',
        'excursion_text'  => "Il nostro staff è a disposizione per organizzare escursioni personalizzate: transfer con guida locale, visite ai siti culturali, trekking sull'Etna estate e inverno, degustazioni di vini DOC Etna, escursioni in barca e molto altro. Contattaci per pianificare il tuo soggiorno ideale.",
        'excursion_cta'   => 'Contatta la struttura',
        'book_title' => 'La tua base in Sicilia',
        'book_text'  => 'Prenota direttamente per il miglior prezzo garantito e un benvenuto speciale.',
        'book_btn'   => 'Prenota il soggiorno',
        'distances_title' => 'Distanze da Almaretna',
    ],
    'en' => [
        'eyebrow'   => 'Starting from Almaretna',
        'title'     => 'Discover eastern Sicily',
        'intro'     => "Almaretna is ideally located at the heart of Sicily's east coast, minutes from Fondachello beaches and 20 minutes from Etna. From here, every corner of the island is easy to reach. On request, we arrange personalised excursions to all the destinations listed.",
        'excursion_title' => 'Tailor-made excursions',
        'excursion_text'  => "Our staff is on hand to organise personalised excursions: transfers with a local guide, cultural site visits, summer and winter Etna trekking, Etna DOC wine tastings, boat trips and much more. Contact us to plan your perfect stay.",
        'excursion_cta'   => 'Contact the property',
        'book_title' => 'Your base in Sicily',
        'book_text'  => 'Book directly for the best price guaranteed and a special welcome.',
        'book_btn'   => 'Book your stay',
        'distances_title' => 'Distances from Almaretna',
    ],
    'de' => [
        'eyebrow'   => 'Von Almaretna aus',
        'title'     => 'Ostsizilien entdecken',
        'intro'     => "Almaretna liegt ideal im Herzen der sizilianischen Ostküste, nur Minuten von den Fondachello-Stränden und 20 Minuten vom Ätna entfernt. Von hier aus ist jeder Winkel der Insel leicht erreichbar. Auf Anfrage organisieren wir individuelle Ausflüge zu allen aufgeführten Zielen.",
        'excursion_title' => 'Maßgeschneiderte Ausflüge',
        'excursion_text'  => "Unser Team steht bereit, um individuelle Ausflüge zu organisieren: Transfers mit lokalem Reiseleiter, Kulturbesichtigungen, Sommer- und Winter-Ätna-Trekking, Etna DOC Weinverkostungen, Bootstouren und vieles mehr. Kontaktieren Sie uns für Ihren perfekten Aufenthalt.",
        'excursion_cta'   => 'Kontakt zur Unterkunft',
        'book_title' => 'Ihre Basis in Sizilien',
        'book_text'  => 'Direkt buchen für den besten Preis und einen besonderen Empfang.',
        'book_btn'   => 'Aufenthalt buchen',
        'distances_title' => 'Entfernungen von Almaretna',
    ],
    'fr' => [
        'eyebrow'   => 'Au départ d\'Almaretna',
        'title'     => 'Découvrir la Sicile orientale',
        'intro'     => "Almaretna est idéalement situé au cœur de la côte est de la Sicile, à quelques minutes des plages de Fondachello et à 20 minutes de l'Etna. D'ici, chaque recoin de l'île est facile à atteindre. Sur demande, nous organisons des excursions personnalisées vers toutes les destinations listées.",
        'excursion_title' => 'Excursions sur mesure',
        'excursion_text'  => "Notre équipe est à disposition pour organiser des excursions personnalisées : transferts avec guide local, visites culturelles, trekking sur l'Etna été et hiver, dégustations de vins DOC Etna, sorties en bateau et bien plus. Contactez-nous pour planifier votre séjour idéal.",
        'excursion_cta'   => 'Contacter l\'hébergement',
        'book_title' => 'Votre base en Sicile',
        'book_text'  => 'Réservez directement pour le meilleur prix garanti et un accueil spécial.',
        'book_btn'   => 'Réserver le séjour',
        'distances_title' => 'Distances depuis Almaretna',
    ],
    'es' => [
        'eyebrow'   => 'Desde Almaretna',
        'title'     => 'Descubrir Sicilia oriental',
        'intro'     => "Almaretna está idealmente situado en el corazón de la costa este de Sicilia, a minutos de las playas de Fondachello y a 20 minutos del Etna. Desde aquí, cada rincón de la isla es fácilmente accesible. A petición, organizamos excursiones personalizadas a todos los destinos listados.",
        'excursion_title' => 'Excursiones a medida',
        'excursion_text'  => "Nuestro personal está disponible para organizar excursiones personalizadas: traslados con guía local, visitas a sitios culturales, trekking en el Etna en verano e invierno, catas de vinos DOC Etna, excursiones en barco y mucho más. Contáctenos para planificar su estancia perfecta.",
        'excursion_cta'   => 'Contactar el alojamiento',
        'book_title' => 'Tu base en Sicilia',
        'book_text'  => 'Reserva directamente para el mejor precio garantizado y una bienvenida especial.',
        'book_btn'   => 'Reservar la estancia',
        'distances_title' => 'Distancias desde Almaretna',
    ],
];

$c = $content[$lang] ?? $content['it'];

/* ── SEO ─────────────────────────────────────────────────────────────── */
$seo_overview = [
    'it' => ['title' => 'Scopri la Sicilia Orientale | Almaretna – Villa Sicilia',    'desc' => 'Taormina, Etna, Catania, Siracusa e le spiagge ioniche a portata di mano. Escursioni personalizzate su richiesta da Almaretna.'],
    'en' => ['title' => 'Discover Eastern Sicily | Almaretna – Villa Sicily',         'desc' => 'Taormina, Etna, Catania, Syracuse and Ionian beaches all within easy reach. Personalised excursions on request from Almaretna.'],
    'de' => ['title' => 'Ostsizilien entdecken | Almaretna – Villa Sizilien',         'desc' => 'Taormina, Ätna, Catania, Syrakus und ionische Strände ganz in der Nähe. Individuelle Ausflüge auf Anfrage von Almaretna aus.'],
    'fr' => ['title' => 'Découvrir la Sicile Orientale | Almaretna – Villa Sicile',  'desc' => 'Taormine, Etna, Catane, Syracuse et plages ioniennes à portée de main. Excursions personnalisées sur demande depuis Almaretna.'],
    'es' => ['title' => 'Descubrir Sicilia Oriental | Almaretna – Villa Sicilia',    'desc' => 'Taormina, Etna, Catania, Siracusa y playas jónicas al alcance de la mano. Excursiones personalizadas a petición desde Almaretna.'],
];
$s_seo = $seo_overview[$lang] ?? $seo_overview['it'];
add_filter('pre_get_document_title', function() use ($s_seo) { return $s_seo['title']; }, 99);
add_action('wp_head', function() use ($s_seo) {
    $desc = esc_attr($s_seo['desc']);
    $t    = esc_attr($s_seo['title']);
    echo '<meta name="description" content="'.$desc.'" />'."\n";
    echo '<meta property="og:title" content="'.$t.'" />'."\n";
    echo '<meta property="og:description" content="'.$desc.'" />'."\n";
    echo '<meta property="og:type" content="website" />'."\n";
    $jsonld = ['@context'=>'https://schema.org','@type'=>'TouristDestination','name'=>'Sicilia Orientale','description'=>$s_seo['desc'],'containedInPlace'=>['@type'=>'Country','name'=>'Italy']];
    echo '<script type="application/ld+json">'.json_encode($jsonld, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES).'</script>'."\n";
}, 2);

$book_url = home_url('/prenota/');
$contact_page = get_page_by_path('le-mie-prenotazioni');
$contact_url  = $contact_page ? get_permalink($contact_page) : home_url('/le-mie-prenotazioni/');

get_header();
?>
<style>
.scopri-page{color:#3D3530}
.scopri-hero{background:linear-gradient(135deg,#1a4e6b 0%,#1f5e3e 100%);color:#fff;padding:96px 24px 80px;text-align:center}
.scopri-hero__eyebrow{font-size:.72rem;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:20px}
.scopri-hero__title{font-size:clamp(2.2rem,5.5vw,3.6rem);font-weight:300;margin:0 0 24px;line-height:1.15}
.scopri-hero__intro{font-size:1.1rem;color:rgba(255,255,255,.82);max-width:640px;margin:0 auto;line-height:1.8}
.scopri-body{max-width:960px;margin:0 auto;padding:72px 24px 80px}
.scopri-section-title{font-size:.7rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#C5B49C;margin-bottom:32px;text-align:center}
.scopri-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:72px}
.scopri-card{background:#fff;border:1px solid #e8e2dc;border-radius:14px;padding:28px;text-decoration:none;color:#3D3530;transition:border-color .2s,transform .2s,box-shadow .2s;display:flex;flex-direction:column;gap:10px}
.scopri-card:hover{border-color:#C5B49C;transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.07)}
.scopri-card__icon{font-size:2.2rem}
.scopri-card__title{font-size:1.15rem;font-weight:600;margin:0}
.scopri-card__subtitle{font-size:.9rem;color:#8a7a72;margin:0;line-height:1.5}
.scopri-card__distance{font-size:.8rem;font-weight:700;color:#1a4e6b;margin-top:auto;padding-top:12px;border-top:1px solid #f0ebe6}
.scopri-excursion{background:linear-gradient(135deg,#fdf9f5,#f5ede3);border:1px solid #ddd0c4;border-radius:16px;padding:48px;margin-bottom:56px;text-align:center}
.scopri-excursion h2{font-size:1.6rem;font-weight:300;color:#3D3530;margin:0 0 16px}
.scopri-excursion p{color:#6B6058;max-width:540px;margin:0 auto 28px;line-height:1.8;font-size:.97rem}
.scopri-excursion__btn{display:inline-block;background:#1a4e6b;color:#fff;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:600;font-size:.9rem;transition:background .2s}
.scopri-excursion__btn:hover{background:#133b52}
.scopri-book{text-align:center;padding:56px 32px;background:#1a4e6b;border-radius:16px;color:#fff}
.scopri-book h2{font-size:1.5rem;font-weight:300;margin:0 0 10px}
.scopri-book p{color:rgba(255,255,255,.75);margin:0 0 28px;font-size:.95rem}
.scopri-book__btn{display:inline-block;background:#fff;color:#1a4e6b;padding:14px 32px;border-radius:50px;text-decoration:none;font-weight:700;font-size:.95rem;transition:background .15s,transform .15s}
.scopri-book__btn:hover{background:#f0f8ff;transform:translateY(-1px)}
</style>

<main class="scopri-page">
    <div class="scopri-hero">
        <div class="scopri-hero__eyebrow"><?php echo esc_html($c['eyebrow']); ?></div>
        <h1 class="scopri-hero__title"><?php echo esc_html($c['title']); ?></h1>
        <p class="scopri-hero__intro"><?php echo esc_html($c['intro']); ?></p>
    </div>

    <div class="scopri-body">

        <?php
        $dest_title_labels = [
            'it' => 'Le destinazioni', 'en' => 'Destinations',
            'de' => 'Reiseziele', 'fr' => 'Destinations', 'es' => 'Destinos',
        ];
        ?>
        <div class="scopri-section-title"><?php echo esc_html($dest_title_labels[$lang] ?? 'Le destinazioni'); ?></div>
        <div class="scopri-grid">
            <?php foreach ($list as $item) :
                $page = get_page_by_path($item['slug']);
                $url  = $page ? get_permalink($page) : home_url('/' . $item['slug'] . '/');
                if ($lang !== 'it') $url = add_query_arg('lang', $lang, $url);
            ?>
                <a href="<?php echo esc_url($url); ?>" class="scopri-card">
                    <span class="scopri-card__icon"><?php echo esc_html($item['icon']); ?></span>
                    <h2 class="scopri-card__title"><?php echo esc_html($item['title']); ?></h2>
                    <p class="scopri-card__subtitle"><?php echo esc_html($item['subtitle']); ?></p>
                    <div class="scopri-card__distance">📍 <?php echo esc_html($item['distance']); ?></div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="scopri-excursion">
            <h2><?php echo esc_html($c['excursion_title']); ?></h2>
            <p><?php echo esc_html($c['excursion_text']); ?></p>
            <a href="<?php echo esc_url($contact_url); ?>" class="scopri-excursion__btn">
                <?php echo esc_html($c['excursion_cta']); ?>
            </a>
        </div>

        <div class="scopri-book">
            <h2><?php echo esc_html($c['book_title']); ?></h2>
            <p><?php echo esc_html($c['book_text']); ?></p>
            <a href="<?php echo esc_url($book_url); ?>" class="scopri-book__btn">
                <?php echo esc_html($c['book_btn']); ?>
            </a>
        </div>

    </div>
</main>

<?php get_footer(); ?>
