<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent py-4"
        x-data="{ scrolled: false, mobileOpen: false }"
        x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
        :class="scrolled ? 'bg-white/85 dark:bg-[#070708]/90 backdrop-blur-xl border-b border-black/[0.08] dark:border-white/[0.08] shadow-sm py-3' : 'bg-transparent py-4'">
    <div class="max-w-[1550px] mx-auto px-6 sm:px-8 lg:px-12 w-full">
        <div class="flex items-center justify-between gap-6">
            
            <!-- Left: Brand Identity -->
            <a href="{{ url('/') }}" class="flex items-center gap-3.5 cursor-pointer group select-none shrink-0">
                <div class="w-10 h-10 rounded-xl bg-black border border-white/20 flex items-center justify-center p-2 shadow-md transition-transform group-hover:scale-105">
                    <img src="/images/gezux-favicon.jpg" alt="GEZUX Logo" class="w-full h-full object-contain invert">
                </div>
                <span class="font-display font-bold text-2xl tracking-wider text-[#09090B] dark:text-[#F7F7F5]">
                    GEZUX
                </span>
            </a>

            <!-- Center: Navigation Links -->
            <nav class="hidden md:flex items-center gap-2 lg:gap-3 shrink-0">
                @php $customHeaderItems = getCmsMenu('header'); @endphp
                @if($customHeaderItems && $customHeaderItems->count() > 0)
                    @foreach($customHeaderItems as $item)
                        @php 
                            $isCurrent = request()->is(ltrim($item->url, '/')) || (request()->path() === '/' && ($item->url === '/' || $item->url === ''));
                        @endphp
                        <a href="{{ url($item->url) }}" target="{{ $item->target }}" 
                           class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer {{ $isCurrent ? 'text-black dark:text-white bg-black/[0.06] dark:bg-white/[0.08]' : 'text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white' }}">
                            {{ $item->title }}
                        </a>
                    @endforeach
                @else
                    <a href="{{ url('/') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer {{ request()->is('/') ? 'text-black dark:text-white bg-black/[0.06] dark:bg-white/[0.08]' : 'text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white' }}">
                        Overview
                    </a>
                    <a href="{{ url('/marketplace') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer {{ request()->is('marketplace*') ? 'text-black dark:text-white bg-black/[0.06] dark:bg-white/[0.08]' : 'text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white' }}">
                        Campaigns
                    </a>
                    <a href="{{ route('blog.index') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer {{ request()->is('blog*') ? 'text-black dark:text-white bg-black/[0.06] dark:bg-white/[0.08]' : 'text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white' }}">
                        Blog
                    </a>
                    <a href="{{ url('/brand') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white">
                        For Brands
                    </a>
                    <a href="{{ url('/clipper') }}" 
                       class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors cursor-pointer text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white">
                        For Clippers
                    </a>
                @endif
                <button type="button" @click="calculatorOpen = true"
                        class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors flex items-center gap-1.5 cursor-pointer text-gray-600 dark:text-[#A1A1AA] hover:text-black dark:hover:text-white">
                    <i data-lucide="calculator" class="w-4 h-4 text-violet-500"></i>
                    Calculator
                </button>
            </nav>

            <!-- Right: Actions & Tools -->
            <div class="hidden lg:flex items-center gap-3 shrink-0 whitespace-nowrap">
                
                <!-- Theme Toggle Button -->
                <button @click="toggleTheme()" :title="isDark ? 'Switch to Light Theme' : 'Switch to Dark Theme'"
                        class="p-2.5 rounded-xl border transition-all cursor-pointer bg-white dark:bg-[#121218] border-black/10 dark:border-white/10 text-violet-600 dark:text-violet-400 hover:bg-slate-50 dark:hover:bg-white/5 shadow-xs">
                    <i x-show="!isDark" data-lucide="moon" class="w-4 h-4"></i>
                    <i x-show="isDark" data-lucide="sun" class="w-4 h-4"></i>
                </button>

                @guest
                    <!-- Sign In -->
                    <a href="{{ url('/login') }}" class="px-4 py-2 text-sm font-semibold rounded-xl border transition-all flex items-center gap-1.5 cursor-pointer text-slate-800 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white bg-slate-100 dark:bg-white/[0.05] hover:bg-slate-200 dark:hover:bg-white/10 border-black/10 dark:border-white/10 tactile-btn">
                        <i data-lucide="log-in" class="w-4 h-4 text-violet-500"></i>
                        Sign In
                    </a>

                    <!-- Register -->
                    <a href="{{ url('/register') }}" class="px-5 py-2 text-sm font-semibold rounded-xl border transition-all flex items-center gap-1.5 cursor-pointer text-white bg-violet-600 hover:bg-violet-500 shadow-md shadow-violet-600/25 border-violet-500 tactile-btn">
                        <i data-lucide="sparkles" class="w-4 h-4 text-violet-200"></i>
                        Register
                    </a>
                @else
                    <!-- Authenticated Quick Switcher Pill -->
                    <a href="{{ auth()->user()->role === 'brand' ? url('/brand') : (auth()->user()->role === 'admin' ? url('/admin') : url('/clipper')) }}"
                       class="px-4 py-2 text-sm font-semibold rounded-xl border border-black/10 dark:border-white/10 bg-slate-100 dark:bg-white/[0.05] text-gray-800 dark:text-gray-200 hover:text-black dark:hover:text-white flex items-center gap-2 tactile-btn">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ auth()->user()->role === 'brand' ? 'Brand Hub' : (auth()->user()->role === 'admin' ? 'Admin Panel' : 'Clipper Studio') }}</span>
                    </a>
                    <form method="POST" action="{{ url('/logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-2 text-xs font-mono font-medium rounded-lg border border-black/10 dark:border-white/10 text-gray-500 hover:text-rose-600 transition-colors cursor-pointer">
                            Logout
                        </button>
                    </form>
                @endguest
            </div>

            <!-- Mobile Action Buttons -->
            <div class="lg:hidden flex items-center gap-2">
                <button @click="toggleTheme()" class="p-2 rounded-xl border transition-all bg-white dark:bg-[#121218] border-black/10 dark:border-white/10 text-violet-600">
                    <i x-show="!isDark" data-lucide="moon" class="w-4 h-4"></i>
                    <i x-show="isDark" data-lucide="sun" class="w-4 h-4"></i>
                </button>
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-xl border transition-all bg-white dark:bg-[#121218] border-black/10 dark:border-white/10 text-black dark:text-white">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Dropdown Navigation -->
    <div x-show="mobileOpen" x-cloak class="lg:hidden bg-white dark:bg-[#0B0B10] border-b border-black/10 dark:border-white/10 p-4 space-y-3 shadow-lg">
        @if($customHeaderItems && $customHeaderItems->count() > 0)
            @foreach($customHeaderItems as $item)
                <a href="{{ url($item->url) }}" target="{{ $item->target }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">
                    {{ $item->title }}
                </a>
            @endforeach
        @else
            <a href="{{ url('/') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">Overview</a>
            <a href="{{ url('/marketplace') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">Campaigns</a>
            <a href="{{ url('/brand') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">For Brands</a>
            <a href="{{ url('/clipper') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">For Clippers</a>
            <a href="{{ url('/#trust') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-gray-800 dark:text-gray-200">FAQ</a>
        @endif
        <button type="button" @click="calculatorOpen = true; mobileOpen = false" class="w-full text-left px-3 py-2 rounded-lg text-sm font-semibold text-violet-600 dark:text-violet-400">Calculator</button>
        <div class="pt-2 border-t border-gray-200 dark:border-white/10 flex flex-col gap-2">
            @guest
                <a href="{{ url('/login') }}" class="text-center py-2 text-sm font-semibold rounded-lg bg-gray-100 dark:bg-white/5 text-gray-800 dark:text-gray-200">Sign In</a>
                <a href="{{ url('/register') }}" class="text-center py-2 text-sm font-semibold rounded-lg bg-violet-600 text-white">Register</a>
            @else
                <a href="{{ auth()->user()->role === 'brand' ? url('/brand') : (auth()->user()->role === 'admin' ? url('/admin') : url('/clipper')) }}" class="text-center py-2 text-sm font-semibold rounded-lg bg-violet-600 text-white">Open Workspace</a>
            @endguest
        </div>
    </div>
</header>
