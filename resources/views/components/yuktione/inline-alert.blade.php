@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $type = in_array($type, ['success', 'error', 'warning', 'info'], true) ? $type : 'info';
@endphp

<div {{ $attributes->class(['yk-inline-alert', "yk-inline-alert--{$type}"]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <div class="yk-inline-alert__content">
        @if ($title)
            <p class="yk-inline-alert__title">{{ $title }}</p>
        @endif
        <div class="yk-inline-alert__message">{{ $slot }}</div>
    </div>
    @if ($dismissible)
        <button type="button" class="yk-feedback__close" data-yuktione-dismiss aria-label="Dismiss message">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
</div>
