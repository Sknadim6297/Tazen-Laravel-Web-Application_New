@extends('professional.layout.layout')

@section('title', 'All Bookings | Yuktione')

@section('styles')
<style>
    .booking-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 50;
        overflow-y: auto;
        background: rgb(15 23 42 / .5);
        padding: 1rem;
    }

    .booking-modal-panel {
        width: min(100%, 56rem);
        max-height: calc(100dvh - 2rem);
        overflow-y: auto;
        margin: 0 auto;
        border: 1px solid #e0e9f0;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 24px 70px -24px rgb(38 50 56 / .3);
    }

    .booking-modal-panel--narrow {
        width: min(100%, 42rem);
    }
</style>
@endsection

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5">
    <header class="flex flex-col gap-4 rounded-panel border border-border bg-surface px-5 py-4 shadow-card sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <nav aria-label="Breadcrumb" class="mb-1 text-xs font-medium text-text-muted">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a class="text-brand-primary hover:text-brand-800" href="{{ route('professional.dashboard') }}">Dashboard</a></li>
                    <li aria-hidden="true" class="text-border">/</li>
                    <li aria-current="page" class="text-text-secondary">Bookings</li>
                </ol>
            </nav>
            <h1 class="text-2xl font-semibold tracking-tight text-text-primary sm:text-[1.75rem]">All Bookings</h1>
            <p class="mt-1 text-sm text-text-secondary">Manage consultations, schedules, meeting links and customer documents.</p>
        </div>
        <a href="{{ route('professional.dashboard') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-control border border-border bg-white px-4 py-2 text-sm font-semibold text-text-secondary transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
            <i class="fas fa-arrow-left text-xs" aria-hidden="true"></i>
            Back to Dashboard
        </a>
    </header>

    <section class="rounded-panel border border-border bg-surface p-5 shadow-card sm:p-6" aria-labelledby="booking-filters-title">
        <div class="mb-4 flex items-center gap-2">
            <span class="grid size-8 place-items-center rounded-lg bg-brand-50 text-brand-primary" aria-hidden="true">
                <i class="fas fa-sliders-h text-sm"></i>
            </span>
            <div>
                <h2 id="booking-filters-title" class="text-base font-semibold text-text-primary">Filter bookings</h2>
                <p class="text-xs text-text-muted">Refine the list by plan, status, customer or booking date.</p>
            </div>
        </div>

        <form action="{{ route('professional.booking.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div>
                <label for="plan_type" class="mb-1.5 block text-xs font-semibold text-text-secondary">Plan type</label>
                <select class="min-h-11 w-full rounded-control border border-border bg-white px-3 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" name="plan_type" id="plan_type">
                    <option value="">All plans</option>
                    @foreach($planTypes as $type)
                        <option value="{{ $type }}" {{ request('plan_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-xs font-semibold text-text-secondary">Status</label>
                <select class="min-h-11 w-full rounded-control border border-border bg-white px-3 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" name="status" id="status">
                    <option value="">All statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div>
                <label for="search_name" class="mb-1.5 block text-xs font-semibold text-text-secondary">Customer or service</label>
                <input class="min-h-11 w-full rounded-control border border-border bg-white px-3 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" type="search" name="search_name" id="search_name" value="{{ request('search_name') }}" placeholder="Search by name or service">
            </div>

            <div>
                <label for="search_date_from" class="mb-1.5 block text-xs font-semibold text-text-secondary">From date</label>
                <input class="min-h-11 w-full rounded-control border border-border bg-white px-3 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" type="date" name="search_date_from" id="search_date_from" value="{{ request('search_date_from') }}">
            </div>

            <div>
                <label for="search_date_to" class="mb-1.5 block text-xs font-semibold text-text-secondary">To date</label>
                <input class="min-h-11 w-full rounded-control border border-border bg-white px-3 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" type="date" name="search_date_to" id="search_date_to" value="{{ request('search_date_to') }}">
            </div>

            <div class="flex flex-col gap-2 self-end sm:flex-row">
                <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    Search
                </button>
                <a href="{{ route('professional.booking.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-control border border-brand-200 bg-white px-4 py-2 text-sm font-semibold text-brand-primary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    <i class="fas fa-undo" aria-hidden="true"></i>
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-panel border border-border bg-surface shadow-card" aria-labelledby="bookings-overview-title">
        <div class="flex flex-col gap-1 border-b border-divider px-5 py-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 id="bookings-overview-title" class="text-base font-semibold text-text-primary">Bookings overview</h2>
                <p class="mt-0.5 text-xs text-text-muted">Review schedules, update documents and connect with customers.</p>
            </div>
            @if($bookings->total() > 0)
                <p class="text-xs font-medium text-text-muted">
                    Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }}
                </p>
            @endif
        </div>

        @if($bookings->isEmpty())
            <div class="px-5 py-12 text-center sm:py-16">
                <span class="mx-auto grid size-12 place-items-center rounded-full bg-brand-50 text-brand-primary" aria-hidden="true">
                    <i class="fas fa-calendar-times text-lg"></i>
                </span>
                @if($hasAnyBookings)
                    <h3 class="mt-4 text-base font-semibold text-text-primary">No bookings found</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-text-secondary">Try adjusting your filters to find the bookings you're looking for.</p>
                    <a href="{{ route('professional.booking.index') }}" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-control border border-brand-200 px-4 py-2 text-sm font-semibold text-brand-primary hover:bg-brand-50">Reset filters</a>
                @else
                    <h3 class="mt-4 text-base font-semibold text-text-primary">No bookings yet</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-text-secondary">Your customer bookings will appear here.</p>
                @endif
            </div>
        @else
            <div class="hidden md:block">
                <div class="overflow-x-auto overscroll-x-contain">
                    <table class="w-full min-w-[1280px] border-collapse text-left text-sm">
                        <caption class="sr-only">Professional customer bookings, schedules, documents and actions</caption>
                        <thead class="bg-surface-muted text-xs font-semibold uppercase tracking-wide text-text-secondary">
                            <tr>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Customer</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Service and plan</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Schedule</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Meeting</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Status</th>
                                <th scope="col" class="px-4 py-3.5">Admin remarks</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Your document</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Customer documents</th>
                                <th scope="col" class="whitespace-nowrap px-4 py-3.5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-divider">
                            @foreach($bookings as $booking)
                                @php
                                    $timedates = $booking->timedates ?? collect();
                                    $nextTimedate = $timedates
                                        ->filter(fn ($timedate) => \Carbon\Carbon::parse($timedate->date)->isFuture())
                                        ->sortBy('date')
                                        ->first();
                                    $allCompleted = $timedates->isNotEmpty() && $timedates->every(fn ($timedate) => $timedate->status === 'completed');
                                    $meetingLink = $booking->meeting_link ?: optional($timedates->first(fn ($timedate) => $timedate->meeting_link))->meeting_link;
                                    $customerDocuments = collect(explode(',', $booking->customer_document ?? ''))->map(fn ($path) => trim($path))->filter();
                                @endphp
                                <tr class="align-top transition hover:bg-surface-muted/70">
                                    <td class="max-w-56 px-4 py-4">
                                        <p class="font-semibold text-text-primary">{{ $booking->customer_name }}</p>
                                        @if($booking->customer_email)
                                            <p class="mt-1 break-all text-xs text-text-muted">{{ $booking->customer_email }}</p>
                                        @endif
                                        <button type="button" class="questionnaire-info mt-2 inline-flex min-h-8 items-center gap-1.5 rounded-lg px-2 text-xs font-semibold text-brand-primary hover:bg-brand-50" data-booking-id="{{ $booking->id }}">
                                            <i class="fas fa-clipboard-list" aria-hidden="true"></i> Questionnaire
                                        </button>
                                    </td>
                                    <td class="max-w-56 px-4 py-4">
                                        <p class="font-medium text-text-primary">{{ $booking->service_name ?: 'Service not specified' }}</p>
                                        @if($booking->sub_service_name)
                                            <p class="mt-1 text-xs text-text-secondary">{{ $booking->sub_service_name }}</p>
                                        @endif
                                        <span class="mt-2 inline-flex rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium capitalize text-brand-800">{{ str_replace('_', ' ', $booking->plan_type ?: 'Plan not specified') }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4">
                                        <p class="font-medium text-text-primary">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</p>
                                        @if($nextTimedate)
                                            <p class="mt-1 text-xs leading-5 text-text-secondary">{{ str_replace(',', ' · ', $nextTimedate->time_slot) }}</p>
                                            <p class="text-xs text-text-muted">Session: {{ \Carbon\Carbon::parse($nextTimedate->date)->format('d M Y') }}</p>
                                        @else
                                            <p class="mt-1 text-xs text-text-muted">{{ str_replace(',', ' · ', $booking->time_slot) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($meetingLink)
                                            <a href="{{ $meetingLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-2 whitespace-nowrap rounded-control border border-brand-200 px-3 py-1.5 text-xs font-semibold text-brand-primary hover:bg-brand-50">
                                                <i class="fas fa-video" aria-hidden="true"></i> Join meeting
                                            </a>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-800">Link not available</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $allCompleted ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-800' }}">
                                            {{ $allCompleted ? 'Completed' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="max-w-48 whitespace-normal px-4 py-4 text-xs leading-5 text-text-secondary">{{ $booking->remarks ?: 'No remarks' }}</td>
                                    <td class="px-4 py-4">
                                        @if($booking->professional_documents)
                                            <a href="{{ asset('storage/' . $booking->professional_documents) }}" target="_blank" rel="noopener noreferrer" class="block max-w-40 truncate text-xs font-medium text-brand-primary hover:underline" title="{{ basename($booking->professional_documents) }}">
                                                <i class="fas fa-file-pdf mr-1" aria-hidden="true"></i>{{ basename($booking->professional_documents) }}
                                            </a>
                                        @endif
                                        <button type="button" class="mt-2 inline-flex min-h-9 items-center gap-1.5 rounded-control bg-brand-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-800" onclick="openUploadModal('{{ $booking->id }}')">
                                            <i class="fas fa-{{ $booking->professional_documents ? 'sync' : 'upload' }}" aria-hidden="true"></i>
                                            {{ $booking->professional_documents ? 'Update' : 'Upload PDF' }}
                                        </button>
                                    </td>
                                    <td class="px-4 py-4">
                                        @forelse($customerDocuments as $document)
                                            <a href="{{ asset('storage/' . $document) }}" target="_blank" rel="noopener noreferrer" class="mb-1 block max-w-40 truncate text-xs font-medium text-brand-primary hover:underline" title="{{ basename($document) }}">
                                                <i class="fas fa-file-alt mr-1" aria-hidden="true"></i>{{ basename($document) }}
                                            </a>
                                        @empty
                                            <span class="text-xs text-text-muted">None provided</span>
                                        @endforelse
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex flex-col items-start gap-2">
                                            <button type="button" class="view-details inline-flex min-h-9 items-center gap-1.5 rounded-control border border-border px-3 py-1.5 text-xs font-semibold text-text-secondary hover:bg-surface-muted" data-booking-id="{{ $booking->id }}">
                                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> Details
                                            </button>
                                            <a href="{{ route('professional.chat.open', $booking->id) }}" class="chat-button relative inline-flex min-h-9 items-center gap-1.5 rounded-control px-3 py-1.5 text-xs font-semibold text-brand-primary hover:bg-brand-50" target="_blank" rel="noopener noreferrer" title="Chat with customer" data-booking-id="{{ $booking->id }}">
                                                <i class="fas fa-comments" aria-hidden="true"></i> Chat
                                                <span class="chat-badge absolute -right-1 -top-1 hidden min-w-4 rounded-full bg-red-600 px-1 text-center text-[10px] leading-4 text-white" data-chat-badge aria-label="Unread messages">0</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-3 p-4 md:hidden">
                @foreach($bookings as $booking)
                    @php
                        $timedates = $booking->timedates ?? collect();
                        $nextTimedate = $timedates
                            ->filter(fn ($timedate) => \Carbon\Carbon::parse($timedate->date)->isFuture())
                            ->sortBy('date')
                            ->first();
                        $allCompleted = $timedates->isNotEmpty() && $timedates->every(fn ($timedate) => $timedate->status === 'completed');
                        $meetingLink = $booking->meeting_link ?: optional($timedates->first(fn ($timedate) => $timedate->meeting_link))->meeting_link;
                        $customerDocuments = collect(explode(',', $booking->customer_document ?? ''))->map(fn ($path) => trim($path))->filter();
                    @endphp
                    <article class="rounded-panel border border-border bg-white p-4 shadow-card">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="break-words font-semibold text-text-primary">{{ $booking->customer_name }}</h3>
                                @if($booking->customer_email)
                                    <p class="mt-0.5 break-all text-xs text-text-muted">{{ $booking->customer_email }}</p>
                                @endif
                            </div>
                            <span class="inline-flex shrink-0 rounded-full px-2.5 py-1 text-xs font-semibold {{ $allCompleted ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-800' }}">
                                {{ $allCompleted ? 'Completed' : 'Pending' }}
                            </span>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-3 border-t border-divider pt-3 text-sm">
                            <div class="col-span-2">
                                <dt class="text-xs font-medium text-text-muted">Service</dt>
                                <dd class="mt-0.5 font-medium text-text-primary">{{ $booking->service_name ?: 'Service not specified' }}</dd>
                                @if($booking->sub_service_name)
                                    <dd class="mt-0.5 text-xs text-text-secondary">{{ $booking->sub_service_name }}</dd>
                                @endif
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-muted">Plan</dt>
                                <dd class="mt-0.5 capitalize text-text-primary">{{ str_replace('_', ' ', $booking->plan_type ?: 'Not specified') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-text-muted">Booking date</dt>
                                <dd class="mt-0.5 text-text-primary">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-xs font-medium text-text-muted">Session time</dt>
                                <dd class="mt-0.5 text-text-primary">
                                    @if($nextTimedate)
                                        {{ \Carbon\Carbon::parse($nextTimedate->date)->format('d M Y') }} · {{ str_replace(',', ' · ', $nextTimedate->time_slot) }}
                                    @else
                                        {{ str_replace(',', ' · ', $booking->time_slot) ?: 'Not scheduled' }}
                                    @endif
                                </dd>
                            </div>
                            <div class="col-span-2">
                                <dt class="text-xs font-medium text-text-muted">Admin remarks</dt>
                                <dd class="mt-0.5 break-words text-text-secondary">{{ $booking->remarks ?: 'No remarks' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 space-y-3 border-t border-divider pt-3">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($meetingLink)
                                    <a href="{{ $meetingLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-2 rounded-control border border-brand-200 px-3 py-1.5 text-xs font-semibold text-brand-primary hover:bg-brand-50">
                                        <i class="fas fa-video" aria-hidden="true"></i> Join meeting
                                    </a>
                                @else
                                    <span class="text-xs text-text-muted">Meeting link not available</span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <p class="text-xs font-semibold text-text-secondary">Your document</p>
                                @if($booking->professional_documents)
                                    <a href="{{ asset('storage/' . $booking->professional_documents) }}" target="_blank" rel="noopener noreferrer" class="block truncate text-xs font-medium text-brand-primary hover:underline">{{ basename($booking->professional_documents) }}</a>
                                @endif
                                <button type="button" class="inline-flex min-h-9 items-center gap-1.5 rounded-control bg-brand-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-800" onclick="openUploadModal('{{ $booking->id }}')">
                                    <i class="fas fa-{{ $booking->professional_documents ? 'sync' : 'upload' }}" aria-hidden="true"></i>
                                    {{ $booking->professional_documents ? 'Update PDF' : 'Upload PDF' }}
                                </button>
                            </div>

                            <div>
                                <p class="text-xs font-semibold text-text-secondary">Customer documents</p>
                                @forelse($customerDocuments as $document)
                                    <a href="{{ asset('storage/' . $document) }}" target="_blank" rel="noopener noreferrer" class="mt-1 block truncate text-xs font-medium text-brand-primary hover:underline">{{ basename($document) }}</a>
                                @empty
                                    <p class="mt-1 text-xs text-text-muted">None provided</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 border-t border-divider pt-3">
                            <button type="button" class="view-details inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-control border border-border px-3 py-2 text-xs font-semibold text-text-secondary hover:bg-surface-muted" data-booking-id="{{ $booking->id }}">
                                <i class="fas fa-calendar-alt" aria-hidden="true"></i> Details
                            </button>
                            <button type="button" class="questionnaire-info inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-control border border-brand-200 px-3 py-2 text-xs font-semibold text-brand-primary hover:bg-brand-50" data-booking-id="{{ $booking->id }}">
                                <i class="fas fa-clipboard-list" aria-hidden="true"></i> Questionnaire
                            </button>
                            <a href="{{ route('professional.chat.open', $booking->id) }}" class="chat-button relative inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-control bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-primary hover:bg-brand-100" target="_blank" rel="noopener noreferrer" data-booking-id="{{ $booking->id }}">
                                <i class="fas fa-comments" aria-hidden="true"></i> Chat
                                <span class="chat-badge absolute right-1 top-1 hidden min-w-4 rounded-full bg-red-600 px-1 text-center text-[10px] leading-4 text-white" data-chat-badge aria-label="Unread messages">0</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($bookings->hasPages())
                <nav class="border-t border-divider px-4 py-4 sm:px-5" aria-label="Bookings pagination">
                    {{ $bookings->links() }}
                </nav>
            @endif
        @endif
    </section>
</div>

<div id="bookingDetailModal" class="booking-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="bookingDetailTitle">
    <section class="booking-modal-panel">
        <header class="flex items-center justify-between border-b border-divider px-5 py-4">
            <div>
                <h2 id="bookingDetailTitle" class="text-lg font-semibold text-text-primary">Booking details</h2>
                <p class="mt-0.5 text-xs text-text-muted">Review session details and update completion status.</p>
            </div>
            <button type="button" class="close-booking-modal grid size-9 place-items-center rounded-lg text-text-muted hover:bg-surface-muted hover:text-text-primary" aria-label="Close booking details">&times;</button>
        </header>
        <div id="bookingDetailContent" class="overflow-x-auto p-4 sm:p-5">
            <table class="w-full min-w-[620px] border-collapse text-left text-sm" id="details-table">
                <thead class="bg-surface-muted text-xs font-semibold text-text-secondary">
                    <tr>
                        <th scope="col" class="px-3 py-2.5">Date</th>
                        <th scope="col" class="px-3 py-2.5">Time slot</th>
                        <th scope="col" class="px-3 py-2.5">Remarks</th>
                        <th scope="col" class="px-3 py-2.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider"></tbody>
            </table>
        </div>
    </section>
</div>

<div id="questionnaireModal" class="booking-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="questionnaireTitle">
    <section class="booking-modal-panel booking-modal-panel--narrow">
        <header class="flex items-center justify-between border-b border-divider px-5 py-4">
            <div>
                <h2 id="questionnaireTitle" class="text-lg font-semibold text-text-primary">Questionnaire answers</h2>
                <p class="mt-0.5 text-xs text-text-muted">Customer responses associated with this booking.</p>
            </div>
            <button type="button" class="close-questionnaire-modal grid size-9 place-items-center rounded-lg text-text-muted hover:bg-surface-muted hover:text-text-primary" aria-label="Close questionnaire">&times;</button>
        </header>
        <div id="questionnaireContent" class="p-5"></div>
    </section>
</div>

<div id="uploadModal" class="booking-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="uploadModalTitle">
    <section class="booking-modal-panel booking-modal-panel--narrow">
        <header class="flex items-center justify-between border-b border-divider px-5 py-4">
            <div>
                <h2 id="uploadModalTitle" class="text-lg font-semibold text-text-primary">Upload document</h2>
                <p class="mt-0.5 text-xs text-text-muted">Attach a PDF document to this booking.</p>
            </div>
            <button type="button" class="close-upload-modal grid size-9 place-items-center rounded-lg text-text-muted hover:bg-surface-muted hover:text-text-primary" aria-label="Close upload dialog">&times;</button>
        </header>
        <form id="uploadForm" class="space-y-4 p-5" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="booking_id" name="booking_id" value="">
            <div>
                <label for="document" class="mb-1.5 block text-sm font-semibold text-text-secondary">PDF document</label>
                <input type="file" id="document" name="document" accept=".pdf,application/pdf" required class="block min-h-11 w-full rounded-control border border-border bg-white p-2 text-sm text-text-primary file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:font-semibold file:text-brand-primary focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100">
                <p class="mt-1.5 text-xs text-text-muted">PDF only. Maximum file size: 2 MB.</p>
            </div>
            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                <i class="fas fa-upload" aria-hidden="true"></i> Upload document
            </button>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const detailModal = document.getElementById('bookingDetailModal');
    const questionnaireModal = document.getElementById('questionnaireModal');
    const uploadModal = document.getElementById('uploadModal');
    const detailBody = document.querySelector('#details-table tbody');
    const questionnaireContent = document.getElementById('questionnaireContent');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const toast = (type, message) => {
        if (window.YuktioneToast?.[type]) {
            window.YuktioneToast[type](message);
        } else if (window.toastr?.[type]) {
            window.toastr[type](message);
        }
    };

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[character]);

    const showModal = (modal) => {
        modal.style.display = 'block';
        document.body.classList.add('overflow-hidden');
        modal.querySelector('button')?.focus();
    };

    const hideModal = (modal) => {
        modal.style.display = 'none';
        if (![detailModal, questionnaireModal, uploadModal].some(item => item.style.display === 'block')) {
            document.body.classList.remove('overflow-hidden');
        }
    };

    document.querySelectorAll('.close-booking-modal').forEach(button => button.addEventListener('click', () => hideModal(detailModal)));
    document.querySelectorAll('.close-questionnaire-modal').forEach(button => button.addEventListener('click', () => hideModal(questionnaireModal)));
    document.querySelectorAll('.close-upload-modal').forEach(button => button.addEventListener('click', () => hideModal(uploadModal)));

    [detailModal, questionnaireModal, uploadModal].forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) hideModal(modal);
        });
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            [detailModal, questionnaireModal, uploadModal].forEach(modal => {
                if (modal.style.display === 'block') hideModal(modal);
            });
        }
    });

    window.openUploadModal = (bookingId) => {
        document.getElementById('booking_id').value = bookingId;
        document.getElementById('document').value = '';
        showModal(uploadModal);
    };

    document.addEventListener('click', async event => {
        const detailsButton = event.target.closest('.view-details');
        if (detailsButton) {
            const bookingId = detailsButton.dataset.bookingId;
            detailBody.innerHTML = '<tr><td colspan="4" class="px-3 py-8 text-center text-sm text-text-muted">Loading booking details…</td></tr>';
            showModal(detailModal);
            try {
                const response = await fetch(`/professional/bookings/${bookingId}/details`, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`Booking details request failed (${response.status}).`);
                const data = await response.json();
                detailBody.innerHTML = '';
                (data.dates || []).forEach(item => {
                    (item.time_slot || []).forEach(slot => {
                        const completed = String(item.status).toLowerCase() === 'completed';
                        detailBody.insertAdjacentHTML('beforeend', `
                            <tr class="align-middle">
                                <td class="whitespace-nowrap px-3 py-3 text-text-primary">${escapeHtml(item.date)}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-text-secondary">${escapeHtml(slot)}</td>
                                <td class="px-3 py-3"><input type="text" class="remark-input min-h-10 w-full rounded-control border border-border px-3 text-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-100" value="${escapeHtml(item.remarks || '')}" placeholder="Optional remarks"></td>
                                <td class="px-3 py-3">
                                    <label class="inline-flex items-center gap-2 whitespace-nowrap text-xs font-medium text-text-secondary">
                                        <input type="checkbox" class="status-checkbox size-4 rounded border-border text-brand-primary focus:ring-brand-primary" ${completed ? 'checked' : ''}>
                                        <span class="status-text">${completed ? 'Completed' : 'Pending'}</span>
                                    </label>
                                    <input type="hidden" class="slot-data" data-booking-id="${escapeHtml(bookingId)}" data-date="${escapeHtml(item.date)}" data-slot="${escapeHtml(slot)}">
                                </td>
                            </tr>
                        `);
                    });
                });
                if (!detailBody.children.length) {
                    detailBody.innerHTML = '<tr><td colspan="4" class="px-3 py-8 text-center text-sm text-text-muted">No session details are available.</td></tr>';
                }
            } catch (error) {
                console.error('Unable to load booking details.', error);
                detailBody.innerHTML = '<tr><td colspan="4" class="px-3 py-8 text-center text-sm text-red-700">Could not load booking details. Please try again.</td></tr>';
                toast('error', 'Could not load booking details.');
            }
            return;
        }

        const questionnaireButton = event.target.closest('.questionnaire-info');
        if (!questionnaireButton) return;
        const bookingId = questionnaireButton.dataset.bookingId;
        questionnaireContent.innerHTML = '<p class="py-8 text-center text-sm text-text-muted"><i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>Loading questionnaire answers…</p>';
        showModal(questionnaireModal);
        try {
            const response = await fetch(`/professional/bookings/${bookingId}/questionnaire`, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error(`Questionnaire request failed (${response.status}).`);
            const data = await response.json();
            if (!data.success) {
                questionnaireContent.innerHTML = `<div class="rounded-control border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900"><h3 class="font-semibold">No answers found</h3><p class="mt-1">The customer has not completed a questionnaire for this booking.</p></div>`;
                return;
            }
            const booking = data.booking_details || {};
            const answers = data.answers || [];
            questionnaireContent.innerHTML = `
                <div class="mb-4 rounded-control bg-brand-50 p-4">
                    <p class="font-semibold text-text-primary">${escapeHtml(booking.customer_name)}</p>
                    <p class="mt-1 text-sm text-text-secondary">${escapeHtml(booking.service_name)}${booking.sub_service_name ? ` · ${escapeHtml(booking.sub_service_name)}` : ''}</p>
                    <p class="mt-1 text-xs text-text-muted">${booking.booking_date ? escapeHtml(booking.booking_date) + ' · ' : ''}${answers.length} answer${answers.length === 1 ? '' : 's'}</p>
                </div>
                ${answers.length ? answers.map((answer, index) => `
                    <article class="mb-3 rounded-control border border-border p-4">
                        <h3 class="text-sm font-semibold text-text-primary"><span class="mr-2 text-brand-primary">${index + 1}.</span>${escapeHtml(answer.question)}</h3>
                        <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-text-secondary">${escapeHtml(answer.answer)}</p>
                    </article>
                `).join('') : '<p class="py-6 text-center text-sm text-text-muted">No questionnaire answers are available.</p>'}
            `;
        } catch (error) {
            console.error('Unable to load questionnaire answers.', error);
            questionnaireContent.innerHTML = '<p class="rounded-control border border-red-200 bg-red-50 p-4 text-sm text-red-800">Could not load questionnaire answers. Please try again.</p>';
            toast('error', 'Could not load questionnaire answers.');
        }
    });

    detailBody.addEventListener('change', async event => {
        const checkbox = event.target.closest('.status-checkbox');
        if (!checkbox) return;
        const row = checkbox.closest('tr');
        const data = row.querySelector('.slot-data');
        const remarks = row.querySelector('.remark-input').value.trim();
        const nextStatus = checkbox.checked ? 'completed' : 'pending';
        if (!remarks && !window.confirm('Continue without adding remarks?')) {
            checkbox.checked = !checkbox.checked;
            return;
        }

        checkbox.disabled = true;
        try {
            const response = await fetch('/professional/bookings/update-status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
                body: JSON.stringify({
                    booking_id: data.dataset.bookingId,
                    date: data.dataset.date,
                    slot: data.dataset.slot,
                    remarks,
                    status: nextStatus,
                }),
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Unable to update booking status.');
            row.querySelector('.status-text').textContent = nextStatus === 'completed' ? 'Completed' : 'Pending';
            toast('success', result.message || 'Booking status updated.');
        } catch (error) {
            console.error('Unable to update booking status.', error);
            checkbox.checked = !checkbox.checked;
            toast('error', error.message || 'Failed to update booking status.');
        } finally {
            checkbox.disabled = false;
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', async event => {
        event.preventDefault();
        const form = event.currentTarget;
        const submit = form.querySelector('button[type="submit"]');
        const file = document.getElementById('document').files[0];
        if (!file) {
            toast('error', 'Please select a PDF to upload.');
            return;
        }
        if (file.type !== 'application/pdf' || file.size > 2 * 1024 * 1024) {
            toast('error', 'Choose a PDF file no larger than 2 MB.');
            return;
        }

        submit.disabled = true;
        submit.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> Uploading…';
        try {
            const response = await fetch(`/professional/bookings/${document.getElementById('booking_id').value}/upload-documents`, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error('Unable to upload this document. Please check the PDF and try again.');
            toast('success', 'Document uploaded successfully.');
            hideModal(uploadModal);
            window.location.reload();
        } catch (error) {
            console.error('Unable to upload booking document.', error);
            toast('error', 'Unable to upload this document. Please try again.');
        } finally {
            submit.disabled = false;
            submit.innerHTML = '<i class="fas fa-upload" aria-hidden="true"></i> Upload document';
        }
    });

    const refreshUnreadCounts = () => {
        document.querySelectorAll('.chat-button').forEach(async link => {
            const badge = link.querySelector('[data-chat-badge]');
            try {
                const response = await fetch(`/professional/chat/booking/${link.dataset.bookingId}/unread-count`, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error(`Unread count request failed (${response.status}).`);
                const result = await response.json();
                if (result.success && result.unread_count > 0) {
                    badge.textContent = result.unread_count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } catch (error) {
                console.error('Unable to refresh booking chat unread count.', error);
            }
        });
    };

    refreshUnreadCounts();
    window.setInterval(refreshUnreadCounts, 30000);
});
</script>
@endsection
