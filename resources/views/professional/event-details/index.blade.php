@extends('professional.layout.layout')

@section('title', 'Event Details | Yuktione')

@section('content')
<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Event management</p>
            <h1 class="mt-1 text-2xl font-semibold text-brand-950">Event Details</h1>
            <p class="mt-1 text-sm text-text-muted">Complete and manage the information customers need before booking.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('professional.events.create') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Create event</a>
            @if($eventsWithoutDetails->isNotEmpty())
                <a href="{{ route('professional.event-details.create') }}" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Add event details</a>
            @endif
        </div>
    </header>

    @if($eventsWithoutDetails->isNotEmpty())
        <section class="overflow-hidden rounded-panel border border-warning/20 bg-white shadow-card" aria-labelledby="missing-details-heading">
            <header class="flex flex-wrap items-center justify-between gap-2 border-b border-warning/20 bg-warning-surface px-4 py-3.5 sm:px-5">
                <div>
                    <h2 id="missing-details-heading" class="text-sm font-semibold text-warning">Events without details</h2>
                    <p class="mt-0.5 text-xs text-warning">Add event information so customers can discover how to participate.</p>
                </div>
                <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-warning">{{ $eventsWithoutDetails->count() }} pending</span>
            </header>
            <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-5">
                @foreach($eventsWithoutDetails as $event)
                    <article class="flex flex-col rounded-control border border-divider p-4">
                        <h3 class="text-sm font-semibold text-brand-950">{{ $event->heading }}</h3>
                        <p class="mt-1 line-clamp-2 text-xs leading-5 text-text-muted">{{ $event->short_description }}</p>
                        <div class="mt-auto flex items-center justify-between gap-3 pt-4">
                            <span class="text-xs text-text-secondary">{{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M d, Y') : 'No date set' }}</span>
                            <a href="{{ route('professional.event-details.create', ['event_id' => $event->id]) }}" class="inline-flex min-h-9 items-center rounded-control bg-warning-surface px-3 text-xs font-semibold text-warning hover:bg-amber-100">Add details</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card" aria-labelledby="event-details-heading">
        <header class="flex flex-col gap-3 border-b border-divider px-4 py-4 sm:flex-row sm:items-end sm:justify-between sm:px-5">
            <div>
                <h2 id="event-details-heading" class="text-sm font-semibold text-brand-950">Your event details</h2>
                <p class="mt-0.5 text-xs text-text-muted">Manage schedules, pricing, event mode and customer-facing descriptions.</p>
            </div>
            @if($eventdetails->isNotEmpty())
                <label class="block w-full sm:max-w-xs">
                    <span class="sr-only">Search event details</span>
                    <input id="event-detail-search" type="search" placeholder="Search title, type, city..." class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                </label>
            @endif
        </header>

        @if($eventdetails->isEmpty())
            <div class="px-4 py-12 text-center sm:px-6">
                <div class="mx-auto grid size-12 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none"><rect x="3.75" y="5.75" width="16.5" height="14.5" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7.5 3.75v4M16.5 3.75v4M4 10h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-brand-950">No event details yet</h3>
                <p class="mt-1 text-xs text-text-muted">Create an event first, then add its detailed information.</p>
                <a href="{{ route('professional.events.create') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Create event</a>
            </div>
        @else
            <div id="event-detail-list" class="grid gap-4 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-5">
                @foreach($eventdetails as $detail)
                    @php
                        $bannerImages = $detail->banner_image;
                        if (is_string($bannerImages)) {
                            $decodedBannerImages = json_decode($bannerImages, true);
                            $bannerImages = is_array($decodedBannerImages) ? $decodedBannerImages : [];
                        }
                        $firstBanner = is_array($bannerImages) ? ($bannerImages[0] ?? null) : null;
                        $bannerExists = $firstBanner && \Illuminate\Support\Facades\Storage::disk('public')->exists($firstBanner);
                        $eventTitle = $detail->event->heading ?? 'Event';
                        $searchContent = \Illuminate\Support\Str::lower(implode(' ', [$eventTitle, $detail->event_type, $detail->city, $detail->event_mode]));
                    @endphp
                    <article data-event-detail-card data-search="{{ $searchContent }}" class="flex min-w-0 flex-col overflow-hidden rounded-panel border border-divider bg-white">
                        <div class="aspect-[16/8] bg-brand-50">
                            @if($bannerExists)
                                <img src="{{ asset('storage/' . $firstBanner) }}" alt="{{ $eventTitle }}" class="h-full w-full object-cover">
                            @else
                                <div class="grid h-full place-items-center text-brand-700" role="img" aria-label="No event banner available">
                                    <svg class="size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="8.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.5"/><path d="m4 17 5-5 3 3 3-4 5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-[11px] font-medium uppercase tracking-wide text-brand-700">{{ $detail->event_type }}</p>
                                    <h3 class="mt-1 break-words text-sm font-semibold text-brand-950">{{ $eventTitle }}</h3>
                                </div>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $detail->event_mode === 'online' ? 'bg-info-surface text-info' : 'bg-warning-surface text-warning' }}">{{ ucfirst($detail->event_mode ?? 'N/A') }}</span>
                            </div>
                            <dl class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                <div><dt class="text-text-muted">Starting date</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $detail->starting_date?->format('M d, Y') ?? 'N/A' }}</dd></div>
                                <div><dt class="text-text-muted">Starting fee</dt><dd class="mt-0.5 font-medium text-text-primary">₹{{ number_format($detail->starting_fees, 2) }}</dd></div>
                                @if($detail->city)<div class="col-span-2"><dt class="text-text-muted">City</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $detail->city }}</dd></div>@endif
                            </dl>
                            <div class="mt-auto flex flex-wrap gap-2 border-t border-divider pt-3 mt-4">
                                <a href="{{ route('professional.event-details.show', $detail) }}" class="inline-flex min-h-9 items-center justify-center rounded-control bg-brand-50 px-3 text-xs font-semibold text-brand-800 hover:bg-brand-100">View</a>
                                <a href="{{ route('professional.event-details.edit', $detail) }}" class="inline-flex min-h-9 items-center justify-center rounded-control border border-border px-3 text-xs font-semibold text-text-secondary hover:bg-surface-muted">Edit</a>
                                <form action="{{ route('professional.event-details.destroy', $detail) }}" method="POST" data-confirm-submit data-confirm-title="Delete event details?" data-confirm-message="The uploaded event detail images and details will be removed.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex min-h-9 items-center justify-center rounded-control border border-error/20 px-3 text-xs font-semibold text-error hover:bg-error-surface">Delete</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div id="event-detail-no-results" hidden class="px-4 py-10 text-center">
                <h3 class="text-sm font-semibold text-brand-950">No matching event details</h3>
                <p class="mt-1 text-xs text-text-muted">Try a different search term.</p>
            </div>
            <footer class="flex flex-col gap-3 border-t border-divider px-4 py-3 text-xs text-text-muted sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p id="event-detail-count" aria-live="polite"></p>
                <nav id="event-detail-pagination" class="flex flex-wrap items-center gap-1" aria-label="Event details pagination"></nav>
            </footer>
        @endif
    </section>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const searchInput = document.getElementById('event-detail-search');
    const cards = [...document.querySelectorAll('[data-event-detail-card]')];
    const count = document.getElementById('event-detail-count');
    const pagination = document.getElementById('event-detail-pagination');
    const noResults = document.getElementById('event-detail-no-results');
    if (!searchInput || !cards.length || !count || !pagination || !noResults) return;

    const pageSize = 10;
    let currentPage = 1;
    const filteredCards = () => {
        const term = searchInput.value.trim().toLocaleLowerCase();
        return cards.filter((card) => card.dataset.search.includes(term));
    };
    const render = () => {
        const matches = filteredCards();
        const pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
        currentPage = Math.min(currentPage, pageCount);
        const visible = new Set(matches.slice((currentPage - 1) * pageSize, currentPage * pageSize));
        cards.forEach((card) => { card.hidden = !visible.has(card); });
        noResults.hidden = matches.length > 0;
        count.textContent = matches.length
            ? `Showing ${(currentPage - 1) * pageSize + 1}–${Math.min(currentPage * pageSize, matches.length)} of ${matches.length} event details`
            : 'No event details found';

        pagination.replaceChildren();
        if (pageCount <= 1) return;
        for (let page = 1; page <= pageCount; page++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = String(page);
            button.setAttribute('aria-label', `Page ${page}`);
            button.setAttribute('aria-current', page === currentPage ? 'page' : 'false');
            button.className = `min-h-9 min-w-9 rounded-control px-2 text-xs font-semibold ${page === currentPage ? 'bg-brand-primary text-white' : 'border border-border text-text-secondary hover:bg-surface-muted'}`;
            button.addEventListener('click', () => { currentPage = page; render(); });
            pagination.append(button);
        }
    };
    searchInput.addEventListener('input', () => { currentPage = 1; render(); });
    render();
})();

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
