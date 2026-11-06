@extends('layouts.app')

@section('title', 'Campaign Marketplace | GEZUX Performance Clipping')
@section('meta_description', 'Discover high-paying short-form video clip bounties. Filter by platform, CPM rate, and niche.')

@section('content')
<div class="min-h-screen pt-28 pb-20 relative overflow-hidden bg-slate-50 dark:bg-[#070708] text-slate-900 dark:text-[#F7F7F5] transition-colors duration-300" x-data="{
    search: '',
    selectedPlatform: 'all',
    selectedCategory: 'all',
    sortBy: 'highest_rate'
}">
    <!-- Background Ambient Glows -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[800px] h-[450px] bg-gradient-to-tr from-violet-500/10 via-purple-500/10 to-indigo-500/5 dark:from-violet-600/15 dark:via-purple-600/10 dark:to-transparent blur-[140px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 right-10 w-[500px] h-[350px] bg-cyan-500/5 dark:bg-cyan-600/10 blur-[130px] pointer-events-none -z-10"></div>

    <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 w-full">
        
        <!-- Header Hero -->
        <div class="text-center max-w-4xl mx-auto mb-12">
            <h1 class="text-3xl sm:text-6xl font-display font-extrabold tracking-tight text-slate-900 dark:text-white mb-5">
                Active Clip <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 dark:from-violet-400 dark:via-purple-300 dark:to-white">Campaigns</span>
            </h1>
            <p class="text-slate-600 dark:text-[#A1A1AA] text-base sm:text-lg leading-relaxed max-w-3xl mx-auto">
                Discover verified brand campaigns with guaranteed bounty rewards. Clip high-converting moments, distribute across TikTok, Reels &amp; YouTube Shorts, and get paid per verified thousand views.
            </p>
        </div>

        <!-- Filter & Search Controls Bar -->
        <div class="bg-white dark:bg-[#0E0E16] border border-slate-200 dark:border-white/[0.08] rounded-2xl p-4 sm:p-5 mb-8 shadow-sm dark:shadow-2xl backdrop-blur-md transition-colors">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-center">
                
                <!-- Search Input -->
                <div class="md:col-span-5 relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 dark:text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Search campaign, brand name, or tag..." 
                        class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-10 pr-9 py-2.5 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-[#71717A] input-focus-ring transition-colors"
                    >
                    <button 
                        type="button" 
                        x-show="search" 
                        @click="search = ''" 
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-white text-xs"
                    >
                        &times;
                    </button>
                </div>

                <!-- Platform Filter -->
                <div class="md:col-span-4 flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0 scrollbar-none">
                    <button 
                        type="button"
                        @click="selectedPlatform = 'all'" 
                        :class="selectedPlatform === 'all' 
                            ? 'bg-violet-600 text-white font-bold shadow-sm shadow-violet-600/30' 
                            : 'bg-slate-100 dark:bg-[#14141E] text-slate-600 dark:text-[#A1A1AA] hover:text-slate-900 dark:hover:text-white border border-slate-200/60 dark:border-white/5'"
                        class="filter-chip px-3 py-2 rounded-xl text-xs transition-colors whitespace-nowrap">
                        All
                    </button>
                    <button 
                        type="button"
                        @click="selectedPlatform = 'tiktok'" 
                        :class="selectedPlatform === 'tiktok' 
                            ? 'bg-violet-600 text-white font-bold shadow-sm shadow-violet-600/30' 
                            : 'bg-slate-100 dark:bg-[#14141E] text-slate-600 dark:text-[#A1A1AA] hover:text-slate-900 dark:hover:text-white border border-slate-200/60 dark:border-white/5'"
                        class="filter-chip px-3 py-2 rounded-xl text-xs transition-colors whitespace-nowrap flex items-center gap-1.5">
                        <i data-lucide="video" class="w-3.5 h-3.5"></i> TikTok
                    </button>
                    <button 
                        type="button"
                        @click="selectedPlatform = 'youtube'" 
                        :class="selectedPlatform === 'youtube' 
                            ? 'bg-violet-600 text-white font-bold shadow-sm shadow-violet-600/30' 
                            : 'bg-slate-100 dark:bg-[#14141E] text-slate-600 dark:text-[#A1A1AA] hover:text-slate-900 dark:hover:text-white border border-slate-200/60 dark:border-white/5'"
                        class="filter-chip px-3 py-2 rounded-xl text-xs transition-colors whitespace-nowrap flex items-center gap-1.5">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i> YT Shorts
                    </button>
                    <button 
                        type="button"
                        @click="selectedPlatform = 'instagram'" 
                        :class="selectedPlatform === 'instagram' 
                            ? 'bg-violet-600 text-white font-bold shadow-sm shadow-violet-600/30' 
                            : 'bg-slate-100 dark:bg-[#14141E] text-slate-600 dark:text-[#A1A1AA] hover:text-slate-900 dark:hover:text-white border border-slate-200/60 dark:border-white/5'"
                        class="filter-chip px-3 py-2 rounded-xl text-xs transition-colors whitespace-nowrap flex items-center gap-1.5">
                        <i data-lucide="instagram" class="w-3.5 h-3.5"></i> Reels
                    </button>
                </div>

                <!-- Sorting Dropdown -->
                <div class="md:col-span-3 flex justify-end">
                    <div class="relative w-full">
                        <select 
                            x-model="sortBy"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-slate-700 dark:text-[#D4D4D8] focus:outline-none focus:border-violet-500 appearance-none cursor-pointer"
                        >
                            <option value="highest_rate">Highest Rate ($ / 1k views)</option>
                            <option value="highest_pool">Largest Campaign Pool</option>
                            <option value="ending_soon">Ending Soonest</option>
                        </select>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 dark:text-[#71717A] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                    </div>
                </div>

            </div>

            <!-- Categories Pills -->
            <div class="flex items-center gap-2 mt-4 pt-3.5 border-t border-slate-100 dark:border-white/[0.05] overflow-x-auto text-xs scrollbar-none">
                <span class="text-slate-500 dark:text-[#71717A] font-mono text-[11px] uppercase tracking-wider mr-1">Niche:</span>
                @php
                    $categories = ['All', 'AI & Software', 'Crypto & Web3', 'Gaming', 'Fintech', 'Hardware', 'Productivity'];
                @endphp
                @foreach($categories as $cat)
                    @php $catSlug = strtolower(explode(' ', $cat)[0]); @endphp
                    <button 
                        type="button"
                        @click="selectedCategory = '{{ $catSlug }}'" 
                        :class="selectedCategory === '{{ $catSlug }}' 
                            ? 'bg-violet-50 dark:bg-violet-950/60 border-violet-400 dark:border-violet-500/60 text-violet-700 dark:text-violet-300 font-bold' 
                            : 'bg-slate-50 dark:bg-[#14141E] border-slate-200/80 dark:border-white/5 text-slate-600 dark:text-[#A1A1AA] hover:text-slate-900 dark:hover:text-white'"
                        class="filter-chip px-3 py-1 rounded-full border transition-all whitespace-nowrap text-[11px]">
                        {{ $cat }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Campaign Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
                @php
                    $pct = $campaign->total_budget > 0 ? min(100, round((($campaign->total_budget - $campaign->remaining_budget) / $campaign->total_budget) * 100)) : 0;
                    $brandName = $campaign->brand->company_name ?? ($campaign->brand->name ?? 'Verified Advertiser');
                    $rate = $campaign->reward_rate_per_thousand ?? 2.50;
                @endphp
                <div 
                    x-show="
                        (search === '' || '{{ strtolower($campaign->title) }}'.includes(search.toLowerCase()) || '{{ strtolower($brandName) }}'.includes(search.toLowerCase())) &&
                        (selectedCategory === 'all' || '{{ strtolower($campaign->category ?? $campaign->title) }}'.includes(selectedCategory))
                    "
                    class="group relative rounded-3xl bg-white dark:bg-[#0E0E16] border border-slate-200/90 dark:border-white/[0.08] p-7 sm:p-8 flex flex-col justify-between card-lift shadow-md hover:shadow-2xl hover:shadow-violet-500/15 dark:hover:shadow-violet-900/25 transition-all duration-300"
                >
                    <!-- Glowing Top Accent on Hover -->
                    <div class="absolute top-0 left-8 right-8 h-0.5 bg-gradient-to-r from-transparent via-violet-500/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <!-- Top Card Info -->
                    <div>
                        <!-- Brand & Status -->
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center font-extrabold text-violet-700 dark:text-violet-300 text-sm shadow-sm">
                                    {{ strtoupper(substr($brandName, 0, 2)) }}
                                </div>
                                <div>
                                    <h2 class="font-extrabold text-slate-900 dark:text-white text-base group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors flex items-center gap-1.5">
                                        {{ $brandName }}
                                        <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                                    </h2>
                                    <span class="text-xs text-slate-500 dark:text-[#71717A] font-mono">Verified Sponsor</span>
                                </div>
                            </div>
                            @if($campaign->status === 'half_filled')
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-400 flex items-center gap-1.5 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    ⚡ Half Filled
                                </span>
                            @elseif($campaign->status === 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 flex items-center gap-1.5 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                    Completed
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5 shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Active
                                </span>
                            @endif
                        </div>

                        <!-- Campaign Title & Description -->
                        <a href="{{ url('/campaigns/' . $campaign->id) }}" class="block text-decoration-none">
                            <h3 class="text-lg sm:text-xl font-display font-extrabold text-slate-900 dark:text-white mb-2.5 leading-snug line-clamp-2 hover:text-violet-600 dark:hover:text-violet-300 transition-colors">
                                {{ $campaign->title }}
                            </h3>
                        </a>
                        <p class="text-sm text-slate-600 dark:text-[#A1A1AA] mb-6 line-clamp-2 leading-relaxed">
                            {{ $campaign->description ?? 'Create viral short-form clips featuring product features, highlights, and reaction commentary with our official campaign hashtag.' }}
                        </p>

                        <!-- Key Metrics Grid -->
                        <div class="grid grid-cols-2 gap-3.5 p-4 rounded-2xl bg-slate-50 dark:bg-[#14141E] border border-slate-200/80 dark:border-white/5 mb-6">
                            @if($campaign->isFixedPerVideo())
                                <div>
                                    <span class="text-[10px] uppercase font-mono font-semibold text-amber-600 dark:text-amber-400 block mb-0.5">Fixed / Video</span>
                                    <span class="text-xl font-black text-amber-600 dark:text-amber-400 font-mono metric-tabular">${{ number_format($campaign->reward_per_video, 2) }}</span>
                                    <span class="text-[10px] font-mono text-slate-500 dark:text-[#71717A] block mt-0.5">Target: {{ $campaign->formatted_expected_views }}</span>
                                </div>
                            @else
                                <div>
                                    <span class="text-xs uppercase font-mono font-semibold text-slate-500 dark:text-[#71717A] block mb-1">Rate / 1k Views</span>
                                    <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 font-mono metric-tabular">${{ number_format($rate, 2) }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="text-xs uppercase font-mono font-semibold text-slate-500 dark:text-[#71717A] block mb-1">Remaining Pool</span>
                                <span class="text-xl font-black text-slate-900 dark:text-white font-mono metric-tabular">${{ number_format($campaign->remaining_budget, 0) }}</span>
                            </div>
                        </div>

                        <!-- Campaign Budget Pool Progress -->
                        <div class="mb-6">
                            <div class="flex justify-between text-xs font-mono text-slate-500 dark:text-[#71717A] mb-2">
                                <span>Pool Claimed</span>
                                <span class="text-slate-900 dark:text-white font-bold metric-tabular">{{ $pct }}% (${{ number_format($campaign->total_budget, 0) }} total)</span>
                            </div>
                            <div class="h-2.5 w-full bg-slate-200 dark:bg-white/[0.08] rounded-full overflow-hidden p-0.5">
                                <div class="h-full bg-gradient-to-r from-violet-600 via-purple-500 to-cyan-400 rounded-full relative" style="width: {{ $pct }}%">
                                    <div class="absolute right-0 top-0 bottom-0 w-2 rounded-full bg-white progress-tip-luminous"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Platform Tags (TikTok, Shorts, Reels with brand icons) -->
                        <div class="flex items-center gap-2 mb-7">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-slate-100 dark:bg-white/[0.06] text-slate-700 dark:text-[#D4D4D8] border border-slate-200 dark:border-white/5 flex items-center gap-1.5 shadow-xs">
                                <i class="fab fa-tiktok"></i> TikTok
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-slate-100 dark:bg-white/[0.06] text-slate-700 dark:text-[#D4D4D8] border border-slate-200 dark:border-white/5 flex items-center gap-1.5 shadow-xs">
                                <i class="fab fa-youtube text-red-500"></i> Shorts
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-medium bg-slate-100 dark:bg-white/[0.06] text-slate-700 dark:text-[#D4D4D8] border border-slate-200 dark:border-white/5 flex items-center gap-1.5 shadow-xs">
                                <i class="fab fa-instagram text-pink-500"></i> Reels
                            </span>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="pt-5 border-t border-slate-100 dark:border-white/[0.06] flex items-center gap-3">
                        @auth
                            <a 
                                href="{{ route('clipper.submissions.create', ['campaign_id' => $campaign->id]) }}"
                                class="flex-1 py-3 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm transition-all shadow-md shadow-violet-600/25 flex items-center justify-center gap-2 tactile-btn cursor-pointer"
                            >
                                <i data-lucide="scissors" class="w-4 h-4"></i>
                                Clip Bounty
                            </a>
                        @else
                            <a 
                                href="{{ route('register') }}"
                                class="flex-1 py-3 px-5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm transition-all shadow-md shadow-violet-600/25 flex items-center justify-center gap-2 tactile-btn cursor-pointer"
                            >
                                <i data-lucide="scissors" class="w-4 h-4"></i>
                                Clip Bounty
                            </a>
                        @endauth
                        <a 
                            href="{{ url('/campaigns/' . $campaign->id) }}"
                            class="py-3 px-4 rounded-xl bg-slate-100 dark:bg-white/[0.08] hover:bg-slate-200 dark:hover:bg-white/15 text-slate-800 dark:text-white border border-slate-200 dark:border-white/10 transition-colors flex items-center justify-center tactile-btn"
                            title="View Full Campaign Guidelines"
                        >
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-3 text-center py-16 bg-white dark:bg-[#0E0E16] border border-slate-200 dark:border-white/[0.08] rounded-2xl shadow-sm">
                    <i data-lucide="inbox" class="w-12 h-12 text-slate-400 dark:text-[#71717A] mx-auto mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">No Active Campaigns Found</h3>
                    <p class="text-sm text-slate-500 dark:text-[#A1A1AA] max-w-md mx-auto mb-6">
                        There are currently no active public bounties matching your search. Check back shortly or create one from your Brand portal!
                    </p>
                    <a href="{{ url('/brand') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-violet-600 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-violet-600/30 hover:bg-violet-500 transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Launch Brand Bounty
                    </a>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
