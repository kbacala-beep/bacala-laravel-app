<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // ── Reports ───────────────────────────────────────────────────
    Route::resource('reports', ReportsController::class);

    Route::get('/reports-archive',                      [ReportsController::class, 'archive'])->name('reports.archive');
    Route::post('/reports-archive/{id}/restore',        [ReportsController::class, 'restore'])->name('reports.restore');
    Route::delete('/reports-archive/{id}/force-delete', [ReportsController::class, 'forceDelete'])->name('reports.forceDelete');

    Route::get('/activity-log', [ReportsController::class, 'activityLog'])->name('reports.activityLog');

    // ── Profile ───────────────────────────────────────────────────
    Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile',       [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── User Management (admin only — enforced in controller) ─────
    Route::get('/users',                  [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}',           [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit',      [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',           [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/suspend',  [UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/change-role', [UserManagementController::class, 'changeRole'])->name('users.changeRole');

    // ── Notifications ──────────────────────────────────────────────
    Route::get('/notifications',               [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread',        [NotificationController::class, 'getUnread'])->name('notifications.getUnread');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all',     [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::get('/notifications/preferences',   [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::put('/notifications/preferences',   [NotificationController::class, 'updatePreferences'])->name('notifications.updatePreferences');
});

require __DIR__.'/auth.php';