<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository
{
    /**
     * Get all published posts, ordered by newest first.
     */
    public function getPublished(int $perPage = 10): LengthAwarePaginator
    {
        return Post::published()->paginate($perPage);
    }

    /**
     * Get all posts belonging to a specific user.
     */
    public function getByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::where('user_id', $userId)
                   ->orderByDesc('created_at')
                   ->paginate($perPage);
    }

    /**
     * Create a new post, auto-generating slug and published_at.
     */
    public function create(array $data): Post
    {
        $data['slug']         = $this->generateSlug($data['title']);
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        return Post::create($data);
    }

    /**
     * Update an existing post.
     */
    public function update(Post $post, array $data): Post
    {
        if (isset($data['title']) && $data['title'] !== $post->title) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        if ($data['status'] === 'published' && $post->status !== 'published') {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'draft') {
            $data['published_at'] = null;
        }

        $post->update($data);

        return $post->fresh();
    }

    /**
     * Soft-delete a post.
     */
    public function delete(Post $post): void
    {
        $post->delete();
    }

    /**
     * Generate a unique slug from a title.
     */
    private function generateSlug(string $title): string
    {
        $slug  = \Illuminate\Support\Str::slug($title);
        $count = Post::withTrashed()->where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
