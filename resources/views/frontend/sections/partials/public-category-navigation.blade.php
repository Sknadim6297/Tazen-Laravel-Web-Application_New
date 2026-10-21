@php
    $isMobileNavigation = $mobile ?? false;
    $navigationPrefix = $isMobileNavigation ? 'mobile' : 'desktop';
@endphp

@if ($trendingHeader)
    <li class="tazen-header__menu-item tazen-header__menu-item--category" data-category-item>
        <div class="tazen-header__category-heading">
            <a class="tazen-header__link" href="{{ route('gridlisting') }}">{{ $trendingHeader->tagline }}</a>
            <button class="tazen-header__category-toggle" type="button" aria-expanded="false" aria-controls="{{ $navigationPrefix }}-trending-menu" aria-label="Show {{ $trendingHeader->tagline }} services" data-category-toggle>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
        </div>
        <div class="tazen-header__dropdown" id="{{ $navigationPrefix }}-trending-menu" data-category-menu>
            <div class="tazen-header__dropdown-heading">
                <h2>{{ $trendingHeader->tagline }} Services</h2>
            </div>
            @if ($trendingHeader->features->isNotEmpty())
                <div class="tazen-header__dropdown-columns">
                    @foreach ($trendingHeader->features as $feature)
                        <section class="tazen-header__dropdown-column">
                            <h3>{{ $feature->feature_heading }}</h3>
                            <ul>
                                @forelse ($feature->services as $service)
                                    <li><a href="{{ route('service.show', $service->slug) }}">{{ $service->name }}</a></li>
                                @empty
                                    <li><span>No services available</span></li>
                                @endforelse
                            </ul>
                        </section>
                    @endforeach
                </div>
            @else
                <div class="tazen-header__dropdown-columns">
                    @foreach ($trendingServices as $chunk)
                        <section class="tazen-header__dropdown-column">
                            <h3>{{ $loop->first ? 'Popular Services' : 'More Services' }}</h3>
                            <ul>
                                @foreach ($chunk as $subService)
                                    <li><a href="{{ route('service.show', $subService->service?->slug) }}">{{ $subService->name }}</a></li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </li>
@else
    <li class="tazen-header__menu-item tazen-header__menu-item--category" data-category-item>
        <div class="tazen-header__category-heading">
            <a class="tazen-header__link" href="{{ route('gridlisting') }}">Trending</a>
            <button class="tazen-header__category-toggle" type="button" aria-expanded="false" aria-controls="{{ $navigationPrefix }}-trending-menu" aria-label="Show Trending services" data-category-toggle>
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
        </div>
        <div class="tazen-header__dropdown" id="{{ $navigationPrefix }}-trending-menu" data-category-menu>
            <div class="tazen-header__dropdown-heading">
                <h2>Trending Services</h2>
            </div>
            <div class="tazen-header__dropdown-columns">
                @foreach ($trendingServices as $chunk)
                    <section class="tazen-header__dropdown-column">
                        <h3>{{ $loop->first ? 'Popular Services' : 'More Services' }}</h3>
                        <ul>
                            @foreach ($chunk as $subService)
                                <li><a href="{{ route('service.show', $subService->service?->slug) }}">{{ $subService->name }}</a></li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </div>
    </li>
@endif

@foreach ($otherHeaders as $header)
    <li class="tazen-header__menu-item tazen-header__menu-item--category" data-category-item>
        <div class="tazen-header__category-heading">
            <button class="tazen-header__category-label" type="button" aria-expanded="false" aria-controls="{{ $navigationPrefix }}-category-{{ $header->id }}" data-category-toggle>
                {{ $header->tagline }}
                <i class="fas fa-chevron-down" aria-hidden="true"></i>
            </button>
        </div>
        <div class="tazen-header__dropdown" id="{{ $navigationPrefix }}-category-{{ $header->id }}" data-category-menu>
            <div class="tazen-header__dropdown-columns">
                @foreach ($header->features as $feature)
                    <section class="tazen-header__dropdown-column">
                        <h3>{{ $feature->feature_heading }}</h3>
                        <ul>
                            @forelse ($feature->services as $service)
                                <li><a href="{{ route('service.show', $service->slug) }}">{{ $service->name }}</a></li>
                            @empty
                                <li><span>No services available</span></li>
                            @endforelse
                        </ul>
                    </section>
                @endforeach
            </div>
        </div>
    </li>
@endforeach
