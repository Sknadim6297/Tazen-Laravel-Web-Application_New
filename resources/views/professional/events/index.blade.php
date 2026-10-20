@extends('professional.layout.layout')

@section('title', 'My Events | Yuktione')

@section('content')
<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Events</p>
            <h1 class="mt-1 text-2xl font-semibold text-brand-950">My Events</h1>
            <p class="mt-1 text-sm text-text-muted">Create, organize and manage your professional events.</p>
        </div>
        <a href="{{ route('professional.events.create') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
            <span aria-hidden="true">+</span> Create event
        </a>
    </header>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Event summary">
        @foreach([
            ['label' => 'All events', 'value' => $statistics['total'], 'tone' => 'text-brand-800', 'bg' => 'bg-brand-50'],
            ['label' => 'Pending review', 'value' => $statistics['pending'], 'tone' => 'text-warning', 'bg' => 'bg-warning-surface'],
            ['label' => 'Approved', 'value' => $statistics['approved'], 'tone' => 'text-success', 'bg' => 'bg-success-surface'],
            ['label' => 'Needs attention', 'value' => $statistics['rejected'], 'tone' => 'text-error', 'bg' => 'bg-error-surface'],
        ] as $stat)
            <article class="rounded-panel border border-divider bg-white p-4 shadow-card">
                <p class="text-xs font-medium text-text-muted">{{ $stat['label'] }}</p>
                <p class="mt-2 text-2xl font-semibold {{ $stat['tone'] }}">{{ $stat['value'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card" aria-labelledby="events-heading">
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-divider px-4 py-4 sm:px-5">
            <div>
                <h2 id="events-heading" class="text-sm font-semibold text-brand-950">Your events</h2>
                <p class="mt-0.5 text-xs text-text-muted">Review event details, approval status and management options.</p>
            </div>
            <span class="text-xs text-text-muted">{{ $events->total() }} {{ \Illuminate\Support\Str::plural('event', $events->total()) }}</span>
        </header>

        @if($events->isEmpty())
            <div class="px-4 py-12 text-center sm:px-6">
                <div class="mx-auto grid size-12 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none"><rect x="3.75" y="5.75" width="16.5" height="14.5" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7.5 3.75v4M16.5 3.75v4M4 10h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-brand-950">No Events Yet</h3>
                <p class="mt-1 text-xs text-text-muted">Your professional events will appear here once created.</p>
                <a href="{{ route('professional.events.create') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Create your first event</a>
            </div>
        @else
            <div class="grid gap-4 p-4 sm:p-5 xl:grid-cols-2">
                @foreach($events as $event)
                    @php
                        $statusStyles = [
                            'approved' => 'bg-success-surface text-success ring-success/20',
                            'rejected' => 'bg-error-surface text-error ring-error/20',
                            'pending' => 'bg-warning-surface text-warning ring-warning/20',
                        ];
                        $statusStyle = $statusStyles[$event->status] ?? 'bg-surface-muted text-text-secondary ring-border';
                        $eventImageExists = $event->card_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($event->card_image);
                    @endphp
                    <article class="overflow-hidden rounded-panel border border-divider bg-white transition hover:shadow-card">
                        <div class="grid sm:grid-cols-[10rem_minmax(0,1fr)]">
                            <div class="aspect-[16/9] bg-brand-50 sm:aspect-auto sm:min-h-52">
                                @if($eventImageExists)
                                    <img src="{{ asset('storage/' . $event->card_image) }}" alt="{{ $event->heading }}" class="h-full w-full object-cover">
                                @else
                                    <div class="grid h-full min-h-36 place-items-center text-brand-700" role="img" aria-label="No event image available">
                                        <svg class="size-9" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><circle cx="8.5" cy="9" r="1.5" stroke="currentColor" stroke-width="1.5"/><path d="m4 17 5-5 3 3 3-4 5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex min-w-0 flex-col p-4">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-medium uppercase tracking-wide text-brand-700">{{ $event->mini_heading }}</p>
                                        <h3 class="mt-1 break-words text-base font-semibold leading-snug text-brand-950">{{ $event->heading }}</h3>
                                    </div>
                                    <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize ring-1 ring-inset {{ $statusStyle }}">{{ $event->status }}</span>
                                </div>
                                <p class="mt-2 line-clamp-2 text-xs leading-5 text-text-secondary">{{ $event->short_description }}</p>
                                <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
                                    <div>
                                        <dt class="text-text-muted">Date & time</dt>
                                        <dd class="mt-0.5 font-medium text-text-primary">
                                            {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M d, Y') : 'Not set' }}
                                            @if($event->time)<span class="text-text-muted"> · {{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}</span>@endif
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-text-muted">Starting fee</dt>
                                        <dd class="mt-0.5 font-medium text-text-primary">₹{{ number_format($event->starting_fees, 2) }}</dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-text-muted">Meeting link</dt>
                                        <dd class="mt-0.5">
                                            @if($event->meet_link)
                                                <a href="{{ $event->meet_link }}" target="_blank" rel="noopener noreferrer" class="font-medium text-brand-700 underline underline-offset-2">Join meeting</a>
                                            @else
                                                <span class="text-text-secondary">Not set</span>
                                            @endif
                                        </dd>
                                    </div>
                                </dl>
                                <div class="mt-auto flex flex-wrap gap-2 border-t border-divider pt-3">
                                    <a href="{{ route('professional.events.show', $event) }}" class="inline-flex min-h-9 items-center justify-center rounded-control bg-brand-50 px-3 text-xs font-semibold text-brand-800 hover:bg-brand-100">View event</a>
                                    @if($event->status !== 'approved')
                                        <a href="{{ route('professional.events.edit', $event) }}" class="inline-flex min-h-9 items-center justify-center rounded-control border border-border px-3 text-xs font-semibold text-text-secondary hover:bg-surface-muted">Edit</a>
                                        <form action="{{ route('professional.events.destroy', $event) }}" method="POST" data-confirm-submit data-confirm-title="Delete event?" data-confirm-message="This event will be permanently deleted.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-9 items-center justify-center rounded-control border border-error/20 px-3 text-xs font-semibold text-error hover:bg-error-surface">Delete</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('professional.event-bookings.index') }}" class="ml-auto inline-flex min-h-9 items-center justify-center px-2 text-xs font-semibold text-brand-700 hover:text-brand-900">Event bookings <span aria-hidden="true" class="ml-1">→</span></a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($events->hasPages())
                <div class="border-t border-divider px-4 py-3 sm:px-5">{{ $events->links() }}</div>
            @endif
        @endif
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
