@extends('admin.layouts.app')

@section('panel')
<form action="{{ route('admin.cms.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row gy-4">

        <!-- Header -->
        <div class="col-12">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h4 class="mb-1 text-dark fw-bold"><i class="las la-pen-fancy text-primary me-1"></i> Compose Blog Article</h4>
                        <p class="text-muted mb-0">Craft long-form editorial content, tutorials, and ecosystem announcements.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="las la-arrow-left"></i> Back to Posts
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="las la-check-circle"></i> Publish Article
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
                        <input type="text" name="title" id="postTitleInput" class="form-control form-control-lg fs-5" placeholder="e.g. How Top Creators Earn $5,000/Month Clipping Podcasts" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">URL Permaslug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted font-monospace">{{ url('/blog/') }}/</span>
                            <input type="text" name="slug" id="postSlugInput" class="form-control font-monospace" placeholder="how-top-creators-earn" value="{{ old('slug') }}">
                        </div>
                        <small class="text-muted">Auto-generated from title if left blank.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Excerpt / Teaser</label>
                        <textarea name="excerpt" rows="2" class="form-control" placeholder="Compelling 1-2 sentence preview for cards and social shares...">{{ old('excerpt') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Article Content (TECVEQ Rich Block Editor) <span class="text-danger">*</span></label>
                        <x-tecveq-rich-editor name="content" :value="old('content')" placeholder="Write your story, craft headings, insert blocks..." />
                    </div>
                </div>
            </div>

            <!-- TECVEQ SEO Suite (Search Architecture Engine) -->
            <x-tecveq-seo-meta-box 
                :title="old('title')" 
                :slug="old('slug')" 
                :metaTitle="old('meta_title')" 
                :metaDescription="old('meta_description')" 
                :metaKeywords="old('meta_keywords')" 
                :focusKeyword="old('focus_keyword')" 
                :customSchema="old('custom_schema', '')"
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
                            <option value="published" @selected(old('status') == 'published')>Published (Live Immediately)</option>
                            <option value="draft" @selected(old('status') == 'draft')>Draft (Private)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" name="is_featured" class="form-check-input" id="isFeaturedSwitch" value="1" @checked(old('is_featured'))>
                        <label class="form-check-label fw-bold text-dark" for="isFeaturedSwitch">Pin as Featured Post</label>
                        <small class="text-muted d-block">Featured posts appear at the top of the blog showcase.</small>
                    </div>
                </div>
            </div>

            <!-- Featured Image Card -->
            <div class="card b-radius--10 border shadow-sm mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-image text-primary me-1"></i> Featured Image Asset</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Upload Image File</label>
                        <input type="file" name="featured_image_file" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: 1200×630px JPG, PNG, or WEBP.</small>
                    </div>

                    <div class="text-center my-2 text-muted small fw-bold">OR PROVIDE ASSET URL</div>

                    <div class="mb-0">
                        <label class="form-label fw-bold">Featured Image Direct URL</label>
                        <input type="text" name="featured_image_url" class="form-control" placeholder="https://images.unsplash.com/photo-..." value="{{ old('featured_image_url') }}">
                    </div>
                </div>
            </div>

            <!-- Submit Button Card -->
            <div class="card b-radius--10 border shadow-sm mt-4">
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="las la-save"></i> Save & Publish Article
                        </button>
                        <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary">
                            Discard
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
document.getElementById('postTitleInput')?.addEventListener('input', function() {
    const slugInput = document.getElementById('postSlugInput');
    if (slugInput && !slugInput.dataset.touched) {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    }
});
document.getElementById('postSlugInput')?.addEventListener('input', function() {
    this.dataset.touched = true;
});
</script>
<x-tecveq-file-manager-modal />
@endsection
