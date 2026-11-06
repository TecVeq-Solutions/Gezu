<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Creator Portal' }} — GEZUX</title>

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
            --gezux-creator: #10b981;
            --gezux-creator-hover: #059669;
            --gezux-creator-gradient: linear-gradient(135deg, #10b981, #06b6d4);
            --gezux-dark: #0b0b10;
            --gezux-surface: #121218;
            --gezux-border: rgba(255, 255, 255, 0.08);
        }
        .bg--primary { background-color: #10b981 !important; color: #fff !important; }
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

        /* HYIPLab Standardized Form Control Discipline (40px) */
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
            border-color: #10b981 !important;
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.15) !important;
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

        /* HYIPLab Standardized Creator Buttons */
        .btn-creator {
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
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff;
            border: none;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-creator:hover {
            background: linear-gradient(135deg, #059669, #047857);
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }
        .btn-creator.btn-lg, .btn-creator.btn--lg {
            height: 46px !important;
            min-height: 46px !important;
            padding: 0 24px !important;
            font-size: 15px !important;
            border-radius: 6px !important;
        }
        .btn-creator.btn-sm, .btn-creator.btn--sm {
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

        /* Standard Modern Stat-Card (Disciplined Metric Architecture) */
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

        /* Clean Table Discipline (Reference Site Architecture) */
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
            background: linear-gradient(135deg, #10b981, #06b6d4);
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
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(6, 182, 212, 0.25));
            border: 1px solid rgba(16, 185, 129, 0.45);
            font-weight: 600;
        }
        .sidebar__menu .sidebar-menu-item a .menu-icon {
            font-size: 19px;
            color: #64748b;
            min-width: 20px;
            text-align: center;
        }
        .sidebar__menu .sidebar-menu-item.active a .menu-icon {
            color: #34d399;
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
                <span>You are currently viewing as <strong>{{ Auth::user()->name }}</strong> (Creator/Clipper ID: {{ Auth::id() }}).</span>
            </div>
            <a href="{{ route('admin.impersonate.leave') }}" class="btn btn-dark btn-sm fw-bold">
                <i class="las la-sign-out-alt"></i> Exit Impersonation & Return to Admin
            </a>
        </div>
    @endif

    @yield('content')

    <!-- Core Scripts -->
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
            if (localStorage.getItem('clipper_sidebar_collapsed') === '1' && $(window).width() > 991) {
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
                    localStorage.setItem('clipper_sidebar_collapsed', isCollapsed ? '1' : '0');
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

    @stack('script')
</body>
</html>
