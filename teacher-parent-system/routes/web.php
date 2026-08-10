<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendance;
use App\Http\Controllers\Teacher\DashboardController as TeacherDash;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendance;
use App\Http\Controllers\Teacher\ProfileController as TeacherProfile;
use App\Http\Controllers\Parent\DashboardController as ParentDash;
use App\Http\Controllers\Parent\ProfileController;
use App\Http\Controllers\Admin\MessageController as AdminMessage;
use App\Http\Controllers\MessageInboxController;
use App\Http\Controllers\Teacher\HomeworkController as TeacherHomework;
use App\Http\Controllers\Parent\HomeworkController as ParentHomework;
use App\Http\Controllers\Admin\HomeworkController as AdminHomework;

// Root: send guests to login, and already-authenticated users straight to
// their dashboard. This must NOT sit behind the `guest` middleware — Laravel's
// RedirectIfAuthenticated has no `dashboard`/`home` route to fall back to in
// this app, so it defaults to redirecting back to `/`, which would bounce
// forever between `/` and `/login` (ERR_TOO_MANY_REDIRECTS) for anyone with
// an active session.
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        'parent'  => redirect()->route('parent.dashboard'),
        default   => redirect()->route('login'),
    };
});

// Guest routes
Route::middleware('guest')->group(function () {
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
        Route::get('/attendance/history', [AdminAttendance::class, 'history'])->name('attendance.history');
        Route::get('/messages', [AdminMessage::class, 'index'])->name('messages.index');
        Route::post('/messages', [AdminMessage::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [AdminMessage::class, 'show'])->name('messages.show');
        Route::get('/homework', [AdminHomework::class, 'index'])->name('homework.index');
        Route::get('/homework/{homework}', [AdminHomework::class, 'show'])->name('homework.show');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');
        Route::resource('users',    UserController::class);
        Route::patch('classes/{school_class}/remove-teacher', [ClassController::class, 'removeTeacher'])->name('classes.remove-teacher');
        Route::patch('classes/{school_class}/reset', [ClassController::class, 'resetClass'])->name('classes.reset');
        Route::resource('classes',  ClassController::class)
            ->parameters(['classes' => 'school_class']);
        Route::resource('students', StudentController::class);
    });

// Teacher routes
Route::middleware(['auth', 'teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [TeacherDash::class, 'index'])->name('dashboard');
        Route::get('/attendance', [TeacherAttendance::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [TeacherAttendance::class, 'store'])->name('attendance.store');
        Route::get('/attendance/history', [TeacherAttendance::class, 'history'])->name('attendance.history');
        Route::get('/profile', [TeacherProfile::class, 'show'])->name('profile');
        Route::put('/profile', [TeacherProfile::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [TeacherProfile::class, 'updatePassword'])->name('profile.password');
        Route::get('/messages', [MessageInboxController::class, 'index'])->name('messages.index');
        Route::get('/messages/{recipient}', [MessageInboxController::class, 'show'])->name('messages.show');
        Route::get('/homework', [TeacherHomework::class, 'index'])->name('homework.index');
        Route::get('/homework/create', [TeacherHomework::class, 'create'])->name('homework.create');
        Route::post('/homework', [TeacherHomework::class, 'store'])->name('homework.store');
        Route::get('/homework/{homework}', [TeacherHomework::class, 'show'])->name('homework.show');
        Route::get('/homework/{homework}/edit', [TeacherHomework::class, 'edit'])->name('homework.edit');
        Route::put('/homework/{homework}', [TeacherHomework::class, 'update'])->name('homework.update');
        Route::put('/homework/{homework}/submissions/{submission}', [TeacherHomework::class, 'grade'])->name('homework.grade');
        Route::delete('/homework/{homework}', [TeacherHomework::class, 'destroy'])->name('homework.destroy');
    });

// Parent routes
Route::middleware(['auth', 'parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard',        [ParentDash::class, 'index'])->name('dashboard');
        Route::get('/profile',          [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile/student',  [ProfileController::class, 'updateStudentProfile'])->name('profile.student');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/messages', [MessageInboxController::class, 'index'])->name('messages.index');
        Route::get('/messages/{recipient}', [MessageInboxController::class, 'show'])->name('messages.show');
        Route::get('/homework', [ParentHomework::class, 'index'])->name('homework.index');
        Route::get('/homework/{homework}', [ParentHomework::class, 'show'])->name('homework.show');
        Route::post('/homework/{homework}/submit', [ParentHomework::class, 'submitFile'])->name('homework.submit');
        Route::post('/homework/{homework}/start', [ParentHomework::class, 'startQuiz'])->name('homework.start');
        Route::post('/homework/{homework}/answer', [ParentHomework::class, 'submitQuiz'])->name('homework.answer');
    });
