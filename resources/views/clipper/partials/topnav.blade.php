<nav class="navbar-wrapper">
    <div class="navbar__left">
        <button type="button" class="res-sidebar-open-btn me-3"><i class="las la-bars"></i></button>
        <span class="d-none d-sm-inline-block text-white fw-bold fs-6">
            {{ Auth::user()->name ?? 'Creator' }}
        </span>
    </div>

    <div class="navbar__right d-flex align-items-center gap-2">
        <a href="{{ route('clipper.submissions.create') }}" class="btn btn-creator btn-sm d-flex align-items-center gap-1">
            <i class="las la-plus"></i> Submit Clip
        </a>

        <a href="{{ route('clipper.bounties') }}" class="btn btn-outline-light btn-sm d-none d-md-flex align-items-center gap-1">
            <i class="las la-fire"></i> Browse Bounties
        </a>

        @if(session()->has('impersonated_by'))
            <a href="{{ route('admin.impersonate.leave') }}" class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                <i class="las la-user-shield"></i> Return to Admin
            </a>
        @endif

        <!-- User Dropdown Menu -->
        <div class="dropdown">
            <button class="btn btn-dark btn-sm dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(255,255,255,0.08);">
                <div class="avatar text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="background: linear-gradient(135deg, #10b981, #06b6d4); width: 28px; height: 28px; font-size: 13px;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'C', 0, 1)) }}
                </div>
                <span class="text-white small fw-semibold d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom">
                    <p class="mb-0 fw-bold small text-dark">{{ Auth::user()->name }}</p>
                    <p class="mb-0 text-muted" style="font-size: 11px;">{{ Auth::user()->email }}</p>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-dark" href="{{ route('clipper.profile') }}">
                        <i class="las la-user-circle text-primary fs-5"></i> Creator Profile & Channels
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-dark" href="{{ route('clipper.wallet') }}">
                        <i class="las la-wallet text-success fs-5"></i> Wallet & Earnings
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-dark" href="{{ route('clipper.payout-settings') }}">
                        <i class="las la-university text-info fs-5"></i> Bank Payout Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('web.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger">
                            <i class="las la-sign-out-alt fs-5"></i> Sign Out
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
