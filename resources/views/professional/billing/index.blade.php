@extends('professional.layout.layout')

@section('title', 'Billing & Payments | Yuktione')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-6">
    <header class="flex flex-col gap-1">
        <p class="text-xs font-semibold uppercase tracking-wide text-brand-700">Billing</p>
        <h1 class="text-2xl font-semibold leading-tight text-brand-950">Billing &amp; Payments</h1>
        <p class="text-sm text-text-muted">Review your customer booking charges, earnings and billing activity.</p>
    </header>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3" aria-label="Billing summary">
        <article class="rounded-panel border border-divider bg-white p-4 shadow-card">
            <p class="text-xs font-medium text-text-muted">Gross booking value</p>
            <p class="mt-2 text-xl font-semibold text-brand-950">₹{{ number_format($totalGrossAmount, 2) }}</p>
            <p class="mt-1 text-[11px] text-text-muted">Customer charges across listed bookings</p>
        </article>
        <article class="rounded-panel border border-divider bg-white p-4 shadow-card">
            <p class="text-xs font-medium text-text-muted">Platform deductions</p>
            <p class="mt-2 text-xl font-semibold text-text-primary">₹{{ number_format($totalMarginDeducted, 2) }}</p>
            <p class="mt-1 text-[11px] text-text-muted">Platform fee and applicable fee taxes</p>
        </article>
        <article class="rounded-panel border border-divider bg-white p-4 shadow-card sm:col-span-2 xl:col-span-1">
            <p class="text-xs font-medium text-text-muted">Professional earnings</p>
            <p class="mt-2 text-xl font-semibold text-success">₹{{ number_format($totalNetAmount, 2) }}</p>
            <p class="mt-1 text-[11px] text-text-muted">After the deductions shown above</p>
        </article>
    </section>

    <section class="rounded-panel border border-divider bg-white shadow-card" aria-labelledby="billing-filters-title">
        <div class="border-b border-divider px-4 py-3.5 sm:px-5">
            <h2 id="billing-filters-title" class="text-sm font-semibold text-brand-950">Filter billing activity</h2>
        </div>
        <form action="{{ route('professional.billing.index') }}" method="GET" class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-5">
            <div class="space-y-1.5">
                <label for="start_date" class="block text-xs font-medium text-text-primary">From date</label>
                <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
            </div>
            <div class="space-y-1.5">
                <label for="end_date" class="block text-xs font-medium text-text-primary">To date</label>
                <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
            </div>
            <div class="space-y-1.5">
                <label for="plan_type" class="block text-xs font-medium text-text-primary">Session type</label>
                <select id="plan_type" name="plan_type" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <option value="">All types</option>
                    <option value="monthly" @selected(request('plan_type') === 'monthly')>Monthly</option>
                    <option value="quarterly" @selected(request('plan_type') === 'quarterly')>Quarterly</option>
                    <option value="free_hand" @selected(request('plan_type') === 'free_hand')>Free hand</option>
                    <option value="one_time" @selected(request('plan_type') === 'one_time')>One time</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="service" class="block text-xs font-medium text-text-primary">Service</label>
                <select id="service" name="service" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <option value="">All services</option>
                    @foreach($services as $service)
                        <option value="{{ $service }}" @selected(request('service') === $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-1.5">
                <label for="payment_status" class="block text-xs font-medium text-text-primary">Payment status</label>
                <select id="payment_status" name="payment_status" class="w-full rounded-control border border-border bg-white px-3 py-2.5 text-sm text-text-primary focus:border-brand-primary focus:outline-none focus:ring-4 focus:ring-brand-100">
                    <option value="">All statuses</option>
                    <option value="paid" @selected(request('payment_status') === 'paid')>Paid</option>
                    <option value="unpaid" @selected(request('payment_status') === 'unpaid')>Unpaid</option>
                </select>
            </div>
            <div class="flex flex-wrap gap-2 sm:col-span-2 xl:col-span-5">
                <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-control bg-brand-primary px-4 py-2 text-xs font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">Apply filters</button>
                <a href="{{ route('professional.billing.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-white px-4 py-2 text-xs font-semibold text-text-secondary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">Reset</a>
            </div>
        </form>
    </section>

    <section class="space-y-3" aria-labelledby="billing-history-title">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h2 id="billing-history-title" class="text-base font-semibold text-brand-950">Billing activity</h2>
                <p class="mt-1 text-xs text-text-muted">Booking transactions and their available invoices.</p>
            </div>
            <span class="text-xs text-text-muted">{{ $bookings->count() }} {{ \Illuminate\Support\Str::plural('record', $bookings->count()) }}</span>
        </div>

        @if($bookings->isEmpty() && $subscriptionHistory->isEmpty())
            <div class="rounded-panel border border-dashed border-border bg-white px-5 py-10 text-center">
                <div class="mx-auto flex size-11 items-center justify-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none"><path d="M6 3.75h8.25L19.5 9v11.25H6V3.75Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M14 4v5h5M9 13h7M9 16.5h7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-brand-950">No Billing History Yet</h3>
                <p class="mt-1 text-xs text-text-muted">Your billing activity will appear here when booking records are available.</p>
            </div>
        @elseif($bookings->isEmpty())
            <p class="rounded-panel border border-divider bg-white px-4 py-5 text-center text-xs text-text-muted">There are no booking transactions for these filters.</p>
        @else
            <div class="space-y-3">
                @foreach($bookings as $booking)
                    <article class="rounded-panel border border-divider bg-white p-4 shadow-card sm:p-5">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-semibold text-brand-950">{{ $booking->service_name ?: 'Booking' }}</h3>
                                    <x-yuktione.status-badge :status="$booking->paid_status ?: 'unpaid'" />
                                    @if($booking->plan_type)
                                        <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-medium text-brand-800">{{ ucwords(str_replace('_', ' ', $booking->plan_type)) }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-text-muted">{{ $booking->customer_name ?: 'Customer' }} · {{ $booking->created_at?->format('d M Y') }}</p>
                                @if($booking->transaction_number)
                                    <p class="mt-1 break-all text-[11px] text-text-muted">Transaction: {{ $booking->transaction_number }}</p>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-x-5 gap-y-2 text-xs sm:min-w-64">
                                <span class="text-text-muted">Customer amount</span><span class="text-right font-medium text-text-primary">₹{{ number_format($booking->customer_amount, 2) }}</span>
                                <span class="text-text-muted">Platform fee</span><span class="text-right text-text-secondary">₹{{ number_format($booking->platform_fee, 2) }}</span>
                                <span class="text-text-muted">Fee taxes</span><span class="text-right text-text-secondary">₹{{ number_format($booking->platform_cgst + $booking->platform_sgst, 2) }}</span>
                                <span class="border-t border-divider pt-2 font-semibold text-brand-950">Your earnings</span><span class="border-t border-divider pt-2 text-right font-semibold text-success">₹{{ number_format($booking->professional_earning, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 border-t border-divider pt-3">
                            <a href="{{ route('professional.billing.download-invoice', ['booking' => $booking->id]) }}" class="inline-flex min-h-9 items-center justify-center rounded-control border border-border px-3 py-1.5 text-xs font-medium text-brand-800 transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                                Download your invoice
                            </a>
                            <a href="{{ route('professional.billing.customer-invoice.view', ['booking' => $booking->id]) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center justify-center rounded-control border border-border px-3 py-1.5 text-xs font-medium text-text-secondary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                                View customer invoice
                            </a>
                            <a href="{{ route('professional.billing.customer-invoice.download', ['booking' => $booking->id]) }}" class="inline-flex min-h-9 items-center justify-center rounded-control border border-border px-3 py-1.5 text-xs font-medium text-text-secondary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                                Download customer invoice
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    @if($subscriptionHistory->isNotEmpty())
        <section class="overflow-hidden rounded-panel border border-divider bg-white shadow-card" aria-labelledby="subscription-payments-title">
            <div class="border-b border-divider px-4 py-3.5 sm:px-5">
                <h2 id="subscription-payments-title" class="text-sm font-semibold text-brand-950">Subscription payments</h2>
                <p class="mt-1 text-xs text-text-muted">Plan purchases and their recorded payment status.</p>
            </div>
            <div class="divide-y divide-divider">
                @foreach($subscriptionHistory as $purchase)
                    <article class="grid gap-2 px-4 py-3.5 sm:grid-cols-[minmax(0,1fr)_auto_auto] sm:items-center sm:px-5">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-xs font-semibold text-text-primary">{{ $purchase->plan_name }}</h3>
                                <x-yuktione.status-badge :status="$purchase->payment_status" />
                            </div>
                            <p class="mt-1 text-[11px] text-text-muted">
                                {{ $purchase->created_at->format('d M Y') }}
                                @if($purchase->payment_method) · {{ ucfirst($purchase->payment_method) }} @endif
                                @if($purchase->payment_id) · Payment ID {{ $purchase->payment_id }} @endif
                            </p>
                            @if($purchase->payment_screenshot)
                                <a href="{{ asset('storage/'.$purchase->payment_screenshot) }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex text-[11px] font-medium text-brand-700 underline underline-offset-2">View submitted payment receipt</a>
                            @endif
                        </div>
                        <p class="text-sm font-semibold text-brand-950">{{ $purchase->formatted_price }}</p>
                        @if($purchase->start_date || $purchase->end_date)
                            <p class="text-[11px] text-text-muted">
                                @if($purchase->start_date) Starts {{ $purchase->start_date->format('d M Y') }} @endif
                                @if($purchase->end_date) · Ends {{ $purchase->end_date->format('d M Y') }} @endif
                            </p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection
