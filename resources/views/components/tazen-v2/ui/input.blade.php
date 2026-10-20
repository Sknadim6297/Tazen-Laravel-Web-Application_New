@props([
    'name',
    'label' => null,
    'type' => 'text',
    'id' => null,
    'value' => null,
    'placeholder' => null,
    'error' => null,
    'required' => false,
    'disabled' => false,
])

@php
    $allowedTypes = ['text', 'email', 'password', 'number'];
    $type = in_array($type, $allowedTypes, true) ? $type : 'text';
    $fieldId = $id ?? str_replace(['[', ']', '.'], '-', $name);
    $errorId = $fieldId . '-error';
    $errorMessage = $error ?? $errors->first($name);
    $describedBy = trim(implode(' ', array_filter([
        $attributes->get('aria-describedby'),
        $errorMessage ? $errorId : null,
    ])));
    $inputClasses = 'block min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-base text-text-primary placeholder:text-text-muted focus-visible:border-brand-primary disabled:cursor-not-allowed disabled:bg-surface-muted disabled:text-text-muted';
    if ($errorMessage) {
        $inputClasses .= ' border-error';
    }
    $inputAttributes = $attributes->except(['value', 'aria-invalid', 'aria-describedby']);
@endphp

<div class="grid gap-1.5">
    @if ($label)
        <label for="{{ $fieldId }}" class="text-sm font-semibold text-text-primary">
            {{ $label }}
            @if ($required)
                <span aria-hidden="true" class="text-error">*</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
        @if ($required) required aria-required="true" @endif
        @if ($disabled) disabled @endif
        @if ($errorMessage) aria-invalid="true" aria-describedby="{{ $describedBy }}" @elseif ($describedBy !== '') aria-describedby="{{ $describedBy }}" @endif
        {{ $inputAttributes->merge(['class' => $inputClasses]) }}
    >

    @if ($errorMessage)
        <p id="{{ $errorId }}" class="text-sm text-error" role="alert">{{ $errorMessage }}</p>
    @endif
</div>