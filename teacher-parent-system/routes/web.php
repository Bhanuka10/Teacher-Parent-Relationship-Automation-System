<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDash;
use App\Http\Controllers\Parent\DashboardController as ParentDash;
use App\Http\Controllers\Parent\ProfileController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::redirect('/', '/login');
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

// Force password change
Route::middleware('auth')->group(function () {
    Route::get('/change-password',  [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

// Admin routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDash::class, 'index'])->name('dashboard');
        Route::resource('users',    UserController::class);
        Route::resource('classes',  ClassController::class);
        Route::resource('students', StudentController::class);
    });

// Teacher routes
Route::middleware(['auth', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [TeacherDash::class, 'index'])->name('dashboard');
    });

// Parent routes
Route::middleware(['auth', 'parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard',        [ParentDash::class, 'index'])->name('dashboard');
        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });