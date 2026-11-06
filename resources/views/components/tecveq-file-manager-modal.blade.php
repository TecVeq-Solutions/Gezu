<!-- Universal Modal: TECVEQ Media Library & File Picker -->
<div class="modal fade" id="tecveqFileManagerModal" tabindex="-1" aria-labelledby="tecveqFileManagerModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-xs rounded bg-primary text-white d-flex align-items-center justify-content-center p-1" style="width: 28px; height: 28px;">
                        <i class="las la-photo-video fs-5"></i>
                    </div>
                    <h6 class="modal-title fw-bold text-dark mb-0" id="tecveqFileManagerModalLabel">TECVEQ Media Library & Asset Picker</h6>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary active" id="modalTabMediaLib" onclick="modalSwitchTab('media')">
                                <i class="las la-photo-video me-1"></i> Media Library
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="modalTabExplorer" onclick="modalSwitchTab('explorer')">
                                <i class="las la-folder-open me-1"></i> File Explorer
                            </button>
                        </div>
                        <span class="small fw-bold text-muted font-monospace ms-2" id="modalFolderBreadcrumb">/media-library</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" id="modalFileSearch" class="form-control form-control-sm" placeholder="Filter assets..." style="width: 200px;" onkeyup="modalFilterFiles()">
                        <input type="file" id="modalDirectUploadInput" class="d-none" onchange="modalHandleDirectUpload(event)" multiple accept="image/*,video/*,application/pdf">
                        <button type="button" class="btn btn-sm btn-primary" onclick="document.getElementById('modalDirectUploadInput').click()">
                            <i class="las la-cloud-upload-alt me-1"></i> Upload New
                        </button>
                    </div>
                </div>

                <div class="p-4" style="min-height: 380px;">
                    <div id="modalLoading" class="text-center py-5">
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <span class="small text-muted ms-2">Loading TECVEQ media catalog...</span>
                    </div>

                    <div class="row g-3" id="modalFilesGrid" style="display: none;">
                        <!-- Dynamically populated -->
                    </div>

                    <div id="modalEmptyState" class="text-center py-5 text-muted" style="display: none;">
                        <i class="las la-folder-open fs-1 text-muted d-block mb-1"></i>
                        <p class="small mb-0">No media assets found. Click "Upload New" above.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2 d-flex align-items-center justify-content-between">
                <div class="small text-muted" id="modalSelectedFeedback">
                    No asset selected. Click on an asset to select.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary px-3 fw-bold" id="modalInsertBtn" onclick="modalConfirmInsert()" disabled>
                        Insert Selected Asset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let modalCurrentTab = 'media';
let modalCurrentFolder = '';
let modalFilesList = [];
let modalSelectedFile = null;
let modalCallbackTarget = null;

function openTecveqFileManager(callbackOrTargetId) {
    modalCallbackTarget = callbackOrTargetId;
    modalSelectedFile = null;
    document.getElementById('modalInsertBtn').disabled = true;
    document.getElementById('modalSelectedFeedback').textContent = 'No asset selected. Click on an asset to select.';

    const modal = new bootstrap.Modal(document.getElementById('tecveqFileManagerModal'));
    modal.show();
    modalSwitchTab('media');
}

function modalSwitchTab(tab) {
    modalCurrentTab = tab;
    document.getElementById('modalTabMediaLib').classList.toggle('active', tab === 'media');
    document.getElementById('modalTabExplorer').classList.toggle('active', tab === 'explorer');

    if (tab === 'media') {
        document.getElementById('modalFolderBreadcrumb').textContent = '/media-library';
        modalLoadMediaItems();
    } else {
        modalLoadFiles('');
    }
}

function modalLoadMediaItems() {
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalFilesGrid').style.display = 'none';
    document.getElementById('modalEmptyState').style.display = 'none';

    fetch('{{ route("admin.plugins.media.api.list") }}')
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalLoading').style.display = 'none';
            if (data.status === 'success') {
                modalFilesList = (data.data || []).map(item => ({
                    name: item.original_name,
                    url: item.url,
                    path: item.url,
                    is_image: (item.mime_type || '').startsWith('image/'),
                    is_video: (item.mime_type || '').startsWith('video/'),
                    size_formatted: item.file_size,
                    dimensions: item.dimensions,
                    alt_text: item.alt_text,
                }));
                modalRenderFiles(modalFilesList);
            }
        })
        .catch(err => {
            document.getElementById('modalLoading').style.display = 'none';
            console.error(err);
        });
}

function modalLoadFiles(folder = '') {
    modalCurrentFolder = folder;
    document.getElementById('modalFolderBreadcrumb').textContent = '/uploads' + (folder ? '/' + folder : '');
    document.getElementById('modalLoading').style.display = 'block';
    document.getElementById('modalFilesGrid').style.display = 'none';
    document.getElementById('modalEmptyState').style.display = 'none';

    fetch('{{ route("admin.plugins.filemanager.api.list") }}?folder=' + encodeURIComponent(folder))
        .then(res => res.json())
        .then(data => {
            document.getElementById('modalLoading').style.display = 'none';
            if (data.status === 'success') {
                const folders = (data.folders || []).map(f => ({ ...f, is_dir: true }));
                const files = (data.files || []).map(f => ({ ...f, is_dir: false }));
                modalFilesList = [...folders, ...files];
                modalRenderFiles(modalFilesList);
            }
        })
        .catch(err => {
            document.getElementById('modalLoading').style.display = 'none';
            console.error(err);
        });
}

