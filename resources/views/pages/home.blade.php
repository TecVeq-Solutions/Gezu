@extends('layouts.app')

@section('title', 'GEZUX — The Global Clipping Marketplace')
@section('meta_description', 'GEZUX connects brands with creators who turn long-form videos into short-form content and distribute them across the platforms where attention lives.')

@section('content')
<div class="relative min-h-screen bg-[#FAFAFB] dark:bg-[#070708] overflow-hidden text-[#09090B] dark:text-[#F7F7F5] selection:bg-violet-600 selection:text-white"
     x-data="{
         viewCount: 3744401,
         targetViews: 5000000,
         init() {
             setInterval(() => {
                 this.viewCount += Math.floor(Math.random() * 8) + 3;
             }, 2200);
         },
         get progressPercent() {
             return Math.min(100, ((this.viewCount / this.targetViews) * 100)).toFixed(1);
         }
     }">

    <!-- ==========================================
         SECTION 1: HERO WITH 3D CURSOR RING FIELD
         ========================================== -->
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
        
        <!-- Subtle Geometric Dot Grid Pattern -->
        <div class="absolute inset-0 gezux-bg-dots pointer-events-none opacity-60 dark:opacity-20"></div>

        <!-- WebGL Canvas Background (Cursor Ring Particle Field) -->
        <div class="opacity-90 dark:opacity-80" style="position: absolute; inset: 0px; width: 100%; height: 100%; overflow: hidden; pointer-events: none; background: transparent;">
            <canvas id="cursor-ring-canvas" style="position: absolute; inset: 0px; width: 100%; height: 100%; display: block;"></canvas>
        </div>

        <!-- Ambient Purple / Violet Radial Glows -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-violet-600/15 dark:bg-violet-600/20 blur-[140px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/4 w-[450px] h-[300px] bg-blue-600/10 dark:bg-blue-600/15 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            

            <!-- Hero Headline & Subtitle -->
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-display font-extrabold tracking-[-0.035em] text-gray-950 dark:text-[#F7F7F5] leading-[1.08] font-display-hero">
                    Turn Long-Form Content <br>
                    <span class="text-violet-600 dark:text-violet-400">Into</span> Distribution.
                </h1>
                
                <p class="mt-6 text-lg sm:text-xl text-gray-700 dark:text-[#A1A1AA] max-w-[58ch] mx-auto font-normal leading-relaxed editorial-lead">
                    GEZUX connects brands with creators who turn long-form videos into short-form content and distribute them across the platforms where attention lives.
                </p>

                <!-- Dual CTAs -->
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/brand') }}" 
                       class="starfield-btn starfield-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 font-display text-sm sm:text-base font-bold">
                        <span class="relative z-10 font-medium tracking-wide">Launch a Campaign</span>
                        <i data-lucide="arrow-right" class="relative z-10 w-4 h-4 text-violet-400"></i>
                    </a>
                    
                    <a href="{{ url('/marketplace') }}" 
                       class="starfield-btn starfield-secondary w-full sm:w-auto inline-flex items-center justify-center gap-2 font-display text-sm sm:text-base font-bold">
                        <i data-lucide="video" class="relative z-10 w-4 h-4 text-violet-400"></i>
                        <span class="relative z-10 font-medium tracking-wide">Start Clipping</span>
                    </a>
                </div>

                <p class="mt-4 text-xs text-gray-600 dark:text-[#71717A] font-medium">
                    For brands, creators, podcasts, agencies and media companies.
                </p>
            </div>

            <!-- Live Showcase Card: The Future of AI — Podcast #42 -->
            <div class="mt-16 lg:mt-24 max-w-5xl mx-auto">
                <div class="relative p-1 rounded-2xl bg-gradient-to-b from-gray-200/90 via-gray-100 to-transparent dark:from-white/15 dark:via-white/5 dark:to-transparent shadow-xl dark:shadow-2xl">
                    <div class="bg-white dark:bg-[#0B0B10] rounded-[15px] p-6 lg:p-8 border border-gray-200/90 dark:border-white/[0.08] relative overflow-hidden">
                        
                        <!-- Header inside campaign card -->
                        <div class="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-gray-200/90 dark:border-white/[0.08]">
                            <div class="flex items-center gap-3.5">
                                <div class="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center font-display font-bold text-violet-700 dark:text-violet-300 text-lg shadow-xs">
                                    FL
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            ● LIVE CAMPAIGN
                                        </span>
                                        <span class="text-xs text-gray-600 dark:text-[#71717A] font-medium">Future Labs</span>
                                    </div>
                                    <h3 class="text-lg font-display font-bold text-gray-950 dark:text-white mt-0.5">
                                        The Future of AI — Podcast #42
                                    </h3>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <span class="text-[11px] text-gray-500 dark:text-[#71717A] uppercase font-mono block">Budget Funded</span>
                                    <span class="text-base font-bold text-gray-950 dark:text-white font-mono">$5,000.00</span>
                                </div>
                                <div class="text-right pl-4 border-l border-gray-200 dark:border-white/10">
                                    <span class="text-[11px] text-gray-500 dark:text-[#71717A] uppercase font-mono block">Reward Rate</span>
                                    <span class="text-base font-bold text-violet-600 dark:text-violet-400 font-mono">$1.00 / 1K</span>
                                </div>
                            </div>
                        </div>

                        <!-- Verified Views & Dynamic Progress Bar -->
                        <div class="py-6 border-b border-gray-200/90 dark:border-white/[0.08]">
                            <div class="flex flex-wrap items-end justify-between gap-2 mb-3">
                                <div>
                                    <span class="text-xs text-gray-600 dark:text-[#A1A1AA] uppercase tracking-wider font-mono font-medium">
                                        Verified Eligible Views
                                    </span>
                                    <div class="text-3xl sm:text-4xl font-extrabold text-gray-950 dark:text-white font-mono tracking-tight flex items-center gap-2 mt-1">
                                        <span x-text="viewCount.toLocaleString()">3,744,401</span>
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-violet-100 dark:bg-violet-500/20 text-violet-800 dark:text-violet-300 border border-violet-200 dark:border-violet-500/30">
                                            +14.2% today
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-bold text-gray-950 dark:text-white font-mono" x-text="progressPercent + '%'">74.9%</span>
                                    <span class="text-xs text-gray-600 dark:text-[#71717A] block font-medium">Target: 5,000,000 Views</span>
                                </div>
                            </div>

                            <div class="w-full h-3 rounded-full bg-gray-100 dark:bg-white/[0.06] overflow-hidden p-0.5 border border-gray-200 dark:border-white/[0.08]">
                                <div class="h-full rounded-full bg-gradient-to-r from-violet-600 via-purple-500 to-cyan-400 transition-all duration-700 relative" 
                                     :style="'width: ' + progressPercent + '%'">
                                    <div class="absolute right-0 top-0 bottom-0 w-2 bg-white rounded-full shadow-[0_0_12px_#fff]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Active Creators -->
                        <div class="pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-mono uppercase tracking-wider text-gray-700 dark:text-[#A1A1AA] flex items-center gap-1.5 font-bold">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400"></i>
                                    Top Active Clipping Creators &amp; Verified Platform Distribution
                                </span>
                                <span class="text-[11px] text-gray-500 dark:text-[#71717A]">
                                    Automated view verification active
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <!-- AC -->
                                <div class="p-4 rounded-xl bg-gray-50/90 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 hover:bg-white dark:hover:bg-[#181822] transition-all flex items-center justify-between group shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-pink-500 to-rose-600 flex items-center justify-center font-bold text-white text-xs shadow-xs">
                                            AC
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm font-semibold text-gray-950 dark:text-white">@AlexClips</span>
                                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400"></i>
                                            </div>
                                            <span class="text-xs text-rose-600 dark:text-rose-400 font-semibold">TikTok • USA</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-gray-950 dark:text-white font-mono">128,000</span>
                                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-bold">+$128.00</span>
                                    </div>
                                </div>

                                <!-- MF -->
                                <div class="p-4 rounded-xl bg-gray-50/90 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 hover:bg-white dark:hover:bg-[#181822] transition-all flex items-center justify-between group shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center font-bold text-white text-xs shadow-xs">
                                            MF
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm font-semibold text-gray-950 dark:text-white">@MediaForge</span>
                                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400"></i>
                                            </div>
                                            <span class="text-xs text-purple-600 dark:text-purple-400 font-semibold">Instagram • UK</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-gray-950 dark:text-white font-mono">84,000</span>
                                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-bold">+$84.00</span>
                                    </div>
                                </div>

                                <!-- VC -->
                                <div class="p-4 rounded-xl bg-gray-50/90 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 hover:bg-white dark:hover:bg-[#181822] transition-all flex items-center justify-between group shadow-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-red-500 to-amber-600 flex items-center justify-center font-bold text-white text-xs shadow-xs">
                                            VC
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-1">
                                                <span class="text-sm font-semibold text-gray-950 dark:text-white">@ViralCut</span>
                                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400"></i>
                                            </div>
                                            <span class="text-xs text-red-600 dark:text-red-400 font-semibold">Shorts • Canada</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-gray-950 dark:text-white font-mono">62,000</span>
                                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-bold">+$62.00</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-gray-200/80 dark:border-white/[0.05] flex items-center justify-between text-xs text-gray-600 dark:text-[#71717A]">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
                                    Eligible views verified via API metrics &amp; fraud heuristic filters
                                </span>
                                <span class="font-mono text-[11px] text-violet-700 dark:text-violet-400 font-bold">
                                    One Campaign → Multi-Creator Scaled Reach
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ==========================================
         LIVE NETWORK ACTIVITY MARQUEE TICKER
         ========================================== -->
    <div class="border-y border-gray-200/90 dark:border-white/[0.08] bg-white/90 dark:bg-[#0B0B10]/80 backdrop-blur-md py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-bold text-gray-950 dark:text-[#F7F7F5] uppercase tracking-wider">Live Network Activity</span>
            </div>

            <div class="flex flex-wrap items-center gap-6 sm:gap-10 text-gray-800 dark:text-[#D4D4D8]">
                <div class="flex items-center gap-2">
                    <i data-lucide="flame" class="w-4 h-4 text-amber-500 dark:text-amber-400"></i>
                    <span><strong class="text-gray-950 dark:text-white font-bold">14</strong> campaigns active</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="film" class="w-4 h-4 text-violet-600 dark:text-violet-400"></i>
                    <span><strong class="text-gray-950 dark:text-white font-bold">37</strong> clips submitted recently</span>
                </div>
                <div class="flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    <span><strong class="text-gray-950 dark:text-white font-bold">8</strong> countries generating views</span>
                </div>
            </div>

            <div class="hidden lg:block text-gray-500 dark:text-[#71717A] text-[11px] font-medium">Demo telemetry verified</div>
        </div>
    </div>

    <!-- ==========================================
         SECTION 2: HOW IT WORKS (SYSTEM ARCHITECTURE)
         ========================================== -->
    <section id="how-it-works" class="py-24 lg:py-32 relative bg-[#FAFAFB] dark:bg-[#070708] border-b border-gray-200/90 dark:border-white/[0.08] overflow-hidden">
        
        <!-- Flowing Fiber-Optic Light Cables Animation Canvas -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden" style="position: absolute; inset: 0; width: 100%; height: 100%; z-index: 1;">
            <canvas id="light-cables-canvas" style="position: absolute; inset: 0; width: 100%; height: 100%; display: block; opacity: 0.65;"></canvas>
        </div>

        <!-- Modern Cyber Tech Dot Matrix & Ambient Glow (No harsh white/dark glare) -->
        <div class="absolute inset-0 pointer-events-none opacity-[0.04] dark:opacity-[0.08]" style="background-image: radial-gradient(#8b5cf6 1.5px, transparent 1.5px); background-size: 28px 28px; z-index: 2;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[450px] bg-gradient-to-r from-violet-600/15 via-indigo-600/10 to-cyan-500/10 blur-[160px] rounded-full pointer-events-none z-0"></div>

        <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            <div class="text-center max-w-4xl mx-auto mb-16 lg:mb-20">
                <span class="text-xs sm:text-sm font-mono font-bold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-3.5">
                    System Architecture
                </span>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-display font-extrabold text-gray-950 dark:text-[#F7F7F5] tracking-tight">
                    One Campaign. Multiple Creators. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500 dark:from-white dark:via-violet-200 dark:to-[#A1A1AA]">
                        Measurable Distribution.
                    </span>
                </h2>
                <p class="mt-5 text-base sm:text-lg text-gray-700 dark:text-[#A1A1AA] max-w-2xl mx-auto font-normal leading-relaxed">
                    A transparent four-step performance pipeline connecting video assets with organic viral reach.
                </p>
            </div>

            <div class="relative">
                <!-- Glowing Continuous Pipeline Rail -->
                <div class="hidden xl:block absolute top-[110px] left-16 right-16 h-0.5 bg-gradient-to-r from-violet-600 via-indigo-500 to-emerald-500 opacity-40 pointer-events-none"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-7 relative z-10">
                    
                    <!-- 01 FUND -->
                    <div class="bg-white dark:bg-[#121218] rounded-3xl p-8 sm:p-9 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/60 shadow-md hover:shadow-2xl hover:shadow-violet-600/15 dark:shadow-xl transition-all duration-300 relative group flex flex-col justify-between card-lift min-h-[350px]">
                        <div>
                            <div class="flex items-center justify-between mb-7">
                                <span class="text-sm font-mono font-bold px-3 py-1.5 rounded-lg bg-violet-100 text-violet-800 border border-violet-200 dark:bg-white/[0.08] dark:text-[#D4D4D8] dark:border-white/10 shadow-xs">01</span>
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500/20 to-indigo-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-xs">
                                    <i data-lucide="upload" class="w-6 h-6 text-violet-600 dark:text-violet-400"></i>
                                </div>
                            </div>
                            <span class="text-xs font-mono tracking-widest uppercase text-violet-600 dark:text-violet-400 block font-bold mb-1">FUND</span>
                            <h3 class="text-xl sm:text-2xl font-display font-extrabold text-gray-950 dark:text-white mb-3">Upload &amp; Configure</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                                Brands upload long-form content, set view targets, select target countries and fund the campaign pool.
                            </p>
                        </div>
                        <div class="mt-8 pt-5 border-t border-gray-200/80 dark:border-white/[0.06] flex items-center justify-between text-xs font-mono text-gray-600 dark:text-[#71717A]">
                            <span class="font-bold">Phase 01</span>
                            <span class="group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors font-bold flex items-center gap-1">Continuous Pipeline →</span>
                        </div>
                    </div>

                    <!-- 02 CLIP -->
                    <div class="bg-white dark:bg-[#121218] rounded-3xl p-8 sm:p-9 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/60 shadow-md hover:shadow-2xl hover:shadow-violet-600/15 dark:shadow-xl transition-all duration-300 relative group flex flex-col justify-between card-lift min-h-[350px]">
                        <div>
                            <div class="flex items-center justify-between mb-7">
                                <span class="text-sm font-mono font-bold px-3 py-1.5 rounded-lg bg-purple-100 text-purple-800 border border-purple-200 dark:bg-white/[0.08] dark:text-[#D4D4D8] dark:border-white/10 shadow-xs">02</span>
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500/20 to-pink-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-xs">
                                    <i data-lucide="scissors" class="w-6 h-6 text-purple-600 dark:text-purple-400"></i>
                                </div>
                            </div>
                            <span class="text-xs font-mono tracking-widest uppercase text-purple-600 dark:text-purple-400 block font-bold mb-1">CLIP</span>
                            <h3 class="text-xl sm:text-2xl font-display font-extrabold text-gray-950 dark:text-white mb-3">Create Short-Form</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                                High-caliber clippers discover the campaign, extract the best hooks, and edit vertical 9:16 video assets.
                            </p>
                        </div>
                        <div class="mt-8 pt-5 border-t border-gray-200/80 dark:border-white/[0.06] flex items-center justify-between text-xs font-mono text-gray-600 dark:text-[#71717A]">
                            <span class="font-bold">Phase 02</span>
                            <span class="group-hover:text-purple-600 dark:group-hover:text-purple-300 transition-colors font-bold flex items-center gap-1">Continuous Pipeline →</span>
                        </div>
                    </div>

                    <!-- 03 DISTRIBUTE -->
                    <div class="bg-white dark:bg-[#121218] rounded-3xl p-8 sm:p-9 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/60 shadow-md hover:shadow-2xl hover:shadow-violet-600/15 dark:shadow-xl transition-all duration-300 relative group flex flex-col justify-between card-lift min-h-[350px]">
                        <div>
                            <div class="flex items-center justify-between mb-7">
                                <span class="text-sm font-mono font-bold px-3 py-1.5 rounded-lg bg-blue-100 text-blue-800 border border-blue-200 dark:bg-white/[0.08] dark:text-[#D4D4D8] dark:border-white/10 shadow-xs">03</span>
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500/20 to-cyan-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-xs">
                                    <i data-lucide="share-2" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                                </div>
                            </div>
                            <span class="text-xs font-mono tracking-widest uppercase text-blue-600 dark:text-blue-400 block font-bold mb-1">DISTRIBUTE</span>
                            <h3 class="text-xl sm:text-2xl font-display font-extrabold text-gray-950 dark:text-white mb-3">Publish Everywhere</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                                Creators publish clips to TikTok, YouTube Shorts, Instagram Reels &amp; X with mandatory campaign hashtags.
                            </p>
                        </div>
                        <div class="mt-8 pt-5 border-t border-gray-200/80 dark:border-white/[0.06] flex items-center justify-between text-xs font-mono text-gray-600 dark:text-[#71717A]">
                            <span class="font-bold">Phase 03</span>
                            <span class="group-hover:text-blue-600 dark:group-hover:text-blue-300 transition-colors font-bold flex items-center gap-1">Continuous Pipeline →</span>
                        </div>
                    </div>

                    <!-- 04 VERIFY -->
                    <div class="bg-white dark:bg-[#121218] rounded-3xl p-8 sm:p-9 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/60 shadow-md hover:shadow-2xl hover:shadow-violet-600/15 dark:shadow-xl transition-all duration-300 relative group flex flex-col justify-between card-lift min-h-[350px]">
                        <div>
                            <div class="flex items-center justify-between mb-7">
                                <span class="text-sm font-mono font-bold px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-white/[0.08] dark:text-[#D4D4D8] dark:border-white/10 shadow-xs">04</span>
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/10 border border-gray-200 dark:border-white/10 flex items-center justify-center shadow-xs">
                                    <i data-lucide="shield-check" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                            </div>
                            <span class="text-xs font-mono tracking-widest uppercase text-emerald-600 dark:text-emerald-400 block font-bold mb-1">VERIFY</span>
                            <h3 class="text-xl sm:text-2xl font-display font-extrabold text-gray-950 dark:text-white mb-3">Audit &amp; Automatic Rewards</h3>
                            <p class="text-sm sm:text-base text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                                GEZUX verifies eligible view metrics and credits creator balances according to campaign rates.
                            </p>
                        </div>
                        <div class="mt-8 pt-5 border-t border-gray-200/80 dark:border-white/[0.06] flex items-center justify-between text-xs font-mono text-gray-600 dark:text-[#71717A]">
                            <span class="font-bold">Phase 04</span>
                            <span class="group-hover:text-emerald-600 dark:group-hover:text-emerald-300 transition-colors font-bold flex items-center gap-1">Continuous Pipeline →</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ==========================================
         SECTION 3: WHERE DO YOU FIT? (ROLE SELECTOR)
         ========================================== -->
    <section id="roles" class="py-24 lg:py-32 relative bg-white dark:bg-[#070708] border-b border-gray-200/90 dark:border-white/[0.08] overflow-hidden">
        
        <div class="absolute top-1/2 left-1/4 w-[500px] h-[300px] bg-violet-600/10 blur-[130px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/2 right-1/4 w-[500px] h-[300px] bg-blue-600/10 blur-[130px] rounded-full pointer-events-none"></div>

        <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            <div class="text-center max-w-4xl mx-auto mb-16 lg:mb-20">
                <span class="text-xs sm:text-sm font-mono font-bold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-3.5">
                    Two Sides of the Marketplace
                </span>
                <h2 class="text-3xl sm:text-5xl lg:text-6xl font-display font-extrabold text-gray-950 dark:text-[#F7F7F5] tracking-tight">
                    Where Do You Fit?
                </h2>
                <p class="mt-5 text-base sm:text-lg text-gray-700 dark:text-[#A1A1AA] max-w-2xl mx-auto leading-relaxed">
                    Whether you edit high-retention vertical clips or own long-form intellectual property, GEZUX provides the infrastructure.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Card 1: For Clippers -->
                <div class="bg-white dark:bg-[#0B0B10] rounded-2xl p-8 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 shadow-md hover:shadow-xl hover:shadow-violet-500/10 dark:shadow-xl transition-all duration-300 relative flex flex-col justify-between group overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-60 h-60 bg-violet-600/10 blur-[90px] rounded-full pointer-events-none"></div>
                    
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300 text-xs font-mono font-bold mb-6">
                            <i data-lucide="scissors" class="w-3.5 h-3.5"></i>
                            FOR CLIPPERS
                        </div>

                        <h3 class="text-2xl sm:text-3xl font-display font-extrabold text-gray-950 dark:text-white tracking-tight">
                            Turn Your Editing Skills <br>
                            <span class="text-violet-600 dark:text-violet-400">Into Income.</span>
                        </h3>

                        <p class="mt-4 text-sm text-gray-700 dark:text-[#A1A1AA] leading-relaxed">
                            Browse pre-approved raw content from global podcasts, creators, and technology companies. Pick your favorites, cut dynamic vertical hooks, post across your accounts, and get paid for verified eligible views.
                        </p>

                        <div class="mt-6 space-y-2.5">
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Instant access to verified high-budget campaigns</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Guaranteed prompt payout upon verified view thresholds</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Multi-platform support (TikTok, Shorts, Reels, X)</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Direct earnings ledger with zero hidden transaction deductions</span>
                            </div>
                        </div>

                        <!-- Mock Balance Widget -->
                        <div class="mt-8 p-4 rounded-xl bg-gray-50/90 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-xs space-y-3">
                            <div class="flex items-center justify-between text-xs pb-2 border-b border-gray-200/80 dark:border-white/[0.05]">
                                <span class="text-gray-600 dark:text-[#A1A1AA] font-mono font-medium">Creator Balance Preview</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-mono font-bold">$1,420.50 Available</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div class="p-2.5 rounded-lg bg-white dark:bg-black/40 border border-gray-200/80 dark:border-white/5">
                                    <span class="text-gray-500 dark:text-[#71717A] block font-mono">Verified Views</span>
                                    <span class="font-mono font-extrabold text-gray-950 dark:text-white text-sm">1.84M</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-white dark:bg-black/40 border border-gray-200/80 dark:border-white/5">
                                    <span class="text-gray-500 dark:text-[#71717A] block font-mono">Active Submissions</span>
                                    <span class="font-mono font-extrabold text-gray-950 dark:text-white text-sm">12 Clips</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200/80 dark:border-white/[0.08]">
                        <a href="{{ url('/marketplace') }}" 
                           class="starfield-btn starfield-primary w-full inline-flex items-center justify-center gap-2 font-display text-sm font-semibold transition-transform hover:scale-[1.01]"
                           style="padding: 12px 20px; background: rgb(13, 12, 24); color: rgb(247, 247, 245); border-radius: 12px; border: 1.5px solid rgba(139, 92, 246, 0.4); box-shadow: 0 0 15px -3px rgba(124, 58, 237, 0.3);">
                            <span class="relative z-10">Explore Campaigns</span>
                            <i data-lucide="arrow-right" class="relative z-10 w-4 h-4 text-violet-400"></i>
                        </a>
                    </div>
                </div>

                <!-- Card 2: For Brands & Creators -->
                <div class="bg-white dark:bg-[#0B0B10] rounded-2xl p-8 border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 shadow-md hover:shadow-xl hover:shadow-violet-500/10 dark:shadow-xl transition-all duration-300 relative flex flex-col justify-between group overflow-hidden">
                    <div class="absolute -top-24 -right-24 w-60 h-60 bg-blue-600/10 blur-[90px] rounded-full pointer-events-none"></div>

                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300 text-xs font-mono font-bold mb-6">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                            FOR BRANDS &amp; CREATORS
                        </div>

                        <h3 class="text-2xl sm:text-3xl font-display font-extrabold text-gray-950 dark:text-white tracking-tight">
                            Turn One Video Into A <br>
                            <span class="text-violet-600 dark:text-violet-400">Distribution Network.</span>
                        </h3>

                        <p class="mt-4 text-sm text-gray-700 dark:text-[#A1A1AA] leading-relaxed">
                            Stop relying on single uploads. Launch a campaign with defined reward parameters ($1.00 per 1,000 views) and watch dozens of skilled video editors scale your presence across social algorithms simultaneously.
                        </p>

                        <div class="mt-6 space-y-2.5">
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Pay only for verified eligible view metrics, not empty promises</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Granular target country filters (USA, UK, Tier 1, Global)</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Custom brand safety rules, mandatory hashtags &amp; handles</span>
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-gray-800 dark:text-[#D4D4D8]">
                                <div class="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </div>
                                <span>Live real-time performance analytics &amp; top clipper leaderboards</span>
                            </div>
                        </div>

                        <!-- Brand Vault Preview -->
                        <div class="mt-8 p-4 rounded-xl bg-gray-50/90 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-xs space-y-3">
                            <div class="flex items-center justify-between text-xs pb-2 border-b border-gray-200/80 dark:border-white/[0.05]">
                                <span class="text-gray-600 dark:text-[#A1A1AA] font-mono font-medium">Campaign Analytics Preview</span>
                                <span class="text-blue-600 dark:text-blue-400 font-mono font-bold">11.8M Total Verified Reach</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div class="p-2.5 rounded-lg bg-white dark:bg-black/40 border border-gray-200/80 dark:border-white/5">
                                    <span class="text-gray-500 dark:text-[#71717A] block font-mono">Effective CPV</span>
                                    <span class="font-mono font-extrabold text-gray-950 dark:text-white text-sm">$0.0012 / view</span>
                                </div>
                                <div class="p-2.5 rounded-lg bg-white dark:bg-black/40 border border-gray-200/80 dark:border-white/5">
                                    <span class="text-gray-500 dark:text-[#71717A] block font-mono">Active Clippers</span>
                                    <span class="font-mono font-extrabold text-gray-950 dark:text-white text-sm">48 Creators</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200/80 dark:border-white/[0.08]">
                        <a href="{{ url('/brand') }}" 
                           class="starfield-btn starfield-primary w-full inline-flex items-center justify-center gap-2 font-display text-sm font-semibold transition-transform hover:scale-[1.01]"
                           style="padding: 12px 20px; background: rgb(13, 12, 24); color: rgb(247, 247, 245); border-radius: 12px; border: 1.5px solid rgba(139, 92, 246, 0.4); box-shadow: 0 0 15px -3px rgba(124, 58, 237, 0.3);">
                            <span class="relative z-10">Launch Campaign</span>
                            <i data-lucide="arrow-right" class="relative z-10 w-4 h-4 text-violet-400"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ==========================================
         SECTION 4: CAMPAIGNS LOOKING FOR CREATORS
         ========================================== -->
    <section class="py-24 lg:py-32 relative bg-[#FAFAFB] dark:bg-[#070708] border-b border-gray-200/90 dark:border-white/[0.08] overflow-hidden">
        
        <!-- Three.js 3D Wave Particle Mesh Container -->
        <div id="wave-field-container" class="absolute inset-0 pointer-events-none opacity-40 dark:opacity-60 overflow-hidden" style="width: 100%; height: 100%;"></div>

        <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-2">
                        Live Bounty Board
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-display font-extrabold text-gray-950 dark:text-white tracking-tight">
                        Campaigns Looking for Creators
                    </h2>
                    <p class="mt-2 text-sm text-gray-700 dark:text-[#A1A1AA]">
                        High-budget verified campaigns open for immediate video clip creation and distribution.
                    </p>
                </div>
                <a href="{{ url('/marketplace') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition-colors shadow-sm shrink-0">
                    <span>Explore All Campaigns</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>

            <!-- Campaign Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($featuredCampaigns ?? [] as $campaign)
                    <div class="bg-white dark:bg-[#0B0B10] rounded-2xl border border-gray-200/90 dark:border-white/[0.08] hover:border-violet-500/50 shadow-sm hover:shadow-xl hover:shadow-violet-500/10 dark:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden group">
                        
                        <!-- Media Banner -->
                        <div class="relative h-44 bg-gradient-to-tr from-purple-950 via-indigo-950 to-neutral-900 overflow-hidden">
                            @if($campaign->thumbnail_url)
                                <img src="{{ $campaign->thumbnail_url }}" alt="{{ $campaign->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                            
                            <div class="absolute top-3 left-3 right-3 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full bg-black/70 backdrop-blur-md text-[11px] font-mono text-white border border-white/20 font-medium">
                                    {{ $campaign->category ?? 'Technology & AI' }}
                                </span>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-[10px] font-mono font-bold">
                                    ACTIVE
                                </span>
                            </div>

                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-black/70 backdrop-blur-md border border-white/20 flex items-center justify-center font-display font-bold text-white text-xs">
                                        {{ strtoupper(substr($campaign->brand->name ?? 'GE', 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-bold text-white drop-shadow">
                                        {{ $campaign->brand->name ?? 'Gezux Brand' }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-mono text-amber-300 font-semibold drop-shadow">
                                    3d left
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <h3 class="text-base font-bold text-gray-950 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors line-clamp-1">
                                    {{ $campaign->title }}
                                </h3>
                                <p class="mt-2 text-xs text-gray-600 dark:text-[#A1A1AA] line-clamp-2 leading-relaxed">
                                    {{ $campaign->description }}
                                </p>

                                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/[0.05] grid grid-cols-2 gap-4 text-xs font-mono">
                                    <div>
                                        <span class="text-gray-500 dark:text-[#71717A] text-[11px] block">Reward Rate</span>
                                        <span class="font-bold text-violet-600 dark:text-violet-400 text-sm">${{ number_format($campaign->reward_rate_per_1k_views, 2) }} / 1K</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-500 dark:text-[#71717A] text-[11px] block">Total Budget</span>
                                        <span class="font-bold text-gray-950 dark:text-white text-sm">${{ number_format($campaign->total_budget, 2) }}</span>
                                    </div>
                                </div>

                                <!-- Progress -->
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-[11px] font-mono text-gray-600 dark:text-[#71717A] mb-1">
                                        <span>{{ number_format($campaign->current_verified_views) }} verified</span>
                                        <span class="font-bold">{{ round(min(100, ($campaign->current_verified_views / max(1, $campaign->target_views)) * 100)) }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-white/[0.06] overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-violet-600 to-indigo-500 rounded-full" 
                                             style="width: {{ min(100, ($campaign->current_verified_views / max(1, $campaign->target_views)) * 100) }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/[0.05] flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-[10px] font-mono font-medium">TikTok</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-[10px] font-mono font-medium">Reels</span>
                                </div>

                                <button type="button" 
                                        @click="openSubmitModal('{{ $campaign->id }}', '{{ addslashes($campaign->title) }}', {{ $campaign->reward_rate_per_1k_views }})"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-violet-600 hover:bg-violet-500 text-white transition-colors flex items-center gap-1 shadow-xs">
                                    <span>Clip This</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                @empty
                    <!-- Fallback Featured Campaign Card matching gezux.com -->
                    <div class="bg-white dark:bg-[#0B0B10] rounded-2xl border border-gray-200/90 dark:border-white/[0.08] shadow-sm hover:shadow-xl hover:shadow-violet-500/10 dark:shadow-xl flex flex-col justify-between overflow-hidden group">
                        <div class="relative h-44 bg-gradient-to-tr from-violet-950 via-purple-900 to-indigo-950 overflow-hidden p-4 flex flex-col justify-between">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                            <div class="relative z-10 flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full bg-black/70 backdrop-blur-md text-[11px] font-mono text-white border border-white/20">Technology &amp; AI</span>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 text-[10px] font-mono font-bold">ACTIVE</span>
                            </div>
                            <div class="relative z-10 flex items-end justify-between">
                                <div>
                                    <h4 class="text-base font-bold text-white drop-shadow">Acme AI Studio</h4>
                                </div>
                                <span class="text-xs text-amber-300 font-mono font-bold drop-shadow">3d left</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-base font-bold text-gray-950 dark:text-white">The Future of AI — Podcast #42</h3>
                            <p class="mt-2 text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">We sat down with leading neural network researchers to discuss autonomous robotics, reasoning models, and agentic workflows.</p>
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/[0.05] grid grid-cols-2 gap-4 text-xs font-mono">
                                <div>
                                    <span class="text-gray-500 dark:text-[#71717A] text-[11px] block">Reward Rate</span>
                                    <span class="font-bold text-violet-600 dark:text-violet-400 text-sm">$1.00 / 1K</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-gray-500 dark:text-[#71717A] text-[11px] block">Total Budget</span>
                                    <span class="font-bold text-gray-950 dark:text-white text-sm">$5,000</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] font-mono text-gray-600 dark:text-[#71717A] mb-1">
                                    <span>3,742,810 verified</span>
                                    <span class="font-bold">74.9%</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-white/[0.06] overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-violet-600 to-indigo-500 rounded-full" style="width: 74.9%"></div>
                                </div>
                            </div>
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/[0.05] flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-[10px] text-gray-500 font-mono">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">TikTok</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">Instagram</span>
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">Shorts</span>
                                </div>
                                <a href="{{ url('/marketplace') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-violet-600 hover:bg-violet-500 text-white transition-colors flex items-center gap-1 shadow-xs">
                                    <span>View Campaign</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Bottom Center CTA -->
            <div class="mt-12 text-center">
                <a href="{{ url('/marketplace') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-xs font-semibold text-white bg-black hover:bg-neutral-800 dark:bg-white dark:text-black dark:hover:bg-neutral-200 transition-colors shadow-md">
                    <span>Explore All Campaigns</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- ==========================================
         SECTION 5: BUILT AROUND VERIFIABLE PERFORMANCE
         ========================================== -->
    <section id="trust" class="py-24 lg:py-32 relative bg-white dark:bg-[#070708] border-b border-gray-200/90 dark:border-white/[0.08] overflow-hidden">
        
        <!-- Data Strings / Telemetry Light Beams Canvas -->
        <canvas id="data-strings-canvas" style="position: absolute; inset: 0px; width: 100%; height: 100%; pointer-events: none;" class="opacity-40 dark:opacity-50"></canvas>

        <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-violet-600/10 dark:bg-violet-600/15 blur-[140px] rounded-full pointer-events-none"></div>

        <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
            
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mb-16 pb-8 border-b border-gray-200/90 dark:border-white/[0.08]">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-50 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300 text-xs font-mono font-bold mb-3">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"></i>
                        TRUST &amp; VERIFICATION ENGINE
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-display font-extrabold text-gray-950 dark:text-[#F7F7F5] tracking-tight">
                        Built Around <br>
                        <span class="text-violet-600 dark:text-violet-400">
                            Verifiable Performance.
                        </span>
                    </h2>
                </div>
                <div class="lg:max-w-md">
                    <p class="text-sm text-gray-700 dark:text-[#A1A1AA] leading-relaxed">
                        Every campaign is executed with measurable integrity. Creators earn for legitimate attention, while brands receive verifiable distribution with complete telemetry transparency.
                    </p>
                    <div class="mt-3 flex items-center gap-2 text-xs font-mono text-emerald-700 dark:text-emerald-400 font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Multi-Tier Heuristics &amp; View Auditing Active</span>
                    </div>
                </div>
            </div>

            <!-- 4 Security Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                
                <div class="p-6 rounded-2xl bg-white/95 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-sm hover:shadow-xl hover:shadow-emerald-500/5 hover:border-emerald-500/40 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center mb-4 shadow-xs">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <h4 class="text-base font-bold text-gray-950 dark:text-white mb-2">View Verification</h4>
                    <p class="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                        Eligible submitted views are independently audited before rewards are calculated and settled.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white/95 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-sm hover:shadow-xl hover:shadow-violet-500/5 hover:border-violet-500/40 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center mb-4 shadow-xs">
                        <i data-lucide="file-text" class="w-5 h-5 text-violet-600 dark:text-violet-400"></i>
                    </div>
                    <h4 class="text-base font-bold text-gray-950 dark:text-white mb-2">Campaign Rules</h4>
                    <p class="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                        Every campaign clearly specifies aspect ratios, durations, hashtags, mentions, and safety criteria.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white/95 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-sm hover:shadow-xl hover:shadow-blue-500/5 hover:border-blue-500/40 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center mb-4 shadow-xs">
                        <i data-lucide="user-check" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h4 class="text-base font-bold text-gray-950 dark:text-white mb-2">Creator Verification</h4>
                    <p class="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                        Connected social channels and verified track records allow brands to review creator quality.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-white/95 dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] shadow-sm hover:shadow-xl hover:shadow-amber-500/5 hover:border-amber-500/40 transition-all">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-500/30 flex items-center justify-center mb-4 shadow-xs">
                        <i data-lucide="shield-alert" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h4 class="text-base font-bold text-gray-950 dark:text-white mb-2">Fraud Detection</h4>
                    <p class="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                        Multi-layer heuristics detect velocity spikes, duplicate URLs, and inorganic traffic patterns.
                    </p>
                </div>

            </div>

            <!-- GEZUX Visual Pipeline -->
            <div class="bg-gray-50/80 dark:bg-[#0B0B10] rounded-2xl p-8 border border-gray-200/90 dark:border-white/[0.08] shadow-xs">
                <div class="text-center mb-8">
                    <span class="text-[11px] font-mono uppercase tracking-widest text-violet-600 dark:text-violet-400 font-bold block mb-1">
                        MARKETPLACE MECHANICS PIPELINE
                    </span>
                    <h3 class="text-2xl font-display font-bold text-gray-950 dark:text-white">The GEZUX Engine</h3>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-violet-100 dark:bg-violet-950/60 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                            <i data-lucide="video" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">LONG VIDEO</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">Asset Uploaded</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">CREATORS</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">Claim Campaign</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                            <i data-lucide="scissors" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">SHORT CLIPS</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">9:16 Editing</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                            <i data-lucide="share-2" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">SOCIALS</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">Multi-Platform</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">VERIFIED VIEWS</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">Audited Analytics</span>
                    </div>

                    <div class="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200/90 dark:border-white/[0.08] text-center shadow-xs">
                        <div class="w-9 h-9 mx-auto mb-2 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                            <i data-lucide="award" class="w-4 h-4"></i>
                        </div>
                        <span class="text-[11px] font-mono font-bold block text-gray-950 dark:text-white uppercase">REWARDS</span>
                        <span class="text-[10px] text-gray-600 dark:text-[#71717A]">Direct Payouts</span>
                    </div>
                </div>

                <p class="mt-6 text-center text-[11px] text-gray-500 dark:text-[#71717A]">
                    * All calculations and rewards are determined by verified performance criteria per individual campaign terms.
                </p>
            </div>

        </div>
    </section>

    <!-- ==========================================
         SECTION 6: FINAL CTA (ONE VIDEO CAN GO MUCH FURTHER)
         ========================================== -->
    <section class="py-28 lg:py-40 relative bg-[#FAFAFB] dark:bg-[#070708] overflow-hidden border-t border-gray-200/90 dark:border-white/[0.08]">
        
        <!-- Elegant Ambient Constellation Canvas Background (Replaced Tornado) -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden" style="position: absolute; inset: 0; width: 100%; height: 100%; z-index: 1;">
            <canvas id="cta-ambient-canvas" style="position: absolute; inset: 0; width: 100%; height: 100%; display: block;"></canvas>
        </div>

        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-violet-600/15 dark:bg-violet-600/20 blur-[160px] rounded-full pointer-events-none z-0"></div>
        <div class="absolute top-1/3 left-1/3 w-[500px] h-[300px] bg-indigo-600/10 blur-[140px] rounded-full pointer-events-none z-0"></div>

        <div class="max-w-5xl mx-auto px-6 sm:px-8 text-center relative z-10">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-violet-50 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300 text-xs font-mono font-bold mb-6 shadow-xs">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-violet-600 dark:text-violet-400"></i>
                Scale Organic Reach
            </div>

            <h2 class="text-4xl sm:text-6xl font-display font-extrabold text-gray-950 dark:text-white tracking-tight">
                One Video Can Go <br>
                <span class="text-violet-600 dark:text-violet-400">
                    Much Further.
                </span>
            </h2>

            <p class="mt-6 text-base sm:text-lg text-gray-700 dark:text-[#A1A1AA] max-w-xl mx-auto leading-relaxed">
                Launch a campaign and put your content in the hands of creators around the world. Or monetize your video cutting skills with verified bounties.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/brand') }}" 
                   class="starfield-btn starfield-primary w-full sm:w-auto inline-flex items-center justify-center gap-2 font-display text-sm sm:text-base font-bold">
                    <span class="relative z-10 font-medium tracking-wide">Launch a Campaign</span>
                    <i data-lucide="arrow-right" class="relative z-10 w-4 h-4 text-violet-400"></i>
                </a>

                <a href="{{ url('/marketplace') }}" 
                   class="starfield-btn starfield-secondary w-full sm:w-auto inline-flex items-center justify-center gap-2 font-display text-sm sm:text-base font-bold">
                    <i data-lucide="video" class="relative z-10 w-4 h-4 text-violet-400"></i>
                    <span class="relative z-10 font-medium tracking-wide">Start Clipping</span>
                </a>
            </div>

        </div>
    </section>

</div>
@endsection
