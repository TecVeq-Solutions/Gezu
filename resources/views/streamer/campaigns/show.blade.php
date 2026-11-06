@extends('streamer.layouts.master')

@section('content')
<div class="row g-4">
    <!-- Left Column: Campaign Briefing & Guidelines -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <span class="badge bg-light text-dark border">{{ $campaign->category ?? 'Brand Deal' }}</span>
                    <span class="badge bg-danger-subtle text-danger fw-bold border border-danger-subtle">
                        <i class="fab fa-youtube me-1"></i> YouTube Sponsorship Campaign
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-2">{{ $campaign->title }}</h3>

                <!-- Brand Details (Strict Zero-Contact Policy: Only Brand Name) -->
                <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-danger-subtle border border-danger-subtle d-flex align-items-center justify-content-center fw-bold text-danger" style="width: 38px; height: 38px; font-size: 14px;">
                        {{ strtoupper(substr($campaign->brand->company_name ?? 'B', 0, 1)) }}
                    </div>
                    <div>
                        <span class="d-block fw-bold text-dark fs-6">{{ $campaign->brand->company_name ?? 'Verified Brand' }}</span>
                        <small class="text-muted"><i class="las la-check-circle text-success"></i> Verified Sponsor &bull; Guaranteed Budget</small>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="las la-file-alt text-danger me-1"></i> Campaign Overview</h6>
                    <div class="text-secondary leading-relaxed" style="white-space: pre-line; word-break: break-word;">
                        {{ $campaign->description ?? 'No specific description provided by brand.' }}
                    </div>
                </div>

                <!-- Brand Asset & Creative Assets Reference -->
                @if($campaign->primary_asset_url)
                    <div class="p-3.5 rounded-3 bg-light border mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="las la-cloud-download-alt text-danger me-1"></i> Official Creative Assets &amp; Footage Link</h6>
                                <p class="small text-muted mb-1">Official materials, talking points, and raw footage provided by the brand:</p>
                                <small class="text-muted font-monospace text-truncate d-block" style="max-width: 450px;">{{ $campaign->primary_asset_url }}</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ $campaign->primary_asset_url }}'); this.innerText='Copied!'; setTimeout(()=>this.innerText='Copy Link', 2000)" style="height: 36px;">
                                    <i class="las la-copy"></i> Copy Link
                                </button>
                                <a href="{{ $campaign->primary_asset_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-danger d-flex align-items-center gap-1" style="height: 36px;">
                                    <i class="las la-external-link-alt"></i> Open Creative Assets
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Streamer Guidelines & Rules -->
                <div class="p-4 rounded-3 border" style="background: #fafaf9;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0"><i class="fab fa-youtube text-danger me-2"></i> YouTube Promotion Requirements &amp; Rules</h6>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Brand Guidelines</span>
                    </div>
                    <ul class="list-unstyled mb-0 space-y-2">
                        @foreach($campaign->clean_rules as $rule)
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <i class="las la-check-circle text-success fs-5 mt-0.5 flex-shrink-0"></i>
                                <span class="small text-dark fw-medium">{{ $rule }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Streamer Submissions for this Campaign -->
        @if($userSubmissions->isNotEmpty())
            <div class="card">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fab fa-youtube text-danger me-2"></i> Your Submissions to This Campaign</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Video URL</th>
                                    <th>Observed Views</th>
                                    <th>Bounty</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userSubmissions as $sub)
                                    <tr>
                                        <td>
                                            <a href="{{ $sub->post_url }}" target="_blank" class="small font-monospace text-danger text-decoration-none">
                                                {{ Str::limit($sub->post_url, 35) }} <i class="las la-external-link-alt"></i>
                                            </a>
                                        </td>
                                        <td><span class="font-monospace fw-bold">{{ number_format($sub->observed_views) }}</span></td>
                                        <td><span class="font-monospace fw-bold text-success">${{ number_format($sub->verified_reward > 0 ? $sub->verified_reward : $sub->estimated_reward, 2) }}</span></td>
                                        <td>
                                            @if(in_array($sub->status, ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid']))
                                                <span class="badge-status badge-approved">VERIFIED</span>
                                            @elseif($sub->status === 'rejected')
                                                <span class="badge-status badge-rejected">REJECTED</span>
                                            @else
                                                <span class="badge-status badge-pending">UNDER REVIEW</span>
                                            @endif
                                        </td>
                                        <td><span class="small text-muted">{{ $sub->created_at->format('M d, Y') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Bounty Financials & One-Click Submit -->
    <div class="col-lg-4">
        <!-- Bounty Summary Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="las la-coins text-warning me-2"></i> Bounty Reward Terms</h6>
            </div>
            <div class="card-body p-4">
                @if($campaign->isFixedPerVideo())
                    <div class="text-center py-3 mb-3 rounded bg-light border">
                        <span class="text-muted d-block small mb-1" style="font-size: 11px;">FIXED PAY PER VIDEO</span>
                        <h2 class="mb-0 fw-bold font-monospace text-warning">${{ number_format($campaign->reward_per_video, 2) }}</h2>
                        <span class="text-muted small">Expected: {{ $campaign->formatted_expected_views }}</span>
                    </div>
                @else
                    <div class="text-center py-3 mb-3 rounded bg-light border">
                        <span class="text-muted d-block small mb-1" style="font-size: 11px;">VERIFIED CPM REWARD</span>
                        <h2 class="mb-0 fw-bold font-monospace text-success">${{ number_format($campaign->reward_rate_per_thousand, 2) }}</h2>
                        <span class="text-muted small">USD per 1,000 views</span>
                    </div>
                @endif

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted small">Total Campaign Budget</span>
                        <span class="font-monospace fw-bold">${{ number_format($campaign->total_budget, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted small">Available Bounty Pool</span>
                        <span class="font-monospace fw-bold text-danger">${{ number_format($campaign->remaining_budget, 2) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted small">Target Reach</span>
                        <span class="font-monospace fw-semibold">{{ number_format($campaign->target_views ?? 50000) }} Views</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted small">Target Geo</span>
                        <span class="fw-semibold text-dark">{{ $campaign->target_countries_summary ?? '🌍 Worldwide' }}</span>
                    </li>
                </ul>

                <div class="d-grid gap-2">
                    <a href="{{ route('streamer.submissions.create', ['campaign_id' => $campaign->id]) }}" class="btn btn-streamer text-white fw-bold py-2.5">
                        <i class="las la-upload me-1"></i> Submit YouTube Promo Link
                    </a>
                    <a href="{{ route('streamer.campaigns') }}" class="btn btn-outline-secondary btn-sm" style="height: 38px !important;">
                        &larr; Back to Marketplace
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
