<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle ?? 'Dashboard' }} — TECVEQ Core</title>

    <link rel="shortcut icon" type="image/jpeg" href="{{ siteFavicon() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Core CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <!-- CDN Fallback for Line Awesome & Font Awesome to ensure all icons render properly -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/iziToast.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

    <style>
        :root {
            --tecveq-primary: #6366f1;
            --tecveq-primary-hover: #4f46e5;
            --tecveq-dark: #0b0b10;
            --tecveq-surface: #121218;
            --tecveq-border: rgba(255, 255, 255, 0.08);
        }
        .bg--primary { background-color: #6366f1 !important; color: #fff !important; }
        .bg--secondary { background-color: #64748b !important; color: #fff !important; }
        .bg--success { background-color: #10b981 !important; color: #fff !important; }
        .bg--danger { background-color: #ef4444 !important; color: #fff !important; }
        .bg--warning { background-color: #f59e0b !important; color: #fff !important; }
        .bg--info { background-color: #06b6d4 !important; color: #fff !important; }
        .bg--dark { background-color: #0b0b10 !important; color: #fff !important; }
        .bg--white { background-color: #ffffff !important; }
        .card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }
        body {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #1e293b;
            background-color: #f1f5f9;
            overflow-x: hidden;
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
        }
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active,
            .sidebar.open {
                transform: translateX(0) !important;
            }
            .navbar-wrapper, .body-wrapper {
                margin-left: 0 !important;
            }
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
        .menu-arrow {
            transition: transform 0.25s ease;
            font-size: 13px;
        }
        .sidebar-dropdown > a {
            cursor: pointer;
            user-select: none;
        }
        .sidebar-dropdown > a .menu-arrow {
            font-size: 14px;
            transition: transform 0.25s ease;
            color: #64748b;
        }
        .sidebar-dropdown.open > a .menu-arrow,
        .sidebar-dropdown > a.side-menu--open .menu-arrow,
        .menu-arrow.rotate-180 {
            transform: rotate(180deg) !important;
            color: #818cf8 !important;
        }
        .sidebar-submenu {
            display: none;
            background: rgba(0, 0, 0, 0.25);
            border-radius: 8px;
            margin: 4px 0 6px 12px;
            padding: 4px 0;
            border-left: 2px solid rgba(99, 102, 241, 0.4);
        }
        .sidebar-submenu.sidebar-submenu__open,
        .sidebar-dropdown.open > .sidebar-submenu {
            display: block;
        }
        .sidebar-submenu ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }
        .sidebar-submenu ul li a {
            padding: 8px 14px !important;
            font-size: 13px !important;
            color: #94a3b8 !important;
            font-weight: 500;
        }
        .sidebar-submenu ul li a:hover,
        .sidebar-submenu ul li.active a {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.05);
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
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
            border: 1px solid rgba(99, 102, 241, 0.4);
            font-weight: 600;
        }
        .sidebar__menu .sidebar-menu-item a .menu-icon {
            font-size: 19px;
            color: #64748b;
        }
        .sidebar__menu .sidebar-menu-item.active a .menu-icon {
            color: #818cf8;
        }
        .sidebar__menu-header {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #475569;
            padding: 16px 14px 6px;
            text-transform: uppercase;
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
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            padding: 3px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .sidebar__menu .sidebar-menu-item a {
            font-size: 14px;
            font-weight: 500;
            padding: 10px 18px;
        }
        .res-sidebar-close-btn {
            display: none !important;
        }
        @media (max-width: 991px) {
            .res-sidebar-close-btn {
                display: flex !important;
                align-items: center;
                justify-content: center;
                position: absolute;
                top: 15px;
                right: 15px;
                width: 32px;
                height: 32px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
                border: none;
                z-index: 1050;
                cursor: pointer;
            }
        }
        .sidebar__menu .sidebar-menu-item a .menu-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            line-height: 1.3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            min-width: 20px;
            text-align: center;
            white-space: nowrap;
        }
        .sidebar__menu-header {
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: 0.8px;
            padding: 12px 20px 4px;
        }
        .table {
            font-size: 14px;
        }
        .table thead th {
            font-size: 13.5px;
            font-weight: 600;
            padding: 13px 16px;
        }
        .table td {
            padding: 13px 16px;
            font-size: 14px;
        }
        .form-control, .form-select {
            font-size: 14px;
            padding: 0.55rem 0.85rem;
            border-radius: 8px;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .page-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .version-info {
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.5);
            font-family: 'JetBrains Mono', monospace;
        }
        .table-responsive--md .table {
            border-collapse: separate;
            border-spacing: 0 4px;
        }
        .badge--primary {
            background-color: rgba(99, 102, 241, 0.15);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        .badge--success {
            background-color: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge--warning {
            background-color: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .badge--danger {
            background-color: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .card {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .btn--primary {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border: none;
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
            transition: all 0.2s ease;
        }
        .btn--primary:hover {
            background: linear-gradient(135deg, #4f46e5, #4338ca);
            color: #ffffff;
            transform: translateY(-1px);
        }
    </style>

    @stack('style-lib')
    @stack('style')
</head>
<body>

@yield('content')

<!-- Core JavaScript Libraries -->
<script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/global/js/iziToast.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/app.js') }}"></script>

@include('partials.notify')

<!-- TECVEQ / HYIPLab Inspired Collapsible Sidebar & Dropdown Controller -->
<script>
    (function ($) {
        "use strict";

        // 1. Restore Collapsed Preference
        if (localStorage.getItem('admin_sidebar_collapsed') === '1' && $(window).width() > 991) {
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
                localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? '1' : '0');
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

<div class="sidebar-overlay"></div>

@stack('script-lib')
@stack('script')

</body>
</html>
