<div x-show="submitClipOpen" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/75 backdrop-blur-md"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-lg bg-[#0B0B10] rounded-2xl border border-white/15 p-6 sm:p-8 shadow-2xl overflow-hidden text-white"
         @click.away="submitClipOpen = false">

        <!-- Glow ambient -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-violet-600/15 blur-[80px] rounded-full pointer-events-none"></div>

        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-white/[0.08]">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-violet-950/80 border border-violet-500/30 flex items-center justify-center text-violet-400">
                    <i data-lucide="video" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-base font-display font-bold text-white">
                        Submit Published Clip
                    </h3>
                    <p class="text-xs text-[#A1A1AA]">
                        Connect your live short-form video for automatic audit & reward tracking
                    </p>
                </div>
            </div>
            <button @click="submitClipOpen = false" class="p-1.5 text-[#A1A1AA] hover:text-white rounded-lg hover:bg-white/5 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ url('/clips/submit') }}" class="space-y-4 pt-5">
            @csrf

            <!-- Target Campaign Banner -->
            <div class="p-3.5 rounded-xl bg-[#121218] border border-white/10 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-[#71717A] uppercase tracking-wider block">Target Campaign</span>
                    <span class="text-xs font-semibold text-white" x-text="selectedCampaignTitle || 'Select Campaign'"></span>
                </div>
                <input type="hidden" name="campaign_id" :value="selectedCampaignId">
                <span class="text-xs font-mono font-bold text-emerald-400" x-text="'$' + selectedCampaignRate + ' / 1k views'"></span>
            </div>

            <!-- Platform Selector -->
            <div>
                <label class="block text-xs font-medium text-[#D4D4D8] mb-1.5">Distribution Channel</label>
                <div class="grid grid-cols-4 gap-2" x-data="{ platform: 'TikTok' }">
                    <input type="hidden" name="platform" :value="platform">
                    <button type="button" @click="platform = 'TikTok'"
                            :class="platform === 'TikTok' ? 'border-violet-500 bg-violet-500/15 text-white' : 'border-white/10 bg-[#121218] text-[#A1A1AA] hover:text-white'"
                            class="px-3 py-2 rounded-xl border text-xs font-semibold text-center transition-all">
                        TikTok
                    </button>
                    <button type="button" @click="platform = 'Instagram'"
                            :class="platform === 'Instagram' ? 'border-violet-500 bg-violet-500/15 text-white' : 'border-white/10 bg-[#121218] text-[#A1A1AA] hover:text-white'"
                            class="px-3 py-2 rounded-xl border text-xs font-semibold text-center transition-all">
                        Reels
                    </button>
                    <button type="button" @click="platform = 'YouTube'"
                            :class="platform === 'YouTube' ? 'border-violet-500 bg-violet-500/15 text-white' : 'border-white/10 bg-[#121218] text-[#A1A1AA] hover:text-white'"
                            class="px-3 py-2 rounded-xl border text-xs font-semibold text-center transition-all">
                        Shorts
                    </button>
                    <button type="button" @click="platform = 'X'"
                            :class="platform === 'X' ? 'border-violet-500 bg-violet-500/15 text-white' : 'border-white/10 bg-[#121218] text-[#A1A1AA] hover:text-white'"
                            class="px-3 py-2 rounded-xl border text-xs font-semibold text-center transition-all">
                        X Video
                    </button>
                </div>
            </div>

            <!-- Live Video Link -->
            <div>
                <label class="block text-xs font-medium text-[#D4D4D8] mb-1">Live Video Post URL</label>
                <div class="relative">
                    <i data-lucide="link" class="w-4 h-4 text-[#71717A] absolute left-3.5 top-3"></i>
                    <input type="url" name="post_url" required placeholder="https://www.tiktok.com/@creator/video/..."
                           class="w-full bg-[#121218] border border-white/10 rounded-xl pl-10 pr-3.5 py-2.5 text-xs text-white placeholder-[#71717A] focus:outline-none focus:border-violet-500 font-mono">
                </div>
                <span class="text-[10px] text-[#71717A] mt-1 block">Ensure the video contains official brand hashtags and is publicly accessible.</span>
            </div>

            <!-- Clip Notes / Angle -->
            <div>
                <label class="block text-xs font-medium text-[#D4D4D8] mb-1">Hook / Angle (Optional)</label>
                <input type="text" name="clip_title" placeholder="e.g. 0:42 Autonomous Drone Discussion Cut"
                       class="w-full bg-[#121218] border border-white/10 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-[#71717A] focus:outline-none focus:border-violet-500">
            </div>

            <!-- Rules Confirmation -->
            <div class="pt-2 flex items-start gap-2.5">
                <input type="checkbox" id="rulesCheck" required
                       class="mt-0.5 rounded border-white/20 bg-[#121218] text-violet-600 focus:ring-0">
                <label for="rulesCheck" class="text-xs text-[#A1A1AA] leading-relaxed cursor-pointer select-none">
                    I confirm this video was published by my verified account and complies with the campaign's guidelines and anti-bot verification rules.
                </label>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-white/[0.08]">
                <button type="button" @click="submitClipOpen = false"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-[#A1A1AA] hover:text-white hover:bg-white/5 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 shadow-lg shadow-violet-900/30 flex items-center gap-1.5 transition-all">
                    <span>Submit for Audit</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </button>
            </div>

        </form>

    </div>
</div>
