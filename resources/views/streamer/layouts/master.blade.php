<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Streamer Portal' }} — GEZUX</title>

    <link rel="shortcut icon" type="image/jpeg" href="{{ siteFavicon() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Core CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <!-- CDN Fallback for Line Awesome & Font Awesome to ensure all brand icons render properly -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

    <style>
        :root {
            --gezux-streamer: #ef4444;
            --gezux-streamer-hover: #dc2626;
            --gezux-streamer-gradient: linear-gradient(135deg, #ef4444, #f43f5e, #be123c);
            --gezux-dark: #0b0b10;
            --gezux-surface: #121218;
            --gezux-border: rgba(255, 255, 255, 0.08);
        }
        .bg--primary { background-color: #ef4444 !important; color: #fff !important; }
        .bg--secondary { background-color: #64748b !important; color: #fff !important; }
        .bg--success { background-color: #10b981 !important; color: #fff !important; }
        .bg--danger { background-color: #ef4444 !important; color: #fff !important; }
        .bg--warning { background-color: #f59e0b !important; color: #fff !important; }
        .bg--info { background-color: #06b6d4 !important; color: #fff !important; }
        .bg--dark { background-color: #0b0b10 !important; color: #fff !important; }
        .bg--white { background-color: #ffffff !important; }

        body {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* HYIPLab Standardized Form Controls (40px) */
        .form-control, .form-select {
            height: 40px !important;
            min-height: 40px !important;
            border-radius: 5px !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 13.5px !important;
            padding: 0.4rem 0.8rem !important;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.15) !important;
            outline: 0 !important;
        }
        textarea.form-control {
            height: auto !important;
            min-height: 90px !important;
        }
        .input-group .form-control, .input-group .form-select {
            height: 40px !important;
        }
        .input-group .input-group-text {
            height: 40px !important;
            background-color: #f8fafc;
            border-color: #e2e8f0;
            font-size: 13.5px;
            color: #64748b;
            border-radius: 5px !important;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        /* HYIPLab Standardized Streamer Buttons */
        .btn-streamer {
            height: 38px;
            min-height: 38px;
            padding: 0 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-weight: 600;
            font-size: 13.5px;
            gap: 6px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-streamer:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
        }
        .btn-streamer.btn-lg, .btn-streamer.btn--lg {
            height: 46px !important;
            min-height: 46px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            border-radius: 6px !important;
        }
        .btn-streamer.btn-sm, .btn-streamer.btn--sm {
            height: 32px !important;
            min-height: 32px !important;
            padding: 0 12px !important;
            font-size: 12.5px !important;
            border-radius: 5px !important;
            font-weight: 500 !important;
        }
        .btn-sm, .table td .btn-sm, .table td .btn {
            height: 32px !important;
            min-height: 32px !important;
            padding: 0 12px !important;
            font-size: 12.5px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 5px !important;
            line-height: 1 !important;
        }

        /* Stat Card */
        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.06);
            border-color: #cbd5e1;
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
            flex-shrink: 0;
        }

        /* Table */
        .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 700;
            font-size: 11.5px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0 !important;
            border-top: none;
            padding: 12px 18px;
        }
        .table td {
            padding: 14px 18px;
            vertical-align: middle;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Cards & Structural Wrappers */
        .card {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            background: #ffffff;
            transition: all 0.2s ease;
        }
        .page-wrapper {
            position: relative;
            min-height: 100vh;
        }
        .sidebar {
            background-color: #0b0b10 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1020;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        .navbar-wrapper {
            background-color: #0b0b10 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 14px 28px;
            margin-left: 260px;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .body-wrapper {
            margin-left: 260px;
            min-height: calc(100vh - 70px);
            padding: 28px;
            background-color: #f8fafc;
        }
        .navbar-wrapper, .body-wrapper, .sidebar {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%) !important;
        }

        body.sidebar-collapsed .navbar-wrapper,
        body.sidebar-collapsed .body-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
        }

        .sidebar-collapse-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            font-size: 18px;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .sidebar-collapse-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Streamer Hub Stat Card System */
        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 22px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            height: 100%;
            min-height: 110px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06), 0 3px 6px rgba(0, 0, 0, 0.04);
            border-color: #cbd5e1;
        }

        .stat-card-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 26px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        @media (max-width: 575px) {
            .stat-card {
                padding: 16px 18px;
                min-height: 96px;
            }
            .stat-card-icon {
                width: 44px;
                height: 44px;
                min-width: 44px;
                font-size: 22px;
                border-radius: 10px;
            }
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
            z-index: 1015;
            display: none;
        }

        .sidebar-overlay.active,
        .sidebar-overlay.open {
            display: block;
        }

        @media (max-width: 991px) {
            .sidebar {
                left: -290px !important;
                transform: none !important;
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                z-index: 1040 !important;
            }
            .sidebar.active,
            .sidebar.open {
                left: 0 !important;
                transform: none !important;
            }
            .navbar-wrapper, .body-wrapper {
                margin-left: 0 !important;
            }
        }
        .sidebar__logo {
            padding: 22px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar__logo-text {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
        }
        .sidebar__logo-badge {
            font-size: 11px;
            font-weight: 700;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .sidebar__menu {
            list-style: none;
            padding: 0 10px 30px;
            margin: 0;
        }
        .sidebar__menu .sidebar-menu-item {
            margin-bottom: 3px;
        }
        .sidebar__menu .sidebar-menu-item a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 14px;
            border-radius: 8px;
            color: #94a3b8;
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .sidebar__menu .sidebar-menu-item a:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .sidebar__menu .sidebar-menu-item.active > a {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(220, 38, 38, 0.25));
            border: 1px solid rgba(239, 68, 68, 0.45);
            font-weight: 600;
        }
        .sidebar__menu .sidebar-menu-item a .menu-icon {
            font-size: 19px;
            color: #64748b;
            min-width: 20px;
            text-align: center;
        }
        .sidebar__menu .sidebar-menu-item.active a .menu-icon {
            color: #f87171;
        }
        .sidebar__menu-header {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #475569;
            padding: 16px 14px 6px;
            text-transform: uppercase;
        }
        .menu-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: 20px;
            text-align: center;
            line-height: 1.3;
        }
        .res-sidebar-close-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
            display: none;
        }
        @media (max-width: 991px) {
            .res-sidebar-close-btn {
                display: block;
            }
        }
        .version-info {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            font-family: 'JetBrains Mono', monospace;
        }
        .badge-status {
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-approved { background: #d1fae5; color: #047857; border: 1px solid #a7f3d0; }
        .badge-paid { background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-rejected { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-active { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    </style>
    @stack('style')
</head>
<body>

    @if(session()->has('impersonated_by'))
        <div class="bg-warning text-dark py-2 px-4 d-flex align-items-center justify-content-between position-relative" style="z-index: 9999; font-weight: 600;">
            <div class="d-flex align-items-center gap-2">
                <i class="las la-exclamation-triangle fs-4"></i>
                <span>You are currently viewing as <strong>{{ Auth::user()->name }}</strong> (Streamer ID: {{ Auth::id() }}).</span>
            </div>
            <a href="{{ route('admin.impersonate.leave') }}" class="btn btn-dark btn-sm fw-bold">
                <i class="las la-sign-out-alt"></i> Exit Impersonation & Return to Admin
            </a>
        </div>
    @endif

    <div class="page-wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay"></div>

        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
            <div class="sidebar__logo">
                <a href="{{ route('streamer.dashboard') }}" class="sidebar__main-logo d-flex align-items-center text-decoration-none">
                    <span class="sidebar__logo-text">GEZUX</span>
                    <span class="sidebar__logo-badge ms-2"><i class="fab fa-youtube me-1"></i> STREAMER</span>
                </a>
            </div>

            <div class="sidebar__inner">
                <ul class="sidebar__menu">
                    <li class="sidebar__menu-header">Command Center</li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('streamer.dashboard') }}">
                            <i class="menu-icon las la-tv"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar__menu-header">Brand Sponsorships</li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.campaigns*') ? 'active' : '' }}">
                        <a href="{{ route('streamer.campaigns') }}">
                            <i class="menu-icon las la-bullhorn"></i>
                            <span class="menu-title">Sponsorship Deals</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.submissions.create') ? 'active' : '' }}">
                        <a href="{{ route('streamer.submissions.create') }}">
                            <i class="menu-icon las la-upload"></i>
                            <span class="menu-title">Submit YouTube Video</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.submissions') && !request()->routeIs('streamer.submissions.create') ? 'active' : '' }}">
                        <a href="{{ route('streamer.submissions') }}">
                            <i class="menu-icon fab fa-youtube"></i>
                            <span class="menu-title">My Video Submissions</span>
                        </a>
                    </li>

                    <li class="sidebar__menu-header">Finance &amp; Payouts</li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.wallet') ? 'active' : '' }}">
                        <a href="{{ route('streamer.wallet') }}">
                            <i class="menu-icon las la-wallet"></i>
                            <span class="menu-title">Wallet &amp; Earnings</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.payout-settings') ? 'active' : '' }}">
                        <a href="{{ route('streamer.payout-settings') }}">
                            <i class="menu-icon las la-university"></i>
                            <span class="menu-title">Bank Payout Settings</span>
                        </a>
                    </li>

                    <li class="sidebar__menu-header">Channel &amp; Profile</li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.channels') ? 'active' : '' }}">
                        <a href="{{ route('streamer.channels') }}">
                            <i class="menu-icon las la-play-circle"></i>
                            <span class="menu-title">YouTube Channel</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.profile') ? 'active' : '' }}">
                        <a href="{{ route('streamer.profile') }}">
                            <i class="menu-icon las la-user-circle"></i>
                            <span class="menu-title">Streamer Profile</span>
                        </a>
                    </li>
                    <li class="sidebar-menu-item {{ request()->routeIs('streamer.password') ? 'active' : '' }}">
                        <a href="{{ route('streamer.password') }}">
                            <i class="menu-icon las la-key"></i>
                            <span class="menu-title">Security &amp; Password</span>
                        </a>
                    </li>
                </ul>

                <div class="version-info">
                    <span class="d-block text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">GEZUX Streamer Hub</span>
                    <span class="text-white-50 font-monospace small">v4.0.0-PRO</span>
                </div>
            </div>
        </aside>

        <!-- Top Navbar -->
        <nav class="navbar-wrapper">
            <div class="d-flex align-items-center gap-3">
                <button class="res-sidebar-open-btn d-lg-none" type="button" title="Open Navigation" style="background: transparent; border: none; color: #fff; font-size: 22px; padding: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <i class="las la-bars"></i>
                </button>
                <button class="sidebar-collapse-btn d-none d-lg-inline-flex" type="button" title="Toggle Navigation">
                    <i class="las la-bars"></i>
                </button>
                <div class="d-none d-md-block">
                    <h5 class="text-white mb-0 fw-bold" style="font-size: 16px;">{{ $pageTitle ?? 'Streamer Command Center' }}</h5>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Available Balance Quick Badge -->
                <a href="{{ route('streamer.wallet') }}" class="btn btn-sm btn-dark border border-secondary text-white d-flex align-items-center gap-2 px-3" style="background: rgba(255,255,255,0.06); height: 38px;">
                    <i class="las la-wallet text-danger fs-5"></i>
                    <span class="font-monospace fw-bold text-success">${{ number_format(Auth::user()->wallet->available_balance ?? 0.00, 2) }}</span>
                    <span class="small text-muted d-none d-sm-inline">USD</span>
                </a>

                <!-- Quick Submit YouTube Button -->
                <a href="{{ route('streamer.submissions.create') }}" class="btn btn-sm btn-streamer text-white d-none d-sm-inline-flex" style="height: 38px;">
                    <i class="las la-plus"></i> Submit Video
                </a>

                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-dark dropdown-toggle d-flex align-items-center gap-2 border-0 bg-transparent text-white" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 13px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span class="d-none d-md-inline fw-semibold small">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="min-width: 200px; border-radius: 10px;">
                        <li>
                            <div class="px-3 py-2 border-bottom mb-2">
                                <span class="d-block fw-bold small text-dark">{{ Auth::user()->name }}</span>
                                <span class="d-block text-muted small text-truncate">{{ Auth::user()->email }}</span>
                                <span class="badge bg-danger mt-1">STREAMER</span>
                            </div>
                        </li>
                        <li><a class="dropdown-item py-2 rounded" href="{{ route('streamer.profile') }}"><i class="las la-user-circle me-2 text-danger"></i> Profile Settings</a></li>
                        <li><a class="dropdown-item py-2 rounded" href="{{ route('streamer.wallet') }}"><i class="las la-wallet me-2 text-success"></i> Wallet &amp; Payouts</a></li>
                        <li><a class="dropdown-item py-2 rounded" href="{{ route('streamer.channels') }}"><i class="fab fa-youtube me-2 text-danger"></i> YouTube Channel</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('web.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 rounded text-danger">
                                    <i class="las la-sign-out-alt me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Body -->
        <main class="body-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="las la-check-circle me-2 fs-5"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="las la-exclamation-circle me-2 fs-5"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Sidebar Toggle
        $(document).on('click', '.res-sidebar-open-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.sidebar').addClass('active open');
            $('.sidebar-overlay').addClass('active open');
        });

        $(document).on('click', '.sidebar-collapse-btn', function(e) {
            e.preventDefault();
            if ($(window).width() > 991) {
                $('body').toggleClass('sidebar-collapsed');
            }
        });

        $(document).on('click', '.res-sidebar-close-btn, .sidebar-overlay', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.sidebar').removeClass('active open');
            $('.sidebar-overlay').removeClass('active open');
        });
    </script>
    @stack('script')
</body>
</html>
