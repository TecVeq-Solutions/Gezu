<div class="d-flex mb-30 flex-wrap gap-3 justify-content-between align-items-center mt-3">
    <h6 class="page-title">{{ __($pageTitle ?? 'Dashboard') }}</h6>
    <div class="d-flex flex-wrap justify-content-end gap-2 align-items-center breadcrumb-plugins">
        @stack('breadcrumb-plugins')
    </div>
</div>
