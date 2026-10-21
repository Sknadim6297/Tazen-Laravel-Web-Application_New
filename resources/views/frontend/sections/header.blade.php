@php
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

<header class="tazen-header" id="tazen-public-header" data-public-header>
    <div class="tazen-header__bar tazen-header__inner">
        <a class="tazen-header__brand" href="{{ route('home') }}" aria-label="Tazen home">
            <img src="{{ asset('customer-css/assets/images/tazen_logo.png') }}" alt="Tazen" width="200" height="54">
        </a>

        <button class="tazen-header__mobile-toggle" type="button" aria-expanded="false" aria-controls="tazen-header-content" aria-label="Open navigation menu" data-mobile-nav-toggle>
            <span></span><span></span><span></span>
        </button>

        <div class="tazen-header__content" id="tazen-header-content" data-mobile-nav>
            <nav class="tazen-header__navigation" aria-label="Main navigation">
                <ul class="tazen-header__navigation-list">
                    <li><a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home')])>Home</a></li>
                    <li><a href="{{ route('gridlisting') }}" @class(['is-active' => request()->routeIs('gridlisting')])>Explore</a></li>
                    <li><a href="#" data-placeholder-link aria-disabled="true">Tools</a></li>
                    <li><a href="{{ route('professionals') }}" @class(['is-active' => request()->routeIs('professionals')])>Experts</a></li>
                    <li><a href="#" data-placeholder-link aria-disabled="true">Programmes</a></li>
                    <li><a href="{{ url('/about') }}" @class(['is-active' => request()->is('about')])>About</a></li>
                </ul>
            </nav>

            <form class="tazen-header__search" action="{{ route('search.services') }}" method="get" data-header-search data-search-endpoint="{{ route('search.services') }}" data-service-url="{{ url('/service') }}">
                <label class="visually-hidden" for="tazen-header-search">Search services</label>
                <input id="tazen-header-search" type="search" name="query" placeholder="Search anything..." autocomplete="off" required>
                <button type="submit" aria-label="Search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>
                <span class="tazen-header__search-status" role="status" aria-live="polite" data-search-status></span>
            </form>

            <div class="tazen-header__actions">
                <button class="tazen-header__utility-button" type="button" aria-label="Notifications unavailable" disabled>
                    <i class="fas fa-bell" aria-hidden="true"></i>
                </button>
                @if ($messagesUrl)
                    <a class="tazen-header__utility-button" href="{{ $messagesUrl }}" aria-label="Messages">
                        <i class="fas fa-comment-dots" aria-hidden="true"></i>
                    </a>
                @else
                    <button class="tazen-header__utility-button" type="button" aria-label="Messages unavailable" disabled>
                        <i class="fas fa-comment-dots" aria-hidden="true"></i>
                    </button>
                @endif

                @if ($account)
                    <div class="tazen-header__account" data-account-menu>
                        <button class="tazen-header__account-toggle" type="button" aria-expanded="false" aria-controls="tazen-account-menu" data-account-toggle>
                            <img src="{{ $profilePhoto }}" alt="" width="38" height="38">
                            <span class="tazen-header__account-details">
                                <span class="tazen-header__account-name">{{ $account->name }}</span>
                                <span class="tazen-header__account-role">{{ $accountRole }}</span>
                            </span>
                            <i class="fas fa-chevron-down" aria-hidden="true"></i>
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
                @endif
            </div>
        </div>

        <button class="tazen-header__backdrop" type="button" aria-label="Close navigation menu" data-mobile-nav-backdrop hidden></button>
    </div>
</header>

<script src="{{ asset('frontend/assets/js/public-chrome.js') }}" defer></script>
