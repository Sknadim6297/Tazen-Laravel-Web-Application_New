<svg viewBox="0 0 80 80" focusable="false">
    @if ($icon === 'journal')
        <rect x="18" y="14" width="44" height="52" rx="8"></rect>
        <path d="M30 28h22M30 38h22M30 48h14"></path>
    @elseif ($icon === 'gratitude')
        <path d="M40 62s-16-9.5-16-20a9 9 0 0 1 16-5 9 9 0 0 1 16 5c0 10.5-16 20-16 20Z"></path>
    @else
        <circle cx="40" cy="40" r="22"></circle>
        <path d="M31 36h.1M49 36h.1M30 48c2.4 3 5.4 4.5 10 4.5S47.6 51 50 48"></path>
    @endif
</svg>
