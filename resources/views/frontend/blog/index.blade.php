@extends('layouts.app')

@section('title', 'Official Blog & Creator Insights — TECVEQ')
@section('meta_description', 'Discover performance video strategies, short-form editing tactics, and creator economy trends on the official TECVEQ editorial.')

@section('content')
<div class="pt-28 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header Section -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-mono font-semibold bg-violet-500/10 text-violet-600 dark:text-violet-400 border border-violet-500/20 mb-4">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> TECVEQ EDITORIAL & RESEARCH
        </span>
        <h1 class="text-4xl sm:text-5xl font-display font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">
            Creator Insights & Video Distribution Playbooks
        </h1>
        <p class="mt-4 text-base sm:text-lg text-gray-600 dark:text-[#A1A1AA] leading-relaxed">
            The definitive tactics, algorithmic updates, and workflow strategies used by top video clippers and global advertisers.
        </p>
    </div>

    <!-- Search & Category Filters -->
    <div class="mb-12 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Category Pills -->
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            <a href="{{ route('blog.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ !request('category') ? 'bg-violet-600 text-white shadow-md shadow-violet-500/20' : 'bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#D4D4D8] hover:border-violet-500/40' }}">
                All Topics
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('blog.index', ['category' => $cat->slug]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition-all {{ request('category') === $cat->slug ? 'bg-violet-600 text-white shadow-md shadow-violet-500/20' : 'bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-[#D4D4D8] hover:border-violet-500/40' }}">
                    {{ $cat->name }} ({{ $cat->posts_count }})
                </a>
            @endforeach
        </div>

        <!-- Search Form -->
        <form action="{{ route('blog.index') }}" method="GET" class="w-full md:w-72">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search articles..." 
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500/50">
            </div>
        </form>
    </div>

    <!-- Featured Post Hero (If on page 1 and no search) -->
    @if(!request('search') && !request('category') && $featuredPosts->isNotEmpty())
        @php $featured = $featuredPosts->first(); @endphp
        <div class="mb-14 group">
            <a href="{{ route('blog.show', $featured->slug) }}" class="block">
                <div class="relative rounded-3xl overflow-hidden bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 shadow-xl transition-all duration-300 group-hover:border-violet-500/50 group-hover:shadow-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 items-center">
                        <div class="lg:col-span-7 h-72 sm:h-96 overflow-hidden bg-slate-900 relative">
                            <img src="{{ $featured->featured_image ?: '/assets/admin/images/blog/blog-1.jpg' }}" alt="{{ $featured->title }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-violet-600 text-white shadow-lg">
                                    ★ FEATURED STORY
                                </span>
                            </div>
                        </div>
                        <div class="lg:col-span-5 p-8 sm:p-10 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-[#A1A1AA] mb-3">
                                    <span class="font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider text-[11px] font-mono">
                                        {{ $featured->category->name ?? 'Article' }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $featured->published_at ? $featured->published_at->format('M d, Y') : $featured->created_at->format('M d, Y') }}</span>
                                </div>
                                <h2 class="text-2xl sm:text-3xl font-display font-bold text-gray-900 dark:text-white group-hover:text-violet-500 transition-colors leading-snug">
                                    {{ $featured->title }}
                                </h2>
                                <p class="mt-4 text-sm text-gray-600 dark:text-[#A1A1AA] line-clamp-3 leading-relaxed">
                                    {{ $featured->excerpt }}
                                </p>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-violet-500 text-white flex items-center justify-center font-bold text-xs">
                                        {{ substr($featured->author->name ?? 'Admin', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-700 dark:text-[#D4D4D8]">
                                        {{ $featured->author->name ?? 'TECVEQ Team' }}
                                    </span>
                                </div>
                                <span class="text-xs font-semibold text-violet-600 dark:text-violet-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                    Read Article <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    <!-- Articles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($posts as $post)
            <article class="group flex flex-col rounded-2xl bg-white dark:bg-[#121218] border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm hover:shadow-xl hover:border-violet-500/40 transition-all duration-300">
                <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video overflow-hidden bg-slate-900 relative">
                    <img src="{{ $post->featured_image ?: '/assets/admin/images/blog/blog-2.jpg' }}" alt="{{ $post->title }}" 
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @if($post->category)
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-black/75 text-white backdrop-blur-md">
                                {{ $post->category->name }}
                            </span>
                        </div>
                    @endif
                </a>

                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-[#A1A1AA] mb-2 font-mono">
                            <span>{{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }}</span>
                            <span>•</span>
                            <span><i data-lucide="eye" class="w-3 h-3 inline"></i> {{ number_format($post->views) }} views</span>
                        </div>
                        <h3 class="font-display font-bold text-lg text-gray-900 dark:text-white group-hover:text-violet-500 transition-colors line-clamp-2 leading-snug">
                            <a href="{{ route('blog.show', $post->slug) }}">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="mt-2.5 text-xs text-gray-600 dark:text-[#A1A1AA] line-clamp-3 leading-relaxed">
                            {{ $post->excerpt }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                        <span class="text-[11px] text-gray-500 dark:text-[#71717A]">
                            By {{ $post->author->name ?? 'Editorial' }}
                        </span>
                        <a href="{{ route('blog.show', $post->slug) }}" class="text-xs font-semibold text-violet-600 dark:text-violet-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Read <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full text-center py-16">
                <i data-lucide="book-open" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Articles Found</h3>
                <p class="text-sm text-gray-500 mt-1">Try searching for other keywords or select another topic.</p>
                <a href="{{ route('blog.index') }}" class="mt-4 inline-block px-4 py-2 rounded-xl text-xs font-semibold bg-violet-600 text-white">
                    View All Articles
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $posts->links() }}
        </div>
    @endif

</div>
@endsection
