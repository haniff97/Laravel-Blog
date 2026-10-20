<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptTrackerPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_import_is_published_with_all_five_images_and_is_editable(): void
    {
        $author = User::factory()->create();
        $this->artisan('blog:import-receipt-tracker', ['--author' => $author->id])->assertSuccessful();
        $post = Post::where('slug', 'receipt-tracker')->sole();
        $this->assertSame($author->id, $post->user_id);
        $this->assertSame('published', $post->status);

        $this->get('/')->assertOk()->assertSee($post->title);
        $response = $this->get('/blog/receipt-tracker')->assertOk();
        foreach (['7658', '7669', '7642', '7700', '7692'] as $suffix) {
            $response->assertSee('media_178802719'.$suffix.'.png');
        }
        $this->assertSame(5, substr_count($post->rendered_content, '<img '));
        $this->actingAs($author)->get(route('admin.posts.edit', $post))->assertOk()->assertSee($post->title);

        $post->update(['content' => 'Author edited content']);
        $this->artisan('blog:import-receipt-tracker', ['--author' => $author->id])->assertSuccessful();
        $this->assertDatabaseCount('posts', 1);
        $this->assertSame('Author edited content', $post->fresh()->content);
    }

    public function test_import_requires_an_existing_author(): void
    {
        $this->artisan('blog:import-receipt-tracker', ['--author' => 999])->assertFailed();
        $this->assertDatabaseCount('posts', 0);
    }
}
