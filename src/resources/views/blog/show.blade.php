@extends('layouts.app')

@section('title', $post->title)

@section('content')
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('home') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Back to posts</a>
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $post->title }}</h1>
        <p class="text-sm text-gray-400 mb-8">{{ $post->published_at->format('F j, Y') }} · by {{ $post->user->name }}</p>
        <div class="prose prose-lg dark:prose-invert max-w-none">
            {!! $post->rendered_content !!}
        </div>
    </div>
@endsection
