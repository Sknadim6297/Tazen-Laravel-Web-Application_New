@props(['status' => ''])

@php
    $normalized = strtolower(trim((string) $status));
    $variant = match ($normalized) {
        'completed', 'paid', 'confirmed', 'approved', 'success', 'active' => 'success',
        'pending', 'processing', 'rescheduled' => 'warning',
        'cancelled', 'canceled', 'failed', 'rejected', 'error' => 'error',
        default => 'neutral',
    };
    $label = $status !== '' ? ucfirst($normalized) : 'Unknown';
@endphp

<x-tazen-v2.ui.badge :variant="$variant" {{ $attributes->except('status') }}>{{ $label }}</x-tazen-v2.ui.badge>
