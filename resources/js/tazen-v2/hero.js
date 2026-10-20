document.querySelectorAll('[data-tazen-v2-hero-search]').forEach((form) => {
    const input = form.querySelector('input[name="query"]');
    const suggestions = form.querySelector('datalist');
    const status = form.querySelector('[data-tazen-v2-search-status]');
    const submitButton = form.querySelector('[data-tazen-v2-search-submit]');

    if (!input || !suggestions || !status || !submitButton) {
        return;
    }

    let suggestionTimer;
    let suggestionRequest;

    const searchServices = async (query, signal) => {
        const url = new URL(form.action, window.location.origin);
        url.searchParams.set(input.name, query);

        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            signal,
        });

        if (!response.ok) {
            throw new Error('Service search failed');
        }

        const result = await response.json();
        return Array.isArray(result.services) ? result.services : [];
    };

    input.addEventListener('input', () => {
        window.clearTimeout(suggestionTimer);
        suggestionRequest?.abort();

        const query = input.value.trim();
        suggestions.replaceChildren();

        if (!query) {
            return;
        }

        suggestionTimer = window.setTimeout(async () => {
            suggestionRequest = new AbortController();

            try {
                const services = await searchServices(query, suggestionRequest.signal);

                services.forEach((service) => {
                    if (!service.name) {
                        return;
                    }

                    const option = document.createElement('option');
                    option.value = service.name;
                    suggestions.append(option);
                });
            } catch (error) {
                if (error.name !== 'AbortError') {
                    status.textContent = 'Service suggestions are temporarily unavailable.';
                }
            }
        }, 200);
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const query = input.value.trim();
        if (!query) {
            input.reportValidity();
            return;
        }

        window.clearTimeout(suggestionTimer);
        suggestionRequest?.abort();
        submitButton.disabled = true;
        status.textContent = 'Searching services...';

        try {
            const services = await searchServices(query);
            const service = services.find((item) => item.name?.toLowerCase() === query.toLowerCase()) ?? services[0];

            if (!service?.slug) {
                status.textContent = 'No matching services found. Try another search.';
                return;
            }

            window.location.assign(form.dataset.serviceRoute.replace('__slug__', encodeURIComponent(service.slug)));
        } catch {
            status.textContent = 'Service search is temporarily unavailable. Please try again.';
        } finally {
            submitButton.disabled = false;
        }
    });
});