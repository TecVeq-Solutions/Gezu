@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                        <i class="las la-folder-open fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 text-dark fw-bold">TECVEQ File Manager</h4>
                            <span class="badge badge--primary font-monospace">v7.2.1 ACTIVE</span>
                            <span class="badge bg-light text-dark border">Core Media Engine</span>
                        </div>
                        <p class="text-muted mb-0">Centralized media, asset storage, and file explorer. Upload, organize, preview, and generate immutable public asset links.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="loadFiles()">
                        <i class="las la-sync"></i> Refresh
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newFolderModal">
                        <i class="las la-folder-plus"></i> New Folder
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadFilesModal">
                        <i class="las la-cloud-upload-alt"></i> Upload Files
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Explorer Card -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            
            <!-- Toolbar & Breadcrumb -->
            <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-folder text-primary fs-4"></i>
                    <span class="fw-bold text-dark font-monospace" id="currentFolderBreadcrumb">/</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm" style="width: 240px;">
                        <span class="input-group-text bg-light border-end-0"><i class="las la-search text-muted"></i></span>
                        <input type="text" id="fileSearchInput" class="form-control border-start-0" placeholder="Filter files..." onkeyup="filterFiles()">
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" id="viewGridBtn" onclick="setViewMode('grid')"><i class="las la-th-large"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="viewListBtn" onclick="setViewMode('list')"><i class="las la-list"></i></button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="row g-0">
                    
                    <!-- File Browser Area -->
                    <div class="col-lg-8 col-xl-9 p-4 border-end" style="min-height: 480px;">
                        <div id="fileExplorerLoading" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="text-muted small mt-2">Loading TECVEQ media repository...</p>
                        </div>

                        <!-- Grid View -->
                        <div class="row g-3" id="filesGridContainer" style="display: none;">
                            <!-- Populated dynamically via JS -->
                        </div>

                        <!-- Empty State -->
                        <div id="filesEmptyState" class="text-center py-5 text-muted" style="display: none;">
                            <i class="las la-folder-open fs-1 text-muted d-block mb-2"></i>
                            <h6 class="fw-bold text-dark">This folder is empty</h6>
                            <p class="small text-muted mb-3">Upload your first image, video, or document to this folder.</p>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadFilesModal">
                                <i class="las la-cloud-upload-alt"></i> Upload Files
                            </button>
                        </div>
                    </div>

                    <!-- Inspector Panel -->
                    <div class="col-lg-4 col-xl-3 p-4 bg-light">
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-info-circle text-primary me-1"></i> Asset Inspector</h6>

                        <div id="inspectorEmpty" class="text-center py-5 text-muted">
                            <i class="las la-mouse-pointer fs-2 d-block mb-2 text-muted"></i>
                            <p class="small mb-0">Select an item to view preview, dimensions, and copy its public URL.</p>
                        </div>

                        <div id="inspectorDetails" style="display: none;">
                            <!-- Preview Box -->
                            <div class="bg-white border rounded p-2 text-center mb-3 shadow-sm" style="min-height: 140px; display: flex; align-items: center; justify-content: center;">
                                <img id="inspectorImage" src="" alt="Preview" style="max-height: 130px; max-width: 100%; object-fit: contain; border-radius: 4px; display: none;">
                                <i id="inspectorIcon" class="las la-file-alt text-secondary" style="font-size: 64px; display: none;"></i>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted fw-bold d-block">File Name</label>
                                <span id="inspectorName" class="fw-bold text-dark small text-break"></span>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block">Size</label>
                                    <span id="inspectorSize" class="small text-dark font-monospace"></span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted fw-bold d-block">Type</label>
                                    <span id="inspectorExt" class="badge bg-secondary font-monospace text-uppercase"></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small text-muted fw-bold d-block">Public Access URL</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="inspectorUrl" class="form-control font-monospace small bg-white" readonly>
                                    <button class="btn btn-outline-primary" type="button" onclick="copyInspectorUrl()">
                                        <i class="las la-copy"></i>
                                    </button>
                                </div>
                                <small class="text-success small d-none" id="copySuccessMsg"><i class="las la-check"></i> Copied to clipboard!</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" id="inspectorEditBtn" class="btn btn-sm btn-primary" style="display: none;" onclick="openFileEditor()">
                                    <i class="las la-code me-1"></i> Edit in Code Editor
                                </button>
                                <a id="inspectorDownloadBtn" href="#" download class="btn btn-sm btn-outline-dark">
                                    <i class="las la-download me-1"></i> Download Asset
                                </a>
                                <button type="button" id="inspectorZipBtn" class="btn btn-sm btn-outline-secondary" onclick="zipSelectedItem()">
                                    <i class="las la-file-archive me-1"></i> Create ZIP Archive
                                </button>
                                <button type="button" id="inspectorUnzipBtn" class="btn btn-sm btn-outline-success" style="display: none;" onclick="unzipSelectedItem()">
                                    <i class="las la-box-open me-1"></i> Extract Archive Here
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSelectedItem()">
                                    <i class="las la-trash me-1"></i> Delete Item
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal: Upload Files -->
<div class="modal fade" id="uploadFilesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="uploadFilesForm" onsubmit="handleUploadSubmit(event)">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-dark"><i class="las la-cloud-upload-alt text-primary me-1"></i> Upload Assets to File Manager</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Files (Images, Videos, PDFs, ZIPs)</label>
                        <input type="file" id="uploadFilesInput" class="form-control" multiple required>
                        <small class="text-muted d-block mt-1">Multi-file selection supported. Maximum 50MB per file.</small>
                    </div>
                    <div id="uploadProgressContainer" class="d-none mt-3">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%;"></div>
                        </div>
                        <small class="text-muted d-block mt-1">Uploading and indexing files...</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="uploadSubmitBtn" class="btn btn-sm btn-primary px-3">Start Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: New Folder -->
