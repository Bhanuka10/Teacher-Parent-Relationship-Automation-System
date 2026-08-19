<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();
        $date = $request->query('date', now()->toDateString());

        $schoolClass = $teacher->schoolClass()->with(['students.profile'])->first();

        if (!$schoolClass) {
            return view('teacher.attendance.index', [
                'schoolClass' => null,
                'students' => collect(),
                'date' => $date,
                'attendanceByStudent' => collect(),
            ]);
        }

        $students = $schoolClass->students->sortBy('name')->values();

        $attendanceByStudent = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendance.index', compact('schoolClass', 'students', 'date', 'attendanceByStudent'));
    }

    public function store(Request $request)
    {
        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass()->with('students')->first();

        if (!$schoolClass) {
            return redirect()->route('teacher.attendance.index')
                ->withErrors(['attendance' => 'No class is assigned to this teacher.']);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['nullable', 'array'],
            'status.*' => ['required', 'in:present,absent'],
        ]);

        $date = $validated['date'];
        $statuses = $validated['status'] ?? [];

        // Days already on an approved leave are locked — the grid doesn't
        // render radios for them (see teacher/attendance/index.blade.php),
        // so they're always missing from $statuses. Without this guard
        // they'd silently fall through to the 'absent' default below and
        // overwrite the approved leave record.
        $leaveLockedStudentIds = Attendance::whereIn('student_id', $schoolClass->students->pluck('id'))
            ->whereDate('date', $date)
            ->where('status', 'leave')
            ->pluck('student_id');

        foreach ($schoolClass->students as $student) {
            if ($leaveLockedStudentIds->contains($student->id)) {
                continue;
            }

            $status = $statuses[$student->id] ?? 'absent';

            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'marked_by' => $teacher->id,
                ]
            );
        }

        return redirect()->route('teacher.attendance.index', ['date' => $date])
            ->with('success', 'Attendance saved successfully.');
    }

    public function history(Request $request)
    {
        $teacher = auth()->user();
        $filterDate = $request->query('date');

        $schoolClass = $teacher->schoolClass()->with(['students.profile'])->first();

        if (!$schoolClass) {
            return view('teacher.attendance.history', [
                'schoolClass' => null,
                'records' => collect(),
                'filterDate' => $filterDate,
                'exportFrom' => $request->query('from'),
                'exportTo' => $request->query('to'),
            ]);
        }

        $records = Attendance::with(['student.profile'])
            ->whereIn('student_id', $schoolClass->students->pluck('id'))
            ->when($filterDate, function ($query) use ($filterDate) {
                $query->whereDate('date', $filterDate);
            })
            ->orderByDesc('date')
            ->orderBy('student_id')
            ->get();

        return view('teacher.attendance.history', [
            'schoolClass' => $schoolClass,
            'records' => $records,
            'filterDate' => $filterDate,
            'exportFrom' => $request->query('from'),
            'exportTo' => $request->query('to'),
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $teacher = auth()->user();
        $schoolClass = $teacher->schoolClass()->with(['students.profile'])->first();

        abort_unless($schoolClass, 404, 'No class is assigned to this teacher.');

        $records = Attendance::with(['student.profile', 'student.schoolClass', 'markedBy'])
            ->whereIn('student_id', $schoolClass->students->pluck('id'))
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('date', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('date', '<=', $to))
            ->orderBy('date')
            ->orderBy('student_id')
            ->get();

        return $this->streamCsv($records, 'attendance-'.Str::slug($schoolClass->name));
    }

    private function streamCsv($records, string $filenamePrefix)
    {
        $filename = $filenamePrefix.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Date', 'Student', 'Index No.', 'Class', 'Status', 'Marked By']);

            foreach ($records as $record) {
                fputcsv($handle, [
                    $record->date->format('Y-m-d'),
                    $record->student->name ?? '',
                    $record->student->profile?->index_number ?? $record->student->admission_number ?? '',
                    $record->student->schoolClass?->name ?? '',
                    $record->status === 'leave' ? 'On Leave' : ucfirst($record->status),
                    $record->markedBy?->name ?? '',
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
