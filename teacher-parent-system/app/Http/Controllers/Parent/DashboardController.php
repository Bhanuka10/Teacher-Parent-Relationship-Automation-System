<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\MessageRecipient;
use App\Services\ExamResultService;

class DashboardController extends Controller
{
    public function __construct(private ExamResultService $results) {}

    public function index()
    {
        $parent = auth()->user();
        $student = $parent->students()->with('schoolClass')->first();
        $schoolClass = $student?->schoolClass;

        $unreadMessages = MessageRecipient::where('user_id', $parent->id)->whereNull('read_at')->count();

        $todayAttendance = null;
        $trendLabels = [];
        $trendPresent = [];
        $trendAbsent = [];

        if ($student) {
            $todayAttendance = Attendance::where('student_id', $student->id)
                ->whereDate('date', now()->toDateString())
                ->first();

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $trendLabels[] = $date->format('D');

                $record = Attendance::where('student_id', $student->id)
                    ->whereDate('date', $date->toDateString())
                    ->first();

                $trendPresent[] = $record?->status === 'present' ? 1 : 0;
                $trendAbsent[] = $record?->status === 'absent' ? 1 : 0;
            }
        }

        $weekPresent = array_sum($trendPresent);
        $weekAbsent = array_sum($trendAbsent);
        $weekMarked = $weekPresent + $weekAbsent;
        $weekUnmarked = max(7 - $weekMarked, 0);
        $weekRate = $weekMarked > 0 ? (int) round(($weekPresent / $weekMarked) * 100) : 0;

        $dueHomeworkCount = 0;
        $recentSubmissions = collect();
        $nextDue = null;

        if ($student) {
            $dueHomeworkCount = HomeworkSubmission::where('student_id', $student->id)
                ->whereNull('submitted_at')
                ->whereHas('homework', fn ($q) => $q->whereNull('due_at')->orWhere('due_at', '>', now()))
                ->count();

            $recentSubmissions = HomeworkSubmission::where('student_id', $student->id)
                ->with('homework')
                ->get()
                ->sortByDesc(fn ($submission) => $submission->homework->created_at)
                ->take(5)
                ->values();

            $nextDue = $schoolClass
                ? Homework::where('school_class_id', $schoolClass->id)
                    ->whereNotNull('due_at')
                    ->where('due_at', '>', now())
                    ->orderBy('due_at')
                    ->first()
                : null;
        }

        $resultHistory = ($student && $schoolClass)
            ? $this->results->termHistoryFor($student, $schoolClass)->reverse()->take(4)->values()
            : collect();

        return view('parent.dashboard', compact(
            'parent',
            'student',
            'schoolClass',
            'todayAttendance',
            'unreadMessages',
            'trendLabels',
            'trendPresent',
            'trendAbsent',
            'weekPresent',
            'weekAbsent',
            'weekUnmarked',
            'weekRate',
            'dueHomeworkCount',
            'recentSubmissions',
            'nextDue',
            'resultHistory'
        ));
    }
}