<div class="modal fade" id="newFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form onsubmit="handleNewFolderSubmit(event)">
                <div class="modal-header border-bottom">
                    <h6 class="modal-title fw-bold text-dark"><i class="las la-folder-plus text-primary me-1"></i> Create Folder</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <label class="form-label small fw-bold">Folder Name</label>
                    <input type="text" id="newFolderNameInput" class="form-control form-control-sm" placeholder="e.g. banners" required>
                </div>
                <div class="modal-footer bg-light border-top p-2">
                    <button type="button" class="btn btn-xs btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-xs btn-primary px-3">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: In-Browser Code / Text Editor -->
<div class="modal fade" id="codeEditorModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="las la-code fs-4 text-info"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="editorModalTitle">File Editor</h6>
                        <small class="text-white-50 font-monospace" id="editorModalFilePath" style="font-size: 11px;"></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" onclick="saveFileEditor()">
                        <i class="las la-save me-1"></i> Save Changes
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-dark">
                <div id="editorLoading" class="text-center py-5 text-white">
                    <div class="spinner-border spinner-border-sm text-info me-2"></div> Loading file contents...
                </div>
                <textarea id="fileEditorTextarea" class="form-control font-monospace text-light bg-dark border-0 p-3"
                          style="min-height: 520px; font-size: 13px; line-height: 1.5; outline: none; box-shadow: none; resize: vertical; display: none;"
                          spellcheck="false"></textarea>
            </div>
            <div class="modal-footer bg-dark border-top border-secondary py-2 d-flex justify-content-between text-muted small">
                <span>Press <strong>Tab</strong> for indent. Changes are written directly to file on server.</span>
                <span id="editorStatusMsg" class="text-success fw-bold"></span>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
let currentFolder = '';
let currentFiles = [];
let selectedItem = null;

document.addEventListener('DOMContentLoaded', function() {
    loadFiles();
});

function loadFiles(folder = '') {
    currentFolder = folder;
    document.getElementById('currentFolderBreadcrumb').textContent = '/' + (folder ? folder : '');
    document.getElementById('fileExplorerLoading').style.display = 'block';
    document.getElementById('filesGridContainer').style.display = 'none';
    document.getElementById('filesEmptyState').style.display = 'none';
    deselectItem();

    fetch('{{ route("admin.plugins.filemanager.api.list") }}?folder=' + encodeURIComponent(folder))
        .then(res => res.json())
        .then(data => {
            document.getElementById('fileExplorerLoading').style.display = 'none';
            if (data.status === 'success') {
                const dirs = data.directories || data.folders || [];
                const fls = data.files || [];
                currentFiles = [...dirs, ...fls];
                renderFiles(currentFiles);
            }
        })
        .catch(err => {
            document.getElementById('fileExplorerLoading').style.display = 'none';
            console.error(err);
        });
}

