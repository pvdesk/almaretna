<?php
/**
 * Almaretna Child — Sistema i18n
 * Lingue: IT | EN | DE | FR | ES
 * Rilevamento: ?lang=xx → cookie alm_lang → default it
 *
 * @package AlmaretnaChild
 */
declare(strict_types=1);

defined('ABSPATH') || exit;

/* ═══════════════════════════════════════════════════════════════
   RILEVAMENTO LINGUA
   ═══════════════════════════════════════════════════════════════ */

// Gestione cookie lingua — via send_headers (prima dell'output)
add_action('send_headers', function (): void {
    $allowed = ['it', 'en', 'de', 'fr', 'es'];
    $lang    = sanitize_key((string) ($_GET['lang'] ?? ''));
    if (in_array($lang, $allowed, true)) {
        // ?lang=XX presente: imposta cookie di SESSIONE (scade alla chiusura del browser)
        setcookie('alm_lang', $lang, 0, '/');
        $_COOKIE['alm_lang'] = $lang;
    } else {
        // Nessun ?lang= nell'URL: cancella il cookie → la pagina mostra sempre italiano
        if (isset($_COOKIE['alm_lang'])) {
            setcookie('alm_lang', '', time() - 3600, '/');
            unset($_COOKIE['alm_lang']);
        }
    }
});

function alm_get_lang(): string {
    $allowed = ['it', 'en', 'de', 'fr', 'es'];
    // 1. ?lang= nella URL — priorità massima (clic selettore lingua)
    $get_lang = sanitize_key((string) ($_GET['lang'] ?? ''));
    if (in_array($get_lang, $allowed, true)) return $get_lang;
    // 2. Cookie (richieste successive dopo il clic)
    $lang = $_COOKIE['alm_lang'] ?? '';
    if (in_array($lang, $allowed, true)) return $lang;
    // 3. Default: italiano (lingua predefinita del sito)
    return 'it';
}

/* ═══════════════════════════════════════════════════════════════
   TRADUZIONI
   ═══════════════════════════════════════════════════════════════ */

