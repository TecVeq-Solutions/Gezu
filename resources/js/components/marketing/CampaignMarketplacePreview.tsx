import React from 'react';
import { Campaign } from '../../types';
import { ArrowRight, Video, Globe2, Clock, DollarSign, BarChart2, Sparkles } from 'lucide-react';
import { ScrollWaveField } from './ScrollWaveField';
import { StarfieldButton } from '../common/StarfieldButton';

interface CampaignMarketplacePreviewProps {
  campaigns: Campaign[];
  onSelectCampaign: (campaign: Campaign) => void;
  onExploreAll: () => void;
}

export const CampaignMarketplacePreview: React.FC<CampaignMarketplacePreviewProps> = ({
  campaigns,
  onSelectCampaign,
  onExploreAll
}) => {
  return (
    <section className="py-24 lg:py-32 relative bg-gray-50 dark:bg-[#070708] border-b border-black/[0.08] dark:border-white/[0.08] overflow-hidden">
      
      {/* 3D WebGL Scroll Wave Particle Field Background */}
      <ScrollWaveField
        colors={["#8B5CF6", "#38BDF8", "#EC4899", "#6366F1"]}
        gridCols={80}
        gridRows={50}
        waveSpeed={1.4}
        waveHeight={28}
        pointSize={3.6}
        showLines={true}
        scrollSensitivity={0.1}
        opacity={0.45}
      />

      {/* Ambient background glows */}
      <div className="absolute top-1/3 left-1/4 w-[500px] h-[300px] bg-violet-600/10 blur-[130px] rounded-full pointer-events-none" />
      <div className="absolute bottom-1/4 right-1/4 w-[500px] h-[300px] bg-cyan-600/10 blur-[130px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div>
            <span className="text-xs font-mono font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-3">
              Live Bounty Board
            </span>
            <h2 className="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight">
              Campaigns Looking for Creators
            </h2>
            <p className="mt-3 text-sm text-gray-600 dark:text-[#A1A1AA] max-w-xl">
              High-budget verified campaigns open for immediate video clip creation and distribution.
            </p>
          </div>

          <StarfieldButton
            size="sm"
            variant="secondary"
            onClick={onExploreAll}
            icon={<ArrowRight className="w-3.5 h-3.5 text-violet-500 dark:text-violet-400" />}
            iconPosition="right"
          >
            Explore All Campaigns
          </StarfieldButton>
        </div>

        {/* 3-4 Premium Campaign Cards Grid (Section 15) */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {campaigns.slice(0, 3).map((camp) => {
            const progress = ((camp.verifiedViews / camp.targetViews) * 100).toFixed(1);
            return (
              <div 
                key={camp.id}
                className="bg-white dark:bg-[#0B0B10] rounded-2xl overflow-hidden border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 shadow-sm dark:shadow-xl transition-all duration-300 flex flex-col justify-between group"
              >
                {/* Image Header with Badge Overlay */}
                <div className="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-black/60">
                  <img 
                    src={camp.thumbnail} 
                    alt={camp.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 dark:opacity-80"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 dark:from-[#0B0B10] dark:via-transparent dark:to-black/40" />
                  
                  {/* Category & Status pills */}
                  <div className="absolute top-3 left-3 flex items-center gap-2">
                    <span className="text-[10px] font-mono font-semibold px-2.5 py-1 rounded-full bg-black/70 backdrop-blur-md text-[#D4D4D8] border border-white/15">
                      {camp.category}
                    </span>
                  </div>

                  <div className="absolute top-3 right-3">
                    <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-emerald-500/20 backdrop-blur-md text-emerald-400 border border-emerald-500/30">
                      ACTIVE
                    </span>
                  </div>

                  {/* Brand identifier at bottom of thumbnail */}
                  <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs">
                    <span className="font-mono font-semibold text-white/90 drop-shadow">
                      {camp.brandName}
                    </span>
                    <span className="text-[11px] text-[#A1A1AA] font-mono flex items-center gap-1 drop-shadow">
                      <Clock className="w-3 h-3 text-amber-400" />
                      {camp.daysRemaining}d left
                    </span>
                  </div>
                </div>

                {/* Card Content Body */}
                <div className="p-6 flex-1 flex flex-col justify-between space-y-5">
                  <div>
                    <h3 className="text-lg font-display font-bold text-gray-900 dark:text-white group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors line-clamp-1">
                      {camp.title}
                    </h3>
                    <p className="mt-2 text-xs text-gray-600 dark:text-[#A1A1AA] line-clamp-2 leading-relaxed">
                      {camp.description}
                    </p>
                  </div>

                  {/* Key Stats Bar */}
                  <div className="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 dark:border-white/[0.06] text-xs">
                    <div className="p-2.5 rounded-xl bg-gray-50 dark:bg-[#121218] border border-gray-200 dark:border-white/5">
                      <span className="text-[10px] text-gray-500 dark:text-[#71717A] font-mono block">Reward Rate</span>
                      <span className="font-mono font-bold text-violet-600 dark:text-violet-400 text-sm">
                        ${camp.rewardRatePerThousand.toFixed(2)} / 1K
                      </span>
                    </div>
                    <div className="p-2.5 rounded-xl bg-gray-50 dark:bg-[#121218] border border-gray-200 dark:border-white/5">
                      <span className="text-[10px] text-gray-500 dark:text-[#71717A] font-mono block">Total Budget</span>
                      <span className="font-mono font-bold text-gray-900 dark:text-white text-sm">
                        ${camp.totalBudget.toLocaleString()}
                      </span>
                    </div>
                  </div>

                  {/* Verified Views Progress */}
                  <div>
                    <div className="flex items-center justify-between text-[11px] font-mono text-gray-600 dark:text-[#A1A1AA] mb-1.5">
                      <span>{camp.verifiedViews.toLocaleString()} verified</span>
                      <span className="text-gray-900 dark:text-white font-bold">{progress}%</span>
                    </div>
                    <div className="w-full h-2 rounded-full bg-gray-200 dark:bg-white/[0.08] overflow-hidden">
                      <div 
                        className="h-full bg-gradient-to-r from-violet-600 to-cyan-400 rounded-full"
                        style={{ width: `${progress}%` }}
                      />
                    </div>
                  </div>

                  {/* Supported Platforms & Target Country */}
                  <div className="flex items-center justify-between text-[11px] pt-2 border-t border-gray-100 dark:border-white/[0.06]">
                    <div className="flex items-center gap-1.5 text-gray-700 dark:text-[#D4D4D8]">
                      {camp.platforms.map((p, idx) => (
                        <span key={idx} className="px-1.5 py-0.5 rounded bg-gray-100 dark:bg-white/[0.05] text-[10px] font-mono text-gray-600 dark:text-[#A1A1AA]">
                          {p}
                        </span>
                      ))}
                    </div>
                    <span className="text-xs text-gray-600 dark:text-[#A1A1AA] font-mono flex items-center gap-1">
                      <Globe2 className="w-3 h-3 text-blue-500 dark:text-blue-400" />
                      {camp.countries[0]?.countryCode} +{camp.countries.length - 1}
                    </span>
                  </div>

                  {/* Action CTA */}
                  <button
                    onClick={() => onSelectCampaign(camp)}
                    className="w-full py-2.5 rounded-xl font-semibold text-xs text-gray-900 dark:text-white bg-gray-100 hover:bg-violet-600 hover:text-white dark:bg-white/[0.06] dark:hover:bg-violet-600 border border-gray-200 dark:border-white/10 hover:border-violet-500 transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-sm"
                  >
                    View Campaign
                    <ArrowRight className="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>
            );
          })}
        </div>

        {/* Marketplace CTA below cards (Section 16) */}
        <div className="mt-12 flex justify-center">
          <StarfieldButton
            size="lg"
            variant="primary"
            onClick={onExploreAll}
            icon={<ArrowRight className="w-4 h-4" />}
            iconPosition="right"
          >
            Explore All Campaigns
          </StarfieldButton>
        </div>

      </div>
    </section>
  );
};
