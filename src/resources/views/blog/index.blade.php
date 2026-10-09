@extends('layouts.app')
@section('title', 'Journal')
@section('content')
    <section class="journal-intro">
        <p class="eyebrow"><span class="status-dot"></span> THE JOURNAL</p>
        <h1>Good ideas deserve<br>a little <em>space.</em></h1>
        <p class="intro-copy">Thoughts, discoveries, and stories worth sharing.<br>A small corner of the internet to slow down and read.</p>
        <a href="#latest" class="text-link">Explore the latest <span aria-hidden="true">↓</span></a>
        <span class="intro-decoration" aria-hidden="true">✳</span>
    </section>
    <section id="latest" class="posts-section" aria-labelledby="latest-heading">
        <div class="section-heading"><h2 id="latest-heading">Latest stories</h2><span>{{ $posts->total() }} {{ Str::plural('story', $posts->total()) }}</span></div>
        @if($posts->isEmpty())
            <div class="empty-state"><span class="empty-symbol" aria-hidden="true">✳</span><h3>A fresh page.</h3><p>There are no stories here yet. Check back soon for something new.</p>@auth<a href="{{ route('admin.posts.create') }}" class="text-link">Write your first story →</a>@endauth</div>
        @else
            <div class="story-grid">
                @foreach($posts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="story-card">
                        <div class="story-meta"><span>JOURNAL</span><time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('M j, Y') }}</time></div>
                        <h3>{{ $post->title }}</h3>
                        <p>{{ $post->excerpt ?? Str::limit(strip_tags($post->rendered_content), 160) }}</p>
                        <div class="story-bottom"><span>{{ max(1, (int) ceil(str_word_count(strip_tags($post->rendered_content)) / 200)) }} min read</span><span class="story-arrow" aria-hidden="true">↗</span></div>
                    </a>
                @endforeach
            </div>
            <div class="pagination">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
