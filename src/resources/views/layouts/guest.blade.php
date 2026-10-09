<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'My Blog') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="journal-shell auth-shell">
    <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true">m.</span> My Blog</a>
    <main class="auth-card"><p class="eyebrow">YOUR WRITING SPACE</p><h1>{{ request()->routeIs('login') ? 'Welcome back.' : (request()->routeIs('register') ? 'Make yourself at home.' : 'Account settings.') }}</h1><p class="auth-subtitle">{{ request()->routeIs('login') ? 'Sign in to bring your next idea to life.' : 'A little space for your next chapter.' }}</p>{{ $slot }}</main>
    <a href="{{ route('home') }}" class="text-link">← Back to the journal</a>
</body>
</html>
