/**
 * Almaretna — Admin Dashboard JS
 * Caricato solo sul dashboard WP (index.php)
 * Dipende da: jquery, wp.media (wp_enqueue_media)
 */
/* global wp, ajaxurl */
(function () {
    'use strict';

    /* ── Copia shortcode ──────────────────────────────────────── */
    window.almCopy = function (btn, code) {
        function ok() {
            btn.textContent = '✓ Copiato';
            btn.classList.add('copied');
            setTimeout(function () {
                btn.textContent = 'Copia';
                btn.classList.remove('copied');
            }, 2200);
        }
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(code).then(ok);
        } else {
            var ta = document.createElement('textarea');
            ta.value = code;
            ta.style.cssText = 'position:fixed;top:-9999px;opacity:0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try { document.execCommand('copy'); ok(); } catch (e) { /* noop */ }
            document.body.removeChild(ta);
        }
    };

    /* ── Toggle amenity via AJAX ──────────────────────────────── */
    window.almToggleAmenity = function (btn) {
        var roomId  = btn.dataset.room;
        var amenity = btn.dataset.amenity;
        var nonce   = btn.dataset.nonce;
        var enable  = !btn.classList.contains('on');

        btn.classList.add('saving');

        var fd = new FormData();
        fd.append('action',  'alm_toggle_amenity');
        fd.append('nonce',   nonce);
        fd.append('room_id', roomId);
        fd.append('amenity', amenity);
        fd.append('enable',  enable ? '1' : '0');

        fetch(ajaxurl, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.classList.remove('saving');
                if (data.success) {
                    btn.classList.toggle('on', enable);
                    btn.classList.add('saved-ok');
                    setTimeout(function () { btn.classList.remove('saved-ok'); }, 900);
                }
            })
            .catch(function () { btn.classList.remove('saving'); });
    };

    /* ── Seleziona foto camera via WP Media ───────────────────── */
    window.almPickPhoto = function (roomId, nonce) {
        if (typeof wp === 'undefined' || !wp.media) {
            alert('Media uploader non disponibile. Ricarica la pagina.');
            return;
        }

        var frame = wp.media({
            title:    'Scegli immagine per la camera',
            button:   { text: 'Usa questa immagine' },
            multiple: false,
            library:  { type: 'image' }
        });

        frame.on('select', function () {
            var att  = frame.state().get('selection').first().toJSON();
            var wrap = document.getElementById('alm-thumb-wrap-' + roomId);
            var img  = document.getElementById('alm-thumb-img-'  + roomId);
            if (wrap) wrap.classList.add('is-loading');

            var fd = new FormData();
            fd.append('action',   'alm_set_room_thumb');
            fd.append('nonce',    nonce);
            fd.append('room_id',  roomId);
            fd.append('media_id', att.id);

            fetch(ajaxurl, { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (wrap) wrap.classList.remove('is-loading');
                    if (data.success && data.data.thumb) {
                        if (wrap) wrap.classList.remove('ad-room-row__thumb--empty');
                        if (img && img.tagName !== 'IMG') {
                            var newImg    = document.createElement('img');
                            newImg.id     = img.id;
                            newImg.alt    = '';
                            img.parentNode.replaceChild(newImg, img);
                            img = newImg;
                        }
                        if (img) img.src = data.data.thumb + '?t=' + Date.now();
                        var row     = document.getElementById('alm-rr-' + roomId);
                        var pickBtn = row && row.querySelector('.ad-btn--sand');
                        if (pickBtn) pickBtn.innerHTML = '📷 Cambia';
                    }
                })
                .catch(function () { if (wrap) wrap.classList.remove('is-loading'); });
        });

        frame.open();
    };

})();
