<?php
declare(strict_types=1);

/**
 * Almaretna Child — Custom Post Types e Taxonomies
 *
 * Registra:
 *  - CPT: almaretna_room (camere)
 *  - CPT: almaretna_rate (tariffe stagionali, gestito dal plugin)
 *  - Taxonomy: room_type
 *  - Taxonomy: room_floor
 *  - Taxonomy: amenity
 *  - Meta fields per almaretna_room
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ─── CPT: almaretna_room ─────────────────────────────────────────────────────

add_action('init', function (): void {
    $labels = [
        'name'                  => _x('Camere', 'Post type general name', 'almaretna-child'),
        'singular_name'         => _x('Camera', 'Post type singular name', 'almaretna-child'),
        'menu_name'             => __('Camere', 'almaretna-child'),
        'name_admin_bar'        => __('Camera', 'almaretna-child'),
        'add_new'               => __('Aggiungi nuova', 'almaretna-child'),
        'add_new_item'          => __('Aggiungi nuova camera', 'almaretna-child'),
        'new_item'              => __('Nuova camera', 'almaretna-child'),
        'edit_item'             => __('Modifica camera', 'almaretna-child'),
        'view_item'             => __('Visualizza camera', 'almaretna-child'),
        'all_items'             => __('Tutte le camere', 'almaretna-child'),
        'search_items'          => __('Cerca camere', 'almaretna-child'),
        'parent_item_colon'     => __('Camera padre:', 'almaretna-child'),
        'not_found'             => __('Nessuna camera trovata.', 'almaretna-child'),
        'not_found_in_trash'    => __('Nessuna camera nel cestino.', 'almaretna-child'),
        'featured_image'        => __('Immagine principale camera', 'almaretna-child'),
        'set_featured_image'    => __('Imposta immagine principale', 'almaretna-child'),
        'remove_featured_image' => __('Rimuovi immagine principale', 'almaretna-child'),
        'use_featured_image'    => __('Usa come immagine principale', 'almaretna-child'),
        'archives'              => __('Archivio camere', 'almaretna-child'),
        'insert_into_item'      => __('Inserisci nella camera', 'almaretna-child'),
        'uploaded_to_this_item' => __('Caricato per questa camera', 'almaretna-child'),
        'items_list'            => __('Lista camere', 'almaretna-child'),
        'items_list_navigation' => __('Navigazione lista camere', 'almaretna-child'),
        'filter_items_list'     => __('Filtra lista camere', 'almaretna-child'),
    ];

    register_post_type('almaretna_room', [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'camere', 'with_front' => false],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-building',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'taxonomies'         => ['room_type', 'room_floor', 'amenity'],
    ]);

    if (get_option('alm_flush_rewrite_rules_camere_v2') !== '1') {
        flush_rewrite_rules(false);
        update_option('alm_flush_rewrite_rules_camere_v2', '1');
    }
});

// ─── CPT: almaretna_rate ─────────────────────────────────────────────────────

add_action('init', function (): void {
    $labels = [
        'name'               => _x('Tariffe', 'Post type general name', 'almaretna-child'),
        'singular_name'      => _x('Tariffa', 'Post type singular name', 'almaretna-child'),
        'menu_name'          => __('Tariffe', 'almaretna-child'),
        'add_new'            => __('Aggiungi nuova', 'almaretna-child'),
        'add_new_item'       => __('Aggiungi nuova tariffa', 'almaretna-child'),
        'edit_item'          => __('Modifica tariffa', 'almaretna-child'),
        'view_item'          => __('Visualizza tariffa', 'almaretna-child'),
        'all_items'          => __('Tutte le tariffe', 'almaretna-child'),
        'search_items'       => __('Cerca tariffe', 'almaretna-child'),
        'not_found'          => __('Nessuna tariffa trovata.', 'almaretna-child'),
        'not_found_in_trash' => __('Nessuna tariffa nel cestino.', 'almaretna-child'),
    ];

    register_post_type('almaretna_rate', [
        'labels'          => $labels,
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'almaretna-booking',
        'show_in_rest'    => false,
        'capability_type' => 'post',
        'hierarchical'    => false,
        'supports'        => ['title', 'custom-fields'],
    ]);
});

// ─── Taxonomy: room_type ─────────────────────────────────────────────────────

add_action('init', function (): void {
    $labels = [
        'name'              => _x('Tipologie camera', 'Taxonomy general name', 'almaretna-child'),
        'singular_name'     => _x('Tipologia', 'Taxonomy singular name', 'almaretna-child'),
        'search_items'      => __('Cerca tipologie', 'almaretna-child'),
        'all_items'         => __('Tutte le tipologie', 'almaretna-child'),
        'parent_item'       => __('Tipologia padre', 'almaretna-child'),
        'parent_item_colon' => __('Tipologia padre:', 'almaretna-child'),
        'edit_item'         => __('Modifica tipologia', 'almaretna-child'),
        'update_item'       => __('Aggiorna tipologia', 'almaretna-child'),
        'add_new_item'      => __('Aggiungi tipologia', 'almaretna-child'),
        'new_item_name'     => __('Nuova tipologia', 'almaretna-child'),
        'menu_name'         => __('Tipologie', 'almaretna-child'),
        'not_found'         => __('Nessuna tipologia trovata.', 'almaretna-child'),
    ];

    register_taxonomy('room_type', ['almaretna_room'], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'tipo-camera'],
    ]);
});

// ─── Taxonomy: room_floor ─────────────────────────────────────────────────────

add_action('init', function (): void {
    $labels = [
        'name'              => _x('Piani', 'Taxonomy general name', 'almaretna-child'),
        'singular_name'     => _x('Piano', 'Taxonomy singular name', 'almaretna-child'),
        'search_items'      => __('Cerca piano', 'almaretna-child'),
        'all_items'         => __('Tutti i piani', 'almaretna-child'),
        'parent_item'       => __('Piano padre', 'almaretna-child'),
        'parent_item_colon' => __('Piano padre:', 'almaretna-child'),
        'edit_item'         => __('Modifica piano', 'almaretna-child'),
        'update_item'       => __('Aggiorna piano', 'almaretna-child'),
        'add_new_item'      => __('Aggiungi piano', 'almaretna-child'),
        'new_item_name'     => __('Nuovo piano', 'almaretna-child'),
        'menu_name'         => __('Piani', 'almaretna-child'),
        'not_found'         => __('Nessun piano trovato.', 'almaretna-child'),
    ];

    register_taxonomy('room_floor', ['almaretna_room'], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => false,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'piano'],
    ]);
});

// ─── Taxonomy: amenity ────────────────────────────────────────────────────────

add_action('init', function (): void {
    $labels = [
        'name'                       => _x('Dotazioni', 'Taxonomy general name', 'almaretna-child'),
        'singular_name'              => _x('Dotazione', 'Taxonomy singular name', 'almaretna-child'),
        'search_items'               => __('Cerca dotazioni', 'almaretna-child'),
        'popular_items'              => __('Dotazioni frequenti', 'almaretna-child'),
        'all_items'                  => __('Tutte le dotazioni', 'almaretna-child'),
        'edit_item'                  => __('Modifica dotazione', 'almaretna-child'),
        'update_item'                => __('Aggiorna dotazione', 'almaretna-child'),
        'add_new_item'               => __('Aggiungi dotazione', 'almaretna-child'),
        'new_item_name'              => __('Nuova dotazione', 'almaretna-child'),
        'separate_items_with_commas' => __('Separa le dotazioni con virgole', 'almaretna-child'),
        'add_or_remove_items'        => __('Aggiungi o rimuovi dotazioni', 'almaretna-child'),
        'choose_from_most_used'      => __('Scegli tra le più usate', 'almaretna-child'),
        'menu_name'                  => __('Dotazioni', 'almaretna-child'),
        'not_found'                  => __('Nessuna dotazione trovata.', 'almaretna-child'),
    ];

    register_taxonomy('amenity', ['almaretna_room'], [
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'dotazione'],
    ]);
});

// ─── Meta fields: almaretna_room ─────────────────────────────────────────────

add_action('init', function (): void {
    $room_meta_fields = [
        '_room_id' => [
            'type'         => 'string',
            'description'  => 'ID univoco camera (es. P1-S01)',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => '',
        ],
        '_room_capacity_adults' => [
            'type'         => 'integer',
            'description'  => 'Capacità adulti',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 1,
        ],
        '_room_capacity_children' => [
            'type'         => 'integer',
            'description'  => 'Capacità bambini',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
        '_room_base_price' => [
            'type'         => 'number',
            'description'  => 'Prezzo base per notte in euro',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0.0,
        ],
        '_room_floor' => [
            'type'         => 'string',
            'description'  => 'Piano della camera (piano-1, piano-2, mansarda)',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => '',
        ],
        '_room_bathroom_type' => [
            'type'         => 'string',
            'description'  => 'Tipo bagno: private | shared',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 'private',
        ],
        '_room_shared_with' => [
            'type'         => 'string',
            'description'  => 'ID camera con cui si condivide il bagno (es. MA-M02)',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => '',
        ],
        '_room_beds24_id' => [
            'type'         => 'string',
            'description'  => 'ID camera su Beds24',
            'single'       => true,
            'show_in_rest' => false,
            'default'      => '',
        ],
        '_room_is_family_unit' => [
            'type'         => 'boolean',
            'description'  => 'True se fa parte di una suite familiare',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => false,
        ],
        '_room_family_unit_rooms' => [
            'type'         => 'string',
            'description'  => 'JSON array degli ID camere che compongono la suite familiare',
            'single'       => true,
            'show_in_rest' => false,
            'default'      => '',
        ],
        '_room_min_stay' => [
            'type'         => 'integer',
            'description'  => 'Soggiorno minimo in notti',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 1,
        ],
        '_room_active' => [
            'type'         => 'boolean',
            'description'  => 'Camera attiva e prenotabile',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => true,
        ],
        '_room_footer_img_1' => [
            'type'         => 'integer',
            'description'  => 'Immagine del footer 1',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
        '_room_footer_img_2' => [
            'type'         => 'integer',
            'description'  => 'Immagine del footer 2',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
        '_room_footer_img_3' => [
            'type'         => 'integer',
            'description'  => 'Immagine del footer 3',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
        '_room_footer_img_4' => [
            'type'         => 'integer',
            'description'  => 'Immagine del footer 4',
            'single'       => true,
            'show_in_rest' => true,
            'default'      => 0,
        ],
    ];

    foreach ($room_meta_fields as $meta_key => $args) {
        register_post_meta('almaretna_room', $meta_key, $args);
    }
});

// ─── Inserimento terms predefiniti all'attivazione ────────────────────────────

add_action('after_switch_theme', 'alm_insert_default_terms');

/**
 * Inserisce i terms predefiniti per le tassonomie al primo caricamento.
 */
