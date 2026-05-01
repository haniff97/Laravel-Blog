@extends('layouts.app')

@section('title', 'Blog')

@section('content')
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Latest Posts</h1>

    @if($posts->isEmpty())
        <p class="text-gray-500 dark:text-gray-400">No posts yet. Check back soon!</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($posts as $post)
            <a href="{{ route('blog.show', $post) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $post->title }}</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ $post->excerpt ?? Str::limit(strip_tags($post->rendered_content), 100) }}</p>
                <span class="text-xs text-gray-400">{{ $post->published_at->diffForHumans() }}</span>
            </a>
            @endforeach
        </div>
        <div class="mt-8">{{ $posts->links() }}</div>
    @endif
@endsection
