@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        
        <!-- Filter and Search Header -->
        <div class="card b-radius--10 mb-4 border">
            <div class="card-body p-3">
                <form action="{{ route('admin.users') }}" method="GET" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-sm {{ empty($role) ? 'btn-primary' : 'btn-outline-secondary' }}" style="height: 38px; display: inline-flex; align-items: center;">All Users</a>
                        <a href="{{ route('admin.users', ['role' => 'brand']) }}" class="btn btn-sm {{ $role === 'brand' ? 'btn-info text-white' : 'btn-outline-info' }}" style="height: 38px; display: inline-flex; align-items: center;">Brands</a>
                        <a href="{{ route('admin.users', ['role' => 'clipper']) }}" class="btn btn-sm {{ $role === 'clipper' ? 'btn-success text-white' : 'btn-outline-success' }}" style="height: 38px; display: inline-flex; align-items: center;">Clippers</a>
                        <a href="{{ route('admin.users', ['role' => 'streamer']) }}" class="btn btn-sm {{ $role === 'streamer' ? 'btn-danger text-white' : 'btn-outline-danger' }}" style="height: 38px; display: inline-flex; align-items: center;">Streamers</a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ safeRoute('admin.notifications.all', [], '/admin/notifications/send-all') }}" class="btn btn-sm btn-outline-primary" style="height: 38px; display: inline-flex; align-items: center;">
                            <i class="las la-broadcast-tower me-1"></i> Broadcast
                        </a>
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search name or email..." style="min-width: 220px; height: 38px;">
                        <button type="submit" class="btn btn-primary" style="height: 38px; width: 38px; padding: 0;"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card b-radius--10 border">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Wallet Balance</th>
                                <th>Total Withdrawn</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $user->name }}</span>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>
                                        @if($user->role === 'brand')
                                            <span class="badge badge--primary"><i class="las la-building"></i> BRAND</span>
                                        @elseif($user->role === 'streamer')
                                            <span class="badge badge--danger" style="background: #dc2626;"><i class="fab fa-youtube"></i> STREAMER</span>
                                        @elseif($user->role === 'clipper')
                                            <span class="badge badge--success"><i class="las la-scissors"></i> CLIPPER</span>
                                        @else
                                            <span class="badge badge--warning"><i class="las la-shield-alt"></i> ADMIN</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold">${{ number_format($user->wallet->available_balance ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-success">${{ number_format($user->wallet->total_withdrawn ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge badge--success">ACTIVE</span>
                                        @else
                                            <span class="badge badge--danger">BANNED</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!$user->isAdmin())
                                                <!-- Full User Details & Edit -->
                                                <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-outline-info" title="User Profile & Balance Adjust" style="height: 32px; display: inline-flex; align-items: center;">
                                                    <i class="las la-user-edit me-1"></i> Edit
                                                </a>

                                                <!-- Send Direct Notification -->
                                                <a href="{{ safeRoute('admin.users.notification.single', ['id' => $user->id], '/admin/users/' . $user->id . '/notification') }}" class="btn btn-sm btn-outline-warning" title="Send Notification" style="height: 32px; width: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="las la-bell"></i>
                                                </a>

                                                <!-- One-Click Impersonation -->
                                                <a href="{{ route('admin.impersonate', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Login as this user" style="height: 32px; display: inline-flex; align-items: center;">
                                                    <i class="las la-sign-in-alt me-1"></i> Login
                                                </a>

                                                <!-- Ban / Activate Status Form -->
                                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm {{ $user->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $user->status === 'active' ? 'Ban User' : 'Unban User' }}" style="height: 32px; width: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                        <i class="las {{ $user->status === 'active' ? 'la-user-slash' : 'la-user-check' }}"></i>
                                                    </button>
                                                </form>

                                                <!-- Delete User Button -->
                                                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delUserModal{{ $user->id }}" title="Delete User Permanently" style="height: 32px; width: 32px; padding: 0;">
                                                    <i class="las la-trash"></i>
                                                </button>

                                                <!-- Delete User Modal -->
                                                <div class="modal fade" id="delUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title fw-bold text-danger"><i class="las la-user-times me-1"></i> Delete User Account</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <p class="mb-2">Are you sure you want to permanently delete user <strong>{{ $user->name }}</strong> (<code>{{ $user->email }}</code>)?</p>
                                                                    <div class="alert alert-danger py-2 small mb-0">
                                                                        <i class="las la-exclamation-circle me-1"></i>
                                                                        This will permanently delete their profile, wallet, associated submissions, and campaigns. This action cannot be undone.
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal" style="height: 36px;">Cancel</button>
                                                                    <button type="submit" class="btn btn-sm btn-danger" style="height: 36px;"><i class="las la-trash me-1"></i> Permanently Delete User</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <a href="{{ route('admin.users.detail', $user->id) }}" class="btn btn-sm btn-outline-info" title="View Admin Profile" style="height: 32px; display: inline-flex; align-items: center;">
                                                    <i class="las la-user-shield me-1"></i> Profile
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer py-3 bg-white">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
