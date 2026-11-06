@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center gy-4">

    <!-- Telemetry Metric Cards -->
    <div class="col-12">
        <div class="row gy-3">
            <div class="col-md-4 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Total Captured Errors</small>
                            <h3 class="fw-bold mb-0 text-danger mt-1 font-monospace">{{ number_format($totalErrors) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 46px; height: 46px; background: rgba(239, 68, 68, 0.12);">
                            <i class="las la-bug fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Errors Today</small>
                            <h3 class="fw-bold mb-0 {{ $todayErrors > 0 ? 'text-warning' : 'text-success' }} mt-1 font-monospace">{{ number_format($todayErrors) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center {{ $todayErrors > 0 ? 'text-warning' : 'text-success' }}" style="width: 46px; height: 46px; background: {{ $todayErrors > 0 ? 'rgba(245, 158, 11, 0.12)' : 'rgba(16, 185, 129, 0.12)' }};">
                            <i class="las la-clock fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card b-radius--10 border p-3 bg-white shadow-xs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase fw-bold" style="font-size: 11px;">Exception Profiles</small>
                            <h3 class="fw-bold mb-0 text-primary mt-1 font-monospace">{{ count($distinctTypes) }}</h3>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 46px; height: 46px; background: rgba(99, 102, 241, 0.12);">
                            <i class="las la-project-diagram fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Action Controls -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-exclamation-circle text-danger fs-4"></i>
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">Real-Time Application Error Telemetry</h5>
                        <small class="text-muted">Live database recording of uncaught backend exceptions, 500 errors, and route faults.</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if($totalErrors > 0)
                        <button type="button" class="btn btn-outline-danger btn-sm fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#clearErrorsModal" style="height: 38px;">
                            <i class="las la-trash-alt fs-5"></i> Clear All Logs
                        </button>
                    @endif
                    <a href="{{ url('/clear-cache') }}" class="btn btn-outline-warning btn-sm fw-bold d-flex align-items-center gap-1" style="height: 38px;">
                        <i class="las la-sync-alt fs-5"></i> Flush Framework Cache
                    </a>
                </div>
            </div>

            <!-- Search & Type Filter Bar -->
            <div class="p-3 bg-light border-bottom">
                <form method="GET" action="{{ route('admin.errors.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-6 col-sm-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="las la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by error message, exception type, file, or URL..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-8">
                        <select name="exception_type" class="form-select">
                            <option value="">All Exception Types</option>
                            @foreach($distinctTypes as $type)
                                <option value="{{ $type }}" {{ request('exception_type') == $type ? 'selected' : '' }}>
                                    {{ class_basename($type) }} ({{ $type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                        @if(request()->hasAny(['search', 'exception_type']))
                            <a href="{{ route('admin.errors.index') }}" class="btn btn-secondary px-3" title="Reset Filters"><i class="las la-undo"></i></a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Error Records Table -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table--light table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 130px;">Time / User</th>
                                <th style="min-width: 280px;">Error Details & Location</th>
                                <th style="max-width: 250px;">Requested URL</th>
                                <th style="width: 160px;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold text-dark small" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $log->created_at->diffForHumans() }}
                                            </span>
                                            <span class="text-muted" style="font-size: 11px;">
                                                {{ $log->created_at->format('M d, H:i:s') }}
                                            </span>
                                            <div class="mt-1">
                                                @if($log->user)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 10.5px;">
                                                        <i class="las la-user"></i> {{ Str::limit($log->user->name, 14) }} (#{{ $log->user_id }})
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border" style="font-size: 10.5px;">
                                                        <i class="las la-user-secret"></i> Guest / System
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="mb-1">
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace fw-bold" style="font-size: 11px;">
                                                {{ class_basename($log->exception_type) }}
                                            </span>
                                            <small class="text-muted font-monospace ms-1" style="font-size: 11px;">
                                                {{ $log->exception_type }}
                                            </small>
                                        </div>

                                        <div class="fw-bold text-dark text-break small mb-1" style="font-size: 13px;">
                                            {{ $log->message }}
                                        </div>

                                        <div class="text-muted font-monospace" style="font-size: 11.5px;">
                                            <i class="las la-file-code text-secondary"></i> {{ $log->file }} : <strong class="text-danger">{{ $log->line }}</strong>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="text-break font-monospace small text-muted" style="font-size: 12px; max-width: 250px;">
                                            <a href="{{ $log->url }}" target="_blank" class="text-decoration-none text-info text-truncate d-inline-block" style="max-width: 240px;" title="{{ $log->url }}">
                                                {{ $log->url }}
                                            </a>
                                        </div>
                                    </td>

                                    <td class="text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <!-- Copy Full Stacktrace Button -->
                                            <button type="button" class="btn btn-sm btn-outline-primary copy-error-btn" data-error-id="{{ $log->id }}" title="Copy Complete Error & Stacktrace" style="height: 32px; display: inline-flex; align-items: center; gap: 4px; padding: 0 8px; font-size: 12px;">
                                                <i class="las la-copy fs-6"></i> Copy
                                            </button>

                                            <!-- View Modal Button -->
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#errorModal{{ $log->id }}" title="View Full Stacktrace" style="height: 32px; width: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="las la-eye fs-6"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.errors.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this error log entry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Log Entry" style="height: 32px; width: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="las la-trash fs-6"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Hidden text payload for 1-click clipboard copy -->
                                        <textarea id="errorPayload{{ $log->id }}" class="d-none">Exception: {{ $log->exception_type }}
Message: {{ $log->message }}
Location: {{ $log->file }} (Line: {{ $log->line }})
URL: {{ $log->url }}
User: {{ $log->user ? $log->user->name . ' (ID: ' . $log->user_id . ')' : 'Guest' }}
Time: {{ $log->created_at->format('Y-m-d H:i:s') }}

Stacktrace:
{{ $log->trace }}</textarea>

                                        <!-- Full Stacktrace Modal -->
                                        <div class="modal fade text-start" id="errorModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-dark text-white">
                                                        <h6 class="modal-title font-monospace small">
                                                            <i class="las la-bug text-danger me-1"></i> {{ $log->exception_type }}
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 bg-light">
                                                        <div class="alert alert-danger py-2 mb-3">
                                                            <div class="fw-bold">{{ $log->message }}</div>
                                                            <small class="font-monospace text-muted">{{ $log->file }} (Line: {{ $log->line }})</small>
                                                        </div>

                                                        <div class="mb-3 d-flex flex-wrap gap-2 small">
                                                            <span class="badge bg-white text-dark border"><strong>URL:</strong> {{ $log->url }}</span>
                                                            <span class="badge bg-white text-dark border"><strong>User:</strong> {{ $log->user ? $log->user->name : 'Guest' }}</span>
                                                            <span class="badge bg-white text-dark border"><strong>Time:</strong> {{ $log->created_at }}</span>
                                                        </div>

                                                        <h6 class="fw-bold small text-dark mb-2">Full Exception Stacktrace:</h6>
                                                        <pre class="bg-dark text-light p-3 rounded font-monospace small" style="max-height: 400px; overflow-y: auto; font-size: 12px; white-space: pre-wrap; word-break: break-all;">{{ $log->trace ?: 'No stacktrace captured.' }}</pre>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary btn-sm copy-modal-btn" data-target="errorPayload{{ $log->id }}">
                                                            <i class="las la-copy me-1"></i> Copy Error & Trace
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success mb-3" style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1);">
                                                <i class="las la-check-circle fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark">Clean Telemetry - No System Errors Recorded!</h5>
                                            <p class="text-muted small mb-0">The application is running cleanly with zero unhandled exceptions.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logs->hasPages())
                    <div class="p-3 border-top">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: Confirm Clear All Logs -->
<div class="modal fade" id="clearErrorsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.errors.clear') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle me-1"></i> Clear All Error Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete all <strong>{{ number_format($totalErrors) }}</strong> error records from the database?</p>
                    <small class="text-muted">This action permanently purges the <code>error_logs</code> telemetry table.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="las la-trash me-1"></i> Yes, Purge All Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1-Click Copy Handler for Error Buttons
        document.querySelectorAll('.copy-error-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-error-id');
                const textarea = document.getElementById('errorPayload' + id);
                if (textarea) {
                    navigator.clipboard.writeText(textarea.value).then(function () {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Copied!',
                                message: 'Full error details & stacktrace copied to clipboard.',
                                position: 'topRight'
                            });
                        } else {
                            alert('Copied to clipboard!');
                        }
                    });
                }
            });
        });

        // Copy button inside modal
        document.querySelectorAll('.copy-modal-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const textarea = document.getElementById(targetId);
                if (textarea) {
                    navigator.clipboard.writeText(textarea.value).then(function () {
                        if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: 'Copied!',
                                message: 'Stacktrace copied to clipboard.',
                                position: 'topRight'
                            });
                        } else {
                            alert('Copied to clipboard!');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
