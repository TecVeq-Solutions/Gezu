import React, { useState } from 'react';
import { Campaign, ClipSubmission } from '../../types';
import { 
  Sparkles, 
  Plus, 
  TrendingUp, 
  DollarSign, 
  Eye, 
  Users, 
  BarChart3, 
  Globe, 
  Clock, 
  CheckCircle2, 
  ShieldCheck, 
  X, 
  ArrowRight, 
  Video, 
  Layers, 
  Zap, 
  Radio, 
  Share2, 
  Filter, 
  Search,
  ExternalLink,
  ChevronRight,
  PieChart,
  Activity,
  Award,
  AlertCircle,
  Calendar
} from 'lucide-react';
import { StarfieldButton } from '../common/StarfieldButton';
import { PerformanceAreaChart } from '../common/PerformanceAreaChart';
import { ChannelDistributionDonut } from '../common/ChannelDistributionDonut';

interface BrandDashboardProps {
  campaigns: Campaign[];
  submissions: ClipSubmission[];
  onCreateCampaign: (newCampaign: Campaign) => void;
  onExploreCampaigns: () => void;
}

export const BrandDashboard: React.FC<BrandDashboardProps> = ({
  campaigns,
  submissions,
  onCreateCampaign,
  onExploreCampaigns
}) => {
  const [wizardOpen, setWizardOpen] = useState<boolean>(false);
  const [wizardStep, setWizardStep] = useState<number>(1);
  const [activeTab, setActiveTab] = useState<'campaigns' | 'analytics' | 'submissions' | 'telemetry'>('campaigns');
  const [campaignSearch, setCampaignSearch] = useState<string>('');
  const [selectedCategoryFilter, setSelectedCategoryFilter] = useState<string>('all');
  const [selectedCampaignForModal, setSelectedCampaignForModal] = useState<Campaign | null>(null);

  // New campaign form state
  const [newTitle, setNewTitle] = useState<string>('');
  const [newCategory, setNewCategory] = useState<string>('Technology & AI');
  const [newDesc, setNewDesc] = useState<string>('');
  const [newVideoUrl, setNewVideoUrl] = useState<string>('');
  const [newBudget, setNewBudget] = useState<number>(3000);
  const [newRewardRate, setNewRewardRate] = useState<number>(1.00);
  const [newTargetViews, setNewTargetViews] = useState<number>(3000000);
  const [newHashtags, setNewHashtags] = useState<string>('#BrandVision #AI #GEZUX');
  const [wizardError, setWizardError] = useState<string>('');

  const totalFunded = campaigns.reduce((acc, c) => acc + c.totalBudget, 0);
  const totalViews = campaigns.reduce((acc, c) => acc + c.verifiedViews, 0);
  const totalClips = submissions.length + 84;
  const activeCampaignsCount = campaigns.filter(c => c.status === 'active').length;

  // Real mock chart telemetry data points
  const telemetryTrendData = [
    { date: 'Sep 24', value: Math.max(12000, totalViews * 0.4) },
    { date: 'Sep 25', value: Math.max(28000, totalViews * 0.52) },
    { date: 'Sep 26', value: Math.max(45000, totalViews * 0.68) },
    { date: 'Sep 27', value: Math.max(62000, totalViews * 0.79) },
    { date: 'Sep 28', value: Math.max(89000, totalViews * 0.88) },
    { date: 'Sep 29', value: Math.max(120000, totalViews * 0.94) },
    { date: 'Today', value: Math.max(154000, totalViews) }
  ];

  const filteredCampaigns = campaigns.filter(c => {
    const matchesSearch = c.title.toLowerCase().includes(campaignSearch.toLowerCase()) || 
                          c.category.toLowerCase().includes(campaignSearch.toLowerCase());
    const matchesCategory = selectedCategoryFilter === 'all' || c.category === selectedCategoryFilter;
    return matchesSearch && matchesCategory;
  });

  const handleLaunchCampaign = () => {
    if (!newTitle.trim() || !newVideoUrl.trim()) {
      setWizardError('Please provide both campaign title and raw video URL.');
      return;
    }

    const calculatedViews = Math.round((newBudget / newRewardRate) * 1000);

    const created: Campaign = {
      id: `camp-${Date.now().toString().slice(-4)}`,
      title: newTitle,
      slug: newTitle.toLowerCase().replace(/\s+/g, '-'),
      brandName: 'Acme AI Studio',
      brandLogo: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=120&q=80',
      category: newCategory,
      description: newDesc || 'Autonomous vertical distribution campaign with escrow-backed creator rewards.',
      sourceVideoUrl: newVideoUrl,
      sourceDuration: '45m 00s',
      thumbnail: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?auto=format&fit=crop&w=800&q=80',
      totalBudget: newBudget,
      remainingBudget: newBudget,
      rewardRatePerThousand: newRewardRate,
      currency: 'USD',
      targetViews: calculatedViews,
      verifiedViews: 0,
      status: 'active',
      platforms: ['TikTok', 'Instagram', 'YouTube Shorts'],
      countries: [
        { countryCode: 'US', countryName: 'United States', rewardMultiplier: 1.0, targetViews: calculatedViews * 0.6 },
        { countryCode: 'GB', countryName: 'United Kingdom', rewardMultiplier: 0.95, targetViews: calculatedViews * 0.4 }
      ],
      requirements: {
        aspectRatio: '9:16 Vertical',
        duration: '15–60 Seconds',
        requiredHashtags: newHashtags.split(' ').filter(Boolean),
        requiredMentions: ['@AcmeAIStudio'],
        prohibitedContent: ['Misleading edits', 'Low resolution', 'Copyright strikes'],
        brandMentionRequired: true
      },
      deadline: '2026-11-30',
      daysRemaining: 60,
      totalSubmissions: 0,
      createdAt: new Date().toISOString().slice(0, 10)
    };

    onCreateCampaign(created);
    setWizardOpen(false);
    setWizardStep(1);
    setNewTitle('');
    setNewVideoUrl('');
    setNewDesc('');
    setWizardError('');
  };

  return (
    <div className="relative pt-28 pb-24 min-h-screen bg-[#070708] overflow-hidden">
      {/* Ambient background glow orbs */}
      <div className="absolute top-16 left-1/4 -translate-x-1/2 w-[600px] h-[350px] bg-blue-600/15 blur-[140px] rounded-full pointer-events-none" />
      <div className="absolute top-48 right-10 w-[500px] h-[320px] bg-violet-600/15 blur-[130px] rounded-full pointer-events-none" />
      <div className="absolute bottom-10 left-1/3 w-[550px] h-[280px] bg-cyan-500/10 blur-[120px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">
        
        {/* Portal Header */}
        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-white/[0.08]">
          <div className="flex items-center gap-4">
            <div className="relative">
              <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600/30 to-indigo-600/30 border border-blue-500/40 flex items-center justify-center text-blue-400 font-bold shadow-xl shadow-blue-950/50 backdrop-blur-md">
                <Sparkles className="w-7 h-7 text-blue-400" />
              </div>
              <span className="absolute -bottom-1 -right-1 flex h-3.5 w-3.5">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span className="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#070708]"></span>
              </span>
            </div>

            <div>
              <div className="flex flex-wrap items-center gap-2.5">
                <h1 className="text-2xl sm:text-3xl font-display font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-white via-[#F7F7F5] to-[#D4D4D8]">
                  Brand Intelligence Studio
                </h1>
                <span className="text-[10px] font-mono px-2.5 py-0.5 rounded-full bg-blue-500/15 text-blue-400 border border-blue-500/30 font-bold tracking-wider">
                  ENTERPRISE SPONSOR
                </span>
                <span className="text-[10px] font-mono px-2.5 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">
                  ● ESCROW VAULT SECURED
                </span>
              </div>
              <p className="text-xs text-[#A1A1AA] mt-1">
                Organization: <strong className="text-white font-semibold">Acme AI Studio</strong> • Multi-Platform Organic Distribution Node
              </p>
            </div>
          </div>

          <div className="flex flex-wrap items-center gap-3">
            <button
              onClick={onExploreCampaigns}
              className="px-4 py-2.5 rounded-xl font-semibold text-xs text-[#D4D4D8] hover:text-white bg-[#121218]/80 hover:bg-[#18181F] border border-white/[0.08] hover:border-white/20 transition-all flex items-center gap-2 backdrop-blur-md"
            >
              <Globe className="w-3.5 h-3.5 text-cyan-400" />
              Live Marketplace
            </button>
            <StarfieldButton
              size="md"
              variant="primary"
              onClick={() => {
                setWizardError('');
                setWizardOpen(true);
              }}
              icon={<Plus className="w-4 h-4" />}
              iconPosition="left"
            >
              Launch New Campaign
            </StarfieldButton>
          </div>
        </div>

        {/* 4 Core Brand Metrics Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          
          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-blue-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-blue-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Total Campaign Escrow</span>
              <div className="p-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 group-hover:scale-110 transition-transform">
                <DollarSign className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-white tracking-tight">
              ${totalFunded.toLocaleString()}.00
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-emerald-400 font-mono font-semibold flex items-center gap-1">
                <TrendingUp className="w-3 h-3" /> 100% Escrow Backed
              </span>
              <span className="text-[#71717A] font-mono">{activeCampaignsCount} Active Pools</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-emerald-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-emerald-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Verified Reach</span>
              <div className="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 group-hover:scale-110 transition-transform">
                <Eye className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-emerald-400 tracking-tight">
              {(totalViews / 1000000).toFixed(2)}M <span className="text-sm font-normal text-emerald-500/70">Views</span>
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-emerald-400 font-mono font-semibold flex items-center gap-1">
                <ShieldCheck className="w-3 h-3" /> 100% Sybil Audited
              </span>
              <span className="text-[#71717A] font-mono">Real Organic</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-violet-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-violet-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Effective CPM</span>
              <div className="p-2 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 group-hover:scale-110 transition-transform">
                <TrendingUp className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-violet-300 tracking-tight">
              $1.10 <span className="text-xs text-[#A1A1AA] font-normal">/ 1K views</span>
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-violet-400 font-mono font-semibold">
                88% cheaper
              </span>
              <span className="text-[#71717A]">vs Paid Social Ads</span>
            </div>
          </div>

          <div className="group relative p-5 rounded-2xl bg-gradient-to-b from-[#121218]/90 to-[#0B0B10]/90 border border-white/[0.08] hover:border-cyan-500/40 transition-all duration-300 backdrop-blur-xl shadow-xl hover:shadow-cyan-950/20">
            <div className="flex items-center justify-between text-xs text-[#A1A1AA] mb-2.5 font-mono">
              <span className="tracking-wider uppercase text-[11px]">Clipper Force</span>
              <div className="p-2 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 group-hover:scale-110 transition-transform">
                <Users className="w-4 h-4" />
              </div>
            </div>
            <div className="text-2xl sm:text-3xl font-bold font-mono text-cyan-300 tracking-tight">
              {totalClips} <span className="text-sm font-normal text-cyan-400/70">Clips</span>
            </div>
            <div className="mt-3 pt-3 border-t border-white/[0.06] flex items-center justify-between text-[11px]">
              <span className="text-cyan-400 font-mono font-semibold">
                38 Active Editors
              </span>
              <span className="text-[#71717A] font-mono">3 Platforms</span>
            </div>
          </div>

        </div>

        {/* Tab Navigation Controls */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/[0.08] pb-2">
          <div className="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
            <button
              onClick={() => setActiveTab('campaigns')}
              className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                activeTab === 'campaigns' 
                  ? 'text-white bg-gradient-to-r from-blue-600/30 to-indigo-600/30 border border-blue-500/40 shadow-lg shadow-blue-950/30' 
                  : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
              }`}
            >
              <Layers className="w-3.5 h-3.5 text-blue-400" />
              Campaigns Ledger ({campaigns.length})
            </button>

            <button
              onClick={() => setActiveTab('analytics')}
              className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                activeTab === 'analytics' 
                  ? 'text-white bg-gradient-to-r from-blue-600/30 to-indigo-600/30 border border-blue-500/40 shadow-lg shadow-blue-950/30' 
                  : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
              }`}
            >
              <BarChart3 className="w-3.5 h-3.5 text-violet-400" />
              Live Telemetry & Charts
            </button>

            <button
              onClick={() => setActiveTab('submissions')}
              className={`px-4 py-2.5 text-xs font-bold rounded-xl transition-all flex items-center gap-2 cursor-pointer ${
                activeTab === 'submissions' 
                  ? 'text-white bg-gradient-to-r from-blue-600/30 to-indigo-600/30 border border-blue-500/40 shadow-lg shadow-blue-950/30' 
                  : 'text-[#A1A1AA] hover:text-white hover:bg-white/[0.03]'
              }`}
            >
              <Video className="w-3.5 h-3.5 text-emerald-400" />
              Submissions Review ({submissions.length})
            </button>
          </div>

          {activeTab === 'campaigns' && (
            <div className="relative">
              <Search className="w-3.5 h-3.5 text-[#71717A] absolute left-3 top-1/2 -translate-y-1/2" />
              <input
                type="text"
                placeholder="Filter campaigns..."
                value={campaignSearch}
                onChange={(e) => setCampaignSearch(e.target.value)}
                className="bg-[#121218] border border-white/[0.08] focus:border-blue-500/50 rounded-xl pl-8 pr-3 py-1.5 text-xs text-white placeholder-[#71717A] focus:outline-none transition-all w-full sm:w-56"
              />
            </div>
          )}
        </div>

        {/* TAB 1: Campaigns Ledger */}
        {activeTab === 'campaigns' && (
          <div className="space-y-6">
            <div className="bg-[#0B0B10]/90 rounded-2xl border border-white/[0.08] overflow-hidden backdrop-blur-xl shadow-2xl">
              <div className="p-4 sm:p-5 border-b border-white/[0.08] flex flex-wrap items-center justify-between gap-4">
                <div>
                  <h3 className="text-sm font-bold font-display text-white">Active Campaigns Ledger</h3>
                  <p className="text-xs text-[#71717A] mt-0.5">Real-time view tracking, escrow pool telemetry & creator yield</p>
                </div>
                <button
                  onClick={() => {
                    setWizardError('');
                    setWizardOpen(true);
                  }}
                  className="px-3.5 py-1.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
                >
                  <Plus className="w-3.5 h-3.5" /> Add Campaign
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs">
                  <thead className="bg-[#121218]/90 text-[#71717A] font-mono uppercase text-[10px] border-b border-white/[0.06]">
                    <tr>
                      <th className="py-3.5 px-4 sm:px-6">Campaign Info</th>
                      <th className="py-3.5 px-4">Status</th>
                      <th className="py-3.5 px-4">Escrow Pool</th>
                      <th className="py-3.5 px-4">Reward Rate</th>
                      <th className="py-3.5 px-4">Verified Views Progress</th>
                      <th className="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-white/[0.04] text-[#D4D4D8]">
                    {filteredCampaigns.map((camp) => {
                      const prog = Number(((camp.verifiedViews / Math.max(camp.targetViews, 1)) * 100).toFixed(1));
                      return (
                        <tr key={camp.id} className="hover:bg-white/[0.02] transition-colors group">
                          <td className="py-4 px-4 sm:px-6">
                            <div className="flex items-center gap-3">
                              <img 
                                src={camp.thumbnail} 
                                alt="" 
                                className="w-10 h-10 rounded-lg object-cover border border-white/10 shrink-0" 
                              />
                              <div>
                                <span className="text-white font-bold block text-sm group-hover:text-blue-400 transition-colors">
                                  {camp.title}
                                </span>
                                <span className="text-[11px] text-[#71717A] font-mono">
                                  {camp.category} • {camp.daysRemaining}d remaining
                                </span>
                              </div>
                            </div>
                          </td>
                          <td className="py-4 px-4">
                            <span className="px-2.5 py-1 rounded-full bg-emerald-500/15 text-emerald-400 font-mono text-[10px] font-bold border border-emerald-500/30 inline-flex items-center gap-1.5">
                              <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                              ACTIVE
                            </span>
                          </td>
                          <td className="py-4 px-4 font-mono">
                            <span className="text-white font-bold text-sm">${camp.totalBudget.toLocaleString()}.00</span>
                            <span className="text-[10px] text-[#71717A] block font-normal">
                              Remaining: ${camp.remainingBudget.toFixed(2)}
                            </span>
                          </td>
                          <td className="py-4 px-4 font-mono text-violet-400 font-bold">
                            ${camp.rewardRatePerThousand.toFixed(2)} <span className="text-[10px] text-[#71717A] font-normal">/ 1K views</span>
                          </td>
                          <td className="py-4 px-4">
                            <div className="w-40">
                              <div className="flex justify-between text-[10px] font-mono mb-1.5">
                                <span className="text-[#A1A1AA]">{(camp.verifiedViews / 1000).toFixed(0)}K / {(camp.targetViews / 1000).toFixed(0)}K</span>
                                <span className="text-white font-bold">{prog}%</span>
                              </div>
                              <div className="w-full h-2 rounded-full bg-white/10 overflow-hidden p-0.5">
                                <div 
                                  className="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all duration-500" 
                                  style={{ width: `${Math.min(prog, 100)}%` }} 
                                />
                              </div>
                            </div>
                          </td>
                          <td className="py-4 px-4 sm:px-6 text-right">
                            <button
                              onClick={() => setSelectedCampaignForModal(camp)}
                              className="px-3.5 py-1.5 bg-white/[0.05] hover:bg-blue-600 hover:text-white text-[#D4D4D8] rounded-xl font-mono text-xs transition-all border border-white/[0.08] cursor-pointer"
                            >
                              Manage ↗
                            </button>
                          </td>
                        </tr>
                      );
                    })}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}

        {/* TAB 2: Live Telemetry & Interactive Charts */}
        {activeTab === 'analytics' && (
          <div className="space-y-6">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
              <div className="lg:col-span-8">
                <PerformanceAreaChart
                  title="Aggregate Distribution Velocity"
                  subtitle="Verified audience impressions aggregated daily across creator nodes"
                  data={telemetryTrendData}
                  metricSuffix=" Views"
                  colorScheme="blue"
                  height={260}
                />
              </div>
              <div className="lg:col-span-4">
                <ChannelDistributionDonut
                  title="Network Reach Share"
                  subtitle="Audited impression volume breakdown"
                  totalViews={Math.max(totalViews, 2450000)}
                />
              </div>
            </div>
          </div>
        )}

        {/* TAB 3: Submissions Queue */}
        {activeTab === 'submissions' && (
          <div className="bg-[#0B0B10]/90 rounded-2xl border border-white/[0.08] overflow-hidden backdrop-blur-xl p-6 space-y-6 shadow-2xl">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/[0.06] pb-4">
              <div>
                <h3 className="text-base font-bold text-white font-display">Creator Submissions Review</h3>
                <p className="text-xs text-[#71717A] mt-0.5">Audit clips published across TikTok, Shorts, and Instagram with anti-fraud telemetry verification.</p>
              </div>
              <span className="text-xs font-mono px-3 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold self-start sm:self-auto">
                {submissions.length} Total Submissions
              </span>
            </div>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {submissions.map(sub => (
                <div 
                  key={sub.id} 
                  className="p-4 rounded-xl bg-gradient-to-b from-[#121218] to-[#0E0E14] border border-white/[0.06] hover:border-blue-500/30 transition-all flex flex-col justify-between gap-4"
                >
                  <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                      <img src={sub.creatorAvatar} alt="" className="w-11 h-11 rounded-full object-cover border border-white/10" />
                      <div>
                        <span className="font-bold text-white text-sm block">{sub.creatorHandle}</span>
                        <div className="flex items-center gap-2 mt-0.5">
                          <span className="text-[10px] font-mono px-2 py-0.5 rounded bg-white/5 text-[#A1A1AA] border border-white/10">
                            {sub.platform}
                          </span>
                          <span className="text-[11px] text-[#71717A] truncate max-w-[140px]">
                            {sub.campaignTitle}
                          </span>
                        </div>
                      </div>
                    </div>

                    <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">
                      VERIFIED
                    </span>
                  </div>

                  <div className="grid grid-cols-2 gap-2 pt-2 border-t border-white/[0.04] text-xs">
                    <div>
                      <span className="text-[10px] text-[#71717A] block font-mono">Eligible Views</span>
                      <span className="font-mono font-bold text-white text-sm">{sub.eligibleViews.toLocaleString()}</span>
                    </div>
                    <div>
                      <span className="text-[10px] text-[#71717A] block font-mono">Payout Escrow</span>
                      <span className="font-mono font-bold text-emerald-400 text-sm">+${(sub.verifiedReward || sub.estimatedReward).toFixed(2)}</span>
                    </div>
                  </div>

                  <div className="pt-2 flex items-center justify-between gap-2 border-t border-white/[0.04]">
                    <span className="text-[11px] text-[#71717A] font-mono flex items-center gap-1">
                      <Clock className="w-3 h-3" /> Submitted {sub.submittedAt}
                    </span>
                    <a
                      href={sub.postUrl}
                      target="_blank"
                      rel="noreferrer"
                      className="px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white rounded-lg text-xs font-semibold transition-all border border-blue-500/30 flex items-center gap-1"
                    >
                      Inspect Source <ExternalLink className="w-3 h-3" />
                    </a>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

      </div>

      {/* Campaign Details Modal */}
      {selectedCampaignForModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
          <div className="relative w-full max-w-2xl bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-6">
            <div className="flex items-center justify-between pb-4 border-b border-white/[0.08]">
              <div>
                <span className="text-[10px] font-mono uppercase font-bold text-blue-400">Campaign Management</span>
                <h3 className="text-xl font-display font-bold text-white mt-0.5">{selectedCampaignForModal.title}</h3>
              </div>
              <button onClick={() => setSelectedCampaignForModal(null)} className="p-1.5 rounded-lg bg-white/5 text-[#A1A1AA] hover:text-white cursor-pointer">
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
              <div className="p-3 rounded-xl bg-[#121218] border border-white/10">
                <span className="text-[#71717A] block font-mono">Budget</span>
                <span className="text-white font-bold font-mono text-base">${selectedCampaignForModal.totalBudget.toLocaleString()}</span>
              </div>
              <div className="p-3 rounded-xl bg-[#121218] border border-white/10">
                <span className="text-[#71717A] block font-mono">Reward / 1K</span>
                <span className="text-violet-400 font-bold font-mono text-base">${selectedCampaignForModal.rewardRatePerThousand.toFixed(2)}</span>
              </div>
              <div className="p-3 rounded-xl bg-[#121218] border border-white/10">
                <span className="text-[#71717A] block font-mono">Verified Views</span>
                <span className="text-emerald-400 font-bold font-mono text-base">{(selectedCampaignForModal.verifiedViews / 1000).toFixed(0)}K</span>
              </div>
              <div className="p-3 rounded-xl bg-[#121218] border border-white/10">
                <span className="text-[#71717A] block font-mono">Days Left</span>
                <span className="text-cyan-400 font-bold font-mono text-base">{selectedCampaignForModal.daysRemaining} Days</span>
              </div>
            </div>

            <div className="space-y-2 text-xs">
              <span className="text-white font-bold block">Raw Source Video URL:</span>
              <div className="p-3 rounded-xl bg-[#121218] border border-white/10 font-mono text-blue-400 truncate">
                {selectedCampaignForModal.sourceVideoUrl}
              </div>
            </div>

            <div className="space-y-2 text-xs">
              <span className="text-white font-bold block">Mandatory Hashtags:</span>
              <div className="flex flex-wrap gap-2">
                {selectedCampaignForModal.requirements.requiredHashtags.map(tag => (
                  <span key={tag} className="px-2.5 py-1 rounded-md bg-blue-500/10 text-blue-400 border border-blue-500/30 font-mono">
                    {tag}
                  </span>
                ))}
              </div>
            </div>

            <div className="pt-4 border-t border-white/[0.08] flex justify-end gap-3">
              <button 
                onClick={() => setSelectedCampaignForModal(null)}
                className="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white text-xs font-semibold cursor-pointer"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      )}

      {/* 100% Fully Responsive 3-Step Campaign Wizard Modal */}
      {wizardOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-black/85 backdrop-blur-xl animate-in fade-in duration-200">
          <div className="relative w-full max-w-xl bg-[#0B0B10] rounded-2xl border border-white/20 p-5 sm:p-8 shadow-2xl overflow-y-auto max-h-[92vh] space-y-6">
            
            {/* Wizard Header */}
            <div className="flex items-center justify-between pb-4 border-b border-white/[0.08]">
              <div>
                <span className="text-[10px] font-mono uppercase font-bold text-blue-400 tracking-wider">
                  Step 0{wizardStep} of 03 — Campaign Builder
                </span>
                <h3 className="text-lg font-display font-bold text-white mt-0.5">
                  {wizardStep === 1 ? 'Source Content & Category' : wizardStep === 2 ? 'Reward Rate & Budget' : 'Review & Launch Campaign'}
                </h3>
              </div>
              <button 
                onClick={() => {
                  setWizardOpen(false);
                  setWizardError('');
                }} 
                className="p-1.5 rounded-lg text-[#A1A1AA] hover:text-white hover:bg-white/5 transition-colors cursor-pointer"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            {/* Error Notification */}
            {wizardError && (
              <div className="p-3 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                <AlertCircle className="w-4 h-4 shrink-0" />
                <span>{wizardError}</span>
              </div>
            )}

            {/* Step 1: Content */}
            {wizardStep === 1 && (
              <div className="space-y-4 text-xs">
                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Campaign Title *</label>
                  <input
                    type="text"
                    placeholder="e.g. AI Founder Keynote Highlights"
                    value={newTitle}
                    onChange={(e) => {
                      setNewTitle(e.target.value);
                      if (wizardError) setWizardError('');
                    }}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl p-3 text-white focus:outline-none focus:border-blue-500 transition-colors"
                  />
                </div>

                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Category</label>
                  <select
                    value={newCategory}
                    onChange={(e) => setNewCategory(e.target.value)}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl p-3 text-white focus:outline-none"
                  >
                    <option value="Technology & AI">Technology & AI</option>
                    <option value="Hardware & Tech">Hardware & Tech</option>
                    <option value="Business & Startups">Business & Startups</option>
                    <option value="Gaming & VFX">Gaming & VFX</option>
                  </select>
                </div>

                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Raw Source Video URL (YouTube / Drive / S3) *</label>
                  <input
                    type="url"
                    placeholder="https://youtube.com/watch?v=..."
                    value={newVideoUrl}
                    onChange={(e) => {
                      setNewVideoUrl(e.target.value);
                      if (wizardError) setWizardError('');
                    }}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl p-3 text-white font-mono focus:outline-none focus:border-blue-500 transition-colors"
                  />
                </div>

                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Campaign Description / Instructions</label>
                  <textarea
                    rows={3}
                    placeholder="What specific moments, hooks, or highlights should creators focus on?"
                    value={newDesc}
                    onChange={(e) => setNewDesc(e.target.value)}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl p-3 text-white focus:outline-none focus:border-blue-500 transition-colors"
                  />
                </div>

                <div className="pt-4 border-t border-white/[0.08] flex justify-end">
                  <button
                    onClick={() => {
                      if (!newTitle.trim() || !newVideoUrl.trim()) {
                        setWizardError('Please enter both a title and video URL before proceeding.');
                        return;
                      }
                      setWizardStep(2);
                      setWizardError('');
                    }}
                    className="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl flex items-center gap-1.5 shadow-lg shadow-blue-950/40 cursor-pointer"
                  >
                    Next: Set Budget <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            )}

            {/* Step 2: Budget & Rewards */}
            {wizardStep === 2 && (
              <div className="space-y-4 text-xs">
                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Total Escrow Budget Fund ($ USD)</label>
                  <div className="relative">
                    <span className="absolute left-3 top-3 text-white font-mono">$</span>
                    <input
                      type="number"
                      min="100"
                      step="100"
                      value={newBudget}
                      onChange={(e) => setNewBudget(Math.max(100, Number(e.target.value)))}
                      className="w-full bg-[#121218] border border-white/10 rounded-xl pl-7 pr-3 py-3 text-white font-mono focus:outline-none focus:border-blue-500 text-base"
                    />
                  </div>
                </div>

                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Reward Rate per 1,000 Verified Views</label>
                  <div className="grid grid-cols-4 gap-2">
                    {[0.80, 1.00, 1.20, 1.50].map(r => (
                      <button
                        key={r}
                        type="button"
                        onClick={() => setNewRewardRate(r)}
                        className={`py-2.5 rounded-xl font-mono border transition-all cursor-pointer ${
                          newRewardRate === r 
                            ? 'bg-blue-600 border-blue-400 text-white font-bold shadow-lg shadow-blue-950/40' 
                            : 'bg-[#121218] border-white/10 text-[#A1A1AA] hover:text-white'
                        }`}
                      >
                        ${r.toFixed(2)}
                      </button>
                    ))}
                  </div>
                </div>

                <div>
                  <label className="text-[#D4D4D8] font-mono block mb-1 font-semibold">Mandatory Hashtags</label>
                  <input
                    type="text"
                    value={newHashtags}
                    onChange={(e) => setNewHashtags(e.target.value)}
                    className="w-full bg-[#121218] border border-white/10 rounded-xl p-3 text-white font-mono focus:outline-none"
                  />
                </div>

                <div className="p-4 rounded-xl bg-blue-950/40 border border-blue-500/30 text-blue-200 space-y-1">
                  <span className="font-mono text-[11px] block text-blue-300">Estimated Organic Reach:</span>
                  <span className="text-xl sm:text-2xl font-bold font-mono text-white">
                    {Math.round((newBudget / newRewardRate) * 1000).toLocaleString()} Verified Views
                  </span>
                </div>

                <div className="pt-4 border-t border-white/[0.08] flex justify-between">
                  <button 
                    onClick={() => setWizardStep(1)} 
                    className="px-4 py-2.5 text-[#A1A1AA] hover:text-white cursor-pointer font-semibold"
                  >
                    Back
                  </button>
                  <button
                    onClick={() => setWizardStep(3)}
                    className="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl flex items-center gap-1.5 shadow-lg shadow-blue-950/40 cursor-pointer"
                  >
                    Next: Review & Launch <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            )}

            {/* Step 3: Review */}
            {wizardStep === 3 && (
              <div className="space-y-4 text-xs">
                <div className="p-4 rounded-xl bg-[#121218] border border-white/10 space-y-2.5 font-mono">
                  <div className="flex justify-between">
                    <span className="text-[#71717A]">Title:</span>
                    <strong className="text-white font-sans">{newTitle}</strong>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#71717A]">Category:</span>
                    <strong className="text-white">{newCategory}</strong>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#71717A]">Funded Escrow:</span>
                    <strong className="text-emerald-400 font-bold">${newBudget.toLocaleString()}.00</strong>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#71717A]">Reward Rate:</span>
                    <strong className="text-violet-400 font-bold">${newRewardRate.toFixed(2)} / 1K views</strong>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-[#71717A]">Target Reach:</span>
                    <strong className="text-white font-bold">{Math.round((newBudget / newRewardRate) * 1000).toLocaleString()} views</strong>
                  </div>
                </div>

                <div className="pt-4 border-t border-white/[0.08] flex justify-between">
                  <button 
                    onClick={() => setWizardStep(2)} 
                    className="px-4 py-2.5 text-[#A1A1AA] hover:text-white cursor-pointer font-semibold"
                  >
                    Back
                  </button>
                  <button
                    onClick={handleLaunchCampaign}
                    className="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-900/40 cursor-pointer"
                  >
                    Fund & Publish Campaign (${newBudget.toLocaleString()})
                  </button>
                </div>
              </div>
            )}

          </div>
        </div>
      )}

    </div>
  );
};
