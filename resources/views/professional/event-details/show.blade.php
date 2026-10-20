@extends('professional.layout.layout')

@section('title', 'Event Details | Yuktione')

@section('content')
@php
    $bannerImages = $eventDetail->banner_image;
    if (is_string($bannerImages)) {
        $decodedBannerImages = json_decode($bannerImages, true);
        $bannerImages = is_array($decodedBannerImages) ? $decodedBannerImages : [];
    }
    $galleryImages = $eventDetail->event_gallery;
    if (is_string($galleryImages)) {
        $decodedGalleryImages = json_decode($galleryImages, true);
        $galleryImages = is_array($decodedGalleryImages) ? $decodedGalleryImages : [];
    }
    $bannerImages = is_array($bannerImages)
        ? array_values(array_filter($bannerImages, fn ($image) => is_string($image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($image)))
        : [];
    $galleryImages = is_array($galleryImages)
        ? array_values(array_filter($galleryImages, fn ($image) => is_string($image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($image)))
        : [];
@endphp
<div class="space-y-5">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Event details / Overview</p>
            <h1 class="mt-1 break-words text-2xl font-semibold text-brand-950">{{ $eventDetail->event->heading ?? 'Event details' }}</h1>
            <p class="mt-1 text-sm text-text-muted">{{ $eventDetail->event_type }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('professional.event-details.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Back to details</a>
            <a href="{{ route('professional.event-details.edit', $eventDetail) }}" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Edit details</a>
        </div>
    </header>

    <section class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_minmax(17rem,0.8fr)]">
        <article class="space-y-4">
            @if(count($bannerImages))
                <div class="grid gap-3 {{ count($bannerImages) > 1 ? 'sm:grid-cols-2' : '' }}">
                    @foreach($bannerImages as $image)
                        @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($image))
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $eventDetail->event->heading ?? 'Event' }} banner" class="aspect-video w-full rounded-panel border border-divider object-cover shadow-card">
                        @endif
                    @endforeach
                </div>
            @else
                <div class="grid aspect-video place-items-center rounded-panel border border-divider bg-brand-50 text-brand-700" role="img" aria-label="No event banner available">
                    <svg class="size-12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="8.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.5"/><path d="m4 17 5-5 3 3 3-4 5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            @endif

            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <h2 class="text-sm font-semibold text-brand-950">About this event</h2>
                <div class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-text-secondary">{{ $eventDetail->event_details ?: 'No additional event information has been provided.' }}</div>
            </section>

            @if(count($galleryImages))
                <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                    <h2 class="text-sm font-semibold text-brand-950">Event gallery</h2>
                    <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach($galleryImages as $image)
                            @if(\Illuminate\Support\Facades\Storage::disk('public')->exists($image))
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $eventDetail->event->heading ?? 'Event' }} gallery image" class="aspect-[4/3] w-full rounded-control object-cover">
                            @endif
                        @endforeach
                    </div>
                </section>
            @endif
        </article>

        <aside class="space-y-4">
            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 class="text-sm font-semibold text-brand-950">Event overview</h2>
                    <span class="rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $eventDetail->event_mode === 'online' ? 'bg-info-surface text-info' : 'bg-warning-surface text-warning' }}">{{ ucfirst($eventDetail->event_mode ?? 'N/A') }}</span>
                </div>
                <dl class="mt-3 divide-y divide-divider">
                    <div class="py-3"><dt class="text-xs text-text-muted">Event type</dt><dd class="mt-1 text-sm font-medium text-text-primary">{{ $eventDetail->event_type }}</dd></div>
                    <div class="py-3"><dt class="text-xs text-text-muted">Starting date</dt><dd class="mt-1 text-sm font-medium text-text-primary">{{ $eventDetail->starting_date?->format('l, F j, Y') ?? 'Not scheduled' }}</dd></div>
                    @if($eventDetail->time)<div class="py-3"><dt class="text-xs text-text-muted">Time</dt><dd class="mt-1 text-sm font-medium text-text-primary">{{ \Carbon\Carbon::parse($eventDetail->time)->format('h:i A') }}</dd></div>@endif
                    <div class="py-3"><dt class="text-xs text-text-muted">Starting fee</dt><dd class="mt-1 text-lg font-semibold text-brand-950">₹{{ number_format($eventDetail->starting_fees, 2) }}</dd></div>
                    @if($eventDetail->city)<div class="py-3"><dt class="text-xs text-text-muted">City</dt><dd class="mt-1 text-sm font-medium text-text-primary">{{ $eventDetail->city }}</dd></div>@endif
                    <div class="py-3"><dt class="text-xs text-text-muted">Created</dt><dd class="mt-1 text-sm font-medium text-text-primary">{{ $eventDetail->created_at?->format('M d, Y') ?? 'N/A' }}</dd></div>
                </dl>
            </section>
            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <h2 class="text-sm font-semibold text-brand-950">Manage details</h2>
                <div class="mt-3 grid gap-2">
                    <a href="{{ route('professional.event-details.edit', $eventDetail) }}" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Edit event details</a>
                    <a href="{{ route('professional.event-details.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">All event details</a>
                    @if($eventDetail->event)
                        <a href="{{ route('professional.events.show', $eventDetail->event) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">View event</a>
                    @endif
                    <form action="{{ route('professional.event-details.destroy', $eventDetail) }}" method="POST" data-confirm-submit data-confirm-title="Delete event details?" data-confirm-message="The event detail record and its uploaded images will be deleted.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full min-h-10 rounded-control border border-error/20 px-4 text-sm font-semibold text-error hover:bg-error-surface">Delete event details</button>
                    </form>
                </div>
            </section>
        </aside>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('submit', async (event) => {
    const form = event.target.closest('[data-confirm-submit]');
    if (!form) return;
    event.preventDefault();
    if (!window.YuktioneDialog) {
        console.error('Yuktione confirmation dialog is unavailable.');
        window.YuktioneToast?.error('Unable to confirm this action. Please refresh and try again.');
        return;
    }
    const confirmed = await window.YuktioneDialog.confirm({
        title: form.dataset.confirmTitle || 'Are you sure?',
        message: form.dataset.confirmMessage || 'Please confirm this action.',
        confirmText: 'Delete',
        cancelText: 'Cancel',
        variant: 'danger',
    });
    if (confirmed) form.submit();
});
</script>
@endsection
