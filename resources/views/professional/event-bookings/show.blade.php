@extends('professional.layout.layout')

@section('title', 'Booking Details | Yuktione')

@section('content')
@php
    $bookingStatus = $booking->status ?? (in_array($booking->payment_status, ['success', 'paid']) ? 'confirmed' : 'pending');
    $bookingStatusStyle = [
        'confirmed' => 'bg-success-surface text-success ring-success/20',
        'completed' => 'bg-info-surface text-info ring-info/20',
        'cancelled' => 'bg-error-surface text-error ring-error/20',
        'pending' => 'bg-warning-surface text-warning ring-warning/20',
    ][$bookingStatus] ?? 'bg-surface-muted text-text-secondary ring-border';
    $paymentStatus = strtolower($booking->payment_status ?? 'unknown');
    $paymentStatusStyle = [
        'paid' => 'bg-success-surface text-success ring-success/20',
        'success' => 'bg-success-surface text-success ring-success/20',
        'pending' => 'bg-warning-surface text-warning ring-warning/20',
        'failed' => 'bg-error-surface text-error ring-error/20',
        'refunded' => 'bg-surface-muted text-text-secondary ring-border',
    ][$paymentStatus] ?? 'bg-surface-muted text-text-secondary ring-border';
@endphp
<div class="space-y-5">
    <header class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Event bookings / Booking #{{ $booking->id }}</p>
            <h1 class="mt-1 break-words text-2xl font-semibold text-brand-950">{{ $booking->event_name ?? $booking->event->heading ?? 'Event booking' }}</h1>
            <p class="mt-1 text-sm text-text-muted">Reservation by {{ $booking->user->name ?? 'Customer' }}</p>
        </div>
        <a href="{{ route('professional.event-bookings.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Back to bookings</a>
    </header>

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(17rem,0.65fr)]">
        <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card" aria-labelledby="booking-info-heading">
            <header class="border-b border-divider px-4 py-4 sm:px-5">
                <h2 id="booking-info-heading" class="text-sm font-semibold text-brand-950">Reservation information</h2>
                <p class="mt-0.5 text-xs text-text-muted">Attendee, event, schedule and payment details.</p>
            </header>
            <dl class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5">
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Attendee</dt>
                    <dd class="mt-1 break-words text-sm font-semibold text-brand-950">{{ $booking->user->name ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Email</dt>
                    <dd class="mt-1 break-all text-sm font-medium text-text-primary">{{ $booking->user->email ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Phone</dt>
                    <dd class="mt-1 text-sm font-medium text-text-primary">{{ $booking->phone ?? $booking->user->mobile ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Event</dt>
                    <dd class="mt-1 break-words text-sm font-medium text-text-primary">{{ $booking->event_name ?? $booking->event->heading ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Event date</dt>
                    <dd class="mt-1 text-sm font-medium text-text-primary">
                        @if($booking->event_date)
                            {{ \Carbon\Carbon::parse($booking->event_date)->format('F d, Y') }}
                        @elseif($booking->event?->date)
                            {{ \Carbon\Carbon::parse($booking->event->date)->format('F d, Y') }}
                        @else
                            N/A
                        @endif
                    </dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Event type</dt>
                    <dd class="mt-1 text-sm font-medium text-text-primary">{{ $booking->type ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Location</dt>
                    <dd class="mt-1 break-words text-sm font-medium text-text-primary">{{ $booking->location ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Attendees</dt>
                    <dd class="mt-1 text-sm font-medium text-text-primary">{{ $booking->persons ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Booking date</dt>
                    <dd class="mt-1 text-sm font-medium text-text-primary">{{ $booking->created_at?->format('F d, Y h:i A') ?? 'N/A' }}</dd>
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Payment status</dt>
                    <dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize ring-1 ring-inset {{ $paymentStatusStyle }}">{{ $booking->payment_status ?? 'Unknown' }}</span></dd>
                </div>
                <div class="rounded-control bg-brand-50 p-3">
                    <dt class="text-[11px] font-medium text-brand-800">Total amount</dt>
                    <dd class="mt-1 text-lg font-semibold text-brand-950">₹{{ number_format($booking->total_price ?? $booking->price ?? 0, 2) }}</dd>
                    @if($booking->price !== null && $booking->total_price !== null && $booking->price != $booking->total_price)
                        <p class="mt-0.5 text-[11px] text-text-muted">Base fee: ₹{{ number_format($booking->price, 2) }}</p>
                    @endif
                </div>
                <div class="rounded-control bg-surface-muted p-3">
                    <dt class="text-[11px] font-medium text-text-muted">Booking status</dt>
                    <dd class="mt-1"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize ring-1 ring-inset {{ $bookingStatusStyle }}">{{ $bookingStatus }}</span></dd>
                </div>
                @if($booking->event?->meet_link)
                    <div class="rounded-control bg-surface-muted p-3 sm:col-span-2">
                        <dt class="text-[11px] font-medium text-text-muted">Meeting link</dt>
                        <dd class="mt-1"><a href="{{ $booking->event->meet_link }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-brand-700 underline underline-offset-2">Open meeting <span aria-hidden="true">↗</span></a></dd>
                    </div>
                @endif
            </dl>
        </section>

        <aside class="space-y-4">
            <section class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                <h2 class="text-sm font-semibold text-brand-950">Manage booking</h2>
                <p class="mt-1 text-xs text-text-muted">Update the reservation status.</p>
                <div class="mt-4 grid gap-2">
                    @if($bookingStatus === 'pending')
                        <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Confirm this booking?" data-confirm-message="The customer will be notified of the booking update.">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="w-full min-h-10 rounded-control bg-success px-4 text-sm font-semibold text-white hover:bg-green-700">Confirm booking</button>
                        </form>
                        <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Cancel this booking?" data-confirm-message="Confirm that this reservation should be cancelled.">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="w-full min-h-10 rounded-control border border-error/20 px-4 text-sm font-semibold text-error hover:bg-error-surface">Cancel booking</button>
                        </form>
                    @elseif($bookingStatus === 'confirmed')
                        <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Complete this booking?" data-confirm-message="Confirm the event booking has been completed.">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full min-h-10 rounded-control bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800">Mark completed</button>
                        </form>
                        <form action="{{ route('professional.event-bookings.update-status', $booking->id) }}" method="POST" data-confirm-submit data-confirm-title="Cancel this booking?" data-confirm-message="Confirm that this reservation should be cancelled.">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="w-full min-h-10 rounded-control border border-error/20 px-4 text-sm font-semibold text-error hover:bg-error-surface">Cancel booking</button>
                        </form>
                    @elseif($bookingStatus === 'completed')
                        <p class="rounded-control bg-success-surface p-3 text-xs text-success" role="status">This booking is complete.</p>
                    @elseif($bookingStatus === 'cancelled')
                        <p class="rounded-control bg-error-surface p-3 text-xs text-error" role="status">This booking has been cancelled.</p>
                    @endif
                    @if($booking->user?->email)
                        <a href="mailto:{{ $booking->user->email }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Contact attendee</a>
                    @endif
                    @if($booking->phone ?? $booking->user?->mobile)
                        <a href="tel:{{ $booking->phone ?? $booking->user?->mobile }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Call attendee</a>
                    @endif
                    @if($booking->event)
                        <a href="{{ route('professional.events.show', $booking->event) }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 text-sm font-semibold text-text-secondary hover:bg-surface-muted">View event</a>
                    @endif
                </div>
            </section>
        </aside>
    </div>
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
        variant: form.querySelector('[name="status"]')?.value === 'cancelled' ? 'danger' : 'warning',
    });
    if (confirmed) form.submit();
});
</script>
@endsection
