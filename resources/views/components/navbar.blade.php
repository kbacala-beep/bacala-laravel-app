<nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
    <a href="/dashboard" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>

    <div class="navbar-nav align-items-center ms-auto">

        {{-- ── Messages dropdown (real data) ──────────────────── --}}
        <div class="nav-item dropdown" style="position: relative; display: inline-block; margin-right: 1rem;">
            <button class="btn btn-link" id="messageBell" data-bs-toggle="dropdown" aria-expanded="false" onclick="loadMessageDropdown()" style="color: var(--text-primary); text-decoration: none; font-size: 1.1rem; position: relative; padding: 0.5rem;">
                <i class="fa fa-envelope"></i>
            </button>
            <span class="badge rounded-pill bg-danger" id="msg-badge" style="display: none; position: absolute; top: -5px; right: -5px; padding: 2px 6px; font-size: 0.7rem;">
                <span id="messageCount">0</span>
            </span>

            <div class="dropdown-menu dropdown-menu-end" style="background: var(--surface-02) !important; border: 1px solid var(--border); border-radius: 8px; min-width: 320px; max-width: 400px; z-index: 1050;">
                <div class="dropdown-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border); padding: 12px 16px; background: var(--surface-02) !important;">
                    <span style="font-weight: 600; color: var(--text-primary);">Messages</span>
                </div>

                <div id="message-dropdown-items" style="max-height: 400px; overflow-y: auto; background: var(--surface-02) !important;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                        <i class="fa fa-inbox" style="margin-right: 8px;"></i> No new messages
                    </div>
                </div>

                <div class="dropdown-divider" style="margin: 0; border-top: 1px solid var(--border);"></div>
                <a href="{{ route('messages.index') }}" class="dropdown-item" style="padding: 10px 16px; color: var(--primary-hover); text-decoration: none; font-size: 0.85rem; background: var(--surface-02) !important;">
                    View all messages →
                </a>
            </div>
        </div>

        {{-- Notifications Bell --}}
        <div class="nav-item me-3">
            @include('components.notification-bell')
        </div>

        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img class="rounded-circle me-lg-2"
                    src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('img/default-user.png') }}"
                    alt="Profile Picture" style="width: 40px; height: 40px;">
                <span class="d-none d-lg-inline-flex">{{ Auth::user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                <!-- Route My Profile to /profile -->
                <a href="{{ route('profile.edit') }}" class="dropdown-item">My Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Log Out</button>
                </form>
            </div>
        </div>
    </div>
</nav>