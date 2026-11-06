@extends('brand.layouts.app')

@section('panel')
<!-- Summary Metrics Row -->
<div class="row g-3 g-xl-4 mb-4">
    <div class="col-lg-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Verified Views</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">{{ number_format($totalViews) }}</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-eye"></i> Real-time Telemetry
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-chart-bar"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Approved Clips</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">{{ number_format($approvedSubmissions) }}</h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-check-circle"></i> Payout Qualified
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-video"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Submissions</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">{{ number_format($totalSubmissions) }}</h3>
                <span class="badge bg-light text-muted border d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-file-upload text-info"></i> All Inbound
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="las la-layer-group"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Approval Rate</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">
                    {{ $totalSubmissions > 0 ? round(($approvedSubmissions / $totalSubmissions) * 100) : 100 }}%
                </h3>
                <span class="badge bg-info-subtle text-info border border-info-subtle d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-percentage"></i> Acceptance Ratio
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                <i class="las la-award"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Performing Creators -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-trophy text-warning fs-5"></i> Top Performing Creators
                </h6>
                <span class="badge bg-light text-muted border">By Verified Views</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Creator</th>
                                <th>Contact Email</th>
                                <th class="text-end">Delivered Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCreators as $creator)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 13px;">
                                                {{ strtoupper(substr($creator->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark">{{ $creator->name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted small">{{ $creator->email }}</span></td>
                                    <td class="text-end">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-1" style="font-size: 12px;">
                                            {{ number_format($creator->brand_views) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="las la-users-slash fs-1 text-muted opacity-50 d-block mb-2"></i>
                                        No creator telemetry recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Breakdown -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-bullhorn text-primary fs-5"></i> Campaign Delivery Breakdown
                </h6>
                <span class="badge bg-light text-muted border">Fulfillment Status</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Target Views</th>
                                <th>Delivered</th>
                                <th class="text-end">Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $camp)
                                @php
                                    $d = (int) $camp->submissions->sum('observed_views');
                                    $t = (int) ($camp->target_views ?: 10000);
                                    $pct = $t > 0 ? min(100, round(($d / $t) * 100)) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">{{ $camp->title }}</div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $camp->category }}</small>
                                    </td>
                                    <td class="font-monospace text-muted small">{{ number_format($t) }}</td>
                                    <td class="font-monospace fw-bold text-dark small">{{ number_format($d) }}</td>
                                    <td class="text-end">
                                        <div class="d-inline-flex align-items-center gap-2">
                                            <div class="progress" style="width: 60px; height: 6px; background-color: #e2e8f0;">
                                                <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span class="badge {{ $pct >= 100 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }}" style="font-size: 11px; min-width: 44px;">
                                                {{ $pct }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="las la-bullhorn fs-1 text-muted opacity-50 d-block mb-2"></i>
                                        No campaign data available.
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
