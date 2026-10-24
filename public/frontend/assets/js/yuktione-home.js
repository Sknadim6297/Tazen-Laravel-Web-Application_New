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
            const response = await fetch(`/search-services?query=${encodeURIComponent(query)}`, {
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
                link.href = `/service/${encodeURIComponent(service.slug)}`;
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
            window.location.assign(`/service/${encodeURIComponent(service.slug)}`);
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

    document.addEventListener('click', (event) => {
        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
            hideResults();
        }
    });
}
