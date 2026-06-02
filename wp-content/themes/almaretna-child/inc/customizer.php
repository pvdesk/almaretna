<?php
declare(strict_types=1);

/**
 * Almaretna Child Theme — Customizer Options
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

add_action('customize_register', function (WP_Customize_Manager $wp_customize): void {
    // Sezione Almaretna Theme Options
    $wp_customize->add_section('almaretna_theme_options', [
        'title'       => __('Opzioni Tema Almaretna', 'almaretna-child'),
        'priority'    => 30,
        'description' => __('Personalizza le immagini e i testi della Home e delle pagine principali.', 'almaretna-child'),
    ]);

    // ─── HERO SECTION ────────────────────────────────────────────────────────

    // Hero Background Image
    $wp_customize->add_setting('almaretna_hero_bg', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'almaretna_hero_bg', [
        'label'    => __('Immagine Sfondo Hero Home', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'settings' => 'almaretna_hero_bg',
    ]));

    // Hero Eyebrow
    $wp_customize->add_setting('almaretna_hero_eyebrow', [
        'default'           => 'Nunziata di Mascali · Sicilia',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('almaretna_hero_eyebrow', [
        'label'    => __('Sopratitolo Hero', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'text',
    ]);

    // Hero Title
    $wp_customize->add_setting('almaretna_hero_title', [
        'default'           => "Un rifugio\nalle pendici dell'Etna",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('almaretna_hero_title', [
        'label'    => __('Titolo Hero (usa il tasto invio per andare a capo)', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'textarea',
    ]);

    // Hero Subtitle
    $wp_customize->add_setting('almaretna_hero_subtitle', [
        'default'           => "Villa con piscina panoramica, camere eleganti\ne colazioni artigianali tra gli agrumeti.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('almaretna_hero_subtitle', [
        'label'    => __('Sottotitolo Hero', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'textarea',
    ]);

    // ─── VILLA SECTION ───────────────────────────────────────────────────────

    // Villa Image
    $wp_customize->add_setting('almaretna_villa_img', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'almaretna_villa_img', [
        'label'    => __('Immagine Sezione Villa', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'settings' => 'almaretna_villa_img',
    ]));

    // Villa Eyebrow
    $wp_customize->add_setting('almaretna_villa_eyebrow', [
        'default'           => 'La nostra storia',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('almaretna_villa_eyebrow', [
        'label'    => __('Sopratitolo Sezione Villa', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'text',
    ]);

    // Villa Title
    $wp_customize->add_setting('almaretna_villa_title', [
        'default'           => 'Un angolo di Sicilia autentica',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('almaretna_villa_title', [
        'label'    => __('Titolo Sezione Villa', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'text',
    ]);

    // Villa Text 1
    $wp_customize->add_setting('almaretna_villa_text_1', [
        'default'           => "Almaretna è una villa di charme immersa negli agrumeti alle pendici dell'Etna, nel suggestivo borgo di Nunziata di Mascali. Qui il tempo rallenta, i profumi di zagara riempiono l'aria e la vista sul vulcano ti lascia senza parole.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('almaretna_villa_text_1', [
        'label'    => __('Testo Villa Paragrafo 1', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'textarea',
    ]);

    // Villa Text 2
    $wp_customize->add_setting('almaretna_villa_text_2', [
        'default'           => "Ogni camera è curata nei dettagli per offrirti il massimo del comfort, mentre la nostra piscina panoramica diventa il punto di incontro perfetto tra cielo, Etna e mare.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('almaretna_villa_text_2', [
        'label'    => __('Testo Villa Paragrafo 2', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'type'     => 'textarea',
    ]);

    // ─── GALLERIA HOMEPAGE ───────────────────────────────────────────────────
    // 6 slot immagini per la galleria strip in home page.
    // Se vuoti, vengono usate le foto delle camere come fallback.

    $wp_customize->add_section('almaretna_gallery', [
        'title'       => __('Galleria Homepage', 'almaretna-child'),
        'priority'    => 31,
        'description' => __('Carica fino a 6 foto per la galleria nella home page. Lascia vuote per usare automaticamente le foto delle camere.', 'almaretna-child'),
    ]);

    for ($i = 1; $i <= 6; $i++) {
        $wp_customize->add_setting('almaretna_gallery_img_' . $i, [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(
            new WP_Customize_Image_Control($wp_customize, 'almaretna_gallery_img_' . $i, [
                'label'    => sprintf(__('Foto galleria %d', 'almaretna-child'), $i),
                'section'  => 'almaretna_gallery',
                'settings' => 'almaretna_gallery_img_' . $i,
            ])
        );
    }

    // ─── SEZIONE PISCINA ─────────────────────────────────────────────────────

    $wp_customize->add_section('almaretna_pool', [
        'title'    => __('Sezione Piscina', 'almaretna-child'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('almaretna_pool_img', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'almaretna_pool_img', [
        'label'    => __('Foto principale piscina', 'almaretna-child'),
        'section'  => 'almaretna_pool',
        'settings' => 'almaretna_pool_img',
    ]));

    $wp_customize->add_setting('almaretna_pool_img2', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'almaretna_pool_img2', [
        'label'    => __('Foto secondaria (es. bar piscina)', 'almaretna-child'),
        'section'  => 'almaretna_pool',
        'settings' => 'almaretna_pool_img2',
    ]));

    // ─── ROOMS ARCHIVE HERO ──────────────────────────────────────────────────

    // Rooms Hero Background Image
    $wp_customize->add_setting('almaretna_rooms_hero_bg', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'almaretna_rooms_hero_bg', [
        'label'    => __('Immagine Sfondo Hero Camere', 'almaretna-child'),
        'section'  => 'almaretna_theme_options',
        'settings' => 'almaretna_rooms_hero_bg',
    ]));
});
