@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    {{-- Page Header --}}
    <div class="d-flex align-items-center mb-4 gap-3">
        <div class="position-relative" style="width:64px; height:64px; flex-shrink:0;">
            <img class="rounded-circle border border-2 w-100 h-100"
                 style="object-fit:cover; border-color: var(--primary) !important;"
                 src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('img/default-user.png') }}"
                 alt="Profile Photo">
            <div class="bg-success rounded-circle border border-2 position-absolute end-0 bottom-0"
                 style="width:14px; height:14px; border-color: var(--bg) !important;"></div>
        </div>
        <div>
            <h4 class="mb-0" style="color: var(--text-primary); font-weight:500;">{{ $user->name }}</h4>
            <span style="color: var(--primary-hover); font-size:0.82rem;">
                @php
                    $role = $user->role;
                    echo is_object($role) ? $role->name : ($role ?? 'Resident');
                @endphp
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> Profile updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> Password updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT COLUMN --}}
        <div class="col-lg-7">

            {{-- Profile Information --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-user" style="color: var(--primary-hover);"></i>
                    <span>Profile Information</span>
                </div>
                <div class="card-body">
                    <p style="color: var(--text-muted); font-size:0.83rem;" class="mb-4">
                        Update your account's name, email address, and profile photo.
                    </p>

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Profile Photo Upload --}}
                        <div class="mb-4">
                            <label class="form-label">Profile Photo</label>
                            <div class="d-flex align-items-center gap-3">
                                <img id="photoPreview"
                                     class="rounded-circle"
                                     style="width:52px; height:52px; object-fit:cover; border: 2px solid var(--border-strong);"
                                     src="{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : asset('img/default-user.png') }}"
                                     alt="Preview">
                                <div>
                                    <input type="file" name="profile_photo" id="profile_photo"
                                           class="form-control @error('profile_photo') is-invalid @enderror"
                                           accept="image/*"
                                           onchange="previewPhoto(this)">
                                    <div class="form-text">JPG, PNG. Max 2MB.</div>
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Update Password --}}
            <div class="card">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-lock" style="color: var(--primary-hover);"></i>
                    <span>Update Password</span>
                </div>
                <div class="card-body">
                    <p style="color: var(--text-muted); font-size:0.83rem;" class="mb-4">
                        Ensure your account is using a strong, unique password.
                    </p>

                    <form method="POST" action="{{ route('profile.updatePassword') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password') is-invalid @enderror"
                                   autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-key me-1"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-5">

            {{-- Account Summary --}}
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="fa fa-info-circle" style="color: var(--primary-hover);"></i>
                    <span>Account Details</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="background: var(--surface-02); border-color: var(--border); padding: 12px 20px;">
                            <span style="color: var(--text-muted); font-size:0.83rem;">Member Since</span>
                            <span style="color: var(--text-primary); font-size:0.85rem;">
                                {{ $user->created_at->format('M d, Y') }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="background: var(--surface-02); border-color: var(--border); padding: 12px 20px;">
                            <span style="color: var(--text-muted); font-size:0.83rem;">Role</span>
                            <span class="badge" style="background: var(--primary-light); color: var(--primary-hover); border: 1px solid rgba(198,40,40,0.3); border-radius: 20px; padding: 4px 12px;">
                                @php
                                    $role = $user->role;
                                    echo is_object($role) ? $role->name : ($role ?? 'Resident');
                                @endphp
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="background: var(--surface-02); border-color: var(--border); padding: 12px 20px;">
                            <span style="color: var(--text-muted); font-size:0.83rem;">Email Verified</span>
                            @if($user->email_verified_at)
                                <span class="badge" style="background: rgba(46,125,50,0.2); color: #81C784; border: 1px solid rgba(46,125,50,0.4); border-radius: 20px; padding: 4px 12px;">
                                    <i class="fa fa-check me-1"></i> Verified
                                </span>
                            @else
                                <span class="badge" style="background: rgba(245,127,23,0.2); color: #FFB74D; border: 1px solid rgba(245,127,23,0.4); border-radius: 20px; padding: 4px 12px;">
                                    Unverified
                                </span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"
                            style="background: var(--surface-02); border-color: var(--border); padding: 12px 20px;">
                            <span style="color: var(--text-muted); font-size:0.83rem;">Last Updated</span>
                            <span style="color: var(--text-primary); font-size:0.85rem;">
                                {{ $user->updated_at->format('M d, Y') }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card" style="border-color: rgba(198,40,40,0.35);">
                <div class="card-header d-flex align-items-center gap-2" style="background: rgba(198,40,40,0.1) !important; border-bottom-color: rgba(198,40,40,0.35);">
                    <i class="fa fa-exclamation-triangle" style="color: var(--primary-hover);"></i>
                    <span style="color: var(--primary-hover);">Danger Zone</span>
                </div>
                <div class="card-body">
                    <p style="color: var(--text-muted); font-size:0.83rem;" class="mb-3">
                        Once your account is deleted, all data will be permanently removed. This action cannot be undone.
                    </p>
                    <button type="button" class="btn btn-danger w-100"
                            data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fa fa-trash me-1"></i> Delete Account
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom-color: rgba(198,40,40,0.35);">
                <h5 class="modal-title" style="color: var(--primary-hover);">
                    <i class="fa fa-exclamation-triangle me-2"></i> Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p style="color: var(--text-secondary); font-size:0.88rem;">
                        Are you sure you want to permanently delete your account? This will remove all your data and cannot be undone.
                    </p>
                    <div class="mt-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" id="delete_password" name="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Your password">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer" style="border-top-color: rgba(198,40,40,0.35);">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash me-1"></i> Yes, Delete My Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('photoPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

@endsection