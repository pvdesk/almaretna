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
