@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        
        <!-- Filter and Search Header -->
        <div class="card b-radius--10 mb-4 border">
            <div class="card-body p-3">
                <form action="{{ route('admin.campaigns') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.campaigns') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}">All Campaigns</a>
                        <a href="{{ route('admin.campaigns', ['status' => 'active']) }}" class="btn btn-sm {{ $status === 'active' ? 'btn-success text-white' : 'btn-outline-success' }}">Active</a>
                        <a href="{{ route('admin.campaigns', ['status' => 'paused']) }}" class="btn btn-sm {{ $status === 'paused' ? 'btn-warning text-white' : 'btn-outline-warning' }}">Paused</a>
                        <a href="{{ route('admin.campaigns', ['status' => 'completed']) }}" class="btn btn-sm {{ $status === 'completed' ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">Completed</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search title or brand..." style="min-width: 220px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Campaigns Table -->
        <div class="card b-radius--10 border">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Brand Advertiser</th>
                                <th>Total Budget</th>
                                <th>Remaining</th>
                                <th>CPM Rate</th>
                                <th>Views Delivered</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($campaigns as $camp)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $camp->title }}</span>
                                        <small class="text-muted font-monospace">#{{ $camp->id }} — {{ $camp->category ?? 'Tech' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $camp->brand->company_name ?? 'Advertiser' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace">${{ number_format($camp->total_budget, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-primary">${{ number_format($camp->remaining_budget, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($camp->isFixedPerVideo())
                                            <span class="badge font-monospace" style="background:#f3e8ff; color:#7e22ce; border:1px solid #d8b4fe;">${{ number_format($camp->reward_per_video, 2) }}/vid</span>
                                            @if($camp->target_views_per_video)
                                                <small class="d-block text-muted" style="font-size:10px;">{{ $camp->formatted_expected_views }}</small>
                                            @endif
                                        @else
                                            <span class="badge badge--success">${{ number_format($camp->reward_rate_per_thousand, 2) }}/1k</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold">{{ number_format($camp->verified_views ?? 0) }}</span>
                                    </td>
                                    <td>
                                        @if($camp->status === 'active')
                                            <span class="badge badge--success">ACTIVE</span>
                                        @elseif($camp->status === 'paused')
                                            <span class="badge badge--warning">PAUSED</span>
                                        @elseif($camp->status === 'completed')
                                            <span class="badge badge--dark">COMPLETED</span>
                                        @else
                                            <span class="badge badge--secondary">{{ strtoupper($camp->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('admin.campaigns.edit', $camp->id) }}" class="btn btn-sm btn-outline-info d-inline-flex align-items-center justify-content-center gap-1 px-2.5" title="Edit Campaign" style="height: 32px; font-size: 12px; font-weight: 600;">
                                                <i class="las la-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.campaigns.toggle', $camp->id) }}" method="POST" class="d-inline m-0">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $camp->status === 'active' ? 'btn-outline-warning' : 'btn-outline-success' }} d-inline-flex align-items-center justify-content-center" title="{{ $camp->status === 'active' ? 'Pause Campaign' : 'Activate Campaign' }}" style="height: 32px; width: 32px; padding: 0;">
                                                <i class="las {{ $camp->status === 'active' ? 'la-pause' : 'la-play' }}"></i>
                                                </button>
                                            </form>
                                            <a href="{{ url('/campaigns/' . $camp->id) }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center justify-content-center" title="View Public Page" style="height: 32px; width: 32px; padding: 0;">
                                                <i class="las la-external-link-alt"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delCampModal{{ $camp->id }}" title="Delete Campaign Permanently" style="height: 32px; width: 32px; padding: 0;">
                                                <i class="las la-trash"></i>
                                            </button>

                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="delCampModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.campaigns.delete', $camp->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h6 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle me-1"></i> Delete Campaign</h6>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body text-start">
                                                                <p class="mb-2">Are you sure you want to permanently delete campaign <strong>{{ $camp->title }}</strong> (#{{ $camp->id }})?</p>
                                                                <p class="text-danger small mb-0"><i class="las la-info-circle me-1"></i> This will delete all associated country targets and video submissions.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="height: 36px;">Cancel</button>
                                                                <button type="submit" class="btn btn-sm btn-danger" style="height: 36px;"><i class="las la-trash me-1"></i> Delete Campaign</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No campaigns found matching filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($campaigns->hasPages())
                <div class="card-footer py-3 bg-white">
                    {{ $campaigns->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