function alm_insert_default_terms(): void {
    if (get_option('alm_terms_inserted') === '1') {
        return;
    }

    // room_type
    $room_types = [
        ['name' => 'Singola',          'slug' => 'singola'],
        ['name' => 'Matrimoniale',     'slug' => 'matrimoniale'],
        ['name' => 'Suite Familiare',  'slug' => 'suite-familiare'],
        ['name' => 'Mansarda',         'slug' => 'mansarda'],
    ];

    foreach ($room_types as $term) {
        if (!term_exists($term['slug'], 'room_type')) {
            wp_insert_term($term['name'], 'room_type', ['slug' => $term['slug']]);
        }
    }

    // room_floor
    $room_floors = [
        ['name' => 'Piano 1',   'slug' => 'piano-1'],
        ['name' => 'Piano 2',   'slug' => 'piano-2'],
        ['name' => 'Mansarda',  'slug' => 'mansarda'],
    ];

    foreach ($room_floors as $term) {
        if (!term_exists($term['slug'], 'room_floor')) {
            wp_insert_term($term['name'], 'room_floor', ['slug' => $term['slug']]);
        }
    }

    // amenity
    $amenities = [
        // Vista / posizione
        ['name' => 'Vista mare',            'slug' => 'vista-mare'],
        ['name' => 'Vista Etna',            'slug' => 'vista-etna'],
        ['name' => 'Terrazza/Balcone',      'slug' => 'terrazza'],
        // Comfort
        ['name' => 'Aria condizionata',     'slug' => 'aria-condizionata'],
        ['name' => 'Riscaldamento',         'slug' => 'riscaldamento'],
        ['name' => 'Wi-Fi',                 'slug' => 'wifi'],
        // Bagno
        ['name' => 'Bagno privato',         'slug' => 'bagno-privato'],
        ['name' => 'Bagno condiviso',       'slug' => 'bagno-condiviso'],
        ['name' => 'Doccia',                'slug' => 'doccia'],
        ['name' => 'Vasca da bagno',        'slug' => 'vasca'],
        ['name' => 'Phon',                  'slug' => 'phon'],
        ['name' => 'Asciugamani',           'slug' => 'asciugamani'],
        // In camera
        ['name' => 'Biancheria inclusa',    'slug' => 'biancheria'],
        ['name' => 'Frigorifero',           'slug' => 'frigorifero'],
        ['name' => 'Cassaforte',            'slug' => 'cassaforte'],
        // Extra
        ['name' => 'Pulizia inclusa',       'slug' => 'pulizia'],
        ['name' => 'Parcheggio',            'slug' => 'parcheggio'],
    ];

    foreach ($amenities as $term) {
        if (!term_exists($term['slug'], 'amenity')) {
            wp_insert_term($term['name'], 'amenity', ['slug' => $term['slug']]);
        }
    }

    update_option('alm_terms_inserted', '1');
}

