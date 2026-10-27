const searchInput = document.getElementById('serviceSearch');
const searchResults = document.getElementById('searchResults');
const searchButton = document.getElementById('searchButton');

if (searchInput && searchResults && searchButton) {
    let searchTimeout;
    let requestNumber = 0;
    let currentResults = [];

    const hideResults = () => {
        searchResults.classList.add('d-none');
        searchResults.style.display = 'none';
        searchResults.replaceChildren();
        currentResults = [];
    };

    const showMessage = (message) => {
        const list = document.createElement('div');
        const item = document.createElement('div');

        list.className = 'list-group';
        item.className = 'list-group-item text-muted';
        item.textContent = message;
        list.append(item);
        searchResults.replaceChildren(list);
        searchResults.classList.remove('d-none');
        searchResults.style.display = 'block';
    };

    const findServices = async (query) => {
        const requestId = ++requestNumber;

        if (!query) {
            hideResults();
            return [];
        }

        try {
            const endpoint = searchInput.dataset.searchEndpoint || '/search-services';
            const response = await fetch(`${endpoint}?query=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`Service search returned ${response.status}.`);
            }

            const data = await response.json();
            const services = Array.isArray(data.services)
                ? data.services.filter((service) => typeof service?.slug === 'string' && typeof service?.name === 'string')
                : [];

            if (requestId !== requestNumber) {
                return [];
            }

            currentResults = services;
            if (!services.length) {
                showMessage('No results found. Try a different search.');
                return services;
            }

            const list = document.createElement('div');
            list.className = 'list-group';

            services.forEach((service) => {
                const link = document.createElement('a');
                link.className = 'list-group-item list-group-item-action';
                const serviceBase = (searchInput.dataset.serviceBase || '/service').replace(/\/$/, '');
                link.href = `${serviceBase}/${encodeURIComponent(service.slug)}`;
                link.textContent = service.name;
                link.setAttribute('role', 'option');
                list.append(link);
            });

            searchResults.replaceChildren(list);
            searchResults.classList.remove('d-none');
            searchResults.style.display = 'block';
            return services;
        } catch (error) {
            if (requestId === requestNumber) {
                currentResults = [];
                showMessage('Search is temporarily unavailable. Please try again.');
                console.error('Unable to search homepage services:', error);
            }

            return [];
        }
    };

    const openFirstResult = async () => {
        const query = searchInput.value.trim();
        if (!query) {
            searchInput.focus();
            return;
        }

        const services = currentResults.length ? currentResults : await findServices(query);
        const exactMatch = services.find((service) => service.name.toLowerCase() === query.toLowerCase());
        const service = exactMatch || services[0];

        if (service) {
            const serviceBase = (searchInput.dataset.serviceBase || '/service').replace(/\/$/, '');
            window.location.assign(`${serviceBase}/${encodeURIComponent(service.slug)}`);
        }
    };

    searchInput.addEventListener('input', () => {
        window.clearTimeout(searchTimeout);
        searchTimeout = window.setTimeout(() => findServices(searchInput.value.trim()), 300);
    });

    searchInput.addEventListener('focus', () => {
        const query = searchInput.value.trim();
        if (query) {
            findServices(query);
        }
    });

    searchInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            openFirstResult();
        } else if (event.key === 'Escape') {
            hideResults();
        }
    });

    searchButton.addEventListener('click', openFirstResult);

    document.querySelectorAll('[data-hero-search]').forEach((button) => {
        button.addEventListener('click', () => {
            const term = button.getAttribute('data-hero-search') || '';
            searchInput.value = term;
            searchInput.focus();
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });

    document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            hideResults();
        }
    });
}

document.querySelectorAll('.yk-home-blog-card__image img').forEach((image) => {
    image.addEventListener('error', () => {
        image.hidden = true;
        const fallback = image.parentElement?.querySelector('[data-blog-image-fallback]');
        if (fallback) {
            fallback.hidden = false;
        }
    }, { once: true });
});

document.querySelectorAll('[data-testimonial-avatar]').forEach((image) => {
    image.addEventListener('error', () => {
        image.hidden = true;
        const fallback = image.parentElement?.querySelector('.yk-home-testimonial-card__avatar-fallback');
        if (fallback) {
            fallback.hidden = false;
        }
    }, { once: true });
});

document.querySelectorAll('[data-event-image], [data-how-image]').forEach((image) => {
    image.addEventListener('error', () => {
        image.hidden = true;
        const fallback = image.parentElement?.querySelector('.yk-home-event-card__image-fallback, .yk-home-how-card__image-fallback');
        if (fallback) {
            fallback.hidden = false;
        }
    }, { once: true });
});

document.querySelectorAll('[data-testimonial-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('[data-testimonial-track]');
    const previous = carousel.querySelector('[data-testimonial-previous]');
    const next = carousel.querySelector('[data-testimonial-next]');

    if (!(track instanceof HTMLElement) || !(previous instanceof HTMLButtonElement) || !(next instanceof HTMLButtonElement)) {
        return;
    }

    const updateControls = () => {
        const maxScrollLeft = track.scrollWidth - track.clientWidth;
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft >= maxScrollLeft - 1;
    };

    const scrollByCard = (direction) => {
        const card = track.querySelector('.yk-home-testimonial-card');
        if (!(card instanceof HTMLElement)) {
            return;
        }

        const gap = Number.parseFloat(getComputedStyle(track).columnGap) || 0;
        track.scrollBy({
            left: direction * (card.getBoundingClientRect().width + gap),
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
        });
    };

    previous.addEventListener('click', () => scrollByCard(-1));
    next.addEventListener('click', () => scrollByCard(1));
    track.addEventListener('scroll', updateControls, { passive: true });
    window.addEventListener('resize', updateControls);
    updateControls();
});

document.querySelectorAll('[data-home-faq]').forEach((faq) => {
    faq.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const toggle = event.target.closest('[data-home-faq-toggle]');
        if (!toggle || !faq.contains(toggle)) {
            return;
        }

        const panelId = toggle.getAttribute('aria-controls');
        const panel = panelId ? document.getElementById(panelId) : null;
        if (!panel) {
            console.error('The FAQ answer panel could not be found.');
            return;
        }

        const shouldExpand = toggle.getAttribute('aria-expanded') !== 'true';
        faq.querySelectorAll('[data-home-faq-toggle][aria-expanded="true"]').forEach((openToggle) => {
            if (openToggle === toggle) {
                return;
            }

            const openPanelId = openToggle.getAttribute('aria-controls');
            const openPanel = openPanelId ? document.getElementById(openPanelId) : null;
            openToggle.setAttribute('aria-expanded', 'false');
            openPanel?.setAttribute('aria-hidden', 'true');
            if (openPanel) {
                openPanel.inert = true;
            }
        });

        toggle.setAttribute('aria-expanded', String(shouldExpand));
        panel.setAttribute('aria-hidden', String(!shouldExpand));
        panel.inert = !shouldExpand;
    });
});

document.querySelectorAll('[data-hero-video]').forEach((card) => {
    const video = card.querySelector('video');
    const playButtons = card.querySelectorAll('[data-hero-play]');

    if (!(video instanceof HTMLVideoElement) || !playButtons.length) {
        return;
    }

    const stop = () => {
        video.pause();
        video.controls = false;
        card.classList.remove('is-playing');
    };

    const play = async () => {
        card.classList.add('is-playing');
        video.controls = true;

        try {
            await video.play();
        } catch (error) {
            stop();
            console.error('The homepage video could not be played.', error);
        }
    };

    playButtons.forEach((button) => {
        button.addEventListener('click', play);
    });

    video.addEventListener('ended', stop);
    video.addEventListener('error', () => {
        stop();
        console.error('The homepage video file could not be loaded.');
    });
});

const revealTargets = document.querySelectorAll('[data-home-reveal]');
if (revealTargets.length && !window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
    document.documentElement.classList.add('yk-home-reveal-enabled');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -24px 0px' });

    revealTargets.forEach((target) => revealObserver.observe(target));
}
