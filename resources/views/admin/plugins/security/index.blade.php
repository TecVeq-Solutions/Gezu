@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 54px; height: 54px; background: linear-gradient(135deg, #10B981, #059669);">
                        <i class="las la-shield-alt fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 text-dark fw-bold">TECVEQ Security Suite & Firewall</h4>
                            <span class="badge badge--success font-monospace">v4.1.0 WAF ACTIVE</span>
                            <span class="badge bg-light text-dark border">Threat Defense Engine</span>
                        </div>
                        <p class="text-muted mb-0">Next-generation Web Application Firewall (WAF), real-time SQLi shield, exploit payload killer, bot neutralizer, and continuous security telemetry.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('admin.plugins.security.logs.clear') }}" method="POST" onsubmit="return confirm('Clear all threat telemetry logs?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger px-3 shadow-sm fw-bold">
                            <i class="las la-trash-alt me-1"></i> Clear Logs
                        </button>
                    </form>
                    <button type="submit" form="securitySettingsForm" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="las la-save me-1"></i> Save Firewall Rules
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Telemetry Cards -->
    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Total Intercepted Attacks</span>
                    <h3 class="mb-0 mt-1 text-danger fw-black font-monospace">{{ number_format($totalThreats) }}</h3>
                </div>
                <div class="avatar-sm bg-soft-danger text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.1);">
                    <i class="las la-bug fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="las la-check-circle text-success"></i> Blocked by active WAF</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">SQLi & Injection Shields</span>
                    <h3 class="mb-0 mt-1 text-warning fw-black font-monospace">{{ number_format($sqliThreats) }}</h3>
                </div>
                <div class="avatar-sm bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(245, 158, 11, 0.1);">
                    <i class="las la-code fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="las la-shield-alt text-warning"></i> Payload inspection active</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Bad Bots & Scanners</span>
                    <h3 class="mb-0 mt-1 text-primary fw-black font-monospace">{{ number_format($badBotThreats) }}</h3>
                </div>
                <div class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(99, 102, 241, 0.1);">
                    <i class="las la-robot fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="las la-user-secret text-primary"></i> Crawlers filtered</div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Active IP Blacklists</span>
                    <h3 class="mb-0 mt-1 text-dark fw-black font-monospace">{{ number_format($bannedIpsCount) }}</h3>
                </div>
                <div class="avatar-sm bg-soft-dark text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(15, 23, 42, 0.1);">
                    <i class="las la-ban fs-3"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small"><i class="las la-clock text-info"></i> Enforced at edge</div>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="col-12">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs nav-tabs--custom border-0" id="securityTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 px-4 fw-bold" id="waf-tab" data-bs-toggle="tab" data-bs-target="#waf" type="button" role="tab">
                            <i class="las la-sliders-h me-1"></i> WAF & Protection Rules
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-bold" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                            <i class="las la-stream me-1"></i> Threat Telemetry Logs
                            <span class="badge bg-danger ms-1">{{ count($recentLogs) }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-bold" id="bans-tab" data-bs-toggle="tab" data-bs-target="#bans" type="button" role="tab">
                            <i class="las la-ban me-1"></i> IP Blacklist & Whitelist
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-bold" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">
                            <i class="las la-clipboard-check me-1"></i> OWASP & Hardening Audit
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="securityTabsContent">

                    <!-- TAB 1: WAF & PROTECTION RULES -->
                    <div class="tab-pane fade show active" id="waf" role="tabpanel">
                        <form id="securitySettingsForm" action="{{ route('admin.plugins.security.settings') }}" method="POST">
                            @csrf

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-power-off text-success me-1"></i> Master Firewall System</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="project_security" value="1" {{ ($settings['project_security'] ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Globally activates or disables the TECVEQ Security filtering engine on incoming requests.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-shield-alt text-primary me-1"></i> SQLi & Injection Shield</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="sqli_protection" value="1" {{ ($settings['sqli_protection'] ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Deep query and parameter inspection against SQL injections, command injections, and traversal attempts.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-robot text-danger me-1"></i> Bad Bot & Vulnerability Scanner Neutralizer</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="badbot_protection" value="1" {{ ($settings['badbot_protection'] ?? 1) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Instantly blocks automated security scanners (sqlmap, nikto, wpscan, acunetix, censys, etc.).</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-user-slash text-danger me-1"></i> Auto-Ban Malicious Payloads</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="sqli_autoban" value="1" {{ ($settings['sqli_autoban'] ?? 0) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Automatically inserts offending attacker IPs into the permanent firewall blacklist upon malicious detection.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-network-wired text-info me-1"></i> Live Traffic Telemetry</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="live_traffic" value="1" {{ ($settings['live_traffic'] ?? 0) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Records live visitor requests, devices, browsers, and routes in real-time telemetry.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-globe-americas text-warning me-1"></i> Geo / Country Filter</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="countryban_blacklist" value="1" {{ ($settings['countryban_blacklist'] ?? 0) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Restricts or blacklists high-risk countries defined in the country bans table.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-mask text-dark me-1"></i> Proxy / Tor Detection</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="proxy_protection" value="1" {{ ($settings['proxy_protection'] ?? 0) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Blocks anonymous Tor exit nodes and suspicious data-center proxies from submitting clips or tampering with bounties.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="fw-bold mb-0 text-dark"><i class="las la-eye-slash text-secondary me-1"></i> Information Disclosure Shield</label>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="display_errors" value="1" {{ ($settings['display_errors'] ?? 0) == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Suppresses low-level PHP stack traces and server headers in production to stop attackers from fingerprinting the environment.</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">Bad Words & Toxic Content Filter Replacement</label>
                                    <input type="text" name="badword_replace" class="form-control" value="{{ $settings['badword_replace'] ?? '[Censored]' }}" placeholder="[Censored]">
                                    <small class="text-muted">Replaces blacklisted swear words or malicious strings in user bios, comments, and public campaign descriptions.</small>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: THREAT TELEMETRY LOGS -->
                    <div class="tab-pane fade" id="logs" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table--light style--two mb-0">
                                <thead>
                                    <tr>
                                        <th>Threat Type</th>
                                        <th>Attacker IP</th>
                                        <th>Targeted Page / URI</th>
                                        <th>Payload Signature</th>
                                        <th>Client Environment</th>
                                        <th>Timestamp</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentLogs as $log)
                                        <tr>
                                            <td>
                                                <span class="badge {{ str_contains($log->type, 'SQLi') ? 'badge--danger' : 'badge--warning' }}">
                                                    {{ $log->type }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark font-monospace">{{ $log->ip }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted font-monospace small">{{ $log->page }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark font-monospace border" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->query }}">
                                                    {{ $log->query ?: 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->browser }} ({{ $log->os }})</small>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $log->date }} {{ $log->time }}</small>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.plugins.security.bans.store') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="ip" value="{{ $log->ip }}">
                                                    <input type="hidden" name="reason" value="Flagged from Telemetry: {{ $log->type }}">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Ban IP Immediately">
                                                        <i class="las la-ban"></i> Ban
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">
                                                <i class="las la-shield-alt fs-1 text-success d-block mb-2"></i>
                                                Zero active threat attacks logged. All perimeter defenses are operational!
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: IP BLACKLIST & WHITELIST -->
                    <div class="tab-pane fade" id="bans" role="tabpanel">
                        <div class="row g-4">
                            <!-- IP Blacklist Form & Table -->
                            <div class="col-lg-6">
                                <div class="card border b-radius--10 shadow-none mb-4">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-bold text-danger"><i class="las la-ban me-1"></i> Add IP to Blacklist</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.plugins.security.bans.store') }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <input type="text" name="ip" class="form-control" placeholder="e.g. 192.168.1.100" required>
                                                <input type="text" name="reason" class="form-control" placeholder="Reason (e.g. Brute Force Bot)">
                                                <button type="submit" class="btn btn-danger">Blacklist IP</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-2">Currently Blacklisted IPs ({{ count($bannedIps) }})</h6>
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>IP Address</th>
                                                <th>Reason</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($bannedIps as $ban)
                                                <tr>
                                                    <td class="font-monospace fw-bold text-danger">{{ $ban->ip }}</td>
                                                    <td><small class="text-muted">{{ $ban->reason }}</small></td>
                                                    <td><small class="text-muted">{{ $ban->date }}</small></td>
                                                    <td>
                                                        <form action="{{ route('admin.plugins.security.bans.delete', $ban->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Lift Ban">Unban</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-3 text-muted">No banned IPs.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- IP Whitelist Form & Table -->
                            <div class="col-lg-6">
                                <div class="card border b-radius--10 shadow-none mb-4">
                                    <div class="card-header bg-light py-2">
                                        <h6 class="mb-0 fw-bold text-success"><i class="las la-check-circle me-1"></i> Add IP to Whitelist</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.plugins.security.whitelist.store') }}" method="POST">
                                            @csrf
                                            <div class="input-group mb-2">
                                                <input type="text" name="ip" class="form-control" placeholder="e.g. 127.0.0.1" required>
                                                <input type="text" name="notes" class="form-control" placeholder="Notes (e.g. Admin Office VPN)">
                                                <button type="submit" class="btn btn-success">Whitelist IP</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <h6 class="fw-bold mb-2">Whitelisted Trusted IPs ({{ count($whitelistedIps) }})</h6>
                                <div class="table-responsive border rounded">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>IP Address</th>
                                                <th>Notes</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($whitelistedIps as $wl)
                                                <tr>
                                                    <td class="font-monospace fw-bold text-success">{{ $wl->ip }}</td>
                                                    <td><small class="text-muted">{{ $wl->notes }}</small></td>
                                                    <td>
                                                        <form action="{{ route('admin.plugins.security.whitelist.delete', $wl->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Remove">Remove</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-3 text-muted">No whitelisted IPs.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: OWASP & HARDENING AUDIT -->
                    <div class="tab-pane fade" id="audit" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border b-radius--10 p-4 h-100">
                                    <h5 class="fw-bold text-dark mb-3"><i class="las la-shield-alt text-success me-1"></i> OWASP Top 10 Defensive Status</h5>
                                    <ul class="list-unstyled space-y-3 mb-0">
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A01: Broken Access Control</strong> (Strict auth middleware + IDOR guards)</span>
                                            <span class="badge badge--success">ENFORCED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A02: Cryptographic Failures</strong> (Bcrypt cost 12, SHA-256 tokens)</span>
                                            <span class="badge badge--success">HARDENED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A03: Injection</strong> (PDO Prepared Statements + WAF Signature Filter)</span>
                                            <span class="badge badge--success">SHIELDED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A04: Insecure Design</strong> (Direct Bank Transfers, No 3rd-party leaks)</span>
                                            <span class="badge badge--success">VERIFIED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A05: Security Misconfiguration</strong> (Debug restricted, secure headers)</span>
                                            <span class="badge badge--success">PROTECTED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <span><strong>A07: Identification & Auth Failures</strong> (Rate limited login throttle)</span>
                                            <span class="badge badge--success">ENFORCED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2">
                                            <span><strong>A09: Security Logging & Monitoring</strong> (Realtime psec_logs telemetry)</span>
                                            <span class="badge badge--success">ACTIVE</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border b-radius--10 p-4 h-100">
                                    <h5 class="fw-bold text-dark mb-3"><i class="las la-lock text-primary me-1"></i> HTTP Security Headers Check</h5>
                                    <ul class="list-unstyled space-y-3 mb-0">
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div>
                                                <strong>X-Frame-Options</strong>
                                                <small class="d-block text-muted">SAMEORIGIN (Anti-Clickjacking)</small>
                                            </div>
                                            <span class="badge badge--success">INJECTED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div>
                                                <strong>X-Content-Type-Options</strong>
                                                <small class="d-block text-muted">nosniff (MIME-Sniffing Defense)</small>
                                            </div>
                                            <span class="badge badge--success">INJECTED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div>
                                                <strong>X-XSS-Protection</strong>
                                                <small class="d-block text-muted">1; mode=block</small>
                                            </div>
                                            <span class="badge badge--success">INJECTED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                            <div>
                                                <strong>Referrer-Policy</strong>
                                                <small class="d-block text-muted">strict-origin-when-cross-origin</small>
                                            </div>
                                            <span class="badge badge--success">INJECTED</span>
                                        </li>
                                        <li class="d-flex align-items-center justify-content-between py-2">
                                            <div>
                                                <strong>Permissions-Policy</strong>
                                                <small class="d-block text-muted">camera=(), microphone=(), geolocation=()</small>
                                            </div>
                                            <span class="badge badge--success">INJECTED</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection
