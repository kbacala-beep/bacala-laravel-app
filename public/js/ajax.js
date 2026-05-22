(function ($) {
    "use strict";

    // ── CSRF token for all AJAX requests ──────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ── Global AJAX error handler ─────────────────────────────────
    $(document).ajaxError(function (event, xhr, settings, thrownError) {
        // Skip — errors handled locally by the calling code
        // Only catch ones that slip through without a local error handler
        if (settings.suppressGlobalError) return;

        if (xhr.status === 419) {
            showToast('Your session has expired. Please refresh the page and try again.', 'warning');
            return;
        }
        if (xhr.status === 403) {
            showToast('You are not authorized to perform this action.', 'error');
            return;
        }
        if (xhr.status === 404) {
            showToast('The requested resource was not found.', 'error');
            return;
        }
        if (xhr.status === 500) {
            let msg = 'A server error occurred. Check the logs for details.';
            try {
                const json = JSON.parse(xhr.responseText);
                if (json.error) msg = json.error;
            } catch (e) {}
            showToast(msg, 'error');
            return;
        }
        if (xhr.status === 0) {
            showToast('Network error. Please check your connection.', 'error');
            return;
        }
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
            success: { bg: 'rgba(46,125,50,0.15)',  border: 'rgba(46,125,50,0.4)',  icon: 'fa-check-circle',       color: '#81C784' },
            error:   { bg: 'rgba(198,40,40,0.15)',  border: 'rgba(198,40,40,0.4)',  icon: 'fa-times-circle',       color: '#EF9A9A' },
            warning: { bg: 'rgba(245,127,23,0.15)', border: 'rgba(245,127,23,0.4)', icon: 'fa-exclamation-circle', color: '#FFB74D' },
            info:    { bg: 'rgba(2,119,189,0.15)',  border: 'rgba(2,119,189,0.4)',  icon: 'fa-info-circle',        color: '#81D4FA' },
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
            requestAnimationFrame(function () {
                toast.css({ opacity: 1, transform: 'translateX(0)' });
            });
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
            var msg  = $flash.data('message');
            var type = $flash.data('type') || 'success';
            if (msg) showToast(msg, type);
        }
    });

    // ── Reports: inline status update (admin) ─────────────────────
    // Uses data-resident stored on the row so we don't rely on column index
    $(document).on('change', '.status-select', function () {
        var $select   = $(this);
        var url       = $select.data('url');
        var status    = $select.val();
        var $row      = $select.closest('tr');
        // Store original so we can revert on failure
        var original  = $select.data('original') || status;
        // Get resident name from data attribute on the row, fallback to strong text
        var residentName = $row.data('resident') || $row.find('strong').first().text();

        $select.prop('disabled', true);

        $.ajax({
            url:    url,
            method: 'POST',
            data: {
                _method:        'PUT',
                status:         status,
                resident_name:  residentName,
            },
            success: function (res) {
                showToast(res.message, 'success');
                $select.data('original', status);
            },
            error: function (xhr) {
                var msg = 'Failed to update status.';
                try { var j = JSON.parse(xhr.responseText); if (j.error) msg = j.error; } catch(e) {}
                showToast(msg, 'error');
                $select.val(original);
            },
            complete: function () {
                $select.prop('disabled', false);
            }
        });
    });

    // ── Reports: AJAX archive (admin, index) ──────────────────────
    $(document).on('click', '.ajax-archive', function () {
        var $btn = $(this);
        if (!confirm('Archive this report?')) return;
        $btn.prop('disabled', true);

        $.ajax({
            url:    $btn.data('url'),
            method: 'DELETE',
            success: function (res) {
                removeReportRow($btn.data('id'), res.message);
            },
            error: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // ── Reports: AJAX delete (resident, own reports) ──────────────
    $(document).on('click', '.ajax-delete', function () {
        var $btn = $(this);
        if (!confirm('Are you sure you want to delete this report?')) return;
        $btn.prop('disabled', true);

        $.ajax({
            url:    $btn.data('url'),
            method: 'DELETE',
            success: function (res) {
                removeReportRow($btn.data('id'), res.message);
            },
            error: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    // ── Reports: AJAX delete with redirect (show page) ────────────
    $(document).on('click', '.ajax-delete-redirect', function () {
        var $btn     = $(this);
        var url      = $btn.data('url');
        var redirect = $btn.data('redirect');
        var isAdmin  = $btn.find('i').hasClass('fa-archive');
        var msg      = isAdmin ? 'Archive this report?' : 'Are you sure you want to delete this report?';

        if (!confirm(msg)) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url:    url,
            method: 'DELETE',
            success: function (res) {
                showToast(res.message, 'success');
                setTimeout(function () { window.location.href = redirect; }, 1200);
            },
            error: function () {
                $btn.prop('disabled', false);
            }
        });
    });

    function removeReportRow(id, message) {
        var $row = $('#report-row-' + id);
        $row.css({ transition: 'opacity 0.3s ease, transform 0.3s ease', opacity: 0, transform: 'translateX(20px)' });
        setTimeout(function () {
            $row.remove();
            showToast(message, 'success');
        }, 320);
    }

    // ── Archive: restore ──────────────────────────────────────────
    $(document).on('click', '.ajax-restore', function () {
        var $btn = $(this);
        var id   = $btn.data('id');
        var url  = $btn.data('url');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');

        $.ajax({
            url:    url,
            method: 'POST',
            success: function (res) {
                removeArchiveRow(id, res.message);
            },
            error: function () {
                showToast('Failed to restore report.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-undo me-1"></i> Restore');
            }
        });
    });

    // ── Archive: force delete ─────────────────────────────────────
    $(document).on('click', '.ajax-force-delete', function () {
        var $btn = $(this);
        var id   = $btn.data('id');
        var url  = $btn.data('url');

        if (!confirm('Permanently delete this report? This cannot be undone.')) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');

        $.ajax({
            url:    url,
            method: 'DELETE',
            success: function (res) {
                removeArchiveRow(id, res.message);
            },
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
            $row.remove();
            showToast(message, 'success');
            if ($('#archive-table tbody tr').length === 0) {
                $('#archive-table tbody').append(
                    '<tr id="empty-row"><td colspan="7" class="text-center text-muted py-4">' +
                    '<i class="fa fa-archive me-2"></i> No archived reports found.</td></tr>'
                );
            }
        }, 320);
    }

    // ── Users: suspend ────────────────────────────────────────────
    var activeSuspendId   = null;
    var activeSuspendUrl  = null;

    $(document).on('click', '.btn-suspend', function () {
        activeSuspendId  = $(this).data('id');
        activeSuspendUrl = $(this).data('url');          // set via data-url on button
        $('#suspend-user-name').text($(this).data('name'));
        $('#suspend-reason').val('');
        var $m = $('#suspendModal');
        $m.css('display', 'flex').css('opacity', '0');
        setTimeout(function () { $m.css({ opacity: '1', transition: 'opacity 0.2s ease' }); }, 10);
    });

    window.closeSuspendModal = function () {
        $('#suspendModal').css('opacity', '0');
        setTimeout(function () { $('#suspendModal').css('display', 'none'); }, 200);
        activeSuspendId = null;
        activeSuspendUrl = null;
    };

    window.confirmSuspend = function () {
        if (!activeSuspendId) return;
        var $btn = $('#suspend-confirm-btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Suspending...');

        $.ajax({
            url:    activeSuspendUrl || ('/users/' + activeSuspendId + '/suspend'),
            method: 'POST',
            data:   { suspension_reason: $('#suspend-reason').val() },
            success: function (res) {
                showToast(res.message, 'success');
                closeSuspendModal();
                setTimeout(function () { window.location.reload(); }, 800);
            },
            error: function () {
                showToast('Failed to suspend user.', 'error');
            },
            complete: function () {
                $btn.prop('disabled', false).html('<i class="fa fa-ban me-1"></i> Suspend Account');
            }
        });
    };

    // ── Users: activate ───────────────────────────────────────────
    $(document).on('click', '.btn-activate', function () {
        var id   = $(this).data('id');
        var name = $(this).data('name');
        var url  = $(this).data('url');               // set via data-url on button

        if (!confirm('Reactivate ' + name + '\'s account?')) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');

        $.ajax({
            url:    url || ('/users/' + id + '/activate'),
            method: 'POST',
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

})(jQuery);