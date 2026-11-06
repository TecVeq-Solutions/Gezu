import React, { useState } from 'react';
import { Campaign, Platform } from '../../types';
import { Search, Filter, Globe, Clock, ArrowRight, Video, CheckCircle2, ChevronRight, Share2, DollarSign, X } from 'lucide-react';

interface MarketplacePageProps {
  campaigns: Campaign[];
  onSelectCampaignForSubmission: (camp: Campaign) => void;
  onLaunchCampaign: () => void;
}

export const MarketplacePage: React.FC<MarketplacePageProps> = ({
  campaigns,
  onSelectCampaignForSubmission,
  onLaunchCampaign
}) => {
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [selectedCategory, setSelectedCategory] = useState<string>('All');
  const [selectedPlatform, setSelectedPlatform] = useState<string>('All');
  const [sortBy, setSortBy] = useState<'highest_reward' | 'newest' | 'most_views'>('highest_reward');
  const [selectedDetailCamp, setSelectedDetailCamp] = useState<Campaign | null>(null);

  const categories = ['All', 'Technology & AI', 'Hardware & Tech', 'Business & Startups', 'Gaming & VFX'];

  const filteredCampaigns = campaigns.filter(c => {
    const matchesSearch = c.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
                          c.brandName.toLowerCase().includes(searchQuery.toLowerCase()) ||
                          c.description.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCat = selectedCategory === 'All' || c.category === selectedCategory;
    const matchesPlatform = selectedPlatform === 'All' || c.platforms.includes(selectedPlatform as Platform);
    return matchesSearch && matchesCat && matchesPlatform;
  }).sort((a, b) => {
    if (sortBy === 'highest_reward') return b.rewardRatePerThousand - a.rewardRatePerThousand;
    if (sortBy === 'most_views') return b.verifiedViews - a.verifiedViews;
    return b.daysRemaining - a.daysRemaining;
  });

  return (
    <div className="pt-28 pb-24 min-h-screen bg-[#070708]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Marketplace Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-8 border-b border-white/[0.08]">
          <div>
            <span className="text-xs font-mono font-semibold uppercase tracking-widest text-violet-400 block mb-1">
              Creator Marketplace
            </span>
            <h1 className="text-3xl sm:text-4xl font-display font-extrabold text-[#F7F7F5] tracking-tight">
              Active Campaigns
            </h1>
            <p className="mt-1 text-xs sm:text-sm text-[#A1A1AA]">
              Discover verified campaigns, create short-form edits, and earn for eligible verified views.
            </p>
          </div>

          <button
            onClick={onLaunchCampaign}
            className="px-5 py-2.5 rounded-xl font-semibold text-xs text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-800/20 transition-all self-start md:self-auto flex items-center gap-1.5"
          >
            Launch a Campaign
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>

        {/* Filter Controls Bar */}
        <div className="my-8 grid grid-cols-1 md:grid-cols-4 gap-3">
          
          {/* Search box */}
          <div className="relative md:col-span-2">
            <Search className="w-4 h-4 text-[#71717A] absolute left-3.5 top-3" />
            <input
              type="text"
              placeholder="Search campaigns by keyword, podcast or brand..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full bg-[#121218] border border-white/10 rounded-xl pl-10 pr-4 py-2.5 text-xs text-white placeholder-[#71717A] focus:outline-none focus:border-violet-500"
            />
          </div>

          {/* Category Dropdown */}
          <div>
            <select
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
              className="w-full bg-[#121218] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-violet-500"
            >
              {categories.map(cat => (
                <option key={cat} value={cat} className="bg-[#121218] text-white">
                  Category: {cat}
                </option>
              ))}
            </select>
          </div>

          {/* Sort By Dropdown */}
          <div>
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value as any)}
              className="w-full bg-[#121218] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-violet-500"
            >
              <option value="highest_reward" className="bg-[#121218]">Sort: Highest Reward Rate</option>
              <option value="most_views" className="bg-[#121218]">Sort: Most Verified Views</option>
              <option value="newest" className="bg-[#121218]">Sort: Ending Soon</option>
            </select>
          </div>

        </div>

        {/* Campaign Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {filteredCampaigns.map((camp) => {
            const progress = ((camp.verifiedViews / camp.targetViews) * 100).toFixed(1);
            return (
              <div 
                key={camp.id}
                className="bg-[#0B0B10] rounded-2xl overflow-hidden border border-white/[0.08] hover:border-violet-500/40 transition-all duration-300 flex flex-col justify-between group"
              >
                {/* Thumbnail */}
                <div className="relative h-44 w-full overflow-hidden bg-black">
                  <img 
                    src={camp.thumbnail} 
                    alt={camp.title}
                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-80"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#0B0B10] via-transparent to-black/50" />
                  
                  <div className="absolute top-3 left-3">
                    <span className="text-[10px] font-mono font-semibold px-2 py-0.5 rounded-full bg-black/80 backdrop-blur-md text-[#D4D4D8] border border-white/15">
                      {camp.category}
                    </span>
                  </div>

                  <div className="absolute top-3 right-3">
                    <span className="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-emerald-500/20 backdrop-blur-md text-emerald-400 border border-emerald-500/30">
                      ● ACTIVE
                    </span>
                  </div>

                  <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs">
                    <span className="font-mono font-bold text-white drop-shadow">
                      {camp.brandName}
                    </span>
                    <span className="text-[11px] text-[#A1A1AA] font-mono flex items-center gap-1 drop-shadow">
                      <Clock className="w-3 h-3 text-amber-400" />
                      {camp.daysRemaining} days left
                    </span>
                  </div>
                </div>

                {/* Content */}
                <div className="p-5 flex-1 flex flex-col justify-between space-y-4">
                  <div>
                    <h3 className="text-base font-display font-bold text-white group-hover:text-violet-300 transition-colors line-clamp-1">
                      {camp.title}
                    </h3>
                    <p className="mt-1 text-xs text-[#A1A1AA] line-clamp-2 leading-relaxed">
                      {camp.description}
                    </p>
                  </div>

                  {/* Metrics */}
                  <div className="grid grid-cols-2 gap-2 text-xs">
                    <div className="p-2.5 rounded-lg bg-[#121218] border border-white/5">
                      <span className="text-[10px] text-[#71717A] font-mono block">Reward Rate</span>
                      <span className="font-mono font-bold text-violet-400 text-sm">
                        ${camp.rewardRatePerThousand.toFixed(2)} / 1K
                      </span>
                    </div>
                    <div className="p-2.5 rounded-lg bg-[#121218] border border-white/5">
                      <span className="text-[10px] text-[#71717A] font-mono block">Fund Pool</span>
                      <span className="font-mono font-bold text-white text-sm">
                        ${camp.totalBudget.toLocaleString()}
                      </span>
                    </div>
                  </div>

                  {/* Progress */}
                  <div>
                    <div className="flex items-center justify-between text-[11px] font-mono text-[#A1A1AA] mb-1">
                      <span>{camp.verifiedViews.toLocaleString()} / {camp.targetViews.toLocaleString()}</span>
                      <span className="text-white font-bold">{progress}%</span>
                    </div>
                    <div className="w-full h-1.5 rounded-full bg-white/[0.08] overflow-hidden">
                      <div 
                        className="h-full bg-gradient-to-r from-violet-600 to-cyan-400 rounded-full"
                        style={{ width: `${progress}%` }}
                      />
                    </div>
                  </div>

                  {/* Platforms */}
                  <div className="flex items-center justify-between text-[10px] font-mono text-[#71717A] pt-1">
                    <div className="flex items-center gap-1">
                      {camp.platforms.map((p, i) => (
                        <span key={i} className="px-1.5 py-0.5 rounded bg-white/[0.04] text-[#D4D4D8]">
                          {p}
                        </span>
                      ))}
                    </div>
                    <span>{camp.totalSubmissions} clips submitted</span>
                  </div>

                  {/* Actions */}
                  <div className="grid grid-cols-2 gap-2 pt-2 border-t border-white/[0.06]">
                    <button
                      onClick={() => setSelectedDetailCamp(camp)}
                      className="py-2 rounded-lg text-xs font-semibold text-[#D4D4D8] hover:text-white bg-white/[0.04] hover:bg-white/[0.08] border border-white/10 transition-all text-center"
                    >
                      Inspect Rules
                    </button>
                    <button
                      onClick={() => onSelectCampaignForSubmission(camp)}
                      className="py-2 rounded-lg text-xs font-semibold text-white bg-violet-600 hover:bg-violet-500 transition-all flex items-center justify-center gap-1 cursor-pointer"
                    >
                      <Video className="w-3.5 h-3.5" />
                      Submit Clip
                    </button>
                  </div>

                </div>
              </div>
            );
          })}
        </div>

      </div>

      {/* Campaign Detail Inspector Modal */}
      {selectedDetailCamp && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
          <div className="relative w-full max-w-2xl bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-y-auto max-h-[90vh]">
            
            <div className="flex items-start justify-between pb-4 border-b border-white/[0.08]">
              <div>
                <span className="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-violet-500/20 text-violet-300 border border-violet-500/30">
                  {selectedDetailCamp.category}
                </span>
                <h3 className="text-xl font-display font-bold text-white mt-2">
                  {selectedDetailCamp.title}
                </h3>
                <span className="text-xs text-[#71717A] font-mono">By {selectedDetailCamp.brandName} • Duration: {selectedDetailCamp.sourceDuration}</span>
              </div>
              <button 
                onClick={() => setSelectedDetailCamp(null)}
                className="p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5"
              >
                <X className="w-5 h-5" />
              </button>
            </div>

            <div className="mt-6 space-y-6 text-xs">
              
              {/* Description & Source Video Link */}
              <div>
                <h4 className="font-mono font-bold text-white uppercase text-[11px] mb-1">Source Content Context:</h4>
                <p className="text-[#A1A1AA] leading-relaxed">
                  {selectedDetailCamp.description}
                </p>
                <div className="mt-2.5 p-3 rounded-xl bg-[#121218] border border-white/10 flex items-center justify-between">
                  <span className="text-[#D4D4D8] font-mono truncate mr-2">{selectedDetailCamp.sourceVideoUrl}</span>
                  <a 
                    href={selectedDetailCamp.sourceVideoUrl} 
                    target="_blank" 
                    rel="noreferrer"
                    className="px-3 py-1 bg-white/10 hover:bg-white/20 text-white rounded font-semibold shrink-0"
                  >
                    Open Raw Video ↗
                  </a>
                </div>
              </div>

              {/* Requirements & Guidelines */}
              <div className="p-4 rounded-xl bg-[#121218] border border-white/10 space-y-3">
                <h4 className="font-mono font-bold text-violet-400 uppercase text-[11px]">Submission Requirements:</h4>
                <div className="grid grid-cols-2 gap-3 text-[#D4D4D8]">
                  <div>
                    <span className="text-[#71717A] block">Aspect Ratio:</span>
                    <strong className="text-white">{selectedDetailCamp.requirements.aspectRatio}</strong>
                  </div>
                  <div>
                    <span className="text-[#71717A] block">Duration:</span>
                    <strong className="text-white">{selectedDetailCamp.requirements.duration}</strong>
                  </div>
                  <div className="col-span-2">
                    <span className="text-[#71717A] block">Required Hashtags:</span>
                    <div className="flex flex-wrap gap-1 mt-1">
                      {selectedDetailCamp.requirements.requiredHashtags.map((h, i) => (
                        <span key={i} className="px-2 py-0.5 rounded bg-violet-950/80 border border-violet-500/30 text-violet-300 font-mono text-[10px]">
                          {h}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Target Country Weights */}
              <div>
                <h4 className="font-mono font-bold text-white uppercase text-[11px] mb-2">Target Countries & Multipliers:</h4>
                <div className="grid grid-cols-3 gap-2">
                  {selectedDetailCamp.countries.map(c => (
                    <div key={c.countryCode} className="p-2.5 rounded-lg bg-[#121218] border border-white/5 font-mono">
                      <span className="text-white font-bold block">{c.countryName} ({c.countryCode})</span>
                      <span className="text-violet-400 text-[10px]">{c.rewardMultiplier}x Rate Multiplier</span>
                    </div>
                  ))}
                </div>
              </div>

            </div>

            <div className="mt-8 pt-4 border-t border-white/[0.08] flex items-center justify-between">
              <span className="font-mono text-sm text-emerald-400 font-bold">
                Reward: ${selectedDetailCamp.rewardRatePerThousand.toFixed(2)} / 1,000 Verified Views
              </span>
              <button
                onClick={() => {
                  const camp = selectedDetailCamp;
                  setSelectedDetailCamp(null);
                  onSelectCampaignForSubmission(camp);
                }}
                className="px-6 py-2.5 rounded-xl font-semibold text-xs text-white bg-violet-600 hover:bg-violet-500 transition-all flex items-center gap-1.5 cursor-pointer shadow-lg shadow-violet-900/30"
              >
                <Video className="w-4 h-4" />
                Join & Submit Clip
              </button>
            </div>

          </div>
        </div>
      )}

    </div>
  );
};
