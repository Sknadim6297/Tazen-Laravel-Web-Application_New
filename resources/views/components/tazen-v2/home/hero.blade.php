@props(['services' => []])

@php
    $featuredServices = collect($services)->take(4);
@endphp

<section aria-labelledby="tazen-v2-hero-title" class="overflow-hidden bg-background">
    <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 sm:py-16 md:grid-cols-2 md:gap-12 lg:px-8 lg:py-24">
        <div class="max-w-2xl">
            <p class="mb-5 inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-xs font-semibold uppercase text-brand-900">
                <span aria-hidden="true" class="size-2 rounded-full bg-brand-primary"></span>
                Yuktione expert consultations
            </p>

            <h1 id="tazen-v2-hero-title" class="font-display text-4xl font-semibold leading-tight text-brand-secondary sm:text-5xl">
                One platform. Multiple ways to move forward.
            </h1>

            <p class="mt-5 max-w-xl text-base leading-7 text-text-secondary sm:text-lg">
                Explore expert services and find a consultation that fits what you need.
            </p>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <x-tazen-v2.ui.button href="{{ route('gridlisting') }}" size="lg">
                    Find an expert
                </x-tazen-v2.ui.button>
                <x-tazen-v2.ui.button href="{{ route('event.list') }}" variant="outline" size="lg">
                    Explore events
                </x-tazen-v2.ui.button>
            </div>

            <form
                action="{{ route('search.services') }}"
                method="GET"
                data-tazen-v2-hero-search
                data-service-route="{{ route('service.show', ['service' => '__slug__']) }}"
                class="mt-9 rounded-panel border border-border bg-surface p-4 shadow-card sm:p-5"
            >
                <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto] sm:items-end">
                    <x-tazen-v2.ui.input
                        name="query"
                        label="Search services"
                        placeholder="What would you like help with?"
                        list="tazen-v2-service-suggestions"
                        autocomplete="off"
                        required
                        aria-describedby="tazen-v2-service-search-status"
                    />
                    <datalist id="tazen-v2-service-suggestions"></datalist>
                    <x-tazen-v2.ui.button type="submit" size="md" data-tazen-v2-search-submit>
                        Search services
                    </x-tazen-v2.ui.button>
                </div>
                <p id="tazen-v2-service-search-status" class="mt-2 min-h-5 text-sm text-text-secondary" role="status" aria-live="polite" data-tazen-v2-search-status></p>
            </form>

            @if ($featuredServices->isNotEmpty())
                <nav aria-label="Popular services" class="mt-6">
                    <ul class="flex flex-wrap gap-2">
                        @foreach ($featuredServices as $service)
                            <li>
                                <a
                                    href="{{ route('service.show', $service->slug) }}"
                                    class="inline-flex min-h-10 items-center rounded-full border border-brand-200 bg-surface px-4 py-2 text-sm font-medium text-brand-secondary transition-colors hover:border-brand-primary hover:text-brand-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                                >
                                    {{ $service->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>

        <figure class="relative mx-auto w-full max-w-xl md:ml-auto">
            <div aria-hidden="true" class="absolute -bottom-4 -left-4 z-0 h-24 w-24 rounded-panel bg-linear-to-br from-brand-700/20 to-brand-400/20 sm:-bottom-5 sm:-left-5 sm:h-32 sm:w-32"></div>
            <div aria-hidden="true" class="absolute -right-3 -top-3 z-0 h-16 w-16 rounded-panel border-2 border-brand-accent/60 sm:-right-4 sm:-top-4 sm:h-20 sm:w-20"></div>
            <img
                src="{{ asset('frontend/assets/img/slides/slide_home_2.jpg') }}"
                alt="A professional reviewing information on a tablet"
                width="1450"
                height="800"
                fetchpriority="high"
                class="relative z-10 aspect-4/3 w-full rounded-panel object-cover object-center shadow-popover"
            >
        </figure>
    </div>
</section>

@once
    @push('scripts')
        @vite('resources/js/tazen-v2/hero.js')
    @endpush
@endonce