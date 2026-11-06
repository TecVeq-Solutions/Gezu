import React from 'react';
import { Upload, Scissors, Share2, ShieldCheck, ArrowRight } from 'lucide-react';
import { LightCables } from './LightCables';

export const HowItWorks: React.FC = () => {
  const steps = [
    {
      num: '01',
      title: 'FUND',
      subtitle: 'Upload & Configure',
      desc: 'Brands upload long-form content, set view targets, select target countries and fund the campaign pool.',
      icon: Upload,
      accent: 'from-violet-500/20 to-indigo-500/10',
      iconColor: 'text-violet-400'
    },
    {
      num: '02',
      title: 'CLIP',
      subtitle: 'Create Short-Form',
      desc: 'High-caliber clippers discover the campaign, extract the best hooks, and edit vertical 9:16 video assets.',
      icon: Scissors,
      accent: 'from-purple-500/20 to-pink-500/10',
      iconColor: 'text-purple-400'
    },
    {
      num: '03',
      title: 'DISTRIBUTE',
      subtitle: 'Publish Everywhere',
      desc: 'Creators publish clips to TikTok, YouTube Shorts, Instagram Reels & X with mandatory campaign hashtags.',
      icon: Share2,
      accent: 'from-blue-500/20 to-cyan-500/10',
      iconColor: 'text-blue-400'
    },
    {
      num: '04',
      title: 'VERIFY',
      subtitle: 'Audit & Automatic Rewards',
      desc: 'GEZUX verifies eligible view metrics and credits creator balances according to campaign rates.',
      icon: ShieldCheck,
      accent: 'from-emerald-500/20 to-teal-500/10',
      iconColor: 'text-emerald-400'
    }
  ];

  return (
    <section id="how-it-works" className="py-24 lg:py-32 relative bg-gray-50 dark:bg-[#070708] border-b border-black/[0.08] dark:border-white/[0.08] overflow-hidden">
      
      {/* Light Cables Flowing WebGL Stream Background */}
      <LightCables
        background="#070708"
        baseColor="#0B0B10"
        accentColor="#7C3AED"
        highlight="#C084FC"
        direction="ttb"
        speed={38}
        hover={130}
        grab={120}
        bundle={{
          count: 42,
          bend: 38,
          spread: 95,
          thickness: 130,
          widthStart: 10,
          widthEnd: 260
        }}
        flow={{
          flow: 220,
          pulses: 2
        }}
        className="opacity-40 dark:opacity-70"
      />

      {/* Subtle radial ambient glow */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-violet-600/10 blur-[140px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 lg:mb-24">
          <span className="text-xs font-mono font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400 block mb-3">
            System Architecture
          </span>
          <h2 className="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight">
            One Campaign. Multiple Creators. <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-gray-900 to-gray-600 dark:from-white dark:to-[#A1A1AA]">
              Measurable Distribution.
            </span>
          </h2>
          <p className="mt-4 text-base text-gray-600 dark:text-[#A1A1AA]">
            A transparent four-step performance pipeline connecting video assets with organic viral reach.
          </p>
        </div>

        {/* 4 Connected Horizontal Steps (Section 13) */}
        <div className="relative">
          
          {/* Continuous connection line behind cards on desktop */}
          <div className="hidden lg:block absolute top-1/2 left-8 right-8 h-0.5 bg-gradient-to-r from-violet-600 via-blue-500 to-emerald-500 -translate-y-12 opacity-30 pointer-events-none" />

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
            {steps.map((step, idx) => {
              const Icon = step.icon;
              return (
                <div 
                  key={step.num}
                  className="bg-white dark:bg-[#121218] rounded-2xl p-6 border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/40 shadow-sm dark:shadow-xl transition-all duration-300 relative group flex flex-col justify-between"
                >
                  <div>
                    {/* Top step badge & icon */}
                    <div className="flex items-center justify-between mb-6">
                      <span className="text-xs font-mono font-bold px-2.5 py-1 rounded-md bg-gray-100 dark:bg-white/[0.06] text-gray-800 dark:text-[#D4D4D8] border border-gray-200 dark:border-white/10">
                        {step.num}
                      </span>
                      <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${step.accent} border border-gray-200 dark:border-white/10 flex items-center justify-center`}>
                        <Icon className={`w-5 h-5 ${step.iconColor}`} />
                      </div>
                    </div>

                    <span className="text-[11px] font-mono tracking-widest uppercase text-violet-600 dark:text-violet-400 block font-semibold">
                      {step.title}
                    </span>
                    <h3 className="text-lg font-display font-bold text-gray-900 dark:text-white mt-1 mb-2.5">
                      {step.subtitle}
                    </h3>
                    <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                      {step.desc}
                    </p>
                  </div>

                  <div className="mt-6 pt-4 border-t border-gray-200 dark:border-white/[0.05] flex items-center justify-between text-[11px] text-gray-500 dark:text-[#71717A]">
                    <span>Phase 0{idx + 1}</span>
                    <span className="group-hover:text-violet-600 dark:group-hover:text-violet-300 transition-colors font-medium">Continuous Pipeline →</span>
                  </div>
                </div>
              );
            })}
          </div>

        </div>

      </div>
    </section>
  );
};
