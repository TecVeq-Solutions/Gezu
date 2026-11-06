@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.settings.social') }}" method="POST">
            @csrf

            <div class="card b-radius--10 border">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="card-title mb-0 fw-bold">Social Platform Integrations & API Credentials</h6>
                        <small class="text-muted">Configure OAuth 2.0 app credentials for creator account linking and video telemetry.</small>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                        <i class="las la-save me-1"></i> Save All Platforms
                    </button>
                </div>
                <div class="card-body p-4">

                    <!-- Master Policy: Require Social Account Connection Before Clip Submission -->
                    <div class="p-3 mb-4 rounded-3 border bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 22px;">
                                <i class="las la-shield-alt"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Require Connected Social Account to Submit Campaign Video for Clippers</h6>
                                <p class="text-muted small mb-0">
                                    When enabled (ON), clippers must link and verify their social account (TikTok, YouTube, Instagram, etc.) in their profile before submitting video clips. If disabled (OFF), clippers can submit public video URLs without prior connection.
                                </p>
                            </div>
                        </div>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" name="require_social_account_for_clips" value="1" {{ !empty($requireSocialAccountForClips) ? 'checked' : '' }} style="width: 3em; height: 1.5em; cursor: pointer;">
                        </div>
                    </div>
                    
                    <!-- Platform Tabs Navigation -->
                    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="socialTabs" role="tablist">
                        @foreach($platforms as $index => $plat)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link btn-sm {{ $index === 0 ? 'active' : '' }} text-uppercase fw-bold" id="{{ $plat }}-tab" data-bs-toggle="pill" data-bs-target="#tab-{{ $plat }}" type="button" role="tab">
                                    {{ $plat === 'x' ? 'X (Twitter)' : ucfirst($plat) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Platform Tab Panes -->
                    <div class="tab-content" id="socialTabsContent">
                        @foreach($platforms as $index => $plat)
                            @php $p = $data[$plat]; @endphp
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab-{{ $plat }}" role="tabpanel">
                                <div class="row gy-3">
                                    <div class="col-12 d-flex align-items-center justify-content-between p-3 rounded bg-light border">
                                        <div>
                                            <span class="fw-bold d-block text-dark">{{ ucfirst($plat) }} API Integration</span>
                                            <small class="text-muted">Enable or disable creator linking for this network.</small>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="{{ $plat }}_enabled" value="1" {{ $p['enabled'] ? 'checked' : '' }} style="width: 2.5em; height: 1.3em;">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Client ID / App ID</label>
                                        <input type="text" name="{{ $plat }}_client_id" class="form-control font-monospace" value="{{ $p['client_id'] }}" placeholder="Enter App or Client ID">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Client Secret / App Secret</label>
                                        <input type="password" name="{{ $plat }}_client_secret" class="form-control font-monospace" value="{{ $p['client_secret'] }}" placeholder="Enter App or Client Secret">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">API Key / Access Key</label>
                                        <input type="text" name="{{ $plat }}_api_key" class="form-control font-monospace" value="{{ $p['api_key'] }}" placeholder="Optional API Key">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">OAuth 2.0 Redirect URI</label>
                                        <input type="text" name="{{ $plat }}_redirect_url" class="form-control font-monospace" value="{{ $p['redirect_url'] }}" placeholder="{{ url('/auth/' . $plat . '/callback') }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Authorized Scopes</label>
                                        <input type="text" name="{{ $plat }}_scopes" class="form-control font-monospace" value="{{ $p['scopes'] }}" placeholder="e.g. user.info.basic,video.list,read:insights">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
                <div class="card-footer bg-white border-top py-3 text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="las la-save me-1"></i> Save Platform Configurations
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
