@extends('customer.layout.layout')

@section('styles')
<style>
    .custom-modal {
        display: none;
    }

    .custom-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.375rem;
    }

    .custom-calendar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .custom-calendar-day-header,
    .custom-calendar-day {
        display: flex;
        min-height: 2.5rem;
        align-items: center;
        justify-content: center;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .custom-calendar-day-header {
        color: #526b84;
        font-weight: 600;
    }

    .custom-calendar-day {
        border: 1px solid #c9dfef;
    }

    .custom-calendar-nav,
    .custom-calendar-day.available {
        background: #eff9ff;
        color: #026fd8;
        cursor: pointer;
    }

    .custom-calendar-nav {
        min-height: 2.5rem;
        border: 1px solid #c9dfef;
        border-radius: 0.375rem;
        padding: 0 0.75rem;
    }

    .custom-calendar-month-year {
        color: #082b58;
        font-weight: 600;
    }

    .custom-calendar-day.available:hover,
    .custom-calendar-day.selected {
        background: #026fd8;
        color: #fff;
    }

    .custom-calendar-day.unavailable,
    .custom-calendar-day.past-date,
    .custom-calendar-day.other-month {
        color: #8a9bad;
    }

    .custom-time-slot-option {
        cursor: pointer;
        border: 1px solid #c9dfef;
        border-radius: 0.375rem;
        padding: 0.625rem 0.75rem;
    }

    .custom-time-slot-option:hover,
    .custom-time-slot-option.selected {
        border-color: #026fd8;
        background: #eff9ff;
        color: #026fd8;
    }

    .custom-time-slot-option.disabled {
        cursor: not-allowed;
        opacity: 0.55;
    }

    .custom-calendar-loading,
    .custom-loading-slots,
    .custom-no-slots-available {
        padding: 0.75rem;
        color: #526b84;
        font-size: 0.875rem;
        text-align: center;
    }

    .custom-calendar-error {
        padding: 0.75rem;
        color: #b42318;
        font-size: 0.875rem;
        text-align: center;
    }
</style>
@endsection

@section('content')
@php
    $hasActiveFilters = request()->filled('search_name')
        || (request()->filled('service') && request('service') !== 'all')
        || (request()->filled('plan_type') && request('plan_type') !== 'all')
        || request()->filled('search_date_from')
        || request()->filled('search_date_to');
@endphp

<div class="mx-auto grid max-w-7xl gap-6">
    <x-tazen-v2.panel.breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('user.dashboard')],
        ['label' => 'Upcoming Appointments'],
    ]" />

    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-brand-primary">Consultations</p>
            <h1 class="mt-1 font-display text-page-title font-semibold text-brand-secondary">Upcoming Appointments</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary sm:text-base">
                Manage your scheduled consultations and upcoming sessions.
            </p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-sm font-semibold text-brand-secondary">
            <x-tazen-v2.panel.icon name="clock" class="size-4 text-brand-primary" />
            {{ $bookings->count() }} {{ Str::plural('appointment', $bookings->count()) }}
        </span>
    </header>

    <section class="rounded-panel border border-border bg-surface p-5 shadow-card sm:p-6" aria-labelledby="appointment-filters-heading">
        <div class="mb-5">
            <h2 id="appointment-filters-heading" class="font-display text-card-title font-semibold text-brand-secondary">Filter Appointments</h2>
            <p class="mt-1 text-sm text-text-secondary">Search and narrow down your scheduled consultations.</p>
        </div>

        <form action="{{ route('user.upcoming-appointment.index') }}" method="GET" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="grid gap-1.5">
                <label for="search_name" class="text-sm font-semibold text-text-primary">Search appointment</label>
                <input type="search" name="search_name" id="search_name" value="{{ request('search_name') }}" placeholder="Professional, service or plan"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary placeholder:text-text-muted focus-visible:border-brand-primary">
            </div>

            <div class="grid gap-1.5">
                <label for="service" class="text-sm font-semibold text-text-primary">Service</label>
                <select name="service" id="service"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
                    <option value="all">All services</option>
                    @foreach($serviceOptions as $service)
                        <option value="{{ $service }}" @selected(request('service') == $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-1.5">
                <label for="plan_type" class="text-sm font-semibold text-text-primary">Plan type</label>
                <select name="plan_type" id="plan_type"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
                    <option value="all">All plans</option>
                    @foreach($planTypeOptions as $planType)
                        <option value="{{ $planType }}" @selected(request('plan_type') == $planType)>
                            {{ $formattedPlanTypes[$planType] ?? $planType }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-1.5">
                <label for="search_date_from" class="text-sm font-semibold text-text-primary">From date</label>
                <input type="date" name="search_date_from" id="search_date_from" value="{{ request('search_date_from') }}"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
            </div>

            <div class="grid gap-1.5">
                <label for="search_date_to" class="text-sm font-semibold text-text-primary">To date</label>
                <input type="date" name="search_date_to" id="search_date_to" value="{{ request('search_date_to') }}"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-control border border-brand-primary bg-brand-primary px-4 text-sm font-semibold text-white transition-colors hover:border-brand-800 hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    <x-tazen-v2.panel.icon name="search" class="size-4" />
                    Apply Filters
                </button>
                <a href="{{ route('user.upcoming-appointment.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary transition-colors hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-panel border border-border bg-surface shadow-card" aria-labelledby="appointment-results-heading">
        <div class="flex flex-col gap-1 border-b border-divider px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 id="appointment-results-heading" class="font-display text-card-title font-semibold text-brand-secondary">Your appointments</h2>
                <p class="mt-1 text-sm text-text-secondary">
                    {{ $bookings->count() }} {{ Str::plural('result', $bookings->count()) }}
                    <span class="text-text-muted">· Pending and confirmed future sessions</span>
                </p>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="p-5 sm:p-8">
                <x-tazen-v2.panel.empty-state
                    icon="calendar"
                    :title="$hasActiveFilters ? 'No appointments match these filters' : 'No Upcoming Appointments'"
                    :description="$hasActiveFilters
                        ? 'Try adjusting your search or clear the filters to see your scheduled consultations.'
                        : 'Your scheduled consultations will appear here once you book an appointment.'"
                >
                    <x-slot:actions>
                        @if($hasActiveFilters)
                            <a href="{{ route('user.upcoming-appointment.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary hover:bg-surface-muted">
                                Clear Filters
                            </a>
                        @else
                            <x-tazen-v2.ui.button href="{{ route('gridlisting') }}" size="sm">Explore Professionals</x-tazen-v2.ui.button>
                        @endif
                    </x-slot:actions>
                </x-tazen-v2.panel.empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1800px] table-auto text-left text-sm">
                    <thead class="bg-surface-muted text-xs uppercase tracking-wide text-text-secondary">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">S.No</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Date</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Month</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Day</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Time</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Professional Name</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Service Category</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Sub-Service</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Plan Type</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Sessions Taken</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Sessions Remaining</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Meet Link</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Upload Document</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Professional Document</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-4 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider text-text-primary">
                        @foreach($bookings as $index => $booking)
                            @php
                                $upcomingTimedate = $booking->timedates->first();
                                $rescheduleEnabled = $booking->reschedule_count < $booking->max_reschedules_allowed;
                                $remainingReschedules = $booking->max_reschedules_allowed - $booking->reschedule_count;
                            @endphp

                            @if($upcomingTimedate)
                                <tr class="transition-colors hover:bg-surface-muted/70">
                                    <td class="whitespace-nowrap px-4 py-4 text-text-secondary">{{ $index + 1 }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($upcomingTimedate->date)->format('M d, Y') }}</p>
                                        <p class="mt-1 text-xs text-text-muted">{{ \Carbon\Carbon::parse($upcomingTimedate->date)->format('l') }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ \Carbon\Carbon::parse($upcomingTimedate->date)->format('F') }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ \Carbon\Carbon::parse($upcomingTimedate->date)->format('D') }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 font-medium">{{ $upcomingTimedate->time_slot }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 font-semibold">{{ $booking->professional->name ?? 'Not Assigned' }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $booking->service_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $booking->sub_service_name ?: '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        @if($booking->plan_type)
                                            <span class="inline-flex rounded-full border border-brand-200 bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-secondary">
                                                {{ $booking->formatted_plan_type }}
                                            </span>
                                        @else
                                            <span class="text-text-muted">No plan</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $booking->sessions_taken ?? 0 }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">{{ $booking->sessions_remaining ?? 0 }}</td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        @if($upcomingTimedate->meeting_link)
                                            <a href="{{ $upcomingTimedate->meeting_link }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-2 rounded-control border border-brand-200 bg-brand-50 px-3 text-sm font-semibold text-brand-primary hover:bg-brand-100">
                                                <span aria-hidden="true">↗</span> Join
                                            </a>
                                        @else
                                            <span class="text-text-muted">No link</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($booking->customer_document)
                                                <a href="{{ asset('storage/' . $booking->customer_document) }}" target="_blank" rel="noopener noreferrer" title="View your uploaded document" aria-label="View your uploaded document" class="inline-flex size-9 items-center justify-center rounded-control border border-border bg-surface text-text-secondary hover:bg-surface-muted">
                                                    <x-tazen-v2.panel.icon name="inbox" class="size-4" />
                                                </a>
                                            @endif
                                            <button type="button" class="upload-btn inline-flex min-h-9 items-center gap-2 rounded-control border border-brand-primary px-3 text-xs font-semibold {{ $booking->customer_document ? 'bg-surface text-brand-primary hover:bg-brand-50' : 'bg-brand-primary text-white hover:bg-brand-800' }}"
                                                data-booking-id="{{ $booking->id }}" title="{{ $booking->customer_document ? 'Update document' : 'Upload document' }}">
                                                {{ $booking->customer_document ? 'Update' : 'Upload' }}
                                            </button>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        @if($booking->professional_documents)
                                            <div class="flex items-center gap-2">
                                                @foreach(explode(',', $booking->professional_documents) as $document)
                                                    <a href="{{ asset('storage/' . $document) }}" target="_blank" rel="noopener noreferrer" title="View professional document" aria-label="View professional document" class="inline-flex size-9 items-center justify-center rounded-control border border-border bg-surface text-text-secondary hover:bg-surface-muted">
                                                        <x-tazen-v2.panel.icon name="inbox" class="size-4" />
                                                    </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-text-muted">No documents</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        @if($rescheduleEnabled)
                                            <button type="button" class="reschedule-btn inline-flex min-h-9 items-center gap-2 rounded-control border border-border bg-surface px-3 text-xs font-semibold text-text-primary hover:bg-surface-muted"
                                                data-booking-id="{{ $booking->id }}" title="Reschedule appointment ({{ $remainingReschedules }} remaining)">
                                                <x-tazen-v2.panel.icon name="calendar" class="size-4 text-brand-primary" />
                                                Reschedule
                                            </button>
                                        @else
                                            <span class="text-xs text-text-muted" title="Maximum reschedules reached">Limit reached</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<!-- Custom Upload Modal -->
