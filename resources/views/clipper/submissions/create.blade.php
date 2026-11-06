@extends('clipper.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-8 col-lg-10">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-cloud-upload-alt text-success fs-4"></i> Submit Published Video Clip
                    </h5>
                    <small class="text-muted">Submit your public short-form clip to qualify for verified CPM views payouts.</small>
                </div>
                <a href="{{ route('clipper.submissions') }}" class="btn btn-sm btn-outline-secondary" style="height: 36px;">
                    <i class="las la-arrow-left me-1"></i> My Submissions
                </a>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('clipper.submissions.store') }}">
                    @csrf

                    @if(!empty($requireSocial))
                        <div class="p-3 mb-4 rounded-3 border bg-light">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <span class="fw-bold text-dark small"><i class="las la-shield-alt text-primary me-1"></i> Social Channel Connection Policy: ACTIVE</span>
                                <a href="{{ route('clipper.profile') }}#social-channels" class="btn btn-outline-primary btn-sm py-1 px-2" style="font-size: 11px;">
                                    <i class="las la-cog me-1"></i> Manage Social Channels
                                </a>
                            </div>
                            <p class="text-muted small mb-2" style="font-size: 12px;">Admin requires you to connect your verified social handle before submitting clips. Only videos from your connected channels will be eligible for reward verification.</p>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $tiktokLinked = isset($connectedAccounts['tiktok']) && !empty($connectedAccounts['tiktok']->handle);
                                    $youtubeLinked = isset($connectedAccounts['youtube']) && !empty($connectedAccounts['youtube']->handle);
                                    $instagramLinked = isset($connectedAccounts['instagram']) && !empty($connectedAccounts['instagram']->handle);
                                @endphp
                                <span class="badge {{ $tiktokLinked ? 'badge--success' : 'badge--warning' }} p-2">
                                    <i class="fab fa-tiktok me-1"></i> TikTok: {{ $tiktokLinked ? $connectedAccounts['tiktok']->handle : 'Not Connected' }}
                                </span>
                                <span class="badge {{ $youtubeLinked ? 'badge--success' : 'badge--warning' }} p-2">
                                    <i class="fab fa-youtube me-1"></i> YouTube: {{ $youtubeLinked ? $connectedAccounts['youtube']->handle : 'Not Connected' }}
                                </span>
                                <span class="badge {{ $instagramLinked ? 'badge--success' : 'badge--warning' }} p-2">
                                    <i class="fab fa-instagram me-1"></i> Instagram: {{ $instagramLinked ? $connectedAccounts['instagram']->handle : 'Not Connected' }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <!-- 1. Campaign Bounty Select -->
                    <div class="mb-4">
                        <label class="form-label">Target Bounty Campaign <span class="text-danger">*</span></label>
                        <select name="campaign_id" class="form-select" required>
                            <option value="">-- Choose an Active Campaign Bounty --</option>
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}" {{ (old('campaign_id') == $camp->id || $preselectedCampaignId == $camp->id) ? 'selected' : '' }}>
                                    {{ $camp->title }} (Rate: ${{ number_format($camp->reward_rate_per_thousand, 2) }}/1K | Remaining: ${{ number_format($camp->remaining_budget, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1" style="font-size: 12px;">Choose which brand bounty you are submitting content for.</small>
                    </div>

                    <!-- 2. Platform Selection (Balanced Checkable Cards) -->
                    <div class="mb-4">
                        <label class="form-label d-block">Social Platform <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input type="radio" name="platform" value="TikTok" checked class="form-check-input m-0 flex-shrink-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-tiktok fs-5 text-dark"></i>
                                        <span class="fw-semibold small text-dark">TikTok</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input type="radio" name="platform" value="YouTube Shorts" class="form-check-input m-0 flex-shrink-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-youtube fs-5 text-danger"></i>
                                        <span class="fw-semibold small text-dark">YouTube Shorts</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input type="radio" name="platform" value="Instagram Reels" class="form-check-input m-0 flex-shrink-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-instagram fs-5 text-danger"></i>
                                        <span class="fw-semibold small text-dark">Instagram Reels</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Direct Post URL -->
                    <div class="mb-4">
                        <label class="form-label">Published Clip URL <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="las la-link"></i></span>
                            <input type="url" name="post_url" value="{{ old('post_url') }}" class="form-control font-monospace" placeholder="https://www.tiktok.com/@creator/video/..." required>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 12px;">Make sure the video is public and accessible so views can be observed and verified.</small>
                    </div>

                    <!-- 4. Channel Handle -->
                    <div class="mb-4">
                        <label class="form-label">Your Channel / Account Handle (Optional)</label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" name="channel_handle" value="{{ old('channel_handle') }}" class="form-control" placeholder="username">
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 12px;">Used for automatic verification against your creator profile.</small>
                    </div>

                    <!-- 5. Direct Bank Transfer Notice -->
                    <div class="p-3 bg-light rounded-3 mb-4 border d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                            <i class="las la-university fs-4"></i>
                        </div>
                        <div class="small text-muted">
                            <strong class="text-dark d-block">Direct Bank Payout Guarantee:</strong>
                            Once your clip is reviewed and approved by the brand, the Admin will initiate a direct bank transfer to your configured bank account.
                        </div>
                    </div>

                    <!-- 6. Submit Action -->
                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('clipper.submissions') }}" class="btn btn-outline-secondary px-4" style="height: 44px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-creator px-4" style="height: 44px;">
                            <i class="las la-check-circle me-1"></i> Confirm &amp; Submit Video Clip
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
