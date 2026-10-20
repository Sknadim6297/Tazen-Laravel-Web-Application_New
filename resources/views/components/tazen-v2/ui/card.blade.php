@props(['padding' => 'md'])

@php
    $paddingClasses = [
        'sm' => 'p-4',
        'md' => 'p-6',
        'lg' => 'p-8',
    ];
    $contentPadding = $paddingClasses[$padding] ?? $paddingClasses['md'];
@endphp

<section {{ $attributes->class('overflow-hidden rounded-card border border-border bg-surface shadow-card') }}>
    @isset($heading)
        <header class="border-b border-divider px-6 py-4">
            {{ $heading }}
        </header>
    @endisset

    <div class="{{ $contentPadding }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="border-t border-divider bg-surface-muted px-6 py-4">
            {{ $footer }}
        </footer>
    @endisset
</section>