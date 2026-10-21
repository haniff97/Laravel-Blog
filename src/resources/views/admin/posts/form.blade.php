@php($editing = isset($post))
<div class="post-editor admin-page" data-post-editor data-upload-url="{{ route('admin.posts.images') }}" data-preview-url="{{ route('admin.posts.preview') }}">
    <form method="POST" action="{{ $editing ? route('admin.posts.update', $post) : route('admin.posts.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="editor-heading">
            <div><a class="text-link" href="{{ route('admin.posts.index') }}">&larr; Back to posts</a><h1>{{ $editing ? 'Edit post' : 'New post' }}</h1></div>
            <button class="editor-save" type="submit"><i data-lucide="save" aria-hidden="true"></i>{{ $editing ? 'Save changes' : 'Save post' }}</button>
        </div>
        @if($errors->any())<div class="editor-errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <div class="editor-layout">
            <div class="editor-main">
                <label for="title">Title</label>
                <input class="editor-title" type="text" id="title" name="title" value="{{ old('title', $post->title ?? '') }}" maxlength="255" placeholder="Give your story a title" required>
                <div class="editor-content-label"><label for="content">Content</label><span data-word-count>0 words</span></div>
                <div class="editor-surface">
                    <div class="editor-toolbar">
                        <div class="editor-tools" aria-label="Formatting">
                            <button type="button" data-format="bold" title="Bold" aria-label="Bold"><i data-lucide="bold"></i></button>
                            <button type="button" data-format="italic" title="Italic" aria-label="Italic"><i data-lucide="italic"></i></button>
                            <button type="button" data-format="heading" title="Heading" aria-label="Heading"><i data-lucide="heading-2"></i></button>
                            <button type="button" data-format="link" title="Insert link" aria-label="Insert link"><i data-lucide="link"></i></button>
                            <button type="button" data-pick-image title="Add pictures" aria-label="Add pictures"><i data-lucide="image-plus"></i></button>
                        </div>
                        <div class="editor-modes" role="group" aria-label="Editor mode"><button type="button" data-mode="write" aria-pressed="true">Write</button><button type="button" data-mode="preview" aria-pressed="false">Preview</button></div>
                    </div>
                    <textarea id="content" name="content" required placeholder="Start your story...">{{ old('content', $post->content ?? '') }}</textarea>
                    <div class="editor-preview article-content prose max-w-none" data-preview hidden aria-label="Post preview" aria-live="polite"></div>
                </div>
                <p class="editor-feedback" data-feedback role="status" aria-live="polite"></p>
            </div>
            <aside class="editor-sidebar" aria-label="Post settings">
                <section><h2>Publishing</h2><label for="status">Status</label><select id="status" name="status"><option value="draft" @selected(old('status', $post->status ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(old('status', $post->status ?? 'draft') === 'published')>Published</option></select>
                    @if($editing && $post->status === 'published')<a class="text-link editor-view" href="{{ route('blog.show', $post) }}" target="_blank" rel="noopener">View published post &nearr;</a>@endif
                </section>
                <section><h2>Excerpt</h2><label class="sr-only" for="excerpt">Excerpt</label><textarea id="excerpt" name="excerpt" maxlength="500" rows="4" placeholder="A short summary for the blog listing">{{ old('excerpt', $post->excerpt ?? '') }}</textarea><span class="editor-optional">Optional</span></section>
                <section><h2>Pictures</h2><button class="editor-upload" type="button" data-pick-image><i data-lucide="image-plus" aria-hidden="true"></i>Add pictures</button><input type="file" data-image-input accept="image/jpeg,image/png,image/webp,image/gif" multiple hidden aria-label="Choose pictures"><p class="editor-file-types">JPG, PNG, WebP or GIF · Up to 5 MB each</p><div class="editor-image-list" data-image-list></div></section>
            </aside>
        </div>
    </form>
</div>
