<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Lecturer\LecturerDashboardController;
use App\Http\Controllers\Lecturer\LecturerDiscussionController;
use App\Http\Controllers\Lecturer\LecturerCourseController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentDiscussionController;

// ── Public routes ──────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::get('/password/reset', fn() => view('auth.passwords.email'))->name('password.request');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin routes ───────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/',            [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics',   [AdminDashboardController::class, 'analytics'])->name('analytics');
    Route::get('/discussions', [AdminDashboardController::class, 'discussions'])->name('discussions');
    Route::get('/courses',     [AdminDashboardController::class, 'courses'])->name('courses');
    Route::get('/reports',     [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/settings',    [AdminDashboardController::class, 'settings'])->name('settings');

    // Member actions
    Route::get('/members', [AdminUserController::class, 'index'])
        ->name('Users.index');

    Route::post('/members/{user}/approve', [AdminUserController::class, 'approve'])
        ->name('Users.approve');

    Route::post('/members/{user}/decline', [AdminUserController::class, 'decline'])
        ->name('Users.decline');

    Route::post('/members/{user}/warn', [AdminUserController::class, 'warn'])
        ->name('Users.warn');

    Route::post('/members/{user}/blacklist', [AdminUserController::class, 'blacklist'])
        ->name('Users.blacklist');

    Route::post('/members/{user}/unblacklist', [AdminUserController::class, 'unblacklist'])
        ->name('Users.unblacklist');

    Route::post('/members/{user}/logout', [AdminUserController::class, 'logout'])
        ->name('Users.logout');
});

// ── Lecturer routes ────────────────────────────────────────
Route::prefix('lecturer')->name('lecturer.')->middleware(['auth', 'role:lecturer'])->group(function () {
    Route::get('/',          [LecturerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/engagement',[LecturerDashboardController::class, 'engagement'])->name('engagement');
    Route::get('/pinned',    [LecturerDashboardController::class, 'pinned'])->name('pinned');
    Route::get('/flagged',   [LecturerDashboardController::class, 'flagged'])->name('flagged');
    Route::resource('/courses',       LecturerCourseController::class)->names('courses');
    Route::resource('/discussions',   LecturerDiscussionController::class)->names('discussions');
    Route::resource('/announcements', \App\Http\Controllers\Lecturer\LecturerAnnouncementController::class)->names('announcements');
    Route::get('/categories', [LecturerDashboardController::class, 'categories'])
    ->name('categories');

Route::get('/quizzes', [LecturerDashboardController::class, 'quizzes'])
    ->name('quizzes');

Route::get('/performance', [LecturerDashboardController::class, 'performance'])
    ->name('performance');

Route::get('/messages', [LecturerDashboardController::class, 'messages'])
    ->name('messages');

Route::get('/notifications', [LecturerDashboardController::class, 'notifications'])
    ->name('notifications');

Route::get('/settings', [LecturerDashboardController::class, 'settings'])
    ->name('settings');
    
    Route::get('/quizzes/create', [LecturerDashboardController::class, 'createQuiz'])
    ->name('quizzes.create');
});

// ── Student routes ─────────────────────────────────────────
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/',                 [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/saved',            [StudentDashboardController::class, 'saved'])->name('saved');
    Route::get('/profile',          [StudentDashboardController::class, 'profile'])->name('profile');
    Route::get('/notifications',    [StudentDashboardController::class, 'notifications'])->name('notifications');
    Route::get('/courses/browse',   [StudentDashboardController::class, 'browseCourses'])->name('courses.browse');
    Route::get('/courses/{course}', [StudentDashboardController::class, 'course'])->name('course');
    Route::resource('/discussions', StudentDiscussionController::class)->names('discussions');
    Route::get('/categories', [StudentDashboardController::class, 'categories'])->name('categories');
    Route::get('/quizzes',          [StudentDashboardController::class, 'quizzes'])->name('quizzes');
    Route::get('/messages',         [StudentDashboardController::class, 'messages'])->name('messages');
    Route::get('/settings',          [StudentDashboardController::class, 'settings'])->name('settings');
    Route::get('/reports', [StudentDashboardController::class, 'reports'])
    ->name('reports');

});

// ── Redirect root to correct dashboard by role ─────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return match(auth()->user()->role) {
        'admin'    => redirect()->route('admin.dashboard'),
        'lecturer' => redirect()->route('lecturer.dashboard'),
        default    => redirect()->route('student.dashboard'),
    };
})->middleware('auth');