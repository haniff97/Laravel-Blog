@extends('layouts.app')

@section('title', 'New Post')

@section('content')
<div class="admin-page">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-indigo-600 hover:underline mb-6 inline-block">← Back to posts</a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">New Post</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="excerpt">Excerpt <span class="text-gray-400">(optional)</span></label>
                <input type="text" id="excerpt" name="excerpt" value="{{ old('excerpt') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="content">Content <span class="text-gray-400">(Markdown supported)</span></label>
                <textarea id="content" name="content" rows="16" required
                    class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-mono text-sm">{{ old('content') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1" for="status">Status</label>
                <select id="status" name="status" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Save Post</button>
        </form>
    </div>
</div>
@endsection
