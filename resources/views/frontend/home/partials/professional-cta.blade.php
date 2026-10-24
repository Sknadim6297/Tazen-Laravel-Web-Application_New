<section class="yk-home-professional" aria-labelledby="yk-home-professional-title">
    <div class="yk-home-container">
        <div class="yk-home-professional__panel">
            <div class="yk-home-professional__copy">
                <p class="yk-home-professional__eyebrow">For professionals</p>
                <h2 id="yk-home-professional-title">Your expertise deserves a bigger audience.</h2>
                <p>Create your professional profile, showcase your services and connect with people looking for expert guidance.</p>
                <a class="yk-home-button yk-home-button--light" href="{{ route('professional.register') }}">
                    Join as a Professional
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path d="M4 12h15M13 6l6 6-6 6"/>
                    </svg>
                </a>
            </div>
            <div class="yk-home-professional__media">
                <img
                    src="{{ asset('frontend/assets/img/professionals_photos/job-bussiness-career.jpg') }}"
                    alt="A team of professionals ready to share their expertise"
                    loading="lazy"
                    decoding="async"
                >
                <span class="yk-home-professional__media-accent" aria-hidden="true"></span>
            </div>
        </div>
    </div>
</section>
