@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">
    <!-- Top Stats & Header -->
    <div class="col-12">
        <div class="card bg-white border rounded-3 p-4 shadow-sm">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35);">
                        <i class="las la-photo-video fs-1"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 fw-bold text-dark">TECVEQ Media Library Organizer</h4>
                            <span class="badge bg-primary text-white px-2 py-1">v2.1.5</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="las la-check-circle"></i> Active</span>
                        </div>
                        <p class="text-muted small mb-0 mt-1">WordPress-style centralized media library. Organize images, campaign videos, brand kits, and article assets with instant preview and folder taxonomies.</p>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2" data-bs-toggle="collapse" data-bs-target="#uploadMediaSection">
                        <i class="las la-cloud-upload-alt fs-5"></i> Add New Media
                    </button>
                    <a href="{{ route('admin.plugins.filemanager.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2">
                        <i class="las la-folder-open"></i> File Manager
                    </a>
                </div>
            </div>

            <hr class="my-3 border-light-subtle">

            <!-- Category Pills & Storage Meter -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('admin.plugins.media.index', ['category' => 'all', 'type' => $type, 'search' => $search]) }}"
                       class="btn btn-sm {{ $category === 'all' ? 'btn-dark' : 'btn-outline-secondary' }} rounded-pill px-3">
                        All Items ({{ $counts['all'] }})
                    </a>
                    <a href="{{ route('admin.plugins.media.index', ['category' => 'campaign_video', 'type' => $type, 'search' => $search]) }}"
                       class="btn btn-sm {{ $category === 'campaign_video' ? 'btn-danger' : 'btn-outline-danger' }} rounded-pill px-3">
                        <i class="las la-video"></i> Campaign Videos ({{ $counts['campaign_video'] }})
                    </a>
                    <a href="{{ route('admin.plugins.media.index', ['category' => 'articles', 'type' => $type, 'search' => $search]) }}"
                       class="btn btn-sm {{ $category === 'articles' ? 'btn-info' : 'btn-outline-info' }} rounded-pill px-3">
                        <i class="las la-newspaper"></i> Blog & Articles ({{ $counts['articles'] }})
                    </a>
                    <a href="{{ route('admin.plugins.media.index', ['category' => 'branding', 'type' => $type, 'search' => $search]) }}"
                       class="btn btn-sm {{ $category === 'branding' ? 'btn-warning' : 'btn-outline-warning' }} rounded-pill px-3">
                        <i class="las la-certificate"></i> Brand Kits ({{ $counts['branding'] }})
                    </a>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="small text-muted"><i class="las la-hdd me-1"></i> Total Storage: <strong class="text-dark">{{ $totalStorageFormatted }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsible Upload Box -->
    <div class="col-12 collapse" id="uploadMediaSection">
        <div class="card border border-primary border-opacity-25 rounded-3 p-4 bg-light shadow-sm">
            <h5 class="fw-bold text-dark mb-2"><i class="las la-cloud-upload-alt text-primary me-1"></i> Upload Assets to Media Library</h5>
            <p class="text-muted small mb-3">Upload high-resolution images, brand campaign videos (MP4, WebM, MOV), or documents. Files will be cataloged and accessible across all CMS editors.</p>

            <form action="{{ route('admin.plugins.media.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Select Files <span class="text-danger">*</span></label>
                        <input type="file" name="files[]" multiple required class="form-control" accept="image/*,video/*,application/pdf,application/zip">
                        <small class="text-muted">Max 100MB per file. Multi-file upload supported.</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-dark">Category / Folder</label>
                        <select name="category" class="form-select">
                            <option value="general">General Media</option>
                            <option value="articles">Blog & Article Images</option>
                            <option value="campaign_video">Campaign Videos</option>
                            <option value="branding">Brand Kits & Logos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100 fw-bold">
                            <i class="las la-upload me-1"></i> Start Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="col-12">
        <div class="card bg-white border rounded-3 p-3 shadow-sm">
            <form action="{{ route('admin.plugins.media.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="category" value="{{ $category }}">

                <div class="col-md-3">
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Media Types</option>
                        <option value="image" {{ $type === 'image' ? 'selected' : '' }}>Images only ({{ $counts['images'] }})</option>
                        <option value="video" {{ $type === 'video' ? 'selected' : '' }}>Videos only ({{ $counts['videos'] }})</option>
                        <option value="document" {{ $type === 'document' ? 'selected' : '' }}>Documents / Archives</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white"><i class="las la-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by filename or title..." value="{{ $search }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                        @if(!empty($search))
                            <a href="{{ route('admin.plugins.media.index', ['category' => $category, 'type' => $type]) }}" class="btn btn-outline-danger">Clear</a>
                        @endif
                    </div>
                </div>

                <div class="col-md-3 text-end">
                    <span class="text-muted small">Showing {{ $mediaItems->firstItem() ?? 0 }} - {{ $mediaItems->lastItem() ?? 0 }} of {{ $mediaItems->total() }} items</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="col-12">
        @if($mediaItems->count() > 0)
            <div class="row g-3">
                @foreach($mediaItems as $item)
                    <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                        <div class="card h-100 border rounded-3 overflow-hidden shadow-sm position-relative media-card" style="transition: transform 0.15s ease, box-shadow 0.15s ease;">
                            <!-- Category Badge -->
                            <div class="position-absolute top-0 end-0 m-2 z-2">
                                @if($item->category === 'campaign_video')
                                    <span class="badge bg-danger shadow-sm"><i class="las la-video"></i> Video</span>
                                @elseif($item->category === 'articles')
                                    <span class="badge bg-info text-white shadow-sm">Article</span>
                                @elseif($item->category === 'branding')
                                    <span class="badge bg-warning text-dark shadow-sm">Brand</span>
                                @else
                                    <span class="badge bg-secondary shadow-sm">Media</span>
                                @endif
                            </div>

                            <!-- Thumbnail Preview -->
                            <div class="ratio ratio-1x1 bg-light border-bottom d-flex align-items-center justify-content-center overflow-hidden cursor-pointer"
                                 onclick="openMediaModal({{ json_encode($item) }})">
                                @if($item->isImage())
                                    <img src="{{ $item->url }}" alt="{{ $item->alt_text }}" class="w-100 h-100 object-fit-cover" loading="lazy">
                                @elseif($item->isVideo())
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark text-white p-2">
                                        <i class="las la-play-circle fs-1 text-danger"></i>
                                        <small class="mt-1 font-monospace" style="font-size: 10px;">{{ strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION)) }}</small>
                                    </div>
                                @else
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted p-2">
                                        <i class="las la-file-alt fs-1 text-primary"></i>
                                        <small class="mt-1 font-monospace" style="font-size: 10px;">{{ strtoupper(pathinfo($item->filename, PATHINFO_EXTENSION)) }}</small>
                                    </div>
                                @endif
                            </div>

                            <!-- Meta info -->
                            <div class="card-body p-2 d-flex flex-column justify-content-between">
                                <div class="text-truncate small fw-semibold text-dark" title="{{ $item->original_name }}">
                                    {{ $item->original_name }}
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-muted mt-1" style="font-size: 11px;">
                                    <span>{{ $item->formatted_size }}</span>
                                    <span>{{ $item->dimensions ?: (pathinfo($item->filename, PATHINFO_EXTENSION)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $mediaItems->links() }}
            </div>
        @else
            <div class="card bg-white border rounded-3 p-5 text-center shadow-sm">
                <i class="las la-folder-open display-4 text-muted mb-3"></i>
                <h5 class="fw-bold text-dark">No Media Items Found</h5>
                <p class="text-muted small mb-3">No assets have been cataloged in this folder yet. Click "Add New Media" to upload files.</p>
                <div>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse" data-bs-target="#uploadMediaSection">
                        <i class="las la-cloud-upload-alt me-1"></i> Upload First Asset
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal: Media Asset Details & Actions (WordPress Style) -->
<div class="modal fade" id="mediaDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white py-3">
                <h6 class="modal-title fw-bold" id="detailModalTitle"><i class="las la-file-alt me-1"></i> Attachment Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left: Preview Player / Image -->
                    <div class="col-md-7 d-flex flex-column align-items-center justify-content-center bg-light rounded-3 p-3 border">
                        <div id="modalPreviewContainer" class="w-100 text-center"></div>
                    </div>

                    <!-- Right: Metadata & Editable Fields -->
                    <div class="col-md-5 d-flex flex-column justify-content-between">
                        <div>
                            <div class="mb-3 pb-3 border-bottom">
                                <strong class="d-block text-dark small" id="detailOriginalName"></strong>
                                <span class="text-muted d-block" style="font-size: 11px;" id="detailMetaInfo"></span>
                            </div>

                            <form id="mediaUpdateForm">
                                <input type="hidden" id="detailId" name="id">
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-dark">Alternative Text (Alt)</label>
                                    <input type="text" id="detailAltText" class="form-control form-control-sm" placeholder="Describe image for SEO">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Category Taxonomy</label>
                                    <select id="detailCategory" class="form-select form-select-sm">
                                        <option value="general">General Media</option>
                                        <option value="articles">Blog & Article Images</option>
                                        <option value="campaign_video">Campaign Videos</option>
                                        <option value="branding">Brand Kits & Logos</option>
                                    </select>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="saveMediaMeta()">
                                    <i class="las la-save me-1"></i> Update Metadata
                                </button>
                            </form>

                            <div class="mt-3">
                                <label class="form-label small fw-bold text-dark">Direct File URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="detailUrl" class="form-control" readonly>
                                    <button class="btn btn-outline-secondary" type="button" onclick="copyDetailUrl()">
                                        <i class="las la-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 mt-3 border-top d-flex align-items-center justify-content-between">
                            <form action="{{ route('admin.plugins.media.delete') }}" method="POST" onsubmit="return confirm('Permanently delete this media asset from disk?');">
                                @csrf
                                <input type="hidden" name="id" id="deleteDetailId">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="las la-trash-alt me-1"></i> Delete Asset
                                </button>
                            </form>

                            <a href="#" id="detailDownloadLink" class="btn btn-sm btn-dark" download target="_blank">
                                <i class="las la-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.media-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}
.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('script')
<script>
let currentModalItem = null;

function openMediaModal(item) {
    currentModalItem = item;
    document.getElementById('detailId').value = item.id;
    document.getElementById('deleteDetailId').value = item.id;
    document.getElementById('detailOriginalName').textContent = item.original_name;
    document.getElementById('detailAltText').value = item.alt_text || '';
    document.getElementById('detailCategory').value = item.category || 'general';
    document.getElementById('detailUrl').value = item.url;
    document.getElementById('detailDownloadLink').href = item.url;

    const metaParts = [];
    if (item.formatted_size) metaParts.push(item.formatted_size);
    if (item.dimensions) metaParts.push(item.dimensions);
    if (item.mime_type) metaParts.push(item.mime_type);
    document.getElementById('detailMetaInfo').textContent = metaParts.join(' • ');

    const previewContainer = document.getElementById('modalPreviewContainer');
    if (item.mime_type && item.mime_type.startsWith('image/')) {
        previewContainer.innerHTML = `<img src="${item.url}" alt="${item.alt_text || ''}" class="img-fluid rounded shadow-sm" style="max-height: 380px;">`;
    } else if (item.mime_type && item.mime_type.startsWith('video/')) {
        previewContainer.innerHTML = `
            <video controls class="w-100 rounded shadow-sm" style="max-height: 380px;">
                <source src="${item.url}" type="${item.mime_type}">
                Your browser does not support HTML5 video playback.
            </video>
        `;
    } else {
        previewContainer.innerHTML = `
            <div class="py-5 text-center text-muted">
                <i class="las la-file-alt display-1 text-primary"></i>
                <div class="mt-2 fw-bold">${item.filename}</div>
            </div>
        `;
    }

    const modal = new bootstrap.Modal(document.getElementById('mediaDetailModal'));
    modal.show();
}

function copyDetailUrl() {
    const input = document.getElementById('detailUrl');
    input.select();
    document.execCommand('copy');
    alert('Public URL copied to clipboard: ' + input.value);
}

function saveMediaMeta() {
    const id = document.getElementById('detailId').value;
    const altText = document.getElementById('detailAltText').value;
    const category = document.getElementById('detailCategory').value;

    fetch('{{ route('admin.plugins.media.meta') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ id: id, alt_text: altText, category: category })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Metadata updated successfully.');
        } else {
            alert('Failed to update metadata.');
        }
    })
    .catch(err => {
        alert('Error updating metadata: ' + err);
    });
}
</script>
@endpush
