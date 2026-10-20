document.querySelectorAll('[data-yuktione-panel]').forEach((root) => {
    const sidebar = root.querySelector('[data-panel-sidebar]');
    const overlay = root.querySelector('[data-panel-overlay]');
    const openButtons = root.querySelectorAll('[data-panel-open]');
    const closeButtons = root.querySelectorAll('[data-panel-close]');

    const setSidebarOpen = (open) => {
        if (!sidebar) {
            return;
        }

        const isDesktop = window.matchMedia('(min-width: 64rem)').matches;
        sidebar.dataset.open = open ? 'true' : 'false';
        sidebar.setAttribute('aria-hidden', isDesktop || open ? 'false' : 'true');

        if (overlay) {
            overlay.dataset.open = open ? 'true' : 'false';
            overlay.hidden = !open;
        }

        document.body.classList.toggle(
            'overflow-hidden',
            open && window.matchMedia('(max-width: 63.99rem)').matches
        );

        openButtons.forEach((button) => {
            button.setAttribute('aria-expanded', String(open));
        });
    };

    setSidebarOpen(false);

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            setSidebarOpen(true);
            sidebar?.querySelector('a, button')?.focus();
        });
    });

    closeButtons.forEach((button) => {
        button.addEventListener('click', () => setSidebarOpen(false));
    });

    overlay?.addEventListener('click', () => setSidebarOpen(false));

    root.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        setSidebarOpen(false);
        root.querySelectorAll('[data-panel-dropdown]').forEach((dropdown) => closeDropdown(dropdown));
    });

    const desktopBreakpoint = window.matchMedia('(min-width: 64rem)');
    desktopBreakpoint.addEventListener('change', () => {
        setSidebarOpen(false);
        document.body.classList.remove('overflow-hidden');
    });

    root.querySelectorAll('[data-panel-dropdown]').forEach((dropdown) => {
        const trigger = dropdown.querySelector('[data-panel-dropdown-trigger]');
        const panel = dropdown.querySelector('[data-panel-dropdown-panel]');

        if (!trigger || !panel) {
            return;
        }

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = trigger.getAttribute('aria-expanded') === 'true';
            root.querySelectorAll('[data-panel-dropdown]').forEach((item) => {
                if (item !== dropdown) {
                    closeDropdown(item);
                }
            });
            isOpen ? closeDropdown(dropdown) : openDropdown(dropdown);
        });
    });

    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element) || !event.target.closest('[data-panel-dropdown]')) {
            root.querySelectorAll('[data-panel-dropdown]').forEach((dropdown) => closeDropdown(dropdown));
        }
    });
});

function openDropdown(dropdown) {
    const trigger = dropdown.querySelector('[data-panel-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-panel-dropdown-panel]');
    trigger?.setAttribute('aria-expanded', 'true');
    panel?.classList.remove('hidden');
}

function closeDropdown(dropdown) {
    const trigger = dropdown.querySelector('[data-panel-dropdown-trigger]');
    const panel = dropdown.querySelector('[data-panel-dropdown-panel]');
    trigger?.setAttribute('aria-expanded', 'false');
    panel?.classList.add('hidden');
}
