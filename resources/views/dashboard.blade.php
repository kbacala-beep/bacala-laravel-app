@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    @php
        $role     = Auth::user()->role;
        $isAdmin  = strtolower(is_object($role) ? $role->name : ($role ?? 'resident')) === 'admin';
    @endphp

    @if($isAdmin)
    {{-- ── Admin: System-Wide Stats ─────────────────────────────── --}}
    <h2 class="mb-1">Dashboard</h2>
    <p style="color:var(--text-muted); font-size:0.85rem;" class="mb-4">System overview</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(198,40,40,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-users" style="color:var(--primary-hover); font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Users</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $totalUsers }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(198,40,40,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-file-alt" style="color:var(--primary-hover); font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Reports</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $totalReports }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(198,40,40,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-calendar-day" style="color:var(--primary-hover); font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Reports Today</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $reportsToday }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(198,40,40,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-paperclip" style="color:var(--primary-hover); font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Attachments</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $attachmentsCount }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status breakdown --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:10px; height:10px; border-radius:50%; background:#FFC107; flex-shrink:0;"></div>
                <div style="flex:1;">
                    <div style="font-size:0.78rem; color:var(--text-muted);">Pending</div>
                    <div style="font-size:1.2rem; font-weight:600; color:var(--text-primary);">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:10px; height:10px; border-radius:50%; background:#42A5F5; flex-shrink:0;"></div>
                <div style="flex:1;">
                    <div style="font-size:0.78rem; color:var(--text-muted);">In Progress</div>
                    <div style="font-size:1.2rem; font-weight:600; color:var(--text-primary);">{{ $inProgressCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:10px; height:10px; border-radius:50%; background:#66BB6A; flex-shrink:0;"></div>
                <div style="flex:1;">
                    <div style="font-size:0.78rem; color:var(--text-muted);">Resolved</div>
                    <div style="font-size:1.2rem; font-weight:600; color:var(--text-primary);">{{ $resolvedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    @else
    {{-- ── Resident: My Stats ────────────────────────────────────── --}}
    <h2 class="mb-1">Welcome back, {{ Auth::user()->name }}</h2>
    <p style="color:var(--text-muted); font-size:0.85rem;" class="mb-4">Here's a summary of your reports</p>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(198,40,40,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-file-alt" style="color:var(--primary-hover); font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">My Reports</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $myTotal }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(255,193,7,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-clock" style="color:#FFC107; font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Pending</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $myPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(66,165,245,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-spinner" style="color:#42A5F5; font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">In Progress</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $myInProgress }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="rounded p-4 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:48px; height:48px; border-radius:12px; background:rgba(102,187,106,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-check-circle" style="color:#66BB6A; font-size:1.2rem;"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Resolved</div>
                    <div style="font-size:1.6rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $myResolved }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Recent Reports ────────────────────────────────────────── --}}
    <div class="rounded p-4" style="background:var(--surface-02); border:1px solid var(--border);">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0" style="font-weight:500;">
                {{ $isAdmin ? 'Recent Reports' : 'My Recent Reports' }}
            </h6>
            <a href="{{ route('reports.index') }}" style="font-size:0.83rem; color:var(--primary-hover);">
                View All <i class="fa fa-arrow-right ms-1" style="font-size:0.75rem;"></i>
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>#</th>
                        @if($isAdmin)<th>Submitted By</th>@endif
                        <th>Subject</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentReports as $report)
                        <tr>
                            <td style="white-space:nowrap; font-size:0.82rem; color:var(--text-muted);">{{ $report->created_at->format('M d, Y') }}</td>
                            <td style="font-size:0.82rem; color:var(--text-muted);">#{{ $report->id }}</td>
                            @if($isAdmin)
                                <td style="font-size:0.85rem;">{{ $report->user->name ?? 'N/A' }}</td>
                            @endif
                            <td style="font-size:0.85rem;">{{ $report->subject }}</td>
                            <td>
                                @if($report->category)
                                    <span style="display:inline-flex; align-items:center; gap:4px; background:{{ $report->category->color }}1a; border:1px solid {{ $report->category->color }}4d; border-radius:20px; padding:2px 8px; font-size:0.75rem; color:{{ $report->category->color }}; white-space:nowrap;">
                                        <i class="fa {{ $report->category->icon }}" style="font-size:0.65rem;"></i>
                                        {{ $report->category->name }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge
                                    @if($report->status === 'Pending') bg-warning
                                    @elseif($report->status === 'Resolved') bg-success
                                    @else bg-primary @endif"
                                    style="font-size:0.72rem;">
                                    {{ $report->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-sm btn-info" style="font-size:0.78rem; padding:3px 10px;">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 6 }}" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                                <i class="fa fa-inbox me-2"></i> No reports yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
