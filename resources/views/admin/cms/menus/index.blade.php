@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Header & Menu Selector -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h4 class="mb-1 text-dark fw-bold"><i class="las la-bars text-primary me-1"></i> Navigation Menu Builder</h4>
                    <p class="text-muted mb-0">Configure, customize, and reorder public header navigation, footer quick links, and legal menus.</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <form action="{{ route('admin.cms.menus.index') }}" method="GET" class="d-flex align-items-center gap-2">
                        <label class="form-label mb-0 fw-bold text-nowrap">Select Menu:</label>
                        <select name="menu" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 200px;">
                            @foreach($menus as $m)
                                <option value="{{ $m->slug }}" @selected($activeMenu && $activeMenu->slug === $m->slug)>
                                    {{ $m->name }} ({{ $m->slug }})
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($activeMenu)
        <!-- Left Column: Add Items to Menu -->
        <div class="col-xl-4 col-lg-5">

            <!-- Card 1: Add Pages -->
            <div class="card b-radius--10 border shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-file-alt text-primary me-1"></i> Add CMS Pages</h6>
                    <span class="badge bg-light text-muted border">{{ $pages->count() }} Pages Available</span>
                </div>
                <div class="card-body p-3">
                    <div style="max-height: 240px; overflow-y: auto;" class="border rounded p-2 mb-3 bg-light">
                        @foreach($pages as $page)
                            <div class="form-check py-1 border-bottom border-light">
                                <input class="form-check-input page-checkbox" type="checkbox" value="{{ $page->id }}" data-title="{{ $page->title }}" data-url="/p/{{ $page->slug }}" id="pageCheck{{ $page->id }}">
                                <label class="form-check-label text-dark small" for="pageCheck{{ $page->id }}">
                                    <strong>{{ $page->title }}</strong>
                                    <span class="text-muted d-block font-monospace" style="font-size: 11px;">/p/{{ $page->slug }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-sm btn-outline-primary w-100" id="addSelectedPagesBtn">
                        <i class="las la-plus-circle"></i> Add Selected Pages to Menu
                    </button>
                </div>
            </div>

            <!-- Card 2: Add Custom Link -->
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark"><i class="las la-link text-primary me-1"></i> Add Custom Link</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.cms.menus.items.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="menu_id" value="{{ $activeMenu->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">URL / Route <span class="text-danger">*</span></label>
                            <input type="text" name="url" class="form-control font-monospace" placeholder="/marketplace or https://..." required>
                            <small class="text-muted">Relative URL (e.g. <code>/marketplace</code>, <code>/blog</code>) or absolute URL.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Text / Label <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Browse Campaigns" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Open In</label>
                            <select name="target" class="form-select form-select-sm">
                                <option value="_self">Same Tab (_self)</option>
                                <option value="_blank">New Tab (_blank)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="las la-plus"></i> Add Custom Link to Menu
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Column: Current Menu Structure -->
        <div class="col-xl-8 col-lg-7">
            <div class="card b-radius--10 border shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="las la-sitemap text-primary me-1"></i> Menu Structure: <span class="text-primary">{{ $activeMenu->name }}</span>
                        </h6>
                        <small class="text-muted font-monospace">Location Tag: {{ $activeMenu->slug }}</small>
                    </div>
                    <span class="badge bg-soft-primary text-primary px-3 py-1 font-monospace">
                        {{ $activeMenu->items->count() }} Total Links
                    </span>
                </div>

                <div class="card-body p-4">
                    @if($activeMenu->items->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="las la-compass fs-1 text-secondary d-block mb-2"></i>
                            This menu has no navigation links yet. Use the left panel to add pages or custom links.
                        </div>
                    @else
                        <p class="text-muted small mb-3">
                            <i class="las la-info-circle"></i> Edit label, URL, order position, or remove items directly below.
                        </p>

                        <div class="d-flex flex-column gap-3">
                            @foreach($activeMenu->items as $index => $item)
                                <div class="card border b-radius--8 shadow-none bg-light p-3">
                                    <form action="{{ route('admin.cms.menus.items.update', $item->id) }}" method="POST" class="row g-3 align-items-end">
                                        @csrf
                                        @method('PUT')

                                        <div class="col-xl-1 col-md-2 col-4">
                                            <label class="form-label small text-muted mb-1 fw-bold">Order</label>
                                            <input type="number" name="order" class="form-control form-control-sm text-center font-monospace fw-bold" value="{{ $item->order }}" min="0">
                                        </div>

                                        <div class="col-xl-3 col-md-4 col-8">
                                            <label class="form-label small text-muted mb-1 fw-bold">Label</label>
                                            <input type="text" name="title" class="form-control form-control-sm fw-bold" value="{{ $item->title }}" required>
                                        </div>

                                        <div class="col-xl-4 col-md-6 col-12">
                                            <label class="form-label small text-muted mb-1 fw-bold">URL / Path</label>
                                            <input type="text" name="url" class="form-control form-control-sm font-monospace" value="{{ $item->url }}" required>
                                        </div>

                                        <div class="col-xl-2 col-md-6 col-6">
                                            <label class="form-label small text-muted mb-1 fw-bold">Target</label>
                                            <select name="target" class="form-select form-select-sm">
                                                <option value="_self" @selected($item->target == '_self')>Same Tab (_self)</option>
                                                <option value="_blank" @selected($item->target == '_blank')>New Tab (_blank)</option>
                                            </select>
                                        </div>

                                        <div class="col-xl-2 col-md-6 col-6 d-flex align-items-center justify-content-end gap-2">
                                            <button type="submit" class="btn btn-sm btn-outline-success px-3 fw-bold flex-fill" title="Save this item">
                                                <i class="las la-check"></i> Save
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger px-3 flex-fill" data-bs-toggle="modal" data-bs-target="#deleteMenuItemModal{{ $item->id }}" title="Delete item">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </div>
                                    </form>

                                    <!-- Delete Item Modal -->
                                    <div class="modal fade" id="deleteMenuItemModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-start">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger"><i class="las la-exclamation-triangle"></i> Remove Menu Link</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="text-dark">Remove <strong>"{{ $item->title }}"</strong> from this menu?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.cms.menus.items.delete', $item->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger px-3">Remove</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>

<!-- Hidden Form for Batch Page Adding -->
<form id="batchAddPagesForm" action="{{ route('admin.cms.menus.items.add') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="menu_id" value="{{ $activeMenu->id ?? 0 }}">
    <input type="hidden" name="title" id="batchTitle">
    <input type="hidden" name="url" id="batchUrl">
    <input type="hidden" name="target" value="_self">
    <input type="hidden" name="page_id" id="batchPageId">
</form>

<script>
document.getElementById('addSelectedPagesBtn')?.addEventListener('click', function() {
    const checked = document.querySelectorAll('.page-checkbox:checked');
    if (!checked.length) {
        alert('Please select at least one page checkbox to add.');
        return;
    }

    // Add first selected page (or sequentially)
    const first = checked[0];
    document.getElementById('batchTitle').value = first.dataset.title;
    document.getElementById('batchUrl').value = first.dataset.url;
    document.getElementById('batchPageId').value = first.value;
    document.getElementById('batchAddPagesForm').submit();
});
</script>
@endsection
