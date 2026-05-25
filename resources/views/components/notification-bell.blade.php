<!-- Notification Bell Icon -->
<div class="dropdown" style="position: relative; display: inline-block;">
    <button class="btn btn-link" id="notificationBell" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--text-primary); text-decoration: none; font-size: 1.1rem; position: relative; padding: 0.5rem;">
        <i class="fa fa-bell"></i>
    </button>
    <span class="badge rounded-pill bg-danger" id="notificationBadge" style="display: none; position: absolute; top: -5px; right: -5px; padding: 2px 6px; font-size: 0.7rem;">
        <span id="notificationCount">0</span>
    </span>

    <div class="dropdown-menu dropdown-menu-end" style="background: var(--surface-02) !important; border: 1px solid var(--border); border-radius: 8px; min-width: 320px; max-width: 400px; z-index: 1050; overflow-x: hidden;">
        <div class="dropdown-header d-flex align-items-center justify-content-between" style="border-bottom: 1px solid var(--border); padding: 12px 16px; background: var(--surface-02) !important;">
            <span style="font-weight: 600; color: var(--text-primary);">Notifications</span>
            <button type="button" id="markAllReadBtn" class="btn btn-sm btn-link" style="color: var(--primary-hover); text-decoration: none; padding: 0; font-size: 0.8rem;">
                Mark all as read
            </button>
        </div>

        <div id="notificationList" style="max-height: 400px; overflow-y: auto; overflow-x: hidden; background: var(--surface-02) !important;">
            <!-- Notifications will be loaded here via AJAX -->
            <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                <i class="fa fa-inbox" style="margin-right: 8px;"></i> No new notifications
            </div>
        </div>

        <div class="dropdown-divider" style="margin: 0; border-top: 1px solid var(--border);"></div>
        <a href="{{ route('notifications.index') }}" class="dropdown-item" style="padding: 10px 16px; color: var(--primary-hover); text-decoration: none; font-size: 0.85rem; background: var(--surface-02) !important;">
            View all notifications →
        </a>
    </div>
</div>

<script>
(function() {
    "use strict";

    // Get CSRF token
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    // Load unread notifications
    function loadNotifications() {
        fetch('{{ route("notifications.getUnread") }}', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(res => {
            const badge = document.getElementById('notificationBadge');
            const count = document.getElementById('notificationCount');
            const list = document.getElementById('notificationList');

            count.textContent = res.count;
            badge.style.display = res.count > 0 ? '' : 'none';

            if (res.notifications && res.notifications.length > 0) {
                let html = '';
                res.notifications.forEach(notif => {
                    html += `
                        <a href="#" class="dropdown-item notification-item" data-id="${notif.id}" style="border-bottom: 1px solid var(--border); padding: 12px 16px; display: block; text-decoration: none; color: var(--text-primary); transition: background 0.2s; word-wrap: break-word; overflow-wrap: break-word;">
                            <div style="font-size: 0.9rem; font-weight: 500; margin-bottom: 4px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${notif.title}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">${notif.message}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">${notif.created_at}</div>
                        </a>
                    `;
                });
                list.innerHTML = html;

                // Handle notification click to mark as read
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const notifId = this.getAttribute('data-id');
                        fetch('{{ route("notifications.markAsRead", ":id") }}'.replace(':id', notifId), {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken(),
                                'Accept': 'application/json',
                            }
                        })
                        .then(() => loadNotifications());
                    });
                });
            } else {
                list.innerHTML = `
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                        <i class="fa fa-inbox" style="margin-right: 8px;"></i> No new notifications
                    </div>
                `;
            }
        })
        .catch(error => console.error('Failed to load notifications:', error));
    }

    // Load notifications on page load
    loadNotifications();

    // Refresh every 10 seconds
    setInterval(loadNotifications, 10000);

    // Mark all as read
    const markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch('{{ route("notifications.markAllAsRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json',
                }
            })
            .then(() => loadNotifications());
        });
    }

})();
</script>
