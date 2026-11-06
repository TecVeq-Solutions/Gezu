@extends('clipper.layouts.app')

@section('panel')
<div class="row g-4 mb-4">
    <!-- Main Briefing Column -->
    <div class="col-lg-8">
        <!-- Campaign Summary Card -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('clipper.bounties') }}" class="btn btn-sm btn-outline-secondary" style="height: 32px;">
                        <i class="las la-arrow-left me-1"></i> Back
                    </a>
                    <span class="badge bg-success-subtle text-success border border-success-subtle text-uppercase fw-bold">{{ $campaign->status }}</span>
                    <span class="badge bg-light text-dark border">{{ $campaign->category ?? 'General' }}</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">Brand:</span>
                    <strong class="text-dark">{{ $campaign->brand?->company_name ?? 'Advertiser' }}</strong>
                </div>
            </div>
            <div class="card-body p-4">
                <h3 class="fw-bold text-dark mb-3">{{ $campaign->title }}</h3>

                <!-- Key Metrics Ribbon -->
                <div class="row g-3 p-3 bg-light rounded-3 mb-4 border">
                    <div class="col-sm-4 text-center border-end">
                        @if($campaign->isFixedPerVideo())
                            <span class="text-muted small text-uppercase fw-bold d-block" style="font-size: 11px;">Fixed Pay / Video</span>
                            <strong class="fs-4 text-warning font-monospace">${{ number_format($campaign->reward_per_video, 2) }}</strong>
                            <span class="text-muted small d-block" style="font-size: 11px;">Target: {{ $campaign->formatted_expected_views }}</span>
                        @else
                            <span class="text-muted small text-uppercase fw-bold d-block" style="font-size: 11px;">Reward Rate</span>
                            <strong class="fs-4 text-success font-monospace">${{ number_format($campaign->reward_rate_per_thousand, 2) }}</strong>
                            <span class="text-muted small d-block" style="font-size: 11px;">per 1,000 views</span>
                        @endif
                    </div>
                    <div class="col-sm-4 text-center border-end">
                        <span class="text-muted small text-uppercase fw-bold d-block" style="font-size: 11px;">Remaining Bounty</span>
                        <strong class="fs-4 text-dark font-monospace">${{ number_format($campaign->remaining_budget, 2) }}</strong>
                        <span class="text-muted small d-block" style="font-size: 11px;">of ${{ number_format($campaign->total_budget, 2) }}</span>
                    </div>
                    <div class="col-sm-4 text-center">
                        <span class="text-muted small text-uppercase fw-bold d-block" style="font-size: 11px;">Target Goal</span>
                        <strong class="fs-4 text-primary font-monospace">{{ number_format($campaign->target_views) }}</strong>
                        <span class="text-muted small d-block" style="font-size: 11px;">total views</span>
                    </div>
                </div>

                <!-- Audience Geo-Targeting & Regional CPM Rates -->
                <div class="card border rounded-3 mb-4 overflow-hidden shadow-none bg-light bg-opacity-50">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                            <i class="las la-globe-americas text-primary fs-5"></i> Audience Geo-Targeting &amp; Payout Rates
                        </span>
                        @if($campaign->country_targeting_mode === 'worldwide')
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">🌍 Worldwide</span>
                        @elseif($campaign->country_targeting_mode === 'single')
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">🎯 Single Country</span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info-subtle font-monospace">🗺️ Multi-Country Markets</span>
                        @endif
                    </div>
                    <div class="card-body p-3">
                        @if($campaign->country_targeting_mode === 'worldwide')
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-1">🌍</div>
                                <div>
                                    <strong class="text-dark d-block">Open to Creators &amp; Audiences Worldwide</strong>
                                    <small class="text-muted">Views from all countries are eligible and compensated at the global base CPM rate of <strong class="text-success font-monospace">${{ number_format($campaign->reward_rate_per_thousand, 2) }} / 1,000 verified views</strong>.</small>
                                </div>
                            </div>
                        @elseif($campaign->country_targeting_mode === 'single' && $campaign->target_countries_list->isNotEmpty())
                            @php $targetCountry = $campaign->target_countries_list->first(); @endphp
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 bg-white rounded border">
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $targetCountry->flag_url }}" width="28" height="21" class="rounded-1 border shadow-xs" alt="{{ $targetCountry->country_code }}">
                                    <div>
                                        <strong class="text-dark d-block fs-6">{{ $targetCountry->country_name }} ({{ $targetCountry->country_code }})</strong>
                                        <small class="text-muted">Exclusive Target Market</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-success-subtle text-success border fs-6 font-monospace py-1.5 px-3">
                                        ${{ number_format($targetCountry->cpm_rate, 2) }} / 1k views
                                    </span>
                                    <small class="text-muted d-block mt-0.5" style="font-size: 11px;">Target market CPM bounty</small>
                                </div>
                            </div>
                        @elseif($campaign->country_targeting_mode === 'multiple' && $campaign->target_countries_list->isNotEmpty())
                            <p class="text-muted small mb-2">This campaign rewards creators with custom CPM rates tailored per viewer/creator market:</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-hover align-middle mb-0 bg-white">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Target Country</th>
                                            <th class="text-center" style="width: 80px;">Code</th>
                                            <th class="text-end" style="width: 170px;">Reward Rate (CPM)</th>
                                            <th class="text-end" style="width: 140px;">Est. Reach</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($campaign->target_countries_list as $tc)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $tc->flag_url }}" width="24" height="18" class="rounded-1 border shadow-xs" alt="{{ $tc->country_code }}">
                                                        <span class="fw-semibold text-dark">{{ $tc->country_name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center font-monospace fw-bold text-muted">{{ $tc->country_code }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-success-subtle text-success border font-monospace fw-bold" style="font-size: 12.5px;">
                                                        ${{ number_format($tc->cpm_rate, 2) }} / 1k
                                                    </span>
                                                </td>
                                                <td class="text-end font-monospace small text-muted">
                                                    {{ number_format($tc->target_views) }} views
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="d-flex align-items-center gap-3">
                                <div class="fs-1">🌍</div>
                                <div>
                                    <strong class="text-dark d-block">Open to Creators &amp; Audiences Worldwide</strong>
                                    <small class="text-muted">Global base rate: <strong class="text-success font-monospace">${{ number_format($campaign->reward_rate_per_thousand, 2) }} / 1,000 views</strong>.</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Briefing & Instructions -->
                <h5 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="las la-file-alt text-success fs-4"></i> Campaign Brief &amp; Guidelines
                </h5>
                <div class="p-3 bg-white border rounded-3 mb-4 text-dark" style="white-space: pre-line; line-height: 1.6; font-size: 14px;">
                    {{ $campaign->description }}
                </div>

                <!-- Source Assets Section -->
                @if($campaign->primary_asset_url)
                <h5 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="las la-folder-open text-primary fs-4"></i> Official Creative Assets &amp; Raw Footage
                </h5>
                <div class="p-3 bg-light border rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong class="text-dark d-block">Official Brand Assets / Cloud Folder</strong>
                        <small class="text-muted font-monospace text-truncate d-block" style="max-width: 450px;">{{ $campaign->primary_asset_url }}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $campaign->primary_asset_url }}'); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy Link', 2000)" style="height: 36px;">
                            <i class="las la-copy"></i> Copy Link
                        </button>
                        <a href="{{ $campaign->primary_asset_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm d-flex align-items-center gap-1" style="height: 36px;">
                            <i class="las la-external-link-alt"></i> Open Creative Assets
                        </a>
                    </div>
                </div>
                @endif

                <!-- Brand Specific Clipping Rules -->
                <h5 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="las la-shield-alt text-warning fs-4"></i> Brand Clipping Rules &amp; Verification
                </h5>
                <ul class="list-group list-group-flush border rounded-3 mb-0">
                    @foreach($campaign->clean_rules as $rule)
                        <li class="list-group-item d-flex align-items-start gap-2 py-2.5">
                            <i class="las la-check-circle text-success fs-5 mt-0.5 flex-shrink-0"></i>
                            <span class="text-dark fw-medium">{{ $rule }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- My Submissions for This Bounty -->
        @if($userSubmissions->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-history text-secondary fs-5"></i> My Submissions for this Bounty ({{ $userSubmissions->count() }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Platform</th>
                                    <th>Clip Link</th>
                                    <th>Views</th>
                                    <th>Reward</th>
                                    <th>Status</th>
                                    <th class="text-end">Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userSubmissions as $sub)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                                @if(str_contains(strtolower($sub->platform), 'tiktok'))
                                                    <i class="fab fa-tiktok"></i>
                                                @elseif(str_contains(strtolower($sub->platform), 'youtube'))
                                                    <i class="fab fa-youtube text-danger"></i>
                                                @elseif(str_contains(strtolower($sub->platform), 'instagram'))
                                                    <i class="fab fa-instagram text-danger"></i>
                                                @endif
                                                {{ $sub->platform }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ $sub->post_url }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="height: 32px;">
                                                <i class="las la-external-link-alt"></i> Watch
                                            </a>
                                        </td>
                                        <td><strong class="font-monospace text-dark">{{ number_format($sub->observed_views) }}</strong></td>
                                        <td><strong class="text-success font-monospace">${{ number_format($sub->verified_reward > 0 ? $sub->verified_reward : $sub->estimated_reward, 2) }}</strong></td>
                                        <td>
                                            @if(in_array($sub->status, ['approved_by_brand', 'approved_pending_payout', 'verified']))
                                                <span class="badge-status badge-approved">Approved</span>
                                            @elseif($sub->status === 'paid')
                                                <span class="badge-status badge-paid">Paid</span>
                                            @elseif($sub->status === 'rejected')
                                                <span class="badge-status badge-rejected">Rejected</span>
                                            @else
                                                <span class="badge-status badge-pending">Under Review</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted small">{{ $sub->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Submission Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden sticky-top" style="top: 20px;">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-paper-plane text-success fs-5"></i> Submit Clip for Bounty
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('clipper.submissions.store') }}">
                    @csrf
                    <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">

                    <div class="mb-3">
                        <label class="form-label">Target Platform <span class="text-danger">*</span></label>
                        <select name="platform" class="form-select" required>
                            <option value="">Select Platform</option>
                            <option value="TikTok">TikTok</option>
                            <option value="YouTube Shorts">YouTube Shorts</option>
                            <option value="Instagram Reels">Instagram Reels</option>
                            <option value="X / Twitter">X / Twitter</option>
                            <option value="Facebook Reels">Facebook Reels</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Video URL <span class="text-danger">*</span></label>
                        <input type="url" name="post_url" class="form-control font-monospace" placeholder="https://www.tiktok.com/@creator/video/..." required>
                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Direct public link to your published clip.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Creator Handle (Optional)</label>
                        <input type="text" name="channel_handle" class="form-control" placeholder="@yourhandle">
                    </div>

                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Audience Scope:</span>
                            <strong class="text-dark font-monospace">{{ $campaign->target_countries_summary }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Campaign CPM:</span>
                            <strong class="text-success font-monospace">${{ number_format($campaign->reward_rate_per_thousand, 2) }}/1K</strong>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Payout Method:</span>
                            <strong class="text-dark">Direct Bank Transfer</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-creator w-100" style="height: 44px;">
                        <i class="las la-cloud-upload-alt me-1"></i> Submit Video Clip
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
