@extends('layouts.app')
@section('title', $post->title)
@section('content')
    <article class="article-page">
        <a href="{{ route('home') }}" class="text-link back-link">← All stories</a>
        <header class="article-header">
            <p class="eyebrow">THE JOURNAL</p>
            <h1>{{ $post->title }}</h1>
            @if($post->excerpt)<p class="article-excerpt">{{ $post->excerpt }}</p>@endif
            <div class="article-byline"><span class="author-avatar" aria-hidden="true">{{ Str::upper(Str::substr($post->user->name, 0, 1)) }}</span><div><span class="author-name">{{ $post->user->name }}</span><p><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('F j, Y') }}</time> · {{ max(1, (int) ceil(str_word_count(strip_tags($post->rendered_content)) / 200)) }} min read</p></div></div>
        </header>
        <div class="prose prose-lg max-w-none article-content">{!! $post->rendered_content !!}</div>
        <div class="article-end"><span aria-hidden="true">✳</span><a href="{{ route('home') }}" class="text-link">Back to the journal →</a></div>
    </article>
@endsection
