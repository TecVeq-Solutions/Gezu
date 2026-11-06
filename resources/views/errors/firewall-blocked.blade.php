<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Access Blocked — TECVEQ Security' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #070708; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-white">
    <div class="max-w-md w-full bg-[#0E0E16] border border-red-500/30 rounded-3xl p-8 shadow-2xl relative overflow-hidden text-center">
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-red-600 via-rose-500 to-amber-500"></div>
        
        <div class="w-16 h-16 mx-auto mb-6 rounded-2xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-extrabold tracking-tight mb-2">{{ $title ?? 'Access Denied' }}</h1>
        <p class="text-sm text-slate-400 mb-6 leading-relaxed">{{ $reason ?? 'Your request violated security protocols and was blocked.' }}</p>

        <div class="bg-black/40 border border-white/5 rounded-2xl p-4 text-xs font-mono space-y-2 mb-6 text-left">
            <div class="flex justify-between">
                <span class="text-slate-500">Threat Type:</span>
                <span class="text-rose-400 font-bold">{{ $type ?? 'Firewall Violation' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Your IP:</span>
                <span class="text-slate-300">{{ $ip ?? 'Unknown' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Firewall Engine:</span>
                <span class="text-slate-300">TECVEQ Security v4.1</span>
            </div>
        </div>

        <a href="/" class="inline-block w-full py-3 px-6 rounded-xl bg-white/10 hover:bg-white/15 text-white font-bold text-xs uppercase tracking-wider transition-all">
            Return to Homepage
        </a>
    </div>
</body>
</html>
