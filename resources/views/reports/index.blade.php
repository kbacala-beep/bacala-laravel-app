@extends('layouts.app')

@section('title', 'Barangay Reports')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">
    <h2 class="mb-4">Barangay Reports</h2>

    @php
        $role        = Auth::user()->role;
        $currentRole = strtolower(is_object($role) ? $role->name : ($role ?? 'resident'));
        $isAdmin     = $currentRole === 'admin';
    @endphp

    @if(!$isAdmin)
        <a href="{{ route('reports.create') }}" class="btn btn-primary mb-3">
            <i class="fa fa-plus me-1"></i> Add New Report
        </a>
    @endif

    {{-- ── Filter Bar ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('reports.index') }}" id="filter-form">
        <div class="d-flex flex-wrap gap-2 mb-4 align-items-end">

            {{-- Search --}}
            <div style="position:relative; flex: 1; min-width: 200px;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.8rem; pointer-events:none;">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search resident, subject..."
                       style="width:100%; background:var(--surface-03); border:1px solid var(--border);
                              border-radius:8px; padding:9px 14px 9px 34px; color:var(--text-primary);
                              font-size:0.85rem; outline:none; transition:border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px var(--primary-glow)';"
                       onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            </div>

            {{-- Status --}}
            <select name="status"
                    style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                           padding:9px 32px 9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                           appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236E6E73' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\");
                           background-repeat:no-repeat; background-position:right 10px center; min-width:140px; cursor:pointer;"
                    onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending"     {{ request('status') === 'Pending'     ? 'selected' : '' }}>Pending</option>
                <option value="In Progress" {{ request('status') === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Resolved"    {{ request('status') === 'Resolved'    ? 'selected' : '' }}>Resolved</option>
            </select>

            {{-- Category --}}
            <select name="category_id"
                    style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                           padding:9px 32px 9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                           appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236E6E73' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\");
                           background-repeat:no-repeat; background-position:right 10px center; min-width:160px; cursor:pointer;"
                    onchange="this.form.submit()">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                <i class="fa fa-search me-1"></i> Search
            </button>

            @if(request()->hasAny(['search', 'status', 'category_id']))
                <a href="{{ route('reports.index') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:9px 14px;
                          background:transparent; border:1px solid var(--border); border-radius:8px;
                          color:var(--text-muted); font-size:0.85rem; text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='var(--text-primary)';"
                   onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-muted)';">
                    <i class="fa fa-times"></i> Clear
                </a>
            @endif
        </div>

        {{-- Active filter summary --}}
        @if(request()->hasAny(['search', 'status', 'category_id']))
            <div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
                <span style="font-size:0.78rem; color:var(--text-muted);">Filtering by:</span>
                @if(request('search'))
                    <span style="background:rgba(198,40,40,0.12); border:1px solid rgba(198,40,40,0.3); border-radius:20px; padding:3px 10px; font-size:0.78rem; color:var(--primary-hover);">
                        "{{ request('search') }}"
                    </span>
                @endif
                @if(request('status'))
                    <span style="background:rgba(198,40,40,0.12); border:1px solid rgba(198,40,40,0.3); border-radius:20px; padding:3px 10px; font-size:0.78rem; color:var(--primary-hover);">
                        {{ request('status') }}
                    </span>
                @endif
                @if(request('category_id'))
                    @php $activeCat = $categories->firstWhere('id', request('category_id')); @endphp
                    @if($activeCat)
                        <span style="background:rgba(198,40,40,0.12); border:1px solid rgba(198,40,40,0.3); border-radius:20px; padding:3px 10px; font-size:0.78rem; color:var(--primary-hover);">
                            {{ $activeCat->name }}
                        </span>
                    @endif
                @endif
            </div>
        @endif
    </form>

    @if($isAdmin)
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- ADMIN: Single flat table (original behaviour)             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle rounded overflow-hidden" id="reports-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Resident</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Submitted By</th>
                    <th>Attachments</th>
                    <th>Submitted</th>
                    <th class="col-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $index => $report)
                    @php
                        $photos    = $report->attachments->filter(fn($a) => str_starts_with($a->file_type, 'image'))->count();
                        $documents = $report->attachments->filter(fn($a) => $a->file_type === 'application/pdf')->count();
                        $parts = [];
                        if ($photos > 0)    $parts[] = $photos    . ' ' . ($photos    === 1 ? 'photo'    : 'photos');
                        if ($documents > 0) $parts[] = $documents . ' ' . ($documents === 1 ? 'document' : 'documents');
                        $attachmentSummary = count($parts) ? implode(' · ', $parts) : null;
                    @endphp
                    <tr id="report-row-{{ $report->id }}" data-id="{{ $report->id }}">
                        <td>{{ ($reports->currentPage() - 1) * $reports->perPage() + $index + 1 }}</td>
                        <td><strong>{{ $report->resident_name }}</strong></td>
                        <td>
                            @if($report->category)
                                <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $report->category->color }}1a;
                                             border:1px solid {{ $report->category->color }}4d; border-radius:20px;
                                             padding:3px 10px; font-size:0.78rem; color:{{ $report->category->color }}; white-space:nowrap;">
                                    <i class="fa {{ $report->category->icon }}" style="font-size:0.7rem;"></i>
                                    {{ $report->category->name }}
                                </span>
                            @else
                                <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                            @endif
                        </td>
                        <td>{{ $report->subject }}</td>
                        <td>
                            <select class="status-select form-select form-select-sm"
                                    data-id="{{ $report->id }}"
                                    data-url="{{ route('reports.update', $report->id) }}"
                                    style="width:auto; min-width:130px; font-size:0.8rem;">
                                <option value="Pending"     {{ $report->status === 'Pending'     ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ $report->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Resolved"    {{ $report->status === 'Resolved'    ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </td>
                        <td>
                            {{ $report->user->name ?? 'N/A' }}
                            <small class="text-muted">
                                ({{ is_object($report->user->role ?? null) ? $report->user->role->name : ($report->user->role ?? 'Resident') }})
                            </small>
                        </td>
                        <td>
                            @if($attachmentSummary)
                                <span style="color:var(--primary-hover); font-size:0.85rem;">
                                    <i class="fa fa-paperclip me-1"></i>{{ $attachmentSummary }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.85rem;">None</span>
                            @endif
                        </td>
                        <td>{{ $report->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye me-1"></i> View
                            </a>
                            <button type="button" class="btn btn-danger btn-sm ajax-archive"
                                    data-id="{{ $report->id }}"
                                    data-url="{{ route('reports.destroy', $report->id) }}">
                                <i class="fa fa-archive me-1"></i> Archive
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr id="empty-row">
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa fa-search me-2"></i>
                            {{ request()->hasAny(['search','status','category_id']) ? 'No reports match your filters.' : 'No reports found.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $reports->links() }}</div>

    @else
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- RESIDENT: Pinned "My Reports" + "Other Reports" below     --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    {{-- ── My Reports (pinned) ─────────────────────────────────── --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span style="width:4px; height:18px; background:var(--primary); border-radius:2px; flex-shrink:0; display:inline-block;"></span>
            <h6 class="mb-0" style="font-weight:600; color:var(--text-primary);">My Reports</h6>
            <span style="background:rgba(198,40,40,0.15); border:1px solid rgba(198,40,40,0.3); border-radius:20px; padding:1px 8px; font-size:0.72rem; color:var(--primary-hover);">
                {{ $myReports->total() }} {{ Str::plural('report', $myReports->total()) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="my-reports-table" style="border-color: rgba(198,40,40,0.2);">
                <thead style="background: rgba(198,40,40,0.08);">
                    <tr>
                        <th>#</th>
                        <th>Resident</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Attachments</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myReports as $index => $report)
                        @php
                            $photos    = $report->attachments->filter(fn($a) => str_starts_with($a->file_type, 'image'))->count();
                            $documents = $report->attachments->filter(fn($a) => $a->file_type === 'application/pdf')->count();
                            $parts = [];
                            if ($photos > 0)    $parts[] = $photos    . ' ' . ($photos    === 1 ? 'photo'    : 'photos');
                            if ($documents > 0) $parts[] = $documents . ' ' . ($documents === 1 ? 'document' : 'documents');
                            $attachmentSummary = count($parts) ? implode(' · ', $parts) : null;
                        @endphp
                        <tr id="report-row-{{ $report->id }}" data-id="{{ $report->id }}">
                            <td>{{ ($myReports->currentPage() - 1) * $myReports->perPage() + $index + 1 }}</td>
                            <td><strong>{{ $report->resident_name }}</strong></td>
                            <td>
                                @if($report->category)
                                    <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $report->category->color }}1a;
                                                 border:1px solid {{ $report->category->color }}4d; border-radius:20px;
                                                 padding:3px 10px; font-size:0.78rem; color:{{ $report->category->color }}; white-space:nowrap;">
                                        <i class="fa {{ $report->category->icon }}" style="font-size:0.7rem;"></i>
                                        {{ $report->category->name }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                                @endif
                            </td>
                            <td>{{ $report->subject }}</td>
                            <td>
                                <span class="badge
                                    @if($report->status === 'Pending') bg-warning
                                    @elseif($report->status === 'Resolved') bg-success
                                    @else bg-primary @endif">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td>
                                @if($attachmentSummary)
                                    <span style="color:var(--primary-hover); font-size:0.85rem;">
                                        <i class="fa fa-paperclip me-1"></i>{{ $attachmentSummary }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:0.85rem;">None</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-pencil-alt me-1"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger btn-sm ajax-delete"
                                        data-id="{{ $report->id }}"
                                        data-url="{{ route('reports.destroy', $report->id) }}">
                                    <i class="fa fa-trash me-1"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                                <i class="fa fa-inbox me-2"></i> You haven't submitted any reports yet.
                                <a href="{{ route('reports.create') }}" style="color:var(--primary-hover); margin-left:4px;">Submit one now</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($myReports->hasPages())
            <div class="mt-2">{{ $myReports->appends(request()->query())->links() }}</div>
        @endif
    </div>

    {{-- ── Other Reports ────────────────────────────────────────── --}}
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span style="width:4px; height:18px; background:var(--surface-04); border-radius:2px; flex-shrink:0; display:inline-block;"></span>
            <h6 class="mb-0" style="font-weight:600; color:var(--text-secondary);">Other Reports</h6>
            <span style="background:var(--surface-03); border:1px solid var(--border); border-radius:20px; padding:1px 8px; font-size:0.72rem; color:var(--text-muted);">
                {{ $otherReports->total() }} {{ Str::plural('report', $otherReports->total()) }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="other-reports-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Resident</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted By</th>
                        <th>Attachments</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($otherReports as $index => $report)
                        @php
                            $photos    = $report->attachments->filter(fn($a) => str_starts_with($a->file_type, 'image'))->count();
                            $documents = $report->attachments->filter(fn($a) => $a->file_type === 'application/pdf')->count();
                            $parts = [];
                            if ($photos > 0)    $parts[] = $photos    . ' ' . ($photos    === 1 ? 'photo'    : 'photos');
                            if ($documents > 0) $parts[] = $documents . ' ' . ($documents === 1 ? 'document' : 'documents');
                            $attachmentSummary = count($parts) ? implode(' · ', $parts) : null;
                        @endphp
                        <tr id="report-row-{{ $report->id }}" data-id="{{ $report->id }}">
                            <td>{{ ($otherReports->currentPage() - 1) * $otherReports->perPage() + $index + 1 }}</td>
                            <td><strong>{{ $report->resident_name }}</strong></td>
                            <td>
                                @if($report->category)
                                    <span style="display:inline-flex; align-items:center; gap:5px; background:{{ $report->category->color }}1a;
                                                 border:1px solid {{ $report->category->color }}4d; border-radius:20px;
                                                 padding:3px 10px; font-size:0.78rem; color:{{ $report->category->color }}; white-space:nowrap;">
                                        <i class="fa {{ $report->category->icon }}" style="font-size:0.7rem;"></i>
                                        {{ $report->category->name }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                                @endif
                            </td>
                            <td>{{ $report->subject }}</td>
                            <td>
                                <span class="badge
                                    @if($report->status === 'Pending') bg-warning
                                    @elseif($report->status === 'Resolved') bg-success
                                    @else bg-primary @endif">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td>
                                {{ $report->user->name ?? 'N/A' }}
                                <small class="text-muted">
                                    ({{ is_object($report->user->role ?? null) ? $report->user->role->name : ($report->user->role ?? 'Resident') }})
                                </small>
                            </td>
                            <td>
                                @if($attachmentSummary)
                                    <span style="color:var(--primary-hover); font-size:0.85rem;">
                                        <i class="fa fa-paperclip me-1"></i>{{ $attachmentSummary }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size:0.85rem;">None</span>
                                @endif
                            </td>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                                <i class="fa fa-search me-2"></i>
                                {{ request()->hasAny(['search','status','category_id']) ? 'No other reports match your filters.' : 'No other reports found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($otherReports->hasPages())
            <div class="mt-2">{{ $otherReports->appends(request()->query())->links() }}</div>
        @endif
    </div>
    @endif {{-- end resident view --}}

</div>
@endsection

@push('scripts')
<script>
(function($) {
    "use strict";

    // ── Inline status update (admin only) ────────────────────────
    $(document).on('change', '.status-select', function () {
        const $select  = $(this);
        const url      = $select.data('url');
        const status   = $select.val();
        const original = $select.data('original') || $select.find('option[selected]').val() || $select.val();

        $select.prop('disabled', true);

        $.ajax({
            url: url, method: 'PUT',
            data: {
                status: status,
                resident_name: $select.closest('tr').find('td:nth-child(2) strong').text(),
                _method: 'PUT',
            },
            success: function (res) {
                showToast(res.message, 'success');
                $select.data('original', status);
            },
            error: function () { $select.val(original); },
            complete: function () { $select.prop('disabled', false); }
        });
    });

    // ── AJAX archive (admin) ──────────────────────────────────────
    $(document).on('click', '.ajax-archive', function () {
        const $btn = $(this);
        if (!confirm('Archive this report?')) return;
        $btn.prop('disabled', true);
        $.ajax({
            url: $btn.data('url'), method: 'DELETE',
            success: function (res) { removeRow($btn.data('id'), res.message, 9); },
            error:   function ()    { $btn.prop('disabled', false); }
        });
    });

    // ── AJAX delete (resident, own reports) ──────────────────────
    $(document).on('click', '.ajax-delete', function () {
        const $btn = $(this);
        if (!confirm('Are you sure you want to delete this report?')) return;
        $btn.prop('disabled', true);
        $.ajax({
            url: $btn.data('url'), method: 'DELETE',
            success: function (res) { removeRow($btn.data('id'), res.message, 8); },
            error:   function ()    { $btn.prop('disabled', false); }
        });
    });

    function removeRow(id, message, cols) {
        const $row = $('#report-row-' + id);
        $row.css({ transition: 'opacity 0.3s ease, transform 0.3s ease', opacity: 0, transform: 'translateX(20px)' });
        setTimeout(function () {
            $row.remove();
            showToast(message, 'success');
        }, 320);
    }

})(jQuery);
</script>
@endpush
