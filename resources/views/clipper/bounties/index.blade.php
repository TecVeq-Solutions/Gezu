@extends('clipper.layouts.app')

@section('panel')
<div class="row mb-4">
    <!-- Filter and Search Header (Disciplined 38px Toolbar) -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('clipper.bounties') }}" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white" style="height: 38px;"><i class="las la-search text-muted"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search bounties by title, brand, or keywords..." style="height: 38px;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category" class="form-select" style="height: 38px;">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-select" style="height: 38px;">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest First</option>
                            <option value="cpm_desc" {{ request('sort') === 'cpm_desc' ? 'selected' : '' }}>Highest CPM ($/1k)</option>
                            <option value="budget_desc" {{ request('sort') === 'budget_desc' ? 'selected' : '' }}>Highest Remaining</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-creator w-100 d-inline-flex align-items-center justify-content-center gap-1" style="height: 38px;">
                            <i class="las la-filter"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'sort']))
                            <a href="{{ route('clipper.bounties') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" style="height: 38px; width: 38px; padding: 0;" title="Reset Filters">
                                <i class="las la-redo"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bounties Catalog Grid -->
<div class="row g-4">
    @forelse($bounties as $bounty)
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100 d-flex flex-column justify-content-between">
                <div>
                    <!-- Card Header with Brand & CPM -->
                    <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; font-size: 14px;">
                                {{ strtoupper(substr($bounty->brand?->company_name ?? 'B', 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold text-dark d-block text-truncate" style="max-width: 150px; font-size: 13.5px;">{{ $bounty->brand?->company_name ?? 'Brand Advertiser' }}</span>
                                <span class="badge bg-light text-muted border" style="font-size: 10.5px;">{{ $bounty->category ?? 'General' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($bounty->isFixedPerVideo())
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace py-1 px-2" style="font-size: 13px;">
                                    ${{ number_format($bounty->reward_per_video, 2) }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 10.5px;">Fixed / {{ $bounty->formatted_expected_views }}</small>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace py-1 px-2" style="font-size: 13px;">
                                    ${{ number_format($bounty->reward_rate_per_thousand, 2) }}
                                </span>
                                <small class="text-muted d-block" style="font-size: 10.5px;">per 1,000 views</small>
                            @endif
                        </div>
                    </div>

                    <!-- Bounty Body -->
                    <div class="p-3">
                        <h5 class="fw-bold text-dark mb-2 fs-6">
                            <a href="{{ route('clipper.bounties.show', $bounty->id) }}" class="text-dark text-decoration-none">
                                {{ $bounty->title }}
                            </a>
                        </h5>
                        <p class="text-muted small mb-3" style="min-height: 42px; line-height: 1.5;">
                            {{ Str::limit($bounty->description, 105) }}
                        </p>

                        <!-- Budget Progress -->
                        @php
                            $spentPercent = $bounty->total_budget > 0 
                                ? min(100, max(0, round((($bounty->total_budget - $bounty->remaining_budget) / $bounty->total_budget) * 100))) 
                                : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Remaining Budget:</span>
                                <strong class="text-dark font-monospace">${{ number_format($bounty->remaining_budget, 2) }} / ${{ number_format($bounty->total_budget, 2) }}</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" style="width: {{ 100 - $spentPercent }}%;"></div>
                            </div>
                        </div>

                        <!-- Platform & Geo Badges -->
                        <div class="d-flex align-items-center gap-1 flex-wrap mb-2">
                            <span class="small text-muted me-1">Platforms:</span>
                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1"><i class="fab fa-tiktok"></i> TikTok</span>
                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1"><i class="fab fa-youtube text-danger"></i> YouTube</span>
                            <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1"><i class="fab fa-instagram text-danger"></i> IG Reels</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 font-monospace" style="font-size: 11px;">
                                <i class="las la-globe-americas"></i> {{ $bounty->target_countries_summary }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Actions (Equal 34px Button Height) -->
                <div class="p-3 bg-light border-top d-flex gap-2 justify-content-between align-items-center">
                    <a href="{{ route('clipper.bounties.show', $bounty->id) }}" class="btn btn-outline-secondary btn-sm flex-grow-1" style="height: 34px;">
                        <i class="las la-info-circle me-1"></i> Briefing &amp; Assets
                    </a>
                    <a href="{{ route('clipper.submissions.create', ['campaign_id' => $bounty->id]) }}" class="btn btn-creator btn-sm flex-grow-1" style="height: 34px;">
                        <i class="las la-paper-plane me-1"></i> Submit Clip
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 text-center py-5">
                <i class="las la-search fs-1 text-muted mb-2 opacity-50 d-block"></i>
                <h5 class="text-dark fw-bold">No bounties found</h5>
                <p class="text-muted">No campaigns matched your search or category filter. Try clearing the filters.</p>
                <div class="mt-2">
                    <a href="{{ route('clipper.bounties') }}" class="btn btn-outline-secondary btn-sm" style="height: 34px;">Clear All Filters</a>
                </div>
            </div>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($bounties->hasPages())
        <div class="col-12 mt-4 d-flex justify-content-center">
            {{ $bounties->links() }}
        </div>
    @endif
</div>
@endsection