function alm_get_strings(): array {
    return [

/* ─── ITALIANO ─────────────────────────────────────────────── */
'it' => [
    // Nav
    'nav.book_now'    => 'Prenota ora',
    'nav.rooms'       => 'Camere',
    'nav.open_menu'   => 'Apri menu',
    'nav.close_menu'  => 'Chiudi menu',
    'nav.main_menu'   => 'Menu principale',
    'nav.mobile_menu' => 'Menu mobile',
    // CTA
    'cta.book'        => 'Prenota il tuo soggiorno',
    'cta.rooms'       => 'Scopri le camere',
    'cta.all_rooms'   => 'Tutte le camere',
    'cta.choose'      => 'Scegli le date',
    'cta.book_exp'    => 'Prenota la tua esperienza',
    'cta.maps'        => 'Apri su Google Maps',
    // Hero
    'hero.eyebrow'    => 'Nunziata di Mascali &nbsp;·&nbsp; Etna · Sicilia',
    'hero.scroll'     => 'Scorri',
    'hero.title'      => "Un rifugio dove l'Etna\ntocca il cielo",
    'hero.subtitle'   => "Villa con piscina panoramica alle pendici del vulcano, tra ulivi e silenzio",
    // Booking strip
    'strip.checkin'   => 'Check-in',
    'strip.checkout'  => 'Check-out',
    'strip.guests'    => 'Ospiti',
    'strip.date_ph'   => 'Scegli la data',
    'strip.a1'        => '1 adulto',
    'strip.a2'        => '2 adulti',
    'strip.a3'        => '3 adulti',
    'strip.a4'        => '4 adulti',
    'strip.a5'        => '5+ adulti',
    'strip.verify'    => 'Verifica disponibilità',
    // Experience
    'exp.eyebrow'     => "L'esperienza",
    'exp.quote'       => '"Qui il tempo non si misura in ore,<br>ma in profumi d\'ulivo, in tramonti sull\'Etna<br>e in silenzi che non dimenticherai mai."',
    'exp.author'      => '— Un ospite di Almaretna',
    // Rooms
    'rooms.eyebrow'   => 'Dove soggiornare',
    'rooms.headline'  => 'Le nostre <em>camere</em>',
    'rooms.guests_sfx'=> ' ospiti',
    'rooms.from'      => 'Da',
    'rooms.book'      => 'Prenota',
    'rooms.night'     => '/notte',
    // Story
    'story.eyebrow'   => 'La nostra storia',
    'story.headline'  => 'Un angolo di<br><em>Sicilia autentica</em>',
    'story.text1'     => "Almaretna è una villa di charme immersa nell'uliveto alle pendici dell'Etna, nel suggestivo borgo di Nunziata di Mascali. Qui il tempo rallenta, i profumi di zagara riempiono l'aria e la vista sul vulcano ti lascia senza parole.",
    'story.text2'     => "Ogni camera è curata nei dettagli per offrirti il massimo del comfort, mentre la nostra piscina panoramica diventa il punto di incontro perfetto tra cielo, Etna e mare.",
    'story.cta'       => 'Scopri le camere',
    'story.badge'     => 'Ospitalità',
    // Pool
    'pool.eyebrow'    => 'La nostra piscina',
    'pool.headline'   => "Un tuffo<br>nel <em>cuore dell'Etna</em>",
    'pool.desc'       => "La piscina di Almaretna è uno spazio pensato per il vero relax: <strong>12 metri per 5 di larghezza</strong>, con una profondità che arriva fino a <strong>3,5 metri</strong>. Immersa nel silenzio dell'uliveto con vista sull'Etna, è il luogo perfetto per una nuotata rigenerante o per oziare al sole.",
    'pool.badge'      => 'Lunghezza piscina',
    'pool.spec1'      => 'Superficie nuoto',
    'pool.spec2_val'  => 'fino a 3,5 m',
    'pool.spec2'      => 'Profondità massima',
    'pool.bar_title'  => 'Bar a bordo piscina',
    'pool.bar_desc'   => 'A disposizione degli ospiti, il locale bar è attrezzato per ogni esigenza:',
    // Numbers
    'num.rooms'       => 'Camere esclusive',
    'num.sunsets'     => "Tramonti sull'Etna",
    'num.sea'         => 'Dal mare',
    'num.rating'      => 'Valutazione media',
    // Gallery
    'gallery.all'     => 'Vedi tutte le foto →',
    // Testimonials
    'test.eyebrow'    => 'Cosa dicono i nostri ospiti',
    'test.headline'   => "Storie di <em>chi c'era</em>",
    'test.rating'     => 'su Google &amp; Booking.com',
    // Location
    'loc.eyebrow'     => 'Come raggiungerci',
    'loc.headline'    => "Nel cuore<br>della <em>Sicilia</em>",
    'loc.maps'        => 'Apri su Google Maps',
    'loc.map_title'   => 'Posizione Almaretna',
    // Final CTA
    'fcta.eyebrow'    => 'Prenotazione diretta',
    'fcta.headline'   => "La tua vacanza<br><em>siciliana perfetta</em><br>ti aspetta",
    'fcta.text'       => 'Prenota direttamente e ottieni il miglior prezzo garantito, cancellazione flessibile e un benvenuto speciale.',
    'fcta.btn'        => 'Scegli le date',
    'fcta.tel'        => 'O chiamaci:',
    'fcta.g1'         => 'Miglior prezzo garantito',
    'fcta.g2'         => 'Cancellazione flessibile',
    'fcta.g3'         => 'Pagamento 100% sicuro',
    // Footer
    'foot.tagline'    => "Un rifugio di lusso alle pendici dell'Etna, tra natura e benessere.",
    'foot.explore'    => 'Esplora',
    'foot.our_rooms'  => 'Le nostre camere',
    'foot.book'       => 'Prenota',
    'foot.contacts'   => 'Contatti',
    'foot.info'       => 'Informazioni',
    'foot.cin'        => 'Check-in:',
    'foot.cout'       => 'Check-out:',
    'foot.cin_from'   => 'dalle ',
    'foot.cout_by'    => 'entro le ',
    'foot.pool'       => 'Piscina panoramica',
    'foot.bar'        => 'Bar bordo piscina attrezzato',
    'foot.wifi'       => 'Wi-Fi gratuito',
    'foot.parking'    => 'Parcheggio gratuito',
    'foot.rights'     => 'Tutti i diritti riservati.',
    'foot.payments'   => 'Pagamenti sicuri con',
    'foot.legal'         => 'Informazioni legali',
    'foot.legal_privacy' => 'Privacy Policy',
    'foot.legal_cookie'  => 'Cookie Policy',
    'foot.legal_terms'   => 'Termini e Condizioni',
    'foot.legal_recesso' => 'Diritto di Recesso',
    // Pagina prenotazione
    'book.checkin_info'    => 'Check-in dalle 15:00 &nbsp;·&nbsp; Check-out entro le 11:00',
    'book.steps_label'     => 'Fasi prenotazione',
    'book.step1'           => 'Date',
    'book.step2'           => 'Camera',
    'book.step3'           => 'Dati',
    'book.step4'           => 'Pagamento',
    'book.secure_pay'      => 'Pagamento sicuro',
    'book.secure_pay_desc' => 'Dati crittografati SSL. Pagamento gestito e protetto da Stripe.',
    'book.direct'          => 'Prenotazione diretta',
    'book.direct_desc'     => 'Senza intermediari. Miglior prezzo online garantito.',
    'book.instant'         => 'Conferma immediata',
    'book.instant_desc'    => "Ricevi la conferma via email all'istante, non appena completato il pagamento.",
    'book.page_title'      => 'Prenota il tuo soggiorno',
    'seo.prenota_desc'     => 'Prenota direttamente ad Almaretna: verifica disponibilità, pagamento sicuro con Stripe, miglior prezzo garantito.',
    // Pagina camere
    'rooms.page_h1'        => 'Camere',
    'rooms.page_subtitle'  => 'Sette camere distribuite su tre livelli, ognuna con la sua luce.',
    'rooms.filter'         => 'Filtra',
    'rooms.type_label'     => 'Tipologia',
    'rooms.all_types'      => 'Tutte',
    'rooms.floor_label'    => 'Piano',
    'rooms.all_floors'     => 'Tutti',
    'rooms.guests_label'   => 'Ospiti',
    'rooms.any'            => 'Qualsiasi',
    'rooms.guest_1'        => '%d ospite',
    'rooms.guest_n'        => '%d ospiti',
    'rooms.reset'          => 'Azzera',
    'rooms.n_avail_1'      => '%d camera disponibile',
    'rooms.n_avail_n'      => '%d camere disponibili',
    'rooms.cta_title'      => 'Pronto a prenotare il tuo soggiorno?',
    'rooms.cta_desc'       => 'Verifica la disponibilità e prenota direttamente online, senza intermediari.',
    'rooms.verify_avail'   => 'Verifica disponibilità',
    // Singola camera
    'room.breadcrumb'      => 'Camere',
    'room.img_view'        => 'Vedi immagine',
    'room.the_room'        => 'La camera',
    'room.amenities'       => 'Dotazioni',
    'room.stay_details'    => 'Dettagli soggiorno',
    'room.adults_1'        => 'Max %d adulto',
    'room.adults_n'        => 'Max %d adulti',
    'room.children_1'      => '%d bambino',
    'room.children_n'      => '%d bambini',
    'room.min_stay_n'      => 'Minimo %d notti di soggiorno',
    'room.min_stay_1'      => '1 notte minimo di soggiorno',
    'room.checkin_time'    => 'Check-in: ore 15:00',
    'room.checkout_time'   => 'Check-out: ore 11:00',
    'room.availability'    => 'Disponibilità camera',
    'room.related'         => 'Camere correlate',
    'room.per_night'       => '/ notte',
    'room.discover'        => 'Scopri',
    // Shortcode camere (card + price-box + calendario)
    'sc.no_rooms'          => 'Nessuna camera disponibile con i filtri selezionati.',
    'sc.price_tag'         => 'da €%s/notte',
    'sc.cal_prev'          => 'Mese precedente',
    'sc.cal_next'          => 'Mese successivo',
    'sc.cal_title'         => 'Disponibilità',
    'sc.cal_loading'       => 'Calendario disponibilità in caricamento…',
    'sc.up_to_adults'      => 'Fino a %d adulti',
    'sc.children'          => 'bambini',
    'sc.min_stay'          => 'Soggiorno minimo: %d notti',
    'sc.date_ph'           => 'gg/mm/aaaa',
    'sc.secure_cancel'     => 'Pagamento sicuro. Cancellazione gratuita.',
    'sc.no_extra'          => 'Pagamento sicuro. Nessun costo extra.',
    // Helper — etichette piano e bagno
    'h.floor_1'            => 'Piano 1',
    'h.floor_2'            => 'Piano 2',
    'h.floor_m'            => 'Mansarda',
    'h.shared_bath_with'   => 'Bagno condiviso con la camera adiacente (%s).',
    'h.shared_bath'        => 'Bagno condiviso con la camera adiacente.',
    // Wizard prenotazione
    'wiz.when_stay'        => 'Quando vorresti soggiornare?',
    'wiz.checkin'          => 'Check-in',
    'wiz.checkout'         => 'Check-out',
    'wiz.adults'           => 'Adulti',
    'wiz.children'         => 'Bambini (0-12 anni)',
    'wiz.search'           => 'Cerca camere disponibili',
    'wiz.choose_room'      => 'Scegli la camera',
    'wiz.searching'        => 'Ricerca in corso...',
    'wiz.back_dates'       => 'Modifica date',
    'wiz.your_data'        => 'I tuoi dati',
    'wiz.first_name'       => 'Nome',
    'wiz.last_name'        => 'Cognome',
    'wiz.email'            => 'Email',
    'wiz.phone'            => 'Telefono',
    'wiz.extras'           => 'Servizi opzionali',
    'wiz.special_req'      => 'Richieste speciali',
    'wiz.optional'         => 'opzionale',
    'wiz.req_ph'           => 'Allergie, orario di arrivo previsto, esigenze particolari...',
    'wiz.i_accept'         => 'Ho letto e accetto la',
    'wiz.privacy_policy'   => 'Privacy Policy',
    'wiz.cancel_policy'    => 'Politica di cancellazione',
    'wiz.summary'          => 'Riepilogo',
    'wiz.nights'           => 'Notti',
    'wiz.guests'           => 'Ospiti',
    'wiz.room_price'       => 'Prezzo camera',
    'wiz.extras_label'     => 'Extras',
    'wiz.total'            => 'Totale',
    'wiz.back_rooms'       => 'Torna alla scelta camera',
    'wiz.proceed_pay'      => 'Procedi al pagamento',
    'wiz.payment'          => 'Pagamento',
    'wiz.room_label'       => 'Camera',
    'wiz.dates'            => 'Date',
    'wiz.total_pay'        => 'Totale da pagare',
    'wiz.pay_loading'      => 'Caricamento modulo pagamento...',
    'wiz.pay_secure'       => 'Pagamento sicuro gestito da Stripe. I tuoi dati sono crittografati.',
    'wiz.your_booking'     => 'La tua prenotazione',
    'wiz.pay_now'          => 'Paga ora',
    'wiz.back_data'        => 'Modifica dati',
    'wiz.confirmed'        => 'Prenotazione confermata!',
    'wiz.confirm_email'    => "Riceverai una email di conferma a breve all'indirizzo indicato.",
    'wiz.checkin_inst'     => 'Indicazioni check-in:',
    'wiz.checkin_times'    => 'Check-in dalle ore 15:00 — Check-out entro le ore 11:00',
    'wiz.address_label'    => 'Indirizzo:',
    'wiz.back_home'        => 'Torna alla home',
    'wiz.form_unavailable' => 'Il modulo di prenotazione è in fase di configurazione. Torna presto!',
    // JS inline (iniettati via wp_localize_script)
    'js.calculating'       => 'Calcolo in corso...',
    'js.dates_unavail'     => 'Date non disponibili.',
    'js.night_1'           => 'notte',
    'js.night_n'           => 'notti',
    'js.selected'          => 'selezionate',
    'js.conn_error'        => 'Errore di connessione.',
],

/* ─── ENGLISH ──────────────────────────────────────────────── */
'en' => [
    'nav.book_now'    => 'Book now',
    'nav.rooms'       => 'Rooms',
    'nav.open_menu'   => 'Open menu',
    'nav.close_menu'  => 'Close menu',
    'nav.main_menu'   => 'Main menu',
    'nav.mobile_menu' => 'Mobile menu',
    'cta.book'        => 'Book your stay',
    'cta.rooms'       => 'Discover the rooms',
    'cta.all_rooms'   => 'All rooms',
    'cta.choose'      => 'Choose dates',
    'cta.book_exp'    => 'Book your experience',
    'cta.maps'        => 'Open in Google Maps',
    'hero.eyebrow'    => 'Nunziata di Mascali &nbsp;·&nbsp; Etna · Sicily',
    'hero.scroll'     => 'Scroll',
    'hero.title'      => "A retreat where Etna\ntouches the sky",
    'hero.subtitle'   => "Villa with panoramic pool on the slopes of the volcano, among olive trees and silence",
    'strip.checkin'   => 'Check-in',
    'strip.checkout'  => 'Check-out',
    'strip.guests'    => 'Guests',
    'strip.date_ph'   => 'Choose date',
    'strip.a1'        => '1 adult',
    'strip.a2'        => '2 adults',
    'strip.a3'        => '3 adults',
    'strip.a4'        => '4 adults',
    'strip.a5'        => '5+ adults',
    'strip.verify'    => 'Check availability',
    'exp.eyebrow'     => 'The experience',
    'exp.quote'       => '"Here time is not measured in hours,<br>but in the scent of olive trees, in sunsets over Etna,<br>and in silences you will never forget."',
    'exp.author'      => '— A guest of Almaretna',
    'rooms.eyebrow'   => 'Where to stay',
    'rooms.headline'  => 'Our <em>rooms</em>',
    'rooms.guests_sfx'=> ' guests',
    'rooms.from'      => 'From',
    'rooms.book'      => 'Book',
    'rooms.night'     => '/night',
    'story.eyebrow'   => 'Our story',
    'story.headline'  => 'A corner of<br><em>authentic Sicily</em>',
    'story.text1'     => "Almaretna is a charming villa nestled in the olive grove on the slopes of Etna, in the picturesque village of Nunziata di Mascali. Here time slows down, the scent of orange blossom fills the air and the view of the volcano leaves you speechless.",
    'story.text2'     => "Every room is carefully designed to offer you the utmost comfort, while our panoramic pool becomes the perfect meeting point between sky, Etna and sea.",
    'story.cta'       => 'Discover the rooms',
    'story.badge'     => 'Hospitality',
    'pool.eyebrow'    => 'Our pool',
    'pool.headline'   => 'A dip<br>in the <em>heart of Etna</em>',
    'pool.desc'       => "Almaretna's pool is a space designed for true relaxation: <strong>12 metres long and 5 metres wide</strong>, with a depth of up to <strong>3.5 metres</strong>. Nestled in the silence of the olive grove with views of Etna, it is the perfect place for a refreshing swim or to lounge in the sun.",
    'pool.badge'      => 'Pool length',
    'pool.spec1'      => 'Swimming area',
    'pool.spec2_val'  => 'up to 3.5 m',
    'pool.spec2'      => 'Maximum depth',
    'pool.bar_title'  => 'Poolside bar',
    'pool.bar_desc'   => 'Available to guests, the bar area is fully equipped:',
    'num.rooms'       => 'Exclusive rooms',
    'num.sunsets'     => 'Etna sunsets',
    'num.sea'         => 'From the sea',
    'num.rating'      => 'Average rating',
    'gallery.all'     => 'View all photos →',
    'test.eyebrow'    => 'What our guests say',
    'test.headline'   => 'Stories from <em>those who were here</em>',
    'test.rating'     => 'on Google &amp; Booking.com',
    'loc.eyebrow'     => 'How to reach us',
    'loc.headline'    => 'In the heart<br>of <em>Sicily</em>',
    'loc.maps'        => 'Open in Google Maps',
    'loc.map_title'   => 'Almaretna location',
    'fcta.eyebrow'    => 'Direct booking',
    'fcta.headline'   => 'Your perfect<br><em>Sicilian holiday</em><br>awaits',
    'fcta.text'       => 'Book directly and get the best price guaranteed, flexible cancellation and a special welcome.',
    'fcta.btn'        => 'Choose dates',
    'fcta.tel'        => 'Or call us:',
    'fcta.g1'         => 'Best price guaranteed',
    'fcta.g2'         => 'Flexible cancellation',
    'fcta.g3'         => '100% secure payment',
    'foot.tagline'    => 'A luxury retreat on the slopes of Etna, between nature and wellbeing.',
    'foot.explore'    => 'Explore',
    'foot.our_rooms'  => 'Our rooms',
    'foot.book'       => 'Book',
    'foot.contacts'   => 'Contacts',
    'foot.info'       => 'Information',
    'foot.cin'        => 'Check-in:',
    'foot.cout'       => 'Check-out:',
    'foot.cin_from'   => 'from ',
    'foot.cout_by'    => 'by ',
    'foot.pool'       => 'Panoramic pool',
    'foot.bar'        => 'Equipped poolside bar',
    'foot.wifi'       => 'Free Wi-Fi',
    'foot.parking'    => 'Free parking',
    'foot.rights'     => 'All rights reserved.',
    'foot.payments'   => 'Secure payments with',
    'foot.legal'         => 'Legal',
    'foot.legal_privacy' => 'Privacy Policy',
    'foot.legal_cookie'  => 'Cookie Policy',
    'foot.legal_terms'   => 'Terms & Conditions',
    'foot.legal_recesso' => 'Right of Withdrawal',
    // Booking page
    'book.checkin_info'    => 'Check-in from 3:00 PM &nbsp;·&nbsp; Check-out by 11:00 AM',
    'book.steps_label'     => 'Booking steps',
    'book.step1'           => 'Dates',
    'book.step2'           => 'Room',
    'book.step3'           => 'Details',
    'book.step4'           => 'Payment',
    'book.secure_pay'      => 'Secure payment',
    'book.secure_pay_desc' => 'SSL encrypted data. Payment managed and protected by Stripe.',
    'book.direct'          => 'Direct booking',
    'book.direct_desc'     => 'No intermediaries. Best online price guaranteed.',
    'book.instant'         => 'Instant confirmation',
    'book.instant_desc'    => 'Receive email confirmation instantly, as soon as payment is complete.',
    'book.page_title'      => 'Book your stay',
    'seo.prenota_desc'     => 'Book directly at Almaretna: check availability, secure payment with Stripe, best price guaranteed.',
    // Rooms page
    'rooms.page_h1'        => 'Rooms',
    'rooms.page_subtitle'  => 'Seven rooms on three levels, each with its own light.',
    'rooms.filter'         => 'Filter',
    'rooms.type_label'     => 'Type',
    'rooms.all_types'      => 'All',
    'rooms.floor_label'    => 'Floor',
    'rooms.all_floors'     => 'All',
    'rooms.guests_label'   => 'Guests',
    'rooms.any'            => 'Any',
    'rooms.guest_1'        => '%d guest',
    'rooms.guest_n'        => '%d guests',
    'rooms.reset'          => 'Reset',
    'rooms.n_avail_1'      => '%d room available',
    'rooms.n_avail_n'      => '%d rooms available',
    'rooms.cta_title'      => 'Ready to book your stay?',
    'rooms.cta_desc'       => 'Check availability and book directly online, with no intermediaries.',
    'rooms.verify_avail'   => 'Check availability',
    // Single room
    'room.breadcrumb'      => 'Rooms',
    'room.img_view'        => 'View image',
    'room.the_room'        => 'The room',
    'room.amenities'       => 'Amenities',
    'room.stay_details'    => 'Stay details',
    'room.adults_1'        => 'Max %d adult',
    'room.adults_n'        => 'Max %d adults',
    'room.children_1'      => '%d child',
    'room.children_n'      => '%d children',
    'room.min_stay_n'      => 'Minimum %d nights stay',
    'room.min_stay_1'      => 'Minimum 1 night stay',
    'room.checkin_time'    => 'Check-in: 3:00 PM',
    'room.checkout_time'   => 'Check-out: 11:00 AM',
    'room.availability'    => 'Room availability',
    'room.related'         => 'Related rooms',
    'room.per_night'       => '/ night',
    'room.discover'        => 'Discover',
    'sc.no_rooms'          => 'No rooms available with the selected filters.',
    'sc.price_tag'         => 'from €%s/night',
    'sc.cal_prev'          => 'Previous month',
    'sc.cal_next'          => 'Next month',
    'sc.cal_title'         => 'Availability',
    'sc.cal_loading'       => 'Loading availability calendar…',
    'sc.up_to_adults'      => 'Up to %d adults',
    'sc.children'          => 'children',
    'sc.min_stay'          => 'Minimum stay: %d nights',
    'sc.date_ph'           => 'dd/mm/yyyy',
    'sc.secure_cancel'     => 'Secure payment. Free cancellation.',
    'sc.no_extra'          => 'Secure payment. No extra fees.',
    'h.floor_1'            => 'Floor 1',
    'h.floor_2'            => 'Floor 2',
    'h.floor_m'            => 'Attic',
    'h.shared_bath_with'   => 'Shared bathroom with the adjacent room (%s).',
    'h.shared_bath'        => 'Shared bathroom with the adjacent room.',
    'wiz.when_stay'        => 'When would you like to stay?',
    'wiz.checkin'          => 'Check-in',
    'wiz.checkout'         => 'Check-out',
    'wiz.adults'           => 'Adults',
    'wiz.children'         => 'Children (0-12 yrs)',
    'wiz.search'           => 'Search available rooms',
    'wiz.choose_room'      => 'Choose your room',
    'wiz.searching'        => 'Searching...',
    'wiz.back_dates'       => 'Edit dates',
    'wiz.your_data'        => 'Your details',
    'wiz.first_name'       => 'First name',
    'wiz.last_name'        => 'Last name',
    'wiz.email'            => 'Email',
    'wiz.phone'            => 'Phone',
    'wiz.extras'           => 'Optional services',
    'wiz.special_req'      => 'Special requests',
    'wiz.optional'         => 'optional',
    'wiz.req_ph'           => 'Allergies, expected arrival time, special needs...',
    'wiz.i_accept'         => 'I have read and accept the',
    'wiz.privacy_policy'   => 'Privacy Policy',
    'wiz.cancel_policy'    => 'Cancellation Policy',
    'wiz.summary'          => 'Summary',
    'wiz.nights'           => 'Nights',
    'wiz.guests'           => 'Guests',
    'wiz.room_price'       => 'Room price',
    'wiz.extras_label'     => 'Extras',
    'wiz.total'            => 'Total',
    'wiz.back_rooms'       => 'Back to room selection',
    'wiz.proceed_pay'      => 'Proceed to payment',
    'wiz.payment'          => 'Payment',
    'wiz.room_label'       => 'Room',
    'wiz.dates'            => 'Dates',
    'wiz.total_pay'        => 'Total to pay',
    'wiz.pay_loading'      => 'Loading payment form...',
    'wiz.pay_secure'       => 'Secure payment by Stripe. Your data is encrypted.',
    'wiz.your_booking'     => 'Your booking',
    'wiz.pay_now'          => 'Pay now',
    'wiz.back_data'        => 'Edit details',
    'wiz.confirmed'        => 'Booking confirmed!',
    'wiz.confirm_email'    => 'You will receive a confirmation email shortly at the provided address.',
    'wiz.checkin_inst'     => 'Check-in instructions:',
    'wiz.checkin_times'    => 'Check-in from 3:00 PM — Check-out by 11:00 AM',
    'wiz.address_label'    => 'Address:',
    'wiz.back_home'        => 'Back to home',
    'wiz.form_unavailable' => 'The booking form is being set up. Come back soon!',
    'js.calculating'       => 'Calculating...',
    'js.dates_unavail'     => 'Dates unavailable.',
    'js.night_1'           => 'night',
    'js.night_n'           => 'nights',
    'js.selected'          => 'selected',
    'js.conn_error'        => 'Connection error.',
],

/* ─── DEUTSCH ──────────────────────────────────────────────── */
'de' => [
    'nav.book_now'    => 'Jetzt buchen',
    'nav.rooms'       => 'Zimmer',
    'nav.open_menu'   => 'Menü öffnen',
    'nav.close_menu'  => 'Menü schließen',
    'nav.main_menu'   => 'Hauptmenü',
    'nav.mobile_menu' => 'Mobiles Menü',
    'cta.book'        => 'Jetzt buchen',
    'cta.rooms'       => 'Zimmer entdecken',
    'cta.all_rooms'   => 'Alle Zimmer',
    'cta.choose'      => 'Daten auswählen',
    'cta.book_exp'    => 'Ihr Erlebnis buchen',
    'cta.maps'        => 'In Google Maps öffnen',
    'hero.eyebrow'    => 'Nunziata di Mascali &nbsp;·&nbsp; Ätna · Sizilien',
    'hero.scroll'     => 'Scrollen',
    'hero.title'      => "Ein Rückzugsort wo der Ätna\nden Himmel berührt",
    'hero.subtitle'   => "Villa mit Panoramabecken am Fuße des Vulkans, zwischen Olivenbäumen und Stille",
    'strip.checkin'   => 'Anreise',
    'strip.checkout'  => 'Abreise',
    'strip.guests'    => 'Gäste',
    'strip.date_ph'   => 'Datum wählen',
    'strip.a1'        => '1 Erwachsener',
    'strip.a2'        => '2 Erwachsene',
    'strip.a3'        => '3 Erwachsene',
    'strip.a4'        => '4 Erwachsene',
    'strip.a5'        => '5+ Erwachsene',
    'strip.verify'    => 'Verfügbarkeit prüfen',
    'exp.eyebrow'     => 'Das Erlebnis',
    'exp.quote'       => '"Hier misst man die Zeit nicht in Stunden,<br>sondern im Duft der Olivenbäume, in Sonnenuntergängen über dem Ätna<br>und in Stille, die man nie vergisst."',
    'exp.author'      => '— Ein Gast von Almaretna',
    'rooms.eyebrow'   => 'Wo übernachten',
    'rooms.headline'  => 'Unsere <em>Zimmer</em>',
    'rooms.guests_sfx'=> ' Gäste',
    'rooms.from'      => 'Ab',
    'rooms.book'      => 'Buchen',
    'rooms.night'     => '/Nacht',
    'story.eyebrow'   => 'Unsere Geschichte',
    'story.headline'  => 'Ein Stück<br><em>echtes Sizilien</em>',
    'story.text1'     => "Almaretna ist eine charmante Villa inmitten eines Olivenhains am Fuße des Ätna, im malerischen Dorf Nunziata di Mascali. Hier verlangsamt sich die Zeit, der Duft von Orangenblüten erfüllt die Luft und der Blick auf den Vulkan lässt einen sprachlos.",
    'story.text2'     => "Jedes Zimmer ist sorgfältig gestaltet, um Ihnen maximalen Komfort zu bieten, während unser Panoramabecken zum perfekten Treffpunkt zwischen Himmel, Ätna und Meer wird.",
    'story.cta'       => 'Zimmer entdecken',
    'story.badge'     => 'Gastfreundschaft',
    'pool.eyebrow'    => 'Unser Pool',
    'pool.headline'   => 'Ein Sprung<br>ins <em>Herz des Ätna</em>',
    'pool.desc'       => "Der Pool von Almaretna ist ein Ort für echte Entspannung: <strong>12 Meter lang und 5 Meter breit</strong>, mit einer Tiefe von bis zu <strong>3,5 Metern</strong>. Eingebettet in die Stille des Olivenhains mit Blick auf den Ätna, ist er der ideale Ort für ein erfrischendes Bad oder zum Entspannen in der Sonne.",
    'pool.badge'      => 'Poollänge',
    'pool.spec1'      => 'Schwimmbereich',
    'pool.spec2_val'  => 'bis zu 3,5 m',
    'pool.spec2'      => 'Maximale Tiefe',
    'pool.bar_title'  => 'Pool-Bar',
    'pool.bar_desc'   => 'Den Gästen steht eine vollausgestattete Bar für alle Bedürfnisse zur Verfügung:',
    'num.rooms'       => 'Exklusive Zimmer',
    'num.sunsets'     => 'Ätna-Sonnenuntergänge',
    'num.sea'         => 'Vom Meer',
    'num.rating'      => 'Durchschnittsbewertung',
    'gallery.all'     => 'Alle Fotos ansehen →',
    'test.eyebrow'    => 'Was unsere Gäste sagen',
    'test.headline'   => 'Geschichten von <em>unseren Gästen</em>',
    'test.rating'     => 'auf Google &amp; Booking.com',
    'loc.eyebrow'     => 'Wie Sie uns erreichen',
    'loc.headline'    => 'Im Herzen<br><em>Siziliens</em>',
    'loc.maps'        => 'In Google Maps öffnen',
    'loc.map_title'   => 'Standort Almaretna',
    'fcta.eyebrow'    => 'Direktbuchung',
    'fcta.headline'   => 'Ihr perfekter<br><em>Sizilienurlaub</em><br>wartet auf Sie',
    'fcta.text'       => 'Buchen Sie direkt und erhalten Sie den besten Preis garantiert, flexible Stornierung und einen besonderen Willkommensgruß.',
    'fcta.btn'        => 'Daten auswählen',
    'fcta.tel'        => 'Oder rufen Sie uns an:',
    'fcta.g1'         => 'Bester Preis garantiert',
    'fcta.g2'         => 'Flexible Stornierung',
    'fcta.g3'         => '100% sichere Zahlung',
    'foot.tagline'    => 'Ein luxuriöser Rückzugsort am Fuße des Ätna, zwischen Natur und Wohlbefinden.',
    'foot.explore'    => 'Entdecken',
    'foot.our_rooms'  => 'Unsere Zimmer',
    'foot.book'       => 'Buchen',
    'foot.contacts'   => 'Kontakt',
    'foot.info'       => 'Informationen',
    'foot.cin'        => 'Check-in:',
    'foot.cout'       => 'Check-out:',
    'foot.cin_from'   => 'ab ',
    'foot.cout_by'    => 'bis ',
    'foot.pool'       => 'Panoramabecken',
    'foot.bar'        => 'Vollausgestattete Pool-Bar',
    'foot.wifi'       => 'Kostenloses WLAN',
    'foot.parking'    => 'Kostenloses Parken',
    'foot.rights'     => 'Alle Rechte vorbehalten.',
    'foot.payments'   => 'Sichere Zahlungen mit',
    'foot.legal'         => 'Rechtliches',
    'foot.legal_privacy' => 'Datenschutzerklärung',
    'foot.legal_cookie'  => 'Cookie-Richtlinie',
    'foot.legal_terms'   => 'AGB',
    'foot.legal_recesso' => 'Widerrufsrecht',
    // Buchungsseite
    'book.checkin_info'    => 'Check-in ab 15:00 Uhr &nbsp;·&nbsp; Check-out bis 11:00 Uhr',
    'book.steps_label'     => 'Buchungsschritte',
    'book.step1'           => 'Termine',
    'book.step2'           => 'Zimmer',
    'book.step3'           => 'Details',
    'book.step4'           => 'Zahlung',
    'book.secure_pay'      => 'Sichere Zahlung',
    'book.secure_pay_desc' => 'SSL-verschlüsselte Daten. Zahlung verwaltet und gesichert durch Stripe.',
    'book.direct'          => 'Direktbuchung',
    'book.direct_desc'     => 'Ohne Vermittler. Bester Online-Preis garantiert.',
    'book.instant'         => 'Sofortbestätigung',
    'book.instant_desc'    => 'Erhalten Sie sofort eine E-Mail-Bestätigung, sobald die Zahlung abgeschlossen ist.',
    'book.page_title'      => 'Ihren Aufenthalt buchen',
    'seo.prenota_desc'     => 'Buchen Sie direkt bei Almaretna: Verfügbarkeit prüfen, sichere Zahlung mit Stripe, bester Preis garantiert.',
    // Zimmerseite
    'rooms.page_h1'        => 'Zimmer',
    'rooms.page_subtitle'  => 'Sieben Zimmer auf drei Ebenen, jedes mit seinem eigenen Licht.',
    'rooms.filter'         => 'Filtern',
    'rooms.type_label'     => 'Typ',
    'rooms.all_types'      => 'Alle',
    'rooms.floor_label'    => 'Etage',
    'rooms.all_floors'     => 'Alle',
    'rooms.guests_label'   => 'Gäste',
    'rooms.any'            => 'Beliebig',
    'rooms.guest_1'        => '%d Gast',
    'rooms.guest_n'        => '%d Gäste',
    'rooms.reset'          => 'Zurücksetzen',
    'rooms.n_avail_1'      => '%d Zimmer verfügbar',
    'rooms.n_avail_n'      => '%d Zimmer verfügbar',
    'rooms.cta_title'      => 'Bereit, Ihren Aufenthalt zu buchen?',
    'rooms.cta_desc'       => 'Prüfen Sie die Verfügbarkeit und buchen Sie direkt online, ohne Zwischenhändler.',
    'rooms.verify_avail'   => 'Verfügbarkeit prüfen',
    // Einzelzimmer
    'room.breadcrumb'      => 'Zimmer',
    'room.img_view'        => 'Bild ansehen',
    'room.the_room'        => 'Das Zimmer',
    'room.amenities'       => 'Ausstattung',
    'room.stay_details'    => 'Aufenthaltsdetails',
    'room.adults_1'        => 'Max. %d Erwachsener',
    'room.adults_n'        => 'Max. %d Erwachsene',
    'room.children_1'      => '%d Kind',
    'room.children_n'      => '%d Kinder',
    'room.min_stay_n'      => 'Mindestaufenthalt %d Nächte',
    'room.min_stay_1'      => 'Mindestaufenthalt 1 Nacht',
    'room.checkin_time'    => 'Check-in: 15:00 Uhr',
    'room.checkout_time'   => 'Check-out: 11:00 Uhr',
    'room.availability'    => 'Zimmerverfügbarkeit',
    'room.related'         => 'Ähnliche Zimmer',
    'room.per_night'       => '/ Nacht',
    'room.discover'        => 'Entdecken',
    'sc.no_rooms'          => 'Keine Zimmer mit den gewählten Filtern verfügbar.',
    'sc.price_tag'         => 'ab €%s/Nacht',
    'sc.cal_prev'          => 'Vorheriger Monat',
    'sc.cal_next'          => 'Nächster Monat',
    'sc.cal_title'         => 'Verfügbarkeit',
    'sc.cal_loading'       => 'Verfügbarkeitskalender wird geladen…',
    'sc.up_to_adults'      => 'Bis zu %d Erwachsene',
    'sc.children'          => 'Kinder',
    'sc.min_stay'          => 'Mindestaufenthalt: %d Nächte',
    'sc.date_ph'           => 'TT/MM/JJJJ',
    'sc.secure_cancel'     => 'Sichere Zahlung. Kostenlose Stornierung.',
    'sc.no_extra'          => 'Sichere Zahlung. Keine zusätzlichen Gebühren.',
    'h.floor_1'            => 'Etage 1',
    'h.floor_2'            => 'Etage 2',
    'h.floor_m'            => 'Dachgeschoss',
    'h.shared_bath_with'   => 'Gemeinsames Bad mit dem Nebenzimmer (%s).',
    'h.shared_bath'        => 'Gemeinsames Bad mit dem Nebenzimmer.',
    'wiz.when_stay'        => 'Wann möchten Sie übernachten?',
    'wiz.checkin'          => 'Anreise',
    'wiz.checkout'         => 'Abreise',
    'wiz.adults'           => 'Erwachsene',
    'wiz.children'         => 'Kinder (0–12 J.)',
    'wiz.search'           => 'Verfügbare Zimmer suchen',
    'wiz.choose_room'      => 'Zimmer wählen',
    'wiz.searching'        => 'Suche läuft…',
    'wiz.back_dates'       => 'Daten ändern',
    'wiz.your_data'        => 'Ihre Daten',
    'wiz.first_name'       => 'Vorname',
    'wiz.last_name'        => 'Nachname',
    'wiz.email'            => 'E-Mail',
    'wiz.phone'            => 'Telefon',
    'wiz.extras'           => 'Optionale Leistungen',
    'wiz.special_req'      => 'Sonderwünsche',
    'wiz.optional'         => 'optional',
    'wiz.req_ph'           => 'Allergien, voraussichtliche Ankunftszeit, besondere Bedürfnisse…',
    'wiz.i_accept'         => 'Ich habe gelesen und akzeptiere die',
    'wiz.privacy_policy'   => 'Datenschutzerklärung',
    'wiz.cancel_policy'    => 'Stornierungsrichtlinie',
    'wiz.summary'          => 'Zusammenfassung',
    'wiz.nights'           => 'Nächte',
    'wiz.guests'           => 'Gäste',
    'wiz.room_price'       => 'Zimmerpreis',
    'wiz.extras_label'     => 'Extras',
    'wiz.total'            => 'Gesamt',
    'wiz.back_rooms'       => 'Zurück zur Zimmerauswahl',
    'wiz.proceed_pay'      => 'Weiter zur Zahlung',
    'wiz.payment'          => 'Zahlung',
    'wiz.room_label'       => 'Zimmer',
    'wiz.dates'            => 'Termine',
    'wiz.total_pay'        => 'Zu zahlender Betrag',
    'wiz.pay_loading'      => 'Zahlungsformular wird geladen…',
    'wiz.pay_secure'       => 'Sichere Zahlung über Stripe. Ihre Daten sind verschlüsselt.',
    'wiz.your_booking'     => 'Ihre Buchung',
    'wiz.pay_now'          => 'Jetzt zahlen',
    'wiz.back_data'        => 'Daten bearbeiten',
    'wiz.confirmed'        => 'Buchung bestätigt!',
    'wiz.confirm_email'    => 'Sie erhalten in Kürze eine Bestätigungs-E-Mail an die angegebene Adresse.',
    'wiz.checkin_inst'     => 'Check-in Hinweise:',
    'wiz.checkin_times'    => 'Check-in ab 15:00 Uhr — Check-out bis 11:00 Uhr',
    'wiz.address_label'    => 'Adresse:',
    'wiz.back_home'        => 'Zur Startseite',
    'wiz.form_unavailable' => 'Das Buchungsformular wird eingerichtet. Kommen Sie bald wieder!',
    'js.calculating'       => 'Wird berechnet...',
    'js.dates_unavail'     => 'Daten nicht verfügbar.',
    'js.night_1'           => 'Nacht',
    'js.night_n'           => 'Nächte',
    'js.selected'          => 'ausgewählt',
    'js.conn_error'        => 'Verbindungsfehler.',
],

/* ─── FRANÇAIS ─────────────────────────────────────────────── */
'fr' => [
    'nav.book_now'    => 'Réserver',
    'nav.rooms'       => 'Chambres',
    'nav.open_menu'   => 'Ouvrir le menu',
    'nav.close_menu'  => 'Fermer le menu',
    'nav.main_menu'   => 'Menu principal',
    'nav.mobile_menu' => 'Menu mobile',
    'cta.book'        => 'Réservez votre séjour',
    'cta.rooms'       => 'Découvrir les chambres',
    'cta.all_rooms'   => 'Toutes les chambres',
    'cta.choose'      => 'Choisir les dates',
    'cta.book_exp'    => 'Réservez votre expérience',
    'cta.maps'        => 'Ouvrir dans Google Maps',
    'hero.eyebrow'    => 'Nunziata di Mascali &nbsp;·&nbsp; Etna · Sicile',
    'hero.scroll'     => 'Défiler',
    'hero.title'      => "Un refuge où l'Etna\ntouche le ciel",
    'hero.subtitle'   => "Villa avec piscine panoramique sur les pentes du volcan, entre oliviers et silence",
    'strip.checkin'   => 'Arrivée',
    'strip.checkout'  => 'Départ',
    'strip.guests'    => 'Voyageurs',
    'strip.date_ph'   => 'Choisir la date',
    'strip.a1'        => '1 adulte',
    'strip.a2'        => '2 adultes',
    'strip.a3'        => '3 adultes',
    'strip.a4'        => '4 adultes',
    'strip.a5'        => '5+ adultes',
    'strip.verify'    => 'Vérifier disponibilité',
    'exp.eyebrow'     => "L'expérience",
    'exp.quote'       => '"Ici, le temps ne se mesure pas en heures,<br>mais en parfums d\'olivier, en couchers de soleil sur l\'Etna<br>et en silences que vous n\'oublierez jamais."',
    'exp.author'      => "— Un hôte d'Almaretna",
    'rooms.eyebrow'   => 'Où séjourner',
    'rooms.headline'  => 'Nos <em>chambres</em>',
    'rooms.guests_sfx'=> ' voyageurs',
    'rooms.from'      => 'À partir de',
    'rooms.book'      => 'Réserver',
    'rooms.night'     => '/nuit',
    'story.eyebrow'   => 'Notre histoire',
    'story.headline'  => 'Un coin de<br><em>Sicile authentique</em>',
    'story.text1'     => "Almaretna est une villa de charme nichée dans l'oliveraie sur les pentes de l'Etna, dans le pittoresque village de Nunziata di Mascali. Ici le temps ralentit, les parfums de fleurs d'oranger emplissent l'air et la vue sur le volcan vous laisse sans voix.",
    'story.text2'     => "Chaque chambre est soignée dans les moindres détails pour vous offrir un confort maximum, tandis que notre piscine panoramique devient le point de rencontre parfait entre ciel, Etna et mer.",
    'story.cta'       => 'Découvrir les chambres',
    'story.badge'     => 'Hospitalité',
    'pool.eyebrow'    => 'Notre piscine',
    'pool.headline'   => "Un plongeon<br>au <em>cœur de l'Etna</em>",
    'pool.desc'       => "La piscine d'Almaretna est un espace conçu pour la vraie détente : <strong>12 mètres de long et 5 mètres de large</strong>, avec une profondeur allant jusqu'à <strong>3,5 mètres</strong>. Nichée dans le silence de l'oliveraie avec vue sur l'Etna, c'est l'endroit idéal pour une baignade revitalisante ou pour se prélasser au soleil.",
    'pool.badge'      => 'Longueur de la piscine',
    'pool.spec1'      => 'Surface de natation',
    'pool.spec2_val'  => "jusqu'à 3,5 m",
    'pool.spec2'      => 'Profondeur maximale',
    'pool.bar_title'  => 'Bar au bord de la piscine',
    'pool.bar_desc'   => 'À la disposition des hôtes, le bar est entièrement équipé :',
    'num.rooms'       => 'Chambres exclusives',
    'num.sunsets'     => "Couchers de soleil sur l'Etna",
    'num.sea'         => 'De la mer',
    'num.rating'      => 'Note moyenne',
    'gallery.all'     => 'Voir toutes les photos →',
    'test.eyebrow'    => 'Ce que disent nos hôtes',
    'test.headline'   => 'Histoires de <em>ceux qui étaient là</em>',
    'test.rating'     => 'sur Google &amp; Booking.com',
    'loc.eyebrow'     => 'Comment nous rejoindre',
    'loc.headline'    => 'Au cœur<br>de la <em>Sicile</em>',
    'loc.maps'        => 'Ouvrir dans Google Maps',
    'loc.map_title'   => "Position d'Almaretna",
    'fcta.eyebrow'    => 'Réservation directe',
    'fcta.headline'   => 'Vos vacances<br><em>siciliennes parfaites</em><br>vous attendent',
    'fcta.text'       => "Réservez directement et obtenez le meilleur prix garanti, une annulation flexible et un accueil spécial.",
    'fcta.btn'        => 'Choisir les dates',
    'fcta.tel'        => 'Ou appelez-nous :',
    'fcta.g1'         => 'Meilleur prix garanti',
    'fcta.g2'         => 'Annulation flexible',
    'fcta.g3'         => 'Paiement 100% sécurisé',
    'foot.tagline'    => "Un refuge de luxe sur les pentes de l'Etna, entre nature et bien-être.",
    'foot.explore'    => 'Explorer',
    'foot.our_rooms'  => 'Nos chambres',
    'foot.book'       => 'Réserver',
    'foot.contacts'   => 'Contacts',
    'foot.info'       => 'Informations',
    'foot.cin'        => 'Check-in :',
    'foot.cout'       => 'Check-out :',
    'foot.cin_from'   => 'à partir de ',
    'foot.cout_by'    => 'avant ',
    'foot.pool'       => 'Piscine panoramique',
    'foot.bar'        => 'Bar au bord de la piscine équipé',
    'foot.wifi'       => 'Wi-Fi gratuit',
    'foot.parking'    => 'Parking gratuit',
    'foot.rights'     => 'Tous droits réservés.',
    'foot.payments'   => 'Paiements sécurisés avec',
    'foot.legal'         => 'Informations légales',
    'foot.legal_privacy' => 'Politique de confidentialité',
    'foot.legal_cookie'  => 'Politique de cookies',
    'foot.legal_terms'   => 'Conditions générales',
    'foot.legal_recesso' => 'Droit de rétractation',
    // Page réservation
    'book.checkin_info'    => 'Arrivée à partir de 15h00 &nbsp;·&nbsp; Départ avant 11h00',
    'book.steps_label'     => 'Étapes de réservation',
    'book.step1'           => 'Dates',
    'book.step2'           => 'Chambre',
    'book.step3'           => 'Détails',
    'book.step4'           => 'Paiement',
    'book.secure_pay'      => 'Paiement sécurisé',
    'book.secure_pay_desc' => 'Données cryptées SSL. Paiement géré et protégé par Stripe.',
    'book.direct'          => 'Réservation directe',
    'book.direct_desc'     => 'Sans intermédiaires. Meilleur prix en ligne garanti.',
    'book.instant'         => 'Confirmation immédiate',
    'book.instant_desc'    => 'Recevez la confirmation par e-mail instantanément, dès que le paiement est effectué.',
    'book.page_title'      => 'Réservez votre séjour',
    'seo.prenota_desc'     => 'Réservez directement à Almaretna : vérifiez les disponibilités, paiement sécurisé avec Stripe, meilleur prix garanti.',
    // Page chambres
    'rooms.page_h1'        => 'Chambres',
    'rooms.page_subtitle'  => 'Sept chambres réparties sur trois niveaux, chacune avec sa propre lumière.',
    'rooms.filter'         => 'Filtrer',
    'rooms.type_label'     => 'Type',
    'rooms.all_types'      => 'Toutes',
    'rooms.floor_label'    => 'Étage',
    'rooms.all_floors'     => 'Tous',
    'rooms.guests_label'   => 'Voyageurs',
    'rooms.any'            => 'Indifférent',
    'rooms.guest_1'        => '%d voyageur',
    'rooms.guest_n'        => '%d voyageurs',
    'rooms.reset'          => 'Réinitialiser',
    'rooms.n_avail_1'      => '%d chambre disponible',
    'rooms.n_avail_n'      => '%d chambres disponibles',
    'rooms.cta_title'      => 'Prêt à réserver votre séjour ?',
    'rooms.cta_desc'       => 'Vérifiez la disponibilité et réservez directement en ligne, sans intermédiaires.',
    'rooms.verify_avail'   => 'Vérifier disponibilité',
    // Chambre individuelle
    'room.breadcrumb'      => 'Chambres',
    'room.img_view'        => "Voir l'image",
    'room.the_room'        => 'La chambre',
    'room.amenities'       => 'Équipements',
    'room.stay_details'    => 'Détails du séjour',
    'room.adults_1'        => 'Max %d adulte',
    'room.adults_n'        => 'Max %d adultes',
    'room.children_1'      => '%d enfant',
    'room.children_n'      => '%d enfants',
    'room.min_stay_n'      => 'Séjour minimum %d nuits',
    'room.min_stay_1'      => 'Séjour minimum 1 nuit',
    'room.checkin_time'    => 'Arrivée : 15h00',
    'room.checkout_time'   => 'Départ : 11h00',
    'room.availability'    => 'Disponibilité de la chambre',
    'room.related'         => 'Chambres similaires',
    'room.per_night'       => '/ nuit',
    'room.discover'        => 'Découvrir',
    'sc.no_rooms'          => 'Aucune chambre disponible avec les filtres sélectionnés.',
    'sc.price_tag'         => 'dès €%s/nuit',
    'sc.cal_prev'          => 'Mois précédent',
    'sc.cal_next'          => 'Mois suivant',
    'sc.cal_title'         => 'Disponibilité',
    'sc.cal_loading'       => 'Chargement du calendrier de disponibilité…',
    'sc.up_to_adults'      => "Jusqu'à %d adultes",
    'sc.children'          => 'enfants',
    'sc.min_stay'          => 'Séjour minimum : %d nuits',
    'sc.date_ph'           => 'jj/mm/aaaa',
    'sc.secure_cancel'     => 'Paiement sécurisé. Annulation gratuite.',
    'sc.no_extra'          => 'Paiement sécurisé. Aucun frais supplémentaire.',
    'h.floor_1'            => 'Étage 1',
    'h.floor_2'            => 'Étage 2',
    'h.floor_m'            => 'Mansarde',
    'h.shared_bath_with'   => 'Salle de bain partagée avec la chambre adjacente (%s).',
    'h.shared_bath'        => 'Salle de bain partagée avec la chambre adjacente.',
    'wiz.when_stay'        => 'Quand souhaitez-vous séjourner ?',
    'wiz.checkin'          => 'Arrivée',
    'wiz.checkout'         => 'Départ',
    'wiz.adults'           => 'Adultes',
    'wiz.children'         => 'Enfants (0-12 ans)',
    'wiz.search'           => 'Rechercher des chambres disponibles',
    'wiz.choose_room'      => 'Choisissez votre chambre',
    'wiz.searching'        => 'Recherche en cours…',
    'wiz.back_dates'       => 'Modifier les dates',
    'wiz.your_data'        => 'Vos coordonnées',
    'wiz.first_name'       => 'Prénom',
    'wiz.last_name'        => 'Nom de famille',
    'wiz.email'            => 'E-mail',
    'wiz.phone'            => 'Téléphone',
    'wiz.extras'           => 'Services optionnels',
    'wiz.special_req'      => 'Demandes spéciales',
    'wiz.optional'         => 'facultatif',
    'wiz.req_ph'           => "Allergies, heure d'arrivée prévue, besoins particuliers…",
    'wiz.i_accept'         => "J'ai lu et j'accepte la",
    'wiz.privacy_policy'   => 'Politique de confidentialité',
    'wiz.cancel_policy'    => "Politique d'annulation",
    'wiz.summary'          => 'Récapitulatif',
    'wiz.nights'           => 'Nuits',
    'wiz.guests'           => 'Voyageurs',
    'wiz.room_price'       => 'Prix de la chambre',
    'wiz.extras_label'     => 'Extras',
    'wiz.total'            => 'Total',
    'wiz.back_rooms'       => 'Retour au choix de chambre',
    'wiz.proceed_pay'      => 'Procéder au paiement',
    'wiz.payment'          => 'Paiement',
    'wiz.room_label'       => 'Chambre',
    'wiz.dates'            => 'Dates',
    'wiz.total_pay'        => 'Total à payer',
    'wiz.pay_loading'      => 'Chargement du formulaire de paiement…',
    'wiz.pay_secure'       => 'Paiement sécurisé via Stripe. Vos données sont chiffrées.',
    'wiz.your_booking'     => 'Votre réservation',
    'wiz.pay_now'          => 'Payer maintenant',
    'wiz.back_data'        => 'Modifier les données',
    'wiz.confirmed'        => 'Réservation confirmée !',
    'wiz.confirm_email'    => "Vous recevrez un e-mail de confirmation à l'adresse indiquée.",
    'wiz.checkin_inst'     => 'Instructions check-in :',
    'wiz.checkin_times'    => 'Arrivée à partir de 15h00 — Départ avant 11h00',
    'wiz.address_label'    => 'Adresse :',
    'wiz.back_home'        => "Retour à l'accueil",
    'wiz.form_unavailable' => 'Le formulaire de réservation est en cours de configuration. Revenez bientôt !',
    'js.calculating'       => 'Calcul en cours...',
    'js.dates_unavail'     => 'Dates non disponibles.',
    'js.night_1'           => 'nuit',
    'js.night_n'           => 'nuits',
    'js.selected'          => 'sélectionnées',
    'js.conn_error'        => 'Erreur de connexion.',
],

/* ─── ESPAÑOL ──────────────────────────────────────────────── */
'es' => [
    'nav.book_now'    => 'Reservar ahora',
    'nav.rooms'       => 'Habitaciones',
    'nav.open_menu'   => 'Abrir menú',
    'nav.close_menu'  => 'Cerrar menú',
    'nav.main_menu'   => 'Menú principal',
    'nav.mobile_menu' => 'Menú móvil',
    'cta.book'        => 'Reserva tu estancia',
    'cta.rooms'       => 'Descubre las habitaciones',
    'cta.all_rooms'   => 'Todas las habitaciones',
    'cta.choose'      => 'Elegir fechas',
    'cta.book_exp'    => 'Reserva tu experiencia',
    'cta.maps'        => 'Abrir en Google Maps',
    'hero.eyebrow'    => 'Nunziata di Mascali &nbsp;·&nbsp; Etna · Sicilia',
    'hero.scroll'     => 'Desplazar',
    'hero.title'      => "Un refugio donde el Etna\ntoca el cielo",
    'hero.subtitle'   => "Villa con piscina panorámica en las faldas del volcán, entre olivos y silencio",
    'strip.checkin'   => 'Llegada',
    'strip.checkout'  => 'Salida',
    'strip.guests'    => 'Huéspedes',
    'strip.date_ph'   => 'Elige la fecha',
    'strip.a1'        => '1 adulto',
    'strip.a2'        => '2 adultos',
    'strip.a3'        => '3 adultos',
    'strip.a4'        => '4 adultos',
    'strip.a5'        => '5+ adultos',
    'strip.verify'    => 'Comprobar disponibilidad',
    'exp.eyebrow'     => 'La experiencia',
    'exp.quote'       => '"Aquí el tiempo no se mide en horas,<br>sino en aromas de olivo, en atardeceres sobre el Etna<br>y en silencios que nunca olvidarás."',
    'exp.author'      => '— Un huésped de Almaretna',
    'rooms.eyebrow'   => 'Dónde alojarse',
    'rooms.headline'  => 'Nuestras <em>habitaciones</em>',
    'rooms.guests_sfx'=> ' huéspedes',
    'rooms.from'      => 'Desde',
    'rooms.book'      => 'Reservar',
    'rooms.night'     => '/noche',
    'story.eyebrow'   => 'Nuestra historia',
    'story.headline'  => 'Un rincón de<br><em>Sicilia auténtica</em>',
    'story.text1'     => "Almaretna es una villa de encanto inmersa en el olivar en las faldas del Etna, en el pintoresco pueblo de Nunziata di Mascali. Aquí el tiempo se ralentiza, los aromas de azahar llenan el aire y la vista del volcán te deja sin palabras.",
    'story.text2'     => "Cada habitación está cuidada en los detalles para ofrecerte el máximo confort, mientras que nuestra piscina panorámica se convierte en el punto de encuentro perfecto entre el cielo, el Etna y el mar.",
    'story.cta'       => 'Descubre las habitaciones',
    'story.badge'     => 'Hospitalidad',
    'pool.eyebrow'    => 'Nuestra piscina',
    'pool.headline'   => 'Un chapuzón<br>en el <em>corazón del Etna</em>',
    'pool.desc'       => "La piscina de Almaretna es un espacio pensado para el verdadero relax: <strong>12 metros de largo y 5 de ancho</strong>, con una profundidad de hasta <strong>3,5 metros</strong>. Inmersa en el silencio del olivar con vistas al Etna, es el lugar perfecto para un refrescante baño o para descansar al sol.",
    'pool.badge'      => 'Longitud de la piscina',
    'pool.spec1'      => 'Área de natación',
    'pool.spec2_val'  => 'hasta 3,5 m',
    'pool.spec2'      => 'Profundidad máxima',
    'pool.bar_title'  => 'Bar junto a la piscina',
    'pool.bar_desc'   => 'A disposición de los huéspedes, el bar está completamente equipado:',
    'num.rooms'       => 'Habitaciones exclusivas',
    'num.sunsets'     => 'Atardeceres en el Etna',
    'num.sea'         => 'Del mar',
    'num.rating'      => 'Valoración media',
    'gallery.all'     => 'Ver todas las fotos →',
    'test.eyebrow'    => 'Lo que dicen nuestros huéspedes',
    'test.headline'   => 'Historias de <em>quienes estuvieron</em>',
    'test.rating'     => 'en Google &amp; Booking.com',
    'loc.eyebrow'     => 'Cómo llegar',
    'loc.headline'    => 'En el corazón<br>de <em>Sicilia</em>',
    'loc.maps'        => 'Abrir en Google Maps',
    'loc.map_title'   => 'Ubicación de Almaretna',
    'fcta.eyebrow'    => 'Reserva directa',
    'fcta.headline'   => 'Tu perfecta<br><em>vacación siciliana</em><br>te espera',
    'fcta.text'       => 'Reserva directamente y obtén el mejor precio garantizado, cancelación flexible y una bienvenida especial.',
    'fcta.btn'        => 'Elegir fechas',
    'fcta.tel'        => 'O llámanos:',
    'fcta.g1'         => 'Mejor precio garantizado',
    'fcta.g2'         => 'Cancelación flexible',
    'fcta.g3'         => 'Pago 100% seguro',
    'foot.tagline'    => 'Un refugio de lujo en las faldas del Etna, entre naturaleza y bienestar.',
    'foot.explore'    => 'Explorar',
    'foot.our_rooms'  => 'Nuestras habitaciones',
    'foot.book'       => 'Reservar',
    'foot.contacts'   => 'Contacto',
    'foot.info'       => 'Información',
    'foot.cin'        => 'Check-in:',
    'foot.cout'       => 'Check-out:',
    'foot.cin_from'   => 'desde las ',
    'foot.cout_by'    => 'antes de las ',
    'foot.pool'       => 'Piscina panorámica',
    'foot.bar'        => 'Bar junto a la piscina equipado',
    'foot.wifi'       => 'Wi-Fi gratuito',
    'foot.parking'    => 'Aparcamiento gratuito',
    'foot.rights'     => 'Todos los derechos reservados.',
    'foot.payments'   => 'Pagos seguros con',
    'foot.legal'         => 'Legal',
    'foot.legal_privacy' => 'Política de privacidad',
    'foot.legal_cookie'  => 'Política de cookies',
    'foot.legal_terms'   => 'Términos y condiciones',
    'foot.legal_recesso' => 'Derecho de desistimiento',
    // Página reserva
    'book.checkin_info'    => 'Llegada a partir de las 15:00 &nbsp;·&nbsp; Salida antes de las 11:00',
    'book.steps_label'     => 'Pasos de reserva',
    'book.step1'           => 'Fechas',
    'book.step2'           => 'Habitación',
    'book.step3'           => 'Datos',
    'book.step4'           => 'Pago',
    'book.secure_pay'      => 'Pago seguro',
    'book.secure_pay_desc' => 'Datos encriptados SSL. Pago gestionado y protegido por Stripe.',
    'book.direct'          => 'Reserva directa',
    'book.direct_desc'     => 'Sin intermediarios. Mejor precio online garantizado.',
    'book.instant'         => 'Confirmación inmediata',
    'book.instant_desc'    => 'Recibe la confirmación por e-mail al instante, en cuanto se complete el pago.',
    'book.page_title'      => 'Reserve su estancia',
    'seo.prenota_desc'     => 'Reserve directamente en Almaretna: compruebe disponibilidad, pago seguro con Stripe, mejor precio garantizado.',
    // Página habitaciones
    'rooms.page_h1'        => 'Habitaciones',
    'rooms.page_subtitle'  => 'Siete habitaciones distribuidas en tres niveles, cada una con su propia luz.',
    'rooms.filter'         => 'Filtrar',
    'rooms.type_label'     => 'Tipo',
    'rooms.all_types'      => 'Todas',
    'rooms.floor_label'    => 'Planta',
    'rooms.all_floors'     => 'Todas',
    'rooms.guests_label'   => 'Huéspedes',
    'rooms.any'            => 'Cualquiera',
    'rooms.guest_1'        => '%d huésped',
    'rooms.guest_n'        => '%d huéspedes',
    'rooms.reset'          => 'Restablecer',
    'rooms.n_avail_1'      => '%d habitación disponible',
    'rooms.n_avail_n'      => '%d habitaciones disponibles',
    'rooms.cta_title'      => '¿Listo para reservar tu estancia?',
    'rooms.cta_desc'       => 'Comprueba la disponibilidad y reserva directamente online, sin intermediarios.',
    'rooms.verify_avail'   => 'Comprobar disponibilidad',
    // Habitación individual
    'room.breadcrumb'      => 'Habitaciones',
    'room.img_view'        => 'Ver imagen',
    'room.the_room'        => 'La habitación',
    'room.amenities'       => 'Equipamiento',
    'room.stay_details'    => 'Detalles de la estancia',
    'room.adults_1'        => 'Máx. %d adulto',
    'room.adults_n'        => 'Máx. %d adultos',
    'room.children_1'      => '%d niño',
    'room.children_n'      => '%d niños',
    'room.min_stay_n'      => 'Estancia mínima de %d noches',
    'room.min_stay_1'      => 'Estancia mínima de 1 noche',
    'room.checkin_time'    => 'Check-in: 15:00 h',
    'room.checkout_time'   => 'Check-out: 11:00 h',
    'room.availability'    => 'Disponibilidad de la habitación',
    'room.related'         => 'Habitaciones similares',
    'room.per_night'       => '/ noche',
    'room.discover'        => 'Descubrir',
    'sc.no_rooms'          => 'No hay habitaciones disponibles con los filtros seleccionados.',
    'sc.price_tag'         => 'desde €%s/noche',
    'sc.cal_prev'          => 'Mes anterior',
    'sc.cal_next'          => 'Mes siguiente',
    'sc.cal_title'         => 'Disponibilidad',
    'sc.cal_loading'       => 'Cargando calendario de disponibilidad…',
    'sc.up_to_adults'      => 'Hasta %d adultos',
    'sc.children'          => 'niños',
    'sc.min_stay'          => 'Estancia mínima: %d noches',
    'sc.date_ph'           => 'dd/mm/aaaa',
    'sc.secure_cancel'     => 'Pago seguro. Cancelación gratuita.',
    'sc.no_extra'          => 'Pago seguro. Sin cargos adicionales.',
    'h.floor_1'            => 'Planta 1',
    'h.floor_2'            => 'Planta 2',
    'h.floor_m'            => 'Ático',
    'h.shared_bath_with'   => 'Baño compartido con la habitación adyacente (%s).',
    'h.shared_bath'        => 'Baño compartido con la habitación adyacente.',
    'wiz.when_stay'        => '¿Cuándo le gustaría alojarse?',
    'wiz.checkin'          => 'Llegada',
    'wiz.checkout'         => 'Salida',
    'wiz.adults'           => 'Adultos',
    'wiz.children'         => 'Niños (0-12 años)',
    'wiz.search'           => 'Buscar habitaciones disponibles',
    'wiz.choose_room'      => 'Elige tu habitación',
    'wiz.searching'        => 'Buscando…',
    'wiz.back_dates'       => 'Cambiar fechas',
    'wiz.your_data'        => 'Sus datos',
    'wiz.first_name'       => 'Nombre',
    'wiz.last_name'        => 'Apellido',
    'wiz.email'            => 'E-mail',
    'wiz.phone'            => 'Teléfono',
    'wiz.extras'           => 'Servicios opcionales',
    'wiz.special_req'      => 'Peticiones especiales',
    'wiz.optional'         => 'opcional',
    'wiz.req_ph'           => 'Alergias, hora de llegada prevista, necesidades especiales…',
    'wiz.i_accept'         => 'He leído y acepto la',
    'wiz.privacy_policy'   => 'Política de privacidad',
    'wiz.cancel_policy'    => 'Política de cancelación',
    'wiz.summary'          => 'Resumen',
    'wiz.nights'           => 'Noches',
    'wiz.guests'           => 'Huéspedes',
    'wiz.room_price'       => 'Precio habitación',
    'wiz.extras_label'     => 'Extras',
    'wiz.total'            => 'Total',
    'wiz.back_rooms'       => 'Volver a selección de habitación',
    'wiz.proceed_pay'      => 'Proceder al pago',
    'wiz.payment'          => 'Pago',
    'wiz.room_label'       => 'Habitación',
    'wiz.dates'            => 'Fechas',
    'wiz.total_pay'        => 'Total a pagar',
    'wiz.pay_loading'      => 'Cargando formulario de pago…',
    'wiz.pay_secure'       => 'Pago seguro a través de Stripe. Sus datos están cifrados.',
    'wiz.your_booking'     => 'Su reserva',
    'wiz.pay_now'          => 'Pagar ahora',
    'wiz.back_data'        => 'Editar datos',
    'wiz.confirmed'        => '¡Reserva confirmada!',
    'wiz.confirm_email'    => 'Recibirá un correo de confirmación en breve en la dirección indicada.',
    'wiz.checkin_inst'     => 'Instrucciones de check-in:',
    'wiz.checkin_times'    => 'Check-in desde las 15:00 — Check-out antes de las 11:00',
    'wiz.address_label'    => 'Dirección:',
    'wiz.back_home'        => 'Volver al inicio',
    'wiz.form_unavailable' => 'El formulario de reserva está en configuración. ¡Vuelve pronto!',
    'js.calculating'       => 'Calculando...',
    'js.dates_unavail'     => 'Fechas no disponibles.',
    'js.night_1'           => 'noche',
    'js.night_n'           => 'noches',
    'js.selected'          => 'seleccionadas',
    'js.conn_error'        => 'Error de conexión.',
],

    ]; // end return
}

