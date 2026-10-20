@extends('customer.layout.layout')

@section('title', 'Billing & Invoices | Yuktione')

@section('content')
@php
    $hasActiveFilters = request()->filled('start_date')
        || request()->filled('end_date')
        || request()->filled('service')
        || request()->filled('plan_type');
@endphp

<div class="mx-auto grid max-w-7xl gap-6">
    <x-tazen-v2.panel.breadcrumbs :items="[
        ['label' => 'Dashboard', 'url' => route('user.dashboard')],
        ['label' => 'Billing'],
    ]" />

    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-brand-primary">Account</p>
            <h1 class="mt-1 font-display text-page-title font-semibold text-brand-secondary">Billing &amp; Invoices</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary sm:text-base">
                Track your consultation payments, view transaction history and download invoices.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex min-h-9 items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1.5 text-sm font-semibold text-brand-secondary">
                <x-tazen-v2.panel.icon name="wallet" class="size-4 text-brand-primary" />
                {{ $bookings->count() }} {{ Str::plural('transaction', $bookings->count()) }}
            </span>
            <a href="{{ route('user.billing.export-all', request()->all()) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control border border-brand-primary bg-brand-primary px-4 text-sm font-semibold text-white transition-colors hover:border-brand-800 hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v10.5m0 0 4.5-4.5m-4.5 4.5-4.5-4.5M4.5 15.75v3A1.5 1.5 0 0 0 6 20.25h12a1.5 1.5 0 0 0 1.5-1.5v-3" />
                </svg>
                Export as PDF
            </a>
        </div>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Billing summary">
        <article class="flex items-center gap-4 rounded-panel border border-border bg-surface p-5 shadow-card">
            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-card bg-brand-50 text-brand-primary">
                <x-tazen-v2.panel.icon name="wallet" class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-text-secondary">Total Paid</p>
                <p class="mt-1 break-words font-display text-xl font-semibold text-brand-secondary">
                    {{ getCurrencySymbol() }}{{ number_format(convertCurrency($bookings->sum('amount')), 2) }}
                </p>
            </div>
        </article>
        <article class="flex items-center gap-4 rounded-panel border border-border bg-surface p-5 shadow-card">
            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-card bg-brand-50 text-brand-primary">
                <x-tazen-v2.panel.icon name="spark" class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-text-secondary">Unique Services</p>
                <p class="mt-1 font-display text-xl font-semibold text-brand-secondary">{{ $bookings->pluck('service_name')->filter()->unique()->count() }}</p>
            </div>
        </article>
        <article class="flex items-center gap-4 rounded-panel border border-border bg-surface p-5 shadow-card sm:col-span-2 xl:col-span-1">
            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-card bg-brand-50 text-brand-primary">
                <x-tazen-v2.panel.icon name="user" class="size-5" />
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-text-secondary">Professionals</p>
                <p class="mt-1 font-display text-xl font-semibold text-brand-secondary">{{ $bookings->pluck('professional_id')->filter()->unique()->count() }}</p>
            </div>
        </article>
    </section>

    <section class="rounded-panel border border-border bg-surface p-5 shadow-card sm:p-6" aria-labelledby="transaction-filters-heading">
        <div class="mb-5">
            <h2 id="transaction-filters-heading" class="font-display text-card-title font-semibold text-brand-secondary">Filter Transactions</h2>
            <p class="mt-1 text-sm text-text-secondary">Narrow your billing history by date, service or plan.</p>
        </div>
        <form action="{{ route('user.billing.index') }}" method="GET" id="filter-form" class="grid grid-cols-1 items-end gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="grid gap-1.5">
                <label for="start_date" class="text-sm font-semibold text-text-primary">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
            </div>
            <div class="grid gap-1.5">
                <label for="end_date" class="text-sm font-semibold text-text-primary">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                    class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
            </div>
            <div class="grid gap-1.5">
                <label for="service" class="text-sm font-semibold text-text-primary">Service</label>
                <select id="service" name="service" class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
                    <option value="">All Services</option>
                    @foreach($services as $service)
                        <option value="{{ $service }}" @selected(request('service') == $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-1.5">
                <label for="plan_type" class="text-sm font-semibold text-text-primary">Plan Type</label>
                <select id="plan_type" name="plan_type" class="min-h-11 w-full rounded-control border border-border bg-surface px-3 py-2 text-sm text-text-primary focus-visible:border-brand-primary">
                    <option value="">All Plans</option>
                    <option value="monthly" @selected(request('plan_type') == 'monthly')>Monthly</option>
                    <option value="quarterly" @selected(request('plan_type') == 'quarterly')>Quarterly</option>
                    <option value="one_time" @selected(request('plan_type') == 'one_time')>One Time</option>
                    <option value="free_hand" @selected(request('plan_type') == 'free_hand')>Free Hand</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 sm:col-span-2 sm:flex-row xl:col-span-4">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-control border border-brand-primary bg-brand-primary px-4 text-sm font-semibold text-white transition-colors hover:border-brand-800 hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    <x-tazen-v2.panel.icon name="search" class="size-4" />
                    Apply Filters
                </button>
                <a href="{{ route('user.billing.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary transition-colors hover:bg-surface-muted focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                    Reset
                </a>
            </div>
        </form>
    </section>

    <section class="overflow-hidden rounded-panel border border-border bg-surface shadow-card" aria-labelledby="all-transactions-heading">
        <div class="flex flex-col gap-1 border-b border-divider px-5 py-4 sm:px-6">
            <h2 id="all-transactions-heading" class="font-display text-card-title font-semibold text-brand-secondary">All Transactions</h2>
            <p class="text-sm text-text-secondary">{{ $bookings->count() }} {{ Str::plural('transaction', $bookings->count()) }} found</p>
        </div>

        @if($bookings->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] border-collapse text-left text-sm">
                    <thead class="bg-surface-muted text-xs font-semibold uppercase tracking-wide text-text-secondary">
                        <tr>
                            <th scope="col" class="w-16 px-5 py-3.5">No.</th>
                            <th scope="col" class="w-32 px-5 py-3.5">Date</th>
                            <th scope="col" class="min-w-48 px-5 py-3.5">Service Taken</th>
                            <th scope="col" class="min-w-48 px-5 py-3.5">Professional</th>
                            <th scope="col" class="w-36 px-5 py-3.5">Plan Type</th>
                            <th scope="col" class="w-36 px-5 py-3.5">Amount</th>
                            <th scope="col" class="w-32 px-5 py-3.5">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($bookings as $index => $booking)
                            @php
                                $planKey = str_replace('_', '-', $booking->plan_type);
                            @endphp
                            <tr class="transition-colors hover:bg-surface-muted/70">
                                <td class="px-5 py-4 text-text-secondary">{{ $index + 1 }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-text-primary">{{ $booking->created_at?->format('d M Y') ?? '—' }}</td>
                                <td class="px-5 py-4 font-medium text-text-primary">{{ $booking->service_name ?: 'Not provided' }}</td>
                                <td class="px-5 py-4 text-text-primary">{{ $booking->professional->name ?? 'Not provided' }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full border border-brand-200 bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-secondary">
                                        {{ $booking->plan_type ? ucwords(str_replace('_', ' ', $booking->plan_type)) : 'Not provided' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-brand-secondary">{{ getCurrencySymbol() }}{{ number_format(convertCurrency($booking->amount), 2) }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('user.billing.download', $booking->id) }}" class="inline-flex size-10 items-center justify-center rounded-control border border-border bg-surface text-brand-primary transition-colors hover:border-brand-200 hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring" aria-label="Download invoice for transaction {{ $booking->id }}" title="Download invoice">
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75v10.5m0 0 4.5-4.5m-4.5 4.5-4.5-4.5M4.5 15.75v3A1.5 1.5 0 0 0 6 20.25h12a1.5 1.5 0 0 0 1.5-1.5v-3" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-5 sm:p-8">
                <x-tazen-v2.panel.empty-state
                    icon="wallet"
                    :title="$hasActiveFilters ? 'No Matching Transactions' : 'No Transactions Yet'"
                    :description="$hasActiveFilters
                        ? 'No transactions match these filters. Adjust your selections or clear all filters to view your billing history.'
                        : 'Your consultation payments and invoices will appear here when you have a completed booking.'"
                >
                    @if($hasActiveFilters)
                        <x-slot:actions>
                            <a href="{{ route('user.billing.index') }}" class="inline-flex min-h-10 items-center justify-center rounded-control border border-border bg-surface px-4 text-sm font-semibold text-text-primary hover:bg-surface-muted">
                                Clear Filters
                            </a>
                        </x-slot:actions>
                    @endif
                </x-tazen-v2.panel.empty-state>
            </div>
        @endif
    </section>
</div>
@endsection
