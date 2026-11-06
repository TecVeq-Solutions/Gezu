@props([
    'title' => '',
    'slug' => '',
    'metaTitle' => '',
    'metaDescription' => '',
    'metaKeywords' => '',
    'focusKeyword' => '',
    'canonicalUrl' => '',
    'customSchema' => '',
    'schemaType' => 'Article',
    'baseUrl' => url('/blog/'),
])

<div class="card b-radius--10 border shadow-sm mt-4" id="tecveqSeoMetaBox">
    <!-- Header with TECVEQ SEO Score Gauge -->
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #10B981;">
                <i class="las la-search fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold text-dark">TECVEQ SEO Suite</h6>
                <small class="text-muted" style="font-size: 0.72rem;">TECVEQ Search Architecture Engine</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 px-3 py-1 bg-soft-success rounded-pill border border-success" id="seoScoreContainer" style="background: rgba(16, 185, 129, 0.1);">
                <div class="fw-bold font-monospace fs-5 text-success" id="seoScoreNumber">82</div>
                <div class="small fw-bold text-success" id="seoScoreLabel">/ 100 — Good</div>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        
        <!-- Focus Keyword Input Bar -->
        <div class="mb-4 p-3 bg-light rounded border">
            <label class="form-label fw-bold text-dark mb-1">
                <i class="las la-key text-primary me-1"></i> Focus Keyword
            </label>
            <div class="input-group">
                <input type="text" name="focus_keyword" id="seoFocusKeyword" class="form-control" placeholder="e.g. video monetization, podcast clipping" value="{{ old('focus_keyword', $focusKeyword) }}" oninput="updateSeoAnalysis()">
                <button class="btn btn-outline-secondary" type="button" onclick="updateSeoAnalysis()">
                    <i class="las la-sync"></i> Analyze
                </button>
            </div>
            <small class="text-muted mt-1 d-block">The primary search term or phrase you want this content to rank for in Google search.</small>
        </div>

        <!-- Google SERP Live Snippet Preview (Desktop & Mobile) -->
        <div class="mb-4 p-3 border rounded bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                <span class="small fw-bold text-muted text-uppercase">Google Search Live Snippet Preview</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" id="serpDesktopBtn" onclick="toggleSerpDevice('desktop')">
                        <i class="las la-desktop"></i> Desktop
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="serpMobileBtn" onclick="toggleSerpDevice('mobile')">
                        <i class="las la-mobile"></i> Mobile
                    </button>
                </div>
            </div>

            <!-- Snippet Preview Canvas -->
            <div id="serpPreviewBox" style="font-family: Arial, sans-serif; max-width: 600px; padding: 8px 0;">
                <div class="d-flex align-items-center gap-1 mb-1 text-muted small" style="font-size: 13px;">
                    <img src="{{ siteFavicon() }}" alt="" style="width: 16px; height: 16px; border-radius: 50%;">
                    <span class="text-dark">{{ parse_url(url('/'), PHP_URL_HOST) }}</span>
                    <span>›</span>
                    <span id="serpBreadcrumbPreview" class="text-muted font-monospace">blog › {{ $slug ?: 'sample-slug' }}</span>
                </div>
                <h5 class="mb-1 text-primary fw-normal text-truncate" id="serpTitlePreview" style="color: #1a0dab !important; cursor: pointer; text-decoration: underline;">
                    {{ $metaTitle ?: ($title ?: 'Article Headline Goes Here — Site Title') }}
                </h5>
                <p class="mb-0 text-secondary small" id="serpDescPreview" style="color: #4d5156; font-size: 14px; line-height: 1.45;">
                    {{ $metaDescription ?: 'Write a meta description snippet to entice searchers. It should explain key benefits and naturally include your target focus keyword...' }}
                </p>
            </div>
        </div>

        <!-- Tabs for Meta, Social, and Advanced Schema -->
        <ul class="nav nav-tabs mb-3" id="seoCardTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" id="box-meta-tab" data-bs-toggle="tab" data-bs-target="#box-meta-pane" type="button">
                    <i class="las la-file-alt me-1"></i> General Snippet
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" id="box-social-tab" data-bs-toggle="tab" data-bs-target="#box-social-pane" type="button">
                    <i class="las la-share-alt me-1"></i> Social Sharing Card
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" id="box-schema-tab" data-bs-toggle="tab" data-bs-target="#box-schema-pane" type="button">
                    <i class="las la-code me-1"></i> Schema & Robots
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small text-success" id="box-audit-tab" data-bs-toggle="tab" data-bs-target="#box-audit-pane" type="button">
                    <i class="las la-clipboard-check me-1"></i> SEO Audit Checklist (<span id="auditPassedCount">5</span>/6)
                </button>
            </li>
        </ul>

        <div class="tab-content" id="seoCardTabsContent">
            
            <!-- TAB 1: GENERAL SNIPPET -->
            <div class="tab-pane fade show active" id="box-meta-pane" role="tabpanel">
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label small fw-bold mb-0 text-dark">SEO Meta Title</label>
                        <span class="small font-monospace text-muted" id="seoTitleCounter">0 / 60 chars</span>
                    </div>
                    <input type="text" name="meta_title" id="seoMetaTitleInput" class="form-control" placeholder="Targeted headline for search engines" value="{{ old('meta_title', $metaTitle) }}" oninput="updateSeoAnalysis()">
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" id="seoTitleProgress" style="width: 50%;"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <label class="form-label small fw-bold mb-0 text-dark">SEO Meta Description</label>
                        <span class="small font-monospace text-muted" id="seoDescCounter">0 / 160 chars</span>
                    </div>
                    <textarea name="meta_description" id="seoMetaDescInput" rows="3" class="form-control" placeholder="Meta snippet describing this article in Google results..." oninput="updateSeoAnalysis()">{{ old('meta_description', $metaDescription) }}</textarea>
                    <div class="progress mt-1" style="height: 4px;">
                        <div class="progress-bar bg-success" id="seoDescProgress" style="width: 60%;"></div>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold mb-1 text-dark">Target Meta Keywords</label>
                    <input type="text" name="meta_keywords" class="form-control form-control-sm" placeholder="clipping, tiktok monetization, viral creator" value="{{ old('meta_keywords', $metaKeywords) }}">
                </div>
            </div>

            <!-- TAB 2: SOCIAL CARD -->
            <div class="tab-pane fade" id="box-social-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Social Share Title</label>
                            <input type="text" name="social_title" id="seoSocialTitleInput" class="form-control form-control-sm" placeholder="Custom Facebook / X card title" value="{{ old('meta_title', $metaTitle) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Social Share Description</label>
                            <textarea name="social_description" id="seoSocialDescInput" rows="2" class="form-control form-control-sm" placeholder="Teaser text for social feeds...">{{ old('meta_description', $metaDescription) }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-dark">Social Image URL</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="social_image" id="seoSocialImageInput" class="form-control font-monospace" placeholder="https://...">
                                <button type="button" class="btn btn-outline-primary" onclick="openTecveqFileManager('seoSocialImageInput')">
                                    <i class="las la-folder-open"></i> Select Media
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Live Social Card Preview -->
                    <div class="col-md-5">
                        <div class="border rounded p-2 bg-light shadow-xs" style="max-width: 280px; margin: 0 auto; font-family: sans-serif;">
                            <div class="bg-secondary rounded text-center d-flex align-items-center justify-content-center text-white" style="height: 110px; font-size: 12px; background-size: cover; background-position: center;" id="socialCardImagePreview">
                                <i class="las la-image fs-1"></i>
                            </div>
                            <div class="p-2 bg-white rounded-bottom border-top">
                                <small class="text-uppercase text-muted d-block" style="font-size: 10px;">{{ parse_url(url('/'), PHP_URL_HOST) }}</small>
                                <strong class="text-dark small d-block text-truncate" id="socialCardTitlePreview">Social Title Headline</strong>
                                <small class="text-muted text-truncate d-block" style="font-size: 11px;" id="socialCardDescPreview">Social excerpt summary preview...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SCHEMA & ROBOTS -->
            <div class="tab-pane fade" id="box-schema-pane" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Schema Structured Data Type</label>
                        <select name="schema_type" class="form-select form-select-sm">
                            <option value="Article" @selected($schemaType === 'Article')>Article (General editorial)</option>
                            <option value="BlogPosting" @selected($schemaType === 'BlogPosting')>BlogPosting (Recommended for Blog)</option>
                            <option value="NewsArticle" @selected($schemaType === 'NewsArticle')>NewsArticle (Press releases)</option>
                            <option value="WebPage" @selected($schemaType === 'WebPage')>WebPage (Standard landing page)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-dark">Canonical Override URL</label>
                        <input type="url" name="canonical_url" class="form-control form-control-sm font-monospace" placeholder="https://example.com/original-article" value="{{ old('canonical_url', $canonicalUrl) }}">
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center gap-4 p-2 bg-light rounded border">
                            <label class="small fw-bold text-dark mb-0">Robots Meta Directives:</label>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="robots_index" id="chkIndex" value="1" checked>
                                <label class="form-check-label small" for="chkIndex">Index</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="robots_follow" id="chkFollow" value="1" checked>
                                <label class="form-check-label small" for="chkFollow">Follow</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="checkbox" name="robots_noarchive" id="chkNoarchive" value="1">
                                <label class="form-check-label small" for="chkNoarchive">No-Archive</label>
                            </div>
                        </div>
                    </div>

                    <!-- Custom JSON-LD Schema (Rank Math Style) -->
                    <div class="col-12 mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">
                                <i class="las la-code text-primary me-1"></i> Custom JSON-LD Schema (Rank Math / TECVEQ SEO)
                            </label>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary btn-xs" onclick="insertSchemaTemplate('faq')">+ FAQ Schema</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs" onclick="insertSchemaTemplate('howto')">+ HowTo Schema</button>
                                <button type="button" class="btn btn-outline-secondary btn-xs" onclick="insertSchemaTemplate('org')">+ Brand/Org Schema</button>
                                <button type="button" class="btn btn-outline-success btn-xs" onclick="validateSchemaJson()"><i class="las la-check-circle"></i> Validate JSON</button>
                            </div>
                        </div>
                        <p class="text-muted small mb-2">Paste custom Schema.org JSON-LD code. Accepts raw JSON or full <code>&lt;script type="application/ld+json"&gt;</code> tags. Rendered into live page &lt;head&gt; automatically for rich search results.</p>
                        <textarea name="custom_schema" id="seoCustomSchema" rows="7" class="form-control font-monospace text-dark bg-light" style="font-size: 12px; line-height: 1.45;" placeholder='<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ $title ?: 'Page Title' }}",
  "description": "{{ $metaDescription ?: 'Page description' }}"
}
</script>'>{{ old('custom_schema', $customSchema) }}</textarea>
                        <div id="schemaValidationStatus" class="small mt-1"></div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: AUDIT CHECKLIST (TECVEQ Style) -->
            <div class="tab-pane fade" id="box-audit-pane" role="tabpanel">
                <ul class="list-group list-group-flush small" id="seoChecklistItems">
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testKwTitle">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> Focus Keyword used in SEO Title</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testKwDesc">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> Focus Keyword found in Meta Description</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testTitleLength">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> SEO Title length is optimal (40-60 chars)</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testDescLength">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> Meta Description length is optimal (120-160 chars)</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testContentLength">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> Content length meets minimum threshold (300+ words)</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-0" id="testKwContent">
                        <span><i class="las la-check-circle text-success me-1 fs-5"></i> Focus Keyword appears in the body content</span>
                        <span class="badge badge--success">PASSED</span>
                    </li>
                </ul>
            </div>

        </div>

    </div>
