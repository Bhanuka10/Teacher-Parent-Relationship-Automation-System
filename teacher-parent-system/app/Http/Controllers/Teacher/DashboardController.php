<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\MessageRecipient;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        $unreadMessages = MessageRecipient::where('user_id', $teacher->id)->whereNull('read_at')->count();

        $schoolClass = $teacher->schoolClass()->with(['students' => fn ($q) => $q->orderBy('name')])->first();
        $studentIds = $schoolClass?->students->pluck('id') ?? collect();
        $studentCount = $studentIds->count();

        $today = now()->toDateString();
        $todayRecords = $studentCount
            ? Attendance::whereIn('student_id', $studentIds)->whereDate('date', $today)->get()->keyBy('student_id')
            : collect();

        $todayPresent = $todayRecords->where('status', 'present')->count();
        $todayAbsent = $todayRecords->where('status', 'absent')->count();
        $todayMarked = $todayRecords->isNotEmpty();
        $todayRate = $studentCount > 0 ? (int) round(($todayPresent / $studentCount) * 100) : 0;

        $trendLabels = [];
        $trendPresent = [];
        $trendAbsent = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('D');

            $dayRecords = $studentCount
                ? Attendance::whereIn('student_id', $studentIds)->whereDate('date', $date->toDateString())->get()
                : collect();

            $trendPresent[] = $dayRecords->where('status', 'present')->count();
            $trendAbsent[] = $dayRecords->where('status', 'absent')->count();
        }

        $toGradeCount = HomeworkSubmission::whereHas('homework', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->whereNotNull('submitted_at')
            ->whereNull('graded_at')
            ->count();

        $homeworkCount = $schoolClass ? Homework::where('school_class_id', $schoolClass->id)->count() : 0;

        $recentHomeworks = $schoolClass
            ? Homework::where('school_class_id', $schoolClass->id)
                ->withCount([
                    'submissions as total_count',
                    'submissions as submitted_count' => fn ($q) => $q->whereNotNull('submitted_at'),
                    'submissions as graded_count' => fn ($q) => $q->whereNotNull('graded_at'),
                ])
                ->latest()
                ->take(5)
                ->get()
            : collect();

        $nextDue = $schoolClass
            ? Homework::where('school_class_id', $schoolClass->id)
                ->whereNotNull('due_at')
                ->where('due_at', '>', now())
                ->orderBy('due_at')
                ->first()
            : null;

        $roster = $schoolClass
            ? $schoolClass->students->map(fn ($student) => [
                'student' => $student,
                'status' => $todayRecords->get($student->id)?->status,
            ])
            : collect();

        return view('teacher.dashboard', compact(
            'teacher',
            'unreadMessages',
            'schoolClass',
            'studentCount',
            'todayPresent',
            'todayAbsent',
            'todayMarked',
            'todayRate',
            'trendLabels',
            'trendPresent',
            'trendAbsent',
            'toGradeCount',
            'homeworkCount',
            'recentHomeworks',
            'nextDue',
            'roster'
        ));
    }
}
