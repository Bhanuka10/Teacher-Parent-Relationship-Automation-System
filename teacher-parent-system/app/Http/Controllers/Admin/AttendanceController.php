<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function history(Request $request)
    {
        $grade = $request->query('grade');
        $section = $request->query('section');
        $date = $request->query('date');

        $records = Attendance::with([
                'student.profile',
                'student.schoolClass',
                'markedBy',
            ])
            ->when($date, function ($query) use ($date) {
                $query->whereDate('date', $date);
            })
            ->when($grade || $section, function ($query) use ($grade, $section) {
                $query->whereHas('student.schoolClass', function ($classQuery) use ($grade, $section) {
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
            })
            ->orderByDesc('date')
            ->orderBy('student_id')
            ->get();

        return view('admin.attendance.history', compact('records', 'grade', 'section', 'date'));
    }
}
