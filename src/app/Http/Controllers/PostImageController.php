<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120']]);
        $path = $request->file('image')->store('posts/'.$request->user()->id, 'public');
        abort_unless($path, 500, 'Image could not be saved.');

        return response()->json([
            'url' => route('blog.images', ['user' => $request->user()->id, 'filename' => basename($path)], false),
        ], 201);
    }

    public function show(string $user, string $filename)
    {
        $path = 'posts/'.$user.'/'.$filename;
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, ['X-Content-Type-Options' => 'nosniff']);
    }
}
