document.querySelectorAll('[data-public-header]').forEach((header) => {
    const mobileToggle = header.querySelector('[data-mobile-nav-toggle]');
    const mobileNav = header.querySelector('[data-mobile-nav]');
    const mobileBackdrop = header.querySelector('[data-mobile-nav-backdrop]');
    const accountToggle = header.querySelector('[data-account-toggle]');
    const accountMenu = header.querySelector('[data-account-menu]');
    const searchForm = header.querySelector('[data-header-search]');
    const desktopBreakpoint = window.matchMedia('(min-width: 68.75rem)');

    const setMobileNavigation = (isOpen) => {
        if (!mobileToggle || !mobileNav || !mobileBackdrop) {
            return;
        }

        mobileToggle.setAttribute('aria-expanded', String(isOpen));
        mobileToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
        mobileNav.classList.toggle('is-open', isOpen);
        mobileBackdrop.hidden = !isOpen;
        document.documentElement.classList.toggle('tazen-public-nav-open', isOpen);

        if (isOpen) {
            mobileNav.querySelector('a[href]')?.focus();
        } else if (document.activeElement !== mobileToggle) {
            mobileToggle.focus();
        }
    };

    mobileToggle?.addEventListener('click', () => {
        setMobileNavigation(mobileToggle.getAttribute('aria-expanded') !== 'true');
    });
    mobileBackdrop?.addEventListener('click', () => setMobileNavigation(false));

    accountToggle?.addEventListener('click', () => {
        if (!accountMenu) {
            return;
        }

        const isOpen = accountToggle.getAttribute('aria-expanded') === 'true';
        accountToggle.setAttribute('aria-expanded', String(!isOpen));
        accountMenu.hidden = isOpen;

        if (!isOpen) {
            accountMenu.querySelector('a, button')?.focus();
        }
    });

    searchForm?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const input = searchForm.querySelector('input[name="query"]');
        const submitButton = searchForm.querySelector('button[type="submit"]');
        const status = searchForm.querySelector('[data-search-status]');
        const query = input?.value.trim();

        if (!query || !submitButton || !status) {
            return;
        }

        status.textContent = '';
        submitButton.disabled = true;

        try {
            const endpoint = searchForm.dataset.searchEndpoint;
            const serviceUrl = searchForm.dataset.serviceUrl;

            if (!endpoint || !serviceUrl) {
                throw new Error('The service search is not configured.');
            }

            const response = await fetch(`${endpoint}?query=${encodeURIComponent(query)}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`Service search returned ${response.status}.`);
            }

            const data = await response.json();
            const services = Array.isArray(data.services) ? data.services : [];
            const result = services.find((service) =>
                service.name?.toLowerCase() === query.toLowerCase()
            ) || services[0];

            if (!result?.slug) {
                status.textContent = 'No matching services found. Try another search.';
                return;
            }

            window.location.assign(`${serviceUrl}/${encodeURIComponent(result.slug)}`);
        } catch (error) {
            console.error('Unable to search services:', error);
            status.textContent = 'Search is unavailable. Please try again.';
        } finally {
            submitButton.disabled = false;
        }
    });

    header.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        if (event.target.closest('[data-placeholder-link]')) {
            event.preventDefault();
        }

        if (accountMenu && !event.target.closest('[data-account-menu]')) {
            accountMenu.hidden = true;
            accountToggle?.setAttribute('aria-expanded', 'false');
        }

        if (mobileNav?.classList.contains('is-open') && event.target.closest('a[href]')) {
            setMobileNavigation(false);
        }
    });

    header.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (accountMenu && !accountMenu.hidden) {
                accountMenu.hidden = true;
                accountToggle?.setAttribute('aria-expanded', 'false');
                accountToggle?.focus();
            } else if (mobileNav?.classList.contains('is-open')) {
                setMobileNavigation(false);
            }
        }

        if (event.key === 'Tab' && mobileNav?.classList.contains('is-open')) {
            const focusable = [...mobileNav.querySelectorAll('a[href], button:not([disabled]), input:not([disabled])')]
                .filter((element) => !element.closest('[hidden]'));
            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last?.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first?.focus();
            }
        }
    });

    desktopBreakpoint.addEventListener('change', (event) => {
        if (event.matches && mobileNav?.classList.contains('is-open')) {
            setMobileNavigation(false);
        }
    });
});

document.querySelectorAll('[data-contact-redirect]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const email = new FormData(form).get('email');

        if (typeof email !== 'string' || email.length === 0) {
            return;
        }

        window.location.href = `/contact?email=${encodeURIComponent(email)}`;
    });
});
