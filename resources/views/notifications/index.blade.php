@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5" style="background: var(--bg); min-height: 100vh;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Notifications</h2>
        <a href="{{ route('notifications.preferences') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-cog me-1"></i> Notification Settings
        </a>
    </div>

    @if($notifications->count() > 0)
        <div class="row">
            <div class="col-md-8">
                <div class="card" style="background: var(--surface-02); border: 1px solid var(--border);">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notif)
                                <div class="list-group-item" style="border-bottom: 1px solid var(--border); padding: 16px 20px; @if(!$notif->read)background: rgba(198,40,40,0.05);@endif">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 style="color: var(--text-primary); font-weight: 600; margin: 0; flex: 1;">
                                            @if(!$notif->read)
                                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: var(--primary-hover); margin-right: 8px;"></span>
                                            @endif
                                            {{ $notif->title }}
                                        </h5>
                                        <form action="{{ route('notifications.delete', $notif->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link" style="color: var(--text-muted); text-decoration: none; padding: 0;">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <p style="color: var(--text-secondary); font-size: 0.95rem; margin: 0 0 8px 0;">{{ $notif->message }}</p>
                                    <div style="display: flex; gap: 12px; align-items: center;">
                                        <small style="color: var(--text-muted); font-size: 0.85rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                        @if(!$notif->read)
                                            <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST" style="margin: 0; display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-link" style="color: var(--primary-hover); text-decoration: none; padding: 0; font-size: 0.85rem;">
                                                    Mark as read
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if($notifications->hasPages())
                    <div class="mt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card" style="background: var(--surface-02); border: 1px solid var(--border);">
                    <div class="card-header" style="background: var(--surface-02); border-bottom: 1px solid var(--border);">
                        <span style="color: var(--text-primary);">Notification Stats</span>
                    </div>
                    <div class="card-body" style="background: var(--surface-02);">
                        @php
                            $unreadCount = Auth::user()->notifications()->where('read', false)->count();
                            $readCount = Auth::user()->notifications()->where('read', true)->count();
                            $totalCount = Auth::user()->notifications()->count();
                        @endphp

                        <div class="mb-4">
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Unread</div>
                            <div style="font-size: 1.8rem; font-weight: 600; color: var(--primary-hover);">{{ $unreadCount }}</div>
                        </div>

                        <div class="mb-4">
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Read</div>
                            <div style="font-size: 1.8rem; font-weight: 600; color: #66BB6A;">{{ $readCount }}</div>
                        </div>

                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Total</div>
                            <div style="font-size: 1.8rem; font-weight: 600; color: var(--text-primary);">{{ $totalCount }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card text-center py-5" style="background: var(--surface-02); border: 1px solid var(--border);">
            <div style="color: var(--text-muted); font-size: 3rem; margin-bottom: 16px;">
                <i class="fa fa-inbox"></i>
            </div>
            <h5 style="color: var(--text-primary); margin-bottom: 8px;">No Notifications</h5>
            <p style="color: var(--text-muted); margin-bottom: 24px;">You're all caught up! Check back later for updates.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    @endif

</div>
@endsection
