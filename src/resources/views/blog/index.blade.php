<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <nav class="bg-white dark:bg-gray-800 shadow mb-8">
        <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white">My Blog</a>
            <div class="flex gap-4">
                @auth
                    <a href="{{ route('admin.posts.index') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900">Admin</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Latest Posts</h1>

        @if($posts->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">No posts yet. Check back soon!</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                <a href="{{ route('blog.show', $post) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $post->title }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ $post->excerpt ?? Str::limit($post->content, 100) }}</p>
                    <span class="text-xs text-gray-400">{{ $post->published_at->diffForHumans() }}</span>
                </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @endif
    </main>
</body>
</html>
