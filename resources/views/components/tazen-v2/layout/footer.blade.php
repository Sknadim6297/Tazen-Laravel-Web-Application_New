@php
    $footerLinkClasses = 'inline-flex min-h-8 items-center rounded-control text-sm text-text-secondary underline-offset-4 transition-colors hover:text-brand-primary hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-focus-ring';
@endphp

<footer class="border-t border-divider bg-surface-muted">
<footer aria-label="Yuktione site footer" class="bg-[#111111] text-white">
    <div class="mx-auto max-w-7xl px-4 pb-8 pt-12 sm:px-6 sm:pb-10 sm:pt-14 lg:px-8 lg:pt-16">
        <div class="grid grid-cols-2 gap-x-6 gap-y-10 sm:gap-x-10 lg:grid-cols-[minmax(10rem,1.2fr)_repeat(4,minmax(0,1fr))] lg:gap-x-8">
            <div class="col-span-2 flex items-center justify-center lg:col-span-1 lg:items-start lg:justify-start">
                <a href="{{ route('home') }}" aria-label="Yuktione home" class="inline-flex rounded-control focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-[#19b9f2]">
                    <img
                        src="{{ asset('frontend/assets/Yuktione_logo.png') }}"
                        alt="Yuktione"
                        width="155"
                        height="64"
                        loading="lazy"
                        class="h-auto w-36 object-contain sm:w-40"
                    >
                </a>
            </div>

            <nav aria-labelledby="yuktione-footer-discover">
                <h2 id="yuktione-footer-discover" class="mb-3 text-xs font-semibold uppercase tracking-widest text-white">Discover</h2>
                <ul class="grid gap-1">
                    <li><a href="{{ route('gridlisting') }}" class="{{ $footerLinkClasses }}">Find Professionals</a></li>
                    <li><a href="{{ route('login') }}" class="{{ $footerLinkClasses }}">Customer Login</a></li>
                    <li><a href="{{ route('tazen-v2.preview') }}#tazen-v2-how-it-works-title" class="{{ $footerLinkClasses }}">How It Works</a></li>
                </ul>
            </nav>

            <nav aria-labelledby="yuktione-footer-company">
                <h2 id="yuktione-footer-company" class="mb-3 text-xs font-semibold uppercase tracking-widest text-white">Company</h2>
                <ul class="grid gap-1">
                    <li><a href="{{ url('/about') }}" class="{{ $footerLinkClasses }}">About Us</a></li>
                    <li><a href="{{ url('/contact') }}" class="{{ $footerLinkClasses }}">Contact Us</a></li>
                    <li><a href="{{ route('help.index') }}" class="{{ $footerLinkClasses }}">Support</a></li>
                </ul>
            </nav>

            <nav aria-labelledby="yuktione-footer-explore">
                <h2 id="yuktione-footer-explore" class="mb-3 text-xs font-semibold uppercase tracking-widest text-white">Explore</h2>
                <ul class="grid gap-1">
                    <li><a href="{{ route('professional.register') }}" class="{{ $footerLinkClasses }}">Become a Professional</a></li>
                    <li><a href="{{ route('blog.index') }}" class="{{ $footerLinkClasses }}">Blogs</a></li>
                    <li><a href="{{ route('event.list') }}" class="{{ $footerLinkClasses }}">Events</a></li>
                </ul>
            </nav>

            <nav aria-labelledby="yuktione-footer-legal">
                <h2 id="yuktione-footer-legal" class="mb-3 text-xs font-semibold uppercase tracking-widest text-white">Legal &amp; Help</h2>
                <ul class="grid gap-1">
                    <li><a href="{{ route('privacy') }}" class="{{ $footerLinkClasses }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="{{ $footerLinkClasses }}">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('help.index') }}" class="{{ $footerLinkClasses }}">FAQs</a></li>
                </ul>
            </nav>
        </div>

        <div class="mt-10 border-t border-[#3a3a3a] pt-6 sm:mt-12 sm:pt-7">
            <p class="text-center text-sm text-[#b8b8b8]">&copy; {{ now()->year }} Yuktione. All rights reserved.</p>
        </div>
    </div>
</footer>

        <nav aria-labelledby="tazen-footer-quick-links">
            <h2 id="tazen-footer-quick-links" class="mb-4 text-sm font-semibold text-text-primary">Quick Links</h2>
            <ul class="grid gap-2">
                <li><a href="{{ route('home') }}" class="{{ $footerLinkClasses }}">Home</a></li>
                <li><a href="{{ route('event.list') }}" class="{{ $footerLinkClasses }}">Events</a></li>
                <li><a href="{{ route('blog.index') }}" class="{{ $footerLinkClasses }}">Insights</a></li>
            </ul>
        </nav>

        <nav aria-labelledby="tazen-footer-explore">
            <h2 id="tazen-footer-explore" class="mb-4 text-sm font-semibold text-text-primary">Explore Experts</h2>
            <ul class="grid gap-2">
                <li><a href="{{ route('gridlisting') }}" class="{{ $footerLinkClasses }}">Browse experts</a></li>
                <li><a href="{{ route('services.search') }}" class="{{ $footerLinkClasses }}">Search services</a></li>
            </ul>
        </nav>

        <nav aria-labelledby="tazen-footer-company">
            <h2 id="tazen-footer-company" class="mb-4 text-sm font-semibold text-text-primary">Company</h2>
            <ul class="grid gap-2">
                <li><a href="{{ url('/about') }}" class="{{ $footerLinkClasses }}">About Yuktione</a></li>
                <li><a href="{{ route('career') }}" class="{{ $footerLinkClasses }}">Careers</a></li>
                <li><a href="{{ url('/contact') }}" class="{{ $footerLinkClasses }}">Contact</a></li>
            </ul>
        </nav>

        <nav aria-labelledby="tazen-footer-support">
            <h2 id="tazen-footer-support" class="mb-4 text-sm font-semibold text-text-primary">Help &amp; Support</h2>
            <ul class="grid gap-2">
                <li><a href="{{ route('help.index') }}" class="{{ $footerLinkClasses }}">Help center</a></li>
                <li><a href="{{ route('privacy') }}" class="{{ $footerLinkClasses }}">Privacy policy</a></li>
                <li><a href="{{ route('terms') }}" class="{{ $footerLinkClasses }}">Terms &amp; conditions</a></li>
            </ul>
        </nav>
    </div>

    <div class="border-t border-divider bg-surface">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-sm text-text-muted sm:px-6 lg:px-8">
            <p>&copy; {{ now()->year }} Yuktione. All rights reserved.</p>
        </div>
    </div>
</footer>