@props([
    'title' => 'Nothing to show yet',
    'description' => null,
    'icon' => 'inbox',
])

<div {{ $attributes->class('rounded-card border border-dashed border-border bg-surface-muted px-5 py-10 text-center') }}>
    <div class="mx-auto mb-3 inline-flex size-12 items-center justify-center rounded-full bg-brand-50 text-brand-primary">
        <x-tazen-v2.panel.icon :name="$icon" class="size-6" />
    </div>
    <p class="text-base font-semibold text-text-primary">{{ $title }}</p>
    @if ($description)
        <p class="mt-2 text-sm leading-6 text-text-secondary">{{ $description }}</p>
    @endif
    @isset($actions)
        <div class="mt-4 flex justify-center gap-2">{{ $actions }}</div>
    @endisset
</div>
