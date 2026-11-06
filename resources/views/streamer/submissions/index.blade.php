@extends('streamer.layouts.master')

@section('content')
<!-- Filter Header -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('streamer.submissions') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('streamer.submissions') }}" class="btn {{ !request('status') || request('status') === 'all' ? 'btn-danger text-white' : 'btn-secondary-rgba' }}">All Submissions</a>
                <a href="{{ route('streamer.submissions', ['status' => 'pending']) }}" class="btn {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-warning-rgba' }}">Under Review</a>
                <a href="{{ route('streamer.submissions', ['status' => 'approved']) }}" class="btn {{ request('status') === 'approved' ? 'btn-success text-white' : 'btn-success-rgba' }}">Approved / Verified</a>
                <a href="{{ route('streamer.submissions', ['status' => 'rejected']) }}" class="btn {{ request('status') === 'rejected' ? 'btn-danger text-white' : 'btn-danger-rgba' }}">Rejected</a>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search link or campaign..." style="min-width: 220px;">
                    <button type="submit" class="btn btn-secondary-rgba px-3"><i class="las la-search"></i></button>
                </div>
                <a href="{{ route('streamer.submissions.create') }}" class="btn btn-streamer text-white text-nowrap">
                    <i class="las la-plus me-1"></i> New Submission
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Submissions Table -->
<div class="card shadow-sm border">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Video</th>
                        <th>Campaign</th>
                        <th>Views Reach</th>
                        <th>Bounty Reward</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th class="text-end">Watch</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $sub)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($sub->external_post_id)
                                        <img src="https://img.youtube.com/vi/{{ $sub->external_post_id }}/default.jpg" alt="Thumb" class="rounded border" style="width: 60px; height: 45px; object-fit: cover;">
                                    @else
                                        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-danger" style="width: 60px; height: 45px;">
                                            <i class="fab fa-youtube fs-4"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ $sub->post_url }}" target="_blank" class="font-monospace small text-danger text-decoration-none d-block fw-bold" style="max-width: 220px;">
                                            {{ Str::limit($sub->post_url, 38) }}
                                        </a>
                                        @if($sub->external_post_id)
                                            <small class="text-muted font-monospace">ID: {{ $sub->external_post_id }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('streamer.campaigns.show', $sub->campaign_id) }}" class="fw-semibold text-dark text-decoration-none d-block">
                                    {{ $sub->campaign->title ?? 'Campaign' }}
                                </a>
                                <small class="text-muted">{{ $sub->campaign->brand->company_name ?? 'Advertiser' }}</small>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold">{{ number_format($sub->observed_views) }}</span>
                                <small class="d-block text-muted">Observed Views</small>
                            </td>
                            <td>
                                @if($sub->verified_reward > 0)
                                    <span class="font-monospace fw-bold text-success fs-6">${{ number_format($sub->verified_reward, 2) }}</span>
                                    <small class="d-block text-success">Verified Payout</small>
                                @else
                                    <span class="font-monospace fw-bold text-muted fs-6">${{ number_format($sub->estimated_reward, 2) }}</span>
                                    <small class="d-block text-muted">Estimated</small>
                                @endif
                            </td>
                            <td>
                                @if(in_array($sub->status, ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid']))
                                    <span class="badge-status badge-approved"><i class="las la-check-circle"></i> VERIFIED</span>
                                @elseif($sub->status === 'rejected')
                                    <span class="badge-status badge-rejected" title="{{ $sub->rejection_reason }}"><i class="las la-times-circle"></i> REJECTED</span>
                                    @if($sub->rejection_reason)
                                        <small class="d-block text-danger mt-1" style="font-size: 11px;">{{ Str::limit($sub->rejection_reason, 25) }}</small>
                                    @endif
                                @else
                                    <span class="badge-status badge-pending"><i class="las la-clock"></i> UNDER REVIEW</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">{{ $sub->created_at->format('M d, Y') }}</span>
                                <small class="d-block text-muted font-monospace">{{ $sub->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ $sub->post_url }}" target="_blank" class="btn btn-sm btn-danger-rgba" title="Watch on YouTube">
                                    <i class="fab fa-youtube text-danger me-1"></i> Watch
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fab fa-youtube fs-1 text-danger d-block mb-2"></i>
                                <h6 class="fw-bold text-dark">No video submissions found</h6>
                                <p class="small text-muted mb-3">You haven't submitted any YouTube videos matching this query.</p>
                                <a href="{{ route('streamer.submissions.create') }}" class="btn btn-streamer btn-sm text-white">
                                    <i class="las la-upload me-1"></i> Submit YouTube Promo Link
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center mt-4">
    {{ $submissions->links() }}
</div>
@endsection
