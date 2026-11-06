@extends('brand.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-1 fw-bold text-dark">
                    <i class="las la-id-badge text-primary me-1"></i> Brand Profile & Organization Settings
                </h5>
                <small class="text-muted">Manage your public brand identity, contact channels, and security credentials.</small>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="las la-check-circle fs-4"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                        <i class="las la-exclamation-triangle fs-4"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('brand.profile') }}" method="POST">
                    @csrf

                    <!-- 1. Organization Information -->
                    <h6 class="fw-bold small text-dark mb-3">
                        <i class="las la-building text-primary me-1"></i> Company Details
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Brand / Company Name <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" required value="{{ old('company_name', $brand->company_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                            <input type="email" name="contact_email" class="form-control" required value="{{ old('contact_email', $brand->contact_email ?? $user->email) }}">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Official Website</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-globe"></i></span>
                                <input type="url" name="website" class="form-control" placeholder="https://yourbrand.com" value="{{ old('website', $brand->website) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Industry / Sector</label>
                            <input type="text" name="industry" class="form-control" placeholder="e.g. AI SaaS, E-Commerce, Gaming" value="{{ old('industry', $brand->industry) }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Brand Bio & Default Creator Guidelines</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Briefly introduce your brand to creators who browse your campaigns...">{{ old('description', $brand->description) }}</textarea>
                    </div>

                    <hr class="my-4 border-light-subtle">

                    <!-- 2. Security & Password Credentials -->
                    <div id="password" class="card bg-light border rounded-3 p-3 mb-4">
                        <h6 class="fw-bold small text-dark mb-3">
                            <i class="las la-lock text-primary me-1"></i> Security & Password Update
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Current Password</label>
                                <input type="password" name="current_password" class="form-control" placeholder="••••••••">
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Required only to change password</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" placeholder="Min 8 characters">
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Leave blank to keep unchanged</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm password">
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Repeat new password</small>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Actions -->
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-brand px-4" style="height: 44px;">
                            <i class="las la-save me-1"></i> Save Profile Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
