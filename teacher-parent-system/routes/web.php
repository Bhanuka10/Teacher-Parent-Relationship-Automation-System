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
use App\Http\Controllers\Teacher\ExamController as TeacherExam;
use App\Http\Controllers\Admin\ExamController as AdminExam;
use App\Http\Controllers\Admin\GradingSchemeController;
use App\Http\Controllers\Parent\ResultController as ParentResult;
use App\Http\Controllers\Teacher\LeaveController as TeacherLeave;
use App\Http\Controllers\Admin\LeaveController as AdminLeave;
use App\Http\Controllers\Parent\LeaveController as ParentLeave;
use App\Http\Controllers\Teacher\MyLeaveController as TeacherMyLeave;
use App\Http\Controllers\Admin\StaffLeaveController as AdminStaffLeave;

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
        Route::get('/leaves', [AdminLeave::class, 'index'])->name('leaves.index');
        Route::get('/leaves/{leaveRequest}', [AdminLeave::class, 'show'])->name('leaves.show');
        Route::get('/staff-leaves', [AdminStaffLeave::class, 'index'])->name('staff-leaves.index');
        Route::get('/staff-leaves/{teacherLeaveRequest}', [AdminStaffLeave::class, 'show'])->name('staff-leaves.show');
        Route::put('/staff-leaves/{teacherLeaveRequest}/review', [AdminStaffLeave::class, 'review'])->name('staff-leaves.review');
        Route::get('/messages', [AdminMessage::class, 'index'])->name('messages.index');
        Route::post('/messages', [AdminMessage::class, 'store'])->name('messages.store');
        Route::get('/messages/{message}', [AdminMessage::class, 'show'])->name('messages.show');
        Route::get('/homework', [AdminHomework::class, 'index'])->name('homework.index');
        Route::get('/homework/{homework}', [AdminHomework::class, 'show'])->name('homework.show');
        Route::get('/exams', [AdminExam::class, 'index'])->name('exams.index');
        Route::get('/exams/{exam}', [AdminExam::class, 'show'])->name('exams.show');
        Route::get('/grading-scheme', [GradingSchemeController::class, 'index'])->name('grading-scheme.index');
        Route::post('/grading-scheme', [GradingSchemeController::class, 'store'])->name('grading-scheme.store');
        Route::put('/grading-scheme/{gradingSchemeBand}', [GradingSchemeController::class, 'update'])->name('grading-scheme.update');
        Route::delete('/grading-scheme/{gradingSchemeBand}', [GradingSchemeController::class, 'destroy'])->name('grading-scheme.destroy');
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
        Route::get('/leaves', [TeacherLeave::class, 'index'])->name('leaves.index');
        Route::get('/leaves/{leaveRequest}', [TeacherLeave::class, 'show'])->name('leaves.show');
        Route::put('/leaves/{leaveRequest}/review', [TeacherLeave::class, 'review'])->name('leaves.review');
        Route::get('/my-leave', [TeacherMyLeave::class, 'index'])->name('my-leave.index');
        Route::get('/my-leave/create', [TeacherMyLeave::class, 'create'])->name('my-leave.create');
        Route::post('/my-leave', [TeacherMyLeave::class, 'store'])->name('my-leave.store');
        Route::get('/my-leave/{teacherLeaveRequest}/edit', [TeacherMyLeave::class, 'edit'])->name('my-leave.edit');
        Route::put('/my-leave/{teacherLeaveRequest}', [TeacherMyLeave::class, 'update'])->name('my-leave.update');
        Route::delete('/my-leave/{teacherLeaveRequest}', [TeacherMyLeave::class, 'destroy'])->name('my-leave.destroy');
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
        Route::get('/exams', [TeacherExam::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [TeacherExam::class, 'create'])->name('exams.create');
        Route::post('/exams', [TeacherExam::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}', [TeacherExam::class, 'show'])->name('exams.show');
        Route::get('/exams/{exam}/edit', [TeacherExam::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{exam}', [TeacherExam::class, 'update'])->name('exams.update');
        Route::put('/exams/{exam}/marks', [TeacherExam::class, 'saveMarks'])->name('exams.marks.save');
        Route::delete('/exams/{exam}', [TeacherExam::class, 'destroy'])->name('exams.destroy');
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
        Route::get('/results', [ParentResult::class, 'index'])->name('results.index');
        Route::get('/results/{exam}', [ParentResult::class, 'show'])->name('results.show');
        Route::get('/leaves', [ParentLeave::class, 'index'])->name('leaves.index');
        Route::get('/leaves/create', [ParentLeave::class, 'create'])->name('leaves.create');
        Route::post('/leaves', [ParentLeave::class, 'store'])->name('leaves.store');
        Route::get('/leaves/{leaveRequest}/edit', [ParentLeave::class, 'edit'])->name('leaves.edit');
        Route::put('/leaves/{leaveRequest}', [ParentLeave::class, 'update'])->name('leaves.update');
        Route::delete('/leaves/{leaveRequest}', [ParentLeave::class, 'destroy'])->name('leaves.destroy');
    });
