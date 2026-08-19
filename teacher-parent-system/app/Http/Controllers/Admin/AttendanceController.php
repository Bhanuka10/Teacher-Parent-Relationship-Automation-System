<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $grade = $request->query('grade');
        $section = $request->query('section');
        $date = $request->query('date');

        $query = $this->filteredQuery($grade, $section, $date)
            ->orderByDesc('date')
            ->orderBy('student_id');

        $total   = $query->count();
        $records = $query->paginate(10)->withQueryString();

        return view('admin.attendance.history', compact('records', 'grade', 'section', 'date', 'total'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $grade = $request->query('grade');
        $section = $request->query('section');

        $records = $this->filteredQuery($grade, $section, null, $validated['from'] ?? null, $validated['to'] ?? null)
            ->orderBy('date')
            ->orderBy('student_id')
            ->get();

        $filename = 'attendance-'.now()->format('Ymd-His').'.csv';

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

    private function filteredQuery(?string $grade, ?string $section, ?string $date, ?string $from = null, ?string $to = null): Builder
    {
        return Attendance::with([
                'student.profile',
                'student.schoolClass',
                'markedBy',
            ])
            ->when($date, function ($q) use ($date) {
                $q->whereDate('date', $date);
            })
            ->when($from, function ($q) use ($from) {
                $q->whereDate('date', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('date', '<=', $to);
            })
            ->when($grade || $section, function ($q) use ($grade, $section) {
                $q->whereHas('student.schoolClass', function ($classQuery) use ($grade, $section) {
                    if ($grade && $section) {
                        $classQuery->where('name', $grade.'-'.$section);
                        return;
                    }
                    if ($grade) {
                        $classQuery->where('name', 'like', $grade.'-%');
                    }
                    if ($section) {
                        $classQuery->where('name', 'like', '%-'.$section);
                    }
                });
            });
    }
}