// ─── Flush rewrite rules al primo caricamento dopo attivazione ────────────────

add_action('init', function (): void {
    if (get_option('alm_flush_rewrite_needed') === '1') {
        flush_rewrite_rules();
        delete_option('alm_flush_rewrite_needed');
    }
});

add_action('after_switch_theme', function (): void {
    update_option('alm_flush_rewrite_needed', '1');
});

// ─── Metabox: Dettagli Camera ────────────────────────────────────────────────

add_action('add_meta_boxes', function (): void {
    add_meta_box(
        'alm_room_details',
        __('Dettagli Camera', 'almaretna-child'),
        'alm_room_details_callback',
        'almaretna_room',
        'normal',
        'high'
    );
});

function alm_room_details_callback(WP_Post $post): void {
    wp_nonce_field('alm_room_details_save', 'alm_room_details_nonce');
    $m = [
        'id'               => get_post_meta($post->ID, '_room_id', true),
        'capacity_adults'  => get_post_meta($post->ID, '_room_capacity_adults', true) ?: 1,
        'capacity_children'=> get_post_meta($post->ID, '_room_capacity_children', true) ?: 0,
        'base_price'       => get_post_meta($post->ID, '_room_base_price', true) ?: '',
        'bathroom_type'    => get_post_meta($post->ID, '_room_bathroom_type', true) ?: 'private',
        'shared_with'      => get_post_meta($post->ID, '_room_shared_with', true),
        'beds24_id'        => get_post_meta($post->ID, '_room_beds24_id', true),
        'min_stay'         => get_post_meta($post->ID, '_room_min_stay', true) ?: 1,
        'active'           => get_post_meta($post->ID, '_room_active', true),
        'size_m2'          => get_post_meta($post->ID, '_room_size_m2', true) ?: '',
    ];
    $active = ($m['active'] === '' || $m['active'] === '1' || $m['active'] === true);
    ?>
    <style>
    .alm-mb { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; padding:4px 0 16px; }
    .alm-mb label { display:block; font-weight:600; font-size:12px; text-transform:uppercase; letter-spacing:.06em; color:#3c434a; margin-bottom:4px; }
    .alm-mb input[type=text],.alm-mb input[type=number],.alm-mb select { width:100%; padding:6px 8px; border:1px solid #8c8f94; border-radius:4px; font-size:13px; }
    .alm-mb-full { grid-column:1/-1; }
    .alm-mb-active { display:flex; align-items:center; gap:10px; padding:10px 14px; background:#f0f9f0; border:1px solid #a8d5a2; border-radius:6px; grid-column:1/-1; }
    .alm-mb-active input { width:auto !important; transform:scale(1.3); cursor:pointer; }
    .alm-mb-active strong { font-size:13px; color:#2e7d32; }
    </style>
    <div class="alm-mb">
        <div>
            <label><?php _e('ID Camera (es. P1-M01)', 'almaretna-child'); ?></label>
            <input type="text" name="_room_id" value="<?php echo esc_attr($m['id']); ?>" placeholder="P1-M01" />
        </div>
        <div>
            <label><?php _e('Prezzo base / notte (€)', 'almaretna-child'); ?></label>
            <input type="number" name="_room_base_price" value="<?php echo esc_attr((string)$m['base_price']); ?>" min="0" step="0.01" placeholder="80.00" />
        </div>
        <div>
            <label><?php _e('Dimensione (m²)', 'almaretna-child'); ?></label>
            <input type="number" name="_room_size_m2" value="<?php echo esc_attr((string)$m['size_m2']); ?>" min="0" step="1" placeholder="20" />
        </div>
        <div>
            <label><?php _e('Capacità adulti', 'almaretna-child'); ?></label>
            <input type="number" name="_room_capacity_adults" value="<?php echo esc_attr((string)$m['capacity_adults']); ?>" min="1" max="10" />
        </div>
        <div>
            <label><?php _e('Capacità bambini', 'almaretna-child'); ?></label>
            <input type="number" name="_room_capacity_children" value="<?php echo esc_attr((string)$m['capacity_children']); ?>" min="0" max="10" />
        </div>
        <div>
            <label><?php _e('Soggiorno minimo (notti)', 'almaretna-child'); ?></label>
            <input type="number" name="_room_min_stay" value="<?php echo esc_attr((string)$m['min_stay']); ?>" min="1" max="30" />
        </div>
        <div>
            <label><?php _e('Tipo bagno', 'almaretna-child'); ?></label>
            <select name="_room_bathroom_type">
                <option value="private"  <?php selected($m['bathroom_type'], 'private'); ?>><?php _e('Privato', 'almaretna-child'); ?></option>
                <option value="shared"   <?php selected($m['bathroom_type'], 'shared'); ?>><?php _e('Condiviso', 'almaretna-child'); ?></option>
            </select>
        </div>
        <div>
            <label><?php _e('Condiviso con (ID camera)', 'almaretna-child'); ?></label>
            <input type="text" name="_room_shared_with" value="<?php echo esc_attr($m['shared_with']); ?>" placeholder="P1-M02" />
        </div>
        <div>
            <label><?php _e('Beds24 Room ID', 'almaretna-child'); ?></label>
            <input type="text" name="_room_beds24_id" value="<?php echo esc_attr($m['beds24_id']); ?>" placeholder="" />
        </div>
        <div class="alm-mb-active">
            <input type="checkbox" name="_room_active" id="_room_active" value="1" <?php checked($active); ?> />
            <label for="_room_active" style="margin:0;font-weight:700;text-transform:none;letter-spacing:0;font-size:13px;">
                <strong><?php _e('Camera attiva e visibile sul sito', 'almaretna-child'); ?></strong>
                <span style="font-weight:400;color:#555;"> — <?php _e('deseleziona per nasconderla senza eliminarla', 'almaretna-child'); ?></span>
            </label>
        </div>
    </div>
    <?php
}

add_action('save_post_almaretna_room', function (int $post_id): void {
    if (!isset($_POST['alm_room_details_nonce']) ||
        !wp_verify_nonce($_POST['alm_room_details_nonce'], 'alm_room_details_save') ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    $text_fields = ['_room_id', '_room_shared_with', '_room_beds24_id'];
    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    $int_fields = ['_room_capacity_adults', '_room_capacity_children', '_room_min_stay', '_room_size_m2'];
    foreach ($int_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, absint($_POST[$key]));
        }
    }

    if (isset($_POST['_room_base_price'])) {
        update_post_meta($post_id, '_room_base_price', (float) $_POST['_room_base_price']);
    }

    $bath = sanitize_text_field($_POST['_room_bathroom_type'] ?? 'private');
    update_post_meta($post_id, '_room_bathroom_type', in_array($bath, ['private', 'shared'], true) ? $bath : 'private');

    update_post_meta($post_id, '_room_active', isset($_POST['_room_active']) ? '1' : '0');
});

// ─── Metabox: Traduzioni Camera ──────────────────────────────────────────────

add_action('add_meta_boxes', function (): void {
    add_meta_box(
        'alm_room_translations',
        __('Traduzioni Camera (EN / DE / FR / ES)', 'almaretna-child'),
        'alm_room_translations_callback',
        'almaretna_room',
        'normal',
        'high'
    );
});

/**
 * Renderizza il metabox per le traduzioni della camera.
 */
function alm_room_translations_callback(WP_Post $post): void {
    wp_nonce_field('alm_save_room_translations', 'alm_room_translations_nonce');

    $langs = ['en' => 'English', 'de' => 'Deutsch', 'fr' => 'Français', 'es' => 'Español'];
    $fields = [
        'title'   => ['label' => 'Titolo / Title', 'type' => 'text'],
        'excerpt' => ['label' => 'Estratto / Excerpt', 'type' => 'textarea'],
        'content' => ['label' => 'Descrizione completa / Content', 'type' => 'editor'],
    ];
    ?>
    <style>
    .alm-trans-tabs { display:flex; gap:0; border-bottom:1px solid #ddd; margin-bottom:16px; }
    .alm-trans-tab { padding:7px 18px; cursor:pointer; font-size:13px; font-weight:600; border:1px solid #ddd; border-bottom:none; background:#f6f7f7; color:#666; margin-right:3px; border-radius:3px 3px 0 0; }
    .alm-trans-tab.active { background:#fff; color:#1d2327; border-bottom:1px solid #fff; margin-bottom:-1px; }
    .alm-trans-panel { display:none; }
    .alm-trans-panel.active { display:block; }
    .alm-trans-field { margin-bottom:14px; }
    .alm-trans-field label { display:block; font-weight:600; font-size:12px; color:#555; margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
    .alm-trans-field input[type=text], .alm-trans-field textarea { width:100%; }
    .alm-trans-field textarea { min-height:90px; resize:vertical; }
    </style>
    <div class="alm-trans-tabs" id="alm-trans-tabs">
        <?php foreach ($langs as $code => $name) : ?>
        <div class="alm-trans-tab<?php echo $code === 'en' ? ' active' : ''; ?>"
             data-lang="<?php echo esc_attr($code); ?>">
            <?php echo esc_html($name); ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php foreach ($langs as $code => $name) : ?>
    <div class="alm-trans-panel<?php echo $code === 'en' ? ' active' : ''; ?>"
         id="alm-trans-panel-<?php echo esc_attr($code); ?>">
        <?php foreach ($fields as $fkey => $fconf) :
            $meta_key = "_room_{$fkey}_{$code}";
            $value    = get_post_meta($post->ID, $meta_key, true);
        ?>
        <div class="alm-trans-field">
            <label for="<?php echo esc_attr($meta_key); ?>"><?php echo esc_html($fconf['label']); ?></label>
            <?php if ($fconf['type'] === 'textarea') : ?>
                <textarea id="<?php echo esc_attr($meta_key); ?>"
                          name="<?php echo esc_attr($meta_key); ?>"
                          rows="4"><?php echo esc_textarea((string) $value); ?></textarea>
            <?php elseif ($fconf['type'] === 'editor') : ?>
                <textarea id="<?php echo esc_attr($meta_key); ?>"
                          name="<?php echo esc_attr($meta_key); ?>"
                          rows="8"><?php echo esc_textarea((string) $value); ?></textarea>
            <?php else : ?>
                <input type="text"
                       id="<?php echo esc_attr($meta_key); ?>"
                       name="<?php echo esc_attr($meta_key); ?>"
                       value="<?php echo esc_attr((string) $value); ?>" />
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <script>
    (function () {
        var tabs   = document.querySelectorAll('#alm-trans-tabs .alm-trans-tab');
        var panels = document.querySelectorAll('.alm-trans-panel');
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); });
                panels.forEach(function (p) { p.classList.remove('active'); });
                tab.classList.add('active');
                var panel = document.getElementById('alm-trans-panel-' + tab.dataset.lang);
                if (panel) panel.classList.add('active');
            });
        });
    })();
    </script>
    <p style="color:#888;font-size:12px;margin-top:8px;">
        Lascia vuoto per usare il testo italiano predefinito.
    </p>
    <?php
}

