<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\PostImageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use Spatie\ResponseCache\Middlewares\DoNotCacheResponse;

// Public blog — cache these
Route::middleware([CacheResponse::class])->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('home');
    Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');
});

// Auth routes (Breeze)
Route::get('/media/posts/{user}/{filename}', [PostImageController::class, 'show'])
    ->whereNumber('user')->where('filename', '[A-Za-z0-9]+\.(jpg|jpeg|png|webp|gif)')->name('blog.images');

require __DIR__.'/auth.php';

// Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.posts.index');
})->middleware(['auth'])->name('dashboard');

// Admin — never cache these
Route::middleware(['auth', DoNotCacheResponse::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/post-images', [PostImageController::class, 'store'])->middleware('throttle:30,1')->name('posts.images');
    Route::post('/posts/preview', [PostController::class, 'preview'])->middleware('throttle:60,1')->name('posts.preview');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// Profile (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
