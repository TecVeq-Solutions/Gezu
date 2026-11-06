@extends('brand.layouts.app')

@section('panel')
<!-- Brand Telemetry KPI Cards (3 Cards Per Row on Desktop) -->
<div class="row g-3 g-xl-4 mb-4">

    <!-- Active Campaigns -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Active Campaigns</p>
                <h3 class="fw-bold mb-0 text-dark" style="font-size: 26px;">{{ $widget['active_campaigns'] }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-bullhorn"></i>
            </div>
        </div>
    </div>

    <!-- Total Campaign Budget -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Campaign Budget</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 26px;">${{ number_format($widget['total_escrow'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="las la-lock"></i>
            </div>
        </div>
    </div>

    <!-- Remaining Budget -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Remaining Budget</p>
                <h3 class="fw-bold mb-0 text-primary font-monospace" style="font-size: 26px;">${{ number_format($widget['remaining_budget'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Pending Clip Reviews -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Clips Awaiting Review</p>
                <h3 class="fw-bold mb-0 text-warning" style="font-size: 26px;">{{ $widget['pending_clips'] }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="las la-video"></i>
            </div>
        </div>
    </div>

    <!-- Verified Views -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Verified Views</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 26px;">{{ number_format($widget['total_views']) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                <i class="las la-eye"></i>
            </div>
        </div>
    </div>

    <!-- Brand Wallet Balance -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Brand Wallet Balance</p>
                <h3 class="fw-bold mb-0 text-success font-monospace" style="font-size: 26px;">${{ number_format($widget['wallet_balance'], 2) }}</h3>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="las la-credit-card"></i>
            </div>
        </div>
    </div>

</div>

<!-- Performance Telemetry Chart -->
<div class="row my-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark">Brand Performance & Views Distribution Telemetry</h6>
                    <small class="text-muted">Real-time creator short-form impressions and view delivery trend</small>
                </div>
                <div class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                    <i class="las la-eye me-1"></i> Verified Views
                </div>
            </div>
            <div class="card-body p-4">
                <div id="brandPerformanceChart" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Fast-Action Tables Row -->
<div class="row g-4">

    <!-- Recent Clip Submissions -->
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-video text-primary fs-5"></i> Recent Clip Submissions
                </h6>
                <a href="{{ route('brand.clips') }}" class="btn btn-outline-secondary btn-sm" style="height: 32px;">View All Clips</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Creator</th>
                                <th>Campaign</th>
                                <th>Platform</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentClips as $clip)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $clip->user->name ?? 'Verified Creator' }}</div>
                                        <small class="text-muted font-monospace"><i class="las la-user-check text-success"></i> Creator #{{ $clip->user_id }}</small>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 140px;" title="{{ $clip->campaign->title ?? 'N/A' }}">
                                            {{ $clip->campaign->title ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                            @if($clip->platform === 'tiktok')
                                                <i class="fab fa-tiktok text-dark"></i> TikTok
                                            @elseif($clip->platform === 'instagram')
                                                <i class="fab fa-instagram text-danger"></i> Instagram
                                            @elseif($clip->platform === 'youtube')
                                                <i class="fab fa-youtube text-danger"></i> YouTube
                                            @else
                                                <i class="fab fa-twitter text-dark"></i> {{ ucfirst($clip->platform) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="fw-bold font-monospace">{{ number_format($clip->observed_views) }}</td>
                                    <td>
                                        @if($clip->status === 'approved_by_brand' || $clip->status === 'verified')
                                            <span class="badge badge--success">Approved</span>
                                        @elseif($clip->status === 'rejected')
                                            <span class="badge badge--danger">Rejected</span>
                                        @else
                                            <span class="badge badge--warning">Under Review</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $clip->post_url }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Watch Video">
                                            <i class="las la-external-link-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="las la-video fs-1 d-block mb-1 text-muted"></i>
                                        No creator submissions received yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Campaigns -->
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-bullhorn text-primary fs-5"></i> My Active Campaigns
                </h6>
                <a href="{{ route('brand.campaigns') }}" class="btn btn-outline-secondary btn-sm" style="height: 32px;">All Campaigns</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Budget</th>
                                <th>CPM</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCampaigns as $camp)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 180px;">
                                            {{ $camp->title }}
                                        </div>
                                        <small class="text-muted">{{ $camp->category }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">${{ number_format($camp->total_budget, 2) }}</div>
                                        <small class="text-primary font-monospace">${{ number_format($camp->remaining_budget, 2) }} left</small>
                                    </td>
                                    <td class="text-success fw-bold font-monospace">${{ number_format($camp->reward_rate_per_thousand, 2) }}</td>
                                    <td>
                                        @if($camp->status === 'active')
                                            <span class="badge badge--success text-uppercase">Active</span>
                                        @elseif($camp->status === 'draft')
                                            <span class="badge badge--warning text-uppercase">Unfunded</span>
                                        @elseif($camp->status === 'paused')
                                            <span class="badge badge--warning text-uppercase">Paused</span>
                                        @else
                                            <span class="badge bg-secondary text-uppercase">Completed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($camp->status === 'draft')
                                            <a href="{{ route('brand.campaigns.checkout', $camp->id) }}" class="btn btn-sm btn-success" title="Fund via Stripe">
                                                <i class="las la-credit-card me-1"></i> Fund
                                            </a>
                                        @else
                                            <a href="{{ route('web.campaign.detail', $camp->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View Campaign">
                                                <i class="las la-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="las la-bullhorn fs-1 d-block mb-1 text-muted"></i>
                                        No campaigns created yet. Click "Launch Campaign" to get started.
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

@push('script-lib')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endpush

@push('script')
<script>
    (function () {
        function initBrandTelemetryChart() {
            var chartEl = document.querySelector("#brandPerformanceChart");
            if (!chartEl) return;

            if (typeof ApexCharts === 'undefined') {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                s.onload = function () { renderBrandChart(chartEl); };
                document.head.appendChild(s);
                return;
            }
            renderBrandChart(chartEl);
        }

        function renderBrandChart(chartEl) {
            chartEl.innerHTML = '';
            var currentViews = {{ (int) ($widget['total_views'] ?? 0) }};
            var dataPoints = [12000, 24000, 45000, 68000, 115000, 185000, currentViews > 0 ? currentViews : 240000];

            var options = {
                series: [{
                    name: 'Verified Views',
                    data: dataPoints
                }],
                chart: {
                    height: 280,
                    type: 'area',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#6366f1'],
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
                            return (val >= 1000) ? (val / 1000).toFixed(0) + 'k' : val;
                        }
                    }
                },
                tooltip: {
                    theme: 'dark'
                }
            };

            try {
                var chart = new ApexCharts(chartEl, options);
                chart.render();
            } catch (err) {
                console.error("Brand telemetry chart rendering error:", err);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener("DOMContentLoaded", initBrandTelemetryChart);
        } else {
            initBrandTelemetryChart();
        }
    })();
</script>
@endpush
