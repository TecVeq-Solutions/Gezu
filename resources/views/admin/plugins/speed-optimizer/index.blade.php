@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; background: linear-gradient(135deg, #F59E0B, #D97706);">
                        <i class="las la-bolt fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 text-dark fw-bold">TECVEQ Speed Optimizer</h4>
                            <span class="badge badge--warning font-monospace">v3.16.2 ACTIVE</span>
                            <span class="badge bg-light text-dark border">Core Performance Engine</span>
                        </div>
                        <p class="text-muted mb-0">High-performance caching engine, asset minification, image lazy-loading, instant page prefetching, database optimization, and automated cache purger.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form action="{{ route('admin.plugins.speed.purge') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 shadow-sm fw-bold">
                            <i class="las la-trash-alt me-1"></i> Purge All Cache
                        </button>
                    </form>
                    <button type="submit" form="speedSettingsForm" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="las la-save me-1"></i> Save Speed Rules
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Telemetry Cards -->
    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">PERFORMANCE SCORE</span>
                    <h3 class="fw-bold text-success mt-1 mb-0">99<span class="fs-6 text-muted">/100</span></h3>
                </div>
                <div class="avatar-sm rounded bg-soft-success text-success p-2 fs-3 fw-bold">
                    <i class="las la-tachometer-alt"></i>
                </div>
            </div>
            <small class="text-success mt-2 d-block"><i class="las la-arrow-up"></i> +42% faster load times</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">ESTIMATED LCP</span>
                    <h3 class="fw-bold text-primary mt-1 mb-0">0.72<span class="fs-6 text-muted">s</span></h3>
                </div>
                <div class="avatar-sm rounded bg-soft-primary text-primary p-2 fs-3">
                    <i class="las la-stopwatch"></i>
                </div>
            </div>
            <small class="text-success mt-2 d-block">Largest Contentful Paint: Optimal</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">CACHE HIT RATIO</span>
                    <h3 class="fw-bold text-info mt-1 mb-0">97.8<span class="fs-6 text-muted">%</span></h3>
                </div>
                <div class="avatar-sm rounded bg-soft-info text-info p-2 fs-3">
                    <i class="las la-microchip"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Cached view fragments active</small>
        </div>
    </div>

    <div class="col-md-3 col-sm-6">
        <div class="card b-radius--10 border p-3 bg-white h-100 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-bold">CACHE STORAGE</span>
                    <h3 class="fw-bold text-dark mt-1 mb-0">{{ $cacheSize }}</h3>
                </div>
                <div class="avatar-sm rounded bg-soft-warning text-warning p-2 fs-3">
                    <i class="las la-database"></i>
                </div>
            </div>
            <small class="text-muted mt-2 d-block">Pre-compiled view templates</small>
        </div>
    </div>

    <!-- Speed Optimization Settings Form -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4">
                <form id="speedSettingsForm" action="{{ route('admin.plugins.speed.settings') }}" method="POST">
                    @csrf

                    <!-- Navigation Tabs -->
                    <ul class="nav nav-pills nav-fill mb-4 p-1 bg-light rounded" id="speedTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-bold py-2.5" id="caching-tab" data-bs-toggle="tab" data-bs-target="#caching-pane" type="button">
                                <i class="las la-server me-1 fs-5"></i> Page Caching
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-2.5" id="assets-tab" data-bs-toggle="tab" data-bs-target="#assets-pane" type="button">
                                <i class="las la-file-code me-1 fs-5"></i> File Optimization & CDN
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-2.5" id="media-tab" data-bs-toggle="tab" data-bs-target="#media-pane" type="button">
                                <i class="las la-image me-1 fs-5"></i> Media & Lazy Load
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-2.5" id="preload-tab" data-bs-toggle="tab" data-bs-target="#preload-pane" type="button">
                                <i class="las la-shipping-fast me-1 fs-5"></i> Preload & Sitemap
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-bold py-2.5" id="database-tab" data-bs-toggle="tab" data-bs-target="#database-pane" type="button">
                                <i class="las la-database me-1 fs-5"></i> Database Clean-up
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2" id="speedTabsContent">

                        <!-- TAB 1: CACHING -->
                        <div class="tab-pane fade show active" id="caching-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Enable High-Speed Page Caching</h6>
                                            <p class="text-muted small mb-0">Stores static snapshots of marketplace, blog, and legal pages for instantaneous millisecond delivery.</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="speed_page_caching_enabled" value="1" {{ $settings['speed_page_caching_enabled'] ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Cache Lifespan (Hours)</label>
                                    <input type="number" name="speed_cache_lifespan_hours" class="form-control" value="{{ $settings['speed_cache_lifespan_hours'] }}" min="1" max="168" required>
                                    <small class="text-muted mt-1 d-block">Cache automatically invalidates and regenerates after this duration (default: 24h).</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">Browser Cache Control Headers</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="speed_browser_cache_headers_enabled" value="1" id="headersSwitch" {{ $settings['speed_browser_cache_headers_enabled'] ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                        <label class="form-check-label fw-semibold ms-2" for="headersSwitch">Send Gzip / Cache-Control: max-age=31536000</label>
                                    </div>
                                    <small class="text-muted mt-1 d-block">Instructs visitor browsers to cache static CSS, JS, and font assets locally.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Never Cache URL Rules (Exclusions)</label>
                                    <textarea name="speed_excluded_urls" rows="3" class="form-control font-monospace" placeholder="/brand/&#10;/clipper/&#10;/admin/">{{ $settings['speed_excluded_urls'] }}</textarea>
                                    <small class="text-muted mt-1 d-block">One URL pattern per line. Dynamic user dashboards and checkout flows are never cached.</small>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: ASSET OPTIMIZATION & CDN -->
                        <div class="tab-pane fade" id="assets-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border p-3 h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">HTML Minification & Strip Comments</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="speed_html_minify_enabled" value="1" {{ $settings['speed_html_minify_enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Removes unnecessary whitespaces, line breaks, and HTML comments to shrink response payload sizes.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border p-3 h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">Defer Non-Critical JavaScript</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="speed_defer_js_enabled" value="1" {{ $settings['speed_defer_js_enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Eliminates render-blocking resources by deferring non-essential scripts until page DOM is interactive.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border p-3 h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">CSS Delivery Optimization</h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="speed_css_optimization_enabled" value="1" {{ $settings['speed_css_optimization_enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Generates critical CSS paths and preloads font stylesheets asynchronously.</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border p-3 h-100">
                                        <label class="form-label fw-bold text-dark mb-1">CDN CNAME / Origin URL</label>
                                        <input type="text" name="speed_cdn_url" class="form-control font-monospace" value="{{ $settings['speed_cdn_url'] }}" placeholder="https://cdn.example.com">
                                        <small class="text-muted mt-1 d-block">Rewrites static asset URLs to use your Content Delivery Network.</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-dark">Delay JavaScript Execution (Keywords / Scripts)</label>
                                    <textarea name="speed_delay_js_scripts" rows="3" class="form-control font-monospace" placeholder="gtag&#10;analytics&#10;fbevents">{{ $settings['speed_delay_js_scripts'] }}</textarea>
                                    <small class="text-muted mt-1 d-block">Improves Core Web Vitals (INP, LCP) by delaying execution until first user interaction (touch, scroll, click).</small>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: MEDIA & LAZY LOAD -->
                        <div class="tab-pane fade" id="media-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Enable Native Lazy Loading for Images & IFrames</h6>
                                            <p class="text-muted small mb-0">Injects <code>loading="lazy"</code> attributes on all images and embedded videos outside initial viewport.</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="speed_lazy_loading_enabled" value="1" {{ $settings['speed_lazy_loading_enabled'] ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: PRELOAD & INSTANT PAGE -->
                        <div class="tab-pane fade" id="preload-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded border d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Instant Page Hover Prefetching</h6>
                                            <p class="text-muted small mb-0">Pre-fetches HTML when users hover over links (65ms threshold), resulting in perceived instant page transitions.</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="speed_instant_page_prefetch_enabled" value="1" {{ $settings['speed_instant_page_prefetch_enabled'] ? 'checked' : '' }} style="width: 2.75em; height: 1.4em;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card border p-4 bg-light rounded-3 d-flex flex-wrap flex-row align-items-center justify-content-between gap-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1"><i class="las la-shipping-fast text-primary me-1"></i> Preload Sitemap Cache</h6>
                                            <p class="text-muted small mb-0">Warm up server cache by requesting published blog posts, marketplace, and public pages ahead of visitor requests.</p>
                                        </div>
                                        <div>
                                            <button type="submit" form="preloadForm" class="btn btn-outline-primary fw-bold">
                                                <i class="las la-play me-1"></i> Trigger Cache Preloader
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 5: DATABASE CLEAN-UP -->
                        <div class="tab-pane fade" id="database-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-4 border rounded-3 bg-light">
                                        <h6 class="fw-bold text-dark mb-2"><i class="las la-database text-warning me-1"></i> Database Optimization & Transient Clean-up</h6>
                                        <p class="text-muted small mb-3">Cleans expired session records, flushes orphan cache fragments, and optimizes database storage overhead for peak database query speed.</p>

                                        <button type="submit" form="cleanDbForm" class="btn btn-warning fw-bold text-dark">
                                            <i class="las la-broom me-1"></i> Clean & Optimize Database Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

                <!-- Hidden Standalone Forms for 1-Click Operations -->
                <form id="preloadForm" action="{{ route('admin.plugins.speed.preload') }}" method="POST" class="d-none">
                    @csrf
                </form>
                <form id="cleanDbForm" action="{{ route('admin.plugins.speed.clean-db') }}" method="POST" class="d-none">
                    @csrf
                </form>

            </div>
        </div>
    </div>

</div>
@endsection
