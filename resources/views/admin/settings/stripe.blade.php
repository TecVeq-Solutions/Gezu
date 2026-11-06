@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center gy-4">
    <div class="col-xl-9 col-lg-11">
        
        <!-- Main Gateway Configuration Card (HYIPLab Inspired) -->
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 44px; height: 44px; background: #6366f1;">
                        <i class="lab la-stripe fs-2"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Stripe Automatic Payment Gateway</h5>
                        <small class="text-muted">Standard Stripe Checkout SDK & webhook listener for instant brand deposit settlements.</small>
                    </div>
                </div>
                <div>
                    <span id="gatewayStatusBadge" class="badge {{ ($settings['mode'] ?? 'test') === 'test' ? 'bg-warning text-dark' : 'bg-success text-white' }} px-3 py-2 fw-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.05em;">
                        <i class="las la-shield-alt me-1"></i> {{ ($settings['mode'] ?? 'test') === 'test' ? 'Sandbox Mode' : 'Live Production' }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.settings.stripe') }}" method="POST" id="stripeSettingsForm">
                    @csrf

                    <!-- 1. Webhook Endpoint URL with 1-Click Copy (HYIPLab Format) -->
                    <div class="p-3 bg-light rounded-3 border mb-4">
                        <label class="form-label fw-bold text-dark small mb-1 d-flex align-items-center justify-content-between">
                            <span><i class="las la-link text-primary me-1"></i> Stripe Webhook Endpoint URL</span>
                            <span class="text-success small fw-semibold"><i class="las la-check-circle"></i> Active Endpoint</span>
                        </label>
                        <div class="input-group">
                            <input type="text" id="webhookUrlInput" class="form-control font-monospace bg-white" value="{{ url('/api/webhooks/stripe') }}" readonly style="height: 44px;">
                            <button type="button" class="btn btn-outline-primary px-3 fw-bold copy-btn" data-target="webhookUrlInput" title="Copy Webhook URL">
                                <i class="las la-copy me-1"></i> Copy URL
                            </button>
                        </div>
                        <small class="text-muted d-block mt-1.5" style="font-size: 12px;">
                            <i class="las la-info-circle text-primary"></i> Paste this into your <strong>Stripe Dashboard &rarr; Developers &rarr; Webhooks</strong> and subscribe to <code>checkout.session.completed</code>.
                        </small>
                    </div>

                    <!-- 2. Primary Gateway Parameters (HYIPLab 4-Field Standard) -->
                    <div class="row gy-3">
                        
                        <!-- Environment Mode -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="las la-toggle-on text-primary me-1"></i> Environment Mode <span class="text-danger">*</span>
                            </label>
                            <select name="stripe_mode" id="stripeModeSelect" class="form-select fw-bold" style="height: 44px;" onchange="syncStripeMode()">
                                <option value="test" {{ ($settings['mode'] ?? 'test') === 'test' ? 'selected' : '' }}>Sandbox / Test Mode (Simulated Charges)</option>
                                <option value="live" {{ ($settings['mode'] ?? 'test') === 'live' ? 'selected' : '' }}>Live Production (Real Card Billing)</option>
                            </select>
                        </div>

                        <!-- Base Currency -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="las la-money-bill-wave text-success me-1"></i> Base Currency Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="currency" class="form-control font-monospace fw-bold text-uppercase" style="height: 44px;" value="{{ $settings['currency'] ?? 'USD' }}" required placeholder="USD">
                            <small class="text-muted d-block mt-1" style="font-size: 11.5px;">Standard ISO 3-letter currency code (e.g. USD, EUR, GBP, AUD, CAD).</small>
                        </div>

                        <!-- Field 1: Publishable Key -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">
                                    <i class="las la-key text-info me-1"></i> Stripe Publishable Key <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-light text-muted border font-monospace" id="pubKeyBadge">pk_...</span>
                            </div>
                            <input type="text" name="stripe_publishable_key" id="stripePubKey" class="form-control font-monospace" style="height: 44px;" value="{{ ($settings['mode'] ?? 'test') === 'live' ? ($settings['live_publishable_key'] ?? '') : ($settings['test_publishable_key'] ?? '') }}" placeholder="pk_test_... or pk_live_..." required>
                            <small class="text-muted" style="font-size: 11.5px;">Client-side key loaded during Stripe checkout sessions.</small>
                        </div>

                        <!-- Field 2: Secret Key -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">
                                    <i class="las la-lock text-warning me-1"></i> Stripe Secret Key <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-light text-muted border font-monospace" id="secKeyBadge">sk_...</span>
                            </div>
                            <div class="input-group">
                                <input type="password" name="stripe_secret_key" id="stripeSecKey" class="form-control font-monospace" style="height: 44px;" value="{{ ($settings['mode'] ?? 'test') === 'live' ? ($settings['live_secret_key'] ?? '') : ($settings['test_secret_key'] ?? '') }}" placeholder="sk_test_... or sk_live_..." required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="stripeSecKey" title="Toggle visibility">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" style="font-size: 11.5px;">Server-side private key used to initialize payment sessions.</small>
                        </div>

                        <!-- Field 3: Webhook Signing Secret -->
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-bold text-dark small mb-0">
                                    <i class="las la-shield-alt text-success me-1"></i> Webhook Signing Secret <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-light text-muted border font-monospace">whsec_...</span>
                            </div>
                            <div class="input-group">
                                <input type="password" name="stripe_webhook_secret" id="stripeWhSecKey" class="form-control font-monospace" style="height: 44px;" value="{{ $settings['webhook_secret'] ?? '' }}" placeholder="whsec_..." required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="stripeWhSecKey" title="Toggle visibility">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted" style="font-size: 11.5px;">Cryptographic secret generated by Stripe to verify webhook integrity.</small>
                        </div>

                    </div>

                    <!-- Hidden Mode-specific Fallback Inputs to preserve both test/live keys in DB -->
                    <input type="hidden" name="stripe_test_publishable_key" id="hiddenTestPub" value="{{ $settings['test_publishable_key'] ?? '' }}">
                    <input type="hidden" name="stripe_test_secret_key" id="hiddenTestSec" value="{{ $settings['test_secret_key'] ?? '' }}">
                    <input type="hidden" name="stripe_live_publishable_key" id="hiddenLivePub" value="{{ $settings['live_publishable_key'] ?? '' }}">
                    <input type="hidden" name="stripe_live_secret_key" id="hiddenLiveSec" value="{{ $settings['live_secret_key'] ?? '' }}">

                    <div class="border-top mt-4 pt-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <small class="text-muted"><i class="las la-shield-alt text-success"></i> Credentials are encrypted in database and automatically applied to brand campaign checkout.</small>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" style="height: 44px;">
                            <i class="las la-save me-1"></i> Save Stripe Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Setup Guidance Card (HYIPLab Inspired) -->
        <div class="card b-radius--10 border shadow-sm mt-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="las la-question-circle text-info me-1"></i> Stripe Gateway Setup Checklist
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light border h-100">
                            <span class="badge bg-primary text-white mb-2">Step 1</span>
                            <h6 class="fw-bold small text-dark mb-1">Get API Credentials</h6>
                            <p class="text-muted small mb-0">Navigate to <strong>dashboard.stripe.com &rarr; Developers &rarr; API Keys</strong>. Copy your Publishable key and Secret key.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light border h-100">
                            <span class="badge bg-primary text-white mb-2">Step 2</span>
                            <h6 class="fw-bold small text-dark mb-1">Add Webhook Endpoint</h6>
                            <p class="text-muted small mb-0">In <strong>Developers &rarr; Webhooks</strong>, click <em>Add destination</em>. Paste the Webhook URL above and select event <code>checkout.session.completed</code>.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded bg-light border h-100">
                            <span class="badge bg-primary text-white mb-2">Step 3</span>
                            <h6 class="fw-bold small text-dark mb-1">Copy Signing Secret</h6>
                            <p class="text-muted small mb-0">Reveal the <strong>Signing Secret</strong> (starts with <code>whsec_</code>) from the webhook page and paste it into the field above.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('script')
<script>
    "use strict";

    function syncStripeMode() {
        const mode = document.getElementById('stripeModeSelect').value;
        const statusBadge = document.getElementById('gatewayStatusBadge');
        const pubKeyBadge = document.getElementById('pubKeyBadge');
        const secKeyBadge = document.getElementById('secKeyBadge');

        if (mode === 'live') {
            statusBadge.className = 'badge bg-success text-white px-3 py-2 fw-bold text-uppercase';
            statusBadge.innerHTML = '<i class="las la-shield-alt me-1"></i> Live Production';
            pubKeyBadge.innerText = 'pk_live_...';
            secKeyBadge.innerText = 'sk_live_...';
        } else {
            statusBadge.className = 'badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase';
            statusBadge.innerHTML = '<i class="las la-shield-alt me-1"></i> Sandbox Mode';
            pubKeyBadge.innerText = 'pk_test_...';
            secKeyBadge.innerText = 'sk_test_...';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        syncStripeMode();

        // 1-Click Copy Handler
        document.querySelectorAll('.copy-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    input.select();
                    navigator.clipboard.writeText(input.value).then(function () {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Copied!',
                                message: 'Webhook URL copied to clipboard.',
                                position: 'topRight'
                            });
                        }
                    });
                }
            });
        });

        // Toggle Password Visibility
        document.querySelectorAll('.toggle-password').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (input) {
                    const isPassword = input.type === 'password';
                    input.type = isPassword ? 'text' : 'password';
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.className = isPassword ? 'las la-eye-slash' : 'las la-eye';
                    }
                }
            });
        });

        // Form Submit: Ensure mode-specific keys are preserved
        const form = document.getElementById('stripeSettingsForm');
        if (form) {
            form.addEventListener('submit', function () {
                const mode = document.getElementById('stripeModeSelect').value;
                const pub = document.getElementById('stripePubKey').value;
                const sec = document.getElementById('stripeSecKey').value;

                if (mode === 'live') {
                    document.getElementById('hiddenLivePub').value = pub;
                    document.getElementById('hiddenLiveSec').value = sec;
                } else {
                    document.getElementById('hiddenTestPub').value = pub;
                    document.getElementById('hiddenTestSec').value = sec;
                }
            });
        }
    });
</script>
@endpush
