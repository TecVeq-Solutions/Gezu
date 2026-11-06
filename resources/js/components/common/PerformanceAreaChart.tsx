import React, { useState } from 'react';
import { TrendingUp, Sparkles, Calendar, Layers, Eye, DollarSign } from 'lucide-react';

interface DataPoint {
  date: string;
  value: number;
  secondaryValue?: number;
  label?: string;
}

interface PerformanceAreaChartProps {
  title: string;
  subtitle?: string;
  data: DataPoint[];
  metricPrefix?: string;
  metricSuffix?: string;
  colorScheme?: 'violet' | 'emerald' | 'blue' | 'cyan';
  timeframe?: string;
  onTimeframeChange?: (timeframe: string) => void;
  height?: number;
  showControls?: boolean;
}

export const PerformanceAreaChart: React.FC<PerformanceAreaChartProps> = ({
  title,
  subtitle = 'Telemetry aggregated across all distributed nodes',
  data,
  metricPrefix = '',
  metricSuffix = '',
  colorScheme = 'violet',
  timeframe = '30D',
  onTimeframeChange,
  height = 240,
  showControls = true
}) => {
  const [selectedPointIndex, setSelectedPointIndex] = useState<number | null>(null);
  const [activeTimeframe, setActiveTimeframe] = useState<string>(timeframe);

  const colors = {
    violet: {
      stroke: '#8B5CF6',
      stop1: 'rgba(139, 92, 246, 0.45)',
      stop2: 'rgba(139, 92, 246, 0.02)',
      dot: '#A855F7',
      glow: 'rgba(139, 92, 246, 0.3)',
      text: 'text-violet-400',
      badge: 'bg-violet-500/15 border-violet-500/30 text-violet-300'
    },
    emerald: {
      stroke: '#22C55E',
      stop1: 'rgba(34, 197, 94, 0.45)',
      stop2: 'rgba(34, 197, 94, 0.02)',
      dot: '#4ADE80',
      glow: 'rgba(34, 197, 94, 0.3)',
      text: 'text-emerald-400',
      badge: 'bg-emerald-500/15 border-emerald-500/30 text-emerald-300'
    },
    blue: {
      stroke: '#3B82F6',
      stop1: 'rgba(59, 130, 246, 0.45)',
      stop2: 'rgba(59, 130, 246, 0.02)',
      dot: '#60A5FA',
      glow: 'rgba(59, 130, 246, 0.3)',
      text: 'text-blue-400',
      badge: 'bg-blue-500/15 border-blue-500/30 text-blue-300'
    },
    cyan: {
      stroke: '#06B6D4',
      stop1: 'rgba(6, 182, 212, 0.45)',
      stop2: 'rgba(6, 182, 212, 0.02)',
      dot: '#22D3EE',
      glow: 'rgba(6, 182, 212, 0.3)',
      text: 'text-cyan-400',
      badge: 'bg-cyan-500/15 border-cyan-500/30 text-cyan-300'
    }
  }[colorScheme];

  if (!data || data.length === 0) {
    return (
      <div className="p-6 rounded-2xl bg-[#0B0B10]/90 border border-white/[0.08] text-center text-xs text-[#A1A1AA]">
        No telemetry data recorded yet.
      </div>
    );
  }

  const values = data.map(d => d.value);
  const maxValue = Math.max(...values, 1);
  const minValue = Math.min(...values, 0);
  const range = maxValue - minValue || 1;

  const width = 800;
  const paddingX = 40;
  const paddingY = 30;
  const chartWidth = width - paddingX * 2;
  const chartHeight = height - paddingY * 2;

  // Calculate coordinates
  const points = data.map((d, index) => {
    const x = paddingX + (index / (data.length - 1 || 1)) * chartWidth;
    const y = height - paddingY - ((d.value - minValue) / range) * chartHeight;
    return { x, y, ...d };
  });

  // Construct smooth cubic bezier SVG path
  let pathD = `M ${points[0].x} ${points[0].y}`;
  for (let i = 0; i < points.length - 1; i++) {
    const curr = points[i];
    const next = points[i + 1];
    const cpX1 = curr.x + (next.x - curr.x) / 2;
    const cpY1 = curr.y;
    const cpX2 = curr.x + (next.x - curr.x) / 2;
    const cpY2 = next.y;
    pathD += ` C ${cpX1} ${cpY1}, ${cpX2} ${cpY2}, ${next.x} ${next.y}`;
  }

  const areaD = `${pathD} L ${points[points.length - 1].x} ${height - paddingY} L ${points[0].x} ${height - paddingY} Z`;

  const activePoint = selectedPointIndex !== null ? points[selectedPointIndex] : points[points.length - 1];

  const handleTimeframeClick = (tf: string) => {
    setActiveTimeframe(tf);
    if (onTimeframeChange) onTimeframeChange(tf);
  };

  return (
    <div className="p-5 sm:p-6 rounded-2xl bg-gradient-to-b from-[#121218]/95 to-[#0B0B10]/95 border border-white/[0.08] hover:border-white/15 transition-all backdrop-blur-xl shadow-2xl relative overflow-hidden group">
      
      {/* Chart Topbar Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-white/[0.06]">
        <div>
          <div className="flex items-center gap-2">
            <h3 className="text-base font-display font-bold text-white tracking-tight">{title}</h3>
            <span className={`text-[10px] font-mono px-2 py-0.5 rounded-full font-bold border ${colors.badge}`}>
              ● LIVE TELEMETRY
            </span>
          </div>
          <p className="text-xs text-[#A1A1AA] mt-0.5">{subtitle}</p>
        </div>

        <div className="flex flex-wrap items-center gap-3">
          {/* Active Highlight Badge */}
          <div className="px-3 py-1.5 rounded-xl bg-white/[0.04] border border-white/[0.08] font-mono flex items-center gap-2 text-xs">
            <span className="text-[#71717A] text-[11px]">{activePoint.date}:</span>
            <span className="font-bold text-white text-sm">
              {metricPrefix}{activePoint.value.toLocaleString()}{metricSuffix}
            </span>
          </div>

          {/* Timeframe Selectors */}
          {showControls && (
            <div className="flex items-center gap-1 bg-[#070708] p-1 rounded-xl border border-white/[0.08]">
              {['7D', '30D', '90D', 'ALL'].map(tf => (
                <button
                  key={tf}
                  onClick={() => handleTimeframeClick(tf)}
                  className={`px-2.5 py-1 text-[11px] font-mono font-bold rounded-lg transition-all ${
                    activeTimeframe === tf 
                      ? 'bg-white/15 text-white shadow-sm' 
                      : 'text-[#71717A] hover:text-white'
                  }`}
                >
                  {tf}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* SVG Canvas */}
      <div className="relative mt-4 w-full overflow-hidden select-none">
        <svg
          viewBox={`0 0 ${width} ${height}`}
          className="w-full h-auto overflow-visible"
          style={{ minHeight: `${height}px` }}
        >
          <defs>
            <linearGradient id={`gradient-${colorScheme}`} x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stopColor={colors.stop1} />
              <stop offset="100%" stopColor={colors.stop2} />
            </linearGradient>
            <filter id="glow-filter" x="-20%" y="-20%" width="140%" height="140%">
              <feGaussianBlur stdDeviation="4" result="blur" />
              <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
          </defs>

          {/* Grid lines */}
          <line x1={paddingX} y1={paddingY} x2={width - paddingX} y2={paddingY} stroke="rgba(255,255,255,0.04)" strokeDasharray="4 4" />
          <line x1={paddingX} y1={paddingY + chartHeight / 2} x2={width - paddingX} y2={paddingY + chartHeight / 2} stroke="rgba(255,255,255,0.04)" strokeDasharray="4 4" />
          <line x1={paddingX} y1={height - paddingY} x2={width - paddingX} y2={height - paddingY} stroke="rgba(255,255,255,0.08)" />

          {/* Area Fill */}
          <path d={areaD} fill={`url(#gradient-${colorScheme})`} />

          {/* Stroke Curve */}
          <path
            d={pathD}
            fill="none"
            stroke={colors.stroke}
            strokeWidth="3"
            strokeLinecap="round"
            strokeLinejoin="round"
            filter="url(#glow-filter)"
          />

          {/* Data Points and Interactivity */}
          {points.map((pt, i) => {
            const isHovered = selectedPointIndex === i || (selectedPointIndex === null && i === points.length - 1);
            return (
              <g 
                key={i} 
                className="cursor-pointer" 
                onMouseEnter={() => setSelectedPointIndex(i)}
                onMouseLeave={() => setSelectedPointIndex(null)}
              >
                {/* Hit area */}
                <circle cx={pt.x} cy={pt.y} r="18" fill="transparent" />

                {/* Visible dot */}
                <circle
                  cx={pt.x}
                  cy={pt.y}
                  r={isHovered ? 6 : 3.5}
                  fill={isHovered ? colors.dot : '#0B0B10'}
                  stroke={colors.stroke}
                  strokeWidth={isHovered ? 3 : 2}
                  className="transition-all duration-200"
                />

                {/* Vertical Guideline on Hover */}
                {isHovered && (
                  <line
                    x1={pt.x}
                    y1={paddingY}
                    x2={pt.x}
                    y2={height - paddingY}
                    stroke="rgba(255,255,255,0.2)"
                    strokeDasharray="3 3"
                  />
                )}
              </g>
            );
          })}
        </svg>

        {/* Bottom X-Axis Labels */}
        <div className="flex justify-between items-center text-[10px] font-mono text-[#71717A] px-2 pt-2 border-t border-white/[0.04]">
          {data.filter((_, idx) => idx === 0 || idx === Math.floor(data.length / 2) || idx === data.length - 1).map((d, i) => (
            <span key={i}>{d.date}</span>
          ))}
        </div>
      </div>

    </div>
  );
};
