<!DOCTYPE html>
<html lang="en" class="light" id="html-root">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password | GEZUX</title>
    <link rel="icon" type="image/jpeg" href="/images/gezux-favicon.jpg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

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

    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-[#F8FAFC] dark:bg-[#070708] flex items-center justify-center p-4 selection:bg-violet-500 selection:text-white transition-colors duration-300">

    <div class="w-full max-w-md bg-white dark:bg-[#0B0B10] border border-slate-200 dark:border-white/10 rounded-2xl p-6 sm:p-8 shadow-xl">
        <div class="text-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-4">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-violet-600 to-indigo-500 flex items-center justify-center text-white font-display font-extrabold text-lg shadow-md shadow-violet-500/20">G</span>
                <span class="font-display font-bold text-xl text-slate-900 dark:text-white tracking-tight">GEZUX</span>
            </a>
            <h1 class="text-2xl font-display font-bold text-slate-900 dark:text-white">Reset Account Password</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Enter your registered email address and we'll send verification details to restore your account access.</p>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-xs flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 rounded-xl text-xs flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('web.forgot-password.send') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-mono font-bold uppercase text-slate-700 dark:text-slate-300 mb-1.5">Registered Email</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        value="{{ old('email') }}"
                        placeholder="creator@email.com" 
                        class="w-full bg-slate-50 dark:bg-[#14141E] border border-slate-200 dark:border-white/10 rounded-xl pl-9 pr-3.5 py-2.5 text-sm text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-violet-500 transition-colors"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-500 hover:to-indigo-500 text-white font-semibold py-2.5 px-4 rounded-xl text-sm shadow-md shadow-violet-500/20 active:scale-[0.99] transition-all flex items-center justify-center gap-2"
            >
                <i data-lucide="send" class="w-4 h-4"></i>
                <span>Dispatch Password Reset</span>
            </button>
        </form>

        <div class="mt-6 text-center border-t border-slate-100 dark:border-white/5 pt-4">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs text-violet-600 dark:text-violet-400 font-semibold hover:underline">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Return to Sign In</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
        });
    </script>
</body>
</html>
