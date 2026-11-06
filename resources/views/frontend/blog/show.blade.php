@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' — GEZUX')
@section('meta_description', $post->meta_description ?: $post->excerpt)

@section('schema')
    @if(!empty($post->custom_schema))
        {!! str_contains($post->custom_schema, '<script') ? $post->custom_schema : '<script type="application/ld+json">' . $post->custom_schema . '</script>' !!}
    @endif
@endsection

@section('content')
<article class="pt-28 pb-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 dark:text-[#A1A1AA] mb-6 font-mono">
            <a href="{{ url('/') }}" class="hover:text-black dark:hover:text-white transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-black dark:hover:text-white transition-colors">Blog</a>
            @if($post->category)
                <span>/</span>
                <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="text-violet-600 dark:text-violet-400 hover:underline">
                    {{ $post->category->name }}
                </a>
            @endif
        </nav>

        <!-- Article Header -->
        <header class="mb-8">
            <h1 class="text-3xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
                {{ $post->title }}
            </h1>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-4 py-4 border-y border-gray-200 dark:border-white/10 text-xs">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-violet-600 text-white flex items-center justify-center font-bold text-sm shadow-md">
                        {{ substr($post->author->name ?? 'Admin', 0, 1) }}
                    </div>
                    <div>
                        <span class="font-bold text-gray-900 dark:text-white block">{{ $post->author->name ?? 'GEZUX Team' }}</span>
                        <span class="text-gray-500 dark:text-[#71717A]">{{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-4 text-gray-500 dark:text-[#A1A1AA] font-mono">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="eye" class="w-4 h-4 text-violet-500"></i> {{ number_format($post->views) }} views
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-4 h-4 text-violet-500"></i> ~{{ max(1, round(str_word_count(strip_tags($post->content)) / 200)) }} min read
                    </span>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        @if($post->featured_image)
            <div class="rounded-3xl overflow-hidden mb-10 shadow-2xl border border-gray-200 dark:border-white/10 bg-slate-900">
                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="w-full max-h-[480px] object-cover">
            </div>
        @endif

        <!-- Content Body -->
        <div class="prose prose-lg dark:prose-invert max-w-none text-gray-800 dark:text-[#D4D4D8] leading-relaxed" style="font-size: 1.075rem;">
            {!! $post->content !!}
        </div>

        <!-- Social Share Bar -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:border-white/10 flex flex-wrap items-center justify-between gap-4">
            <span class="text-xs font-mono font-bold uppercase tracking-wider text-gray-500 dark:text-[#A1A1AA]">
                Share This Article:
            </span>
            <div class="flex items-center gap-2">
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}" 
                   target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white transition-colors">
                    Share on X
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" 
                   target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white transition-colors">
                    Share on LinkedIn
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}" 
                   target="_blank" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#D4D4D8] hover:text-black dark:hover:text-white transition-colors">
                    WhatsApp
                </a>
            </div>
        </div>

    </div>

    <!-- Related Articles -->
    @if($relatedPosts->isNotEmpty())
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-20 pt-16 border-t border-gray-200 dark:border-white/10">
            <h3 class="text-2xl font-display font-bold text-gray-900 dark:text-white mb-8">
                Recommended Articles
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedPosts as $rel)
                    <article class="group rounded-2xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 overflow-hidden p-5 shadow-sm hover:border-violet-500/40 transition-all">
                        <span class="text-[10px] font-mono font-bold uppercase text-violet-600 dark:text-violet-400">
                            {{ $rel->category->name ?? 'Article' }}
                        </span>
                        <h4 class="mt-2 font-display font-bold text-base text-gray-900 dark:text-white group-hover:text-violet-500 transition-colors line-clamp-2">
                            <a href="{{ route('blog.show', $rel->slug) }}">
                                {{ $rel->title }}
                            </a>
                        </h4>
                        <p class="mt-2 text-xs text-gray-500 dark:text-[#A1A1AA] line-clamp-2">
                            {{ $rel->excerpt }}
                        </p>
                        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-[11px] text-gray-400">
                            <span>{{ $rel->published_at ? $rel->published_at->format('M d, Y') : $rel->created_at->format('M d, Y') }}</span>
                            <span class="text-violet-600 dark:text-violet-400 font-semibold">Read &rarr;</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</article>
@endsection
