<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-secondary navbar-dark">
        <a href="{{ route('dashboard') }}" class="navbar-brand mx-4 mb-3">
            <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>BrgyCIRS</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle"
                     src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('img/default-user.png') }}"
                     alt="Profile Picture"
                     style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6>{{ Auth::user()->name }}</h6>
                <span>
                    @php
                        $role = Auth::user()->role;
                        echo is_object($role) ? $role->name : ($role ?? 'Resident');
                    @endphp
                </span>
            </div>
        </div>

        <div class="navbar-nav w-100">
            <a href="{{ route('dashboard') }}" class="nav-item nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item nav-link {{ request()->routeIs('reports.index') || request()->routeIs('reports.show') || request()->routeIs('reports.create') || request()->routeIs('reports.edit') ? 'active' : '' }}">
                <i class="fa fa-laptop me-2"></i>Reports
            </a>
            <a href="{{ route('notifications.index') }}" class="nav-item nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <i class="fa fa-bell me-2"></i>Notifications
            </a>

            @php
                $userRole = Auth::user()->role;
                $roleName = is_object($userRole) ? $userRole->name : $userRole;
            @endphp

            @if(strtolower($roleName ?? '') === 'admin')
                <a href="{{ route('reports.archive') }}" class="nav-item nav-link {{ request()->routeIs('reports.archive') ? 'active' : '' }}">
                    <i class="fa fa-archive me-2"></i>Archives
                </a>
            @endif

            @php
                $sidebarRole = strtolower(is_object(Auth::user()->role) ? Auth::user()->role->name : (Auth::user()->role ?? 'resident'));
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