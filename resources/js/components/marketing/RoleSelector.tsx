import React from 'react';
import { Scissors, Sparkles, ArrowRight, Check, TrendingUp, BarChart3, Wallet, Users } from 'lucide-react';
import { Vortex } from './Vortex';
import { StarfieldButton } from '../common/StarfieldButton';

interface RoleSelectorProps {
  onSelectClipper: () => void;
  onSelectBrand: () => void;
}

export const RoleSelector: React.FC<RoleSelectorProps> = ({
  onSelectClipper,
  onSelectBrand
}) => {
  return (
    <section className="py-24 lg:py-32 relative bg-white dark:bg-[#070708] border-b border-black/[0.08] dark:border-white/[0.08] overflow-hidden">
      
      {/* 3D WebGL Vortex / Tornado Background */}
      <Vortex
        topRadius={350}
        waistRadius={50}
        bottomRadius={1100}
        twist={3.2}
        zoom={72}
        speed={12}
        direction="right"
        lineOptions={{ count: 220, color: "#7C3AED", glow: 8 }}
        dotOptions={{ count: 5500, size: 22, color: "#C084FC", glow: 8, flicker: 10 }}
        cometOptions={{ count: 12, speed: 7, color: "#38BDF8", glow: 8, tail: 18, delay: 6, collide: 5 }}
        repel={true}
        repelOptions={{ radius: 65, strength: 18 }}
        className="opacity-30 dark:opacity-60"
      />

      {/* Ambient background glows */}
      <div className="absolute top-1/2 left-1/4 w-[500px] h-[300px] bg-violet-600/10 blur-[130px] rounded-full pointer-events-none" />
      <div className="absolute top-1/2 right-1/4 w-[500px] h-[300px] bg-blue-600/10 blur-[130px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Header */}
        <div className="text-center max-w-3xl mx-auto mb-16">
          <span className="text-xs font-mono font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-3">
            Two Sides of the Marketplace
          </span>
          <h2 className="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight">
            Where Do You Fit?
          </h2>
          <p className="mt-4 text-base text-gray-600 dark:text-[#A1A1AA]">
            Whether you edit high-retention vertical clips or own long-form intellectual property, GEZUX provides the infrastructure.
          </p>
        </div>

        {/* Dual Side Cards (Section 14) */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          
          {/* FOR CLIPPERS */}
          <div className="bg-gray-50 dark:bg-[#0B0B10] rounded-2xl p-8 border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 shadow-sm dark:shadow-xl transition-all duration-300 relative flex flex-col justify-between group overflow-hidden">
            <div className="absolute -top-24 -right-24 w-60 h-60 bg-violet-600/10 blur-[90px] rounded-full pointer-events-none" />
            
            <div>
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300 text-xs font-mono font-semibold mb-6">
                <Scissors className="w-3.5 h-3.5" />
                FOR CLIPPERS
              </div>

              <h3 className="text-2xl sm:text-3xl font-display font-bold text-gray-900 dark:text-white tracking-tight">
                Turn Your Editing Skills <br />
                <span className="text-violet-600 dark:text-violet-400">Into Income.</span>
              </h3>

              <p className="mt-4 text-sm text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                Browse pre-approved raw content from global podcasts, creators, and technology companies. Pick your favorites, cut dynamic vertical hooks, post across your accounts, and get paid for verified eligible views.
              </p>

              {/* Bullet checklist */}
              <div className="mt-6 space-y-2.5">
                {[
                  'Instant access to verified high-budget campaigns',
                  'Guaranteed prompt payout upon verified view thresholds',
                  'Multi-platform support (TikTok, Shorts, Reels, X)',
                  'Direct earnings ledger with zero hidden transaction deductions'
                ].map((item, i) => (
                  <div key={i} className="flex items-center gap-2.5 text-xs text-gray-700 dark:text-[#D4D4D8]">
                    <div className="w-4 h-4 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                      <Check className="w-2.5 h-2.5" />
                    </div>
                    <span>{item}</span>
                  </div>
                ))}
              </div>

              {/* Clipper Preview Mock Widget */}
              <div className="mt-8 p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] shadow-sm space-y-3">
                <div className="flex items-center justify-between text-xs pb-2 border-b border-gray-100 dark:border-white/[0.05]">
                  <span className="text-gray-500 dark:text-[#A1A1AA] font-mono">Creator Balance Preview</span>
                  <span className="text-emerald-600 dark:text-emerald-400 font-mono font-bold">$1,420.50 Available</span>
                </div>
                <div className="grid grid-cols-2 gap-2 text-[11px]">
                  <div className="p-2 rounded bg-gray-50 dark:bg-black/40 border border-gray-100 dark:border-white/5">
                    <span className="text-gray-500 dark:text-[#71717A] block">Verified Views</span>
                    <span className="font-mono font-bold text-gray-900 dark:text-white text-sm">1.84M</span>
                  </div>
                  <div className="p-2 rounded bg-gray-50 dark:bg-black/40 border border-gray-100 dark:border-white/5">
                    <span className="text-gray-500 dark:text-[#71717A] block">Active Submissions</span>
                    <span className="font-mono font-bold text-gray-900 dark:text-white text-sm">12 Clips</span>
                  </div>
                </div>
              </div>

            </div>

            <div className="mt-8 pt-6 border-t border-gray-200 dark:border-white/[0.08]">
              <StarfieldButton
                size="md"
                variant="primary"
                onClick={onSelectClipper}
                icon={<ArrowRight className="w-4 h-4" />}
                iconPosition="right"
                className="w-full"
              >
                Explore Campaigns
              </StarfieldButton>
            </div>
          </div>

          {/* FOR BRANDS */}
          <div className="bg-gray-50 dark:bg-[#0B0B10] rounded-2xl p-8 border border-gray-200 dark:border-white/[0.08] hover:border-blue-500/40 shadow-sm dark:shadow-xl transition-all duration-300 relative flex flex-col justify-between group overflow-hidden">
            <div className="absolute -top-24 -right-24 w-60 h-60 bg-blue-600/10 blur-[90px] rounded-full pointer-events-none" />

            <div>
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300 text-xs font-mono font-semibold mb-6">
                <Sparkles className="w-3.5 h-3.5" />
                FOR BRANDS & CREATORS
              </div>

              <h3 className="text-2xl sm:text-3xl font-display font-bold text-gray-900 dark:text-white tracking-tight">
                Turn One Video Into A <br />
                <span className="text-blue-600 dark:text-blue-400">Distribution Network.</span>
              </h3>

              <p className="mt-4 text-sm text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                Stop relying on single uploads. Launch a campaign with defined reward parameters ($1.00 per 1,000 views) and watch dozens of skilled video editors scale your presence across social algorithms simultaneously.
              </p>

              {/* Bullet checklist */}
              <div className="mt-6 space-y-2.5">
                {[
                  'Pay only for verified eligible view metrics, not empty promises',
                  'Granular target country filters (USA, UK, Tier 1, Global)',
                  'Custom brand safety rules, mandatory hashtags & handles',
                  'Live real-time performance analytics & top clipper leaderboards'
                ].map((item, i) => (
                  <div key={i} className="flex items-center gap-2.5 text-xs text-gray-700 dark:text-[#D4D4D8]">
                    <div className="w-4 h-4 rounded-full bg-blue-100 dark:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                      <Check className="w-2.5 h-2.5" />
                    </div>
                    <span>{item}</span>
                  </div>
                ))}
              </div>

              {/* Brand Preview Mock Widget */}
              <div className="mt-8 p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] shadow-sm space-y-3">
                <div className="flex items-center justify-between text-xs pb-2 border-b border-gray-100 dark:border-white/[0.05]">
                  <span className="text-gray-500 dark:text-[#A1A1AA] font-mono">Campaign Analytics Preview</span>
                  <span className="text-blue-600 dark:text-blue-400 font-mono font-bold">11.8M Total Verified Reach</span>
                </div>
                <div className="grid grid-cols-2 gap-2 text-[11px]">
                  <div className="p-2 rounded bg-gray-50 dark:bg-black/40 border border-gray-100 dark:border-white/5">
                    <span className="text-gray-500 dark:text-[#71717A] block">Effective CPV</span>
                    <span className="font-mono font-bold text-gray-900 dark:text-white text-sm">$0.0012 / view</span>
                  </div>
                  <div className="p-2 rounded bg-gray-50 dark:bg-black/40 border border-gray-100 dark:border-white/5">
                    <span className="text-gray-500 dark:text-[#71717A] block">Active Clippers</span>
                    <span className="font-mono font-bold text-gray-900 dark:text-white text-sm">48 Creators</span>
                  </div>
                </div>
              </div>

            </div>

            <div className="mt-8 pt-6 border-t border-gray-200 dark:border-white/[0.08]">
              <StarfieldButton
                size="md"
                variant="brand"
                onClick={onSelectBrand}
                icon={<ArrowRight className="w-4 h-4" />}
                iconPosition="right"
                className="w-full"
              >
                Launch Campaign
              </StarfieldButton>
            </div>
          </div>

        </div>

      </div>
    </section>
  );
};
