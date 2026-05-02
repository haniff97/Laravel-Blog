<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\ResponseCache\Facades\ResponseCache;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::published()->with('user')->paginate(9);
        return view('blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        abort_if($post->status !== 'published', 404);
        $post->load('user');
        return view('blog.show', compact('post'));
    }

    public function adminIndex()
    {
        $posts = Post::where('user_id', Auth::id())
                     ->with('user')
                     ->orderByDesc('created_at')
                     ->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status'  => 'required|in:draft,published',
        ]);

        $validated['slug']         = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['user_id']      = Auth::id();
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;

        DB::transaction(function () use ($validated) {
            Post::create($validated);
        });

        ResponseCache::clear();
        return redirect()->route('admin.posts.index')->with('success', 'Post created!');
        
    }

    public function edit(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status'  => 'required|in:draft,published',
        ]);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        DB::transaction(function () use ($post, $validated) {
            $post->update($validated);
        });

        ResponseCache::clear();
        return redirect()->route('admin.posts.index')->with('success', 'Post updated!');
    }

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        $slug = $post->slug;
        $post->delete();

        ResponseCache::clear();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted!');
    }

}