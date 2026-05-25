<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

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

    // ── User Management ───────────────────────────────────────────
    Route::get('/users',                     [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit',         [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',              [UserManagementController::class, 'update'])->name('users.update');
    Route::get('/users/{user}',              [UserManagementController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/suspend',     [UserManagementController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/activate',    [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/change-role', [UserManagementController::class, 'changeRole'])->name('users.changeRole');

    // ── Notifications ─────────────────────────────────────────────
    Route::get('/notifications',                          [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread',                   [NotificationController::class, 'getUnread'])->name('notifications.getUnread');
    Route::get('/notifications/preferences',              [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::put('/notifications/preferences',              [NotificationController::class, 'updatePreferences'])->name('notifications.updatePreferences');
    Route::post('/notifications/read-all',                [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{notification}/read',     [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{notification}',        [NotificationController::class, 'delete'])->name('notifications.delete');

    // ── Messages ──────────────────────────────────────────────────
    // IMPORTANT: static routes MUST come before {user} wildcard
    Route::get('/messages',                  [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/unread-count',     [MessageController::class, 'unreadCount'])->name('messages.unreadCount');
    Route::get('/messages/{user}',           [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages/{user}/send',     [MessageController::class, 'send'])->name('messages.send')->middleware('throttle:30,1');
    Route::get('/messages/{user}/poll',      [MessageController::class, 'poll'])->name('messages.poll');
});

require __DIR__.'/auth.php';