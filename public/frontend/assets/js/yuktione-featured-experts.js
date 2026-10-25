(() => {
    const section = document.querySelector('[data-featured-experts]');
    if (!section) return;

    const filters = [...section.querySelectorAll('[data-expert-filter]')];
    const cards = [...section.querySelectorAll('[data-expert-card]')];
    const track = section.querySelector('[data-expert-track]');
    const controls = section.querySelector('[data-expert-controls]');
    const previousButton = section.querySelector('[data-expert-previous]');
    const nextButton = section.querySelector('[data-expert-next]');
    const emptyFilterMessage = section.querySelector('[data-filter-empty]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    section.querySelectorAll('[data-expert-photo]').forEach((image) => {
        image.addEventListener('error', () => {
            image.hidden = true;
            const fallback = image.parentElement?.querySelector('.yk-expert-card__initials');
            if (fallback) fallback.hidden = false;
        }, { once: true });
    });

    if (track && controls && previousButton && nextButton) {
        const updateControls = () => {
            const canScroll = track.scrollWidth > track.clientWidth + 2;
            controls.hidden = !canScroll;
            previousButton.disabled = !canScroll || track.scrollLeft <= 2;
            nextButton.disabled = !canScroll || track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
        };

        const scrollTrack = (direction) => {
            track.scrollBy({
                left: direction * Math.max(track.clientWidth * 0.82, 184),
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
            });
        };

        previousButton.addEventListener('click', () => scrollTrack(-1));
        nextButton.addEventListener('click', () => scrollTrack(1));
        track.addEventListener('scroll', updateControls, { passive: true });
        window.addEventListener('resize', updateControls, { passive: true });

        track.addEventListener('keydown', (event) => {
            if (event.target !== track) return;
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                event.preventDefault();
                scrollTrack(event.key === 'ArrowRight' ? 1 : -1);
            } else if (event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                track.scrollTo({
                    left: event.key === 'Home' ? 0 : track.scrollWidth,
                    behavior: reducedMotion.matches ? 'auto' : 'smooth',
                });
            }
        });

        window.requestAnimationFrame(updateControls);
    }

    filters.forEach((filter) => {
        filter.addEventListener('click', () => {
            const selectedCategory = filter.dataset.expertFilter;

            filters.forEach((button) => {
                const isActive = button === filter;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-pressed', String(isActive));
            });

            let visibleCards = 0;
            cards.forEach((card) => {
                const categories = (card.dataset.categories || '').split(/\s+/).filter(Boolean);
                card.hidden = selectedCategory !== 'all' && !categories.includes(selectedCategory);
                if (!card.hidden) visibleCards += 1;
            });
            if (emptyFilterMessage) emptyFilterMessage.hidden = visibleCards > 0;

            if (track) {
                track.scrollTo({ left: 0, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
                window.requestAnimationFrame(() => {
                    track.dispatchEvent(new Event('scroll'));
                });
            }
        });
    });

    const revealElements = [...section.querySelectorAll('[data-reveal]')];
    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: 0.12 });

        section.classList.add('is-enhanced');
        revealElements.forEach((element) => observer.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('is-visible'));
    }
})();
