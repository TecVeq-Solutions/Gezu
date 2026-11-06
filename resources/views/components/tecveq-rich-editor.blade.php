@props([
    'name' => 'content',
    'value' => '',
    'placeholder' => 'Write your story, headings, and blocks here...',
    'height' => '420px',
])

<div class="tecveq-editor-wrapper card border shadow-xs" id="tecveqEditorContainer">
    
    <!-- Top Toolbar (TECVEQ Block Studio) -->
    <div class="card-header bg-white p-2 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2" id="tecveqEditorToolbar">
        
        <!-- Formatting Tools -->
        <div class="d-flex flex-wrap align-items-center gap-1">
            
            <!-- Block Format Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle py-1 px-2 fw-bold" type="button" data-bs-toggle="dropdown" id="blockFormatBtn">
                    Paragraph
                </button>
                <ul class="dropdown-menu shadow-sm">
                    <li><a class="dropdown-item small" href="javascript:void(0)" onclick="formatBlock('p')">Paragraph</a></li>
                    <li><a class="dropdown-item small fw-bold fs-5" href="javascript:void(0)" onclick="formatBlock('h2')">Heading 2</a></li>
                    <li><a class="dropdown-item small fw-bold fs-6" href="javascript:void(0)" onclick="formatBlock('h3')">Heading 3</a></li>
                    <li><a class="dropdown-item small fw-bold" href="javascript:void(0)" onclick="formatBlock('h4')">Heading 4</a></li>
                    <li><a class="dropdown-item small font-monospace" href="javascript:void(0)" onclick="formatBlock('pre')">Code Block</a></li>
                </ul>
            </div>

            <div class="vr mx-1 my-1"></div>

            <!-- Inline Styles -->
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Bold (Ctrl+B)" onclick="execEditorCommand('bold')"><i class="las la-bold"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Italic (Ctrl+I)" onclick="execEditorCommand('italic')"><i class="las la-italic"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Underline" onclick="execEditorCommand('underline')"><i class="las la-underline"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Strikethrough" onclick="execEditorCommand('strikeThrough')"><i class="las la-strikethrough"></i></button>

            <div class="vr mx-1 my-1"></div>

            <!-- Alignment -->
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Align Left" onclick="execEditorCommand('justifyLeft')"><i class="las la-align-left"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Align Center" onclick="execEditorCommand('justifyCenter')"><i class="las la-align-center"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Align Right" onclick="execEditorCommand('justifyRight')"><i class="las la-align-right"></i></button>

            <div class="vr mx-1 my-1"></div>

            <!-- Lists & Blocks -->
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Bullet List" onclick="execEditorCommand('insertUnorderedList')"><i class="las la-list-ul"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Numbered List" onclick="execEditorCommand('insertOrderedList')"><i class="las la-list-ol"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Quote Callout" onclick="insertQuoteBlock()"><i class="las la-quote-right"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Columns 50/50" onclick="insertColumnsBlock()"><i class="las la-columns"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Callout Banner" onclick="insertCalloutBanner()"><i class="las la-bullhorn"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Horizontal Divider" onclick="insertDivider()"><i class="las la-minus"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Insert Table" onclick="insertTable()"><i class="las la-table"></i></button>
            <button type="button" class="btn btn-sm btn-light py-1 px-2" title="Hyperlink" onclick="insertHyperlink()"><i class="las la-link"></i></button>

            <div class="vr mx-1 my-1"></div>

            <!-- Media Insert from TECVEQ Media Library & File Manager -->
            <button type="button" class="btn btn-sm btn-primary py-1 px-2 text-white fw-bold shadow-xs" title="Select or Upload media via TECVEQ Media Library" onclick="openTecveqFileManager(insertImageFromManager)">
                <i class="las la-photo-video me-1"></i> Add Media
            </button>
            <button type="button" class="btn btn-sm btn-outline-info py-1 px-2" title="Embed Video" onclick="insertVideoEmbed()">
                <i class="las la-video"></i> Video Embed
            </button>
        </div>

        <!-- Mode Toggles & Reading Time Counter -->
        <div class="d-flex align-items-center gap-2">
            <span class="small font-monospace text-muted" id="editorWordCount">0 words • 1 min read</span>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary active py-1" id="modeVisualBtn" onclick="switchEditorMode('visual')">
                    <i class="las la-eye"></i> Visual
                </button>
                <button type="button" class="btn btn-outline-secondary py-1" id="modeSourceBtn" onclick="switchEditorMode('source')">
                    <i class="las la-code"></i> Source
                </button>
            </div>
        </div>

    </div>

    <!-- Visual WYSIWYG Editable Canvas -->
    <div class="card-body p-4 bg-white" id="tecveqVisualCanvas" contenteditable="true" style="min-height: {{ $height }}; outline: none; font-size: 16px; line-height: 1.7; color: #1f2937;">
        {!! $value ?: '' !!}
    </div>

    <!-- Source HTML Monospaced Textarea (Hidden by default) -->
    <textarea name="{{ $name }}" id="tecveqEditorHiddenInput" class="form-control font-monospace border-0 p-4" style="min-height: {{ $height }}; display: none; font-size: 14px; line-height: 1.6; border-radius: 0;" placeholder="HTML source code..." required>{{ $value }}</textarea>

    <!-- Editor Footer Info -->
    <div class="card-footer bg-light p-2 border-top d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.75rem;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary font-monospace">TECVEQ Block Editor v24.2.0</span>
            <span>Paragraph, Media & Columns active</span>
        </div>
        <div>
            <span>Press <strong>Enter</strong> for new block, <strong>Shift+Enter</strong> for soft break.</span>
        </div>
    </div>
