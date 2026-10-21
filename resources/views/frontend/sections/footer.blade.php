<footer class="tazen-footer" id="tazen-public-footer">
    <div class="tazen-footer__main">
        <div class="tazen-footer__brand">
            <a class="tazen-footer__logo" href="{{ url('/') }}" aria-label="Tazen home">
                <img src="{{ asset('customer-css/assets/images/tazen_logo.png') }}" alt="Tazen" width="200" height="54" loading="lazy">
            </a>
            <p>Discover trusted professionals and services for your next step.</p>
            <nav class="tazen-footer__social" aria-label="Tazen on social media">
                <a href="https://www.facebook.com/share/18vaMJRLFG/" target="_blank" rel="noopener noreferrer" aria-label="Tazen on Facebook"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                <a href="https://www.instagram.com/tazen.in?igsh=eml2bnhsMTdvaTlj" target="_blank" rel="noopener noreferrer" aria-label="Tazen on Instagram"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                <a href="https://www.linkedin.com/company/tazen/" target="_blank" rel="noopener noreferrer" aria-label="Tazen on LinkedIn"><i class="fab fa-linkedin-in" aria-hidden="true"></i></a>
                <a href="https://www.youtube.com/@Tazenofficial1" target="_blank" rel="noopener noreferrer" aria-label="Tazen on YouTube"><i class="fab fa-youtube" aria-hidden="true"></i></a>
                <a href="https://x.com/Tazenofficial_" target="_blank" rel="noopener noreferrer" aria-label="Tazen on X"><i class="fab fa-x-twitter" aria-hidden="true"></i></a>
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
                <i class="fas fa-envelope" aria-hidden="true"></i>
                <span>help@tazen.in</span>
            </a>
            <p>For support and enquiries, contact the Tazen team.</p>
            <form class="tazen-footer__contact-form" data-contact-redirect>
                <label for="tazen-footer-email">Send us your email</label>
                <div>
                    <input id="tazen-footer-email" type="email" name="email" placeholder="Your email" required>
                    <button type="submit" aria-label="Continue to contact form">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
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
