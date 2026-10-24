@php
    $hasUpcomingEvents = $eventDetails->contains(
        fn ($eventDetail) => $eventDetail->event_date && !$eventDetail->event_date->copy()->startOfDay()->lt(\Carbon\Carbon::today())
    );
@endphp

<section class="yk-home-events" aria-labelledby="yk-home-events-title">
    <div class="yk-home-container">
        <header class="yk-home-events__heading">
            <div class="yk-home-events__heading-copy">
                <p class="yk-home-events__eyebrow">Events &amp; Workshops</p>
                <h2 id="yk-home-events-title">Discover Events &amp; Expert-Led Sessions</h2>
                <p>Explore workshops, webinars and experiences designed to help you learn and grow.</p>
            </div>
            <a class="yk-home-events__all-link" href="{{ route('event.list') }}">
                View All Events
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M4 12h15M13 6l6 6-6 6"/>
                </svg>
            </a>
        </header>

        @if ($eventDetails->isNotEmpty())
            @if (!$hasUpcomingEvents)
                <p class="yk-home-events__archive-label">Past Events</p>
            @endif

            <div class="yk-home-events__grid">
                @foreach ($eventDetails as $eventDetail)
                    @php
                        $imagePath = is_array($eventDetail->banner_image)
                            ? ($eventDetail->banner_image[0] ?? null)
                            : $eventDetail->banner_image;
                        $imagePath = $imagePath ?: $eventDetail->event->card_image;
                        $cleanImagePath = ltrim((string) $imagePath, '/');
                        if (\Illuminate\Support\Str::startsWith($cleanImagePath, ['http://', 'https://'])) {
                            $imageUrl = $cleanImagePath;
                        } else {
                            $storagePath = \Illuminate\Support\Str::startsWith($cleanImagePath, 'storage/')
                                ? \Illuminate\Support\Str::after($cleanImagePath, 'storage/')
                                : $cleanImagePath;
                            $imageUrl = $storagePath ? asset('storage/' . $storagePath) : null;
                        }
                        $isPastEvent = $eventDetail->event_date
                            && $eventDetail->event_date->copy()->startOfDay()->lt(\Carbon\Carbon::today());
                    @endphp

                    <article class="yk-home-event-card">
                        <a class="yk-home-event-card__image" href="{{ url('/allevent/' . $eventDetail->event_id) }}" aria-label="View {{ $eventDetail->event->heading }}">
                            @if ($imageUrl)
                                <img
                                    src="{{ $imageUrl }}"
                                    alt="{{ $eventDetail->event->heading }}"
                                    loading="lazy"
                                    decoding="async"
                                    data-event-image
                                >
                            @endif
                            <span class="yk-home-event-card__image-fallback" aria-hidden="{{ $imageUrl ? 'true' : 'false' }}" @if ($imageUrl) hidden @endif>
                                <svg viewBox="0 0 64 64" focusable="false">
                                    <path d="M12 14h40v36H12zM19 9v10m26-10v10M12 24h40"/>
                                    <path d="m22 40 7-7 6 6 5-5 8 8"/>
                                    <circle cx="23" cy="31" r="2"/>
                                </svg>
                            </span>
                        </a>

                        <div class="yk-home-event-card__body">
                            <div class="yk-home-event-card__badges">
                                @if ($eventDetail->event_type)
                                    <span class="yk-home-event-card__type">{{ $eventDetail->event_type }}</span>
                                @endif
                                @if ($eventDetail->event_date)
                                    <span class="yk-home-event-card__status {{ $isPastEvent ? 'is-past' : 'is-upcoming' }}">
                                        {{ $isPastEvent ? 'Past Event' : 'Upcoming' }}
                                    </span>
                                @endif
                            </div>
                            <h3>{{ $eventDetail->event->heading }}</h3>

                            @if ($eventDetail->description)
                                <p class="yk-home-event-card__description">{{ $eventDetail->description }}</p>
                            @endif

                            <dl class="yk-home-event-card__details">
                                @if ($eventDetail->event_date)
                                    <div>
                                        <dt>
                                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                                <path d="M16 3v4M8 3v4M3 10h18"/>
                                            </svg>
                                            Date
                                        </dt>
                                        <dd>{{ $eventDetail->event_date->format('j M Y') }}</dd>
                                    </div>
                                @endif

                                @if ($eventDetail->organizer_name)
                                    <div>
                                        <dt>
                                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                                <circle cx="12" cy="8" r="4"/>
                                                <path d="M4 21a8 8 0 0 1 16 0"/>
                                            </svg>
                                            Organizer
                                        </dt>
                                        <dd>{{ $eventDetail->organizer_name }}</dd>
                                    </div>
                                @endif
                            </dl>

                            <div class="yk-home-event-card__footer">
                                <span class="yk-home-event-card__price">{{ formatPrice($eventDetail->starting_fees) }}</span>
                                <a class="yk-home-event-card__button" href="{{ url('/allevent/' . $eventDetail->event_id) }}">
                                    View Event
                                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                        <path d="M4 12h15M13 6l6 6-6 6"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="yk-home-events__empty">No events are available right now. Please check back soon.</p>
        @endif

    </div>
</section>
