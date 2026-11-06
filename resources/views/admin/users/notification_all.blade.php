@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center gy-4">

    <!-- Audience Stat Cards -->
    <div class="col-xl-10 col-lg-12">
        <div class="row gy-3">
            <div class="col-lg-3 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Total Network</small>
                            <h4 class="fw-bold mb-0 text-dark mt-1 font-monospace">{{ $stats['total_users'] }}</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 44px; height: 44px; background: rgba(99, 102, 241, 0.1);">
                            <i class="las la-users fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Brand Advertisers</small>
                            <h4 class="fw-bold mb-0 text-info mt-1 font-monospace">{{ $stats['brand_users'] }}</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 44px; height: 44px; background: rgba(14, 165, 233, 0.1);">
                            <i class="las la-building fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Clipper Creators</small>
                            <h4 class="fw-bold mb-0 text-success mt-1 font-monospace">{{ $stats['clipper_users'] }}</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.1);">
                            <i class="las la-scissors fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Push Subscriptions</small>
                            <h4 class="fw-bold mb-0 text-warning mt-1 font-monospace">{{ $stats['devices_registered'] }}</h4>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.1);">
                            <i class="las la-bell fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Broadcast Compose Card -->
    <div class="col-xl-10 col-lg-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-broadcast-tower text-primary me-1"></i> Broadcast Notification to Community</h5>
                    <small class="text-muted">Broadcast major announcements, campaign drops, and payout updates to selected audiences.</small>
                </div>
                <span class="badge bg-primary text-white px-3 py-1 fw-bold">Mass Dispatch</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ safeRoute('admin.notifications.all', [], '/admin/notifications/send-all') }}" method="POST">
                    @csrf

                    <div class="row gy-4">
                        <!-- Audience Targeting -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark mb-1">Target Audience <span class="text-danger">*</span></label>
                            <select name="target_audience" class="form-select fw-bold" style="height: 44px;" required>
                                <option value="all">All Registered Users (Brands + Creators)</option>
                                <option value="brand">Brand Advertisers Only</option>
                                <option value="clipper">Clipper Creators Only</option>
                                <option value="active">Active & Verified Accounts Only</option>
                            </select>
                            <small class="text-muted">Targeted segments will receive this broadcast alert.</small>
                        </div>

                        <!-- Delivery Channel -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark mb-1">Broadcast Channel <span class="text-danger">*</span></label>
                            <select name="via" class="form-select fw-bold" style="height: 44px;" required>
                                <option value="both">Multi-Channel: In-App Feed + Email + Firebase Push</option>
                                <option value="email">Email Broadcast Only</option>
                                <option value="push">Firebase Browser Web Push Only</option>
                            </select>
                            <small class="text-muted">Multi-channel reaches users across their active notification methods.</small>
                        </div>

                        <!-- Subject -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark mb-1">Broadcast Subject / Headline <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" style="height: 44px;" placeholder="e.g. 🔥 New $10,000 Bounty Pool Live: TikTok Clip Challenges Open" required value="{{ old('subject') }}">
                        </div>

                        <!-- Action URL -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark mb-1">Call to Action Target URL</label>
                            <input type="url" name="action_url" class="form-control font-monospace" style="height: 44px;" placeholder="{{ url('/clipper/bounties') }}" value="{{ old('action_url') }}">
                            <small class="text-muted">Optional link opened when user taps the notification.</small>
                        </div>

                        <!-- Message Content -->
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark mb-1">Broadcast Body Content <span class="text-danger">*</span></label>
                            <textarea name="message" rows="7" class="form-control" placeholder="Write comprehensive broadcast announcement..." required>{{ old('message') }}</textarea>
                            <small class="text-muted">Plaintext with automatic paragraph formatting applied upon delivery.</small>
                        </div>
                    </div>

                    <div class="border-top mt-4 pt-3 d-flex align-items-center justify-content-between">
                        <small class="text-muted"><i class="las la-shield-alt text-success"></i> High-throughput async queueing ensures reliable delivery across large user bases.</small>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="height: 44px;">
                            <i class="las la-paper-plane me-1"></i> Send Broadcast Announcement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Recent Broadcast Logs -->
    <div class="col-xl-10 col-lg-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-history text-secondary me-1"></i> Past Community Broadcast History</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Audience</th>
                                <th>Channel</th>
                                <th>Recipients</th>
                                <th>Sent Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBroadcasts as $b)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark d-block text-truncate" style="max-width: 320px;">{{ $b->title }}</span>
                                        <small class="text-muted text-truncate d-block" style="max-width: 320px;">{{ $b->body }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ strtoupper($b->metadata['audience'] ?? 'ALL') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $b->channel === 'email' ? 'badge--primary' : ($b->channel === 'push' ? 'badge--warning' : 'badge--success') }}">
                                            {{ strtoupper($b->channel) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace">{{ $b->metadata['recipients_count'] ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $b->sent_at ? $b->sent_at->format('M d, Y H:i') : $b->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No global broadcasts dispatched yet.</td>
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
