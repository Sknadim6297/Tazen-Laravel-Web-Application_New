@props(['events' => null])

@php
    $eventCollection = collect($events ?? []);
    $resolveEventImage = static function ($path): ?string {
        if (!filled($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            $urlPath = $parts['path'] ?? '';

            if (!in_array($scheme, ['http', 'https'], true)) {
                return null;
            }

            if (!in_array($host, ['localhost', '127.0.0.1'], true) || !str_starts_with($urlPath, '/storage/')) {
                return $path;
            }

            $path = substr($urlPath, strlen('/storage/'));
        }

        $path = ltrim($path, '/');
        $storagePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;

        if (is_file(public_path($path))) {
            return asset($path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            && is_file(public_path('storage/' . $storagePath))) {
            return asset('storage/' . $storagePath);
        }

        return null;
    };
@endphp

<section aria-labelledby="tazen-v2-events-title" class="bg-surface py-14 sm:py-18 lg:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Learn and connect</p>
                <h2 id="tazen-v2-events-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                    Upcoming Events
                </h2>
                <p class="mt-3 text-base leading-7 text-text-secondary">
                    Explore events published by Yuktione and its professionals.
                </p>
            </div>

            <x-tazen-v2.ui.button href="{{ route('event.list') }}" variant="outline" size="md" class="shrink-0">
                View all events
            </x-tazen-v2.ui.button>
        </div>

        @if ($eventCollection->isEmpty())
            <div class="rounded-card border border-border bg-background px-5 py-8 text-center sm:px-8">
                <p class="text-base font-medium text-text-primary">No events are available right now.</p>
                <p class="mt-2 text-sm text-text-secondary">Please check back later for published events.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
                @foreach ($eventCollection as $event)
                    @php
                        $eventImage = $resolveEventImage($event->image ?? null);
                        $eventTitle = $event->title ?? 'Event';
                    @endphp

                    <article aria-labelledby="tazen-v2-event-{{ $loop->index }}" class="group flex h-full flex-col overflow-hidden rounded-card border border-border bg-surface shadow-card transition-shadow hover:shadow-popover">
                        @if ($eventImage)
                            <img
                                src="{{ $eventImage }}"
                                alt="{{ $eventTitle }}"
                                width="800"
                                height="500"
                                loading="lazy"
                                class="aspect-16/10 w-full object-cover"
                            >
                        @else
                            <div aria-hidden="true" class="flex aspect-16/10 items-center justify-center bg-brand-50">
                                <span class="rounded-full border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-primary">Yuktione event</span>
                            </div>
                        @endif

                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <h3 id="tazen-v2-event-{{ $loop->index }}" class="font-display text-card-title font-semibold text-brand-secondary">
                                {{ $eventTitle }}
                            </h3>

                            @if (filled($event->description ?? null))
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-text-secondary">{{ $event->description }}</p>
                            @endif

                            <dl class="mt-4 grid gap-2 text-sm text-text-secondary">
                                @if (filled($event->date ?? null))
                                    <div class="flex gap-2">
                                        <dt class="font-semibold text-text-primary">Date</dt>
                                        <dd>{{ \Illuminate\Support\Carbon::parse($event->date)->format('D, M j, Y') }}</dd>
                                    </div>
                                @endif
                                @if (filled($event->time ?? null))
                                    <div class="flex gap-2">
                                        <dt class="font-semibold text-text-primary">Time</dt>
                                        <dd>{{ \Illuminate\Support\Carbon::parse($event->time)->format('g:i A') }}</dd>
                                    </div>
                                @endif
                                @if (filled($event->location ?? null))
                                    <div class="flex gap-2">
                                        <dt class="font-semibold text-text-primary">Location</dt>
                                        <dd>{{ $event->location }}</dd>
                                    </div>
                                @elseif (filled($event->event_mode ?? null))
                                    <div class="flex gap-2">
                                        <dt class="font-semibold text-text-primary">Format</dt>
                                        <dd>{{ \Illuminate\Support\Str::headline($event->event_mode) }}</dd>
                                    </div>
                                @endif
                                @if (is_numeric($event->price ?? null) && function_exists('formatPrice'))
                                    <div class="flex gap-2">
                                        <dt class="font-semibold text-text-primary">Price</dt>
                                        <dd>{{ formatPrice($event->price) }}</dd>
                                    </div>
                                @endif
                            </dl>

                            <div class="mt-auto pt-5">
                                <x-tazen-v2.ui.button href="{{ route('event.details', $event->event_id) }}" variant="primary" size="sm" class="w-full">
                                    View event
                                </x-tazen-v2.ui.button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>