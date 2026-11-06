import React from 'react';
import { Sparkles, ArrowRight, ShieldCheck, TrendingUp, BarChart3, Users, DollarSign, CheckCircle2, Globe } from 'lucide-react';

interface ForBrandsPageProps {
  onLaunchCampaign: () => void;
  onOpenCalculator: () => void;
}

export const ForBrandsPage: React.FC<ForBrandsPageProps> = ({
  onLaunchCampaign,
  onOpenCalculator
}) => {
  return (
    <div className="pt-32 pb-24 min-h-screen bg-gray-50 dark:bg-[#070708]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
        
        {/* Hero */}
        <div className="text-center max-w-3xl mx-auto">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300 text-xs font-mono font-semibold mb-6">
            <Sparkles className="w-3.5 h-3.5" />
            FOR BRANDS, PODCASTS & ENTERPRISE MEDIA
          </div>

          <h1 className="text-4xl sm:text-6xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight leading-tight">
            Turn One Video Into A <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-gray-900 to-cyan-600 dark:from-blue-400 dark:via-white dark:to-cyan-300">
              Distribution Network.
            </span>
          </h1>

          <p className="mt-6 text-base sm:text-lg text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
            Stop relying on single organic uploads. Launch performance-backed clipping campaigns and activate hundreds of skilled editors to scale your reach across TikTok, Shorts, and Reels simultaneously.
          </p>

          <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
            <button
              onClick={onLaunchCampaign}
              className="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold rounded-xl shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2 cursor-pointer"
            >
              Launch Your Campaign
              <ArrowRight className="w-4 h-4" />
            </button>
            <button
              onClick={onOpenCalculator}
              className="px-8 py-3.5 bg-white dark:bg-[#121218] hover:bg-gray-100 dark:hover:bg-[#18181F] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-[#F7F7F5] font-semibold rounded-xl transition-all cursor-pointer shadow-sm"
            >
              Calculate Estimated Reach
            </button>
          </div>
        </div>

        {/* 3 Core Brand Value Pillars */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="p-8 rounded-2xl bg-white dark:bg-[#0B0B10] border border-gray-200 dark:border-white/[0.08] shadow-sm dark:shadow-xl space-y-4">
            <div className="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
              <DollarSign className="w-6 h-6" />
            </div>
            <h3 className="text-xl font-display font-bold text-gray-900 dark:text-white">Pay Only For Verified Views</h3>
            <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
              No upfront creator retainers or unpredictable influencer sponsorships. You set the rate (e.g. $1.00 per 1,000 verified views) and funds are only disbursed when eligible views are confirmed.
            </p>
          </div>

          <div className="p-8 rounded-2xl bg-white dark:bg-[#0B0B10] border border-gray-200 dark:border-white/[0.08] shadow-sm dark:shadow-xl space-y-4">
            <div className="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center text-violet-600 dark:text-violet-400">
              <ShieldCheck className="w-6 h-6" />
            </div>
            <h3 className="text-xl font-display font-bold text-gray-900 dark:text-white">Guaranteed Brand Safety</h3>
            <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
              Enforce strict rules on mandatory hashtags, brand mentions, allowed aspect ratios, and prohibited sensitive topics. Every submission is audited before reward settlement.
            </p>
          </div>

          <div className="p-8 rounded-2xl bg-white dark:bg-[#0B0B10] border border-gray-200 dark:border-white/[0.08] shadow-sm dark:shadow-xl space-y-4">
            <div className="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 border border-cyan-200 dark:border-cyan-500/30 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
              <Globe className="w-6 h-6" />
            </div>
            <h3 className="text-xl font-display font-bold text-gray-900 dark:text-white">Granular Country Targeting</h3>
            <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
              Target high-value purchasing markets (US, UK, Canada, Germany, UAE) with custom reward multipliers to ensure your content reaches your exact target customer demographics.
            </p>
          </div>
        </div>

      </div>
    </div>
  );
};
