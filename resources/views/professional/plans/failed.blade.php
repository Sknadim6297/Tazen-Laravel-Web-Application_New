@extends('professional.layout.layout')

@section('title', 'Payment Not Verified | Yuktione')

@section('content')
<main class="mx-auto w-full max-w-3xl px-4 py-8 sm:px-6 lg:px-8" aria-labelledby="payment-failed-title">
    <section class="rounded-panel border border-border bg-surface p-6 text-center shadow-card sm:p-10">
        <div class="mx-auto mb-5 grid size-14 place-items-center rounded-full bg-amber-50 text-amber-700" aria-hidden="true">
            <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v4.5m0 3h.008v.008H12v-.008ZM10.3 3.9 2.9 17.1a1.5 1.5 0 0 0 1.31 2.24h15.58a1.5 1.5 0 0 0 1.31-2.24L13.7 3.9a1.95 1.95 0 0 0-3.4 0Z" />
            </svg>
        </div>

        <h1 id="payment-failed-title" class="text-2xl font-semibold tracking-tight text-text-primary sm:text-3xl">
            We couldn’t verify your payment
        </h1>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-text-secondary sm:text-base">
            Check your plan status before trying again. If you were charged, please contact support so we can help confirm the payment.
        </p>

        <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
            <a href="{{ route('professional.plans.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-control bg-brand-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                View available plans
            </a>
            <a href="{{ route('professional.plans.my-plan') }}" class="inline-flex min-h-11 items-center justify-center rounded-control border border-brand-200 bg-white px-5 py-2.5 text-sm font-semibold text-brand-primary transition hover:bg-brand-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                Check my plan
            </a>
        </div>
    </section>
</main>
@endsection
