@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="mb-0">Messages</h2>
            <p style="color:var(--text-muted); font-size:0.85rem; margin-top:4px;">
                Your conversations with other users.
            </p>
        </div>

        {{-- New conversation button --}}
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newConversationModal">
            <i class="fa fa-edit me-1"></i> New Message
        </button>
    </div>

    @if($conversations->isEmpty())
        <div style="text-align:center; padding:80px 0; color:var(--text-muted);">
            <i class="fa fa-envelope-open" style="font-size:2.5rem; margin-bottom:16px; display:block; opacity:0.35;"></i>
            <div style="font-size:0.95rem; margin-bottom:8px;">No messages yet</div>
            <div style="font-size:0.83rem;">Start a conversation with another user.</div>
        </div>
    @else
        <div class="d-flex flex-column gap-2">
            @foreach($conversations as $convo)
                <a href="{{ route('messages.conversation', $convo['partner']->id) }}"
                   style="display:flex; align-items:center; gap:14px; padding:14px 18px;
                          background:var(--surface-02); border:1px solid {{ $convo['unread_count'] > 0 ? 'rgba(198,40,40,0.3)' : 'var(--border)' }};
                          border-radius:12px; text-decoration:none; transition:border-color 0.2s, background 0.2s;"
                   onmouseover="this.style.background='var(--surface-03)';"
                   onmouseout="this.style.background='var(--surface-02)';">

                    {{-- Avatar --}}
                    <div style="position:relative; flex-shrink:0;">
                        <img src="{{ $convo['partner']->profile_photo ? asset('storage/'.$convo['partner']->profile_photo) : asset('img/default-user.png') }}"
                             style="width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid var(--border);"
                             alt="{{ $convo['partner']->name }}">
                        @if($convo['unread_count'] > 0)
                            <span style="position:absolute; top:-2px; right:-2px; width:18px; height:18px;
                                         background:var(--primary); border-radius:50%; display:flex; align-items:center;
                                         justify-content:center; font-size:0.65rem; font-weight:700; color:#fff; border:2px solid var(--bg);">
                                {{ $convo['unread_count'] > 9 ? '9+' : $convo['unread_count'] }}
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div style="flex:1; min-width:0;">
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:3px;">
                            <span style="font-weight:{{ $convo['unread_count'] > 0 ? '600' : '500' }};
                                         color:var(--text-primary); font-size:0.92rem;">
                                {{ $convo['partner']->name }}
                            </span>
                            <span style="font-size:0.75rem; color:var(--text-muted); flex-shrink:0; margin-left:12px;">
                                {{ $convo['latest']->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div style="font-size:0.83rem; color:{{ $convo['unread_count'] > 0 ? 'var(--text-secondary)' : 'var(--text-muted)' }};
                                    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            @if($convo['latest']->sender_id === Auth::id())
                                <span style="color:var(--text-muted);">You: </span>
                            @endif
                            {{ Str::limit($convo['latest']->body, 60) }}
                        </div>
                    </div>

                    @if($convo['unread_count'] > 0)
                        <i class="fa fa-circle" style="color:var(--primary); font-size:0.5rem; flex-shrink:0;"></i>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
</div>

{{-- New Conversation Modal --}}
<div class="modal fade" id="newConversationModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--surface-02); border:1px solid var(--border); border-radius:14px;">
            <div class="modal-header" style="border-bottom:1px solid var(--border);">
                <h5 class="modal-title" style="color:var(--text-primary); font-size:1rem;">
                    <i class="fa fa-edit me-2" style="color:var(--primary-hover);"></i>New Message
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div style="position:relative; margin-bottom:16px;">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:0.8rem; pointer-events:none;">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" id="userSearch" placeholder="Search users..."
                           style="width:100%; background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                                  padding:9px 14px 9px 34px; color:var(--text-primary); font-size:0.85rem; outline:none;"
                           oninput="filterUsers(this.value)">
                </div>
                <div id="userList" style="display:flex; flex-direction:column; gap:6px; max-height:280px; overflow-y:auto;">
                    @foreach($users as $user)
                        <a href="{{ route('messages.conversation', $user->id) }}"
                           class="user-option"
                           data-name="{{ strtolower($user->name) }}"
                           style="display:flex; align-items:center; gap:12px; padding:10px 12px;
                                  background:var(--surface-03); border:1px solid var(--border); border-radius:8px;
                                  text-decoration:none; transition:border-color 0.2s;"
                           onmouseover="this.style.borderColor='rgba(198,40,40,0.4)';"
                           onmouseout="this.style.borderColor='var(--border)';">
                            <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('img/default-user.png') }}"
                                 style="width:36px; height:36px; border-radius:50%; object-fit:cover; border:1px solid var(--border); flex-shrink:0;" alt="">
                            <span style="color:var(--text-primary); font-size:0.88rem; font-weight:500;">{{ $user->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterUsers(val) {
    val = val.toLowerCase();
    document.querySelectorAll('.user-option').forEach(el => {
        el.style.display = el.dataset.name.includes(val) ? 'flex' : 'none';
    });
}
</script>
@endpush