@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">
    <!-- Left Column: Admin Account Overview -->
    <div class="col-xl-4 col-lg-5">
        <div class="card b-radius--10 overflow-hidden border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="p-4 bg--dark text-center position-relative" style="background: linear-gradient(135deg, #0b0b10 0%, #1e1b4b 100%);">
                    <div class="avatar avatar--xl mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255, 255, 255, 0.15);">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <h5 class="text-white mb-1 fw-bold">{{ auth()->user()->name ?? 'Super Administrator' }}</h5>
                    <span class="badge badge--success px-3 py-1 font-monospace">{{ strtoupper(auth()->user()->role ?? 'ADMIN') }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="las la-envelope me-1 text-primary"></i> Email</span>
                        <span class="fw-bold font-monospace text-dark">{{ auth()->user()->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="las la-user-shield me-1 text-warning"></i> Access Role</span>
                        <span class="badge badge--primary">{{ ucfirst(auth()->user()->role) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="las la-calendar me-1 text-info"></i> Member Since</span>
                        <span class="fw-semibold text-secondary">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'Active' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                        <span class="text-muted"><i class="las la-shield-alt me-1 text-success"></i> Security Status</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Active & Protected</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Password Change Form -->
    <div class="col-xl-8 col-lg-7">
        <div class="card b-radius--10 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="las la-key text-primary me-1"></i> Update Admin Password
                    </h5>
                    <small class="text-muted">Ensure your account uses a strong, random password with letters, numbers, and symbols.</small>
                </div>
                <span class="badge bg-light text-dark border">Bcrypt Hashing</span>
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

                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">
                            Current Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="las la-lock"></i></span>
                            <input type="password" name="current_password" class="form-control" required placeholder="Enter active password" autocomplete="current-password">
                        </div>
                        <div class="form-text small text-muted">Required to authorize authentication changes.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">
                                New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="las la-key"></i></span>
                                <input type="password" name="new_password" class="form-control" required minlength="8" placeholder="Min 8 characters" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">
                                Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="las la-check-double"></i></span>
                                <input type="password" name="new_password_confirmation" class="form-control" required minlength="8" placeholder="Repeat new password" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('admin.settings.general') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="las la-cog me-1"></i> General Settings
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="las la-save me-1"></i> Save New Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
