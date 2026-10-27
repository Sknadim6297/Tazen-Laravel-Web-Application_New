@php
    $talkExperts = collect($featuredExperts ?? [])->take(6);
    $talkReviewStats = $talkExperts->isEmpty()
        ? collect()
        : \App\Models\Review::query()
            ->whereIn('professional_id', $talkExperts->pluck('id'))
            ->where('is_approved', 1)
            ->selectRaw('professional_id, COUNT(*) as review_count, AVG(rating) as average_rating')
            ->groupBy('professional_id')
            ->get()
            ->keyBy('professional_id');
    $talkTones = ['lavender', 'mint', 'blush', 'blue', 'cream', 'mint'];
@endphp

<section class="yk-talk" aria-labelledby="yk-talk-title">
    <div class="yk-talk__inner">
        <header class="yk-talk__head">
            <div class="yk-talk__copy">
                <p class="yk-talk__eyebrow">Talk to our experts</p>
                <h2 class="yk-talk__title" id="yk-talk-title">
                    <span class="yk-talk__heading-navy">Trusted guidance.</span>
                    <span class="yk-talk__heading-lavender">Brighter tomorrows.</span>
                </h2>
                <p class="yk-talk__lead">Connect with experienced experts for personalised advice and support.</p>
            </div>
            <a class="yk-talk__all" href="{{ route('professionals') }}">View All Experts <span aria-hidden="true">→</span></a>
            <svg class="yk-talk__leaves" viewBox="0 0 180 150" aria-hidden="true" focusable="false">
                <path d="M96 146c8-46 24-86 50-122" fill="none" stroke="#7d9a68" stroke-width="2.2" stroke-linecap="round"></path>
                <path d="M140 18c16 18 16 42 2 58-22-8-36-30-32-54 10-2 20-4 30-4Z" fill="#9d81db" opacity=".8"></path>
                <path d="M128 58c22 6 40 22 44 46-26 2-44-12-56-30 2-8 6-14 12-16Z" fill="#8aaf74"></path>
                <path d="M116 100c18 12 24 32 16 52-22-4-36-20-38-40 6-6 14-10 22-12Z" fill="#b7a4e4"></path>
            </svg>
        </header>

        @if ($talkExperts->isEmpty())
            <p class="yk-talk__empty">Approved experts will appear here once their profiles are public.</p>
        @else
            <ul class="yk-talk__grid">
                @foreach ($talkExperts as $expert)
                    @php
                        $displayName = trim($expert->profile?->name ?: $expert->name);
                        $nameParts = collect(preg_split('/\s+/', $displayName))->filter()->take(2);
                        $initials = $nameParts
                            ->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))
                            ->implode('');

                        $specialization = $expert->profile?->specialization;
                        $serviceNames = $expert->professionalServices
                            ->map(fn ($professionalService) => $professionalService->service?->name)
                            ->filter()
                            ->unique()
                            ->values();
                        if (!filled($specialization)) {
                            $specialization = $serviceNames->first();
                        }

                        $tags = $expert->professionalServices
                            ->flatMap(fn ($professionalService) => $professionalService->subServices->pluck('name'))
                            ->filter()
                            ->unique()
                            ->take(3)
                            ->values();
                        if ($tags->isEmpty()) {
                            $tags = $serviceNames
                                ->reject(fn ($name) => strcasecmp((string) $name, (string) $specialization) === 0)
                                ->take(3)
                                ->values();
                        }

                        $reviewStat = $talkReviewStats->get($expert->id);
                        $reviewCount = (int) ($reviewStat->review_count ?? 0);
                        $averageRating = $reviewCount > 0 ? number_format((float) $reviewStat->average_rating, 1) : null;

                        $photoPath = $expert->profile?->photo;
                        $photoNeedsReview = $photoPath === 'professional_documents/McPqTOLmpLHhukE6ka0DJmmNWTU3qG6segI57ccy.jpg'
                            || $photoPath === 'uploads/professionals/photo/media_68b52ad01436a.jpg';
                        $photoUrl = null;
                        if (filled($photoPath) && !$photoNeedsReview) {
                            $photoUrl = preg_match('/^https?:\/\//i', $photoPath)
                                ? $photoPath
                                : asset(str_starts_with($photoPath, 'storage/') ? $photoPath : 'storage/' . ltrim($photoPath, '/'));
                        }

                        $profileUrl = route('professionals.details', [
                            'id' => $expert->id,
                            'professional_name' => \Illuminate\Support\Str::slug($expert->name),
                        ]);
                        $tone = $talkTones[$loop->index % count($talkTones)];
                    @endphp
                    <li>
                        <article class="yk-talk__card yk-talk__card--{{ $tone }}">
                            <div class="yk-talk__portrait">
                                <svg class="yk-talk__botanical" viewBox="0 0 140 140" aria-hidden="true" focusable="false">
                                    <circle cx="70" cy="74" r="52" fill="rgba(255,255,255,0.55)"></circle>
                                    <path d="M18 78c16-28 34-36 48-22-8 18-22 28-48 22Z" fill="#8fb67c" opacity=".85"></path>
                                    <path d="M122 70c-14-26-34-30-46-14 10 16 24 24 46 14Z" fill="#b7a4e4" opacity=".8"></path>
                                    <path d="M28 108c18-6 28-2 34 10-16 2-28-2-34-10Z" fill="#7ea36a"></path>
                                    <path d="M112 104c-16-4-28 2-32 14 16 0 28-4 32-14Z" fill="#9d81db" opacity=".7"></path>
                                </svg>
                                @if ($photoUrl)
                                    <img class="yk-talk__photo" src="{{ $photoUrl }}" alt="{{ $displayName }}" width="160" height="160" loading="lazy" onerror="this.hidden=true; this.nextElementSibling.hidden=false;">
                                    <span class="yk-talk__initials" hidden aria-hidden="true">{{ $initials }}</span>
                                @else
                                    <span class="yk-talk__initials" aria-hidden="true">{{ $initials }}</span>
                                @endif
                            </div>
                            <h3 class="yk-talk__name">{{ $displayName }}</h3>
                            @if (filled($specialization))
                                <p class="yk-talk__role">{{ $specialization }}</p>
                            @endif
                            @if ($averageRating)
                                <p class="yk-talk__rating">
                                    <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="#e0a106" d="M8 1.4 9.8 5.8l4.7.4-3.6 3.1 1.1 4.6L8 11.6 3.9 14l1.1-4.6L1.5 6.2l4.7-.4Z"></path></svg>
                                    <span>{{ $averageRating }}</span>
                                    <span class="yk-talk__count">({{ $reviewCount }})</span>
                                </p>
                            @endif
                            @if ($tags->isNotEmpty())
                                <ul class="yk-talk__tags">
                                    @foreach ($tags as $tag)
                                        <li>{{ $tag }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <a class="yk-talk__book" href="{{ $profileUrl }}">Book Now <span aria-hidden="true">→</span></a>
                        </article>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
