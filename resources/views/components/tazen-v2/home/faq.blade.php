@props(['faqs' => null])

@php
    $faqCollection = collect($faqs ?? []);
@endphp

<section aria-labelledby="yuktione-faq-title" class="bg-surface py-14 sm:py-18 lg:py-24">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mb-7 max-w-2xl sm:mb-9">
            <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Help and support</p>
            <h2 id="yuktione-faq-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                Frequently Asked Questions
            </h2>
        </div>

        @if ($faqCollection->isEmpty())
            <div class="rounded-card border border-border bg-surface-muted px-5 py-7 text-center sm:px-8">
                <p class="text-sm leading-6 text-text-secondary">There are no published answers available right now.</p>
            </div>
        @else
            <div class="grid gap-3">
                @foreach ($faqCollection as $faq)
                    <details name="yuktione-faq" class="group rounded-card border border-border bg-surface px-4 transition-colors open:border-brand-200 open:bg-brand-50/50 sm:px-5">
                        <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 rounded-control py-3 font-display text-base font-semibold text-brand-secondary marker:hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring [&::-webkit-details-marker]:hidden sm:text-lg">
                            <span>{{ $faq->question }}</span>
                            <span aria-hidden="true" class="inline-flex size-8 shrink-0 items-center justify-center rounded-full border border-brand-200 bg-surface text-lg leading-none text-brand-primary">
                                <span class="group-open:hidden">+</span>
                                <span class="hidden group-open:inline">-</span>
                            </span>
                        </summary>
                        <div class="border-t border-divider py-4 text-sm leading-6 text-text-secondary sm:py-5">
                            {{ $faq->answer }}
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </div>
</section>