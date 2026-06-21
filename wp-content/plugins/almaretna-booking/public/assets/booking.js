/* global flatpickr, alm_vars, Stripe */
/* Almaretna Booking — wizard prenotazione + room price box (vanilla JS) */
'use strict';

(function () {

    // ── Costanti globali ─────────────────────────────────────────────────
    const vars  = window.alm_vars || {};
    const i18n  = vars.i18n || {};
    const REST  = (vars.rest_url || '').replace(/\/?$/, '/');

    // ── Wizard: esiste solo nella pagina prenotazione ────────────────────
    const wizard = document.getElementById('alm-booking-wizard');

    // Inizializza sempre i datepicker del room-price-box
    // Lo script è in footer: DOMContentLoaded potrebbe essere già sparato — controlla readyState
    function _almInit() {
        initRoomPriceBoxes();
        if (wizard) initWizard();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', _almInit);
    } else {
        _almInit();
    }

    // ════════════════════════════════════════════════════════════════════
    //  ROOM PRICE BOX  (shortcode [alm_room_price] nelle single camera)
    // ════════════════════════════════════════════════════════════════════

    function initRoomPriceBoxes() {
        const locale = flatpickr && flatpickr.l10ns && flatpickr.l10ns.it
            ? flatpickr.l10ns.it
            : 'default';

        document.querySelectorAll('.alm-datepicker-checkin').forEach(function (ciInput) {
            const roomId  = ciInput.dataset.roomId;
            const coInput = document.querySelector('.alm-datepicker-checkout[data-room-id="' + roomId + '"]');
            if (!coInput) return;

            let fpCo;

            const fpCi = flatpickr(ciInput, {
                locale:     locale,
                dateFormat: 'd/m/Y',
                minDate:    'today',
                onClose:    function (sel) {
                    if (!sel[0]) return;
                    const next = new Date(sel[0]);
                    next.setDate(next.getDate() + 1);
                    if (fpCo) {
                        fpCo.set('minDate', next);
                        if (fpCo.selectedDates[0] && fpCo.selectedDates[0] <= sel[0]) {
                            fpCo.clear();
                        }
                        fpCo.open();
                    }
                },
            });

            fpCo = flatpickr(coInput, {
                locale:     locale,
                dateFormat: 'd/m/Y',
                minDate:    new Date(Date.now() + 86400000),
                onClose:    function (sel) {
                    if (fpCi.selectedDates[0] && sel[0]) {
                        fetchRoomPricePreview(roomId, fpCi.selectedDates[0], sel[0]);
                    }
                },
            });
        });
    }

    async function fetchRoomPricePreview(roomId, checkinDate, checkoutDate) {
        const checkin  = toYMD(checkinDate);
        const checkout = toYMD(checkoutDate);
        const preview  = document.getElementById('rpb-price-preview-' + roomId);
        const btn      = document.getElementById('rpb-btn-' + roomId);

        if (preview) {
            preview.style.display = 'block';
            preview.textContent   = 'Calcolo prezzo in corso…';
        }

        try {
            const data = await apiGet('price', { room_id: roomId, checkin, checkout });

            if (data.code) {
                if (preview) preview.textContent = data.message || 'Prezzo non disponibile.';
                return;
            }

            const nights = data.nights || getNights(checkin, checkout);
            const total  = data.total  || data.subtotal || 0;

            if (preview) {
                preview.textContent =
                    nights + ' ' + (nights === 1 ? 'notte' : 'notti') +
                    ' — Totale: ' + formatPrice(total);
            }
            if (btn) {
                const base = btn.href.split('?')[0];
                btn.href = base + '?room=' + roomId + '&checkin=' + checkin + '&checkout=' + checkout;
            }
        } catch {
            if (preview) preview.textContent = 'Prezzo non disponibile.';
        }
    }

    // ════════════════════════════════════════════════════════════════════
    //  WIZARD PRENOTAZIONE
    // ════════════════════════════════════════════════════════════════════

    function initWizard() {

        // ── Stato ────────────────────────────────────────────────────────
        const state = {
            step:           1,
            checkinDate:    null,   // 'YYYY-MM-DD'
            checkoutDate:   null,
            nights:         0,
            adults:         1,
            children:       0,
            selectedRoom:   null,   // oggetto room dall'API
            priceData:      null,   // risposta GET /price
            availableExtras:[],     // lista extras dal DB
            selectedExtras: {},     // { extra_id: qty }
            bookingId:      null,
            bookingRef:     null,
            clientSecret:   null,
            stripe:         null,
            stripeElements: null,
            stripeReady:    false,
        };

        const NONCE = wizard.dataset.nonce || vars.nonce || '';

        // ── Riferimenti DOM ───────────────────────────────────────────────
        const $  = function (id) { return document.getElementById(id); };

        const allSteps      = wizard.querySelectorAll('.booking-step-content[data-step]');
        const progressItems = document.querySelectorAll('.booking-steps__item');

        // Step 1
        const checkinInput   = $('alm-checkin');
        const checkoutInput  = $('alm-checkout');
        const adultsSelect   = $('alm-adults');
        const childrenSelect = $('alm-children');
        const searchBtn      = $('alm-search-rooms');
        const step1Error     = $('alm-step1-error');

        // Step 2
        const roomsList    = $('alm-rooms-list');
        const roomsLoading = $('alm-rooms-loading');
        const step2Error   = $('alm-step2-error');

        // Step 3
        const firstNameInput = $('alm-first-name');
        const lastNameInput  = $('alm-last-name');
        const emailInput     = $('alm-email');
        const phoneInput     = $('alm-phone');
        const extrasSection  = $('alm-extras-section');
        const extrasList     = $('alm-extras-list');
        const specialReqs    = $('alm-special-requests');
        const privacyCk      = $('alm-privacy');
        const tcCk           = $('alm-tc');
        const toPaymentBtn   = $('alm-to-payment');
        const step3Error     = $('alm-step3-error');

        // Riepilogo step 3
        const summaryImg       = $('alm-summary-img');
        const summaryRoomName  = $('alm-summary-room-name');
        const summaryCheckin   = $('alm-summary-checkin');
        const summaryCheckout  = $('alm-summary-checkout');
        const summaryNights    = $('alm-summary-nights');
        const summaryGuests    = $('alm-summary-guests');
        const summaryRoomPrice = $('alm-summary-room-price');
        const summaryExtrasRow = $('alm-summary-extras-row');
        const summaryExtras    = $('alm-summary-extras');
        const summaryTotal     = $('alm-summary-total');

        // Step 4
        const payRoomName        = $('alm-pay-room-name');
        const payDates           = $('alm-pay-dates');
        const payGuests          = $('alm-pay-guests');
        const payTotal           = $('alm-pay-total');
        const paySummaryRoom     = $('alm-pay-summary-room');
        const paySummaryCheckin  = $('alm-pay-summary-checkin');
        const paySummaryCheckout = $('alm-pay-summary-checkout');
        const paySummaryTotal    = $('alm-pay-summary-total');
        const stripeElContainer  = $('stripe-payment-element');
        const paymentError       = $('payment-error');
        const submitPaymentBtn   = $('alm-submit-payment');

        // Conferma
        const stepConfirm    = $('alm-step-confirm');
        const confirmRef     = $('alm-confirm-ref');
        const confirmDetails = $('alm-confirm-details');

        // ── API ────────────────────────────────────────────────────────────

        function apiGet(path, params) {
            params = params || {};
            const url = new URL(REST + path);
            Object.keys(params).forEach(function (k) {
                url.searchParams.set(k, String(params[k]));
            });
            // Endpoint pubblici: nessun nonce per evitare 403 da pagine in cache
            return fetch(url.toString(), {
                credentials: 'same-origin',
            }).then(function (r) {
                return r.json().then(function (data) {
                    return data;
                });
            });
        }

        function apiPost(path, body) {
            return fetch(REST + path, {
                method:      'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce':   NONCE,
                },
                body: JSON.stringify(body || {}),
            }).then(function (r) { return r.json(); });
        }

        // ── Utilità ────────────────────────────────────────────────────────

        function toYMD(dateObj) {
            const y = dateObj.getFullYear();
            const m = String(dateObj.getMonth() + 1).padStart(2, '0');
            const d = String(dateObj.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        }

        function getNights(checkin, checkout) {
            if (!checkin || !checkout) return 0;
            return Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
        }

        function formatDateDisplay(ymd) {
            if (!ymd) return '—';
            const parts = ymd.split('-');
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }

        function buildGuestsLabel() {
            let s = state.adults + ' ' + (state.adults === 1 ? 'adulto' : 'adulti');
            if (state.children > 0) {
                s += ', ' + state.children + ' ' + (state.children === 1 ? 'bambino' : 'bambini');
            }
            return s;
        }

        function setText(el, val) {
            if (el) el.textContent = String(val);
        }

        function showError(el, msg) {
            if (!el) return;
            el.textContent    = msg || '';
            el.style.display  = msg ? 'block' : 'none';
        }

        function showPayError(msg) {
            if (!paymentError) return;
            var msgEl = paymentError.querySelector('.alm-pay-alert__msg');
            if (msgEl) { msgEl.textContent = msg; } else { paymentError.textContent = msg; }
            paymentError.style.display = msg ? 'flex' : 'none';
            if (msg) { paymentError.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
        }
        if (paymentError) {
            var closeBtn = paymentError.querySelector('.alm-pay-alert__close');
            if (closeBtn) closeBtn.addEventListener('click', function () { showPayError(''); });
        }

        function clearErrors() {
            [step1Error, step2Error, step3Error].forEach(function (el) {
                if (el) { el.textContent = ''; el.style.display = 'none'; }
            });
            showPayError('');
            wizard.querySelectorAll('.form-error').forEach(function (el) {
                el.textContent = '';
            });
        }

        function escHtml(str) {
            const d = document.createElement('div');
            d.textContent = String(str);
            return d.innerHTML;
        }

        function calcExtrasTotal() {
            let total = 0;
            state.availableExtras.forEach(function (extra) {
                const qty = state.selectedExtras[extra.id] || 0;
                if (qty > 0) total += extra.unit_price * qty;
            });
            return total;
        }

        // ── Navigazione step ───────────────────────────────────────────────

        function goToStep(n) {
            allSteps.forEach(function (s) {
                s.classList.toggle('is-active', parseInt(s.dataset.step, 10) === n);
            });
            progressItems.forEach(function (item) {
                const sn = parseInt(item.dataset.step, 10);
                item.classList.toggle('is-active',    sn === n);
                item.classList.toggle('is-completed', sn < n);
            });
            state.step = n;
            const scrollTarget = document.querySelector('.booking-steps') || wizard;
            scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Pulsanti "Indietro"
        wizard.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-goto-step]');
            if (!btn) return;
            goToStep(parseInt(btn.dataset.gotoStep, 10));
        });

        // ── Flatpickr: wizard ─────────────────────────────────────────────

        let fpCheckin, fpCheckout;

        function initWizardDatepickers() {
            const locale = flatpickr && flatpickr.l10ns && flatpickr.l10ns.it
                ? flatpickr.l10ns.it
                : 'default';

            fpCheckin = flatpickr(checkinInput, {
                locale:     locale,
                dateFormat: 'd/m/Y',
                minDate:    'today',
                onClose:    function (sel) {
                    if (!sel[0]) return;
                    state.checkinDate = toYMD(sel[0]);
                    const next = new Date(sel[0]);
                    next.setDate(next.getDate() + 1);
                    fpCheckout.set('minDate', next);
                    if (fpCheckout.selectedDates[0] && fpCheckout.selectedDates[0] <= sel[0]) {
                        fpCheckout.clear();
                        state.checkoutDate = null;
                    }
                    fpCheckout.open();
                },
            });

            fpCheckout = flatpickr(checkoutInput, {
                locale:     locale,
                dateFormat: 'd/m/Y',
                minDate:    new Date(Date.now() + 86400000),
                onClose:    function (sel) {
                    if (!sel[0]) return;
                    state.checkoutDate = toYMD(sel[0]);
                    state.nights       = getNights(state.checkinDate, state.checkoutDate);
                },
            });

            // Prefill da URL params
            const params   = new URLSearchParams(window.location.search);
            const ciParam  = params.get('checkin');
            const coParam  = params.get('checkout');
            if (ciParam) {
                const d = new Date(ciParam);
                if (!isNaN(d)) { fpCheckin.setDate(d, true); state.checkinDate = ciParam; }
            }
            if (coParam) {
                const d = new Date(coParam);
                if (!isNaN(d)) { fpCheckout.setDate(d, true); state.checkoutDate = coParam; }
            }
            if (ciParam && coParam) state.nights = getNights(ciParam, coParam);
        }

        initWizardDatepickers();

        // ── STEP 1: cerca camere ──────────────────────────────────────────

        searchBtn && searchBtn.addEventListener('click', async function () {
            clearErrors();
            if (!state.checkinDate) {
                showError(step1Error, 'Seleziona la data di check-in.');
                return;
            }
            if (!state.checkoutDate) {
                showError(step1Error, 'Seleziona la data di check-out.');
                return;
            }
            state.nights = getNights(state.checkinDate, state.checkoutDate);
            if (state.nights < 1) {
                showError(step1Error, 'Il check-out deve essere successivo al check-in.');
                return;
            }
            state.adults   = parseInt(adultsSelect ? adultsSelect.value : 1, 10)   || 1;
            state.children = parseInt(childrenSelect ? childrenSelect.value : 0, 10) || 0;

            goToStep(2);
            await loadRooms();
        });

        async function loadRooms() {
            if (roomsLoading) roomsLoading.style.display = 'flex';
            if (roomsList)    roomsList.innerHTML = '';
            showError(step2Error, '');

            try {
                const resp = await apiGet('rooms', {
                    checkin:  state.checkinDate,
                    checkout: state.checkoutDate,
                    adults:   state.adults,
                    children: state.children,
                });

                if (roomsLoading) roomsLoading.style.display = 'none';

                // Nonce scaduto (pagina in cache): ricarica silenziosamente per ottenerne uno fresco
                if (resp && resp.code === 'rest_cookie_invalid_nonce') {
                    window.location.reload();
                    return;
                }

                // Altro WP_Error (rate limit, ecc.)
                if (resp && resp.code) {
                    showError(step2Error, resp.message || i18n.error_generic || 'Errore. Riprova.');
                    return;
                }

                const data = Array.isArray(resp) ? resp : (resp.rooms || []);

                if (!data.length) {
                    if (roomsList) {
                        roomsList.innerHTML = '<p class="room-results__empty">' +
                            escHtml(i18n.no_rooms || 'Nessuna camera disponibile per le date selezionate.') +
                            '</p>';
                    }
                    return;
                }

                if (roomsList) {
                    roomsList.innerHTML = data.map(renderRoomCard).join('');
                    roomsList.querySelectorAll('[data-select-room]').forEach(function (btn) {
                        btn.addEventListener('click', function () {
                            const id   = parseInt(this.dataset.selectRoom, 10);
                            const room = data.find(function (r) { return r.id === id; });
                            if (room) onRoomSelected(room);
                        });
                    });
                }

            } catch (err) {
                if (roomsLoading) roomsLoading.style.display = 'none';
                showError(step2Error, (err && err.message) || i18n.error_generic || 'Errore. Riprova.');
            }
        }

        function renderRoomCard(room) {
            const thumb   = room.thumbnail || room.thumbnail_url || '';

            // Piano Intero: badge dedicato
            const entireFloorBadgeHtml = room.is_entire_floor
                ? '<span class="room-result-card__badge room-result-card__badge--entire-floor">Piano Intero</span>'
                : '';

            // Suite CARMINA: badge dedicato invece del piano
            const suiteBadgeHtml = (!room.is_entire_floor && room.is_suite)
                ? '<span class="room-result-card__badge room-result-card__badge--suite">Matrimoniale + Doppia</span>'
                : '';

            let floorText = '';
            if (!room.is_suite && !room.is_entire_floor && room.floor) {
                if (room.floor === 'piano-1' || room.floor === '1') floorText = 'Primo Piano';
                else if (room.floor === 'piano-2' || room.floor === '2') floorText = 'Secondo Piano';
                else if (room.floor === 'mansarda') floorText = 'Mansarda';
                else floorText = room.floor.charAt(0).toUpperCase() + room.floor.slice(1);
            }

            const floorBadgeHtml = (!room.is_suite && !room.is_entire_floor && floorText)
                ? '<span class="room-result-card__badge">' + escHtml(floorText) + '</span>'
                : '';

            const imgHtml = '<div class="room-result-card__image-wrap">' +
                (entireFloorBadgeHtml || suiteBadgeHtml || floorBadgeHtml) +
                (thumb
                    ? '<img src="' + escHtml(thumb) + '" alt="' + escHtml(room.title || room.name || '') + '" class="room-result-card__image" loading="lazy" />'
                    : '<div class="room-result-card__img-placeholder"></div>'
                ) +
                '</div>';

            const capacityText = room.is_entire_floor
                ? 'Tutte le camere del piano'
                : room.is_suite
                    ? 'Fino a ' + (room.capacity_adults || 2) + ' adulti + ' + (room.capacity_children || 2) + ' bambini'
                    : 'Fino a ' + (room.capacity_adults || 2) + ' ospiti' +
                      (room.capacity_children > 0 ? ' + ' + room.capacity_children + ' bimbi' : '');
            
            const bathroomText = room.bathroom_type === 'shared' ? 'Bagno condiviso' : 'Bagno privato';
            const bathroomIcon = room.bathroom_type === 'shared' ? 'dashicons-warning' : 'dashicons-yes-alt';

            const metaHtml = '<div class="room-result-card__meta">' +
                '<span class="room-result-card__meta-item">' +
                '<span class="dashicons dashicons-admin-users"></span> ' + escHtml(capacityText) +
                '</span>' +
                '<span class="room-result-card__meta-item">' +
                '<span class="dashicons ' + bathroomIcon + '"></span> ' + escHtml(bathroomText) +
                '</span>' +
                '</div>';

            const amenitiesHtml = (room.amenities && room.amenities.length)
                ? '<div class="room-result-card__amenities">' +
                  room.amenities.slice(0, 5).map(function(a) {
                      return '<span class="room-result-card__amenity-tag">' + escHtml(a.name) + '</span>';
                  }).join('') +
                  '</div>'
                : '';

            const sharingHtml = room.sharing_note
                ? '<div class="room-result-card__sharing-note">' + escHtml(room.sharing_note) + '</div>'
                : '';

            const suiteNoteHtml = room.is_suite && room.suite_note
                ? '<div class="room-result-card__suite-note">' +
                  '<span class="dashicons dashicons-admin-home" style="font-size:14px;width:14px;height:14px;vertical-align:middle;margin-right:4px;"></span>' +
                  escHtml(room.suite_note) + '</div>'
                : '';

            const entireFloorNoteHtml = room.is_entire_floor && room.entire_floor_note
                ? '<div class="room-result-card__entire-floor-note">' +
                  '<span class="dashicons dashicons-admin-home" style="font-size:14px;width:14px;height:14px;vertical-align:middle;margin-right:4px;"></span>' +
                  escHtml(room.entire_floor_note) + '</div>'
                : '';

            const minStayHtml = (room.min_stay && room.min_stay > 1)
                ? '<div style="color:var(--color-text-light);font-size:.75rem;margin-top:.25rem;">' +
                  '<span class="dashicons dashicons-calendar-alt" style="font-size:14px;width:14px;height:14px;vertical-align:middle;margin-right:4px;"></span>' +
                  'Soggiorno minimo: ' + room.min_stay + ' notti</div>'
                : '';

            const nightLabel    = state.nights === 1 ? 'notte' : 'notti';
            const pricePerNight = parseFloat(room.price_per_night || room.base_price || 0);
            const totalEur      = parseFloat(room.total_price || 0) || (pricePerNight * state.nights);
            const totalPriceHtml = pricePerNight
                ? '<div class="room-result-card__price-total">Totale ' + formatPrice(totalEur) + ' per ' + state.nights + ' ' + nightLabel + '</div>'
                : '';

            const isSelected = state.selectedRoom && state.selectedRoom.id === room.id;
            const btnText = isSelected ? 'Selezionata' : 'Seleziona';
            const btnClass = isSelected ? 'btn btn-success' : 'btn btn-primary';
            const cardClass = isSelected ? 'room-result-card is-selected' : 'room-result-card';

            return '<div class="' + cardClass + '" role="listitem">' +
                imgHtml +
                '<div class="room-result-card__body">' +
                '<h3 class="room-result-card__title">' + escHtml(room.title || room.name || '') + '</h3>' +
                metaHtml +
                amenitiesHtml +
                minStayHtml +
                suiteNoteHtml +
                entireFloorNoteHtml +
                sharingHtml +
                '</div>' +
                '<div class="room-result-card__pricing">' +
                '<div class="room-result-card__price-box">' +
                '<div class="room-result-card__price-per-night">' +
                '<span class="room-result-card__price-amount">€ ' + escHtml(pricePerNight.toFixed(2).replace('.', ',')) + '</span>' +
                '<span class="room-result-card__price-unit"> / notte</span>' +
                '</div>' +
                totalPriceHtml +
                '</div>' +
                '<button type="button" class="' + btnClass + '" data-select-room="' + room.id + '">' + escHtml(btnText) + '</button>' +
                '</div>' +
                '</div>';
        }

        // ── STEP 2 → STEP 3: camera selezionata ──────────────────────────

        async function onRoomSelected(room) {
            state.selectedRoom = room;
            clearErrors();

            // Evidenzia la selezione
            if (roomsList) {
                roomsList.querySelectorAll('.room-result-card').forEach(function (card) {
                    card.classList.remove('is-selected');
                    const btn = card.querySelector('[data-select-room]');
                    if (btn) {
                        btn.className = 'btn btn-primary';
                        btn.textContent = 'Seleziona';
                    }
                });
                const selBtn = roomsList.querySelector('[data-select-room="' + room.id + '"]');
                if (selBtn) {
                    const card = selBtn.closest('.room-result-card');
                    if (card) {
                        card.classList.add('is-selected');
                        selBtn.className = 'btn btn-success';
                        selBtn.textContent = 'Selezionata';
                    }
                }
            }

            try {
                const priceData = await apiGet('price', {
                    room_id:  room.id,
                    checkin:  state.checkinDate,
                    checkout: state.checkoutDate,
                });

                if (priceData.code) {
                    showError(step2Error, priceData.message || i18n.error_generic || 'Errore nel calcolo del prezzo.');
                    return;
                }

                state.priceData      = priceData;
                state.selectedExtras = {};
                state.availableExtras= [];

                await loadAvailableExtras();
                updateSummary();
                goToStep(3);

            } catch (err) {
                showError(step2Error, (err && err.message) || i18n.error_generic || 'Errore. Riprova.');
            }
        }

        // ── Extras ────────────────────────────────────────────────────────

        async function loadAvailableExtras() {
            if (!extrasSection || !extrasList) return;
            try {
                const data = await apiGet('extras');
                if (!Array.isArray(data) || !data.length) return;

                state.availableExtras = data;
                extrasList.innerHTML  = data.map(renderExtraRow).join('');
                extrasSection.style.display = 'block';

                extrasList.querySelectorAll('.alm-extra-qty').forEach(function (input) {
                    input.addEventListener('change', function () {
                        const id  = parseInt(this.dataset.extraId, 10);
                        const qty = Math.max(0, parseInt(this.value, 10) || 0);
                        state.selectedExtras[id] = qty;
                        updateSummary();
                    });
                });
            } catch {
                // Endpoint extras assente — non bloccante
            }
        }

        function renderExtraRow(extra) {
            const unitEur = parseFloat(extra.unit_price || 0).toFixed(2).replace('.', ',');
            const priceLabels = {
                per_night:  '€ ' + unitEur + ' / notte',
                per_person: '€ ' + unitEur + ' / persona',
                per_stay:   '€ ' + unitEur,
            };
            const priceLabel = priceLabels[extra.billing_type] || '€ ' + unitEur;

            return '<div class="extra-row" style="display:flex;align-items:center;justify-content:space-between;' +
                'padding:.625rem 0;border-bottom:1px solid var(--color-border);">' +
                '<div>' +
                '<span style="font-weight:500;">' + escHtml(extra.name) + '</span>' +
                '<small style="display:block;color:var(--color-text-light);">' + escHtml(priceLabel) + '</small>' +
                '</div>' +
                '<input type="number" class="form-control alm-extra-qty" data-extra-id="' + extra.id + '"' +
                ' min="0" max="10" value="0" style="width:72px;text-align:center;" />' +
                '</div>';
        }

        // ── Riepilogo ─────────────────────────────────────────────────────

        function updateSummary() {
            const room  = state.selectedRoom;
            const price = state.priceData;
            if (!room || !price) return;

            const thumb = room.thumbnail || room.thumbnail_url || '';
            if (summaryImg && thumb) {
                summaryImg.src           = thumb;
                summaryImg.alt           = room.title || room.name || '';
                summaryImg.style.display = 'block';
            }
            setText(summaryRoomName,  room.title || room.name || '');
            setText(summaryCheckin,   formatDateDisplay(state.checkinDate));
            setText(summaryCheckout,  formatDateDisplay(state.checkoutDate));
            setText(summaryNights,    state.nights + ' ' + (state.nights === 1 ? 'notte' : 'notti'));
            setText(summaryGuests,    buildGuestsLabel());
            setText(summaryRoomPrice, formatPrice(price.subtotal || 0));

            const extrasTotal = calcExtrasTotal();
            if (summaryExtrasRow && summaryExtras) {
                summaryExtrasRow.style.display = extrasTotal > 0 ? 'flex' : 'none';
                setText(summaryExtras, formatPrice(extrasTotal));
            }
            const total = (price.subtotal || 0) + extrasTotal;
            setText(summaryTotal, formatPrice(total));
        }

        // ── STEP 3 → STEP 4: procedi al pagamento ────────────────────────

        toPaymentBtn && toPaymentBtn.addEventListener('click', async function () {
            clearErrors();
            if (!validateStep3()) return;

            const origLabel  = this.textContent;
            this.disabled    = true;
            this.textContent = i18n.processing || 'Elaborazione…';

            try {
                const extrasPayload = state.availableExtras
                    .filter(function (e) { return (state.selectedExtras[e.id] || 0) > 0; })
                    .map(function (e) { return { extra_id: e.id, qty: state.selectedExtras[e.id] }; });

                const res = await apiPost('booking/create', {
                    room_id:           state.selectedRoom.id,
                    checkin:           state.checkinDate,
                    checkout:          state.checkoutDate,
                    adults:            state.adults,
                    children:          state.children,
                    guest_first_name:  firstNameInput ? firstNameInput.value.trim() : '',
                    guest_last_name:   lastNameInput  ? lastNameInput.value.trim()  : '',
                    guest_email:       emailInput     ? emailInput.value.trim()     : '',
                    guest_phone:       phoneInput     ? phoneInput.value.trim()     : '',
                    special_requests:  specialReqs    ? specialReqs.value.trim()    : '',
                    extras:            extrasPayload,
                });

                if (!res.booking_id) {
                    showError(step3Error, res.message || i18n.error_generic || 'Errore nella prenotazione. Riprova.');
                    return;
                }

                state.bookingId    = res.booking_id;
                state.bookingRef   = res.booking_ref;
                state.clientSecret = res.payment_intent_client_secret;

                populateStep4();
                goToStep(4);
                await initStripeElement();

            } catch (err) {
                showError(step3Error, (err && err.message) || i18n.error_generic || 'Errore. Riprova.');
            } finally {
                this.disabled    = false;
                this.textContent = origLabel;
            }
        });

        function validateStep3() {
            let valid = true;

            const fields = [
                ['alm-first-name', firstNameInput, 'Il nome è obbligatorio.'],
                ['alm-last-name',  lastNameInput,  'Il cognome è obbligatorio.'],
                ['alm-email',      emailInput,     'L\'email è obbligatoria.'],
                ['alm-phone',      phoneInput,     'Il telefono è obbligatorio.'],
            ];

            fields.forEach(function (row) {
                const errId = row[0], field = row[1], msg = row[2];
                const errEl = document.getElementById(errId + '-error');
                if (!field || !field.value.trim()) {
                    if (errEl) errEl.textContent = msg;
                    valid = false;
                }
            });

            if (emailInput && emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                const errEl = document.getElementById('alm-email-error');
                if (errEl) errEl.textContent = 'Inserisci un\'email valida.';
                valid = false;
            }

            if (!valid) return false;

            if (!privacyCk || !privacyCk.checked) {
                showError(step3Error, i18n.accept_privacy || 'Accetta la privacy policy per continuare.');
                return false;
            }
            if (!tcCk || !tcCk.checked) {
                showError(step3Error, i18n.accept_tc || 'Accetta i termini e condizioni per continuare.');
                return false;
            }

            return true;
        }

        function populateStep4() {
            const room  = state.selectedRoom;
            const price = state.priceData;
            const extrasTotal = calcExtrasTotal();
            const total       = (price.subtotal || 0) + extrasTotal;
            const totalFmt    = formatPrice(total);
            const datesStr    = formatDateDisplay(state.checkinDate) + ' → ' + formatDateDisplay(state.checkoutDate);

            setText(payRoomName,        room.title || room.name || '');
            setText(payDates,           datesStr);
            setText(payGuests,          buildGuestsLabel());
            setText(payTotal,           totalFmt);
            setText(paySummaryRoom,     room.title || room.name || '');
            setText(paySummaryCheckin,  formatDateDisplay(state.checkinDate));
            setText(paySummaryCheckout, formatDateDisplay(state.checkoutDate));
            setText(paySummaryTotal,    totalFmt);

            if (submitPaymentBtn) {
                var lbl = submitPaymentBtn.querySelector('.alm-pay-btn__label');
                if (lbl) { lbl.textContent = 'Paga ' + totalFmt; }
                else { submitPaymentBtn.textContent = 'Paga ' + totalFmt; }
            }
        }

        // ── Stripe Payment Element ────────────────────────────────────────

        async function initStripeElement() {
            const pubKey = vars.stripe_publishable_key;
            if (!pubKey || !state.clientSecret) {
                if (stripeElContainer) {
                    stripeElContainer.innerHTML =
                        '<p style="color:var(--color-error,#c62828);padding:1rem 0;">' +
                        'Configurazione pagamento non disponibile. Contatta la struttura.</p>';
                }
                return;
            }

            try {
                const stripe = Stripe(pubKey);
                state.stripe = stripe;

                const elements = stripe.elements({
                    clientSecret: state.clientSecret,
                    appearance: {
                        theme: 'stripe',
                        variables: {
                            colorPrimary:    '#C5B49C',
                            colorBackground: '#ffffff',
                            colorText:       '#5C5246',
                            fontFamily:      'Inter, system-ui, -apple-system, sans-serif',
                            borderRadius:    '8px',
                        },
                        rules: {
                            '.Input': {
                                border:  '1px solid #EFEBE4',
                                padding: '10px 12px',
                            },
                            '.Input:focus': {
                                borderColor: '#C5B49C',
                                boxShadow:   '0 0 0 3px rgba(197,180,156,.20)',
                            },
                            '.AccordionItem': {
                                border:       '1px solid #EFEBE4',
                                borderRadius: '8px',
                                marginBottom: '8px',
                            },
                            '.AccordionItem--selected': {
                                borderColor: '#C5B49C',
                                boxShadow:   '0 0 0 1px #C5B49C',
                            },
                        },
                    },
                });
                state.stripeElements = elements;

                if (stripeElContainer) stripeElContainer.innerHTML = '';

                const paymentEl = elements.create('payment', {
                    layout: {
                        type:               'accordion',
                        defaultCollapsed:    false,
                        radios:              false,
                        spacedAccordionItems: true,
                    },
                });
                paymentEl.mount('#stripe-payment-element');

                paymentEl.on('ready', function () {
                    state.stripeReady = true;
                    if (submitPaymentBtn) submitPaymentBtn.disabled = false;
                });

                paymentEl.on('change', function (e) {
                    showPayError(e.error ? e.error.message : '');
                });

            } catch {
                if (stripeElContainer) {
                    stripeElContainer.innerHTML =
                        '<p style="color:var(--color-error,#c62828);padding:1rem 0;">' +
                        'Errore nel caricamento del modulo pagamento. Aggiorna la pagina.</p>';
                }
            }
        }

        submitPaymentBtn && submitPaymentBtn.addEventListener('click', async function () {
            if (!state.stripeReady || !state.stripe || !state.stripeElements) return;

            const btn = this;
            btn.disabled = true;
            btn.classList.add('is-loading');
            showPayError('');

            const returnUrl = buildReturnUrl();

            try {
                const result = await state.stripe.confirmPayment({
                    elements:      state.stripeElements,
                    confirmParams: { return_url: returnUrl },
                    redirect:      'if_required',
                });

                if (result.error) {
                    showPayError(result.error.message);
                    btn.disabled = false;
                    btn.classList.remove('is-loading');
                    return;
                }

                if (result.paymentIntent && result.paymentIntent.status === 'succeeded') {
                    let confirmNetworkError = false;
                    try {
                        const confirmRes = await apiPost('booking/confirm', {
                            ref:               state.bookingRef,
                            payment_intent_id: result.paymentIntent.id,
                        });
                        if (!confirmRes || confirmRes.code) {
                            showPayError(
                                'Pagamento ricevuto — errore nella conferma. ' +
                                'Contatta la struttura con riferimento: ' + (state.bookingRef || '')
                            );
                            btn.disabled = false;
                            btn.classList.remove('is-loading');
                            return;
                        }
                    } catch {
                        confirmNetworkError = true;
                    }
                    showConfirmation();
                    if (confirmNetworkError && confirmDetails) {
                        confirmDetails.insertAdjacentHTML('afterend',
                            '<p style="color:var(--color-warning,#f57c00);margin-top:var(--space-md);' +
                            'font-size:var(--fs-sm);">Se non ricevi email di conferma entro pochi minuti, ' +
                            'contatta la struttura indicando il riferimento: ' +
                            escHtml(state.bookingRef || '') + '</p>'
                        );
                    }
                }

            } catch {
                showPayError(i18n.error_payment || 'Errore nel pagamento. Verifica i dati della carta.');
                btn.disabled = false;
                btn.classList.remove('is-loading');
            }
        });

        function buildReturnUrl() {
            const base = vars.confirmation_url
                ? String(vars.confirmation_url).replace(/\/?$/, '/')
                : window.location.href.split('?')[0] + '/';
            return base + '?booking_ref=' + encodeURIComponent(state.bookingRef || '');
        }

        // ── Persistenza stato (cambio lingua) ────────────────────────────

        function saveStateToSession() {
            if (!state.checkinDate) return;
            try {
                sessionStorage.setItem('alm_booking_state', JSON.stringify({
                    ts:            Date.now(),
                    checkinDate:   state.checkinDate,
                    checkoutDate:  state.checkoutDate,
                    nights:        state.nights,
                    adults:        state.adults,
                    children:      state.children,
                    step:          Math.min(state.step, 3),
                    selectedRoom:  state.selectedRoom,
                    priceData:     state.priceData,
                    selectedExtras:state.selectedExtras,
                    firstName:     firstNameInput  ? firstNameInput.value.trim()  : '',
                    lastName:      lastNameInput   ? lastNameInput.value.trim()   : '',
                    email:         emailInput      ? emailInput.value.trim()      : '',
                    phone:         phoneInput      ? phoneInput.value.trim()      : '',
                }));
            } catch (e) {}
        }

        function clearSessionState() {
            try { sessionStorage.removeItem('alm_booking_state'); } catch (e) {}
        }

        window.addEventListener('beforeunload', function () {
            if (state.step > 1 || state.checkinDate) saveStateToSession();
        });

        // ── Conferma ──────────────────────────────────────────────────────

        function showConfirmation() {
            clearSessionState();
            allSteps.forEach(function (s) { s.classList.remove('is-active'); });

            if (stepConfirm) {
                stepConfirm.style.display = 'block';
                stepConfirm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            if (confirmRef) confirmRef.textContent = '#' + (state.bookingRef || '—');

            if (confirmDetails && state.selectedRoom) {
                const r = state.selectedRoom;
                confirmDetails.innerHTML =
                    '<p><strong>Camera:</strong> '    + escHtml(r.name)                              + '</p>' +
                    '<p><strong>Check-in:</strong> '  + escHtml(formatDateDisplay(state.checkinDate)) +
                    ' dalle ore ' + escHtml(vars.checkin_time || '15:00')                             + '</p>' +
                    '<p><strong>Check-out:</strong> ' + escHtml(formatDateDisplay(state.checkoutDate)) +
                    ' entro le ore ' + escHtml(vars.checkout_time || '11:00')                         + '</p>' +
                    '<p><strong>Ospiti:</strong> '    + escHtml(buildGuestsLabel())                   + '</p>';
            }

            progressItems.forEach(function (item) { item.classList.add('is-completed'); });
        }

        // ── Ritorno da redirect Stripe ────────────────────────────────────

        (function handleRedirectReturn() {
            const params         = new URLSearchParams(window.location.search);
            const redirectStatus = params.get('redirect_status');
            const bookingRef     = params.get('booking_ref');

            if (!redirectStatus || !bookingRef) return;

            if (redirectStatus === 'succeeded') {
                state.bookingRef = bookingRef;
                const paymentIntentId = params.get('payment_intent') || '';

                // Chiama booking/confirm per garantire DB + email anche se il webhook è in ritardo
                apiPost('booking/confirm', {
                    ref:               bookingRef,
                    payment_intent_id: paymentIntentId,
                }).catch(function () {});

                apiGet('booking/status', { ref: bookingRef })
                    .then(function (data) {
                        if (data && !data.code) {
                            state.selectedRoom = { name: data.room_name || 'Camera' };
                            state.checkinDate  = data.checkin  || null;
                            state.checkoutDate = data.checkout || null;
                            state.adults       = data.adults   || 1;
                            state.children     = data.children || 0;
                        }
                        showConfirmation();
                    })
                    .catch(function () { showConfirmation(); });

            } else if (redirectStatus === 'requires_payment_method') {
                goToStep(4);
                showPayError(i18n.error_payment || 'Pagamento non completato. Riprova con un\'altra carta.');
            }
        })();

        // ── Prefill URL params + ripristino sessione (cambio lingua) ─────

        (function prefillFromUrl() {
            const params   = new URLSearchParams(window.location.search);
            const roomId   = params.get('room');
            const checkin  = params.get('checkin');
            const checkout = params.get('checkout');
            const adults   = params.get('adults');
            const children = params.get('children');

            if (checkin || checkout || roomId) {
                // URL params presenti: prefill diretto (link da room-price-box)
                if (checkin  && fpCheckin)  { fpCheckin.setDate(new Date(checkin),   true); state.checkinDate = checkin; }
                if (checkout && fpCheckout) { fpCheckout.setDate(new Date(checkout), true); state.checkoutDate = checkout; }
                if (adults   && adultsSelect)   adultsSelect.value   = adults;
                if (children && childrenSelect) childrenSelect.value = children;
                if (checkin && checkout) state.nights = getNights(checkin, checkout);

                if (roomId && checkin && checkout) {
                    state.adults   = parseInt(adults,   10) || 1;
                    state.children = parseInt(children, 10) || 0;
                    setTimeout(function () {
                        goToStep(2);
                        loadRooms().then(function () {
                            const selBtn = roomsList &&
                                roomsList.querySelector('[data-select-room="' + roomId + '"]');
                            if (selBtn) selBtn.click();
                        });
                    }, 200);
                }
                return;
            }

            // Nessun URL param — prova a ripristinare lo stato salvato (es. dopo cambio lingua)
            try {
                const raw = sessionStorage.getItem('alm_booking_state');
                if (!raw) return;
                const saved = JSON.parse(raw);
                if (!saved || !saved.ts || !saved.checkinDate || Date.now() - saved.ts > 1800000) {
                    clearSessionState();
                    return;
                }

                // Ripristina stato
                state.checkinDate    = saved.checkinDate;
                state.checkoutDate   = saved.checkoutDate  || null;
                state.nights         = saved.nights        || 0;
                state.adults         = saved.adults        || 1;
                state.children       = saved.children      || 0;
                state.selectedExtras = saved.selectedExtras || {};

                // Ripristina UI step 1
                if (fpCheckin  && saved.checkinDate)  fpCheckin.setDate(new Date(saved.checkinDate),  true);
                if (fpCheckout && saved.checkoutDate) fpCheckout.setDate(new Date(saved.checkoutDate), true);
                if (adultsSelect   && saved.adults   > 0)  adultsSelect.value   = String(saved.adults);
                if (childrenSelect && saved.children >= 0) childrenSelect.value = String(saved.children);

                // Ripristina dati ospite (step 3)
                if (firstNameInput && saved.firstName) firstNameInput.value = saved.firstName;
                if (lastNameInput  && saved.lastName)  lastNameInput.value  = saved.lastName;
                if (emailInput     && saved.email)     emailInput.value     = saved.email;
                if (phoneInput     && saved.phone)     phoneInput.value     = saved.phone;

                if (saved.step < 2 || !saved.checkoutDate) return;

                // Ripristina step 2 e camere
                setTimeout(function () {
                    goToStep(2);
                    loadRooms().then(function () {
                        if (saved.step < 3 || !saved.selectedRoom) return;

                        // Ripristina camera selezionata e vai a step 3
                        state.selectedRoom = saved.selectedRoom;
                        state.priceData    = saved.priceData;
                        updateSummary();

                        if (roomsList) {
                            const selBtn = roomsList.querySelector('[data-select-room="' + saved.selectedRoom.id + '"]');
                            if (selBtn) {
                                const card = selBtn.closest('.room-result-card');
                                if (card) card.classList.add('is-selected');
                                selBtn.className   = 'btn btn-success';
                                selBtn.textContent = 'Selezionata';
                            }
                        }

                        if (saved.priceData) goToStep(3);
                    });
                }, 200);
            } catch (e) {}
        })();

    } // end initWizard()

    // ════════════════════════════════════════════════════════════════════
    //  Funzioni condivise (wizard + room price box)
    // ════════════════════════════════════════════════════════════════════

    function apiGet(path, params) {
        params = params || {};
        const url = new URL(REST + path);
        Object.keys(params).forEach(function (k) {
            url.searchParams.set(k, String(params[k]));
        });
        return fetch(url.toString(), {
            credentials: 'same-origin',
        }).then(function (r) { return r.json(); });
    }

    function toYMD(dateObj) {
        const y = dateObj.getFullYear();
        const m = String(dateObj.getMonth() + 1).padStart(2, '0');
        const d = String(dateObj.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function getNights(checkin, checkout) {
        if (!checkin || !checkout) return 0;
        return Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
    }

    function formatPrice(euros) {
        const val = parseFloat(euros || 0).toFixed(2).replace('.', ',');
        return '€ ' + val.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

})();
