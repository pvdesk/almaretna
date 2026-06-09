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
            'template' => 'templates/template-legal.php',
            'excerpt'  => 'Informativa sul trattamento dei dati personali ai sensi del GDPR (Reg. UE 2016/679).',
            'content'  => '<p>Ai sensi del Regolamento UE 2016/679 (GDPR) e del D.Lgs. 196/2003 (Codice Privacy), questa informativa descrive come <strong>Almaretna</strong> raccoglie, utilizza e protegge i tuoi dati personali.</p>

<h2>1. Titolare del trattamento</h2>
<p>Gestore della struttura ricettiva <strong>Almaretna</strong><br>
Via Scorciavacca Montarsi, 48 — Nunziata di Mascali (CT), 95016<br>
Email: <a href="mailto:info@almaretna.it">info@almaretna.it</a> &nbsp;|&nbsp; Tel: <a href="tel:+393332621974">+39 333 262 1974</a></p>

<h2>2. Dati raccolti</h2>
<p>In fase di prenotazione raccogliamo:</p>
<ul>
<li>Nome e cognome</li>
<li>Indirizzo email e numero di telefono</li>
<li>Date e dettagli del soggiorno (numero ospiti, richieste speciali)</li>
<li>Dati di pagamento — elaborati da <strong>Stripe</strong>, mai archiviati sui nostri server</li>
</ul>
<p>Tramite cookie tecnici raccogliamo dati anonimi di navigazione: preferenza lingua e sessione. Per i dettagli consulta la <a href="/cookie-policy/">Cookie Policy</a>.</p>

<h2>3. Finalità e base giuridica</h2>
<table>
<thead><tr><th>Finalità</th><th>Base giuridica (GDPR art. 6)</th></tr></thead>
<tbody>
<tr><td>Gestione prenotazione e comunicazioni di soggiorno</td><td>Esecuzione del contratto — lett. b</td></tr>
<tr><td>Adempimenti fiscali e registrazione ISTAT</td><td>Obbligo legale — lett. c</td></tr>
<tr><td>Reminder pre-arrivo e comunicazioni di servizio</td><td>Legittimo interesse — lett. f</td></tr>
<tr><td>Analisi anonima del traffico web</td><td>Legittimo interesse — lett. f</td></tr>
</tbody>
</table>

<h2>4. Conservazione</h2>
<ul>
<li><strong>Dati di prenotazione:</strong> 10 anni per obbligo fiscale (D.P.R. 600/73)</li>
<li><strong>Dati di contatto non legati a prenotazioni:</strong> eliminati entro 12 mesi dall\'ultima interazione</li>
<li><strong>Log di accesso:</strong> 30 giorni</li>
</ul>

<h2>5. Condivisione con terze parti</h2>
<p><strong>Stripe Inc.</strong> elabora i pagamenti online. È certificata PCI-DSS livello 1 e conforme al GDPR. Informativa: <a href="https://stripe.com/privacy" target="_blank" rel="noopener noreferrer">stripe.com/privacy</a>.</p>
<p>Non vendiamo né cediamo i tuoi dati personali a terzi per finalità di marketing.</p>

<h2>6. I tuoi diritti</h2>
<p>Puoi esercitare in qualsiasi momento i seguenti diritti scrivendo a <a href="mailto:info@almaretna.it">info@almaretna.it</a>:</p>
<ul>
<li><strong>Accesso</strong> — conoscere quali dati trattiamo (art. 15)</li>
<li><strong>Rettifica</strong> — correggere dati inesatti (art. 16)</li>
<li><strong>Cancellazione</strong> — "diritto all\'oblio" (art. 17)</li>
<li><strong>Limitazione</strong> — sospendere il trattamento (art. 18)</li>
<li><strong>Portabilità</strong> — ricevere i dati in formato leggibile (art. 20)</li>
<li><strong>Opposizione</strong> — opporti al trattamento basato su legittimo interesse (art. 21)</li>
</ul>
<p>Hai inoltre il diritto di proporre reclamo al <strong>Garante per la protezione dei dati personali</strong>: <a href="https://www.garanteprivacy.it" target="_blank" rel="noopener noreferrer">garanteprivacy.it</a></p>

