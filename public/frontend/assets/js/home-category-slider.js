document.addEventListener('DOMContentLoaded', function () {
    const track = document.querySelector('[data-category-slider-track]');
    if (!track) return;

    const controls = document.querySelector('.homepage-category-slider__controls');
    const previous = document.querySelector('[data-category-slide-prev]');
    const next = document.querySelector('[data-category-slide-next]');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let updateTimer;

    function updateControls() {
        const maxScroll = track.scrollWidth - track.clientWidth;
        const canScroll = maxScroll > 1;
        controls.hidden = !canScroll;
        track.classList.toggle('is-single-category', track.children.length === 1);
        if (!canScroll) {
            previous.disabled = true;
            next.disabled = true;
            return;
        }

        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft >= maxScroll - 1;
    }

    function showImageFallback(image) {
        image.hidden = true;
        const fallback = image.parentElement.querySelector('.homepage-category-card__fallback');
        if (fallback) fallback.removeAttribute('hidden');
    }

    track.querySelectorAll('.homepage-category-card__art img').forEach(function (image) {
        image.addEventListener('error', function () {
            showImageFallback(image);
        }, { once: true });

        if (image.complete && image.naturalWidth === 0) {
            showImageFallback(image);
        }
    });

    function scrollTrack(direction) {
        const card = track.querySelector('.homepage-category-card');
        if (!card) return;
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        const distance = (card.getBoundingClientRect().width + gap) * (direction === 'next' ? 2 : -2);
        track.scrollBy({ left: distance, behavior: prefersReducedMotion ? 'auto' : 'smooth' });
    }

    previous.addEventListener('click', function () {
        scrollTrack('previous');
    });

    next.addEventListener('click', function () {
        scrollTrack('next');
    });

    track.addEventListener('scroll', function () {
        window.clearTimeout(updateTimer);
        updateTimer = window.setTimeout(updateControls, 80);
    }, { passive: true });

    track.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            scrollTrack(event.key === 'ArrowRight' ? 'next' : 'previous');
        }
    });

    track.addEventListener('wheel', function (event) {
        if (event.shiftKey && Math.abs(event.deltaY) > Math.abs(event.deltaX) && track.scrollWidth > track.clientWidth) {
            event.preventDefault();
            track.scrollLeft += event.deltaY;
        }
    }, { passive: false });

    window.addEventListener('resize', updateControls, { passive: true });
    updateControls();
});
