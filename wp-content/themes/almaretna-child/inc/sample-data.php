<?php
declare(strict_types=1);

/**
 * Almaretna Child — Inserimento dati camere
 *
 * Funzione idempotente: controlla _room_id prima di inserire.
 * Eseguita automaticamente al primo caricamento dopo attivazione tema.
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

/**
 * Inserisce le 7 camere di Almaretna come CPT almaretna_room.
 *
 * @return void
 */
function alm_insert_sample_rooms(): void {
    $rooms = [

        // ── Piano 1 ──────────────────────────────────────────────────────────

        [
            'title'   => 'Camera Singola — Piano 1',
            'excerpt' => 'Camera singola con bagno privato al primo piano, vista sulla campagna siciliana. Ideale per viaggiatori solitari che cercano riservatezza e silenzio alle pendici dell\'Etna.',
            'content' => '<p>Una camera raccolta e luminosa al primo piano della villa. Il bagno privato in camera garantisce totale riservatezza. La finestra si affaccia sulla campagna siciliana con vista sull\'Etna sullo sfondo, perfetta per iniziare la giornata con calma.</p><p>La posizione al piano 1 la rende ideale per chi preferisce evitare le scale pur godendo di tutta la quiete della villa.</p>',
            'meta'    => [
                '_room_id'               => 'P1-S01',
                '_room_capacity_adults'  => 1,
                '_room_capacity_children'=> 0,
                '_room_base_price'       => 60.00,
                '_room_floor'            => 'piano-1',
                '_room_bathroom_type'    => 'private',
                '_room_shared_with'      => '',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => false,
                '_room_family_unit_rooms'=> '',
                '_room_min_stay'         => 1,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['singola'],
                'room_floor' => ['piano-1'],
                'amenity'    => ['bagno-privato', 'vista-etna', 'aria-condizionata', 'wifi'],
            ],
        ],

        // ── Piano 2 ──────────────────────────────────────────────────────────

        [
            'title'   => 'Matrimoniale Vista Mare A — Piano 2',
            'excerpt' => 'Camera matrimoniale con bagno privato e vista panoramica sulla costa jonica da Taormina a Catania e sull\'Etna.',
            'content' => '<p>Una delle camere più richieste della villa: affacciata sulla costa jonica, offre una vista che spazia da Taormina a nord fino a Catania e al mare aperto a sud. L\'Etna si staglia imponente sul retro.</p><p>Bagno privato in camera, letto matrimoniale, aria condizionata silenziosa. Ideale per coppie o soggiorni romantici. Soggiorno minimo 2 notti.</p>',
            'meta'    => [
                '_room_id'               => 'P2-M01',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 1,
                '_room_base_price'       => 90.00,
                '_room_floor'            => 'piano-2',
                '_room_bathroom_type'    => 'private',
                '_room_shared_with'      => '',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => false,
                '_room_family_unit_rooms'=> '',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['matrimoniale'],
                'room_floor' => ['piano-2'],
                'amenity'    => ['bagno-privato', 'vista-mare', 'vista-etna', 'aria-condizionata', 'wifi'],
            ],
        ],

        [
            'title'   => 'Matrimoniale Vista Mare B — Piano 2',
            'excerpt' => 'Camera matrimoniale con bagno privato e vista panoramica. Stessa tipologia della camera A, ideale per gruppi o famiglie che prenotano insieme.',
            'content' => '<p>Speculare alla camera Matrimoniale Vista Mare A, questa stanza condivide lo stesso piano e la stessa straordinaria vista sul mare jonio. Perfetta per chi viaggia con amici o familiari che occupano la camera adiacente.</p><p>Bagno privato indipendente, letto matrimoniale, dotazioni complete. La combinazione delle due camere A+B permette di ospitare fino a 4 adulti con privacy totale.</p>',
            'meta'    => [
                '_room_id'               => 'P2-M02',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 1,
                '_room_base_price'       => 90.00,
                '_room_floor'            => 'piano-2',
                '_room_bathroom_type'    => 'private',
                '_room_shared_with'      => '',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => false,
                '_room_family_unit_rooms'=> '',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['matrimoniale'],
                'room_floor' => ['piano-2'],
                'amenity'    => ['bagno-privato', 'vista-mare', 'vista-etna', 'aria-condizionata', 'wifi'],
            ],
        ],

        [
            'title'   => 'Suite Familiare — Camera Matrimoniale',
            'excerpt' => 'Parte della Suite Familiare al piano 2. Camera matrimoniale con disimpegno privato che conduce al bagno in comune con la camera doppia.',
            'content' => '<p>La Suite Familiare è composta da due camere fisiche collegate da un disimpegno privato e un bagno condiviso. Questa è la camera matrimoniale della suite: doppio letto, vista mare, e un piccolo salottino nel disimpegno.</p><p>La suite può essere prenotata come unità intera (per famiglie fino a 4 persone) oppure come camera singola, in quel caso il bagno sarà condiviso con la camera doppia adiacente. Indicato per famiglie con bambini o gruppi che desiderano stare vicini pur avendo spazi distinti.</p>',
            'meta'    => [
                '_room_id'               => 'P2-FAM-M',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 0,
                '_room_base_price'       => 80.00,
                '_room_floor'            => 'piano-2',
                '_room_bathroom_type'    => 'shared',
                '_room_shared_with'      => 'P2-FAM-D',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => true,
                '_room_family_unit_rooms'=> '["P2-FAM-M","P2-FAM-D"]',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['suite-familiare'],
                'room_floor' => ['piano-2'],
                'amenity'    => ['bagno-condiviso', 'vista-mare', 'aria-condizionata', 'wifi'],
            ],
        ],

        [
            'title'   => 'Suite Familiare — Camera Doppia',
            'excerpt' => 'Parte della Suite Familiare al piano 2. Camera con due letti singoli e disimpegno privato che conduce al bagno in comune.',
            'content' => '<p>La camera doppia della Suite Familiare: due letti singoli, ideale per bambini o ragazzi. Collegata tramite disimpegno privato alla camera matrimoniale e al bagno comune.</p><p>Prenotabile separatamente o come parte della suite intera. Se prenotata separatamente, il bagno è condiviso con la camera matrimoniale adiacente. La suite completa (matrimoniale + doppia) ospita fino a 4 adulti o 2 adulti + 2 bambini.</p>',
            'meta'    => [
                '_room_id'               => 'P2-FAM-D',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 0,
                '_room_base_price'       => 80.00,
                '_room_floor'            => 'piano-2',
                '_room_bathroom_type'    => 'shared',
                '_room_shared_with'      => 'P2-FAM-M',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => true,
                '_room_family_unit_rooms'=> '["P2-FAM-M","P2-FAM-D"]',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['suite-familiare'],
                'room_floor' => ['piano-2'],
                'amenity'    => ['bagno-condiviso', 'vista-mare', 'aria-condizionata', 'wifi'],
            ],
        ],

        // ── Mansarda ─────────────────────────────────────────────────────────

        [
            'title'   => 'Matrimoniale Mansarda A',
            'excerpt' => 'Camera matrimoniale in mansarda con soffitto a travi, vista sull\'Etna. Bagno condiviso con la camera adiacente.',
            'content' => '<p>Le camere in mansarda sono le più caratteristiche della villa: soffitto a travi di legno, atmosfera autentica, una luce che cambia con le ore del giorno. Questa si affaccia sull\'Etna: al mattino la montagna emerge tra le nuvole, la sera si colora di rosso.</p><p>Il bagno è condiviso con la camera Mansarda B adiacente. Quando entrambe le camere sono occupate, il bagno è ad uso esclusivo dei soli ospiti delle due camere. Soggiorno minimo 2 notti.</p>',
            'meta'    => [
                '_room_id'               => 'MA-M01',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 0,
                '_room_base_price'       => 75.00,
                '_room_floor'            => 'mansarda',
                '_room_bathroom_type'    => 'shared',
                '_room_shared_with'      => 'MA-M02',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => false,
                '_room_family_unit_rooms'=> '',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['matrimoniale'],
                'room_floor' => ['mansarda'],
                'amenity'    => ['bagno-condiviso', 'vista-etna', 'aria-condizionata', 'wifi'],
            ],
        ],

        [
            'title'   => 'Matrimoniale Mansarda B',
            'excerpt' => 'Camera matrimoniale in mansarda con soffitto a travi, vista sulla costa jonica. Bagno condiviso con la camera adiacente.',
            'content' => '<p>La seconda camera della mansarda: stesso fascino rustico con travi a vista, ma con la finestra che guarda verso la costa jonica. Da qui si vede la linea blu del mare e, nelle giornate più limpide, la sagoma di Taormina.</p><p>Il bagno è condiviso con la camera Mansarda A adiacente, riservato esclusivamente agli ospiti delle due camere. Soggiorno minimo 2 notti.</p>',
            'meta'    => [
                '_room_id'               => 'MA-M02',
                '_room_capacity_adults'  => 2,
                '_room_capacity_children'=> 0,
                '_room_base_price'       => 75.00,
                '_room_floor'            => 'mansarda',
                '_room_bathroom_type'    => 'shared',
                '_room_shared_with'      => 'MA-M01',
                '_room_beds24_id'        => '',
                '_room_is_family_unit'   => false,
                '_room_family_unit_rooms'=> '',
                '_room_min_stay'         => 2,
                '_room_active'           => true,
            ],
            'taxonomies' => [
                'room_type'  => ['matrimoniale'],
                'room_floor' => ['mansarda'],
                'amenity'    => ['bagno-condiviso', 'vista-mare', 'aria-condizionata', 'wifi'],
            ],
        ],
    ];

    foreach ($rooms as $room_data) {
        alm_insert_single_room($room_data);
    }
}

