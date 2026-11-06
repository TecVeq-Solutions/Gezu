@extends('streamer.layouts.master')

@section('content')
<div class="row g-4 mb-4">
    <!-- Available Balance Card -->
    <div class="col-md-4">
        <div class="stat-card border-danger">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Available for Payout</span>
                <h2 class="mb-0 fw-bold font-monospace text-danger">${{ number_format($wallet->available_balance, 2) }}</h2>
                <small class="text-muted">Direct Local/Int'l Bank Transfer</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <i class="las la-money-bill-wave"></i>
            </div>
        </div>
    </div>

    <!-- Pending Verification Balance Card -->
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Pending Payout Review</span>
                <h2 class="mb-0 fw-bold font-monospace text-warning">${{ number_format($wallet->pending_balance, 2) }}</h2>
                <small class="text-muted">In processing pipeline</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <i class="las la-hourglass-half"></i>
            </div>
        </div>
    </div>

    <!-- Total Withdrawn Card -->
    <div class="col-md-4">
        <div class="stat-card">
            <div>
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1" style="letter-spacing: 0.5px;">Lifetime Bank Payouts</span>
                <h2 class="mb-0 fw-bold font-monospace text-success">${{ number_format($wallet->total_withdrawn, 2) }}</h2>
                <small class="text-success"><i class="las la-check-circle"></i> Successfully Received</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-university"></i>
            </div>
        </div>
    </div>
</div>

<!-- Request Bank Withdrawal Box -->
<div class="card mb-4 border">
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="las la-money-check-alt text-danger me-2"></i> Request Direct Bank Payout</h6>
        <a href="{{ route('streamer.payout-settings') }}" class="small fw-semibold text-danger text-decoration-none">
            <i class="las la-cog"></i> Edit Bank Account
        </a>
    </div>
    <div class="card-body p-4">
        @if(empty($wallet->payout_bank_name) || empty($wallet->payout_account_number))
            <div class="alert alert-warning border-0 d-flex align-items-center justify-content-between p-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-exclamation-triangle fs-4 text-warning"></i>
                    <span>Please add your Direct Bank Transfer details before requesting a withdrawal.</span>
                </div>
                <a href="{{ route('streamer.payout-settings') }}" class="btn btn-warning btn-sm fw-bold">Setup Bank Account</a>
            </div>
        @else
            <div class="p-3 rounded bg-light border mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-sm-3">
                        <span class="text-muted small d-block">Payout Bank:</span>
                        <strong class="text-dark">{{ $wallet->payout_bank_name }}</strong>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small d-block">Account Holder:</span>
                        <strong class="text-dark">{{ $wallet->payout_account_holder }}</strong>
                    </div>
                    <div class="col-sm-3">
                        <span class="text-muted small d-block">Account Number / IBAN:</span>
                        <strong class="font-monospace text-dark">{{ $wallet->payout_account_number }}</strong>
                    </div>
                    <div class="col-sm-3 text-sm-end">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5"><i class="las la-check-circle"></i> Bank Active</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('streamer.withdraw') }}" method="POST" class="row g-3 align-items-center">
                @csrf
                <div class="col-sm-6 col-md-4">
                    <label class="form-label">Withdrawal Amount ($ USD)</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input 
                            type="number" 
                            name="amount" 
                            min="20" 
                            max="{{ $wallet->available_balance }}" 
                            step="0.01" 
                            value="{{ old('amount', $wallet->available_balance >= 20 ? $wallet->available_balance : 20) }}" 
                            class="form-control font-monospace fw-bold" 
                            required
                        >
                    </div>
                    <small class="text-muted">Minimum withdrawal: $20.00 USD</small>
                </div>
                <div class="col-sm-6 col-md-4 mt-sm-auto">
                    <button type="submit" class="btn btn-streamer text-white fw-bold w-100" {{ $wallet->available_balance < 20 ? 'disabled' : '' }}>
                        <i class="las la-arrow-circle-right me-1"></i> Request Bank Payout
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

<!-- Withdrawals History Table -->
<div class="card shadow-sm border">
    <div class="card-header bg-transparent border-bottom py-3">
        <h6 class="mb-0 fw-bold text-dark"><i class="las la-history text-danger me-2"></i> Bank Payout Ledger</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Destination Bank Details</th>
                        <th>Status</th>
                        <th>Requested Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr>
                            <td><span class="font-monospace small fw-bold">#WD-{{ $w->id }}</span></td>
                            <td><span class="font-monospace fw-bold text-danger">${{ number_format($w->amount, 2) }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $w->method }}</span></td>
                            <td><small class="font-monospace text-muted">{{ $w->destination_reference }}</small></td>
                            <td>
                                @if($w->status === 'completed')
                                    <span class="badge-status badge-approved"><i class="las la-check"></i> TRANSFERRED</span>
                                @elseif($w->status === 'rejected')
                                    <span class="badge-status badge-rejected"><i class="las la-times"></i> REJECTED</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="las la-clock"></i> PROCESSING</span>
                                @endif
                            </td>
                            <td><span class="small text-muted">{{ $w->created_at->format('M d, Y h:i A') }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No withdrawal history recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">
    {{ $withdrawals->links() }}
</div>
@endsection
