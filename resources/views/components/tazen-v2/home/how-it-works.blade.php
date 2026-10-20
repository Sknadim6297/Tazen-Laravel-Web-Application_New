@props(['records' => null])

@php
    $howItWorksRecords = collect($records ?? []);
    $resolveStepImage = static function ($path): ?string {
        if (!filled($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parts = parse_url($path);
            $scheme = strtolower($parts['scheme'] ?? '');
            $host = strtolower($parts['host'] ?? '');
            $urlPath = $parts['path'] ?? '';

            if (!in_array($scheme, ['http', 'https'], true)) {
                return null;
            }

            if (!in_array($host, ['localhost', '127.0.0.1'], true) || !str_starts_with($urlPath, '/storage/')) {
                return $path;
            }

            $path = substr($urlPath, strlen('/storage/'));
        }

        $path = ltrim($path, '/');
        $storagePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;

        if (is_file(public_path($path))) {
            return asset($path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)
            && is_file(public_path('storage/' . $storagePath))) {
            return asset('storage/' . $storagePath);
        }

        return null;
    };
@endphp

<section aria-labelledby="tazen-v2-how-it-works-title" class="bg-surface-muted py-10 sm:py-12 lg:py-14">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto mb-6 max-w-2xl text-center sm:mb-8">
            <p class="mb-2 text-xs font-semibold uppercase text-brand-primary">A simple process</p>
            <h2 id="tazen-v2-how-it-works-title" class="font-display text-2xl font-semibold leading-tight text-brand-secondary sm:text-3xl">
                How it works
            </h2>
        </div>

        @if ($howItWorksRecords->isEmpty())
            <div class="rounded-card border border-border bg-surface px-5 py-8 text-center sm:px-8">
                <p class="text-base text-text-secondary">How it works information is not available right now.</p>
            </div>
        @else
            <div class="grid gap-6">
                @foreach ($howItWorksRecords as $record)
                    @if (filled($record->section_sub_heading))
                        <p class="mx-auto max-w-2xl text-center text-sm leading-6 text-text-secondary">
                            {{ $record->section_sub_heading }}
                        </p>
                    @endif

                    @php
                        $steps = [
                            ['heading' => $record->heading1, 'description' => $record->description1, 'image' => $record->image1],
                            ['heading' => $record->heading2, 'description' => $record->description2, 'image' => $record->image2],
                            ['heading' => $record->heading3, 'description' => $record->description3, 'image' => $record->image3],
                        ];
                    @endphp

                    <ol class="grid grid-cols-1 gap-y-8 sm:grid-cols-2 sm:gap-x-5 sm:gap-y-8 lg:grid-cols-3 lg:gap-x-10 lg:gap-y-6">
                        @foreach ($steps as $step)
                            @if (filled($step['heading']) || filled($step['description']) || filled($step['image']))
                                @php
                                    $stepImage = $resolveStepImage($step['image']);
                                @endphp
                                <li class="relative h-full">
                                    <article class="flex h-full flex-col rounded-card border border-border bg-surface p-4 shadow-card sm:p-5">
                                        <div class="flex items-center gap-3">
                                            <span aria-hidden="true" class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-50 font-display text-xs font-semibold text-brand-primary">
                                                {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            @if (filled($step['heading']))
                                                <h3 class="font-display text-base font-semibold leading-snug text-brand-secondary sm:text-lg">
                                                    {{ $step['heading'] }}
                                                </h3>
                                            @endif
                                        </div>

                                        <div class="mt-3 flex h-28 items-center justify-center overflow-hidden rounded-control bg-surface-muted p-2">
                                            @if ($stepImage)
                                                <img
                                                    src="{{ $stepImage }}"
                                                    alt="{{ $step['heading'] ?: 'How it works step ' . $loop->iteration }}"
                                                    width="640"
                                                    height="480"
                                                    loading="lazy"
                                                    class="h-full max-h-28 w-full object-contain"
                                                >
                                            @else
                                                <span aria-hidden="true" class="size-12 rounded-full bg-brand-100"></span>
                                            @endif
                                        </div>

                                        @if (filled($step['description']))
                                            <p class="mt-3 text-sm leading-5 text-text-secondary">
                                                {{ $step['description'] }}
                                            </p>
                                        @endif
                                    </article>

                                    @if (! $loop->last)
                                        <span aria-hidden="true" class="pointer-events-none absolute left-1/2 top-full z-10 mt-1 flex size-7 -translate-x-1/2 items-center justify-center rounded-full border border-brand-200 bg-surface text-base font-semibold text-brand-primary sm:hidden">↓</span>
                                        <span aria-hidden="true" class="pointer-events-none absolute right-0 top-1/2 z-10 hidden size-8 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-brand-200 bg-surface text-lg font-semibold text-brand-primary lg:flex">→</span>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ol>
                @endforeach
            </div>
        @endif
    </div>
</section>