<div id="uploadModal" class="custom-modal fixed inset-0 z-50 overflow-y-auto bg-brand-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="upload-modal-title">
    <div class="custom-modal-content mx-auto my-6 w-full max-w-xl overflow-hidden rounded-panel border border-border bg-surface shadow-popover">
        <div class="flex items-center justify-between border-b border-divider px-5 py-4">
            <h2 id="upload-modal-title" class="font-display text-card-title font-semibold text-brand-secondary">Upload Document</h2>
            <button type="button" class="inline-flex size-10 items-center justify-center rounded-control text-text-secondary hover:bg-surface-muted" onclick="closeModal('uploadModal')" aria-label="Close upload dialog">&times;</button>
        </div>
        <div class="p-5">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="booking_id" id="bookingId">
                <div class="grid gap-2">
                    <label for="document" class="text-sm font-semibold text-text-primary">Select document</label>
                    <input type="file" class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary file:mr-3 file:rounded file:border-0 file:bg-brand-50 file:px-3 file:py-1 file:font-semibold file:text-brand-primary focus-visible:border-brand-primary" id="document" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                    <p class="text-xs text-text-muted">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 2MB)</p>
                </div>
                <div id="filePreview" class="mt-4 rounded-control border border-divider bg-surface-muted p-3" style="display: none">
                    <p class="text-xs font-semibold uppercase text-text-muted">Selected file</p>
                    <p class="mt-1 break-all text-sm text-text-primary" id="selectedFileName"></p>
                </div>
            </form>
        </div>
        <div class="flex justify-end gap-2 border-t border-divider px-5 py-4">
            <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary hover:bg-surface-muted" onclick="closeModal('uploadModal')">Cancel</button>
            <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-control border border-brand-primary bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800" id="uploadBtn">Upload</button>
        </div>
    </div>
