@extends('layouts.app')

@section('title', 'Clipper Studio — Creator Operations | GEZUX')
@section('meta_description', 'Track short-form clip earnings, bank payout destination, verified view telemetry, and instant payments.')

@section('content')
<div class="relative pt-28 pb-24 min-h-screen bg-[#070708] overflow-hidden text-[#F7F7F5]" x-data="{
    activeTab: 'submissions',
    payoutModalOpen: false,
    clipModalOpen: false,
    selectedCampaignId: '',
    selectedCampaignTitle: '',
    searchQuery: '',
    selectedPlatformFilter: 'all'
}" @open-clip-modal.window="selectedCampaignId = $event.detail.campaignId || ''; selectedCampaignTitle = $event.detail.campaignTitle || ''; clipModalOpen = true;">

    <!-- Ambient background glow orbs -->
    <div class="absolute top-16 left-1/4 -translate-x-1/2 w-[600px] h-[350px] bg-violet-600/15 blur-[140px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute top-48 right-10 w-[500px] h-[320px] bg-purple-600/15 blur-[130px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 left-1/3 w-[550px] h-[280px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">
        
        <!-- Alerts & Feedback -->
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 flex items-center justify-between text-xs font-mono">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
        @endif
        @if(session('info'))
            <div class="p-4 rounded-xl bg-violet-500/10 border border-violet-500/30 text-violet-300 flex items-center justify-between text-xs font-mono">
                <div class="flex items-center gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-violet-400"></i>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-violet-400 hover:text-white">&times;</button>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-mono space-y-1">
                @foreach($errors->all() as $err)
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                        <span>{{ $err }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Portal Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-white/[0.08]">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-600/30 to-purple-600/30 border border-violet-500/40 flex items-center justify-center text-violet-400 font-bold shadow-xl shadow-violet-950/50 backdrop-blur-md">
                        <i data-lucide="video" class="w-7 h-7 text-violet-400"></i>
                    </div>
                    <span class="absolute -bottom-1 -right-1 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#070708]"></span>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-display font-bold text-white tracking-tight">Clipper Studio</h1>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-violet-500/20 text-violet-300 border border-violet-500/30">
                            CREATOR PORTAL
                        </span>
                    </div>
                    <p class="text-xs text-[#A1A1AA] mt-1">
                        Welcome back, <strong class="text-white">{{ $user->name }}</strong>. Payouts sent directly to your registered bank account or mobile wallet.
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="button" @click="activeTab = 'bank'"
                        class="px-4 py-2.5 rounded-xl text-xs font-semibold text-white bg-white/[0.05] hover:bg-white/[0.1] border border-white/10 hover:border-violet-500/40 transition-all flex items-center gap-2">
                    <i data-lucide="landmark" class="w-4 h-4 text-violet-400"></i>
                    <span>Bank & Payout Setup</span>
                </button>
                <button type="button" @click="clipModalOpen = true; selectedCampaignId = ''; selectedCampaignTitle = '';"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-900/30 flex items-center gap-2 transition-all uppercase tracking-wider">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Submit New Clip</span>
                </button>
            </div>
        </div>

        <!-- 4 Key Creator Telemetry Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Metric 1: Total Transferred / Paid Out -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Total Payouts Received</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                        <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    ${{ number_format($wallet->total_withdrawn, 2) }}
                </div>
                <span class="text-[11px] text-emerald-400 flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-3 h-3 text-emerald-400"></i> Verified bank transfers
                </span>
            </div>

            <!-- Metric 2: Verified Views -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Observed Video Views</span>
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    {{ number_format($totalViews) }}
                </div>
                <span class="text-[11px] text-[#A1A1AA] flex items-center gap-1">
                    Across TikTok, Shorts & Reels
                </span>
            </div>

            <!-- Metric 3: Active Clips -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Clips Submitted</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                        <i data-lucide="film" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    {{ count($submissions) }}
                </div>
                <span class="text-[11px] text-violet-300 flex items-center gap-1">
                    <i data-lucide="sparkles" class="w-3 h-3 text-violet-400"></i> Performance tracked live
                </span>
            </div>

            <!-- Metric 4: Payout Destination Status -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-indigo-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Bank Payout Rail</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-sm font-bold text-white mb-1 truncate">
                    {{ $wallet->payout_bank_name ?: 'No Bank Added' }}
                </div>
                <span class="text-[11px] {{ $wallet->payout_account_number ? 'text-emerald-400' : 'text-amber-400' }} flex items-center gap-1">
                    <i data-lucide="{{ $wallet->payout_account_number ? 'check' : 'alert-circle' }}" class="w-3 h-3"></i>
                    {{ $wallet->payout_account_number ? 'Ready for Admin payout' : 'Click setup to add account' }}
                </span>
            </div>

        </div>

        <!-- Multi-Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-white/[0.08] pb-4 overflow-x-auto scrollbar-none">
            <button type="button" @click="activeTab = 'submissions'"
                    :class="activeTab === 'submissions' ? 'text-violet-400 border-b-2 border-violet-400 font-bold pb-2 -mb-4' : 'text-[#A1A1AA] hover:text-white pb-2 -mb-4'"
                    class="px-4 text-xs font-mono transition-colors flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="list" class="w-4 h-4"></i>
                <span>My Submitted Clips ({{ count($submissions) }})</span>
            </button>
            <button type="button" @click="activeTab = 'radar'"
                    :class="activeTab === 'radar' ? 'text-violet-400 border-b-2 border-violet-400 font-bold pb-2 -mb-4' : 'text-[#A1A1AA] hover:text-white pb-2 -mb-4'"
                    class="px-4 text-xs font-mono transition-colors flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="compass" class="w-4 h-4"></i>
                <span>Active Bounties ({{ count($availableCampaigns) }})</span>
            </button>
            <button type="button" @click="activeTab = 'bank'"
                    :class="activeTab === 'bank' ? 'text-violet-400 border-b-2 border-violet-400 font-bold pb-2 -mb-4' : 'text-[#A1A1AA] hover:text-white pb-2 -mb-4'"
                    class="px-4 text-xs font-mono transition-colors flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="landmark" class="w-4 h-4"></i>
                <span>Bank & Payout Details</span>
            </button>
        </div>

        <!-- TAB 1: SUBMISSIONS TABLE -->
        <div x-show="activeTab === 'submissions'" class="space-y-4">
            
            <!-- Table Search & Filter Bar -->
            <div class="p-4 rounded-xl bg-[#0B0B10] border border-white/[0.08] flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="relative w-full sm:w-72">
                    <i data-lucide="search" class="w-4 h-4 text-[#71717A] absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="searchQuery" placeholder="Filter by campaign or platform..."
                           class="w-full bg-[#121218] border border-white/10 rounded-xl pl-10 pr-4 py-2 text-xs text-white placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto overflow-x-auto">
                    <button type="button" @click="selectedPlatformFilter = 'all'"
                            :class="selectedPlatformFilter === 'all' ? 'bg-violet-600 text-white' : 'bg-[#121218] text-[#A1A1AA]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">All</button>
                    <button type="button" @click="selectedPlatformFilter = 'tiktok'"
                            :class="selectedPlatformFilter === 'tiktok' ? 'bg-violet-600 text-white' : 'bg-[#121218] text-[#A1A1AA]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">TikTok</button>
                    <button type="button" @click="selectedPlatformFilter = 'youtube'"
                            :class="selectedPlatformFilter === 'youtube' ? 'bg-violet-600 text-white' : 'bg-[#121218] text-[#A1A1AA]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">YouTube</button>
                    <button type="button" @click="selectedPlatformFilter = 'instagram'"
                            :class="selectedPlatformFilter === 'instagram' ? 'bg-violet-600 text-white' : 'bg-[#121218] text-[#A1A1AA]'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors">Instagram</button>
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-2xl bg-[#0B0B10] border border-white/[0.08] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] tracking-wider border-b border-white/[0.08]">
                            <tr>
                                <th class="py-3.5 px-6">Campaign & Brand</th>
                                <th class="py-3.5 px-6">Clip Video URL</th>
                                <th class="py-3.5 px-6">Platform</th>
                                <th class="py-3.5 px-6">Observed Views</th>
                                <th class="py-3.5 px-6">Payout Due</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Submitted</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05] text-[#D4D4D8]">
                            @forelse($submissions as $sub)
                                <tr class="hover:bg-white/[0.02] transition-colors"
                                    x-show="(selectedPlatformFilter === 'all' || '{{ strtolower($sub->platform) }}'.includes(selectedPlatformFilter)) &&
                                            (!searchQuery || '{{ strtolower($sub->campaign->title ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($sub->platform) }}'.includes(searchQuery.toLowerCase()))">
                                    <td class="py-4 px-6 font-medium text-white">
                                        <div class="font-bold text-sm">{{ $sub->campaign->title ?? 'Bounty Campaign' }}</div>
                                        <div class="text-[11px] text-[#71717A] font-mono">{{ $sub->campaign->brand->company_name ?? 'Advertiser' }}</div>
                                    </td>
                                    <td class="py-4 px-6 font-mono text-[11px]">
                                        <a href="{{ $sub->post_url }}" target="_blank" class="text-violet-400 hover:underline flex items-center gap-1">
                                            <span class="truncate max-w-[150px]">{{ $sub->post_url }}</span>
                                            <i data-lucide="external-link" class="w-3 h-3 flex-shrink-0"></i>
                                        </a>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/[0.06] border border-white/10 uppercase">
                                            {{ $sub->platform }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-mono font-bold text-white">
                                        {{ number_format($sub->observed_views) }}
                                    </td>
                                    <td class="py-4 px-6 font-mono font-bold text-emerald-400">
                                        ${{ number_format($sub->verified_reward ?? $sub->estimated_reward, 2) }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($sub->status === 'approved_by_brand')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-violet-500/10 border border-violet-500/30 text-violet-300 flex items-center gap-1 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-violet-400 animate-pulse"></span>
                                                Brand Approved (Pending Admin Payout)
                                            </span>
                                        @elseif($sub->status === 'verified')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center gap-1 w-max">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                Paid to Bank
                                            </span>
                                        @elseif($sub->status === 'rejected')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center gap-1 w-max">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center gap-1 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Pending Brand Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right text-[11px] text-[#71717A] font-mono">
                                        {{ $sub->created_at ? $sub->created_at->diffForHumans() : 'Just now' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#71717A]">
                                        <i data-lucide="film" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="font-medium text-white mb-1">No clip submissions yet</p>
                                        <p class="text-[11px] mb-4">Pick an active bounty from the Opportunity Radar tab and submit your short-form video link.</p>
                                        <button type="button" @click="activeTab = 'radar'" class="px-4 py-2 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                                            Explore Bounties
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- TAB 2: ACTIVE BOUNTIES RADAR -->
        <div x-show="activeTab === 'radar'" class="space-y-6" style="display: none;">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($availableCampaigns as $camp)
                    <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-mono font-bold text-violet-400">{{ $camp->brand->company_name ?? 'Verified Brand' }}</span>
                                <span class="text-xs font-mono text-emerald-400 font-bold">${{ number_format($camp->reward_rate_per_thousand, 2) }} / 1k</span>
                            </div>
                            <h4 class="font-display font-bold text-white text-base mb-2 group-hover:text-violet-300 transition-colors">{{ $camp->title }}</h4>
                            <p class="text-xs text-[#A1A1AA] mb-4 line-clamp-2 leading-relaxed">{{ $camp->description ?? 'Create viral hooks featuring key moments and post with campaign hashtag.' }}</p>
                        </div>
                        <div class="pt-4 border-t border-white/[0.05] flex items-center justify-between">
                            <span class="text-[11px] font-mono text-[#71717A]">Budget Pool: ${{ number_format($camp->remaining_budget, 0) }}</span>
                            <button type="button" @click="selectedCampaignId = '{{ $camp->id }}'; selectedCampaignTitle = '{{ addslashes($camp->title) }}'; clipModalOpen = true;"
                                    class="px-3.5 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                                Clip This
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-12 text-center text-[#71717A]">
                        <p class="text-white font-bold">No active bounties at this moment.</p>
                        <p class="text-xs mt-1">Check back shortly as new campaigns are published frequently.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- TAB 3: BANK & PAYOUT SETTINGS -->
        <div x-show="activeTab === 'bank'" class="space-y-6" style="display: none;">
            
            <div class="p-6 rounded-2xl bg-gradient-to-r from-violet-950/40 via-[#0B0B10] to-[#0B0B10] border border-violet-500/30">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                            <h3 class="text-base font-bold text-white">Manual Admin Bank Transfer Rail</h3>
                        </div>
                        <p class="text-xs text-[#A1A1AA] max-w-2xl leading-relaxed">
                            GEZUX pays clippers directly to their personal bank account or mobile wallet. You do <strong>not</strong> need to set up Stripe. When a brand approves your submitted clip, Admin reviews the clip and executes the wire/mobile payment straight into the account specified below.
                        </p>
                    </div>
                    <div class="px-4 py-2 rounded-xl bg-violet-500/10 border border-violet-500/30 text-violet-300 text-xs font-mono">
                        NO STRIPE ONBOARDING NEEDED
                    </div>
                </div>
            </div>

            <div class="max-w-2xl bg-[#0B0B10] border border-white/[0.08] rounded-2xl p-6 sm:p-8">
                <h4 class="text-sm font-bold text-white uppercase tracking-wider font-mono mb-4">Payout Account Details</h4>
                <form action="{{ route('web.clipper.payout-settings') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Bank / Mobile Wallet Provider Name *</label>
                        <input type="text" name="payout_bank_name" value="{{ old('payout_bank_name', $wallet->payout_bank_name) }}" required
                               placeholder="e.g. Chase / Bank of America / Meezan Bank / HBL / EasyPaisa / JazzCash / Wise"
                               class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Account Beneficiary / Legal Title *</label>
                        <input type="text" name="payout_account_holder" value="{{ old('payout_account_holder', $wallet->payout_account_holder ?: $user->name) }}" required
                               placeholder="Full name as printed on bank statement / ID"
                               class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Account Number / IBAN *</label>
                            <input type="text" name="payout_account_number" value="{{ old('payout_account_number', $wallet->payout_account_number) }}" required
                                   placeholder="Account # or International IBAN"
                                   class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-xs placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                        </div>
                        <div>
                            <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Phone Number (Mobile Transfer / SMS) *</label>
                            <input type="text" name="payout_phone_number" value="{{ old('payout_phone_number', $wallet->payout_phone_number) }}" required
                                   placeholder="e.g. +1 (555) 019-2834 or +92 300 1234567"
                                   class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-xs placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Routing / SWIFT Code (Optional for International Wire)</label>
                        <input type="text" name="payout_routing_swift" value="{{ old('payout_routing_swift', $wallet->payout_routing_swift) }}"
                               placeholder="e.g. MEZNPKKA or Routing 021000021"
                               class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-xs placeholder-[#71717A] focus:outline-none focus:border-violet-500">
                    </div>
                    <div class="pt-3">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-lg shadow-violet-900/30 transition-all">
                            Save Payout Bank Details
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

    <!-- Submit Clip Modal -->
    <div x-show="clipModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" style="display: none;">
        <div @click.outside="clipModalOpen = false" class="w-full max-w-lg bg-[#0B0B10] border border-violet-500/40 rounded-2xl p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-black text-white">Register Viral Clip Submission</h3>
                    <p class="text-xs text-[#A1A1AA]">Submit your short-form video link for brand review</p>
                </div>
                <button @click="clipModalOpen = false" class="text-gray-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="{{ route('web.clips.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Select Bounty Campaign</label>
                    <select name="campaign_id" x-model="selectedCampaignId" required
                            class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs focus:outline-none focus:border-violet-500">
                        <option value="">-- Choose Campaign --</option>
                        @foreach($availableCampaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }} (${{ number_format($c->reward_rate_per_thousand, 2) }}/1k)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Publishing Platform</label>
                    <select name="platform" required
                            class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs focus:outline-none focus:border-violet-500">
                        <option value="TikTok">TikTok</option>
                        <option value="YouTube Shorts">YouTube Shorts</option>
                        <option value="Instagram Reels">Instagram Reels</option>
                        <option value="X">X (Twitter Video)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Clip URL</label>
                    <input type="url" name="clip_url" required placeholder="https://www.tiktok.com/@user/video/1234567890..."
                           class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-xs focus:outline-none focus:border-violet-500">
                </div>
                <div class="p-3 rounded-xl bg-violet-500/10 border border-violet-500/20 text-[11px] text-violet-300 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-violet-400 flex-shrink-0 mt-0.5"></i>
                    <span>Once approved by the Brand, your reward will automatically be scheduled for Admin manual wire payment to your saved bank account.</span>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider hover:opacity-95 transition-opacity">
                        Submit Clip For Approval
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
