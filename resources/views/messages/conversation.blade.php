@extends('layouts.app')

@section('title', 'Conversation with ' . $user->name)

@section('content')

    <div class="container-fluid pt-4 px-4 pb-5">

        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 mb-4">

            <a href="{{ route('messages.index') }}" class="btn btn-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i>
                Back
            </a>

            <img src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('img/default-user.png') }}"
                style="
                        width:38px;
                        height:38px;
                        border-radius:50%;
                        object-fit:cover;
                        border:2px solid var(--border);
                    ">

            <div>

                <div style="
                        font-weight:600;
                        color:var(--text-primary);
                        line-height:1.2;
                    ">
                    {{ $user->name }}
                </div>

                <div style="
                        font-size:0.75rem;
                        color:var(--text-muted);
                    ">
                    {{ $user->role_relation->name ?? 'Resident' }}
                </div>

            </div>
        </div>

        {{-- Chat Card --}}
        <div class="card" style="
                    display:flex;
                    flex-direction:column;
                    height:calc(100vh - 240px);
                    min-height:500px;
                 ">

            {{-- Messages --}}
            <div id="chat-body" class="chat-body">

                @forelse($messages as $message)

                    @php
                        $isMine = $message->sender_id === Auth::id();
                    @endphp

                    @if($isMine)
                        <div id="msg-{{ $message->id }}" class="text-end mb-3 d-flex justify-content-end">
                            <div style="max-width: 70%; min-width: 200px; width: fit-content;">
                                <div class="chat-bubble mine text-start">
                                    {{ $message->body }}
                                </div>
                                <div class="chat-time mine">
                                    {{ $message->created_at->format('M d, H:i') }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="msg-{{ $message->id }}" class="text-start mb-3 d-flex justify-content-start">
                            <div class="d-flex align-items-end gap-2" style="max-width: 70%; min-width: 200px;">
                                <img src="{{ $message->sender->profile_photo ? asset('storage/' . $message->sender->profile_photo) : asset('img/default-user.png') }}"
                                    class="chat-avatar">
                                <div style="width: 100%;">
                                    <div class="chat-bubble other">
                                        {{ $message->body }}
                                    </div>
                                    <div class="chat-time">
                                        {{ $message->created_at->format('M d, H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                @empty

                    <div class="empty-chat">
                        No messages yet.
                    </div>

                @endforelse

            </div>

            {{-- Input --}}
            <div style="
                    border-top:1px solid var(--border);
                    padding:14px 16px;

                    display:flex;
                    gap:10px;
                    align-items:flex-end;

                    background:var(--surface-02);
                ">

                <textarea id="message-input" placeholder="Type a message..." rows="1" style="
                            flex:1;

                            background:var(--surface-03);

                            border:1px solid var(--border);
                            border-radius:10px;

                            padding:10px 14px;

                            color:var(--text-primary);

                            font-family:inherit;
                            font-size:0.88rem;

                            outline:none;
                            resize:none;

                            max-height:120px;

                            overflow-y:auto;
                        " onkeydown="handleKey(event)"></textarea>

                <button id="send-btn" onclick="sendMessage()" style="
                            background:var(--primary);

                            border:none;
                            border-radius:10px;

                            width:44px;
                            height:44px;

                            color:#fff;

                            cursor:pointer;

                            display:flex;
                            align-items:center;
                            justify-content:center;

                            flex-shrink:0;
                        ">
                    <i class="fa fa-paper-plane"></i>
                </button>

            </div>

        </div>
    </div>

@endsection

@push('scripts')

    <script>

        (function ($) {

            "use strict";

            $('#msg-badge').hide();

            const sendUrl =
                "{{ route('messages.send', $user->id) }}";

            const pollUrl =
                "{{ route('messages.poll', $user->id) }}";

            const partPhoto =
                "{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('img/default-user.png') }}";

            let lastMsgId =
                {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};

            let pollTimer;

            function scrollToBottom(smooth = true) {

                const body =
                    document.getElementById('chat-body');

                body.scrollTo({
                    top: body.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto'
                });
            }

            scrollToBottom(false);

            $('#message-input').on('input', function () {

                this.style.height = 'auto';

                this.style.height =
                    Math.min(this.scrollHeight, 120) + 'px';
            });

            window.handleKey = function (e) {

                if (e.key === 'Enter' && !e.shiftKey) {

                    e.preventDefault();

                    sendMessage();
                }
            };

            window.sendMessage = function () {
                const body = $('#message-input').val().trim();
                if (!body) return;

                // 1. Create a temporary ID for the "ghost" message
                const tempId = 'temp-' + Date.now();
                const $btn = $('#send-btn');
                const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                // 2. Clear input and show loading on button
                $('#message-input').val('').css('height', 'auto');
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

                // 3. Append the "Ghost Message" immediately with low opacity
                const ghostHtml = `
                <div id="${tempId}" class="text-end mb-3" style="opacity: 0.7;">
                    <div class="chat-bubble mine">
                        ${escapeHtml(body)}
                    </div>
                    <div class="chat-time mine">
                        <i class="fa fa-circle-notch fa-spin me-1"></i> Sending...
                    </div>
                </div>
            `;
                $('#chat-body').append(ghostHtml);
                $('#empty-chat').remove();
                scrollToBottom(true);

                // 4. Send to server
                $.ajax({
                    url: sendUrl,
                    method: 'POST',
                    data: { body: body },
                    success: function (res) {
                        // Update the ghost message with the real data
                        const $msg = $('#' + tempId);
                        $msg.attr('id', 'msg-' + res.message.id);
                        $msg.css('opacity', '1');
                        $msg.find('.chat-time').html(res.message.created_at);

                        lastMsgId = res.message.id;
                    },
                    error: function () {
                        // Handle failure
                        const $msg = $('#' + tempId);
                        $msg.find('.chat-time').html('<span class="text-danger">Failed to send</span>');
                        $msg.css('opacity', '1');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i>');
                    }
                });
            };

                function appendMessage(msg, isMine) {
                if (document.getElementById('msg-' + msg.id)) return;

                let html = '';
                if (isMine) {
                    html = `
                <div id="msg-${msg.id}" class="text-end mb-3 d-flex justify-content-end">
                    <div style="max-width: 70%; min-width: 200px; width: fit-content;">
                        <div class="chat-bubble mine text-start">
                            ${escapeHtml(msg.body)}
                        </div>
                        <div class="chat-time mine">
                            ${msg.created_at}
                        </div>
                    </div>
                </div>
            `;
                } else {
                    html = `
                <div id="msg-${msg.id}" class="text-start mb-3 d-flex justify-content-start">
                    <div class="d-flex align-items-end gap-2" style="max-width: 70%; min-width: 200px;">
                        <img src="${partPhoto}" class="chat-avatar">
                        <div style="width: 100%;">
                            <div class="chat-bubble other">
                                ${escapeHtml(msg.body)}
                            </div>
                            <div class="chat-time">
                                ${msg.created_at}
                            </div>
                        </div>
                    </div>
                </div>
            `;
                }

                $('#chat-body').append(html);
                scrollToBottom(true);
            }

            function escapeHtml(str) {

                return str
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function poll() {

                $.ajax({

                    url: pollUrl,

                    data: {
                        since_id: lastMsgId
                    },

                    suppressGlobalError: true,

                    success: function (res) {

                        if (res.messages.length > 0) {

                            res.messages.forEach(function (msg) {

                                if (!document.getElementById('msg-' + msg.id)) {

                                    $('#empty-chat').remove();

                                    appendMessage(msg, false);

                                    lastMsgId = msg.id;
                                }
                            });
                        }
                    }
                });
            }

            pollTimer = setInterval(poll, 3000);

        })(jQuery);

    </script>

@endpush