/**
 * Inserisce una singola camera se non esiste già (controllo su _room_id).
 *
 * @param array<string, mixed> $room_data
 * @return int|null  Post ID inserito, o null se già esistente / errore.
 */
function alm_insert_single_room(array $room_data): ?int {
    $room_id_value = $room_data['meta']['_room_id'] ?? '';

    // Idempotenza: se esiste già una camera con questo _room_id, salta
    $existing = get_posts([
        'post_type'      => 'almaretna_room',
        'post_status'    => 'any',
        'posts_per_page' => 1,
        'meta_query'     => [[
            'key'     => '_room_id',
            'value'   => $room_id_value,
            'compare' => '=',
        ]],
    ]);

    if (!empty($existing)) {
        return null;
    }

    // Inserisci il post
    $post_id = wp_insert_post([
        'post_type'    => 'almaretna_room',
        'post_status'  => 'publish',
        'post_title'   => $room_data['title'],
        'post_excerpt' => $room_data['excerpt'],
        'post_content' => $room_data['content'] ?? '',
        'menu_order'   => alm_room_menu_order($room_id_value),
    ], true);

    if (is_wp_error($post_id)) {
        return null;
    }

    // Salva meta
    foreach ($room_data['meta'] as $key => $value) {
        if (is_bool($value)) {
            update_post_meta($post_id, $key, $value ? '1' : '0');
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    // Assegna tassonomie
    foreach ($room_data['taxonomies'] as $taxonomy => $slugs) {
        $term_ids = [];
        foreach ($slugs as $slug) {
            $term = get_term_by('slug', $slug, $taxonomy);
            if ($term instanceof WP_Term) {
                $term_ids[] = $term->term_id;
            }
        }
        if (!empty($term_ids)) {
            wp_set_object_terms($post_id, $term_ids, $taxonomy);
        }
    }

    return $post_id;
}

/**
 * Restituisce un menu_order per ordinare le camere per piano.
 *
 * @param string $room_id
 * @return int
 */
function alm_room_menu_order(string $room_id): int {
    $order = [
        'P1-S01'   => 10,
        'P2-M01'   => 20,
        'P2-M02'   => 30,
        'P2-FAM-M' => 40,
        'P2-FAM-D' => 50,
        'MA-M01'   => 60,
        'MA-M02'   => 70,
    ];
    return $order[$room_id] ?? 99;
}

// ─── Hook: esegui all'init, una sola volta ────────────────────────────────────

add_action('init', function (): void {
    if (get_option('alm_sample_rooms_inserted') !== '1') {
        // I terms devono esistere prima di assegnare le tassonomie
        if (get_option('alm_terms_inserted') === '1') {
            alm_insert_sample_rooms();
            update_option('alm_sample_rooms_inserted', '1');
        }
    }
});
