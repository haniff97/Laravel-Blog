<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostEditorTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_requires_authentication(): void
    {
        Storage::fake('public');
        $this->postJson('/admin/post-images', ['image' => UploadedFile::fake()->image('photo.png')])->assertUnauthorized();
        $this->postJson('/admin/posts/preview', ['content' => 'Test'])->assertUnauthorized();
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_image_can_be_uploaded_used_in_a_post_and_served_publicly(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $upload = $this->actingAs($user)->postJson('/admin/post-images', [
            'image' => UploadedFile::fake()->image('receipt.png'),
        ])->assertCreated();
        $url = $upload->json('url');
        $files = Storage::disk('public')->allFiles('posts/'.$user->id);
        $this->assertCount(1, $files);
        $this->assertStringStartsWith('/media/posts/'.$user->id.'/', $url);

        $this->post('/admin/posts', [
            'title' => 'Post with a picture', 'content' => '![Receipt]('.$url.')', 'status' => 'published',
        ])->assertRedirect(route('admin.posts.index'));
        $post = Post::sole();
        $this->assertStringContainsString('<img src="'.$url.'"', $post->rendered_content);
        $this->app['auth']->forgetGuards();
        $this->get($url)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->get('/media/posts/'.$user->id.'/missing.png')->assertNotFound();
    }

    public function test_invalid_and_oversized_images_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        foreach ([UploadedFile::fake()->create('file.svg', 1, 'image/svg+xml'), UploadedFile::fake()->image('big.jpg')->size(5121)] as $file) {
            $this->postJson('/admin/post-images', ['image' => $file])->assertUnprocessable()->assertJsonValidationErrors('image');
        }
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_preview_uses_safe_markdown_rendering(): void
    {
        $this->actingAs(User::factory()->create());
        $html = $this->postJson('/admin/posts/preview', [
            'content' => '**Hello** <script>alert(1)</script> [unsafe](javascript:alert(1))',
        ])->assertOk()->json('html');
        $this->assertStringContainsString('<strong>Hello</strong>', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('href="javascript:', $html);
        $this->get('/admin/posts/create')->assertOk()->assertSee('data-post-editor', false)->assertSee('Add pictures');
    }
}
