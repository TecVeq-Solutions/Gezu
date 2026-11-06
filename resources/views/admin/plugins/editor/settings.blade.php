@extends('admin.layouts.app')

@section('panel')
<div class="row gy-4">

    <!-- Top Banner -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; background: linear-gradient(135deg, #8B5CF6, #6D28D9);">
                        <i class="las la-edit fs-2"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h4 class="mb-0 text-dark fw-bold">TECVEQ Rich Editor</h4>
                            <span class="badge badge--primary font-monospace" style="background: #7C3AED;">v24.2.0 ACTIVE</span>
                            <span class="badge bg-light text-dark border">Block Studio Engine</span>
                        </div>
                        <p class="text-muted mb-0">Visual block editor with real-time formatting, markdown shortcuts, file manager media insertion, and source code mode.</p>
                    </div>
                </div>
                <div>
                    <button type="submit" form="editorSettingsForm" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="las la-save me-1"></i> Save Editor Preferences
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <div class="col-12">
        <div class="card b-radius--10 border shadow-sm">
            <div class="card-body p-4">
                <form id="editorSettingsForm" action="{{ route('admin.plugins.editor.settings') }}" method="POST">
                    @csrf
                    <div class="row g-4">

                        <div class="col-md-6">
                            <div class="card border p-3 h-100">
                                <label class="form-label fw-bold text-dark">Default Editor Mode</label>
                                <select name="editor_default_mode" class="form-select">
                                    <option value="visual" @selected($settings['editor_default_mode'] === 'visual')>Visual Block Mode (WYSIWYG - Recommended)</option>
                                    <option value="source" @selected($settings['editor_default_mode'] === 'source')>HTML / Source Code Mode</option>
                                </select>
                                <small class="text-muted mt-2 d-block">Choose which mode opens by default when creating or editing blog posts and CMS pages.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border p-3 h-100">
                                <label class="form-label fw-bold text-dark">Auto-Save Draft Interval (Seconds)</label>
                                <select name="editor_auto_save_seconds" class="form-select">
                                    <option value="15" @selected($settings['editor_auto_save_seconds'] == 15)>Every 15 Seconds</option>
                                    <option value="30" @selected($settings['editor_auto_save_seconds'] == 30)>Every 30 Seconds (Recommended)</option>
                                    <option value="60" @selected($settings['editor_auto_save_seconds'] == 60)>Every 1 Minute</option>
                                    <option value="0" @selected($settings['editor_auto_save_seconds'] == 0)>Disabled</option>
                                </select>
                                <small class="text-muted mt-2 d-block">Automatically saves drafts into browser LocalStorage to protect against unexpected closures.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border p-3 h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fw-bold mb-0 text-dark">Browser Spellcheck Integration</h6>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="editor_spellcheck_enabled" value="1" {{ $settings['editor_spellcheck_enabled'] ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <p class="text-muted small mb-0">Enables native spell-checking and grammar underline inside the visual editor blocks.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card border p-3 h-100">
                                <label class="form-label fw-bold text-dark">Typography Font Scale</label>
                                <select name="editor_typography_scale" class="form-select">
                                    <option value="modern" @selected($settings['editor_typography_scale'] === 'modern')>Modern Clean (Inter / Outfit 16px Base)</option>
                                    <option value="editorial" @selected($settings['editor_typography_scale'] === 'editorial')>Editorial Magazine (Merriweather / Serif 18px Base)</option>
                                    <option value="compact" @selected($settings['editor_typography_scale'] === 'compact')>Compact Tech (System UI 14px Base)</option>
                                </select>
                                <small class="text-muted mt-2 d-block">Defines the visual font rendering scale inside the content composing viewport.</small>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
