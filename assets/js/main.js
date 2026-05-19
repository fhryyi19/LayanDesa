/**
 * LayanDesa - Main JavaScript
 * Modern Website Pelayanan Publik Desa Berbasis Web
 * 2025
 */

'use strict';

// =============================================
// DOM Ready
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    initNavbar();
    initHamburger();
    initScrollAnimations();
    initCounterAnimation();
    initFormValidation();
    initSmoothScroll();
    highlightActiveNav();
    initAlertDismiss();
});

// =============================================
// Navbar Scroll Effect
// =============================================
function initNavbar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    const handleScroll = () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll(); // Run on load
}

// =============================================
// Hamburger Menu (Mobile)
// =============================================
function initHamburger() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');

    if (!hamburger || !navMenu) return;

    hamburger.addEventListener('click', function () {
        this.classList.toggle('active');
        navMenu.classList.toggle('open');

        // Prevent body scroll when menu is open
        document.body.style.overflow = navMenu.classList.contains('open') ? 'hidden' : '';
    });

    // Close menu when a nav link is clicked
    navMenu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('open');
            document.body.style.overflow = '';
        });
    });

    // Close menu on backdrop click
    navMenu.addEventListener('click', function (e) {
        if (e.target === this) {
            hamburger.classList.remove('active');
            this.classList.remove('open');
            document.body.style.overflow = '';
        }
    });
}

// =============================================
// Scroll Animations (Intersection Observer)
// =============================================
function initScrollAnimations() {
    const selectors = [
        '.news-card', '.service-card', '.announcement-item',
        '.stat-item', '.section-header', '.org-card',
        '.layanan-card', '.visi-card', '.org-node'
    ];

    // Check if IntersectionObserver is supported
    if (!('IntersectionObserver' in window)) {
        // Fallback: show all elements immediately
        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                el.classList.add('fade-in');
            });
        });
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                // Use setTimeout for stagger effect without inline opacity
                setTimeout(() => {
                    entry.target.classList.add('fade-in');
                }, index * 60);
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.08,
        rootMargin: '0px 0px -30px 0px'
    });

    selectors.forEach(sel => {
        document.querySelectorAll(sel).forEach(el => {
            // Use CSS class, NOT inline style (inline style overrides CSS .fade-in)
            el.classList.add('anim-ready');
            observer.observe(el);
        });
    });
}

// =============================================
// Counter Animation
// =============================================
function initCounterAnimation() {
    const counters = document.querySelectorAll('.stat-number, .hero-stat .number');
    if (!counters.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.dataset.animated) {
                entry.target.dataset.animated = 'true';
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
}

function animateCounter(element) {
    const text = element.textContent.trim();
    const suffix = text.replace(/[0-9,]/g, '');
    const target = parseInt(text.replace(/[^0-9]/g, ''));

    if (isNaN(target)) return;

    const duration = 2000;
    const startTime = performance.now();

    const update = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Ease out quad
        const eased = 1 - (1 - progress) * (1 - progress);
        const current = Math.floor(eased * target);

        element.textContent = current.toLocaleString('id-ID') + suffix;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            element.textContent = target.toLocaleString('id-ID') + suffix;
        }
    };

    requestAnimationFrame(update);
}

// =============================================
// Form Validation
// =============================================
function initFormValidation() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', validateContactForm);
    }
}

function validateContactForm(e) {
    const form = e.target;
    let isValid = true;

    // Reset previous errors
    form.querySelectorAll('.error-message').forEach(el => el.remove());
    form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

    // Validate fields
    const fields = [
        { id: 'nama', label: 'Nama lengkap', minLength: 3 },
        { id: 'email', label: 'Email', type: 'email' },
        { id: 'subjek', label: 'Subjek', minLength: 5 },
        { id: 'isi', label: 'Pesan', minLength: 20 }
    ];

    fields.forEach(field => {
        const el = document.getElementById(field.id);
        if (!el) return;

        const value = el.value.trim();

        if (!value) {
            showError(el, `${field.label} tidak boleh kosong.`);
            isValid = false;
        } else if (field.type === 'email' && !isValidEmail(value)) {
            showError(el, 'Format email tidak valid.');
            isValid = false;
        } else if (field.minLength && value.length < field.minLength) {
            showError(el, `${field.label} minimal ${field.minLength} karakter.`);
            isValid = false;
        }
    });

    if (!isValid) {
        e.preventDefault();
        // Scroll to first error
        const firstError = form.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
}

function showError(element, message) {
    element.classList.add('is-invalid');
    const errEl = document.createElement('div');
    errEl.className = 'error-message';
    errEl.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg> ${message}`;
    element.parentNode.insertBefore(errEl, element.nextSibling);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// =============================================
// Highlight Active Navigation Link
// =============================================
function highlightActiveNav() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentPage || href.includes(currentPage))) {
            link.classList.add('active');
        }
    });
}

// =============================================
// Smooth Scroll for Anchor Links
// =============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// =============================================
// Alert Auto-Dismiss
// =============================================
function initAlertDismiss() {
    document.querySelectorAll('.alert').forEach(alert => {
        const delay = parseInt(alert.dataset.dismissAfter) || 5000;
        if (alert.classList.contains('alert-success') || 
            alert.classList.contains('alert-error') ||
            alert.getAttribute('data-auto-dismiss') !== 'false') {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 400);
            }, delay);
        }
    });
}

// =============================================
// Read More Toggle
// =============================================
document.querySelectorAll('[data-toggle="readmore"]').forEach(btn => {
    btn.addEventListener('click', function () {
        const targetId = this.dataset.target;
        const target = document.getElementById(targetId);
        if (!target) return;

        const isExpanded = target.style.maxHeight && target.style.maxHeight !== '100px';
        if (isExpanded) {
            target.style.maxHeight = '100px';
            target.style.overflow = 'hidden';
            this.textContent = 'Baca Selengkapnya';
        } else {
            target.style.maxHeight = target.scrollHeight + 'px';
            target.style.overflow = 'visible';
            this.textContent = 'Sembunyikan';
        }
    });
});
