<footer class="bg-gray-50 dark:bg-[#070708] border-t border-black/[0.08] dark:border-white/[0.08] pt-20 pb-14 text-sm text-gray-600 dark:text-[#A1A1AA]">
    <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 w-full">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-10 lg:gap-14 mb-16">
            
            <!-- Brand Column -->
            <div class="col-span-2 space-y-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3.5 cursor-pointer select-none">
                    <div class="w-10 h-10 rounded-xl bg-black border border-white/20 flex items-center justify-center p-2 shadow-sm">
                        <img alt="GEZUX Logo" class="w-full h-full object-contain invert" src="/images/gezux-favicon.jpg">
                    </div>
                    <span class="font-display font-bold text-2xl tracking-wider text-gray-900 dark:text-white">GEZUX</span>
                </a>
                <p class="text-sm text-gray-600 dark:text-[#A1A1AA] max-w-md leading-relaxed">
                    The global performance marketplace for short-form video distribution. Content is the asset, clippers are the distribution network, and verified views are the measurable outcome.
                </p>
                <div class="flex flex-wrap items-center gap-2.5 pt-2">
                    <span class="px-3 py-1.5 rounded-lg bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-xs font-mono font-medium text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs">TikTok</span>
                    <span class="px-3 py-1.5 rounded-lg bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-xs font-mono font-medium text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs">Instagram</span>
                    <span class="px-3 py-1.5 rounded-lg bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-xs font-mono font-medium text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs">YouTube Shorts</span>
                    <span class="px-3 py-1.5 rounded-lg bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-xs font-mono font-medium text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs">X</span>
                    <span class="px-3 py-1.5 rounded-lg bg-white dark:bg-white/[0.04] border border-gray-200 dark:border-white/10 text-xs font-mono font-medium text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white hover:border-violet-500/40 cursor-pointer transition-colors shadow-xs">LinkedIn</span>
                </div>
            </div>

            <!-- Platform Links -->
            <div class="space-y-4">
                <span class="text-xs font-mono uppercase tracking-widest text-gray-950 dark:text-[#F7F7F5] font-bold block">Platform</span>
                <ul class="space-y-3">
                    <li><a href="{{ url('/marketplace') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Campaigns</a></li>
                    <li><a href="{{ url('/#how-it-works') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">How It Works</a></li>
                    <li><a href="{{ url('/brand') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">For Brands</a></li>
                    <li><a href="{{ url('/clipper') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">For Clippers</a></li>
                </ul>
            </div>

            <!-- Resources Links -->
            <div class="space-y-4">
                <span class="text-xs font-mono uppercase tracking-widest text-gray-950 dark:text-[#F7F7F5] font-bold block">Resources</span>
                <ul class="space-y-3">
                    @php $quickLinks = getCmsMenu('footer_quick'); @endphp
                    @if($quickLinks->isNotEmpty())
                        @foreach($quickLinks as $qLink)
                            <li><a href="{{ url($qLink->url) }}" target="{{ $qLink->target }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">{{ $qLink->title }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ route('blog.index') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Official Blog</a></li>
                        <li><a href="{{ url('/clipper') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Creator Guidelines</a></li>
                        <li><a href="{{ url('/brand') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Campaign Guidelines</a></li>
                        <li><button type="button" @click="calculatorOpen = true" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-left text-sm font-medium">Help &amp; FAQs</button></li>
                    @endif
                </ul>
            </div>

            <!-- Legal Links -->
            <div class="space-y-4">
                <span class="text-xs font-mono uppercase tracking-widest text-gray-950 dark:text-[#F7F7F5] font-bold block">Legal &amp; Policies</span>
                <ul class="space-y-3">
                    @php $legalLinks = getCmsMenu('footer_legal'); @endphp
                    @if($legalLinks->isNotEmpty())
                        @foreach($legalLinks as $lLink)
                            <li><a href="{{ url($lLink->url) }}" target="{{ $lLink->target }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">{{ $lLink->title }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ url('/p/terms-of-service') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Terms of Service</a></li>
                        <li><a href="{{ url('/p/privacy-policy') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Privacy Policy</a></li>
                        <li><a href="{{ url('/p/clipper-agreement') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Clipper Agreement</a></li>
                        <li><a href="{{ url('/p/brand-guidelines') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors cursor-pointer text-sm font-medium">Brand Guidelines</a></li>
                    @endif
                </ul>
            </div>

        </div>

        <div class="pt-8 border-t border-gray-200 dark:border-white/[0.08] flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500 dark:text-[#71717A]">
            <div>
                © 2026 GEZUX. All rights reserved.
            </div>
            <div class="font-mono text-xs text-right">Reward rates are determined by campaign rules &amp; eligible verified views.</div>
        </div>

    </div>
</footer>
