@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Action Card -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-dark fw-bold"><i class="las la-puzzle-piece text-primary me-1"></i> Plugins & Modular Extensions</h4>
                    <p class="text-muted mb-0">Extend TECVEQ core capabilities with hot-swappable plugins for analytics, authentication, payouts, and AI automations.</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#pluginDevGuideModal">
                        <i class="las la-book-open"></i> Developer Guide
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                        <i class="las la-cloud-upload-alt"></i> Upload Plugin (.ZIP)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Plugins Table / Catalog -->
    <div class="col-12">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-bold text-dark">Installed Extensions & Modules</h5>
                <span class="badge badge--primary">{{ $plugins->count() }} Installed</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>Plugin / Extension</th>
                                <th>Version</th>
                                <th>Type</th>
                                <th>Target Panels</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $isGoogleEnabled = (bool)\App\Models\AuthSetting::get('google_oauth_enabled', false);
                            @endphp
                            <!-- Core Google OAuth Extension (HYIPLab + Gezux standard) -->
                            <tr class="table-light">
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold text-danger" style="width: 40px; height: 40px; background: rgba(239, 68, 68, 0.1);">
                                            <i class="lab la-google fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark d-block">Google OAuth 2.0 &amp; Social Sign-In</span>
                                                <span class="badge bg-danger text-white" style="font-size: 0.68rem;">OFFICIAL</span>
                                            </div>
                                            <small class="text-muted">Instant one-tap login and registration with Google accounts for Brands and Creators.</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="font-monospace small">v2.0</span></td>
                                <td><span class="badge bg-light text-dark border">Authentication</span></td>
                                <td><span class="badge bg-soft-info text-info">Brand &amp; Clipper</span></td>
                                <td>
                                    @if($isGoogleEnabled)
                                        <span class="badge badge--success"><i class="las la-check-circle"></i> Enabled</span>
                                    @else
                                        <span class="badge badge--warning"><i class="las la-exclamation-circle"></i> Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.settings.auth') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="las la-cog"></i> Configure
                                    </a>
                                </td>
                            </tr>
                            @forelse($plugins as $plugin)
                                @php
                                    $manifest = is_array($plugin->manifest) ? $plugin->manifest : json_decode($plugin->manifest ?? '[]', true);
                                    $icon = $manifest['icon'] ?? 'las la-cube';
                                    $settingsUrl = $manifest['settings_url'] ?? null;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; background: rgba(99, 102, 241, 0.1);">
                                                <i class="{{ $icon }} fs-3"></i>
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold text-dark d-block">{{ $plugin->name }}</span>
                                                    @if(!empty($manifest['badge']))
                                                        <span class="badge bg-light text-primary border" style="font-size: 0.68rem;">{{ $manifest['badge'] }}</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block" style="font-size: 0.78rem;">{{ $manifest['description'] ?? $plugin->plugin_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge--dark font-monospace">{{ $plugin->version }}</span>
                                    </td>
                                    <td>
                                        <span class="text-uppercase small fw-semibold text-secondary">{{ $plugin->type }}</span>
                                    </td>
                                    <td>
                                        @if(is_array($plugin->panels))
                                            @foreach($plugin->panels as $pnl)
                                                <span class="badge bg-light text-dark border">{{ strtoupper($pnl) }}</span>
                                            @endforeach
                                        @else
                                            <span class="badge bg-light text-dark border">ALL PANELS</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($plugin->status === 'ENABLED')
                                            <span class="badge badge--success"><i class="las la-check-circle"></i> ACTIVE</span>
                                        @else
                                            <span class="badge badge--secondary"><i class="las la-pause"></i> DISABLED</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            @if($settingsUrl)
                                                <a href="{{ url($settingsUrl) }}" class="btn btn-sm btn-outline-primary py-1 px-2" title="Configure Plugin Settings">
                                                    <i class="las la-cog"></i> Settings
                                                </a>
                                            @endif
                                            <form action="{{ route('admin.plugins.toggle', $plugin->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $plugin->status === 'ENABLED' ? 'btn-outline-warning' : 'btn-outline-success' }} py-1 px-2">
                                                    <i class="las {{ $plugin->status === 'ENABLED' ? 'la-toggle-on' : 'la-toggle-off' }}"></i>
                                                    {{ $plugin->status === 'ENABLED' ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="las la-puzzle-piece fs-1 d-block mb-2 text-muted"></i>
                                        <p class="mb-2">No third-party plugins installed yet.</p>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                                            Upload First Plugin (.ZIP)
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Upload Plugin Archive -->
<div class="modal fade" id="uploadPluginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.plugins.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-dark"><i class="las la-cloud-upload-alt text-primary me-1"></i> Upload Plugin Bundle</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Plugin Archive (.ZIP) <span class="text-danger">*</span></label>
                        <input type="file" name="plugin_zip" class="form-control" accept=".zip" required>
                        <small class="text-muted d-block mt-1">Accepts packaged zip archives up to 50MB with valid module manifest.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Install & Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Developer Guide -->
<div class="modal fade" id="pluginDevGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold text-dark"><i class="las la-book-open text-info me-1"></i> Developer Guide — Building TECVEQ Plugins</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-dark mb-2">1. Plugin Directory Structure</h6>
                <pre class="bg-dark text-white p-3 rounded font-monospace small">
my-plugin.zip
├── manifest.json
├── src/
│   ├── Providers/
│   │   └── PluginServiceProvider.php
│   └── Http/Controllers/
└── resources/views/
                </pre>

                <h6 class="fw-bold text-dark mt-3 mb-2">2. Sample manifest.json</h6>
                <pre class="bg-dark text-white p-3 rounded font-monospace small">
{
    "id": "google-oauth-plus",
    "name": "Google One-Tap & OAuth 2.0",
    "version": "1.0.0",
    "author": "TECVEQ Labs",
    "panels": ["admin", "brand", "clipper"],
    "provider": "Plugins\\GoogleOAuth\\GoogleOAuthServiceProvider"
}
                </pre>

                <h6 class="fw-bold text-dark mt-3 mb-2">3. Activation Lifecycle</h6>
                <p class="text-muted small mb-0">
                    Once uploaded, TECVEQ extracts the bundle, verifies the cryptographic SHA-256 hash, registers the module provider, and automatically displays the extension in this registry.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Got It</button>
            </div>
        </div>
    </div>
</div>
@endsection
