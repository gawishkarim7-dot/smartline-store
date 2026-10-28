const header = document.getElementById('site-header');
const toggle = document.getElementById('nav-toggle');
const mobileNav = document.getElementById('mobile-nav');

if (toggle && mobileNav) {
    toggle.addEventListener('click', () => {
        const open = !mobileNav.classList.contains('hidden');
        mobileNav.classList.toggle('hidden', open);
        toggle.setAttribute('aria-expanded', String(!open));
    });
}

if (header) {
    const onScroll = () => header.classList.toggle('is-solid', window.scrollY > 24);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

const lightbox = document.getElementById('store-lightbox');
const lightboxImage = document.getElementById('store-lightbox-image');
const lightboxClose = document.getElementById('store-lightbox-close');

const closeLightbox = () => {
    if (!lightbox) {
        return;
    }

    lightbox.classList.remove('is-open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('lightbox-open');

    if (lightboxImage) {
        lightboxImage.removeAttribute('src');
        lightboxImage.alt = '';
    }
};

const openLightbox = (src, alt = '') => {
    if (!lightbox || !lightboxImage || !src) {
        return;
    }

    lightboxImage.src = src;
    lightboxImage.alt = alt || '';
    lightbox.classList.add('is-open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.classList.add('lightbox-open');
    lightboxClose?.focus();
};

document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-lightbox]');
    if (!trigger) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    const src = trigger.getAttribute('data-lightbox-src') || trigger.getAttribute('href');
    const alt = trigger.getAttribute('data-lightbox-alt') || '';
    openLightbox(src, alt);
});

lightboxClose?.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();
    closeLightbox();
});

lightbox?.addEventListener('click', (event) => {
    if (event.target === lightbox) {
        closeLightbox();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && lightbox?.classList.contains('is-open')) {
        closeLightbox();
    }
});
