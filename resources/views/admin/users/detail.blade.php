@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Action Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-lg bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px; background: rgba(99, 102, 241, 0.1);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="mb-1 text-dark fw-bold">{{ $user->name }}</h4>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="text-muted"><i class="las la-envelope"></i> {{ $user->email }}</span>
                            <span class="text-muted">•</span>
                            @if($user->role === 'brand')
                                <span class="badge badge--primary"><i class="las la-building"></i> BRAND</span>
                            @elseif($user->role === 'clipper')
                                <span class="badge badge--success"><i class="las la-scissors"></i> CLIPPER / CREATOR</span>
                            @else
                                <span class="badge badge--warning"><i class="las la-shield-alt"></i> ADMINISTRATOR</span>
                            @endif

                            @if($user->status === 'active')
                                <span class="badge badge--success">ACTIVE</span>
                            @else
                                <span class="badge badge--danger">BANNED</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustBalanceModal" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-wallet me-1"></i> Adjust Balance
                    </button>

                    <a href="{{ safeRoute('admin.users.notification.single', ['id' => $user->id], '/admin/users/' . $user->id . '/notification') }}" class="btn btn-outline-info" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-bell me-1"></i> Send Notification
                    </a>

                    @if(!$user->isAdmin())
                        <a href="{{ route('admin.impersonate', $user->id) }}" class="btn btn-outline-primary" title="Login as this user" style="height: 44px; display: inline-flex; align-items: center;">
                            <i class="las la-sign-in-alt me-1"></i> Login As User
                        </a>

                        <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-outline-danger' : 'btn-outline-success' }}" style="height: 44px; display: inline-flex; align-items: center;">
                                <i class="las {{ $user->status === 'active' ? 'la-user-slash' : 'la-user-check' }} me-1"></i>
                                {{ $user->status === 'active' ? 'Ban Account' : 'Unban Account' }}
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary" style="height: 44px; display: inline-flex; align-items: center;">
                        <i class="las la-arrow-left me-1"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 High-Impact KPI Metrics -->
    <div class="col-xl-3 col-sm-6">
        <div class="card b-radius--10 border p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Available Balance</span>
                    <h3 class="fw-bold mb-0 text-success mt-1 font-monospace">${{ number_format($user->wallet->available_balance ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 bg-soft-success text-success rounded-circle" style="background: rgba(16, 185, 129, 0.1);">
                    <i class="las la-wallet fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card b-radius--10 border p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Withdrawn</span>
                    <h3 class="fw-bold mb-0 text-primary mt-1 font-monospace">${{ number_format($user->wallet->total_withdrawn ?? 0, 2) }}</h3>
                </div>
                <div class="p-3 bg-soft-primary text-primary rounded-circle" style="background: rgba(99, 102, 241, 0.1);">
                    <i class="las la-money-bill-wave fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card b-radius--10 border p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                        {{ $user->role === 'brand' ? 'Campaigns Created' : 'Clip Submissions' }}
                    </span>
                    <h3 class="fw-bold mb-0 text-dark mt-1 font-monospace">
                        {{ $user->role === 'brand' ? ($user->brand?->campaigns()->count() ?? 0) : ($user->submissions()->count() ?? 0) }}
                    </h3>
                </div>
                <div class="p-3 bg-soft-info text-info rounded-circle" style="background: rgba(14, 165, 233, 0.1);">
                    <i class="las {{ $user->role === 'brand' ? 'la-bullhorn' : 'la-video' }} fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card b-radius--10 border p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.05em;">Member Since</span>
                    <h5 class="fw-bold mb-0 text-dark mt-1">{{ $user->created_at->format('M d, Y') }}</h5>
                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                </div>
                <div class="p-3 bg-soft-warning text-warning rounded-circle" style="background: rgba(245, 158, 11, 0.1);">
                    <i class="las la-calendar-check fs-2"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Direct Bank Account (For Clipper) or Brand Profile (For Brand) -->
    @if($user->role === 'clipper')
        <div class="col-lg-6">
            <div class="card b-radius--10 border h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-university text-primary me-1"></i> Clipper Direct Bank Details</h5>
                    <span class="badge badge--success">Direct Wire Transfer</span>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 rounded bg-light border mb-3">
                        <div class="row gy-3">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Bank Name</small>
                                <span class="fw-bold text-dark fs-6">{{ $user->wallet->payout_bank_name ?: 'Not configured by creator' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Account Holder Title</small>
                                <span class="fw-bold text-dark fs-6">{{ $user->wallet->payout_account_holder ?: 'N/A' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Account / IBAN Number</small>
                                <span class="font-monospace fw-bold text-primary fs-6">{{ $user->wallet->payout_account_number ?: 'N/A' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">WhatsApp / Phone</small>
                                <span class="fw-bold text-dark fs-6">{{ $user->wallet->payout_phone_number ?: 'N/A' }}</span>
                            </div>
                            <div class="col-sm-12">
                                <small class="text-muted d-block">Swift / Routing Code</small>
                                <span class="font-monospace text-muted">{{ $user->wallet->payout_routing_swift ?: 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted"><i class="las la-info-circle"></i> When processing payouts for this creator, copy their bank transfer details above and paste wire confirmation transaction ID in the payout record.</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card b-radius--10 border h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-share-alt text-info me-1"></i> Connected Social Channels</h5>
                    <span class="badge badge--info">{{ $user->socialAccounts->count() }} Connected</span>
                </div>
                <div class="card-body p-4">
                    @forelse($user->socialAccounts as $account)
                        <div class="d-flex align-items-center justify-content-between p-2.5 mb-2 border rounded bg-white">
                            <div class="d-flex align-items-center gap-2">
                                <i class="lab la-{{ strtolower(explode(' ', $account->platform)[0]) }} fs-4 text-primary"></i>
                                <div>
                                    <span class="fw-bold text-dark d-block">{{ $account->platform }}</span>
                                    <small class="text-muted">{{ $account->handle ?: $account->account_name }}</small>
                                </div>
                            </div>
                            <span class="badge badge--success"><i class="las la-check-circle"></i> Verified</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="las la-link fs-2 text-muted mb-2"></i>
                            <p class="mb-0">No external social channels connected yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @elseif($user->role === 'brand')
        <div class="col-12">
            <div class="card b-radius--10 border">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-building text-primary me-1"></i> Brand Studio Profile</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Company Name</small>
                            <span class="fw-bold text-dark fs-6">{{ $user->brand->company_name ?? $user->name }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Brand Contact Email</small>
                            <span class="fw-bold text-dark fs-6">{{ $user->brand->contact_email ?? $user->email }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Website / Social URL</small>
                            <span class="fw-bold text-primary fs-6">{{ $user->brand->website_url ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit User Profile Form -->
    <div class="col-lg-7">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-user-edit text-primary me-1"></i> Edit User Information</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf

                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" style="height: 44px;" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" style="height: 44px;" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" style="height: 44px;" required>
                                <option value="clipper" {{ $user->role === 'clipper' ? 'selected' : '' }}>Clipper / Creator</option>
                                <option value="brand" {{ $user->role === 'brand' ? 'selected' : '' }}>Brand Advertiser</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Platform Administrator</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" style="height: 44px;" required>
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Active (Unrestricted)</option>
                                <option value="banned" {{ $user->status === 'banned' ? 'selected' : '' }}>Banned (Restricted)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Set New Password</label>
                            <input type="password" name="password" class="form-control" style="height: 44px;" placeholder="Leave blank to keep unchanged">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" style="height: 44px;" placeholder="Repeat new password">
                        </div>

                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="height: 44px;">
                                <i class="las la-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Ledger History -->
    <div class="col-lg-5">
        <div class="card b-radius--10 border h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-history text-secondary me-1"></i> Recent Wallet Activity</h5>
                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#adjustBalanceModal">
                    <i class="las la-plus-circle"></i> Adjust
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <span class="badge {{ $trx->type === 'credit' || $trx->type === 'deposit' ? 'badge--success' : 'badge--warning' }}">
                                            {{ strtoupper($trx->type) }}
                                        </span>
                                        <small class="d-block text-muted text-truncate" style="max-width: 130px;">{{ $trx->description }}</small>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold {{ $trx->type === 'credit' || $trx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                            {{ $trx->type === 'credit' || $trx->type === 'deposit' ? '+' : '-' }}${{ number_format($trx->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $trx->created_at->format('M d, H:i') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No transactions recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Adjust Balance (Credit / Debit) -->
<div class="modal fade" id="adjustBalanceModal" tabindex="-1" aria-labelledby="adjustBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="adjustBalanceModalLabel"><i class="las la-wallet text-success me-1"></i> Adjust User Balance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.adjust-balance', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Operation Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" style="height: 44px;" required>
                            <option value="credit">Add Balance (Credit +)</option>
                            <option value="debit">Subtract Balance (Debit -)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount ($ USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control" style="height: 44px;" placeholder="50.00" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason / Memo <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="e.g. Administrative credit bonus, Direct bank reconciliation adjustment..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="height: 44px;">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold" style="height: 44px;"><i class="las la-check me-1"></i> Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
