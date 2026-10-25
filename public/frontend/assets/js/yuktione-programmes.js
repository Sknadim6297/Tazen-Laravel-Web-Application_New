(() => {
    document.querySelectorAll('[data-programme-detail-image]').forEach((image) => {
        image.addEventListener('error', () => {
            image.hidden = true;
            const fallback = image.parentElement?.querySelector('.programme-detail__image-fallback');
            if (fallback) fallback.hidden = false;
        }, { once: true });
    });

    document.querySelectorAll('[data-programme-list-image]').forEach((image) => {
        image.addEventListener('error', () => {
            image.hidden = true;
            const fallback = image.parentElement?.parentElement?.querySelector('.programme-list-image-fallback');
            if (fallback) fallback.hidden = false;
        }, { once: true });
    });

    const section = document.querySelector('[data-programmes]');
    if (!section) return;

    const tabs = [...section.querySelectorAll('[data-programme-tab]')];
    const panels = [...section.querySelectorAll('[data-programme-panel]')];
    const controls = section.querySelector('[data-programme-controls]');
    const previousButton = section.querySelector('[data-programme-previous]');
    const nextButton = section.querySelector('[data-programme-next]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    const activePanel = () => section.querySelector('[data-programme-panel]:not([hidden])');

    const updateControls = () => {
        const track = activePanel()?.querySelector('[data-programme-track]');
        if (!controls || !previousButton || !nextButton) return;
        if (!track) {
            controls.hidden = true;
            return;
        }

        const canScroll = track.scrollWidth > track.clientWidth + 2;
        controls.hidden = !canScroll;
        previousButton.disabled = !canScroll || track.scrollLeft <= 2;
        nextButton.disabled = !canScroll || track.scrollLeft + track.clientWidth >= track.scrollWidth - 2;
    };

    const scrollTrack = (direction) => {
        const track = activePanel()?.querySelector('[data-programme-track]');
        if (!track) return;
        track.scrollBy({
            left: direction * Math.max(track.clientWidth * .82, 240),
            behavior: reducedMotion.matches ? 'auto' : 'smooth',
        });
    };

    previousButton?.addEventListener('click', () => scrollTrack(-1));
    nextButton?.addEventListener('click', () => scrollTrack(1));

    const activateTab = (tab) => {
        const selectedTab = tab.dataset.programmeTab;
        tabs.forEach((candidate) => {
            const selected = candidate === tab;
            candidate.classList.toggle('is-active', selected);
            candidate.setAttribute('aria-selected', String(selected));
            candidate.tabIndex = selected ? 0 : -1;
        });

        panels.forEach((panel) => {
            const selected = panel.dataset.programmePanel === selectedTab;
            panel.hidden = !selected;
            panel.classList.remove('is-entering');
            if (selected) {
                window.requestAnimationFrame(() => panel.classList.add('is-entering'));
            }
        });

        window.requestAnimationFrame(updateControls);
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', (event) => {
            let nextIndex;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
            else if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
            else if (event.key === 'Home') nextIndex = 0;
            else if (event.key === 'End') nextIndex = tabs.length - 1;
            else return;

            event.preventDefault();
            tabs[nextIndex].focus();
            activateTab(tabs[nextIndex]);
        });
    });

    section.querySelector('[data-programme-show-past]')?.addEventListener('click', () => {
        const pastTab = tabs.find((tab) => tab.dataset.programmeTab === 'past');
        if (pastTab) {
            activateTab(pastTab);
            pastTab.focus();
        }
    });

    panels.forEach((panel) => {
        const track = panel.querySelector('[data-programme-track]');
        if (!track) return;

        track.addEventListener('scroll', updateControls, { passive: true });
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
    });

    section.querySelectorAll('[data-programme-image]').forEach((image) => {
        image.addEventListener('error', () => {
            image.hidden = true;
            const fallback = image.parentElement?.querySelector('.yk-home-event-card__image-fallback');
            if (fallback) {
                fallback.hidden = false;
                fallback.setAttribute('aria-hidden', 'false');
            }
        }, { once: true });
    });

    window.addEventListener('resize', updateControls, { passive: true });

    const revealElements = [...section.querySelectorAll('[data-programme-reveal], [data-programme-card]')];
    if (!reducedMotion.matches && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: .12 });

        section.classList.add('is-enhanced');
        revealElements.forEach((element, index) => {
            element.style.setProperty('--programme-delay', `${Math.min(index * 55, 385)}ms`);
            observer.observe(element);
        });
    } else {
        revealElements.forEach((element) => element.classList.add('is-visible'));
    }

    const selectedTab = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true');
    if (selectedTab) activateTab(selectedTab);
    window.requestAnimationFrame(updateControls);
})();
