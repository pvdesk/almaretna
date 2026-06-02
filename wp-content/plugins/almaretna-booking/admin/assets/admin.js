/* Almaretna Booking — Admin JS (jQuery slim + vanilla) */
/* global alm_admin */
'use strict';

(function ($) {

    // ── Date validation: end >= start ─────────────────────────────────────
    function bindDateRange(startId, endId) {
        var $start = $('#' + startId);
        var $end   = $('#' + endId);
        if (!$start.length || !$end.length) return;

        $start.on('change', function () {
            var minDate = this.value;
            if (minDate && $end.val() && $end.val() < minDate) {
                $end.val('');
            }
            $end.attr('min', minDate);
        });
    }

    bindDateRange('block-start', 'block-end');
    bindDateRange('rate-start',  'rate-end');

    // ── Conferma azioni distruttive ───────────────────────────────────────
    $('form').on('submit', function () {
        var $btn = $(this).find('[type="submit"].button-link-delete');
        if ($btn.length && !$btn.data('confirmed')) {
            // Già gestito con onclick confirm() nel HTML
        }
    });

    // ── Filter bar auto-submit su select ─────────────────────────────────
    $('.alm-filter-bar select').on('change', function () {
        // Non auto-submit per evitare race condition con altri select
    });

    // ── Dismiss notices ───────────────────────────────────────────────────
    $(document).on('click', '.notice.is-dismissible .notice-dismiss', function () {
        $(this).closest('.notice').fadeOut(200, function () { $(this).remove(); });
    });

}(window.jQuery));
