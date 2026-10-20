document.querySelectorAll('[data-tazen-v2-navbar]').forEach((navbar) => {
    const toggle = navbar.querySelector('[data-tazen-v2-menu-toggle]');
    const panel = navbar.querySelector('[data-tazen-v2-menu-panel]');
    const label = navbar.querySelector('[data-tazen-v2-menu-label]');

    if (!toggle || !panel || !label) {
        return;
    }

    const setExpanded = (expanded) => {
        toggle.setAttribute('aria-expanded', String(expanded));
        toggle.setAttribute('aria-label', expanded ? 'Close navigation menu' : 'Open navigation menu');
        label.textContent = expanded ? 'Close' : 'Menu';
        panel.classList.toggle('max-lg:hidden', !expanded);
    };

    toggle.addEventListener('click', () => {
        setExpanded(toggle.getAttribute('aria-expanded') !== 'true');
    });

    navbar.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            event.preventDefault();
            setExpanded(false);
            toggle.focus();
        }
    });

    panel.addEventListener('click', (event) => {
        if (event.target instanceof Element && event.target.closest('a')) {
            setExpanded(false);
        }
    });

    const desktopBreakpoint = window.matchMedia('(min-width: 64rem)');
    desktopBreakpoint.addEventListener('change', () => {
        if (desktopBreakpoint.matches) {
            setExpanded(false);
        }
    });
});