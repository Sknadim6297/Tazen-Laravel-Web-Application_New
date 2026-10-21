@extends('professional.layout.layout')

@section('title', 'Additional Services')

@section('styles')
<style>
    .additional-service-modal[hidden] { display: none; }
</style>
@endsection

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-6">
    <header class="flex flex-col gap-5 rounded-panel border border-border bg-white p-5 shadow-card sm:flex-row sm:items-center sm:justify-between sm:p-6">
        <div>
            <nav aria-label="Breadcrumb" class="mb-3 text-sm text-text-muted">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a class="transition hover:text-brand-primary" href="{{ route('professional.dashboard') }}">Dashboard</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="font-medium text-text-primary" aria-current="page">Additional Services</li>
                </ol>
            </nav>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-brand-primary">Service management</p>
            <h1 class="text-2xl font-bold tracking-tight text-text-primary sm:text-3xl">Additional Services</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary">Create and track services related to your customer bookings.</p>
        </div>
        <a href="{{ route('professional.additional-services.create') }}" class="inline-flex min-h-11 shrink-0 items-center justify-center gap-2 rounded-control bg-brand-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
            <i class="fas fa-plus" aria-hidden="true"></i>
            Add New Service
        </a>
    </header>

    <section class="overflow-hidden rounded-panel border border-border bg-white shadow-card" aria-labelledby="service-filters-title">
        <div class="border-b border-border px-5 py-4 sm:px-6">
            <h2 id="service-filters-title" class="text-base font-semibold text-text-primary">Filter services</h2>
            <p class="mt-1 text-sm text-text-muted">Filters apply across all of your services, not just the current page.</p>
        </div>
        <form method="GET" action="{{ route('professional.additional-services.index') }}" class="space-y-4 p-5 sm:p-6">
            @if(count($filterErrors))
                <div class="rounded-control border border-error/30 bg-error-surface p-4 text-sm text-error" role="alert">
                    <p class="font-semibold">Please check your filter values:</p>
                    <ul class="mt-2 list-inside list-disc space-y-1">
                        @foreach($filterErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div>
                    <label for="filterSearch" class="mb-1.5 block text-sm font-medium text-text-secondary">Search</label>
                    <input id="filterSearch" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Service, customer or booking" class="min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm placeholder:text-text-muted focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                </div>
                <div>
                    <label for="filterCustomer" class="mb-1.5 block text-sm font-medium text-text-secondary">Customer</label>
                    <select id="filterCustomer" name="customer_id" class="min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                        <option value="">All customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" @selected((string) ($filters['customer_id'] ?? '') === (string) $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterPaymentStatus" class="mb-1.5 block text-sm font-medium text-text-secondary">Payment status</label>
                    <select id="filterPaymentStatus" name="payment_status" class="min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                        <option value="">All payment statuses</option>
                        @foreach(['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterServiceStatus" class="mb-1.5 block text-sm font-medium text-text-secondary">Service status</label>
                    <select id="filterServiceStatus" name="service_status" class="min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                        <option value="">All service statuses</option>
                        @foreach([
                            'completed' => 'Completed',
                            'in_progress' => 'In Progress',
                            'ready_to_start' => 'Ready to Start',
                            'awaiting_delivery_date' => 'Awaiting Delivery Date',
                            'awaiting_payment' => 'Awaiting Payment',
                            'rejected' => 'Rejected',
                            'under_negotiation' => 'Under Negotiation',
                            'price_updated' => 'Price Updated',
                            'pending_review' => 'Pending Review',
                            'awaiting_admin' => 'Awaiting Admin',
                            'pending' => 'Pending',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['service_status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterDateRange" class="mb-1.5 block text-sm font-medium text-text-secondary">Created date</label>
                    <select id="filterDateRange" name="date_range" class="min-h-11 w-full rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                        <option value="">All time</option>
                        <option value="today" @selected(($filters['date_range'] ?? '') === 'today')>Today</option>
                        <option value="week" @selected(($filters['date_range'] ?? '') === 'week')>Last 7 days</option>
                        <option value="month" @selected(($filters['date_range'] ?? '') === 'month')>Last month</option>
                        <option value="custom" @selected(($filters['date_range'] ?? '') === 'custom')>Custom range</option>
                    </select>
                </div>
                <div id="customDateRange" class="{{ ($filters['date_range'] ?? '') === 'custom' ? 'grid' : 'hidden' }} grid-cols-2 gap-3">
                    <div>
                        <label for="filterDateFrom" class="mb-1.5 block text-sm font-medium text-text-secondary">From</label>
                        <input id="filterDateFrom" name="from_date" type="date" value="{{ $filters['from_date'] ?? '' }}" class="min-h-11 w-full min-w-0 rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                    </div>
                    <div>
                        <label for="filterDateTo" class="mb-1.5 block text-sm font-medium text-text-secondary">To</label>
                        <input id="filterDateTo" name="to_date" type="date" value="{{ $filters['to_date'] ?? '' }}" class="min-h-11 w-full min-w-0 rounded-control border border-border bg-white px-3 py-2 text-sm text-text-primary shadow-sm focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3 border-t border-border pt-4 sm:flex-row sm:items-center">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control bg-brand-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    Apply filters
                </button>
                <a href="{{ route('professional.additional-services.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control border border-border bg-white px-5 py-2.5 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    Clear filters
                </a>
                <span class="text-sm text-text-muted sm:ml-auto" aria-live="polite">
                    {{ number_format($additionalServices->total()) }} {{ \Illuminate\Support\Str::plural('service', $additionalServices->total()) }}
                </span>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-panel border border-border bg-white shadow-card" aria-labelledby="services-list-title">
        <div class="flex flex-col gap-1 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 id="services-list-title" class="text-base font-semibold text-text-primary">Your additional services</h2>
                <p class="mt-1 text-sm text-text-muted">Review service details and continue managing delivery and consultation status.</p>
            </div>
        </div>

        @if($additionalServices->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1080px] border-collapse text-left text-sm">
                    <caption class="sr-only">Additional services created by you</caption>
                    <thead class="bg-surface-muted text-xs uppercase tracking-wide text-text-secondary">
                        <tr>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Service</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Customer</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Booking</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Total price</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Service status</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Payment</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Created</th>
                            <th scope="col" class="whitespace-nowrap px-4 py-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($additionalServices as $service)
                            @php
                                $statusText = 'Pending';
                                $statusTone = 'bg-warning-surface text-warning';
                                $statusHint = null;

                                if ($service->consulting_status === 'done' && $service->customer_confirmed_at) {
                                    $statusText = 'Completed';
                                    $statusTone = 'bg-success-surface text-success';
                                } elseif ($service->consulting_status === 'in_progress') {
                                    $statusText = 'In Progress';
                                    $statusTone = 'bg-success-surface text-success';
                                } elseif ($service->admin_status === 'approved') {
                                    if ($service->payment_status === 'paid') {
                                        if ($service->delivery_date_set) {
                                            $statusText = 'Ready to Start';
                                            $statusTone = 'bg-success-surface text-success';
                                            $statusHint = 'Delivery date set';
                                        } else {
                                            $statusText = 'Awaiting Delivery Date';
                                            $statusHint = 'Set a delivery date to proceed';
                                        }
                                    } else {
                                        $statusText = 'Awaiting Payment';
                                        $statusHint = 'Customer payment is pending';
                                    }
                                } elseif ($service->admin_status === 'rejected') {
                                    $statusText = 'Rejected';
                                    $statusTone = 'bg-error-surface text-error';
                                    $statusHint = $service->admin_reason;
                                } elseif ($service->admin_status === 'pending') {
                                    if ($service->negotiation_status === 'user_negotiated') {
                                        $statusText = 'Under Negotiation';
                                        $statusHint = 'Awaiting review';
                                    } elseif ($service->negotiation_status === 'admin_responded') {
                                        $statusText = 'Price Updated';
                                        $statusHint = 'Awaiting customer response';
                                    } elseif ($service->professional_status === 'pending') {
                                        $statusText = 'Pending Review';
                                        $statusHint = 'Awaiting approval';
                                    } elseif ($service->professional_status === 'approved') {
                                        $statusText = 'Awaiting Admin';
                                        $statusHint = 'Under review';
                                    }
                                }
                            @endphp
                            <tr class="align-top transition hover:bg-surface-muted/60">
                                <td class="max-w-64 px-4 py-4">
                                    <a href="{{ route('professional.additional-services.show', $service->id) }}" class="font-semibold text-text-primary hover:text-brand-primary">{{ $service->service_name }}</a>
                                    <p class="mt-1 text-xs text-text-muted">Service #{{ $service->id }}</p>
                                </td>
                                <td class="px-4 py-4 text-text-secondary">{{ $service->user->name ?? 'Customer unavailable' }}</td>
                                <td class="px-4 py-4">
                                    <span class="font-medium text-text-secondary">Booking #{{ $service->booking_id }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 font-semibold text-text-primary">₹{{ number_format($service->final_price, 2) }}</td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusTone }}">{{ $statusText }}</span>
                                    @if($statusHint)
                                        <p class="mt-1 max-w-52 text-xs leading-5 text-text-muted">{{ $statusHint }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    @if($service->payment_status === 'paid')
                                        <span class="inline-flex rounded-full bg-success-surface px-2.5 py-1 text-xs font-semibold text-success">Paid</span>
                                    @elseif($service->payment_status === 'failed')
                                        <span class="inline-flex rounded-full bg-error-surface px-2.5 py-1 text-xs font-semibold text-error">Failed</span>
                                    @elseif($service->payment_status === 'pending')
                                        <span class="inline-flex rounded-full bg-warning-surface px-2.5 py-1 text-xs font-semibold text-warning">Pending</span>
                                    @else
                                        <span class="text-text-muted">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-text-secondary">{{ optional($service->created_at)->format('M d, Y') }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex min-w-40 flex-col items-start gap-2">
                                        <a href="{{ route('professional.additional-services.show', $service->id) }}" class="inline-flex min-h-9 items-center gap-2 rounded-control border border-border px-3 py-1.5 text-xs font-semibold text-text-secondary transition hover:border-brand-primary hover:text-brand-primary">
                                            <i class="fas fa-eye" aria-hidden="true"></i> View details
                                        </a>
                                        @if($service->canSetDeliveryDate())
                                            <button type="button" class="set-delivery-date inline-flex min-h-9 items-center gap-2 rounded-control border border-border px-3 py-1.5 text-xs font-semibold text-text-secondary transition hover:border-brand-primary hover:text-brand-primary" data-id="{{ $service->id }}">
                                                <i class="fas fa-calendar-plus" aria-hidden="true"></i> Set delivery
                                            </button>
                                        @endif
                                        @if($service->canBeCompletedByProfessional())
                                            <button type="button" class="mark-completed inline-flex min-h-9 items-center gap-2 rounded-control border border-success/30 px-3 py-1.5 text-xs font-semibold text-success transition hover:bg-success-surface" data-id="{{ $service->id }}">
                                                <i class="fas fa-check" aria-hidden="true"></i> Mark completed
                                            </button>
                                        @endif
                                        @if($service->consulting_status === 'done' && $service->payment_status === 'paid')
                                            <a href="{{ route('professional.additional-services.invoice', $service->id) }}" class="inline-flex min-h-9 items-center gap-2 rounded-control border border-border px-3 py-1.5 text-xs font-semibold text-text-secondary transition hover:border-brand-primary hover:text-brand-primary">
                                                <i class="fas fa-file-invoice" aria-hidden="true"></i> View invoice
                                            </a>
                                            <a href="{{ route('professional.additional-services.invoice.pdf', $service->id) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center gap-2 rounded-control border border-border px-3 py-1.5 text-xs font-semibold text-text-secondary transition hover:border-brand-primary hover:text-brand-primary">
                                                <i class="fas fa-download" aria-hidden="true"></i> Download invoice
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-border px-5 py-4 sm:px-6">
                {{ $additionalServices->links() }}
            </div>
        @else
            <div class="px-5 py-12 text-center sm:px-6 sm:py-16">
                <span class="mx-auto flex size-14 items-center justify-center rounded-full bg-brand-50 text-brand-primary" aria-hidden="true">
                    <i class="fas fa-layer-group text-xl"></i>
                </span>
                @if($hasServices)
                    <h3 class="mt-4 text-lg font-semibold text-text-primary">No matching services</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-text-muted">Try adjusting your filters to find the service you’re looking for.</p>
                    <a href="{{ route('professional.additional-services.index') }}" class="mt-5 inline-flex min-h-10 items-center justify-center rounded-control border border-border px-4 py-2 text-sm font-semibold text-text-secondary transition hover:bg-surface-muted">Clear filters</a>
                @else
                    <h3 class="mt-4 text-lg font-semibold text-text-primary">No additional services yet</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-text-muted">Services you create for customer bookings will appear here.</p>
                    <a href="{{ route('professional.additional-services.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-control bg-brand-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">
                        <i class="fas fa-plus" aria-hidden="true"></i> Create service
                    </a>
                @endif
            </div>
        @endif
    </section>
</div>

<div id="markCompletedModal" class="additional-service-modal fixed inset-0 z-50 hidden items-center justify-center bg-brand-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="markCompletedTitle" hidden>
    <div class="w-full max-w-md rounded-panel border border-border bg-white p-5 shadow-popover sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="markCompletedTitle" class="text-lg font-semibold text-text-primary">Mark consultation completed?</h2>
                <p class="mt-2 text-sm leading-6 text-text-secondary">The customer will be notified to confirm completion.</p>
            </div>
            <button type="button" class="close-service-modal rounded-control p-2 text-text-muted hover:bg-surface-muted" aria-label="Close dialog"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button" class="close-service-modal min-h-10 rounded-control border border-border px-4 py-2 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Cancel</button>
            <button type="button" id="confirmComplete" class="min-h-10 rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Mark completed</button>
        </div>
    </div>
</div>

<div id="deliveryDateModal" class="additional-service-modal fixed inset-0 z-50 hidden items-center justify-center bg-brand-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="deliveryDateTitle" hidden>
    <form id="deliveryDateForm" class="w-full max-w-lg rounded-panel border border-border bg-white p-5 shadow-popover sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 id="deliveryDateTitle" class="text-lg font-semibold text-text-primary">Set delivery date</h2>
                <p class="mt-2 text-sm leading-6 text-text-secondary">Choose the delivery date and explain it to the customer.</p>
            </div>
            <button type="button" class="close-service-modal rounded-control p-2 text-text-muted hover:bg-surface-muted" aria-label="Close dialog"><i class="fas fa-times" aria-hidden="true"></i></button>
        </div>
        <div class="mt-5 space-y-4">
            <div>
                <label for="delivery_date" class="mb-1.5 block text-sm font-medium text-text-secondary">Delivery date</label>
                <input type="date" id="delivery_date" name="delivery_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required class="min-h-11 w-full rounded-control border border-border px-3 py-2 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20">
            </div>
            <div>
                <label for="delivery_reason" class="mb-1.5 block text-sm font-medium text-text-secondary">Reason</label>
                <textarea id="delivery_reason" name="delivery_reason" rows="3" maxlength="1000" required class="w-full rounded-control border border-border px-3 py-2 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-2 focus:ring-brand-primary/20"></textarea>
            </div>
        </div>
        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <button type="button" class="close-service-modal min-h-10 rounded-control border border-border px-4 py-2 text-sm font-semibold text-text-secondary hover:bg-surface-muted">Cancel</button>
            <button type="submit" class="min-h-10 rounded-control bg-brand-primary px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Save delivery date</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(() => {
    const dateRange = document.getElementById('filterDateRange');
    const customDateRange = document.getElementById('customDateRange');

    dateRange?.addEventListener('change', () => {
        customDateRange.classList.toggle('hidden', dateRange.value !== 'custom');
        customDateRange.classList.toggle('grid', dateRange.value === 'custom');
        document.getElementById('filterDateFrom').required = dateRange.value === 'custom';
        document.getElementById('filterDateTo').required = dateRange.value === 'custom';
    });
    if (dateRange?.value === 'custom') {
        document.getElementById('filterDateFrom').required = true;
        document.getElementById('filterDateTo').required = true;
    }

    let currentServiceId = null;
    const modalElements = document.querySelectorAll('.additional-service-modal');

    function openModal(modal) {
        modal.hidden = false;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal(modal) {
        modal.hidden = true;
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        if (![...modalElements].some((item) => !item.hidden)) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.querySelectorAll('.mark-completed').forEach((button) => {
        button.addEventListener('click', () => {
            currentServiceId = button.dataset.id;
            openModal(document.getElementById('markCompletedModal'));
        });
    });

    document.querySelectorAll('.set-delivery-date').forEach((button) => {
        button.addEventListener('click', () => {
            currentServiceId = button.dataset.id;
            openModal(document.getElementById('deliveryDateModal'));
        });
    });

    document.querySelectorAll('.close-service-modal').forEach((button) => {
        button.addEventListener('click', () => closeModal(button.closest('.additional-service-modal')));
    });

    modalElements.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) closeModal(modal);
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            modalElements.forEach((modal) => {
                if (!modal.hidden) closeModal(modal);
            });
        }
    });

    async function postAction(url, formData) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData,
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            const validationMessage = result.errors
                ? Object.values(result.errors).flat().join(' ')
                : result.message;
            throw new Error(validationMessage || 'Unable to update this service. Please try again.');
        }

        return result;
    }

    document.getElementById('confirmComplete').addEventListener('click', async (event) => {
        if (!currentServiceId) return;

        const button = event.currentTarget;
        button.disabled = true;
        try {
            const url = @json(route('professional.additional-services.mark-completed', ['id' => '__SERVICE_ID__']));
            const result = await postAction(url.replace('__SERVICE_ID__', encodeURIComponent(currentServiceId)), new FormData());
            window.YuktioneToast?.success(result.message || 'Consultation marked as completed.');
            window.location.reload();
        } catch (error) {
            window.YuktioneToast?.error(error.message);
        } finally {
            button.disabled = false;
        }
    });

    document.getElementById('deliveryDateForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!currentServiceId) return;

        const form = event.currentTarget;
        const button = form.querySelector('[type="submit"]');
        button.disabled = true;
        try {
            const url = @json(route('professional.additional-services.set-delivery-date', ['id' => '__SERVICE_ID__']));
            const result = await postAction(url.replace('__SERVICE_ID__', encodeURIComponent(currentServiceId)), new FormData(form));
            window.YuktioneToast?.success(result.message || 'Delivery date saved.');
            window.location.reload();
        } catch (error) {
            window.YuktioneToast?.error(error.message);
        } finally {
            button.disabled = false;
        }
    });
})();
</script>
@endsection
