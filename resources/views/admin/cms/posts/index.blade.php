@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Header & Action Row -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-dark fw-bold"><i class="las la-blog text-primary me-1"></i> Blog Posts & Creator Insights</h4>
                    <p class="text-muted mb-0">Publish platform guides, industry deep-dives, and announcements to drive organic discovery.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.cms.categories.index') }}" class="btn btn-outline-primary">
                        <i class="las la-folder"></i> Manage Categories
                    </a>
                    <a href="{{ route('admin.cms.posts.create') }}" class="btn btn-primary px-3 d-flex align-items-center gap-1 shadow-sm">
                        <i class="las la-plus-circle fs-5"></i> Write New Post
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('admin.cms.posts.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="las la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search article title or keywords..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="published" @selected(request('status') == 'published')>Published</option>
                            <option value="draft" @selected(request('status') == 'draft')>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="las la-redo-alt"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table--light style--two mb-0 align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-white py-3">Article</th>
                            <th class="text-white py-3">Category</th>
                            <th class="text-white py-3 text-center">Views</th>
                            <th class="text-white py-3 text-center">Status</th>
                            <th class="text-white py-3 text-center">Published</th>
                            <th class="text-white py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($posts as $post)
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded overflow-hidden bg-light border flex-shrink-0" style="width: 60px; height: 45px;">
                                            @if($post->featured_image)
                                                <img src="{{ $post->featured_image }}" alt="" class="w-100 h-100 object-fit-cover">
                                            @else
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
                                                    <i class="las la-image fs-4"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.cms.posts.edit', $post->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $post->title }}
                                            </a>
                                            @if($post->is_featured)
                                                <span class="badge bg-warning text-dark ms-1" style="font-size: 10px;">FEATURED</span>
                                            @endif
                                            <div class="text-muted small mt-0.5">
                                                <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="text-primary text-decoration-none font-monospace">
                                                    /blog/{{ $post->slug }} <i class="las la-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($post->category)
                                        <span class="badge bg-soft-primary text-primary border border-primary px-2 py-1">
                                            {{ $post->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Uncategorized</span>
                                    @endif
                                </td>
                                <td class="text-center font-monospace fw-bold text-dark">
                                    {{ number_format($post->views) }}
                                </td>
                                <td class="text-center">
                                    @if($post->status === 'published')
                                        <span class="badge bg-success px-2 py-1"><i class="las la-check-circle"></i> Published</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="las la-pencil-ruler"></i> Draft</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not Set' }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ url('/blog/' . $post->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Preview Article">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.cms.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Article">
                                            <i class="las la-pen"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePostModal{{ $post->id }}" title="Delete">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deletePostModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle"></i> Confirm Removal</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p class="text-dark">Are you sure you want to permanently delete post <strong>"{{ $post->title }}"</strong>?</p>
                                                    <small class="text-muted">This action will erase the article and historical view statistics.</small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.cms.posts.delete', $post->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger px-3">Delete Post</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="las la-newspaper fs-1 d-block mb-2 text-secondary"></i>
                                        No blog articles found matching criteria.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($posts->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
