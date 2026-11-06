@extends('streamer.layouts.master')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Total Earnings -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Total Earnings</span>
                <h3 class="mb-0 fw-bold font-monospace text-dark">${{ number_format($widget['total_earnings'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Stat 2: Available Balance -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Available Balance</span>
                <h3 class="mb-0 fw-bold font-monospace text-danger">${{ number_format($widget['available_balance'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="las la-coins"></i>
            </div>
        </div>
    </div>

    <!-- Stat 3: Total YouTube Views -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">YouTube Reach</span>
                <h3 class="mb-0 fw-bold font-monospace text-dark">{{ number_format($widget['total_views']) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="las la-eye"></i>
            </div>
        </div>
    </div>

    <!-- Stat 4: Active Sponsorship Deals -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Open Brand Deals</span>
                <h3 class="mb-0 fw-bold font-monospace text-primary">{{ $widget['active_bounties'] }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-bullhorn"></i>
            </div>
        </div>
    </div>
</div>

@if(!$hasBankSetup)
    <!-- Direct Bank Account Setup Alert -->
    <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border-left: 4px solid #ef4444 !important;">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 20px;">
                    <i class="las la-university"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold text-danger">Setup Direct Bank Transfer Details</h6>
                    <p class="mb-0 text-muted small">Configure your direct local/international bank account to receive video promotion bounty payouts smoothly.</p>
                </div>
            </div>
            <a href="{{ route('streamer.payout-settings') }}" class="btn btn-streamer btn-sm text-white px-3">
                <i class="las la-cog"></i> Add Bank Details
            </a>
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Chart: Monthly YouTube Views Reach -->
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="fab fa-youtube text-danger me-2"></i> YouTube Promotion Views Analytics</h6>
                <span class="badge bg-danger">Past 6 Months</span>
            </div>
            <div class="card-body p-3">
                <div id="youtubeViewsChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Pipeline Summary -->
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark"><i class="las la-tasks text-primary me-2"></i> Promotion Pipeline</h6>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success rounded-circle p-2"><i class="las la-check"></i></span>
                        <span class="fw-semibold text-secondary small">Approved &amp; Verified</span>
                    </div>
                    <span class="fw-bold font-monospace fs-6">{{ $widget['approved_clips'] }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning rounded-circle p-2"><i class="las la-hourglass-half"></i></span>
                        <span class="fw-semibold text-secondary small">Under Brand Review</span>
                    </div>
                    <span class="fw-bold font-monospace fs-6 text-warning">{{ $widget['pending_clips'] }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary rounded-circle p-2"><i class="las la-file-video"></i></span>
                        <span class="fw-semibold text-secondary small">Total Video Submissions</span>
                    </div>
                    <span class="fw-bold font-monospace fs-6 text-primary">{{ $widget['total_submissions'] }}</span>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('streamer.submissions.create') }}" class="btn btn-streamer text-white fw-bold">
                        <i class="las la-upload me-1"></i> Submit YouTube Promo Link
                    </a>
                    <a href="{{ route('streamer.campaigns') }}" class="btn btn-secondary-rgba">
                        <i class="las la-search me-1"></i> Find More Sponsorships
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Sponsorship Deals -->
<div class="card mb-4">
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="las la-star text-warning me-2"></i> Featured Brand Sponsorship Campaigns</h6>
        <a href="{{ route('streamer.campaigns') }}" class="small fw-bold text-danger text-decoration-none">View All Deals &rarr;</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Campaign / Brand</th>
                        <th>Category</th>
                        <th>Target Reach</th>
                        <th>Reward Rate (CPM)</th>
                        <th>Budget Remaining</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topBounties as $bounty)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-danger" style="width: 40px; height: 40px; font-size: 14px;">
                                        {{ substr($bounty->brand->company_name ?? 'GEZUX', 0, 2) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('streamer.campaigns.show', $bounty->id) }}" class="fw-bold text-dark text-decoration-none d-block">
                                            {{ $bounty->title }}
                                        </a>
                                        <small class="text-muted">{{ $bounty->brand->company_name ?? 'Verified Advertiser' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $bounty->category ?? 'Gaming & Tech' }}</span></td>
                            <td>
                                <span class="small font-monospace">{{ number_format($bounty->target_views ?? 50000) }} Views</span>
                                <small class="d-block text-muted">{{ $bounty->target_countries_summary ?? '🌍 Worldwide' }}</small>
                            </td>
                            <td>
                                <span class="fw-bold font-monospace text-success">${{ number_format($bounty->reward_rate_per_thousand, 2) }}</span>
                                <small class="text-muted">/1k views</small>
                            </td>
                            <td>
                                <span class="font-monospace fw-semibold">${{ number_format($bounty->remaining_budget, 2) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('streamer.submissions.create', ['campaign_id' => $bounty->id]) }}" class="btn btn-sm btn-streamer text-white">
                                    <i class="las la-video"></i> Promote
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No active sponsorship campaigns available at this moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Submissions -->
<div class="card">
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="fab fa-youtube text-danger me-2"></i> Recent YouTube Video Submissions</h6>
        <a href="{{ route('streamer.submissions') }}" class="small fw-bold text-danger text-decoration-none">View All Submissions &rarr;</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Video Link</th>
                        <th>Campaign</th>
                        <th>Views Observed</th>
                        <th>Est. Bounty</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th class="text-end">Watch</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSubmissions as $sub)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fab fa-youtube text-danger fs-5"></i>
                                    <span class="font-monospace small text-truncate" style="max-width: 200px;">
                                        {{ $sub->post_url }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $sub->campaign->title ?? 'Campaign' }}</span>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold">{{ number_format($sub->observed_views) }}</span>
                            </td>
                            <td>
                                <span class="font-monospace fw-bold text-success">${{ number_format($sub->verified_reward > 0 ? $sub->verified_reward : $sub->estimated_reward, 2) }}</span>
                            </td>
                            <td>
                                @if(in_array($sub->status, ['approved_by_brand', 'approved_pending_payout', 'verified', 'paid']))
                                    <span class="badge-status badge-approved"><i class="las la-check-circle"></i> VERIFIED</span>
                                @elseif($sub->status === 'rejected')
                                    <span class="badge-status badge-rejected"><i class="las la-times-circle"></i> REJECTED</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="las la-clock"></i> UNDER REVIEW</span>
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">{{ $sub->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ $sub->post_url }}" target="_blank" class="btn btn-sm btn-danger-rgba" title="Open in YouTube">
                                    <i class="fab fa-youtube text-danger me-1"></i> Watch
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                You haven't submitted any YouTube promotion links yet. Click <strong>"Submit YouTube Video"</strong> to start earning!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var labels = @json($chartLabels);
        var views = @json($chartViews);

        var options = {
            series: [{
                name: 'YouTube Views Reach',
                data: views
            }],
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                sparkline: { enabled: false }
            },
            colors: ['#ef4444'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            dataLabels: { enabled: false },
            xaxis: {
                categories: labels,
                labels: {
                    style: { colors: '#94a3b8', fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return (val >= 1000) ? (val / 1000).toFixed(1) + 'k' : val;
                    },
                    style: { colors: '#94a3b8', fontSize: '12px' }
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function (val) {
                        return val.toLocaleString() + ' views';
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#youtubeViewsChart"), options);
        chart.render();
    });
</script>
@endpush
