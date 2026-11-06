@extends('brand.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <!-- Filter & Search Toolbar (Fully Responsive) -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('brand.campaigns') }}" method="GET" class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-3">
                    <!-- Status Filter Tabs (Touch scroll on mobile) -->
                    <div class="d-flex flex-wrap align-items-center gap-2 filter-btn-group">
                        <a href="{{ route('brand.campaigns') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}" style="height: 38px;">
                            All
                        </a>
                        <a href="{{ route('brand.campaigns', ['status' => 'active']) }}" class="btn btn-sm {{ $status === 'active' ? 'btn-success text-white' : 'btn-outline-success' }}" style="height: 38px;">
                            <i class="las la-check-circle me-1"></i> Active
                        </a>
                        <a href="{{ route('brand.campaigns', ['status' => 'draft']) }}" class="btn btn-sm {{ $status === 'draft' ? 'btn-warning text-white' : 'btn-outline-warning' }}" style="height: 38px;">
                            <i class="las la-clock me-1"></i> Drafts
                        </a>
                        <a href="{{ route('brand.campaigns', ['status' => 'paused']) }}" class="btn btn-sm {{ $status === 'paused' ? 'btn-info text-white' : 'btn-outline-info' }}" style="height: 38px;">
                            <i class="las la-pause me-1"></i> Paused
                        </a>
                        <a href="{{ route('brand.campaigns', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-secondary text-white' : 'btn-outline-secondary' }}" style="height: 38px;">
                            Completed
                        </a>
                    </div>

                    <!-- Search & Create Button -->
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                        <div class="input-group input-group-sm flex-grow-1" style="min-width: 220px;">
                            <input type="text" name="search" class="form-control" placeholder="Search campaign title..." value="{{ request('search') }}" style="height: 38px;">
                            <button class="btn btn-primary px-3" type="submit" style="height: 38px;"><i class="las la-search"></i></button>
                        </div>
                        <a href="{{ route('brand.campaigns.create') }}" class="btn btn-sm btn-brand d-flex align-items-center justify-content-center gap-1 text-nowrap px-3" style="height: 38px;">
                            <i class="las la-plus"></i> Launch Campaign
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Desktop & Tablet Table View (>= 768px) -->
        <div class="card border-0 shadow-sm d-none d-md-block">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Campaign Title</th>
                                <th>Category</th>
                                <th>Total Budget</th>
                                <th>Remaining</th>
                                <th>Payout Rate</th>
                                <th>Delivered Views</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $camp)
                                @php
                                    $delivered = (int) $camp->submissions->sum('observed_views');
                                    $target = (int) ($camp->target_views ?: 10000);
                                    $pct = $target > 0 ? min(100, round(($delivered / $target) * 100)) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $camp->title }}</div>
                                        <small class="text-muted">#{{ $camp->id }} &bull; Created {{ $camp->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $camp->category }}</span>
                                    </td>
                                    <td class="fw-bold font-monospace text-dark">${{ number_format($camp->total_budget, 2) }}</td>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary">${{ number_format($camp->remaining_budget, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($camp->isFixedPerVideo())
                                            <span class="badge font-monospace" style="background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;">${{ number_format($camp->reward_per_video, 2) }}/vid</span>
                                            @if($camp->target_views_per_video)
                                                <small class="d-block text-muted" style="font-size:10.5px;">{{ $camp->formatted_expected_views }} target</small>
                                            @endif
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">${{ number_format($camp->reward_rate_per_thousand, 2) }}/1k</span>
                                        @endif
                                    </td>
                                    <td style="min-width: 140px;">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <span class="fw-semibold text-dark font-monospace">{{ number_format($delivered) }}</span>
                                            <span class="text-muted">/ {{ number_format($target) }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($camp->status === 'active')
                                            <span class="badge badge--success">ACTIVE</span>
                                        @elseif($camp->status === 'draft')
                                            <span class="badge badge--warning">UNFUNDED</span>
                                        @elseif($camp->status === 'paused')
                                            <span class="badge badge--warning">PAUSED</span>
                                        @else
                                            <span class="badge bg-secondary">COMPLETED</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            @if($camp->status === 'draft')
                                                <a href="{{ route('brand.campaigns.checkout', $camp->id) }}" class="btn btn-sm btn-success d-inline-flex align-items-center justify-content-center gap-1 px-2.5" style="height: 32px; font-size: 12px; font-weight: 600;">
                                                    <i class="las la-credit-card"></i> Fund with Stripe
                                                </a>
                                            @else
                                                <a href="{{ route('web.campaign.detail', $camp->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" title="View Marketplace Listing" style="height: 32px; width: 32px; padding: 0;">
                                                    <i class="las la-external-link-alt"></i>
                                                </a>

                                                <a href="{{ route('brand.campaigns.edit', $camp->id) }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center justify-content-center gap-1 px-2.5" title="Edit Campaign Details" style="height: 32px; font-size: 12px; font-weight: 600;">
                                                    <i class="las la-edit"></i> Edit
                                                </a>

                                                @if(in_array($camp->status, ['active', 'paused']))
                                                    <!-- Pause / Resume Toggle -->
                                                    <form action="{{ route('brand.campaigns.toggle', $camp->id) }}" method="POST" class="d-inline m-0">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $camp->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} d-inline-flex align-items-center justify-content-center" title="{{ $camp->status === 'active' ? 'Pause Campaign' : 'Resume Campaign' }}" style="height: 32px; width: 32px; padding: 0;">
                                                            <i class="las {{ $camp->status === 'active' ? 'la-pause' : 'la-play' }}"></i>
                                                        </button>
                                                    </form>

                                                    <!-- End Campaign Button -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#endModal{{ $camp->id }}" title="End Campaign & Refund Leftover Budget" style="height: 32px; width: 32px; padding: 0;">
                                                        <i class="las la-stop-circle"></i>
                                                    </button>
                                                @endif
                                            @endif

                                            <!-- Delete Campaign Button -->
                                            <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#deleteCampModal{{ $camp->id }}" title="Delete Campaign Permanently" style="height: 32px; width: 32px; padding: 0;">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="las la-bullhorn fs-1 d-block mb-2 text-muted"></i>
                                        No campaigns found matching your filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($campaigns->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $campaigns->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Mobile Card List View (< 768px) -->
        <div class="d-md-none">
            @forelse($campaigns as $camp)
                @php
                    $delivered = (int) $camp->submissions->sum('observed_views');
                    $target = (int) ($camp->target_views ?: 10000);
                    $pct = $target > 0 ? min(100, round(($delivered / $target) * 100)) : 0;
                @endphp
                <div class="card mb-3 border rounded-3 p-3 bg-white shadow-xs">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ $camp->title }}</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mt-0.5">
                                <span class="badge bg-light text-dark border">{{ $camp->category }}</span>
                                <small class="text-muted font-monospace">#{{ $camp->id }}</small>
                                <small class="text-muted">&bull; {{ $camp->created_at->format('M d') }}</small>
                            </div>
                        </div>
                        <div>
                            @if($camp->status === 'active')
                                <span class="badge badge--success">ACTIVE</span>
                            @elseif($camp->status === 'draft')
                                <span class="badge badge--warning">UNFUNDED</span>
                            @elseif($camp->status === 'paused')
                                <span class="badge badge--warning">PAUSED</span>
                            @else
                                <span class="badge bg-secondary">COMPLETED</span>
                            @endif
                        </div>
                    </div>

                    <!-- 3-Col Stats -->
                    <div class="row g-1 p-2 bg-light rounded-2 mb-2 text-center" style="font-size: 11px;">
                        <div class="col-4 border-end">
                            <span class="text-muted d-block" style="font-size: 9.5px; text-transform: uppercase;">Budget</span>
                            <strong class="font-monospace text-dark">${{ number_format($camp->total_budget, 2) }}</strong>
                        </div>
                        <div class="col-4 border-end">
                            <span class="text-muted d-block" style="font-size: 9.5px; text-transform: uppercase;">Payout</span>
                            @if($camp->isFixedPerVideo())
                                <strong class="font-monospace text-purple" style="color: #7e22ce;">${{ number_format($camp->reward_per_video, 2) }}/v</strong>
                            @else
                                <strong class="font-monospace text-success">${{ number_format($camp->reward_rate_per_thousand, 2) }}/1k</strong>
                            @endif
                        </div>
                        <div class="col-4">
                            <span class="text-muted d-block" style="font-size: 9.5px; text-transform: uppercase;">Remaining</span>
                            <strong class="font-monospace text-primary">${{ number_format($camp->remaining_budget, 2) }}</strong>
                        </div>
                    </div>

                    <!-- Views Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1" style="font-size: 11px;">
                            <span class="text-muted">Reach:</span>
                            <span class="fw-bold font-monospace text-dark">{{ number_format($delivered) }} / {{ number_format($target) }} ({{ $pct }}%)</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <!-- Action Buttons for Mobile -->
                    <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            @if($camp->status === 'draft')
                                <a href="{{ route('brand.campaigns.checkout', $camp->id) }}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 px-3" style="height: 34px; font-size: 12px; font-weight: 600;">
                                    <i class="las la-credit-card"></i> Fund Campaign
                                </a>
                            @else
                                <a href="{{ route('web.campaign.detail', $camp->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" style="height: 34px; width: 34px; padding: 0;">
                                    <i class="las la-external-link-alt"></i>
                                </a>
                                <a href="{{ route('brand.campaigns.edit', $camp->id) }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 px-2.5" style="height: 34px; font-size: 12px;">
                                    <i class="las la-edit"></i> Edit
                                </a>
                                @if(in_array($camp->status, ['active', 'paused']))
                                    <form action="{{ route('brand.campaigns.toggle', $camp->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $camp->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} d-inline-flex align-items-center justify-content-center" style="height: 34px; width: 34px; padding: 0;">
                                            <i class="las {{ $camp->status === 'active' ? 'la-pause' : 'la-play' }}"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#endModal{{ $camp->id }}" style="height: 34px; width: 34px; padding: 0;">
                                        <i class="las la-stop-circle"></i>
                                    </button>
                                @endif
                            @endif
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#deleteCampModal{{ $camp->id }}" style="height: 34px; width: 34px; padding: 0;">
                            <i class="las la-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm p-4 text-center text-muted">
                    <i class="las la-bullhorn fs-1 d-block mb-2 text-muted"></i>
                    No campaigns found matching your filter.
                </div>
            @endforelse

            @if($campaigns->hasPages())
                <div class="p-3 d-flex justify-content-center">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

        <!-- Shared Modals for End & Delete -->
        @foreach($campaigns as $camp)
            <!-- End Confirmation Modal -->
            <div class="modal fade" id="endModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('brand.campaigns.end', $camp->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">End Campaign & Refund Budget</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <p class="mb-2">Are you sure you want to conclude <strong>{{ $camp->title }}</strong>?</p>
                                <div class="alert alert-info py-2 small mb-0">
                                    <i class="las la-info-circle me-1"></i>
                                    Remaining unspent budget of <strong>${{ number_format($camp->remaining_budget, 2) }}</strong> will be automatically credited back to your Brand Wallet.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="height: 36px;">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-danger" style="height: 36px;">Confirm & Refund</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteCampModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('brand.campaigns.delete', $camp->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold text-danger">
                                    <i class="las la-exclamation-triangle me-1"></i> Delete Campaign
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                <p class="mb-2">Are you sure you want to permanently delete campaign <strong>{{ $camp->title }}</strong>?</p>
                                @if((float)$camp->remaining_budget > 0)
                                    <div class="alert alert-success py-2 small mb-0">
                                        <i class="las la-shield-alt me-1"></i>
                                        Remaining unspent budget of <strong>${{ number_format($camp->remaining_budget, 2) }}</strong> will be automatically credited back to your Brand Wallet.
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">This campaign and all associated submissions will be permanently removed.</p>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="height: 36px;">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-danger" style="height: 36px;">
                                    <i class="las la-trash me-1"></i> Permanently Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
@endsection
