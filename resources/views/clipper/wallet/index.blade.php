@extends('clipper.layouts.app')

@section('panel')
<div class="row g-3 g-xl-4 mb-4">
    <!-- Balance & Finance KPIs (3 Equal Stat Cards) -->
    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Available Wallet Balance</p>
                <h3 class="fw-bold mb-1 text-success font-monospace" style="font-size: 26px;">${{ number_format($wallet->available_balance, 2) }}</h3>
                <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-money-bill-wave"></i> Ready for direct wire
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-wallet"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Lifetime Earned</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">${{ number_format($totalEarned, 2) }}</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-award"></i> Gross verified earnings
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="las la-coins"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Withdrawn to Bank</p>
                <h3 class="fw-bold mb-1 text-dark font-monospace" style="font-size: 26px;">${{ number_format($wallet->total_withdrawn, 2) }}</h3>
                <span class="badge bg-light text-muted border d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                    <i class="las la-university text-info"></i> Direct bank settled
                </span>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-receipt"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Direct Bank Account Information Card -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-university text-success fs-5"></i> Direct Bank Transfer Destination
                </h6>
                <a href="{{ route('clipper.payout-settings') }}" class="btn btn-sm btn-outline-secondary" style="height: 32px;">
                    <i class="las la-edit me-1"></i> Edit Bank Details
                </a>
            </div>
            <div class="card-body p-4">
                @if($hasBankSetup)
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Bank / Institution Name</span>
                            <strong class="text-dark fs-6">{{ $wallet->payout_bank_name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Account Holder Name</span>
                            <strong class="text-dark fs-6">{{ $wallet->payout_account_holder }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Account Number / IBAN</span>
                            <strong class="text-dark fs-6 font-monospace">{{ $wallet->payout_account_number }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Contact Phone / WhatsApp</span>
                            <strong class="text-dark fs-6">{{ $wallet->payout_phone_number ?: 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="alert alert-success mt-4 mb-0 py-2.5 px-3 small border-0 bg-success-subtle text-success d-flex align-items-center gap-2 rounded-3">
                        <i class="las la-check-circle fs-5 flex-shrink-0"></i>
                        <span>Your bank details are active. The Admin will use these details to send your payouts directly.</span>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="las la-university fs-1 text-warning mb-2 d-block"></i>
                        <h6 class="fw-bold text-dark">No Direct Bank Account Configured</h6>
                        <p class="text-muted small mb-3">Please configure your bank name, account holder, and account number to receive automatic or manual withdrawals.</p>
                        <a href="{{ route('clipper.payout-settings') }}" class="btn btn-creator btn-sm px-3" style="height: 38px;">
                            <i class="las la-plus-circle me-1"></i> Set Up Bank Details Now
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Withdrawal Card -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-paper-plane text-primary fs-5"></i> Request Bank Withdrawal
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('clipper.withdraw') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Available Balance</label>
                        <input type="text" class="form-control bg-light font-monospace fw-bold" value="${{ number_format($wallet->available_balance, 2) }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Withdrawal Amount ($ USD) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text fw-bold">$</span>
                            <input type="number" step="0.01" min="5" max="{{ $wallet->available_balance }}" name="amount" class="form-control font-monospace fw-bold" placeholder="Minimum $5.00" required {{ $wallet->available_balance < 5 ? 'disabled' : '' }}>
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Minimum manual withdrawal is $5.00 USD.</small>
                    </div>

                    @if($wallet->available_balance < 5)
                        <div class="alert alert-warning py-2 px-3 small border-0 mb-3 rounded-3">
                            <i class="las la-info-circle me-1"></i> Balance must be at least $5.00 to request a withdrawal.
                        </div>
                    @endif

                    <button type="submit" class="btn btn-creator w-100" style="height: 44px;" {{ ($wallet->available_balance < 5 || !$hasBankSetup) ? 'disabled' : '' }}>
                        <i class="las la-check-circle me-1"></i> Submit Withdrawal to Bank
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bank Payouts History Table -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-file-invoice-dollar text-success fs-5"></i> Direct Bank Payouts History
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Payout ID</th>
                                <th>Campaign / Source</th>
                                <th>Amount ($)</th>
                                <th>Method</th>
                                <th>Destination</th>
                                <th>Status</th>
                                <th class="text-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                                <tr>
                                    <td>
                                        <code class="text-muted small bg-light p-1 rounded font-monospace">#{{ $payout->id }}</code>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">
                                            {{ $payout->campaign?->title ?? 'Manual Withdrawal' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $payout->campaign?->brand?->company_name ?? 'Wallet Transfer' }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold font-monospace text-success fs-6">${{ number_format($payout->net_payout, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            <i class="las la-university me-1 text-success"></i> Direct Bank Wire
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted font-monospace text-truncate d-block" style="max-width: 200px;" title="{{ $payout->destination_account }}">
                                            {{ $payout->destination_account ?: 'Bank on File' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if(in_array($payout->status, ['COMPLETED', 'paid', 'settled']))
                                            <span class="badge-status badge-approved"><i class="las la-check"></i> Wired</span>
                                        @elseif(in_array($payout->status, ['PENDING_ADMIN_REVIEW', 'pending', 'processing']))
                                            <span class="badge-status badge-pending"><i class="las la-clock"></i> Processing Wire</span>
                                        @else
                                            <span class="badge-status badge-rejected"><i class="las la-times"></i> {{ ucfirst($payout->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $payout->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="las la-wallet fs-1 mb-2 text-muted opacity-50 d-block"></i>
                                        No bank payouts recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payouts->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $payouts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
