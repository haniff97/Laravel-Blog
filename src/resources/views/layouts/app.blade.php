<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Blog')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <nav class="bg-white dark:bg-gray-800 shadow mb-8">
        <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white">My Blog</a>
            <div class="flex gap-4 items-center">
                @auth
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900">Admin</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 pb-16">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-6">{{ session('success') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
