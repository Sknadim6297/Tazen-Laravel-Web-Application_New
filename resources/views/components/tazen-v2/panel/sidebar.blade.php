@props([
    'items' => [],
    'brandHref' => null,
])

@php
    $brandHref = $brandHref ?? route('home');
@endphp

<aside
    {{ $attributes->merge([
        'data-panel-sidebar' => true,
        'data-open' => 'false',
        'aria-label' => 'Panel navigation',
        'class' => 'fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col border-r border-border bg-surface motion-safe:transition-transform motion-safe:duration-200 data-[open=true]:translate-x-0 lg:translate-x-0',
    ]) }}
>
    <div class="flex items-center justify-between gap-3 border-b border-divider px-5 py-4">
        <a href="{{ $brandHref }}" class="inline-flex min-h-11 items-center rounded-control focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
            <img
                src="{{ asset('frontend/assets/Yuktione_logo.png') }}"
                alt="Yuktione"
                width="155"
                height="64"
                class="h-10 w-auto max-w-40 object-contain object-left"
            >
        </a>
        <button
            type="button"
            data-panel-close
            class="inline-flex size-11 items-center justify-center rounded-control border border-border text-text-secondary hover:bg-surface-muted lg:hidden"
            aria-label="Close navigation"
        >
            <x-tazen-v2.panel.icon name="close" />
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Panel modules">
        @foreach ($items as $section)
            @if (! empty($section['title']))
                <p class="mb-2 mt-4 px-3 text-xs font-semibold uppercase tracking-wide text-text-muted first:mt-0">
                    {{ $section['title'] }}
                </p>
            @endif
            <ul class="grid gap-1">
                @foreach ($section['links'] as $link)
                    <li>
                        <a
                            href="{{ $link['url'] }}"
                            @if (! empty($link['active'])) aria-current="page" @endif
                            @if (! empty($link['external'])) target="{{ $link['target'] ?? '_self' }}" @endif
                            class="flex min-h-11 items-center gap-3 rounded-control px-3 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring {{ ! empty($link['active']) ? 'bg-brand-50 text-brand-primary' : 'text-text-secondary hover:bg-surface-muted hover:text-text-primary' }}"
                        >
                            <x-tazen-v2.panel.icon :name="$link['icon'] ?? 'dot'" class="size-5" />
                            <span>{{ $link['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    @isset($footer)
        <div class="border-t border-divider p-3">
            {{ $footer }}
        </div>
    @endisset
</aside>
