<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <nav class="bg-white dark:bg-gray-800 shadow mb-8">
        <div class="max-w-3xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900 dark:text-white">My Blog</a>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 pb-16">
        <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Back to posts</a>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $post->title }}</h1>
        <p class="text-sm text-gray-400 mb-8">{{ $post->published_at->format('F j, Y') }} · by {{ $post->user->name }}</p>
        <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200 leading-relaxed">
            {!! nl2br(e($post->content)) !!}
        </div>
    </main>
</body>
</html>
