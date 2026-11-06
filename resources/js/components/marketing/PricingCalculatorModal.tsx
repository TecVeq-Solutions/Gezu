import React, { useState } from 'react';
import { X, Calculator, Info, ArrowRight, DollarSign, Eye, Globe } from 'lucide-react';

interface PricingCalculatorModalProps {
  isOpen: boolean;
  onClose: () => void;
  onLaunchCampaign: () => void;
}

export const PricingCalculatorModal: React.FC<PricingCalculatorModalProps> = ({
  isOpen,
  onClose,
  onLaunchCampaign
}) => {
  const [targetViews, setTargetViews] = useState<number>(500000);
  const [selectedCountry, setSelectedCountry] = useState<string>('US');
  const [rewardRate, setRewardRate] = useState<number>(1.00); // $ per 1,000 views

  if (!isOpen) return null;

  const countryMultipliers: Record<string, { name: string; rate: number }> = {
    US: { name: 'United States', rate: 1.00 },
    GB: { name: 'United Kingdom', rate: 0.95 },
    CA: { name: 'Canada', rate: 0.90 },
    AU: { name: 'Australia', rate: 0.90 },
    DE: { name: 'Germany', rate: 0.95 },
    AE: { name: 'UAE', rate: 1.10 },
    GLOBAL: { name: 'Global Tier 2/3', rate: 0.50 }
  };

  const effectiveRate = rewardRate * (countryMultipliers[selectedCountry]?.rate || 1);
  const estimatedBudget = (targetViews / 1000) * effectiveRate;
  const estimatedClipsNeeded = Math.ceil(targetViews / 35000); // Average 35k views per winning clip

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 dark:bg-black/80 backdrop-blur-md animate-in fade-in duration-200">
      <div className="relative w-full max-w-xl bg-white dark:bg-[#0B0B10] rounded-2xl border border-gray-200 dark:border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden text-gray-900 dark:text-white">
        
        {/* Glow ambient */}
        <div className="absolute top-0 right-0 w-48 h-48 bg-violet-600/10 blur-[80px] rounded-full pointer-events-none" />

        {/* Header */}
        <div className="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-white/[0.08]">
          <div className="flex items-center gap-2.5">
            <div className="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-950/80 border border-violet-200 dark:border-violet-500/30 flex items-center justify-center text-violet-600 dark:text-violet-400">
              <Calculator className="w-4 h-4" />
            </div>
            <div>
              <h3 className="text-base font-display font-bold text-gray-950 dark:text-white">
                Campaign & Creator ROI Calculator
              </h3>
              <p className="text-xs text-gray-500 dark:text-[#A1A1AA]">
                Simulate view targets and estimated campaign funding pools
              </p>
            </div>
          </div>
          <button 
            onClick={onClose}
            className="p-1.5 text-gray-400 hover:text-gray-900 dark:text-[#A1A1AA] dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Inputs */}
        <div className="mt-6 space-y-5">
          
          {/* Target Views Slider */}
          <div>
            <div className="flex items-center justify-between text-xs mb-2 font-mono">
              <span className="text-gray-700 dark:text-[#D4D4D8]">Target Eligible Views:</span>
              <span className="text-gray-900 dark:text-white font-bold text-sm">{targetViews.toLocaleString()} views</span>
            </div>
            <input 
              type="range" 
              min="50000" 
              max="10000000" 
              step="50000"
              value={targetViews}
              onChange={(e) => setTargetViews(Number(e.target.value))}
              className="w-full accent-violet-500 bg-gray-200 dark:bg-white/10 h-2 rounded-lg cursor-pointer"
            />
            <div className="flex justify-between text-[10px] text-gray-400 dark:text-[#71717A] font-mono mt-1">
              <span>50K</span>
              <span>1M</span>
              <span>5M</span>
              <span>10M+</span>
            </div>
          </div>

          {/* Country Selection */}
          <div>
            <label className="text-xs text-gray-700 dark:text-[#D4D4D8] font-mono block mb-1.5">
              Target Audience Geography:
            </label>
            <select 
              value={selectedCountry}
              onChange={(e) => setSelectedCountry(e.target.value)}
              className="w-full bg-gray-50 dark:bg-[#121218] border border-gray-300 dark:border-white/10 rounded-lg p-2.5 text-xs text-gray-900 dark:text-white focus:outline-none focus:border-violet-500"
            >
              {Object.entries(countryMultipliers).map(([code, item]) => (
                <option key={code} value={code} className="bg-white dark:bg-[#121218] text-gray-900 dark:text-white">
                  {item.name} ({code})
                </option>
              ))}
            </select>
          </div>

          {/* Reward Rate */}
          <div>
            <label className="text-xs text-gray-700 dark:text-[#D4D4D8] font-mono block mb-1.5">
              Campaign Reward Rate (per 1,000 Verified Views):
            </label>
            <div className="grid grid-cols-4 gap-2">
              {[0.75, 1.00, 1.25, 1.50].map((rate) => (
                <button
                  key={rate}
                  type="button"
                  onClick={() => setRewardRate(rate)}
                  className={`py-2 text-xs font-mono rounded-lg border transition-all ${rewardRate === rate ? 'bg-violet-600 border-violet-500 text-white font-bold' : 'bg-gray-50 dark:bg-[#121218] border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#A1A1AA] hover:text-gray-900 dark:hover:text-white'}`}
                >
                  ${rate.toFixed(2)}
                </button>
              ))}
            </div>
          </div>

          {/* Estimated Results Card */}
          <div className="p-4 rounded-xl bg-violet-50 dark:bg-violet-950/30 border border-violet-200 dark:border-violet-500/30 space-y-3">
            <div className="flex items-center justify-between text-xs">
              <span className="text-violet-900 dark:text-violet-200 font-mono">Estimated Campaign Budget:</span>
              <span className="text-xl font-bold font-mono text-violet-700 dark:text-white">
                ${estimatedBudget.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
              </span>
            </div>
            
            <div className="grid grid-cols-2 gap-2 pt-2 border-t border-violet-200 dark:border-violet-500/20 text-[11px]">
              <div className="text-violet-800 dark:text-violet-300">
                <span className="text-gray-500 dark:text-[#A1A1AA] block">Effective CPV:</span>
                <span className="font-mono font-bold">${(effectiveRate / 1000).toFixed(4)} / view</span>
              </div>
              <div className="text-violet-800 dark:text-violet-300">
                <span className="text-gray-500 dark:text-[#A1A1AA] block">Avg. Clip Submissions:</span>
                <span className="font-mono font-bold">~{estimatedClipsNeeded} creator clips</span>
              </div>
            </div>
          </div>

          {/* Qualified Disclaimer (Section 29) */}
          <div className="flex items-start gap-2 text-[10px] text-gray-500 dark:text-[#71717A] leading-normal">
            <Info className="w-3.5 h-3.5 shrink-0 mt-0.5 text-violet-600 dark:text-violet-400" />
            <span>
              This is an illustrative calculation. Actual campaign rates and rewards are determined by individual campaign rules, country weights, and verified eligible metrics.
            </span>
          </div>

        </div>

        {/* Footer Actions */}
        <div className="mt-6 pt-4 border-t border-gray-100 dark:border-white/[0.08] flex items-center justify-end gap-3">
          <button
            onClick={onClose}
            className="px-4 py-2 text-xs font-medium text-gray-600 hover:text-gray-900 dark:text-[#A1A1AA] dark:hover:text-white transition-colors cursor-pointer"
          >
            Close
          </button>
          <button
            onClick={() => {
              onClose();
              onLaunchCampaign();
            }}
            className="px-5 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-500 rounded-lg shadow-lg shadow-violet-800/30 transition-all flex items-center gap-1.5 cursor-pointer"
          >
            Launch with this Budget
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>

      </div>
    </div>
  );
};
