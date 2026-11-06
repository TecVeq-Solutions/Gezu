@php
    $userId = Auth::id();
    $pendingSubmissionsCount = \App\Models\ClipSubmission::where('user_id', $userId)
        ->whereIn('status', ['pending', 'under_review'])
        ->count();
    $wallet = \App\Models\Wallet::where('user_id', $userId)->first();
    $hasBankDetails = $wallet && !empty($wallet->payout_bank_name) && !empty($wallet->payout_account_number);
@endphp

<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        
        <!-- GEZUX Sidebar Creator Logo & Desktop Collapse Button -->
        <div class="sidebar__logo d-flex align-items-center justify-content-between">
            <a href="{{ safeRoute('clipper.dashboard', [], '/clipper') }}" class="d-flex align-items-center text-decoration-none gap-2">
                <img src="{{ siteLogo() }}" alt="GEZUX" style="width: 32px; height: 32px; border-radius: 8px; object-fit: cover;">
                <span class="sidebar__logo-text">GEZUX</span>
                <span class="sidebar__logo-badge">CREATOR</span>
            </a>
            <button type="button" class="sidebar-collapse-btn d-none d-lg-flex" title="Toggle Sidebar">
                <i class="las la-angle-double-left text-muted fs-5"></i>
            </button>
        </div>

        <div class="sidebar__menu-wrapper">
            <ul class="sidebar__menu">

                <!-- OVERVIEW -->
                <li class="sidebar__menu-header">OVERVIEW</li>
                <li class="sidebar-menu-item {{ menuActive('clipper.dashboard') }}">
                    <a href="{{ safeRoute('clipper.dashboard', [], '/clipper') }}" class="nav-link">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">Command Center</span>
                    </a>
                </li>

                <!-- CAMPAIGN BOUNTIES -->
                <li class="sidebar__menu-header">EARN & BOUNTIES</li>
                <li class="sidebar-menu-item {{ menuActive('clipper.bounties*') }}">
                    <a href="{{ safeRoute('clipper.bounties', [], '/clipper/bounties') }}" class="nav-link">
                        <i class="menu-icon las la-fire"></i>
                        <span class="menu-title">Browse Bounties</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('clipper.submissions.create') }}">
                    <a href="{{ safeRoute('clipper.submissions.create', [], '/clipper/submissions/create') }}" class="nav-link">
                        <i class="menu-icon las la-cloud-upload-alt"></i>
                        <span class="menu-title">Submit New Clip</span>
                    </a>
                </li>

                <!-- CONTENT TRACKER -->
                <li class="sidebar__menu-header">MY CONTENT</li>
                <li class="sidebar-menu-item {{ menuActive('clipper.submissions') }}">
                    <a href="{{ safeRoute('clipper.submissions', [], '/clipper/submissions') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-video"></i>
                        <span class="menu-title">Clip Submissions</span>
                        @if($pendingSubmissionsCount > 0)
                            <span class="menu-badge bg--warning ms-auto">{{ $pendingSubmissionsCount }}</span>
                        @endif
                    </a>
                </li>

                <!-- FINANCE & BANK PAYOUTS -->
                <li class="sidebar__menu-header">EARNINGS & PAYOUTS</li>
                <li class="sidebar-menu-item {{ menuActive('clipper.wallet*') }}">
                    <a href="{{ safeRoute('clipper.wallet', [], '/clipper/wallet') }}" class="nav-link">
                        <i class="menu-icon las la-wallet"></i>
                        <span class="menu-title">Wallet & Ledger</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('clipper.payout-settings*') }}">
                    <a href="{{ safeRoute('clipper.payout-settings', [], '/clipper/payout-settings') }}" class="nav-link d-flex align-items-center">
                        <i class="menu-icon las la-university"></i>
                        <span class="menu-title">Bank Payout Settings</span>
                        @if(!$hasBankDetails)
                            <span class="menu-badge bg--danger ms-auto" title="Bank Details Missing">Setup</span>
                        @else
                            <span class="menu-badge bg--success ms-auto"><i class="las la-check"></i></span>
                        @endif
                    </a>
                </li>

                <!-- ACCOUNT & SOCIAL CHANNELS -->
                <li class="sidebar__menu-header">CREATOR PROFILE</li>
                <li class="sidebar-menu-item {{ menuActive('clipper.channels*') }}">
                    <a href="{{ safeRoute('clipper.channels', [], '/clipper/channels') }}" class="nav-link">
                        <i class="menu-icon las la-share-alt"></i>
                        <span class="menu-title">Social Channels Hub</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('clipper.profile*') }}">
                    <a href="{{ safeRoute('clipper.profile', [], '/clipper/profile') }}" class="nav-link">
                        <i class="menu-icon las la-user-circle"></i>
                        <span class="menu-title">Account Profile</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->is('clipper/password*') ? 'active' : '' }}">
                    <a href="{{ safeRoute('clipper.password', [], '/clipper/profile#password') }}" class="nav-link">
                        <i class="menu-icon las la-key text-warning"></i>
                        <span class="menu-title">Change Password</span>
                    </a>
                </li>

            </ul>
        </div>

        <!-- Footer Version Info -->
        <div class="version-info text-center text-uppercase">
            <span class="text--success fw-bold">GEZUX CREATOR</span>
            <span class="text--muted ms-1">v1.3.0</span>
        </div>

    </div>
</div>
