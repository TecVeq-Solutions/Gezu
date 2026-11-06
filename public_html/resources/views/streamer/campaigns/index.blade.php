@extends('streamer.layouts.master')

@section('content')
<!-- Filter & Search Bar -->
<div class="card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('streamer.campaigns') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="las la-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search campaign title, brand, or keywords...">
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <select name="category" class="form-select">
                    <option value="all">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <select name="sort" class="form-select">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Newest</option>
                    <option value="cpm_desc" {{ request('sort') === 'cpm_desc' ? 'selected' : '' }}>Highest CPM</option>
                    <option value="budget_desc" {{ request('sort') === 'budget_desc' ? 'selected' : '' }}>Highest Budget</option>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-streamer text-white">
                    <i class="las la-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sponsorship Cards Grid -->
<div class="row g-4 mb-4">
    @forelse($campaigns as $campaign)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow-sm border hover-shadow transition">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <!-- Header: Brand Info & Category -->
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-light text-dark border">{{ $campaign->category ?? 'Brand Deal' }}</span>
                                @if($campaign->status === 'half_filled')
                                    <span class="badge bg-warning-subtle text-warning fw-bold border border-warning-subtle">
                                        <i class="las la-bolt"></i> Half Filled
                                    </span>
                                @elseif($campaign->status === 'completed')
                                    <span class="badge bg-dark-subtle text-dark fw-bold border border-dark-subtle">
                                        Completed
                                    </span>
                                @endif
                            </div>
                            <span class="badge bg-danger-subtle text-danger fw-bold border border-danger-subtle">
                                <i class="fab fa-youtube me-1"></i> YouTube Promo
                            </span>
                        </div>

                        <!-- Campaign Title -->
                        <h5 class="fw-bold text-dark mb-2">
                            <a href="{{ route('streamer.campaigns.show', $campaign->id) }}" class="text-dark text-decoration-none">
                                {{ $campaign->title }}
                            </a>
                        </h5>

                        <!-- Brand Info -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-danger" style="width: 28px; height: 28px; font-size: 11px;">
                                {{ substr($campaign->brand->company_name ?? 'GEZUX', 0, 2) }}
                            </div>
                            <span class="small text-muted fw-semibold">{{ $campaign->brand->company_name ?? 'Verified Advertiser' }}</span>
                        </div>

                        <!-- Brief Description -->
                        <p class="text-secondary small mb-3 text-truncate-2" style="min-height: 40px;">
                            {{ Str::limit($campaign->description, 110) }}
                        </p>

                        <!-- Key Metrics Grid -->
                        <div class="p-3 rounded bg-light border mb-3">
                            <div class="row g-2 text-center">
                                <div class="col-6 border-end">
                                    @if($campaign->isFixedPerVideo())
                                        <span class="text-muted d-block small" style="font-size: 11px;">FIXED / VIDEO</span>
                                        <span class="fw-bold font-monospace text-warning fs-6">${{ number_format($campaign->reward_per_video, 2) }}</span>
                                        <span class="small text-muted d-block text-truncate" style="font-size: 10px;">Target: {{ $campaign->formatted_expected_views }}</span>
                                    @else
                                        <span class="text-muted d-block small" style="font-size: 11px;">CPM REWARD</span>
                                        <span class="fw-bold font-monospace text-success fs-6">${{ number_format($campaign->reward_rate_per_thousand, 2) }}</span>
                                        <span class="small text-muted d-block" style="font-size: 10px;">per 1,000 views</span>
                                    @endif
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block small" style="font-size: 11px;">REMAINING BUDGET</span>
                                    <span class="fw-bold font-monospace text-dark fs-6">${{ number_format($campaign->remaining_budget, 2) }}</span>
                                    <span class="small text-muted d-block" style="font-size: 10px;">Available Bounty</span>
                                </div>
                            </div>
                        </div>

                        <!-- Geo & Reach Target -->
                        <div class="d-flex align-items-center justify-content-between text-muted small mb-2">
                            <span><i class="las la-globe me-1"></i> Target:</span>
                            <span class="fw-semibold text-dark">{{ $campaign->target_countries_summary ?? '🌍 Worldwide' }}</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2 pt-3 border-top mt-3">
                        <a href="{{ route('streamer.submissions.create', ['campaign_id' => $campaign->id]) }}" class="btn btn-streamer text-white fw-bold">
                            <i class="las la-upload me-1"></i> Submit YouTube Promo Link
                        </a>
                        <a href="{{ route('streamer.campaigns.show', $campaign->id) }}" class="btn btn-secondary-rgba">
                            <i class="las la-info-circle me-1"></i> View Briefing &amp; Assets
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center">
                <i class="las la-search fs-1 text-muted mb-2"></i>
                <h5 class="fw-bold text-dark">No sponsorship campaigns found</h5>
                <p class="text-muted small">Try broadening your search keywords or clear your filters.</p>
                <div>
                    <a href="{{ route('streamer.campaigns') }}" class="btn btn-outline-danger btn-sm">Clear Filters</a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $campaigns->links() }}
</div>
@endsection
