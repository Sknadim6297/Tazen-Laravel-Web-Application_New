@extends('professional.layout.layout')

@section('title', $event->heading . ' | Yuktione')

@section('content')
@php
    $statusStyles = [
        'approved' => 'bg-success-surface text-success ring-success/20',
        'rejected' => 'bg-error-surface text-error ring-error/20',
        'pending' => 'bg-warning-surface text-warning ring-warning/20',
    ];
    $statusStyle = $statusStyles[$event->status] ?? 'bg-surface-muted text-text-secondary ring-border';
    $eventImageExists = $event->card_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->card_image);
@endphp
<div class="space-y-5">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">My events / Overview</p>
            <h1 class="mt-1 break-words text-2xl font-semibold text-brand-950">{{ $event->heading }}</h1>
            @if($event->mini_heading)<p class="mt-1 text-sm text-text-muted">{{ $event->mini_heading }}</p>@endif
        </div>
        <span class="inline-flex w-fit shrink-0 items-center rounded-full px-3 py-1.5 text-xs font-semibold capitalize ring-1 ring-inset {{ $statusStyle }}">{{ $event->status }}</span>
    </header>

    <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.8fr)]">
        <article class="overflow-hidden rounded-panel border border-divider bg-white shadow-card">
            <div class="aspect-[16/8] max-h-[28rem] bg-brand-50">
                @if($eventImageExists)
                    <img src="{{ asset('storage/' . $event->card_image) }}" alt="{{ $event->heading }}" class="h-full w-full object-cover">
                @else
                    <div class="grid h-full min-h-48 place-items-center text-brand-700" role="img" aria-label="No event image available">
                        <svg class="size-12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="8.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.5"/><path d="m4 17 5-5 3 3 3-4 5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                @endif
            </div>
            <div class="space-y-4 p-4 sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-brand-950">About this event</h2>
                    <p class="mt-2 whitespace-pre-line break-words text-sm leading-6 text-text-secondary">{{ $event->short_description }}</p>
                </div>
                @if($event->admin_notes)
                    <div class="rounded-control border border-warning/20 bg-warning-surface p-3 text-sm text-warning">
                        <h3 class="text-xs font-semibold">Admin notes</h3>
                        <p class="mt-1 whitespace-pre-line">{{ $event->admin_notes }}</p>
                    </div>
                @endif
            </div>
        </article>

        <aside class="space-y-4">
            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <h2 class="text-sm font-semibold text-brand-950">Event information</h2>
                <dl class="mt-3 divide-y divide-divider">
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-text-muted">Date & time</dt>
                        <dd class="text-right text-xs font-medium text-text-primary">
                            {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('l, M d, Y') : 'Not set' }}
                            @if($event->time)<span class="block mt-1 text-text-muted">{{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}</span>@endif
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-text-muted">Type</dt>
                        <dd class="text-right text-xs font-medium text-text-primary">{{ $event->mini_heading ?: '—' }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-text-muted">Starting fee</dt>
                        <dd class="text-right text-sm font-semibold text-brand-950">₹{{ number_format($event->starting_fees, 2) }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-text-muted">Meeting link</dt>
                        <dd class="text-right text-xs font-medium">
                            @if($event->meet_link)
                                <a href="{{ $event->meet_link }}" target="_blank" rel="noopener noreferrer" class="text-brand-700 underline underline-offset-2">Join meeting</a>
                            @else
                                <span class="text-text-secondary">Not set by admin</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-4 py-3">
                        <dt class="text-xs text-text-muted">Created</dt>
                        <dd class="text-right text-xs font-medium text-text-primary">{{ $event->created_at->format('M d, Y') }}</dd>
                    </div>
                    @if($event->approved_at)
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-xs text-text-muted">Approved</dt>
                            <dd class="text-right text-xs font-medium text-text-primary">{{ $event->approved_at->format('M d, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <h2 class="text-sm font-semibold text-brand-950">Manage event</h2>
                <div class="mt-3 grid gap-2">
                    <a href="{{ route('professional.events.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Back to events</a>
                    @if($event->status !== 'approved')
                        <a href="{{ route('professional.events.edit', $event) }}" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Edit event</a>
                        <form action="{{ route('professional.events.destroy', $event) }}" method="POST" data-confirm-submit data-confirm-title="Delete event?" data-confirm-message="This event will be permanently deleted.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full min-h-10 rounded-control border border-error/20 px-4 text-sm font-semibold text-error hover:bg-error-surface">Delete event</button>
                        </form>
                    @endif
                    @if($event->status === 'approved')
                        <a href="{{ route('event.details', $event->id) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-control border border-brand-200 bg-brand-50 px-4 text-sm font-semibold text-brand-800 hover:bg-brand-100">View public event</a>
                    @endif
                    <a href="{{ route('professional.event-details.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Event details</a>
                    <a href="{{ route('professional.event-bookings.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Event bookings</a>
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
