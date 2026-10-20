<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\ResponseCache\Facades\ResponseCache;

class ImportReceiptTrackerPost extends Command
{
    protected $signature = 'blog:import-receipt-tracker {--author= : Existing author user ID}';

    protected $description = 'Import the Receipt Tracker article as a published Markdown blog post';

    public function handle(): int
    {
        if (Post::withTrashed()->where('slug', 'receipt-tracker')->exists()) {
            $this->info('Receipt Tracker already exists; existing content was preserved.');

            return self::SUCCESS;
        }

        $author = $this->option('author')
            ? User::find($this->option('author'))
            : (User::count() === 1 ? User::first() : null);

        if (! $author) {
            $this->error('Choose an existing author with --author=USER_ID.');

            return self::FAILURE;
        }

        $content = file_get_contents(resource_path('content/receipt-tracker.md'));

        DB::transaction(fn () => Post::create([
            'user_id' => $author->id,
            'title' => 'Receipt Tracker: scanning receipts and understanding expenses',
            'slug' => 'receipt-tracker',
            'excerpt' => 'A look at what the project does, how a receipt becomes a saved transaction, and where AI fits into the process.',
            'content' => $content,
            'status' => 'published',
            'published_at' => now(),
        ]));

        ResponseCache::clear();
        $this->info('Receipt Tracker published at /blog/receipt-tracker.');

        return self::SUCCESS;
    }
}