<h2>7. Cookie</h2>
<p>Per l\'elenco completo dei cookie utilizzati e le istruzioni per disabilitarli consulta la <a href="/cookie-policy/">Cookie Policy</a>.</p>',
        ],

        // ── Cookie Policy ─────────────────────────────────────────────────────
        [
            'title'    => 'Cookie Policy',
            'slug'     => 'cookie-policy',
            'template' => 'templates/template-legal.php',
            'excerpt'  => 'Informativa sui cookie utilizzati dal sito almaretna.it ai sensi del D.Lgs. 69/2012.',
            'content'  => '<p>Questo sito utilizza esclusivamente cookie tecnici necessari al funzionamento. Non utilizziamo cookie di profilazione o di tracciamento pubblicitario.</p>

<h2>1. Cosa sono i cookie</h2>
<p>I cookie sono piccoli file di testo salvati nel browser durante la navigazione. Permettono al sito di ricordare le preferenze dell\'utente e di funzionare correttamente tra una visita e l\'altra.</p>

<h2>2. Cookie tecnici necessari</h2>
<p>Questi cookie sono strettamente necessari al funzionamento del sito e non richiedono consenso (art. 122 co. 1 D.Lgs. 196/2003):</p>
<table>
<thead><tr><th>Nome cookie</th><th>Scopo</th><th>Durata</th></tr></thead>
<tbody>
<tr><td><code>alm_lang</code></td><td>Salva la preferenza lingua selezionata dall\'utente (IT/EN/DE/FR/ES)</td><td>1 anno</td></tr>
<tr><td><code>wordpress_logged_in_*</code></td><td>Sessione autenticata (solo area amministrativa)</td><td>Sessione</td></tr>
<tr><td><code>cookie_notice_accepted</code></td><td>Registra l\'avvenuta visualizzazione del banner cookie</td><td>1 anno</td></tr>
</tbody>
</table>

<h2>3. Cookie di terze parti</h2>
<h3>Stripe (elaborazione pagamenti)</h3>
<p>Durante il processo di pagamento, <strong>Stripe Inc.</strong> deposita cookie tecnici necessari per la sicurezza e il corretto completamento della transazione. Questi cookie sono attivi solo durante il checkout e sono soggetti alla <a href="https://stripe.com/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy di Stripe</a>.</p>
<p>Stripe è certificata PCI-DSS livello 1 e conforme al GDPR.</p>

<h2>4. Cookie NON utilizzati</h2>
<p>Questo sito <strong>non utilizza</strong>:</p>
<ul>
<li>Google Analytics o altri strumenti di analisi comportamentale</li>
<li>Pixel di Facebook, TikTok o altri social</li>
<li>Cookie pubblicitari o di retargeting</li>
<li>Cookie di terze parti per profilazione</li>
</ul>

<h2>5. Come gestire i cookie</h2>
<p>Puoi modificare le impostazioni dei cookie in qualsiasi momento dal tuo browser:</p>
<ul>
<li><a href="https://support.google.com/chrome/answer/95647?hl=it" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
<li><a href="https://support.mozilla.org/it/kb/protezione-antitracciamento-avanzata-firefox-desktop" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
<li><a href="https://support.apple.com/it-it/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Apple Safari</a></li>
<li><a href="https://support.microsoft.com/it-it/windows/eliminare-e-gestire-i-cookie-168dab11-0753-043d-7c16-ede5947fc64d" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
</ul>
<p>La disabilitazione dei cookie tecnici potrebbe compromettere alcune funzionalità del sito (es. selezione lingua, processo di prenotazione).</p>

<h2>6. Contatti</h2>
<p>Per qualsiasi domanda sui cookie o sul trattamento dei dati: <a href="mailto:info@almaretna.it">info@almaretna.it</a>.<br>
Per l\'informativa completa sul trattamento dei dati personali consulta la <a href="/privacy-policy/">Privacy Policy</a>.</p>',
        ],

        // ── Termini e Condizioni ──────────────────────────────────────────────
        [
            'title'    => 'Termini e Condizioni',
            'slug'     => 'termini-condizioni',
            'template' => 'templates/template-legal.php',
            'excerpt'  => 'Termini e condizioni generali di prenotazione e soggiorno presso Almaretna.',
            'content'  => '<p>I presenti Termini e Condizioni regolano il rapporto contrattuale tra il gestore di <strong>Almaretna</strong> (di seguito "la struttura") e l\'ospite che effettua una prenotazione tramite il sito almaretna.it o altri canali autorizzati.</p>

<h2>1. Prenotazione e conferma</h2>
<p>La prenotazione si considera confermata al ricevimento dell\'email di conferma e del pagamento dell\'importo richiesto. La struttura invierà conferma all\'indirizzo email indicato entro 24 ore dalla prenotazione.</p>
<p>Ogni prenotazione è <strong>nominativa e non trasferibile</strong> a terzi senza autorizzazione scritta della struttura.</p>
<p>La struttura si riserva il diritto di rifiutare prenotazioni in caso di informazioni incomplete o errate.</p>

<h2>2. Prezzi e pagamento</h2>
<p>I prezzi indicati sono per camera/notte, IVA inclusa. Il pagamento avviene in modo sicuro tramite <strong>Stripe</strong>; la struttura non archivia i dati della carta di credito.</p>
<p>Può essere richiesta una <strong>caparra confirmatoria</strong> (generalmente il 30% del totale) al momento della prenotazione. Il saldo si intende dovuto al check-in salvo diversa indicazione.</p>

<h2>3. Cancellazione e rimborsi</h2>
<p>Le condizioni di cancellazione dipendono dalla tariffa selezionata e sono indicate in fase di prenotazione:</p>
<ul>
<li><strong>Tariffa flessibile:</strong> cancellazione gratuita entro il termine indicato; oltre tale termine verrà addebitata la prima notte o la caparra versata.</li>
<li><strong>Tariffa non rimborsabile:</strong> nessun rimborso in caso di cancellazione o no-show.</li>
</ul>
<p>I rimborsi sono elaborati entro <strong>5–10 giorni lavorativi</strong> sul metodo di pagamento originale.</p>
<p>Per richiedere una cancellazione scrivere a <a href="mailto:info@almaretna.it">info@almaretna.it</a> indicando il numero di prenotazione (formato ALM-XXXX-XXXX).</p>

<h2>4. Check-in e check-out</h2>
<ul>
<li><strong>Check-in:</strong> dalle ore <strong>15:00</strong> alle ore <strong>21:00</strong></li>
<li><strong>Check-out:</strong> entro le ore <strong>11:00</strong></li>
</ul>
<p>Arrivi oltre le 21:00 o partenze anticipate devono essere comunicati anticipatamente. Check-in tardivi fuori orario sono possibili previo accordo scritto.</p>

<h2>5. Regolamento della struttura</h2>
<ul>
<li>Vietato fumare negli ambienti interni</li>
<li>Rispettare il silenzio dopo le <strong>23:00</strong></li>
<li>Non introdurre animali domestici nella struttura</li>
<li>Segnalare immediatamente danni o malfunzionamenti alla direzione</li>
<li>La piscina è ad uso esclusivo degli ospiti; i minori devono essere sorvegliati da un adulto</li>
<li>La struttura non è accessibile a persone con disabilità motorie gravi</li>
</ul>
<p>La struttura si riserva il diritto di richiedere l\'allontanamento di ospiti che non rispettino il regolamento, <strong>senza rimborso</strong>.</p>

<h2>6. Responsabilità</h2>
<p>La struttura non è responsabile per furto, smarrimento o danni a oggetti personali. Per valori importanti si consiglia di utilizzare la cassaforte in camera.</p>
<p>La struttura declina ogni responsabilità per infortuni causati da inosservanza del regolamento o da comportamenti imprudenti (piscina, aree esterne).</p>

<h2>7. Privacy</h2>
<p>Il trattamento dei dati personali avviene nel rispetto del GDPR. Per i dettagli consulta la <a href="/privacy-policy/">Privacy Policy</a>.</p>

<h2>8. Legge applicabile e foro competente</h2>
<p>I presenti termini sono regolati dalla <strong>legge italiana</strong>. Per qualsiasi controversia non risolvibile in via amichevole, è competente il <strong>Foro di Catania</strong>.</p>

<h2>9. Contatti</h2>
<p><a href="mailto:info@almaretna.it">info@almaretna.it</a> &nbsp;|&nbsp; <a href="tel:+393332621974">+39 333 262 1974</a></p>',
        ],

        // ── Diritto di Recesso ────────────────────────────────────────────────
        [
            'title'    => 'Diritto di Recesso',
            'slug'     => 'diritto-di-recesso',
            'template' => 'templates/template-legal.php',
            'excerpt'  => 'Informativa sul diritto di recesso per i contratti di alloggio turistico ai sensi del D.Lgs. 206/2005.',
            'content'  => '<p>Questa pagina fornisce le informazioni previste dal <strong>D.Lgs. 206/2005 (Codice del Consumo)</strong> e dalla <strong>Direttiva UE 2011/83/UE</strong> in materia di diritto di recesso per i contratti conclusi a distanza.</p>

<h2>1. Contratti di alloggio turistico: esenzione dal recesso standard</h2>
<p>Ai sensi dell\'<strong>art. 47, comma 1, lettera g) del D.Lgs. 206/2005</strong> — che recepisce l\'art. 16, lettera l) della Direttiva 2011/83/UE — i contratti di <strong>fornitura di servizi di alloggio per fini non abitativi</strong> (quali i soggiorni turistici) con <strong>data o periodo di esecuzione specifici</strong> sono <em>esclusi</em> dall\'applicazione del diritto di recesso di 14 giorni previsto per i contratti a distanza.</p>
<p>Questa esclusione si applica alle prenotazioni effettuate su almaretna.it: una volta confermata la prenotazione, non è possibile invocare il diritto di recesso ex art. 52 D.Lgs. 206/2005.</p>

<h2>2. Perché esiste questa esenzione?</h2>
<p>L\'esenzione è prevista dalla legge per tutelare le strutture ricettive che, a fronte di una prenotazione, devono bloccare disponibilità e organizzare risorse per date precise. Consentire recessi illimitati renderebbe impossibile la gestione delle disponibilità.</p>

<h2>3. Cancellazione volontaria</h2>
<p>Sebbene il diritto di recesso legale non si applichi, è sempre possibile <strong>cancellare volontariamente</strong> la prenotazione nel rispetto della politica di cancellazione specifica della tariffa scelta, come indicato nel riepilogo della prenotazione e nei <a href="/termini-condizioni/">Termini e Condizioni</a>.</p>
<table>
<thead><tr><th>Tariffa</th><th>Possibilità di cancellazione</th><th>Rimborso</th></tr></thead>
<tbody>
<tr><td>Flessibile</td><td>Entro il termine indicato in prenotazione</td><td>Sì, parziale o totale</td></tr>
<tr><td>Non rimborsabile</td><td>Sempre possibile</td><td>No</td></tr>
</tbody>
</table>

<h2>4. Modalità di cancellazione</h2>
<p>Per cancellare una prenotazione scrivere a <a href="mailto:info@almaretna.it">info@almaretna.it</a> indicando:</p>
<ul>
<li>Numero di prenotazione (formato ALM-XXXX-XXXX)</li>
<li>Nome del titolare della prenotazione</li>
<li>Date del soggiorno</li>
</ul>
<p>La struttura confermerà la cancellazione entro 24 ore lavorative.</p>

<h2>5. Rimborsi</h2>
<p>Eventuali rimborsi previsti dalla politica di cancellazione sono elaborati entro <strong>5–10 giorni lavorativi</strong> tramite il metodo di pagamento originale (Stripe).</p>

<h2>6. Riferimenti normativi</h2>
<ul>
<li>D.Lgs. 206/2005, art. 47 comma 1 lett. g) — Esclusioni dal diritto di recesso</li>
<li>Direttiva UE 2011/83/UE, art. 16 lett. l) — Diritti dei consumatori</li>
<li>Regolamento UE 2016/679 (GDPR) — Protezione dei dati personali</li>
</ul>

