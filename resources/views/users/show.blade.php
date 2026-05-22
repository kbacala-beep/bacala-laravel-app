@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">User Profile</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Users
        </a>
    </div>

    <div class="row g-4">

        {{-- ── Left: Profile Card ───────────────────────────────── --}}
        <div class="col-md-4">
            <div class="card h-auto">
                <div class="card-body text-center py-4">
                    <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('img/default-user.png') }}"
                         style="width:88px; height:88px; border-radius:50%; object-fit:cover; border:3px solid var(--border); margin-bottom:16px;"
                         alt="{{ $user->name }}">
                    <h5 style="color:var(--text-primary); font-weight:600; margin-bottom:4px;">{{ $user->name }}</h5>
                    <div style="color:var(--text-muted); font-size:0.85rem; margin-bottom:12px;">{{ $user->email }}</div>

                    {{-- Status badge --}}
                    @if($user->is_suspended)
                        <span style="display:inline-flex; align-items:center; gap:5px; background:rgba(239,83,80,0.1); border:1px solid rgba(239,83,80,0.3); border-radius:20px; padding:4px 14px; font-size:0.8rem; color:#EF5350;">
                            <i class="fa fa-ban" style="font-size:0.7rem;"></i> Suspended
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:5px; background:rgba(102,187,106,0.1); border:1px solid rgba(102,187,106,0.25); border-radius:20px; padding:4px 14px; font-size:0.8rem; color:#66BB6A;">
                            <i class="fa fa-circle" style="font-size:0.55rem;"></i> Active
                        </span>
                    @endif
                </div>

                <div style="border-top:1px solid var(--border); padding:20px 24px;">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Role</div>
                            <div style="font-size:0.88rem; color:var(--text-primary);">
                                {{ is_object($user->role) ? $user->role->name : ($user->role ?? 'Resident') }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Member Since</div>
                            <div style="font-size:0.88rem; color:var(--text-primary);">{{ $user->created_at->format('M d, Y') }}</div>
                        </div>
                        @if($user->phone)
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Phone</div>
                                <div style="font-size:0.88rem; color:var(--text-primary);">{{ $user->phone }}</div>
                            </div>
                        @endif
                        @if($user->address)
                            <div>
                                <div style="font-size:0.72rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Address</div>
                                <div style="font-size:0.88rem; color:var(--text-primary);">{{ $user->address }}</div>
                            </div>
                        @endif
                        @if($user->is_suspended && $user->suspension_reason)
                            <div>
                                <div style="font-size:0.72rem; color:#EF5350; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Suspension Reason</div>
                                <div style="font-size:0.85rem; color:#EF9A9A;">{{ $user->suspension_reason }}</div>
                            </div>
                        @endif
                        @if($user->is_suspended && $user->suspended_at)
                            <div>
                                <div style="font-size:0.72rem; color:#EF5350; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px;">Suspended On</div>
                                <div style="font-size:0.85rem; color:#EF9A9A;">{{ $user->suspended_at->format('M d, Y H:i') }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Suspend / Activate action --}}
                <div style="border-top:1px solid var(--border); padding:16px 24px;">
                    @if($user->is_suspended)
                        <button type="button" class="btn btn-success w-100 btn-activate"
                                data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                            <i class="fa fa-user-check me-1"></i> Reactivate Account
                        </button>
                    @else
                        <button type="button" class="btn btn-danger w-100 btn-suspend"
                                data-id="{{ $user->id }}" data-name="{{ $user->name }}">
                            <i class="fa fa-ban me-1"></i> Suspend Account
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Right: Stats + Reports ───────────────────────────── --}}
        <div class="col-md-8">

            {{-- Stats row --}}
            <div class="row g-3 mb-4">
                @php
                    $stats = [
                        ['label' => 'Total Reports', 'value' => $reportStats['total'],       'color' => 'var(--primary-hover)', 'bg' => 'rgba(198,40,40,0.1)',   'icon' => 'fa-file-alt'],
                        ['label' => 'Pending',        'value' => $reportStats['pending'],     'color' => '#FBC02D',              'bg' => 'rgba(251,192,45,0.1)',  'icon' => 'fa-clock'],
                        ['label' => 'In Progress',    'value' => $reportStats['in_progress'], 'color' => '#81D4FA',              'bg' => 'rgba(2,119,189,0.1)',   'icon' => 'fa-spinner'],
                        ['label' => 'Resolved',       'value' => $reportStats['resolved'],    'color' => '#66BB6A',              'bg' => 'rgba(102,187,106,0.1)', 'icon' => 'fa-check-circle'],
                    ];
                @endphp
                @foreach($stats as $stat)
                    <div class="col-6 col-sm-3">
                        <div class="p-3 rounded d-flex align-items-center gap-3" style="background:var(--surface-02); border:1px solid var(--border);">
                            <div style="width:38px; height:38px; border-radius:9px; background:{{ $stat['bg'] }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="fa {{ $stat['icon'] }}" style="color:{{ $stat['color'] }}; font-size:0.9rem;"></i>
                            </div>
                            <div>
                                <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">{{ $stat['label'] }}</div>
                                <div style="font-size:1.3rem; font-weight:600; color:var(--text-primary); line-height:1.2;">{{ $stat['value'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Reports table --}}
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>Reports by {{ $user->name }}</span>
                    @if($lastActive)
                        <span style="font-size:0.78rem; color:var(--text-muted);">
                            Last active {{ \Carbon\Carbon::parse($lastActive)->diffForHumans() }}
                        </span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Category</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $i => $report)
                                    <tr>
                                        <td style="font-size:0.82rem; color:var(--text-muted);">
                                            {{ ($reports->currentPage()-1)*$reports->perPage()+$i+1 }}
                                        </td>
                                        <td>
                                            @if($report->category)
                                                <span style="display:inline-flex; align-items:center; gap:5px;
                                                             background:{{ $report->category->color }}1a;
                                                             border:1px solid {{ $report->category->color }}4d;
                                                             border-radius:20px; padding:3px 10px;
                                                             font-size:0.75rem; color:{{ $report->category->color }}; white-space:nowrap;">
                                                    <i class="fa {{ $report->category->icon }}" style="font-size:0.65rem;"></i>
                                                    {{ $report->category->name }}
                                                </span>
                                            @else
                                                <span style="color:var(--text-muted); font-size:0.82rem;">—</span>
                                            @endif
                                        </td>
                                        <td style="font-size:0.88rem;">{{ $report->subject }}</td>
                                        <td>
                                            <span class="badge
                                                @if($report->status === 'Pending') bg-warning
                                                @elseif($report->status === 'Resolved') bg-success
                                                @else bg-primary @endif">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td style="font-size:0.82rem; color:var(--text-muted);">
                                            {{ $report->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('reports.show', $report->id) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4" style="color:var(--text-muted); font-size:0.85rem;">
                                            <i class="fa fa-inbox me-2"></i> No reports submitted yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($reports->hasPages())
                    <div class="card-footer">{{ $reports->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Suspend Modal --}}
<div id="suspendModal" style="display:none; position:fixed; inset:0; z-index:9000; align-items:center; justify-content:center;">
    <div style="position:absolute; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(4px);" onclick="closeSuspendModal()"></div>
    <div style="position:relative; z-index:1; background:var(--surface-02); border:1px solid var(--border-strong);
                border-radius:var(--radius-lg); padding:28px 32px; width:100%; max-width:440px; box-shadow:var(--shadow-lg);">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0" style="font-weight:600; color:#EF5350;"><i class="fa fa-ban me-2"></i>Suspend User</h5>
            <button onclick="closeSuspendModal()" style="background:transparent; border:none; color:var(--text-muted); font-size:1.1rem; cursor:pointer;">
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
                             padding:10px 14px; color:var(--text-primary); font-size:0.85rem; outline:none; resize:vertical; font-family:inherit;"
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

@push('scripts')
<script>
(function($) {
    "use strict";

    let activeSuspendId = null;

    $(document).on('click', '.btn-suspend', function () {
        activeSuspendId = $(this).data('id');
        $('#suspend-user-name').text($(this).data('name'));
        $('#suspend-reason').val('');
        const $m = $('#suspendModal');
        $m.css('display', 'flex').css('opacity', '0');
        setTimeout(() => $m.css({ opacity: '1', transition: 'opacity 0.2s ease' }), 10);
    });

    window.closeSuspendModal = function () {
        $('#suspendModal').css('opacity', '0');
        setTimeout(() => $('#suspendModal').css('display', 'none'), 200);
        activeSuspendId = null;
    };

    window.confirmSuspend = function () {
        if (!activeSuspendId) return;
        const $btn = $('#suspend-confirm-btn');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Suspending...');

        $.ajax({
            url:    '/users/' + activeSuspendId + '/suspend',
            method: 'POST',
            data:   { suspension_reason: $('#suspend-reason').val() },
            success: function (res) {
                showToast(res.message, 'success');
                closeSuspendModal();
                setTimeout(() => window.location.reload(), 800);
            },
            error: function () { showToast('Failed to suspend user.', 'error'); },
            complete: function () { $btn.prop('disabled', false).html('<i class="fa fa-ban me-1"></i> Suspend Account'); }
        });
    };

    $(document).on('click', '.btn-activate', function () {
        const id   = $(this).data('id');
        const name = $(this).data('name');
        if (!confirm('Reactivate ' + name + '\'s account?')) return;
        const $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url:    '/users/' + id + '/activate',
            method: 'POST',
            success: function (res) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.reload(), 800);
            },
            error: function () {
                showToast('Failed to reactivate user.', 'error');
                $btn.prop('disabled', false);
            }
        });
    });

    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && activeSuspendId) closeSuspendModal();
    });

})(jQuery);
</script>
@endpush