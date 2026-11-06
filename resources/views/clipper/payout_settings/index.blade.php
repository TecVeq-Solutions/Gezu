@extends('clipper.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <!-- Direct Bank Payout Form Card -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-university text-success fs-4"></i> Direct Bank Payout Settings
                    </h5>
                    <small class="text-muted">Manage your official bank wire account details for direct bounty earnings transfer.</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold text-uppercase px-2.5 py-1.5" style="font-size: 11px;">
                    <i class="las la-shield-alt me-1"></i> Direct Wire / Transfer
                </span>
            </div>
            <div class="card-body p-4">
                <!-- Info Alert -->
                <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 p-3 rounded-3 mb-4 bg-info-subtle text-dark">
                    <i class="las la-info-circle fs-3 text-info flex-shrink-0 mt-1"></i>
                    <div class="small">
                        <strong class="d-block mb-1">Direct Bank Wire Payouts:</strong>
                        All approved clip bounty earnings and manual wallet withdrawals are processed by the platform Administrator via direct bank transfer. Ensure your account number and holder name match your official bank statement.
                    </div>
                </div>

                <form method="POST" action="{{ route('clipper.payout-settings') }}">
                    @csrf

                    <div class="row g-3">
                        <!-- Bank Name -->
                        <div class="col-md-6">
                            <label class="form-label">Bank / Financial Institution Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-university"></i></span>
                                <input type="text" name="payout_bank_name" value="{{ old('payout_bank_name', $wallet->payout_bank_name) }}" class="form-control" placeholder="e.g. Chase, Revolut, Meezan Bank, HBL" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">The official name of your bank.</small>
                        </div>

                        <!-- Account Holder Name -->
                        <div class="col-md-6">
                            <label class="form-label">Account Holder Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-user-check"></i></span>
                                <input type="text" name="payout_account_holder" value="{{ old('payout_account_holder', $wallet->payout_account_holder ?? $user->name) }}" class="form-control" placeholder="e.g. Alex Rivers" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Must match the legal name on your bank account.</small>
                        </div>

                        <!-- Account Number / IBAN -->
                        <div class="col-md-6">
                            <label class="form-label">Account Number or IBAN <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-credit-card"></i></span>
                                <input type="text" name="payout_account_number" value="{{ old('payout_account_number', $wallet->payout_account_number) }}" class="form-control font-monospace" placeholder="e.g. PK36MEZN000123456789 or 123456789" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Your domestic account number or international IBAN.</small>
                        </div>

                        <!-- Phone / WhatsApp Number -->
                        <div class="col-md-6">
                            <label class="form-label">Contact Phone / WhatsApp <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-phone"></i></span>
                                <input type="text" name="payout_phone_number" value="{{ old('payout_phone_number', $wallet->payout_phone_number) }}" class="form-control" placeholder="e.g. +1 555 123 4567" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">For bank transfer confirmation and transaction receipts.</small>
                        </div>

                        <!-- Swift / Routing / Branch Code -->
                        <div class="col-12">
                            <label class="form-label">SWIFT / BIC / Routing Code (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-globe"></i></span>
                                <input type="text" name="payout_routing_swift" value="{{ old('payout_routing_swift', $wallet->payout_routing_swift) }}" class="form-control font-monospace" placeholder="e.g. MEZNUS33, 021000021">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Required only for international wires or specific domestic clearing systems.</small>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="text-muted small"><i class="las la-lock text-success me-1"></i> Encrypted &amp; secured on GEZUX</span>
                        <button type="submit" class="btn btn-creator px-4" style="height: 44px;">
                            <i class="las la-save me-1"></i> Save Bank Payout Details
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Workflow Explanation Card -->
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="las la-exchange-alt text-primary fs-5"></i> How Creator Direct Bank Payouts Work
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-sm-4 text-center">
                        <div class="rounded-circle bg-light text-primary mx-auto d-flex align-items-center justify-content-center mb-2 border" style="width: 52px; height: 52px;">
                            <i class="las la-cloud-upload-alt fs-3"></i>
                        </div>
                        <h6 class="fw-bold fs-6 mb-1 text-dark">1. Submit Clip</h6>
                        <small class="text-muted">Submit your published TikTok, Shorts, or Reels video link.</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="rounded-circle bg-light text-warning mx-auto d-flex align-items-center justify-content-center mb-2 border" style="width: 52px; height: 52px;">
                            <i class="las la-check-circle fs-3"></i>
                        </div>
                        <h6 class="fw-bold fs-6 mb-1 text-dark">2. Brand Review</h6>
                        <small class="text-muted">The brand validates views and approves the submission reward.</small>
                    </div>
                    <div class="col-sm-4 text-center">
                        <div class="rounded-circle bg-light text-success mx-auto d-flex align-items-center justify-content-center mb-2 border" style="width: 52px; height: 52px;">
                            <i class="las la-university fs-3"></i>
                        </div>
                        <h6 class="fw-bold fs-6 mb-1 text-dark">3. Direct Wire</h6>
                        <small class="text-muted">Admin wires the verified payout straight to your bank account.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