function modalRenderFiles(items) {
    const grid = document.getElementById('modalFilesGrid');
    grid.innerHTML = '';

    if (items.length === 0) {
        document.getElementById('modalEmptyState').style.display = 'block';
        return;
    }

    document.getElementById('modalEmptyState').style.display = 'none';
    grid.style.display = 'flex';

    if (modalCurrentTab === 'explorer' && modalCurrentFolder) {
        const upCol = document.createElement('div');
        upCol.className = 'col-4 col-md-3 col-lg-2';
        upCol.innerHTML = `
            <div class="card h-100 border p-2 text-center cursor-pointer" onclick="modalGoUp()" style="cursor: pointer; background: #fafafa;">
                <i class="las la-level-up-alt text-primary fs-2 mb-1"></i>
                <span class="small fw-bold text-dark d-block">.. [Back]</span>
            </div>
        `;
        grid.appendChild(upCol);
    }

    items.forEach((item, index) => {
        const col = document.createElement('div');
        col.className = 'col-4 col-md-3 col-lg-2';

        if (item.is_dir) {
            col.innerHTML = `
                <div class="card h-100 border p-2 text-center cursor-pointer" onclick="modalLoadFiles('${item.path}')" style="cursor: pointer;">
                    <i class="las la-folder text-warning fs-1 mb-1"></i>
                    <span class="small fw-bold text-dark d-block text-truncate" title="${item.name}">${item.name}</span>
                </div>
            `;
        } else {
            let thumb = `<i class="las la-file-alt text-secondary fs-1 mb-1"></i>`;
            if (item.is_image) {
                thumb = `<img src="${item.url}" alt="${item.name}" style="height: 54px; max-width: 100%; object-fit: contain; border-radius: 4px; margin-bottom: 4px;">`;
            } else if (item.is_video) {
                thumb = `<i class="las la-video text-danger fs-1 mb-1"></i>`;
            }

            col.innerHTML = `
                <div class="card h-100 border p-2 text-center cursor-pointer modal-file-card" onclick="modalSelectItem(${index})" id="modalCard_${index}" style="cursor: pointer;">
                    <div style="height: 56px; display: flex; align-items: center; justify-content: center;">
                        ${thumb}
                    </div>
                    <span class="small fw-semibold text-dark d-block text-truncate mt-1" style="font-size: 0.75rem;" title="${item.name}">${item.name}</span>
                </div>
            `;
        }
        grid.appendChild(col);
    });
}

function modalSelectItem(index) {
    document.querySelectorAll('.modal-file-card').forEach(el => el.classList.remove('border-primary', 'bg-soft-primary'));
    const card = document.getElementById('modalCard_' + index);
    if (card) card.classList.add('border-primary', 'bg-soft-primary');

    modalSelectedFile = modalFilesList[index];
    if (modalSelectedFile) {
        document.getElementById('modalInsertBtn').disabled = false;
        document.getElementById('modalSelectedFeedback').innerHTML = `<strong class="text-primary">${modalSelectedFile.name}</strong> (${modalSelectedFile.size_formatted || ''})`;
    }
}

function modalConfirmInsert() {
    if (!modalSelectedFile) return;

    if (typeof modalCallbackTarget === 'function') {
        modalCallbackTarget(modalSelectedFile);
    } else if (typeof modalCallbackTarget === 'string') {
        const input = document.getElementById(modalCallbackTarget);
        if (input) {
            input.value = modalSelectedFile.url;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    bootstrap.Modal.getInstance(document.getElementById('tecveqFileManagerModal')).hide();
}

function modalGoUp() {
    if (!modalCurrentFolder) return;
    const parts = modalCurrentFolder.split('/');
    parts.pop();
    modalLoadFiles(parts.join('/'));
}

function modalFilterFiles() {
    const q = document.getElementById('modalFileSearch').value.toLowerCase();
    const filtered = modalFilesList.filter(item => item.name.toLowerCase().includes(q));
    modalRenderFiles(filtered);
}

function modalHandleDirectUpload(e) {
    const files = e.target.files;
    if (files.length === 0) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('category', 'articles');

    document.getElementById('modalLoading').style.display = 'block';

    const endpoint = modalCurrentTab === 'media' ? '{{ route("admin.plugins.media.upload") }}' : '{{ route("admin.plugins.filemanager.upload") }}';

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (modalCurrentTab === 'media') {
            modalLoadMediaItems();
        } else {
            modalLoadFiles(modalCurrentFolder);
        }
    })
    .catch(err => {
        alert('Upload failed: ' + err);
        if (modalCurrentTab === 'media') modalLoadMediaItems(); else modalLoadFiles(modalCurrentFolder);
    });
}
</script>
