@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; background: linear-gradient(135deg, #10B981, #059669);">
                        <i class="las la-search fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 text-dark fw-bold">TECVEQ SEO Plugin</h4>
                            <span class="badge badge--success font-monospace">v1.0.280 ACTIVE</span>
                            <span class="badge bg-light text-dark border">Core Architecture</span>
                        </div>
                        <p class="text-muted mb-0">Search engine optimization suite with dynamic XML sitemaps, webmaster tools verification, custom code injection, 301/302 redirections, and 404 error tracking.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-outline-success">
                        <i class="las la-sitemap"></i> View XML Sitemap
                    </a>
                    <button type="submit" form="seoSettingsForm" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="las la-save me-1"></i> Save SEO Configuration
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SEO Performance Telemetry Cards -->
    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">XML SITEMAP</span>
                    <h5 class="fw-bold text-dark mt-1 mb-0">{{ ($postCount + $pageCount + 3) }} URLs Indexed</h5>
                </div>
                <div class="avatar-sm rounded bg-soft-success text-success p-2 fs-4">
                    <i class="las la-check-circle"></i>
                </div>
            </div>
            <small class="text-success mt-2 d-block"><i class="las la-link"></i> Live at <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-success fw-bold">/sitemap.xml</a></small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">GLOBAL ROBOTS</span>
                    <h5 class="fw-bold text-dark mt-1 mb-0">{{ $settings['seo_robots_index'] ? 'Index, Follow' : 'Noindex, Nofollow' }}</h5>
                </div>
                <div class="avatar-sm rounded bg-soft-primary text-primary p-2 fs-4">
                    <i class="las la-robot"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Search crawler directive</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">ACTIVE REDIRECTS</span>
                    <h5 class="fw-bold text-dark mt-1 mb-0">{{ $redirections->count() }} Rules Active</h5>
                </div>
                <div class="avatar-sm rounded bg-soft-info text-info p-2 fs-4">
                    <i class="las la-exchange-alt"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">301 / 302 Redirection engine</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">404 MONITOR</span>
                    <h5 class="fw-bold text-dark mt-1 mb-0">{{ $logs404->count() }} Logged Hits</h5>
                </div>
                <div class="avatar-sm rounded bg-soft-warning text-warning p-2 fs-4">
                    <i class="las la-exclamation-triangle"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Broken link telemetry</small>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4">

                <!-- Navigation Tabs -->
                <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="seoTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold py-2.5" id="general-tab" data-bs-toggle="tab" data-bs-target="#general-pane" type="button">
                            <i class="las la-globe me-1 fs-5"></i> General Meta & Titles
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="webmaster-tab" data-bs-toggle="tab" data-bs-target="#webmaster-pane" type="button">
                            <i class="las la-tools me-1 fs-5"></i> Webmaster Tools & Verification
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="customcode-tab" data-bs-toggle="tab" data-bs-target="#customcode-pane" type="button">
                            <i class="las la-code me-1 fs-5"></i> Custom Tags & Scripts
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="redirects-tab" data-bs-toggle="tab" data-bs-target="#redirects-pane" type="button">
                            <i class="las la-exchange-alt me-1 fs-5"></i> Redirections Manager
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="monitor404-tab" data-bs-toggle="tab" data-bs-target="#monitor404-pane" type="button">
                            <i class="las la-bug me-1 fs-5"></i> 404 Error Monitor
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold py-2.5" id="sitemap-tab" data-bs-toggle="tab" data-bs-target="#sitemap-pane" type="button">
                            <i class="las la-sitemap me-1 fs-5"></i> XML Sitemap
                        </button>
                    </li>
                </ul>

                <!-- TAB CONTENT -->
                <div class="tab-content pt-2" id="seoTabsContent">

                    <!-- TAB 1: GENERAL META -->
                    <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
                        <form id="seoSettingsForm" action="{{ route('admin.plugins.seo.settings') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Default Meta Title Template</label>
                                    <input type="text" name="seo_default_meta_title" class="form-control" value="{{ $settings['seo_default_meta_title'] }}" placeholder="%title% | %sitename%" required>
                                    <small class="text-muted mt-1 d-block">Variables supported: <code>%title%</code>, <code>%sitename%</code>, <code>%sep%</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Canonical URL Base</label>
                                    <input type="url" name="seo_default_canonical_base" class="form-control font-monospace" value="{{ $settings['seo_default_canonical_base'] }}" required>
                                    <small class="text-muted mt-1 d-block">Used to prevent duplicate content penalties across domain variants.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Global Fallback Meta Description</label>
                                    <textarea name="seo_default_meta_description" rows="3" class="form-control" placeholder="Compelling 150-160 character description of TECVEQ platform..." required>{{ $settings['seo_default_meta_description'] }}</textarea>
                                    <small class="text-muted mt-1 d-block">Displayed in Google search snippets when individual posts or pages don't provide custom descriptions.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Primary Schema Structured Data Type</label>
                                    <select name="seo_schema_type" class="form-select">
                                        <option value="Organization" @selected($settings['seo_schema_type'] === 'Organization')>Organization (Standard)</option>
                                        <option value="Corporation" @selected($settings['seo_schema_type'] === 'Corporation')>Corporation</option>
                                        <option value="WebSite" @selected($settings['seo_schema_type'] === 'WebSite')>WebSite</option>
                                        <option value="FinancialProduct" @selected($settings['seo_schema_type'] === 'FinancialProduct')>Financial Product / Marketplace</option>
                                    </select>
                                    <small class="text-muted mt-1 d-block">Root JSON-LD schema injected into public pages header.</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Breadcrumb Navigation Markup</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="seo_breadcrumbs_enabled" value="1" id="breadcrumbsSwitch" {{ $settings['seo_breadcrumbs_enabled'] ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                        <label class="form-check-label fw-semibold ms-2" for="breadcrumbsSwitch">Enable Google Rich Breadcrumbs in Search Results</label>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Twitter / X Publisher Handle</label>
                                    <input type="text" name="seo_twitter_handle" class="form-control" value="{{ $settings['seo_twitter_handle'] }}" placeholder="@tecveq">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Default Social Card Image (1200x630)</label>
                                    <input type="file" name="seo_social_image_file" class="form-control" accept="image/*">
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- TAB 2: WEBMASTER TOOLS VERIFICATION INSIDE FORM -->
                            <div class="row g-4" id="webmaster-inner">
                                <div class="col-12">
                                    <h5 class="fw-bold text-dark mb-1"><i class="las la-tools text-primary me-1"></i> Webmaster Tools Verification</h5>
                                    <p class="text-muted small">Enter verification codes or meta tokens to verify ownership with search engines and platforms.</p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="lab la-google text-danger me-1"></i> Google Search Console</label>
                                    <input type="text" name="seo_google_verification_code" class="form-control font-monospace" value="{{ $settings['seo_google_verification_code'] }}" placeholder="e.g. google-site-verification token or code">
                                    <small class="text-muted">Injected as <code>&lt;meta name="google-site-verification"&gt;</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="lab la-microsoft text-primary me-1"></i> Bing Webmaster Tools</label>
                                    <input type="text" name="seo_bing_verification_code" class="form-control font-monospace" value="{{ $settings['seo_bing_verification_code'] }}" placeholder="e.g. msvalidate.01 token">
                                    <small class="text-muted">Injected as <code>&lt;meta name="msvalidate.01"&gt;</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="las la-search text-info me-1"></i> Baidu Webmaster Tools</label>
                                    <input type="text" name="seo_baidu_verification_code" class="form-control font-monospace" value="{{ $settings['seo_baidu_verification_code'] }}" placeholder="baidu-site-verification token">
                                    <small class="text-muted">Injected as <code>&lt;meta name="baidu-site-verification"&gt;</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="las la-globe-americas text-warning me-1"></i> Yandex Webmaster Tools</label>
                                    <input type="text" name="seo_yandex_verification_code" class="form-control font-monospace" value="{{ $settings['seo_yandex_verification_code'] }}" placeholder="yandex-verification code">
                                    <small class="text-muted">Injected as <code>&lt;meta name="yandex-verification"&gt;</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="lab la-pinterest text-danger me-1"></i> Pinterest Verification</label>
                                    <input type="text" name="seo_pinterest_verification_code" class="form-control font-monospace" value="{{ $settings['seo_pinterest_verification_code'] }}" placeholder="p:domain_verify token">
                                    <small class="text-muted">Injected as <code>&lt;meta name="p:domain_verify"&gt;</code></small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark"><i class="las la-shield-alt text-success me-1"></i> Norton Safe Web Verification</label>
                                    <input type="text" name="seo_norton_verification_code" class="form-control font-monospace" value="{{ $settings['seo_norton_verification_code'] }}" placeholder="norton-safeweb verification token">
                                    <small class="text-muted">Injected as <code>&lt;meta name="norton-safeweb-site-verification"&gt;</code></small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- CUSTOM CODE & SCRIPTS INSIDE FORM -->
                            <div class="row g-4" id="customcode-inner">
                                <div class="col-12">
                                    <h5 class="fw-bold text-dark mb-1"><i class="las la-code text-primary me-1"></i> Custom Meta Tags & Global Code Injection</h5>
                                    <p class="text-muted small">Inject custom meta tags, Google Tag Manager scripts, CSS overrides, or analytics across the entire frontend.</p>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Custom &lt;head&gt; Code & Meta Tags</label>
                                    <textarea name="seo_custom_header_code" rows="4" class="form-control font-monospace" placeholder="<meta name='author' content='TECVEQ'>&#10;<script>/* Header tracking code */</script>">{{ $settings['seo_custom_header_code'] }}</textarea>
                                    <small class="text-muted">Injected right before <code>&lt;/head&gt;</code> on all frontend pages.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Custom Opening &lt;body&gt; Code (GTM noscript, etc.)</label>
                                    <textarea name="seo_custom_body_code" rows="3" class="form-control font-monospace" placeholder="<!-- Google Tag Manager (noscript) -->">{{ $settings['seo_custom_body_code'] }}</textarea>
                                    <small class="text-muted">Injected immediately after opening <code>&lt;body&gt;</code> tag.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Custom Footer Scripts</label>
                                    <textarea name="seo_custom_footer_code" rows="3" class="form-control font-monospace" placeholder="<script>/* Live chat widget or conversion tracker */</script>">{{ $settings['seo_custom_footer_code'] }}</textarea>
                                    <small class="text-muted">Injected right before closing <code>&lt;/body&gt;</code> tag.</small>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                    <i class="las la-save me-1"></i> Save SEO Configuration
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: WEBMASTER DIRECT JUMP PANE -->
                    <div class="tab-pane fade" id="webmaster-pane" role="tabpanel">
                        <div class="p-3 bg-light rounded border mb-3">
                            <p class="mb-0 text-muted">Webmaster tools fields are located directly in the main form. You can configure Google, Bing, Baidu, Yandex, Pinterest, and Norton verification tokens above.</p>
                        </div>
                    </div>

                    <!-- TAB 3: CUSTOM CODE DIRECT JUMP PANE -->
                    <div class="tab-pane fade" id="customcode-pane" role="tabpanel">
                        <div class="p-3 bg-light rounded border mb-3">
                            <p class="mb-0 text-muted">Custom Header, Body, and Footer script injection blocks are configured directly in the main form above.</p>
                        </div>
                    </div>

                    <!-- TAB 4: REDIRECTIONS MANAGER -->
                    <div class="tab-pane fade" id="redirects-pane" role="tabpanel">
                        <div class="row g-4">
                            <!-- Add Redirection Rule Form -->
                            <div class="col-md-5">
                                <div class="card border p-3 rounded-3 bg-light">
                                    <h6 class="fw-bold text-dark mb-3"><i class="las la-plus-circle text-primary me-1"></i> Add New Redirection Rule</h6>
                                    <form action="{{ route('admin.plugins.seo.redirections.store') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Source URL (Path) <span class="text-danger">*</span></label>
                                            <input type="text" name="source_url" id="redirectSourceInput" class="form-control" placeholder="/old-campaign-url" required>
                                            <small class="text-muted">Relative path, e.g. <code>/old-page</code></small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Target Destination URL <span class="text-danger">*</span></label>
                                            <input type="text" name="target_url" class="form-control" placeholder="/new-campaign-page or https://..." required>
                                            <small class="text-muted">Relative path or full external URL</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark">Redirect Status Code</label>
                                            <select name="status_code" class="form-select">
                                                <option value="301">301 — Permanent Redirection (SEO juice passed)</option>
                                                <option value="302">302 — Temporary Redirection</option>
                                                <option value="307">307 — Strict Temporary Redirection</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                                            <i class="las la-plus me-1"></i> Save Redirection
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Active Redirections Table -->
                            <div class="col-md-7">
                                <div class="card border rounded-3 p-0 overflow-hidden shadow-sm">
                                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                                        <h6 class="fw-bold text-dark mb-0"><i class="las la-list me-1"></i> Active Redirection Rules ({{ $redirections->count() }})</h6>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Source</th>
                                                    <th>Target</th>
                                                    <th>Type</th>
                                                    <th>Hits</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($redirections as $redir)
                                                    <tr>
                                                        <td><code class="text-dark">{{ $redir->source_url }}</code></td>
                                                        <td><span class="text-truncate d-inline-block small" style="max-width: 160px;">{{ $redir->target_url }}</span></td>
                                                        <td><span class="badge bg-secondary font-monospace">{{ $redir->status_code }}</span></td>
                                                        <td><span class="badge bg-light text-dark border">{{ $redir->hits_count }}</span></td>
                                                        <td>
                                                            <form action="{{ route('admin.plugins.seo.redirections.delete', $redir->id) }}" method="POST" onsubmit="return confirm('Delete this redirection rule?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                                                    <i class="las la-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-muted small">No active redirection rules configured.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: 404 ERROR MONITOR -->
                    <div class="tab-pane fade" id="monitor404-pane" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-1"><i class="las la-bug text-danger me-1"></i> 404 Error Monitor Logs</h6>
                                <p class="text-muted small mb-0">Monitors missing URLs visited by crawlers and visitors to prevent traffic loss.</p>
                            </div>
                            @if($logs404->count() > 0)
                                <form action="{{ route('admin.plugins.seo.404.clear') }}" method="POST" onsubmit="return confirm('Clear all 404 error logs?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="las la-trash-alt me-1"></i> Clear 404 Logs
                                    </button>
                                </form>
                            @endif
                        </div>

                        <div class="card border rounded-3 overflow-hidden shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Requested Missing URI</th>
                                            <th>Hits</th>
                                            <th>Referer</th>
                                            <th>Last Visited</th>
                                            <th class="text-end">Fix Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logs404 as $log)
                                            <tr>
                                                <td><code class="text-danger">{{ $log->uri }}</code></td>
                                                <td><span class="badge bg-danger-subtle text-danger border border-danger-subtle">{{ $log->hits }} hits</span></td>
                                                <td><small class="text-muted">{{ $log->referer ?: 'Direct / Search Bot' }}</small></td>
                                                <td><small class="text-muted">{{ $log->last_occurred_at ? date('Y-m-d H:i', strtotime($log->last_occurred_at)) : 'Recent' }}</small></td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2"
                                                            onclick="quickRedirect('{{ addslashes($log->uri) }}')">
                                                        <i class="las la-exchange-alt me-1"></i> Add 301 Redirect
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="las la-check-circle fs-1 text-success d-block mb-2"></i>
                                                    <strong>Zero 404 Errors Logged</strong>
                                                    <p class="small text-muted mb-0">No broken links or 404 hits have been recorded.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: XML SITEMAP -->
                    <div class="tab-pane fade" id="sitemap-pane" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Enable Dynamic XML Sitemap</h6>
                                        <p class="text-muted small mb-0">Generates a standard sitemap compliant with Google, Bing, and Yandex webmaster specifications.</p>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="seo_sitemap_enabled" value="1" {{ $settings['seo_sitemap_enabled'] ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Sitemap Index Endpoint</label>
                                <div class="input-group">
                                    <input type="text" class="form-control font-monospace" value="{{ url('/sitemap.xml') }}" readonly>
                                    <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-outline-secondary">
                                        <i class="las la-external-link-alt"></i> Open
                                    </a>
                                </div>
                                <small class="text-muted mt-1 d-block">Submit this URL to Google Search Console and Bing Webmaster.</small>
                            </div>
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
function quickRedirect(missingUri) {
    // Switch to redirects tab
    const redirectTab = new bootstrap.Tab(document.getElementById('redirects-tab'));
    redirectTab.show();

    // Populate the source URL input
    const input = document.getElementById('redirectSourceInput');
    if (input) {
        input.value = missingUri;
        input.focus();
    }
}
</script>
@endpush
