@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'loadingText' => 'Loading',
])

@php
    $variantClasses = [
        'primary' => 'border border-brand-primary bg-brand-primary text-white hover:bg-brand-800 hover:border-brand-800',
        'secondary' => 'border border-brand-secondary bg-brand-secondary text-white hover:opacity-90',
        'outline' => 'border border-border bg-surface text-text-primary hover:bg-surface-muted',
        'danger' => 'border border-error bg-error text-white hover:opacity-90',
    ];
    $sizeClasses = [
        'sm' => 'min-h-9 gap-2 px-3 text-sm',
        'md' => 'min-h-11 gap-2 px-4 text-sm',
        'lg' => 'min-h-12 gap-2 px-5 text-base',
    ];
    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-control font-semibold transition-colors',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring',
        'disabled:cursor-not-allowed disabled:opacity-60',
        $variantClasses[$variant] ?? $variantClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ]);
    $unavailable = $disabled || $loading;
@endphp

@if ($href !== null)
    <a
        @if (! $unavailable) href="{{ $href }}" @endif
        @if ($unavailable) aria-disabled="true" tabindex="-1" @endif
        @if ($loading) aria-busy="true" @endif
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($loading)
            <span aria-hidden="true" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent"></span>
            <span class="sr-only">{{ $loadingText }}</span>
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        @disabled($unavailable)
        @if ($loading) aria-busy="true" @endif
        {{ $attributes->merge(['class' => $classes]) }}
    >
        @if ($loading)
            <span aria-hidden="true" class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent"></span>
            <span class="sr-only">{{ $loadingText }}</span>
        @endif
        {{ $slot }}
    </button>
@endif