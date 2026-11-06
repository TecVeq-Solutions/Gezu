import React from 'react';
import { ShieldCheck, FileCheck, UserCheck, ShieldAlert, Video, Users, Scissors, Share2, Eye, Award } from 'lucide-react';
import { DataStrings } from './DataStrings';

export const TrustSection: React.FC = () => {
  const principles = [
    {
      title: 'View Verification',
      desc: 'Eligible submitted views are independently audited before rewards are calculated and settled.',
      icon: ShieldCheck,
      color: 'text-emerald-400'
    },
    {
      title: 'Campaign Rules',
      desc: 'Every campaign clearly specifies aspect ratios, durations, hashtags, mentions, and safety criteria.',
      icon: FileCheck,
      color: 'text-violet-400'
    },
    {
      title: 'Creator Verification',
      desc: 'Connected social channels and verified track records allow brands to review creator quality.',
      icon: UserCheck,
      color: 'text-blue-400'
    },
    {
      title: 'Fraud Detection',
      desc: 'Multi-layer heuristics detect velocity spikes, duplicate URLs, and inorganic traffic patterns.',
      icon: ShieldAlert,
      color: 'text-amber-400'
    }
  ];

  return (
    <section className="py-24 lg:py-32 relative bg-white dark:bg-[#070708] border-b border-black/[0.08] dark:border-white/[0.08] overflow-hidden">
      
      {/* Data Strings Interactive Telemetry Stream Background */}
      <DataStrings
        orientation="down"
        colorMode="flow"
        palette={["#7C3AED", "#3B82F6", "#A855F7", "#38BDF8", "#8B5CF6"]}
        strands={1300}
        focus={100}
        waist={0}
        sweep={0}
        reach={100}
        curve={100}
        strandWidth={24}
        strandOpacity={15}
        particles={160}
        cursor={60}
        className="opacity-30 dark:opacity-50"
      />

      {/* Ambient background glow */}
      <div className="absolute top-1/3 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-violet-600/15 blur-[140px] rounded-full pointer-events-none" />

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {/* Split Left-Right Editorial Header */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end mb-16 lg:mb-20 pb-10 border-b border-gray-200 dark:border-white/[0.08]">
          
          {/* Left Column: Eyebrow + Main Heading */}
          <div className="lg:col-span-7 space-y-3">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 text-xs font-mono font-semibold text-violet-700 dark:text-violet-300 backdrop-blur-md">
              <ShieldCheck className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
              TRUST & VERIFICATION ENGINE
            </div>
            
            <h2 className="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight leading-[1.12]">
              Built Around <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-gray-900 to-cyan-600 dark:from-violet-400 dark:via-white dark:to-cyan-300">
                Verifiable Performance.
              </span>
            </h2>
          </div>

          {/* Right Column: High-contrast Description & Live Badge */}
          <div className="lg:col-span-5 space-y-4 lg:pl-6">
            <p className="text-sm sm:text-base text-gray-600 dark:text-[#D4D4D8] leading-relaxed font-normal">
              Every campaign is executed with measurable integrity. Creators earn for legitimate attention, while brands receive verifiable distribution with complete telemetry transparency.
            </p>
            <div className="flex items-center gap-2 text-xs font-mono text-emerald-600 dark:text-emerald-400">
              <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
              Multi-Tier Heuristics & View Auditing Active
            </div>
          </div>

        </div>

        {/* 4 Core Principles with Crisp Backdrop Cards (Section 17) */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
          {principles.map((p, idx) => {
            const Icon = p.icon;
            return (
              <div 
                key={idx}
                className="p-7 rounded-2xl bg-gray-50/90 dark:bg-[#0B0B10]/90 backdrop-blur-xl border border-gray-200 dark:border-white/[0.12] hover:border-violet-500/40 hover:bg-white dark:hover:bg-[#121218]/95 transition-all duration-300 space-y-3 shadow-sm dark:shadow-xl group"
              >
                <div className="w-11 h-11 rounded-xl bg-white dark:bg-white/[0.06] border border-gray-200 dark:border-white/10 flex items-center justify-center group-hover:scale-105 transition-transform shadow-sm">
                  <Icon className={`w-5 h-5 ${p.color}`} />
                </div>
                <h3 className="text-base font-display font-bold text-gray-900 dark:text-[#F7F7F5]">
                  {p.title}
                </h3>
                <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
                  {p.desc}
                </p>
              </div>
            );
          })}
        </div>

        {/* Visual Explanation Flow Architecture (Section 18) */}
        <div className="p-8 sm:p-10 rounded-2xl bg-gray-50/90 dark:bg-[#0B0B10]/90 backdrop-blur-xl border border-gray-200 dark:border-white/[0.12] shadow-sm dark:shadow-2xl relative overflow-hidden">
          <div className="text-center mb-8">
            <span className="text-[11px] font-mono uppercase tracking-widest text-violet-600 dark:text-violet-400 font-semibold block">
              Marketplace Mechanics Pipeline
            </span>
            <h3 className="text-xl sm:text-2xl font-display font-bold text-gray-900 dark:text-white mt-1">
              The GEZUX Engine
            </h3>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-6 gap-4 relative z-10 text-center">
            
            {/* Step 1 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-violet-500/30 transition-all flex flex-col items-center justify-center shadow-sm">
              <div className="w-10 h-10 rounded-lg bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center mb-2.5 text-violet-700 dark:text-violet-400">
                <Video className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-gray-900 dark:text-white block">LONG VIDEO</span>
              <span className="text-[10px] text-gray-500 dark:text-[#A1A1AA] mt-0.5">Asset Uploaded</span>
            </div>

            {/* Step 2 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-indigo-500/30 transition-all flex flex-col items-center justify-center shadow-sm">
              <div className="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-950/80 border border-indigo-200 dark:border-indigo-500/30 flex items-center justify-center mb-2.5 text-indigo-700 dark:text-indigo-400">
                <Users className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-gray-900 dark:text-white block">CREATORS</span>
              <span className="text-[10px] text-gray-500 dark:text-[#A1A1AA] mt-0.5">Claim Campaign</span>
            </div>

            {/* Step 3 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-purple-500/30 transition-all flex flex-col items-center justify-center shadow-sm">
              <div className="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950/80 border border-purple-200 dark:border-purple-500/30 flex items-center justify-center mb-2.5 text-purple-700 dark:text-purple-400">
                <Scissors className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-gray-900 dark:text-white block">SHORT CLIPS</span>
              <span className="text-[10px] text-gray-500 dark:text-[#A1A1AA] mt-0.5">9:16 Editing</span>
            </div>

            {/* Step 4 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-blue-500/30 transition-all flex flex-col items-center justify-center shadow-sm">
              <div className="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-500/30 flex items-center justify-center mb-2.5 text-blue-700 dark:text-blue-400">
                <Share2 className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-gray-900 dark:text-white block">SOCIALS</span>
              <span className="text-[10px] text-gray-500 dark:text-[#A1A1AA] mt-0.5">Multi-Platform</span>
            </div>

            {/* Step 5 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/[0.08] hover:border-cyan-500/30 transition-all flex flex-col items-center justify-center shadow-sm">
              <div className="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-950/80 border border-cyan-200 dark:border-cyan-500/30 flex items-center justify-center mb-2.5 text-cyan-700 dark:text-cyan-400">
                <Eye className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-gray-900 dark:text-white block">VERIFIED VIEWS</span>
              <span className="text-[10px] text-gray-500 dark:text-[#A1A1AA] mt-0.5">Audited Analytics</span>
            </div>

            {/* Step 6 */}
            <div className="p-4 rounded-xl bg-white dark:bg-[#121218] border border-emerald-500/40 flex flex-col items-center justify-center shadow-md">
              <div className="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-500/30 flex items-center justify-center mb-2.5 text-emerald-700 dark:text-emerald-400">
                <Award className="w-5 h-5" />
              </div>
              <span className="text-xs font-mono font-bold text-emerald-700 dark:text-emerald-300 block">REWARDS</span>
              <span className="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5 font-medium">Direct Payouts</span>
            </div>

          </div>

          <div className="mt-6 text-center text-[11px] text-gray-500 dark:text-[#A1A1AA]">
            * All calculations and rewards are determined by verified performance criteria per individual campaign terms.
          </div>
        </div>

      </div>
    </section>
  );
};
