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
                <svg viewBox="0 0 360 280">
                    <ellipse cx="210" cy="150" rx="96" ry="108" fill="#e7f6ef"/>
                    <path d="M28 210c36-70 82-108 136-98-28 46-64 74-136 98Z" fill="#7ea36a"/>
                    <path d="M58 62c46-18 82 10 92 46-36 8-74 0-92-46Z" fill="#9d81db" opacity=".85"/>
                    <path d="M250 28c28 46 18 92-18 120-8-46-8-82 18-120Z" fill="#8fb67c"/>
                    <path d="M278 156c36 8 64 36 64 72-46-8-74-28-64-72Z" fill="#b7a4e4"/>
                    <path d="M16 168c28-8 46 10 50 36-28 4-46-10-50-36Z" fill="#316d61" opacity=".75"/>
                </svg>
            </div>
        </div>
    </section>

    <div class="yk-toolpage__wrap">
        <nav class="yk-toolpage__cats" aria-label="Tool categories">
            <a href="{{ $link(['category' => null]) }}" @class(['is-active' => $filters['category'] === ''])>
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
                            <small>(0)</small>
                        </a>
                    @endforeach
                </fieldset>
            </aside>

            <section class="yk-toolpage__results" aria-label="Tool results">
                <div class="yk-toolpage__toolbar">
                    <p>Showing {{ $items->total() }} {{ $items->total() === 1 ? 'tool' : 'tools' }}</p>
                    <button class="yk-toolpage__filter-open" type="button" data-tools-open>Filters</button>
                </div>
                <p class="yk-toolpage__empty">
                    @if ($filtering)
                        No tools match these filters.
                        <a href="{{ route('tools.index') }}">Reset Tools</a>
                    @else
                        YuktiOne does not have a public tools catalogue yet. Published tools will appear here.
                    @endif
                </p>
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
