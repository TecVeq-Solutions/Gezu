@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold">General Site Information & Brand Assets</h6>
                <small class="text-muted">Configure platform title, brand logos, favicon, primary accent color, and currency symbols.</small>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.general') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row gy-4">
                        <!-- Brand Identity Uploads -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Platform Logo</label>
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                <img src="{{ siteLogo() }}" alt="Current Logo" class="rounded border bg-white p-1" style="height: 48px; width: auto; max-width: 120px; object-fit: contain;">
                                <div class="flex-fill">
                                    <input type="file" name="site_logo" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted" style="font-size: 0.75rem;">PNG, SVG, or WEBP (Recommended: 250x60px)</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Platform Favicon</label>
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                <img src="{{ siteFavicon() }}" alt="Current Favicon" class="rounded border bg-white p-1" style="height: 48px; width: 48px; object-fit: contain;">
                                <div class="flex-fill">
                                    <input type="file" name="site_favicon" class="form-control form-control-sm" accept="image/*,.ico">
                                    <small class="text-muted" style="font-size: 0.75rem;">Square PNG, ICO (Recommended: 64x64px)</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Platform Name</label>
                            <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'TECVEQ' }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Base Currency Symbol / Code</label>
                            <input type="text" name="base_currency" class="form-control" value="{{ $settings['base_currency'] ?? 'USD' }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small">Site Tagline / Description</label>
                            <input type="text" name="site_tagline" class="form-control" value="{{ $settings['site_tagline'] ?? '' }}" placeholder="The Global Clipping Performance Marketplace">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Primary Brand Accent Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" value="{{ $settings['primary_color'] ?? '#6366f1' }}" style="max-width: 50px;" oninput="document.getElementById('color_hex').value = this.value">
                                <input type="text" id="color_hex" name="primary_color" class="form-control font-monospace" value="{{ $settings['primary_color'] ?? '#6366f1' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="las la-save me-1"></i> Save General Settings & Assets
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Admin Password Change Card -->
        <div class="card b-radius--10 border mt-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold"><i class="las la-user-shield text-primary me-1"></i> Admin Account Security & Password</h6>
                    <small class="text-muted">Update your administrator account login password with secure bcrypt hashing.</small>
                </div>
                <span class="badge badge--primary">Bcrypt Hash</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ Route::has('admin.settings.password') ? route('admin.settings.password') : url('/admin/settings/password') }}" method="POST">
                    @csrf

                    <div class="row gy-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small">Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Enter current admin password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="8" placeholder="Minimum 8 characters">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required minlength="8" placeholder="Repeat new password">
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="las la-key me-1"></i> Update Admin Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
