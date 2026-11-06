import React from 'react';
import { Activity, Globe, Film, Flame } from 'lucide-react';

export const ActivityBar: React.FC = () => {
  return (
    <div className="border-y border-black/[0.08] dark:border-white/[0.08] bg-white/80 dark:bg-[#0B0B10]/70 backdrop-blur-md py-3.5">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-4 text-xs font-mono">
        
        {/* Live status badge */}
        <div className="flex items-center gap-2">
          <span className="relative flex h-2 w-2">
            <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span className="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
          </span>
          <span className="font-bold text-gray-900 dark:text-[#F7F7F5] uppercase tracking-wider">
            Live Network Activity
          </span>
        </div>

        {/* Activity items */}
        <div className="flex flex-wrap items-center gap-6 sm:gap-10 text-gray-700 dark:text-[#D4D4D8]">
          <div className="flex items-center gap-2">
            <Flame className="w-4 h-4 text-amber-500 dark:text-amber-400" />
            <span><strong className="text-gray-900 dark:text-white font-bold">14</strong> campaigns active</span>
          </div>

          <div className="flex items-center gap-2">
            <Film className="w-4 h-4 text-violet-500 dark:text-violet-400" />
            <span><strong className="text-gray-900 dark:text-white font-bold">37</strong> clips submitted recently</span>
          </div>

          <div className="flex items-center gap-2">
            <Globe className="w-4 h-4 text-blue-500 dark:text-blue-400" />
            <span><strong className="text-gray-900 dark:text-white font-bold">8</strong> countries generating views</span>
          </div>
        </div>

        {/* Status remark */}
        <div className="hidden lg:block text-gray-500 dark:text-[#71717A] text-[11px]">
          Demo telemetry verified
        </div>

      </div>
    </div>
  );
};
