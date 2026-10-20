@props([
    'professional',
    'compact' => false,
])

@php
    $profile = $professional->relationLoaded('profile') ? $professional->profile : null;
    $professionalServices = $professional->relationLoaded('professionalServices')
        ? $professional->professionalServices
        : collect();
    $primaryService = $professionalServices->first();
    $serviceName = null;

    if ($primaryService) {
        if ($primaryService->relationLoaded('service') && $primaryService->service) {
            $serviceName = $primaryService->service->name;
        } elseif (filled($primaryService->service_name)) {
            $serviceName = $primaryService->service_name;
        }
    }

    $specialization = $profile?->specialization;
    if (is_array($specialization)) {
        $specialization = implode(', ', array_filter($specialization));
    }

    $displayName = filled($professional->name) ? $professional->name : 'Professional';
    $description = filled($profile?->bio)
        ? $profile->bio
        : ($primaryService && filled($primaryService->description) ? $primaryService->description : null);
    $photoUrl = asset('img/default-avatar.png');
    $photoPath = $profile?->photo;

    if (filled($photoPath) && filter_var($photoPath, FILTER_VALIDATE_URL)) {
        $parts = parse_url($photoPath);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');
        $urlPath = $parts['path'] ?? '';

        if (in_array($scheme, ['http', 'https'], true)) {
            if (in_array($host, ['localhost', '127.0.0.1'], true) && str_starts_with($urlPath, '/storage/')) {
                $photoPath = substr($urlPath, strlen('/storage/'));
            } else {
                $photoUrl = $photoPath;
                $photoPath = null;
            }
        } else {
            $photoPath = null;
        }
    }

    if (filled($photoPath)) {
        $photoPath = ltrim($photoPath, '/');
        $storagePath = str_starts_with($photoPath, 'storage/')
            ? substr($photoPath, strlen('storage/'))
            : $photoPath;

        if (is_file(public_path($photoPath))) {
            $photoUrl = asset($photoPath);
        } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            && is_file(public_path('storage/' . $storagePath))) {
            $photoUrl = asset('storage/' . $storagePath);
        }
    }

    $approvedReviews = $professional->relationLoaded('reviews')
        ? $professional->reviews->filter(fn ($review) => (bool) $review->is_approved && is_numeric($review->rating))
        : collect();
    $averageRating = $approvedReviews->isNotEmpty() ? $approvedReviews->avg('rating') : null;

    $startingPrice = $profile && is_numeric($profile->starting_price)
        ? (float) $profile->starting_price
        : null;
    $currencySymbol = function_exists('getCurrencySymbol') ? getCurrencySymbol() : null;
    $convertedPrice = $startingPrice !== null && function_exists('convertCurrency')
        ? convertCurrency($startingPrice)
        : null;

    $profileUrl = route('professionals.details', [
        'id' => $professional->id,
        'professional_name' => \Illuminate\Support\Str::slug($displayName),
    ]);
@endphp

<article {{ $attributes->class('h-full') }}>
    <x-tazen-v2.ui.card padding="{{ $compact ? 'sm' : 'md' }}" class="group flex h-full flex-col transition-shadow hover:shadow-popover">
        <div class="flex items-start gap-4">
            <img
                src="{{ $photoUrl }}"
                alt="{{ $displayName }} profile photo"
                width="80"
                height="80"
                loading="lazy"
                class="size-16 shrink-0 rounded-full border border-border bg-surface-muted object-cover sm:size-20"
            >

            <div class="min-w-0 flex-1">
                <h2 class="font-display {{ $compact ? 'text-base' : 'text-card-title' }} font-semibold text-brand-secondary">
                    {{ $displayName }}
                </h2>
                @if (filled($specialization) || filled($serviceName))
                    <p class="mt-1 text-sm font-medium text-brand-primary">
                        {{ $specialization ?: $serviceName }}
                    </p>
                @endif
            </div>
        </div>

        @if (filled($description))
            <p class="mt-4 line-clamp-3 text-sm leading-6 text-text-secondary">
                {{ \Illuminate\Support\Str::limit(trim(strip_tags($description)), 180) }}
            </p>
        @endif

        @if ($approvedReviews->isNotEmpty() || ($startingPrice !== null && $currencySymbol && $convertedPrice !== null))
            <dl class="mt-5 flex flex-wrap gap-x-6 gap-y-3 border-t border-divider pt-4 text-sm">
                @if ($approvedReviews->isNotEmpty())
                    <div>
                        <dt class="text-xs text-text-muted">Rating</dt>
                        <dd class="mt-1 font-semibold text-text-primary">
                            <span aria-label="{{ number_format($averageRating, 1) }} out of 5 stars">{{ number_format($averageRating, 1) }} / 5</span>
                            <span class="font-normal text-text-secondary">({{ $approvedReviews->count() }} {{ \Illuminate\Support\Str::plural('review', $approvedReviews->count()) }})</span>
                        </dd>
                    </div>
                @endif

                @if ($startingPrice !== null && $currencySymbol && $convertedPrice !== null)
                    <div>
                        <dt class="text-xs text-text-muted">Starting price</dt>
                        <dd class="mt-1 font-semibold text-text-primary">{{ $currencySymbol }}{{ number_format((float) $convertedPrice, 0) }} per session</dd>
                    </div>
                @endif
            </dl>
        @endif

        <x-slot:footer>
            <x-tazen-v2.ui.button href="{{ $profileUrl }}" variant="outline" size="sm" class="w-full">
                View profile
            </x-tazen-v2.ui.button>
        </x-slot:footer>
    </x-tazen-v2.ui.card>
</article>