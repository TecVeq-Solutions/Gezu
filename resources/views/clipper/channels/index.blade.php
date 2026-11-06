@extends('clipper.layouts.app')

@section('panel')
<div class="row g-4">

    <!-- Header Banner -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="las la-share-alt text-success fs-4"></i> Connected Social Channels
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 14px;">
                        Connect and verify your creator distribution channels. Submissions from verified channels get processed faster.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Channels Grid (Stackposts style with Balanced Geometry) -->
    @foreach($platformsConfig as $key => $platform)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden h-100 d-flex flex-column justify-content-between">
                <div>
                    <!-- Card Top -->
                    <div class="card-header bg-white py-3 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" style="width: 38px; height: 38px; background: {{ $platform['color'] }}; font-size: 17px;">
                                <i class="{{ $platform['icon'] }}"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="mb-0 fw-bold text-dark text-truncate" style="font-size: 14.5px;">{{ $platform['name'] }}</h6>
                                <span class="text-muted d-block text-truncate" style="font-size: 11px;">{{ $platform['desc'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3">
                        @if(!$platform['is_enabled_by_admin'])
                            <div class="p-3 rounded-3 border bg-light">
                                <div class="d-flex align-items-center gap-1 fw-bold text-muted" style="font-size: 12.5px;">
                                    <i class="las la-lock"></i> Disabled by Admin
                                </div>
                                <small class="d-block mt-1 text-muted" style="font-size: 11.5px; line-height: 1.4;">
                                    Platform administrator has not enabled this channel yet.
                                </small>
                            </div>
                        @elseif($platform['account'])
                            <div class="p-3 rounded-3 border" style="background: #ecfdf5; border-color: #a7f3d0 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="badge px-2 py-1 text-white fw-bold d-inline-flex align-items-center gap-1" style="background: #059669; font-size: 10.5px; letter-spacing: 0.4px;">
                                        <i class="las la-check-circle"></i> CONNECTED
                                    </span>
                                    <span class="fw-semibold text-success" style="font-size: 11.5px;">Verified</span>
                                </div>
                                <h5 class="fw-bold font-monospace mb-0 mt-2 text-truncate text-dark" style="font-size: 14.5px;">
                                    {{ $platform['account']->handle }}
                                </h5>
                            </div>
                        @else
                            <div class="p-3 rounded-3 border" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="d-flex align-items-center gap-1 fw-bold text-success" style="font-size: 12.5px;">
                                    <i class="las la-plug"></i> Ready to Connect
                                </div>
                                <small class="d-block mt-1 text-muted" style="font-size: 11.5px; line-height: 1.4;">
                                    Link your channel to submit performance clips directly.
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Footer / Actions -->
                <div class="card-footer bg-white border-top p-3">
                    @if(!$platform['is_enabled_by_admin'])
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 fw-semibold" style="height: 36px;" disabled>
                            <i class="las la-ban me-1"></i> Unavailable
                        </button>
                    @elseif($platform['account'])
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary flex-fill fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="height: 36px;" data-bs-toggle="modal" data-bs-target="#connectModal{{ $key }}">
                                <i class="las la-pen"></i> Update
                            </button>
                            <form action="{{ route('clipper.channels') }}" method="POST" class="d-inline m-0">
                                @csrf
                                <input type="hidden" name="platform_key" value="{{ $key }}">
                                <input type="hidden" name="action" value="disconnect">
                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" style="height: 36px; width: 36px; padding: 0;" title="Disconnect Channel">
                                    <i class="las la-unlink"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <button type="button" class="btn btn-sm btn-creator w-100 d-inline-flex align-items-center justify-content-center gap-1 fw-bold" style="height: 36px;" data-bs-toggle="modal" data-bs-target="#connectModal{{ $key }}">
                            <i class="las la-plus-circle"></i> Connect {{ $platform['name'] }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        @if($platform['is_enabled_by_admin'])
            <!-- Connect / Update Channel Modal -->
            <div class="modal fade" id="connectModal{{ $key }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                        <form action="{{ route('clipper.channels') }}" method="POST">
                            @csrf
                            <input type="hidden" name="platform_key" value="{{ $key }}">
                            <input type="hidden" name="action" value="connect">
                            
                            <div class="modal-header border-bottom py-3 px-4 bg-white">
                                <h6 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px; background: {{ $platform['color'] }}; font-size: 14px;">
                                        <i class="{{ $platform['icon'] }}"></i>
                                    </div>
                                    Connect {{ $platform['name'] }} Channel
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body p-4 bg-white">
                                <div class="mb-3">
                                    <label class="form-label">Channel Handle or Profile URL <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text fw-bold text-muted">@</span>
                                        <input type="text" name="handle" class="form-control fw-semibold" placeholder="your_handle or profile link" value="{{ $platform['account']->handle ?? '' }}" required>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 12px;">Enter your exact handle on {{ $platform['name'] }} where you publish clip submissions.</small>
                                </div>
                            </div>

                            <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" style="height: 38px;" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-creator btn-sm px-4" style="height: 38px;">
                                    <i class="las la-check me-1"></i> Save &amp; Verify Channel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
@endsection
