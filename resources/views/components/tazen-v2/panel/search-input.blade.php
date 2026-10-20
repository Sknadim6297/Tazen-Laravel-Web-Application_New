@props([
    'name' => 'search',
    'value' => null,
    'placeholder' => 'Search',
    'label' => 'Search',
])

<label class="grid gap-1.5">
    <span class="text-sm font-semibold text-text-primary">{{ $label }}</span>
    <span class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-text-muted">
            <x-tazen-v2.panel.icon name="search" class="size-4" />
        </span>
        <input
            type="search"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->class('block min-h-11 w-full rounded-control border border-border bg-surface py-2 pl-10 pr-3 text-sm text-text-primary placeholder:text-text-muted') }}
        >
    </span>
</label>
