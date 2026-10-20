@props(['status', 'label' => null])

@php
    $normalizedStatus = strtolower((string) $status);
    $classes = match ($normalizedStatus) {
        'success', 'active', 'paid', 'accepted', 'approved' => 'bg-success-surface text-success',
        'failed', 'rejected', 'unpaid' => 'bg-error-surface text-error',
        'pending' => 'bg-warning-surface text-warning',
        default => 'bg-surface-muted text-text-secondary',
    };
    $displayLabel = $label ?? ucfirst(str_replace('_', ' ', (string) $status));
@endphp

<span {{ $attributes->class(["inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold leading-4 {$classes}"]) }}>
    {{ $displayLabel }}
</span>
