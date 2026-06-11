<?php
defined('ABSPATH') || exit;

/* ─── Metabox foto destinazione ──────────────────────────────────────────── */

add_action('add_meta_boxes', 'alm_dest_add_photo_metabox');
function alm_dest_add_photo_metabox() {
    add_meta_box('alm_dest_photos', '📸 Foto destinazione', 'alm_dest_photos_render', 'page', 'normal', 'high');
}

function alm_dest_photos_render($post) {
    $tpl = get_post_meta($post->ID, '_wp_page_template', true);
    if (!in_array($tpl, ['page-destinazione.php', 'page-scopri-la-sicilia.php'], true)) return;

    wp_nonce_field('alm_dest_photos_save', 'alm_dest_nonce');

    $hero_id     = (int) get_post_meta($post->ID, '_alm_dest_hero', true);
    $gallery_raw = (string) get_post_meta($post->ID, '_alm_dest_gallery', true);
    $gallery_ids = array_filter(array_map('intval', explode(',', $gallery_raw)));
    $hero_url    = $hero_id ? wp_get_attachment_image_url($hero_id, 'medium') : '';
    ?>
    <div style="display:flex;gap:32px;flex-wrap:wrap;padding:4px 0">

        <!-- Hero -->
        <div style="flex:0 0 260px">
            <p style="margin:0 0 8px;font-weight:600;font-size:.82rem;text-transform:uppercase;letter-spacing:1px;color:#555">
                Foto principale (hero)
            </p>
            <div id="alm-hero-preview" style="width:260px;height:160px;background:#f0f0f0;border:2px dashed #ccc;border-radius:6px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:8px">
                <?php if ($hero_url): ?>
                    <img src="<?php echo esc_url($hero_url); ?>" style="width:100%;height:100%;object-fit:cover">
                <?php else: ?>
                    <span style="color:#aaa;font-size:.82rem">Nessuna foto</span>
                <?php endif; ?>
            </div>
            <input type="hidden" name="alm_dest_hero" id="alm-hero-id" value="<?php echo esc_attr($hero_id ?: ''); ?>">
            <button type="button" class="button button-secondary" id="alm-hero-btn">Seleziona foto</button>
            <button type="button" class="button" id="alm-hero-rm"
                style="color:#a00;margin-left:6px;<?php echo $hero_id ? '' : 'display:none'; ?>">
                Rimuovi
            </button>
        </div>

        <!-- Gallery -->
        <div style="flex:1;min-width:280px">
            <p style="margin:0 0 8px;font-weight:600;font-size:.82rem;text-transform:uppercase;letter-spacing:1px;color:#555">
                Galleria fotografica
            </p>
            <div id="alm-gallery-wrap" style="display:flex;flex-wrap:wrap;gap:8px;min-height:88px;padding:10px;background:#fafafa;border:1px solid #e0e0e0;border-radius:6px;margin-bottom:8px;align-items:flex-start">
                <?php foreach ($gallery_ids as $gid):
                    $gurl = wp_get_attachment_image_url($gid, 'thumbnail');
                    if (!$gurl) continue;
                ?>
                    <div class="alm-gthumb" data-id="<?php echo (int)$gid; ?>"
                         style="position:relative;width:78px;height:78px;border-radius:5px;overflow:hidden;border:2px solid #ddd;flex-shrink:0">
                        <img src="<?php echo esc_url($gurl); ?>" style="width:100%;height:100%;object-fit:cover">
                        <button type="button" onclick="almGalleryRemove(this,<?php echo (int)$gid; ?>)"
                                style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,.65);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:12px;cursor:pointer;line-height:1;padding:0">
                            ×
                        </button>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($gallery_ids)): ?>
                    <span id="alm-gallery-empty" style="color:#bbb;font-size:.82rem;align-self:center">Nessuna foto</span>
                <?php endif; ?>
            </div>
            <input type="hidden" name="alm_dest_gallery" id="alm-gallery-ids"
                   value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>">
            <button type="button" class="button button-secondary" id="alm-gallery-btn">+ Aggiungi foto</button>
        </div>

    </div>

    <script>
    (function($){
        /* Hero */
        var heroFrame;
        $('#alm-hero-btn').on('click', function(){
            if (!heroFrame) {
                heroFrame = wp.media({title:'Foto principale',button:{text:'Usa questa foto'},multiple:false,library:{type:'image'}});
                heroFrame.on('select', function(){
                    var a = heroFrame.state().get('selection').first().toJSON();
                    $('#alm-hero-id').val(a.id);
                    var url = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                    $('#alm-hero-preview').html('<img src="'+url+'" style="width:100%;height:100%;object-fit:cover">');
                    $('#alm-hero-rm').show();
                });
            }
            heroFrame.open();
        });
        $('#alm-hero-rm').on('click', function(){
            $('#alm-hero-id').val('');
            $('#alm-hero-preview').html('<span style="color:#aaa;font-size:.82rem">Nessuna foto</span>');
            $(this).hide();
        });

        /* Gallery */
        $('#alm-gallery-btn').on('click', function(){
            var frame = wp.media({title:'Galleria fotografica',button:{text:'Aggiungi selezione'},multiple:true,library:{type:'image'}});
            frame.on('select', function(){
                var ids = $('#alm-gallery-ids').val() ? $('#alm-gallery-ids').val().split(',').filter(Boolean) : [];
                $('#alm-gallery-empty').remove();
                frame.state().get('selection').each(function(a){
                    var d = a.toJSON();
                    if (ids.indexOf(String(d.id)) === -1) {
                        ids.push(String(d.id));
                        var thumb = (d.sizes && d.sizes.thumbnail) ? d.sizes.thumbnail.url : d.url;
                        $('#alm-gallery-wrap').append(
                            '<div class="alm-gthumb" data-id="'+d.id+'" style="position:relative;width:78px;height:78px;border-radius:5px;overflow:hidden;border:2px solid #ddd;flex-shrink:0">'+
                            '<img src="'+thumb+'" style="width:100%;height:100%;object-fit:cover">'+
                            '<button type="button" onclick="almGalleryRemove(this,'+d.id+')" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,.65);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:12px;cursor:pointer;line-height:1;padding:0">×</button>'+
                            '</div>'
                        );
                    }
                });
                $('#alm-gallery-ids').val(ids.join(','));
            });
            frame.open();
        });
    })(jQuery);

    function almGalleryRemove(btn, id) {
        btn.closest('.alm-gthumb').remove();
        var $inp = jQuery('#alm-gallery-ids');
        var ids  = $inp.val().split(',').filter(function(x){ return x && parseInt(x) !== id; });
        $inp.val(ids.join(','));
        if (!jQuery('#alm-gallery-wrap .alm-gthumb').length) {
            jQuery('#alm-gallery-wrap').append('<span id="alm-gallery-empty" style="color:#bbb;font-size:.82rem;align-self:center">Nessuna foto</span>');
        }
    }
    </script>
    <?php
}

