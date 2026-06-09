<?php
declare(strict_types=1);

/**
 * Almaretna — SEO Meta Box
 *
 * Card editabile nel backend per Meta Title e Meta Description.
 * Funziona su: page, almaretna_room.
 * - Anteprima live SERP Google
 * - Barra di avanzamento con feedback colore
 * - Lasciare vuoto = usa il default automatico del tema
 *
 * @package AlmaretnaChild
 */

defined('ABSPATH') || exit;

// ── Registra meta box ─────────────────────────────────────────────────────────

add_action('add_meta_boxes', function (): void {
    foreach (['page', 'almaretna_room'] as $screen) {
        add_meta_box(
            'alm_seo_meta',
            __('SEO — Meta Title & Description', 'almaretna-child'),
            'alm_seo_meta_box_render',
            $screen,
            'normal',
            'high'
        );
    }
});

// ── Render ────────────────────────────────────────────────────────────────────

function alm_seo_meta_box_render(WP_Post $post): void {

    $seo_title = (string) get_post_meta($post->ID, '_alm_seo_title', true);
    $seo_desc  = (string) get_post_meta($post->ID, '_alm_seo_description', true);

    $ph_title  = alm_seo_fallback_title($post);
    $ph_desc   = alm_seo_fallback_desc($post);

    // URL preview (non salvata, solo visiva)
    $permalink   = get_permalink($post) ?: home_url('/');
    $parsed      = parse_url((string) $permalink);
    $display_url = ($parsed['host'] ?? 'almaretna.it') . ($parsed['path'] ?? '/');

    $pre_title = $seo_title ?: $ph_title;
    $pre_desc  = $seo_desc  ?: $ph_desc;

    wp_nonce_field('alm_seo_save_' . $post->ID, '_alm_seo_nonce');
    ?>

    <style id="alm-seo-admin-css">
    .alm-seo-wrap            { padding: 2px 0 6px; }

    /* ── Preview Google ── */
    .alm-seo-preview-label   { font-size:11px;text-transform:uppercase;letter-spacing:.07em;color:#999;margin:0 0 8px;font-family:-apple-system,sans-serif; }
    .alm-seo-preview         { background:#fff;border:1px solid #ddd;border-radius:8px;padding:14px 18px;margin-bottom:22px;font-family:Arial,sans-serif; }
    .alm-seo-preview__title  { font-size:18px;color:#1a0dab;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px; }
    .alm-seo-preview__url    { font-size:13px;color:#006621;margin-bottom:4px; }
    .alm-seo-preview__desc   { font-size:13px;color:#4d4d4d;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

    /* ── Fields ── */
    .alm-seo-field           { margin-bottom:18px; }
    .alm-seo-field > label   { display:block;font-weight:600;font-size:13px;color:#1d2327;margin-bottom:5px; }
    .alm-seo-field input[type="text"],
    .alm-seo-field textarea  { width:100%;box-sizing:border-box;border:1px solid #8c8f94;border-radius:4px;padding:7px 10px;font-size:14px;line-height:1.5;transition:border-color .15s,box-shadow .15s; }
    .alm-seo-field input:focus,
    .alm-seo-field textarea:focus { border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;outline:none; }
    .alm-seo-field textarea  { resize:vertical;min-height:68px; }

    /* ── Progress bar ── */
    .alm-seo-bar-wrap        { height:4px;background:#f0f0f0;border-radius:2px;margin-top:6px;overflow:hidden; }
    .alm-seo-bar             { height:100%;border-radius:2px;transition:width .2s,background-color .2s;width:0;background:#c3c4c7; }
    .alm-seo-bar.empty       { background:#c3c4c7; }
    .alm-seo-bar.short       { background:#dba617; }
    .alm-seo-bar.ok          { background:#00a32a; }
    .alm-seo-bar.warn        { background:#dba617; }
    .alm-seo-bar.over        { background:#d63638; }

    /* ── Status row ── */
    .alm-seo-status          { display:flex;justify-content:space-between;align-items:center;margin-top:5px; }
    .alm-seo-hint            { font-size:12px;color:#888; }
    .alm-seo-hint.short,
    .alm-seo-hint.warn       { color:#dba617; }
    .alm-seo-hint.ok         { color:#00a32a; }
    .alm-seo-hint.over       { color:#d63638; }
    .alm-seo-count           { font-size:12px;color:#666;white-space:nowrap; }
    .alm-seo-note            { font-size:11px;color:#aaa;margin:4px 0 0; }
    </style>

    <div class="alm-seo-wrap">

        <p class="alm-seo-preview-label">Anteprima Google</p>

        <div class="alm-seo-preview">
            <div class="alm-seo-preview__title" id="alm-prev-title"><?php echo esc_html($pre_title); ?></div>
            <div class="alm-seo-preview__url"><?php echo esc_html($display_url); ?></div>
            <div class="alm-seo-preview__desc"  id="alm-prev-desc"><?php echo esc_html($pre_desc); ?></div>
        </div>

        <!-- Meta Title -->
        <div class="alm-seo-field">
            <label for="alm_seo_title">Meta Title</label>
            <input type="text"
                   id="alm_seo_title"
                   name="alm_seo_title"
                   value="<?php echo esc_attr($seo_title); ?>"
                   placeholder="<?php echo esc_attr($ph_title); ?>"
                   maxlength="80">
            <div class="alm-seo-bar-wrap">
                <div class="alm-seo-bar" id="alm-title-bar"></div>
            </div>
            <div class="alm-seo-status">
                <span class="alm-seo-hint" id="alm-title-hint">Consigliato: 50–60 caratteri</span>
                <span class="alm-seo-count" id="alm-title-count">0 / 60</span>
            </div>
            <p class="alm-seo-note">Lascia vuoto per usare il valore automatico (visibile come segnaposto).</p>
        </div>

        <!-- Meta Description -->
        <div class="alm-seo-field">
            <label for="alm_seo_description">Meta Description</label>
            <textarea id="alm_seo_description"
                      name="alm_seo_description"
                      placeholder="<?php echo esc_attr($ph_desc); ?>"
                      maxlength="320"><?php echo esc_textarea($seo_desc); ?></textarea>
            <div class="alm-seo-bar-wrap">
                <div class="alm-seo-bar" id="alm-desc-bar"></div>
            </div>
            <div class="alm-seo-status">
                <span class="alm-seo-hint" id="alm-desc-hint">Consigliato: 120–160 caratteri</span>
                <span class="alm-seo-count" id="alm-desc-count">0 / 160</span>
            </div>
            <p class="alm-seo-note">Lascia vuoto per usare il valore automatico.</p>
        </div>

    </div>

    <script>
    (function () {
        'use strict';

        var titleInput = document.getElementById('alm_seo_title');
        var descInput  = document.getElementById('alm_seo_description');
        var prevTitle  = document.getElementById('alm-prev-title');
        var prevDesc   = document.getElementById('alm-prev-desc');

        var titleBar   = document.getElementById('alm-title-bar');
        var titleHint  = document.getElementById('alm-title-hint');
        var titleCount = document.getElementById('alm-title-count');

        var descBar    = document.getElementById('alm-desc-bar');
        var descHint   = document.getElementById('alm-desc-hint');
        var descCount  = document.getElementById('alm-desc-count');

        var defTitle = <?php echo wp_json_encode($ph_title); ?>;
        var defDesc  = <?php echo wp_json_encode($ph_desc); ?>;

        // ── Title ──────────────────────────────────────────────────────
        function updateTitle() {
            var v   = titleInput.value;
            var len = v.length;
            var max = 60;
            var pct = Math.min(len / max * 100, 100);

            titleCount.textContent = len + ' / ' + max;
            titleBar.style.width   = pct + '%';

            var cls, msg;
            if      (len === 0)  { cls = 'empty'; msg = 'Consigliato: 50–60 caratteri'; }
            else if (len < 30)   { cls = 'short'; msg = 'Troppo corto'; }
            else if (len <= 60)  { cls = 'ok';    msg = '✓ Ottimo'; }
            else if (len <= 70)  { cls = 'warn';  msg = 'Potrebbe essere troncato'; }
            else                 { cls = 'over';  msg = 'Troppo lungo — verrà troncato'; }

            titleBar.className  = 'alm-seo-bar ' + cls;
            titleHint.className = 'alm-seo-hint ' + cls;
            titleHint.textContent = msg;
            prevTitle.textContent = v || defTitle;
        }

        // ── Description ────────────────────────────────────────────────
        function updateDesc() {
            var v   = descInput.value;
            var len = v.length;
            var max = 160;
            var pct = Math.min(len / max * 100, 100);

            descCount.textContent = len + ' / ' + max;
            descBar.style.width   = pct + '%';

            var cls, msg;
            if      (len === 0)   { cls = 'empty'; msg = 'Consigliato: 120–160 caratteri'; }
            else if (len < 80)    { cls = 'short'; msg = 'Troppo corta'; }
            else if (len <= 160)  { cls = 'ok';    msg = '✓ Ottima'; }
            else if (len <= 200)  { cls = 'warn';  msg = 'Potrebbe essere troncata'; }
            else                  { cls = 'over';  msg = 'Troppo lunga — verrà troncata'; }

            descBar.className  = 'alm-seo-bar ' + cls;
            descHint.className = 'alm-seo-hint ' + cls;
            descHint.textContent = msg;
            prevDesc.textContent = v || defDesc;
        }

        titleInput.addEventListener('input', updateTitle);
        descInput.addEventListener('input', updateDesc);

        // Inizializza al caricamento pagina
        updateTitle();
        updateDesc();
    })();
    </script>

    <?php
}

// ── Salva ─────────────────────────────────────────────────────────────────────

add_action('save_post', function (int $post_id): void {
    // Nonce
    if (empty($_POST['_alm_seo_nonce'])) return;
    if (!wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['_alm_seo_nonce'])),
        'alm_seo_save_' . $post_id
    )) return;

    // Permessi
    if (!current_user_can('edit_post', $post_id)) return;

    // Salta autosave e revisioni
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;

    // ── Meta Title ──
    $title = isset($_POST['alm_seo_title'])
        ? sanitize_text_field(wp_unslash($_POST['alm_seo_title']))
        : '';

    if ($title !== '') {
        update_post_meta($post_id, '_alm_seo_title', $title);
    } else {
        delete_post_meta($post_id, '_alm_seo_title');
    }

    // ── Meta Description ──
    $desc = isset($_POST['alm_seo_description'])
        ? sanitize_textarea_field(wp_unslash($_POST['alm_seo_description']))
        : '';

    if ($desc !== '') {
        update_post_meta($post_id, '_alm_seo_description', $desc);
    } else {
        delete_post_meta($post_id, '_alm_seo_description');
    }
});

// ── Fallback title/description (usati anche come placeholder) ─────────────────

function alm_seo_fallback_title(WP_Post $post): string {
    $front_id = (int) get_option('page_on_front');
    if ($front_id && $post->ID === $front_id) {
        return 'Almaretna — Villa con piscina alle pendici dell\'Etna | Sicilia';
    }
    switch ($post->post_name) {
        case 'home':    return 'Almaretna — Villa con piscina alle pendici dell\'Etna | Sicilia';
        case 'camere':  return 'Camere — Almaretna | Villa con piscina sull\'Etna';
        case 'prenota': return 'Prenota — Almaretna | Miglior prezzo garantito';
    }
    if ($post->post_type === 'almaretna_room') {
        return $post->post_title . ' — Almaretna | Villa sull\'Etna, Sicilia';
    }
    return $post->post_title . ' — Almaretna';
}

function alm_seo_fallback_desc(WP_Post $post): string {
    $front_id = (int) get_option('page_on_front');
    if ($front_id && $post->ID === $front_id) {
        return 'Prenota il tuo soggiorno ad Almaretna: villa esclusiva con piscina a Nunziata di Mascali, tra Taormina e Catania. Vista sull\'Etna, camere eleganti, miglior prezzo garantito.';
    }
    switch ($post->post_name) {
        case 'home':
            return 'Prenota il tuo soggiorno ad Almaretna: villa esclusiva con piscina a Nunziata di Mascali, tra Taormina e Catania. Vista sull\'Etna, camere eleganti, miglior prezzo garantito.';
        case 'camere':
            return 'Sette camere eleganti ad Almaretna: suite con vista Etna, mansarda romantica, suite famiglia. Piscina esclusiva, parcheggio gratuito. Prenota direttamente al miglior prezzo.';
        case 'prenota':
            return 'Prenota direttamente ad Almaretna: verifica disponibilità, pagamento sicuro con Stripe, miglior prezzo garantito.';
    }
    if (has_excerpt($post)) {
        return wp_strip_all_tags(get_the_excerpt($post));
    }
    return wp_trim_words(wp_strip_all_tags($post->post_content), 30, '…');
}
