<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#168A80">
    <title>Forgot Password | Yuktione</title>
    @include('partials.dm-sans')
    @vite(['resources/css/tazen.css', 'resources/css/auth-theme.css'])
</head>
<body class="yuktione-auth-page min-h-screen bg-background px-4 py-8 font-sans text-text-primary sm:px-6">
    <main class="mx-auto flex min-h-[75vh] w-full max-w-lg items-center justify-center">
        <section class="w-full rounded-panel border border-border bg-white p-6 shadow-card sm:p-9" aria-labelledby="recovery-title">
            <a href="{{ route('home') }}" class="mb-7 flex justify-center" aria-label="Yuktione home">
                <img src="{{ asset('frontend/assets/Yuktione_logo.png') }}" alt="Yuktione" class="h-auto max-h-16 w-auto max-w-[210px] object-contain">
            </a>
            <div class="mb-7">
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-brand-primary">Account recovery</p>
                <h1 id="recovery-title" class="text-2xl font-semibold text-brand-secondary">Forgot password?</h1>
                <p class="mt-2 text-sm leading-6 text-text-secondary">Enter your email address and we’ll send a password reset link if an account matches.</p>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-card border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('forgot.send') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-text-primary">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        class="w-full rounded-control border border-border bg-white px-4 py-3 text-sm text-text-primary placeholder:text-text-muted shadow-sm outline-none transition focus:border-brand-primary focus:ring-4 focus:ring-brand-100 @error('email') border-red-500 @enderror">
                    @error('email')
                        <span class="mt-2 block text-sm text-red-700" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <button type="submit" class="w-full rounded-control bg-brand-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200">
                    {{ __('Send Password Reset Link') }}
                </button>
                <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-brand-primary transition hover:text-brand-800">Back to Login</a>
            </form>
        </section>
    </main>
</body>
</html>
