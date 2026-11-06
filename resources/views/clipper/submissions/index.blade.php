@extends('clipper.layouts.app')

@section('panel')
<div class="row mb-4">
    <!-- Header Toolbar (Standardized 38px Height) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('clipper.submissions') }}" class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <!-- Status Filter Pills -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ route('clipper.submissions') }}" class="btn btn-sm {{ !request('status') || request('status') === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}" style="height: 38px;">
                            All Submissions
                        </a>
                        <a href="{{ route('clipper.submissions', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-white' : 'btn-outline-warning' }}" style="height: 38px;">
                            <i class="las la-clock me-1"></i> Under Review
                        </a>
                        <a href="{{ route('clipper.submissions', ['status' => 'approved']) }}" class="btn btn-sm {{ request('status') === 'approved' ? 'btn-success text-white' : 'btn-outline-success' }}" style="height: 38px;">
                            <i class="las la-check-circle me-1"></i> Approved (Bank Queued)
                        </a>
                        <a href="{{ route('clipper.submissions', ['status' => 'paid']) }}" class="btn btn-sm {{ request('status') === 'paid' ? 'btn-info text-white' : 'btn-outline-info' }}" style="height: 38px;">
                            <i class="las la-university me-1"></i> Paid to Bank
                        </a>
                        <a href="{{ route('clipper.submissions', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger text-white' : 'btn-outline-danger' }}" style="height: 38px;">
                            <i class="las la-times-circle me-1"></i> Rejected
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        <div class="input-group input-group-sm" style="max-width: 260px;">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search campaign or URL..." style="height: 38px;">
                            <button class="btn btn-primary" type="submit" style="height: 38px;"><i class="las la-search"></i></button>
                        </div>
                        <a href="{{ route('clipper.submissions.create') }}" class="btn btn-creator btn-sm d-flex align-items-center gap-1 text-nowrap" style="height: 38px; padding: 0 16px;">
                            <i class="las la-plus-circle"></i> Submit Clip
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Submissions Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-film text-success fs-5"></i> Clip Submissions & Verifications
                </h6>
                <span class="badge bg-light text-muted border">Creator Telemetry</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Campaign / Bounty</th>
                                <th>Platform</th>
                                <th>Published Clip</th>
                                <th>Observed Views</th>
                                <th>Verified Reward</th>
                                <th>Status</th>
                                <th class="text-end">Submitted Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $clip)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $clip->campaign?->title ?? 'Campaign Bounty' }}">
                                            {{ $clip->campaign?->title ?? 'Campaign Bounty' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 11.5px;"><i class="las la-building"></i> {{ $clip->campaign?->brand?->company_name ?? 'Advertiser' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                            @if(str_contains(strtolower($clip->platform), 'tiktok'))
                                                <i class="fab fa-tiktok text-dark"></i> TikTok
                                            @elseif(str_contains(strtolower($clip->platform), 'youtube'))
                                                <i class="fab fa-youtube text-danger"></i> YouTube Shorts
                                            @elseif(str_contains(strtolower($clip->platform), 'instagram'))
                                                <i class="fab fa-instagram text-danger"></i> Instagram Reels
                                            @else
                                                <i class="las la-video text-primary"></i> {{ $clip->platform }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($clip->post_url)
                                            <a href="{{ $clip->post_url }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="height: 32px;">
                                                <i class="las la-external-link-alt"></i> Watch Clip
                                            </a>
                                        @else
                                            <span class="text-muted small">No URL</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-dark">{{ number_format($clip->observed_views) }}</span>
                                    </td>
                                    <td>
                                        @if($clip->verified_reward > 0)
                                            <span class="fw-bold font-monospace text-success">${{ number_format($clip->verified_reward, 2) }}</span>
                                        @else
                                            <span class="text-muted font-monospace">${{ number_format($clip->estimated_reward, 2) }} <small>(est.)</small></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($clip->status, ['approved_by_brand', 'approved_pending_payout', 'verified']))
                                            <span class="badge-status badge-approved" title="Approved by brand. Queued for Admin direct bank transfer.">
                                                <i class="las la-check"></i> Approved
                                            </span>
                                        @elseif($clip->status === 'paid')
                                            <span class="badge-status badge-paid" title="Funds transferred directly to your bank account.">
                                                <i class="las la-university"></i> Paid to Bank
                                            </span>
                                        @elseif($clip->status === 'rejected')
                                            <div>
                                                <span class="badge-status badge-rejected" title="{{ $clip->rejection_reason ?? 'Does not meet guidelines' }}">
                                                    <i class="las la-times"></i> Rejected
                                                </span>
                                                @if($clip->rejection_reason)
                                                    <small class="d-block text-danger mt-1" style="font-size: 11px;">
                                                        {{ Str::limit($clip->rejection_reason, 40) }}
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge-status badge-pending">
                                                <i class="las la-clock"></i> Under Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $clip->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="las la-video-slash fs-1 mb-2 text-muted opacity-50 d-block"></i>
                                        <p class="mb-2">No submissions found matching your filters.</p>
                                        <a href="{{ route('clipper.bounties') }}" class="btn btn-sm btn-creator" style="height: 34px;">
                                            <i class="las la-fire me-1"></i> Browse Open Bounties
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
