@extends('brand.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <!-- Filter Header (Standardized 38px Height Toolbar) -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('brand.clips') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('brand.clips') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}" style="height: 38px;">
                            All Clips
                        </a>
                        <a href="{{ route('brand.clips', ['status' => 'pending']) }}" class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-white' : 'btn-outline-warning' }}" style="height: 38px;">
                            <i class="las la-clock me-1"></i> Pending Review
                        </a>
                        <a href="{{ route('brand.clips', ['status' => 'approved_by_brand']) }}" class="btn btn-sm {{ $status === 'approved_by_brand' ? 'btn-success text-white' : 'btn-outline-success' }}" style="height: 38px;">
                            <i class="las la-check-circle me-1"></i> Approved
                        </a>
                        <a href="{{ route('brand.clips', ['status' => 'rejected']) }}" class="btn btn-sm {{ $status === 'rejected' ? 'btn-danger text-white' : 'btn-outline-danger' }}" style="height: 38px;">
                            <i class="las la-times-circle me-1"></i> Rejected
                        </a>
                    </div>
                    <div class="input-group input-group-sm" style="max-width: 320px;">
                        <input type="text" name="search" class="form-control" placeholder="Search creator, clip URL, campaign..." value="{{ request('search') }}" style="height: 38px;">
                        <button class="btn btn-primary" type="submit" style="height: 38px;"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Creator</th>
                                <th>Campaign</th>
                                <th>Platform</th>
                                <th>Clip URL</th>
                                <th>Views Delivered</th>
                                <th>Reward ($)</th>
                                <th>Status</th>
                                <th class="text-end">Verification Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $sub)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar bg-primary-subtle text-primary border border-primary-subtle rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
                                                {{ strtoupper(substr($sub->user->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $sub->user->name ?? 'Verified Creator' }}</div>
                                                <small class="text-muted font-monospace"><i class="las la-user-check text-success"></i> Creator #{{ $sub->user_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="{{ $sub->campaign->title ?? 'N/A' }}">
                                            {{ $sub->campaign->title ?? 'N/A' }}
                                        </div>
                                        <small class="text-muted">CPM: ${{ number_format($sub->campaign->reward_rate_per_thousand ?? 0, 2) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                            @if($sub->platform === 'tiktok')
                                                <i class="fab fa-tiktok text-dark"></i> TikTok
                                            @elseif($sub->platform === 'instagram')
                                                <i class="fab fa-instagram text-danger"></i> Instagram
                                            @elseif($sub->platform === 'youtube')
                                                <i class="fab fa-youtube text-danger"></i> YouTube
                                            @else
                                                <i class="fab fa-x-twitter text-dark"></i> {{ ucfirst($sub->platform) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ $sub->post_url }}" target="_blank" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="height: 34px;">
                                            <i class="las la-play"></i> Watch Clip
                                        </a>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-dark fs-6">{{ number_format($sub->observed_views) }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-success fs-6">
                                            ${{ number_format($sub->verified_reward > 0 ? $sub->verified_reward : $sub->estimated_reward, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        {!! showStatusBadge($sub->status) !!}
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($sub->status, ['pending', 'under_review', 'submitted']))
                                            <div class="d-flex justify-content-end align-items-center gap-1">
                                                <!-- Approve Form -->
                                                <form action="{{ route('brand.clips.approve', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success d-flex align-items-center gap-1" style="height: 34px;" title="Approve Clip & Queue Payout">
                                                        <i class="las la-check"></i> Approve
                                                    </button>
                                                </form>

                                                <!-- Reject Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $sub->id }}" style="height: 34px; width: 34px; padding: 0;" title="Reject Clip">
                                                    <i class="las la-times"></i>
                                                </button>

                                                <!-- Rejection Modal -->
                                                <div class="modal fade" id="rejectModal{{ $sub->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content text-start">
                                                            <form action="{{ route('brand.clips.reject', $sub->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title fw-bold">Reject Video Submission</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="E.g. Video did not feature the required brand tag, views suspect, or low quality audio..."></textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="height: 36px;">Cancel</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger" style="height: 36px;">Confirm Rejection</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="las la-video fs-1 d-block mb-2 text-muted"></i>
                                        No video submissions found for this filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
