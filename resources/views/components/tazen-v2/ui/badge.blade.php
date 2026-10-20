@props(['variant' => 'neutral'])

@php
    $variantClasses = [
        'success' => 'bg-success-surface text-success',
        'warning' => 'bg-warning-surface text-warning',
        'error' => 'bg-error-surface text-error',
        'info' => 'bg-info-surface text-info',
        'neutral' => 'bg-surface-muted text-text-secondary',
    ];
    $classes = implode(' ', [
        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
        $variantClasses[$variant] ?? $variantClasses['neutral'],
    ]);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</span>