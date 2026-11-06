import React from 'react';
import { Video, ArrowRight, DollarSign, Zap, CheckCircle2, TrendingUp, ShieldCheck } from 'lucide-react';

interface ForClippersPageProps {
  onExploreCampaigns: () => void;
  onOpenAuth: () => void;
}

export const ForClippersPage: React.FC<ForClippersPageProps> = ({
  onExploreCampaigns,
  onOpenAuth
}) => {
  return (
    <div className="pt-32 pb-24 min-h-screen bg-gray-50 dark:bg-[#070708]">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
        
        {/* Hero */}
        <div className="text-center max-w-3xl mx-auto">
          <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-violet-100 dark:bg-violet-950/60 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300 text-xs font-mono font-semibold mb-6">
            <Video className="w-3.5 h-3.5" />
            FOR CREATORS, EDITORS & CLIP CHANNELS
          </div>

          <h1 className="text-4xl sm:text-6xl font-display font-extrabold text-gray-900 dark:text-[#F7F7F5] tracking-tight leading-tight">
            Turn Your Editing Skills <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 via-gray-900 to-pink-600 dark:from-violet-400 dark:via-white dark:to-pink-300">
              Into Scalable Income.
            </span>
          </h1>

          <p className="mt-6 text-base sm:text-lg text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
            Find pre-approved raw content from global podcasters and tech leaders. Cut high-retention 9:16 vertical clips, publish on your channels, and earn transparent bounties for every eligible view.
          </p>

          <div className="mt-8 flex flex-wrap items-center justify-center gap-4">
            <button
              onClick={onExploreCampaigns}
              className="px-8 py-3.5 bg-violet-600 hover:bg-violet-500 text-white font-semibold rounded-xl shadow-lg shadow-violet-900/20 transition-all flex items-center gap-2 cursor-pointer"
            >
              Explore Active Campaigns
              <ArrowRight className="w-4 h-4" />
            </button>
            <button
              onClick={onOpenAuth}
              className="px-8 py-3.5 bg-white dark:bg-[#121218] hover:bg-gray-100 dark:hover:bg-[#18181F] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-[#F7F7F5] font-semibold rounded-xl transition-all cursor-pointer shadow-sm"
            >
              Create Creator Account
            </button>
          </div>
        </div>

        {/* 4 Steps for Clippers */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {[
            { num: '01', title: 'Pick a Campaign', desc: 'Browse verified high-budget campaigns looking for clippers in your niche.' },
            { num: '02', title: 'Extract Hooks', desc: 'Cut 15–60s vertical clips with clean subtitles, sound effects, and zoom cuts.' },
            { num: '03', title: 'Publish on Socials', desc: 'Post to your TikTok, Shorts, Reels or X accounts with required campaign tags.' },
            { num: '04', title: 'Cash Out Earnings', desc: 'Track verified view telemetry and withdraw directly via Wise, Bank, Crypto or PayPal.' }
          ].map((s, idx) => (
            <div key={idx} className="p-6 rounded-2xl bg-white dark:bg-[#0B0B10] border border-gray-200 dark:border-white/[0.08] shadow-sm dark:shadow-xl space-y-3">
              <span className="text-xs font-mono font-bold px-2.5 py-1 rounded bg-violet-100 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 text-violet-700 dark:text-violet-300">
                STEP {s.num}
              </span>
              <h3 className="text-lg font-display font-bold text-gray-900 dark:text-white mt-2">{s.title}</h3>
              <p className="text-xs text-gray-600 dark:text-[#A1A1AA] leading-relaxed">{s.desc}</p>
            </div>
          ))}
        </div>

      </div>
    </div>
  );
};
