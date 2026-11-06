@extends('admin.layouts.app')

@section('panel')
<form action="{{ route('admin.cms.pages.update', $page->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row gy-4">

        <!-- Header -->
        <div class="col-12">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h4 class="mb-0 text-dark fw-bold"><i class="las la-edit text-primary me-1"></i> Edit Page: {{ $page->title }}</h4>
                            @if($page->is_system)
                                <span class="badge bg-secondary">SYSTEM CORE</span>
                            @endif
                        </div>
                        <p class="text-muted mb-0">Live URL: <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="text-primary font-monospace">{{ url('/p/' . $page->slug) }} <i class="las la-external-link-alt"></i></a></p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="btn btn-outline-info">
                            <i class="las la-eye"></i> View Live
                        </a>
                        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary">
                            <i class="las la-arrow-left"></i> Back to Pages
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="las la-save"></i> Update Page
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-8 col-lg-7">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-pen text-primary me-1"></i> Page Content & Typography</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-lg fs-5" value="{{ old('title', $page->title) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">URL Permaslug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted font-monospace">{{ url('/p/') }}/</span>
                            <input type="text" name="slug" class="form-control font-monospace" value="{{ old('slug', $page->slug) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Excerpt / Meta Abstract</label>
                        <textarea name="excerpt" rows="2" class="form-control">{{ old('excerpt', $page->excerpt) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Body (TECVEQ Rich Block Editor) <span class="text-danger">*</span></label>
                        <x-tecveq-rich-editor name="content" :value="old('content', $page->content)" placeholder="Write document body, craft headings, insert blocks..." />
                    </div>
                </div>
            </div>

            <!-- TECVEQ SEO Suite (Search Architecture Engine) -->
            <x-tecveq-seo-meta-box 
                :title="$page->title" 
                :slug="$page->slug" 
                :metaTitle="$page->meta_title" 
                :metaDescription="$page->meta_description" 
                :metaKeywords="$page->meta_keywords" 
                :focusKeyword="$page->meta_keywords" 
                :customSchema="$page->custom_schema ?? ''"
                baseUrl="{{ url('/p/') }}/"
                schemaType="WebPage" 
            />
        </div>

        <!-- Sidebar Options -->
        <div class="col-xl-4 col-lg-5">
            <!-- Publishing Attributes -->
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-cog text-primary me-1"></i> Document Settings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Publication Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="published" @selected(old('status', $page->status) == 'published')>Published (Live to Public)</option>
                            <option value="draft" @selected(old('status', $page->status) == 'draft')>Draft (Hidden)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Classification <span class="text-danger">*</span></label>
                        <select name="page_type" class="form-select" required>
                            <option value="standard" @selected(old('page_type', $page->page_type) == 'standard')>Standard Content Page</option>
                            <option value="legal_terms" @selected(old('page_type', $page->page_type) == 'legal_terms')>Legal: Terms of Service</option>
                            <option value="legal_privacy" @selected(old('page_type', $page->page_type) == 'legal_privacy')>Legal: Privacy Policy</option>
                            <option value="legal_clipper" @selected(old('page_type', $page->page_type) == 'legal_clipper')>Legal: Clipper Agreement</option>
                            <option value="legal_brand" @selected(old('page_type', $page->page_type) == 'legal_brand')>Legal: Brand Guidelines</option>
                            <option value="about" @selected(old('page_type', $page->page_type) == 'about')>Company: About Us</option>
                            <option value="contact" @selected(old('page_type', $page->page_type) == 'contact')>Company: Contact</option>
                        </select>
                    </div>

                    <div class="p-3 bg-light rounded small text-muted mb-3">
                        <div><strong>Created:</strong> {{ $page->created_at->format('M d, Y H:i') }}</div>
                        <div class="mt-1"><strong>Last Modified:</strong> {{ $page->updated_at->diffForHumans() }}</div>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="las la-save"></i> Save Page Changes
                        </button>
                        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary">
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
