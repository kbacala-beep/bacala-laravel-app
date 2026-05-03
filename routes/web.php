<?php

use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ProfileController;
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
    // Reports resource routes
    Route::resource('reports', ReportsController::class);

    // Archive routes (admin only — enforced in controller)
    Route::get('/reports-archive', [ReportsController::class, 'archive'])->name('reports.archive');
    Route::post('/reports-archive/{id}/restore', [ReportsController::class, 'restore'])->name('reports.restore');
    Route::delete('/reports-archive/{id}/force-delete', [ReportsController::class, 'forceDelete'])->name('reports.forceDelete');

    // Activity Log (admin only — enforced in controller)
    Route::get('/activity-log', [ReportsController::class, 'activityLog'])->name('reports.activityLog');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // User Management — admin only (controller enforces this)
    Route::get('/users',              [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit',  [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',       [UserManagementController::class, 'update'])->name('users.update');
});

require __DIR__.'/auth.php';