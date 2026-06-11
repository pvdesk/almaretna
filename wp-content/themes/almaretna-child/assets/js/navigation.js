/* Almaretna — Navigation: hamburger, scroll header, booking-bar date logic */
(function () {
    'use strict';

    var header    = document.getElementById('site-header');
    var toggle    = document.getElementById('nav-toggle');
    var mobileNav = document.getElementById('mobile-nav');
    var closeBtn  = document.getElementById('nav-close');
    var backdrop  = document.getElementById('nav-backdrop');

    // ── Scroll: header background ─────────────────────────────────
    if (header) {
        var scrolled = false;
        window.addEventListener('scroll', function () {
            var shouldScroll = window.scrollY > 60;
            if (shouldScroll !== scrolled) {
                scrolled = shouldScroll;
                header.classList.toggle('is-scrolled', scrolled);
            }
        }, { passive: true });
    }

    // ── Mobile nav open/close ─────────────────────────────────────
    function openNav() {
        if (!mobileNav) return;
        mobileNav.classList.add('is-open');
        mobileNav.setAttribute('aria-hidden', 'false');
        if (backdrop) backdrop.classList.add('is-visible');
        if (toggle)   toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeNav() {
        if (!mobileNav) return;
        mobileNav.classList.remove('is-open');
        mobileNav.setAttribute('aria-hidden', 'true');
        if (backdrop) backdrop.classList.remove('is-visible');
        if (toggle)   toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (toggle)   toggle.addEventListener('click', openNav);
    if (closeBtn) closeBtn.addEventListener('click', closeNav);
    if (backdrop) backdrop.addEventListener('click', closeNav);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeNav();
    });

    // ── Booking bar: auto-advance checkin → checkout ──────────────
    var checkinInput  = document.getElementById('hb-checkin');
    var checkoutInput = document.getElementById('hb-checkout');

    if (checkinInput && checkoutInput) {
        checkinInput.addEventListener('change', function () {
            var ci = this.value;
            if (!ci) return;
            // checkout min = checkin + 1 day
            var minDate = new Date(ci);
            minDate.setDate(minDate.getDate() + 1);
            var y = minDate.getFullYear();
            var m = String(minDate.getMonth() + 1).padStart(2, '0');
            var d = String(minDate.getDate()).padStart(2, '0');
            checkoutInput.min = y + '-' + m + '-' + d;

            if (checkoutInput.value && checkoutInput.value <= ci) {
                checkoutInput.value = '';
            }
            checkoutInput.focus();
        });
    }

}());

/* ─── Mega Menu ─────────────────────────────────────────────────────────── */
(function () {
    'use strict';

    var li       = document.getElementById('nav-scopri-li');
    var btn      = document.getElementById('nav-scopri-btn');
    var panel    = document.getElementById('nav-mega-scopri');
    var backdrop = document.getElementById('nav-mega-backdrop');

    if (!li || !btn || !panel) return;

    var loaded     = false;
    var hoverTimer = null;
    var closeTimer = null;
    var isTouch    = window.matchMedia('(hover: none)').matches;

    function openMega() {
        clearTimeout(closeTimer);
        li.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
        if (backdrop) backdrop.classList.add('is-visible');
        if (!loaded) { loaded = true; fetchImages(); }
    }

    function closeMega() {
        li.classList.remove('is-open');
        btn.setAttribute('aria-expanded', 'false');
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
        if (backdrop) backdrop.classList.remove('is-visible');
    }

    /* Hover (pointer device only) */
    if (!isTouch) {
        li.addEventListener('mouseenter', function () {
            clearTimeout(closeTimer);
            hoverTimer = setTimeout(openMega, 80);
        });
        li.addEventListener('mouseleave', function () {
            clearTimeout(hoverTimer);
            closeTimer = setTimeout(closeMega, 180);
        });
        panel.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
        panel.addEventListener('mouseleave', function () {
            closeTimer = setTimeout(closeMega, 180);
        });
    }

    /* Click (all devices) */
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (li.classList.contains('is-open')) { closeMega(); } else { openMega(); }
    });

    if (backdrop) backdrop.addEventListener('click', closeMega);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && li.classList.contains('is-open')) {
            closeMega();
            btn.focus();
        }
    });

    document.addEventListener('click', function (e) {
        if (!li.contains(e.target) && !panel.contains(e.target)) {
            closeMega();
        }
    });

    /* ── AJAX: carica hero images al primo apertura ── */
    function fetchImages() {
        if (!window.almNav || !window.almNav.ajaxUrl) {
            revealCards();
            return;
        }
        var grid = document.getElementById('nav-mega-grid');
        if (!grid) return;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', window.almNav.ajaxUrl);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            try {
                var res = JSON.parse(xhr.responseText);
                if (res.success && res.data) {
                    grid.querySelectorAll('.mega-card').forEach(function (card) {
                        var slug = card.getAttribute('data-slug');
                        if (slug && res.data[slug]) {
                            var img = card.querySelector('.mega-card__img');
                            if (img) img.style.backgroundImage = 'url(' + res.data[slug] + ')';
                        }
                    });
                }
            } catch (err) {}
            revealCards();
        };
        xhr.onerror = revealCards;
        xhr.send('action=alm_nav_destinations');
    }

    function revealCards() {
        var cards = panel.querySelectorAll('.mega-card');
        cards.forEach(function (card, i) {
            card.classList.remove('mega-card--skeleton');
            setTimeout(function () { card.classList.add('is-visible'); }, i * 55);
        });
    }

    /* ── Mobile accordion ── */
    var mobileItem   = document.querySelector('.mobile-nav__item--has-sub');
    var mobileToggle = mobileItem ? mobileItem.querySelector('.mobile-nav__sub-toggle') : null;

    if (mobileItem && mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            var open = mobileItem.classList.toggle('is-open');
            mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

}());

