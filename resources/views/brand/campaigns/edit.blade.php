@extends('brand.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark">
                        <i class="las la-edit text-primary me-1"></i> Edit Performance Campaign
                    </h5>
                    <small class="text-muted">Campaign #{{ $campaign->id }} &bull; {{ $campaign->title }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($campaign->status === 'draft')
                        <a href="{{ route('brand.campaigns.checkout', $campaign->id) }}" class="btn btn-sm btn-success fw-bold" style="height: 36px; display: inline-flex; align-items: center;">
                            <i class="las la-credit-card me-1"></i> Fund Campaign (${{ number_format($campaign->total_budget, 2) }})
                        </a>
                    @else
                        <a href="{{ route('web.campaign.detail', $campaign->id) }}" target="_blank" class="btn btn-sm btn-outline-info" style="height: 36px;">
                            <i class="las la-external-link-alt me-1"></i> View Public Briefing
                        </a>
                    @endif
                    <a href="{{ route('brand.campaigns') }}" class="btn btn-sm btn-outline-secondary" style="height: 36px;">
                        <i class="las la-arrow-left me-1"></i> Back to Campaigns
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                @if($campaign->status === 'draft')
                    <div class="alert alert-warning border border-warning d-flex align-items-center justify-content-between p-3 mb-4 rounded-3 shadow-xs flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <i class="las la-exclamation-triangle fs-2 text-warning"></i>
                            <div>
                                <strong class="d-block text-dark">Campaign Unfunded (Draft Status)</strong>
                                <small class="text-muted">This campaign is not published on the marketplace yet because the bounty deposit is pending.</small>
                            </div>
                        </div>
                        <a href="{{ route('brand.campaigns.checkout', $campaign->id) }}" class="btn btn-success btn-sm px-3 fw-bold flex-shrink-0" style="height: 38px; display: inline-flex; align-items: center;">
                            <i class="las la-credit-card me-1"></i> Deposit & Fund via Stripe (${{ number_format($campaign->total_budget, 2) }})
                        </a>
                    </div>
                @endif

                <form action="{{ route('brand.campaigns.update', $campaign->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- 1. Title & Category -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @php
                                    $cats = ['Technology & AI', 'Gaming & Esports', 'E-commerce & DTC', 'Crypto & Web3', 'Lifestyle & Fashion', 'Finance & SaaS', 'Health & Fitness'];
                                @endphp
                                @foreach($cats as $cat)
                                    <option value="{{ $cat }}" {{ $campaign->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 2. Video Assets Studio (Upload or URL) -->
                    <div class="card border rounded-3 p-3 mb-4 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold text-dark mb-0">
                                <i class="las la-video text-primary me-1"></i> Campaign Video Footage
                            </label>
                            <span class="badge bg-primary-subtle text-primary border">
                                <i class="las la-photo-video me-1"></i> GEZUX Media Library
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">Upload New Video File (MP4, WebM, MOV)</label>
                                <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/quicktime,video/x-m4v">
                                <small class="text-muted d-block mt-1">Cataloged in Media Library under Campaign Videos.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Current Video / Cloud Assets URL (Google Drive, Dropbox, YouTube)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="las la-link"></i></span>
                                    <input type="url" name="asset_url" class="form-control" value="{{ old('asset_url', $campaign->asset_url ?: $campaign->source_video_url) }}" placeholder="https://drive.google.com/... or https://youtube.com/...">
                                </div>
                                <small class="text-muted d-block mt-1">Direct link where creators download raw footage and creative assets.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Read-Only Budget Economics Row -->
                    <div class="card bg-white border p-3 mb-4 rounded-3">
                        <h6 class="fw-bold small text-dark mb-3">
                            <i class="las la-coins text-primary me-1"></i> Locked Campaign Financials
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Allocated Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">$</span>
                                    <input type="text" class="form-control font-monospace bg-light fw-bold" value="{{ number_format($campaign->total_budget, 2) }}" readonly>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Initial campaign deposit</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Remaining Budget</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">$</span>
                                    <input type="text" class="form-control font-monospace bg-light fw-bold text-primary" value="{{ number_format($campaign->remaining_budget, 2) }}" readonly>
                                </div>
                                <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Available bounty balance</small>
                            </div>

                            <div class="col-md-4">
                                @if($campaign->isFixedPerVideo())
                                    <label class="form-label">Fixed Bounty / Video</label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">$</span>
                                        <input type="text" class="form-control font-monospace bg-light fw-bold text-purple" style="color: #7e22ce;" value="{{ number_format($campaign->reward_per_video, 2) }}" readonly>
                                        <span class="input-group-text small text-muted">/ video</span>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Target: {{ $campaign->formatted_expected_views ?: '80k views/video' }}</small>
                                @else
                                    <label class="form-label">Payout Rate (CPM)</label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold">$</span>
                                        <input type="text" class="form-control font-monospace bg-light fw-bold text-success" value="{{ number_format($campaign->reward_rate_per_thousand, 2) }}" readonly>
                                        <span class="input-group-text small text-muted">/ 1k views</span>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Locked rate per 1k views</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. Schedule & Targets -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Target Views</label>
                            <input type="number" name="target_views" class="form-control font-monospace" value="{{ old('target_views', $campaign->target_views) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Campaign Status</label>
                            @if($campaign->status === 'draft')
                                <select name="status_disabled" class="form-select bg-light" disabled>
                                    <option value="draft" selected>Draft (Awaiting Stripe Bounty Deposit)</option>
                                </select>
                                <input type="hidden" name="status" value="draft">
                                <small class="text-danger mt-1 d-block" style="font-size: 11.5px;">Must be funded via Stripe before it can become Active.</small>
                            @else
                                <select name="status" class="form-select">
                                    <option value="active" {{ $campaign->status === 'active' ? 'selected' : '' }}>Active (Accepting Submissions)</option>
                                    <option value="paused" {{ $campaign->status === 'paused' ? 'selected' : '' }}>Paused (Temporarily Closed)</option>
                                </select>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', $campaign->start_at ? $campaign->start_at->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', $campaign->end_at ? $campaign->end_at->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <!-- 5. Briefing & Rules -->
                    <div class="mb-4">
                        <label class="form-label">Creator Briefing &amp; Campaign Overview</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Specify video requirements, mandatory hashtags, talking points, and audio guidelines...">{{ old('description', $campaign->description) }}</textarea>
                    </div>

                    <!-- 5.1 Specific Clipping Rules & Requirements -->
                    <div class="mb-4">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span><i class="las la-shield-alt text-warning me-1"></i> Specific Clipping Rules &amp; Verification Criteria</span>
                            <span class="badge bg-light text-muted border">One rule per line</span>
                        </label>
                        <textarea name="rules" class="form-control font-monospace" rows="5" placeholder="- Rule 1&#10;- Rule 2&#10;- Rule 3">{{ old('rules', $campaign->rules) }}</textarea>
                        <small class="text-muted mt-1 d-block">These rules will appear directly on the campaign page with verification checkmarks for clippers and streamers.</small>
                    </div>

                    <!-- 6. Action Buttons -->
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('brand.campaigns') }}" class="btn btn-outline-secondary px-3" style="height: 44px;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-brand px-4" style="height: 44px;">
                            <i class="las la-save me-1"></i> Save Campaign Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection
