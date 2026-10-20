@props(['label' => 'Loading'])

<div {{ $attributes->class('grid gap-3') }} role="status" aria-live="polite">
    <span class="sr-only">{{ $label }}</span>
    <div class="h-24 animate-pulse rounded-card bg-brand-100 motion-reduce:animate-none"></div>
    <div class="h-24 animate-pulse rounded-card bg-brand-100 motion-reduce:animate-none"></div>
    <div class="h-24 animate-pulse rounded-card bg-brand-100 motion-reduce:animate-none"></div>
</div>
