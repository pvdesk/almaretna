/**
 * Almaretna — Booking form JS
 * Gestisce la logica premium del widget box prezzi per la singola camera
 */

/* global alm_theme_vars, flatpickr */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Ritarda leggermente l'inizializzazione per disattivare eventuali istanze standard del plugin
        setTimeout(initPremiumPriceBoxes, 50);

        // Gallery lightbox (CSS-only, attiva via click su miniature)
        const thumbLabels = document.querySelectorAll('.room-gallery__thumb');
        thumbLabels.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                thumbLabels.forEach(function (t) { t.classList.remove('is-active'); });
                thumb.classList.add('is-active');
            });
        });
    });

    function initPremiumPriceBoxes() {
        if (typeof flatpickr === 'undefined') return;

        const locale = flatpickr.l10ns && flatpickr.l10ns.it ? flatpickr.l10ns.it : 'default';

        document.querySelectorAll('.room-price-box').forEach(function (box) {
            const roomId = box.dataset.roomId;
            const ciInput = box.querySelector('.alm-datepicker-checkin');
            const coInput = box.querySelector('.alm-datepicker-checkout');
            const priceAmountEl = box.querySelector('[data-js="price-amount"]');
            const bookBtn = box.querySelector('.room-price-box__form a.btn');
            
            if (!ciInput || !coInput || !priceAmountEl || !bookBtn) return;

            // Conserva il prezzo base originale
            const basePriceHtml = priceAmountEl.innerHTML;

            // Distrugge flatpickr esistenti creati dal plugin per evitare conflitti
            if (ciInput._flatpickr) ciInput._flatpickr.destroy();
            if (coInput._flatpickr) coInput._flatpickr.destroy();

            // Crea elemento per messaggi di stato/errore se non esiste
            let statusEl = box.querySelector('.room-price-box__status-msg');
            if (!statusEl) {
                statusEl = document.createElement('div');
                statusEl.className = 'room-price-box__status-msg mt-3 text-center small fw-semibold';
                statusEl.style.transition = 'opacity 0.2s ease';
                statusEl.style.opacity = '0';
                box.querySelector('.room-price-box__form').appendChild(statusEl);
            }

            let fpCo;

            const fpCi = flatpickr(ciInput, {
                locale: locale,
                dateFormat: 'd/m/Y',
                minDate: 'today',
                onChange: function (selectedDates) {
                    if (selectedDates.length === 0) return;
                    const minCheckout = new Date(selectedDates[0]);
                    minCheckout.setDate(minCheckout.getDate() + 1);
                    if (fpCo) {
                        fpCo.set('minDate', minCheckout);
                        if (fpCo.selectedDates[0] && fpCo.selectedDates[0] <= selectedDates[0]) {
                            fpCo.clear();
                        }
                        fpCo.open();
                    }
                    updatePrice();
                }
            });

            fpCo = flatpickr(coInput, {
                locale: locale,
                dateFormat: 'd/m/Y',
                minDate: new Date(Date.now() + 86400000),
                onChange: function () {
                    updatePrice();
                }
            });

            async function updatePrice() {
                const ciDate = fpCi.selectedDates[0];
                const coDate = fpCo.selectedDates[0];

                if (!ciDate || !coDate) {
                    // Ripristina stato iniziale
                    animatePriceChange(basePriceHtml);
                    statusEl.style.opacity = '0';
                    bookBtn.classList.remove('disabled');
                    bookBtn.style.pointerEvents = 'auto';
                    bookBtn.href = bookBtn.href.split('?')[0];
                    return;
                }

                // Converti date in YYYY-MM-DD
                const checkin = toYMD(ciDate);
                const checkout = toYMD(coDate);

                // Mostra stato di caricamento con fade-out temporaneo del prezzo
                priceAmountEl.style.opacity = '0.4';
                statusEl.style.color = 'var(--color-text-light)';
                statusEl.textContent = (alm_theme_vars.i18n && alm_theme_vars.i18n.calculating) ? alm_theme_vars.i18n.calculating : 'Calcolo in corso...';
                statusEl.style.opacity = '1';

                try {
                    const url = `${alm_theme_vars.rest_url}price?room_id=${roomId}&checkin=${checkin}&checkout=${checkout}`;
                    const response = await fetch(url);
                    const data = await response.json();

                    priceAmountEl.style.opacity = '1';

                    if (data.code) {
                        // Errore di disponibilità o soggiorno minimo
                        statusEl.style.color = 'var(--color-error)';
                        statusEl.textContent = data.message || (alm_theme_vars.i18n && alm_theme_vars.i18n.dates_unavail ? alm_theme_vars.i18n.dates_unavail : 'Date non disponibili.');
                        bookBtn.classList.add('disabled');
                        bookBtn.style.pointerEvents = 'none';
                        animatePriceChange(basePriceHtml);
                    } else {
                        // Successo!
                        const nights = data.nights || 1;
                        const total = data.total || data.subtotal || 0;
                        const i18n = alm_theme_vars.i18n || {};
                        const nightsLabel = nights === 1 ? (i18n.night_1 || 'notte') : (i18n.night_n || 'notti');
                        const selectedLabel = i18n.selected || 'selezionate';

                        statusEl.style.color = 'var(--color-success)';
                        statusEl.textContent = `${nights} ${nightsLabel} ${selectedLabel}`;

                        // Formatta prezzo con euro
                        const formattedPrice = formatPrice(total);
                        animatePriceChange(formattedPrice);

                        // Abilita prenota ora e aggiorna URL
                        bookBtn.classList.remove('disabled');
                        bookBtn.style.pointerEvents = 'auto';
                        const baseUrl = bookBtn.href.split('?')[0];
                        bookBtn.href = `${baseUrl}?room=${roomId}&checkin=${checkin}&checkout=${checkout}`;
                    }
                } catch (e) {
                    priceAmountEl.style.opacity = '1';
                    statusEl.style.color = 'var(--color-error)';
                    statusEl.textContent = (alm_theme_vars.i18n && alm_theme_vars.i18n.conn_error) ? alm_theme_vars.i18n.conn_error : 'Errore di connessione.';
                    animatePriceChange(basePriceHtml);
                }
            }

            function animatePriceChange(newHtml) {
                priceAmountEl.style.transition = 'opacity 0.15s ease';
                priceAmountEl.style.opacity = '0';
                setTimeout(() => {
                    priceAmountEl.innerHTML = newHtml;
                    priceAmountEl.style.opacity = '1';
                }, 150);
            }
        });
    }

    function toYMD(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatPrice(price) {
        return `&euro;&nbsp;${parseFloat(price).toFixed(2).replace('.', ',')}`;
    }
})();
