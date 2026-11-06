@extends('admin.layouts.app')

@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card b-radius--10 border">
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark"><i class="las la-edit text-primary me-1"></i> Edit Campaign Protocol</h5>
                    <small class="text-muted">Campaign #{{ $campaign->id }} &bull; Brand: <strong>{{ $campaign->brand->company_name ?? 'N/A' }}</strong></small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('web.campaign.detail', $campaign->id) }}" target="_blank" class="btn btn-sm btn-outline-info">
                        <i class="las la-external-link-alt"></i> View Public Page
                    </a>
                    <a href="{{ route('admin.campaigns') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="las la-arrow-left"></i> Back to Campaigns
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('admin.campaigns.update', $campaign->id) }}" method="POST">
                    @csrf

                    <div class="row gy-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold">Campaign Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $campaign->title) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @php
                                    $cats = ['Technology & AI', 'Gaming & Esports', 'E-commerce & DTC', 'Crypto & Web3', 'Lifestyle & Fashion', 'Finance & SaaS', 'Health & Fitness'];
                                @endphp
                                @foreach($cats as $cat)
                                    <option value="{{ $cat }}" {{ $campaign->category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Source Video / Assets URL <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="las la-link"></i></span>
                                <input type="url" name="source_video_url" class="form-control" value="{{ old('source_video_url', $campaign->source_video_url) }}" placeholder="https://drive.google.com/... or YouTube link" required>
                            </div>
                            <small class="text-muted">Direct link where clippers download source footage and creative assets.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="adminStatusSelect" class="form-select fw-semibold" required onchange="onAdminStatusChange(this.value)">
                                <option value="active" {{ $campaign->status === 'active' ? 'selected' : '' }}>🟢 Active (Live on Marketplace - Open)</option>
                                <option value="half_filled" {{ $campaign->status === 'half_filled' ? 'selected' : '' }}>⚡ Half Filled (Live - ~50% Claimed / Highlighted)</option>
                                <option value="completed" {{ $campaign->status === 'completed' ? 'selected' : '' }}>🏁 Completed (100% Claimed / Budget Exhausted)</option>
                                <option value="paused" {{ $campaign->status === 'paused' ? 'selected' : '' }}>⏸️ Paused (Temporarily on hold)</option>
                                <option value="cancelled" {{ $campaign->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                <option value="draft" {{ $campaign->status === 'draft' ? 'selected' : '' }}>📝 Draft</option>
                            </select>
                            <small class="text-muted">Use <strong>Half Filled</strong> to highlight ~50% claimed pool on marketplace & bounties list.</small>
                        </div>

                        <!-- Interactive Reward Pool & Claimed Budget Manager -->
                        <div class="col-12 my-2">
                            <div class="card border bg-light shadow-none p-3" style="border-radius: 8px;">
                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="las la-coins text-warning"></i> Campaign Reward Pool & Claimed Budget Control</h6>
                                        <small class="text-muted">Adjust total pool, claimed amount, or remaining budget. You can also click the quick presets below.</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        <span class="small fw-bold text-muted me-1">Quick Presets:</span>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPoolClaimedPercent(0)">0% Fresh</button>
                                        <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPoolClaimedPercent(25)">25% Claimed</button>
                                        <button type="button" class="btn btn-xs btn-outline-warning fw-bold" onclick="setPoolClaimedPercent(50, true)">⚡ 50% Half Filled</button>
                                        <button type="button" class="btn btn-xs btn-outline-info" onclick="setPoolClaimedPercent(75)">75% Claimed</button>
                                        <button type="button" class="btn btn-xs btn-outline-dark fw-bold" onclick="setPoolClaimedPercent(100, false, true)">🏁 100% Complete</button>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Total Budget ($ USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold">$</span>
                                            <input type="number" step="0.01" min="1.00" name="total_budget" id="adminTotalBudget" class="form-control font-monospace fw-bold" value="{{ old('total_budget', $campaign->total_budget) }}" required oninput="recalcFromTotal()">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Pool Claimed / Spent ($ USD)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold text-warning">$</span>
                                            @php
                                                $initialClaimed = max(0, (float)$campaign->total_budget - (float)$campaign->remaining_budget);
                                            @endphp
                                            <input type="number" step="0.01" min="0.00" id="adminClaimedBudget" class="form-control font-monospace fw-bold text-warning" value="{{ number_format($initialClaimed, 2, '.', '') }}" oninput="recalcFromClaimed()">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted text-uppercase mb-1">Remaining Budget ($ USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white fw-bold text-success">$</span>
                                            <input type="number" step="0.01" min="0.00" name="remaining_budget" id="adminRemainingBudget" class="form-control font-monospace fw-bold text-success" value="{{ old('remaining_budget', $campaign->remaining_budget) }}" required oninput="recalcFromRemaining()">
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar Indicator -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between small fw-bold mb-1">
                                        <span class="text-warning" id="poolClaimedLabel">Claimed: ${{ number_format($initialClaimed, 2) }}</span>
                                        <span class="text-muted" id="poolPercentLabel">50% Claimed</span>
                                        <span class="text-success" id="poolRemainingLabel">Remaining: ${{ number_format($campaign->remaining_budget, 2) }}</span>
                                    </div>
                                    <div class="progress" style="height: 10px; background-color: #e2e8f0; border-radius: 6px;">
                                        <div id="poolClaimedBar" class="progress-bar bg-warning" role="progressbar" style="width: 50%;"></div>
                                        <div id="poolRemainingBar" class="progress-bar bg-success" role="progressbar" style="width: 50%;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Payout Model</label>
                            <select name="payout_model" id="adminPayoutModel" class="form-select" onchange="toggleAdminPayoutModel(this.value)">
                                <option value="cpm" {{ ($campaign->payout_model ?? 'cpm') === 'cpm' ? 'selected' : '' }}>Performance CPM ($ per 1k views)</option>
                                <option value="fixed_per_video" {{ ($campaign->payout_model ?? '') === 'fixed_per_video' ? 'selected' : '' }}>Fixed Pay Per Video Bounty</option>
                            </select>
                        </div>

                        <div class="col-md-4" id="adminCpmRateBox">
                            <label class="form-label fw-bold">Reward Rate ($ per 1,000 Views)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.10" name="reward_rate_per_thousand" class="form-control font-monospace" value="{{ old('reward_rate_per_thousand', $campaign->reward_rate_per_thousand ?? 2.50) }}">
                            </div>
                        </div>

                        <div class="col-md-4" id="adminFixedRewardBox">
                            <label class="form-label fw-bold">Fixed Reward ($ per Video)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="1.00" name="reward_per_video" class="form-control font-monospace" value="{{ old('reward_per_video', $campaign->reward_per_video ?? 25.00) }}">
                            </div>
                        </div>

                        <div class="col-md-4" id="adminExpectedViewsBox">
                            <label class="form-label fw-bold">Expected Views (per Video Target)</label>
                            <input type="number" min="1000" step="1000" name="target_views_per_video" class="form-control font-monospace" value="{{ old('target_views_per_video', $campaign->target_views_per_video ?? 80000) }}" placeholder="e.g. 80000">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Target Views</label>
                            <input type="number" name="target_views" class="form-control font-monospace" value="{{ old('target_views', $campaign->target_views) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-bold">Verified Views Tracked</label>
                            <input type="number" name="verified_views" class="form-control font-monospace" value="{{ old('verified_views', $campaign->verified_views) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="date" name="start_at" class="form-control" value="{{ old('start_at', $campaign->start_at ? $campaign->start_at->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date</label>
                            <input type="date" name="end_at" class="form-control" value="{{ old('end_at', $campaign->end_at ? $campaign->end_at->format('Y-m-d') : '') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Briefing & Requirements Description</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Specify instructions for creators, hooks, hashtags, and requirements...">{{ old('description', $campaign->description) }}</textarea>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="las la-save"></i> Save Campaign Changes
                            </button>
                            <a href="{{ route('admin.campaigns') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
function toggleAdminPayoutModel(model) {
    const isFixed = (model === 'fixed_per_video');
    const cpmBox = document.getElementById('adminCpmRateBox');
    const fixedBox = document.getElementById('adminFixedRewardBox');
    const viewsBox = document.getElementById('adminExpectedViewsBox');
    if (cpmBox) cpmBox.style.display = isFixed ? 'none' : 'block';
    if (fixedBox) fixedBox.style.display = isFixed ? 'block' : 'none';
    if (viewsBox) viewsBox.style.display = isFixed ? 'block' : 'none';
}

function updatePoolProgressUI(total, remaining, claimed) {
    total = Math.max(0.01, total);
    remaining = Math.max(0, Math.min(total, remaining));
    claimed = Math.max(0, Math.min(total, claimed));

    const claimedPct = Math.min(100, Math.max(0, Math.round((claimed / total) * 100)));
    const remainingPct = 100 - claimedPct;

    const claimedBar = document.getElementById('poolClaimedBar');
    const remBar = document.getElementById('poolRemainingBar');
    const claimedLbl = document.getElementById('poolClaimedLabel');
    const remLbl = document.getElementById('poolRemainingLabel');
    const pctLbl = document.getElementById('poolPercentLabel');

    if (claimedBar) claimedBar.style.width = claimedPct + '%';
    if (remBar) remBar.style.width = remainingPct + '%';
    if (claimedLbl) claimedLbl.innerText = 'Claimed: $' + claimed.toFixed(2);
    if (remLbl) remLbl.innerText = 'Remaining: $' + remaining.toFixed(2);
    if (pctLbl) pctLbl.innerText = claimedPct + '% Claimed (' + remainingPct + '% Available)';
}

function recalcFromTotal() {
    const total = parseFloat(document.getElementById('adminTotalBudget')?.value) || 0;
    const remaining = parseFloat(document.getElementById('adminRemainingBudget')?.value) || 0;
    const claimedInput = document.getElementById('adminClaimedBudget');
    const claimed = Math.max(0, total - remaining);
    if (claimedInput) claimedInput.value = claimed.toFixed(2);
    updatePoolProgressUI(total, remaining, claimed);
}

function recalcFromClaimed() {
    const total = parseFloat(document.getElementById('adminTotalBudget')?.value) || 0;
    const claimed = parseFloat(document.getElementById('adminClaimedBudget')?.value) || 0;
    const remInput = document.getElementById('adminRemainingBudget');
    const remaining = Math.max(0, total - claimed);
    if (remInput) remInput.value = remaining.toFixed(2);
    updatePoolProgressUI(total, remaining, claimed);
}

function recalcFromRemaining() {
    const total = parseFloat(document.getElementById('adminTotalBudget')?.value) || 0;
    const remaining = parseFloat(document.getElementById('adminRemainingBudget')?.value) || 0;
    const claimedInput = document.getElementById('adminClaimedBudget');
    const claimed = Math.max(0, total - remaining);
    if (claimedInput) claimedInput.value = claimed.toFixed(2);
    updatePoolProgressUI(total, remaining, claimed);
}

function setPoolClaimedPercent(pct, setHalfFilled = false, setCompleted = false) {
    const totalInput = document.getElementById('adminTotalBudget');
    let total = parseFloat(totalInput?.value) || 1000;
    if (total <= 0) {
        total = 1000;
        if (totalInput) totalInput.value = '1000.00';
    }

    const claimed = (total * pct) / 100;
    const remaining = Math.max(0, total - claimed);

    const claimedInput = document.getElementById('adminClaimedBudget');
    const remInput = document.getElementById('adminRemainingBudget');
    const statusSelect = document.getElementById('adminStatusSelect');

    if (claimedInput) claimedInput.value = claimed.toFixed(2);
    if (remInput) remInput.value = remaining.toFixed(2);

    if (setHalfFilled && statusSelect) {
        statusSelect.value = 'half_filled';
    } else if (setCompleted && statusSelect) {
        statusSelect.value = 'completed';
    } else if (pct === 0 && statusSelect && (statusSelect.value === 'completed' || statusSelect.value === 'half_filled')) {
        statusSelect.value = 'active';
    }

    updatePoolProgressUI(total, remaining, claimed);
}

function onAdminStatusChange(status) {
    if (status === 'half_filled') {
        const total = parseFloat(document.getElementById('adminTotalBudget')?.value) || 0;
        const remaining = parseFloat(document.getElementById('adminRemainingBudget')?.value) || 0;
        // If remaining is 100%, suggest 50%
        if (remaining >= total && total > 0) {
            setPoolClaimedPercent(50);
        }
    } else if (status === 'completed') {
        setPoolClaimedPercent(100);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('adminPayoutModel');
    if (sel) toggleAdminPayoutModel(sel.value);

    const total = parseFloat(document.getElementById('adminTotalBudget')?.value) || 0;
    const rem = parseFloat(document.getElementById('adminRemainingBudget')?.value) || 0;
    const claimed = Math.max(0, total - rem);
    const claimedInput = document.getElementById('adminClaimedBudget');
    if (claimedInput) claimedInput.value = claimed.toFixed(2);
    updatePoolProgressUI(total, rem, claimed);
});
</script>
@endpush
