@props(['records' => null])

@php
    $whyChooseRecords = collect($records ?? []);
@endphp

<section aria-labelledby="tazen-v2-why-choose-title" class="bg-background py-12 sm:py-14 lg:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($whyChooseRecords->isEmpty())
            <div class="rounded-card border border-border bg-surface px-5 py-8 text-center sm:px-8">
                <h2 id="tazen-v2-why-choose-title" class="font-display text-3xl font-semibold text-brand-secondary">Why Choose Yuktione?</h2>
                <p class="mt-3 text-sm text-text-secondary">This information is not available right now.</p>
            </div>
        @else
            @foreach ($whyChooseRecords as $record)
                @php
                    $benefits = collect([
                        ['eyebrow' => $record->card1_mini_heading, 'heading' => $record->card1_heading, 'description' => $record->card1_description],
                        ['eyebrow' => $record->card2_mini_heading, 'heading' => $record->card2_heading, 'description' => $record->card2_description],
                        ['eyebrow' => $record->card3_mini_heading, 'heading' => $record->card3_heading, 'description' => $record->card3_description],
                        ['eyebrow' => $record->card4_mini_heading, 'heading' => $record->card4_heading, 'description' => $record->card4_description],
                    ])->filter(fn ($benefit) => filled($benefit['heading']) || filled($benefit['description']))->values();
                @endphp

                <div class="mb-8 max-w-3xl sm:mb-10">
                    <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Why Yuktione</p>
                    <h2 id="tazen-v2-why-choose-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                        {{ $record->section_heading }}
                    </h2>
                    @if (filled($record->section_sub_heading))
                        <p class="mt-3 text-base leading-7 text-text-secondary">{{ $record->section_sub_heading }}</p>
                    @endif
                </div>

                @if ($benefits->isEmpty())
                    <p class="rounded-card border border-border bg-surface px-5 py-8 text-center text-sm text-text-secondary">
                        Benefit information is not available right now.
                    </p>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 xl:grid-cols-4">
                        @foreach ($benefits as $benefit)
                            <article aria-labelledby="tazen-v2-benefit-{{ $loop->parent->index }}-{{ $loop->index }}" class="h-full">
                                <x-tazen-v2.ui.card padding="md" class="group h-full border-t-2 border-t-brand-primary transition-shadow hover:shadow-popover">
                                    @if (filled($benefit['eyebrow']))
                                        <p class="mb-3 text-xs font-semibold uppercase text-brand-primary">{{ $benefit['eyebrow'] }}</p>
                                    @endif
                                    @if (filled($benefit['heading']))
                                        <h3 id="tazen-v2-benefit-{{ $loop->parent->index }}-{{ $loop->index }}" class="font-display text-card-title font-semibold leading-snug text-brand-secondary">
                                            {{ $benefit['heading'] }}
                                        </h3>
                                    @endif
                                    @if (filled($benefit['description']))
                                        <p class="mt-3 text-sm leading-6 text-text-secondary">{{ $benefit['description'] }}</p>
                                    @endif
                                </x-tazen-v2.ui.card>
                            </article>
                        @endforeach
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</section>