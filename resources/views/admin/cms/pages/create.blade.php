@extends('admin.layouts.app')

@section('panel')
<form action="{{ route('admin.cms.pages.store') }}" method="POST">
    @csrf
    <div class="row gy-4">

        <!-- Header -->
        <div class="col-12">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h4 class="mb-1 text-dark fw-bold"><i class="las la-plus-circle text-primary me-1"></i> Create New Page / Legal Document</h4>
                        <p class="text-muted mb-0">Publish dynamic pages, legal contracts, or custom landing templates.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary">
                            <i class="las la-arrow-left"></i> Back to Pages
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">
                            <i class="las la-save"></i> Publish Page
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
                        <input type="text" name="title" id="pageTitleInput" class="form-control form-control-lg fs-5" placeholder="e.g. Creator Payment Terms & Wire Protocol" value="{{ old('title') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">URL Permaslug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted font-monospace">{{ url('/p/') }}/</span>
                            <input type="text" name="slug" id="pageSlugInput" class="form-control font-monospace" placeholder="creator-payment-terms" value="{{ old('slug') }}">
                        </div>
                        <small class="text-muted">Leave empty to auto-generate from page title.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Excerpt / Meta Abstract</label>
                        <textarea name="excerpt" rows="2" class="form-control" placeholder="Brief summary of this document (displayed in cards and search previews)...">{{ old('excerpt') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Body (TECVEQ Rich Block Editor) <span class="text-danger">*</span></label>
                        <x-tecveq-rich-editor name="content" :value="old('content')" placeholder="Write document body, craft headings, insert blocks..." />
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
                :focusKeyword="old('meta_keywords')" 
                :customSchema="old('custom_schema', '')"
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
                            <option value="published" @selected(old('status') == 'published')>Published (Live to Public)</option>
                            <option value="draft" @selected(old('status') == 'draft')>Draft (Hidden)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Page Classification <span class="text-danger">*</span></label>
                        <select name="page_type" class="form-select" required>
                            <option value="standard" @selected(old('page_type') == 'standard')>Standard Content Page</option>
                            <option value="legal_terms" @selected(old('page_type') == 'legal_terms')>Legal: Terms of Service</option>
                            <option value="legal_privacy" @selected(old('page_type') == 'legal_privacy')>Legal: Privacy Policy</option>
                            <option value="legal_clipper" @selected(old('page_type') == 'legal_clipper')>Legal: Clipper Agreement</option>
                            <option value="legal_brand" @selected(old('page_type') == 'legal_brand')>Legal: Brand Guidelines</option>
                            <option value="about" @selected(old('page_type') == 'about')>Company: About Us</option>
                            <option value="contact" @selected(old('page_type') == 'contact')>Company: Contact</option>
                        </select>
                        <small class="text-muted d-block mt-1">Classifies page for legal disclosures and navigation filters.</small>
                    </div>

                    <hr>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="las la-check-circle"></i> Save & Publish Document
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

<script>
document.getElementById('pageTitleInput')?.addEventListener('input', function() {
    const slugInput = document.getElementById('pageSlugInput');
    if (slugInput && !slugInput.dataset.touched) {
        slugInput.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
    }
});
document.getElementById('pageSlugInput')?.addEventListener('input', function() {
    this.dataset.touched = true;
});
</script>
<x-tecveq-file-manager-modal />
@endsection