function renderFiles(items) {
    const grid = document.getElementById('filesGridContainer');
    grid.innerHTML = '';

    if (items.length === 0) {
        document.getElementById('filesEmptyState').style.display = 'block';
        return;
    }

    document.getElementById('filesEmptyState').style.display = 'none';
    grid.style.display = 'flex';

    // If inside a subfolder, add "Up / Back" item
    if (currentFolder) {
        const upCol = document.createElement('div');
        upCol.className = 'col-6 col-md-4 col-xl-3';
        upCol.innerHTML = `
            <div class="card h-100 border p-3 text-center cursor-pointer file-item-card" onclick="goUpFolder()" style="cursor: pointer; background: #fafafa;">
                <i class="las la-level-up-alt text-primary fs-1 mb-1"></i>
                <span class="small fw-bold text-dark d-block">.. [Back]</span>
            </div>
        `;
        grid.appendChild(upCol);
    }

    items.forEach((item, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 col-md-4 col-xl-3';

        if (item.is_dir) {
            col.innerHTML = `
                <div class="card h-100 border p-3 text-center cursor-pointer file-item-card" onclick="openFolder('${item.path}')" style="cursor: pointer;">
                    <i class="las la-folder text-warning fs-1 mb-1"></i>
                    <span class="small fw-bold text-dark d-block text-truncate" title="${item.name}">${item.name}</span>
                    <small class="text-muted" style="font-size: 0.72rem;">Folder</small>
                </div>
            `;
        } else {
            let thumb = `<i class="las la-file-alt text-secondary fs-1 mb-1"></i>`;
            if (item.is_image) {
                thumb = `<img src="${item.url}" alt="${item.name}" style="height: 52px; max-width: 100%; object-fit: contain; border-radius: 4px; margin-bottom: 6px;">`;
            } else if (item.is_media) {
                thumb = `<i class="las la-file-video text-info fs-1 mb-1"></i>`;
            }

            col.innerHTML = `
                <div class="card h-100 border p-2 text-center cursor-pointer file-item-card" onclick="selectItem(${index})" id="fileCard_${index}" style="cursor: pointer;">
                    <div style="height: 60px; display: flex; align-items: center; justify-content: center;">
                        ${thumb}
                    </div>
                    <span class="small fw-bold text-dark d-block text-truncate mt-1" title="${item.name}">${item.name}</span>
                    <small class="text-muted" style="font-size: 0.7rem;">${item.size_formatted}</small>
                </div>
            `;
        }
        grid.appendChild(col);
    });
}

function selectItem(index) {
    document.querySelectorAll('.file-item-card').forEach(el => el.classList.remove('border-primary', 'bg-soft-primary'));
    const card = document.getElementById('fileCard_' + index);
    if (card) {
        card.classList.add('border-primary', 'bg-soft-primary');
    }

    selectedItem = currentFiles[index];
    if (!selectedItem) return;

    document.getElementById('inspectorEmpty').style.display = 'none';
    document.getElementById('inspectorDetails').style.display = 'block';

    document.getElementById('inspectorName').textContent = selectedItem.name;
    document.getElementById('inspectorSize').textContent = selectedItem.size_formatted || '-';
    document.getElementById('inspectorUrl').value = selectedItem.url || selectedItem.path;
    const dlBtn = document.getElementById('inspectorDownloadBtn');
    if (dlBtn) {
        if (selectedItem.url) {
            dlBtn.href = selectedItem.url;
            dlBtn.style.display = 'inline-block';
        } else {
            dlBtn.style.display = 'none';
        }
    }

    const img = document.getElementById('inspectorImage');
    const icon = document.getElementById('inspectorIcon');
    const editBtn = document.getElementById('inspectorEditBtn');
    const unzipBtn = document.getElementById('inspectorUnzipBtn');

    if (editBtn) editBtn.style.display = selectedItem.is_code ? 'block' : 'none';
    if (unzipBtn) unzipBtn.style.display = selectedItem.is_zip ? 'block' : 'none';

    if (selectedItem.is_image) {
        img.src = selectedItem.url;
        img.style.display = 'block';
        icon.style.display = 'none';
    } else {
        img.style.display = 'none';
        icon.style.display = 'block';
    }
}

function deselectItem() {
    selectedItem = null;
    document.getElementById('inspectorEmpty').style.display = 'block';
    document.getElementById('inspectorDetails').style.display = 'none';
}

function openFolder(path) {
    loadFiles(path);
}

function goUpFolder() {
    if (!currentFolder) return;
    const parts = currentFolder.split('/');
    parts.pop();
    loadFiles(parts.join('/'));
}

function copyInspectorUrl() {
    const copyText = document.getElementById('inspectorUrl');
    copyText.select();
    navigator.clipboard.writeText(copyText.value);
    const msg = document.getElementById('copySuccessMsg');
    msg.classList.remove('d-none');
    setTimeout(() => msg.classList.add('d-none'), 2500);
}

