<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Soft deletes
            $table->softDeletes();

            // Indexes for common queries
            $table->index('status');
            $table->index('published_at');
            $table->index('user_id');
            $table->index(['status', 'published_at']); // composite for published scope
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status', 'published_at']);
        });
    }
};
