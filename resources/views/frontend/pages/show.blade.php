@extends('layouts.app')

@section('title', ($page->meta_title ?: $page->title) . ' — GEZUX')
@section('meta_description', $page->meta_description ?: $page->excerpt)

@section('schema')
    @if(!empty($page->custom_schema))
        {!! str_contains($page->custom_schema, '<script') ? $page->custom_schema : '<script type="application/ld+json">' . $page->custom_schema . '</script>' !!}
    @endif
@endsection

@section('content')
<div class="pt-28 pb-24 bg-gradient-to-b from-slate-50 via-white to-slate-50 dark:from-[#09090D] dark:via-[#0F0F17] dark:to-[#09090D] min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumbs Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 dark:text-[#A1A1AA] mb-8 font-mono">
            <a href="{{ url('/') }}" class="hover:text-violet-600 dark:hover:text-white transition-colors flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
            </a>
            <span class="text-slate-300 dark:text-white/20">/</span>
            <span class="text-slate-500 dark:text-[#71717A]">Platform Governance</span>
            <span class="text-slate-300 dark:text-white/20">/</span>
            <span class="text-violet-600 dark:text-violet-400 font-semibold">{{ $page->title }}</span>
        </nav>

        <!-- Document Header Hero Card -->
        <header class="relative p-6 sm:p-10 rounded-3xl bg-white dark:bg-[#12121C] border border-slate-200/80 dark:border-white/[0.08] shadow-sm mb-12 overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-violet-500/10 dark:bg-violet-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="px-3.5 py-1 rounded-full text-xs font-mono font-bold bg-violet-500/10 text-violet-700 dark:text-violet-300 border border-violet-500/20">
                            {{ $page->type_name ?: 'Official Document' }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono text-emerald-700 dark:text-emerald-300 bg-emerald-500/10 border border-emerald-500/20 font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Active &amp; Enforced
                        </span>
                    </div>
                    <span class="text-xs font-mono text-slate-500 dark:text-[#71717A]">
                        Last Revised: {{ $page->updated_at ? $page->updated_at->format('F d, Y') : date('F d, Y') }}
                    </span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
                    {{ $page->title }}
                </h1>

                @if($page->excerpt)
                    <p class="text-base sm:text-lg text-slate-600 dark:text-[#A1A1AA] leading-relaxed max-w-3xl">
                        {{ $page->excerpt }}
                    </p>
                @endif
            </div>
        </header>

        <!-- Main Document Body with Rich Typography & Semantic Hierarchy -->
        <main class="cms-document-container p-6 sm:p-12 rounded-3xl bg-white dark:bg-[#12121C] border border-slate-200/80 dark:border-white/[0.08] shadow-sm">
            <div class="cms-document-body">
                {!! $page->content !!}
            </div>
        </main>

        <!-- Document Footer, Authentication & Print Bar -->
        <footer class="mt-10 p-6 rounded-2xl bg-white/60 dark:bg-white/[0.02] border border-slate-200/60 dark:border-white/[0.05] flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-[#71717A]">
            <div class="flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-violet-500"></i>
                <span>Official Document ID: <code class="font-mono font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-white/10 px-2 py-0.5 rounded">GEZUX-{{ strtoupper(substr($page->slug, 0, 8)) }}-{{ $page->id ?: '01' }}</code></span>
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden sm:inline font-mono">GEZUX Platform Protocol v2.5</span>
                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-white/10 hover:border-violet-500 dark:hover:border-violet-400 hover:text-violet-600 dark:hover:text-violet-300 transition-all flex items-center gap-1.5 font-mono bg-white dark:bg-[#181824] shadow-xs cursor-pointer">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Document
                </button>
            </div>
        </footer>

    </div>
</div>

<style>
/* Executive Document Typography Styles */
.cms-document-body {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: #334155;
}
.dark .cms-document-body {
    color: #D4D4D8;
}

.cms-document-body h2 {
    font-size: 1.625rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #0f172a;
    margin-top: 2.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dark .cms-document-body h2 {
    color: #F8FAFC;
    border-bottom-color: rgba(255, 255, 255, 0.08);
}
.cms-document-body h2:first-of-type {
    margin-top: 0;
}

.cms-document-body h3 {
    font-size: 1.25rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: #1e293b;
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    padding-left: 0.75rem;
    border-left: 3px solid #8b5cf6;
}
.dark .cms-document-body h3 {
    color: #E2E8F0;
    border-left-color: #a78bfa;
}

.cms-document-body p {
    margin-bottom: 1.25rem;
}

.cms-document-body p.lead {
    font-size: 1.2rem;
    font-weight: 500;
    color: #475569;
    line-height: 1.7;
    margin-bottom: 2rem;
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}
.dark .cms-document-body p.lead {
    color: #cbd5e1;
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.06);
}

.cms-document-body ul, .cms-document-body ol {
    margin-top: 0.75rem;
    margin-bottom: 1.5rem;
    padding-left: 1.25rem;
}
.cms-document-body ul {
    list-style: none;
    padding-left: 0;
}
.cms-document-body ul li {
    position: relative;
    padding-left: 1.75rem;
    margin-bottom: 0.65rem;
}
.cms-document-body ul li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 0;
    font-weight: 800;
    color: #10b981;
    font-size: 0.95rem;
}

.cms-document-body ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
}
.cms-document-body ol li {
    margin-bottom: 0.65rem;
    padding-left: 0.25rem;
}

.cms-document-body strong {
    font-weight: 700;
    color: #0f172a;
}
.dark .cms-document-body strong {
    color: #ffffff;
}

.cms-document-body .doc-card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
}
.cms-document-body .doc-stat-card {
    padding: 1.25rem;
    border-radius: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    text-align: center;
}
.dark .cms-document-body .doc-stat-card {
    background: rgba(255, 255, 255, 0.03);
    border-color: rgba(255, 255, 255, 0.06);
}
.cms-document-body .doc-stat-number {
    font-size: 1.75rem;
    font-weight: 800;
    font-family: monospace;
    color: #7c3aed;
    display: block;
    margin-bottom: 0.25rem;
}
.dark .cms-document-body .doc-stat-number {
    color: #a78bfa;
}
.cms-document-body .doc-stat-label {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 600;
}
.dark .cms-document-body .doc-stat-label {
    color: #94a3b8;
}

.cms-document-body .callout-box {
    margin: 1.5rem 0;
    padding: 1.25rem 1.5rem;
    border-radius: 1rem;
    background: rgba(139, 92, 246, 0.06);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-left: 4px solid #8b5cf6;
}
.cms-document-body .callout-box p {
    margin-bottom: 0;
}
</style>
@endsection