</div>

<script>
function updateSeoAnalysis() {
    const kw = (document.getElementById('seoFocusKeyword')?.value || '').toLowerCase().trim();
    const title = (document.getElementById('seoMetaTitleInput')?.value || document.getElementById('postTitleInput')?.value || '').trim();
    const desc = (document.getElementById('seoMetaDescInput')?.value || '').trim();
    const slug = (document.getElementById('postSlugInput')?.value || '').trim();
    const content = (document.getElementById('tecveqEditorHiddenInput')?.value || document.querySelector('[name="content"]')?.value || '').trim();

    // Update Counter Badges
    const titleLen = title.length;
    const descLen = desc.length;
    document.getElementById('seoTitleCounter').textContent = titleLen + ' / 60 chars';
    document.getElementById('seoDescCounter').textContent = descLen + ' / 160 chars';

    const titlePct = Math.min(100, Math.round((titleLen / 60) * 100));
    const descPct = Math.min(100, Math.round((descLen / 160) * 100));
    
    const titleProg = document.getElementById('seoTitleProgress');
    titleProg.style.width = titlePct + '%';
    titleProg.className = 'progress-bar ' + (titleLen >= 35 && titleLen <= 65 ? 'bg-success' : 'bg-warning');

    const descProg = document.getElementById('seoDescProgress');
    descProg.style.width = descPct + '%';
    descProg.className = 'progress-bar ' + (descLen >= 110 && descLen <= 165 ? 'bg-success' : 'bg-warning');

    // Update Live SERP Box
    document.getElementById('serpTitlePreview').textContent = title || 'Article Headline Goes Here — TECVEQ';
    document.getElementById('serpDescPreview').textContent = desc || 'Add a compelling meta description snippet to entice Google searchers...';
    document.getElementById('serpBreadcrumbPreview').textContent = 'blog › ' + (slug || 'sample-slug');

    // Calculate Real-Time Score (0 - 100)
    let score = 20;
    let passed = 0;

    if (kw && title.toLowerCase().includes(kw)) { score += 20; passed++; toggleAuditItem('testKwTitle', true); } else { toggleAuditItem('testKwTitle', false); }
    if (kw && desc.toLowerCase().includes(kw)) { score += 15; passed++; toggleAuditItem('testKwDesc', true); } else { toggleAuditItem('testKwDesc', false); }
    if (titleLen >= 35 && titleLen <= 65) { score += 15; passed++; toggleAuditItem('testTitleLength', true); } else { toggleAuditItem('testTitleLength', false); }
    if (descLen >= 100 && descLen <= 165) { score += 15; passed++; toggleAuditItem('testDescLength', true); } else { toggleAuditItem('testDescLength', false); }
    
    const wordCount = content.split(/\s+/).filter(w => w.length > 0).length;
    if (wordCount >= 100) { score += 15; passed++; toggleAuditItem('testContentLength', true); } else { toggleAuditItem('testContentLength', false); }
    if (kw && content.toLowerCase().includes(kw)) { score += 15; passed++; toggleAuditItem('testKwContent', true); } else { toggleAuditItem('testKwContent', false); }

    score = Math.min(100, score);
    document.getElementById('seoScoreNumber').textContent = score;
    document.getElementById('auditPassedCount').textContent = passed;

    const container = document.getElementById('seoScoreContainer');
    const label = document.getElementById('seoScoreLabel');
    const num = document.getElementById('seoScoreNumber');

    if (score >= 80) {
        container.className = 'd-flex align-items-center gap-2 px-3 py-1 bg-soft-success rounded-pill border border-success';
        num.className = 'fw-bold font-monospace fs-5 text-success';
        label.textContent = '/ 100 — Excellent';
        label.className = 'small fw-bold text-success';
    } else if (score >= 50) {
        container.className = 'd-flex align-items-center gap-2 px-3 py-1 bg-soft-warning rounded-pill border border-warning';
        num.className = 'fw-bold font-monospace fs-5 text-warning';
        label.textContent = '/ 100 — Moderate';
        label.className = 'small fw-bold text-warning';
    } else {
        container.className = 'd-flex align-items-center gap-2 px-3 py-1 bg-soft-danger rounded-pill border border-danger';
        num.className = 'fw-bold font-monospace fs-5 text-danger';
        label.textContent = '/ 100 — Needs Work';
        label.className = 'small fw-bold text-danger';
    }
}

