@extends('streamer.layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="las la-user-circle text-danger me-2"></i> Streamer Profile Settings
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('streamer.profile') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address (Account Login)</label>
                        <input type="email" value="{{ $user->email }}" class="form-control" disabled readonly style="background-color: #f1f5f9;">
                        <small class="text-muted">Contact platform support if you need to update your registered email address.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Creator / Streamer Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" value="{{ old('display_name', $profile->display_name ?? $user->name) }}" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bio &amp; Streamer Summary</label>
                        <textarea name="bio" rows="3" class="form-control" placeholder="Share your YouTube niches, streaming schedule, and brand deal experience...">{{ old('bio', $profile->bio ?? '') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Country Code (ISO 2-letter)</label>
                        <input type="text" name="country" value="{{ old('country', $profile->country ?? 'US') }}" class="form-control" maxlength="5">
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('streamer.password') }}" class="btn btn-outline-secondary">
                            <i class="las la-key me-1"></i> Change Password
                        </a>
                        <button type="submit" class="btn btn-streamer text-white fw-bold px-4">
                            <i class="las la-save me-1"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
