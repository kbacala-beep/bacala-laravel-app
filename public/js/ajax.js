(function ($) {
    "use strict";

    // ── CSRF token for all AJAX requests ──────────────────────────
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ── Global AJAX error handler ─────────────────────────────────
    // Catches any AJAX request that doesn't have its own error handler,
    // and also normalises error responses into readable messages.
    $(document).ajaxError(function (event, xhr, settings, thrownError) {
        // 419 = CSRF token mismatch (session expired)
        if (xhr.status === 419) {
            showToast('Your session has expired. Please refresh the page and try again.', 'warning');
            return;
        }

        // 403 = Unauthorized
        if (xhr.status === 403) {
            showToast('You are not authorized to perform this action.', 'error');
            return;
        }

        // 404 = Route not found (usually a config issue)
        if (xhr.status === 404) {
            showToast('The requested resource was not found.', 'error');
            return;
        }

        // 500 = Server error — try to show Laravel's message if available
        if (xhr.status === 500) {
            let msg = 'A server error occurred. Check the logs for details.';
            try {
                const json = JSON.parse(xhr.responseText);
                if (json.error) msg = json.error;
            } catch (e) { /* response wasn't JSON, use default */ }
            showToast(msg, 'error');
            return;
        }

        // 0 = Network error / request aborted
        if (xhr.status === 0) {
            showToast('Network error. Please check your connection.', 'error');
            return;
        }
    });

    // ── Toast notification system ─────────────────────────────────
    function getToastContainer() {
        if (!$('#toast-container').length) {
            $('body').append(`
                <div id="toast-container"
                     style="position:fixed; bottom:28px; right:28px; z-index:99999;
                            display:flex; flex-direction:column; gap:10px; align-items:flex-end;">
                </div>
            `);
        }
        return $('#toast-container');
    }

    window.showToast = function (message, type = 'success') {
        const colors = {
            success: { bg: 'rgba(46,125,50,0.15)',  border: 'rgba(46,125,50,0.4)',  icon: 'fa-check-circle',       color: '#81C784' },
            error:   { bg: 'rgba(198,40,40,0.15)',  border: 'rgba(198,40,40,0.4)',  icon: 'fa-times-circle',       color: '#EF9A9A' },
            warning: { bg: 'rgba(245,127,23,0.15)', border: 'rgba(245,127,23,0.4)', icon: 'fa-exclamation-circle', color: '#FFB74D' },
            info:    { bg: 'rgba(2,119,189,0.15)',  border: 'rgba(2,119,189,0.4)',  icon: 'fa-info-circle',        color: '#81D4FA' },
        };

        const c = colors[type] || colors.success;

        const toast = $(`
            <div style="
                background: ${c.bg};
                border: 1px solid ${c.border};
                border-radius: 10px;
                padding: 12px 16px;
                display: flex;
                align-items: center;
                gap: 10px;
                min-width: 280px;
                max-width: 380px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.4);
                backdrop-filter: blur(8px);
                opacity: 0;
                transform: translateX(20px);
                transition: opacity 0.25s ease, transform 0.25s ease;
                font-family: 'Roboto', sans-serif;
                font-size: 0.87rem;
                color: ${c.color};
                cursor: pointer;
            ">
                <i class="fa ${c.icon}" style="flex-shrink:0; font-size:1rem;"></i>
                <span style="flex:1; line-height:1.4;">${message}</span>
                <i class="fa fa-times" style="flex-shrink:0; opacity:0.5; font-size:0.8rem;"></i>
            </div>
        `);

        getToastContainer().append(toast);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.css({ opacity: 1, transform: 'translateX(0)' });
            });
        });

        toast.on('click', () => dismissToast(toast));
        setTimeout(() => dismissToast(toast), 4000);
    };

    function dismissToast(toast) {
        toast.css({ opacity: 0, transform: 'translateX(20px)' });
        setTimeout(() => toast.remove(), 300);
    }

    // ── Flash session messages → toasts ───────────────────────────
    $(document).ready(function () {
        const $flash = $('#flash-message');
        if ($flash.length) {
            const msg  = $flash.data('message');
            const type = $flash.data('type') || 'success';
            if (msg) showToast(msg, type);
        }
    });

})(jQuery);