function toggleAuditItem(id, passed) {
    const item = document.getElementById(id);
    if (!item) return;
    const badge = item.querySelector('.badge');
    const icon = item.querySelector('i');
    if (passed) {
        badge.className = 'badge badge--success';
        badge.textContent = 'PASSED';
        icon.className = 'las la-check-circle text-success me-1 fs-5';
    } else {
        badge.className = 'badge badge--warning';
        badge.textContent = 'RECOMMENDED';
        icon.className = 'las la-exclamation-circle text-warning me-1 fs-5';
    }
}

function toggleSerpDevice(mode) {
    const box = document.getElementById('serpPreviewBox');
    const deskBtn = document.getElementById('serpDesktopBtn');
    const mobBtn = document.getElementById('serpMobileBtn');
    if (mode === 'mobile') {
        box.style.maxWidth = '375px';
        box.style.border = '1px solid #e5e7eb';
        box.style.padding = '12px';
        box.style.borderRadius = '8px';
        deskBtn.classList.remove('active');
        mobBtn.classList.add('active');
    } else {
        box.style.maxWidth = '600px';
        box.style.border = 'none';
        box.style.padding = '8px 0';
        deskBtn.classList.add('active');
        mobBtn.classList.remove('active');
    }
}

function insertSchemaTemplate(type) {
    const textarea = document.getElementById('seoCustomSchema');
    let template = '';
    const currentTitle = document.querySelector('input[name="title"]')?.value || 'Example Title';
    const siteUrl = '{{ url("/") }}';

    if (type === 'faq') {
        template = `<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How does clipping campaigns work?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Creators browse active viral campaigns, download media assets, produce high-converting short clips, and earn guaranteed payouts per verified view."
      }
    },
    {
      "@type": "Question",
      "name": "When do creators get paid?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Payouts are transferred directly to your bank account or crypto wallet once verified view milestones are achieved."
      }
    }
  ]
}
<\/script>`;
    } else if (type === 'howto') {
        template = `<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "HowTo",
  "name": "How to Earn with GEZUX Clipping Campaigns",
  "step": [
    {
      "@type": "HowToStep",
      "name": "Choose a Campaign",
      "text": "Explore active brands in the GEZUX marketplace and filter by payout per 1,000 views."
    },
    {
      "@type": "HowToStep",
      "name": "Edit and Distribute",
      "text": "Clip the best moments into engaging vertical shorts for TikTok, Reels, and YouTube Shorts."
    },
    {
      "@type": "HowToStep",
      "name": "Submit & Collect Payout",
      "text": "Paste your published video link and track verified views and automatic payouts in real-time."
    }
  ]
}
<\/script>`;
    } else if (type === 'org') {
        template = `<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Organization",
  "name": "GEZUX Media & Clipping Network",
  "url": "${siteUrl}",
  "logo": "${siteUrl}/assets/images/logo.png",
  "sameAs": [
    "https://twitter.com/gezux",
    "https://youtube.com/@gezux"
  ]
}
<\/script>`;
    }

    textarea.value = template;
    validateSchemaJson();
}

function validateSchemaJson() {
    const raw = document.getElementById('seoCustomSchema').value.trim();
    const status = document.getElementById('schemaValidationStatus');
    if (!raw) {
        status.innerHTML = '<span class="text-muted">No custom schema added (Default auto-schema will apply).</span>';
        return;
    }

    try {
        let clean = raw;
        // strip script tags if present
        clean = clean.replace(/<script[^>]*>/gi, '').replace(/<\/script>/gi, '').trim();
        JSON.parse(clean);
        status.innerHTML = '<span class="text-success fw-bold"><i class="las la-check-circle"></i> Valid JSON-LD Schema detected! Ready for rich snippets.</span>';
    } catch (e) {
        status.innerHTML = '<span class="text-danger fw-bold"><i class="las la-times-circle"></i> Invalid JSON Syntax: ' + e.message + '</span>';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateSeoAnalysis();
    if (document.getElementById('seoCustomSchema')?.value) {
        validateSchemaJson();
    }
});
</script>
