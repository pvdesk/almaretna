<?php
declare(strict_types=1);

/**
 * Almaretna Child — Creazione pagine WordPress
 *
 * Funzione idempotente: verifica lo slug prima di creare.
 * Eseguita automaticamente dopo il setup del tema.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

/**
 * Crea tutte le pagine del sito Almaretna.
 *
 * @return void
 */
function alm_create_pages(): void {
    $pages = alm_get_pages_config();
    foreach ($pages as $page) {
        alm_create_single_page($page);
    }
}

/**
 * Crea una singola pagina se non esiste già per slug.
 *
 * @param array<string, mixed> $page
 * @return int|null  Post ID o null se già esistente.
 */
function alm_create_single_page(array $page): ?int {
    // Idempotenza: controlla per slug
    $existing = get_page_by_path($page['slug']);
    if ($existing instanceof WP_Post) {
        // Aggiorna il template se non è impostato
        if (!empty($page['template'])) {
            $current_template = get_post_meta($existing->ID, '_wp_page_template', true);
            if (empty($current_template) || $current_template === 'default') {
                update_post_meta($existing->ID, '_wp_page_template', $page['template']);
            }
        }
        return null;
    }

    $post_data = [
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => $page['title'],
        'post_name'    => $page['slug'],
        'post_content' => $page['content'],
        'post_excerpt' => $page['excerpt'] ?? '',
    ];

    if (!empty($page['parent_slug'])) {
        $parent = get_page_by_path($page['parent_slug']);
        if ($parent instanceof WP_Post) {
            $post_data['post_parent'] = $parent->ID;
        }
    }

    $post_id = wp_insert_post($post_data, true);

    if (is_wp_error($post_id)) {
        return null;
    }

    // Assegna template pagina
    if (!empty($page['template'])) {
        update_post_meta($post_id, '_wp_page_template', $page['template']);
    }

    // Meta extra
    if (!empty($page['meta'])) {
        foreach ($page['meta'] as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }

    return $post_id;
}

/**
 * Restituisce la configurazione completa di tutte le pagine.
 *
 * @return array<int, array<string, mixed>>
 */
function alm_get_pages_config(): array {
    return [

        // ── Home ─────────────────────────────────────────────────────────────
        [
            'title'   => 'Home',
            'slug'    => 'home',
            'template'=> '',
            'excerpt' => 'Villa con piscina e vista sulla costa jonica da Taormina a Catania. Nunziata di Mascali, Sicilia.',
            'content' => '<!-- HERO: Titolo principale, sottotitolo, CTA -->
<section class="alm-hero section-padding" style="background:linear-gradient(180deg,#FAF6EE 0%,#FCFAF7 100%);color:var(--color-text);text-align:center;padding:7rem 0 5rem;border-bottom:1px solid var(--color-border);">
<div class="container">
<h1 style="font-family:var(--font-heading);font-size:var(--fs-4xl);color:var(--color-text);margin-bottom:1rem;">Dove il mare incontra l\'Etna</h1>
<p style="font-size:var(--fs-lg);color:var(--color-text-light);max-width:600px;margin:0 auto 2rem;">Una villa esclusiva tra Taormina e Catania, con piscina e vista sulla costa jonica. Il tuo rifugio privato in Sicilia.</p>
<a href="/prenota/" class="btn btn-primary btn-lg" style="background-color:var(--color-primary);border-color:var(--color-primary);color:var(--color-white);padding:12px 30px;border-radius:30px;text-decoration:none;font-weight:600;display:inline-block;">Verifica disponibilità</a>
</div>
</section>

<!-- INTRO STRUTTURA -->
<section class="section-padding">
<div class="container" style="max-width:800px;">
<h2 class="section-title text-center">Almaretna</h2>
<div class="divider divider--center"></div>
<p style="font-size:var(--fs-md);line-height:1.9;color:var(--color-text);text-align:center;">
Almaretna è una dimora privata immersa nella campagna di Nunziata di Mascali, alle pendici dell\'Etna. Da qui il panorama abbraccia l\'intera costa jonica: a nord Taormina con i suoi teatri antichi, a sud Catania e il mare aperto. La piscina di 12 metri si affaccia su questo scenario unico. Camere eleganti, aria silenziosa, un luogo dove il tempo rallenta.
</p>
</div>
</section>

<!-- SEZIONE PISCINA -->
<section class="section-padding" style="background:var(--color-white);">
<div class="container">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-2xl);align-items:center;">
<div>
<h2 class="section-title">La piscina</h2>
<div class="divider"></div>
<p style="font-size:var(--fs-md);line-height:1.8;color:var(--color-text);">
12 metri per 5, profondità fino a 3 metri. Acqua limpida, sdraio, silenzio. La piscina è il cuore della villa, il posto dove la giornata inizia tardi e finisce quando le stelle si accendono.
</p>
<p style="font-size:var(--fs-sm);color:var(--color-text-light);margin-top:1rem;">Uso esclusivo per gli ospiti della villa &middot; Profondità max 3 m</p>
</div>
<div style="background:var(--color-border);border-radius:var(--radius-lg);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
<span class="dashicons dashicons-water" style="font-size:64px;width:64px;height:64px;color:var(--color-primary);opacity:.4;"></span>
</div>
</div>
</div>
</section>

<!-- SEZIONE VISTA -->
<section class="section-padding" style="background:var(--color-bg);">
<div class="container" style="text-align:center;">
<h2 class="section-title">180° di Sicilia</h2>
<div class="divider divider--center"></div>
<p style="font-size:var(--fs-md);line-height:1.8;max-width:700px;margin:0 auto 2rem;">
Dalla terrazza e dalle camere si vede tutto: la sagoma dell\'Etna al mattino con le sue nuvole, la linea blu del mare, le luci di Taormina la sera. Una vista che non stanca mai.
</p>
</div>
</section>

<!-- SEZIONE CAMERE PREVIEW -->
<section class="section-padding" style="background:var(--color-white);">
<div class="container">
<h2 class="section-title text-center">Le camere</h2>
<p class="section-subtitle text-center" style="margin-inline:auto;">Sette camere di carattere, ognuna con la sua luce.</p>
[alm_rooms_grid columns="3"]
<div style="text-align:center;margin-top:2rem;">
<a href="/camere/" class="btn btn-outline btn-lg">Vedi tutte le camere</a>
</div>
</div>
</section>

<!-- DOVE SIAMO -->
<section class="section-padding" style="background:#FAF6EE;color:var(--color-text);border-top:1px solid var(--color-border);border-bottom:1px solid var(--color-border);">
<div class="container">
<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-2xl);align-items:center;">
<div>
<h2 style="font-family:var(--font-heading);font-size:var(--fs-2xl);color:var(--color-text);margin-bottom:1rem;">Nunziata di Mascali</h2>
<div style="width:60px;height:3px;background:var(--color-primary);margin-bottom:1.5rem;"></div>
<p style="color:var(--color-text-light);line-height:1.8;font-size:var(--fs-md);">
A 20 minuti da Taormina, 30 da Catania e dal suo aeroporto, 5 minuti dal mare. Una posizione perfetta per esplorare la Sicilia orientale senza rinunciare alla quiete di una villa privata.
</p>
<ul style="margin-top:1.5rem;list-style:none;display:flex;flex-direction:column;gap:.75rem;padding-left:0;">
<li style="display:flex;gap:.75rem;align-items:center;color:var(--color-text);">
<span class="dashicons dashicons-location" style="color:var(--color-primary);"></span>
Taormina — 20 min
</li>
<li style="display:flex;gap:.75rem;align-items:center;color:var(--color-text);">
<span class="dashicons dashicons-location" style="color:var(--color-primary);"></span>
Catania &amp; Aeroporto — 30 min
</li>
<li style="display:flex;gap:.75rem;align-items:center;color:var(--color-text);">
<span class="dashicons dashicons-location" style="color:var(--color-primary);"></span>
Mare — 5 min
</li>
<li style="display:flex;gap:.75rem;align-items:center;color:var(--color-text);">
<span class="dashicons dashicons-location" style="color:var(--color-primary);"></span>
Etna — 40 min
</li>
</ul>
</div>
<div>
<a href="/dove-siamo/" class="btn btn-outline-primary btn-lg" style="display:block;text-align:center;border:1px solid var(--color-primary);color:var(--color-primary);padding:12px 24px;border-radius:30px;text-decoration:none;font-weight:600;background-color:transparent;">Come raggiungerci</a>
</div>
</div>
</div>
</section>',
        ],

        // ── Camere ───────────────────────────────────────────────────────────
        [
            'title'    => 'Camere',
            'slug'     => 'camere',
            'template' => 'templates/template-rooms.php',
            'excerpt'  => 'Sette camere distribuite su tre livelli, con vista sul mare o sull\'Etna.',
            'content'  => '<p style="font-size:var(--fs-md);line-height:1.8;max-width:700px;margin:0 auto 2rem;text-align:center;">Sette camere distribuite su tre livelli. Alcune con bagno privato e vista sul mare, altre organizzate in suite per famiglie o gruppi. Tutte con aria condizionata, wifi e quella luce particolare che solo la Sicilia sa dare.</p>',
        ],

        // ── Prenota ───────────────────────────────────────────────────────────
        [
            'title'    => 'Prenota il tuo soggiorno',
            'slug'     => 'prenota',
            'template' => 'templates/template-booking.php',
            'excerpt'  => 'Verifica la disponibilità e prenota direttamente. Check-in dalle 15:00 — Check-out entro le 11:00.',
            'content'  => '[alm_booking_form]',
        ],

        // ── Struttura & Servizi ───────────────────────────────────────────────
        [
            'title'    => 'La villa e i servizi',
            'slug'     => 'struttura',
            'template' => '',
            'excerpt'  => 'Piscina 12×5 m, parcheggio privato, posizione esclusiva a Nunziata di Mascali.',
            'content'  => '<section class="section-padding">
<div class="container" style="max-width:800px;">

<h1 class="section-title">La villa e i servizi</h1>
<div class="divider"></div>

<!-- Piscina -->
<div style="margin-bottom:3rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;color:var(--color-primary);">
<span class="dashicons dashicons-water" style="margin-right:.5rem;"></span>La piscina
</h2>
<p style="line-height:1.8;">12 × 5 metri, profondità massima 3 m. La piscina è ad uso esclusivo degli ospiti della villa. Sdraio e lettini disponibili senza prenotazione. Aperta dall\'alba al tramonto.</p>
</div>

<!-- Parcheggio -->
<div style="margin-bottom:3rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;color:var(--color-primary);">
<span class="dashicons dashicons-car" style="margin-right:.5rem;"></span>Parcheggio
</h2>
<p style="line-height:1.8;">Parcheggio privato e gratuito all\'interno della proprietà per tutti gli ospiti. Spazio sufficiente per più veicoli.</p>
</div>

<!-- Posizione -->
<div style="margin-bottom:3rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;color:var(--color-primary);">
<span class="dashicons dashicons-location" style="margin-right:.5rem;"></span>Posizione
</h2>
<p style="line-height:1.8;">Via Scorciavacca Montarsi 48, Nunziata di Mascali (CT). A 20 minuti da Taormina, 30 minuti dall\'aeroporto di Catania, 5 minuti dal mare.</p>
</div>

<!-- Regole -->
<div style="margin-bottom:3rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;color:var(--color-primary);">
<span class="dashicons dashicons-info" style="margin-right:.5rem;"></span>Regole della struttura
</h2>
<ul style="line-height:1.9;padding-left:1.5rem;">
<li>Check-in dalle ore <strong>15:00</strong></li>
<li>Check-out entro le ore <strong>11:00</strong></li>
<li>Vietato fumare negli ambienti interni</li>
<li>Rispettare il silenzio dopo le ore 23:00</li>
<li>La struttura non è accessibile ai disabili motori</li>
</ul>
</div>

<!-- Animali -->
<div style="margin-bottom:3rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;color:var(--color-primary);">
<span class="dashicons dashicons-pets" style="margin-right:.5rem;"></span>Animali domestici
</h2>
<p style="line-height:1.8;">Spiacenti, gli animali domestici non sono ammessi nella struttura.</p>
</div>

<div style="text-align:center;margin-top:3rem;">
<a href="/prenota/" class="btn btn-primary btn-lg">Prenota ora</a>
</div>

</div>
</section>',
        ],

        // ── Dove siamo ────────────────────────────────────────────────────────
        [
            'title'    => 'Come raggiungerci',
            'slug'     => 'dove-siamo',
            'template' => '',
            'excerpt'  => 'Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT). A 20 min da Taormina, 30 min da Catania.',
            'content'  => '<section class="section-padding">
<div class="container" style="max-width:800px;">

<h1 class="section-title">Come raggiungerci</h1>
<div class="divider"></div>

<p style="font-size:var(--fs-md);margin-bottom:2rem;"><strong>Indirizzo:</strong><br>
Via Scorciavacca Montarsi, 48<br>
Nunziata di Mascali (CT) — 95016<br>
Sicilia, Italia</p>

<!-- In aereo -->
<div style="margin-bottom:2rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:.75rem;">
<span class="dashicons dashicons-airplane" style="margin-right:.5rem;color:var(--color-primary);"></span>In aereo
</h2>
<p style="line-height:1.8;"><strong>Aeroporto di Catania (CTA) — Fontanarossa</strong><br>
A circa 30 minuti in auto. È l\'aeroporto più comodo per raggiungere la villa.<br>
Dal terminal: segui le indicazioni per l\'A18 direzione Messina, uscita Fiumefreddo, poi segui le indicazioni per Nunziata di Mascali.</p>
</div>

<!-- In treno -->
<div style="margin-bottom:2rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:.75rem;">
<span class="dashicons dashicons-networking" style="margin-right:.5rem;color:var(--color-primary);"></span>In treno
</h2>
<p style="line-height:1.8;"><strong>Stazione di Giarre-Riposto</strong><br>
A circa 10 minuti in auto dalla villa. Collegata con Catania (45 min) e Messina (1h). Su prenotazione è possibile organizzare il trasferimento dalla stazione.</p>
</div>

<!-- In auto -->
<div style="margin-bottom:2rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:.75rem;">
<span class="dashicons dashicons-car" style="margin-right:.5rem;color:var(--color-primary);"></span>In auto
</h2>
<p style="line-height:1.8;"><strong>Autostrada A18 Messina–Catania</strong><br>
Uscita <strong>Fiumefreddo di Sicilia</strong>, poi seguire le indicazioni per Nunziata di Mascali. La villa si trova in posizione panoramica con ampio parcheggio privato gratuito.</p>
</div>

<!-- Distanze chiave -->
<div style="background:var(--color-white);border-radius:var(--radius-lg);padding:2rem;box-shadow:var(--shadow-sm);margin-bottom:2rem;">
<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1.5rem;">Distanze principali</h2>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Taormina</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">20 min in auto</span></div>
</div>
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Catania</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">30 min in auto</span></div>
</div>
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Aeroporto CTA</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">30 min in auto</span></div>
</div>
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Etna (Rifugio Sapienza)</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">40 min in auto</span></div>
</div>
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Mare (Fondachello)</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">5 min in auto</span></div>
</div>
<div style="display:flex;gap:.75rem;align-items:center;">
<span class="dashicons dashicons-location" style="color:var(--color-secondary);flex-shrink:0;"></span>
<div><strong>Siracusa</strong><br><span style="color:var(--color-text-light);font-size:var(--fs-sm);">1h 15 min in auto</span></div>
</div>
</div>
</div>

<!-- Mappa placeholder -->
<div style="background:var(--color-border);border-radius:var(--radius-lg);height:400px;display:flex;align-items:center;justify-content:center;margin-bottom:2rem;">
<p style="color:var(--color-text-light);text-align:center;">
[Inserire qui Google Maps embed]<br>
<small>Impostare in wp-admin dopo la configurazione del sito</small>
</p>
</div>

</div>
</section>',
        ],

        // ── Contatti ─────────────────────────────────────────────────────────
        [
            'title'    => 'Contattaci',
            'slug'     => 'contatti',
            'template' => '',
            'excerpt'  => 'Contatta Almaretna per informazioni e prenotazioni.',
            'content'  => '<section class="section-padding">
<div class="container" style="max-width:700px;">

<h1 class="section-title">Contattaci</h1>
<div class="divider"></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:3rem;">
<div>
<h3 style="font-family:var(--font-heading);font-size:var(--fs-lg);margin-bottom:1rem;">Dove siamo</h3>
<p style="line-height:1.9;color:var(--color-text);">
Via Scorciavacca Montarsi, 48<br>
Nunziata di Mascali (CT)<br>
95016 — Sicilia
</p>
</div>
<div>
<h3 style="font-family:var(--font-heading);font-size:var(--fs-lg);margin-bottom:1rem;">Contatti diretti</h3>
<p style="line-height:2;">
<span class="dashicons dashicons-email-alt" style="color:var(--color-primary);margin-right:.5rem;"></span>
<strong>Email:</strong> <a href="mailto:">[da completare]</a><br>
<span class="dashicons dashicons-phone" style="color:var(--color-primary);margin-right:.5rem;"></span>
<strong>Telefono:</strong> <a href="tel:">[da completare]</a>
</p>
</div>
</div>

<div style="background:var(--color-white);border-radius:var(--radius-lg);padding:2rem;box-shadow:var(--shadow-sm);margin-bottom:2rem;">
<h3 style="font-family:var(--font-heading);font-size:var(--fs-lg);margin-bottom:1rem;">Orari reception</h3>
<p style="line-height:1.9;">
Check-in: <strong>15:00 – 21:00</strong><br>
Check-out: <strong>entro le 11:00</strong><br>
Per arrivi in orari diversi contattarci in anticipo.
</p>
</div>

<!-- Mappa placeholder -->
<div style="background:var(--color-border);border-radius:var(--radius-lg);height:350px;display:flex;align-items:center;justify-content:center;">
<p style="color:var(--color-text-light);text-align:center;">
[Inserire qui Google Maps embed]
</p>
</div>

</div>
</section>',
        ],

        // ── Privacy Policy ────────────────────────────────────────────────────
        [
            'title'    => 'Privacy Policy',
            'slug'     => 'privacy-policy',
            'template' => '',
            'excerpt'  => 'Informativa sul trattamento dei dati personali ai sensi del GDPR.',
            'content'  => '<section class="section-padding">
<div class="container" style="max-width:800px;">

<h1 class="section-title">Privacy Policy</h1>
<p style="color:var(--color-text-light);font-size:var(--fs-sm);margin-bottom:2rem;">Ultimo aggiornamento: ' . date('d/m/Y') . '</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">1. Titolare del trattamento</h2>
<p style="line-height:1.8;margin-bottom:2rem;">
Il titolare del trattamento dei dati personali è il gestore della struttura ricettiva Almaretna, con sede in Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT), 95016 Italia.<br>
Email di contatto: <a href="mailto:">[inserire email titolare]</a>
</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">2. Dati raccolti</h2>
<p style="line-height:1.8;margin-bottom:1rem;">In fase di prenotazione raccogliamo i seguenti dati personali:</p>
<ul style="line-height:1.9;padding-left:1.5rem;margin-bottom:2rem;">
<li>Nome e cognome</li>
<li>Indirizzo email</li>
<li>Numero di telefono</li>
<li>Dati relativi al soggiorno (date, numero ospiti)</li>
<li>Dati di pagamento (gestiti da Stripe, non archiviati sul nostro server)</li>
<li>Eventuali richieste speciali</li>
</ul>
<p style="line-height:1.8;margin-bottom:2rem;">Attraverso i cookie raccogliamo dati tecnici di navigazione (vedi sezione Cookie policy).</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">3. Finalità del trattamento</h2>
<p style="line-height:1.8;margin-bottom:1rem;">I dati vengono trattati per le seguenti finalità:</p>
<ul style="line-height:1.9;padding-left:1.5rem;margin-bottom:2rem;">
<li><strong>Gestione della prenotazione:</strong> elaborazione, conferma e comunicazione del soggiorno</li>
<li><strong>Adempimenti fiscali e contabili:</strong> fatturazione, registrazione ai fini ISTAT</li>
<li><strong>Comunicazioni di servizio:</strong> email di conferma, reminder pre-arrivo</li>
<li><strong>Miglioramento del servizio:</strong> analisi anonima del traffico web</li>
</ul>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">4. Base giuridica</h2>
<p style="line-height:1.8;margin-bottom:2rem;">
Il trattamento è fondato sul contratto (art. 6, par. 1, lett. b GDPR) per la gestione delle prenotazioni, sull\'obbligo legale (lett. c) per gli adempimenti fiscali, e sul legittimo interesse (lett. f) per le comunicazioni di servizio connesse al soggiorno.
</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">5. Conservazione dei dati</h2>
<p style="line-height:1.8;margin-bottom:2rem;">
I dati relativi alle prenotazioni sono conservati per 10 anni ai fini fiscali, come previsto dalla normativa italiana. I dati di contatto non connessi a prenotazioni vengono cancellati entro 12 mesi dall\'ultima interazione.
</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">6. Diritti dell\'interessato</h2>
<p style="line-height:1.8;margin-bottom:1rem;">Hai il diritto di:</p>
<ul style="line-height:1.9;padding-left:1.5rem;margin-bottom:2rem;">
<li>Accedere ai tuoi dati personali (art. 15 GDPR)</li>
<li>Rettificare dati inesatti (art. 16 GDPR)</li>
<li>Richiedere la cancellazione (art. 17 GDPR)</li>
<li>Limitare il trattamento (art. 18 GDPR)</li>
<li>Portabilità dei dati (art. 20 GDPR)</li>
<li>Opporti al trattamento (art. 21 GDPR)</li>
<li>Proporre reclamo al Garante per la protezione dei dati personali (www.garanteprivacy.it)</li>
</ul>
<p style="line-height:1.8;margin-bottom:2rem;">Per esercitare i tuoi diritti scrivi a: <a href="mailto:">[inserire email titolare]</a></p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">7. Cookie policy</h2>
<p style="line-height:1.8;margin-bottom:1rem;">Il sito utilizza i seguenti tipi di cookie:</p>
<ul style="line-height:1.9;padding-left:1.5rem;margin-bottom:2rem;">
<li><strong>Cookie tecnici necessari:</strong> sessione WordPress, preferenze lingua. Non richiedono consenso.</li>
<li><strong>Cookie di terze parti:</strong> Stripe (pagamenti) — vedi <a href="https://stripe.com/privacy" target="_blank" rel="noopener">privacy policy Stripe</a>.</li>
</ul>
<p style="line-height:1.8;">Puoi gestire o disabilitare i cookie nelle impostazioni del tuo browser in qualsiasi momento.</p>

</div>
</section>',
        ],

        // ── Cancellazione ─────────────────────────────────────────────────────
        [
            'title'    => 'Politica di cancellazione',
            'slug'     => 'cancellazione',
            'template' => '',
            'excerpt'  => 'Politica di cancellazione e rimborso di Almaretna.',
            'content'  => '<section class="section-padding">
<div class="container" style="max-width:800px;">

<h1 class="section-title">Politica di cancellazione</h1>
<div class="divider"></div>

<div class="alert alert-warning" style="margin-bottom:2rem;">
<span class="dashicons dashicons-info"></span>
<strong>Nota:</strong> La politica di cancellazione definitiva deve essere definita con il proprietario. Il contenuto qui sotto è un placeholder.
</div>

<p style="line-height:1.8;margin-bottom:2rem;color:var(--color-text-light);">
<em>[Da definire con il proprietario della struttura prima del go live. Di seguito un esempio di struttura tipica per una casa vacanze in Sicilia.]</em>
</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">Condizioni generali</h2>
<ul style="line-height:2;padding-left:1.5rem;margin-bottom:2rem;">
<li><strong>Cancellazione gratuita:</strong> [X] giorni prima del check-in — rimborso completo</li>
<li><strong>Cancellazione parziale:</strong> tra [X] e [Y] giorni prima — rimborso del [%]</li>
<li><strong>Cancellazione tardiva:</strong> meno di [Y] giorni prima — nessun rimborso</li>
<li><strong>No-show:</strong> addebito dell\'intero importo</li>
</ul>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">Come richiedere la cancellazione</h2>
<p style="line-height:1.8;margin-bottom:2rem;">
Per cancellare una prenotazione scrivi a <a href="mailto:">[email struttura]</a> indicando il numero di riferimento prenotazione (formato ALM-XXXX-XXXX).
</p>

<h2 style="font-family:var(--font-heading);font-size:var(--fs-xl);margin-bottom:1rem;">Rimborsi</h2>
<p style="line-height:1.8;margin-bottom:2rem;">
I rimborsi vengono elaborati entro 5-10 giorni lavorativi sul metodo di pagamento originale (carta di credito/debito via Stripe).
</p>

</div>
</section>',
        ],

    ];
}

// ─── Hook: esegui dopo setup tema, una sola volta ─────────────────────────────

add_action('after_setup_theme', function (): void {
    if (get_option('alm_pages_created') !== '1') {
        alm_create_pages();
        update_option('alm_pages_created', '1');
    }
});

// Forza l'aggiornamento grafico delle pagine già presenti nel database con la nuova palette beige/bianco
add_action('init', function (): void {
    if (get_option('alm_pages_updated_beige_v3') !== '1') {
        $pages = alm_get_pages_config();
        foreach ($pages as $p) {
            $existing = get_page_by_path($p['slug']);
            if ($existing instanceof WP_Post) {
                wp_update_post([
                    'ID'           => $existing->ID,
                    'post_content' => $p['content'],
                ]);
            }
        }
        update_option('alm_pages_updated_beige_v3', '1');
    }
}, 20);
