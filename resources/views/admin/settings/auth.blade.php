@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark"><i class="las la-shield-alt text-primary me-1"></i> Authentication, Verification & Security Policies</h5>
                    <p class="text-muted small mb-0">Configure OTP verification codes format, email domain policies, Google OAuth, and session security.</p>
                </div>
                <div>
                    <button type="submit" form="authSettingsForm" class="btn btn-primary px-4 fw-bold">
                        <i class="las la-save me-1"></i> Save All Policies
                    </button>
                </div>
            </div>

            <div class="card-body p-4">
                <form id="authSettingsForm" action="{{ route('admin.settings.auth') }}" method="POST">
                    @csrf

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-2.5" id="otp-tab" data-bs-toggle="tab" data-bs-target="#otp-pane" type="button" role="tab">
                                <i class="las la-sms me-1 fs-5"></i> Verification Codes (OTP)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2.5" id="domain-tab" data-bs-toggle="tab" data-bs-target="#domain-pane" type="button" role="tab">
                                <i class="las la-filter me-1 fs-5"></i> Email Domain Policy & Shield
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2.5" id="google-tab" data-bs-toggle="tab" data-bs-target="#google-pane" type="button" role="tab">
                                <i class="lab la-google me-1 fs-5"></i> Google OAuth Gateway
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2.5" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab">
                                <i class="las la-lock me-1 fs-5"></i> General Security & Sessions
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2" id="authTabsContent">

                        <!-- TAB 1: OTP VERIFICATION CODES -->
                        <div class="tab-pane fade show active" id="otp-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Require Email Verification</h6>
                                            <p class="text-muted small mb-0">Enforce email OTP confirmation before users can access Creator or Brand portals.</p>
                                        </div>
                                        <div class="form-check form-switch form-switch-md">
                                            <input class="form-check-input" type="checkbox" name="email_verification_required" value="1" id="emailVerificationSwitch" {{ !empty($settings['email_verification_required']) ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-white py-2.5 border-bottom">
                                            <h6 class="fw-bold mb-0 text-dark small"><i class="las la-font text-primary me-1"></i> Code Character Format</h6>
                                        </div>
                                        <div class="card-body">
                                            <label class="form-label small fw-bold text-muted">Character Set</label>
                                            <select name="verification_code_type" class="form-select">
                                                <option value="numeric" {{ ($settings['verification_code_type'] ?? '') === 'numeric' ? 'selected' : '' }}>Numbers Only (0-9) - Default</option>
                                                <option value="alphanumeric" {{ ($settings['verification_code_type'] ?? '') === 'alphanumeric' ? 'selected' : '' }}>Alphanumeric (Letters + Numbers, Excludes ambiguous 0/O, 1/I/l)</option>
                                                <option value="letters" {{ ($settings['verification_code_type'] ?? '') === 'letters' ? 'selected' : '' }}>Letters Only (Uppercase A-Z)</option>
                                            </select>
                                            <div class="form-text small mt-2 text-muted">
                                                Choose whether OTP verification codes contain numbers only, uppercase letters only, or mixed alphanumeric characters.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border h-100">
                                        <div class="card-header bg-white py-2.5 border-bottom">
                                            <h6 class="fw-bold mb-0 text-dark small"><i class="las la-ruler-horizontal text-primary me-1"></i> Code Digit Length</h6>
                                        </div>
                                        <div class="card-body">
                                            <label class="form-label small fw-bold text-muted">Total Characters / Digits</label>
                                            <select name="verification_code_length" class="form-select">
                                                <option value="4" {{ ($settings['verification_code_length'] ?? 6) == 4 ? 'selected' : '' }}>4 Digits</option>
                                                <option value="5" {{ ($settings['verification_code_length'] ?? 6) == 5 ? 'selected' : '' }}>5 Digits</option>
                                                <option value="6" {{ ($settings['verification_code_length'] ?? 6) == 6 ? 'selected' : '' }}>6 Digits (Recommended Standard)</option>
                                                <option value="7" {{ ($settings['verification_code_length'] ?? 6) == 7 ? 'selected' : '' }}>7 Digits</option>
                                                <option value="8" {{ ($settings['verification_code_length'] ?? 6) == 8 ? 'selected' : '' }}>8 Digits (Maximum Security)</option>
                                            </select>
                                            <div class="form-text small mt-2 text-muted">
                                                Specifies the exact length of the generated verification code.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-dark">Code Expiry (Minutes)</label>
                                    <input type="number" name="verification_code_expiry_minutes" class="form-control" value="{{ $settings['verification_code_expiry_minutes'] ?? 10 }}" min="1" max="60" required>
                                    <small class="text-muted">Valid before expiration (e.g. 10 min).</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-dark">Max Failed Attempts</label>
                                    <input type="number" name="verification_max_attempts" class="form-control" value="{{ $settings['verification_max_attempts'] ?? 5 }}" min="1" max="10" required>
                                    <small class="text-muted">Brute force lock threshold.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-dark">Resend Cooldown (Seconds)</label>
                                    <input type="number" name="verification_resend_cooldown_seconds" class="form-control" value="{{ $settings['verification_resend_cooldown_seconds'] ?? 60 }}" min="10" max="300" required>
                                    <small class="text-muted">Delay before user can resend code.</small>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold small text-dark">Hourly Limit per Email</label>
                                    <input type="number" name="verification_hourly_limit" class="form-control" value="{{ $settings['verification_hourly_limit'] ?? 5 }}" min="1" max="50" required>
                                    <small class="text-muted">Max code requests per hour.</small>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: EMAIL DOMAIN POLICIES -->
                        <div class="tab-pane fade" id="domain-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border">
                                        <label class="form-label fw-bold text-dark mb-1">Registration Domain Policy Mode</label>
                                        <select name="email_policy_mode" class="form-select">
                                            <option value="allow_normal_with_disposable_blocklist" {{ ($settings['email_policy_mode'] ?? '') === 'allow_normal_with_disposable_blocklist' ? 'selected' : '' }}>
                                                Allow Normal Domains + Block Disposable Temporary Providers (Recommended)
                                            </option>
                                            <option value="allow_configured_only" {{ ($settings['email_policy_mode'] ?? '') === 'allow_configured_only' ? 'selected' : '' }}>
                                                Strict Whitelist Only (Only allow specified domains below e.g. gmail.com, outlook.com)
                                            </option>
                                            <option value="block_configured_only" {{ ($settings['email_policy_mode'] ?? '') === 'block_configured_only' ? 'selected' : '' }}>
                                                Block Configured Domains Only (Allow all except custom blocklist)
                                            </option>
                                        </select>
                                        <div class="form-text small mt-1 text-muted">
                                            Controls what email providers and domain extensions are accepted during user registration.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark small"><i class="las la-check-circle text-success me-1"></i> Authorized / Whitelisted Email Domains</label>
                                    <textarea name="allowed_domains" class="form-control font-monospace" rows="3" placeholder="gmail.com, outlook.com, hotmail.com, yahoo.com, icloud.com, proton.me">{{ $settings['allowed_domains'] ?? '' }}</textarea>
                                    <div class="form-text small text-muted">
                                        Comma-separated or line-separated list of allowed email domain extensions (e.g., <code>gmail.com, outlook.com, company.com</code>). When in Strict Whitelist mode, only registrations from these domains are accepted.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small"><i class="las la-user-check text-primary me-1"></i> Exact Whitelisted Emails (Bypass filters)</label>
                                    <textarea name="allowed_exact_emails" class="form-control font-monospace" rows="3" placeholder="partner@customdomain.io, vip@external.com">{{ $settings['allowed_exact_emails'] ?? '' }}</textarea>
                                    <div class="form-text small text-muted">Comma-separated exact email addresses that are always allowed regardless of domain rules.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small"><i class="las la-ban text-danger me-1"></i> Exact Blacklisted Emails (Permanent block)</label>
                                    <textarea name="blocked_exact_emails" class="form-control font-monospace" rows="3" placeholder="spammer@domain.com, fraud@abuse.org">{{ $settings['blocked_exact_emails'] ?? '' }}</textarea>
                                    <div class="form-text small text-muted">Comma-separated exact email addresses that are blocked immediately from registration.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: GOOGLE OAUTH GATEWAY -->
                        <div class="tab-pane fade" id="google-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Google Sign-In Gateway</h6>
                                            <p class="text-muted small mb-0">Allow users to log in or register instantly using their Google Account.</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            @if(!empty($googleStatus['healthy']))
                                                <span class="badge bg-success"><i class="las la-check-circle"></i> {{ $googleStatus['status'] }}</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="las la-exclamation-triangle"></i> {{ $googleStatus['status'] }}</span>
                                            @endif
                                            <div class="form-check form-switch form-switch-md">
                                                <input class="form-check-input" type="checkbox" name="google_oauth_enabled" value="1" id="googleOauthSwitch" {{ !empty($settings['google_oauth_enabled']) ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Google Client ID</label>
                                    <input type="text" name="google_client_id" class="form-control font-monospace" value="{{ $settings['google_client_id'] ?? '' }}" placeholder="xxxxxx-xxxxxx.apps.googleusercontent.com">
                                    <div class="form-text small text-muted">From Google Cloud Console &rarr; APIs & Services &rarr; Credentials</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Google Client Secret</label>
                                    <input type="password" name="google_client_secret" class="form-control font-monospace" value="{{ $settings['google_client_secret'] ?? '' }}" placeholder="••••••••••••••••">
                                    <div class="form-text small text-muted">Secret is encrypted. Enter a new value only to update it.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Authorized Redirect URI (Canonical Callback)</label>
                                    <div class="input-group">
                                        <input type="text" id="googleRedirectUriInput" class="form-control font-monospace bg-light" value="{{ $settings['google_redirect_uri'] ?? url('/auth/google/callback') }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('googleRedirectUriInput').value); alert('Copied Redirect URI!');">
                                            <i class="las la-copy"></i> Copy
                                        </button>
                                    </div>
                                    <div class="form-text small text-muted">Add this exact URI in Google Console under "Authorized redirect URIs".</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Authorized JavaScript Origins</label>
                                    <input type="text" name="google_js_origins" class="form-control font-monospace" value="{{ $settings['google_js_origins'] ?? url('/') }}" placeholder="https://example.com">
                                    <div class="form-text small text-muted">Your public domain URL.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Allowed Google Domains (Optional restriction)</label>
                                    <input type="text" name="google_allowed_domains" class="form-control" value="{{ $settings['google_allowed_domains'] ?? '' }}" placeholder="e.g. gmail.com, outlook.com">
                                    <div class="form-text small text-muted">Leave empty to allow any valid Google account, or restrict by domain.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-dark">Allowed Exact Google Emails (Optional)</label>
                                    <input type="text" name="google_allowed_exact_emails" class="form-control" value="{{ $settings['google_allowed_exact_emails'] ?? '' }}" placeholder="e.g. founder@domain.com">
                                    <div class="form-text small text-muted">Specific Google emails granted registration access.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: GENERAL SECURITY & SESSIONS -->
                        <div class="tab-pane fade" id="security-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="card border h-100 p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark small">"Remember Me" Login</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="remember_me_enabled" value="1" {{ !empty($settings['remember_me_enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Allow persistent session tokens when user selects "Remember me" on sign in.</p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border h-100 p-3">
                                        <label class="form-label fw-bold small text-dark mb-1">Minimum Password Length</label>
                                        <input type="number" name="password_min_length" class="form-control" value="{{ $settings['password_min_length'] ?? 8 }}" min="8" max="64" required>
                                        <div class="form-text small text-muted mt-1">Minimum characters required for user passwords (default 8).</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card border h-100 p-3">
                                        <label class="form-label fw-bold small text-dark mb-1">Session Lifetime (Minutes)</label>
                                        <input type="number" name="session_lifetime_minutes" class="form-control" value="{{ $settings['session_lifetime_minutes'] ?? 120 }}" min="15" max="10080" required>
                                        <div class="form-text small text-muted mt-1">Inactive duration before auto-logout (default 120 min).</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Bottom Action Bar -->
                    <div class="border-top mt-4 pt-3 d-flex align-items-center justify-content-between">
                        <small class="text-muted"><i class="las la-info-circle"></i> Changes take effect immediately across all registration and verification flows.</small>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="las la-save me-1"></i> Save All Policies
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
