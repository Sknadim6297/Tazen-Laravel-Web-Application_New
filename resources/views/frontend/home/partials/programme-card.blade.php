@php
    $imagePath = is_array($programme->banner_image)
        ? ($programme->banner_image[0] ?? null)
        : $programme->banner_image;
    $imagePath = $imagePath ?: $programme->event->card_image;
    $cleanImagePath = ltrim((string) $imagePath, '/');
    if (\Illuminate\Support\Str::startsWith($cleanImagePath, ['http://', 'https://'])) {
        $imageUrl = $cleanImagePath;
    } else {
        $storagePath = \Illuminate\Support\Str::startsWith($cleanImagePath, 'storage/')
            ? \Illuminate\Support\Str::after($cleanImagePath, 'storage/')
            : $cleanImagePath;
        $imageUrl = $storagePath ? asset('storage/' . $storagePath) : null;
    }
    $isPastProgramme = $programme->event_date
        && $programme->event_date->copy()->startOfDay()->lt(\Carbon\Carbon::today());
    $programmeUrl = url('/allevent/' . $programme->event_id);
@endphp

<article class="yk-home-event-card" data-programme-card data-programme-reveal>
    <a class="yk-home-event-card__image" href="{{ $programmeUrl }}" aria-label="View programme: {{ $programme->event->heading }}">
        @if ($imageUrl)
            <img
                src="{{ $imageUrl }}"
                alt="{{ $programme->event->heading }}"
                width="640"
                height="360"
                loading="lazy"
                decoding="async"
                data-programme-image
            >
        @endif
        <span class="yk-home-event-card__image-fallback" aria-hidden="{{ $imageUrl ? 'true' : 'false' }}" @if ($imageUrl) hidden @endif>
            <svg viewBox="0 0 64 64" focusable="false" aria-hidden="true">
                <path d="M12 14h40v36H12zM19 9v10m26-10v10M12 24h40"/>
                <path d="m22 40 7-7 6 6 5-5 8 8"/>
                <circle cx="23" cy="31" r="2"/>
            </svg>
        </span>
    </a>

    <div class="yk-home-event-card__body">
        <div class="yk-home-event-card__badges">
            @if ($programme->event_type)
                <span class="yk-home-event-card__type">{{ $programme->event_type }}</span>
            @endif
            @if ($programme->event_date)
                <span class="yk-home-event-card__status {{ $isPastProgramme ? 'is-past' : 'is-upcoming' }}">
                    {{ $isPastProgramme ? 'Past Programme' : 'Upcoming' }}
                </span>
            @endif
        </div>
        <h3 title="{{ $programme->event->heading }}">{{ $programme->event->heading }}</h3>

        @if (filled($programme->description))
            <p class="yk-home-event-card__description">{{ $programme->description }}</p>
        @endif

        @if ($programme->event_date || $programme->organizer_name)
            <dl class="yk-home-event-card__details">
                @if ($programme->event_date)
                    <div>
                        <dt>Programme Date</dt>
                        <dd>{{ $programme->event_date->format('j M Y') }}</dd>
                    </div>
                @endif
                @if ($programme->organizer_name)
                    <div>
                        <dt>Organizer</dt>
                        <dd>{{ $programme->organizer_name }}</dd>
                    </div>
                @endif
            </dl>
        @endif

        <div class="yk-home-event-card__footer">
            @if (is_numeric($programme->starting_fees) && (float) $programme->starting_fees >= 0)
                <span class="yk-home-event-card__price">{{ formatPrice($programme->starting_fees) }}</span>
            @else
                <span class="yk-home-event-card__price yk-home-event-card__price--empty"></span>
            @endif
            <a class="yk-home-event-card__button" href="{{ $programmeUrl }}">
                View Programme
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M4 12h15M13 6l6 6-6 6"/>
                </svg>
            </a>
        </div>
    </div>
</article>
