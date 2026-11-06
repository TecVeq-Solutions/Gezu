@extends('layouts.app')

@section('title', $title . ' | GEZUX')
@section('meta_description', 'Read the official GEZUX Privacy Policy covering creator bank transfers, advertiser data, and platform telemetry.')

@section('content')
<div class="relative pt-28 pb-24 min-h-screen bg-[#070708] overflow-hidden text-[#F7F7F5]">
    <!-- Ambient Glows -->
    <div class="absolute top-16 left-1/4 -translate-x-1/2 w-[600px] h-[350px] bg-violet-600/15 blur-[140px] rounded-full pointer-events-none -z-10"></div>
    <div class="absolute top-48 right-10 w-[500px] h-[320px] bg-purple-600/15 blur-[130px] rounded-full pointer-events-none -z-10"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Breadcrumb -->
        <div class="mb-6">
            <a href="{{ route('web.home') }}" class="inline-flex items-center gap-2 text-xs font-mono text-[#A1A1AA] hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4 text-violet-400"></i>
                <span>Back to Home</span>
            </a>
        </div>

        <div class="bg-[#0B0B10] border border-white/[0.08] rounded-3xl p-8 sm:p-12 shadow-2xl backdrop-blur-xl">
            <div class="border-b border-white/[0.08] pb-6 mb-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-mono bg-violet-500/10 border border-violet-500/30 text-violet-300 mb-3">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span>LEGAL COMPLIANCE</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-display font-extrabold text-white tracking-tight mb-2">
                    {{ $title }}
                </h1>
                <p class="text-xs font-mono text-[#71717A]">
                    Last Updated: {{ $updatedAt }}
                </p>
            </div>

            <div class="text-xs sm:text-sm text-[#D4D4D8] leading-relaxed space-y-6 whitespace-pre-line font-sans">
                {{ $content }}
            </div>
        </div>

    </div>
</div>
@endsection
