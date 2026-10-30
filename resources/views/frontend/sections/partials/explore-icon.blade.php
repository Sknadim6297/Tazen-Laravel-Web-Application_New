@php
    $paths = [
        'grid' => '<rect x="4" y="4" width="6" height="6" rx="1"/><rect x="14" y="4" width="6" height="6" rx="1"/><rect x="4" y="14" width="6" height="6" rx="1"/><rect x="14" y="14" width="6" height="6" rx="1"/>',
        'lotus' => '<path d="M12 20c2-4 2-7 0-10-2 3-2 6 0 10Z"/><path d="M12 20c4-1 7-3 8-7-3 1-6 2-8 7Z"/><path d="M12 20c-4-1-7-3-8-7 3 1 6 2 8 7Z"/><path d="M12 11c2-3 2-6 0-8-2 2-2 5 0 8Z"/>',
        'moon' => '<path d="M15 3.5A7.5 7.5 0 1 0 20.5 14 6 6 0 0 1 15 3.5Z"/>',
        'heart' => '<path d="M12 19s-7-4.4-7-9a4 4 0 0 1 7-2 4 4 0 0 1 7 2c0 4.6-7 9-7 9Z"/>',
        'chart' => '<path d="M4 19h16"/><path d="m6 15 4-5 3 3 5-7"/>',
        'coins' => '<ellipse cx="12" cy="7" rx="7" ry="3"/><path d="M5 7v5c0 1.7 3.1 3 7 3s7-1.3 7-3V7"/><path d="M5 12v5c0 1.7 3.1 3 7 3s7-1.3 7-3v-5"/>',
        'leaf' => '<path d="M5 19C14 19 19 13 19 5 11 5 5 10 5 19Z"/><path d="M8 16c3-1 6-4 7-8"/>',
        'family' => '<circle cx="8" cy="8" r="2"/><circle cx="16" cy="8" r="2"/><circle cx="12" cy="11" r="2"/><path d="M4 19c.5-2.5 2.2-4 4-4s3.5 1.5 4 4"/><path d="M12 19c.5-2.5 2.2-4 4-4s3.5 1.5 4 4"/>',
        'cup' => '<path d="M6 8h9v5a4 4 0 0 1-4 4H10a4 4 0 0 1-4-4V8Z"/><path d="M15 9h2a2 2 0 0 1 0 4h-2"/><path d="M8 20h6"/>',
        'spark' => '<path d="M12 3v4"/><path d="M12 17v4"/><path d="M3 12h4"/><path d="M17 12h4"/><path d="m6 6 2.5 2.5"/><path d="m15.5 15.5 2.5 2.5"/><path d="m18 6-2.5 2.5"/><path d="m8.5 15.5-2.5 2.5"/>',
        'bulb' => '<path d="M9 18h6"/><path d="M10 21h4"/><path d="M8 14a4.5 4.5 0 1 1 8 0c0 2-1 3-2 4H10c-1-1-2-2-2-4Z"/>',
        'fitness' => '<path d="M6 9v6"/><path d="M18 9v6"/><path d="M4 10v4"/><path d="M20 10v4"/><path d="M6 12h12"/>',
    ];
@endphp
<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">{!! $paths[$icon] ?? $paths['leaf'] !!}</svg>
