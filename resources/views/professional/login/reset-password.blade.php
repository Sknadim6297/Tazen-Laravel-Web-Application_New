<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | Yuktione</title>
    @vite('resources/css/tazen.css')
</head>
<body class="min-h-screen bg-brand-50 px-4 py-10 font-sans text-text-primary sm:px-6">
    <main class="mx-auto flex min-h-[75vh] w-full max-w-lg items-center justify-center">
        <section class="w-full rounded-panel border border-brand-100 bg-white p-6 shadow-card sm:p-9">
            <a href="{{ route('home') }}" class="mb-7 flex justify-center" aria-label="Yuktione home">
                <img src="{{ asset('frontend/assets/Yuktione_logo.png') }}" alt="Yuktione" class="h-auto max-h-16 w-auto max-w-[210px] object-contain">
            </a>
            <div class="mb-7">
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.16em] text-brand-700">Secure your account</p>
                <h1 class="text-2xl font-semibold text-brand-950">Reset password</h1>
                <p class="mt-2 text-sm leading-6 text-text-secondary">Choose a new password for your professional account.</p>
            </div>

            <form method="POST" action="{{ route('professional.password.reset') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-brand-950">{{ __('E-Mail Address') }}</label>
                    <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                        class="w-full rounded-control border border-brand-200 bg-white px-4 py-3 text-sm text-text-primary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:ring-4 focus:ring-brand-100 @error('email') border-red-500 @enderror">
                    @error('email')
                        <span class="mt-2 block text-sm text-red-700" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-brand-950">{{ __('New Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full rounded-control border border-brand-200 bg-white px-4 py-3 text-sm text-text-primary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:ring-4 focus:ring-brand-100 @error('password') border-red-500 @enderror">
                    @error('password')
                        <span class="mt-2 block text-sm text-red-700" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="mb-2 block text-sm font-medium text-brand-950">{{ __('Confirm Password') }}</label>
                    <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full rounded-control border border-brand-200 bg-white px-4 py-3 text-sm text-text-primary shadow-sm outline-none transition placeholder:text-slate-400 focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
                </div>

                <button type="submit" class="w-full rounded-control bg-brand-primary px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200">
                    {{ __('Reset Password') }}
                </button>
                <a href="{{ route('professional.login') }}" class="block text-center text-sm font-medium text-brand-700 transition hover:text-brand-950">Back to Login</a>
            </form>
        </section>
    </main>
</body>
</html>