</div>

<!-- Custom Reschedule Modal -->
<div id="rescheduleModal" class="custom-modal fixed inset-0 z-50 overflow-y-auto bg-brand-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="reschedule-modal-title">
    <div class="custom-modal-content mx-auto my-6 w-full max-w-2xl overflow-hidden rounded-panel border border-border bg-surface shadow-popover">
        <div class="flex items-center justify-between border-b border-divider px-5 py-4">
            <h2 id="reschedule-modal-title" class="font-display text-card-title font-semibold text-brand-secondary">Reschedule Appointment</h2>
            <button type="button" class="inline-flex size-10 items-center justify-center rounded-control text-text-secondary hover:bg-surface-muted" onclick="closeModal('rescheduleModal')" aria-label="Close reschedule dialog">&times;</button>
        </div>
        <div class="max-h-[75vh] overflow-y-auto p-5">
            <form id="rescheduleForm" class="grid gap-5">
                @csrf
                <input type="hidden" name="booking_id" id="rescheduleBookingId">
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-text-primary">Current appointment</label>
                    <div class="rounded-control border border-info bg-info-surface p-3 text-sm text-text-secondary">
                        <div id="currentAppointmentInfo"></div>
                    </div>
                </div>
                
                <div>
                    <label for="rescheduleCalendar" class="mb-2 block text-sm font-semibold text-text-primary">Select new date</label>
                    <div id="rescheduleCalendar" class="rounded-control border border-divider p-3"></div>
                    <input type="hidden" id="selectedDate" name="new_date" required>
                    <p class="mt-2 text-xs text-text-muted">
                        <span class="text-success">● Available dates</span> ·
                        <span class="text-text-muted">● Unavailable dates</span>
                    </p>
                </div>
                
                <div>
                    <label class="mb-2 block text-sm font-semibold text-text-primary">Available time slots</label>
                    <div id="rescheduleTimeSlots" class="grid gap-2 sm:grid-cols-2">
                        <div class="rounded-control bg-surface-muted p-3 text-center text-sm text-text-muted">Please select a date first</div>
                    </div>
                </div>
                
                <div class="grid gap-1.5">
                    <label for="rescheduleReason" class="text-sm font-semibold text-text-primary">Reason for reschedule <span class="text-error">*</span></label>
                    <textarea class="w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary placeholder:text-text-muted focus-visible:border-brand-primary" id="rescheduleReason" name="reschedule_reason" rows="3" required placeholder="Please provide a reason for rescheduling..."></textarea>
                </div>
                
                <div id="rescheduleInfo" class="rounded-control border border-warning bg-warning-surface p-3 text-sm text-warning" style="display: none">
                    <i class="fas fa-info-circle"></i> <span id="rescheduleInfoText"></span>
                </div>
            </form>
        </div>
        <div class="flex justify-end gap-2 border-t border-divider px-5 py-4">
            <button type="button" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary hover:bg-surface-muted" onclick="closeModal('rescheduleModal')">Cancel</button>
            <button type="button" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-control border border-brand-primary bg-brand-primary px-4 text-sm font-semibold text-white hover:bg-brand-800 disabled:cursor-not-allowed disabled:opacity-60" id="rescheduleBtn" disabled>
                <i class="fas fa-calendar-alt"></i> Reschedule Appointment
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    const fileInput = document.getElementById('document');
    const filePreview = document.getElementById('filePreview');
    const selectedFileName = document.getElementById('selectedFileName');
    const modal = document.getElementById('uploadModal');

    // Modal functions
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'block';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
        document.body.style.overflow = '';
    };

    // Close modal when clicking outside for all modals
    document.querySelectorAll('.custom-modal').forEach(modal => {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Prevent modal from closing when clicking inside the modal content for all modals
    document.querySelectorAll('.custom-modal-content').forEach(content => {
        content.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });

    // Handle ESC key to close modals
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.custom-modal.show').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });

    // Handle file input change
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'image/jpg'
            ];
            if (!allowedTypes.includes(file.type)) {
                toastr.error('Invalid file type. Allowed: PDF, DOC, DOCX, JPG, JPEG, PNG.');
                this.value = '';
                filePreview.style.display = 'none';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                toastr.error('File size exceeds 2MB.');
                this.value = '';
                filePreview.style.display = 'none';
                return;
            }
            selectedFileName.textContent = file.name;
            filePreview.style.display = 'block';
        } else {
            filePreview.style.display = 'none';
        }
    });

    // Handle upload button click
    document.querySelectorAll('.upload-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('bookingId').value = this.dataset.bookingId;
            filePreview.style.display = 'none';
            uploadForm.reset();
            openModal('uploadModal');
        });
    });

    // Handle file upload
    uploadBtn.addEventListener('click', function() {
        const formData = new FormData(uploadForm);

        // Disable the upload button and show loading state
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="size-4 animate-spin rounded-full border-2 border-current border-r-transparent" role="status" aria-hidden="true"></span> Uploading...';

        fetch('{{ route("user.upload.document") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                if (data.errors) {
                    // Show all validation errors
                    Object.values(data.errors).forEach(msgArr => {
                        if (Array.isArray(msgArr)) {
                            msgArr.forEach(msg => toastr.error(msg));
                        } else {
                            toastr.error(msgArr);
                        }
                    });
                } else {
                    toastr.error(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Error uploading document');
        })
        .finally(() => {
            // Re-enable the upload button and restore original text
            uploadBtn.disabled = false;
            uploadBtn.innerHTML = 'Upload';
        });
    });

    // Reschedule functionality
    let selectedTimeSlot = null;
    let currentBookingData = null;
    let availableDates = [];
    let currentCalendarDate = new Date();
    let professionalAvailabilityData = [];
    let existingBookings = {};

    // Handle reschedule button click
    document.querySelectorAll('.reschedule-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.bookingId;
            openRescheduleModal(bookingId);
        });
    });

    // Open reschedule modal
    function openRescheduleModal(bookingId) {
        // Find booking data from the current page
        const row = document.querySelector(`[data-booking-id="${bookingId}"]`).closest('tr');
        const cells = row.querySelectorAll('td');
        
        // Extract current appointment info
        const currentDate = cells[1].textContent.trim();
        const currentTime = cells[4].textContent.trim();
        const professionalName = cells[5].textContent.trim();
        const serviceName = cells[6].textContent.trim();
        
        currentBookingData = {
            id: bookingId,
            currentDate: currentDate,
            currentTime: currentTime,
            professionalName: professionalName,
            serviceName: serviceName
        };

        // Set booking ID in form
        document.getElementById('rescheduleBookingId').value = bookingId;

        // Set current appointment info
        document.getElementById('currentAppointmentInfo').innerHTML = `
            <strong>Service:</strong> ${serviceName}<br>
            <strong>Professional:</strong> ${professionalName}<br>
            <strong>Current Date:</strong> ${currentDate}<br>
            <strong>Current Time:</strong> ${currentTime}
        `;

        // Reset form
        document.getElementById('rescheduleForm').reset();
        document.getElementById('rescheduleBookingId').value = bookingId; // Set again after reset
        document.getElementById('rescheduleTimeSlots').innerHTML = '<div class="rounded-control bg-surface-muted p-3 text-center text-sm text-text-muted">Please select a date first</div>';
        document.getElementById('rescheduleBtn').disabled = true;
        selectedTimeSlot = null;
        currentCalendarDate = new Date();

        // Load professional availability and render calendar
        loadProfessionalAvailability(bookingId);

        openModal('rescheduleModal');
    }

    // Load professional availability data
    function loadProfessionalAvailability(bookingId) {
        const calendarContainer = document.getElementById('rescheduleCalendar');
        calendarContainer.innerHTML = '<div class="custom-calendar-loading">Loading calendar...</div>';

        console.log('Loading availability for booking ID:', bookingId);

        fetch(`{{ url('/user/upcoming-appointment') }}/${bookingId}/professional-availability`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Received availability data:', data);
                if (data.success) {
                    professionalAvailabilityData = data.data;
                    existingBookings = data.existing_bookings || {};
                    console.log('Professional availability data:', professionalAvailabilityData);
                    console.log('Existing bookings:', existingBookings);
                    generateAvailableDates();
                    renderCalendar();
                } else {
                    console.error('API returned error:', data.message);
                    calendarContainer.innerHTML = `<div class="custom-calendar-error">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error loading professional availability:', error);
                calendarContainer.innerHTML = '<div class="custom-calendar-error">Error loading calendar. Please try again.</div>';
            });
    }

    // Generate available dates from professional availability
    function generateAvailableDates() {
        availableDates = [];
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        professionalAvailabilityData.forEach(availability => {
            const monthYear = availability.month; // Format: "2025-11"
            const [year, month] = monthYear.split('-').map(Number);
            const weeklySlots = availability.weekly_slots;

            // Generate dates for the entire month
            const daysInMonth = new Date(year, month, 0).getDate();
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month - 1, day);
                
                // Skip past dates (only allow future dates)
                if (date < today) continue;

                // Get weekday name
                const weekdayMap = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
                const weekday = weekdayMap[date.getDay()];

                // Check if professional has slots for this weekday
                if (weeklySlots && weeklySlots[weekday] && weeklySlots[weekday].length > 0) {
                    availableDates.push(date.toISOString().split('T')[0]);
                }
            }
        });
        
        console.log('Generated available dates:', availableDates);
    }

    // Render calendar
    function renderCalendar() {
        const calendarContainer = document.getElementById('rescheduleCalendar');
        const year = currentCalendarDate.getFullYear();
        const month = currentCalendarDate.getMonth();

        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        // Calculate first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        let calendarHtml = `
            <div class="custom-calendar-header">
                <button type="button" class="custom-calendar-nav" onclick="navigateMonth(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="custom-calendar-month-year">
                    ${monthNames[month]} ${year}
                </div>
                <button type="button" class="custom-calendar-nav" onclick="navigateMonth(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="custom-calendar-grid">
        `;

        // Add day headers
        dayNames.forEach(day => {
            calendarHtml += `<div class="custom-calendar-day-header">${day}</div>`;
        });

        // Add empty cells for days before month starts
        for (let i = firstDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            calendarHtml += `<div class="custom-calendar-day other-month">${day}</div>`;
        }

        // Add days of current month
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateString = date.toISOString().split('T')[0];
            
            let dayClass = 'custom-calendar-day';
            let clickHandler = '';

            if (date < today) {
                dayClass += ' past-date';
            } else if (availableDates.includes(dateString)) {
                dayClass += ' available';
                clickHandler = `onclick="selectCalendarDate('${dateString}')"`;
            } else {
                dayClass += ' unavailable';
            }

            calendarHtml += `<div class="${dayClass}" ${clickHandler}>${day}</div>`;
        }

        // Add empty cells for days after month ends
        const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
        const remainingCells = totalCells - (firstDay + daysInMonth);
        for (let day = 1; day <= remainingCells; day++) {
            calendarHtml += `<div class="custom-calendar-day other-month">${day}</div>`;
        }

        calendarHtml += '</div>';
        calendarContainer.innerHTML = calendarHtml;
    }

    // Navigate calendar months
    window.navigateMonth = function(direction) {
        const today = new Date();
        const newDate = new Date(currentCalendarDate);
        newDate.setMonth(newDate.getMonth() + direction);
        
        // Allow navigation from current month to 6 months ahead
        const maxDate = new Date(today);
        maxDate.setMonth(maxDate.getMonth() + 6);
        
        // Don't allow navigation to past months or more than 6 months ahead
        if (newDate < new Date(today.getFullYear(), today.getMonth(), 1) || newDate > maxDate) {
            return;
        }
        
        currentCalendarDate = newDate;
        renderCalendar();
    };

    // Select calendar date
    window.selectCalendarDate = function(dateString) {
        // Remove previous selection
        document.querySelectorAll('.custom-calendar-day').forEach(day => {
            day.classList.remove('selected');
        });

        // Select current date
        event.target.classList.add('selected');
        document.getElementById('selectedDate').value = dateString;

        // Load time slots for selected date
        loadAvailableTimeSlots(currentBookingData.id, dateString);
    };

    // Load available time slots for selected date
    function loadAvailableTimeSlots(bookingId, date) {
        const slotsContainer = document.getElementById('rescheduleTimeSlots');
        slotsContainer.innerHTML = '<div class="custom-loading-slots">Loading available time slots...</div>';

        displayTimeSlots(professionalAvailabilityData, date);
    }

    // Display time slots
    function displayTimeSlots(availabilityData, selectedDate) {
        const slotsContainer = document.getElementById('rescheduleTimeSlots');
        const dateObj = new Date(selectedDate);
        
        // Get day name (convert to lowercase 3-letter format expected by backend)
        const dayNames = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
        const dayName = dayNames[dateObj.getDay()];
        
        let availableSlots = [];
        let bookedSlots = existingBookings[selectedDate] || [];
        
        console.log('=== TIME SLOT GENERATION DEBUG ===');
        console.log('Selected date:', selectedDate);
        console.log('Date object:', dateObj);
        console.log('Day name:', dayName);
        console.log('Availability data:', availabilityData);
        console.log('Booked slots for this date:', bookedSlots);
        
        // Find slots for the selected day
        availabilityData.forEach((availability, index) => {
            console.log(`Processing availability ${index}:`, availability);
            
            if (availability.weekly_slots && availability.weekly_slots[dayName]) {
                console.log(`Found slots for ${dayName}:`, availability.weekly_slots[dayName]);
                
                availability.weekly_slots[dayName].forEach((slot, slotIndex) => {
                    console.log(`Processing slot ${slotIndex}:`, slot);
                    
                    // Generate time slots based on session duration
                    const sessionDuration = availability.session_duration || 60;
                    const startTime = slot.start_time;
                    const endTime = slot.end_time;
                    
                    console.log(`Slot details - Start: ${startTime}, End: ${endTime}, Duration: ${sessionDuration} mins`);
                    
                    const start = new Date(`2023-01-01 ${startTime}`);
                    const end = new Date(`2023-01-01 ${endTime}`);
                    
                    console.log('Start date object:', start);
                    console.log('End date object:', end);
                    
                    let slotCounter = 0;
                    while (start < end && slotCounter < 20) { // Safety limit
                        const slotStart = start.toTimeString().substr(0, 5);
                        const slotEnd = new Date(start.getTime() + sessionDuration * 60000).toTimeString().substr(0, 5);
                        
                        console.log(`Generated slot ${slotCounter}: ${slotStart} - ${slotEnd}`);
                        
                        // Skip if end time exceeds availability end time
                        if (new Date(`2023-01-01 ${slotEnd}`) > end) {
                            console.log('Slot end time exceeds availability, breaking');
                            break;
                        }
                        
                        // Convert to 12-hour format
                        const slotStart12 = formatTo12Hour(slotStart);
                        const slotEnd12 = formatTo12Hour(slotEnd);
                        const timeSlot = `${slotStart12} - ${slotEnd12}`;
                        
                        console.log(`Formatted slot: ${timeSlot}`);
                        
                        availableSlots.push(timeSlot);
                        start.setMinutes(start.getMinutes() + sessionDuration);
                        slotCounter++;
                    }
                });
            } else {
                console.log(`No slots found for ${dayName} in availability:`, availability.weekly_slots);
            }
        });

        console.log('Final generated available slots:', availableSlots);

        if (availableSlots.length === 0) {
            console.log('No available slots found, showing message');
            slotsContainer.innerHTML = '<div class="custom-no-slots-available">No available time slots for this date</div>';
            return;
        }

        // Remove duplicates and sort
        availableSlots = [...new Set(availableSlots)];
        console.log('After removing duplicates:', availableSlots);
        
        availableSlots.sort((a, b) => {
            const timeA = a.split(' - ')[0];
            const timeB = b.split(' - ')[0];
            return convertTo24Hour(timeA).localeCompare(convertTo24Hour(timeB));
        });

        console.log('After sorting:', availableSlots);

        // Display slots
        let slotsHtml = '';
        availableSlots.forEach(slot => {
            const isBooked = bookedSlots.includes(slot);
            const slotClass = isBooked ? 'custom-time-slot-option disabled' : 'custom-time-slot-option';
            const clickHandler = isBooked ? '' : `onclick="selectTimeSlot('${slot}')"`;
            const statusText = isBooked ? '<small class="mt-1 block text-error">Booked</small>' : '';
            
            slotsHtml += `
                <div class="${slotClass}" ${clickHandler}>
                    ${slot}
                    ${statusText}
                </div>
            `;
        });

        console.log('Generated HTML:', slotsHtml);
        slotsContainer.innerHTML = slotsHtml;
        console.log('=== END TIME SLOT DEBUG ===');
    }
                </div>
            `;
        });

        slotsContainer.innerHTML = slotsHtml;
    }

    // Convert 12-hour to 24-hour format for sorting
    function convertTo24Hour(time12) {
        const [time, modifier] = time12.split(' ');
        let [hours, minutes] = time.split(':');
        if (hours === '12') {
            hours = '00';
        }
        if (modifier === 'PM') {
            hours = parseInt(hours, 10) + 12;
        }
        return `${hours}:${minutes}`;
    }

    // Convert 24-hour to 12-hour format
    function formatTo12Hour(time24) {
        const [hours, minutes] = time24.split(':');
        const hour12 = hours % 12 || 12;
        const ampm = hours >= 12 ? 'PM' : 'AM';
        return `${hour12}:${minutes} ${ampm}`;
    }

    // Select time slot
    window.selectTimeSlot = function(timeSlot) {
        // Don't select if slot is disabled
        if (event.target.classList.contains('disabled')) {
            return;
        }

        // Remove previous selection
        document.querySelectorAll('.custom-time-slot-option').forEach(option => {
            option.classList.remove('selected');
        });

        // Select current option
        event.target.classList.add('selected');
        selectedTimeSlot = timeSlot;

        // Enable reschedule button if all required fields are filled
        checkRescheduleFormValidity();
    };

    // Check if reschedule form is valid
    function checkRescheduleFormValidity() {
        const date = document.getElementById('selectedDate').value;
        const reason = document.getElementById('rescheduleReason').value.trim();
        const rescheduleBtn = document.getElementById('rescheduleBtn');

        rescheduleBtn.disabled = !(date && selectedTimeSlot && reason);
    }

    // Handle reason textarea change
    document.getElementById('rescheduleReason').addEventListener('input', checkRescheduleFormValidity);

    // Handle reschedule submission
    document.getElementById('rescheduleBtn').addEventListener('click', function() {
        if (!selectedTimeSlot || !currentBookingData) {
            toastr.error('Please select a time slot');
            return;
        }

        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('new_date', document.getElementById('selectedDate').value);
        formData.append('new_time_slot', selectedTimeSlot);
        formData.append('reschedule_reason', document.getElementById('rescheduleReason').value);

        // Disable button and show loading
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Rescheduling...';

        fetch(`{{ url('/user/upcoming-appointment') }}/${currentBookingData.id}/reschedule`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
                closeModal('rescheduleModal');
                // Reload page after short delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                if (data.errors) {
                    Object.values(data.errors).forEach(msgArr => {
                        if (Array.isArray(msgArr)) {
                            msgArr.forEach(msg => toastr.error(msg));
                        } else {
                            toastr.error(msgArr);
                        }
                    });
                } else {
                    toastr.error(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('Error rescheduling appointment');
        })
        .finally(() => {
            // Re-enable button
            this.disabled = false;
            this.innerHTML = '<i class="fas fa-calendar-alt"></i> Reschedule Appointment';
        });
    });
});
</script>
@endsection