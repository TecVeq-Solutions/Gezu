import React, { useState, useEffect } from 'react';
import { ArrowRight, Play, CheckCircle2, TrendingUp, Sparkles, Video, Users, Globe2, ShieldCheck } from 'lucide-react';
import { CursorRingField } from './CursorRingField';
import { StarfieldButton } from '../common/StarfieldButton';

interface HeroProps {
  onExploreCampaigns: () => void;
  onLaunchCampaign: () => void;
  onOpenCalculator: () => void;
}

export const Hero: React.FC<HeroProps> = ({
  onExploreCampaigns,
  onLaunchCampaign,
  onOpenCalculator
}) => {
  const [viewCount, setViewCount] = useState(3742810);
  const [activeTab, setActiveTab] = useState<'creators' | 'metrics'>('creators');

  // Subtle real-time increment simulation to demonstrate dynamic system
  useEffect(() => {
    const interval = setInterval(() => {
      setViewCount(prev => prev + Math.floor(Math.random() * 8) + 3);
    }, 2400);
    return () => clearInterval(interval);
  }, []);

  const progressPercent = ((viewCount / 5000000) * 100).toFixed(1);

  return (
    <section className="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
      {/* WebGL Cursor Ring Particle Field - Enhanced Prominence */}
      <CursorRingField 
        colors={["#A855F7", "#8B5CF6", "#3B82F6", "#60A5FA", "#121218"]}
        density={290}
        dotSize={150}
        speed={6}
        cameraDistance={140}
        ring={{ push: 55, width: 12, radius: 16, turbulence: 95 }}
        className="opacity-80"
      />

      {/* Ambient background glow */}
      <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[400px] bg-violet-600/20 blur-[140px] rounded-full pointer-events-none" />
      <div className="absolute top-1/3 left-1/4 w-[450px] h-[300px] bg-blue-600/15 blur-[130px] rounded-full pointer-events-none" />
      
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Top Eyebrow */}
        <div className="flex justify-center">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.04] dark:bg-white/[0.04] bg-black/[0.04] border border-black/10 dark:border-white/[0.08] backdrop-blur-md mb-8">
            <span className="w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
            <span className="w-2 h-2 rounded-full bg-emerald-500 -ml-4" />
            <span className="text-[11px] font-mono tracking-widest text-gray-800 dark:text-[#D4D4D8] uppercase font-semibold">
              The Global Clipping Marketplace
            </span>
          </div>
        </div>

        {/* Hero Headline & Subtitle */}
        <div className="text-center max-w-4xl mx-auto">
          <h1 className="text-4xl sm:text-6xl lg:text-7xl font-display font-extrabold tracking-tight text-gray-900 dark:text-[#F7F7F5] leading-[1.08]">
            Turn Long-Form Content <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-gray-900 to-gray-700 dark:from-white dark:via-[#F7F7F5] dark:to-[#A1A1AA]">
              Into Distribution.
            </span>
          </h1>
          
          <p className="mt-6 text-lg sm:text-xl text-gray-600 dark:text-[#A1A1AA] max-w-2xl mx-auto font-normal leading-relaxed">
            GEZUX connects brands with creators who turn long-form videos into short-form content and distribute them across the platforms where attention lives.
          </p>

          {/* CTAs */}
          <div className="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <StarfieldButton
              size="lg"
              variant="primary"
              onClick={onLaunchCampaign}
              icon={<ArrowRight className="w-4 h-4" />}
              iconPosition="right"
              className="w-full sm:w-auto"
            >
              Launch a Campaign
            </StarfieldButton>
            <StarfieldButton
              size="lg"
              variant="secondary"
              onClick={onExploreCampaigns}
              icon={<Video className="w-4 h-4 text-violet-500 dark:text-violet-400" />}
              iconPosition="left"
              className="w-full sm:w-auto"
            >
              Start Clipping
            </StarfieldButton>
          </div>

          <p className="mt-4 text-xs text-gray-500 dark:text-[#71717A] font-medium">
            For brands, creators, podcasts, agencies and media companies.
          </p>
        </div>

        {/* Live Marketplace / Campaign Visualization (Section 11) */}
        <div className="mt-16 lg:mt-24 max-w-5xl mx-auto">
          <div className="relative p-1 rounded-2xl bg-gradient-to-b from-black/10 via-black/5 to-transparent dark:from-white/15 dark:via-white/5 dark:to-transparent shadow-xl dark:shadow-2xl">
            <div className="bg-white dark:bg-[#0B0B10] rounded-[15px] p-6 lg:p-8 border border-black/10 dark:border-white/[0.08] relative overflow-hidden">
              
              {/* Header inside the campaign visualization card */}
              <div className="flex flex-wrap items-center justify-between gap-4 pb-6 border-b border-gray-200 dark:border-white/[0.08]">
                <div className="flex items-center gap-3.5">
                  <div className="w-12 h-12 rounded-xl bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center font-display font-bold text-violet-700 dark:text-violet-300 text-lg">
                    FL
                  </div>
                  <div>
                    <div className="flex items-center gap-2">
                      <span className="text-xs font-mono font-semibold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                        ● LIVE CAMPAIGN
                      </span>
                      <span className="text-xs text-gray-500 dark:text-[#71717A]">Future Labs</span>
                    </div>
                    <h3 className="text-lg font-display font-bold text-gray-900 dark:text-white mt-0.5">
                      The Future of AI — Podcast #42
                    </h3>
                  </div>
                </div>

                <div className="flex items-center gap-4">
                  <div className="text-right">
                    <span className="text-[11px] text-gray-500 dark:text-[#71717A] uppercase font-mono block">Budget Funded</span>
                    <span className="text-base font-bold text-gray-900 dark:text-white font-mono">$5,000.00</span>
                  </div>
                  <div className="text-right pl-4 border-l border-gray-200 dark:border-white/10">
                    <span className="text-[11px] text-gray-500 dark:text-[#71717A] uppercase font-mono block">Reward Rate</span>
                    <span className="text-base font-bold text-violet-600 dark:text-violet-400 font-mono">$1.00 / 1K</span>
                  </div>
                </div>
              </div>

              {/* Verified Views & Dynamic Progress Bar */}
              <div className="py-6 border-b border-gray-200 dark:border-white/[0.08]">
                <div className="flex flex-wrap items-end justify-between gap-2 mb-3">
                  <div>
                    <span className="text-xs text-gray-500 dark:text-[#A1A1AA] uppercase tracking-wider font-mono">
                      Verified Eligible Views
                    </span>
                    <div className="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white font-mono tracking-tight flex items-center gap-2 mt-1">
                      {viewCount.toLocaleString()}
                      <span className="text-xs font-semibold px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-300 border border-violet-200 dark:border-violet-500/30">
                        +14.2% today
                      </span>
                    </div>
                  </div>
                  <div className="text-right">
                    <span className="text-sm font-bold text-gray-900 dark:text-white font-mono">{progressPercent}%</span>
                    <span className="text-xs text-gray-500 dark:text-[#71717A] block">Target: 5,000,000 Views</span>
                  </div>
                </div>

                {/* Progress bar with glowing head */}
                <div className="w-full h-3 rounded-full bg-gray-100 dark:bg-white/[0.06] overflow-hidden p-0.5 border border-gray-200 dark:border-white/[0.08]">
                  <div 
                    className="h-full rounded-full bg-gradient-to-r from-violet-600 via-purple-500 to-cyan-400 transition-all duration-700 relative"
                    style={{ width: `${progressPercent}%` }}
                  >
                    <div className="absolute right-0 top-0 bottom-0 w-2 bg-white rounded-full shadow-[0_0_12px_#fff]" />
                  </div>
                </div>
              </div>

              {/* Visual Connected Creator Cards (Section 11) */}
              <div className="pt-6">
                <div className="flex items-center justify-between mb-4">
                  <span className="text-xs font-mono uppercase tracking-wider text-gray-600 dark:text-[#A1A1AA] flex items-center gap-1.5 font-semibold">
                    <Users className="w-3.5 h-3.5 text-violet-500 dark:text-violet-400" />
                    Top Active Clipping Creators & Verified Platform Distribution
                  </span>
                  <span className="text-[11px] text-gray-500 dark:text-[#71717A]">
                    Automated view verification active
                  </span>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                  {/* Creator 1 */}
                  <div className="p-4 rounded-xl bg-gray-50 dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 transition-all flex items-center justify-between group">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-pink-500 to-rose-600 flex items-center justify-center font-bold text-white text-xs">
                        AC
                      </div>
                      <div>
                        <div className="flex items-center gap-1">
                          <span className="text-sm font-semibold text-gray-900 dark:text-white">@AlexClips</span>
                          <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <span className="text-xs text-rose-600 dark:text-rose-400 font-medium">TikTok • USA</span>
                      </div>
                    </div>
                    <div className="text-right">
                      <span className="text-sm font-bold text-gray-900 dark:text-white font-mono">128,000</span>
                      <span className="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-semibold">+$128.00</span>
                    </div>
                  </div>

                  {/* Creator 2 */}
                  <div className="p-4 rounded-xl bg-gray-50 dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 transition-all flex items-center justify-between group">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center font-bold text-white text-xs">
                        MF
                      </div>
                      <div>
                        <div className="flex items-center gap-1">
                          <span className="text-sm font-semibold text-gray-900 dark:text-white">@MediaForge</span>
                          <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <span className="text-xs text-purple-600 dark:text-purple-400 font-medium">Instagram • UK</span>
                      </div>
                    </div>
                    <div className="text-right">
                      <span className="text-sm font-bold text-gray-900 dark:text-white font-mono">84,000</span>
                      <span className="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-semibold">+$84.00</span>
                    </div>
                  </div>

                  {/* Creator 3 */}
                  <div className="p-4 rounded-xl bg-gray-50 dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 transition-all flex items-center justify-between group">
                    <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-red-500 to-amber-600 flex items-center justify-center font-bold text-white text-xs">
                        VC
                      </div>
                      <div>
                        <div className="flex items-center gap-1">
                          <span className="text-sm font-semibold text-gray-900 dark:text-white">@ViralCut</span>
                          <CheckCircle2 className="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <span className="text-xs text-red-600 dark:text-red-400 font-medium">Shorts • Canada</span>
                      </div>
                    </div>
                    <div className="text-right">
                      <span className="text-sm font-bold text-gray-900 dark:text-white font-mono">62,000</span>
                      <span className="text-[11px] text-emerald-600 dark:text-emerald-400 block font-mono font-semibold">+$62.00</span>
                    </div>
                  </div>
                </div>

                {/* Subtext */}
                <div className="mt-4 pt-3 border-t border-gray-200 dark:border-white/[0.05] flex items-center justify-between text-xs text-gray-500 dark:text-[#71717A]">
                  <span className="flex items-center gap-1.5">
                    <ShieldCheck className="w-4 h-4 text-emerald-500 dark:text-emerald-400" />
                    Eligible views verified via API metrics & fraud heuristic filters
                  </span>
                  <span className="font-mono text-[11px] text-violet-600 dark:text-violet-400 font-semibold">
                    One Campaign → Multi-Creator Scaled Reach
                  </span>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>
    </section>
  );
};
