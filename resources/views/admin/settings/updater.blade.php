@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center gy-4">
    <div class="col-xl-10 col-lg-12">
        
        <!-- Status Card -->
        <div class="card b-radius--10 border mb-2 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg--success text-white d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; font-size: 26px;">
                            <i class="las la-check-circle"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">TECVEQ System Core is Healthy</h5>
                            <span class="text-muted small">Current Installed Version: <strong class="text-dark">{{ $system['name'] }} {{ $system['version'] }}</strong></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-outline-info btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#updaterDevGuideModal">
                            <i class="las la-book-open me-1"></i> Packaging Guide
                        </button>
                        <a href="{{ route('admin.system.clear-cache') }}" class="btn btn-outline-warning btn-sm fw-bold">
                            <i class="las la-broom me-1"></i> Flush Caches
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deploy Update Archive (.ZIP) -->
        <div class="card b-radius--10 border mb-2 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-cloud-upload-alt text-primary me-1"></i> Deploy System Update Archive</h6>
                <span class="badge badge--warning">ZIP Patch / Full Archive</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.settings.updater.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row gy-3">
                        <div class="col-md-9">
                            <label class="form-label fw-bold">Select Update Archive (.ZIP) <span class="text-danger">*</span></label>
                            <input type="file" name="update_zip" class="form-control" accept=".zip" required>
                            <small class="text-muted d-block mt-1">Accepts cryptographically verified patch packages up to 100MB.</small>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="las la-upload me-1"></i> Upload & Deploy
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Release History Table with SQL Telemetry & Audit Logs -->
        <div class="card b-radius--10 border mb-2 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-history text-primary me-1"></i> Deployed System Releases & Database SQL Audit</h6>
                    <small class="text-muted">Detailed records of all uploaded patches, updated file counts, and SQL execution results.</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border px-2 py-1 small">
                    <i class="las la-database me-1"></i> Migration &amp; SQL Engine
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Version &amp; Release ID</th>
                                <th>Files Updated</th>
                                <th>Database &amp; SQL Status</th>
                                <th>Deployed At</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($releases as $rel)
                                @php
                                    $manifest = is_array($rel->manifest) ? $rel->manifest : json_decode($rel->manifest ?? '[]', true);
                                    $filesCount = $manifest['files_count'] ?? (isset($manifest['files_sample']) ? count($manifest['files_sample']) : null);
                                    $sqlReport = $manifest['sql_report'] ?? null;
                                    $sqlPassed = $sqlReport['queries_passed'] ?? null;
                                    $sqlFailed = $sqlReport['queries_failed'] ?? 0;
                                    $sqlErrors = $sqlReport['errors'] ?? [];
                                    $hasSqlFailure = $sqlFailed > 0 || ($sqlReport['migration_status'] ?? '') === 'FAILED' || !empty($rel->failure_message);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="font-monospace fw-bold text-dark d-block">{{ $rel->version }}</span>
                                        <small class="font-monospace text-muted" style="font-size: 11px;">{{ $rel->release_id }}</small>
                                    </td>
                                    <td>
                                        @if($filesCount)
                                            <span class="badge bg-primary-subtle text-primary border px-2 py-1 font-monospace">
                                                <i class="las la-file-code me-1"></i> {{ $filesCount }} Files
                                            </span>
                                        @else
                                            <span class="text-muted small">System Base</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sqlReport)
                                            @if(!$hasSqlFailure)
                                                <span class="badge bg-success-subtle text-success border px-2 py-1">
                                                    <i class="las la-check-circle me-1"></i> SQL Passed ({{ $sqlPassed }} queries)
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border px-2 py-1" title="{{ $rel->failure_message ?? 'SQL error occurred' }}">
                                                    <i class="las la-exclamation-triangle me-1"></i> SQL Failed ({{ $sqlFailed }} errors)
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">
                                                <i class="las la-check me-1"></i> Migrations OK
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted d-block">{{ $rel->created_at->format('M d, Y H:i') }}</small>
                                        <small class="text-muted" style="font-size: 10.5px;">{{ $rel->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($hasSqlFailure)
                                            <span class="badge bg-warning-subtle text-warning border px-2 py-1 fw-bold">
                                                <i class="las la-exclamation-circle me-1"></i> WARNING
                                            </span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border px-2 py-1 fw-bold">
                                                <i class="las la-check-circle me-1"></i> ACTIVE
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-1" data-bs-toggle="modal" data-bs-target="#releaseModal_{{ $rel->id }}" title="View Release Details">
                                            <i class="las la-eye me-1"></i> Details
                                        </button>

                                        <!-- Release Detail Modal -->
                                        <div class="modal fade" id="releaseModal_{{ $rel->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header border-bottom py-3">
                                                        <h6 class="modal-title fw-bold text-dark">
                                                            <i class="las la-box text-primary me-1"></i> Release Details — {{ $rel->version }} ({{ $rel->release_id }})
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <!-- Summary -->
                                                        <div class="p-3 bg-light rounded-3 border mb-3">
                                                            <span class="fw-bold d-block text-dark mb-1">Execution Summary:</span>
                                                            <p class="text-muted small mb-0">{{ $rel->description }}</p>
                                                        </div>

                                                        <!-- SQL Execution Report -->
                                                        <h6 class="fw-bold text-dark small mb-2"><i class="las la-database text-primary me-1"></i> Database SQL Queries Report</h6>
                                                        @if($sqlReport)
                                                            <div class="row g-2 mb-3">
                                                                <div class="col-sm-4">
                                                                    <div class="p-2 border rounded bg-white text-center">
                                                                        <small class="text-muted d-block" style="font-size: 11px;">Total Queries Run</small>
                                                                        <strong class="font-monospace fs-6">{{ $sqlReport['queries_executed'] ?? 0 }}</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="p-2 border rounded bg-white text-center">
                                                                        <small class="text-success d-block" style="font-size: 11px;">Queries Passed</small>
                                                                        <strong class="font-monospace fs-6 text-success">{{ $sqlReport['queries_passed'] ?? 0 }}</strong>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <div class="p-2 border rounded bg-white text-center">
                                                                        <small class="text-danger d-block" style="font-size: 11px;">Queries Failed</small>
                                                                        <strong class="font-monospace fs-6 {{ ($sqlReport['queries_failed'] ?? 0) > 0 ? 'text-danger' : 'text-muted' }}">
                                                                            {{ $sqlReport['queries_failed'] ?? 0 }}
                                                                        </strong>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if(!empty($sqlReport['sql_files_found']))
                                                                <div class="mb-3">
                                                                    <small class="text-muted fw-bold d-block mb-1">SQL Patch Files Executed:</small>
                                                                    <div class="d-flex flex-wrap gap-1">
                                                                        @foreach($sqlReport['sql_files_found'] as $sff)
                                                                            <span class="badge bg-light text-dark border font-monospace">{{ $sff }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if(!empty($sqlErrors))
                                                                <div class="alert alert-danger p-3 rounded-3 mb-3">
                                                                    <h6 class="fw-bold small text-danger mb-1"><i class="las la-exclamation-triangle me-1"></i> SQL Execution Errors / Issues:</h6>
                                                                    @foreach($sqlErrors as $err)
                                                                        <div class="p-2 bg-white rounded border mb-2 text-dark font-monospace" style="font-size: 11.5px;">
                                                                            <span class="text-muted d-block"><strong>File:</strong> {{ $err['file'] ?? 'unknown' }}</span>
                                                                            <span class="text-danger d-block"><strong>Error:</strong> {{ $err['error'] ?? '' }}</span>
                                                                            @if(!empty($err['query']))
                                                                                <small class="text-muted d-block mt-1"><strong>Query:</strong> {{ $err['query'] }}</small>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif

                                                            @if(!empty($sqlReport['migration_output']))
                                                                <div class="mb-3">
                                                                    <small class="text-muted fw-bold d-block mb-1">Artisan Migration Output:</small>
                                                                    <pre class="bg-dark text-white p-2 rounded font-monospace" style="font-size: 11.5px; max-height: 120px; overflow-y: auto;">{{ $sqlReport['migration_output'] }}</pre>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <p class="text-muted small">Standard migrations verified without separate SQL patch files.</p>
                                                        @endif

                                                        <!-- Files Updated Sample -->
                                                        @if(!empty($manifest['files_sample']))
                                                            <h6 class="fw-bold text-dark small mb-2"><i class="las la-file-alt text-primary me-1"></i> Updated Files Sample ({{ count($manifest['files_sample']) }} of {{ $filesCount }} shown):</h6>
                                                            <div class="p-2 bg-light rounded border" style="max-height: 160px; overflow-y: auto;">
                                                                <ul class="list-unstyled mb-0 font-monospace small">
                                                                    @foreach($manifest['files_sample'] as $f)
                                                                        <li class="py-0.5 text-muted"><i class="las la-check text-success me-1"></i> {{ $f }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer bg-light py-2">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="las la-inbox fs-2 text-secondary d-block mb-1"></i>
                                        No update packages deployed yet. Upload a .ZIP patch above to deploy.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Release Changelog -->
        <div class="card b-radius--10 border">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark"><i class="las la-clipboard-list text-primary me-1"></i> System Changelog & Architecture Notes</h6>
            </div>
            <div class="card-body p-4">
                @foreach($changelog as $ver => $notes)
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge badge--primary font-monospace">{{ $ver }}</span>
                            <span class="fw-bold text-dark">Production Baseline</span>
                        </div>
                        <ul class="list-group list-group-flush small">
                            @foreach($notes as $note)
                                <li class="list-group-item px-0 py-1.5 border-0 d-flex align-items-center gap-2">
                                    <i class="las la-check text-success"></i>
                                    <span>{{ $note }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

<!-- Modal: Developer Packaging Guide -->
<div class="modal fade" id="updaterDevGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold text-dark"><i class="las la-book-open text-info me-1"></i> Developer Guide — Packaging a System Update</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <h6 class="fw-bold text-dark mb-2">1. Directory Structure of an Update ZIP</h6>
                <pre class="bg-dark text-white p-3 rounded font-monospace small">
update-v1.1.0.zip
├── manifest.json
├── database/
│   └── migrations/
└── files/
    ├── app/
    ├── resources/
    └── routes/
                </pre>

                <h6 class="fw-bold text-dark mt-3 mb-2">2. Sample manifest.json</h6>
                <pre class="bg-dark text-white p-3 rounded font-monospace small">
{
    "version": "v1.1.0",
    "type": "patch",
    "required_version": "v1.0.0",
    "requires_migration": true,
    "changelog": [
        "Enhanced social connection protocol",
        "Direct bank wire export format"
    ]
}
                </pre>

                <h6 class="fw-bold text-dark mt-3 mb-2">3. Zero-Downtime Rollback Protection</h6>
                <p class="text-muted small mb-0">
                    The automated engine stages the archive into temporary storage, performs integrity hashing, creates an automatic backup restore point, runs artisan optimize, and switches symlinks seamlessly.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">Close Guide</button>
            </div>
        </div>
    </div>
</div>
@endsection
