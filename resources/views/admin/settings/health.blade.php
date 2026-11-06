@extends('admin.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        
        <!-- System Health Overview -->
        <div class="card b-radius--10 border mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="card-title mb-0 fw-bold">System Health & Diagnostic Telemetry</h6>
                    <small class="text-muted">Environment runtime specifications and cache operations.</small>
                </div>
                <form action="{{ route('admin.settings.health') }}" method="POST">
                    @csrf
                    <input type="hidden" name="clear_cache" value="1">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">
                        <i class="las la-trash me-1"></i> Clear Application Cache
                    </button>
                </form>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th class="bg-light fw-bold text-dark w-25">PHP Version</th>
                                <td class="font-monospace text-primary fw-bold">{{ $health['php_version'] }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Laravel Framework</th>
                                <td class="font-monospace text-dark fw-semibold">{{ $health['laravel_version'] }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Server Operating System</th>
                                <td class="text-dark fw-semibold">{{ $health['server_os'] }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Database Driver</th>
                                <td class="font-monospace text-success fw-bold">{{ strtoupper($health['db_connection']) }} (MySQL 8.x)</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Cache Driver</th>
                                <td class="font-monospace text-dark fw-semibold">{{ strtoupper($health['cache_driver']) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Session Driver</th>
                                <td class="font-monospace text-dark fw-semibold">{{ strtoupper($health['session_driver']) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Application Debug</th>
                                <td><span class="badge badge--primary">{{ $health['app_debug'] }}</span></td>
                            </tr>
                            <tr>
                                <th class="bg-light fw-bold text-dark">Server Timezone</th>
                                <td class="font-monospace text-dark fw-semibold">{{ $health['time_zone'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