/* ═══════════════════════════════════════════════════════════════
   HELPER PRINCIPALE
   ═══════════════════════════════════════════════════════════════ */

function alm_t(string $key, string $lang = ''): string {
    static $cache = [];
    if (!$lang) $lang = alm_get_lang();
    if (!isset($cache[$lang])) {
        $all = alm_get_strings();
        $cache[$lang] = array_merge($all['it'] ?? [], $all[$lang] ?? []);
    }
    return $cache[$lang][$key] ?? $key;
}

/* ═══════════════════════════════════════════════════════════════
   ARRAY TRADOTTI — contenuti strutturati
   ═══════════════════════════════════════════════════════════════ */

function alm_i18n_amenities_bar(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [
            ["Piscina panoramica",  "Vista sull'Etna e sul mare"],
            ['Vista vulcano',       "A 20 min dall'Etna"],
            ['Bar bordo piscina',   'Cucina, forno, caffè'],
            ['Wi-Fi gratuito',      'In tutta la villa'],
            ['Pagamento sicuro',    'Stripe · SSL certificato'],
        ],
        'en' => [
            ['Panoramic pool',      'View of Etna and the sea'],
            ['Volcano view',        '20 min from Etna'],
            ['Poolside bar',        'Kitchen, oven, coffee'],
            ['Free Wi-Fi',          'Throughout the villa'],
            ['Secure payment',      'Stripe · SSL certified'],
        ],
        'de' => [
            ['Panoramabecken',      'Blick auf Ätna und Meer'],
            ['Vulkanblick',         '20 Min. vom Ätna'],
            ['Pool-Bar',            'Küche, Ofen, Kaffee'],
            ['Kostenloses WLAN',    'In der gesamten Villa'],
            ['Sichere Zahlung',     'Stripe · SSL zertifiziert'],
        ],
        'fr' => [
            ['Piscine panoramique', "Vue sur l'Etna et la mer"],
            ['Vue sur le volcan',   "À 20 min de l'Etna"],
            ['Bar au bord de la piscine', 'Cuisine, four, café'],
            ['Wi-Fi gratuit',       'Dans toute la villa'],
            ['Paiement sécurisé',   'Stripe · SSL certifié'],
        ],
        'es' => [
            ['Piscina panorámica',  'Vista del Etna y el mar'],
            ['Vista del volcán',    '20 min del Etna'],
            ['Bar junto a la piscina', 'Cocina, horno, café'],
            ['Wi-Fi gratuito',      'En toda la villa'],
            ['Pago seguro',         'Stripe · SSL certificado'],
        ],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_pills(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [['🌋','Vista Etna'],['🫒','Uliveto secolare'],['🏊','Piscina panoramica'],['🌊','10 min dal mare'],['🌅','Tramonti indimenticabili'],['📶','Wi-Fi gratuito'],['🌿','Aria pulita di montagna'],['⭐','Ospitalità siciliana autentica']],
        'en' => [['🌋','Etna view'],['🫒','Ancient olive grove'],['🏊','Panoramic pool'],['🌊','10 min from the sea'],['🌅','Unforgettable sunsets'],['📶','Free Wi-Fi'],['🌿','Clean mountain air'],['⭐','Authentic Sicilian hospitality']],
        'de' => [['🌋','Ätna-Blick'],['🫒','Jahrhundertealter Olivenhain'],['🏊','Panoramabecken'],['🌊','10 Min. vom Meer'],['🌅','Unvergessliche Sonnenuntergänge'],['📶','Kostenloses WLAN'],['🌿','Klare Bergluft'],['⭐','Echte sizilianische Gastfreundschaft']],
        'fr' => [['🌋',"Vue sur l'Etna"],['🫒','Oliveraie séculaire'],['🏊','Piscine panoramique'],['🌊','10 min de la mer'],['🌅','Couchers de soleil inoubliables'],['📶','Wi-Fi gratuit'],['🌿','Air pur de montagne'],['⭐','Hospitalité sicilienne authentique']],
        'es' => [['🌋','Vista del Etna'],['🫒','Olivar secular'],['🏊','Piscina panorámica'],['🌊','10 min del mar'],['🌅','Atardeceres inolvidables'],['📶','Wi-Fi gratuito'],['🌿','Aire limpio de montaña'],['⭐','Hospitalidad siciliana auténtica']],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_story_checks(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [
            'Piscina con vista panoramica sul vulcano e sul mare',
            'Bar bordo piscina attrezzato con cucina e forno',
            'A 10 minuti dalle spiagge di Fondachello',
            'Check-in flessibile · Assistenza h24',
            'Prenotazione diretta — miglior prezzo garantito',
        ],
        'en' => [
            'Pool with panoramic view of the volcano and sea',
            'Fully equipped poolside bar with kitchen and oven',
            '10 minutes from Fondachello beaches',
            'Flexible check-in · 24h support',
            'Direct booking — best price guaranteed',
        ],
        'de' => [
            'Pool mit Panoramablick auf Vulkan und Meer',
            'Vollausgestattete Pool-Bar mit Küche und Ofen',
            '10 Minuten von den Stränden Fondachello',
            'Flexibler Check-in · 24h-Support',
            'Direktbuchung — bester Preis garantiert',
        ],
        'fr' => [
            'Piscine avec vue panoramique sur le volcan et la mer',
            "Bar au bord de la piscine équipé avec cuisine et four",
            'À 10 minutes des plages de Fondachello',
            'Check-in flexible · Assistance 24h/24',
            'Réservation directe — meilleur prix garanti',
        ],
        'es' => [
            'Piscina con vista panorámica del volcán y el mar',
            'Bar junto a la piscina equipado con cocina y horno',
            'A 10 minutos de las playas de Fondachello',
            'Check-in flexible · Asistencia 24h',
            'Reserva directa — mejor precio garantizado',
        ],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_pool_dotazioni(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => ['Lavello e piano di appoggio','2 servizi igienici dedicati','Punto cottura','Forno elettrico','Macchina del caffè a cialde','Frigorifero'],
        'en' => ['Sink and worktop','2 dedicated toilets','Cooking area','Electric oven','Pod coffee machine','Refrigerator'],
        'de' => ['Waschbecken und Arbeitsfläche','2 separate WCs','Kochstelle','Elektrischer Backofen','Kapselkaffeemaschine','Kühlschrank'],
        'fr' => ['Évier et plan de travail','2 toilettes dédiées','Coin cuisine','Four électrique','Machine à café à dosettes','Réfrigérateur'],
        'es' => ['Fregadero y encimera','2 aseos dedicados','Zona de cocción','Horno eléctrico','Cafetera de cápsulas','Nevera'],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_numbers(): array {
    $lang       = alm_get_lang();
    $room_count = (string) wp_count_posts('almaretna_room')->publish;
    $data = [
        'it' => [[$room_count,'Camere esclusive'],['∞',"Tramonti sull'Etna"],["10'",'Dal mare'],['★ 4.9','Valutazione media']],
        'en' => [[$room_count,'Exclusive rooms'],['∞','Etna sunsets'],["10'",'From the sea'],['★ 4.9','Average rating']],
        'de' => [[$room_count,'Exklusive Zimmer'],['∞','Ätna-Sonnenuntergänge'],["10'",'Vom Meer'],['★ 4.9','Durchschnittsbewertung']],
        'fr' => [[$room_count,'Chambres exclusives'],['∞',"Couchers de soleil sur l'Etna"],["10'",'De la mer'],['★ 4.9','Note moyenne']],
        'es' => [[$room_count,'Habitaciones exclusivas'],['∞','Atardeceres en el Etna'],["10'",'Del mar'],['★ 4.9','Valoración media']],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_distances(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [['✈️','Aeroporto di Catania','30 min'],['🌊','Spiagge di Fondachello','10 min'],['🏛️','Taormina','25 min'],['🌋','Etna Nord – accesso','20 min'],['🚂','Stazione Mascali','5 min'],['🛒','Centro commerciale','8 min']],
        'en' => [['✈️','Catania Airport','30 min'],['🌊','Fondachello beaches','10 min'],['🏛️','Taormina','25 min'],['🌋','Etna Nord – access','20 min'],['🚂','Mascali station','5 min'],['🛒','Shopping centre','8 min']],
        'de' => [['✈️','Flughafen Catania','30 Min.'],['🌊','Strände von Fondachello','10 Min.'],['🏛️','Taormina','25 Min.'],['🌋','Ätna Nord – Zugang','20 Min.'],['🚂','Bahnhof Mascali','5 Min.'],['🛒','Einkaufszentrum','8 Min.']],
        'fr' => [['✈️','Aéroport de Catane','30 min'],['🌊','Plages de Fondachello','10 min'],['🏛️','Taormine','25 min'],['🌋','Etna Nord – accès','20 min'],['🚂','Gare de Mascali','5 min'],['🛒','Centre commercial','8 min']],
        'es' => [['✈️','Aeropuerto de Catania','30 min'],['🌊','Playas de Fondachello','10 min'],['🏛️','Taormina','25 min'],['🌋','Etna Norte – acceso','20 min'],['🚂','Estación de Mascali','5 min'],['🛒','Centro comercial','8 min']],
    ];
    return $data[$lang] ?? $data['it'];
}

function alm_i18n_reviews(): array {
    $lang = alm_get_lang();
    $data = [
        'it' => [
            ['text' => "Un posto fuori dal tempo. La piscina con vista sull'Etna al tramonto è qualcosa che non dimenticherò mai. L'uliveto, il silenzio, i colori della Sicilia — tornerei subito.", 'name' => 'Martina B.', 'origin' => 'Milano', 'init' => 'M', 'stars' => 5],
            ['text' => "Almaretna è esattamente quello che cercavamo: pace, natura autentica, ospitalità vera. Siamo rimasti una settimana e torneremo sicuramente. La camera era curatissima.", 'name' => 'François & Sophie', 'origin' => 'Parigi, Francia', 'init' => 'F', 'stars' => 5],
            ['text' => "La posizione è imbattibile: il mare a 10 minuti, l'Etna praticamente in casa. La struttura è pulita, elegante e gli host sono disponibilissimi. Consiglio vivamente.", 'name' => 'Roberto C.', 'origin' => 'Roma', 'init' => 'R', 'stars' => 5],
        ],
        'en' => [
            ['text' => "A place outside of time. The pool with a view of Etna at sunset is something I will never forget. The olive grove, the silence, the colours of Sicily — I would go back straight away.", 'name' => 'Martina B.', 'origin' => 'Milan', 'init' => 'M', 'stars' => 5],
            ['text' => "Almaretna is exactly what we were looking for: peace, authentic nature, genuine hospitality. We stayed a week and will definitely return. The room was immaculate.", 'name' => 'François & Sophie', 'origin' => 'Paris, France', 'init' => 'F', 'stars' => 5],
            ['text' => "The location is unbeatable: the sea 10 minutes away, Etna practically on your doorstep. The property is clean, elegant and the hosts are extremely helpful. Highly recommended.", 'name' => 'Roberto C.', 'origin' => 'Rome', 'init' => 'R', 'stars' => 5],
        ],
        'de' => [
            ['text' => "Ein Ort außerhalb der Zeit. Der Pool mit Blick auf den Ätna bei Sonnenuntergang ist etwas, das ich nie vergessen werde. Der Olivenhain, die Stille, die Farben Siziliens — ich würde sofort zurückkehren.", 'name' => 'Martina B.', 'origin' => 'Mailand', 'init' => 'M', 'stars' => 5],
            ['text' => "Almaretna ist genau das, was wir gesucht haben: Ruhe, authentische Natur, echte Gastfreundschaft. Wir sind eine Woche geblieben und werden definitiv wiederkommen. Das Zimmer war tadellos.", 'name' => 'François & Sophie', 'origin' => 'Paris, Frankreich', 'init' => 'F', 'stars' => 5],
            ['text' => "Die Lage ist unschlagbar: das Meer 10 Minuten entfernt, der Ätna quasi vor der Haustür. Die Anlage ist sauber, elegant und die Gastgeber sind überaus hilfsbereit. Sehr empfehlenswert.", 'name' => 'Roberto C.', 'origin' => 'Rom', 'init' => 'R', 'stars' => 5],
        ],
        'fr' => [
            ['text' => "Un endroit hors du temps. La piscine avec vue sur l'Etna au coucher du soleil est quelque chose que je n'oublierai jamais. L'oliveraie, le silence, les couleurs de la Sicile — j'y retournerais tout de suite.", 'name' => 'Martina B.', 'origin' => 'Milan', 'init' => 'M', 'stars' => 5],
            ['text' => "Almaretna est exactement ce que nous cherchions : paix, nature authentique, hospitalité vraie. Nous sommes restés une semaine et reviendrons certainement. La chambre était parfaite.", 'name' => 'François & Sophie', 'origin' => 'Paris, France', 'init' => 'F', 'stars' => 5],
            ['text' => "L'emplacement est imbattable : la mer à 10 minutes, l'Etna pratiquement à la maison. La structure est propre, élégante et les hôtes sont très disponibles. Je recommande vivement.", 'name' => 'Roberto C.', 'origin' => 'Rome', 'init' => 'R', 'stars' => 5],
        ],
        'es' => [
            ['text' => "Un lugar fuera del tiempo. La piscina con vistas al Etna al atardecer es algo que nunca olvidaré. El olivar, el silencio, los colores de Sicilia — volvería de inmediato.", 'name' => 'Martina B.', 'origin' => 'Milán', 'init' => 'M', 'stars' => 5],
            ['text' => "Almaretna es exactamente lo que buscábamos: paz, naturaleza auténtica, hospitalidad genuina. Nos quedamos una semana y volveremos sin duda. La habitación era impecable.", 'name' => 'François & Sophie', 'origin' => 'París, Francia', 'init' => 'F', 'stars' => 5],
            ['text' => "La ubicación es inmejorable: el mar a 10 minutos, el Etna prácticamente en casa. El alojamiento es limpio, elegante y los anfitriones son muy serviciales. Muy recomendable.", 'name' => 'Roberto C.', 'origin' => 'Roma', 'init' => 'R', 'stars' => 5],
        ],
    ];
    return $data[$lang] ?? $data['it'];
}

/* ═══════════════════════════════════════════════════════════════
   SELETTORE LINGUA
   ═══════════════════════════════════════════════════════════════ */

function alm_lang_switcher_html(): string {
    $current = alm_get_lang();
    $langs   = [
        'it' => ['🇮🇹', 'Italiano'],
        'en' => ['🇬🇧', 'English'],
        'de' => ['🇩🇪', 'Deutsch'],
        'fr' => ['🇫🇷', 'Français'],
        'es' => ['🇪🇸', 'Español'],
    ];
    $base = remove_query_arg('lang');
    ob_start();
    ?>
    <div class="alm-lang" id="almLang">
        <button class="alm-lang__btn"
                onclick="document.getElementById('almLang').classList.toggle('open')"
                aria-haspopup="true"
                aria-expanded="false">
            <span><?php echo $langs[$current][0]; ?> <span class="alm-lang__code"><?php echo strtoupper($current); ?></span></span>
            <svg class="alm-lang__arrow" width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <ul class="alm-lang__list" role="menu">
            <?php foreach ($langs as $code => [$flag, $name]) :
                $url = add_query_arg('lang', $code, $base);
            ?>
            <li role="none">
                <a href="<?php echo esc_url($url); ?>"
                   role="menuitem"
                   class="alm-lang__item<?php echo $code === $current ? ' alm-lang__item--active' : ''; ?>">
                    <?php echo $flag; ?> <?php echo esc_html($name); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
    return (string) ob_get_clean();
}

/* ═══════════════════════════════════════════════════════════════
   URL HELPER — aggiunge ?lang=XX ai link interni
   ═══════════════════════════════════════════════════════════════ */

function alm_url_with_lang(string $url): string {
    $lang = alm_get_lang();
    if ($lang === 'it') return $url;
    return add_query_arg('lang', $lang, $url);
}

// Traduce i titoli delle voci di menu basandosi sul template della pagina
add_filter('wp_nav_menu_objects', function (array $items, $args): array {
    $lang = alm_get_lang();
    if ($lang === 'it') return $items;
    $map = [
        'templates/template-rooms.php'   => alm_t('nav.rooms'),
        'templates/template-booking.php' => alm_t('nav.book_now'),
    ];
    foreach ($items as $item) {
        if ($item->object !== 'page') continue;
        $tpl = get_page_template_slug((int) $item->object_id);
        if (isset($map[$tpl])) $item->title = $map[$tpl];
    }
    return $items;
}, 10, 2);

// Aggiunge ?lang=XX a tutti i link del menu WordPress
add_filter('nav_menu_link_attributes', function (array $atts, $item, $args, $depth): array {
    $lang = alm_get_lang();
    if ($lang === 'it' || empty($atts['href'])) return $atts;
    $parsed    = parse_url($atts['href']);
    $home_host = parse_url(home_url(), PHP_URL_HOST);
    $is_local  = empty($parsed['host']) || $parsed['host'] === $home_host;
    if ($is_local) {
        $atts['href'] = add_query_arg('lang', $lang, $atts['href']);
    }
    return $atts;
}, 10, 4);

/* ═══════════════════════════════════════════════════════════════
   CSS SELETTORE — inline in wp_head
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_head', function (): void { ?>
<style id="alm-lang-css">
.alm-lang { position:relative; }
.alm-lang__btn {
    display: flex;
    align-items: center;
    gap: 5px;
    background: transparent !important;
    border: 1.5px solid #C5B49C !important;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: .75rem;
    font-weight: 600;
    color: #5C5246 !important;
    cursor: pointer;
    white-space: nowrap;
    transition: border-color .2s, color .2s;
    outline: none !important;
    box-shadow: none !important;
    text-decoration: none !important;
}
.alm-lang__btn:hover,
.alm-lang__btn:focus { border-color: #5C5246 !important; color: #3a3028 !important; outline: none !important; }
.alm-lang__code { letter-spacing: .06em; }
.alm-lang__arrow { transition: transform .2s; flex-shrink: 0; }
.alm-lang.open .alm-lang__arrow { transform: rotate(180deg); }
.alm-lang__list {
    display: none;
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    background: #fff;
    border: 1px solid #EFEBE4;
    border-radius: 8px;
    padding: 4px 0;
    min-width: 130px;
    list-style: none;
    margin: 0;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    z-index: 9999;
}
.alm-lang.open .alm-lang__list { display: block; }
.alm-lang__item {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    font-size: .8rem;
    font-weight: 500;
    color: #1a1e22 !important;
    text-decoration: none !important;
    transition: background .15s;
}
.alm-lang__item:hover { background: #F9F7F3; }
.alm-lang__item--active { font-weight: 700; color: #C5B49C !important; }
/* Chiude al click fuori */
.alm-lang-backdrop {
    display: none;
    position: fixed; inset: 0; z-index: 9998;
}
.alm-lang.open ~ .alm-lang-backdrop,
.alm-lang.open + .alm-lang-backdrop { display: block; }
</style>
<script>
document.addEventListener('click', function(e) {
    var sw = document.getElementById('almLang');
    if (sw && !sw.contains(e.target)) sw.classList.remove('open');
});
</script>
<?php }, 99);

/* ═══════════════════════════════════════════════════════════════
   HERO TITLE — dimensione ridotta per lingue non-IT (20%) e DE (25%)
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_head', function (): void {
    $lang = alm_get_lang();
    // Base −20% già in premium.css / responsive.css; qui solo l'extra −5% per DE
    if ($lang !== 'de') return;
    echo '<style id="alm-hero-title-size">'
       . '.hero-premium__title{font-size:clamp(1.7rem,3.75vw,3.2rem)!important}'
       . '@media(max-width:767px){.hero-premium__title{font-size:clamp(1.4rem,5.25vw,2.1rem)!important}}'
       . '@media(max-width:480px){.hero-premium__title{font-size:1.4rem!important}}'
       . '</style>' . "\n";
}, 20);

/* ═══════════════════════════════════════════════════════════════
   LANG ATTRIBUTE HTML
   ═══════════════════════════════════════════════════════════════ */

add_filter('language_attributes', function (string $output): string {
    $map = ['it' => 'it-IT', 'en' => 'en-US', 'de' => 'de-DE', 'fr' => 'fr-FR', 'es' => 'es-ES'];
    $lang = alm_get_lang();
    if (isset($map[$lang]) && $lang !== 'it') {
        $output = preg_replace('/lang="[^"]*"/', 'lang="' . $map[$lang] . '"', $output);
    }
    return $output;
}, 10, 1);

/* ═══════════════════════════════════════════════════════════════
   HREFLANG SEO
   ═══════════════════════════════════════════════════════════════ */

add_action('wp_head', function (): void {
    if (!is_front_page()) return;
    $base = remove_query_arg('lang', get_home_url());
    $map  = ['it' => 'it', 'en' => 'en', 'de' => 'de', 'fr' => 'fr', 'es' => 'es'];
    foreach ($map as $code => $hreflang) {
        $url = ($code === 'it') ? $base : add_query_arg('lang', $code, $base);
        echo '<link rel="alternate" hreflang="' . esc_attr($hreflang) . '" href="' . esc_url($url) . '">' . "\n";
    }
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($base) . '">' . "\n";
}, 2);
