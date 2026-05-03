@extends('layouts.app')

@section('title', 'Edit Report')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    @php
        $currentRole = strtolower(is_object(Auth::user()->role) ? Auth::user()->role->name : (Auth::user()->role ?? 'resident'));
        $isAdmin = $currentRole === 'admin';
    @endphp

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">{{ $isAdmin ? 'Update Report Status' : 'Edit Report' }}</h2>
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

    @if($isAdmin)
        <div class="alert alert-info mb-4" style="background: rgba(2,119,189,0.12); color: #81D4FA; border-left: 3px solid #0277BD; border-radius: var(--radius-md);">
            <i class="fa fa-info-circle me-2"></i>
            As an admin, you can update the <strong>status</strong> and <strong>resident name</strong> only.
            Subject and description are locked to the original submitter.
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex align-items-center gap-2">
            <i class="fa fa-edit" style="color: var(--primary-hover);"></i>
            <span>Report #{{ $report->id }}</span>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.update', $report->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Resident Name — editable by both --}}
                <div class="mb-3">
                    <label for="resident_name" class="form-label">Resident Name</label>
                    <input type="text" name="resident_name" id="resident_name"
                           class="form-control @error('resident_name') is-invalid @enderror"
                           value="{{ old('resident_name', $report->resident_name) }}"
                           required>
                    @error('resident_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Subject — locked for admins --}}
                <div class="mb-3">
                    <label for="subject" class="form-label">
                        Subject
                        @if($isAdmin)
                            <span style="color: var(--text-muted); font-size:0.78rem;">(read-only)</span>
                        @endif
                    </label>
                    <input type="text" name="subject" id="subject"
                           class="form-control"
                           value="{{ old('subject', $report->subject) }}"
                           @if($isAdmin) readonly style="opacity:0.5; cursor:not-allowed;" @else required @endif>
                </div>

                {{-- Description — locked for admins --}}
                <div class="mb-3">
                    <label for="description" class="form-label">
                        Description
                        @if($isAdmin)
                            <span style="color: var(--text-muted); font-size:0.78rem;">(read-only)</span>
                        @endif
                    </label>
                    <textarea name="description" id="description"
                              class="form-control"
                              rows="4"
                              @if($isAdmin) readonly style="opacity:0.5; cursor:not-allowed;" @else required @endif>{{ old('description', $report->description) }}</textarea>
                </div>

                {{-- Status — editable by both --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="Pending"     {{ $report->status === 'Pending'     ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ $report->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Resolved"    {{ $report->status === 'Resolved'    ? 'selected' : '' }}>Resolved</option>
                    </select>
                </div>

                {{-- Attachments — only for owners --}}
                @if(!$isAdmin)
                    <div class="mb-4">
                        <label for="attachments" class="form-label">Attachments <span style="color:var(--text-muted)">(optional)</span></label>
                        <input type="file" name="attachments[]" id="attachments"
                               class="form-control" multiple>
                        <div class="form-text">You can upload multiple files (jpg, png, pdf). Max 2MB each.</div>

                        @if($report->attachments->count())
                            <div class="mt-3">
                                <div style="color: var(--text-muted); font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em;" class="mb-2">Existing Attachments</div>
                                <ul class="list-unstyled mb-0">
                                    @foreach($report->attachments as $attachment)
                                        <li class="mb-1">
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                               style="color: var(--primary-hover); font-size:0.85rem;">
                                                <i class="fa fa-paperclip me-1"></i>{{ basename($attachment->file_path) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>
                        {{ $isAdmin ? 'Update Status' : 'Save Changes' }}
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection