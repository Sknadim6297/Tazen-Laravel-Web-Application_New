@props(['name' => 'dot', 'class' => 'size-5'])

@php
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3.75 21 10.5V20.25a.75.75 0 0 1-.75.75h-5.25v-6h-4.5v6H3.75A.75.75 0 0 1 3 20.25V10.5Z" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75v2.25M17.25 3.75v2.25M3.75 8.25h16.5M5.25 6h13.5A1.5 1.5 0 0 1 20.25 7.5v11.25A1.5 1.5 0 0 1 18.75 20.25H5.25A1.5 1.5 0 0 1 3.75 18.75V7.5A1.5 1.5 0 0 1 5.25 6Z" />',
        'clock' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v5.25l3 1.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a7.5 7.5 0 0 1 15 0" />',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9M7.5 12h5.25M4.5 6.75A2.25 2.25 0 0 1 6.75 4.5h10.5A2.25 2.25 0 0 1 19.5 6.75v7.5A2.25 2.25 0 0 1 17.25 16.5H9l-4.5 3v-12.75Z" />',
        'spark' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75 8.25 8.25 3.75 9.75l4.5 1.5 1.5 4.5 1.5-4.5 4.5-1.5-4.5-1.5-1.5-4.5Zm7.5 7.5-.75 2.25-2.25.75 2.25.75.75 2.25.75-2.25 2.25-.75-2.25-.75-.75-2.25Z" />',
        'wallet' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25h16.5v9A1.5 1.5 0 0 1 18.75 18.75H5.25A1.5 1.5 0 0 1 3.75 17.25v-9Zm0 0 1.72-2.58A1.5 1.5 0 0 1 6.72 4.5h10.56a1.5 1.5 0 0 1 1.25.67L20.25 8.25M15.75 13.5h.008v.008H15.75V13.5Z" />',
        'plus' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75v10.5M6.75 12h10.5" />',
        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A1.5 1.5 0 0 0 14.25 3.75h-7.5A1.5 1.5 0 0 0 5.25 5.25v13.5A1.5 1.5 0 0 0 6.75 20.25h7.5A1.5 1.5 0 0 0 15.75 18.75V15M12 12h9m0 0-2.25-2.25M21 12l-2.25 2.25" />',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.25 18.75a2.25 2.25 0 0 1-4.5 0M6 9.75a6 6 0 1 1 12 0c0 3.3.75 4.5 1.5 5.25H4.5C5.25 14.25 6 13.05 6 9.75Z" />',
        'menu' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />',
        'close' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6 6 18" />',
        'search' => '<path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17.25 10.5a6.75 6.75 0 1 1-13.5 0 6.75 6.75 0 0 1 13.5 0Z" />',
        'arrow' => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12l-7.5 7.5M21 12H3" />',
        'inbox' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 8.25 12 3.75l8.25 4.5v9A1.5 1.5 0 0 1 18.75 18.75H5.25A1.5 1.5 0 0 1 3.75 17.25v-9Zm0 0 8.25 4.5 8.25-4.5" />',
        'dot' => '<circle cx="12" cy="12" r="3" />',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="m5.25 12.75 4.5 4.5 9-9" />',
        'warning' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4.5M12 17.25h.008v.008H12V17.25Zm-7.07 1.5h14.14a1.5 1.5 0 0 0 1.3-2.25L13.3 5.25a1.5 1.5 0 0 0-2.6 0L3.63 16.5a1.5 1.5 0 0 0 1.3 2.25Z" />',
    ];
    $path = $icons[$name] ?? $icons['dot'];
@endphp

<svg {{ $attributes->class($class . ' shrink-0') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
    {!! $path !!}
</svg>