<h2>7. Contatti</h2>
<p>Per qualsiasi domanda: <a href="mailto:info@almaretna.it">info@almaretna.it</a> &nbsp;|&nbsp; <a href="tel:+393332621974">+39 333 262 1974</a></p>',
        ],

        // ── Cancellazione (legacy — rimane per compatibilità) ─────────────────
        [
            'title'    => 'Politica di cancellazione',
            'slug'     => 'cancellazione',
            'template' => '',
            'excerpt'  => 'Politica di cancellazione e rimborso di Almaretna.',
            'content'  => '',
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

// ─── Migrazione v2: pagine legali professionali ────────────────────────────────
// Crea le 3 pagine legali mancanti, aggiorna la privacy policy e assegna
// il template professionale a tutte e 4 le pagine legali.

add_action('init', function (): void {
    if (get_option('alm_legal_pages_v1') === '1') return;

    $legal_slugs = ['privacy-policy', 'cookie-policy', 'termini-condizioni', 'diritto-di-recesso'];
    $pages_config = alm_get_pages_config();

    // Indicizza config per slug
    $config_by_slug = [];
    foreach ($pages_config as $p) {
        $config_by_slug[$p['slug']] = $p;
    }

    foreach ($legal_slugs as $slug) {
        if (!isset($config_by_slug[$slug])) continue;
        $cfg = $config_by_slug[$slug];

        $existing = get_page_by_path($slug);

        if (!$existing instanceof WP_Post) {
            // Crea la pagina se non esiste
            $post_id = wp_insert_post([
                'post_type'    => 'page',
                'post_status'  => 'publish',
                'post_title'   => $cfg['title'],
                'post_name'    => $slug,
                'post_content' => $cfg['content'],
                'post_excerpt' => $cfg['excerpt'] ?? '',
            ]);
            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, '_wp_page_template', 'templates/template-legal.php');
            }
        } else {
            // Aggiorna contenuto e template della pagina esistente
            if (!empty($cfg['content'])) {
                wp_update_post([
                    'ID'           => $existing->ID,
                    'post_content' => $cfg['content'],
                    'post_excerpt' => $cfg['excerpt'] ?? '',
                ]);
            }
            update_post_meta($existing->ID, '_wp_page_template', 'templates/template-legal.php');
        }
    }

    update_option('alm_legal_pages_v1', '1');
}, 25);
