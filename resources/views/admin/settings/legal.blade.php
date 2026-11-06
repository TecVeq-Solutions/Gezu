@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold">Legal Agreements & Global SEO Policy</h6>
                <small class="text-muted">Edit platform terms of service, privacy policies, and public search engine meta tags.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.legal') }}" method="POST">
                    @csrf

                    <div class="row gy-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small">SEO Meta Title</label>
                            <input type="text" name="seo_meta_title" class="form-control" value="{{ $settings['seo_meta_title'] ?? '' }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">SEO Meta Description</label>
                            <textarea name="seo_meta_description" class="form-control" rows="2" required>{{ $settings['seo_meta_description'] ?? '' }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">SEO Keywords</label>
                            <input type="text" name="seo_meta_keywords" class="form-control" value="{{ $settings['seo_meta_keywords'] ?? '' }}" placeholder="clipping, bounties, viral marketing, tecveq">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Terms of Service Content</label>
                            <textarea name="terms_of_service_content" class="form-control font-monospace" rows="5" required>{{ $settings['terms_of_service_content'] ?? '' }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Privacy Policy Content</label>
                            <textarea name="privacy_policy_content" class="form-control font-monospace" rows="5" required>{{ $settings['privacy_policy_content'] ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="las la-save me-1"></i> Save Legal & SEO Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
