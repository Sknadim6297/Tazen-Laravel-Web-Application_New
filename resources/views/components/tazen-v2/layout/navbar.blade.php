@php
    $navigationLinks = [
        ['label' => 'Home', 'url' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Find experts', 'url' => route('gridlisting'), 'active' => request()->routeIs('gridlisting')],
        ['label' => 'Events', 'url' => route('event.list'), 'active' => request()->routeIs('event.list')],
        ['label' => 'Insights', 'url' => route('blog.index'), 'active' => request()->routeIs('blog.index')],
        ['label' => 'Help', 'url' => route('help.index'), 'active' => request()->routeIs('help.index')],
        ['label' => 'About', 'url' => url('/about'), 'active' => request()->is('about')],
        ['label' => 'Contact', 'url' => url('/contact'), 'active' => request()->is('contact')],
    ];
    $navigationId = 'tazen-v2-primary-navigation';
@endphp

<header data-tazen-v2-navbar class="sticky top-0 z-40 border-b border-divider bg-surface/95 backdrop-blur">
    <div class="mx-auto flex min-h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" aria-label="Yuktione home" class="inline-flex shrink-0 items-center rounded-control focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-focus-ring">
            <img
            src="{{ asset('frontend/assets/Yuktione_logo.png') }}"
            alt="Yuktione"
            width="155"
            height="64"
            class="h-12 w-auto max-w-40 object-contain"
            >
        </a>

        <button
            type="button"
            data-tazen-v2-menu-toggle
            aria-controls="{{ $navigationId }}"
            aria-expanded="false"
            aria-label="Open navigation menu"
            class="inline-flex min-h-11 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring lg:hidden"
        >
            <span data-tazen-v2-menu-label>Menu</span>
        </button>

        <nav
            id="{{ $navigationId }}"
            aria-label="Primary navigation"
            data-tazen-v2-menu-panel
            class="absolute inset-x-0 top-full z-40 border-b border-divider bg-surface p-4 shadow-popover max-lg:hidden lg:static lg:ml-auto lg:flex lg:items-center lg:gap-3 lg:border-0 lg:bg-transparent lg:p-0 lg:shadow-none"
        >
            <ul class="flex flex-col gap-1 lg:flex-row lg:items-center lg:gap-1">
                @foreach ($navigationLinks as $link)
                    <li>
                        <a
                            href="{{ $link['url'] }}"
                            @if ($link['active']) aria-current="page" @endif
                            class="block rounded-control px-3 py-2 text-sm font-semibold text-text-secondary transition-colors hover:bg-surface-muted hover:text-brand-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring {{ $link['active'] ? 'bg-surface-muted text-brand-primary' : '' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-4 flex flex-col gap-2 border-t border-divider pt-4 lg:mt-0 lg:flex-row lg:border-0 lg:pt-0">
                <x-tazen-v2.ui.button href="{{ route('login') }}" variant="primary" size="sm" class="w-full lg:w-auto">
                    Login
                </x-tazen-v2.ui.button>
            </div>
        </nav>
    </div>
</header>

@once
    @push('scripts')
        @vite('resources/js/tazen-v2/navbar.js')
    @endpush
@endonce