add_action('save_post_page', 'alm_dest_photos_save');
function alm_dest_photos_save($post_id) {
    if (empty($_POST['alm_dest_nonce'])) return;
    if (!wp_verify_nonce($_POST['alm_dest_nonce'], 'alm_dest_photos_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $hero = absint($_POST['alm_dest_hero'] ?? 0);
    update_post_meta($post_id, '_alm_dest_hero', $hero ?: '');

    $raw   = sanitize_text_field($_POST['alm_dest_gallery'] ?? '');
    $clean = implode(',', array_filter(array_map('absint', explode(',', $raw))));
    update_post_meta($post_id, '_alm_dest_gallery', $clean);
}

add_action('admin_enqueue_scripts', function($hook) {
    if (!in_array($hook, ['post.php', 'post-new.php'])) return;
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'page') wp_enqueue_media();
});

/* ─── Seconda metabox: Immagini sezioni contenuto ────────────────────────────
   3 slot immagine da posizionare nelle sezioni della pagina destinazione.
   Meta: _alm_dest_sec1, _alm_dest_sec2, _alm_dest_sec3
   ─────────────────────────────────────────────────────────────────────────── */

add_action('add_meta_boxes', 'alm_dest_add_sections_metabox');
function alm_dest_add_sections_metabox() {
    add_meta_box('alm_dest_sections', '🖼 Immagini sezioni pagina', 'alm_dest_sections_render', 'page', 'normal', 'default');
}

function alm_dest_sections_render($post) {
    $tpl = get_post_meta($post->ID, '_wp_page_template', true);
    if ($tpl !== 'page-destinazione.php') return;

    wp_nonce_field('alm_dest_sections_save', 'alm_dest_sections_nonce');

    $slots = [
        '_alm_dest_sec1' => 'Sezione 1 — Storia e origini',
        '_alm_dest_sec2' => 'Sezione 2 — Cosa fare',
        '_alm_dest_sec3' => 'Sezione 3 — Consigli pratici',
    ];
    ?>
    <p style="margin:0 0 16px;font-size:.82rem;color:#666">
        Queste immagini compaiono nelle sezioni di testo della pagina.
        Se non carichi un'immagine, la sezione mostra solo il testo.
    </p>
    <div style="display:flex;gap:20px;flex-wrap:wrap">
    <?php foreach ($slots as $meta => $label) :
        $img_id  = (int) get_post_meta($post->ID, $meta, true);
        $img_url = $img_id ? wp_get_attachment_image_url($img_id, 'medium') : '';
        $uniq    = sanitize_key($meta);
    ?>
        <div style="flex:0 0 220px">
            <p style="margin:0 0 6px;font-weight:600;font-size:.78rem;text-transform:uppercase;letter-spacing:1px;color:#555">
                <?php echo esc_html($label); ?>
            </p>
            <div id="<?php echo $uniq; ?>-preview"
                 style="width:220px;height:140px;background:#f5f5f5;border:2px dashed #ccc;border-radius:6px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:8px">
                <?php if ($img_url) : ?>
                    <img src="<?php echo esc_url($img_url); ?>" style="width:100%;height:100%;object-fit:cover">
                <?php else : ?>
                    <span style="color:#bbb;font-size:.8rem">Nessuna foto</span>
                <?php endif; ?>
            </div>
            <input type="hidden" name="<?php echo esc_attr($meta); ?>"
                   id="<?php echo $uniq; ?>-id"
                   value="<?php echo esc_attr($img_id ?: ''); ?>">
            <button type="button" class="button button-secondary alm-sec-pick"
                    data-target="<?php echo $uniq; ?>">Seleziona</button>
            <button type="button" class="button alm-sec-rm"
                    data-target="<?php echo $uniq; ?>"
                    style="color:#a00;margin-left:6px;<?php echo $img_id ? '' : 'display:none'; ?>">
                Rimuovi
            </button>
        </div>
    <?php endforeach; ?>
    </div>

    <script>
    (function($){
        $('.alm-sec-pick').on('click', function(){
            var t = $(this).data('target');
            var frame = wp.media({title:'Immagine sezione',button:{text:'Usa questa'},multiple:false,library:{type:'image'}});
            frame.on('select', function(){
                var a = frame.state().get('selection').first().toJSON();
                $('#'+t+'-id').val(a.id);
                var url = (a.sizes && a.sizes.medium) ? a.sizes.medium.url : a.url;
                $('#'+t+'-preview').html('<img src="'+url+'" style="width:100%;height:100%;object-fit:cover">');
                $('[data-target="'+t+'"].alm-sec-rm').show();
            });
            frame.open();
        });
        $('.alm-sec-rm').on('click', function(){
            var t = $(this).data('target');
            $('#'+t+'-id').val('');
            $('#'+t+'-preview').html('<span style="color:#bbb;font-size:.8rem">Nessuna foto</span>');
            $(this).hide();
        });
    })(jQuery);
    </script>
    <?php
}

add_action('save_post_page', 'alm_dest_sections_save');
function alm_dest_sections_save($post_id) {
    if (empty($_POST['alm_dest_sections_nonce'])) return;
    if (!wp_verify_nonce($_POST['alm_dest_sections_nonce'], 'alm_dest_sections_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    foreach (['_alm_dest_sec1', '_alm_dest_sec2', '_alm_dest_sec3'] as $meta) {
        $val = absint($_POST[$meta] ?? 0);
        update_post_meta($post_id, $meta, $val ?: '');
    }
}
