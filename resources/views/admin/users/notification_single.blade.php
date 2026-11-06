@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center gy-4">

    <!-- User Header Card -->
    <div class="col-xl-9 col-lg-11">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-lg bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 54px; height: 54px; background: rgba(99, 102, 241, 0.1);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h5 class="mb-1 text-dark fw-bold">{{ $user->name }}</h5>
                        <div class="d-flex flex-wrap align-items-center gap-2 small">
                            <span class="text-muted"><i class="las la-envelope"></i> {{ $user->email }}</span>
                            <span class="text-muted">•</span>
                            @if($user->role === 'brand')
                                <span class="badge badge--primary">BRAND ADVERTISER</span>
                            @elseif($user->role === 'clipper')
                                <span class="badge badge--success">CLIPPER CREATOR</span>
                            @else
                                <span class="badge badge--warning">ADMINISTRATOR</span>
                            @endif
                            <span class="text-muted">•</span>
                            <span class="badge bg-light text-dark border">
                                <i class="las la-bell text-warning"></i> {{ $deviceCount }} FCM Device(s) Registered
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-outline-secondary" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-arrow-left me-1"></i> User Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Form Card -->
    <div class="col-xl-9 col-lg-11">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-paper-plane text-primary me-1"></i> Send Notification Message</h6>
                    <small class="text-muted">Dispatch custom in-app notifications, email alerts, or Firebase browser push.</small>
                </div>
                <span class="badge bg-primary text-white px-3 py-1 fw-bold">Multi-Channel</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ safeRoute('admin.users.notification.single', ['id' => $user->id], '/admin/users/' . $user->id . '/notification') }}" method="POST">
                    @csrf

                    <!-- Channel Selection Row (HYIPLab Inspired) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-dark mb-2">Delivery Channel <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="border rounded p-3 d-flex align-items-center gap-3 cursor-pointer w-100 transition-all channel-card active" id="cardEmail" onclick="selectChannel('email')">
                                    <input type="radio" name="via" value="email" checked class="form-check-input mt-0 d-none">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1);">
                                        <i class="las la-envelope fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">Email Only</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Sent to {{ $user->email }}</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="border rounded p-3 d-flex align-items-center gap-3 cursor-pointer w-100 transition-all channel-card" id="cardPush" onclick="selectChannel('push')">
                                    <input type="radio" name="via" value="push" class="form-check-input mt-0 d-none">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 40px; height: 40px; background: rgba(245, 158, 11, 0.1);">
                                        <i class="las la-bell fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">Firebase Web Push</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $deviceCount }} Token(s) Active</small>
                                    </div>
                                </label>
                            </div>

                            <div class="col-md-4">
                                <label class="border rounded p-3 d-flex align-items-center gap-3 cursor-pointer w-100 transition-all channel-card" id="cardBoth" onclick="selectChannel('both')">
                                    <input type="radio" name="via" value="both" class="form-check-input mt-0 d-none">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.1);">
                                        <i class="las la-bullhorn fs-4"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">Both (Email + Push)</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Maximum engagement</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row gy-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-dark mb-1">Subject / Notification Headline <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" style="height: 44px;" placeholder="e.g. Your payout has been approved & transferred" required value="{{ old('subject') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-dark mb-1">Action URL (Destination on click)</label>
                            <input type="url" name="action_url" class="form-control font-monospace" style="height: 44px;" placeholder="{{ url('/brand/campaigns') }}" value="{{ old('action_url') }}">
                            <small class="text-muted">User will be directed to this link when clicking the notification.</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-dark mb-1">Message Content <span class="text-danger">*</span></label>
                            <textarea name="message" rows="6" class="form-control" placeholder="Write message to {{ $user->name }}..." required>{{ old('message') }}</textarea>
                            <small class="text-muted">Supports clean plaintext and Markdown formatting.</small>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="height: 44px;">
                            <i class="las la-paper-plane me-1"></i> Send Notification Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Audit History of Past Notifications -->
    <div class="col-xl-9 col-lg-11">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-history text-secondary me-1"></i> Notification Delivery History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Channel</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Dispatched At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLogs as $log)
                                <tr>
                                    <td>
                                        <span class="badge {{ $log->channel === 'email' ? 'badge--primary' : ($log->channel === 'push' ? 'badge--warning' : 'badge--success') }}">
                                            {{ strtoupper($log->channel) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark d-block text-truncate" style="max-width: 320px;">{{ $log->title }}</span>
                                        <small class="text-muted text-truncate d-block" style="max-width: 320px;">{{ $log->body }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge--success"><i class="las la-check-circle"></i> {{ strtoupper($log->status) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->sent_at ? $log->sent_at->format('M d, Y H:i') : $log->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No notifications sent to this user yet.</td>
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

@push('style')
<style>
    .channel-card {
        cursor: pointer;
        border: 2px solid #e2e8f0;
        background: #fff;
    }
    .channel-card.active {
        border-color: #6366f1 !important;
        background: rgba(99, 102, 241, 0.04) !important;
    }
</style>
@endpush

@push('script')
<script>
    "use strict";
    function selectChannel(method) {
        document.querySelectorAll('.channel-card').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('input[name="via"]').forEach(r => r.checked = false);

        if (method === 'email') {
            document.getElementById('cardEmail').classList.add('active');
            document.querySelector('input[name="via"][value="email"]').checked = true;
        } else if (method === 'push') {
            document.getElementById('cardPush').classList.add('active');
            document.querySelector('input[name="via"][value="push"]').checked = true;
        } else {
            document.getElementById('cardBoth').classList.add('active');
            document.querySelector('input[name="via"][value="both"]').checked = true;
        }
    }
</script>
@endpush
