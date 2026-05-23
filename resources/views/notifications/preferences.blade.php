@extends('layouts.app')

@section('title', 'Notification Preferences')

@section('content')
<div class="container-fluid pt-4 px-4 pb-5">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="mb-0">Notification Preferences</h2>
        <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Notifications
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span>Choose which notifications you want to receive</span>
                </div>
                <div class="card-body">
                    <form method="PUT" action="{{ route('notifications.updatePreferences') }}" id="preferencesForm">
                        @csrf
                        @method('PUT')

                        {{-- Report Created --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 p-3" style="background: var(--surface-03); border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <h6 style="color: var(--text-primary); font-weight: 600; margin: 0; margin-bottom: 4px;">
                                    <i class="fa fa-file-alt me-2" style="color: var(--primary-hover);"></i> Report Submitted
                                </h6>
                                <small style="color: var(--text-muted);">Get notified when you submit a new report</small>
                            </div>
                            <div class="form-check form-switch" style="margin: 0;">
                                <input class="form-check-input" type="checkbox" name="report_created" id="report_created" 
                                       @if($prefs->report_created) checked @endif style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        {{-- Report Status Changed --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 p-3" style="background: var(--surface-03); border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <h6 style="color: var(--text-primary); font-weight: 600; margin: 0; margin-bottom: 4px;">
                                    <i class="fa fa-sync-alt me-2" style="color: #81D4FA;"></i> Report Status Updated
                                </h6>
                                <small style="color: var(--text-muted);">Get notified when your report status changes</small>
                            </div>
                            <div class="form-check form-switch" style="margin: 0;">
                                <input class="form-check-input" type="checkbox" name="report_status_changed" id="report_status_changed" 
                                       @if($prefs->report_status_changed) checked @endif style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        {{-- User Suspended --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 p-3" style="background: var(--surface-03); border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <h6 style="color: var(--text-primary); font-weight: 600; margin: 0; margin-bottom: 4px;">
                                    <i class="fa fa-ban me-2" style="color: #EF5350;"></i> Account Suspended
                                </h6>
                                <small style="color: var(--text-muted);">Get notified if your account is suspended</small>
                            </div>
                            <div class="form-check form-switch" style="margin: 0;">
                                <input class="form-check-input" type="checkbox" name="user_suspended" id="user_suspended" 
                                       @if($prefs->user_suspended) checked @endif style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        {{-- User Activated --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 p-3" style="background: var(--surface-03); border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <h6 style="color: var(--text-primary); font-weight: 600; margin: 0; margin-bottom: 4px;">
                                    <i class="fa fa-user-check me-2" style="color: #66BB6A;"></i> Account Reactivated
                                </h6>
                                <small style="color: var(--text-muted);">Get notified when your account is reactivated</small>
                            </div>
                            <div class="form-check form-switch" style="margin: 0;">
                                <input class="form-check-input" type="checkbox" name="user_activated" id="user_activated" 
                                       @if($prefs->user_activated) checked @endif style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        {{-- Role Changed --}}
                        <div class="d-flex align-items-center justify-content-between mb-4 p-3" style="background: var(--surface-03); border-radius: 8px; border: 1px solid var(--border);">
                            <div>
                                <h6 style="color: var(--text-primary); font-weight: 600; margin: 0; margin-bottom: 4px;">
                                    <i class="fa fa-user-tie me-2" style="color: #FBC02D;"></i> Role Changed
                                </h6>
                                <small style="color: var(--text-muted);">Get notified when your role is changed</small>
                            </div>
                            <div class="form-check form-switch" style="margin: 0;">
                                <input class="form-check-input" type="checkbox" name="role_changed" id="role_changed" 
                                       @if($prefs->role_changed) checked @endif style="width: 3rem; height: 1.5rem; cursor: pointer;">
                            </div>
                        </div>

                        <hr style="border-top: 1px solid var(--border); margin: 24px 0;">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save me-1"></i> Save Preferences
                            </button>
                            <a href="{{ route('notifications.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <span>Notification Info</span>
                </div>
                <div class="card-body">
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 16px;">
                        <i class="fa fa-info-circle me-2" style="color: var(--primary-hover);"></i>
                        You can customize which notifications you receive. Disabled notifications will not be sent to you.
                    </p>
                    <hr style="border-top: 1px solid var(--border); margin: 16px 0;">
                    <div style="font-size: 0.85rem; color: var(--text-muted);">
                        <p><strong style="color: var(--text-primary);">Tip:</strong> Even with notifications disabled, you can still view all activity in the notification center.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
