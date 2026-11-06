@extends('layouts.app')

@section('title', ($campaign->title ?? 'Campaign Details') . ' | GEZUX')
@section('meta_description', 'View campaign requirements, CPM reward rates, official source media assets, and submit viral video clips.')

@section('content')
<div class="min-h-screen pt-28 pb-24 relative overflow-hidden bg-slate-50 dark:bg-[#070708] text-slate-900 dark:text-[#F7F7F5] transition-colors duration-300" x-data="{
    clipModalOpen: false,
    selectedPlatform: 'TikTok',
    activeTab: 'brief',
    isFixed: {{ $campaign->isFixedPerVideo() ? 'true' : 'false' }},
    calcViews: 50000,
    cpmRate: {{ (float) ($campaign->reward_rate_per_thousand ?? 2.50) }},
    rewardPerVideo: {{ (float) ($campaign->reward_per_video ?? 25.00) }},
    expectedViewsPerVideo: {{ (int) ($campaign->target_views_per_video ?? 80000) }},
    calcVideos: 2
}">
    <!-- Background Ambient Glows -->
    <div class="absolute top-16 left-1/4 -translate-x-1/2 w-[600px] h-[350px] bg-violet-500/10 dark:bg-violet-600/15 blur-[140px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute top-48 right-10 w-[500px] h-[320px] bg-purple-500/10 dark:bg-purple-600/15 blur-[130px] rounded-full pointer-events-none -z-10"></div>

    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
        
        <!-- Alerts & Feedback -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 flex items-center justify-between text-xs font-mono shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="las la-check-circle text-emerald-600 dark:text-emerald-400 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 dark:text-emerald-400 hover:opacity-80 text-lg leading-none">&times;</button>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-800 dark:text-rose-300 text-xs font-mono space-y-1 shadow-sm">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i class="las la-exclamation-circle text-rose-600 dark:text-rose-400 text-base"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Back Breadcrumb & Campaign Header Meta -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('web.marketplace') }}" class="inline-flex items-center gap-2 text-xs font-mono text-slate-500 dark:text-[#A1A1AA] hover:text-violet-600 dark:hover:text-white transition-colors">
                <i class="las la-arrow-left text-violet-500 text-sm"></i>
                <span>Back to Marketplace</span>
            </a>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-mono bg-violet-50 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300">
                    CAMPAIGN #{{ $campaign->id }}
                </span>
                @if($campaign->status === 'active')
                    <span class="px-3 py-1 rounded-full text-[11px] font-mono font-medium bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active Bounty Pool
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-[11px] font-mono bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300 uppercase">
                        {{ $campaign->status }}
                    </span>
                @endif
            </div>
        </div>

        @php
            $pct = $campaign->total_budget > 0 ? min(100, round((($campaign->total_budget - $campaign->remaining_budget) / $campaign->total_budget) * 100)) : 0;
            // STRICT PRIVACY: Only brand company_name is exposed. Never emails, phone numbers, or addresses.
            $brandName = $campaign->brand->company_name ?? ($campaign->brand->name ?? 'Verified Advertiser');
            $assetUrl = $campaign->primary_asset_url;
            $cleanRules = $campaign->clean_rules;
        @endphp

        <!-- Main Campaign Header Card -->
        <div class="bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] rounded-2xl sm:rounded-3xl p-4 sm:p-7 md:p-10 shadow-sm dark:shadow-2xl backdrop-blur-xl mb-8 relative overflow-hidden transition-colors">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-violet-600 via-purple-500 to-indigo-500"></div>

            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-8">
                <div class="max-w-3xl min-w-0">
                    <!-- Brand Eyebrow -->
                    <div class="text-xs font-mono text-violet-600 dark:text-violet-400 font-semibold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <i class="las la-building"></i> {{ $brandName }} &bull; {{ $campaign->category ?? 'Technology & AI' }}
                    </div>

                    <!-- Title -->
                    <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight mb-3 break-words">
                        {{ $campaign->title }}
                    </h1>

                    <!-- Supported Platforms & Geo Audience Scope -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 pt-2 border-t border-slate-100 dark:border-white/5">
                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400 font-semibold">Accepted:</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono bg-slate-100 dark:bg-white/[0.06] text-slate-800 dark:text-white border border-slate-200 dark:border-white/10">
                            <i class="fab fa-tiktok"></i> TikTok
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20">
                            <i class="fab fa-youtube text-red-500"></i> YouTube Shorts
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono bg-pink-50 dark:bg-pink-500/10 text-pink-600 dark:text-pink-400 border border-pink-200 dark:border-pink-500/20">
                            <i class="fab fa-instagram text-pink-500"></i> Instagram Reels
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono bg-slate-100 dark:bg-white/[0.06] text-slate-800 dark:text-white border border-slate-200 dark:border-white/10">
                            <i class="fab fa-x-twitter"></i> X
                        </span>

                        <span class="text-slate-300 dark:text-white/10 px-1 hidden sm:inline">|</span>
                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400 font-semibold">Audience:</span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-mono font-bold bg-violet-50 dark:bg-violet-500/10 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-500/30">
                            <i class="las la-globe text-violet-500"></i>
                            {{ $campaign->target_countries_summary }}
                        </span>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="flex flex-col sm:flex-row lg:flex-col gap-2.5 flex-shrink-0 w-full sm:w-auto">
                    @auth
                        @if(Auth::user()->isClipper())
                            <a href="{{ route('clipper.submissions.create', ['campaign_id' => $campaign->id]) }}"
                               class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider hover:from-violet-500 hover:to-indigo-500 transition-all shadow-lg shadow-violet-600/30 flex items-center justify-center gap-2">
                                <i class="las la-cloud-upload-alt text-base"></i>
                                <span>Submit Clip Video</span>
                            </a>
                        @elseif(Auth::user()->isStreamer())
                            <a href="{{ route('streamer.submissions.create', ['campaign_id' => $campaign->id]) }}"
                               class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-red-600 via-rose-600 to-purple-600 text-white font-extrabold text-xs uppercase tracking-wider hover:from-red-500 hover:to-purple-500 transition-all shadow-lg shadow-red-600/30 flex items-center justify-center gap-2">
                                <i class="fab fa-youtube text-base"></i>
                                <span>Submit Promo Video</span>
                            </a>
                        @else
                            <button type="button" @click="clipModalOpen = true"
                               class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider hover:from-violet-500 hover:to-indigo-500 transition-all shadow-lg shadow-violet-600/30 flex items-center justify-center gap-2">
                                <i class="las la-plus-circle text-base"></i>
                                <span>Submit Video Post</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                           class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider hover:from-violet-500 hover:to-indigo-500 transition-all shadow-lg shadow-violet-600/30 flex items-center justify-center gap-2">
                            <i class="las la-user-plus text-base"></i>
                            <span>Sign Up to Submit Clip</span>
                        </a>
                    @endauth

                    @if($assetUrl)
                        <a href="{{ $assetUrl }}" target="_blank" rel="noopener noreferrer"
                           class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-white/10 dark:hover:bg-white/15 text-slate-800 dark:text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors border border-slate-200 dark:border-white/10">
                            <i class="las la-folder-open text-violet-500 text-sm"></i>
                            <span>Download Assets Folder</span>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-2.5 sm:gap-3 p-3.5 sm:p-5 rounded-2xl bg-slate-50 dark:bg-[#14141E] border border-slate-200/60 dark:border-white/5 mb-6">
                @if($campaign->isFixedPerVideo())
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 col-span-2 sm:col-span-1">
                        <span class="text-[10px] uppercase font-mono text-amber-700 dark:text-amber-300 font-bold block mb-1">
                            ★ Fixed Pay / Video
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 font-mono block truncate">
                            ${{ number_format($campaign->reward_per_video, 2) }}
                        </span>
                        <span class="text-[10px] font-mono text-slate-500 dark:text-[#A1A1AA] block mt-0.5 truncate">
                            Target: {{ $campaign->formatted_expected_views }}
                        </span>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 col-span-2 sm:col-span-1">
                        <span class="text-[10px] uppercase font-mono text-emerald-700 dark:text-emerald-300 font-bold block mb-1">
                            ★ Rate per 1k Views
                        </span>
                        <span class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono block truncate">
                            ${{ number_format($campaign->reward_rate_per_thousand, 2) }}
                        </span>
                    </div>
                @endif
                <div class="p-3">
                    <span class="text-[10px] uppercase font-mono text-slate-500 dark:text-[#A1A1AA] block mb-1">Remaining Budget</span>
                    <span class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white font-mono block truncate">
                        ${{ number_format($campaign->remaining_budget, 2) }}
                    </span>
                </div>
                <div class="p-3">
                    <span class="text-[10px] uppercase font-mono text-slate-500 dark:text-[#A1A1AA] block mb-1">Verified Views</span>
                    <span class="text-lg sm:text-xl font-bold text-cyan-600 dark:text-cyan-400 font-mono block truncate">
                        {{ number_format($campaign->verified_views ?? 0) }}
                    </span>
                </div>
                <div class="p-3">
                    <span class="text-[10px] uppercase font-mono text-slate-500 dark:text-[#A1A1AA] block mb-1">Target Views</span>
                    <span class="text-lg sm:text-xl font-bold text-violet-600 dark:text-violet-400 font-mono block truncate">
                        {{ number_format($campaign->target_views ?? 0) }}
                    </span>
                </div>
                <div class="p-3 col-span-2 sm:col-span-4 lg:col-span-1">
                    <span class="text-[10px] uppercase font-mono text-slate-500 dark:text-[#A1A1AA] block mb-1">Bounty Status</span>
                    <span class="text-xs sm:text-sm font-bold text-emerald-600 dark:text-emerald-400 font-mono flex items-center gap-1.5 mt-1">
                        <i class="las la-check-shield text-emerald-500 text-lg flex-shrink-0"></i>
                        <span class="truncate">100% Pre-Funded</span>
                    </span>
                </div>
            </div>

            <!-- Budget Progress Bar -->
            <div>
                <div class="flex justify-between text-xs font-mono text-slate-500 dark:text-[#A1A1AA] mb-2 flex-wrap gap-1">
                    <span>Campaign Bounty Utilization</span>
                    <span class="text-slate-900 dark:text-white font-bold">{{ $pct }}% Claimed of ${{ number_format($campaign->total_budget, 2) }}</span>
                </div>
                <div class="h-2.5 w-full bg-slate-200 dark:bg-white/[0.08] rounded-full overflow-hidden p-0.5">
                    <div class="h-full bg-gradient-to-r from-violet-600 via-purple-500 to-cyan-400 rounded-full transition-all duration-500 relative" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        </div>

        <!-- HERO ASSET RESOURCE BOX (Brand Creative Assets & Raw Footage) -->
        @if($assetUrl)
            <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-violet-600/10 via-purple-600/5 to-cyan-500/10 border-2 border-violet-500/30 dark:border-violet-500/40 shadow-md mb-8 overflow-hidden">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-violet-600 text-white flex items-center justify-center flex-shrink-0 shadow-lg shadow-violet-600/30">
                            <i class="las la-cloud-download-alt text-xl sm:text-2xl"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-1">
                                <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">Official Brand Creative Assets &amp; Source Footage</h3>
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-mono font-bold bg-violet-500 text-white">READY FOR CLIPPING</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                Download raw footage, gameplay recordings, b-roll, or stream clips provided directly by the brand:
                            </p>
                            <div class="text-xs font-mono text-violet-700 dark:text-violet-300 break-all max-w-xl mt-1 underline">
                                {{ $assetUrl }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 flex-shrink-0 w-full md:w-auto">
                        <button 
                            type="button" 
                            x-data="{ copied: false }" 
                            @click="navigator.clipboard.writeText('{{ $assetUrl }}'); copied = true; setTimeout(() => copied = false, 2500)"
                            class="px-4 py-2.5 rounded-xl bg-white dark:bg-white/10 hover:bg-slate-100 dark:hover:bg-white/20 text-slate-800 dark:text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-colors border border-slate-200 dark:border-white/20 shadow-xs"
                        >
                            <i class="las la-copy text-sm" x-show="!copied"></i>
                            <i class="las la-check text-emerald-500 text-sm" x-show="copied"></i>
                            <span x-text="copied ? 'Copied Link!' : 'Copy Asset Link'"></span>
                        </button>
                        <a 
                            href="{{ $assetUrl }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="px-5 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-md shadow-violet-600/30"
                        >
                            <i class="las la-external-link-alt text-sm"></i>
                            <span>Open Assets Folder</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2-Column Responsive Layout: Left Brief & Rules, Right Calculator & Submissions -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left Main Column (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- 1. Campaign Description & Narrative Brief -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="las la-file-alt text-violet-500 text-lg"></i>
                        Campaign Overview &amp; Concept
                    </h2>
                    <div class="prose dark:prose-invert max-w-none text-slate-700 dark:text-slate-300 text-sm sm:text-base leading-relaxed space-y-3" style="white-space: pre-line; word-break: break-word;">
                        {{ $campaign->description }}
                    </div>
                </div>

                <!-- 2. Brand Clipping Rules & Verification Criteria -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
                    <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="las la-shield-alt text-amber-500 text-lg"></i>
                            Campaign Rules &amp; Submission Criteria
                        </h2>
                        <span class="badge bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30 text-xs px-2.5 py-1">
                            Mandatory for Approval
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                        Follow these brand guidelines carefully to ensure your submitted clip passes verification and unlocks bounty payout:
                    </p>

                    <div class="space-y-3">
                        @foreach($cleanRules as $rule)
                            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-[#14141E] border border-slate-200/60 dark:border-white/5 flex items-start gap-3 transition-colors hover:border-violet-500/30">
                                <div class="w-6 h-6 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-500/20">
                                    <i class="las la-check text-sm font-bold"></i>
                                </div>
                                <span class="text-xs sm:text-sm text-slate-800 dark:text-slate-200 font-medium leading-relaxed">
                                    {{ $rule }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- 3. Audience Geo-Targeting & Per-Market Rates -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
                    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                            <i class="las la-globe-americas text-violet-500 text-lg"></i>
                            Target Audience &amp; Regional Payout Rates
                        </h2>
                        @if($campaign->country_targeting_mode === 'worldwide')
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-medium bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">
                                🌍 Global Scope
                            </span>
                        @elseif($campaign->country_targeting_mode === 'single')
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-500/30">
                                🎯 Single Target Market
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-medium bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-200 dark:border-purple-500/30">
                                🗺️ Multi-Country Markets
                            </span>
                        @endif
                    </div>

                    @if($campaign->country_targeting_mode === 'multiple' && $campaign->target_countries_list->isNotEmpty())
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Custom CPM payout rates applicable based on viewer/creator market:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($campaign->target_countries_list as $tc)
                                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/[0.03] border border-slate-200/60 dark:border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ $tc->flag_url }}" width="24" height="18" class="rounded border shadow-xs" alt="{{ $tc->country_code }}">
                                        <div>
                                            <span class="text-xs font-bold text-slate-800 dark:text-white block">{{ $tc->country_name }}</span>
                                            <span class="text-[10px] font-mono text-slate-400 block">{{ $tc->country_code }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-xs font-bold font-mono text-emerald-600 dark:text-emerald-400 block">${{ number_format($tc->cpm_rate, 2) }}/1k</span>
                                        <span class="text-[10px] font-mono text-slate-400 block">{{ number_format($tc->target_views) }} views</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($campaign->country_targeting_mode === 'single' && $campaign->target_countries_list->isNotEmpty())
                        @php $tc = $campaign->target_countries_list->first(); @endphp
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/[0.03] border border-slate-200/60 dark:border-white/5 flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $tc->flag_url }}" width="32" height="24" class="rounded border shadow-xs" alt="{{ $tc->country_code }}">
                                <div>
                                    <span class="text-sm font-bold text-slate-900 dark:text-white block">{{ $tc->country_name }} ({{ $tc->country_code }})</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 block">Exclusively targeted for this campaign bounty</span>
                                </div>
                            </div>
                            <span class="text-sm font-extrabold font-mono text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 px-3 py-1.5 rounded-xl">
                                ${{ number_format($tc->cpm_rate, 2) }} / 1,000 views
                            </span>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/[0.03] border border-slate-200/60 dark:border-white/5 flex items-center gap-3">
                            <span class="text-2xl">🌍</span>
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Worldwide Audience</span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 block">All countries eligible at the base bounty rate of ${{ number_format($campaign->reward_rate_per_thousand, 2) }} per 1,000 verified views.</span>
                            </div>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right Sidebar Column (4 Cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Interactive Reward Calculator -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-xl bg-violet-100 dark:bg-violet-950/80 text-violet-600 dark:text-violet-300 flex items-center justify-center">
                            <i class="las la-calculator text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">Earnings Calculator</h3>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                @if($campaign->isFixedPerVideo())
                                    Based on ${{ number_format($campaign->reward_per_video, 2) }} / Video Bounties
                                @else
                                    Based on ${{ number_format($campaign->reward_rate_per_thousand, 2) }}/1k CPM
                                @endif
                            </span>
                        </div>
                    </div>

                    <template x-if="isFixed">
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between text-xs font-mono mb-2">
                                    <span class="text-slate-500 dark:text-slate-400">Accepted Videos:</span>
                                    <strong class="text-slate-900 dark:text-white" x-text="calcVideos + (calcVideos == 1 ? ' video' : ' videos')"></strong>
                                </div>
                                <input 
                                    type="range" 
                                    min="1" 
                                    max="20" 
                                    step="1" 
                                    x-model="calcVideos" 
                                    class="w-full h-2 bg-slate-200 dark:bg-white/10 rounded-lg appearance-none cursor-pointer accent-violet-600"
                                >
                                <div class="flex justify-between text-[10px] font-mono text-slate-400 mt-1">
                                    <span>1 video</span>
                                    <span>10 videos</span>
                                    <span>20 videos</span>
                                </div>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-white/[0.04] border border-slate-200 dark:border-white/10 text-xs font-mono flex items-center justify-between">
                                <span class="text-slate-500 dark:text-slate-400">Target Audience Reach:</span>
                                <span class="text-slate-900 dark:text-white font-bold" x-text="Number(calcVideos * expectedViewsPerVideo).toLocaleString() + ' views'"></span>
                            </div>

                            <!-- Projected Payout Result Card -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-amber-500/10 via-amber-500/5 to-emerald-500/10 border border-amber-500/20 text-center">
                                <span class="text-[10px] uppercase font-mono text-amber-700 dark:text-amber-400 font-bold block mb-1">
                                    Projected Creator Bounty
                                </span>
                                <div class="text-3xl font-extrabold font-mono text-amber-600 dark:text-amber-400" x-text="'$' + (calcVideos * rewardPerVideo).toFixed(2)">
                                    ${{ number_format(2 * (float)($campaign->reward_per_video ?? 25), 2) }}
                                </div>
                                <small class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">Paid out directly upon brand video approval</small>
                            </div>
                        </div>
                    </template>

                    <template x-if="!isFixed">
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between text-xs font-mono mb-2">
                                    <span class="text-slate-500 dark:text-slate-400">Target Views:</span>
                                    <strong class="text-slate-900 dark:text-white" x-text="Number(calcViews).toLocaleString() + ' views'"></strong>
                                </div>
                                <input 
                                    type="range" 
                                    min="5000" 
                                    max="500000" 
                                    step="5000" 
                                    x-model="calcViews" 
                                    class="w-full h-2 bg-slate-200 dark:bg-white/10 rounded-lg appearance-none cursor-pointer accent-violet-600"
                                >
                                <div class="flex justify-between text-[10px] font-mono text-slate-400 mt-1">
                                    <span>5K</span>
                                    <span>250K</span>
                                    <span>500K</span>
                                </div>
                            </div>

                            <!-- Projected Payout Result Card -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-500/10 via-emerald-500/5 to-cyan-500/10 border border-emerald-500/20 text-center">
                                <span class="text-[10px] uppercase font-mono text-emerald-700 dark:text-emerald-400 font-bold block mb-1">
                                    Projected Creator Payout
                                </span>
                                <div class="text-3xl font-extrabold font-mono text-emerald-600 dark:text-emerald-400" x-text="'$' + ((calcViews / 1000) * cpmRate).toFixed(2)">
                                    ${{ number_format((50000 / 1000) * (float)($campaign->reward_rate_per_thousand ?? 2.50), 2) }}
                                </div>
                                <small class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 block">Paid out directly upon verification</small>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Fast Submission Card -->
                <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                        <i class="las la-rocket text-violet-500"></i>
                        Ready to Monetize?
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">
                        Create viral video edits using the official campaign assets, publish on your social accounts, and submit your live video link.
                    </p>

                    @auth
                        @if(Auth::user()->isClipper())
                            <a href="{{ route('clipper.submissions.create', ['campaign_id' => $campaign->id]) }}"
                               class="w-full py-3 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2">
                                <i class="las la-upload text-sm"></i>
                                <span>Submit Clipper Video</span>
                            </a>
                        @elseif(Auth::user()->isStreamer())
                            <a href="{{ route('streamer.submissions.create', ['campaign_id' => $campaign->id]) }}"
                               class="w-full py-3 px-4 rounded-xl bg-red-600 hover:bg-red-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-red-600/30 flex items-center justify-center gap-2">
                                <i class="fab fa-youtube text-sm"></i>
                                <span>Submit YouTube Video</span>
                            </a>
                        @else
                            <button type="button" @click="clipModalOpen = true"
                               class="w-full py-3 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2">
                                <i class="las la-plus-circle text-sm"></i>
                                <span>Submit Video Post</span>
                            </button>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                           class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2">
                            <i class="las la-user-plus text-sm"></i>
                            <span>Register to Submit Clips</span>
                        </a>
                    @endauth
                </div>

            </div>

        </div>

    </div>

    <!-- Quick Submission Modal -->
    <div x-show="clipModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-black/80 backdrop-blur-sm transition-opacity" 
             @click="clipModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200 dark:border-white/10 p-6 sm:p-8 shadow-2xl text-slate-900 dark:text-white transition-all">
                <button type="button" @click="clipModalOpen = false" class="absolute right-4 top-4 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xl leading-none">&times;</button>
                
                <div class="mb-5">
                    <div class="w-10 h-10 rounded-2xl bg-violet-100 dark:bg-violet-950/80 text-violet-600 dark:text-violet-300 flex items-center justify-center mb-3">
                        <i class="las la-video text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold">Submit Your Video Post</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Submit your live post URL. Payout will be sent directly to your bank account upon verification.</p>
                </div>

                <form action="{{ route('web.clips.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">

                    <div>
                        <label class="block text-xs font-mono uppercase text-slate-600 dark:text-slate-400 mb-1.5">Social Platform</label>
                        <select name="platform" x-model="selectedPlatform" class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:border-violet-500">
                            <option value="TikTok">TikTok</option>
                            <option value="YouTube Shorts">YouTube Shorts</option>
                            <option value="Instagram Reels">Instagram Reels</option>
                            <option value="X">X (Twitter)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-mono uppercase text-slate-600 dark:text-slate-400 mb-1.5">Live Clip Post URL</label>
                        <input 
                            type="url" 
                            name="clip_url" 
                            required 
                            placeholder="https://www.tiktok.com/@creator/video/123..."
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2 mt-4"
                    >
                        <i class="las la-paper-plane text-sm"></i>
                        <span>Submit for Verification</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
