@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <h2 class="mb-1">User Management</h2>
    <p style="color:var(--text-muted); font-size:0.85rem;" class="mb-4">
        Manage resident accounts and monitor report activity. Your own account is excluded.
    </p>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:40px; height:40px; border-radius:10px; background:rgba(198,40,40,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-users" style="color:var(--primary-hover);"></i>
                </div>
                <div>
                    <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Total Residents</div>
                    <div style="font-size:1.3rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $residentStats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:40px; height:40px; border-radius:10px; background:rgba(102,187,106,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-user-check" style="color:#66BB6A;"></i>
                </div>
                <div>
                    <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Active</div>
                    <div style="font-size:1.3rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $residentStats['active'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="rounded p-3 d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                <div style="width:40px; height:40px; border-radius:10px; background:rgba(239,83,80,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa fa-user-slash" style="color:#EF5350;"></i>
                </div>
                <div>
                    <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Suspended</div>
                    <div style="font-size:1.3rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $residentStats['suspended'] }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('users.index') }}">
        <div class="d-flex flex-wrap gap-2 mb-4 align-items-end">
            <div style="position:relative; flex:1; min-width:200px;">
                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.8rem; pointer-events:none;">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name or email..."
                       style="width:100%; background:var(--surface-03); border:1px solid var(--border);
                              border-radius:8px; padding:9px 14px 9px 34px; color:var(--text-primary);
                              font-size:0.85rem; outline:none; transition:border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px var(--primary-glow)';"
                       onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';">
            </div>
            <button type="submit" class="btn btn-primary" style="padding:9px 18px;">
                <i class="fa fa-search me-1"></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('users.index') }}"
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

    {{-- Admins --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span style="width:4px; height:18px; background:var(--primary); border-radius:2px; flex-shrink:0; display:inline-block;"></span>
            <h6 class="mb-0" style="font-weight:600; color:var(--text-primary);">Admins</h6>
            <span style="background:rgba(198,40,40,0.15); border:1px solid rgba(198,40,40,0.3); border-radius:20px; padding:1px 8px; font-size:0.72rem; color:var(--primary-hover);">
                {{ $admins->total() }} {{ Str::plural('user', $admins->total()) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" style="border-color:rgba(198,40,40,0.2);">
                <thead style="background:rgba(198,40,40,0.08);">
                    <tr><th>#</th><th>User</th><th>Email</th><th>Reports</th><th>Joined</th></tr>
                </thead>
                <tbody>
                    @forelse($admins as $i => $user)
                        <tr>
                            <td style="font-size:0.82rem; color:var(--text-muted);">{{ ($admins->currentPage()-1)*$admins->perPage()+$i+1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('img/default-user.png') }}"
                                         style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid var(--border); flex-shrink:0;" alt="">
                                    <span style="font-weight:500; color:var(--text-primary);">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);">{{ $user->email }}</td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);">{{ $user->reports_count }}</td>
                            <td style="font-size:0.82rem; color:var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                            <i class="fa fa-shield-alt me-2"></i>{{ request('search') ? 'No admins match your search.' : 'No other admins found.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($admins->hasPages())
            <div class="mt-2">{{ $admins->appends(request()->query())->links() }}</div>
        @endif
    </div>

    {{-- Residents --}}
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span style="width:4px; height:18px; background:var(--surface-04); border-radius:2px; flex-shrink:0; display:inline-block;"></span>
            <h6 class="mb-0" style="font-weight:600; color:var(--text-secondary);">Residents</h6>
            <span style="background:var(--surface-03); border:1px solid var(--border); border-radius:20px; padding:1px 8px; font-size:0.72rem; color:var(--text-muted);">
                {{ $residents->total() }} {{ Str::plural('user', $residents->total()) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th><th>User</th><th>Email</th><th>Status</th>
                        <th>Reports</th><th>Last Report</th><th>Joined</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($residents as $i => $user)
                        <tr id="resident-row-{{ $user->id }}">
                            <td style="font-size:0.82rem; color:var(--text-muted);">{{ ($residents->currentPage()-1)*$residents->perPage()+$i+1 }}</td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="d-flex align-items-center gap-2" style="text-decoration:none;">
                                    <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('img/default-user.png') }}"
                                         style="width:34px; height:34px; border-radius:50%; object-fit:cover; border:1px solid var(--border); flex-shrink:0;" alt="">
                                    <span style="font-weight:500; color:var(--text-primary);">{{ $user->name }}</span>
                                </a>
                            </td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);">{{ $user->email }}</td>
                            <td>
                                @if($user->is_suspended)
                                    <span style="display:inline-flex; align-items:center; gap:4px; background:rgba(239,83,80,0.1); border:1px solid rgba(239,83,80,0.3); border-radius:20px; padding:2px 10px; font-size:0.75rem; color:#EF5350;">
                                        <i class="fa fa-ban" style="font-size:0.65rem;"></i> Suspended
                                    </span>
                                @else
                                    <span style="display:inline-flex; align-items:center; gap:4px; background:rgba(102,187,106,0.1); border:1px solid rgba(102,187,106,0.25); border-radius:20px; padding:2px 10px; font-size:0.75rem; color:#66BB6A;">
                                        <i class="fa fa-circle" style="font-size:0.5rem;"></i> Active
                                    </span>
                                @endif
                            </td>
                            <td style="font-size:0.85rem; color:var(--text-secondary);">{{ $user->reports_count }}</td>
                            <td style="font-size:0.82rem; color:var(--text-muted);">
                                {{ $user->reports()->latest()->value('created_at') ? \Carbon\Carbon::parse($user->reports()->latest()->value('created_at'))->format('M d, Y') : '—' }}
                            </td>
                            <td style="font-size:0.82rem; color:var(--text-muted);">{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye me-1"></i> View
                                </a>
                                @if($user->is_suspended)
                                    <button type="button" class="btn btn-success btn-sm btn-activate"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-url="{{ route('users.activate', $user->id) }}">
                                        <i class="fa fa-user-check me-1"></i> Activate
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger btn-sm btn-suspend"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-url="{{ route('users.suspend', $user->id) }}">
                                        <i class="fa fa-ban me-1"></i> Suspend
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                            <i class="fa fa-users me-2"></i>{{ request('search') ? 'No residents match your search.' : 'No residents found.' }}
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($residents->hasPages())
            <div class="mt-2">{{ $residents->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display:none; position:fixed; inset:0; z-index:9000; align-items:center; justify-content:center;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px);" onclick="closeSuspendModal()"></div>
    <div style="position:relative; z-index:1; background:var(--surface-02); border:1px solid var(--border-strong);
                border-radius:var(--radius-lg); padding:28px 32px; width:100%; max-width:440px; box-shadow:var(--shadow-lg);">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0" style="font-weight:600; color:#EF5350;"><i class="fa fa-ban me-2"></i>Suspend User</h5>
            <button onclick="closeSuspendModal()" style="background:transparent; border:none; color:var(--text-muted); font-size:1.1rem; cursor:pointer; padding:4px 8px; border-radius:6px;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <p style="font-size:0.88rem; color:var(--text-secondary); margin-bottom:16px;">
            You are about to suspend <strong id="suspend-user-name" style="color:var(--text-primary);"></strong>.
            They will not be able to log in until reactivated.
        </p>
        <div class="mb-4">
            <label style="font-size:0.82rem; color:var(--text-secondary); margin-bottom:6px; display:block;">Reason <span style="color:var(--text-muted);">(optional)</span></label>
            <textarea id="suspend-reason" rows="3" placeholder="e.g. Submitted false reports repeatedly..."
                      style="width:100%; background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                             padding:10px 14px; color:var(--text-primary); font-size:0.85rem; outline:none; resize:vertical;
                             transition:border-color 0.2s, box-shadow 0.2s; font-family:inherit;"
                      onfocus="this.style.borderColor='var(--primary)'; this.style.boxShadow='0 0 0 3px var(--primary-glow)';"
                      onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none';"></textarea>
        </div>
        <div class="d-flex gap-2 justify-content-end">
            <button onclick="closeSuspendModal()" class="btn btn-secondary">Cancel</button>
            <button id="suspend-confirm-btn" onclick="confirmSuspend()" class="btn btn-danger">
                <i class="fa fa-ban me-1"></i> Suspend Account
            </button>
        </div>
    </div>
</div>
@endsection