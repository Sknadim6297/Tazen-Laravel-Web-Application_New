<footer class="tazen-footer" id="tazen-public-footer">
    <div class="tazen-footer__main">
        <div class="tazen-footer__brand">
            <a class="tazen-footer__logo" href="{{ url('/') }}" aria-label="Tazen home">
                <img src="{{ asset('customer-css/assets/images/tazen_logo.png') }}" alt="Tazen" width="200" height="54" loading="lazy">
            </a>
            <p>Discover trusted professionals and services for your next step.</p>
            <nav class="tazen-footer__social" aria-label="Tazen on social media">
                <a href="https://www.facebook.com/share/18vaMJRLFG/" target="_blank" rel="noopener noreferrer" aria-label="Tazen on Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13.5 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.2v2.2H7.7V13h2.7v8h3.1Z"></path></svg>
                </a>
                <a href="https://www.instagram.com/tazen.in?igsh=eml2bnhsMTdvaTlj" target="_blank" rel="noopener noreferrer" aria-label="Tazen on Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle class="tazen-footer__social-dot" cx="17.5" cy="6.8" r="1"></circle></svg>
                </a>
                <a href="https://www.linkedin.com/company/tazen/" target="_blank" rel="noopener noreferrer" aria-label="Tazen on LinkedIn">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5.2 8.5a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6ZM3.7 10h3v10h-3zM9 10h2.9v1.4h.1a3.2 3.2 0 0 1 2.9-1.6c3.1 0 3.7 2 3.7 4.6V20h-3v-5c0-1.2 0-2.7-1.7-2.7s-2 1.3-2 2.6V20H9z"></path></svg>
                </a>
                <a href="https://www.youtube.com/@Tazenofficial1" target="_blank" rel="noopener noreferrer" aria-label="Tazen on YouTube">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 7.2a2.8 2.8 0 0 0-2-2C17.2 4.7 12 4.7 12 4.7s-5.2 0-7 .5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2.5 12 29 29 0 0 0 3 16.8a2.8 2.8 0 0 0 2 2c1.8.5 7 .5 7 .5s5.2 0 7-.5a2.8 2.8 0 0 0 2-2 29 29 0 0 0 .5-4.8 29 29 0 0 0-.5-4.8Z"></path><path class="tazen-footer__social-play" d="m10 15.5 5-3.5-5-3.5z"></path></svg>
                </a>
                <a href="https://x.com/Tazenofficial_" target="_blank" rel="noopener noreferrer" aria-label="Tazen on X">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M18.9 3H22l-6.8 7.8L23.2 21h-6.3L12 14.7 6.5 21H3.3l7.3-8.4L2.8 3h6.5l4.5 5.9L18.9 3Zm-1.1 16h1.7L8.2 4.9H6.4L17.8 19Z"></path></svg>
                </a>
                <a href="https://wa.me/+919147421560?text=Hello%20Tazen!%20I%20need%20assistance%20with%20your%20services." target="_blank" rel="noopener noreferrer" aria-label="Tazen on WhatsApp">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2.5a9.3 9.3 0 0 0-8 14l-1.2 4.4 4.5-1.2A9.5 9.5 0 1 0 12 2.5Zm0 17.1a7.6 7.6 0 0 1-3.9-1.1l-.3-.2-2.7.7.7-2.6-.2-.3A7.6 7.6 0 1 1 12 19.6Zm4.2-5.7c-.2-.1-1.3-.7-1.5-.8-.2-.1-.4-.1-.5.1-.2.2-.6.8-.8.9-.1.2-.3.2-.5.1-.2-.1-.9-.3-1.8-1.1-.6-.5-1.1-1.2-1.2-1.4-.1-.2 0-.4.1-.5l.4-.5.2-.4c.1-.2 0-.3 0-.5l-.7-1.6c-.2-.4-.4-.4-.5-.4h-.5c-.2 0-.5.1-.7.3-.2.2-.9.9-.9 2.1s.9 2.4 1 2.5c.1.2 1.8 2.7 4.3 3.8.6.3 1.1.4 1.4.5.6.2 1.2.2 1.6.1.5-.1 1.3-.5 1.5-1 .2-.5.2-.9.1-1-.1-.1-.3-.2-.5-.3Z"></path></svg>
                </a>
            </nav>
        </div>

        <nav class="tazen-footer__column" aria-label="Quick links">
            <h2>Quick Links</h2>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/about') }}">About Us</a></li>
                <li><a href="{{ route('professional.login') }}">Join Us as professional</a></li>
                <li><a href="{{ route('login') }}">Customer Login</a></li>
                <li><a href="{{ route('career') }}">Careers</a></li>
            </ul>
        </nav>

        <nav class="tazen-footer__column" aria-label="Services and resources">
            <h2>Services &amp; Resources</h2>
            <ul>
                <li><a href="{{ route('gridlisting') }}">Explore Services</a></li>
                <li><a href="{{ url('/help') }}">Help</a></li>
                <li><a href="{{ route('blog.index') }}">Blog</a></li>
                <li><a href="{{ route('event.list') }}">Events</a></li>
                <li><a href="{{ url('/contact') }}">Contact Us</a></li>
            </ul>
        </nav>

        <div class="tazen-footer__column tazen-footer__contact">
            <h2>Get in Touch</h2>
            <a href="mailto:help@tazen.in">
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m4 7 8 6 8-6"></path></svg>
                <span>help@tazen.in</span>
            </a>
            <p>For support and enquiries, contact the Tazen team.</p>
            <form class="tazen-footer__contact-form" data-contact-redirect>
                <label for="tazen-footer-email">Send us your email</label>
                <div>
                    <input id="tazen-footer-email" type="email" name="email" placeholder="Your email" required>
                    <button type="submit" aria-label="Continue to contact form">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 12h15M13 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="tazen-footer__bottom">
        <span>&copy; {{ date('Y') }} Tazen. All rights reserved.</span>
        <div class="tazen-footer__preferences">
            <label>
                <span class="visually-hidden">Language</span>
                <select aria-label="Language">
                    <option value="English" selected>English</option>
                </select>
            </label>
            @php
                $supportedCurrencies = app(\App\Services\CurrencyService::class)->getSupportedCurrencies();
                $currentCurrency = getCurrentCurrency();
            @endphp
            <form action="{{ route('currency.switch') }}" method="POST">
                @csrf
                <label>
                    <span class="visually-hidden">Currency</span>
                    <select name="currency" aria-label="Select currency" onchange="this.form.submit()">
                        @foreach ($supportedCurrencies as $code => $data)
                            <option value="{{ $code }}" @selected($currentCurrency === $code)>{{ $code }} ({{ $data['symbol'] }})</option>
                        @endforeach
                    </select>
                </label>
            </form>
        </div>
        <nav aria-label="Legal links">
            <a href="{{ url('/terms') }}">Terms and conditions</a>
            <a href="{{ url('/privacy') }}">Privacy</a>
        </nav>
    </div>
</footer>

<div id="toTop" aria-label="Back to top"></div>
<div class="layer"></div>
