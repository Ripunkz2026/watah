document.addEventListener('DOMContentLoaded', function () {
    const landing = document.querySelector('main.landing');
    if (landing) initializeScrollMovement(landing);
    const heroCanvas = document.querySelector('.lp-hero-dots');
    if (heroCanvas) initializeHeroDots(heroCanvas);

    const filters = document.querySelector('.lp-filters');
    const cards = Array.from(document.querySelectorAll('[data-pallet-size]'));
    const count = document.querySelector('.lp-product-count');
    if (filters && count) {
        filters.hidden = false;
        filters.addEventListener('click', function (event) {
            const button = event.target.closest('[data-pallet-filter]');
            if (!button) return;
            const size = button.dataset.palletFilter;
            filters.querySelectorAll('button').forEach(function (item) {
                item.setAttribute('aria-pressed', String(item === button));
            });
            let visibleCount = 0;
            cards.forEach(function (card) {
                card.hidden = size !== 'all' && card.dataset.palletSize !== size;
                if (!card.hidden) visibleCount++;
            });
            count.textContent = visibleCount + ' products' + (size === 'all' ? '' : ' · ' + size + ' pallets');
        });
    }
    const menu = document.querySelector('.lp-mobile-menu');
    if (menu) {
        const toggle = menu.querySelector('summary');
        menu.addEventListener('toggle', function () {
            toggle.setAttribute('aria-label', menu.open ? 'Close navigation' : 'Open navigation');
        });
        menu.addEventListener('focusout', function (event) {
            if (event.relatedTarget && !menu.contains(event.relatedTarget)) menu.open = false;
        });
        window.matchMedia('(min-width: 901px)').addEventListener('change', function (event) {
            if (event.matches) menu.open = false;
        });
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { menu.open = false; });
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && menu.open) {
                menu.open = false;
                toggle.focus();
            }
        });
        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) menu.open = false;
        });
    }
});

function initializeScrollMovement(landing) {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    // Content stays visible when motion is disabled or these APIs are unavailable.
    if (reducedMotion.matches || !('IntersectionObserver' in window) || !Element.prototype.animate) return;

    const targets = landing.querySelectorAll([
        '.lp-hero-copy', '.lp-hero > .lp-showcase', '.lp-brand-row > *',
        '.lp-section-heading', '.lp-product', '.lp-how-intro', '.lp-steps > li',
        '.lp-delivery-copy', '.lp-delivery-form', '.lp-faq > div:first-child',
        '.lp-faq-list > details', '.lp-final-cta > .lp-wrap'
    ].join(', '));
    const animations = new Map();
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) return;
            const target = entry.target;
            observer.unobserve(target);
            if (reducedMotion.matches || target.contains(document.activeElement)) return;

            const siblings = Array.from(target.parentElement.children);
            const stagger = target.matches('.lp-product, .lp-steps > li, .lp-brand-row > *');
            const animation = target.animate([
                { opacity: 0, transform: 'translateY(28px)' },
                { opacity: 1, transform: 'translateY(0)' }
            ], {
                duration: 650,
                delay: stagger ? (siblings.indexOf(target) % 3) * 80 : 0,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'backwards'
            });
            animations.set(target, animation);
            animation.onfinish = animation.oncancel = function () { animations.delete(target); };
        });
    }, { threshold: 0, rootMargin: '0px 0px -32px 0px' });

    targets.forEach(function (target) { observer.observe(target); });
    // Keyboard navigation must never wait for an entrance animation.
    landing.addEventListener('focusin', function (event) {
        animations.forEach(function (animation, target) {
            if (target.contains(event.target)) animation.cancel();
        });
    });
    reducedMotion.addEventListener('change', function (event) {
        if (!event.matches) return;
        observer.disconnect();
        animations.forEach(function (animation) { animation.cancel(); });
        animations.clear();
    });
    window.addEventListener('pagehide', function () {
        animations.forEach(function (animation) { animation.cancel(); });
        animations.clear();
    });
}

function initializeHeroDots(canvas) {
    const context = canvas.getContext('2d');
    if (!context) return;

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const dots = Array.from({ length: 72 }, function () {
        const depth = Math.random();
        return {
            x: Math.random(),
            y: Math.random(),
            radius: 1.1 + depth * 1.9,
            speed: 10 + depth * 24,
            opacity: 0.3 + depth * 0.35
        };
    });
    let width = 0;
    let height = 0;
    let frame = null;
    let previousTime = null;
    let inView = true;

    function draw(elapsed = 0) {
        context.clearRect(0, 0, width, height);
        dots.forEach(function (dot) {
            dot.y -= dot.speed * elapsed / height;
            // Let the entire dot leave the top before reappearing below the bottom.
            if (dot.y < -dot.radius / height) {
                dot.y = 1 + dot.radius / height;
                dot.x = Math.random();
            }
            context.beginPath();
            context.fillStyle = 'rgba(0, 119, 182, ' + dot.opacity + ')';
            context.arc(dot.x * width, dot.y * height, dot.radius, 0, Math.PI * 2);
            context.fill();
        });
    }

    function animate(time) {
        const elapsed = previousTime === null ? 0 : Math.min((time - previousTime) / 1000, 0.05);
        previousTime = time;
        draw(elapsed);
        frame = requestAnimationFrame(animate);
    }

    function updateAnimation() {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
        previousTime = null;
        if (!width || !height) return;
        draw();
        if (!reducedMotion.matches && !document.hidden && inView) {
            frame = requestAnimationFrame(animate);
        }
    }

    function resize() {
        const bounds = canvas.getBoundingClientRect();
        width = bounds.width;
        height = bounds.height;
        const scale = Math.min(window.devicePixelRatio || 1, 2);
        canvas.width = Math.round(width * scale);
        canvas.height = Math.round(height * scale);
        context.setTransform(scale, 0, 0, scale, 0, 0);
        updateAnimation();
    }

    if ('ResizeObserver' in window) new ResizeObserver(resize).observe(canvas.parentElement);
    window.addEventListener('resize', resize);
    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
            inView = entries[0].isIntersecting;
            updateAnimation();
        }).observe(canvas.parentElement);
    }
    reducedMotion.addEventListener('change', updateAnimation);
    document.addEventListener('visibilitychange', updateAnimation);
    resize();
}

