@extends('streamer.layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fab fa-youtube text-danger me-2"></i> Submit YouTube Video Promotion
                </h6>
                <a href="{{ route('streamer.submissions') }}" class="btn btn-outline-secondary btn-sm">
                    &larr; View All Submissions
                </a>
            </div>

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li class="small">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('streamer.submissions.store') }}" method="POST">
                    @csrf

                    <!-- 1. Select Campaign -->
                    <div class="mb-4">
                        <label class="form-label">Sponsorship Campaign <span class="text-danger">*</span></label>
                        <select name="campaign_id" id="campaignSelector" class="form-select" required>
                            <option value="">-- Choose Brand Sponsorship Deal --</option>
                            @foreach($campaigns as $camp)
                                <option value="{{ $camp->id }}" 
                                    data-cpm="{{ $camp->reward_rate_per_thousand }}"
                                    data-brand="{{ $camp->brand->company_name ?? 'Advertiser' }}"
                                    {{ (old('campaign_id') == $camp->id || ($selectedCampaign && $selectedCampaign->id == $camp->id)) ? 'selected' : '' }}>
                                    {{ $camp->title }} (CPM: ${{ number_format($camp->reward_rate_per_thousand, 2) }}/1k views) — {{ $camp->brand->company_name ?? 'GEZUX' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Select which brand deal this video was created for.</small>
                    </div>

                    <!-- 2. YouTube Video URL -->
                    <div class="mb-3">
                        <label class="form-label">Published YouTube Video URL <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fab fa-youtube text-danger fs-5"></i></span>
                            <input 
                                type="url" 
                                name="post_url" 
                                id="youtubeUrlInput" 
                                value="{{ old('post_url') }}" 
                                placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..." 
                                class="form-control" 
                                required
                            >
                        </div>
                        <small class="text-muted">Accepts standard YouTube videos, YouTube Shorts, Livestreams, or VODs.</small>
                    </div>

                    <!-- Live YouTube Video Preview Box (Dynamic) -->
                    <div id="youtubePreviewContainer" class="p-3 rounded border mb-4 d-none" style="background: #fafaf9;">
                        <span class="d-block small text-muted fw-bold text-uppercase mb-2" style="font-size: 11px;">
                            <i class="las la-check-circle text-success me-1"></i> Detected YouTube Video
                        </span>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <img id="youtubeThumbPreview" src="" alt="Thumbnail" class="rounded border shadow-sm" style="width: 140px; height: 80px; object-fit: cover;">
                            <div>
                                <span class="badge bg-danger mb-1"><i class="fab fa-youtube me-1"></i> YouTube Video ID: <span id="youtubeIdBadge" class="font-monospace"></span></span>
                                <p class="mb-0 text-dark small fw-semibold">Video confirmed! Ready for brand review and payout tracking.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Streamer Channel Info -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label class="form-label">Your YouTube Channel Name</label>
                            <input 
                                type="text" 
                                name="channel_name" 
                                value="{{ old('channel_name', $youtubeAccount->account_name ?? Auth::user()->name) }}" 
                                placeholder="e.g. Asad Streams Tech" 
                                class="form-control"
                            >
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">YouTube Channel Handle</label>
                            <input 
                                type="text" 
                                name="channel_handle" 
                                value="{{ old('channel_handle', $youtubeAccount->handle ?? '@' . Str::slug(Auth::user()->name)) }}" 
                                placeholder="@yourhandle" 
                                class="form-control"
                            >
                        </div>
                    </div>

                    <!-- 4. Notes / Timestamp of Sponsor Segment -->
                    <div class="mb-4">
                        <label class="form-label">Sponsorship Timestamp / Notes (Optional)</label>
                        <textarea 
                            name="notes" 
                            rows="2" 
                            placeholder="e.g. Sponsor segment at 02:45 to 04:10 with promo code in description." 
                            class="form-control"
                        >{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('streamer.submissions') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-streamer text-white fw-bold px-4">
                            <i class="las la-paper-plane me-1"></i> Submit Video for Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function parseYouTubeId(url) {
        var regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|shorts\/|live\/)([^#\&\?]*).*/;
        var match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    document.getElementById('youtubeUrlInput').addEventListener('input', function() {
        var url = this.value.trim();
        var videoId = parseYouTubeId(url);
        var container = document.getElementById('youtubePreviewContainer');
        var thumb = document.getElementById('youtubeThumbPreview');
        var badge = document.getElementById('youtubeIdBadge');

        if (videoId) {
            container.classList.remove('d-none');
            thumb.src = 'https://img.youtube.com/vi/' + videoId + '/mqdefault.jpg';
            badge.textContent = videoId;
        } else {
            container.classList.add('d-none');
        }
    });

    // Check on page load if old value exists
    if (document.getElementById('youtubeUrlInput').value.trim() !== '') {
        document.getElementById('youtubeUrlInput').dispatchEvent(new Event('input'));
    }
</script>
@endpush
