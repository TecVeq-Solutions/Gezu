@extends('streamer.layouts.master')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fab fa-youtube text-danger me-2"></i> Connected YouTube Channel
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">
                    Connect and verify your primary YouTube channel where you publish brand promotion segments, videos, and live streams. This helps advertisers verify your channel identity and views reach.
                </p>

                @if($youtubeAccount && $youtubeAccount->is_verified)
                    <div class="p-3 rounded bg-light border mb-4 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 24px;">
                                <i class="fab fa-youtube"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $youtubeAccount->account_name }}</h6>
                                <span class="small text-muted font-monospace">{{ $youtubeAccount->handle }}</span>
                                @if($youtubeAccount->profile_url)
                                    <a href="{{ $youtubeAccount->profile_url }}" target="_blank" class="d-block small text-danger">
                                        {{ $youtubeAccount->profile_url }} <i class="las la-external-link-alt"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5"><i class="las la-check-circle"></i> Connected &amp; Verified</span>
                    </div>
                @endif

                <form action="{{ route('streamer.channels') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">YouTube Channel Name <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            name="channel_name" 
                            value="{{ old('channel_name', $youtubeAccount->account_name ?? $user->name) }}" 
                            placeholder="e.g. Asad Tech Reviews &amp; Gaming" 
                            class="form-control" 
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">YouTube Channel Handle <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="fab fa-youtube text-danger"></i></span>
                            <input 
                                type="text" 
                                name="channel_handle" 
                                value="{{ old('channel_handle', $youtubeAccount->handle ?? '@' . Str::slug($user->name)) }}" 
                                placeholder="@YourChannelHandle" 
                                class="form-control" 
                                required
                            >
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">YouTube Channel URL</label>
                        <input 
                            type="url" 
                            name="channel_url" 
                            value="{{ old('channel_url', $youtubeAccount->profile_url ?? '') }}" 
                            placeholder="https://www.youtube.com/@YourChannelHandle" 
                            class="form-control"
                        >
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                        <a href="{{ route('streamer.dashboard') }}" class="btn btn-outline-secondary">
                            &larr; Dashboard
                        </a>
                        <button type="submit" class="btn btn-streamer text-white fw-bold px-4">
                            <i class="las la-save me-1"></i> Save YouTube Channel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
