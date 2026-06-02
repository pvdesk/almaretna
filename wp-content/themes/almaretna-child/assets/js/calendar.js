/**
 * Almaretna — Availability Calendar Widget
 * Inizializza gli elementi [alm_availability_calendar] (.alm-js-calendar).
 */

/* global alm_theme_vars */

(function () {
    'use strict';

    var DAYS_IT = ['Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa', 'Do'];

    window.AlmCalendar = {

        getBlockedDates: function (roomId, monthFrom, monthTo) {
            var url = alm_theme_vars.rest_url + 'availability'
                + '?room_id=' + encodeURIComponent(roomId)
                + '&month_from=' + encodeURIComponent(monthFrom)
                + '&month_to='   + encodeURIComponent(monthTo || monthFrom);

            return fetch(url).then(function (r) {
                if (!r.ok) return [];
                return r.json().then(function (data) {
                    return data.blocked_dates || [];
                });
            }).catch(function () { return []; });
        },

        formatDate: function (date) {
            var y = date.getFullYear();
            var m = String(date.getMonth() + 1).padStart(2, '0');
            var d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        },

        getNights: function (checkin, checkout) {
            return Math.round((new Date(checkout) - new Date(checkin)) / 86400000);
        },

        formatPrice: function (price) {
            return '€ ' + parseFloat(price || 0).toFixed(2).replace('.', ',');
        },
    };

    // ── Calendar widget init ─────────────────────────────────────────

    function initCalendars() {
        document.querySelectorAll('.alm-js-calendar').forEach(function (el) {
            initOneCalendar(el);
        });
    }

    function initOneCalendar(el) {
        var roomId = el.dataset.roomId;
        if (!roomId) return;

        var today  = new Date();
        var year   = today.getFullYear();
        var month  = today.getMonth(); // 0-based

        var titleEl = el.querySelector('[data-js="cal-title"]');
        var gridEl  = el.querySelector('[data-js="cal-grid"]');
        var prevBtn = el.querySelector('.availability-calendar__nav[data-dir="prev"]');
        var nextBtn = el.querySelector('.availability-calendar__nav[data-dir="next"]');

        if (!gridEl) return;

        function render() {
            var ym = year + '-' + String(month + 1).padStart(2, '0');

            // Title
            if (titleEl) {
                var d = new Date(year, month, 1);
                var monthName = d.toLocaleDateString('it-IT', { month: 'long', year: 'numeric' });
                titleEl.textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);
            }

            // Loading state
            gridEl.innerHTML = '<div style="grid-column:1/-1;text-align:center;padding:2rem;"><span class="spinner"></span></div>';

            // Fetch blocked dates for this month + next (so prev/next feels instant)
            var ymNext = new Date(year, month + 1, 1);
            var ymNextStr = ymNext.getFullYear() + '-' + String(ymNext.getMonth() + 1).padStart(2, '0');

            window.AlmCalendar.getBlockedDates(roomId, ym, ymNextStr).then(function (blocked) {
                renderGrid(gridEl, year, month, blocked);
            });
        }

        function renderGrid(container, y, m, blocked) {
            var blockedSet = {};
            blocked.forEach(function (d) { blockedSet[d] = true; });

            var today     = new Date();
            var todayStr  = window.AlmCalendar.formatDate(today);
            var firstDay  = new Date(y, m, 1);
            var lastDay   = new Date(y, m + 1, 0);
            var startDow  = (firstDay.getDay() + 6) % 7; // Mon=0 … Sun=6
            var totalDays = lastDay.getDate();

            var html = '';

            // Weekday headers
            DAYS_IT.forEach(function (d) {
                html += '<div class="availability-calendar__weekday">' + d + '</div>';
            });

            // Empty cells before first day
            for (var i = 0; i < startDow; i++) {
                html += '<div class="availability-calendar__day availability-calendar__day--empty"></div>';
            }

            // Day cells
            for (var day = 1; day <= totalDays; day++) {
                var dateStr = y + '-' + String(m + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                var cls = 'availability-calendar__day';

                if (dateStr < todayStr) {
                    cls += ' availability-calendar__day--past';
                } else if (blockedSet[dateStr]) {
                    cls += ' availability-calendar__day--booked';
                } else {
                    cls += ' availability-calendar__day--available';
                }

                html += '<div class="' + cls + '">' + day + '</div>';
            }

            container.innerHTML = html;
        }

        // Navigation
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                month--;
                if (month < 0) { month = 11; year--; }
                render();
            });
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                month++;
                if (month > 11) { month = 0; year++; }
                render();
            });
        }

        render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCalendars);
    } else {
        initCalendars();
    }

})();
