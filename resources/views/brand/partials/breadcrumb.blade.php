<div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center mb-4">
    <div>
        <h6 class="page-title text-dark fw-bold mb-1">{{ __($pageTitle ?? 'Command Center') }}</h6>
        <div class="d-flex align-items-center gap-2 small text-muted">
            <a href="{{ route('brand.dashboard') }}" class="text-decoration-none text-muted">Brand</a>
            <span>/</span>
            <span class="text-primary">{{ __($pageTitle ?? 'Command Center') }}</span>
        </div>
    </div>
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
        @stack('breadcrumb-plugins')
    </div>
</div>
