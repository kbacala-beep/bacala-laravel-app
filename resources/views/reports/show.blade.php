@extends('layouts.app')

@section('title', 'Report Details')

@section('content')
    <div class="container-fluid pt-4 pb-5 px-4">
        <h2 class="mb-4">Report Details</h2>

        @php
            $currentRole = strtolower(is_object(Auth::user()->role) ? Auth::user()->role->name : (Auth::user()->role ?? 'resident'));
            $isAdmin     = $currentRole === 'admin';
            $isOwner     = $report->user_id === Auth::id();

            $photos    = $report->attachments->filter(fn($a) => str_starts_with($a->file_type, 'image'));
            $documents = $report->attachments->filter(fn($a) => $a->file_type === 'application/pdf');
            $totalAttachments = $report->attachments->count();
        @endphp

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span>Report #{{ $report->id }}</span>
                <span class="badge
                    @if($report->status === 'Pending') bg-warning
                    @elseif($report->status === 'Resolved') bg-success
                    @else bg-light @endif">
                    {{ $report->status }}
                </span>
            </div>

            <div class="card-body">
                <h4 class="mb-1" style="color: var(--text-primary);">{{ $report->subject }}</h4>
                <p class="mb-4 ps-1" style="color: var(--text-secondary);">{{ $report->description }}</p>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--surface-03); border: 1px solid var(--border);">
                            <div style="color: var(--text-muted); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-1">Resident Name</div>
                            <div style="color: var(--text-primary); font-weight:500;">{{ $report->resident_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--surface-03); border: 1px solid var(--border);">
                            <div style="color: var(--text-muted); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-1">Submitted By</div>
                            <div style="color: var(--text-primary); font-weight:500;">
                                {{ $report->user->name ?? 'N/A' }}
                                <small style="color: var(--primary-hover);">
                                    ({{ is_object($report->user->role ?? null) ? $report->user->role->name : ($report->user->role ?? 'Resident') }})
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--surface-03); border: 1px solid var(--border);">
                            <div style="color: var(--text-muted); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-1">Date Submitted</div>
                            <div style="color: var(--text-primary);">{{ $report->created_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded" style="background: var(--surface-03); border: 1px solid var(--border);">
                            <div style="color: var(--text-muted); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-1">Last Updated</div>
                            <div style="color: var(--text-primary);">{{ $report->updated_at->format('M d, Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Attachments --}}
                <div>
                    <div style="color: var(--text-muted); font-size:0.78rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-3">Attachments</div>

                    @if($totalAttachments === 0)
                        <span style="color: var(--text-muted); font-size:0.85rem;">No attachments uploaded.</span>
                    @else
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            @if($photos->count() > 0)
                                <div class="d-flex align-items-center gap-2">
                                    @foreach($photos->take(3) as $i => $photo)
                                        <div class="gallery-thumb" onclick="openGallery({{ $i }})"
                                             style="position:relative; width:64px; height:64px; border-radius:8px; overflow:hidden; cursor:pointer; border:1px solid var(--border); flex-shrink:0;">
                                            <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                 style="width:100%; height:100%; object-fit:cover;" alt="Photo">
                                            @if($i === 2 && $photos->count() > 3)
                                                <div style="position:absolute; inset:0; background:rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; color:#fff; font-size:0.9rem; font-weight:500;">
                                                    +{{ $photos->count() - 2 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                @if($photos->count() > 1)
                                    <button class="btn btn-secondary btn-sm" onclick="openGallery(0)">
                                        <i class="fa fa-images me-1"></i> View All Photos
                                        <span style="color: var(--text-muted); font-size:0.78rem; margin-left:4px;">({{ $photos->count() }})</span>
                                    </button>
                                @endif
                            @endif

                            @foreach($documents as $doc)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                   class="d-flex align-items-center gap-2 px-3 py-2 rounded"
                                   style="background: var(--surface-03); border: 1px solid var(--border); color: var(--primary-hover); font-size:0.83rem; text-decoration:none;">
                                    <i class="fa fa-file-pdf"></i>
                                    <span style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                        {{ basename($doc->file_path) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>

                @if($isAdmin)
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-warning">
                        <i class="fa fa-sliders-h me-1"></i> Edit Status
                    </a>
                    <button type="button" class="btn btn-danger ajax-delete-redirect"
                            data-id="{{ $report->id }}"
                            data-url="{{ route('reports.destroy', $report->id) }}"
                            data-redirect="{{ route('reports.index') }}">
                        <i class="fa fa-archive me-1"></i> Archive
                    </button>

                @elseif($isOwner)
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-warning">
                        <i class="fa fa-pencil-alt me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger ajax-delete-redirect"
                            data-id="{{ $report->id }}"
                            data-url="{{ route('reports.destroy', $report->id) }}"
                            data-redirect="{{ route('reports.index') }}">
                        <i class="fa fa-trash me-1"></i> Delete
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Gallery overlay --}}
    @if($photos->count() > 0)
        <div id="galleryOverlay" onclick="closeGallery()"
             style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.92); z-index:9999; backdrop-filter:blur(6px);">
        </div>

        <div id="galleryPanel" style="display:none; position:fixed; inset:0; z-index:10000; flex-direction:column;">
            <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.1);">
                <div>
                    <span style="color:#fff; font-weight:500; font-size:1rem;">{{ $report->subject }}</span>
                    <span id="galleryCounter" style="color: rgba(255,255,255,0.45); font-size:0.83rem; margin-left:12px;"></span>
                </div>
                <button onclick="closeGallery()"
                        style="background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:36px; height:36px; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                        onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div style="flex:1; display:flex; align-items:center; justify-content:center; position:relative; padding:24px 80px; min-height:0;">
                <button id="prevBtn" onclick="changePhoto(-1)"
                        style="position:absolute; left:16px; background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:48px; height:48px; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:1;"
                        onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <img id="galleryMainImg" src="" style="max-height:100%; max-width:100%; object-fit:contain; border-radius:8px; box-shadow:0 8px 40px rgba(0,0,0,0.6); transition: opacity 0.2s ease;" alt="Attachment">
                <button id="nextBtn" onclick="changePhoto(1)"
                        style="position:absolute; right:16px; background:rgba(255,255,255,0.1); border:none; border-radius:50%; width:48px; height:48px; color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:1;"
                        onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                        onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <div style="padding:12px 24px 20px; border-top:1px solid rgba(255,255,255,0.08);">
                <div id="thumbStrip" style="display:flex; gap:8px; justify-content:center; flex-wrap:wrap;">
                    @foreach($photos as $i => $photo)
                        <div class="gallery-strip-thumb" data-index="{{ $i }}" onclick="goToPhoto({{ $i }})"
                             style="width:56px; height:56px; border-radius:6px; overflow:hidden; cursor:pointer; border:2px solid transparent; transition:border-color 0.2s; flex-shrink:0;">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" style="width:100%; height:100%; object-fit:cover;" alt="">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
(function($) {
    "use strict";

    // ── AJAX delete/archive with redirect after toast ─────────────
    $(document).on('click', '.ajax-delete-redirect', function () {
        const $btn     = $(this);
        const url      = $btn.data('url');
        const redirect = $btn.data('redirect');
        const isAdmin  = $btn.find('i').hasClass('fa-archive');
        const msg      = isAdmin ? 'Archive this report?' : 'Are you sure you want to delete this report?';

        if (!confirm(msg)) return;

        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Processing...');

        $.ajax({
            url:    url,
            method: 'DELETE',
            success: function (res) {
                showToast(res.message, 'success');
                setTimeout(() => window.location.href = redirect, 1200);
            },
            error: function () {
                showToast('Action failed. Please try again.', 'error');
                $btn.prop('disabled', false);
            }
        });
    });

})(jQuery);

// ── Gallery ───────────────────────────────────────────────────────
const galleryPhotos = @json($photos->values()->map(fn($p) => asset('storage/' . $p->file_path)));
let currentIndex = 0;

function openGallery(index) {
    currentIndex = index;
    document.getElementById('galleryOverlay').style.display = 'block';
    document.getElementById('galleryPanel').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    updateGallery();
}

function closeGallery() {
    document.getElementById('galleryOverlay').style.display = 'none';
    document.getElementById('galleryPanel').style.display = 'none';
    document.body.style.overflow = '';
}

function changePhoto(dir) {
    currentIndex = (currentIndex + dir + galleryPhotos.length) % galleryPhotos.length;
    updateGallery();
}

function goToPhoto(index) {
    currentIndex = index;
    updateGallery();
}

function updateGallery() {
    const img = document.getElementById('galleryMainImg');
    img.style.opacity = '0';
    setTimeout(() => { img.src = galleryPhotos[currentIndex]; img.style.opacity = '1'; }, 150);
    document.getElementById('galleryCounter').textContent = (currentIndex + 1) + ' / ' + galleryPhotos.length;
    document.getElementById('prevBtn').style.opacity = galleryPhotos.length > 1 ? '1' : '0';
    document.getElementById('nextBtn').style.opacity = galleryPhotos.length > 1 ? '1' : '0';
    document.querySelectorAll('.gallery-strip-thumb').forEach((el, i) => {
        el.style.borderColor = i === currentIndex ? '#EF5350' : 'transparent';
        el.style.opacity     = i === currentIndex ? '1' : '0.5';
    });
}

document.addEventListener('keydown', e => {
    if (document.getElementById('galleryPanel') && document.getElementById('galleryPanel').style.display === 'flex') {
        if (e.key === 'ArrowLeft')  changePhoto(-1);
        if (e.key === 'ArrowRight') changePhoto(1);
        if (e.key === 'Escape')     closeGallery();
    }
});
</script>
@endpush