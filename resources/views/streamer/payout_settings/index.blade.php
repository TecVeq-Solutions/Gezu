@extends('streamer.layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="las la-university text-danger me-2"></i> Direct Bank Transfer Payout Settings
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Enter your real bank account details. When your submitted YouTube videos are approved by brands and verified, your earnings will be transferred directly to this bank account upon withdrawal request.
                </p>

                <form action="{{ route('streamer.payout-settings') }}" method="POST">
                    @csrf

                    <!-- Bank Name -->
                    <div class="mb-3">
                        <label class="form-label">Bank Name / Financial Institution <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="payout_bank_name" 
                            value="{{ old('payout_bank_name', $wallet->payout_bank_name) }}" 
                            placeholder="e.g. Chase Bank, Barclays, Meezan Bank, HBL, Bank of America" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <!-- Account Holder Name -->
                    <div class="mb-3">
                        <label class="form-label">Account Holder Full Legal Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="payout_account_holder" 
                            value="{{ old('payout_account_holder', $wallet->payout_account_holder ?? $user->name) }}" 
                            placeholder="Exact name as appears on your bank statement" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <!-- Account Number / IBAN -->
                    <div class="mb-3">
                        <label class="form-label">Account Number / IBAN / Routing <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="payout_account_number" 
                            value="{{ old('payout_account_number', $wallet->payout_account_number) }}" 
                            placeholder="e.g. PK36MEZN0001234567890123 or Account # / Routing #" 
                            class="form-control font-monospace" 
                            required
                        >
                    </div>

                    <!-- Contact Phone -->
                    <div class="mb-4">
                        <label class="form-label">Contact Phone / WhatsApp (For Bank Wire Confirmation)</label>
                        <input 
                            type="text" 
                            name="payout_phone_number" 
                            value="{{ old('payout_phone_number', $wallet->payout_phone_number) }}" 
                            placeholder="+1 234 567 8900" 
                            class="form-control"
                        >
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('streamer.wallet') }}" class="btn btn-outline-secondary">
                            &larr; Return to Wallet
                        </a>
                        <button type="submit" class="btn btn-streamer text-white fw-bold px-4">
                            <i class="las la-save me-1"></i> Save Bank Details
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
