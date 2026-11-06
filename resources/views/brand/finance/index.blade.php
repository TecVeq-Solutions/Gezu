@extends('brand.layouts.app')

@section('panel')
<!-- Financial Health Cards (4 Equal Stat Cards) -->
<div class="row g-3 g-xl-4 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Total Allocated Budget</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 24px;">${{ number_format($totalBudgetAllocated, 2) }}</h3>
                <small class="text-muted" style="font-size: 11.5px;">Allocated across campaigns</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <i class="las la-lock"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Distributed to Creators</p>
                <h3 class="fw-bold mb-0 text-success font-monospace" style="font-size: 24px;">${{ number_format($spentOnCreators, 2) }}</h3>
                <small class="text-success" style="font-size: 11.5px;">Settled verified bounties</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="las la-hand-holding-usd"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Remaining Campaign Pools</p>
                <h3 class="fw-bold mb-0 text-primary font-monospace" style="font-size: 24px;">${{ number_format($remainingBudget, 2) }}</h3>
                <small class="text-primary" style="font-size: 11.5px;">Active reward balance</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                <i class="las la-coins"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card">
            <div>
                <p class="text-uppercase text-muted fw-bold mb-1" style="font-size: 11px; letter-spacing: 0.6px;">Brand Wallet Balance</p>
                <h3 class="fw-bold mb-0 text-dark font-monospace" style="font-size: 24px;">${{ number_format($wallet->available_balance, 2) }}</h3>
                <small class="text-muted" style="font-size: 11.5px;">Refunded unspent credits</small>
            </div>
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <i class="las la-wallet"></i>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Ledger -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark">
                        <i class="las la-history text-primary me-1"></i> Brand Wallet & Transaction Ledger
                    </h5>
                    <small class="text-muted">Itemized audit history of campaign deposits, payouts, and unspent budget refunds.</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Transaction Type</th>
                                <th>Amount ($)</th>
                                <th>Status</th>
                                <th>Reference Identifier</th>
                                <th>Description</th>
                                <th class="text-end">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-primary border text-uppercase">
                                            {{ str_replace('_', ' ', $trx->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($trx->type, ['deposit', 'campaign_refund']))
                                            <span class="fw-bold font-monospace text-success fs-6">+${{ number_format($trx->amount, 2) }}</span>
                                        @else
                                            <span class="fw-bold font-monospace text-danger fs-6">-${{ number_format($trx->amount, 2) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge--success text-uppercase">{{ $trx->status }}</span>
                                    </td>
                                    <td>
                                        <code class="text-muted small bg-light p-1 rounded font-monospace">{{ $trx->reference_id }}</code>
                                    </td>
                                    <td>
                                        <span class="text-dark small">{{ $trx->description }}</span>
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $trx->created_at->format('M d, Y H:i:s') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="las la-exchange-alt fs-1 d-block mb-2 text-muted"></i>
                                        No financial transactions recorded on this brand wallet yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
