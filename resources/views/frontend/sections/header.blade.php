@php
    $isHomePage = request()->routeIs('home');
    $user = Auth::guard('user')->user();
    $professional = Auth::guard('professional')->user();
    $account = $user ?? $professional;
    $accountRole = $user ? 'Customer' : 'Professional';
    $profile = $user
        ? \App\Models\CustomerProfile::where('user_id', $user->id)->first()
        : ($professional
            ? \App\Models\Profile::where('professional_id', $professional->id)->first()
            : null);
    $profilePhoto = $user
        ? ($profile?->profile_image
            ? asset($profile->profile_image)
            : ($user->profile_photo_path
                ? asset('uploads/user_images/' . $user->profile_photo_path)
                : asset('frontend/assets/img/avatar.jpg')))
        : ($professional
            ? ($profile?->photo
                ? asset('storage/' . $profile->photo)
                : ($professional->photo
                    ? asset('storage/' . $professional->photo)
                    : asset('frontend/assets/img/avatar.jpg')))
            : null);
    $messagesUrl = $user
        ? route('user.admin-chat.index')
        : ($professional ? route('professional.admin-chat.index') : null);
@endphp

<header @class(['tazen-header', 'tazen-header--home' => $isHomePage]) id="tazen-public-header" data-public-header>
    <div class="tazen-header__bar tazen-header__inner">
        <a class="tazen-header__brand" href="{{ route('home') }}" aria-label="Yuktione home">
            <img src="{{ asset('frontend/assets/Yuktione_logo.png') }}" alt="Yuktione" width="155" height="52">
        </a>

        <button class="tazen-header__mobile-toggle" type="button" aria-expanded="false" aria-controls="tazen-header-content" aria-label="Open navigation menu" data-mobile-nav-toggle>
            <span></span><span></span><span></span>
        </button>

        <div class="tazen-header__content" id="tazen-header-content" data-mobile-nav>
            <div class="tazen-header__mobile-heading">
                <span>Menu</span>
                <button class="tazen-header__mobile-close" type="button" aria-label="Close navigation menu" data-mobile-nav-close>
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="m6 6 12 12M18 6 6 18"></path>
                    </svg>
                </button>
            </div>
            <nav class="tazen-header__navigation" aria-label="Main navigation">
                <ul class="tazen-header__navigation-list">
                    <li><a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Home</a></li>
                    <li><a href="{{ route('gridlisting') }}" @class(['is-active' => request()->routeIs('gridlisting')])>Explore</a></li>
                    <li><a href="#" data-placeholder-link aria-disabled="true">Tools</a></li>
                    <li><a href="{{ route('professionals') }}" @class(['is-active' => request()->routeIs('professionals')])>Experts</a></li>
                    <li><a href="{{ route('programmes.index') }}" @class(['is-active' => request()->routeIs('programmes.*', 'event.list', 'event.details')])>Programmes</a></li>
                    <li><a href="{{ url('/about') }}" @class(['is-active' => request()->is('about')])>About</a></li>
                </ul>
            </nav>

            @if ($isHomePage)
                <button class="tazen-header__search-toggle" type="button" aria-label="Open search" aria-expanded="false" aria-controls="tazen-header-search" data-home-search-toggle>
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="10.8" cy="10.8" r="6.8"></circle>
                        <path d="m16 16 4.2 4.2"></path>
                    </svg>
                </button>
            @endif

            <form class="tazen-header__search" action="{{ route('search.services') }}" method="get" data-header-search data-search-endpoint="{{ route('search.services') }}" data-service-url="{{ url('/service') }}">
                <label class="visually-hidden" for="tazen-header-search">Search services</label>
                <input id="tazen-header-search" type="search" name="query" placeholder="Search anything..." autocomplete="off" required>
                <button type="submit" aria-label="Search">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <circle cx="10.8" cy="10.8" r="6.8"></circle>
                        <path d="m16 16 4.2 4.2"></path>
                    </svg>
                </button>
                <span class="tazen-header__search-status" role="status" aria-live="polite" data-search-status></span>
            </form>

            <div class="tazen-header__actions">
                @if ($account)
                    <button class="tazen-header__utility-button" type="button" aria-label="Notifications unavailable" disabled>
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>
                    </button>
                    @if ($messagesUrl)
                        <a class="tazen-header__utility-button" href="{{ $messagesUrl }}" aria-label="Messages">
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2v-6.5A7.5 7.5 0 1 1 20 11.5Z"></path>
                                <path d="M8 11.5h.01M12.5 11.5h.01M17 11.5h.01"></path>
                            </svg>
                        </a>
                    @else
                        <button class="tazen-header__utility-button" type="button" aria-label="Messages unavailable" disabled>
                            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-3 2v-6.5A7.5 7.5 0 1 1 20 11.5Z"></path>
                                <path d="M8 11.5h.01M12.5 11.5h.01M17 11.5h.01"></path>
                            </svg>
                        </button>
                    @endif
                @endif

                @if ($account)
                    <div class="tazen-header__account" data-account-menu>
                        <button class="tazen-header__account-toggle" type="button" aria-expanded="false" aria-controls="tazen-account-menu" data-account-toggle>
                            <img src="{{ $profilePhoto }}" alt="" width="38" height="38">
                            <span class="tazen-header__account-details">
                                <span class="tazen-header__account-name">{{ $account->name }}</span>
                                <span class="tazen-header__account-role">{{ $accountRole }}</span>
                            </span>
                            <svg class="tazen-header__account-chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="m6 9 6 6 6-6"></path>
                            </svg>
                        </button>
                        <div class="tazen-header__account-menu" id="tazen-account-menu" hidden>
                            <span class="tazen-header__account-label">{{ $accountRole }}</span>
                            @if ($user)
                                <a href="{{ route('user.dashboard') }}">Dashboard</a>
                                <a href="{{ route('user.profile.index') }}">My Profile</a>
                                <a href="{{ route('user.all-appointment.index') }}">My Bookings</a>
                                <form action="{{ route('user.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            @else
                                <a href="{{ route('professional.dashboard') }}">Dashboard</a>
                                <a href="{{ route('professional.profile.index') }}">My Profile</a>
                                <a href="{{ route('professional.booking.index') }}">My Bookings</a>
                                <form action="{{ route('professional.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <a class="tazen-header__login" href="{{ route('login') }}">Login</a>
                    @if ($isHomePage)
                        <a class="tazen-header__get-started" href="{{ route('register') }}">Get Started</a>
                    @endif
                @endif
            </div>
        </div>

        <button class="tazen-header__backdrop" type="button" aria-label="Close navigation menu" data-mobile-nav-backdrop hidden></button>
    </div>
</header>

<script src="{{ asset('frontend/assets/js/public-chrome.js') }}?v={{ filemtime(public_path('frontend/assets/js/public-chrome.js')) }}" defer></script>
