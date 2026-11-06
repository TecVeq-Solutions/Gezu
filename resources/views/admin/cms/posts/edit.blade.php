@extends('admin.layouts.app')

@section('panel')
<form action="{{ route('admin.cms.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row gy-4">

        <!-- Header -->
        <div class="col-12">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="mb-0 text-dark fw-bold"><i class="las la-edit text-primary me-1"></i> Edit Article: {{ $post->title }}</h4>
                        </div>
                        <p class="text-muted mb-0">Live URL: <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="text-primary font-monospace">{{ url('/blog/' . $post->slug) }} <i class="las la-external-link-alt"></i></a></p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="btn btn-outline-info">
                            <i class="las la-eye"></i> View Live
                        </a>
                        <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="las la-arrow-left"></i> Back to Posts
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="las la-save"></i> Update Article
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Column -->
        <div class="col-xl-8 col-lg-7">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-align-left text-primary me-1"></i> Article Body & Copy</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg fs-5" value="{{ old('title', $post->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">URL Permaslug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted font-monospace">{{ url('/blog/') }}/</span>
                            <input type="text" name="slug" class="form-control font-monospace" value="{{ old('slug', $post->slug) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Excerpt / Teaser</label>
                        <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Article Content (TECVEQ Rich Block Editor) <span class="text-danger">*</span></label>
                        <x-tecveq-rich-editor name="content" :value="old('content', $post->content)" placeholder="Write your story, craft headings, insert blocks..." />
                    </div>
                </div>
            </div>

            <!-- TECVEQ SEO Suite (Search Architecture Engine) -->
            <x-tecveq-seo-meta-box 
                :title="$post->title" 
                :slug="$post->slug" 
                :metaTitle="$post->meta_title" 
                :metaDescription="$post->meta_description" 
                :metaKeywords="$post->meta_keywords" 
                :focusKeyword="$post->meta_keywords" 
                :customSchema="$post->custom_schema ?? ''"
                schemaType="BlogPosting" 
            />
        </div>

        <!-- Sidebar Attributes Column -->
        <div class="col-xl-4 col-lg-5">
            <!-- Publishing Attributes -->
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-sliders-h text-primary me-1"></i> Publication Settings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Publication Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="published" @selected(old('status', $post->status) == 'published')>Published (Live Immediately)</option>
                            <option value="draft" @selected(old('status', $post->status) == 'draft')>Draft (Private)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeaturedSwitch" value="1" @checked(old('is_featured', $post->is_featured))>
                        <label class="form-check-label fw-bold text-dark" for="isFeaturedSwitch">Pin as Featured Post</label>
                    </div>

                    <div class="p-3 bg-light rounded small text-muted">
                        <div><strong>Total Page Views:</strong> {{ number_format($post->views) }}</div>
                        <div class="mt-1"><strong>Author:</strong> {{ $post->author->name ?? 'Admin' }}</div>
                        <div class="mt-1"><strong>Created:</strong> {{ $post->created_at->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Featured Image Card -->
            <div class="card b-radius--10 border shadow-sm mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-image text-primary me-1"></i> Featured Image Asset</h6>
                </div>
                <div class="card-body p-4">
                    @if($post->featured_image)
                        <div class="mb-3 rounded overflow-hidden border">
                            <img src="{{ $post->featured_image }}" alt="" class="w-100 object-fit-cover" style="max-height: 180px;">
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Replace Image File</label>
                        <input type="file" name="featured_image_file" class="form-control" accept="image/*">
                    </div>

                    <div class="text-center my-2 text-muted small fw-bold">OR PROVIDE ASSET URL</div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Featured Image Direct URL</label>
                        <input type="text" name="featured_image_url" class="form-control" value="{{ old('featured_image_url', $post->featured_image) }}">
                    </div>
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="card b-radius--10 border shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="las la-save"></i> Update Article
                        </button>
                        <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>
<x-tecveq-file-manager-modal />
@endsection