</div>

<script>
let editorCurrentMode = 'visual';

function execEditorCommand(command, value = null) {
    document.getElementById('tecveqVisualCanvas').focus();
    document.execCommand(command, false, value);
    syncEditorContent();
}

function formatBlock(tag) {
    document.getElementById('tecveqVisualCanvas').focus();
    document.execCommand('formatBlock', false, tag);
    const labelMap = {
        'p': 'Paragraph',
        'h2': 'Heading 2',
        'h3': 'Heading 3',
        'h4': 'Heading 4',
        'pre': 'Code Block'
    };
    document.getElementById('blockFormatBtn').textContent = labelMap[tag] || 'Paragraph';
    syncEditorContent();
}

function insertQuoteBlock() {
    document.getElementById('tecveqVisualCanvas').focus();
    const html = `<blockquote style="border-left: 4px solid #3b82f6; padding: 8px 16px; margin: 16px 0; background: #f8fafc; color: #475569; font-style: italic;">“Write memorable quote or highlighted perspective here...”</blockquote><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function insertColumnsBlock() {
    document.getElementById('tecveqVisualCanvas').focus();
    const html = `<div style="display:flex;gap:1.5rem;margin:1.5rem 0;flex-wrap:wrap;"><div style="flex:1;min-width:240px;padding:1rem;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;"><h4>Column 1</h4><p>Add your column content here...</p></div><div style="flex:1;min-width:240px;padding:1rem;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;"><h4>Column 2</h4><p>Add your column content here...</p></div></div><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function insertCalloutBanner() {
    document.getElementById('tecveqVisualCanvas').focus();
    const html = `<div style="background:linear-gradient(135deg,#e0e7ff,#c7d2fe);border-left:4px solid #4f46e5;padding:1.25rem;border-radius:8px;margin:1.5rem 0;color:#1e1b4b;"><strong>Key Takeaway:</strong> Write a prominent note or summary here.</div><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function insertDivider() {
    document.getElementById('tecveqVisualCanvas').focus();
    document.execCommand('insertHorizontalRule', false, null);
    syncEditorContent();
}

function insertTable() {
    document.getElementById('tecveqVisualCanvas').focus();
    const html = `<table class="table table-bordered my-3" style="width: 100%; border-collapse: collapse; margin: 16px 0;"><thead><tr style="background:#f1f5f9;"><th style="padding: 8px; border: 1px solid #cbd5e1;">Metric</th><th style="padding: 8px; border: 1px solid #cbd5e1;">Benchmark</th><th style="padding: 8px; border: 1px solid #cbd5e1;">Status</th></tr></thead><tbody><tr><td style="padding: 8px; border: 1px solid #cbd5e1;">CPM Views</td><td style="padding: 8px; border: 1px solid #cbd5e1;">10,000</td><td style="padding: 8px; border: 1px solid #cbd5e1;">Verified</td></tr><tr><td style="padding: 8px; border: 1px solid #cbd5e1;">Bounty Payout</td><td style="padding: 8px; border: 1px solid #cbd5e1;">$25.00</td><td style="padding: 8px; border: 1px solid #cbd5e1;">Settled</td></tr></tbody></table><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function insertHyperlink() {
    const url = prompt('Enter Hyperlink URL:', 'https://');
    if (url && url !== 'https://') {
        document.execCommand('createLink', false, url);
        syncEditorContent();
    }
}

function insertVideoEmbed() {
    const url = prompt('Enter YouTube or Vimeo Video URL:');
    if (!url) return;
    let embedUrl = url;
    if (url.includes('youtube.com/watch?v=')) {
        const id = url.split('watch?v=')[1].split('&')[0];
        embedUrl = 'https://www.youtube.com/embed/' + id;
    } else if (url.includes('youtu.be/')) {
        const id = url.split('youtu.be/')[1].split('?')[0];
        embedUrl = 'https://www.youtube.com/embed/' + id;
    }
    const html = `<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;margin:1rem 0;border-radius:8px;"><iframe src="${embedUrl}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen></iframe></div><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function insertImageFromManager(file) {
    if (!file || !file.url) return;
    document.getElementById('tecveqVisualCanvas').focus();
    const html = `<figure><img src="${file.url}" alt="${file.name}"><figcaption class="text-center text-muted small mt-1">${file.name}</figcaption></figure><p></p>`;
    document.execCommand('insertHTML', false, html);
    syncEditorContent();
}

function switchEditorMode(mode) {
    const visual = document.getElementById('tecveqVisualCanvas');
    const source = document.getElementById('tecveqEditorHiddenInput');
    const vBtn = document.getElementById('modeVisualBtn');
    const sBtn = document.getElementById('modeSourceBtn');

    if (mode === 'source') {
        source.value = visual.innerHTML.trim();
        visual.style.display = 'none';
        source.style.display = 'block';
        vBtn.classList.remove('active');
        sBtn.classList.add('active');
        editorCurrentMode = 'source';
    } else {
        visual.innerHTML = source.value;
        source.style.display = 'none';
        visual.style.display = 'block';
        vBtn.classList.add('active');
        sBtn.classList.remove('active');
        editorCurrentMode = 'visual';
    }
}

function syncEditorContent() {
    const visual = document.getElementById('tecveqVisualCanvas');
    const source = document.getElementById('tecveqEditorHiddenInput');

    if (editorCurrentMode === 'visual') {
        source.value = visual.innerHTML;
    } else {
        visual.innerHTML = source.value;
    }

    // Update word count & reading time
    const text = visual.innerText || '';
    const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
    const readMin = Math.max(1, Math.ceil(words / 200));
    document.getElementById('editorWordCount').textContent = words + ' words • ' + readMin + ' min read';

    // Notify SEO analyzer if present
    if (typeof updateSeoAnalysis === 'function') {
        updateSeoAnalysis();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const visual = document.getElementById('tecveqVisualCanvas');
    const source = document.getElementById('tecveqEditorHiddenInput');

    visual.addEventListener('input', syncEditorContent);
    source.addEventListener('input', syncEditorContent);
    syncEditorContent();
});
</script>
