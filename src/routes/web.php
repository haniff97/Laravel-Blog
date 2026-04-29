<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public blog
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('blog.show');

// Auth routes (Breeze)
require __DIR__.'/auth.php';

// Dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.posts.index');
})->middleware(['auth'])->name('dashboard');

// Admin post management
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/posts', [PostController::class, 'adminIndex'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
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
