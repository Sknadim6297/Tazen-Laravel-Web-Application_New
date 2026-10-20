@props([
    'label',
    'value',
    'hint' => null,
    'href' => null,
    'icon' => 'dot',
])

@php
    $classes = 'flex h-full flex-col rounded-card border border-border bg-surface p-5 shadow-card transition-colors hover:border-brand-200';
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes . ' focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring') }}>
@else
    <div {{ $attributes->class($classes) }}>
@endif
        <div class="mb-4 inline-flex size-10 items-center justify-center rounded-control bg-brand-50 text-brand-primary">
            <x-tazen-v2.panel.icon :name="$icon" class="size-5" />
        </div>
        <p class="text-sm font-medium text-text-secondary">{{ $label }}</p>
        <p class="mt-1 font-display text-2xl font-semibold text-brand-secondary">{{ $value }}</p>
        @if ($hint)
            <p class="mt-2 text-sm text-text-muted">{{ $hint }}</p>
        @endif
        {{ $slot }}
@if ($href)
    </a>
@else
    </div>
@endif
