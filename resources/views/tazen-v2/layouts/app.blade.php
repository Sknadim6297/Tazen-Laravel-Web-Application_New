<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ trim($__env->yieldContent('meta_description', 'Discover expert services and consultations with Yuktione.')) }}">

    <title>{{ trim($__env->yieldContent('title', 'Yuktione')) }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @stack('head')
    @vite('resources/css/tazen.css')
    @vite('resources/css/yuktione-feedback.css')
    @stack('styles')
</head>
<body class="flex min-h-screen flex-col bg-background font-sans text-text-primary antialiased">
    <a
        href="#main-content"
        class="sr-only fixed left-4 top-4 z-50 rounded-control bg-brand-primary px-4 py-3 font-semibold text-white focus:not-sr-only"
    >
        Skip to main content
    </a>

    @yield('header')

    <main id="main-content" class="w-full flex-1" tabindex="-1">
        @yield('content')
    </main>

    @yield('footer')

    <x-yuktione.feedback />
    @stack('scripts')
    @vite('resources/js/yuktione-feedback.js')
</body>
</html>