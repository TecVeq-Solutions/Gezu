@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        
        <!-- Filter Header -->
        <div class="card b-radius--10 mb-4 border">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.payouts') }}" class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}">All Payouts</a>
                        <a href="{{ route('admin.payouts', ['status' => 'PENDING_ADMIN_REVIEW']) }}" class="btn btn-sm {{ $status === 'PENDING_ADMIN_REVIEW' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                            <i class="las la-clock"></i> Pending Wire Transfers
                        </a>
                        <a href="{{ route('admin.payouts', ['status' => 'COMPLETED']) }}" class="btn btn-sm {{ $status === 'COMPLETED' ? 'btn-success text-white' : 'btn-outline-success' }}">
                            <i class="las la-check-circle"></i> Completed
                        </a>
                        <a href="{{ route('admin.payouts', ['status' => 'REJECTED']) }}" class="btn btn-sm {{ $status === 'REJECTED' ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">
                            <i class="las la-times-circle"></i> Rejected
                        </a>
                    </div>
                    <div>
                        <span class="text-muted small"><i class="las la-university"></i> Direct Bank Transfer Protocol</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payouts Table -->
        <div class="card b-radius--10 border">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>Clipper / Creator</th>
                                <th>Campaign / Bounty</th>
                                <th>Clipper Direct Bank Details</th>
                                <th>Gross Requested</th>
                                <th>Commission</th>
                                <th>Net Payable</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payouts as $payout)
                                @php
                                    $wallet = $payout->user?->wallet;
                                    $bankName = $wallet?->payout_bank_name;
                                    $accHolder = $wallet?->payout_account_holder;
                                    $accNum = $wallet?->payout_account_number;
                                    $phone = $wallet?->payout_phone_number;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px; background: rgba(99, 102, 241, 0.1);">
                                                {{ strtoupper(substr($payout->user->name ?? 'C', 0, 2)) }}
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.users.detail', $payout->clipper_id) }}" class="fw-bold text-dark d-block text-decoration-none">
                                                    {{ $payout->user->name ?? 'Creator' }}
                                                </a>
                                                <small class="text-muted">{{ $payout->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate fw-semibold" style="max-width: 130px;" title="{{ $payout->campaign->title ?? 'Direct Bounty Payout' }}">
                                            {{ $payout->campaign->title ?? 'Bounty Payout' }}
                                        </span>
                                        <small class="d-block text-muted">{{ $payout->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="small p-2.5 rounded bg-light border text-dark" style="min-width: 240px;">
                                            @if($bankName)
                                                <div class="fw-bold text-primary"><i class="las la-university"></i> {{ $bankName }}</div>
                                                <div><span class="text-muted">A/C:</span> <strong class="font-monospace text-dark">{{ $accNum }}</strong></div>
                                                <div><span class="text-muted">Title:</span> {{ $accHolder }}</div>
                                                @if($phone)
                                                    <div><span class="text-muted">Phone:</span> {{ $phone }}</div>
                                                @endif
                                            @else
                                                <div class="text-muted font-monospace" style="word-break: break-all;">
                                                    {{ $payout->destination_account ?? 'Direct Bank Transfer' }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-dark fs-6">${{ number_format($payout->gross_earning, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-danger">-${{ number_format($payout->commission_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        <span class="font-monospace fw-bold text-success fs-6">${{ number_format($payout->net_payout, 2) }}</span>
                                    </td>
                                    <td>
                                        {!! showStatusBadge($payout->status) !!}
                                    </td>
                                    <td>
                                        @if($payout->status === 'PENDING_ADMIN_REVIEW' || $payout->status === 'pending')
                                            <div class="d-flex align-items-center gap-2">
                                                <!-- Process Wire Transfer Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#processPayoutModal{{ $payout->id }}" title="Process Bank Wire & Deduct Commission">
                                                    <i class="las la-money-check-alt"></i> Process Payout
                                                </button>

                                                <!-- Reject Modal Trigger -->
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectPayoutModal{{ $payout->id }}" title="Reject Request">
                                                    <i class="las la-times"></i>
                                                </button>
                                            </div>

                                            <!-- Modal: Process Wire Transfer with Dynamic Commission Deduction -->
                                            <div class="modal fade" id="processPayoutModal{{ $payout->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.payouts.complete', $payout->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h6 class="modal-title fw-bold text-white mb-0">
                                                                    <i class="las la-money-check-alt me-1"></i> Direct Bank Wire Settlement #{{ $payout->id }}
                                                                </h6>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4 text-start">
                                                                <!-- Beneficiary Bank Info Box -->
                                                                <div class="p-3 bg-light rounded border mb-3">
                                                                    <div class="text-uppercase fw-bold text-muted small mb-2"><i class="las la-user-check text-success"></i> Beneficiary Bank Details</div>
                                                                    <div class="row gy-2 small">
                                                                        <div class="col-6">
                                                                            <span class="text-muted d-block">Bank Name:</span>
                                                                            <strong class="text-dark">{{ $bankName ?: 'Not specified' }}</strong>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <span class="text-muted d-block">Account Title:</span>
                                                                            <strong class="text-dark">{{ $accHolder ?: ($payout->user->name ?? 'N/A') }}</strong>
                                                                        </div>
                                                                        <div class="col-12">
                                                                            <span class="text-muted d-block">Account / IBAN Number:</span>
                                                                            <strong class="font-monospace text-primary fs-6">{{ $accNum ?: ($payout->destination_account ?? 'N/A') }}</strong>
                                                                        </div>
                                                                        @if($phone)
                                                                            <div class="col-6">
                                                                                <span class="text-muted d-block">WhatsApp/Phone:</span>
                                                                                <strong class="text-dark">{{ $phone }}</strong>
                                                                            </div>
                                                                        @endif
                                                                        @if($wallet?->payout_routing_swift)
                                                                            <div class="col-6">
                                                                                <span class="text-muted d-block">Swift Code:</span>
                                                                                <strong class="font-monospace text-dark">{{ $wallet->payout_routing_swift }}</strong>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <!-- Financial Calculation Block -->
                                                                <div class="row gy-3">
                                                                    <div class="col-sm-6">
                                                                        <label class="form-label small fw-bold">Gross Requested Earning ($)</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text">$</span>
                                                                            <input type="text" id="gross_{{ $payout->id }}" class="form-control font-monospace bg-light fw-bold" value="{{ number_format($payout->gross_earning, 2, '.', '') }}" readonly>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-6">
                                                                        <label class="form-label small fw-bold">Platform Commission Deduction ($)</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text">$</span>
                                                                            <input type="number" step="0.01" min="0" max="{{ $payout->gross_earning }}" name="commission_amount" id="comm_{{ $payout->id }}" class="form-control font-monospace text-danger fw-bold" value="{{ number_format($payout->commission_amount, 2, '.', '') }}" oninput="recalcNet({{ $payout->id }})" required>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <div class="p-3 bg-soft-success rounded border border-success d-flex align-items-center justify-content-between" style="background: rgba(16, 185, 129, 0.08);">
                                                                            <div>
                                                                                <span class="text-uppercase fw-bold text-success small d-block">Net Bank Wire Transfer Amount</span>
                                                                                <small class="text-muted">Exact sum to wire to creator's bank account</small>
                                                                            </div>
                                                                            <h3 class="mb-0 fw-bold font-monospace text-success">$<span id="net_display_{{ $payout->id }}">{{ number_format($payout->net_payout, 2, '.', '') }}</span></h3>
                                                                            <input type="hidden" name="net_payout" id="net_input_{{ $payout->id }}" value="{{ number_format($payout->net_payout, 2, '.', '') }}">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <label class="form-label small fw-bold">Bank Wire Transaction # / Ref ID <span class="text-danger">*</span></label>
                                                                        <input type="text" name="provider_reference" class="form-control font-monospace" placeholder="e.g. WIRE-PK-984210 or Bank Slip TRX#" required>
                                                                    </div>

                                                                    <div class="col-12">
                                                                        <label class="form-label small fw-bold">Admin Transfer Note</label>
                                                                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="e.g. Transferred via Standard Chartered Internet Banking, 20% platform commission retained."></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-sm btn-success px-3">
                                                                    <i class="las la-check-circle"></i> Confirm Bank Transfer & Settle
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectPayoutModal{{ $payout->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('admin.payouts.reject', $payout->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-danger text-white">
                                                                <h6 class="modal-title fw-bold text-white mb-0">Reject Payout Request</h6>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4 text-start">
                                                                <label class="form-label small fw-bold">Reason for Rejection <span class="text-danger">*</span></label>
                                                                <textarea name="reason" class="form-control" rows="3" required placeholder="E.g. Bank account details provided are invalid or account title mismatch..."></textarea>
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
                                            <div class="text-success small fw-bold">
                                                <i class="las la-check-circle"></i> Settled
                                                @if($payout->provider_reference)
                                                    <div class="font-monospace text-muted small">{{ $payout->provider_reference }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="las la-money-check-alt fs-1 d-block mb-2 text-muted"></i>
                                        No clipper payout requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($payouts->hasPages())
                <div class="card-footer py-3 bg-white">
                    {{ $payouts->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<script>
function recalcNet(id) {
    const grossEl = document.getElementById('gross_' + id);
    const commEl = document.getElementById('comm_' + id);
    const netDisplay = document.getElementById('net_display_' + id);
    const netInput = document.getElementById('net_input_' + id);

    if (grossEl && commEl && netDisplay && netInput) {
        const gross = parseFloat(grossEl.value) || 0;
        const comm = parseFloat(commEl.value) || 0;
        const net = Math.max(0, gross - comm);

        netDisplay.innerText = net.toFixed(2);
        netInput.value = net.toFixed(2);
    }
}
</script>
@endsection