/* ─── Scroll Reveal (Intersection Observer) ────────────────── */
(function () {
    'use strict';

    // Hero background: zoom out on load
    var heroBg = document.getElementById('heroBg');
    if (heroBg) {
        requestAnimationFrame(function () {
            heroBg.classList.add('is-loaded');
        });
    }

    // Reveal elements on scroll
    // Fallback: se IntersectionObserver non è disponibile o dopo 1.5s mostra tutto
    function showAll() {
        document.querySelectorAll('[data-reveal]').forEach(function (el) {
            el.classList.add('is-visible');
        });
    }

    if (!('IntersectionObserver' in window)) {
        showAll();
        return;
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -20px 0px'
    });

    var revealEls = document.querySelectorAll('[data-reveal]');

    // Rivela SUBITO tutti gli elementi già visibili nel viewport
    revealEls.forEach(function (el) {
        var rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight + 100) {
            el.classList.add('is-visible');
        } else {
            observer.observe(el);
        }
    });

    // Fallback di sicurezza: 3s (abbastanza per IO su mobile lento)
    var revealFallback = setTimeout(showAll, 3000);

    // Counter animation for numbers section
    var numObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.querySelectorAll('.number-item__num').forEach(function (num) {
                    var text = num.textContent.trim();
                    // Anima solo valori numerici puri
                    var match = text.match(/^(\d+)/);
                    if (match) {
                        var target = parseInt(match[1], 10);
                        var start  = 0;
                        var duration = 1200;
                        var startTime = null;
                        var suffix = text.replace(/^\d+/, '');

                        function step(timestamp) {
                            if (!startTime) startTime = timestamp;
                            var progress = Math.min((timestamp - startTime) / duration, 1);
                            var eased = 1 - Math.pow(1 - progress, 3);
                            num.textContent = Math.floor(eased * target) + suffix;
                            if (progress < 1) requestAnimationFrame(step);
                        }
                        requestAnimationFrame(step);
                    }
                });
                numObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    var numbersSection = document.querySelector('.numbers-section');
    if (numbersSection) numObserver.observe(numbersSection);

    // Header color transition on scroll
    var siteHeader = document.getElementById('site-header');
    if (siteHeader) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > window.innerHeight * 0.8) {
                siteHeader.classList.remove('site-header--home');
            } else if (document.body.classList.contains('home')) {
                siteHeader.classList.add('site-header--home');
            }
        }, { passive: true });
    }

}());

/* ── Language propagation ─────────────────────────────────────────────────
 * Legge la lingua corrente da ?lang= (priorità) o cookie alm_lang.
 * Se diversa da 'it', aggiunge ?lang=XX a TUTTI i link interni
 * (stesso origin) → il PHP riceve sempre $_GET['lang'] e non dipende
 * solo dal cookie (più robusto con caching e browser policy).
 * ───────────────────────────────────────────────────────────────────────── */
(function () {
    'use strict';

    var ALLOWED = ['en', 'de', 'fr', 'es'];

    function langFromUrl() {
        var m = window.location.search.match(/[?&]lang=([a-z]{2})/);
        return m ? m[1] : '';
    }

    function langFromCookie() {
        var m = document.cookie.match(/(?:^|;\s*)alm_lang=([a-z]{2})/);
        return m ? m[1] : '';
    }

    var lang = langFromUrl() || langFromCookie();
    if (!lang || lang === 'it' || ALLOWED.indexOf(lang) === -1) return;

    // Cookie persistente 30 giorni — si imposta solo dopo clic selettore
    var d = new Date();
    d.setDate(d.getDate() + 30);
    document.cookie = 'alm_lang=' + lang + '; path=/; SameSite=Lax; expires=' + d.toUTCString();

    // Propaga ?lang=XX a tutti i link interni
    function propagateLang() {
        var origin = window.location.origin;
        var links  = document.querySelectorAll('a[href]');

        for (var i = 0; i < links.length; i++) {
            var a    = links[i];
            var href = a.getAttribute('href') || '';

            if (!href || href.charAt(0) === '#' ||
                href.indexOf('mailto:') === 0 ||
                href.indexOf('tel:') === 0) continue;

            try {
                var url = new URL(a.href, origin);
                if (url.origin !== origin) continue;
                if (url.searchParams.has('lang')) continue; // preserva i link del selettore lingua
                url.searchParams.set('lang', lang);
                a.href = url.toString();
            } catch (e) {}
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', propagateLang);
    } else {
        propagateLang();
    }
}());
