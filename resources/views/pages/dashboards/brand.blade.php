@extends('layouts.app')

@section('title', 'Brand Operations Portal | GEZUX')
@section('meta_description', 'Manage performance clipping campaigns, creator video submissions, automated view attribution, and budget allocations.')

@section('content')
<div class="relative pt-28 pb-24 min-h-screen bg-[#070708] overflow-hidden text-[#F7F7F5]" x-data="{
    activeTab: 'campaigns',
    newCampaignModal: false,
    paymentMethod: 'stripe',
    campaignBudget: 1000,
    walletBalance: {{ $wallet->available_balance }},
    campaignSearch: ''
}">

    <!-- Ambient background glow orbs -->
    <div class="absolute top-16 right-1/4 w-[600px] h-[350px] bg-purple-600/15 blur-[140px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute top-48 left-10 w-[500px] h-[320px] bg-violet-600/15 blur-[130px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 right-1/3 w-[550px] h-[280px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>

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
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-600/30 to-violet-600/30 border border-purple-500/40 flex items-center justify-center text-purple-300 font-bold shadow-xl shadow-purple-950/50 backdrop-blur-md">
                        <i data-lucide="sparkles" class="w-7 h-7 text-purple-300"></i>
                    </div>
                    <span class="absolute -bottom-1 -right-1 flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#070708]"></span>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-2xl font-display font-bold text-white tracking-tight">Brand Operations Hub</h1>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            ADVERTISER COMMAND
                        </span>
                    </div>
                    <p class="text-xs text-[#A1A1AA] mt-1">
                        Organization: <strong class="text-white">{{ $brand->company_name ?? 'Advertiser Studio' }}</strong>. Campaigns funded via Stripe & platform wallet balance.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="button" @click="newCampaignModal = true"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-900/30 flex items-center gap-2 transition-all uppercase tracking-wider">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Launch New Campaign</span>
                </button>
            </div>
        </div>

        <!-- 4 Key Telemetry Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Metric 1: Brand Wallet Balance -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-purple-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Brand Wallet Balance</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    ${{ number_format($wallet->available_balance, 2) }}
                </div>
                <span class="text-[11px] text-emerald-400 flex items-center gap-1">
                    <i data-lucide="check" class="w-3 h-3"></i> Ready to allocate to campaigns
                </span>
            </div>

            <!-- Metric 2: Total Campaign Pool Allocated -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Total Campaign Budgets</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-400">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    ${{ number_format($totalCampaignBudget, 2) }}
                </div>
                <span class="text-[11px] text-purple-300 flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3 h-3 text-emerald-400"></i> ${{ number_format($totalRemainingBudget, 2) }} unspent
                </span>
            </div>

            <!-- Metric 3: Views Delivered -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-violet-500/40 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Views Generated</span>
                    <div class="w-8 h-8 rounded-lg bg-violet-500/10 flex items-center justify-center text-violet-400">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    {{ number_format($totalViewsDelivered) }}
                </div>
                <span class="text-[11px] text-[#A1A1AA] flex items-center gap-1">
                    Across TikTok, Shorts & Reels
                </span>
            </div>

            <!-- Metric 4: Active Campaigns -->
            <div class="p-6 rounded-2xl bg-[#0B0B10] border border-white/[0.08] hover:border-white/20 transition-all">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono uppercase text-[#A1A1AA]">Total Campaigns</span>
                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-white">
                        <i data-lucide="flag" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-3xl font-black text-white font-mono mb-1">
                    {{ count($campaigns) }}
                </div>
                <span class="text-[11px] text-[#A1A1AA] flex items-center gap-1">
                    {{ count($incomingSubmissions) }} clips submitted
                </span>
            </div>

        </div>

        <!-- Multi-Tab Navigation Bar -->
        <div class="flex items-center gap-2 border-b border-white/[0.08] pb-4 overflow-x-auto scrollbar-none">
            <button type="button" @click="activeTab = 'campaigns'"
                    :class="activeTab === 'campaigns' ? 'text-purple-400 border-b-2 border-purple-400 font-bold pb-2 -mb-4' : 'text-[#A1A1AA] hover:text-white pb-2 -mb-4'"
                    class="px-4 text-xs font-mono transition-colors flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="layers" class="w-4 h-4"></i>
                <span>My Campaigns ({{ count($campaigns) }})</span>
            </button>
            <button type="button" @click="activeTab = 'submissions'"
                    :class="activeTab === 'submissions' ? 'text-purple-400 border-b-2 border-purple-400 font-bold pb-2 -mb-4' : 'text-[#A1A1AA] hover:text-white pb-2 -mb-4'"
                    class="px-4 text-xs font-mono transition-colors flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="check-square" class="w-4 h-4"></i>
                <span>Review Clip Submissions ({{ count($incomingSubmissions) }})</span>
            </button>
        </div>

        <!-- TAB 1: CAMPAIGNS TABLE -->
        <div x-show="activeTab === 'campaigns'" class="space-y-4">
            
            <div class="rounded-2xl bg-[#0B0B10] border border-white/[0.08] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] tracking-wider border-b border-white/[0.08]">
                            <tr>
                                <th class="py-3.5 px-6">Campaign Title</th>
                                <th class="py-3.5 px-6">Rate / 1k Views</th>
                                <th class="py-3.5 px-6">Remaining Budget</th>
                                <th class="py-3.5 px-6">Budget Spend Progress</th>
                                <th class="py-3.5 px-6">Clips Submitted</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05] text-[#D4D4D8]">
                            @forelse($campaigns as $camp)
                                @php
                                    $pct = $camp->total_budget > 0 ? min(100, round((($camp->total_budget - $camp->remaining_budget) / $camp->total_budget) * 100)) : 0;
                                @endphp
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-6 font-bold text-white">
                                        <div class="text-sm">{{ $camp->title }}</div>
                                        <div class="text-[11px] text-[#71717A] font-mono">{{ $camp->category ?? 'Technology & AI' }}</div>
                                    </td>
                                    <td class="py-4 px-6 font-mono font-bold text-violet-400">
                                        ${{ number_format($camp->reward_rate_per_thousand, 2) }}
                                    </td>
                                    <td class="py-4 px-6 font-mono">
                                        <span class="text-white font-bold">${{ number_format($camp->remaining_budget, 2) }}</span> 
                                        <span class="text-[#71717A]">/ ${{ number_format($camp->total_budget, 2) }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="w-28">
                                            <div class="h-1.5 w-full bg-white/[0.06] rounded-full overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-purple-600 to-violet-500" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <span class="text-[10px] text-[#71717A] font-mono mt-0.5 block">{{ $pct }}% utilized</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 font-mono font-bold text-white">
                                        {{ $camp->submissions_count }}
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($camp->status === 'active')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-gray-500/10 border border-gray-500/30 text-gray-400">
                                                {{ ucfirst($camp->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ url('/campaigns/' . $camp->id) }}" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-[#A1A1AA] hover:text-white transition-colors" title="View Public Page">
                                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            </a>
                                            @if($camp->status === 'active')
                                                <form action="{{ route('web.brand.campaign.end', $camp->id) }}" method="POST" onsubmit="return confirm('End this campaign? Any leftover unspent budget of ${{ number_format($camp->remaining_budget, 2) }} will instantly be refunded back into your Brand Wallet.');">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition-colors" title="End Campaign and Return Leftover Budget">
                                                        End & Refund
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#71717A]">
                                        <i data-lucide="layers" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="font-medium text-white mb-1">No campaigns created yet</p>
                                        <p class="text-xs text-[#71717A] mb-4">Launch your first clipping campaign using Stripe or your wallet balance.</p>
                                        <button type="button" @click="newCampaignModal = true" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs uppercase tracking-wider transition-colors">
                                            Launch Campaign
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- TAB 2: SUBMISSIONS REVIEW TABLE -->
        <div x-show="activeTab === 'submissions'" class="space-y-4" style="display: none;">
            <div class="rounded-2xl bg-[#0B0B10] border border-white/[0.08] overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#121218] text-[#A1A1AA] font-mono uppercase text-[10px] tracking-wider border-b border-white/[0.08]">
                            <tr>
                                <th class="py-3.5 px-6">Creator</th>
                                <th class="py-3.5 px-6">Campaign</th>
                                <th class="py-3.5 px-6">Clip Video Link</th>
                                <th class="py-3.5 px-6">Observed Views</th>
                                <th class="py-3.5 px-6">Reward Amount</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Approval Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.05] text-[#D4D4D8]">
                            @forelse($incomingSubmissions as $sub)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="py-4 px-6 font-medium text-white">
                                        <div class="font-bold">{{ $sub->user->name ?? 'Creator' }}</div>
                                        <div class="text-[11px] text-[#71717A] font-mono">
                                            {{ $sub->user->wallet && $sub->user->wallet->payout_bank_name ? $sub->user->wallet->payout_bank_name : 'Bank on file' }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-[#A1A1AA]">
                                        {{ $sub->campaign->title ?? 'Campaign' }}
                                    </td>
                                    <td class="py-4 px-6 font-mono text-[11px]">
                                        <a href="{{ $sub->post_url }}" target="_blank" class="text-violet-400 hover:underline flex items-center gap-1">
                                            <span class="truncate max-w-[140px]">{{ $sub->post_url }}</span>
                                            <i data-lucide="external-link" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                        </a>
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
                                                Approved (Admin Queued)
                                            </span>
                                        @elseif($sub->status === 'verified')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center gap-1 w-max">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                Paid to Clipper
                                            </span>
                                        @elseif($sub->status === 'rejected')
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center gap-1 w-max">
                                                <i data-lucide="x" class="w-3 h-3"></i>
                                                Rejected
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-mono bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center gap-1 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Pending Your Review
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($sub->status === 'pending')
                                            <div class="flex items-center justify-end gap-2">
                                                <form action="{{ route('web.brand.clip.approve', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors flex items-center gap-1">
                                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                                        <span>Approve</span>
                                                    </button>
                                                </form>
                                                <form action="{{ route('web.brand.clip.reject', $sub->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-rose-400 hover:bg-rose-500/10 border border-rose-500/20 transition-colors">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-[#71717A] font-mono">Decision Recorded</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-[#71717A]">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                                        <p class="font-medium text-white mb-1">No incoming clips to review</p>
                                        <p class="text-xs">When clippers create and submit short videos for your campaigns, they appear here for your approval.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Create Campaign Modal -->
    <div x-show="newCampaignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md" style="display: none;">
        <div @click.outside="newCampaignModal = false" class="w-full max-w-lg bg-[#0B0B10] border border-purple-500/40 rounded-2xl p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-lg font-black text-white">Launch Clipping Bounty Campaign</h3>
                    <p class="text-xs text-[#A1A1AA]">Fund your pool via Stripe or available Brand Wallet balance</p>
                </div>
                <button @click="newCampaignModal = false" class="text-gray-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="{{ route('web.campaign.create') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Campaign Title *</label>
                    <input type="text" name="title" required placeholder="e.g. NextGen AI Agent Podcast #42"
                           class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Category</label>
                        <select name="category" class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs focus:outline-none focus:border-purple-500">
                            <option value="Technology & AI">Technology & AI</option>
                            <option value="Finance & Crypto">Finance & Crypto</option>
                            <option value="Gaming & Esports">Gaming & Esports</option>
                            <option value="Podcasts & Shows">Podcasts & Shows</option>
                            <option value="Health & Lifestyle">Health & Lifestyle</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Reward ($ / 1k Views) *</label>
                        <input type="number" name="reward_rate_per_thousand" value="3.50" step="0.10" min="0.5" required
                               class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-sm focus:outline-none focus:border-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Campaign Budget ($ USD) *</label>
                    <input type="number" name="total_budget" x-model="campaignBudget" min="50" step="50" required
                           class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-sm focus:outline-none focus:border-purple-500">
                    <p class="text-[11px] text-[#71717A] mt-1">Leftover unspent budget can be refunded to your brand wallet at any time upon ending the campaign.</p>
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Source Video Link (YouTube / Podcast URL)</label>
                    <input type="url" name="source_video_url" placeholder="https://youtube.com/watch?v=..."
                           class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white font-mono text-xs focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA] mb-1.5">Creative Brief & Guidelines *</label>
                    <textarea name="description" rows="3" required placeholder="Highlight hooks, key discussions, soundbites. Must include hashtag #YourBrand..."
                              class="w-full bg-[#121218] border border-white/10 rounded-xl px-4 py-2.5 text-white text-xs focus:outline-none focus:border-purple-500"></textarea>
                </div>

                <!-- Payment Rail Selection -->
                <div class="space-y-2 pt-2 border-t border-white/[0.08]">
                    <label class="block text-xs font-mono uppercase text-[#A1A1AA]">Select Funding Method *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-3.5 rounded-xl border cursor-pointer transition-all flex items-start gap-3"
                               :class="paymentMethod === 'stripe' ? 'bg-purple-600/10 border-purple-500 text-white' : 'bg-[#121218] border-white/10 text-[#A1A1AA]'">
                            <input type="radio" name="payment_method" value="stripe" x-model="paymentMethod" class="mt-0.5 text-purple-600">
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <i data-lucide="credit-card" class="w-3.5 h-3.5 text-purple-400"></i>
                                    <span>Stripe Payment</span>
                                </div>
                                <span class="text-[10px] text-[#71717A] mt-0.5 block">Card Checkout & instant deposit</span>
                            </div>
                        </label>
                        <label class="p-3.5 rounded-xl border cursor-pointer transition-all flex items-start gap-3"
                               :class="paymentMethod === 'wallet' ? 'bg-purple-600/10 border-purple-500 text-white' : 'bg-[#121218] border-white/10 text-[#A1A1AA]'">
                            <input type="radio" name="payment_method" value="wallet" x-model="paymentMethod" class="mt-0.5 text-purple-600">
                            <div>
                                <div class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <i data-lucide="wallet" class="w-3.5 h-3.5 text-emerald-400"></i>
                                    <span>Brand Wallet</span>
                                </div>
                                <span class="text-[10px] text-emerald-400 mt-0.5 block">${{ number_format($wallet->available_balance, 2) }} available</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 text-white font-extrabold text-xs uppercase tracking-wider hover:opacity-95 transition-opacity flex items-center justify-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span x-text="paymentMethod === 'stripe' ? 'Pay with Stripe & Launch Campaign' : 'Fund from Wallet & Launch Campaign'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
