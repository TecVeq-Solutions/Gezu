import React, { useState } from 'react';
import { Campaign, ClipSubmission, SocialAccount, Wallet, WithdrawalRequest } from '../../types';
import { 
  Wallet as WalletIcon, 
  Video, 
  ArrowUpRight, 
  TrendingUp, 
  CheckCircle2, 
  Clock, 
  ShieldAlert, 
  Share2, 
  Plus, 
  DollarSign, 
  X, 
  Sparkles, 
  Zap, 
  ExternalLink, 
  Layers, 
  Award, 
  Check, 
  Radio, 
  Smartphone,
  ChevronRight,
  BarChart3,
  Flame,
  Globe,
  AlertCircle
} from 'lucide-react';
import { StarfieldButton } from '../common/StarfieldButton';
import { PerformanceAreaChart } from '../common/PerformanceAreaChart';

interface ClipperDashboardProps {
  campaigns: Campaign[];
  submissions: ClipSubmission[];
  socialAccounts: SocialAccount[];
  wallet: Wallet;
  onOpenSubmitClip: () => void;
  onRequestWithdrawal: (amount: number, method: string, destination: string) => void;
  onExploreMarketplace: () => void;
}

export const ClipperDashboard: React.FC<ClipperDashboardProps> = ({
  campaigns,
  submissions,
  socialAccounts,
  wallet,
  onOpenSubmitClip,
  onRequestWithdrawal,
  onExploreMarketplace
}) => {
  const [activeTab, setActiveTab] = useState<'submissions' | 'radar' | 'analytics' | 'accounts' | 'wallet'>('submissions');
  const [withdrawModalOpen, setWithdrawModalOpen] = useState<boolean>(false);
  const [withdrawAmount, setWithdrawAmount] = useState<number>(Math.min(wallet.availableBalance, 500));
  const [withdrawMethod, setWithdrawMethod] = useState<'Wise' | 'Bank Transfer' | 'Crypto (USDC)' | 'PayPal'>('Wise');
  const [withdrawDestination, setWithdrawDestination] = useState<string>('alex.rivers@email.com');
  const [connectAccountModalOpen, setConnectAccountModalOpen] = useState<boolean>(false);
  const [newPlatform, setNewPlatform] = useState<string>('TikTok');
  const [newHandle, setNewHandle] = useState<string>('');

  const totalVerifiedViews = submissions.reduce((acc, sub) => acc + sub.eligibleViews, 0) + 1840000;
  const lifetimeEarned = wallet.totalWithdrawn + wallet.availableBalance + wallet.pendingBalance;

  // Real mock trend points
  const earningsTrendData = [
    { date: 'Sep 24', value: 340 },
    { date: 'Sep 25', value: 520 },
    { date: 'Sep 26', value: 780 },
    { date: 'Sep 27', value: 1120 },
    { date: 'Sep 28', value: 1340 },
    { date: 'Sep 29', value: 1680 },
    { date: 'Today', value: Math.round(lifetimeEarned) || 1840 }
  ];

  const handleWithdrawSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (withdrawAmount > wallet.availableBalance || withdrawAmount <= 0) return;
    onRequestWithdrawal(withdrawAmount, withdrawMethod, withdrawDestination);
    setWithdrawModalOpen(false);
  };

  return (
    <div className="relative pt-28 pb-24 min-h-screen bg-[#070708] overflow-hidden">
      {/* Ambient background glow orbs */}
      <div className="absolute top-16 left-1/4 -translate-x-1/2 w-[600px] h-[350px] bg-violet-600/15 blur-[140px] rounded-full pointer-events-none" />
      <div className="absolute top-48 right-10 w-[500px] h-[320px] bg-emerald-600/15 blur-[130px] rounded-full pointer-events-none" />
      <div className="absolute bottom-10 left-1/3 w-[550px] h-[280px] bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">
        
        {/* Portal Header */}
        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-white/[0.08]">
          <div className="flex items-center gap-4">
            <div className="relative">
              <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-600/30 to-purple-600/30 border border-violet-500/40 flex items-center justify-center text-violet-400 font-bold shadow-xl shadow-violet-950/50 backdrop-blur-md">
                <Video className="w-7 h-7 text-violet-400" />
              </div>
              <span className="absolute -bottom-1 -right-1 flex h-3.5 w-3.5">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#070708]"></span>
              </span>
            </div>

            <div>
              <div className="flex flex-wrap items-center gap-2.5">
                <h1 className="text-2xl sm:text-3xl font-display font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white via-[#F7F7F5] to-[#D4D4D8]">
                  Clipper Command Studio
                </h1>
                <span className="text-[10px] font-mono px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold tracking-wider">
                  TIER 1 VERIFIED CREATOR
                </span>
                <span className="text-[10px] font-mono px-2.5 py-0.5 rounded-full bg-violet-500/15 text-violet-400 border border-violet-500/30 font-bold">
                  ● TOP 5% RETENTION
                </span>
              </div>
              <p className="text-xs text-[#A1A1AA] mt-1">
                Distributor Handle: <strong className="text-white font-semibold">@AlexClips</strong> • Multi-Channel Short-Form Specialist
              </p>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={() => setWithdrawModalOpen(true)}
              className="px-4 py-2.5 rounded-xl font-semibold text-xs text-[#D4D4D8] hover:text-white bg-[#121218]/80 hover:bg-[#18181F] border border-white/[0.08] hover:border-emerald-500/30 transition-all flex items-center gap-2 backdrop-blur-md cursor-pointer"
            >
              <WalletIcon className="w-4 h-4 text-emerald-400" />
              Instant Payout (${wallet.availableBalance.toFixed(2)})
            </button>
            <StarfieldButton
              size="md"
              variant="primary"
              onClick={onOpenSubmitClip}
              icon={<Plus className="w-4 h-4" />}
              iconPosition="left"
            >
              Submit Published Clip
            </StarfieldButton>
          </div>
        </div>

        {/* 4 Metric Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          
          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-emerald-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-emerald-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Available Balance</span>
              <div className="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 group-hover:scale-110 transition-transform">
                <WalletIcon className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-emerald-400 tracking-tight">
              ${wallet.availableBalance.toFixed(2)}
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-emerald-400 font-mono font-semibold flex items-center gap-1">
                <Zap className="w-3 h-3" /> Ready to Withdraw
              </span>
              <span className="text-[#71717A] font-mono">0% platform fee</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-amber-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-amber-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Pending Verification</span>
              <div className="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 group-hover:scale-110 transition-transform">
                <Clock className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-white tracking-tight">
              ${wallet.pendingBalance.toFixed(2)}
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-amber-400 font-mono font-semibold">
                OCR & View Auditing
              </span>
              <span className="text-[#71717A] font-mono">Clears in ~24h</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-violet-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-violet-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Audited Views</span>
              <div className="p-2 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 group-hover:scale-110 transition-transform">
                <TrendingUp className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-violet-300 tracking-tight">
              {(totalVerifiedViews / 1000000).toFixed(2)}M <span className="text-sm font-normal text-violet-400/70">Views</span>
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-violet-400 font-mono font-semibold flex items-center gap-1">
                <TrendingUp className="w-3 h-3" /> +18.4% this week
              </span>
              <span className="text-[#71717A] font-mono">All Handles</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-cyan-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-cyan-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Active Clips</span>
              <div className="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:scale-110 transition-transform">
                <Share2 className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-cyan-300 tracking-tight">
              {submissions.length} <span className="text-sm font-normal text-cyan-400/70">Live Clips</span>
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-cyan-400 font-mono font-semibold">
                Avg Yield: $1.20/1K
              </span>
              <span className="text-[#71717A] font-mono">TikTok / Shorts</span>
            </div>
          </div>

        </div>

        {/* Tab Switcher */}
        <div className="flex items-center gap-2 border-b border-white/[0.08] pb-2 overflow-x-auto">
          <button
            onClick={() => setActiveTab('submissions')}
            className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
              activeTab === 'submissions' 
                ? 'text-white bg-gradient-to-r from-violet-600/30 to-purple-600/30 border border-violet-500/40 shadow-lg shadow-violet-950/30' 
                : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
            }`}
          >
            <Video className="w-3.5 h-3.5 text-violet-400" />
            My Submitted Clips ({submissions.length})
          </button>

          <button
            onClick={() => setActiveTab('radar')}
            className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
              activeTab === 'radar' 
                ? 'text-white bg-gradient-to-r from-violet-600/30 to-purple-600/30 border border-violet-500/40 shadow-lg shadow-violet-950/30' 
                : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
            }`}
          >
            <Flame className="w-3.5 h-3.5 text-amber-400" />
            Bounties Radar ({campaigns.length})
          </button>

          <button
            onClick={() => setActiveTab('analytics')}
            className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
              activeTab === 'analytics' 
                ? 'text-white bg-gradient-to-r from-violet-600/30 to-purple-600/30 border border-violet-500/40 shadow-lg shadow-violet-950/30' 
                : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
            }`}
          >
            <BarChart3 className="w-3.5 h-3.5 text-emerald-400" />
            Earnings Growth
          </button>

          <button
            onClick={() => setActiveTab('accounts')}
            className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
              activeTab === 'accounts' 
                ? 'text-white bg-gradient-to-r from-violet-600/30 to-purple-600/30 border border-violet-500/40 shadow-lg shadow-violet-950/30' 
                : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
            }`}
          >
            <Smartphone className="w-3.5 h-3.5 text-cyan-400" />
            Connected Handles ({socialAccounts.length})
          </button>

          <button
            onClick={() => setActiveTab('wallet')}
            className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
              activeTab === 'wallet' 
                ? 'text-white bg-gradient-to-r from-violet-600/30 to-purple-600/30 border border-violet-500/40 shadow-lg shadow-violet-950/30' 
                : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
            }`}
          >
            <WalletIcon className="w-3.5 h-3.5 text-emerald-400" />
            Wallet & Payouts
          </button>
        </div>

        {/* TAB 1: Submissions */}
        {activeTab === 'submissions' && (
          <div className="space-y-4">
            <div className="bg-[#0B0B10]/90 rounded-2xl border border-white/[0.08] overflow-hidden backdrop-blur-xl shadow-2xl">
              <div className="p-4 sm:p-5 border-b border-white/[0.08] flex flex-wrap items-center justify-between gap-4">
                <div>
                  <h3 className="text-sm font-bold font-display text-white">Submitted Clip Performance Feed</h3>
                  <p className="text-xs text-[#71717A] mt-0.5">Automated telemetry synchronizing view counters every 60 seconds</p>
                </div>
                <button
                  onClick={onOpenSubmitClip}
                  className="px-3.5 py-1.5 bg-violet-600/20 hover:bg-violet-600/30 text-violet-400 border border-violet-500/30 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5" /> Submit New Clip
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs">
                  <thead className="bg-[#121218]/90 text-[#71717A] font-mono uppercase text-[10px] border-b border-white/[0.06]">
                    <tr>
                      <th className="py-3.5 px-4 sm:px-6">Campaign & Brand</th>
                      <th className="py-3.5 px-4">Platform</th>
                      <th className="py-3.5 px-4">Link Source</th>
                      <th className="py-3.5 px-4">Audit Status</th>
                      <th className="py-3.5 px-4 text-right">Eligible Views</th>
                      <th className="py-3.5 px-4 sm:px-6 text-right">Yield</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/[0.04] text-[#D4D4D8]">
                    {submissions.map((sub) => (
                      <tr key={sub.id} className="hover:bg-white/[0.02] transition-colors group">
                        <td className="py-4 px-4 sm:px-6 font-medium">
                          <span className="text-white font-bold block text-sm group-hover:text-violet-400 transition-colors">
                            {sub.campaignTitle}
                          </span>
                          <span className="text-[11px] text-[#71717A] font-mono">{sub.brandName}</span>
                        </td>
                        <td className="py-4 px-4">
                          <span className="px-2.5 py-1 rounded-md bg-white/[0.05] text-[11px] font-mono text-white border border-white/10">
                            {sub.platform}
                          </span>
                        </td>
                        <td className="py-4 px-4 font-mono text-[11px]">
                          <a 
                            href={sub.postUrl} 
                            target="_blank" 
                            rel="noreferrer"
                            className="text-violet-400 hover:text-violet-300 hover:underline max-w-[180px] truncate flex items-center gap-1"
                          >
                            <span className="truncate">{sub.postUrl}</span>
                            <ExternalLink className="w-3 h-3 shrink-0" />
                          </a>
                        </td>
                        <td className="py-4 px-4">
                          {sub.status === 'verified' ? (
                            <span className="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full border border-emerald-500/20">
                              <CheckCircle2 className="w-3 h-3" /> VERIFIED
                            </span>
                          ) : sub.status === 'under_review' ? (
                            <span className="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-full border border-amber-500/20">
                              <Clock className="w-3 h-3 animate-spin" /> AUDITING
                            </span>
                          ) : (
                            <span className="inline-flex items-center gap-1.5 text-[10px] font-mono font-bold text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/20">
                              <ShieldAlert className="w-3 h-3" /> FLAGGED
                            </span>
                          )}
                        </td>
                        <td className="py-4 px-4 text-right font-mono font-bold text-white text-sm">
                          {sub.eligibleViews.toLocaleString()}
                        </td>
                        <td className="py-4 px-4 sm:px-6 text-right font-mono font-bold text-emerald-400 text-sm">
                          +${(sub.verifiedReward || sub.estimatedReward).toFixed(2)}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* TAB 2: Live Bounties Radar */}
        {activeTab === 'radar' && (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <div>
                <h3 className="text-base font-bold text-white font-display">Active Campaign Bounties</h3>
                <p className="text-xs text-[#71717A]">Choose high-reward campaigns and submit your short-form edits for instant rewards.</p>
              </div>
              <button
                onClick={onExploreMarketplace}
                className="px-4 py-2 bg-white/5 hover:bg-white/10 text-white rounded-xl text-xs font-semibold border border-white/10 transition-all flex items-center gap-1.5"
              >
                Full Marketplace <ChevronRight className="w-3.5 h-3.5" />
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
              {campaigns.map((camp) => (
                <div 
                  key={camp.id} 
                  className="bg-[#0B0B10]/95 rounded-2xl border border-white/[0.08] hover:border-violet-500/40 p-5 space-y-4 backdrop-blur-xl shadow-xl transition-all group flex flex-col justify-between"
                >
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-white/5 text-[#D4D4D8] border border-white/10">
                        {camp.category}
                      </span>
                      <span className="text-emerald-400 font-mono font-bold text-xs">
                        ${camp.rewardRatePerThousand.toFixed(2)} / 1K views
                      </span>
                    </div>

                    <h4 className="text-base font-bold text-white group-hover:text-violet-400 transition-colors">
                      {camp.title}
                    </h4>

                    <p className="text-xs text-[#A1A1AA] line-clamp-2 leading-relaxed">
                      {camp.description}
                    </p>
                  </div>

                  <div className="pt-3 border-t border-white/[0.06] flex items-center justify-between">
                    <span className="text-[11px] font-mono text-[#71717A]">
                      Pool: ${camp.totalBudget.toLocaleString()}
                    </span>
                    <button
                      onClick={onOpenSubmitClip}
                      className="px-3.5 py-1.5 bg-violet-600 hover:bg-violet-500 text-white rounded-lg text-xs font-semibold shadow-md shadow-violet-950/40 cursor-pointer flex items-center gap-1"
                    >
                      Clip This <Plus className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* TAB 3: Analytics & Growth Chart */}
        {activeTab === 'analytics' && (
          <div className="space-y-6">
            <PerformanceAreaChart
              title="Cumulative Payout & Earnings Velocity"
              subtitle="Verified payouts disbursed across your linked distributor handles"
              data={earningsTrendData}
              metricPrefix="$"
              colorScheme="emerald"
              height={260}
            />
          </div>
        )}

        {/* TAB 4: Connected Social Accounts */}
        {activeTab === 'accounts' && (
          <div className="space-y-6">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <h3 className="text-base font-bold text-white font-display">Linked Distribution Handles</h3>
                <p className="text-xs text-[#71717A]">Link your verified TikTok, YouTube, and Instagram accounts to unlock faster view verification and instant payouts.</p>
              </div>
              <button
                onClick={() => setConnectAccountModalOpen(true)}
                className="px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white rounded-xl text-xs font-semibold shadow-lg shadow-violet-950/40 transition-all flex items-center gap-1.5 self-start sm:self-auto cursor-pointer"
              >
                <Plus className="w-3.5 h-3.5" /> Connect New Handle
              </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {socialAccounts.map((acc) => (
                <div 
                  key={acc.id} 
                  className="p-5 rounded-2xl bg-[#0B0B10]/90 border border-white/[0.08] hover:border-violet-500/30 transition-all flex flex-col justify-between space-y-4 backdrop-blur-xl shadow-xl"
                >
                  <div className="flex items-center justify-between">
                    <span className="text-xs font-mono font-bold px-2.5 py-1 rounded-md bg-white/[0.06] text-white border border-white/10">
                      {acc.platform}
                    </span>
                    {acc.verified ? (
                      <span className="text-[10px] font-mono text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full flex items-center gap-1 font-semibold">
                        <CheckCircle2 className="w-3 h-3" /> OAuth Active
                      </span>
                    ) : (
                      <span className="text-[10px] font-mono text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded-full">
                        Pending Auth
                      </span>
                    )}
                  </div>

                  <div>
                    <h4 className="text-base font-bold text-white">@{acc.username}</h4>
                    <span className="text-xs text-[#71717A]">{acc.displayName}</span>
                    <div className="mt-3 text-sm font-mono text-violet-300 font-bold flex items-center gap-1.5">
                      <TrendingUp className="w-4 h-4 text-violet-400" />
                      {acc.followersCount.toLocaleString()} <span className="text-xs font-normal text-[#A1A1AA]">Followers</span>
                    </div>
                  </div>

                  <button 
                    onClick={() => alert(`OAuth synchronization refreshed for @${acc.username}`)}
                    className="w-full py-2 rounded-xl text-xs font-semibold text-[#D4D4D8] hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 transition-colors cursor-pointer"
                  >
                    Refresh Telemetry
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* TAB 5: Wallet & Ledger */}
        {activeTab === 'wallet' && (
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div className="lg:col-span-2 bg-[#0B0B10]/90 rounded-2xl border border-white/[0.08] p-6 space-y-6 backdrop-blur-xl shadow-2xl">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.06] pb-4">
                <div>
                  <h3 className="text-base font-bold text-white font-display">Wallet Transactions Ledger</h3>
                  <p className="text-xs text-[#71717A] mt-0.5">Immutable audit trail of verified clip rewards and payout disbursements.</p>
                </div>
                <button 
                  onClick={() => setWithdrawModalOpen(true)}
                  className="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold cursor-pointer shadow-lg shadow-emerald-900/30 flex items-center gap-1.5 self-start sm:self-auto"
                >
                  <WalletIcon className="w-3.5 h-3.5" /> Request Withdrawal
                </button>
              </div>

              <div className="divide-y divide-white/[0.05]">
                {wallet.transactions.map(tx => (
                  <div key={tx.id} className="py-4 flex items-center justify-between text-xs">
                    <div>
                      <span className="font-semibold text-white block text-sm">{tx.description}</span>
                      <span className="text-[11px] text-[#71717A] font-mono">{tx.date} • Ref: {tx.referenceId || tx.id}</span>
                    </div>
                    <div className="text-right">
                      <span className={`font-mono font-bold text-sm ${tx.amount > 0 ? 'text-emerald-400' : 'text-rose-400'}`}>
                        {tx.amount > 0 ? `+$${tx.amount.toFixed(2)}` : `-$${Math.abs(tx.amount).toFixed(2)}`}
                      </span>
                      <span className="text-[10px] uppercase font-mono block text-[#71717A] font-bold">{tx.status}</span>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-[#0B0B10]/90 rounded-2xl border border-white/[0.08] p-6 space-y-6 backdrop-blur-xl shadow-2xl flex flex-col justify-between">
              <div>
                <h3 className="text-base font-bold text-white font-display">Supported Payout Rails</h3>
                <p className="text-xs text-[#71717A] mt-1">Direct wire to 140+ countries with zero fees and fast settlement.</p>
                
                <div className="mt-6 space-y-3 text-xs">
                  <div className="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-between">
                    <span className="text-white font-semibold">Wise Multi-Currency</span>
                    <span className="text-emerald-400 font-mono text-[11px]">~5 mins</span>
                  </div>
                  <div className="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-between">
                    <span className="text-white font-semibold">Crypto (USDC Solana)</span>
                    <span className="text-emerald-400 font-mono text-[11px]">~30 secs</span>
                  </div>
                  <div className="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-between">
                    <span className="text-white font-semibold">Direct ACH / SEPA</span>
                    <span className="text-[#A1A1AA] font-mono text-[11px]">1–2 days</span>
                  </div>
                  <div className="p-3 rounded-xl bg-white/[0.03] border border-white/[0.06] flex items-center justify-between">
                    <span className="text-white font-semibold">PayPal Instant</span>
                    <span className="text-emerald-400 font-mono text-[11px]">Instant</span>
                  </div>
                </div>
              </div>

              <div className="p-4 rounded-xl bg-[#121218] border border-white/10 text-center">
                <span className="text-[11px] text-[#A1A1AA] block mb-2">Lifetime Clipper Earnings:</span>
                <span className="text-xl font-bold font-mono text-emerald-400">${lifetimeEarned.toFixed(2)}</span>
              </div>
            </div>
          </div>
        )}

      </div>

      {/* Connect Account Modal */}
      {connectAccountModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-xl animate-in fade-in duration-200">
          <div className="relative w-full max-w-md bg-[#0B0B10] rounded-2xl border border-white/20 p-6 shadow-2xl space-y-4">
            <div className="flex items-center justify-between pb-3 border-b border-white/[0.08]">
              <h3 className="text-base font-bold text-white">Connect Social Handle</h3>
              <button onClick={() => setConnectAccountModalOpen(false)} className="p-1 text-[#A1A1AA] hover:text-white cursor-pointer">
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="space-y-4 text-xs">
              <div>
                <label className="text-[#D4D4D8] font-mono block mb-1">Platform:</label>
                <select
                  value={newPlatform}
                  onChange={(e) => setNewPlatform(e.target.value)}
                  className="w-full bg-[#121218] border border-white/10 rounded-xl p-2.5 text-white focus:outline-none focus:border-violet-500"
                >
                  <option value="TikTok">TikTok</option>
                  <option value="YouTube Shorts">YouTube Shorts</option>
                  <option value="Instagram Reels">Instagram Reels</option>
                </select>
              </div>

              <div>
                <label className="text-[#D4D4D8] font-mono block mb-1">Account Handle / Username:</label>
                <input
                  type="text"
                  placeholder="@yourhandle"
                  value={newHandle}
                  onChange={(e) => setNewHandle(e.target.value)}
                  className="w-full bg-[#121218] border border-white/10 rounded-xl p-2.5 text-white font-mono focus:outline-none focus:border-violet-500"
                />
              </div>

              <div className="pt-3 border-t border-white/[0.08] flex justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setConnectAccountModalOpen(false)}
                  className="px-4 py-2 text-[#A1A1AA] hover:text-white text-xs font-semibold cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="button"
                  onClick={() => {
                    alert(`OAuth link initiated for ${newHandle || '@handle'} on ${newPlatform}`);
                    setConnectAccountModalOpen(false);
                  }}
                  className="px-5 py-2 bg-violet-600 hover:bg-violet-500 text-white font-semibold rounded-xl shadow-lg shadow-violet-950/40 text-xs cursor-pointer"
                >
                  Authorize Handle
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Withdrawal Request Modal */}
      {withdrawModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-xl animate-in fade-in duration-200">
          <div className="relative w-full max-w-md bg-[#0B0B10] rounded-2xl border border-white/20 p-6 shadow-2xl">
            <div className="flex items-center justify-between pb-4 border-b border-white/[0.08]">
              <div className="flex items-center gap-2">
                <WalletIcon className="w-4 h-4 text-emerald-400" />
                <h3 className="text-base font-bold text-white">Withdraw Creator Balance</h3>
              </div>
              <button onClick={() => setWithdrawModalOpen(false)} className="p-1 text-[#A1A1AA] hover:text-white cursor-pointer">
                <X className="w-5 h-5" />
              </button>
            </div>

            <form onSubmit={handleWithdrawSubmit} className="mt-5 space-y-4 text-xs">
              <div>
                <label className="text-[#D4D4D8] font-mono block mb-1">
                  Withdrawal Amount (Available: ${wallet.availableBalance.toFixed(2)}):
                </label>
                <div className="relative">
                  <span className="absolute left-3 top-2.5 text-white font-mono">$</span>
                  <input
                    type="number"
                    min="1"
                    max={wallet.availableBalance}
                    value={withdrawAmount}
                    onChange={(e) => setWithdrawAmount(Number(e.target.value))}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl pl-7 pr-3 py-2.5 text-white font-mono focus:outline-none focus:border-emerald-500 text-sm"
                  />
                </div>
              </div>

              <div>
                <label className="text-[#D4D4D8] font-mono block mb-1">Payout Method:</label>
                <select
                  value={withdrawMethod}
                  onChange={(e) => setWithdrawMethod(e.target.value as any)}
                  className="w-full bg-[#121218] border border-white/10 rounded-xl p-2.5 text-white focus:outline-none focus:border-emerald-500"
                >
                  <option value="Wise">Wise (Direct Wire / Multi-currency)</option>
                  <option value="Bank Transfer">Direct Bank Transfer (ACH / SEPA)</option>
                  <option value="Crypto (USDC)">Crypto (USDC on Solana / Polygon)</option>
                  <option value="PayPal">PayPal Instant</option>
                </select>
              </div>

              <div>
                <label className="text-[#D4D4D8] font-mono block mb-1">Recipient Destination / Email:</label>
                <input
                  type="text"
                  value={withdrawDestination}
                  onChange={(e) => setWithdrawDestination(e.target.value)}
                  className="w-full bg-[#121218] border border-white/10 rounded-xl p-2.5 text-white font-mono focus:outline-none focus:border-emerald-500"
                />
              </div>

              <div className="p-3 rounded-xl bg-emerald-950/30 border border-emerald-500/20 text-emerald-300 text-[11px] font-mono">
                ✓ 0% Platform Fee • Est. Delivery: Under 15 minutes
              </div>

              <div className="pt-4 border-t border-white/[0.08] flex items-center justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setWithdrawModalOpen(false)}
                  className="px-4 py-2 text-[#A1A1AA] hover:text-white text-xs font-semibold cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold rounded-xl shadow-lg shadow-emerald-950/40 text-xs cursor-pointer"
                >
                  Confirm Payout (${withdrawAmount.toFixed(2)})
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

    </div>
  );
};
