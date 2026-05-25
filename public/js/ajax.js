(function ($) {
    "use strict";

    // ── CSRF token ────────────────────────────────────────────────
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ── Global AJAX error handler ─────────────────────────────────
    $(document).ajaxError(function (event, xhr, settings) {
        if (settings.suppressGlobalError) return;
        if (xhr.status === 419) { showToast('Your session has expired. Please refresh the page.', 'warning'); return; }
        if (xhr.status === 403) { showToast('You are not authorized to perform this action.', 'error'); return; }
        if (xhr.status === 404) { showToast('The requested resource was not found.', 'error'); return; }
        if (xhr.status === 500) {
            var msg = 'A server error occurred. Check the logs for details.';
            try { var j = JSON.parse(xhr.responseText); if (j.error) msg = j.error; } catch (e) { }
            showToast(msg, 'error'); return;
        }
        if (xhr.status === 0) { showToast('Network error. Please check your connection.', 'error'); }
    });

    // ── Toast system ──────────────────────────────────────────────
    function getToastContainer() {
        if (!$('#toast-container').length) {
            $('body').append(
                '<div id="toast-container" style="position:fixed; bottom:28px; right:28px; z-index:99999;' +
                'display:flex; flex-direction:column; gap:10px; align-items:flex-end;"></div>'
            );
        }
        return $('#toast-container');
    }

    window.showToast = function (message, type) {
        type = type || 'success';
        var colors = {
            success: { bg: 'rgba(46,125,50,0.15)', border: 'rgba(46,125,50,0.4)', icon: 'fa-check-circle', color: '#81C784' },
            error: { bg: 'rgba(198,40,40,0.15)', border: 'rgba(198,40,40,0.4)', icon: 'fa-times-circle', color: '#EF9A9A' },
            warning: { bg: 'rgba(245,127,23,0.15)', border: 'rgba(245,127,23,0.4)', icon: 'fa-exclamation-circle', color: '#FFB74D' },
            info: { bg: 'rgba(2,119,189,0.15)', border: 'rgba(2,119,189,0.4)', icon: 'fa-info-circle', color: '#81D4FA' },
        };
        var c = colors[type] || colors.success;
        var toast = $(
            '<div style="background:' + c.bg + '; border:1px solid ' + c.border + '; border-radius:10px;' +
            'padding:12px 16px; display:flex; align-items:center; gap:10px; min-width:280px; max-width:380px;' +
            'box-shadow:0 8px 24px rgba(0,0,0,0.4); backdrop-filter:blur(8px); opacity:0; transform:translateX(20px);' +
            'transition:opacity 0.25s ease, transform 0.25s ease; font-family:Roboto,sans-serif; font-size:0.87rem;' +
            'color:' + c.color + '; cursor:pointer;">' +
            '<i class="fa ' + c.icon + '" style="flex-shrink:0; font-size:1rem;"></i>' +
            '<span style="flex:1; line-height:1.4;">' + message + '</span>' +
            '<i class="fa fa-times" style="flex-shrink:0; opacity:0.5; font-size:0.8rem;"></i>' +
            '</div>'
        );
        getToastContainer().append(toast);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { toast.css({ opacity: 1, transform: 'translateX(0)' }); });
        });
        toast.on('click', function () { dismissToast(toast); });
        setTimeout(function () { dismissToast(toast); }, 4000);
    };

    function dismissToast(toast) {
        toast.css({ opacity: 0, transform: 'translateX(20px)' });
        setTimeout(function () { toast.remove(); }, 300);
    }

    // ── Flash session messages → toasts ───────────────────────────
    $(document).ready(function () {
        var $flash = $('#flash-message');
        if ($flash.length) {
            var msg = $flash.data('message');
            var type = $flash.data('type') || 'success';
            if (msg) showToast(msg, type);
        }
    });

    // ── Reports: inline status update ────────────────────────────
    $(document).on('change', '.status-select', function () {
        var $select = $(this);
        var url = $select.data('url');
        var status = $select.val();
        var original = $select.data('original') || status;
        var $row = $select.closest('tr');
        var residentName = $row.data('resident') || $row.find('strong').first().text();

        $select.prop('disabled', true);

        $.ajax({
            url: url, method: 'POST',
            data: { _method: 'PUT', status: status, resident_name: residentName },
            success: function (res) {
                showToast(res.message, 'success');
                $select.data('original', status);
            },
            error: function (xhr) {
                var msg = 'Failed to update status.';
                try { var j = JSON.parse(xhr.responseText); if (j.error) msg = j.error; } catch (e) { }
                showToast(msg, 'error');
                $select.val(original);
            },
            complete: function () { $select.prop('disabled', false); }
        });
    });

    // ── Reports: AJAX archive ─────────────────────────────────────
    $(document).on('click', '.ajax-archive', function () {
        var $btn = $(this);
        if (!confirm('Archive this report?')) return;
        $btn.prop('disabled', true);
        $.ajax({
            url: $btn.data('url'), method: 'DELETE',
            success: function (res) { removeReportRow($btn.data('id'), res.message); },
            error: function () { $btn.prop('disabled', false); }
        });
    });

    // ── Reports: AJAX delete ──────────────────────────────────────
    $(document).on('click', '.ajax-delete', function () {
        var $btn = $(this);
        if (!confirm('Are you sure you want to delete this report?')) return;
        $btn.prop('disabled', true);
        $.ajax({
            url: $btn.data('url'), method: 'DELETE',
            success: function (res) { removeReportRow($btn.data('id'), res.message); },
            error: function () { $btn.prop('disabled', false); }
        });
    });

    // ── Reports: AJAX delete with redirect (show page) ───────────
    $(document).on('click', '.ajax-delete-redirect', function () {
        var $btn = $(this);
        var url = $btn.data('url');
        var redirect = $btn.data('redirect');
        var isAdmin = $btn.find('i').hasClass('fa-archive');
        var msg = isAdmin ? 'Archive this report?' : 'Are you sure you want to delete this report?';
        if (!confirm(msg)) return;
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing...');
        $.ajax({
            url: url, method: 'DELETE',
            success: function (res) {
                showToast(res.message, 'success');
                setTimeout(function () { window.location.href = redirect; }, 1200);
            },
            error: function () { $btn.prop('disabled', false); }
        });
    });

    function removeReportRow(id, message) {
        var $row = $('#report-row-' + id);
        $row.css({ transition: 'opacity 0.3s ease, transform 0.3s ease', opacity: 0, transform: 'translateX(20px)' });
        setTimeout(function () { $row.remove(); showToast(message, 'success'); }, 320);
    }

    // ── Archive: restore ──────────────────────────────────────────
    $(document).on('click', '.ajax-restore', function () {
        var $btn = $(this), id = $btn.data('id'), url = $btn.data('url');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');
        $.ajax({
            url: url, method: 'POST',
            success: function (res) { removeArchiveRow(id, res.message); },
            error: function () {
                showToast('Failed to restore report.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-undo me-1"></i> Restore');
            }
        });
    });

    // ── Archive: force delete ─────────────────────────────────────
    $(document).on('click', '.ajax-force-delete', function () {
        var $btn = $(this), id = $btn.data('id'), url = $btn.data('url');
        if (!confirm('Permanently delete this report? This cannot be undone.')) return;
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');
        $.ajax({
            url: url, method: 'DELETE',
            success: function (res) { removeArchiveRow(id, res.message); },
            error: function () {
                showToast('Failed to delete report.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-trash me-1"></i> Delete');
            }
        });
    });

    function removeArchiveRow(id, message) {
        var $row = $('#archive-row-' + id);
        $row.css({ transition: 'opacity 0.3s ease, transform 0.3s ease', opacity: 0, transform: 'translateX(20px)' });
        setTimeout(function () {
            $row.remove(); showToast(message, 'success');
            if ($('#archive-table tbody tr').length === 0) {
                $('#archive-table tbody').append(
                    '<tr id="empty-row"><td colspan="7" class="text-center text-muted py-4">' +
                    '<i class="fa fa-archive me-2"></i> No archived reports found.</td></tr>'
                );
            }
        }, 320);
    }

    // ── Users: suspend modal ──────────────────────────────────────
    var activeSuspendId = null, activeSuspendUrl = null;

    $(document).on('click', '.btn-suspend', function () {
        activeSuspendId = $(this).data('id');
        activeSuspendUrl = $(this).data('url');
        $('#suspend-user-name').text($(this).data('name'));
        $('#suspend-reason').val('');
        var $m = $('#suspendModal');
        $m.css('display', 'flex').css('opacity', '0');
        setTimeout(function () { $m.css({ opacity: '1', transition: 'opacity 0.2s ease' }); }, 10);
    });

    window.closeSuspendModal = function () {
        $('#suspendModal').css('opacity', '0');
        setTimeout(function () { $('#suspendModal').css('display', 'none'); }, 200);
        activeSuspendId = null; activeSuspendUrl = null;
    };

    window.confirmSuspend = function () {
        if (!activeSuspendId) return;
        var $btn = $('#suspend-confirm-btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Suspending...');
        $.ajax({
            url: activeSuspendUrl || ('/users/' + activeSuspendId + '/suspend'),
            method: 'POST',
            data: { suspension_reason: $('#suspend-reason').val() },
            success: function (res) {
                showToast(res.message, 'success');
                closeSuspendModal();
                setTimeout(function () { window.location.reload(); }, 800);
            },
            error: function () { showToast('Failed to suspend user.', 'error'); },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-ban me-1"></i> Suspend Account');
            }
        });
    };

    $(document).on('click', '.btn-activate', function () {
        var id = $(this).data('id'), name = $(this).data('name'), url = $(this).data('url');
        if (!confirm('Reactivate ' + name + '\'s account?')) return;
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');
        $.ajax({
            url: url || ('/users/' + id + '/activate'), method: 'POST',
            success: function (res) {
                showToast(res.message, 'success');
                setTimeout(function () { window.location.reload(); }, 800);
            },
            error: function () {
                showToast('Failed to reactivate user.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-user-check me-1"></i> Activate');
            }
        });
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && activeSuspendId) closeSuspendModal();
    });

    // ── Message navbar badge, sidebar badge & dropdown ───────────
    var msgBadgeInterval;

    function fetchUnreadCount() {
        $.ajax({
            url: '/messages/unread-count',
            method: 'GET',
            suppressGlobalError: true,
            success: function (res) {
                // Navbar badge
                var $navbarBadge = $('#msg-badge');
                var $navbarCount = $('#messageCount');
                if (res.count > 0) {
                    $navbarCount.text(res.count > 99 ? '99+' : res.count);
                    $navbarBadge.show();
                } else {
                    $navbarBadge.hide();
                }

                // Sidebar badge
                var $sidebarBadge = $('#sidebar-msg-badge');
                var $sidebarCount = $('#sidebar-msg-count');
                if (res.count > 0) {
                    $sidebarCount.text(res.count > 99 ? '99+' : res.count);
                    $sidebarBadge.show();
                } else {
                    $sidebarBadge.hide();
                }
            }
        });
    }

    window.loadMessageDropdown = function () {
        $.ajax({
            url: '/messages/unread-count',
            method: 'GET',
            suppressGlobalError: true,
            success: function (res) {
                var $container = $('#message-dropdown-items');

                if (!res.recent || res.recent.length === 0) {
                    $container.html(
                        '<div style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.9rem;">' +
                        '<i class="fa fa-inbox" style="margin-right: 8px;"></i> No new messages</div>'
                    );
                    return;
                }

                var html = '';
                res.recent.forEach(function (msg) {
                    html +=
                        '<a href="' + msg.url + '" class="dropdown-item" style="border-bottom: 1px solid var(--border); padding: 12px 16px; display: flex; gap: 12px; align-items: flex-start; text-decoration: none; color: var(--text-primary); transition: background 0.2s;">' +
                        '<img src="' + msg.sender_photo + '" alt="' + msg.sender_name + '" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">' +
                        '<div style="flex: 1; min-width: 0;">' +
                        '<div style="font-size: 0.9rem; font-weight: 500; margin-bottom: 4px; word-wrap: break-word;">' + msg.sender_name + '</div>' +
                        '<div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 6px; word-wrap: break-word; white-space: normal;">' + msg.preview + '</div>' +
                        '<div style="font-size: 0.75rem; color: var(--text-muted);">' + msg.time_ago + '</div>' +
                        '</div>' +
                        '</a>';
                });

                $container.html(html);
            },
            error: function () {
                $('#message-dropdown-items').html('<div style="padding:20px; text-align:center; color:var(--text-muted); font-size:0.9rem;"><i class="fa fa-exclamation-circle me-1"></i> Failed to load messages</div>');
            }
        });
    };

    // Poll unread count every 10 seconds
    $(document).ready(function () {
        fetchUnreadCount();
        msgBadgeInterval = setInterval(fetchUnreadCount, 10000);
    });

})(jQuery);