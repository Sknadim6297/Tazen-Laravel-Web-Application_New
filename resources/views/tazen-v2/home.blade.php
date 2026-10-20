@extends('tazen-v2.layouts.app')

@section('title', 'Yuktione | Expert Consultations')
@section('meta_description', 'Explore expert services and consultations with Yuktione.')

@section('header')
    <x-tazen-v2.layout.navbar />
@endsection

@section('content')
    <x-tazen-v2.home.hero :services="$services" />

    <x-tazen-v2.home.categories :categories="$services" />

    <x-tazen-v2.home.why-choose-us :records="$whychooses" />

    <section aria-labelledby="tazen-v2-featured-experts-title" class="bg-background py-14 sm:py-18 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8 max-w-2xl sm:mb-10">
                <p class="mb-3 text-sm font-semibold uppercase text-brand-primary">Meet the experts</p>
                <h2 id="tazen-v2-featured-experts-title" class="font-display text-3xl font-semibold leading-tight text-brand-secondary sm:text-4xl">
                    Featured professionals
                </h2>
                <p class="mt-3 text-base leading-7 text-text-secondary">
                    Explore professionals currently active on Yuktione.
                </p>
            </div>

            @if ($professionals->isEmpty())
                <div class="rounded-card border border-border bg-surface px-5 py-8 text-center sm:px-8">
                    <p class="text-base font-medium text-text-primary">No active professionals are available to feature right now.</p>
                    <a href="{{ route('gridlisting') }}" class="mt-4 inline-flex min-h-10 items-center rounded-control px-3 text-sm font-semibold text-brand-primary underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring">
                        Browse experts
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-3 lg:gap-6">
                    @foreach ($professionals as $professional)
                        <x-tazen-v2.professional.card :professional="$professional" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <x-tazen-v2.home.events :events="$events" />

    <x-tazen-v2.home.how-it-works :records="$howworks" />

    <x-tazen-v2.home.recent-blogs :posts="$recentBlogPosts" />

    <x-tazen-v2.home.faq :faqs="$faqs" />
@endsection

@section('footer')
    <x-tazen-v2.layout.footer />
@endsection