// Salva metabox traduzioni
add_action('save_post_almaretna_room', function (int $post_id): void {
    if (!isset($_POST['alm_room_translations_nonce']) || !wp_verify_nonce($_POST['alm_room_translations_nonce'], 'alm_save_room_translations')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $langs  = ['en', 'de', 'fr', 'es'];
    $fields = ['title', 'excerpt', 'content'];

    foreach ($langs as $lang) {
        foreach ($fields as $field) {
            $meta_key = "_room_{$field}_{$lang}";
            if (isset($_POST[$meta_key])) {
                $value = wp_kses_post(wp_unslash($_POST[$meta_key]));
                if ($value !== '') {
                    update_post_meta($post_id, $meta_key, $value);
                } else {
                    delete_post_meta($post_id, $meta_key);
                }
            }
        }
    }
}, 10);

// ─── Metabox: Foto Footer Camera ─────────────────────────────────────────────

add_action('add_meta_boxes', function (): void {
    add_meta_box(
        'alm_room_footer_gallery',
        __('Foto Fine Pagina Camera (Pre-Footer)', 'almaretna-child'),
        'alm_room_footer_gallery_callback',
        'almaretna_room',
        'normal',
        'high'
    );
});

/**
 * Renderizza il metabox per caricare le 4 foto pre-footer.
 */
function alm_room_footer_gallery_callback(WP_Post $post): void {
    wp_nonce_field('alm_save_room_footer_gallery', 'alm_room_footer_gallery_nonce');

    echo '<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; padding: 10px 0;">';
    for ($i = 1; $i <= 4; $i++) {
        $meta_key = "_room_footer_img_{$i}";
        $val = (int) get_post_meta($post->ID, $meta_key, true);
        $img_src = '';
        if ($val > 0) {
            $img_src = wp_get_attachment_image_url($val, 'thumbnail');
        }
        ?>
        <div class="alm-footer-img-upload-box" style="border: 1px dashed #ccc; padding: 12px; text-align: center; background: #fafafa; border-radius: 6px;">
            <label style="display: block; font-weight: bold; margin-bottom: 8px; font-size: 12px;">Foto <?php echo $i; ?></label>
            <div class="alm-footer-img-preview-container" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #eee; margin-bottom: 8px; border-radius: 4px; overflow: hidden;">
                <?php if ($img_src) : ?>
                    <img class="alm-footer-img-preview" src="<?php echo esc_url($img_src); ?>" style="max-width: 100%; max-height: 100px; display: block;" />
                <?php else : ?>
                    <span class="alm-footer-img-placeholder" style="color: #999; font-size: 11px;"><?php esc_html_e('Nessuna immagine', 'almaretna-child'); ?></span>
                <?php endif; ?>
            </div>
            <input type="hidden" class="alm-footer-img-id" name="<?php echo esc_attr($meta_key); ?>" value="<?php echo $val ? $val : ''; ?>" />
            <button type="button" class="button alm-footer-img-upload-btn" style="margin-bottom: 4px; width: 100%;"><?php esc_html_e('Scegli', 'almaretna-child'); ?></button>
            <button type="button" class="button button-link-delete alm-footer-img-remove-btn" style="color: #a00; font-size: 11px; width: 100%; text-decoration: none; <?php echo !$val ? 'display: none;' : ''; ?>"><?php esc_html_e('Rimuovi', 'almaretna-child'); ?></button>
        </div>
        <?php
    }
    echo '</div>';

    // Script JS per caricamento Media Library
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.alm-footer-img-upload-btn').click(function(e) {
            e.preventDefault();
            var $box = $(this).closest('.alm-footer-img-upload-box');
            var frame = wp.media({
                title: '<?php esc_attr_e('Seleziona Immagine Footer', 'almaretna-child'); ?>',
                multiple: false,
                library: { type : 'image' },
                button: { text : '<?php esc_attr_e('Usa questa immagine', 'almaretna-child'); ?>' }
            });

            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $box.find('.alm-footer-img-id').val(attachment.id);
                var previewUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $box.find('.alm-footer-img-preview-container').html('<img class="alm-footer-img-preview" src="' + previewUrl + '" style="max-width: 100%; max-height: 100px; display: block;" />');
                $box.find('.alm-footer-img-remove-btn').show();
            });

            frame.open();
        });

        $('.alm-footer-img-remove-btn').click(function(e) {
            e.preventDefault();
            var $box = $(this).closest('.alm-footer-img-upload-box');
            $box.find('.alm-footer-img-id').val('');
            $box.find('.alm-footer-img-preview-container').html('<span class="alm-footer-img-placeholder" style="color: #999; font-size: 11px;"><?php esc_html_e('Nessuna immagine', 'almaretna-child'); ?></span>');
            $(this).hide();
        });
    });
    </script>
    <?php
}

// Enqueue media script per l'uploader
add_action('admin_enqueue_scripts', function (string $hook): void {
    global $post;
    if (in_array($hook, ['post.php', 'post-new.php'], true) && $post instanceof WP_Post && $post->post_type === 'almaretna_room') {
        wp_enqueue_media();
    }
});

// Salva metabox
add_action('save_post_almaretna_room', function (int $post_id): void {
    if (!isset($_POST['alm_room_footer_gallery_nonce']) || !wp_verify_nonce($_POST['alm_room_footer_gallery_nonce'], 'alm_save_room_footer_gallery')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    for ($i = 1; $i <= 4; $i++) {
        $meta_key = "_room_footer_img_{$i}";
        if (isset($_POST[$meta_key])) {
            $val = sanitize_text_field($_POST[$meta_key]);
            if ($val !== '') {
                update_post_meta($post_id, $meta_key, absint($val));
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }
    }
});
