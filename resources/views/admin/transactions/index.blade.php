@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        
        <!-- Search Header -->
        <div class="card b-radius--10 mb-4 border">
            <div class="card-body p-3">
                <form action="{{ route('admin.transactions') }}" method="GET" class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <span class="fw-bold small text-muted">Immutable Ledger Records</span>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm" placeholder="Search reference ID or description..." style="min-width: 250px;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="card b-radius--10 border">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>User Account</th>
                                <th>Transaction Type</th>
                                <th>Amount ($)</th>
                                <th>Status</th>
                                <th>Reference Identifier</th>
                                <th>Description</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr>
                                    <td>
                                        <span class="fw-bold d-block">{{ $trx->wallet->user->name ?? 'User' }}</span>
                                        <small class="text-muted">{{ $trx->wallet->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($trx->type === 'stripe_deposit' || $trx->type === 'campaign_funding')
                                            <span class="badge badge--primary">{{ strtoupper(str_replace('_', ' ', $trx->type)) }}</span>
                                        @elseif($trx->type === 'payout')
                                            <span class="badge badge--success">BANK PAYOUT</span>
                                        @elseif($trx->type === 'campaign_refund')
                                            <span class="badge badge--warning">REFUND</span>
                                        @else
                                            <span class="badge badge--secondary">{{ strtoupper($trx->type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold {{ $trx->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $trx->amount >= 0 ? '+' : '' }}${{ number_format($trx->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge--success">SETTLED</span>
                                    </td>
                                    <td>
                                        <span class="small font-monospace p-1 rounded bg-light border text-truncate d-inline-block" style="max-width: 220px;" title="{{ $trx->reference_id }}">
                                            {{ $trx->reference_id ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted d-inline-block text-truncate" style="max-width: 200px;">{{ $trx->description }}</small>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $trx->created_at->format('M d, Y H:i:s') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No transactions recorded in the ledger yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($transactions->hasPages())
                <div class="card-footer py-3 bg-white">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
