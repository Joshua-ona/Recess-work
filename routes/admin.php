<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/members/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/members/{user}/decline', [AdminUserController::class, 'decline'])->name('users.decline');
    Route::post('/members/{user}/warn', [AdminUserController::class, 'warn'])->name('users.warn');
    Route::post('/members/{user}/blacklist', [AdminUserController::class, 'blacklist'])->name('users.blacklist');
    Route::post('/members/{user}/unblacklist', [AdminUserController::class, 'unblacklist'])->name('users.unblacklist');

    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::resource('/users', AdminUserController::class)->names('users');
    Route::get('/discussions', [AdminDashboardController::class, 'discussions'])->name('discussions');
    Route::get('/courses', [AdminDashboardController::class, 'courses'])->name('courses');
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');

    // Member actions
    Route::post('/members/{user}/approve', [AdminUserController::class, 'approve'])
        ->name('users.approve');

    Route::post('/members/{user}/decline', [AdminUserController::class, 'decline'])
        ->name('users.decline');

    Route::post('/members/{user}/warn', [AdminUserController::class, 'warn'])
        ->name('users.warn');

    Route::post('/members/{user}/blacklist', [AdminUserController::class, 'blacklist'])
        ->name('users.blacklist');

    Route::post('/members/{user}/unblacklist', [AdminUserController::class, 'unblacklist'])
        ->name('users.unblacklist');

    Route::post('/members/{user}/logout', [AdminUserController::class, 'logout'])->name('users.logout');
});
