@extends('layouts.layout')

@section('title', 'Tools | YuktiOne')
@section('meta_description', 'Practical tools and resources to support your mental, emotional and personal growth.')
@section('body_class', 'yk-toolpage-body')

@php
    $base = request()->except('page');
    $link = function (array $set = [], array $forget = []) use ($base) {
        $params = $base;
        foreach ($forget as $key) {
            unset($params[$key]);
        }
        foreach ($set as $key => $value) {
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }

        return route('tools.index', $params);
    };
    $filtering = $filters['q'] !== '' || $filters['category'] !== '';
@endphp

@section('content')
<main class="yk-toolpage" id="tools">
    <section class="yk-toolpage__hero" aria-labelledby="tools-heading">
        <div class="yk-toolpage__wrap yk-toolpage__hero-grid">
            <div>
                <nav class="yk-toolpage__crumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span aria-hidden="true">›</span>
                    <span aria-current="page">Tools</span>
                </nav>
                <h1 id="tools-heading">Tools for a<br><span>brighter you.</span></h1>
                <p class="yk-toolpage__lead">Practical tools and resources to support your mental, emotional and personal growth.</p>
                <form class="yk-toolpage__search" action="{{ route('tools.index') }}" method="get" role="search">
                    @if ($filters['category'] !== '')
                        <input type="hidden" name="category" value="{{ $filters['category'] }}">
                    @endif
                    <label class="visually-hidden" for="tools-search">Search tools, topics, or categories</label>
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="6"/><path d="m16 16 4 4"/></svg>
                    <input id="tools-search" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Search tools, topics, or categories..." autocomplete="off" maxlength="100">
                    <button type="submit" aria-label="Search">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 12h12"/><path d="m13 6 6 6-6 6"/></svg>
                    </button>
                </form>
                <div class="yk-toolpage__topics">
                    <span>Popular searches:</span>
                    <ul>
                        @foreach ($topics as $topic)
                            <li><a href="{{ $link(['q' => $topic]) }}">{{ $topic }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="yk-toolpage__art" aria-hidden="true">
                <svg viewBox="0 0 420 320">
                    <ellipse cx="250" cy="170" rx="120" ry="130" fill="#e7f6ef"/>
                    <path d="M40 250c40-80 90-120 150-110-30 50-70 80-150 110Z" fill="#7ea36a"/>
                    <path d="M70 80c50-20 90 10 100 50-40 10-80 0-100-50Z" fill="#9d81db" opacity=".85"/>
                    <path d="M300 40c30 50 20 100-20 130-10-50-10-90 20-130Z" fill="#8fb67c"/>
                    <path d="M330 180c40 10 70 40 70 80-50-10-80-30-70-80Z" fill="#b7a4e4"/>
                    <path d="M20 200c30-10 50 10 55 40-30 5-50-10-55-40Z" fill="#316d61" opacity=".75"/>
                    <path d="M360 90c20-30 50-30 70-10-30 8-50 16-70 10Z" fill="#a9d0c0"/>
                </svg>
            </div>
        </div>
    </section>

    <div class="yk-toolpage__wrap">
        <nav class="yk-toolpage__cats" aria-label="Tool categories">
            <a href="{{ $link(['category' => null]) }}" @class(['is-all', 'is-active' => $filters['category'] === ''])>
                <span>@include('frontend.sections.partials.explore-icon', ['icon' => 'grid'])</span>
                All
            </a>
            @foreach ($categories as $slug => $category)
                <a href="{{ $link(['category' => $slug]) }}" @class(['is-active' => $filters['category'] === $slug, 'is-'.$category['tone']])>
                    <span>@include('frontend.sections.partials.explore-icon', ['icon' => $category['icon']])</span>
                    {{ $category['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="yk-toolpage__layout">
            <aside class="yk-toolpage__sidebar">
                <div class="yk-toolpage__sidebar-head">
                    <h2>Filters</h2>
                    <a href="{{ route('tools.index') }}">Reset</a>
                </div>
                <fieldset>
                    <legend>Category</legend>
                    @foreach ($categories as $slug => $category)
                        <a href="{{ $link(['category' => $filters['category'] === $slug ? null : $slug]) }}" @if ($filters['category'] === $slug) aria-current="true" @endif>
                            <span class="yk-toolpage__tick" aria-hidden="true"></span>
                            <span>{{ $category['label'] }}</span>
                            <small>({{ $counts[$slug] ?? 0 }})</small>
                        </a>
                    @endforeach
                </fieldset>
            </aside>

            <section class="yk-toolpage__results" aria-label="Tool results">
                <div class="yk-toolpage__toolbar">
                    <p>Showing {{ $items->total() }} {{ $items->total() === 1 ? 'tool' : 'tools' }}</p>
                    <button class="yk-toolpage__filter-open" type="button" data-tools-open>Filters</button>
                </div>
                @if ($items->isEmpty())
                    <div class="yk-toolpage__empty">
                        <span class="yk-toolpage__empty-mark" aria-hidden="true">
                            <svg viewBox="0 0 24 24" focusable="false"><rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><path d="M16.75 13.5v6.5M13.5 16.75h6.5"/></svg>
                        </span>
                        @if ($filtering)
                            <h2>No tools match these filters</h2>
                            <p>Try another topic, or <a href="{{ route('tools.index') }}">reset Tools</a>.</p>
                        @else
                            <h2>No published tools yet</h2>
                            <p>When a tool is ready for everyone, it will appear in this catalogue.</p>
                        @endif
                    </div>
                @else
                    <ul class="yk-toolpage__grid">
                        @foreach ($items as $tool)
                            <li>
                                <article class="yk-toolcard">
                                    <a class="yk-toolcard__media" href="{{ $tool['url'] }}">
                                        <span class="yk-toolcard__mark" aria-hidden="true">
                                            @if ($tool['icon'] === 'mood')
                                                <svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="12" r="8"/><path d="M9 10h.01M15 10h.01"/><path d="M9 14.2c.8 1 1.8 1.5 3 1.5s2.2-.5 3-1.5"/></svg>
                                            @else
                                                @include('frontend.sections.partials.explore-icon', ['icon' => 'leaf'])
                                            @endif
                                        </span>
                                        <span class="yk-toolcard__kicker">{{ $tool['access'] }}</span>
                                    </a>
                                    <div class="yk-toolcard__body">
                                        <h3><a href="{{ $tool['url'] }}">{{ $tool['name'] }}</a></h3>
                                        @if ($tool['description'] !== '')
                                            <p>{{ $tool['description'] }}</p>
                                        @endif
                                        <a class="yk-toolcard__cta" href="{{ $tool['url'] }}">{{ $tool['cta'] }} <span aria-hidden="true">→</span></a>
                                    </div>
                                </article>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
    <button class="yk-toolpage__backdrop" type="button" data-tools-close aria-label="Close filters"></button>
</main>
<script>
(function () {
    var root = document.querySelector('.yk-toolpage');
    if (!root) return;
    var open = function (on) { root.classList.toggle('is-filters-open', on); };
    root.querySelector('[data-tools-open]')?.addEventListener('click', function () { open(true); });
    root.querySelectorAll('[data-tools-close]').forEach(function (button) {
        button.addEventListener('click', function () { open(false); });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') open(false);
    });
})();
</script>
@endsection
