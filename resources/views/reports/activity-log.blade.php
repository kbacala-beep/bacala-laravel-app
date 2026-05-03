@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-0">Activity Log</h2>
            <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">
                A full record of all actions taken in the system.
            </p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    {{-- ── Filters ───────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('reports.activityLog') }}">
        <div class="d-flex flex-wrap gap-2 mb-4 align-items-end">

            {{-- Action type --}}
            <select name="action"
                    style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                           padding:9px 32px 9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                           appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236E6E73' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\");
                           background-repeat:no-repeat; background-position:right 10px center; min-width:180px; cursor:pointer;"
                    onchange="this.form.submit()">
                <option value="">All Actions</option>
                @foreach($actionTypes as $type)
                    <option value="{{ $type }}" {{ request('action') === $type ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $type)) }}
                    </option>
                @endforeach
            </select>

            {{-- User --}}
            <select name="user_id"
                    style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                           padding:9px 32px 9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                           appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236E6E73' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E\");
                           background-repeat:no-repeat; background-position:right 10px center; min-width:160px; cursor:pointer;"
                    onchange="this.form.submit()">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>

            {{-- Date from --}}
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:0.8rem; color:var(--text-muted); white-space:nowrap;">From</label>
                <input type="date" name="from" value="{{ request('from') }}"
                       style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                              padding:9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                              color-scheme:dark;">
            </div>

            {{-- Date to --}}
            <div style="display:flex; align-items:center; gap:6px;">
                <label style="font-size:0.8rem; color:var(--text-muted); white-space:nowrap;">To</label>
                <input type="date" name="to" value="{{ request('to') }}"
                       style="background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                              padding:9px 12px; color:var(--text-primary); font-size:0.85rem; outline:none;
                              color-scheme:dark;">
            </div>

            <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                <i class="fa fa-filter me-1"></i> Filter
            </button>

            @if(request()->hasAny(['action', 'user_id', 'from', 'to']))
                <a href="{{ route('reports.activityLog') }}"
                   style="display:inline-flex; align-items:center; gap:6px; padding:9px 14px;
                          background:transparent; border:1px solid var(--border); border-radius:8px;
                          color:var(--text-muted); font-size:0.85rem; text-decoration:none; transition:all 0.2s;"
                   onmouseover="this.style.borderColor='rgba(255,255,255,0.2)'; this.style.color='var(--text-primary)';"
                   onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--text-muted)';">
                    <i class="fa fa-times"></i> Clear
                </a>
            @endif
        </div>
    </form>

    {{-- ── Log entries ───────────────────────────────────────────── --}}
    <div class="d-flex flex-column gap-2" id="log-list">
        @forelse($logs as $log)
            @php
                $config = match($log->action) {
                    'report_created'              => ['color' => '#81C784', 'bg' => 'rgba(46,125,50,0.1)',  'border' => 'rgba(46,125,50,0.25)',  'icon' => 'fa-plus-circle'],
                    'report_updated'              => ['color' => '#FFB74D', 'bg' => 'rgba(245,127,23,0.1)', 'border' => 'rgba(245,127,23,0.25)', 'icon' => 'fa-edit'],
                    'status_updated'              => ['color' => '#81D4FA', 'bg' => 'rgba(2,119,189,0.1)',  'border' => 'rgba(2,119,189,0.25)',  'icon' => 'fa-sliders-h'],
                    'report_archived'             => ['color' => '#FFB74D', 'bg' => 'rgba(245,127,23,0.1)', 'border' => 'rgba(245,127,23,0.25)', 'icon' => 'fa-archive'],
                    'report_restored'             => ['color' => '#81C784', 'bg' => 'rgba(46,125,50,0.1)',  'border' => 'rgba(46,125,50,0.25)',  'icon' => 'fa-undo'],
                    'report_permanently_deleted'  => ['color' => '#EF9A9A', 'bg' => 'rgba(198,40,40,0.1)',  'border' => 'rgba(198,40,40,0.25)',  'icon' => 'fa-trash'],
                    default                       => ['color' => '#AEAEB2', 'bg' => 'rgba(110,110,115,0.1)','border' => 'rgba(110,110,115,0.25)','icon' => 'fa-circle'],
                };
            @endphp

            <div style="background:{{ $config['bg'] }}; border:1px solid {{ $config['border'] }};
                        border-radius:10px; padding:14px 18px; display:flex; align-items:flex-start; gap:14px;">

                {{-- Icon --}}
                <div style="width:34px; height:34px; border-radius:8px; background:{{ $config['border'] }};
                            display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px;">
                    <i class="fa {{ $config['icon'] }}" style="color:{{ $config['color'] }}; font-size:0.85rem;"></i>
                </div>

                {{-- Body --}}
                <div style="flex:1; min-width:0;">
                    <div style="color:var(--text-primary); font-size:0.88rem; line-height:1.4;">
                        {{ $log->description }}
                    </div>
                    <div style="margin-top:5px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <span style="font-size:0.75rem; color:var(--text-muted);">
                            <i class="fa fa-clock me-1"></i>{{ $log->created_at->format('M d, Y H:i') }}
                            <span style="color:var(--border);">·</span>
                            {{ $log->created_at->diffForHumans() }}
                        </span>
                        @if($log->ip_address)
                            <span style="font-size:0.75rem; color:var(--text-muted);">
                                <i class="fa fa-map-marker-alt me-1"></i>{{ $log->ip_address }}
                            </span>
                        @endif
                        @if($log->meta)
                            <button type="button"
                                    onclick="toggleMeta({{ $log->id }})"
                                    style="font-size:0.73rem; color:var(--text-muted); background:none; border:none; padding:0; cursor:pointer; text-decoration:underline;">
                                details
                            </button>
                        @endif
                    </div>

                    {{-- Meta details (expandable) --}}
                    @if($log->meta)
                        <div id="meta-{{ $log->id }}"
                             style="display:none; margin-top:8px; background:rgba(0,0,0,0.2); border-radius:6px;
                                    padding:8px 12px; font-size:0.78rem; font-family:monospace; color:var(--text-muted);">
                            @foreach($log->meta as $key => $value)
                                <div><span style="color:var(--text-primary);">{{ $key }}</span>: {{ $value }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Action badge --}}
                <span style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase;
                             color:{{ $config['color'] }}; background:{{ $config['border'] }}; border-radius:6px;
                             padding:3px 8px; white-space:nowrap; flex-shrink:0;">
                    {{ str_replace('_', ' ', $log->action) }}
                </span>
            </div>
        @empty
            <div style="text-align:center; padding:60px 0; color:var(--text-muted);">
                <i class="fa fa-clipboard-list" style="font-size:2rem; margin-bottom:12px; display:block; opacity:0.4;"></i>
                No activity log entries found.
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
function toggleMeta(id) {
    const el = document.getElementById('meta-' + id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush