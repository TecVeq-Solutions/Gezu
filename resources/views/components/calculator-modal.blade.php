<div x-show="calculatorOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-xl bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden text-white"
         @click.away="calculatorOpen = false"
         x-data="{
             targetViews: 500000,
             selectedCountry: 'US',
             rewardRate: 1.00,
             countryRates: {
                 'US': { name: 'United States', mult: 1.00 },
                 'GB': { name: 'United Kingdom', mult: 0.95 },
                 'CA': { name: 'Canada', mult: 0.90 },
                 'AU': { name: 'Australia', mult: 0.90 },
                 'DE': { name: 'Germany', mult: 0.95 },
                 'AE': { name: 'UAE', mult: 1.10 },
                 'GLOBAL': { name: 'Global Tier 2/3', mult: 0.50 }
             },
             get effectiveRate() {
                 return (this.rewardRate * (this.countryRates[this.selectedCountry]?.mult || 1)).toFixed(2);
             },
             get estimatedBudget() {
                 return ((this.targetViews / 1000) * this.effectiveRate).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
             },
             get estimatedClips() {
                 return Math.ceil(this.targetViews / 35000);
             }
         }">

        <!-- Glow ambient -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-violet-600/15 blur-[80px] rounded-full pointer-events-none"></div>

        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-white/[0.08]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-950/80 border border-violet-500/30 flex items-center justify-center text-violet-400">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-display font-bold text-white">
                        Campaign & Creator ROI Calculator
                    </h3>
                    <p class="text-xs text-[#A1A1AA]">
                        Simulate view targets and estimated performance funding pools
                    </p>
                </div>
            </div>
            <button @click="calculatorOpen = false" class="p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Body Inputs -->
        <div class="space-y-6 pt-6">
            
            <!-- Target Views Slider -->
            <div>
                <div class="flex justify-between items-center text-xs mb-2">
                    <span class="text-[#D4D4D8] font-medium flex items-center gap-1.5">
                        <i data-lucide="eye" class="w-3.5 h-3.5 text-violet-400"></i>
                        Target Guaranteed Views
                    </span>
                    <span class="font-mono font-bold text-violet-400 text-sm" x-text="Number(targetViews).toLocaleString() + ' Views'"></span>
                </div>
                <input type="range" min="50000" max="5000000" step="50000" x-model="targetViews"
                       class="w-full h-1.5 bg-white/10 rounded-lg appearance-none cursor-pointer accent-violet-600">
                <div class="flex justify-between text-[10px] font-mono text-[#71717A] mt-1">
                    <span>50K</span>
                    <span>1M</span>
                    <span>2.5M</span>
                    <span>5M</span>
                </div>
            </div>

            <!-- Country Selection -->
            <div>
                <label class="block text-xs font-medium text-[#D4D4D8] mb-1.5 flex items-center gap-1.5">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-blue-400"></i>
                    Primary Distribution Geography
                </label>
                <select x-model="selectedCountry"
                        class="w-full bg-[#121218] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-violet-500 font-mono">
                    <template x-for="(val, code) in countryRates" :key="code">
                        <option :value="code" x-text="val.name + ' (' + (val.mult * 100) + '% tier)'"></option>
                    </template>
                </select>
            </div>

            <!-- Reward Rate per 1000 Views -->
            <div>
                <div class="flex justify-between items-center text-xs mb-2">
                    <span class="text-[#D4D4D8] font-medium flex items-center gap-1.5">
                        <i data-lucide="dollar-sign" class="w-3.5 h-3.5 text-emerald-400"></i>
                        Base Bounty Rate (per 1,000 views)
                    </span>
                    <span class="font-mono font-bold text-emerald-400" x-text="'$' + Number(rewardRate).toFixed(2) + ' / 1k views'"></span>
                </div>
                <input type="range" min="0.50" max="3.00" step="0.25" x-model="rewardRate"
                       class="w-full h-1.5 bg-white/10 rounded-lg appearance-none cursor-pointer accent-emerald-500">
            </div>

            <!-- Calculated Summary Card -->
            <div class="p-4 rounded-xl bg-gradient-to-br from-violet-950/40 via-[#121218] to-indigo-950/30 border border-violet-500/30 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-[#A1A1AA]">Estimated Campaign Pool</span>
                    <span class="text-xl font-display font-black text-white" x-text="'$' + estimatedBudget"></span>
                </div>
                <div class="flex justify-between items-center text-xs pt-2 border-t border-white/10 text-[#D4D4D8]">
                    <span>Expected High-Performance Clips</span>
                    <span class="font-mono font-bold text-violet-300" x-text="'~' + estimatedClips + ' edited clips'"></span>
                </div>
                <div class="flex justify-between items-center text-xs text-[#D4D4D8]">
                    <span>Average Cost per View (CPV)</span>
                    <span class="font-mono text-emerald-300" x-text="'$' + (effectiveRate / 1000).toFixed(4)"></span>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <div class="pt-6 mt-6 border-t border-white/[0.08] flex items-center justify-between gap-3">
            <span class="text-[11px] text-[#71717A]">
                100% Platform protected. Zero bot deductions.
            </span>
            <a href="{{ url('/brand') }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 flex items-center gap-1.5 shadow-lg shadow-violet-900/30 transition-all">
                <span>Fund Campaign</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

    </div>
</div>
