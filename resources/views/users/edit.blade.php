@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Edit User</h2>
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back
        </a>
    </div>

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

    <div class="row g-4">

        {{-- Left: avatar --}}
        <div class="col-md-3">
            <div class="card text-center py-4 px-3">
                <img src="{{ $user->profile_photo ? asset('storage/'.$user->profile_photo) : asset('img/default-user.png') }}"
                     style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:3px solid var(--border); margin:0 auto 14px;"
                     alt="{{ $user->name }}">
                <div style="font-weight:600; color:var(--text-primary); font-size:0.95rem;">{{ $user->name }}</div>
                <div style="color:var(--text-muted); font-size:0.82rem; margin-top:3px;">{{ $user->email }}</div>
                <div class="mt-3">
                    @php $roleName = is_object($user->role) ? $user->role->name : ($user->role ?? 'Resident'); @endphp
                    <span style="display:inline-flex; align-items:center; gap:5px;
                                 background:rgba(198,40,40,0.1); border:1px solid rgba(198,40,40,0.3);
                                 border-radius:20px; padding:3px 12px; font-size:0.78rem; color:var(--primary-hover);">
                        {{ $roleName }}
                    </span>
                </div>
                @if($user->is_suspended)
                    <div class="mt-2">
                        <span style="display:inline-flex; align-items:center; gap:5px;
                                     background:rgba(239,83,80,0.1); border:1px solid rgba(239,83,80,0.3);
                                     border-radius:20px; padding:3px 12px; font-size:0.78rem; color:#EF5350;">
                            <i class="fa fa-ban" style="font-size:0.65rem;"></i> Suspended
                        </span>
                    </div>
                @endif
                <div style="color:var(--text-muted); font-size:0.75rem; margin-top:14px;">
                    Member since {{ $user->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Right: form --}}
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-user-edit" style="color:var(--primary-hover);"></i>
                    <span>Edit Profile — {{ $user->name }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label">Role</label>
                                <select name="role_id" id="role_id"
                                        class="form-select @error('role_id') is-invalid @enderror" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text" style="color:var(--text-muted); font-size:0.78rem;">
                                    Changing role to Admin will prevent this user from submitting reports.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i> Save Changes
                            </button>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection