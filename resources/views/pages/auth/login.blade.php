@php
    $googleConfigured = \App\Services\GoogleAuthService::isEnabled() 
        || (bool)\App\Models\AuthSetting::get('google_oauth_enabled') 
        || !empty(\App\Models\AuthSetting::get('google_client_id'))
        || !empty(config('services.google.client_id'));
@endphp
<!DOCTYPE html>
<html lang="en" class="light" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Authentication Portal | GEZUX</title>
    <link rel="icon" type="image/jpeg" href="/images/gezux-favicon.jpg">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Manrope', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>

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

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Sliding Auth Container Styling (inspired by luisandrelemos/Animated-Login-Page) */
        .auth-container {
            position: relative;
            width: 840px;
            max-width: 94vw;
            min-height: 520px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.2), 0 0 40px rgba(124, 58, 237, 0.15);
            transition: all 0.3s ease;
        }
        html.dark .auth-container {
            box-shadow: 0 25px 70px -15px rgba(0, 0, 0, 0.85), 0 0 50px rgba(124, 58, 237, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-box {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in-box {
            left: 0;
            width: 50%;
            z-index: 2;
        }

        .auth-container.right-panel-active .sign-in-box {
            transform: translateX(100%);
            opacity: 0;
            pointer-events: none;
        }

        .sign-up-box {
            left: 0;
            width: 50%;
            opacity: 0;
            z-index: 1;
            pointer-events: none;
        }

        .auth-container.right-panel-active .sign-up-box {
            transform: translateX(100%);
            opacity: 1;
            z-index: 5;
            pointer-events: auto;
            animation: showPanel 0.6s;
        }

        @keyframes showPanel {
            0%, 49.99% {
                opacity: 0;
                z-index: 1;
            }
            50%, 100% {
                opacity: 1;
                z-index: 5;
            }
        }

        /* Overlay Sliding Banner */
        .overlay-container {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            overflow: hidden;
            transition: transform 0.6s ease-in-out;
            z-index: 100;
        }

        .auth-container.right-panel-active .overlay-container {
            transform: translateX(-100%);
        }

        .overlay {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
            position: relative;
            left: -100%;
            height: 100%;
            width: 200%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
        }

        .auth-container.right-panel-active .overlay {
            transform: translateX(50%);
        }

        .overlay-panel {
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 32px;
            text-align: center;
            top: 0;
            height: 100%;
            width: 50%;
            transform: translateX(0);
            transition: transform 0.6s ease-in-out;
            color: #ffffff;
        }

        .overlay-left {
            transform: translateX(-20%);
        }

        .auth-container.right-panel-active .overlay-left {
            transform: translateX(0);
        }

        .overlay-right {
            right: 0;
            transform: translateX(0);
        }

        .auth-container.right-panel-active .overlay-right {
            transform: translateX(20%);
        }

        /* Interactive Yeti Avatar (inspired by Alisherka7/Animated-login-form-Avatar.github.io) */
        .avatar-svg {
            width: 72px;
            height: 72px;
            margin: 0 auto 6px;
            filter: drop-shadow(0 6px 14px rgba(124, 58, 237, 0.2));
            transition: transform 0.3s ease;
        }
        .avatar-svg:hover {
            transform: scale(1.04);
        }

        /* Avatar Paws Covering Eyes Animation */
        .paw-left, .paw-right {
            transform-box: fill-box;
            transform-origin: 50% 100%;
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
            transform: translateY(48px) scale(0.8);
            opacity: 0;
        }

        .avatar-hands-covering .paw-left {
            transform: translateY(0px) rotate(10deg) scale(1.05);
            opacity: 1;
        }
        .avatar-hands-covering .paw-right {
            transform: translateY(0px) rotate(-10deg) scale(1.05);
            opacity: 1;
        }

        /* Pupils Eye Tracking */
        .pupil-left, .pupil-right {
            transition: transform 0.15s ease-out;
        }

        /* Mobile fallback responsive switch */
        @media (max-width: 768px) {
            .auth-container {
                min-height: auto;
                width: 100%;
            }
            .overlay-container {
                display: none;
            }
            .sign-in-box, .sign-up-box {
                position: static;
                width: 100% !important;
                transform: none !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-[#070708] text-slate-900 dark:text-[#F7F7F5] min-h-screen flex items-center justify-center p-4 relative overflow-hidden transition-colors duration-300 font-sans"
      x-data="{
          isSignUp: {{ ($initialSignUp ?? false) || request()->routeIs('register') || request()->has('register') ? 'true' : 'false' }},
          isDark: false,
          passwordCovered: false,
          eyeX: 0,
          role: '{{ request('role') && in_array(request('role'), ['clipper', 'brand', 'streamer']) ? request('role') : 'clipper' }}',
          init() {
              this.isDark = document.documentElement.classList.contains('dark');
              if (window.lucide) window.lucide.createIcons();
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
              if (window.lucide) window.lucide.createIcons();
          },
          trackEmail(val) {
              const len = (val || '').length;
              this.eyeX = Math.min(8, Math.max(-8, (len - 12) * 0.5));
          }
      }">

    <!-- Ambient Glowing Orbs -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-violet-500/20 dark:bg-violet-600/25 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-500/20 dark:bg-indigo-600/25 rounded-full blur-[130px] pointer-events-none"></div>

    <!-- Top Floating Navigation Controls -->
    <div class="fixed top-6 left-6 z-50">
        <a href="{{ route('web.home') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/80 dark:bg-[#0E0E16]/80 border border-slate-200 dark:border-white/10 text-xs font-mono font-medium text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-white backdrop-blur-md shadow-sm transition-all hover:-translate-y-0.5">
            <i data-lucide="arrow-left" class="w-4 h-4 text-violet-500"></i>
            <span>Back to Marketplace</span>
        </a>
    </div>

    <div class="fixed top-6 right-6 z-50 flex items-center gap-3">
        <!-- Theme Toggle -->
        <button 
            type="button" 
            @click="toggleTheme()" 
            class="p-2.5 rounded-xl bg-white/80 dark:bg-[#0E0E16]/80 border border-slate-200 dark:border-white/10 text-slate-700 dark:text-slate-300 hover:text-violet-600 dark:hover:text-violet-400 backdrop-blur-md shadow-sm transition-all"
            title="Toggle theme"
        >
            <i x-show="!isDark" data-lucide="moon" class="w-4 h-4"></i>
            <i x-show="isDark" data-lucide="sun" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- MAIN SLIDING AUTH CONTAINER -->
    <div class="auth-container bg-white dark:bg-[#0E0E16]" :class="isSignUp ? 'right-panel-active' : ''">

        <!-- 1. SIGN IN FORM PANEL -->
        <div class="form-box sign-in-box flex flex-col justify-center px-8 sm:px-10 py-8" :class="{ 'hidden md:flex': isSignUp }">
            <div class="text-center">

                <!-- Interactive SVG Avatar -->
                <div class="relative inline-block" :class="passwordCovered ? 'avatar-hands-covering' : ''">
                    <svg class="avatar-svg" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Head Circle Background -->
                        <circle cx="60" cy="60" r="54" fill="#8B5CF6" fill-opacity="0.15" stroke="#7C3AED" stroke-width="2.5"/>
                        
                        <!-- Cute Ears -->
                        <circle cx="28" cy="38" r="12" fill="#7C3AED"/>
                        <circle cx="28" cy="38" r="7" fill="#DDD6FE"/>
                        <circle cx="92" cy="38" r="12" fill="#7C3AED"/>
                        <circle cx="92" cy="38" r="7" fill="#DDD6FE"/>

                        <!-- Face Shape -->
                        <path d="M30 62C30 44 43 32 60 32C77 32 90 44 90 62C90 80 77 92 60 92C43 92 30 80 30 62Z" fill="#FFFFFF"/>
                        
                        <!-- Eyes White Sclera -->
                        <ellipse cx="48" cy="58" rx="8" ry="10" fill="#F1F5F9" stroke="#0F172A" stroke-width="1.8"/>
                        <ellipse cx="72" cy="58" rx="8" ry="10" fill="#F1F5F9" stroke="#0F172A" stroke-width="1.8"/>

                        <!-- Pupils (Interactive Tracking) -->
                        <g :style="'transform: translate(' + eyeX + 'px, 0px)'">
                            <circle class="pupil-left" cx="49" cy="59" r="4.5" fill="#0F172A"/>
                            <circle cx="50.5" cy="57.5" r="1.5" fill="#FFFFFF"/>
                            
                            <circle class="pupil-right" cx="73" cy="59" r="4.5" fill="#0F172A"/>
                            <circle cx="74.5" cy="57.5" r="1.5" fill="#FFFFFF"/>
                        </g>

                        <!-- Snout & Nose -->
                        <ellipse cx="60" cy="69" rx="6" ry="4" fill="#DDD6FE"/>
                        <path d="M57 68C58.5 69.5 61.5 69.5 63 68L60 71L57 68Z" fill="#7C3AED"/>
                        <!-- Smile -->
                        <path d="M56 73C58 75.5 62 75.5 64 73" stroke="#475569" stroke-width="1.5" stroke-linecap="round"/>

                        <!-- Left Paw (Covers Eyes when password focused) -->
                        <g class="paw-left">
                            <ellipse cx="44" cy="58" rx="13" ry="16" fill="#7C3AED"/>
                            <circle cx="39" cy="51" r="3" fill="#DDD6FE"/>
                            <circle cx="44" cy="48" r="3" fill="#DDD6FE"/>
                            <circle cx="49" cy="51" r="3" fill="#DDD6FE"/>
                            <ellipse cx="44" cy="62" rx="6" ry="4.5" fill="#DDD6FE"/>
                        </g>

                        <!-- Right Paw (Covers Eyes when password focused) -->
                        <g class="paw-right">
                            <ellipse cx="76" cy="58" rx="13" ry="16" fill="#7C3AED"/>
                            <circle cx="71" cy="51" r="3" fill="#DDD6FE"/>
                            <circle cx="76" cy="48" r="3" fill="#DDD6FE"/>
                            <circle cx="81" cy="51" r="3" fill="#DDD6FE"/>
                            <ellipse cx="76" cy="62" rx="6" ry="4.5" fill="#DDD6FE"/>
                        </g>
                    </svg>
                </div>

                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-1">
                    Sign In
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                    Enter your email &amp; password to access your dashboard
                </p>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-3.5 p-3 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-rose-700 dark:text-rose-400 text-xs">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Sign In Form -->
            <form action="{{ route('web.login') }}" method="POST" class="space-y-3.5">
                @csrf

                <div>
                    <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            @input="trackEmail($event.target.value)"
                            placeholder="creator@email.com"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-9 pr-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500 transition-colors"
                        >
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300">Password</label>
                        <a href="{{ url('/forgot-password') }}" class="text-xs text-violet-600 dark:text-violet-400 font-semibold hover:underline">Forgot?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            @focus="passwordCovered = true"
                            @blur="passwordCovered = false"
                            placeholder="••••••••"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-9 pr-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500 transition-colors"
                        >
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-0.5">
                    <label class="flex items-center gap-2 text-slate-600 dark:text-slate-400 cursor-pointer select-none font-medium">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-violet-600 focus:ring-0">
                        <span>Remember session</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-sm tracking-wide transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2 mt-1 cursor-pointer tactile-btn"
                >
                    <span>Sign In</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            @if($googleConfigured)
                <div class="relative my-3.5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-[10px] uppercase">
                        <span class="bg-white dark:bg-[#0E0E16] px-2 text-slate-400 dark:text-slate-500 font-mono font-semibold">Or continue with</span>
                    </div>
                </div>

                <a 
                    href="{{ route('auth.google.redirect') }}" 
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-[#14141E] dark:hover:bg-[#1C1C2A] border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white font-bold text-xs tracking-wide transition-all shadow-sm flex items-center justify-center gap-2.5 cursor-pointer tactile-btn group"
                >
                    <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>
            @endif

            <!-- Mobile Switch Link -->
            <div class="mt-4 text-center text-xs text-slate-500 md:hidden">
                Don't have an account? 
                <button type="button" @click="isSignUp = true" class="text-violet-600 font-bold hover:underline ml-1">Create Account</button>
            </div>
        </div>

        <!-- 2. SIGN UP / REGISTER FORM PANEL -->
        <div class="form-box sign-up-box flex flex-col justify-center px-8 sm:px-10 py-7" :class="{ 'hidden md:flex': !isSignUp }">
            <div class="text-center mb-3.5">
                <div class="w-10 h-10 rounded-xl bg-violet-100 dark:bg-violet-950/80 text-violet-600 dark:text-violet-300 flex items-center justify-center mx-auto mb-1.5 shadow-sm">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-0.5">Create Account</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">Join the performance clipping network</p>
            </div>

            <!-- Role Selector -->
            <div class="grid grid-cols-3 gap-1.5 p-1 rounded-xl bg-slate-100 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 mb-3.5">
                <button 
                    type="button" 
                    @click="role = 'clipper'"
                    :class="role === 'clipper' 
                        ? 'bg-violet-600 text-white font-bold shadow-md shadow-violet-600/25' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="py-1.5 px-2 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1 cursor-pointer tactile-btn"
                >
                    <i data-lucide="scissors" class="w-3.5 h-3.5"></i>
                    <span>Clipper</span>
                </button>
                <button 
                    type="button" 
                    @click="role = 'streamer'"
                    :class="role === 'streamer' 
                        ? 'bg-rose-600 text-white font-bold shadow-md shadow-rose-600/25' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="py-1.5 px-2 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1 cursor-pointer tactile-btn"
                >
                    <i data-lucide="video" class="w-3.5 h-3.5"></i>
                    <span>Streamer</span>
                </button>
                <button 
                    type="button" 
                    @click="role = 'brand'"
                    :class="role === 'brand' 
                        ? 'bg-violet-600 text-white font-bold shadow-md shadow-violet-600/25' 
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    class="py-1.5 px-2 rounded-lg text-xs font-semibold transition-all flex items-center justify-center gap-1 cursor-pointer tactile-btn"
                >
                    <i data-lucide="building" class="w-3.5 h-3.5"></i>
                    <span>Brand</span>
                </button>
            </div>

            <form action="{{ route('web.register') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="role" :value="role">

                <div>
                    <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1" x-text="role === 'clipper' ? 'Creator Name' : (role === 'streamer' ? 'Streamer / Channel Name' : 'Company / Brand Name')"></label>
                    <div class="relative">
                        <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input 
                            type="text" 
                            name="name" 
                            required 
                            placeholder="Alex Rivers"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Email Address</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            placeholder="creator@email.com"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="••••••••"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1">Confirm</label>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="••••••••"
                            class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500"
                        >
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs tracking-wide transition-all shadow-md shadow-violet-600/30 flex items-center justify-center gap-2 mt-1 cursor-pointer tactile-btn"
                >
                    <span>Create Account</span>
                    <i data-lucide="check" class="w-4 h-4"></i>
                </button>
            </form>

            @if(session('pending_google_auth'))
                <div class="mt-3.5 p-3 rounded-xl bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-500/30 text-xs">
                    <div class="flex items-center gap-2 mb-1 font-bold text-violet-700 dark:text-violet-300">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Google Identity Verified</span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 mb-2">
                        Signed in as <strong>{{ session('pending_google_auth.email') }}</strong>. Select your role above and click to finalize:
                    </p>
                    <form action="{{ url('/auth/google/complete') }}" method="POST">
                        @csrf
                        <input type="hidden" name="role" :value="role">
                        <button type="submit" class="w-full py-2 px-3 rounded-lg bg-violet-600 hover:bg-violet-500 text-white font-bold text-xs shadow transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <span>Finalize Account as</span>
                            <span class="uppercase font-mono font-extrabold" x-text="role"></span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </button>
                    </form>
                </div>
            @endif

            @if($googleConfigured)
                <div class="relative my-3">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-[10px] uppercase">
                        <span class="bg-white dark:bg-[#0E0E16] px-2 text-slate-400 dark:text-slate-500 font-mono font-semibold">Or sign up with</span>
                    </div>
                </div>

                <a 
                    :href="'{{ route('auth.google.redirect') }}?role=' + role" 
                    class="w-full py-2.5 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 dark:bg-[#14141E] dark:hover:bg-[#1C1C2A] border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white font-bold text-xs tracking-wide transition-all shadow-sm flex items-center justify-center gap-2.5 cursor-pointer tactile-btn group"
                >
                    <svg class="w-4 h-4 shrink-0 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Sign up with Google</span>
                </a>
            @endif

            <!-- Mobile Switch Link -->
            <div class="mt-3 text-center text-xs text-slate-500 md:hidden">
                Already have an account? 
                <button type="button" @click="isSignUp = false" class="text-violet-600 font-bold hover:underline ml-1">Sign In</button>
            </div>
        </div>

        <!-- 3. SLIDING OVERLAY CONTAINER (Desktop Animation) -->
        <div class="overlay-container hidden md:block">
            <div class="overlay">
                
                <!-- Overlay Left (Visible when on Register form) -->
                <div class="overlay-panel overlay-left">
                    <h2 class="text-2xl sm:text-3xl font-display font-extrabold mb-2.5">Welcome Back!</h2>
                    <p class="text-xs text-violet-100 mb-6 max-w-xs leading-relaxed">
                        Already have a registered GEZUX creator or brand profile? Log in to view active campaign bounties and real-time telemetry.
                    </p>
                    <button 
                        type="button" 
                        @click="isSignUp = false"
                        class="px-7 py-2.5 rounded-xl border-2 border-white/80 text-white font-extrabold text-xs tracking-wide hover:bg-white hover:text-violet-700 transition-all shadow-lg cursor-pointer tactile-btn"
                    >
                        Sign In
                    </button>
                </div>

                <!-- Overlay Right (Visible when on Login form) -->
                <div class="overlay-panel overlay-right">
                    <h2 class="text-2xl sm:text-3xl font-display font-extrabold mb-2.5">Hello, Creator!</h2>
                    <p class="text-xs text-violet-100 mb-6 max-w-xs leading-relaxed">
                        Join the world's leading performance short-form clipping network and get rewarded for verified viral views.
                    </p>
                    <button 
                        type="button" 
                        @click="isSignUp = true"
                        class="px-7 py-2.5 rounded-xl border-2 border-white/80 text-white font-extrabold text-xs tracking-wide hover:bg-white hover:text-violet-700 transition-all shadow-lg cursor-pointer tactile-btn"
                    >
                        Create Account
                    </button>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
