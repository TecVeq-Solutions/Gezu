@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Header & Action Row -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-dark fw-bold"><i class="las la-file-alt text-primary me-1"></i> Content Pages & Legal Agreements</h4>
                    <p class="text-muted mb-0">Create, customize, and maintain static platform pages, terms of service, and privacy policies.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.cms.pages.create') }}" class="btn btn-primary px-3 d-flex align-items-center gap-1 shadow-sm">
                        <i class="las la-plus-circle fs-5"></i> Create New Page
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-3">
                <form action="{{ route('admin.cms.pages.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="las la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search page title or URL slug..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Page Types</option>
                            @foreach($types as $key => $name)
                                <option value="{{ $key }}" @selected(request('type') == $key)>{{ $name }}</option>
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
                        <a href="{{ route('admin.cms.pages.index') }}" class="btn btn-outline-secondary" title="Reset Filters"><i class="las la-redo-alt"></i></a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table--light style--two mb-0 align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-white py-3">Page Title</th>
                            <th class="text-white py-3">Type / Category</th>
                            <th class="text-white py-3">Public Route / Slug</th>
                            <th class="text-white py-3 text-center">Status</th>
                            <th class="text-white py-3 text-center">Last Updated</th>
                            <th class="text-white py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $page)
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded bg-soft-primary text-primary d-flex align-items-center justify-content-center p-2" style="width: 38px; height: 38px;">
                                            @if(str_contains($page->page_type, 'legal'))
                                                <i class="las la-balance-scale fs-4"></i>
                                            @else
                                                <i class="las la-file-alt fs-4"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.cms.pages.edit', $page->id) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $page->title }}
                                            </a>
                                            @if($page->is_system)
                                                <span class="badge bg-secondary ms-1" style="font-size: 10px;">CORE LEGAL</span>
                                            @endif
                                            <small class="text-muted d-block">{{ Str::limit($page->excerpt ?: strip_tags($page->content), 65) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ $page->type_name }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="text-primary font-monospace small text-decoration-none">
                                        /p/{{ $page->slug }} <i class="las la-external-link-alt"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    @if($page->status === 'published')
                                        <span class="badge bg-success px-2 py-1"><i class="las la-check-circle"></i> Published</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="las la-pencil-ruler"></i> Draft</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $page->updated_at->format('M d, Y') }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ url('/p/' . $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Live Preview">
                                            <i class="las la-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.cms.pages.edit', $page->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Page">
                                            <i class="las la-pen"></i> Edit
                                        </a>
                                        @if(!$page->is_system)
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePageModal{{ $page->id }}" title="Delete">
                                                <i class="las la-trash"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Delete Modal -->
                                    @if(!$page->is_system)
                                    <div class="modal fade" id="deletePageModal{{ $page->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle"></i> Confirm Delete</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <p class="text-dark">Are you sure you want to permanently delete page <strong>"{{ $page->title }}"</strong>?</p>
                                                    <small class="text-muted">This will remove the page and break any navigation links referencing <code>/p/{{ $page->slug }}</code>.</small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.cms.pages.delete', $page->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger px-3">Delete Permanently</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="las la-file-alt fs-1 d-block mb-2 text-secondary"></i>
                                        No pages found matching your filters.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pages->hasPages())
                <div class="card-footer bg-white border-top p-3">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
