<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Blog') · My Blog</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="journal-shell">
    <a href="#main-content" class="skip-link">Skip to content</a>
    <header class="site-header">
        <nav class="site-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="brand"><span class="brand-mark" aria-hidden="true">m.</span> My Blog</a>
            <div class="nav-actions">
                <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home', 'blog.show') ? 'is-active' : '' }}">Journal</a>
                @auth
                    <a href="{{ route('admin.posts.index') }}" class="nav-item {{ request()->routeIs('admin.*') ? 'is-active' : '' }}">Manage posts</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-item">Sign out</button></form>
                @endauth
            </div>
        </nav>
    </header>
    <main id="main-content" class="site-main">
        @if(session('success'))
            <div role="status" class="success-message">{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer"><a href="{{ route('home') }}" class="footer-brand">My Blog</a><span>A space for ideas, stories, and everything in between.</span><span>© {{ date('Y') }}</span></footer>
</body>
</html>
