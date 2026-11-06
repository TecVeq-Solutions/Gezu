<!DOCTYPE html>
<html lang="en" class="light" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $defaultTitle = \App\Models\AuthSetting::get('seo_meta_title', 'GEZUX — The Global Short-Form Clipping Marketplace');
        $defaultDesc = \App\Models\AuthSetting::get('seo_meta_description', 'GEZUX is the global clipping performance marketplace connecting brands with creators to turn long-form content into viral distribution.');
        $defaultKeywords = \App\Models\AuthSetting::get('seo_meta_keywords', 'short form clipping, video bounties, viral marketing, tiktok clips, reels creators, youtube shorts monetization');
    @endphp
    <meta name="description" content="@yield('meta_description', $defaultDesc)">
    <meta name="keywords" content="{{ $defaultKeywords }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- OpenGraph & Twitter Cards (GEZUX SEO Engine) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', $defaultTitle)">
    <meta property="og:description" content="@yield('meta_description', $defaultDesc)">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="GEZUX">
    @if($socialImg = \App\Models\AuthSetting::get('seo_social_default_image'))
        <meta property="og:image" content="{{ asset($socialImg) }}">
        <meta name="twitter:image" content="{{ asset($socialImg) }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', $defaultTitle)">
    <meta name="twitter:description" content="@yield('meta_description', $defaultDesc)">
    @if($twHandle = \App\Models\AuthSetting::get('seo_twitter_handle'))
        <meta name="twitter:site" content="{{ $twHandle }}">
    @endif

    @if($gVerify = \App\Models\AuthSetting::get('seo_google_verification_code'))
        <meta name="google-site-verification" content="{{ $gVerify }}">
    @endif
    @if($bingVerify = \App\Models\AuthSetting::get('seo_bing_verification_code'))
        <meta name="msvalidate.01" content="{{ $bingVerify }}">
    @endif
    @if($baiduVerify = \App\Models\AuthSetting::get('seo_baidu_verification_code'))
        <meta name="baidu-site-verification" content="{{ $baiduVerify }}">
    @endif
    @if($yandexVerify = \App\Models\AuthSetting::get('seo_yandex_verification_code'))
        <meta name="yandex-verification" content="{{ $yandexVerify }}">
    @endif
    @if($pinterestVerify = \App\Models\AuthSetting::get('seo_pinterest_verification_code'))
        <meta name="p:domain_verify" content="{{ $pinterestVerify }}">
    @endif
    @if($nortonVerify = \App\Models\AuthSetting::get('seo_norton_verification_code'))
        <meta name="norton-safeweb-site-verification" content="{{ $nortonVerify }}">
    @endif

    <!-- GEZUX SEO Custom Meta & Head Tags -->
    {!! \App\Models\AuthSetting::get('seo_custom_meta_tags') !!}
    {!! \App\Models\AuthSetting::get('seo_custom_header_code') !!}
    @yield('schema')
    @stack('schema')

    <title>@yield('title', $defaultTitle)</title>

    <!-- GEZUX Speed Optimizer — Instant Page Hover Prefetch -->
    @if(\App\Models\AuthSetting::get('speed_instant_page_prefetch_enabled', true))
    <script>
        document.addEventListener('mouseover', function(e) {
            const a = e.target.closest('a');
            if (a && a.href && a.origin === location.origin && !a.dataset.prefetched && !a.href.includes('#')) {
                a.dataset.prefetched = 'true';
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = a.href;
                document.head.appendChild(link);
            }
        }, { passive: true });
    </script>
    @endif

    <!-- Theme Preference Initialization -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('gezux_theme') === 'dark') {
                    document.documentElement.classList.remove('light');
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.classList.add('light');
                }
            } catch(e) {}
        })();
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="/images/gezux-favicon.jpg">

    <!-- Fonts: Inter, Manrope & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN + Compiled Assets) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        obsidian: '#070708',
                        graphite: '#0B0B10',
                        surface: '#121218',
                        surfaceHover: '#18181F',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Manrope', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="/build/assets/app-DCj0AR96.css">
    <!-- Font Awesome 6 & Line Awesome CDN for reliable icon rendering -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" crossorigin="anonymous">

    <!-- Custom CSS Variables & Animations -->
    <style>
        :root {
            --bg-obsidian: #070708;
            --bg-near-black: #0B0B10;
            --bg-surface: #121218;
            --accent-violet: #7C3AED;
        }
        body {
            background-color: #FAFAFB;
            color: #09090B;
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
        }
        html.dark body {
            background-color: #070708;
            color: #F7F7F5;
        }
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        html.dark .glass-panel {
            background: rgba(11, 11, 16, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glow-accent {
            box-shadow: 0 0 35px -5px rgba(124, 58, 237, 0.35);
        }
        .gezux-bg-dots {
            background-image: radial-gradient(rgba(124, 58, 237, 0.14) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        html.dark .gezux-bg-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 28px 28px;
        }
        .gezux-card-light {
            background: #FFFFFF;
            border-color: rgba(228, 228, 231, 0.9);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        }
        html.dark .gezux-card-light {
            background: #121218;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
        }
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: max-content;
            animation: marquee 28s linear infinite;
            will-change: transform;
        }
        .animate-marquee:hover {
            animation-play-state: paused;
        }
        canvas {
            will-change: transform;
            transform: translateZ(0);
        }

        /* =========================================================================
           EMIL KOWALSKI DESIGN ENGINEERING & TASTE SKILL V2 ARCHITECTURE
           ========================================================================= */
        .tactile-btn, .starfield-btn, button:not([disabled]), .btn-tactile, a.btn-tactile {
            -webkit-tap-highlight-color: transparent !important;
            user-select: none;
            transition: transform 180ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 180ms cubic-bezier(0.16, 1, 0.3, 1), background-color 180ms cubic-bezier(0.16, 1, 0.3, 1), border-color 180ms cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: transform;
        }
        .tactile-btn:active, .starfield-btn:active, button:not([disabled]):active {
            transform: scale(0.97) !important;
        }

        /* Card Lift Physics */
        .card-lift {
            transition: transform 240ms cubic-bezier(0.16, 1, 0.3, 1), border-color 240ms cubic-bezier(0.16, 1, 0.3, 1), box-shadow 240ms cubic-bezier(0.16, 1, 0.3, 1) !important;
            will-change: transform, box-shadow;
        }
        .card-lift:hover {
            transform: translateY(-2px) !important;
            border-color: rgba(139, 92, 246, 0.4) !important;
            box-shadow: 0 14px 32px -10px rgba(124, 58, 237, 0.18) !important;
        }
        .card-lift:active {
            transform: scale(0.985) translateY(0) !important;
        }

        /* Display Typography Scale (Taste Skill v2) */
        .font-display-hero {
            letter-spacing: -0.035em !important;
            line-height: 1.08 !important;
        }
        .editorial-lead {
            max-width: 58ch !important;
            line-height: 1.65 !important;
        }

        /* Tabular Fin-tech Metrics (Impeccable Rule) */
        .metric-tabular {
            font-variant-numeric: tabular-nums !important;
            letter-spacing: -0.02em !important;
        }

        /* Luminous Leading Edge for Progress Bars */
        .progress-tip-luminous {
            box-shadow: 0 0 10px #8b5cf6, 0 0 4px #a855f7 !important;
        }

        /* Ambient Ring Focus State */
        .input-focus-ring:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.35) !important;
            border-color: rgba(124, 58, 237, 0.6) !important;
        }

        /* Filter Chips Smooth Acceleration */
        .filter-chip {
            -webkit-tap-highlight-color: transparent !important;
            user-select: none;
            transition: transform 200ms cubic-bezier(0.16, 1, 0.3, 1), background-color 200ms cubic-bezier(0.16, 1, 0.3, 1), border-color 200ms cubic-bezier(0.16, 1, 0.3, 1), color 200ms cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .filter-chip:active {
            transform: scale(0.96) !important;
        }
    </style>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Three.js Library for 3D Wave Grid -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    @stack('styles')
</head>
<body class="bg-[#FAFAFB] dark:bg-[#070708] text-[#09090B] dark:text-[#F7F7F5] antialiased min-h-screen flex flex-col selection:bg-violet-600 selection:text-white"
      x-data="{ 
          isDark: false,
          calculatorOpen: false, 
          submitClipOpen: false, 
          selectedCampaignId: '',
          selectedCampaignTitle: '',
          selectedCampaignRate: 1.0,
          init() {
              this.isDark = document.documentElement.classList.contains('dark');
          },
          toggleTheme() {
              this.isDark = !this.isDark;
              if (this.isDark) {
                  document.documentElement.classList.remove('light');
                  document.documentElement.classList.add('dark');
                  localStorage.setItem('gezux_theme', 'dark');
              } else {
                  document.documentElement.classList.remove('dark');
                  document.documentElement.classList.add('light');
                  localStorage.setItem('gezux_theme', 'light');
              }
              window.dispatchEvent(new CustomEvent('gezux-theme-changed'));
              if (window.lucide) window.lucide.createIcons();
          },
          openSubmitModal(id, title, rate) {
              this.selectedCampaignId = id || '';
              this.selectedCampaignTitle = title || '';
              this.selectedCampaignRate = rate || 1.0;
              this.submitClipOpen = true;
          }
      }">

    <!-- GEZUX SEO Custom Body Code (Scripts & GTM) -->
    {!! \App\Models\AuthSetting::get('seo_custom_body_code') !!}

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl bg-emerald-950/90 border border-emerald-500/40 text-emerald-200 text-sm shadow-2xl backdrop-blur-md">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400"></i>
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-200 ml-2">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl bg-rose-950/90 border border-rose-500/40 text-rose-200 text-sm shadow-2xl backdrop-blur-md">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-400"></i>
            <span>{{ session('error') }}</span>
            <button @click="show = false" class="text-rose-400 hover:text-rose-200 ml-2">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    @endif

    <!-- Navbar Component -->
    @include('components.navbar')

    <!-- Main Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Global Modals -->
    @include('components.calculator-modal')
    @include('components.submit-clip-modal')

    <!-- Footer Component -->
    @include('components.footer')

    <!-- Lucide Icon Initializer -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
        document.addEventListener('alpine:initialized', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')

    <!-- Three.js 3D WebGL Library (Required by Wave Field & CTA Vortex) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <!-- GEZUX Master Animations Engine -->
    <script src="{{ asset('js/gezux-animations.js') }}?v={{ file_exists(public_path('js/gezux-animations.js')) ? filemtime(public_path('js/gezux-animations.js')) : time() }}"></script>

    <!-- GEZUX SEO Custom Footer Code (Analytics & Tracking) -->
    {!! \App\Models\AuthSetting::get('seo_custom_footer_code') !!}

    <!-- HYIPLab Animated iziToast Popups -->
    @include('partials.notify')
</body>
</html>
