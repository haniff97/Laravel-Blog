<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Repositories\PostRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\ResponseCache\Facades\ResponseCache;

class PostController extends Controller
{
    public function __construct(protected PostRepository $postRepository)
    {
    }

    public function index()
    {
        $posts = $this->postRepository->getPublished();
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
        $posts = $this->postRepository->getByUser(Auth::id());
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

        $validated['user_id'] = Auth::id();

        DB::transaction(function () use ($validated) {
            $this->postRepository->create($validated);
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

        DB::transaction(function () use ($post, $validated) {
            $this->postRepository->update($post, $validated);
        });

        ResponseCache::clear();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated!');
    }

    public function destroy(Post $post)
    {
        abort_if($post->user_id !== Auth::id(), 403);

        DB::transaction(function () use ($post) {
            $this->postRepository->delete($post);
        });

        ResponseCache::clear();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted!');
    }

}