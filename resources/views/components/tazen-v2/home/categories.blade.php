@props(['categories' => null])

@php
    $categoryCollection = collect($categories ?? [])->take(6);
    $resolveServiceImage = static function ($path): string {
        $fallback = asset('frontend/assets/img/6.png');

        if (!filled($path)) {
            return $fallback;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            $urlPath = $parts['path'] ?? '';

            if (!in_array($scheme, ['http', 'https'], true)) {
                return $fallback;
            }

            if (in_array($host, ['localhost', '127.0.0.1'], true) && str_starts_with($urlPath, '/storage/')) {
                $path = substr($urlPath, strlen('/storage/'));
            } else {
                return $path;
            }
        }

        $path = ltrim($path, '/');
        $storagePath = str_starts_with($path, 'storage/') ? substr($path, strlen('storage/')) : $path;

        if (is_file(public_path($path))) {
            return asset($path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            && is_file(public_path('storage/' . $storagePath))) {
            return asset('storage/' . $storagePath);
        }

        return $fallback;
    };
@endphp

<section aria-labelledby="tazen-v2-categories-title" class="bg-surface py-14 sm:py-18 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Explore categories</p>
                <h2 id="tazen-v2-categories-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                    Find the expertise you need
                </h2>
                <p class="mt-3 text-base leading-7 text-text-secondary">
                    Browse consultation categories and explore the services available on Yuktione.
                </p>
            </div>

            <x-tazen-v2.ui.button href="{{ route('gridlisting') }}" variant="outline" size="md" class="shrink-0">
                Browse experts
            </x-tazen-v2.ui.button>
        </div>

        @if ($categoryCollection->isEmpty())
            <div class="rounded-card border border-border bg-background px-5 py-8 text-center sm:px-8">
                <p class="text-base font-medium text-text-primary">No consultation categories are available right now.</p>
                <p class="mt-2 text-sm text-text-secondary">You can still browse all experts and services.</p>
                <a href="{{ route('gridlisting') }}" class="mt-4 inline-flex min-h-10 items-center rounded-control px-3 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    Browse experts
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
                @foreach ($categoryCollection as $category)
                    <article class="h-full">
                        <x-tazen-v2.ui.card padding="md" class="group h-full transition-shadow hover:shadow-popover">
                            <div class="mb-5 overflow-hidden rounded-control bg-surface-muted">
                                @if (filled($category->image))
                                    <img
                                        src="{{ $resolveServiceImage($category->image) }}"
                                        alt="{{ $category->name }}"
                                        loading="lazy"
                                        class="aspect-16/10 w-full object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                                    >
                                @else
                                    <div aria-hidden="true" class="flex aspect-16/10 items-center justify-center bg-brand-50">
                                        <span class="font-display text-5xl font-semibold text-brand-primary">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($category->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <h3 id="tazen-v2-category-{{ $loop->index }}" class="font-display text-card-title font-semibold text-text-primary">
                                {{ $category->name }}
                            </h3>

                            @if (filled($category->meta_description ?? null))
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-text-secondary">{{ $category->meta_description }}</p>
                            @endif

                            <x-slot:footer>
                                @if (filled($category->slug))
                                    <a
                                        href="{{ route('service.show', $category->slug) }}"
                                        class="inline-flex min-h-10 items-center gap-2 rounded-control text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                                    >
                                        Explore service
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                @elseif (filled($category->id))
                                    <a
                                        href="{{ route('gridlisting', ['service_id' => $category->id]) }}"
                                        class="inline-flex min-h-10 items-center rounded-control text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring"
                                    >
                                        Find experts
                                    </a>
                                @endif
                            </x-slot:footer>
                        </x-tazen-v2.ui.card>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>