function handleUploadSubmit(e) {
    e.preventDefault();
    const files = document.getElementById('uploadFilesInput').files;
    if (files.length === 0) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('folder', currentFolder);
    formData.append('_token', '{{ csrf_token() }}');

    document.getElementById('uploadProgressContainer').classList.remove('d-none');
    document.getElementById('uploadSubmitBtn').disabled = true;

    fetch('{{ route("admin.plugins.filemanager.upload") }}', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('uploadProgressContainer').classList.add('d-none');
        document.getElementById('uploadSubmitBtn').disabled = false;
        bootstrap.Modal.getInstance(document.getElementById('uploadFilesModal')).hide();
        document.getElementById('uploadFilesForm').reset();
        loadFiles(currentFolder);
    })
    .catch(err => {
        alert('Upload failed: ' + err);
        document.getElementById('uploadProgressContainer').classList.add('d-none');
        document.getElementById('uploadSubmitBtn').disabled = false;
    });
}

function handleNewFolderSubmit(e) {
    e.preventDefault();
    const name = document.getElementById('newFolderNameInput').value;
    if (!name) return;

    fetch('{{ route("admin.plugins.filemanager.folder") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name: name, folder: currentFolder })
    })
    .then(res => res.json())
    .then(data => {
        bootstrap.Modal.getInstance(document.getElementById('newFolderModal')).hide();
        document.getElementById('newFolderNameInput').value = '';
        loadFiles(currentFolder);
    });
}

function deleteSelectedItem() {
    if (!selectedItem) return;
    if (!confirm('Are you sure you want to permanently delete "' + selectedItem.name + '"?')) return;

    fetch('{{ route("admin.plugins.filemanager.delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: selectedItem.path })
    })
    .then(res => res.json())
    .then(data => {
        deselectItem();
        loadFiles(currentFolder);
    });
}

function filterFiles() {
    const q = document.getElementById('fileSearchInput').value.toLowerCase();
    const filtered = currentFiles.filter(item => item.name.toLowerCase().includes(q));
    renderFiles(filtered);
}

// Code Editor Functions
function openFileEditor() {
    if (!selectedItem) return;
    const modal = new bootstrap.Modal(document.getElementById('codeEditorModal'));
    document.getElementById('editorModalTitle').textContent = 'Editing: ' + selectedItem.name;
    document.getElementById('editorModalFilePath').textContent = selectedItem.path;
    document.getElementById('fileEditorTextarea').style.display = 'none';
    document.getElementById('editorLoading').style.display = 'block';
    document.getElementById('editorStatusMsg').textContent = '';
    modal.show();

    fetch('{{ route("admin.plugins.filemanager.read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: selectedItem.path })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('editorLoading').style.display = 'none';
        const txt = document.getElementById('fileEditorTextarea');
        txt.value = data.content || '';
        txt.style.display = 'block';
    })
    .catch(err => {
        alert('Could not read file: ' + err);
    });
}

function saveFileEditor() {
    if (!selectedItem) return;
    const content = document.getElementById('fileEditorTextarea').value;
    const statusMsg = document.getElementById('editorStatusMsg');
    statusMsg.textContent = 'Saving...';

    fetch('{{ route("admin.plugins.filemanager.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: selectedItem.path, content: content })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            statusMsg.textContent = 'Saved successfully at ' + new Date().toLocaleTimeString();
            setTimeout(() => { statusMsg.textContent = ''; }, 3000);
        } else {
            alert('Failed to save file: ' + data.message);
        }
    })
    .catch(err => {
        alert('Error saving file: ' + err);
    });
}

function zipSelectedItem() {
    if (!selectedItem) return;
    if (!confirm('Create a ZIP archive of "' + selectedItem.name + '"?')) return;

    fetch('{{ route("admin.plugins.filemanager.zip") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: selectedItem.path })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Archive created.');
        loadFiles(currentFolder);
    })
    .catch(err => alert('Zip failed: ' + err));
}

function unzipSelectedItem() {
    if (!selectedItem) return;
    if (!confirm('Extract "' + selectedItem.name + '" to current folder?')) return;

    fetch('{{ route("admin.plugins.filemanager.unzip") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ path: selectedItem.path })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.message || 'Extracted.');
        loadFiles(currentFolder);
    })
    .catch(err => alert('Extract failed: ' + err));
}
</script>
@endpush
@endsection
