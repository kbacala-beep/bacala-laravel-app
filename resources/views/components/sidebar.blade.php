<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-secondary navbar-dark">
        <a href="{{ route('dashboard') }}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>BrgyCIRS</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle"
                    src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('img/default-user.png') }}"
                    alt="Profile Picture" style="width: 40px; height: 40px; object-fit: cover;">
                <div
                    class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                </div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                <small class="text-primary fw-bold" style="font-size: 0.7rem;">
                    Brgy. {{ Auth::user()->barangay->name ?? 'System Admin' }}  
                </small>
                <small class="text-muted" style="font-size: 0.7rem; text-transform: uppercase;">
                        | {{ Auth::user()->role_relation->name ?? 'Resident' }}
                </small>
            </div>
        </div>

        <div class="navbar-nav w-100">
            <a href="{{ route('dashboard') }}"
                class="nav-item nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>

            <a href="{{ route('reports.index') }}"
                class="nav-item nav-link {{ request()->routeIs('reports.*') && !request()->routeIs('reports.archive') && !request()->routeIs('reports.activityLog') ? 'active' : '' }}">
                <i class="fa fa-laptop me-2"></i>Reports
            </a>

            <a href="{{ route('messages.index') }}"
                class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}"
                style="display: flex; align-items: center; gap: 10px; position: relative;">
                <i class="fa fa-envelope"></i>
                <span>Messages</span>
                <span id="sidebar-msg-badge"
                    style="display: none; position: absolute; right: 10px; background: var(--primary); color: #fff; border-radius: 20px; font-size: 0.65rem; font-weight: 700; padding: 1px 7px; margin-left: 6px; line-height: 1.5; vertical-align: middle;">
                    <span id="sidebar-msg-count"></span>
                </span>
            </a>

            <a href="{{ route('notifications.index') }}"
                class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                style="display: flex; align-items: center; gap: 10px; position: relative;">
                <i class="fa fa-bell"></i>
                <span>Notifications</span>
                <span id="sidebar-notif-badge"
                    style="display: none; position: absolute; right: 10px; background: var(--primary); color: #fff; border-radius: 20px; font-size: 0.65rem; font-weight: 700; padding: 1px 7px; margin-left: 6px; line-height: 1.5; vertical-align: middle;">
                    <span id="sidebar-notificationCount"></span>
                </span>
            </a>

            {{-- Admin Specific Links --}}
            @if(Auth::user()->isAdmin())
                <hr class="mx-3 my-2" style="opacity: 0.1;">
                <a href="{{ route('reports.archive') }}"
                    class="nav-item nav-link {{ request()->routeIs('reports.archive') ? 'active' : '' }}">
                    <i class="fa fa-archive me-2"></i>Archives
                </a>
            @endif

            @php
                $sidebarRole = strtolower(Auth::user()->role_relation->name ?? 'resident');
            @endphp

            @if($sidebarRole === 'admin')
                <a href="{{ route('users.index') }}"
                    class="nav-item nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa fa-users-cog me-2"></i> User Management
                </a>
                <a href="{{ route('reports.activityLog') }}"
                    class="nav-item nav-link {{ request()->routeIs('reports.activityLog') ? 'active' : '' }}">
                    <i class="fa fa-history me-2"></i> Activity Log
                </a>
            @endif
        </div>
    </nav>
</div>

@push('scripts')
    <script>
        (function ($) {
            function updateSidebarBadge() {
                $.ajax({
                    url: '/notifications/unread',
                    method: 'GET',
                    suppressGlobalError: true,
                    success: function (res) {
                        var $badge = $('#sidebar-notif-badge');
                        var $count = $('#sidebar-notificationCount');
                        if (res.count > 0) {
                            $count.text(res.count > 99 ? '99+' : res.count);
                            $badge.show();
                        } else {
                            $badge.hide();
                        }
                    }
                });
            }

            updateSidebarBadge();
            setInterval(updateSidebarBadge, 10000);
        })(jQuery);
    </script>
@endpush