@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">


    <!-- KPI Metric Card 1: Registered Brands -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <a href="{{ route('admin.users', ['role' => 'brand']) }}" class="text-decoration-none">
            <div class="card b-radius--10 border shadow-xs h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Registered Brands</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace">{{ $widget['total_brands'] }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 52px; height: 52px; background: rgba(99, 102, 241, 0.1); font-size: 24px;">
                            <i class="las la-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- KPI Metric Card 2: Verified Clippers -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <a href="{{ route('admin.users', ['role' => 'clipper']) }}" class="text-decoration-none">
            <div class="card b-radius--10 border shadow-xs h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Verified Clippers</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace">{{ $widget['total_clippers'] }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 52px; height: 52px; background: rgba(14, 165, 233, 0.1); font-size: 24px;">
                            <i class="las la-scissors"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- KPI Metric Card 3: Active Streamers -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <a href="{{ route('admin.users', ['role' => 'streamer']) }}" class="text-decoration-none">
            <div class="card b-radius--10 border shadow-xs h-100 bg-white hover-shadow transition-all" style="border-left: 3px solid #e11d48 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Active Streamers</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace">{{ $widget['total_streamers'] ?? 0 }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 52px; height: 52px; background: rgba(225, 29, 72, 0.1); font-size: 24px;">
                            <i class="las la-video"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- KPI Metric Card 4: Active Campaigns -->
    <div class="col-xl-3 col-md-6 col-sm-6">
        <a href="{{ route('admin.campaigns') }}" class="text-decoration-none">
            <div class="card b-radius--10 border shadow-xs h-100 bg-white hover-shadow transition-all">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Active Campaigns</span>
                            <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace">{{ $widget['active_campaigns'] }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.1); font-size: 24px;">
                            <i class="las la-bullhorn"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- KPI Metric Card 4: Pending Submissions -->
    <div class="col-lg-4 col-sm-6">
        <div class="card b-radius--10 border shadow-xs h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Pending Approvals</span>
                        <h3 class="fw-bold text-dark mt-1 mb-0 font-monospace">{{ $widget['pending_submissions'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 52px; height: 52px; background: rgba(245, 158, 11, 0.1); font-size: 24px;">
                        <i class="las la-hourglass-half"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Metric Card 5: Paid Withdrawals -->
    <div class="col-lg-4 col-sm-6">
        <div class="card b-radius--10 border shadow-xs h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Paid Withdrawals</span>
                        <h3 class="fw-bold text-success mt-1 mb-0 font-monospace">${{ number_format($widget['total_paid_amount'], 2) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 52px; height: 52px; background: rgba(16, 185, 129, 0.1); font-size: 24px;">
                        <i class="las la-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Metric Card 6: Pending Withdrawals -->
    <div class="col-lg-4 col-sm-6">
        <div class="card b-radius--10 border shadow-xs h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Pending Payouts</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0 font-monospace">${{ number_format($widget['pending_payouts_amount'], 2) }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 52px; height: 52px; background: rgba(239, 68, 68, 0.1); font-size: 24px;">
                        <i class="las la-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Interactive Telemetry Chart Row -->
<div class="row gy-4 mt-2">
    <div class="col-xl-12">
        <div class="card border">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold">Marketplace Performance & Verified Views Telemetry</h6>
                <span class="badge badge--primary">Live Feed</span>
            </div>
            <div class="card-body">
                <div id="viewsChart" style="height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Submissions and Campaigns Row -->
<div class="row gy-4 mt-2">

    <!-- Recent Clip Submissions Table -->
    <div class="col-xl-7">
        <div class="card border">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold">Recent Clip Submissions</h6>
                <a href="{{ safeRoute('admin.clips', [], '/admin/clips') }}" class="btn btn-outline-primary btn-sm">View All Clips</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light table-hover mb-0">
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
                            @forelse($recentSubmissions as $sub)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $sub->user->name ?? 'Clipper' }}</span>
                                        <small class="text-muted">{{ $sub->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 140px;">
                                            {{ $sub->campaign->title ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">{{ strtoupper($sub->platform ?? 'TIKTOK') }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace">{{ number_format($sub->observed_views ?? 0) }}</span>
                                    </td>
                                    <td>
                                        @if(in_array($sub->status, ['approved_by_brand', 'verified', 'approved']))
                                            <span class="badge badge--success">Approved</span>
                                        @elseif(in_array($sub->status, ['pending', 'under_review']))
                                            <span class="badge badge--warning">Pending</span>
                                        @else
                                            <span class="badge badge--danger">{{ ucfirst($sub->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $sub->post_url ?? '#' }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Clip Post">
                                            <i class="las la-external-link-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No clip submissions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Active Campaigns Table -->
    <div class="col-xl-5">
        <div class="card border">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold">Active Campaigns</h6>
                <a href="{{ safeRoute('admin.campaigns', [], '/admin/campaigns') }}" class="btn btn-outline-primary btn-sm">All Campaigns</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Budget</th>
                                <th>CPM</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentCampaigns as $camp)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block text-truncate" style="max-width: 160px;">{{ $camp->title }}</span>
                                        <small class="text-muted">{{ $camp->brand->company_name ?? 'Advertiser' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace">${{ number_format($camp->total_budget, 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-success font-monospace fw-bold">${{ number_format($camp->reward_rate_per_thousand, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $camp->status === 'active' ? 'badge--success' : 'badge--warning' }}">
                                            {{ strtoupper($camp->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No campaigns created yet.</td>
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
    "use strict";
    function initViewsChart() {
        var el = document.querySelector("#viewsChart");
        if (!el) return;
        if (typeof ApexCharts === "undefined") {
            setTimeout(initViewsChart, 150);
            return;
        }

        var options = {
            series: [{
                name: 'Verified Views',
                data: [12000, 24000, 48000, 75000, 110000, 165000, 240000]
            }, {
                name: 'Campaign Budget Pool ($)',
                data: [500, 1200, 2500, 4200, 6800, 9500, 14200]
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false }
            },
            colors: ['#6366f1', '#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val.toLocaleString();
                    }
                }
            }
        };

        var chart = new ApexCharts(el, options);
        chart.render();
    }

    if (document.readyState === 'loading') {
        document.addEventListener("DOMContentLoaded", initViewsChart);
    } else {
        initViewsChart();
    }
</script>
@endpush
