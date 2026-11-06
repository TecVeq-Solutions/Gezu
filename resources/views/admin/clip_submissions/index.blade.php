@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        
        <!-- Filter and Search Header -->
        <div class="card b-radius--10 mb-4 border">
            <div class="card-body p-3">
                <form action="{{ route('admin.clips') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.clips') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}">All Clips</a>
                        <a href="{{ route('admin.clips', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-white' : 'btn-outline-warning' }}">
                            Pending Review
                        </a>
                        <a href="{{ route('admin.clips', ['status' => 'verified']) }}" class="btn btn-sm {{ $status === 'verified' ? 'btn-success text-white' : 'btn-outline-success' }}">Approved</a>
                        <a href="{{ route('admin.clips', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger text-white' : 'btn-outline-danger' }}">Rejected</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search creator, clip URL, campaign..." style="min-width: 240px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card b-radius--10 border">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>Creator</th>
                                <th>Campaign</th>
                                <th>Platform</th>
                                <th>Clip URL</th>
                                <th>Observed Views</th>
                                <th>Reward ($)</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $sub)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $sub->user->name ?? 'Creator' }}</span>
                                        <small class="text-muted">{{ $sub->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block text-truncate" style="max-width: 150px;">{{ $sub->campaign->title ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $sub->campaign->brand->company_name ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge--primary">{{ strtoupper($sub->platform ?? 'TIKTOK') }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ $sub->post_url }}" target="_blank" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1" title="Open Post in New Tab">
                                            <i class="las la-play"></i> Watch Clip
                                        </a>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold">{{ number_format($sub->observed_views ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-success">${{ number_format($sub->verified_reward > 0 ? $sub->verified_reward : ($sub->estimated_reward ?? 5.00), 2) }}</span>
                                    </td>
                                    <td>
                                        {!! showStatusBadge($sub->status) !!}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            @if(in_array($sub->status, ['pending', 'under_review']))
                                                <!-- Quick Approve Form -->
                                                <form action="{{ route('admin.clips.approve', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Approve Clip & Queue Payout">
                                                        <i class="las la-check"></i>
                                                    </button>
                                                </form>

                                                <!-- Reject Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $sub->id }}" title="Reject Clip">
                                                    <i class="las la-times"></i>
                                                </button>

                                                <!-- Reject Modal -->
                                                <div class="modal fade" id="rejectModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.clips.reject', $sub->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title fw-bold">Reject Clip Submission</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label class="form-label small fw-bold">Reason for Rejection</label>
                                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="E.g. Guidelines not followed, low audio quality, invalid hashtags..."></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger">Confirm Rejection</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No clip submissions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($submissions->hasPages())
                <div class="card-footer py-3 bg-white">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
