@php
    $pendingClipsCount = \App\Models\ClipSubmission::whereIn('status', ['pending', 'under_review'])->count();
    $pendingPayoutsCount = \App\Models\Payout::where('status', 'PENDING_ADMIN_REVIEW')->count();
    $pendingCampaignsCount = \App\Models\Campaign::where('status', 'draft')->count();
    $errorLogsCount = 0;
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('error_logs')) {
            $errorLogsCount = \App\Models\ErrorLog::count();
        }
    } catch (\Throwable $e) {}
@endphp

<div class="sidebar bg--dark">
    <button type="button" class="res-sidebar-close-btn" title="Close Sidebar"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        
        <!-- Sidebar Brand Logo & Desktop Collapse Button -->
        <div class="sidebar__logo d-flex align-items-center justify-content-between">
            <a href="{{ safeRoute('admin.dashboard', [], '/admin') }}" class="d-flex align-items-center text-decoration-none gap-2">
                <img src="{{ siteLogo() }}" alt="TECVEQ" style="width: 34px; height: 34px; border-radius: 8px; object-fit: cover;">
                <span class="sidebar__logo-text">TECVEQ</span>
                <span class="sidebar__logo-badge">PRO</span>
            </a>
            <button type="button" class="sidebar-collapse-btn d-none d-lg-flex" title="Toggle Sidebar">
                <i class="las la-angle-double-left text-muted fs-5"></i>
            </button>
        </div>

        <div class="sidebar__menu-wrapper">
            <ul class="sidebar__menu">

                <!-- 1. CORE -->
                <li class="sidebar__menu-header">CORE TELEMETRY</li>
                <li class="sidebar-menu-item {{ menuActive('admin.dashboard') }}">
                    <a href="{{ safeRoute('admin.dashboard', [], '/admin') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </li>

                <!-- 2. CAMPAIGNS & CLIPS -->
                <li class="sidebar__menu-header">CAMPAIGNS & BOUNTIES</li>
                <li class="sidebar-menu-item {{ menuActive('admin.campaigns*') }}">
                    <a href="{{ safeRoute('admin.campaigns', [], '/admin/campaigns') }}" class="nav-link">
                        <i class="menu-icon las la-bullhorn"></i>
                        <span class="menu-title">All Campaigns</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.clips*') }}">
                    <a href="{{ safeRoute('admin.clips', [], '/admin/clips') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-video"></i>
                        <span class="menu-title">Clip Submissions</span>
                        @if($pendingClipsCount > 0)
                            <span class="menu-badge bg--warning ms-auto">{{ $pendingClipsCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- 3. USERS & COMMUNITY -->
                <li class="sidebar__menu-header">USERS & COMMUNITY</li>
                <li class="sidebar-menu-item {{ request()->fullUrl() == route('admin.users') ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.users', [], '/admin/users') }}" class="nav-link">
                        <i class="menu-icon las la-users"></i>
                        <span class="menu-title">All Users</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->get('role') == 'brand' ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.users', ['role' => 'brand'], '/admin/users?role=brand') }}" class="nav-link">
                        <i class="menu-icon las la-user-tie text-primary"></i>
                        <span class="menu-title">Brands (Advertisers)</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->get('role') == 'clipper' ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.users', ['role' => 'clipper'], '/admin/users?role=clipper') }}" class="nav-link">
                        <i class="menu-icon las la-user-ninja text-success"></i>
                        <span class="menu-title">Clippers (Creators)</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->get('role') == 'streamer' ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.users', ['role' => 'streamer'], '/admin/users?role=streamer') }}" class="nav-link">
                        <i class="menu-icon fab fa-youtube text-danger"></i>
                        <span class="menu-title">Streamers</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.notifications.all') }}">
                    <a href="{{ safeRoute('admin.notifications.all', [], '/admin/notifications/send-all') }}" class="nav-link">
                        <i class="menu-icon las la-broadcast-tower text-warning"></i>
                        <span class="menu-title">Send Broadcast</span>
                    </a>
                </li>

                <!-- 4. FINANCE & LEDGER -->
                <li class="sidebar__menu-header">FINANCE & LEDGER</li>
                <li class="sidebar-menu-item {{ menuActive('admin.payouts*') }}">
                    <a href="{{ safeRoute('admin.payouts', [], '/admin/payouts') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-money-check-alt"></i>
                        <span class="menu-title">Clipper Payouts</span>
                        @if($pendingPayoutsCount > 0)
                            <span class="menu-badge bg--danger ms-auto">{{ $pendingPayoutsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.transactions*') }}">
                    <a href="{{ safeRoute('admin.transactions', [], '/admin/transactions') }}" class="nav-link">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">Wallet Ledger</span>
                    </a>
                </li>

                <!-- 5. WEBSITE PAGES & CMS -->
                <li class="sidebar__menu-header">PAGES & CONTENT</li>
                <li class="sidebar-menu-item {{ request()->fullUrl() == route('admin.cms.pages.index') ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.cms.pages.index', [], '/admin/cms/pages') }}" class="nav-link">
                        <i class="menu-icon las la-file-alt"></i>
                        <span class="menu-title">All Website Pages</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->get('type') == 'legal_terms' ? 'active' : '' }}">
                    <a href="{{ safeRoute('admin.cms.pages.index', ['type' => 'legal_terms'], '/admin/cms/pages?type=legal_terms') }}" class="nav-link">
                        <i class="menu-icon las la-balance-scale text-info"></i>
                        <span class="menu-title">Terms & Privacy</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.cms.pages.create') }}">
                    <a href="{{ safeRoute('admin.cms.pages.create', [], '/admin/cms/pages/create') }}" class="nav-link">
                        <i class="menu-icon las la-plus-circle text-success"></i>
                        <span class="menu-title">Create New Page</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.cms.posts*') }}">
                    <a href="{{ safeRoute('admin.cms.posts.index', [], '/admin/cms/posts') }}" class="nav-link">
                        <i class="menu-icon las la-blog"></i>
                        <span class="menu-title">Blog Articles</span>
                    </a>
                </li>

                <!-- 6. GATEWAYS & APIS -->
                <li class="sidebar__menu-header">PAYMENT & GATEWAYS</li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.stripe*') }}">
                    <a href="{{ safeRoute('admin.settings.stripe', [], '/admin/settings/stripe') }}" class="nav-link">
                        <i class="lab la-stripe fs-5 me-1 text-primary"></i>
                        <span class="menu-title">Stripe Gateway</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.social*') }}">
                    <a href="{{ safeRoute('admin.settings.social', [], '/admin/settings/social') }}" class="nav-link">
                        <i class="menu-icon las la-share-alt text-info"></i>
                        <span class="menu-title">Social Platform APIs</span>
                    </a>
                </li>

                <!-- 7. SYSTEM SETTINGS -->
                <li class="sidebar__menu-header">SYSTEM & TOOLS</li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.general*') }}">
                    <a href="{{ safeRoute('admin.settings.general', [], '/admin/settings/general') }}" class="nav-link">
                        <i class="menu-icon las la-cog"></i>
                        <span class="menu-title">General Settings</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.auth*') }}">
                    <a href="{{ safeRoute('admin.settings.auth', [], '/admin/settings/auth') }}" class="nav-link">
                        <i class="menu-icon las la-shield-alt text-warning"></i>
                        <span class="menu-title">Auth & Security (Google)</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive(['admin.extensions*', 'admin.plugins*']) }}">
                    <a href="{{ safeRoute('admin.extensions.index', [], '/admin/extensions') }}" class="nav-link">
                        <i class="menu-icon las la-puzzle-piece text-info"></i>
                        <span class="menu-title">Extensions & Modules</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.password*') }}">
                    <a href="{{ safeRoute('admin.password', [], '/admin/password') }}" class="nav-link">
                        <i class="menu-icon las la-key text-warning"></i>
                        <span class="menu-title">Change Password</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.notifications*') }}">
                    <a href="{{ safeRoute('admin.settings.notifications', [], '/admin/settings/notifications') }}" class="nav-link">
                        <i class="menu-icon las la-envelope"></i>
                        <span class="menu-title">Email & Push Setup</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.plugins.security*') }}">
                    <a href="{{ safeRoute('admin.plugins.security.settings', [], '/admin/plugins/security') }}" class="nav-link">
                        <i class="menu-icon las la-shield-alt text-success"></i>
                        <span class="menu-title">Security Firewall (WAF)</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.updater*') }}">
                    <a href="{{ safeRoute('admin.settings.updater', [], '/admin/settings/updater') }}" class="nav-link">
                        <i class="menu-icon las la-cloud-download-alt text-info"></i>
                        <span class="menu-title">System Updater</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.settings.health*') }}">
                    <a href="{{ safeRoute('admin.settings.health', [], '/admin/settings/health') }}" class="nav-link">
                        <i class="menu-icon las la-heartbeat text-danger"></i>
                        <span class="menu-title">System Health</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('admin.errors*') }}">
                    <a href="{{ safeRoute('admin.errors.index', [], '/admin/error-logs') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-bug text-danger"></i>
                        <span class="menu-title">System Error Logs</span>
                        @if($errorLogsCount > 0)
                            <span class="menu-badge bg--danger ms-auto">{{ $errorLogsCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ url('/clear-cache') }}" class="nav-link text-warning">
                        <i class="menu-icon las la-sync-alt text-warning"></i>
                        <span class="menu-title fw-bold">Clear Cache</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Footer Version Info -->
        <div class="version-info text-center text-uppercase">
            <span class="text--primary fw-bold">TECVEQ CORE</span>
            <span class="text--success ms-1">v1.0.0</span>
        </div>

    </div>
</div>
