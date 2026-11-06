@extends('clipper.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
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

        <form method="POST" action="{{ route('clipper.profile') }}">
            @csrf

            <!-- Creator Identity Card -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="las la-user-circle text-success fs-4"></i> Creator Identity &amp; Bio
                        </h5>
                        <small class="text-muted">Manage your public creator profile and editing specialties.</small>
                    </div>
                    <span class="badge bg-light text-dark border px-2.5 py-1">Role: Creator / Clipper</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Creator Bio &amp; Editing Specialties</label>
                            <textarea name="bio" rows="3" class="form-control" placeholder="Share your clipping background, preferred niches (gaming, tech, podcasts), and viral editing styles...">{{ old('bio', $creatorProfile->bio) }}</textarea>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Visible to brand advertisers when reviewing your submissions.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Connected Social Channels -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-share-alt text-primary fs-5"></i> Primary Social Channels
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">Link your active clipping accounts so submissions from these channels are auto-verified.</p>

                    @php
                        $tiktok = $socialAccounts->firstWhere('platform', 'TikTok')?->handle;
                        $youtube = $socialAccounts->firstWhere('platform', 'YouTube Shorts')?->handle;
                        $instagram = $socialAccounts->firstWhere('platform', 'Instagram Reels')?->handle;
                    @endphp

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label d-flex align-items-center gap-1.5">
                                <i class="fab fa-tiktok text-dark"></i> TikTok Handle
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="tiktok_handle" value="{{ old('tiktok_handle', ltrim($tiktok ?? '', '@')) }}" class="form-control" placeholder="your_tiktok">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label d-flex align-items-center gap-1.5">
                                <i class="fab fa-youtube text-danger"></i> YouTube Shorts
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="youtube_handle" value="{{ old('youtube_handle', ltrim($youtube ?? '', '@')) }}" class="form-control" placeholder="channel_handle">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label d-flex align-items-center gap-1.5">
                                <i class="fab fa-instagram text-danger"></i> Instagram Reels
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="instagram_handle" value="{{ old('instagram_handle', ltrim($instagram ?? '', '@')) }}" class="form-control" placeholder="insta_handle">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security & Password -->
            <div id="password" class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-lock text-warning fs-5"></i> Security &amp; Password Update
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep unchanged">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Minimum 8 characters.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Repeat new password.</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn btn-creator px-4" style="height: 44px;">
                    <i class="las la-save me-1"></i> Save Profile Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
