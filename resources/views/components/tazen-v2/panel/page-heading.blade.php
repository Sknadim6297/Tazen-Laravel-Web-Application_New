@props(['title', 'description' => null])

<header {{ $attributes->class('flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between') }}>
    <div class="max-w-2xl">
        <h1 class="font-display text-page-title font-semibold text-brand-secondary">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 text-sm leading-6 text-text-secondary sm:text-base">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex flex-wrap gap-2">{{ $actions }}</div>
    @endisset
</header>
