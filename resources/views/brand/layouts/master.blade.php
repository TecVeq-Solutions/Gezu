<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Brand Portal' }} — GEZUX</title>

    <link rel="shortcut icon" type="image/jpeg" href="{{ siteFavicon() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

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
            --gezux-brand: #6366f1;
            --gezux-brand-hover: #4f46e5;
            --gezux-brand-rgb: 99, 102, 241;
            --gezux-dark: #0b0b10;
            --gezux-dark-rgb: 11, 11, 16;
            --gezux-surface: #121218;
            --gezux-canvas: #f8fafc;
            --gezux-border: #e2e8f0;
            --gezux-border-subtle: #f1f5f9;
            --gezux-text: #0f172a;
            --gezux-text-muted: #64748b;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            background-color: var(--gezux-canvas);
            color: var(--gezux-text);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Standardized Form Discipline (HYIPLab 40px) */
        .form-label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
            display: inline-block;
        }

        .form-control, .form-select {
            height: 40px !important;
            min-height: 40px !important;
            padding: 6px 12px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 5px !important;
            font-size: 13.5px !important;
            color: #0f172a !important;
            background-color: #ffffff !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
            outline: none !important;
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-size: 13.5px;
        }

        textarea.form-control {
            height: auto !important;
            min-height: 90px !important;
            line-height: 1.5 !important;
        }

        .form-control-sm, .form-select-sm {
            height: 32px !important;
            min-height: 32px !important;
            padding: 4px 10px !important;
            font-size: 12.5px !important;
            border-radius: 5px !important;
        }

        /* Input Group Standard Height */
        .input-group {
            display: flex;
            align-items: stretch;
        }

        .input-group .form-control, .input-group .form-select {
            height: 40px !important;
        }

        .input-group .input-group-text {
            height: 40px !important;
            background-color: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            padding: 0 12px !important;
            display: flex;
            align-items: center;
            border-radius: 5px !important;
        }

        .input-group-sm .form-control, .input-group-sm .input-group-text, .input-group-sm .btn {
            height: 32px !important;
            min-height: 32px !important;
        }

        /* Buttons Standard Alignment (HYIPLab Rhythm) */
        .btn {
            font-weight: 600 !important;
            font-size: 13.5px !important;
            border-radius: 5px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
        }

        .btn-brand, .btn-primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
            border: 1px solid #4f46e5 !important;
            color: #ffffff !important;
            box-shadow: 0 1px 3px rgba(99, 102, 241, 0.25) !important;
        }

        .btn-brand:hover, .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35) !important;
            transform: translateY(-1px);
        }

        .btn-sm, .table td .btn-sm, .table td .btn {
            height: 32px !important;
            min-height: 32px !important;
            padding: 0 12px !important;
            font-size: 12.5px !important;
            border-radius: 5px !important;
            line-height: 1 !important;
        }

        .btn-lg, .btn--lg {
            height: 46px !important;
            min-height: 46px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            border-radius: 6px !important;
        }

        .btn-md, .btn-brand:not(.btn-sm):not(.btn-lg), .btn-primary:not(.btn-sm):not(.btn-lg) {
            height: 38px !important;
            min-height: 38px !important;
            padding: 0 18px !important;
        }

        /* Card Container Discipline */
        .card {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            background: #ffffff !important;
            transition: box-shadow 0.2s ease !important;
        }

        .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 16px 22px !important;
        }

        .card-body {
            padding: 22px !important;
        }

        /* Clean SaaS Tables */
        .table {
            margin-bottom: 0 !important;
        }

        .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            border-top: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 16px !important;
            vertical-align: middle !important;
        }

        .table tbody td {
            padding: 13px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #1e293b !important;
            font-size: 13.5px !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* KPI Stat Cards */
        .stat-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            padding: 20px 22px !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05) !important;
            transform: translateY(-1px);
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            color: #ffffff;
        }

        /* Layout & Navigation Shell */
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
            padding: 12px 24px;
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
            min-height: calc(100vh - 66px);
            padding: 24px 28px;
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
            padding: 20px 22px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar__logo-text {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        .sidebar__logo-badge {
            font-size: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: white;
            padding: 2px 7px;
            border-radius: 5px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .sidebar__menu {
            list-style: none;
            padding: 8px 12px 30px;
            margin: 0;
        }

        .sidebar__menu .sidebar-menu-item {
            margin-bottom: 2px;
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
            transition: all 0.15s ease;
        }

        .sidebar__menu .sidebar-menu-item a:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .sidebar__menu .sidebar-menu-item.active > a {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.25), rgba(59, 130, 246, 0.25));
            border: 1px solid rgba(99, 102, 241, 0.45);
            font-weight: 600;
        }

        .sidebar__menu .sidebar-menu-item a .menu-icon {
            font-size: 18px;
            color: #64748b;
            min-width: 20px;
            text-align: center;
        }

        .sidebar__menu .sidebar-menu-item.active a .menu-icon {
            color: #818cf8;
        }

        .sidebar__menu-header {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #475569;
            padding: 14px 14px 6px;
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
            padding: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 11px;
            letter-spacing: 0.8px;
            color: rgba(255, 255, 255, 0.4);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Badges */
        .badge--success { background-color: #10b981 !important; color: #fff !important; }
        .badge--warning { background-color: #f59e0b !important; color: #fff !important; }
        .badge--danger { background-color: #ef4444 !important; color: #fff !important; }
        .badge--primary { background-color: #6366f1 !important; color: #fff !important; }

        .impersonation-alert {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            color: #ffffff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
    @stack('style-lib')
    @stack('style')
</head>
<body>
    @if(session()->has('impersonated_by'))
        <div class="impersonation-alert">
            <div class="d-flex align-items-center gap-2">
                <i class="las la-user-secret fs-5"></i>
                <span>You are currently impersonating <strong>{{ Auth::user()->name }}</strong> (Brand Account).</span>
            </div>
            <a href="{{ route('admin.impersonate.leave') }}" class="btn btn-dark btn-sm fw-bold">
                <i class="las la-sign-out-alt me-1"></i> Exit Impersonation & Return to Admin
            </a>
        </div>
    @endif

    @yield('content')

    <!-- Scripts -->
    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"></script>

    <div class="sidebar-overlay"></div>

    <!-- Collapsible Sidebar Controller & State Persistence -->
    <script>
        (function ($) {
            "use strict";

            // 1. Restore Collapsed Preference
            if (localStorage.getItem('brand_sidebar_collapsed') === '1' && $(window).width() > 991) {
                $('body').addClass('sidebar-collapsed');
            }

            // 2. Collapse Toggle Handler (Desktop & Mobile)
            $(document).on('click', '.res-sidebar-open-btn', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.sidebar').addClass('active open');
                $('.sidebar-overlay').addClass('active open');
            });

            $(document).on('click', '.sidebar-collapse-btn', function (e) {
                e.preventDefault();
                if ($(window).width() > 991) {
                    $('body').toggleClass('sidebar-collapsed');
                    var isCollapsed = $('body').hasClass('sidebar-collapsed');
                    localStorage.setItem('brand_sidebar_collapsed', isCollapsed ? '1' : '0');
                } else {
                    $('.sidebar').toggleClass('active open');
                    $('.sidebar-overlay').toggleClass('active open');
                }
            });

            // 3. Mobile Close Button & Overlay Backdrop
            $(document).on('click', '.res-sidebar-close-btn, .sidebar-overlay', function (e) {
                e.preventDefault();
                e.stopPropagation();
                $('.sidebar').removeClass('active open');
                $('.sidebar-overlay').removeClass('active open');
            });

        })(jQuery);
    </script>

    @include('partials.notify')

    @stack('script-lib')
    @stack('script')
</body>
</html>
