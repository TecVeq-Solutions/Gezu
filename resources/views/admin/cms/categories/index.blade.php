@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Header -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-dark fw-bold"><i class="las la-folder text-primary me-1"></i> Blog & Resource Categories</h4>
                    <p class="text-muted mb-0">Organize articles and performance playbooks into structured taxonomies.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                        <i class="las la-plus-circle"></i> Add New Category
                    </button>
                    <a href="{{ route('admin.cms.posts.index') }}" class="btn btn-outline-secondary">
                        <i class="las la-arrow-left"></i> Back to Posts
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid / Table -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table--light style--two mb-0 align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-white py-3">Category Name</th>
                            <th class="text-white py-3">Slug</th>
                            <th class="text-white py-3 text-center">Articles Count</th>
                            <th class="text-white py-3">Description</th>
                            <th class="text-white py-3 text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded bg-soft-primary text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <i class="{{ $category->icon ?: 'las la-folder' }} fs-4"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark">{{ $category->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code class="text-muted">/blog?category={{ $category->slug }}</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border border-primary px-3 py-1 font-monospace fw-bold">
                                        {{ $category->posts_count }} Articles
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ Str::limit($category->description ?: 'No description provided', 70) }}
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                            <i class="las la-pen"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}">
                                            <i class="las la-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('admin.cms.categories.update', $category->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold text-dark"><i class="las la-edit text-primary me-1"></i> Edit Category: {{ $category->name }}</h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                                                            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Slug <span class="text-danger">*</span></label>
                                                            <input type="text" name="slug" class="form-control font-monospace" value="{{ $category->slug }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">LineAwesome Icon Class</label>
                                                            <input type="text" name="icon" class="form-control" value="{{ $category->icon }}">
                                                            <small class="text-muted">e.g. <code>las la-chart-line</code>, <code>las la-bullhorn</code></small>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Description</label>
                                                            <textarea name="description" rows="3" class="form-control">{{ $category->description }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-sm btn-primary px-3">Update Category</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle"></i> Delete Category</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-dark">Are you sure you want to delete category <strong>"{{ $category->name }}"</strong>?</p>
                                                    <small class="text-muted">Existing articles associated with this category will become Uncategorized.</small>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.cms.categories.delete', $category->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger px-3">Confirm Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    No categories found. Click "Add New Category" above to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-start">
            <form action="{{ route('admin.cms.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title fw-bold text-dark"><i class="las la-plus-circle text-primary me-1"></i> Add New Category</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Video Production" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Slug (Optional)</label>
                        <input type="text" name="slug" class="form-control font-monospace" placeholder="video-production">
                        <small class="text-muted">Auto-generated from name if left empty.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">LineAwesome Icon Class</label>
                        <input type="text" name="icon" class="form-control" placeholder="las la-video" value="las la-folder">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Brief summary of articles in this category..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-3">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
