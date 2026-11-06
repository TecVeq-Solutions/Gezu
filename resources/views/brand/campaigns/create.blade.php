@extends('brand.layouts.app')

@section('panel')
<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">
                            <i class="las la-rocket text-primary me-1"></i> Launch Short-Form Bounty Campaign
                        </h5>
                        <small class="text-muted">Deposit budget into campaign reward pool and connect with viral creators.</small>
                    </div>
                    <a href="{{ route('brand.campaigns') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="las la-arrow-left me-1"></i> Back to Campaigns
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
                    <i class="las la-check-circle fs-4 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center gap-2">
                    <i class="las la-exclamation-circle fs-4 text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger border-0 shadow-sm mb-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="las la-exclamation-triangle fs-4 text-danger"></i>
                        <h6 class="mb-0 fw-bold text-danger">Please fix the following issues to launch:</h6>
                    </div>
                    <ul class="mb-0 ps-3 small text-danger">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form action="{{ route('brand.campaigns.store') }}" method="POST" enctype="multipart/form-data" id="createCampaignForm" novalidate>
                    @csrf

                    <!-- 1. Title & Category -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" style="height: 44px;" required placeholder="e.g. AI Video Editor Launch & TikTok Virality Bounty" value="{{ old('title') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" style="height: 44px;" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- 2. Creative Guidelines & Brief -->
                    <div class="mb-4">
                        <label class="form-label">Campaign Overview & Creative Concept <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Describe what the product or service is, the core campaign message, and the main story or angle creators should take...">{{ old('description') }}</textarea>
                    </div>

                    <!-- 2.1 Specific Clipping Rules & Do's / Don'ts -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="las la-shield-alt text-primary me-1"></i> Specific Clipping Rules &amp; Creator Requirements
                        </label>
                        <textarea name="rules" class="form-control font-monospace" rows="4" placeholder="Enter specific rules (one per line):
- Video length must be between 15-60 seconds
- Include #CampaignTag in caption and pinned comment
- Must use original voiceover or supplied background audio
- No misleading titles or artificial bot views">{{ old('rules') }}</textarea>
                    </div>

                    <!-- 3. Audience Targeting & Campaign Economics (Unified) -->
                    <div class="card border rounded-3 p-3 mb-4 bg-white" id="unifiedEconomicsSection">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="las la-globe-americas text-primary me-1"></i> Audience Targeting & Campaign Economics
                            </h6>
                        </div>

                        <!-- 3.0 Campaign Payout Model: Performance CPM vs. Fixed Pay Per Video -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-2">
                                <i class="las la-hand-holding-usd text-primary me-1"></i> Campaign Payout & Pricing Model <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="card h-100 p-3 border rounded-3 cursor-pointer m-0 bg-white shadow-none payout-model-card" id="cardModelCpm" style="cursor: pointer; border-color: #0d6efd !important; background: #f0f7ff !important;">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="payout_model" value="cpm" id="modelCpm" checked onchange="switchPayoutModel('cpm')">
                                            <div>
                                                <div class="fw-bold text-dark fs-6 mb-0.5">⚡ Performance CPM (Per 1k Views)</div>
                                                <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.35;">Pay for verified reach ($/1,000 views). Best for viral clips and high organic reach.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="card h-100 p-3 border rounded-3 cursor-pointer m-0 bg-white shadow-none payout-model-card" id="cardModelFixed" style="cursor: pointer;">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <input class="form-check-input mt-1 flex-shrink-0" type="radio" name="payout_model" value="fixed_per_video" id="modelFixed" onchange="switchPayoutModel('fixed_per_video')">
                                            <div>
                                                <div class="fw-bold text-dark fs-6 mb-0.5">🎯 Fixed Pay Per Video (Bounty / Video)</div>
                                                <small class="text-muted d-block" style="font-size: 11.5px; line-height: 1.35;">Pay a flat reward for each approved video with an expected audience view target.</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- 3.1 Mode Selector (Worldwide / Single Country / Multi-Country) -->
                        <div class="d-flex gap-2 mb-3" id="countryModeSelector">
                            <label class="country-mode-btn flex-fill text-center p-2 border rounded-3 cursor-pointer" style="cursor:pointer;" id="modeWorldwideLabel">
                                <input type="radio" name="country_targeting_type" value="worldwide" id="modeWorldwide" class="d-none" checked>
                                <div id="modeWorldwideInner" class="d-flex flex-column align-items-center gap-1 py-1">
                                    <span class="fs-4">🌍</span>
                                    <span class="fw-semibold small text-dark">Worldwide</span>
                                    <small class="text-muted" style="font-size:10.5px;">All countries, global base CPM</small>
                                </div>
                            </label>
                            <label class="country-mode-btn flex-fill text-center p-2 border rounded-3 cursor-pointer" style="cursor:pointer;" id="modeSingleLabel">
                                <input type="radio" name="country_targeting_type" value="single" id="modeSingle" class="d-none">
                                <div id="modeSingleInner" class="d-flex flex-column align-items-center gap-1 py-1">
                                    <span class="fs-4">🎯</span>
                                    <span class="fw-semibold small text-dark">Single Country</span>
                                    <small class="text-muted" style="font-size:10.5px;">1 target country, custom CPM</small>
                                </div>
                            </label>
                            <label class="country-mode-btn flex-fill text-center p-2 border rounded-3 cursor-pointer" style="cursor:pointer;" id="modeMultiLabel">
                                <input type="radio" name="country_targeting_type" value="multiple" id="modeMultiple" class="d-none">
                                <div id="modeMultiInner" class="d-flex flex-column align-items-center gap-1 py-1">
                                    <span class="fs-4">🗺️</span>
                                    <span class="fw-semibold small text-dark">Multi-Country</span>
                                    <small class="text-muted" style="font-size:10.5px;">Multiple markets, each CPM</small>
                                </div>
                            </label>
                        </div>

                        <!-- 3.2 Single Country Selector (Only visible in Single Mode) -->
                        <div id="singleCountrySelectorBox" class="p-3 mb-3 border rounded-3 bg-light" style="display: none;">
                            <label class="form-label fw-semibold small mb-2" style="min-height: 22px;">
                                <i class="las la-map-pin text-primary me-1"></i> Choose Target Country <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2 align-items-center">
                                <div class="col-md-7">
                                    <select id="singleCountrySelect" class="form-select" style="height: 44px;" onchange="fillSingleCountry(this)">
                                        <option value="">-- Choose Target Country --</option>
                                        @foreach($presetCountries as $pc)
                                            <option 
                                                value="{{ $pc['code'] }}" 
                                                data-name="{{ $pc['name'] }}" 
                                                data-flag="https://flagcdn.com/24x18/{{ strtolower($pc['code']) }}.png"
                                                data-cpm="{{ $pc['default_cpm'] }}">
                                                {{ $pc['name'] }} (Default ${{ number_format($pc['default_cpm'], 2) }}/1k)
                                            </option>
                                        @endforeach
                                        <option value="__custom__">✏️ Enter Custom Country Name</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div id="singleCountryActiveBadge" class="d-flex align-items-center gap-2 px-3 bg-white border rounded" style="height: 44px;">
                                        <img id="singleCountryFlagImg" src="https://flagcdn.com/24x18/us.png" width="24" height="18" class="rounded-1 border shadow-xs" alt="flag">
                                        <div class="lh-1">
                                            <span id="singleCountryNameDisplay" class="fw-bold small text-dark d-block">United States</span>
                                            <small class="text-muted" style="font-size: 11px;">Active target country</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Single Country Row (No ISO code field for user) -->
                            <div class="row g-2 mt-2 align-items-center" id="singleCustomInputsRow" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label small mb-1">Custom Country Name</label>
                                    <input type="text" name="single_country_name" id="singleCountryName" class="form-control" style="height: 44px;" placeholder="e.g. United States, Germany, Brazil" value="United States">
                                    <input type="hidden" name="single_country_code" id="singleCountryCode" value="US">
                                </div>
                            </div>
                        </div>

                        <!-- 3.3 Financial Calculations Row (Budget, CPM, Estimated Reach) -->
                        <div class="row g-3 mb-3 p-3 rounded-3 bg-light bg-opacity-75 border" id="financialCalculationsRow">
                            <!-- Budget: Always visible -->
                            <div class="col-md-4" id="budgetCol">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-dark small mb-0">Total Budget ($ USD) <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="input-group" style="height: 44px;">
                                        <span class="input-group-text fw-bold bg-light" style="height: 44px; min-width: 44px; justify-content: center;">$</span>
                                        <input type="number" id="budgetInput" name="total_budget" step="any" min="1" class="form-control fw-bold font-monospace text-dark" style="height: 44px; font-size: 15px;" required value="{{ old('total_budget', $defaultBudget ?? '100') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- CPM Column: Shown in CPM Mode -->
                            <div class="col-md-4" id="cpmCardCol">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-dark small mb-0" id="cpmFieldLabel">Global CPM Rate ($/1k Views) <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="input-group" style="height: 44px;">
                                        <span class="input-group-text fw-bold bg-light" style="height: 44px; min-width: 44px; justify-content: center;">$</span>
                                        <input type="number" id="cpmInput" name="reward_rate_per_thousand" step="any" min="0.01" class="form-control fw-bold font-monospace text-dark" style="height: 44px; font-size: 15px;" value="{{ old('reward_rate_per_thousand', '2.5') }}">
                                        <input type="hidden" name="single_country_cpm" id="singleCountryCpm" value="2.5">
                                    </div>
                                </div>
                            </div>

                            <!-- Fixed Reward Per Video: Shown in Fixed Mode -->
                            <div class="col-md-4 d-none" id="fixedRewardCol">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-dark small mb-0">Fixed Bounty / Video ($ USD) <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="input-group" style="height: 44px;">
                                        <span class="input-group-text fw-bold bg-light" style="height: 44px; min-width: 44px; justify-content: center;">$</span>
                                        <input type="number" id="rewardPerVideoInput" name="reward_per_video" step="any" min="0.01" class="form-control fw-bold font-monospace text-dark" style="height: 44px; font-size: 15px;" value="{{ old('reward_per_video', '25') }}">
                                        <span class="input-group-text bg-light small text-muted">/ video</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Expected Views Per Video: Shown in Fixed Mode -->
                            <div class="col-md-4 d-none" id="expectedViewsCol">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-dark small mb-0">Expected Views / Video <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="input-group" style="height: 44px;">
                                        <input type="number" id="targetViewsPerVideoInput" name="target_views_per_video" min="1" step="1" class="form-control fw-bold font-monospace text-dark" style="height: 44px; font-size: 15px;" value="{{ old('target_views_per_video', '80000') }}">
                                        <span class="input-group-text bg-light small text-muted">views</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Estimated Target Reach: Common -->
                            <div class="col-md-4" id="targetViewsCol">
                                <div class="p-3 bg-white rounded-3 border h-100 shadow-xs">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold text-dark small mb-0" id="targetViewsLabel">Estimated Target Reach <span class="text-danger">*</span></label>
                                        <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size: 11px; height: 22px;" onclick="updateViewsFromCpm()" title="Recalculate automatically from budget & rate">
                                            <i class="las la-sync-alt me-0.5"></i> Auto
                                        </button>
                                    </div>
                                    <div class="input-group" style="height: 44px;">
                                        <input type="number" id="targetViewsInput" name="target_views" min="1" step="1" class="form-control fw-bold font-monospace text-primary" style="height: 44px; font-size: 15px;" required value="{{ old('target_views', '40000') }}">
                                        <span class="input-group-text bg-white text-primary fw-semibold small" id="targetViewsAddon" style="height: 44px; font-size: 11.5px;">Est. Views</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3.4 Multi-Country Per-Market Configuration (Only visible in Multi Mode) -->
                        <div id="multiCountryBox" style="display: none;">
                            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                <div>
                                    <label class="form-label fw-bold text-dark small mb-0">
                                        <i class="las la-flag text-primary me-1"></i> Target Countries & Per-Market CPM Rates
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 11.5px;">Select country presets below or add custom countries with their specific CPM reward rate.</small>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm px-3" style="height: 34px;" onclick="addCountryRow()">
                                    <i class="las la-plus me-1"></i> Add Custom Country
                                </button>
                            </div>

                            <!-- Quick Preset Country Chips with Real Flags -->
                            <div class="d-flex flex-wrap gap-1 mb-3" id="presetQuickBtns">
                                @foreach($presetCountries as $pc)
                                    <button type="button" 
                                        class="btn btn-sm btn-outline-secondary preset-quick-btn d-inline-flex align-items-center gap-1 px-2 py-1 bg-white" 
                                        style="font-size: 11.5px; height: 32px;"
                                        onclick="addPresetCountryRow('{{ $pc['code'] }}','{{ $pc['name'] }}',{{ $pc['default_cpm'] }},'https://flagcdn.com/20x15/{{ strtolower($pc['code']) }}.png')"
                                        data-code="{{ $pc['code'] }}">
                                        <img src="https://flagcdn.com/20x15/{{ strtolower($pc['code']) }}.png" width="20" height="15" class="rounded-1 border shadow-xs" alt="{{ $pc['code'] }}">
                                        <strong>{{ $pc['name'] }}</strong>
                                        <span class="text-success fw-bold">(${{ number_format($pc['default_cpm'], 2) }})</span>
                                    </button>
                                @endforeach
                            </div>

                            <!-- Multi-Country Header Row (Clean, No raw Code column for user) -->
                            <div id="multiCountryTableHead" class="d-none d-md-flex align-items-center gap-2 px-3 py-2 bg-light border rounded-top text-muted small fw-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <div style="flex: 1 1 240px;">Target Country &amp; Market</div>
                                <div style="width: 200px;">Reward Rate (CPM)</div>
                                <div style="width: 170px;" class="text-end">Est. Views Reach</div>
                                <div style="width: 44px;" class="text-center">Action</div>
                            </div>

                            <!-- Rows Container -->
                            <div id="multiCountryRows">
                                <!-- Dynamic country rows inserted here -->
                            </div>

                            <!-- Empty Notice -->
                            <div id="multiCountryEmpty" class="text-center text-muted py-3 border border-dashed rounded-3 small bg-light">
                                <i class="las la-map-marked-alt fs-2 text-secondary d-block mb-1"></i>
                                Click any country preset above or click <strong>+ Add Custom Country</strong> to define markets &amp; individual CPMs.
                            </div>
                        </div>

                    </div>

                    <!-- 4. Video & Assets Studio (Dual Choice: Direct Upload or Cloud URL) -->
                    <div class="card border rounded-3 p-3 mb-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <h6 class="fw-bold text-dark mb-0">
                                <i class="las la-video text-primary me-1"></i> Campaign Video & Creative Assets
                            </h6>
                        </div>

                        <!-- Pill Tabs -->
                        <ul class="nav nav-pills mb-3" id="videoSourceTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold btn-sm" id="upload-tab" data-bs-toggle="pill" data-bs-target="#tab-upload" type="button" role="tab">
                                    <i class="las la-cloud-upload-alt me-1"></i> Upload Video Directly (MP4 / WebM / MOV)
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold btn-sm" id="url-tab" data-bs-toggle="pill" data-bs-target="#tab-url" type="button" role="tab">
                                    <i class="las la-link me-1"></i> Provide Video / Cloud Assets Link
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="videoSourceTabContent">
                            <!-- Direct Video Upload Tab -->
                            <div class="tab-pane fade show active" id="tab-upload" role="tabpanel">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-7">
                                        <label class="form-label">Select Raw Footage Video File</label>
                                        <input type="file" name="video_file" id="videoFileInput" class="form-control" style="height: 44px;" accept="video/mp4,video/webm,video/quicktime,video/x-m4v">
                                        <small class="text-muted d-block mt-1">Supports MP4, WebM, MOV up to 100MB.</small>
                                    </div>
                                    <div class="col-md-5">
                                        <div id="videoUploadPreviewBox" class="rounded bg-light border p-2 text-center" style="display: none;">
                                            <video id="videoPreviewEl" controls class="w-100 rounded" style="max-height: 140px;"></video>
                                            <small class="d-block text-success fw-bold mt-1" id="videoFileNameDisplay"></small>
                                        </div>
                                        <div id="videoUploadPlaceholder" class="rounded bg-light border p-3 text-center text-muted">
                                            <i class="las la-play-circle fs-2 text-secondary"></i>
                                            <small class="d-block mt-1">Video preview will appear here upon selection</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- External URL Tab -->
                            <div class="tab-pane fade" id="tab-url" role="tabpanel">
                                <label class="form-label">Raw Footage / Assets Folder URL</label>
                                <div class="input-group" style="height: 44px;">
                                    <span class="input-group-text bg-white" style="height: 44px;"><i class="las la-link"></i></span>
                                    <input type="text" name="asset_url" id="assetUrlInput" class="form-control" style="height: 44px;" placeholder="https://youtube.com/watch?v=... or Google Drive / Dropbox link" value="{{ old('asset_url') }}" onblur="if(this.value && !this.value.match(/^https?:\/\//i)) { this.value = 'https://' + this.value; }">
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 border-light-subtle">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Campaign Expiration Date</label>
                                <input type="date" name="expires_at" class="form-control" style="height: 44px;" value="{{ old('expires_at', now()->addDays(30)->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <!-- 5. Eligible Social Platforms (Balanced Checkable Cards) -->
                    <div class="mb-4">
                        <label class="form-label d-block">Eligible Social Platforms <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="allowed_platforms[]" value="tiktok" id="platTiktok" checked>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-tiktok fs-5 text-dark"></i>
                                        <span class="fw-semibold small text-dark">TikTok</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="allowed_platforms[]" value="instagram" id="platInstagram" checked>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-instagram fs-5 text-danger"></i>
                                        <span class="fw-semibold small text-dark">Instagram</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="allowed_platforms[]" value="youtube" id="platYoutube" checked>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-youtube fs-5 text-danger"></i>
                                        <span class="fw-semibold small text-dark">YouTube</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="card h-100 p-3 border rounded-3 cursor-pointer d-flex flex-row align-items-center gap-2 m-0 bg-white shadow-none" style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0" type="checkbox" name="allowed_platforms[]" value="x" id="platX" checked>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fab fa-x-twitter fs-5 text-dark"></i>
                                        <span class="fw-semibold small text-dark">X (Twitter)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Funding Method Selection -->
                    @php
                        $walletBal = (float) $wallet->available_balance;
                        $initBudget = (float) old('total_budget', $defaultBudget ?? 100);
                        $walletEligible = $walletBal >= 1;
                        $isWalletDefault = old('funding_method') ? (old('funding_method') === 'wallet') : ($walletBal > 0);
                    @endphp

                    <div class="card border p-3 mb-4 rounded-3 bg-white">
                        <label class="form-label mb-2 fw-bold text-dark">
                            Select Campaign Payment Method <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card p-3 rounded-3 border h-100 bg-white m-0 cursor-pointer funding-method-card" id="cardFundWallet" style="cursor: pointer; {{ $isWalletDefault ? 'border-color: #198754 !important; background: #f0fdf4 !important;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="funding_method" id="fundWallet" value="wallet" {{ $isWalletDefault ? 'checked' : '' }} onchange="switchFundingMethod('wallet')">
                                        <div class="w-100">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-bold text-dark"><i class="las la-wallet text-success me-1"></i> Brand Wallet</span>
                                                <span class="fw-bold text-success font-monospace fs-6">${{ number_format($wallet->available_balance, 2) }}</span>
                                            </div>
                                            <div id="walletStatusFeedback" class="small mt-1" style="font-size: 11.5px;"></div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="card p-3 rounded-3 border h-100 bg-white m-0 cursor-pointer funding-method-card" id="cardFundStripe" style="cursor: pointer; {{ !$isWalletDefault ? 'border-color: #0d6efd !important; background: #f0f7ff !important;' : '' }}">
                                    <div class="d-flex align-items-center gap-2">
                                        <input class="form-check-input flex-shrink-0" type="radio" name="funding_method" id="fundStripe" value="stripe" {{ !$isWalletDefault ? 'checked' : '' }} onchange="switchFundingMethod('stripe')">
                                        <div class="w-100">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-bold text-dark"><i class="las la-credit-card text-primary me-1"></i> Stripe Checkout</span>
                                                <span class="badge bg-primary-subtle text-primary border">Card / Apple Pay</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Action Buttons (Strict Equal Height 44px) -->
                    <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                        <a href="{{ route('brand.campaigns') }}" class="btn btn-outline-secondary px-3" style="height: 44px; display: inline-flex; align-items: center;">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-brand px-4" style="height: 44px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="las la-rocket me-1"></i> Launch Campaign
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css"/>
<style>
    .country-mode-btn { transition: all 0.2s ease; border-color: #dee2e6 !important; }
    .country-mode-btn.active { border-color: #0d6efd !important; background: #eef4ff !important; color: #0d6efd !important; }
    .country-mode-btn.active span.text-dark { color: #0d6efd !important; }
    .country-mode-btn.active small { color: #0d6efd !important; }
    .country-mode-btn:hover { border-color: #86b7fe !important; background: #f8faff !important; }
    .border-dashed { border-style: dashed !important; }
    .preset-quick-btn { transition: all 0.15s ease; border-color: #dee2e6; }
    .preset-quick-btn:hover { border-color: #0d6efd; background: #f0f5ff; }
    .preset-quick-btn.used { opacity: 0.45; pointer-events: none; background: #f8f9fa !important; border-color: #ced4da !important; }
    .multi-country-row { border: 1px solid #dee2e6; border-radius: 8px; padding: 10px 14px; background: #ffffff; margin-bottom: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: all 0.2s ease; }
    .multi-country-row:hover { background: #f8faff; border-color: #86b7fe; }
</style>
<script>
    // ─── Unified Geo-Targeting & CPM Calculations ─────────────────
    let multiRowIndex = 0;
    const addedCodes = new Set();
    let currentPayoutModel = 'cpm';

    const budgetInput = document.getElementById('budgetInput');
    const cpmInput = document.getElementById('cpmInput');
    const targetViewsInput = document.getElementById('targetViewsInput');
    const cpmCardCol = document.getElementById('cpmCardCol');
    const fixedRewardCol = document.getElementById('fixedRewardCol');
    const expectedViewsCol = document.getElementById('expectedViewsCol');
    const rewardPerVideoInput = document.getElementById('rewardPerVideoInput');
    const targetViewsPerVideoInput = document.getElementById('targetViewsPerVideoInput');
    const targetViewsAddon = document.getElementById('targetViewsAddon');
    const budgetCol = document.getElementById('budgetCol');
    const targetViewsCol = document.getElementById('targetViewsCol');
    const cpmFieldLabel = document.getElementById('cpmFieldLabel');
    const cpmSubtext = document.getElementById('cpmSubtext');
    const targetViewsLabel = document.getElementById('targetViewsLabel');
    const targetViewsSubtext = document.getElementById('targetViewsSubtext');

    function switchPayoutModel(model) {
        currentPayoutModel = model;
        const cardCpm = document.getElementById('cardModelCpm');
        const cardFixed = document.getElementById('cardModelFixed');

        if (model === 'fixed_per_video') {
            cardFixed.classList.add('active');
            cardFixed.style.borderColor = '#0d6efd';
            cardFixed.style.background = '#f0f7ff';
            cardCpm.classList.remove('active');
            cardCpm.style.borderColor = '';
            cardCpm.style.background = '#ffffff';

            cpmCardCol.classList.add('d-none');
            fixedRewardCol.classList.remove('d-none');
            expectedViewsCol.classList.remove('d-none');

            targetViewsLabel.innerHTML = '<span>Estimated Approved Videos & Reach <span class="text-danger">*</span></span>';
            if (targetViewsSubtext) targetViewsSubtext.textContent = 'Total approved videos and collective audience reach';
            updateFixedCalculations();
        } else {
            cardCpm.classList.add('active');
            cardCpm.style.borderColor = '#0d6efd';
            cardCpm.style.background = '#f0f7ff';
            cardFixed.classList.remove('active');
            cardFixed.style.borderColor = '';
            cardFixed.style.background = '#ffffff';

            fixedRewardCol.classList.add('d-none');
            expectedViewsCol.classList.add('d-none');
            cpmCardCol.classList.remove('d-none');

            targetViewsLabel.innerHTML = '<span>Estimated Target Reach <span class="text-danger">*</span></span>';
            if (targetViewsSubtext) targetViewsSubtext.textContent = 'Calculated as (Budget / CPM) × 1,000 (editable)';
            if (targetViewsAddon) targetViewsAddon.textContent = 'Est. Views';
            updateViewsFromCpm();
        }
    }

    function updateFixedCalculations() {
        if (currentPayoutModel !== 'fixed_per_video') return;
        const b = parseFloat(budgetInput.value) || 0;
        const r = parseFloat(rewardPerVideoInput?.value) || 0;
        const exp = parseInt(targetViewsPerVideoInput?.value) || 0;

        if (r > 0 && b > 0) {
            const vids = Math.floor(b / r);
            const totalViews = vids * exp;
            targetViewsInput.value = totalViews > 0 ? totalViews : vids;
            if (targetViewsSubtext) {
                targetViewsSubtext.innerHTML = `<strong>${vids} videos</strong> @ $${r}/video &times; ${exp >= 1000 ? (exp/1000) + 'k' : exp} views = <strong>${totalViews.toLocaleString()} views</strong>`;
            }
        }
    }

    if (rewardPerVideoInput) rewardPerVideoInput.addEventListener('input', updateFixedCalculations);
    if (targetViewsPerVideoInput) targetViewsPerVideoInput.addEventListener('input', updateFixedCalculations);

    const walletBalance = {{ (float)$wallet->available_balance }};

    function useWalletBalance(amt) {
        budgetInput.value = amt;
        updateViewsFromCpm();
        syncWalletHelper();
    }

    function switchFundingMethod(method) {
        const cardW = document.getElementById('cardFundWallet');
        const cardS = document.getElementById('cardFundStripe');
        if (method === 'wallet') {
            if (cardW) { cardW.style.borderColor = '#198754'; cardW.style.background = '#f0fdf4'; }
            if (cardS) { cardS.style.borderColor = ''; cardS.style.background = '#ffffff'; }
        } else {
            if (cardS) { cardS.style.borderColor = '#0d6efd'; cardS.style.background = '#f0f7ff'; }
            if (cardW) { cardW.style.borderColor = ''; cardW.style.background = '#ffffff'; }
        }
        syncWalletHelper();
    }

    function syncWalletHelper() {
        const b = parseFloat(budgetInput.value) || 0;
        const feedback = document.getElementById('walletStatusFeedback');
        if (!feedback) return;

        if (b > walletBalance) {
            feedback.innerHTML = `<span class="text-danger"><i class="las la-exclamation-circle me-1"></i> Budget ($${b.toFixed(2)}) exceeds wallet ($${walletBalance.toFixed(2)}). Select Stripe Checkout.</span>`;
        } else {
            feedback.innerHTML = '';
        }
    }

    // Auto-Calculate Target Views from Budget & CPM smoothly
    function updateViewsFromCpm() {
        if (currentPayoutModel === 'fixed_per_video') {
            updateFixedCalculations();
            syncWalletHelper();
            return;
        }
        const b = parseFloat(budgetInput.value) || 0;
        const c = parseFloat(cpmInput.value) || 0;
        if (c > 0 && b > 0) {
            const views = Math.round((b / c) * 1000);
            targetViewsInput.value = views;
        } else if (b === 0 || c === 0) {
            targetViewsInput.value = "0";
        }

        // Sync single country CPM hidden input
        const scHidden = document.getElementById('singleCountryCpm');
        if (scHidden) scHidden.value = cpmInput.value;

        // Recalculate all multi-country row reaches
        document.querySelectorAll('[id^="mcCpm_"]').forEach(input => {
            const idx = input.id.replace('mcCpm_', '');
            updateRowReach(idx);
        });

        syncWalletHelper();
    }

    // User can freely edit targetViewsInput without overwriting CPM!
    budgetInput.addEventListener('input', updateViewsFromCpm);
    cpmInput.addEventListener('input', updateViewsFromCpm);
    setTimeout(syncWalletHelper, 150);

    function switchCountryMode(mode) {
        document.querySelectorAll('.country-mode-btn').forEach(el => el.classList.remove('active'));
        const singleBox = document.getElementById('singleCountrySelectorBox');
        const multiBox = document.getElementById('multiCountryBox');

        if (mode === 'worldwide') {
            document.getElementById('modeWorldwideLabel').classList.add('active');
            singleBox.style.display = 'none';
            multiBox.style.display = 'none';
            cpmCardCol.classList.remove('d-none');
            targetViewsCol.classList.remove('d-none');
            cpmCardCol.style.display = '';
            targetViewsCol.style.display = '';
            budgetCol.className = 'col-md-4';
            cpmCardCol.className = 'col-md-4';
            targetViewsCol.className = 'col-md-4';
            cpmFieldLabel.innerHTML = '<span>Global Reward Rate (CPM / 1k Views) <span class="text-danger">*</span></span>';
            if (cpmSubtext) cpmSubtext.textContent = 'Bounty per 1,000 verified views across all countries';
            targetViewsLabel.innerHTML = '<span>Estimated Target Reach <span class="text-danger">*</span></span>';
            if (targetViewsSubtext) targetViewsSubtext.textContent = 'Calculated as (Budget / CPM) × 1,000 (editable)';
            updateViewsFromCpm();
        } else if (mode === 'single') {
            document.getElementById('modeSingleLabel').classList.add('active');
            singleBox.style.display = 'block';
            multiBox.style.display = 'none';
            cpmCardCol.classList.remove('d-none');
            targetViewsCol.classList.remove('d-none');
            cpmCardCol.style.display = '';
            targetViewsCol.style.display = '';
            budgetCol.className = 'col-md-4';
            cpmCardCol.className = 'col-md-4';
            targetViewsCol.className = 'col-md-4';
            cpmFieldLabel.innerHTML = '<span>Target Country CPM ($ / 1k Views) <span class="text-danger">*</span></span>';
            if (cpmSubtext) cpmSubtext.textContent = 'Bounty rate specifically applied to selected target market';
            targetViewsLabel.innerHTML = '<span>Estimated Target Reach <span class="text-danger">*</span></span>';
            if (targetViewsSubtext) targetViewsSubtext.textContent = 'Calculated as (Budget / CPM) × 1,000 (editable)';
            updateViewsFromCpm();
        } else {
            // Multi-Country Mode:
            // Top row ONLY shows Total Budget ($ USD).
            // Global Reward Rate (CPM) and Target Views are COMPLETELY HIDDEN because each country below has its own CPM and Views!
            document.getElementById('modeMultiLabel').classList.add('active');
            singleBox.style.display = 'none';
            multiBox.style.display = 'block';
            cpmCardCol.classList.add('d-none');
            targetViewsCol.classList.add('d-none');
            budgetCol.className = 'col-12';
            updateMultiEmpty();
        }
    }

    // Wire mode selection clicks
    document.querySelectorAll('input[name="country_targeting_type"]').forEach(radio => {
        radio.addEventListener('change', function() { switchCountryMode(this.value); });
    });

    document.getElementById('modeWorldwideLabel').addEventListener('click', () => {
        document.getElementById('modeWorldwide').checked = true;
        switchCountryMode('worldwide');
    });
    document.getElementById('modeSingleLabel').addEventListener('click', () => {
        document.getElementById('modeSingle').checked = true;
        switchCountryMode('single');
    });
    document.getElementById('modeMultiLabel').addEventListener('click', () => {
        document.getElementById('modeMultiple').checked = true;
        switchCountryMode('multiple');
    });

    // Single Country: Dropdown Selection with Flag Update
    function fillSingleCountry(sel) {
        const opt = sel.options[sel.selectedIndex];
        const customInputs = document.getElementById('singleCustomInputsRow');
        const flagImg = document.getElementById('singleCountryFlagImg');
        const nameDisplay = document.getElementById('singleCountryNameDisplay');
        const codeInput = document.getElementById('singleCountryCode');
        const nameInput = document.getElementById('singleCountryName');

        if (!opt.value || opt.value === '__custom__') {
            customInputs.style.display = 'flex';
            flagImg.src = 'https://flagcdn.com/24x18/un.png';
            nameDisplay.textContent = 'Custom Country Market';
            codeInput.value = 'US';
            nameInput.value = '';
            return;
        }

        customInputs.style.display = 'none';
        const codeLower = opt.value.toLowerCase();
        flagImg.src = `https://flagcdn.com/24x18/${codeLower}.png`;
        nameDisplay.textContent = opt.dataset.name;
        codeInput.value = opt.value;
        nameInput.value = opt.dataset.name;

        // Auto-set preset CPM rate for this country
        if (opt.dataset.cpm) {
            cpmInput.value = parseFloat(opt.dataset.cpm).toFixed(2);
            document.getElementById('singleCountryCpm').value = cpmInput.value;
            updateViewsFromCpm();
        }
    }

    // Multi-Country: Check empty rows
    function updateMultiEmpty() {
        const rows = document.getElementById('multiCountryRows');
        document.getElementById('multiCountryEmpty').style.display =
            rows.children.length === 0 ? 'block' : 'none';
    }

    // Multi-Country: Add row (Spacious CPM Field, Editable Views, No visible Code input)
    function addCountryRow(code, name, cpm, flagUrl) {
        const i = multiRowIndex++;
        const container = document.getElementById('multiCountryRows');
        const div = document.createElement('div');
        div.className = 'multi-country-row p-2 border rounded-2 bg-white mb-2 shadow-xs';
        div.id = 'mcRow_' + i;
        div.dataset.code = code || '';

        const codeVal = (code || 'US').toUpperCase();
        const codeLower = (code || 'un').toLowerCase();
        const flagSrc = flagUrl || `https://flagcdn.com/24x18/${codeLower}.png`;
        const cpmVal = cpm ? parseFloat(cpm).toFixed(2) : (parseFloat(cpmInput.value) || 2.50).toFixed(2);
        const budgetVal = parseFloat(budgetInput.value) || 0;
        const initialReach = (budgetVal > 0 && parseFloat(cpmVal) > 0) ? Math.round((budgetVal / parseFloat(cpmVal)) * 1000) : 100000;

        div.innerHTML = `
            <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
                <!-- Target Country Name & Flag (Code is stored in hidden input) -->
                <div class="d-flex align-items-center gap-2" style="flex: 1 1 240px; min-width: 180px;">
                    <img src="${flagSrc}" width="24" height="18" class="rounded-1 border shadow-xs flex-shrink-0" id="mcFlag_${i}" alt="${codeVal}">
                    <input type="text" name="countries[${i}][name]" class="form-control fw-semibold" style="height: 42px;"
                        placeholder="Country Name (e.g. United States)" value="${name || ''}">
                    <input type="hidden" name="countries[${i}][code]" id="mcCode_${i}" value="${codeVal}">
                </div>

                <!-- Reward Rate CPM Input (Spacious, bold, clear) -->
                <div style="width: 200px;" class="flex-shrink-0">
                    <div class="input-group" style="height: 42px;">
                        <span class="input-group-text fw-bold bg-light px-2" style="height: 42px;">$</span>
                        <input type="number" name="countries[${i}][cpm]" id="mcCpm_${i}" step="any" min="0.01"
                            class="form-control font-monospace fw-bold text-end text-dark" style="height: 42px;"
                            placeholder="2.50" value="${cpmVal}" oninput="updateRowReach(${i})"
                            title="CPM rate for ${name || codeVal}">
                        <span class="input-group-text text-muted px-2" style="height: 42px; font-size:11.5px;">/1k</span>
                    </div>
                </div>

                <!-- Target Est. Reach (Editable input per country) -->
                <div style="width: 170px;" class="flex-shrink-0">
                    <div class="input-group" style="height: 42px;">
                        <input type="number" name="countries[${i}][target_views]" id="mcReach_${i}" step="1" min="1"
                            class="form-control font-monospace fw-bold text-primary text-end" style="height: 42px;"
                            value="${initialReach}" placeholder="Views" title="Target Views for ${name || codeVal}">
                        <span class="input-group-text text-muted px-1 small" style="height: 42px; font-size: 10px;">Views</span>
                    </div>
                </div>

                <!-- Remove Button -->
                <div style="width: 44px;" class="flex-shrink-0 text-end">
                    <button type="button" class="btn btn-outline-danger d-flex align-items-center justify-content-center" style="height: 42px; width: 42px;" title="Remove Country" onclick="removeCountryRow('mcRow_${i}','${codeVal}')">
                        <i class="las la-trash-alt fs-5"></i>
                    </button>
                </div>
            </div>`;

        container.appendChild(div);
        updateMultiEmpty();
    }

    function updateRowReach(i) {
        const budgetVal = parseFloat(budgetInput.value) || 0;
        const cpmEl = document.getElementById('mcCpm_' + i);
        const reachEl = document.getElementById('mcReach_' + i);
        if (!cpmEl || !reachEl) return;
        const cpmVal = parseFloat(cpmEl.value) || 0;
        if (budgetVal > 0 && cpmVal > 0) {
            reachEl.value = Math.round((budgetVal / cpmVal) * 1000);
        }
    }

    // Multi-Country: Quick preset button clicked
    function addPresetCountryRow(code, name, cpm, flagUrl) {
        if (addedCodes.has(code)) return;
        addedCodes.add(code);
        document.querySelectorAll(`.preset-quick-btn[data-code="${code}"]`).forEach(b => b.classList.add('used'));
        addCountryRow(code, name, cpm, flagUrl);
    }

    // Multi-Country: Remove row
    function removeCountryRow(rowId, code) {
        const el = document.getElementById(rowId);
        if (el) el.remove();
        if (code) {
            addedCodes.delete(code);
            document.querySelectorAll(`.preset-quick-btn[data-code="${code}"]`).forEach(b => b.classList.remove('used'));
        }
        updateMultiEmpty();
    }

    // ─── Initial Page Load Setup ──────────────────────────────────
    document.addEventListener("DOMContentLoaded", function () {
        switchCountryMode('worldwide');
        updateViewsFromCpm();

        // Video upload live preview
        const videoInput = document.getElementById('videoFileInput');
        const previewBox = document.getElementById('videoUploadPreviewBox');
        const placeholder = document.getElementById('videoUploadPlaceholder');
        const videoEl = document.getElementById('videoPreviewEl');
        const nameDisplay = document.getElementById('videoFileNameDisplay');

        if (videoInput) {
            videoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const url = URL.createObjectURL(file);
                    videoEl.src = url;
                    nameDisplay.textContent = 'Ready: ' + file.name + ' (' + (file.size / 1048576).toFixed(1) + ' MB)';
                    previewBox.style.display = 'block';
                    placeholder.style.display = 'none';
                } else {
                    previewBox.style.display = 'none';
                    placeholder.style.display = 'block';
                }
            });
        }
    });
</script>
@endpush
