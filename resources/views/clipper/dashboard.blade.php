@extends('clipper.layouts.app')

@section('panel')
<!-- Missing Bank Alert (if applicable) -->
@if(!$hasBankSetup)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 rounded-3" style="background: #fffbeb; border-left: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="las la-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">Direct Bank Payout Account Missing</h6>
                        <small class="text-muted">You haven't configured your direct bank details yet. Add your bank name and account number so the Admin can wire your approved clip earnings directly to you!</small>
                    </div>
                </div>
                <a href="{{ route('clipper.payout-settings') }}" class="btn btn-warning btn-sm fw-bold px-3 d-inline-flex align-items-center gap-1" style="height: 38px;">
                    <i class="las la-university"></i> Set Up Bank Details Now
                </a>
            </div>
        </div>
    </div>
@endif

<!-- 6 Primary Creator KPI Cards (3 Cards Per Row on Desktop) -->
<div class="row g-3 g-xl-4 mb-4">

    <!-- Total Lifetime Earned -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Lifetime Earned</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 26px;">${{ number_format($widget['total_earnings'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Available Wallet Balance -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Available Wallet Balance</p>
                <h3 class="fw-bold mb-0 text-success font-monospace" style="font-size: 26px;">${{ number_format($widget['available_balance'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="las la-piggy-bank"></i>
            </div>
        </div>
    </div>

    <!-- Pending Bank Transfers -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Pending Bank Transfers</p>
                <h3 class="fw-bold mb-0 text-warning font-monospace" style="font-size: 26px;">${{ number_format($widget['pending_payouts'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="las la-university"></i>
            </div>
        </div>
    </div>

    <!-- Verified Views Delivered -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Verified Views Delivered</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 26px;">{{ number_format($widget['total_views']) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                <i class="las la-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Approved & Paid Clips -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Approved & Paid Clips</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 26px;">{{ $widget['approved_clips'] }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                <i class="las la-film"></i>
            </div>
        </div>
    </div>

    <!-- Available Bounties -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Available Bounties</p>
                <h3 class="fw-bold mb-0 text-primary font-monospace" style="font-size: 26px;">{{ $widget['active_bounties'] }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-bullhorn"></i>
            </div>
        </div>
    </div>

</div>

<!-- Performance & Hot Bounties Row -->
<div class="row g-4 mb-4">
    <!-- Views Analytics Chart -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-chart-area text-success fs-5"></i> Clip Performance & Views Velocity
                </h6>
                <span class="badge bg-light text-muted border">Performance Telemetry</span>
            </div>
            <div class="card-body p-4">
                <div id="creatorViewsChart" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <!-- Top Open Bounties -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-fire text-danger fs-5"></i> Hot Bounties to Clip
                </h6>
                <a href="{{ route('clipper.bounties') }}" class="btn btn-sm btn-outline-secondary" style="height: 30px; font-size: 11.5px; padding: 0 10px;">View All</a>
            </div>
            <div class="card-body p-3">
                @forelse($topBounties as $bounty)
                    <div class="p-3 mb-3 rounded-3 border bg-light">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-1 fs-6">
                                    <a href="{{ route('clipper.bounties.show', $bounty->id) }}" class="text-dark text-decoration-none">
                                        {{ Str::limit($bounty->title, 26) }}
                                    </a>
                                </h6>
                                <small class="text-muted"><i class="las la-building"></i> {{ $bounty->brand?->company_name ?? 'Brand Advertiser' }}</small>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">${{ number_format($bounty->reward_rate_per_thousand, 2) }}/1K</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <span class="text-muted small">Budget: <strong class="text-dark font-monospace">${{ number_format($bounty->remaining_budget, 2) }}</strong></span>
                            <a href="{{ route('clipper.submissions.create', ['campaign_id' => $bounty->id]) }}" class="btn btn-sm btn-creator" style="height: 32px; padding: 0 12px; font-size: 12px;">
                                <i class="las la-paper-plane me-1"></i> Submit Clip
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="las la-inbox fs-1 mb-2 text-muted opacity-50"></i>
                        <p class="mb-0 small">No active bounties available right now.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Clip Submissions Table -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-video text-primary fs-5"></i> Recent Clip Submissions
                </h6>
                <a href="{{ route('clipper.submissions') }}" class="btn btn-sm btn-outline-secondary" style="height: 32px;">View All Submissions</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Platform</th>
                                <th>Clip Link</th>
                                <th>Observed Views</th>
                                <th>Reward</th>
                                <th>Status</th>
                                <th class="text-end">Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSubmissions as $submission)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $submission->campaign?->title ?? 'Bounty' }}">
                                            {{ $submission->campaign?->title ?? 'Bounty' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 11.5px;">{{ $submission->campaign?->brand?->company_name ?? 'Advertiser' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                            @if(str_contains(strtolower($submission->platform), 'tiktok'))
                                                <i class="fab fa-tiktok text-dark"></i> TikTok
                                            @elseif(str_contains(strtolower($submission->platform), 'youtube'))
                                                <i class="fab fa-youtube text-danger"></i> YouTube Shorts
                                            @elseif(str_contains(strtolower($submission->platform), 'instagram'))
                                                <i class="fab fa-instagram text-danger"></i> Instagram Reels
                                            @elseif(str_contains(strtolower($submission->platform), 'x') || str_contains(strtolower($submission->platform), 'twitter'))
                                                <i class="fab fa-x-twitter text-dark"></i> X
                                            @else
                                                <i class="las la-video text-primary"></i> {{ $submission->platform }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($submission->post_url)
                                            <a href="{{ $submission->post_url }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" style="height: 32px;">
                                                <i class="las la-external-link-alt"></i> Watch Clip
                                            </a>
                                        @else
                                            <span class="text-muted small">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-dark">{{ number_format($submission->observed_views) }}</span>
                                    </td>
                                    <td>
                                        @if($submission->verified_reward > 0)
                                            <span class="fw-bold font-monospace text-success">${{ number_format($submission->verified_reward, 2) }}</span>
                                        @else
                                            <span class="text-muted font-monospace">${{ number_format($submission->estimated_reward, 2) }} <small>(est.)</small></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(in_array($submission->status, ['approved_by_brand', 'approved_pending_payout', 'verified']))
                                            <span class="badge-status badge-approved">
                                                <i class="las la-check"></i> Approved
                                            </span>
                                        @elseif($submission->status === 'paid')
                                            <span class="badge-status badge-paid">
                                                <i class="las la-university"></i> Paid to Bank
                                            </span>
                                        @elseif($submission->status === 'rejected')
                                            <span class="badge-status badge-rejected" title="{{ $submission->rejection_reason }}">
                                                <i class="las la-times"></i> Rejected
                                            </span>
                                        @else
                                            <span class="badge-status badge-pending">
                                                <i class="las la-clock"></i> Under Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $submission->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="las la-video-slash fs-1 mb-2 text-muted opacity-50 d-block"></i>
                                        <p class="mb-2">You haven't submitted any clips yet.</p>
                                        <a href="{{ route('clipper.bounties') }}" class="btn btn-sm btn-creator" style="height: 34px;">
                                            <i class="las la-fire me-1"></i> Browse Open Bounties
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var options = {
            series: [{
                name: 'Observed Views',
                data: [1500, 3200, 6800, 12400, 28000, 45000, {{ $widget['total_views'] > 0 ? $widget['total_views'] : 62000 }}]
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Current'],
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return (val / 1000).toFixed(0) + 'k';
                    }
                }
            },
            tooltip: {
                theme: 'dark'
            }
        };

        var chart = new ApexCharts(document.querySelector("#creatorViewsChart"), options);
        chart.render();
    });
</script>
@endpush
