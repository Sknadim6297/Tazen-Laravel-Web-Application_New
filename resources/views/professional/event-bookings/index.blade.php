@extends('professional.layout.layout')

@section('title', 'Event Bookings | Yuktione')

@section('content')
@php
    $hasFilters = request()->hasAny(['search_name', 'search_date_from', 'search_date_to', 'payment_status', 'event_type']);
@endphp
<div class="space-y-5">
    <header>
        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Bookings & events</p>
        <h1 class="mt-1 text-2xl font-semibold text-brand-950">Event Bookings</h1>
        <p class="mt-1 text-sm text-text-muted">Review attendees and manage reservations for your events.</p>
    </header>

    <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5" aria-labelledby="booking-filter-title">
        <div class="mb-3">
            <h2 id="booking-filter-title" class="text-sm font-semibold text-brand-950">Search and filter</h2>
            <p class="mt-0.5 text-xs text-text-muted">Narrow your bookings by customer, date, payment status or event type.</p>
        </div>
        <form id="search-form" method="GET" action="{{ route('professional.event-bookings.index') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1.4fr)_repeat(4,minmax(0,1fr))_auto] xl:items-end">
            <div class="space-y-1.5">
                <label for="search_name" class="block text-xs font-medium text-text-primary">Customer or event</label>
                <input type="search" name="search_name" id="search_name" value="{{ request('search_name') }}" placeholder="Search name or event" class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
            </div>
            <div class="space-y-1.5">
                <label for="search_date_from" class="block text-xs font-medium text-text-primary">Event date from</label>
                <input type="date" name="search_date_from" id="search_date_from" value="{{ request('search_date_from') }}" class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
            </div>
            <div class="space-y-1.5">
                <label for="search_date_to" class="block text-xs font-medium text-text-primary">Event date to</label>
                <input type="date" name="search_date_to" id="search_date_to" value="{{ request('search_date_to') }}" class="h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
            </div>
            <div class="space-y-1.5">
                <label for="payment_status" class="block text-xs font-medium text-text-primary">Payment status</label>
                <select name="payment_status" id="payment_status" class="h-10 w-full rounded-control border border-border bg-white px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <option value="">All statuses</option>
                    @foreach($paymentStatuses as $status)
                        <option value="{{ $status }}" @selected(request('payment_status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="event_type" class="block text-xs font-medium text-text-primary">Event type</label>
                <select name="event_type" id="event_type" class="h-10 w-full rounded-control border border-border bg-white px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <option value="">All types</option>
                    @foreach($eventTypes as $type)
                        <option value="{{ $type }}" @selected(request('event_type') === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 sm:col-span-2 xl:col-span-1">
                <button type="submit" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Apply</button>
                <a href="{{ route('professional.event-bookings.index') }}" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Reset</a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card" aria-labelledby="bookings-list-title">
        <header class="flex flex-wrap items-center justify-between gap-2 border-b border-divider px-4 py-3.5 sm:px-5">
            <div>
                <h2 id="bookings-list-title" class="text-sm font-semibold text-brand-950">Reservations</h2>
                <p class="mt-0.5 text-xs text-text-muted">Your event attendees and their payment details.</p>
            </div>
            <span class="text-xs text-text-muted">{{ $bookings->total() }} {{ \Illuminate\Support\Str::plural('booking', $bookings->total()) }}</span>
        </header>

        @if($bookings->isEmpty())
            <div class="px-4 py-12 text-center sm:px-6">
                <div class="mx-auto grid size-12 place-items-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true">
                    <svg class="size-6" viewBox="0 0 24 24" fill="none"><rect x="3.75" y="5.75" width="16.5" height="14.5" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M7.5 3.75v4M16.5 3.75v4M4 10h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                @if($hasFilters)
                    <h3 class="mt-3 text-sm font-semibold text-brand-950">No matching bookings</h3>
                    <p class="mt-1 text-xs text-text-muted">Try adjusting your filters to see more results.</p>
                    <a href="{{ route('professional.event-bookings.index') }}" class="mt-4 inline-flex min-h-9 items-center rounded-control border border-border px-3 text-xs font-semibold text-brand-800 hover:bg-brand-50">Reset filters</a>
                @else
                    <h3 class="mt-3 text-sm font-semibold text-brand-950">No Event Bookings Yet</h3>
                    <p class="mt-1 text-xs text-text-muted">Reservations for your events will appear here.</p>
                @endif
            </div>
        @else
            @php
                $paymentStyle = [
                    'paid' => 'bg-success-surface text-success ring-success/20',
                    'success' => 'bg-success-surface text-success ring-success/20',
                    'pending' => 'bg-warning-surface text-warning ring-warning/20',
                    'failed' => 'bg-error-surface text-error ring-error/20',
                    'refunded' => 'bg-surface-muted text-text-secondary ring-border',
                ];
            @endphp
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-surface-muted text-[11px] uppercase tracking-wide text-text-muted">
                        <tr>
                            <th scope="col" class="px-4 py-3 font-semibold">Attendee</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Event</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Event date</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Location</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Seats</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Amount</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Payment</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Booked</th>
                            <th scope="col" class="px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($bookings as $booking)
                            @php
                                $currentPaymentStyle = $paymentStyle[strtolower($booking->payment_status ?? '')] ?? 'bg-surface-muted text-text-secondary ring-border';
                            @endphp
                            <tr class="align-top hover:bg-surface-muted/60">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-text-primary">{{ $booking->user->name ?? 'N/A' }}</p>
                                    <p class="mt-0.5 text-xs text-text-muted">{{ $booking->user->email ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-text-primary">{{ $booking->event_name }}</p>
                                    <p class="mt-0.5 text-xs text-text-muted">{{ ucfirst($booking->type ?? 'offline') }}</p>
                                </td>
                                <td class="px-4 py-3 text-xs text-text-secondary">{{ $booking->event_date ? \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-text-secondary">{{ $booking->location ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-text-secondary">{{ $booking->persons }} {{ \Illuminate\Support\Str::plural('person', $booking->persons) }}</td>
                                <td class="px-4 py-3 text-xs font-semibold text-text-primary">₹{{ number_format($booking->total_price, 2) }}</td>
                                <td class="px-4 py-3"><span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold capitalize ring-1 ring-inset {{ $currentPaymentStyle }}">{{ $booking->payment_status }}</span></td>
                                <td class="px-4 py-3 text-xs text-text-secondary">{{ $booking->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('professional.event-bookings.show', $booking->id) }}" class="inline-flex min-h-8 items-center rounded-control bg-brand-50 px-2.5 text-xs font-semibold text-brand-800 hover:bg-brand-100">View</a>
                                        @if(in_array($booking->payment_status, ['pending', 'failed']))
                                            <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Mark payment as paid?" data-confirm-message="Confirm that payment for this reservation has been received.">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="payment_status" value="paid">
                                                <button type="submit" class="inline-flex min-h-8 items-center rounded-control bg-success-surface px-2.5 text-xs font-semibold text-success hover:bg-green-100">Mark paid</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid gap-3 p-3 md:hidden">
                @foreach($bookings as $booking)
                    @php
                        $currentPaymentStyle = $paymentStyle[strtolower($booking->payment_status ?? '')] ?? 'bg-surface-muted text-text-secondary ring-border';
                    @endphp
                    <article class="rounded-control border border-divider p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-semibold text-brand-950">{{ $booking->event_name }}</h3>
                                <p class="mt-0.5 text-xs text-text-muted">{{ ucfirst($booking->type ?? 'offline') }}</p>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-semibold capitalize ring-1 ring-inset {{ $currentPaymentStyle }}">{{ $booking->payment_status }}</span>
                        </div>
                        <div class="mt-3 border-t border-divider pt-3">
                            <p class="text-xs font-medium text-text-primary">{{ $booking->user->name ?? 'N/A' }}</p>
                            <p class="break-all text-[11px] text-text-muted">{{ $booking->user->email ?? 'N/A' }}</p>
                            <dl class="mt-3 grid grid-cols-2 gap-3 text-xs">
                                <div><dt class="text-text-muted">Event date</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $booking->event_date ? \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') : 'N/A' }}</dd></div>
                                <div><dt class="text-text-muted">Attendees</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $booking->persons }} {{ \Illuminate\Support\Str::plural('person', $booking->persons) }}</dd></div>
                                <div><dt class="text-text-muted">Amount</dt><dd class="mt-0.5 font-medium text-text-primary">₹{{ number_format($booking->total_price, 2) }}</dd></div>
                                <div><dt class="text-text-muted">Booked</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $booking->created_at->format('M d, Y') }}</dd></div>
                                @if($booking->location)<div class="col-span-2"><dt class="text-text-muted">Location</dt><dd class="mt-0.5 font-medium text-text-primary">{{ $booking->location }}</dd></div>@endif
                            </dl>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 border-t border-divider pt-3">
                            <a href="{{ route('professional.event-bookings.show', $booking->id) }}" class="inline-flex min-h-9 items-center rounded-control bg-brand-50 px-3 text-xs font-semibold text-brand-800">View booking</a>
                            @if(in_array($booking->payment_status, ['pending', 'failed']))
                                <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Mark payment as paid?" data-confirm-message="Confirm that payment for this reservation has been received.">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_status" value="paid">
                                    <button type="submit" class="inline-flex min-h-9 items-center rounded-control bg-success-surface px-3 text-xs font-semibold text-success">Mark paid</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <footer class="flex flex-col gap-3 border-t border-divider px-4 py-3 text-xs text-text-muted sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p>Showing {{ $bookings->firstItem() ?? 0 }}–{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings</p>
                <div>{{ $bookings->links() }}</div>
            </footer>
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
        confirmText: 'Continue',
        cancelText: 'Cancel',
        variant: 'warning',
    });
    if (confirmed) form.submit();
});
</script>
@endsection
