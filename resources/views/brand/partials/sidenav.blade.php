@php
    $brandId = \App\Models\Brand::where('user_id', Auth::id())->value('id');
    $brandCampaignIds = $brandId ? \App\Models\Campaign::where('brand_id', $brandId)->pluck('id') : collect();
    $pendingClipsCount = $brandCampaignIds->isNotEmpty() 
        ? \App\Models\ClipSubmission::whereIn('campaign_id', $brandCampaignIds)->whereIn('status', ['pending', 'under_review'])->count()
        : 0;
@endphp

<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        
        <!-- GEZUX Sidebar Brand Logo & Desktop Collapse Button -->
        <div class="sidebar__logo d-flex align-items-center justify-content-between">
            <a href="{{ safeRoute('brand.dashboard', [], '/brand') }}" class="d-flex align-items-center text-decoration-none gap-2">
                <img src="{{ siteLogo() }}" alt="GEZUX" style="width: 32px; height: 32px; border-radius: 8px; object-fit: cover;">
                <span class="sidebar__logo-text">GEZUX</span>
                <span class="sidebar__logo-badge">BRAND</span>
            </a>
            <button type="button" class="sidebar-collapse-btn d-none d-lg-flex" title="Toggle Sidebar">
                <i class="las la-angle-double-left text-muted fs-5"></i>
            </button>
        </div>

        <div class="sidebar__menu-wrapper">
            <ul class="sidebar__menu">

                <!-- OVERVIEW -->
                <li class="sidebar__menu-header">OVERVIEW</li>
                <li class="sidebar-menu-item {{ menuActive('brand.dashboard') }}">
                    <a href="{{ safeRoute('brand.dashboard', [], '/brand') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">Command Center</span>
                    </a>
                </li>

                <!-- CAMPAIGN STUDIO -->
                <li class="sidebar__menu-header">CAMPAIGN STUDIO</li>
                <li class="sidebar-menu-item {{ menuActive('brand.campaigns') }}">
                    <a href="{{ safeRoute('brand.campaigns', [], '/brand/campaigns') }}" class="nav-link">
                        <i class="menu-icon las la-bullhorn"></i>
                        <span class="menu-title">My Campaigns</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('brand.campaigns.create') }}">
                    <a href="{{ safeRoute('brand.campaigns.create', [], '/brand/campaigns/create') }}" class="nav-link">
                        <i class="menu-icon las la-plus-circle"></i>
                        <span class="menu-title">Launch Campaign</span>
                    </a>
                </li>

                <!-- CONTENT & CLIPS -->
                <li class="sidebar__menu-header">CONTENT & CLIPS</li>
                <li class="sidebar-menu-item {{ menuActive('brand.clips*') }}">
                    <a href="{{ safeRoute('brand.clips', [], '/brand/clips') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-video"></i>
                        <span class="menu-title">Video Submissions</span>
                        @if($pendingClipsCount > 0)
                            <span class="menu-badge bg--warning ms-auto">{{ $pendingClipsCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- PERFORMANCE -->
                <li class="sidebar__menu-header">PERFORMANCE & METRICS</li>
                <li class="sidebar-menu-item {{ menuActive('brand.analytics*') }}">
                    <a href="{{ safeRoute('brand.analytics', [], '/brand/analytics') }}" class="nav-link">
                        <i class="menu-icon las la-chart-bar"></i>
                        <span class="menu-title">Analytics & Views</span>
                    </a>
                </li>

                <!-- BILLING & BUDGETS -->
                <li class="sidebar__menu-header">BILLING & BUDGETS</li>
                <li class="sidebar-menu-item {{ menuActive('brand.finance*') }}">
                    <a href="{{ safeRoute('brand.finance', [], '/brand/finance') }}" class="nav-link">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">Billing & Budgets</span>
                    </a>
                </li>

                <!-- ORGANIZATION SETTINGS -->
                <li class="sidebar__menu-header">ACCOUNT SETTINGS</li>
                <li class="sidebar-menu-item {{ menuActive('brand.profile*') }}">
                    <a href="{{ safeRoute('brand.profile', [], '/brand/profile') }}" class="nav-link">
                        <i class="menu-icon las la-id-badge"></i>
                        <span class="menu-title">Brand Profile</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->is('brand/password*') ? 'active' : '' }}">
                    <a href="{{ safeRoute('brand.password', [], '/brand/profile#password') }}" class="nav-link">
                        <i class="menu-icon las la-key text-warning"></i>
                        <span class="menu-title">Change Password</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Footer Version Info -->
        <div class="version-info text-center text-uppercase">
            <span class="text--primary fw-bold">GEZUX BRAND</span>
            <span class="text--success ms-1">v1.3.0</span>
        </div>

    </div>
</div>
