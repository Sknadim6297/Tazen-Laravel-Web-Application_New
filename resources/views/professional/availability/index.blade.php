@extends('professional.layout.layout')

@section('styles')
@vite('resources/css/tazen.css')
@endsection

@section('content')
<div class="content-wrapper w-full text-text-primary">
    <div class="availability-page mx-auto w-full max-w-7xl px-4 py-8 text-text-primary sm:px-6 lg:px-8">
        <div class="availability-shell mx-auto w-full space-y-6">
        <header class="availability-header flex flex-col gap-5 rounded-panel border border-border bg-gradient-to-br from-white to-brand-50 p-6 shadow-card sm:flex-row sm:items-center sm:justify-between">
            <div class="hero-meta">
                <span class="hero-eyebrow inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand-700"><i class="ri-calendar-line"></i>Availability</span>
                <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">My Availability</h1>
                <ul class="hero-breadcrumb mt-3 flex flex-wrap items-center gap-2 text-sm text-text-muted">
                    <li><a href="{{ route('professional.dashboard') }}">Home</a></li>
                    <li class="active" aria-current="page">Availability</li>
                </ul>
            </div>
            <a href="{{ route('professional.availability.create') }}" class="btn btn-primary inline-flex items-center justify-center gap-2 rounded-control bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-300">
                <i class="ri-add-line"></i>
                Add New Availability
            </a>
        </header>

        <section class="filter-card rounded-panel border border-border bg-white p-5 shadow-card">
            <form method="GET" action="{{ route('professional.availability.index') }}">
                <div>
                    <label for="search_month" class="block text-sm font-medium text-text-secondary">Search by Month</label>
                    <select name="search_month" id="search_month" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200">
                        <option value="">All Months</option>
                        @foreach($availableMonths as $month)
                            <option value="{{ $month }}" {{ request('search_month') == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-actions mt-4 flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-outline-primary inline-flex items-center justify-center gap-2 rounded-control border border-brand-600 bg-white px-4 py-2.5 text-sm font-semibold text-brand-800 transition hover:bg-brand-50">
                        <i class="ri-search-line"></i>Search
                    </button>
                    @if(request('search_month'))
                        <a href="{{ route('professional.availability.index') }}" class="btn btn-outline-secondary inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                            <i class="ri-refresh-line"></i>Clear
                        </a>
                    @endif
                </div>
            </form>
        </section>

        @if($availability->count() > 0)
            <section class="availability-grid grid gap-5 lg:grid-cols-2">
                @foreach($availability as $avail)
                    <article class="availability-card overflow-hidden rounded-panel border border-border bg-white shadow-card">
                        <div class="card-top border-b border-divider p-5">
                            <div class="card-meta flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="month-title">{{ \Carbon\Carbon::parse($avail->month . '-01')->format('F Y') }}</h2>
                                    <p class="month-info">Session Duration: {{ $avail->session_duration }} minutes</p>
                                </div>
                                <div class="card-actions flex flex-wrap gap-2">
                                    <a href="{{ route('professional.availability.edit', $avail->id) }}"
                                       class="btn btn-outline-primary inline-flex items-center justify-center gap-2 rounded-control border border-brand-600 bg-white px-4 py-2.5 text-sm font-semibold text-brand-800 transition hover:bg-brand-50" title="Edit Availability">
                                        <i class="ri-edit-line"></i>
                                        <span class="label-text">Edit</span>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger delete-availability inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted border-error/40 text-error hover:bg-error-surface"
                                            data-id="{{ $avail->id }}"
                                            data-month="{{ \Carbon\Carbon::parse($avail->month . '-01')->format('F Y') }}"
                                            title="Delete Availability">
                                        <i class="ri-delete-bin-line"></i>
                                        <span class="label-text">Delete</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-5">
                            @if($avail->slots && $avail->slots->count() > 0)
                                <div class="weekly-block space-y-4">
                                    <div class="weekly-title flex items-center justify-between font-semibold">
                                        <span>Weekly Schedule</span>
                                        <i class="ri-calendar-check-line"></i>
                                    </div>
                                    @php
                                        $weekdays = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
                                        $dayNames = [
                                            'mon' => 'Monday',
                                            'tue' => 'Tuesday',
                                            'wed' => 'Wednesday',
                                            'thu' => 'Thursday',
                                            'fri' => 'Friday',
                                            'sat' => 'Saturday',
                                            'sun' => 'Sunday'
                                        ];
                                        $slotsByDay = $avail->slots->groupBy('weekday');
                                    @endphp

                                    <div class="day-schedule space-y-3">
                                        @foreach($weekdays as $day)
                                            @if(isset($slotsByDay[$day]) && $slotsByDay[$day]->count() > 0)
                                                <div class="day-entry grid gap-2 border-b border-divider pb-3 last:border-0 sm:grid-cols-[8rem_1fr]">
                                                    <div class="day-name font-medium text-text-secondary">{{ $dayNames[$day] }}</div>
                                                    <div class="time-pill-group flex flex-wrap gap-2">
                                                        @foreach($slotsByDay[$day] as $slot)
                                                            <span class="time-pill rounded-full bg-brand-50 px-3 py-1 text-xs font-medium text-brand-800">
                                                                {{ \Carbon\Carbon::parse($slot->start_time)->format('g:i A') }} –
                                                                {{ \Carbon\Carbon::parse($slot->end_time)->format('g:i A') }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="no-slots rounded-control bg-surface-muted p-4 text-sm text-text-muted">No time slots configured for this month.</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </section>
        @else
            <section class="empty-state rounded-panel border border-dashed border-border bg-white p-8 text-center text-text-secondary">
                <i class="ri-calendar-line"></i>
                <h4>No Availability Set</h4>
                <p>You haven't configured your availability yet. Set up your schedule to start receiving bookings.</p>
                <a href="{{ route('professional.availability.create') }}" class="btn inline-flex items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">
                    <i class="ri-add-line"></i>
                    <span>Add Your First Availability</span>
                </a>
            </section>
        @endif
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete availability functionality
    document.querySelectorAll('.delete-availability').forEach(button => {
        button.addEventListener('click', function() {
            const availabilityId = this.getAttribute('data-id');
            const monthName = this.getAttribute('data-month');
            
            if (confirm(`Are you sure you want to delete availability for ${monthName}? This action cannot be undone.`)) {
                fetch(`{{ route('professional.availability.index') }}/${availabilityId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the card from DOM
                        this.closest('.availability-card').remove();
                        
                        // Show success message
                        alert('Availability deleted successfully!');
                        
                        // Reload page if no more cards
                        if (document.querySelectorAll('.availability-card').length === 0) {
                            window.location.reload();
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Failed to delete availability'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the availability');
                });
            }
        });
    });
});
</script>
@endsection
