<nav class="navbar-wrapper bg--dark d-flex flex-wrap align-items-center justify-content-between px-3 py-2">
    <div class="navbar__left d-flex align-items-center">
        <button type="button" class="res-sidebar-open-btn me-3 text-white border-0 bg-transparent fs-4">
            <i class="las la-bars"></i>
        </button>
        <span class="text-white-50 small d-none d-md-inline-block font-monospace">
            TECVEQ / Control Protocol
        </span>
    </div>

    <div class="navbar__right d-flex align-items-center gap-3">
        @if(session()->has('impersonated_by'))
            <a href="{{ route('admin.impersonate.leave') }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                <i class="las la-sign-out-alt"></i> Leave Impersonation
            </a>
        @endif

        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1.5" title="View Public Marketplace">
            <i class="las la-globe"></i>
            <span class="d-none d-sm-inline">Live Marketplace</span>
        </a>

        <!-- Admin Profile Dropdown -->
        <div class="dropdown">
            <button type="button" class="btn btn-dark btn-sm d-flex align-items-center gap-2 border border-secondary" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="w-8 h-8 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; border-radius: 50%;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <span class="text-white small fw-bold d-none d-md-inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                <i class="las la-angle-down text-white-50"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li class="px-3 py-2 border-bottom">
                    <span class="d-block fw-bold small text-dark">{{ auth()->user()->name ?? 'Super Admin' }}</span>
                    <span class="d-block text-muted small text-truncate" style="max-width: 180px;">{{ auth()->user()->email ?? 'admin@gezux.com' }}</span>
                </li>
                <li>
                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2 text-dark text-decoration-none" href="{{ safeRoute('admin.settings.general', [], '/admin/settings/general') }}">
                        <i class="las la-cog me-2 text-primary fs-5"></i> Settings
                    </a>
                </li>
                <li>
                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2 text-dark text-decoration-none" href="{{ safeRoute('admin.password', [], '/admin/password') }}">
                        <i class="las la-key me-2 text-warning fs-5"></i> Password
                    </a>
                </li>
                <li>
                    <form action="{{ route('web.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-menu__item d-flex align-items-center px-3 py-2 text-danger text-decoration-none border-0 bg-transparent w-100 text-start">
                            <i class="las la-sign-out-alt me-2 fs-5"></i> Log Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
