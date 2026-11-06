import React, { useState } from 'react';
import { Smartphone, Video, Radio, TrendingUp, CheckCircle2 } from 'lucide-react';

interface ChannelSegment {
  name: string;
  percentage: number;
  views: number;
  color: string;
  glowColor: string;
  icon?: any;
}

interface ChannelDistributionDonutProps {
  title?: string;
  subtitle?: string;
  segments?: ChannelSegment[];
  totalViews?: number;
}

export const ChannelDistributionDonut: React.FC<ChannelDistributionDonutProps> = ({
  title = 'Multi-Platform Video Telemetry',
  subtitle = 'Audited organic reach distribution by social network',
  totalViews = 2450000,
  segments = [
    { name: 'TikTok', percentage: 54, views: 1323000, color: '#EC4899', glowColor: 'rgba(236, 72, 153, 0.4)' },
    { name: 'YouTube Shorts', percentage: 28, views: 686000, color: '#EF4444', glowColor: 'rgba(239, 68, 68, 0.4)' },
    { name: 'Instagram Reels', percentage: 18, views: 441000, color: '#A855F7', glowColor: 'rgba(168, 85, 247, 0.4)' }
  ]
}) => {
  const [hoveredSegment, setHoveredSegment] = useState<ChannelSegment | null>(null);

  const radius = 68;
  const strokeWidth = 14;
  const circumference = 2 * Math.PI * radius;
  let accumulatedOffset = 0;

  return (
    <div className="p-5 sm:p-6 rounded-2xl bg-gradient-to-b from-[#121218]/95 to-[#0B0B10]/95 border border-white/[0.08] hover:border-white/15 transition-all backdrop-blur-xl shadow-2xl space-y-6">
      
      {/* Header */}
      <div className="flex items-center justify-between pb-3 border-b border-white/[0.06]">
        <div>
          <h3 className="text-base font-display font-bold text-white tracking-tight">{title}</h3>
          <p className="text-xs text-[#A1A1AA] mt-0.5">{subtitle}</p>
        </div>
        <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 font-bold">
          ● VERIFIED
        </span>
      </div>

      {/* Donut & Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center">
        
        {/* Left: Interactive Donut SVG */}
        <div className="sm:col-span-5 flex items-center justify-center relative">
          <svg width="180" height="180" viewBox="0 0 180 180" className="rotate-[-90deg] overflow-visible">
            {/* Background track */}
            <circle
              cx="90"
              cy="90"
              r={radius}
              fill="transparent"
              stroke="rgba(255,255,255,0.05)"
              strokeWidth={strokeWidth}
            />

            {/* Colored Segments */}
            {segments.map((seg, idx) => {
              const dashLength = (seg.percentage / 100) * circumference;
              const strokeDasharray = `${dashLength} ${circumference - dashLength}`;
              const strokeDashoffset = -accumulatedOffset;
              accumulatedOffset += dashLength;

              const isHovered = hoveredSegment?.name === seg.name;

              return (
                <circle
                  key={idx}
                  cx="90"
                  cy="90"
                  r={radius}
                  fill="transparent"
                  stroke={seg.color}
                  strokeWidth={isHovered ? strokeWidth + 4 : strokeWidth}
                  strokeDasharray={strokeDasharray}
                  strokeDashoffset={strokeDashoffset}
                  strokeLinecap="round"
                  className="transition-all duration-300 cursor-pointer"
                  onMouseEnter={() => setHoveredSegment(seg)}
                  onMouseLeave={() => setHoveredSegment(null)}
                />
              );
            })}
          </svg>

          {/* Center Info */}
          <div className="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#71717A]">
              {hoveredSegment ? hoveredSegment.name : 'Total Audited'}
            </span>
            <span className="text-lg font-bold font-mono text-white">
              {hoveredSegment ? `${hoveredSegment.percentage}%` : `${(totalViews / 1000000).toFixed(2)}M`}
            </span>
            <span className="text-[10px] font-mono text-emerald-400">
              {hoveredSegment ? `${(hoveredSegment.views / 1000).toFixed(0)}K Views` : 'Views Tracked'}
            </span>
          </div>
        </div>

        {/* Right: Detailed Channel Breakdown Rows */}
        <div className="sm:col-span-7 space-y-3">
          {segments.map((seg) => {
            const isHovered = hoveredSegment?.name === seg.name;
            return (
              <div
                key={seg.name}
                onMouseEnter={() => setHoveredSegment(seg)}
                onMouseLeave={() => setHoveredSegment(null)}
                className={`p-3 rounded-xl border transition-all cursor-pointer ${
                  isHovered 
                    ? 'bg-white/[0.08] border-white/20 shadow-md' 
                    : 'bg-white/[0.03] border-white/[0.06] hover:bg-white/[0.05]'
                }`}
              >
                <div className="flex items-center justify-between text-xs font-mono mb-1.5">
                  <span className="text-white font-bold flex items-center gap-2">
                    <span 
                      className="w-2.5 h-2.5 rounded-full" 
                      style={{ backgroundColor: seg.color }}
                    />
                    {seg.name}
                  </span>
                  <span className="text-white font-bold">{seg.percentage}%</span>
                </div>

                <div className="flex items-center justify-between text-[11px] text-[#A1A1AA] font-mono">
                  <span>{seg.views.toLocaleString()} verified views</span>
                  <span className="text-emerald-400 font-semibold">Audited</span>
                </div>

                {/* Progress bar */}
                <div className="w-full h-1.5 rounded-full bg-white/10 overflow-hidden mt-2">
                  <div 
                    className="h-full rounded-full transition-all duration-500" 
                    style={{ width: `${seg.percentage}%`, backgroundColor: seg.color }} 
                  />
                </div>
              </div>
            );
          })}
        </div>

      </div>

    </div>
  );
};
