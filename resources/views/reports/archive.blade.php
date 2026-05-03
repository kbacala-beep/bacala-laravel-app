@extends('layouts.app')

@section('title', 'Archived Reports')

@section('content')
    <div class="container-fluid pt-4 px-4 pb-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="mb-0">Archived Reports</h2>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back to Reports
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle rounded overflow-hidden" id="archive-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Resident</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted By</th>
                        <th>Archived On</th>
                        <th class="col-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $index => $report)
                        <tr id="archive-row-{{ $report->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $report->resident_name }}</strong></td>
                            <td>{{ $report->subject }}</td>
                            <td>
                                <span class="badge
                                    @if($report->status === 'Pending') bg-warning
                                    @elseif($report->status === 'Resolved') bg-success
                                    @else bg-light @endif">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td>{{ $report->user->name ?? 'N/A' }}</td>
                            <td>{{ $report->deleted_at->format('M d, Y H:i') }}</td>
                            <td>
                                <button type="button"
                                        class="btn btn-success btn-sm ajax-restore"
                                        data-id="{{ $report->id }}"
                                        data-url="{{ route('reports.restore', $report->id) }}">
                                    <i class="fa fa-undo me-1"></i> Restore
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-sm ajax-force-delete"
                                        data-id="{{ $report->id }}"
                                        data-url="{{ route('reports.forceDelete', $report->id) }}">
                                    <i class="fa fa-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr id="empty-row">
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa fa-archive me-2"></i> No archived reports found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function($) {
    "use strict";

    // ── Restore ───────────────────────────────────────────────────
    $(document).on('click', '.ajax-restore', function () {
        const $btn = $(this);
        const id   = $btn.data('id');
        const url  = $btn.data('url');

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');

        $.ajax({
            url:    url,
            method: 'POST',
            success: function (res) {
                removeArchiveRow(id, res.message, 'success');
            },
            error: function () {
                showToast('Failed to restore report.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-undo me-1"></i> Restore');
            }
        });
    });

    // ── Permanently delete ────────────────────────────────────────
    $(document).on('click', '.ajax-force-delete', function () {
        const $btn = $(this);
        const id   = $btn.data('id');
        const url  = $btn.data('url');

        if (!confirm('Permanently delete this report? This cannot be undone.')) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i>');

        $.ajax({
            url:    url,
            method: 'DELETE',
            success: function (res) {
                removeArchiveRow(id, res.message, 'success');
            },
            error: function () {
                showToast('Failed to delete report.', 'error');
                $btn.prop('disabled', false).html('<i class="fa fa-trash me-1"></i> Delete');
            }
        });
    });

    function removeArchiveRow(id, message, type) {
        const $row = $('#archive-row-' + id);

        $row.css({
            transition: 'opacity 0.3s ease, transform 0.3s ease',
            opacity: 0,
            transform: 'translateX(20px)'
        });

        setTimeout(function () {
            $row.remove();
            showToast(message, type);

            if ($('#archive-table tbody tr').length === 0) {
                $('#archive-table tbody').append(`
                    <tr id="empty-row">
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fa fa-archive me-2"></i> No archived reports found.
                        </td>
                    </tr>
                `);
            }
        }, 320);
    }

})(jQuery);
</script>
@endpush